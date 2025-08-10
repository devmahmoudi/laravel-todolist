<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'parent_id',
        'group_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function parent()
    {
        return $this->belongsTo(Todo::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Todo::class, 'parent_id');
    }

    /**
     * Get all ancestors of the todo
     * This method traverses up the parent hierarchy
     *
     * @return \Illuminate\Support\Collection
     */
    public function ancestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->prepend($current); // Add to beginning to maintain order
            $current = $current->parent;
        }

        return $ancestors; // Already in correct order: root to immediate parent
    }
}
