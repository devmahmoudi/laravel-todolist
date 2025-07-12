<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'parent_id',
        'group_id',
    ];

    /**
     * Get the parent task.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Get the child tasks.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Get the group that owns the task.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get all descendants of this task (recursive).
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id')->with('descendants');
    }

    /**
     * Get all ancestors of this task (recursive).
     */
    public function ancestors(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id')->with('ancestors');
    }

    /**
     * Get all descendants as a flat collection.
     */
    public function getAllDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }

    /**
     * Get all ancestors as a flat collection.
     */
    public function getAllAncestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        
        if ($this->parent) {
            $ancestors->push($this->parent);
            $ancestors = $ancestors->merge($this->parent->getAllAncestors());
        }
        
        return $ancestors;
    }
} 