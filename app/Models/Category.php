<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    protected $fillable = [
        'workspace_id',
        'name',
    ];

    protected static function booted()
    {
        static::deleting(function ($c) {
            $c->tasks->each->delete();
        });
    }
}
