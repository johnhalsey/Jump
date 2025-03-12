import {Head, Link, router} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {useEffect, useRef, useState} from "react"
import axios from 'axios'
import TaskNotes from "@/Partials/TaskNotes.jsx"
import TaskDescription from "@/Partials/TaskDescription.jsx"
import FullPagePanel from "@/Components/FullPagePanel.jsx"
import TaskContext from "@/Partials/TaskContext.jsx"
import eventBus from "@/EventBus.js"

export default function ShowProjectTask ({project, task}) {

    const [statusId, setStatusId] = useState(task.data.status.id)
    const [assigneeId, setAssigneeId] = useState(task.data.assignee?.id)

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
            'status_id': statusId,
            'assignee_id': assigneeId,
        }

        axios.patch('/api/project/' + project.data.id + '/task/' + task.data.id, data)
            .then(response => {
                task = response.data.data
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
        setAssigneeId(e.target.value)
    }

    return (
        <>
            <AuthenticatedLayout
                header={
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        <Link href={'/project/' + task.data.project.id}>{task.data.project.name}</Link>
                    </h2>
                }
            >

                <Head title={task.data.title}/>

                <FullPagePanel title={
                    <div className={'flex justify-between'}>
                        <div className={'grow'}>{task.data.reference + ' - ' + task.data.title}</div>
                        <div>
                            <TaskContext task={task.data}></TaskContext>
                        </div>
                    </div>

                }>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-10">

                        <div>
                            <TaskDescription description={task.data.description}></TaskDescription>

                            <div className="mb-3 mt-8 font-bold">
                                Status
                            </div>
                            <div>
                                <select name="status"
                                        id="status"
                                        className="w-full border shadow rounded border-gray-300"
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
                            <div>
                                <select name="assignee"
                                        id="assignee"
                                        className="w-full border shadow rounded border-gray-300"
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
                            <TaskNotes notes={task.data.notes}></TaskNotes>
                        </div>
                    </div>
                </FullPagePanel>

            </AuthenticatedLayout>
        </>
    );
}
