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

class Teacher extends Model
{
    use HasFactory, BelongsToTenant, HasTranslations;

    public array $translatable = ['name', 'title', 'bio'];

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'title',
        'email',
        'phone',
        'address',
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

    public function features(): HasMany
    {
        return $this->hasMany(TeacherFeature::class)->orderBy('order');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher')
            ->withPivot('id', 'is_active')
            ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id');
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
        return $this->hasManyThrough(Subscription::class, SubjectTeacher::class, 'teacher_id', 'subject_teacher_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get the educational stages of the teacher through their subjects.
     */
    public function educationalStages()
    {
        return \App\Models\EducationalStage::whereHas('grades.subjects.teachers', function ($query) {
            $query->where('teachers.id', $this->id);
        });
    }
}
