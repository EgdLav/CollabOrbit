<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    use HasFactory;
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executor_id');
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected $fillable = [
        'name',
        'description',
        'preview',
        'files',
        'workspace_id',
        'category_id',
        'executor_id',
        'creator_id',
        'due_date',
    ];
    protected $casts = [
        'files' => 'array',
    ];

    public function getFilesUrlsAttribute()
    {
        if (!$this->files) return [];

        return array_map(fn($file) => url(Storage::url($file)), $this->files);
    }
    public function getPreviewUrlAttribute()
    {
        return url(Storage::url($this->preview));
    }

    protected static function booted()
    {
        static::deleting(function($task) {
            if ($task->files) {
                foreach($task->files as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
    }
}
