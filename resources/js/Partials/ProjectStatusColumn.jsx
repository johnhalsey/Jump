import {usePage } from '@inertiajs/react';
import TaskCard from "@/Partials/TaskCard.jsx"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useState} from 'react'
import axios from 'axios'
import LoadingSpinner from '@/Components/LoadingSpinner.jsx'
import * as FormErrors from "@/Utils/FormErrors.js"

export default function ProjectStatusColumn ({status, tasks, addTask = false}) {

    const [newTitle, setNewTitle] = useState('')
    const [loading, setLoading] = useState(false)

    const { project } = usePage().props

    function CreateNewTask () {
        setLoading(true)
        FormErrors.resetErrors()

        axios.post('/api/project/' + project.data.id + '/tasks', {
            title: newTitle
        }).then(response => {
            setNewTitle('')
            tasks.unshift(response.data.data)
            setLoading(false)
        }).catch(error => {
            FormErrors.pushErrors(error.response.data.errors)
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

                    {addTask && <div className="flex items-stretch px-3 my-3">
                        <div className="grow">
                            <input placeholder="New task title here"
                                   type="text"
                                   onChange={setNewTaskTitle}
                                   value={newTitle}
                                   className="p-3 flex grow border rounded shadow w-full border border-gray-300"/>
                            {FormErrors.errorsHas('title') && <div className={'text-red-500'}>
                                {FormErrors.errorValue('title')}
                            </div>}
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
