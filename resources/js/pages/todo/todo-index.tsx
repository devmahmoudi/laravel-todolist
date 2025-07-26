import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';




const TodoIndex = () => {
    const {group, todos} = usePage().props

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `#${group.name} Todo`,
            href: route('group.todo', group.id),
        },
    ];

    console.log(todos);
    

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
               
            </div>
        </AppLayout>
    );
}

export default TodoIndex;