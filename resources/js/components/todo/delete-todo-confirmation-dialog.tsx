import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog"
import { useEffect, useRef } from "react";

const DeleteTodoConfirmationDialog = ({ todo, onConfirm, onClose}) => {
    const triggerRef = useRef(null)

    useEffect(() => {
        triggerRef.current.click()
    }, [])

    return (
        <AlertDialog onOpenChange={(open) => !open ? onClose() : null}>
            <AlertDialogTrigger ref={triggerRef} className="invisible  h-0">Open</AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Do you wanna to delete '{todo.title}' ?
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction onClick={() => onConfirm()}>Continue</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    )
}

export default DeleteTodoConfirmationDialog;