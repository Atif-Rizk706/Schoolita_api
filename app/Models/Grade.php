<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Grade extends Model
{
    use HasFactory, BelongsToTenant, HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = [
        'tenant_id',
        'stage_id',
        'name',
        'slug',
        'code',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($grade) {
            if (empty($grade->code)) {
                $grade->code = self::generateUniqueCode();
            }
        });
    }

    private static function generateUniqueCode(): string
    {
        do {
            $code = 'GRD-' . strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(EducationalStage::class, 'stage_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'grade_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'grade_id');
    }
}
