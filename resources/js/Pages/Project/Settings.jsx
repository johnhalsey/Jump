import {Head, Link, router, usePage} from '@inertiajs/react';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx"
import TextInput from "@/Components/TextInput.jsx"
import {useEffect, useState} from "react"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import axios from "axios"
import Panel from "@/Components/Panel.jsx"
import * as FormErrors from "@/Utils/FormErrors.js"
import FullPagePanel from "@/Components/FullPagePanel.jsx"
import UsersList from "@/Partials/Project/UsersList.jsx"

export default function ProjectSettings ({project}) {

    const [shortCode, setShortCode] = useState(project.data.short_code)
    const [projectName, setProjectName] = useState(project.data.name)
    const [loading, setLoading] = useState(false)

    function saveSettings () {
        if (!project.data.user_can_update) {
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
                        {project.data.user_can_update && <PrimaryButton loading={loading} onClick={saveSettings}>Save</PrimaryButton>}
                        {!project.data.user_can_update && <span>Only Administrators can make changes</span>}
                    </div>}>
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 lg:gap-10">

                        <div>

                            <div className="mb-3 font-bold">
                                Project Name
                            </div>
                            <TextInput className={'w-full'}
                                       value={projectName}
                                       onChange={(e) => setProjectName(e.target.value)}
                                       readOnly={!project.data.user_can_update}
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
                                       readOnly={!project.data.user_can_update}
                            >

                            </TextInput>
                            <div className={'text-sm mt-2 text-gray-500'}>
                                Changing this code will only affect new tasks created
                            </div>
                            {FormErrors.errorsHas('short_code') && <div className={'text-red-500'}>
                                {FormErrors.errorValue('short_code')}
                            </div>}

                        </div>

                        <UsersList project={project.data}></UsersList>

                    </div>
                </FullPagePanel>

            </AuthenticatedLayout>
        </>
    );
}
