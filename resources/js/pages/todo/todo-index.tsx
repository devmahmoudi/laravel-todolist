import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table"
import { dateFnsFormat } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { Separator } from '@radix-ui/react-separator';
import { useState } from 'react';
import CreateTodoDialog from '@/components/todo/create-todo-dialog'
import TodoDetailDialog from '../../components/todo/todo-detail-dialog';
import DeleteTodoConfirmationDialog from '@/components/todo/delete-todo-confirmation-dialog';


const TodoIndex = () => {
    const { group, todos } = usePage().props
    const [showCreateDialog, setShowCreateDialog] = useState(false)
    const [showTodoDetail, setShowTodoDetail] = useState(false)
    const [todoToDelete, setTodoToDelete] = useState(null)

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `#${group.name} Todo`,
            href: route('group.todo', group.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${group.name} todos`} />

            {/* CREATE NEW TODO DIALOG */}
            <CreateTodoDialog open={showCreateDialog} setOpen={setShowCreateDialog} groupId={group.id} />

            {todoToDelete ? <DeleteTodoConfirmationDialog todo={todoToDelete} onConfirm={() => router.delete(route('todo.delete', todoToDelete.id))} onClose={() => setTodoToDelete(null)}/> : null}

            {/* SHOW TODO DETAIL DIALOG  */}
            {showTodoDetail ? (<TodoDetailDialog todo={showTodoDetail} onClose={() => setShowTodoDetail(null)} />) : null}

            {/* TODOS TABLE */}
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <Table>
                    <TableCaption>
                        <div className='grid gap-3'>
                            {!todos.length && (
                                <p>There are no todos in this group</p>
                            )}
                            <Separator className='border-white' />
                            <div>
                                <Button className='cursor-pointer' size={'sm'} onClick={() => setShowCreateDialog(true)}>
                                    <Plus />
                                    <span>Create new todo</span>
                                </Button>
                            </div>
                        </div>
                    </TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-[100px]">Title</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {
                            todos.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="font-medium truncate max-w-[200px] cursor-pointer" onClick={() => setShowTodoDetail(item)}>{item.title}</TableCell>
                                    <TableCell className="truncate max-w-[400px]">{item.description}</TableCell>
                                    <TableCell>{dateFnsFormat(item.created_at, 'PPpp')}</TableCell>
                                    <TableCell>
                                        <Button size={'sm'} variant={'link'} className='text-red-400 cursor-pointer' onClick={() => setTodoToDelete(item)}>Delete</Button>
                                    </TableCell>
                                </TableRow>
                            ))
                        }
                    </TableBody>
                </Table>
            </div>
        </AppLayout>
    );
}

export default TodoIndex;