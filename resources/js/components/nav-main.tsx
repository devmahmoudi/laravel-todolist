import { SidebarGroup, SidebarGroupLabel, SidebarMenu } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Plus } from 'lucide-react';
import GroupsList from '@/components/group/groups-list'

export function NavMain({ items = [] }: { items: NavItem[] }) {
    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel className='flex justify-between'>
                Groups
                <Plus className='cursor-pointer' />
            </SidebarGroupLabel>
            <SidebarMenu>
                <GroupsList/>
            </SidebarMenu>
        </SidebarGroup>
    );
}
