import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { router, usePage } from '@inertiajs/react';
import { Hash, EllipsisVertical, Edit, Trash } from 'lucide-react';
import { useState } from 'react';
import EditGroup from '@/components/group/edit-group'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog"


const GroupItem = ({ item }) => {
    const page = usePage();
    const [enableEditGroup, setEnableEditGroup] = useState(false)
    const [displayDeleteDialog, setDisplayDeleteDialog] = useState(false)

    const handleEditIconClick = (e) => {
        setEnableEditGroup(true)
    }

    const handleDeleteItem = () => {
        router.delete(route('group.destroy', item.id))
    }

    return (
        <>
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
                                        onClick={(e) => {
                                            setTimeout(() => {
                                                setDisplayDeleteDialog(true)
                                            }, 200);
                                        }}
                                    >
                                        <Trash />
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu></>
                    </div>
                </SidebarMenuButton>
            </SidebarMenuItem>

            {/* BEGIN: Deletation Alert Dialog */}
            <AlertDialog open={displayDeleteDialog}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                        <AlertDialogDescription>
                            This action cannot be undone. This will permanently delete the group and its todos.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel className='cursor-pointer' onClick={() => setDisplayDeleteDialog(false)}>Cancel</AlertDialogCancel>
                        <AlertDialogAction className='cursor-pointer' onClick={handleDeleteItem}>Continue</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
            {/* END: Deletation Alert Dialog */}
        </>

    )
}

export default GroupItem;