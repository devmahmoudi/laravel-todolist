import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

interface Todo {
    id: number;
    title: string;
    description: string;
    group_id: number;
}

const TodoDetail = ({todo} : {todo: Todo}) => {

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `# Todo`,
            href: route('todo.show', todo.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
        <Head title={`${todo.title}`} />

        <h1>
            {todo.title}
        </h1>
        </AppLayout>
    )
}

export default TodoDetail;