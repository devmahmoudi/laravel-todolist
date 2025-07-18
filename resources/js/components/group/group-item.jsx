import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Input } from '@/components/ui/input';
import { Link, usePage } from '@inertiajs/react';
import { Hash, Plus, Edit } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { preventNavigate } from '@/lib/utils';


const GroupItem = ({ item }) => {
    const page = usePage();
    const [enableEditInput, setEnableEditInput] = useState(false)
    const inputRef = useRef(null)

    const handleEditIconClick = (e) => {
        preventNavigate(e)
        
        setEnableEditInput(true)
    }

    useEffect(() => {
        if(enableEditInput)
            inputRef.current.focus()
    }, [enableEditInput])

    return (
        <SidebarMenuItem key={item.id}>
            <SidebarMenuButton className='text-gray-400 group' asChild isActive={page.url.startsWith(item.id)} tooltip={{ children: item.name }}>
                <Link href={item.id} prefetch className='flex justify-between hover:[&>svg]:block'>
                    <span className='flex'>
                        {
                            enableEditInput ?
                                (
                                    <Input ref={inputRef} value={item.name} onFocus={(e) => preventNavigate(e)} onClick={(e) => preventNavigate(e)}/>
                                ) :
                                (
                                    <>
                                        <Hash className='w-4 mr-2' />
                                        <span>{item.name}</span>
                                    </>
                                )
                        }
                    </span>
                    <Edit className='justify-self-end hidden py-3 pl-2 box-content' onClick={handleEditIconClick} />
                </Link>
            </SidebarMenuButton>
        </SidebarMenuItem>
    )
}

export default GroupItem;