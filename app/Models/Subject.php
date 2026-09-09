<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'grade_id',
        'name',
        'slug',
        'description',
        'icon',
        'color_theme',
        'lessons_count',
        'is_active',
    ];

    protected $casts = [
        'lessons_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'subject_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'subject_id');
    }
}
