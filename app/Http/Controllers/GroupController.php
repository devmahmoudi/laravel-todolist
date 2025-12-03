<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class GroupController extends Controller
{
    /**
     * Create new Group (API)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:groups,name|max:255',
        ]);

        $group = Auth::user()->groups()->create($validated);

        return response()->json([
            'message' => 'New group has been created.',
            'data' => $group,
        ], 201);
    }

    /**
     * Update the specified Group (API)
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Group $group)
    {
        if ($group->owner_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|unique:groups,name,' . $group->id . '|max:255',
        ]);

        $group->update($validated);

        return response()->json([
            'message' => 'Group has been updated.',
            'data' => $group->fresh(),
        ]);
    }

    /**
     * Delete the specified Group (API)
     *
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Group $group)
    {
        if ($group->owner_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $group->delete();

        return response()->json([
            'message' => 'Group has been deleted.',
        ]);
    }
}
