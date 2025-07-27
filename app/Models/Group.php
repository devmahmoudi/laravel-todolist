<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\GroupOwnerScope;
use Illuminate\Support\Facades\Auth;

#[ScopedBy([GroupOwnerScope::class])]
class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            if (empty($group->owner_id) && Auth::check()) {
                $group->owner_id = Auth::id();
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function todos()
    {
        return $this->hasMany(Todo::class, 'group_id');
    }
}
