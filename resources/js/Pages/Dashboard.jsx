import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({projects}) {

    let projectsList = [];

    projects.data.forEach((project, index) => {
        projectsList.push(<div className="py-12" key={index}>
            <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div className="p-6 text-gray-900">
                        {project.name}
                    </div>
                </div>
            </div>
        </div>)
    })

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            {projectsList}

        </AuthenticatedLayout>
    );
}
