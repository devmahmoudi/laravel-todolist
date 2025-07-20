import { Input } from '@/components/ui/input';
import { useEffect, useState } from "react"
import { useRef } from "react"

const EditGroup = ({ value }) => {
    const inputRef = useRef(null)
    const [name, setName] = useState(value)

    useEffect(() => {
        inputRef.current.focus()
    }, [])

    const handleChange = (e) => setName(e.target.value)

    return (
        <Input className='border-0 px-0 h-[23px] focus:shadow-none' ref={inputRef} value={name} onFocus={(e) => preventNavigate(e)} onClick={(e) => preventNavigate(e)} onChange={handleChange} />
    )
}

export default EditGroup;