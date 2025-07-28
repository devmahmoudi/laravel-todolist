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
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { Separator } from '@radix-ui/react-separator';

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
            <Head title={`${group.name} todos`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <Table>
                    <TableCaption>
                        <div className='grid gap-3'>
                            {!todos.length && (
                                <p>There are no todos in this group</p>
                            )}
                            <Separator className='border-white'/>
                            <div>
                                <Button size={'sm'}>
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