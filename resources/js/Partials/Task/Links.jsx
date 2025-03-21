import {Head, Link} from '@inertiajs/react';
import {useEffect, useState} from "react"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import TextInput from "@/Components/TextInput.jsx"
import axios from "axios"
import FormErrors from "@/Utils/FormErrors.js"

export default function TaskLinks ({task}) {

    const [text, setText] = useState('')
    const [url, setUrl] = useState('')
    const [links, setLinks] = useState([])
    const [loading, setLoading] = useState([])

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
                </div>
                <div className={'col-span-2'}>
                    <TextInput value={url}
                               placeholder={'link url'}
                               className={'w-full'}
                               id={'new-url'}
                               onChange={(e) => setUrl(e.target.value)}
                    ></TextInput>
                </div>
                <div className={'md:col-span-2'}>
                    <PrimaryButton onClick={addLink}>Add</PrimaryButton>
                </div>
            </div>
            <div className={'mt-3'}>
                {links.map((link, index) => (
                    <a key={'link-' + link.id}
                          href={link.url}
                          target="_blank"
                          className={'text-sky-600 hover:text-sky-800 cursor-pointer block'}
                    >
                        {link.text || link.url}
                    </a>
                ))}
            </div>
        </>
    );
}
