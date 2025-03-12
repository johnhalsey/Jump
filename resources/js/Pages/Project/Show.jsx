import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Link, Head, useForm} from '@inertiajs/react';
import {useEffect, useRef, useState} from 'react'
import axios from 'axios'
import ProjectStatusColumn from "@/Partials/ProjectStatusColumn.jsx"
import eventBus from "@/EventBus.js"
import TextInput from "@/Components/TextInput.jsx"
import _debounce from 'lodash/debounce';

export default function ShowProject ({project}) {

    const [loading, setLoading] = useState(true)
    const [tasks, setTasks] = useState([])
    const [search, setSearch] = useState('')

    const firstUpdate = useRef(true);

    useEffect(() => {
        eventBus.on('task-deleted', getTasks)

        if (firstUpdate.current) {
            // only on page load
            getTasks()
            firstUpdate.current = false;
        } else {
            // on change
            searchTasks()
        }

    }, [search])

    const searchTasks = _debounce(function() {
        getTasks()
    }, 1000)

    const getTasks = function () {
        axios.get('/api/project/' + project.data.id + '/tasks', {params: {search: search}})
            .then(response => {
                setTasks(response.data.data)
                setLoading(false)
            })
    }

    const tasksByStatus = function (status) {
        return tasks.filter(task => task.status.name == status)
    }

    return (
        <AuthenticatedLayout
            header={
            <div className={'md:flex justify-between'}>

                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    {project.data.name}
                </h2>
                <div className={'mt-3 md:mt-0'}>
                    <TextInput placeholder={'Search here'}
                               value={search}
                               onChange={(e) => setSearch(e.target.value)}
                    >

                    </TextInput>
                </div>
                <div className={'mt-3 md:mt-0'}>
                    <Link href={'/project/' + project.data.id + '/settings'}
                          className={'text-sky-600 hover:text-sky-800'}
                    >
                        Settings
                    </Link>
                </div>
            </div>
            }
        >
            <Head title={project.data.name}/>

            <div className="w-full mx-auto px-4 sm:px-6 lg:px-8">

                {!loading && <div className="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 lg:gap-10 pb-12">
                    <ProjectStatusColumn
                        status="To Do"
                        tasks={tasksByStatus('To Do')}
                        addTask
                    ></ProjectStatusColumn>

                    <ProjectStatusColumn
                        status="In Progress"
                        tasks={tasksByStatus('In Progress')}
                    ></ProjectStatusColumn>

                    <ProjectStatusColumn
                        status="Done"
                        tasks={tasksByStatus('Done')}
                    ></ProjectStatusColumn>
                </div>}

            </div>

        </AuthenticatedLayout>
    );
}
