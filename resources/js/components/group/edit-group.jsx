import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/react';
import { useEffect, useRef } from "react"

const EditGroup = ({ group, onSaved }) => {
    const inputRef = useRef(null)
    const { data, setData, patch } = useForm({
        name: group.name,
    });

    useEffect(() => {
        inputRef.current.focus()
    }, [])

    const handleSave = () => {
        patch(route('group.update', group.id), {
            onFinish: onSaved
        });
    }

    return (
        <Input
            className='border-0 px-0 h-[23px] focus:shadow-none'
            ref={inputRef}
            value={data.name}
            onChange={e => setData('name', e.target.value)}
            onBlur={handleSave}
            onKeyDown={e => (e.key === 'Enter') && handleSave()} // save with press enter button
        />
    )
}

export default EditGroup;