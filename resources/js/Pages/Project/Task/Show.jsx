import {Head, Link, router} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {useEffect, useRef, useState} from "react"
import axios from 'axios'
import TaskNotes from "@/Partials/TaskNotes.jsx"
import TaskDescription from "@/Partials/TaskDescription.jsx"
import FullPagePanel from "@/Components/FullPagePanel.jsx"
import TaskContext from "@/Partials/TaskContext.jsx"
import eventBus from "@/EventBus.js"
import TextInput from "@/Components/TextInput.jsx"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import Gravatar from "@/Partials/Task/Gravatar.jsx"
import TaskLinks from "@/Partials/Task/Links.jsx"

export default function ShowProjectTask ({project, task}) {

    const [statusId, setStatusId] = useState(task.data.status.id)
    const [assigneeId, setAssigneeId] = useState(task.data.assignee?.id || '')
    const [editTitle, setEditTitle] = useState(false)
    const [taskTitle, setTaskTitle] = useState(task.data.title)

    const firstUpdate = useRef(true);

    useEffect(() => {
        eventBus.on('task-deleted', handleDeletedTask)

        if (firstUpdate.current) {
            // STOP
            firstUpdate.current = false;
            return;
        }

        // hammer time 🔨
        updateTask()

    }, [statusId, assigneeId]);

    function handleDeletedTask (eventTask) {
        if (eventTask.id != task.data.id) {
            // this task was not deleted
            return
        }

        // this task was deleted, send user back to the project page
        router.visit('/project/' + project.data.id)
    }

    const updateTask = function () {
        let data = {
            'status_id': parseInt(statusId),
            'assignee_id': parseInt(assigneeId),
            'title': taskTitle,
        }

        axios.patch('/api/project/' + project.data.id + '/task/' + task.data.id, data)
            .then(response => {
                task = response.data.data
                setEditTitle(false)
                router.reload()
            })
            .catch(error => {
                console.log('error')
                console.log(error)
            })
    }

    function updateStatus (e) {
        setStatusId(e.target.value)
    }

    function updateAssignee (e) {
        let value = e.target.value == 'Unassigned' ? null : e.target.value
        setAssigneeId(value)
    }

    return (
        <>
            <AuthenticatedLayout
                breadcrumb={project.data.breadcrumb}
            >

                <Head title={task.data.title}/>

                <FullPagePanel
                    title={
                    <div className={'flex justify-between'}>
                        <div className={'grow'}>
                            <div className={'text-sm'}>{task.data.reference}</div>

                            {!editTitle &&
                                <div onClick={() => setEditTitle(true)}
                                      className={'cursor-pointer hover:text-sky-600 mt-3'}
                                >
                                    <h2 className={'text-2xl'}>{taskTitle}</h2>

                            </div>
                            }
                            {editTitle && <div className={'flex mt-3'}>
                                <TextInput value={taskTitle}
                                           onChange={(e) => setTaskTitle(e.target.value)}
                                           className={'w-1/2'}>
                                </TextInput>
                                <PrimaryButton onClick={updateTask} className={'ml-3'}>Save</PrimaryButton>
                                <span className={'text-sky-600 hover:text-sky-800 ml-3 self-center cursor-pointer'}
                                      onClick={() => setEditTitle(false)}
                                >Cancel</span>
                            </div>
                            }
                        </div>
                        <div>
                            <TaskContext task={task.data}></TaskContext>
                        </div>
                    </div>

                }>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-10">

                        <div>
                            <TaskDescription description={task.data.description}></TaskDescription>

                            <TaskLinks task={task.data}></TaskLinks>

                            <div className="mb-3 mt-8 font-bold">
                                Status
                            </div>
                            <div>
                                <select name="status"
                                        id="status"
                                        className="w-full border shadow rounded border-gray-200 p-3 bg-white"
                                        onChange={updateStatus}
                                        value={statusId}
                                >
                                    {project.data.statuses.map((projectStatus, index) => (
                                        <option key={'status-' + index}
                                                value={projectStatus.id}
                                        >{projectStatus.name}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="mb-3 mt-8 font-bold">
                                Assignee
                            </div>
                            <div className={'flex'}>
                                <Gravatar user={task.data.assignee}></Gravatar>
                                <select name="assignee"
                                        id="assignee"
                                        className="w-full ml-3 border shadow rounded border-gray-200 p-3 bg-white"
                                        onChange={updateAssignee}
                                        value={assigneeId}
                                >

                                    <option value={null}>
                                        Unassigned
                                    </option>

                                    {project.data.users.map((user, index) => (
                                        <option key={'user-' + index}
                                                value={user.id}
                                        >
                                            {user.full_name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <div className="mt-3 md:mt-0">
                            <TaskNotes></TaskNotes>
                        </div>
                    </div>
                </FullPagePanel>

            </AuthenticatedLayout>
        </>
    );
}
