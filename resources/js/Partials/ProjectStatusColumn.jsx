import {router, usePage} from '@inertiajs/react';
import TaskCard from "@/Partials/TaskCard.jsx"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useEffect, useRef, useState} from 'react'
import axios from 'axios'
import LoadingSpinner from '@/Components/LoadingSpinner.jsx'
import * as FormErrors from "@/Utils/FormErrors.js"
import EventBus from "@/EventBus.js"
import eventBus from "@/EventBus.js"

export default function ProjectStatusColumn ({status, tasks, addTask = false}) {

    const [newTitle, setNewTitle] = useState('')
    const [loading, setLoading] = useState(false)
    const [dragging, setDragging] = useState(null)
    const [draggingOver, setDraggingOver] = useState(false)

    const {project} = usePage().props

    useEffect(() => {
        EventBus.on('task-card-drag-start', showDropableZones)
        EventBus.on('task-card-drag-end', stopDropableZones)
        FormErrors.resetErrors()
    }, [])

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

    function allowDrop (ev) {
        ev.preventDefault();
    }

    function drop (ev) {
        ev.preventDefault();
        let column = ev.target.closest('.column')
        let cardStack = column.querySelector('.task-cards')
        cardStack.appendChild(document.getElementById(dragging.elementId))
        updateTaskStatus(dragging.taskId)
    }

    function showDropableZones (obj) {
        setDragging(obj)
    }

    function stopDropableZones() {
        setDragging(null)
        setDraggingOver(false)
    }

    function updateTaskStatus (id) {
        axios.patch('/api/project/' + project.data.id + '/task/' + id, {
            'status_id': status.id
        })
            .then(response => {
                EventBus.emit('task-card-drag-end')
            })
    }

    function dragoverClasses () {
        return draggingOver ? 'bg-sky-50' : ''
    }

    return (
        <>
            <div className={dragoverClasses() + ' border border-gray-200 bg-gray-50 rounded-md shadow-md column'}
                 onDrop={drop}
                 onDragEnter={() => setDraggingOver(true)}
                 onDragLeave={() => setDraggingOver(false)}
                 onDragOver={allowDrop}
            >
                <div className="p-8 border-dashed border-gray-300 rounded-t-md border-b bg-white">
                    <div className="font-medium text-lg">{status.name} ({status.tasks_count})</div>
                </div>
                <div className="grid grid-cols-1 gap-3 p-3 ">

                    {addTask && <div className="flex items-stretch">
                        <div className="grow">
                            <input placeholder="New task title here"
                                   type="text"
                                   onChange={setNewTaskTitle}
                                   value={newTitle}
                                   className="p-3 flex grow border rounded shadow w-full border border-gray-200 bg-white"/>
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

                    <div className={'grid grid-cols-1 gap-3 h-full task-cards'}>
                        {tasks.map((task, index) => (
                            <TaskCard key={'to-do-' + index}
                                      task={task}>
                            </TaskCard>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
}
