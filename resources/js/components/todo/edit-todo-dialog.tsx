import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import { Textarea } from "@/components/ui/textarea";
import { useForm } from "@inertiajs/react";
import { ChangeEvent, useEffect, useRef } from "react";
import { ClipLoader } from "react-spinners"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogClose,
    DialogTrigger,
} from "@/components/ui/dialog"

interface Todo {
    id: number;
    title: string;
    description: string;
    group_id: number;
}

const EditTodoDialog = ({ todo, onClose }: { todo: Todo, onClose(): void }) => {
    const { data, setData, put, processing, errors, reset } = useForm({
        title: todo?.title || '',
        description: todo?.description || '',
        group_id: todo?.group_id || 0
    })

    const triggerRef = useRef(null)

    useEffect(() => {
        triggerRef.current?.click()
    }, [])

    useEffect(() => {
        if (todo) {
            setData({
                title: todo.title,
                description: todo.description,
                group_id: todo.group_id
            })
        }
    }, [todo])

    const handleChange = (e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => setData({ ...data, [e.target.name]: e.target.value })

    const handleSubmit = () => {
        put(
            route('todo.update', todo.id), {
            onSuccess: () => {
                reset()
                onClose()
            }
        }
        )
    }

    return (
        <Dialog onOpenChange={(open) => !open ? onClose() : null}>
            <DialogTrigger ref={triggerRef} className="invisible h-0">Open</DialogTrigger>
            <DialogContent>
                <h1>Edit todo</h1>
                <Separator />
                <div className="grid gap-2">
                    <Label htmlFor="title">Title</Label>

                    <Input
                        id="title"
                        className="mt-1 block w-full"
                        value={data.title}
                        name="title"
                        onChange={handleChange}
                        required
                        placeholder="todo title ..."
                    />

                    <InputError message={errors.title} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="description">Description</Label>

                    <Textarea
                        id="description"
                        className="mt-1 block w-full"
                        value={data.description}
                        name="description"
                        onChange={handleChange}
                        required
                        placeholder="todo description ..."
                    />

                    <InputError className="mt-2" message={errors.description} />
                </div>

                <div className="flex gap-2 justify-end">
                    <Button className="cursor-pointer bg-blue-500 text-white" onClick={handleSubmit}>
                        {
                            processing ? <ClipLoader size={20} /> : <span>Update</span>
                        }
                    </Button>
                    <Button className="cursor-pointer bg-yellow-300 border-white" onClick={() => onClose()}>Cancel</Button>
                </div>
            </DialogContent>
        </Dialog>

    )
}

export default EditTodoDialog;
