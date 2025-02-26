import { usePage } from '@inertiajs/react'
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useState} from "react"
import axios from "axios"
import {Document} from "postcss"

export default function TaskNotes ({notes}) {

    const [loading, setLoading] = useState(false)
    const [note, setNote] = useState('')
    const [scrollHeight, setScrollHeight] = useState(0)

    const { project, task } = usePage().props

    function addNote () {
        setLoading(true)
        axios.post('/api/project/'+project.data.id+'/task/'+task.data.id+'/notes', {
            note: note
        })
            .then(response => {
                notes.unshift(response.data.data)
                setNote('')
                setScrollHeight(0)
                setLoading(false)
            })
            .catch(error => {
                console.log(error.response)
            })
    }

    function updateNote (e) {
        setNote(e.target.value)
        setScrollHeight(e.target.scrollHeight)
    }

    return (
        <>
            <div className="mb-3 font-bold">
                Notes
            </div>
            <div>
                <div className="border-b border-dashed pb-3">
                    <div className="">
                        {/*{textareaLineHeight()}*/}

                        <textarea
                            id="new-note-textarea"
                            className="p-3 flex grow border rounded shadow w-full border border-gray-300"
                            style={{minHeight: scrollHeight + 'px'}}
                            placeholder="New note here"
                            value={note}
                            onChange={updateNote}
                        >
                        </textarea>
                    </div>
                    <div className="mt-3">
                    <PrimaryButton className=""
                                   disabled={loading}
                                   loading={loading}
                                   onClick={addNote}
                        >
                            Add
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <div className="mt-3 max-h-[1000px] overflow-y-scroll">
                {notes.map((note, index) => (
                    <div className="border rounded shadow mb-3 p-3 bg-white"
                         key={'task-note-' + index}
                    >
                        <div className="whitespace-pre-wrap">{note.note}</div>
                        <div className="text-sm text-right mt-5">
                            {note.user.name} - {note.date}
                        </div>
                    </div>
                ))}
            </div>
        </>
    );
}
