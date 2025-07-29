import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import { Textarea } from "@/components/ui/textarea";
import { useForm } from "@inertiajs/react";
import { ChangeEvent, useEffect } from "react";
import { ClipLoader } from "react-spinners"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"

const CreateTodoDialog = ({ open, setOpen, groupId }) => {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        group_id: groupId
    })

    const handleChange = (e: ChangeEvent<HTMLInputElement>) => setData({ ...data, [e.target.name]: e.target.value })

    const handleSubmit = () => {
        post(
            route('todo.store'), {
            onSuccess: () => setOpen(false)
        }
        )
    }

    return (
        <Dialog open={open} onOpenChange={() => setOpen(false)}>
            <DialogContent>
                <h1>Create new todo</h1>
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
                    <Button className="cursor-pointer" onClick={handleSubmit}>
                        {
                            processing ? <ClipLoader size={20}/> : <span>Create</span>
                        }
                    </Button>
                    <Button className="cursor-pointer bg-yellow-300 border-white" onClick={() => setOpen(false)}>Cancel</Button>
                </div>
            </DialogContent>
        </Dialog>

    )
}

export default CreateTodoDialog;