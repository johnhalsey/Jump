import {Head, Link, usePage} from '@inertiajs/react';
import {useState} from "react"
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import axios from "axios"
import Gravatar from "@/Partials/Task/Gravatar.jsx"
import Tooltip from "@/Components/Tooltip.jsx"

export default function TaskNote ({note, onDelete}) {

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

        axios.patch(route('api.project.task.notes.update', [
            project.data.id,
            task.data.id,
            note.id
        ]), {
            note: content
        })
            .then(response => {
                setEditing(false)
                setLoading(false)
            })
    }

    function deleteNote () {
        axios.delete(route('api.project.task.notes.destroy', [
            project.data.id,
            task.data.id,
            note.id
        ]))
            .then(() => {
                onDelete()
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
                        <div className={'flex justify-between mt-1'}>
                            <div>
                                <PrimaryButton loading={loading}
                                               disabled={loading}
                                               onClick={updateNote}
                                >
                                    Save
                                </PrimaryButton>
                                <span className={'ml-5 cursor-pointer text-sky-600 hover:text-sky-800'}
                                      onClick={() => setEditing(false)}
                                >Cancel</span>
                            </div>
                            <span className={'cursor-pointer'} onClick={() => deleteNote()}>🗑️</span>
                        </div>
                    </div>}
            </div>
        </>
    );
}
