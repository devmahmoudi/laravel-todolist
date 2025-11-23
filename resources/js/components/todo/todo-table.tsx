import CreateTodoDialog from '@/components/todo/create-todo-dialog';
import DeleteTodoConfirmationDialog from '@/components/todo/delete-todo-confirmation-dialog';
import EditTodoDialog from '@/components/todo/edit-todo-dialog';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dateFnsFormat } from '@/lib/utils';
import { Todo } from '@/types';
import { Link, router, useForm } from '@inertiajs/react';
import { Separator } from '@radix-ui/react-separator';
import { Plus } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Checkbox } from '../ui/checkbox';

interface TodoTableProps {
    todos: Todo[];
    groupId: number;
    parentId: number | undefined;
}

const TodoTable = ({ todos, groupId, parentId }: TodoTableProps) => {
    const [showCreateDialog, setShowCreateDialog] = useState(false);
    const [todoToDelete, setTodoToDelete] = useState(null);
    const [todoToEdit, setTodoToEdit] = useState(null);
    const [includeIncomplete, setIncludeIncomlete] = useState(false);

    const { patch, processing, errors } = useForm({});

    useEffect(() => {
        const url = new URL(window.location.href);
        const params = url.searchParams;

        if (includeIncomplete) params.set('completed', '1');
        else params.delete('completed');

        if (window.location.href != url.toString()) router.visit(url.toString(), { preserveState: true });
    }, [includeIncomplete]);

    /**
     * Sends request to server for toggle todo's completed status
     *
     * @param Todo todo
     */
    const handleTodoCompletedToggle = (todo: Todo) => {
        patch(route('todo.toggle-completed', todo.id));
    };

    return (
        <>
            {/* CREATE NEW TODO DIALOG */}
            <CreateTodoDialog open={showCreateDialog} setOpen={setShowCreateDialog} groupId={groupId} parentId={parentId} />

            {/* DELETE TODO CONFIRMATION DIALOG */}
            {todoToDelete ? (
                <DeleteTodoConfirmationDialog
                    todo={todoToDelete}
                    onConfirm={() => router.delete(route('todo.delete', todoToDelete.id))}
                    onClose={() => setTodoToDelete(null)}
                />
            ) : null}

            {/* EDIT TODO DIALOG */}
            {todoToEdit ? <EditTodoDialog todo={todoToEdit} onClose={() => setTodoToEdit(null)} /> : null}

            <div className="flex items-center space-x-2">
                <Checkbox
                    id="terms"
                    checked={includeIncomplete}
                    onClick={() => {
                        setIncludeIncomlete(!includeIncomplete);
                    }}
                />
                <Label htmlFor="terms">Include completed todos</Label>
            </div>

            <Table>
                <TableCaption>
                    <div className="grid gap-3">
                        {!todos.length && <p>There are no todos in this group</p>}
                        <Separator className="border-white" />
                        <div>
                            <Button className="cursor-pointer" size={'sm'} onClick={() => setShowCreateDialog(true)}>
                                <Plus />
                                <span>Create new todo</span>
                            </Button>
                        </div>
                    </div>
                </TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>{/* Completed status */}</TableHead>
                        <TableHead className="w-[100px]">Title</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Created At</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {todos.map((item) => (
                        <TableRow key={item.id}>
                            <TableCell className="cursor-pointer truncate font-medium">
                                <Checkbox
                                    className="cursor-pointer border-1 dark:border-white"
                                    onClick={() => handleTodoCompletedToggle(item)}
                                    checked={Boolean(item.completed_at)}
                                />
                            </TableCell>
                            <TableCell className="max-w-[200px] cursor-pointer truncate font-medium text-blue-500 underline hover:scale-105 transition-all">
                                <Link href={route('todo.show', item.id)}>
                                    {item.title}
                                </Link>
                            </TableCell>
                            <TableCell className="max-w-[400px] truncate">{item.description}</TableCell>
                            <TableCell>{dateFnsFormat(item.created_at, 'PPpp')}</TableCell>
                            <TableCell>
                                <Button size={'sm'} variant={'link'} className="cursor-pointer text-red-400" onClick={() => setTodoToDelete(item)}>
                                    Delete
                                </Button>
                                <Button size={'sm'} variant={'link'} className="cursor-pointer text-blue-400" onClick={() => setTodoToEdit(item)}>
                                    Edit
                                </Button>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </>
    );
};

export default TodoTable;
