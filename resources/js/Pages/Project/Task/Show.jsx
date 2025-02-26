import {Head, Link} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {useState, useEffect, useRef} from "react"
import axios from 'axios'
import PrimaryButton from "@/Components/PrimaryButton.jsx"

export default function ShowProjectTask ({project, task}) {

    const [statusId, setStatusId] = useState(task.data.status.id)
    const [assigneeId, setAssigneeId] = useState(task.data.assignee?.id)
    const [description, setDescription] = useState(task.data.description)
    const [editingDescription, setEditingDescription] = useState(false)
    const [loading, setLoading] = useState(false)

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
        setLoading(true)

        let data = {
            'status_id': statusId,
            'assignee_id': assigneeId,
            'description': description
        }

        axios.patch('/api/project/' + project.data.id + '/task/' + task.data.id, data)
            .then(response => {
                task = response.data.data
                setDescription(task.description)
                setEditingDescription(false)
                setLoading(false)
            })
            .catch(error => {
                console.log('error')
                console.log(error.response.data)
            })
    }

    function updateStatus (e) {
        setStatusId(e.target.value)
    }

    function updateAssignee (e) {
        setAssigneeId(e.target.value)
    }

    function editDescription () {
        setEditingDescription(true)
    }

    function updateDescription (e) {
        setDescription(e.target.value)
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
                                {!editingDescription &&
                                    <div className="bg-white hover:bg-sky-50 p-3 rounded border shadow whitespace-pre-wrap cursor-pointer"
                                         onClick={editDescription}
                                    >
                                        {description}
                                    </div>
                                }

                                {editingDescription && <><textarea
                                    className="w-full border-gray-300 rounded shadow"
                                    rows="10"
                                    value={description}
                                    onChange={updateDescription}></textarea>
                                    <PrimaryButton loading={loading}
                                                   disabled={loading}
                                                   onClick={updateTask}
                                    >
                                        Save
                                    </PrimaryButton>
                                </>
                                }

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
