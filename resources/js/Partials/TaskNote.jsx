import {Head, Link, usePage} from '@inertiajs/react';
import {useState} from "react"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import axios from "axios"
import Gravatar from "@/Partials/Task/Gravatar.jsx"
import Tooltip from "@/Components/Tooltip.jsx"

export default function TaskNote ({note}) {

    const {project, task} = usePage().props

    const [editing, setEditing] = useState(false)
    const [loading, setLoading] = useState(false)
    const [content, setContent] = useState(note.note)
    const [scrollHeight, setScrollHeight] = useState(0)

    function editNote (e) {
        setScrollHeight(e.target.scrollHeight + 40)
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

    function setCursorPosition (e) {
        const input = e.target;
        const length = input.value.length;
        // put the cursor at the end of the note
        input.setSelectionRange(length, length);
        // scroll to the bottom in case the textarea is not quite big enough
        input.scrollTo(0, input.scrollHeight)
    }

    return (
        <>
        <div className={'flex'}>
            <Gravatar user={note.user}></Gravatar>

            {!editing &&
                <div className="border border-gray-200 rounded shadow mb-3 p-3 bg-white hover:bg-sky-50 w-full ml-3"
                     onClick={editNote}>
                    <div className="whitespace-pre-wrap text-gray-600">{content}</div>
                    <div className="text-sm text-gray-600 text-right mt-5">
                        {note.date}
                    </div>
                </div>
            }

            {editing &&
                <div className="mb-3 w-full ml-3">
                <textarea
                    className="w-full border border-gray-200 p-3 text-gray-600 rounded shadow bg-white"
                    value={content}
                    autoFocus
                    onFocus={setCursorPosition}
                    style={{minHeight: scrollHeight + 'px'}}
                    onChange={updateContent}></textarea>
                    <PrimaryButton loading={loading}
                                   disabled={loading}
                                   onClick={updateNote}
                    >
                        Save
                    </PrimaryButton>
                </div>}
        </div>
        </>
    );
}
