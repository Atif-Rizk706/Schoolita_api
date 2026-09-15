<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;

class Subject extends Model
{
    use HasFactory, BelongsToTenant, HasTranslations;

    public array $translatable = ['name', 'description', 'bio'];

    protected $fillable = [
        'tenant_id',
        'grade_id',
        'name',
        'slug',
        'description',
        'bio',
        'icon',
        'color_theme',
        'lessons_count',
        'subscription_price',
        'is_active',
    ];

    protected $casts = [
        'lessons_count'      => 'integer',
        'subscription_price' => 'float',
        'is_active'          => 'boolean',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher')
            ->withPivot('id', 'is_active')
            ->withTimestamps();
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(SubjectOutcome::class)->orderBy('order');
    }

    public function features(): HasMany
    {
        return $this->hasMany(SubjectFeature::class)->orderBy('order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function subscriptions(): HasManyThrough
    {
        return $this->hasManyThrough(Subscription::class, SubjectTeacher::class, 'subject_id', 'subject_teacher_id');
    }
}
