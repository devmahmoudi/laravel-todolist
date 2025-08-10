import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import TodoTable from '@/components/todo/todo-table';


const TodoIndex = () => {
    const { group, todos } = usePage().props
    

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `# ${group.name}`,
            href: route('group.todo', group.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${group.name} todos`} />

            {/* TODOS TABLE */}
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <TodoTable todos={todos} groupId={group.id}/>
            </div>
        </AppLayout>
    );
}

export default TodoIndex;