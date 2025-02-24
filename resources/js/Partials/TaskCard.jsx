import {Head, Link} from '@inertiajs/react';

export default function TaskCard ({task}) {

    return (
        <>
            <div className="m-3 p-3 border rounded shadow">{task.title}</div>
        </>
    );
}
