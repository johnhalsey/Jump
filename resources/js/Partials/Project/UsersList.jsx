import {Head, Link, router} from '@inertiajs/react';
import Panel from "@/Components/Panel.jsx"
import TextInput from "@/Components/TextInput.jsx"
import * as FormErrors from "@/Utils/FormErrors.js"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import axios from "axios"
import {useState} from "react"

export default function UsersList ({project}) {

    const [loading, setLoading] = useState(false)
    const [newEmail, setNewEmail] = useState('')

    function inviteUserToProject () {
        if (!project.user_can_update) {
            return
        }

        setLoading(true)
        FormErrors.resetErrors()

        axios.post('/api/project/' + project.id + '/invitations', {
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
        return project.owners.find(owner => owner.id == user.id) ? 'Administrator' : 'Editor';
    }

    function confirmRemoveUser (user) {
        if (!project.user_can_update) {
            return
        }

        if (!confirm('Are you sure you wish to remove ' + user.name + ' from this project')) {
            return
        }

        axios.delete('/api/project/' + project.id + '/user/' + user.id)
            .then(() => {
                router.reload()
            })
            .catch(error => {
                alert(error.response.data.errors.user[0])
            })
    }

    return (
        <>
            <div className={'mt-3 md:mt-0'}>
                <div className="mb-3 font-bold">
                    Project Users
                </div>

                <Panel className={'pt-5'}>

                    {project.user_can_update && <div className={'flex w-full mb-5 border-b border-dashed pb-5 px-5'}>
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
                            {project.user_can_update && <th></th>}
                        </tr>
                        </thead>
                        <tbody>
                        {project.invitations.map((invite, index) => (
                            <tr key={'project-invitation-' + index}>
                                <td className={'max-w-28 overflow-x-scroll'}>{invite.email}</td>
                                <td className={'hidden sm:table-cell'}>
                                    Invited
                                </td>
                                <td></td>
                            </tr>
                        ))}

                        {project.users.map((user, index) => (
                            <tr key={'project-user-' + index}>
                                <td className={'max-w-28 overflow-x-scroll'}>{user.email}</td>
                                <td className={'hidden sm:table-cell'}>
                                    {userRole(user)}
                                </td>
                                {project.user_can_update && <td className={'text-sm'}>
                                    <span className={'text-sky-600 cursor-pointer'}
                                          onClick={() => confirmRemoveUser(user)}
                                    >
                                        Remove
                                    </span>
                                </td>}
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </Panel>
            </div>
        </>
    );
}
