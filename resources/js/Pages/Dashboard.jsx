import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router, Link} from '@inertiajs/react';
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useState} from "react"
import TextInput from "@/Components/TextInput.jsx"
import axios from "axios"
import Panel from "@/Components/Panel.jsx"
import * as FormErrors from "@/Utils/FormErrors.js"
import ProjectStatusesDonutChart from "@/Partials/Project/ProjectStatusesDonutChart.jsx"
import ProjectAssigneesDonutChart from "@/Partials/Project/ProjectAssigneesDonutChart.jsx"

export default function Dashboard ({projects, default_statuses}) {

    const [loading, setLoading] = useState(false)
    const [projectName, setProjectName] = useState('')
    const [selectedProject, setSelectedProject] = useState(projects.data[0])

    let tableRows = []

    function projectStatusByName (project, statusName) {
        return project.statuses.find(status => status.name == statusName)
    }

    projects.data.forEach((project, index) => {
        tableRows.push(
            <tr className={selectedProject && selectedProject.id == project.id ? 'bg-sky-100' : '' + 'hover:bg-sky-50 cursor-pointer'}
                onClick={() => {
                    selectProject(project)
                }}
                key={'project-' + index}
            >
                <td className={'hidden md:block'}>{project.owners[0].full_name}</td>
                <td>{project.name}</td>
                {default_statuses.map((status, index) => (
                    <td key={'project-' + project.id + '-status-' + index}
                        className={'hidden md:table-cell'}
                    >
                        {projectStatusByName(project, status).tasks_count}
                    </td>
                ))}
                <td><Link href={'/project/' + project.id}
                          className={'text-sky-600 hover:text-sky-800 cursor-pointer'}>Manage</Link></td>
            </tr>
        )
    })

    function projectDefaultStatuses (project) {
        return project.statuses.filter(status => default_statuses.includes(status.name))
    }

    function selectProject (project) {
        setSelectedProject(project)
    }

    function redirectToProject (url) {
        router.visit(url)
    }

    function createProject () {
        setLoading(true)
        FormErrors.resetErrors()
        axios.post('/api/projects', {
            name: projectName
        }).then(response => {
            router.reload()
            setProjectName('')
            setLoading(false)
            // no need to set loading back to false, as page has realoaded
        }).catch(error => {
            FormErrors.pushErrors(error.response.data.errors)
            setLoading(false)
        })
    }

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl">
                    Your Projects
                </h2>
            }
        >
            <Head title="Your Projects"/>

            <div className="flex mx-1 sm:mx-3 md:mx-5">
                <div className={'hidden md:block md:w-80 lg:w-96'}>

                    {selectedProject &&
                        <ProjectStatusesDonutChart project={selectedProject}></ProjectStatusesDonutChart>}
                    {/*<ProjectAssigneesDonutChart project={selectedProject}></ProjectAssigneesDonutChart>*/}
                </div>
                <div className={'col-span-2 grow'}>
                    <Panel className={'pt-3 w-full'}>

                        <div className={'flex w-full mb-3 border-b border-dashed border-gray-300 pb-3 px-3'}>
                            <div className={'flex-grow'}>
                                <TextInput placeholder={'Add new project here'}
                                           className={'w-full border-gray-300 shadow rounded'}
                                           value={projectName}
                                           onChange={e => setProjectName(e.target.value)}
                                ></TextInput>
                                {FormErrors.errorsHas('name') && <div className={'text-red-500'}>
                                    {FormErrors.errorValue('name')}
                                </div>}
                            </div>
                            <div className={'ml-5 content-stretch'}>
                                <PrimaryButton loading={loading}
                                               disabled={loading}
                                               className={'h-full'}
                                               onClick={createProject}
                                >Add</PrimaryButton>
                            </div>
                        </div>

                        {projects.data.length > 0 && <table>
                            <thead>
                            <tr>
                                <th className={'hidden md:block'}>Owner</th>
                                <th>Project Name</th>
                                {default_statuses.map((status, index) => (
                                    <th key={'default-status-' + index}
                                        className={'hidden md:table-cell'}
                                    >{status}</th>
                                ))}
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            {tableRows}
                            </tbody>
                        </table>}
                    </Panel>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
