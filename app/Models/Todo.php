<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
