import TodoTable from '@/components/todo/todo-table';
import AppLayout from '@/layouts/app-layout';
import { Todo, type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Separator } from '@radix-ui/react-separator';

const TodoDetail = ({ todo, ancestors }: { todo: Todo, ancestors: Todo[] }) => {

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `# ${todo.group?.name || 'Group'}`,
            href: route('group.todo', todo.group?.id),
        }
    ].concat(ancestors.map(item => ({
        title: `${item.title}`,
        href: route('todo.show', item.id)
    }))).concat([{
        title: `${todo.title}`,
        href: route('todo.show', todo.id),
    }]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${todo.title}`} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">

                <h3 className='text-xl'>
                    {todo.title}
                </h3>

                <p>
                    {todo.description}
                </p>

                <div>
                    <h4 className="text-lg">Sub Todos</h4>
                    <TodoTable todos={todo.children} groupId={todo.group_id} parentId={todo.id} />
                </div>
            </div>
        </AppLayout>
    )
}

export default TodoDetail;