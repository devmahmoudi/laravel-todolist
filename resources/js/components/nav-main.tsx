import { SidebarGroup, SidebarGroupLabel, SidebarMenu } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import GroupItem from '@/components/group/group-item'

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    const { groups } = page.props


    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel className='flex justify-between'>
                Groups
                <Plus className='cursor-pointer' />
            </SidebarGroupLabel>
            <SidebarMenu>
                {groups.map((item) => (
                    <GroupItem item={item}/>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
