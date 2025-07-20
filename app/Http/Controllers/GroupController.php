<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return back()->with('toast@success', "New group has been created.");
    }
}
