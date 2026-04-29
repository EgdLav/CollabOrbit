<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'invitee_id',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
