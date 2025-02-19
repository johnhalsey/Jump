import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function ShowProject({project}) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    {project.data.name}
                </h2>
            }
        >
            <Head title="Project" />

        <div className="container mx-auto px-4 sm:px-6 lg:px-8">

            <div className="grid grid-cols-3 gap-12 min-h-screen">
                <div className="border bg-white p-8 rounded-md shadow-md">To Do</div>
                <div className="border bg-white p-8 rounded-md shadow-md">In Progress</div>
                <div className="border bg-white p-8 rounded-md shadow-md">Done</div>
            </div>

        </div>


        </AuthenticatedLayout>
    );
}
