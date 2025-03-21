import {usePage, router} from '@inertiajs/react'
import PrimaryButton from "@/Components/PrimaryButton.jsx"
import {useEffect, useRef, useState} from "react"
import axios from "axios"
import TaskNote from "@/Partials/TaskNote.jsx"
import LoadingSpinner from "@/Components/LoadingSpinner.jsx"

export default function TaskNotes () {

    const [loading, setLoading] = useState(true)
    const [note, setNote] = useState('')
    const [scrollHeight, setScrollHeight] = useState(0)
    const [notes, setNotes] = useState([])
    const [pagination, setPagination] = useState({})

    const {project, task} = usePage().props

    const firstUpdate = useRef(true);

    useEffect(() => {

        // fire on ever render

        if (firstUpdate.current) {
            // only fire on first load
            getNotes()
            firstUpdate.current = false;
            return;
        }
    }, []);

    function getNotes (page = 1) {
        setLoading(true)

        if (page == 1) {
            setNotes([])
        }

        let params = {
            project: project.data.id,
            projectTask: task.data.id,
            _query: {
                page: page,
            }
        }

        axios.get(route('api.project.task.notes.index', params))
            .then(response => {
                setNotes([...notes, ...response.data.data])
                setPagination(response.data.meta)
                setLoading(false)
            })
    }

    function addNote () {
        setLoading(true)
        axios.post(route('api.project.task.notes.store', [
            project.data.id,
            task.data.id
        ]), {
            note: note
        })
            .then(response => {
                setNotes([response.data.data, ...notes])
                setNote('')
                setScrollHeight(0)
                setLoading(false)
            })
            .catch(error => {
                console.log(error.response)
            })
    }

    function removeNote (deletedNote) {
        let filtered = notes.filter(note => note.id !== deletedNote.id)
        setNotes(filtered)
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

            <div className="mt-3">
                {notes.map((note, index) => (
                    <TaskNote note={note}
                              key={'note-' + note.id}
                              onDelete={() => removeNote(note)}
                    ></TaskNote>
                ))}
            </div>

            {!loading && pagination.current_page < pagination.last_page &&
                <div className={'text-center'}>
                    <PrimaryButton onClick={() => getNotes(pagination.current_page+1)}>Load more...</PrimaryButton>
                </div>
            }

            {loading &&
                <div className={'text-center'}>
                    <LoadingSpinner></LoadingSpinner>
                </div>
            }
        </>
    );
}
