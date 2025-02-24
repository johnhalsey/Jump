import {Head, Link} from '@inertiajs/react';
import TaskCard from "@/Partials/TaskCard.jsx"

export default function ProjectStatusColumn ({status, tasks}) {

    return (
        <>
            <div className="border bg-gray-50 rounded-md shadow-md">
                <div className="px-8 py-8 border-dashed rounded-t-md border-b bg-white">
                    <div className="font-bold text-lg">{status} ({tasks.length})</div>
                </div>
                <div className="overflow-scroll">
                    {tasks.map((task, index) => (
                        <TaskCard key={'to-do-' + index} task={task}></TaskCard>
                    ))}
                </div>
            </div>
        </>
    );
}
