import {Head, Link} from '@inertiajs/react';
import TaskCard from "@/Partials/TaskCard.jsx"

export default function ProjectStatusColumn ({status, tasks}) {

    return (
        <>
            <div className="border bg-white py-8 rounded-md shadow-md">
                <div className="px-8 pb-8 border-dashed border-b">
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
