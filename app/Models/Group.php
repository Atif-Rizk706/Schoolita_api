<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'teacher_id',
        'subject_id',
        'name',
        'code',
        'capacity',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'capacity'  => 'integer',
        'price'     => 'float',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($group) {
            if (empty($group->code)) {
                $group->code = self::generateUniqueCode($group->tenant_id);
            }
        });
    }

    public static function generateUniqueCode(?int $tenantId = null): string
    {
        do {
            $code = 'GRP-' . strtoupper(Str::random(6));
            $query = self::where('code', $code);
            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }
        } while ($query->exists());

        return $code;
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(GroupDay::class)->orderBy('start_time');
    }

    public function groupUsers(): HasMany
    {
        return $this->hasMany(GroupUser::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->withPivot('id', 'status', 'joined_at', 'notes')
            ->withTimestamps();
    }

    /**
     * Helper accessor for grade through subject.
     */
    public function getGradeAttribute(): ?Grade
    {
        return $this->subject?->grade;
    }

    /**
     * Helper accessor for educational stage through grade.
     */
    public function getStageAttribute(): ?EducationalStage
    {
        return $this->subject?->grade?->stage;
    }
}
