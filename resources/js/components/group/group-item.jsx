import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/react';
import { Hash, Plus, Edit } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { preventNavigate } from '@/lib/utils';
import EditGroup from '@/components/group/edit-group'


const GroupItem = ({ item }) => {
    const page = usePage();
    const [enableEditInput, setEnableEditInput] = useState(false)

    const handleEditIconClick = (e) => {
        preventNavigate(e)

        setEnableEditInput(true)
    }

    return (
        <SidebarMenuItem key={item.id}>
            <SidebarMenuButton className='text-gray-400 group' asChild isActive={page.url.startsWith(item.id)} tooltip={{ children: item.name }}>
                <Link href={item.id} prefetch className='flex justify-between hover:[&>svg]:block'>
                    <span className='flex align-middle'>
                        <Hash className='w-4 mr-2' />
                        {
                            enableEditInput ?
                                (
                                    <EditGroup value={item.name}/>
                                ) :
                                (
                                    <>
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