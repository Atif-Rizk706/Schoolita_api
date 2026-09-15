<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'subject_teacher_id',
        'subject_id',
        'teacher_id',
        'title',
        'slug',
        'description',
        'type',
        'bunny_video_id',
        'bunny_library_id',
        'video_url',
        'embed_url',
        'pdf_url',
        'duration_minutes',
        'is_free_preview',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'is_free_preview' => 'boolean',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title) . '-' . Str::random(5);
            }
        });
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function subjectTeacher(): BelongsTo
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LessonAttachment::class);
    }
}
