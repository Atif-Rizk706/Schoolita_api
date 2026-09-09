<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'role',
        'comment',
        'rating',
        'avatar',
        'color_theme',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_active' => 'boolean',
    ];
}
