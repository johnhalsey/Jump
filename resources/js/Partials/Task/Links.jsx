import {useEffect, useState} from "react"
import {router} from '@inertiajs/react';
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import TextInput from "@/Components/TextInput.jsx"
import axios from "axios"
import * as FormErrors from "@/Utils/FormErrors.js"

export default function TaskLinks ({task}) {

    const [text, setText] = useState('')
    const [url, setUrl] = useState('')
    const [links, setLinks] = useState([])

    useEffect(() => {
        getLinks()
    }, []);

    function getLinks () {
        axios.get(route('api.project.task.links.index', [
            task.project.id,
            task.id,
        ]))
            .then(response => {
                setLinks(response.data.data)
            })
    }

    function addLink () {
        FormErrors.resetErrors()

        axios.post(route('api.project.task.links.store', [
            task.project.id,
            task.id,
        ]), {
            text: text,
            url: url
        })
            .then(response => {
                setText('')
                setUrl('')
                setLinks([...links, response.data.data])
            })
            .catch(error => {
                FormErrors.pushErrors(error.response.data.errors)
                router.reload()
            })
    }

    function deleteLink (link) {
        axios.delete(route('api.project.task.links.destroy', [
            task.project.id,
            task.id,
            link.id
        ]))
            .then(() => {
                getLinks()
            })
    }

    return (
        <>
            <div className="mb-3 mt-8 font-bold">
                Links
            </div>
            <div className={'grid frid-cols-1 md:grid-cols-3 gap-3'}>
                <div>
                    <TextInput value={text}
                               placeholder={'link text'}
                               className={'w-full'}
                               id={'new-name'}
                               onChange={(e) => setText(e.target.value)}
                    >

                    </TextInput>
                    {FormErrors.errorsHas('text') && <div className={'text-red-500'}>
                        {FormErrors.errorValue('text')}
                    </div>}
                </div>
                <div className={'md:col-span-2'}>
                    <TextInput value={url}
                               placeholder={'link url'}
                               className={'w-full'}
                               id={'new-url'}
                               onChange={(e) => setUrl(e.target.value)}
                    ></TextInput>
                    {FormErrors.errorsHas('url') && <div className={'text-red-500'}>
                        {FormErrors.errorValue('url')}
                    </div>}
                </div>
                <div className={'md:col-span-2'}>
                    <PrimaryButton onClick={addLink}>Add</PrimaryButton>
                </div>
            </div>
            <div className={'mt-3'}>
                {links.length > 0 && (
                    <ul>
                        {links.map((link, index) => (
                            <li key={'link-' + link.id}>
                                <span className={'cursor-pointer'}
                                      onClick={() => deleteLink(link)}>🗑</span> -
                                <a
                                    href={link.url}
                                    target="_blank"
                                    className={'ml-1 text-sky-600 hover:text-sky-800 cursor-pointer'}
                                >
                                    {link.text || link.url}
                                </a>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </>
    );
}
