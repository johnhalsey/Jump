import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, router} from '@inertiajs/react';
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useState} from "react"
import TextInput from "@/Components/TextInput.jsx"
import axios from "axios"
import Panel from "@/Components/Panel.jsx"

export default function Dashboard ({projects, default_statuses}) {

    const [loading, setLoading] = useState(false)
    const [projectName, setProjectName] = useState('')
    const [errorMessage, setErrorMessage] = useState(null)

    let tableRows = []

    projects.data.forEach((project, index) => {
        tableRows.push(
            <tr className={'hover:bg-sky-50 cursor-pointer'} onClick={() => {
                redirectToProject('/project/' + project.id)
            }}>
                <td>{project.owners[0].name}</td>
                <td>{project.name}</td>
                {projectDefaultStatuses(project).map((status, index) => (
                    <td key={'project-' + project.id + '-status-' + status.id}>
                        {status.count}
                    </td>
                ))}
            </tr>
        )
    })

    function projectDefaultStatuses (project) {
        return project.statuses.filter(status => default_statuses.includes(status.name))
    }

    function redirectToProject (url) {
        window.location = url
    }

    function createProject () {
        setLoading(true)
        axios.post('/api/projects', {
            name: projectName
        }).then(response => {
            router.reload()
            // no need to set loading back to false, as page has realoaded
        }).catch(error => {
            if (error.response.data.message) {
                setErrorMessage(error.response.data.message)
            }
            setLoading(false)
        })
    }

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Your Projects
                </h2>
            }
        >
            <Head title="Your Projects"/>

            <div className="grid gap-4 grid-cols-3 mx-5">
                <div className={'border p-5'}>Col 1</div>
                <Panel className={'col-span-2 pt-5'}>

                    <div className={'flex w-full mb-5 border-b border-dashed pb-5 px-5'}>
                        <div className={'flex-grow'}>
                            <TextInput placeholder={'Add new project here'}
                                       className={'w-full border-gray-300 shadow rounded'}
                                       value={projectName}
                                       onChange={e => setProjectName(e.target.value)}
                            ></TextInput>
                        </div>
                        <div className={'ml-5 content-stretch'}>
                            <PrimaryButton loading={loading}
                                           disabled={loading}
                                           className={'h-full'}
                                           onClick={createProject}
                            >Add</PrimaryButton>
                        </div>
                    </div>

                    <table>
                        <thead>
                        <tr>
                            <th>Owner</th>
                            <th>Project Name</th>
                            {default_statuses.map((status, index) => (
                                <th key={'default-status-' + index}>{status}</th>
                            ))}
                        </tr>
                        </thead>
                        <tbody>
                        {tableRows}
                        </tbody>
                    </table>
                </Panel>
            </div>

        </AuthenticatedLayout>
    );
}
