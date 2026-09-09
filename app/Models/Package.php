<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'grade_id',
        'subject_id',
        'title',
        'badge',
        'type',
        'price',
        'currency',
        'total_lectures',
        'weekly_lectures',
        'hours_per_lecture',
        'is_popular',
        'color_theme',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'total_lectures' => 'integer',
        'weekly_lectures' => 'integer',
        'hours_per_lecture' => 'float',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class)->orderBy('order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
