import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import {useEffect, useState} from 'react'
import axios from 'axios'
import ProjectStatusColumn from "@/Partials/ProjectStatusColumn.jsx"

export default function ShowProject ({project}) {

    const [loading, setLoading] = useState(true)
    const [tasks, setTasks] = useState([])

    useEffect(() => {
        getTasks()
    }, [])

    const getTasks = function () {
        axios.get('/api/project/' + project.data.id + '/tasks')
            .then(response => {
                setTasks(response.data.data)
                setLoading(false)
            })
    }

    const tasksByStatus = function (status) {
        return tasks.filter(task => task.status == status)
    }

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    {project.data.name}
                </h2>
            }
        >
            <Head title="Project"/>

            <div className="w-full mx-auto px-4 sm:px-6 lg:px-8">

                {!loading && <div className="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 lg:gap-10 pb-12">
                    <ProjectStatusColumn
                        status="To Do"
                        tasks={tasksByStatus('To Do')}
                    ></ProjectStatusColumn>

                    <ProjectStatusColumn
                        status="In Progress"
                        tasks={tasksByStatus('In Progress')}
                    ></ProjectStatusColumn>

                    <ProjectStatusColumn
                        status="Done"
                        tasks={tasksByStatus('Done')}
                    ></ProjectStatusColumn>
                </div>}

            </div>

        </AuthenticatedLayout>
    );
}
