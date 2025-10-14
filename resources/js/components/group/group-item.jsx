import EditGroup from '@/components/group/edit-group';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, router, usePage } from '@inertiajs/react';
import { Edit, EllipsisVertical, Hash, Trash } from 'lucide-react';
import { useRef, useState } from 'react';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { preventNavigate } from '@/lib/utils';
import { AlertDialogTrigger } from '@radix-ui/react-alert-dialog';

const GroupItem = ({ item, isActive }) => {
    const page = usePage();
    const [enableEditGroup, setEnableEditGroup] = useState(false);
    const deleteDialogTriggerRef = useRef();

    const handleEditIconClick = (e) => {
        setEnableEditGroup(true);
    };

    const handleDeleteItem = () => {
        router.delete(route('group.destroy', item.id));
    };

    return (
        <>
            <SidebarMenuItem key={item.id}>
                <SidebarMenuButton
                    className={`group text-gray-400 ${isActive ? 'shadow-md dark:bg-white text-black' : ''}`}
                    asChild
                    isActive={page.url.startsWith(item.id)}
                    tooltip={{ children: item.name }}
                >
                    <Link href={route('group.todo', item.id)} className="flex justify-between hover:[&>svg]:block">
                        <>
                            <span className="flex align-middle">
                                <Hash className="mr-2 w-4" />
                                {enableEditGroup ? (
                                    <EditGroup
                                        group={item}
                                        onSaved={() => {
                                            setEnableEditGroup(false);
                                        }}
                                    />
                                ) : (
                                    <>
                                        <span>{item.name}</span>
                                    </>
                                )}
                            </span>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <EllipsisVertical className="box-content h-4 w-4 py-1" />
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                    <DropdownMenuItem
                                        className="cursor-pointer"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            setTimeout(() => {
                                                handleEditIconClick(e);
                                            }, 200);
                                        }}
                                    >
                                        <Edit />
                                        Edit
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        className="cursor-pointer"
                                        onClick={(e) => {
                                            preventNavigate(e);
                                            setTimeout(() => {
                                                deleteDialogTriggerRef?.current.click();
                                            }, 200);
                                        }}
                                    >
                                        <Trash />
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>

            {/* BEGIN: Deletation Alert Dialog */}
            <AlertDialog>
                <AlertDialogTrigger ref={deleteDialogTriggerRef} />
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                        <AlertDialogDescription>
                            This action cannot be undone. This will permanently delete the group and its todos.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel className="cursor-pointer">Cancel</AlertDialogCancel>
                        <AlertDialogAction className="cursor-pointer" onClick={handleDeleteItem}>
                            Continue
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
            {/* END: Deletation Alert Dialog */}
        </>
    );
};

export default GroupItem;
