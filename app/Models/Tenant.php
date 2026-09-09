<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'logo',
        'cover_image',
        'primary_color',
        'secondary_color',
        'phone',
        'whatsapp',
        'email',
        'address',
        'about_us',
        'vision',
        'mission',
        'working_hours',
        'social_links',
        'hero_title',
        'hero_subtitle',
        'stats',
        'is_active',
        'subscription_plan',
        'subscription_expires_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'stats' => 'array',
        'is_active' => 'boolean',
        'subscription_expires_at' => 'datetime',
    ];

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

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }
}
