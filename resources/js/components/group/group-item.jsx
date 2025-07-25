import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, router, usePage } from '@inertiajs/react';
import { Hash, EllipsisVertical, Plus, Edit, Trash } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { preventNavigate } from '@/lib/utils';
import EditGroup from '@/components/group/edit-group'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"


const GroupItem = ({ item }) => {
    const page = usePage();
    const [enableEditGroup, setEnableEditGroup] = useState(false)

    const handleEditIconClick = (e) => {
        setEnableEditGroup(true)
    }

    const handleDeleteItem = () => {
        router.delete(route('group.destroy', item.id))
    }

    return (
        <SidebarMenuItem key={item.id}>
            <SidebarMenuButton className='text-gray-400 group' asChild isActive={page.url.startsWith(item.id)} tooltip={{ children: item.name }}>
                <div className='flex justify-between hover:[&>svg]:block'>
                    <>
                        <span className='flex align-middle'>
                            <Hash className='w-4 mr-2' />
                            {
                                enableEditGroup ?
                                    (
                                        <EditGroup group={item} onSaved={() => {
                                            setEnableEditGroup(false)
                                        }} />
                                    ) :
                                    (
                                        <>
                                            <span>{item.name}</span>
                                        </>
                                    )
                            }
                        </span>
                        <DropdownMenu>
                            <DropdownMenuTrigger>
                                <EllipsisVertical className='w-4 h-4 py-1 box-content' />
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <DropdownMenuItem
                                    className='cursor-pointer'
                                    onClick={(e) => {
                                        setTimeout(() => {
                                            handleEditIconClick(e);
                                        }, 200);
                                    }}
                                >
                                    <Edit />
                                    Edit
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    className='cursor-pointer'
                                    onClick={handleDeleteItem}
                                >
                                    <Trash />
                                    Delete
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu></>
                </div>
            </SidebarMenuButton>
        </SidebarMenuItem>
    )
}

export default GroupItem;