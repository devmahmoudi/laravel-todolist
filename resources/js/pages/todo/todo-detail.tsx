import TodoTable from '@/components/todo/todo-table';
import { BreadcrumbEllipsis } from '@/components/ui/breadcrumb';
import AppLayout from '@/layouts/app-layout';
import { DropdownBreadcrumbItem, SimpleBreadcrumbItem, BreadcrumbItem, Todo } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Separator } from '@radix-ui/react-separator';

const TodoDetail = ({ todo, ancestors }: { todo: Todo, ancestors: Todo[] }) => {

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `# ${todo.group?.name || 'Group'}`,
            href: route('group.todo', todo.group?.id),
        }
    ];


    if (ancestors.length > 0) {
        // Add deeper ancestors as ellipsis item in the breadcrumb
        if (ancestors.length > 1) {
            const deeperAncestors = ancestors.slice(0, (ancestors.length - 1))

            breadcrumbs.push({
                trigger: <BreadcrumbEllipsis />,
                items: deeperAncestors.map((ancestor) => {
                    return {
                        title: ancestor.title,
                        href: route('todo.show', ancestor.id)
                    }

                })
            })
        }

        // add current todo's parent to the breadcrumb items
        const parent = ancestors.at(-1)
        breadcrumbs.push({
            title: parent.title,
            href: route('todo.show', parent.id)
        })
    }

    // add current todo as latest the breadcrumb item
    breadcrumbs.push({
        title: `${todo.title}`,
        href: route('todo.show', todo.id),
    })

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