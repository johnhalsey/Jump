import {Head, Link, usePage} from '@inertiajs/react';
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useState} from "react"
import axios from "axios"

export default function TaskDescription ({description}) {

    const [editing, setEditing] = useState(false)
    const [loading, setLoading] = useState(false)
    const [content, setContent] = useState(description ?? '')
    const [scrollHeight, setScrollHeight] = useState(0)

    const {project, task} = usePage().props

    function edit (e) {
        setEditing(true)
        setScrollHeight(e.target.scrollHeight)
    }

    function cancelEdit () {
        setEditing(false)
    }

    function updateContent (e) {
        setContent(e.target.value)
        setScrollHeight(e.target.scrollHeight)
    }

    function setCursorPosition (e) {
        const input = e.target;
        const length = input.value.length;
        // put the cursor at the end of the note
        input.setSelectionRange(length, length);
        // scroll to the bottom in case the textarea is not quite big enough
        input.scrollTo(0, input.scrollHeight)
    }

    function updateTaskDescription () {
        setLoading(true)

        axios.patch('/api/project/' + project.data.id + '/task/' + task.data.id, {
            'description': content
        })
            .then(response => {
                setEditing(false)
                setLoading(false)
            })
            .catch(error => {
                console.log('error updating description')
                console.log(error)
            })
    }

    return (
        <>
            <div className="mb-3 font-bold">
                Description
            </div>
            {!editing &&
                <div
                    className="min-h-20 bg-white hover:bg-sky-50 p-3 rounded border border-gray-200 shadow whitespace-pre-wrap cursor-pointer"
                    onClick={edit}
                >
                    {content}
                </div>
            }

            {editing && <>
                <textarea
                    className="w-full border bg-white border-gray-200 rounded shadow p-3 focus:border-sky-300 focus-visible:border-sky-300!"
                    autoFocus
                    onFocus={setCursorPosition}
                    style={{minHeight: scrollHeight + 'px'}}
                    value={content}
                    onChange={updateContent}></textarea>
                <PrimaryButton loading={loading}
                               disabled={loading}
                               onClick={updateTaskDescription}
                >
                    Save
                </PrimaryButton>
                <span onClick={cancelEdit} className={'text-sky-600 cursor-pointer ml-5'}>Cancel</span>
            </>}
        </>
    );
}
