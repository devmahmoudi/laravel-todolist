<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {
        return Inertia::render('todo/todo-index', [
            'group' => $group,
            'todos' => $group->todos()->whereNull('parent_id')->with('children')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        Todo::create(
            $request->validated()
        );
       
        return back()->with('toast.success', 'Todo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        return Inertia::render('todo/todo-detail', [
            'todo' => $todo->load('children')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo->update($request->validated());

        return back()->with('toast.success', 'Todo updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        return back()->with('toast.success', 'Todo deleted successfully.');
    }
}
