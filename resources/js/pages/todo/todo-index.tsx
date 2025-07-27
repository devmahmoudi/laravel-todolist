import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
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

const TodoIndex = () => {
    const { group, todos } = usePage().props

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `#${group.name} Todo`,
            href: route('group.todo', group.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <Table>
                    {!todos.length && (
                        <TableCaption>There are no todos in this group</TableCaption>
                    )}
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-[100px]">Title</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Created At</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {
                            todos.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="font-medium truncate max-w-[200px]">{item.title}</TableCell>
                                    <TableCell className="truncate max-w-[400px]">{item.description}</TableCell>
                                    <TableCell>{dateFnsFormat(item.created_at, 'PPpp')}</TableCell>
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