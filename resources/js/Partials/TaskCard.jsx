import {Head, Link} from '@inertiajs/react';
import TaskContext from "@/Partials/TaskContext.jsx"

export default function TaskCard ({task}) {

    return (
        <>
            <Link href={'/project/' + task.project.id + '/task/' + task.id}>
                <div className="m-3 p-3 border rounded shadow bg-white hover:bg-sky-50">

                    <div>
                        {task.title}
                    </div>
                    <div className="mt-5 flex justify-between">
                        {task.reference}
                        <TaskContext task={task}></TaskContext>
                    </div>
                </div>
            </Link>
        </>
    );
}
