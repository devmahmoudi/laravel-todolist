<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Group;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource (API).
     *
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Group $group)
    {
        $todos = $group->todos()
            ->whereNull('parent_id')
            ->with('children')
            ->when(!request()->has('completed'), function ($builder) {
                $builder->incomplete();
            })
            ->get();

        return response()->json([
            'group' => $group,
            'data' => $todos,
        ]);
    }

    /**
     * Store a newly created resource in storage (API).
     *
     * @param \App\Http\Requests\StoreTodoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTodoRequest $request)
    {
        $todo = Todo::create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Todo created successfully.',
            'data' => $todo,
        ], 201);
    }

    /**
     * Display the specified resource (API).
     *
     * @param \App\Models\Todo $todo
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Todo $todo)
    {
        $todo->load('children');

        $todo->load('group');

        return response()->json([
            'data' => $todo,
            'ancestors' => $todo->ancestors(),
        ]);
    }

    /**
     * Update the specified resource in storage (API).
     *
     * @param \App\Http\Requests\UpdateTodoRequest $request
     * @param \App\Models\Todo $todo
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo->update($request->validated());

        return response()->json([
            'message' => 'Todo updated successfully.',
            'data' => $todo->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage (API).
     *
     * @param \App\Models\Todo $todo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted successfully.',
        ]);
    }

    /**
     * Toggles Todo completed_at field (API)
     * 
     * Sets now as todo's completed_at value if it
     * is null, otherwise sets null
     *
     * @param Todo $todo
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleCompleted(Todo $todo)
    {
        if ($isIncomplete = is_null($todo->completed_at))
            $todo->update(['completed_at' => now()]);
        else
            $todo->update(['completed_at' => null]);

        return response()->json([
            'message' => 'Todo marked as ' . ($isIncomplete ? 'completed' : 'incomplete'),
            'data' => $todo->fresh(),
        ]);
    }
}
