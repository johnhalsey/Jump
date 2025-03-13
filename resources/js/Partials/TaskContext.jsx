import {router} from '@inertiajs/react';
import {useEffect, useState} from "react"
import axios from "axios"
import eventBus from "@/EventBus.js"

export default function TaskContext ({task}) {

    const [show, setShow] = useState(false)

    useEffect(() => {
        eventBus.on('task-options-opened', closeContext)
        eventBus.on('page-clicked', handlePageClickEvent)
    }, []);

    function closeContext(eventTask) {
        if (eventTask.id != task.id) {
            setShow(false)
        }
    }

    function handlePageClickEvent (event) {
        if (event.id && event.id == 'task-context-' + task.id) {
            return
        }

        if (event.parentNode.id && event.parentNode.id == 'task-context-' + task.id) {
            return
        }

        setShow(false)
    }

    function openContext (e) {
        e.preventDefault()
        eventBus.emit('task-options-opened', task)
        setShow(true)
    }

    function deleteTask (e) {
        e.preventDefault()
        axios.delete('/api/project/' + task.project.id + '/task/' + task.id)
            .then(() => {
                eventBus.emit('task-deleted', task)
            })
    }

    return (
        <>
            <div className={'relative overflow-visible z-50'} id={'task-context-' + task.id}>
                <div onClick={openContext}
                     className={'cursor-pointer text-sm text-sky-600 hover:text-sky-800'}>
                    Options
                </div>

                <div
                    className={!show ? 'hidden' : 'absolute ' + 'top-0 right-16 bg-white border border-gray-300 rounded w-36'}>
                    <div className={'p-3 cursor-pointer hover:bg-sky-100'}
                         onClick={deleteTask}
                    >
                        Delete
                    </div>
                </div>
            </div>
        </>
    );
}
