import { usePage } from '@inertiajs/react';
import GroupItem from '@/components/group/group-item'
import CreateGroup from '@/components/group/create-group'


const GroupsList = ({setDisplayCreateGroupInput, displayCreateGroupInput}) => {
    const page = usePage();
    const { groups } = page.props

    console.log(groups);
    

    return (
        <>
            {
                (displayCreateGroupInput && (
                    <CreateGroup onCreated={() => setDisplayCreateGroupInput(false)}/>
                ))
            }
            {groups.map((item) => (
                <GroupItem item={item} key={item.id} />
            ))}
        </>
    )
}

export default GroupsList;