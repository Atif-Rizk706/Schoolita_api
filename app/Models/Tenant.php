<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $with = ['profile'];

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'phone',
        'email',
        'is_active',
        'subscription_plan',
        'subscription_expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_expires_at' => 'datetime',
    ];

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TenantProfile::class);
    }

    public function getLogoAttribute(): ?string
    {
        return $this->profile?->logo;
    }

    public function getCoverImageAttribute(): ?string
    {
        return $this->profile?->cover_image;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(EducationalStage::class)->orderBy('order');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class)->orderBy('order');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
