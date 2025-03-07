import {Head, Link, router, usePage} from '@inertiajs/react';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx"
import TextInput from "@/Components/TextInput.jsx"
import {useState} from "react"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import axios from "axios"
import Panel from "@/Components/Panel.jsx"
import * as FormErrors from "@/Utils/FormErrors.js"
import FullPagePanel from "@/Components/FullPagePanel.jsx"

export default function ProjectSettings ({project}) {

    const [shortCode, setShortCode] = useState(project.data.short_code)
    const [projectName, setProjectName] = useState(project.data.name)
    const [loading, setLoading] = useState(false)
    const [newEmail, setNewEmail] = useState('')

    const {user_can_update_project} = usePage().props

    function saveSettings () {
        if (!user_can_update_project) {
            return
        }

        setLoading(true)
        FormErrors.resetErrors()

        axios.patch('/api/project/' + project.data.id + '/settings', {
            name: projectName,
            short_code: shortCode
        })
            .then(() => {
                router.reload()
                setLoading(false)
            })
            .catch(error => {
                FormErrors.pushErrors(error.response.data.errors)
                setLoading(false)
            })
    }

    function inviteUserToProject () {
        if (!user_can_update_project) {
            return
        }

        setLoading(true)
        FormErrors.resetErrors()

        axios.post('/api/project/' + project.data.id + '/invitations', {
            'email': newEmail
        })
            .then(() => {
                router.reload()
                setLoading(false)
            })
            .catch(error => {
                FormErrors.pushErrors(error.response.data.errors)
                setLoading(false)
            })
    }

    function userRole (user) {
        return project.data.owners.find(owner => owner.id == user.id) ? 'Administrator' : 'Editor';
    }

    function confirmRemoveUser (user) {
        if (!user_can_update_project) {
            return
        }

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

                <FullPagePanel title={
                    <div className={'flex justify-between'}>
                        <span className="font-bold">Settings</span>
                        {user_can_update_project && <PrimaryButton onClick={saveSettings}>Save</PrimaryButton>}
                        {!user_can_update_project && <span>Only Administrators can make changes</span>}
                    </div>}>
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 lg:gap-10">

                        <div>

                            <div className="mb-3 font-bold">
                                Project Name
                            </div>
                            <TextInput className={'w-full'}
                                       value={projectName}
                                       onChange={(e) => setProjectName(e.target.value)}
                                       readOnly={!user_can_update_project}
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
                                       readOnly={!user_can_update_project}
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

                                {user_can_update_project && <div className={'flex w-full mb-5 border-b border-dashed pb-5 px-5'}>
                                    <div className={'flex-grow'}>
                                        <TextInput placeholder={'Add project user email here'}
                                                   className={'w-full border-gray-300 shadow rounded'}
                                                   value={newEmail}
                                                   onChange={e => setNewEmail(e.target.value)}
                                        ></TextInput>
                                        {FormErrors.errorsHas('email') && <div className={'text-red-500'}>
                                            {FormErrors.errorValue('email')}
                                        </div>}
                                    </div>
                                    <div className={'ml-5 content-stretch'}>
                                        <PrimaryButton loading={loading}
                                                       disabled={loading}
                                                       className={'h-full'}
                                                       onClick={inviteUserToProject}
                                        >Add</PrimaryButton>
                                    </div>
                                </div>}

                                <table>
                                    <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th className={'hidden sm:table-cell'}>Permissions</th>
                                        {user_can_update_project && <th></th>}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {project.data.users.map((user, index) => (
                                        <tr key={'project-user-' + index}>
                                            <td className={'max-w-28 overflow-x-scroll'}>{user.email}</td>
                                            <td className={'hidden sm:table-cell'}>
                                                {userRole(user)}
                                            </td>
                                            {user_can_update_project && <td className={'text-sm'}>
                                                <span className={'text-sky-600 cursor-pointer'}
                                                      onClick={() => confirmRemoveUser(user)}
                                                >Remove</span>
                                            </td>}
                                        </tr>
                                    ))}
                                    </tbody>
                                </table>
                            </Panel>
                        </div>
                    </div>
                </FullPagePanel>

            </AuthenticatedLayout>
        </>
    );
}
