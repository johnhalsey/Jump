import {Head, Link} from '@inertiajs/react';

export default function TaskCard ({task}) {

    return (
        <>
            <Link href={'/projects/' + task.project.id + '/task/' + task.id}>
                <div className="m-3 p-3 border rounded shadow bg-white hover:bg-blue-50">

                    <div>
                        {task.title}
                    </div>
                    <div className="mt-5">
                        {task.reference}
                    </div>
                </div>
            </Link>

        </>
    );
}
