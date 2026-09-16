<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class TenantProfile extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'tenant_id',
        'logo',
        'cover_image',
        'primary_color',
        'secondary_color',
        'whatsapp',
        'address',
        'working_hours',
        'about_us',
        'vision',
        'mission',
        'hero_title',
        'hero_subtitle',
        'social_links',
    ];

    public array $translatable = [
        'address',
        'working_hours',
        'about_us',
        'vision',
        'mission',
        'hero_title',
        'hero_subtitle',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->translatable, true)) {
            if (is_string($value) && $value !== '') {
                $value = [
                    'ar' => $value,
                    'en' => $value,
                ];
            } elseif (is_array($value)) {
                $ar = $value['ar'] ?? null;
                $en = $value['en'] ?? null;
                if (!empty($ar) && empty($en)) {
                    $value['en'] = $ar;
                } elseif (!empty($en) && empty($ar)) {
                    $value['ar'] = $en;
                }
            }
        }

        return parent::setAttribute($key, $value);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
