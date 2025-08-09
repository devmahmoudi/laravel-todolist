import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import CreateTodoDialog from '@/components/todo/create-todo-dialog'
import TodoDetailDialog from '../../components/todo/todo-detail-dialog';
import DeleteTodoConfirmationDialog from '@/components/todo/delete-todo-confirmation-dialog';
import EditTodoDialog from '@/components/todo/edit-todo-dialog';
import TodoTable from '@/components/todo/todo-table';


const TodoIndex = () => {
    const { group, todos } = usePage().props
    const [showCreateDialog, setShowCreateDialog] = useState(false)
    const [showTodoDetail, setShowTodoDetail] = useState(false)
    const [todoToDelete, setTodoToDelete] = useState(null)
    const [todoToEdit, setTodoToEdit] = useState(null)

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

            {/* DELETE TODO CONFIRMATION DIALOG */}
            {todoToDelete ? <DeleteTodoConfirmationDialog todo={todoToDelete} onConfirm={() => router.delete(route('todo.delete', todoToDelete.id))} onClose={() => setTodoToDelete(null)} /> : null}

            {/* EDIT TODO DIALOG */}
            {todoToEdit ? <EditTodoDialog todo={todoToEdit} onClose={() => setTodoToEdit(null)} /> : null}

            {/* SHOW TODO DETAIL DIALOG  */}
            {showTodoDetail ? (<TodoDetailDialog todo={showTodoDetail} onClose={() => setShowTodoDetail(null)} />) : null}

            {/* TODOS TABLE */}
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <TodoTable todos={todos} onClickNewTodoButton={() => setShowCreateDialog(true)} onDeleteTodo={setTodoToDelete} onEditTodo={setTodoToEdit} onShowDetail={setShowTodoDetail}/>
            </div>
        </AppLayout>
    );
}

export default TodoIndex;