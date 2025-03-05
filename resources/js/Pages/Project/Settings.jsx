import {Head, Link, router} from '@inertiajs/react';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx"
import TextInput from "@/Components/TextInput.jsx"
import {useState} from "react"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import axios from "axios"
import Panel from "@/Components/Panel.jsx"
import * as FormErrors from "@/Utils/FormErrors.js"

export default function ProjectSettings ({project}) {

    const [shortCode, setShortCode] = useState(project.data.short_code)
    const [projectName, setProjectName] = useState(project.data.name)
    const [newProjectName, setNewProjectName] = useState('')
    const [loading, setLoading] = useState(false)

    function saveSettings () {
        setLoading(true)
        FormErrors.resetErrors()

        axios.patch('/api/project/' + project.data.id + '/settings', {
            name: projectName,
            short_code: shortCode
        })
            .then(response => {
                router.reload()
                setLoading(false)
            })
            .catch(error => {
                FormErrors.pushErrors(error.response.data.errors)
                setLoading(false)
            })
    }

    function inviteUserToProject () {

    }

    function userRole (user) {
        return project.data.owners.find(owner => owner.id == user.id) ? 'Administrator' : 'Editor';
    }

    function confirmRemoveUser (user) {
        if (!confirm('Are you sure you wish to remove ' + user.name + ' from this project')) {
            return
        }

        axios.delete('/api/project/' + project.data.id + '/user/' + user.id)
            .then(() => {
                router.reload()
            })
            .catch(error => {
                alert(error.response.data.errors.user[0])
            })
    }

    return (
        <>
            <AuthenticatedLayout
                header={
                    <div className={'flex justify-between'}>

                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            <Link href={'/project/' + project.data.id}>
                                {project.data.name}
                            </Link>
                        </h2>
                        <div>
                            <Link href={'/project/' + project.data.id + '/settings'}
                                  className={'text-sky-600 hover:text-sky-800'}
                            >
                                Settings
                            </Link>
                        </div>
                    </div>
                }
            >
                <Head title={project.data.name + ' Settings'}/>

                <div className="mx-4 md:mx-8 bg-white rounded-md border shadow">
                    <div className="p-8 border-b border-dashed flex justify-between">
                        <span className={'font-bold'}>Settings</span>
                        <PrimaryButton onClick={saveSettings}>Save</PrimaryButton>
                    </div>

                    <div className="p-8 bg-gray-50 rounded-b-md">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-10">

                            <div>

                                <div className="mb-3 font-bold">
                                    Project Name
                                </div>
                                <TextInput className={'w-full'}
                                           value={projectName}
                                           onChange={(e) => setProjectName(e.target.value)}
                                >
                                </TextInput>
                                {FormErrors.errorsHas('name') && <div className={'text-red-500'}>
                                    {FormErrors.errorValue('name')}
                                </div>}

                                <div className="mb-3 font-bold mt-5">
                                    Short Code
                                </div>

                                <TextInput className={'w-full'}
                                           value={shortCode}
                                           onChange={(e) => setShortCode(e.target.value)}
                                >

                                </TextInput>
                                <div className={'text-sm mt-2 text-gray-500'}>
                                    Changing this code will only affect new tasks created
                                </div>
                                {FormErrors.errorsHas('short_code') && <div className={'text-red-500'}>
                                    {FormErrors.errorValue('short_code')}
                                </div>}

                            </div>

                            <div className={'mt-3 md:mt-0'}>
                                <div className="mb-3 font-bold">
                                    Project Users
                                </div>

                                <Panel className={'pt-5'}>

                                    <div className={'flex w-full mb-5 border-b border-dashed pb-5 px-5'}>
                                        <div className={'flex-grow'}>
                                            <TextInput placeholder={'Add project user email here'}
                                                       className={'w-full border-gray-300 shadow rounded'}
                                                       value={newProjectName}
                                                       onChange={e => setNewProjectName(e.target.value)}
                                            ></TextInput>
                                        </div>
                                        <div className={'ml-5 content-stretch'}>
                                            <PrimaryButton loading={loading}
                                                           disabled={loading}
                                                           className={'h-full'}
                                                           onClick={inviteUserToProject}
                                            >Add</PrimaryButton>
                                        </div>
                                    </div>

                                    <table>
                                        <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Permissions</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {project.data.users.map((user, index) => (
                                            <tr key={'project-user-' + index}>
                                                <td>{user.email}</td>
                                                <td>
                                                    {userRole(user)}
                                                </td>
                                                <td className={'text-sm'}>
                                                    <span className={'text-sky-600 cursor-pointer'}
                                                          onClick={() => confirmRemoveUser(user)}
                                                    >Remove</span></td>
                                            </tr>
                                        ))}
                                        </tbody>
                                    </table>
                                </Panel>
                            </div>
                        </div>
                    </div>
                </div>

            </AuthenticatedLayout>
        </>
    );
}
