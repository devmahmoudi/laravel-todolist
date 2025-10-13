<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Todo extends Model
{
    use HasFactory;

    /**
     * Define fillable props
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'parent_id',
        'group_id',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * The Todo's group
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * The Todo's parent
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Todo::class, 'parent_id');
    }

    /**
     * The Todo's children
     *
     * @return HasMany
     */
    public function children(): HasMany
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

    /**
     * Returns root Todo of current which it's parent_id is null
     * and is start point of hierarchy (First Todo)
     *
     * @return Todo|null
     */
    public function root(): ?Todo
    {
        return $this->ancestors()->first() ?? $this;
    }

    /**
     * Check if the todo is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Mark the todo as completed
     *
     * @return bool
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['completed_at' => now()]);
    }

    /**
     * Mark the todo as incomplete
     *
     * @return bool
     */
    public function markAsIncomplete(): bool
    {
        return $this->update(['completed_at' => null]);
    }

    /**
     * Toggle the completion status of the todo
     *
     * @return bool
     */
    public function toggleCompletion(): bool
    {
        return $this->isCompleted() ? $this->markAsIncomplete() : $this->markAsCompleted();
    }

    /**
     * Select only incomplete todos
     *
     * @param Builder $builder
     * @return void
     */
    #[Scope]
    protected function incomplete(Builder $builder):void
    {
        $builder->whereNull('completed_at');
    }
}
