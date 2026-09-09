<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'title',
        'avatar',
        'bio',
        'experience_years',
        'students_count',
        'rating',
        'whatsapp_number',
        'color_theme',
        'is_active',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'students_count' => 'integer',
        'rating' => 'float',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
