<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Scopes\IncompleteScope;
use Illuminate\Contracts\Database\Query\Builder;

use function PHPUnit\Framework\isNull;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {
        return Inertia::render('todo/todo-index', [
            'group' => $group,
            'todos' => $group->todos()->whereNull('parent_id')->with('children')->when(request()->has('completed'), function ($builder) {
                $builder->withoutGlobalScope(IncompleteScope::class);
            })->get()
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
        $todo->load('children');

        $todo->load('group');

        return Inertia::render('todo/todo-detail', [
            'todo' => $todo,
            'ancestors' => $todo->ancestors()
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

    /**
     * Toggles Todo completed_at field
     * 
     * Sets now as todo's completed_at value if it
     * is null, otherwise sets null
     *
     * @param Todo $todo
     * @return void
     */
    public function toggleCompleted(Todo $todo)
    {
        if ($isIncomplete = is_null($todo->completed_at))
            $todo->update(['completed_at' => now()]);
        else
            $todo->update(['completed_at' => null]);

        return back()->with('toast.success', "Todo marked as " . ($isIncomplete ? "completed" : 'incomplete'));
    }
}
