<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasFactory;
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_workspace');
    }
    public function owner(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'owner_id');
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    protected static function booted()
    {
        static::deleting(function ($ws) {
            $ws->tasks->each->delete();
            $ws->users()->detach();
        });
    }

    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];
}
