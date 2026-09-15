<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'subject_teacher_id',
        'price',
        'currency',
        'payment_method',
        'status',
        'starts_at',
        'ends_at',
        'notes',
    ];

    protected $casts = [
        'price' => 'float',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjectTeacher(): BelongsTo
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
