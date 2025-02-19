import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function ShowProject(project) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    v{project.name}
                </h2>
            }
        >
            <Head title="Project" />


        </AuthenticatedLayout>
    );
}
