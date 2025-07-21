import { SidebarGroup, SidebarGroupLabel, SidebarMenu } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Plus } from 'lucide-react';
import GroupsList from '@/components/group/groups-list'
import { useState } from 'react';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const [displayCreateGroupInput, setDisplayCreateGroupInput] = useState(false)

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel className='flex justify-between'>
                Groups
                <Plus className='cursor-pointer' onClick={() => setDisplayCreateGroupInput(!displayCreateGroupInput)}/>
            </SidebarGroupLabel>
            <SidebarMenu>
                <GroupsList displayCreateGroupInput={displayCreateGroupInput}/>
            </SidebarMenu>
        </SidebarGroup>
    );
}
