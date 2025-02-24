import {Head, Link} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function ShowProjectTask ({task}) {

    return (
        <>
            <AuthenticatedLayout
                header={
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        {task.data.project.name}
                    </h2>
                }
            >

                <div className="mx-8 bg-white rounded-md border shadow">

                    <div className="p-8">
                        {task.data.title}
                    </div>

                </div>

            </AuthenticatedLayout>
        </>
    );
}
