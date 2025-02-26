import {Head, Link, usePage} from '@inertiajs/react';
import {useState} from "react"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import axios from "axios"

export default function TaskNote ({note}) {

    const {project, task} = usePage().props

    const [editing, setEditing] = useState(false)
    const [loading, setLoading] = useState(false)
    const [content, setContent] = useState(note.note)
    const [scrollHeight, setScrollHeight] = useState(0)

    function editNote () {
        setEditing(true)
    }

    function updateContent (e) {
        setContent(e.target.value)
        setScrollHeight(e.target.scrollHeight)
    }

    function updateNote () {
        setLoading(true)

        axios.patch('/api/project/' + project.data.id + '/task/' + task.data.id + '/note/' + note.id, {
            note: content
        })
            .then(response => {
                setEditing(false)
                setLoading(false)
            })
    }

    return (
        <>
            {!editing && <div className="border rounded shadow mb-3 p-3 bg-white hover:bg-sky-50"
                              onClick={editNote}
            >
                <div className="whitespace-pre-wrap">{content}</div>
                <div className="text-sm text-right mt-5">
                    {note.user.name} - {note.date}
                </div>
            </div>}

            {editing && <div className="mb-3">
                <textarea
                    className="w-full border-gray-400 rounded shadow"
                    value={content}
                    style={{minHeight: scrollHeight + 'px'}}
                    onLoadStart={updateContent}
                    onChange={updateContent}></textarea>
                <PrimaryButton loading={loading}
                               disabled={loading}
                               onClick={updateNote}
                >
                    Save
                </PrimaryButton>
            </div>}

        </>
    );
}
