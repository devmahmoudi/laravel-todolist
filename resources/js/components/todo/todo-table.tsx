import { Group, Todo } from '@/types';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table"
import { Separator } from '@radix-ui/react-separator';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { dateFnsFormat } from '@/lib/utils';
import { useState } from 'react';
import CreateTodoDialog from '@/components/todo/create-todo-dialog'
import DeleteTodoConfirmationDialog from '@/components/todo/delete-todo-confirmation-dialog';
import EditTodoDialog from '@/components/todo/edit-todo-dialog';
import { router } from '@inertiajs/react';

interface TodoTableProps {
    todos: Todo[];
    groupId: number;
    parentId: number|undefined;
}

const TodoTable = ({ todos, groupId, parentId }: TodoTableProps) => {
    const [showCreateDialog, setShowCreateDialog] = useState(false)
    const [todoToDelete, setTodoToDelete] = useState(null)
    const [todoToEdit, setTodoToEdit] = useState(null)

    return (
        <>
            {/* CREATE NEW TODO DIALOG */}
            <CreateTodoDialog open={showCreateDialog} setOpen={setShowCreateDialog} groupId={groupId} parentId={parentId}/>

            {/* DELETE TODO CONFIRMATION DIALOG */}
            {todoToDelete ? <DeleteTodoConfirmationDialog todo={todoToDelete} onConfirm={() => router.delete(route('todo.delete', todoToDelete.id))} onClose={() => setTodoToDelete(null)} /> : null}

            {/* EDIT TODO DIALOG */}
            {todoToEdit ? <EditTodoDialog todo={todoToEdit} onClose={() => setTodoToEdit(null)} /> : null}

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
                                <TableCell className="font-medium truncate max-w-[200px] cursor-pointer">
                                    <Link href={route('todo.show', item.id)}>
                                        {item.title}
                                    </Link>
                                </TableCell>
                                <TableCell className="truncate max-w-[400px]">{item.description}</TableCell>
                                <TableCell>{dateFnsFormat(item.created_at, 'PPpp')}</TableCell>
                                <TableCell>
                                    <Button size={'sm'} variant={'link'} className='text-red-400 cursor-pointer' onClick={() => setTodoToDelete(item)}>Delete</Button>
                                    <Button size={'sm'} variant={'link'} className='text-blue-400 cursor-pointer' onClick={() => setTodoToEdit(item)}>Edit</Button>
                                </TableCell>
                            </TableRow>
                        ))
                    }
                </TableBody>
            </Table>
        </>
    )
}

export default TodoTable;