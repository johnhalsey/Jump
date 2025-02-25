import {Head, Link} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {useState} from "react"
import axios from 'axios'

export default function ShowProjectTask ({project, task}) {

    const [status, setStatus] = useState(task.data.status)
    const [assignee, setAssignee] = useState(task.data.assignee)

    const updateTask = function () {
        axios.patch('/api/projects/' + project.data.id + '/tasks/' + task.data.id)
            .then(response => {
                // OK
            })
            .catch(error => {
                // not OK.
            })
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
                                            className="w-full border border-gray-300 shadow rounded "
                                    >
                                        {project.data.statuses.map((status, index) => (
                                            <option key={'status-' + index}
                                                    selected={status.name == task.data.status}>{status.name}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-3 mt-8 font-bold">
                                    Assignee
                                </div>
                                <div>
                                    <select name="assignee"
                                            id="assignee"
                                            className="w-full border border-gray-300 shadow rounded outline-0"
                                    >

                                        <option selected={task.data.assignee == null}>
                                            Unassigned
                                        </option>

                                        {project.data.users.map((user, index) => (
                                            <option key={'user-' + index}
                                                    selected={user.id == task.data.assignee.id}>
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
                                        <div className="border rounded shadow mb-3 p-3 bg-white">
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
