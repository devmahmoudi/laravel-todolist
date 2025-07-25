<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class GroupController extends Controller
{
    /**
     * Craete new Group
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:groups,name|max:255',
        ]);

        Auth::user()->groups()->create($validated);

        return back()->with('toast.success', "New group has been created.");
    }

    /**
     * Update the specified Group
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Group $group)
    {
        if ($group->owner_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|unique:groups,name,' . $group->id . '|max:255',
        ]);

        $group->update($validated);

        return back()->with('toast.success', 'Group has been updated.');
    }

    /**
     * Delete the specified Group
     *
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Group $group)
    {
        if ($group->owner_id !== Auth::id()) {
            abort(403);
        }

        $group->delete();

        return back()->with('toast.success', 'Group has been deleted.');
    }

    
}
