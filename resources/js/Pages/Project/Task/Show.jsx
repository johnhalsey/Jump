import {Head, Link} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {useState, useEffect, useRef} from "react"
import axios from 'axios'

export default function ShowProjectTask ({project, task}) {

    const [statusId, setStatusId] = useState(task.data.status.id)
    const [assigneeId, setAssigneeId] = useState(task.data.assignee.id)
    const firstUpdate = useRef(true);

    useEffect(() => {
        if (firstUpdate.current) {
            // STOP
            firstUpdate.current = false;
            return;
        }

        // hammer time 🔨
        updateTask()
    }, [statusId, assigneeId]);

    const updateTask = function () {
        let data = {
            'status_id': statusId,
            'assignee_id': assigneeId
        }

        axios.patch('/api/project/' + project.data.id + '/task/' + task.data.id, data)
            .then(response => {
                console.log('ok')
            })
            .catch(error => {
                console.log('error')
                console.log(error.response.data)
            })
    }

    function updateStatus(e) {
        setStatusId(e.target.value)
    }

    function updateAssignee(e) {
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

                <div className="mx-4 md:mx-8 bg-white rounded-md border shadow">

                    <div className="p-8 border-b border-dashed">
                        {task.data.reference} - {task.data.title}
                    </div>
                    <div className="p-8 bg-gray-50 rounded-b-md">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-10">

                            <div>
                                <div className="mb-3 font-bold">
                                    Description
                                </div>
                                <div className="bg-white p-3 rounded border shadow">
                                    {task.data.description}
                                </div>

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
                                                {user.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="mt-3 md:mt-0">
                                <div className="mb-3 font-bold">
                                    Notes
                                </div>
                                <div>
                                    {task.data.notes.map((note, index) => (
                                        <div className="border rounded shadow mb-3 p-3 bg-white"
                                             key={'task-note-' + index}
                                        >
                                            <div>{note.note}</div>
                                            <div className="text-sm text-right mt-5">
                                                {note.user.name} - {note.date}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        </>
    );
}
