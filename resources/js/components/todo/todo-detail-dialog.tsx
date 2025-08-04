import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
import { useEffect, useRef } from "react";

const TodoDetailDialog = ({ todo, onClose }) => {
    const triggerRef = useRef()

    useEffect(() => {
        triggerRef.current.click()
    }, [])

    return (
        <Dialog onOpenChange={(open) => !open ? onClose() : null}>
            <DialogTrigger ref={triggerRef} className="invisible h-0">Open</DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{todo.title}</DialogTitle>
                    <DialogDescription>
                        {todo.description}
                    </DialogDescription>
                </DialogHeader>
            </DialogContent>
        </Dialog>
    )
}

export default TodoDetailDialog;