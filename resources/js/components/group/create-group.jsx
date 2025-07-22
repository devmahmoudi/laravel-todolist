import { Input } from '@/components/ui/input';
import { Plus } from 'lucide-react';
import { useForm } from '@inertiajs/react';
import { useEffect, useRef } from "react"

const CreateGroup = ({onCreated}) => {
    const inputRef = useRef(null)
    const { data, setData, post } = useForm({
        name: '',
    });

    useEffect(() => {
        inputRef.current.focus()
    }, [])

    const handleSave = () => {
        post(route('group.store'), {
            onFinish: onCreated
        });
    }

    return (
        <div className='flex items-center px-2'>
            <Plus className='w-4 text-gray-400' />

            <Input
                className='border-none p-2'
                placeholder='Enter the name ...'
                ref={inputRef}
                value={data.name}
                onChange={e => setData('name', e.target.value)}
                onBlur={handleSave}
                onKeyDown={e => (e.key === 'Enter') && handleSave()} // save with press enter button
            />
        </div>
    )
}

export default CreateGroup;