import {Head, Link, router} from '@inertiajs/react';
import TaskCard from "@/Partials/TaskCard.jsx"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useState} from 'react'
import axios from 'axios'
import LoadingSpinner from '@/Components/LoadingSpinner.jsx'

export default function ProjectStatusColumn ({status, tasks, addTask = false}) {

    const [newTitle, setNewTitle] = useState(null)
    const [loading, setLoading] = useState(false)

    function CreateNewTask () {

        setLoading(true)
        axios.post('/api/project/' + tasks[0].project.id + '/tasks', {
            title: newTitle
        }).then(response => {
            setNewTitle(null)
            tasks.unshift(response.data.data)
        }).catch(error => {
            // todo handle this error
        }).finally(() => {
            setLoading(false)
        })

    }

    function setNewTaskTitle (e) {
        setNewTitle(e.target.value)
    }

    return (
        <>
            <div className="border bg-gray-50 rounded-md shadow-md">
                <div className="p-8 border-dashed rounded-t-md border-b bg-white">
                    <div className="font-bold text-lg">{status} ({tasks.length})</div>
                </div>
                <div className="overflow-scroll">

                    {addTask && <div className="flex items-stretch px-3 mt-3">
                        <div className="grow">
                            <input placeholder="New task title here"
                                   type="text"
                                   onChange={setNewTaskTitle}
                                   className="p-3 flex grow border rounded shadow w-full border border-gray-300"/>
                        </div>
                        <div className="shrink ml-3">
                            <PrimaryButton className="h-full"
                                           onClick={CreateNewTask}
                                           disabled={loading}
                            >
                                {loading && <span>
                                    <LoadingSpinner></LoadingSpinner>
                                </span>}

                                {!loading && <span>
                                    Add
                                </span>}

                            </PrimaryButton>
                        </div>
                    </div>}

                    {tasks.map((task, index) => (
                        <TaskCard key={'to-do-' + index} task={task}></TaskCard>
                    ))}
                </div>
            </div>
        </>
    );
}
