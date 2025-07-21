import { usePage } from '@inertiajs/react';
import GroupItem from '@/components/group/group-item'
import CreateGroup from '@/components/group/create-group'


const GroupsList = ({displayCreateGroupInput}) => {
    const page = usePage();
    const { groups } = page.props

    return (
        <>
            {
                (displayCreateGroupInput && (
                    <CreateGroup/>
                ))
            }
            {groups.map((item) => (
                <GroupItem item={item} key={item.id} />
            ))}
        </>
    )
}

export default GroupsList;