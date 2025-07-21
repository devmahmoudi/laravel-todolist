import { usePage } from '@inertiajs/react';
import GroupItem from '@/components/group/group-item'


const GroupsList = () => {
    const page = usePage();
    const { groups } = page.props

    return (
        <>
            {groups.map((item) => (
                <GroupItem item={item} key={item.id} />
            ))}
        </>
    )
}

export default GroupsList;