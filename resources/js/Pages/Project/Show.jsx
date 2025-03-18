import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link} from '@inertiajs/react';
import {useEffect, useRef, useState} from 'react'
import axios from 'axios'
import ProjectStatusColumn from "@/Partials/ProjectStatusColumn.jsx"
import eventBus from "@/EventBus.js"
import TextInput from "@/Components/TextInput.jsx"
import {useDebounce} from "@/Utils/useDebounce.js"
import LoadingSpinner from "@/Components/LoadingSpinner.jsx"
import Gravatar from "@/Partials/Task/Gravatar.jsx"
import Tooltip from "@/Components/Tooltip.jsx"

export default function ShowProject ({project}) {

    const [loading, setLoading] = useState(true)
    const [tasks, setTasks] = useState([])
    const [search, setSearch] = useState('') //  to keep the dom reactive
    const [filteredUserIds, setFilteredUserIds] = useState([])

    const firstUpdate = useRef(true);

    useEffect(() => {
        eventBus.on('task-deleted', getTasks)

        if (firstUpdate.current) {
            // only on page load
            getTasks()
            firstUpdate.current = false;
        } else {
            // other logic here to fire on second load onwards
        }

    }, [])

    const searchTasks = useDebounce(() => {
        getTasks(search, filteredUserIds)
    })

    function setSearchTerm (e) {
        setSearch(e.target.value)

        searchTasks()
    }

    function clearFilters () {
        setSearch('')
        setFilteredUserIds([])
        getTasks('', [])
    }

    // getTasks expects search and users to be passed through, for timing purposes.
    // the state values have not always been set by the time we need to make this call
    function getTasks (search = '', userIds = []) {
        setLoading(true)

        let params = {
            search: search,
            userIds: userIds
        }
        axios.get('/api/project/' + project.data.id + '/tasks', {params})
            .then(response => {
                setTasks(response.data.data)
                setLoading(false)
            })
            .catch((error) => {
                console.log('error getting tasks')
                console.log(error)
                setLoading(false)
            })
    }

    function tasksByStatusId (id) {
        return tasks.filter(task => task.status.id == id)
    }

    function toggleUnnassigned () {
        toggleFilterByUser({id: ''})
    }

    function tasksAreFilteredByNull () {
        return tasksAreFilteredByUser({id: ''})
    }

    function toggleFilterByUser (user) {
        if (tasksAreFilteredByUser(user)) {
            // remove user id from state array
            let idFilter = filteredUserIds.filter(userId => userId != user.id)
            setFilteredUserIds(idFilter)
            getTasks(search, idFilter)

            return
        }

        // add user id to state array
        let idFilter = [...filteredUserIds, user.id]
        setFilteredUserIds(idFilter)
        getTasks(search, idFilter)
    }

    function tasksAreFilteredByUser (user) {
        if (!filteredUserIds.length) {
            return false
        }

        let filter = filteredUserIds.filter(userId => userId == user.id)
        return filter.length > 0
    }

    return (
        <AuthenticatedLayout
            header={
                <>
                    <div className={'md:flex justify-between'}>

                        <div>
                            <h2 className="text-2xl">
                                {project.data.name}
                            </h2>

                            <div className={'md:flex mt-2'}>
                                <div className={'flex'}>
                                    <div onClick={() => toggleUnnassigned()} className={'cursor-pointer'}>
                                        <Tooltip text={'Unassigned'}>
                                            <Gravatar user={null}
                                                      className={tasksAreFilteredByNull() && 'border-sky-600 border-2'}>
                                            </Gravatar>
                                        </Tooltip>
                                    </div>
                                    {project.data.users.map((user, index) => (
                                        user.tasks_count > 0
                                            ? (
                                                <div key={'project-users-' + index}
                                                     className={'-ml-3 cursor-pointer'}
                                                     onClick={() => toggleFilterByUser(user)}
                                                >
                                                    <Tooltip text={user.full_name + ' (' + user.tasks_count + ')'}>
                                                        <Gravatar user={user}
                                                                  className={tasksAreFilteredByUser(user) && 'border-sky-600 border-2'}
                                                        ></Gravatar>
                                                    </Tooltip>
                                                </div>)
                                            : null
                                    ))}
                                </div>

                                <div className={'mt-3 md:mt-0 md:ml-5 '}>
                                    <div className={'flex'}>
                                        <TextInput placeholder={'Search here'}
                                                   value={search}
                                                   onChange={setSearchTerm}
                                                   className={'w-full md:w-auto'}
                                        >
                                        </TextInput>
                                        {loading && <div className={'ml-3 inline-block self-center'}>
                                            <LoadingSpinner></LoadingSpinner>
                                        </div>}
                                        {!loading && (filteredUserIds.length || search != '') &&
                                            <span
                                                className={'ml-3 self-center text-sky-600 hover:text-sky-800 cursor-pointer'}
                                                onClick={clearFilters}
                                            >Clear Filters</span>}
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div className={'shrink mt-3 md:mt-0 md:text-right'}>
                            <Link href={'/project/' + project.data.id + '/settings'}
                                  className={'text-sky-600 hover:text-sky-800'}
                            >
                                Settings
                            </Link>


                        </div>

                    </div>
                </>
            }
        >
            <Head title={project.data.name}/>

            <div className="w-full mx-auto px-4 sm:px-6 lg:px-8">

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 lg:gap-10 pb-12">
                    {project.data.statuses.map((status, index) => (
                        <ProjectStatusColumn
                            key={'project-status-column-' + index}
                            status={status}
                            tasks={tasksByStatusId(status.id)}
                            addTask={status.name == 'To Do'}
                        ></ProjectStatusColumn>
                    ))}
                </div>

            </div>

        </AuthenticatedLayout>
    );
}
