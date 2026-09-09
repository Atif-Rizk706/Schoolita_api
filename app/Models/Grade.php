<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'stage_id',
        'name',
        'slug',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(EducationalStage::class, 'stage_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'grade_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'grade_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'grade_id');
    }
}
