<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'reminder_sent',
        'auto_complete_on_due_date'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reminder_sent' => 'boolean',
        'auto_complete_on_due_date' => 'boolean'
    ];


    /* ╔═════════════════════════ Relationships ═════════════════════╗ */


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
