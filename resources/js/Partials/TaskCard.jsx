import {Head, Link} from '@inertiajs/react';
import TaskContext from "@/Partials/TaskContext.jsx"
import Gravatar from "@/Partials/Task/Gravatar.jsx"
import Tooltip from "@/Components/Tooltip.jsx"
import EventBus from "@/EventBus.js"
import {useEffect, useState} from "react"

export default function TaskCard ({task}) {

    function dragStart () {
        EventBus.emit('task-card-drag-start', {
                elementId: 'task-card-' + task.id,
                taskId: task.id
            }
        )
    }

    return (
        <Link href={'/project/' + task.project.id + '/task/' + task.id}
              id={'task-card-' + task.id}
              draggable="true"
              onDragStart={() => dragStart()}
        >
            <div className={'p-3 border border-gray-200 rounded shadow bg-white hover:bg-sky-50'}>
                <div className={'flex justify-between'}>
                    <div className={'mr-3'}>
                        {task.title}
                    </div>
                    <Tooltip text={task.assignee ? task.assignee.full_name : 'Unassigned'}>
                        <Gravatar user={task.assignee}></Gravatar>
                    </Tooltip>
                </div>
                <div className="mt-5 flex justify-between">
                    <div className={'text-sm'}>{task.reference}</div>
                    <TaskContext task={task}></TaskContext>
                </div>
            </div>
        </Link>
    );
}
