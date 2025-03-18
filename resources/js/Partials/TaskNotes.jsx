import { usePage, router } from '@inertiajs/react'
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useState} from "react"
import axios from "axios"
import TaskNote from "@/Partials/TaskNote.jsx"

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
                setNote('')
                setScrollHeight(0)
                setLoading(false)
                router.reload()
            })
            .catch(error => {
                console.log(error.response)
            })
    }

    function updateNewNote (e) {
        setNote(e.target.value)
        setScrollHeight(e.target.scrollHeight)
    }

    return (
        <>
            <div className="mb-3 font-bold">
                Notes
            </div>
            <div>
                <div className="border-b border-dashed border-gray-300 pb-3">
                    <div className="">
                        <textarea
                            id="new-note-textarea"
                            className="p-3 flex grow bg-white rounded shadow w-full border border-gray-200"
                            style={{minHeight: scrollHeight + 'px'}}
                            placeholder="New note here"
                            value={note}
                            onChange={updateNewNote}
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
                    <TaskNote note={note} key={'note-' + index}></TaskNote>
                ))}
            </div>
        </>
    );
}
