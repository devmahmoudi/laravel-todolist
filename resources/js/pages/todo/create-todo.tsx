import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import { Textarea } from "@/components/ui/textarea";
import { useForm } from "@inertiajs/react";
import { ChangeEvent, useEffect } from "react";

const CreateTodo = () => {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
    })

    const handleChange = (e: ChangeEvent<HTMLInputElement>) => setData({ ...data, [e.target.name]: e.target.value })

    const handleSubmit = () =>{
        alert('Handle submit')
    }

    return (
        <>
            <h1>Create new todo</h1>
            <Separator/>
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

                <InputError className="mt-2" message={errors.title} />
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
                <Button className="cursor-pointer" onClick={handleSubmit}>Create</Button>
                <Button className="cursor-pointer bg-yellow-300 border-white">Cancel</Button>
            </div>
        </>
    )
}

export default CreateTodo;