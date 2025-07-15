import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Hash, Plus } from 'lucide-react';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    const {groups} = page.props


    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel className='flex justify-between'>
                Groups
                <Plus className='cursor-pointer'/>
            </SidebarGroupLabel>
            <SidebarMenu>
                {groups.map((item) => (
                    <SidebarMenuItem key={item.id}>
                        <SidebarMenuButton className='text-gray-400' asChild isActive={page.url.startsWith(item.id)} tooltip={{ children: item.name }}>
                            <Link href={item.id} prefetch>
                                <Hash/>
                                <span>{item.name}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
