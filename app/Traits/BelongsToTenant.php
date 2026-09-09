<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Automatically attach tenant_id on creating
        static::creating(function ($model) {
            $tenantManager = app(TenantManager::class);
            if (empty($model->tenant_id) && $tenantManager->hasTenant()) {
                $model->tenant_id = $tenantManager->getTenantId();
            }
        });

        // Global scope to filter models by active tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantManager = app(TenantManager::class);
            if ($tenantManager->hasTenant()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantManager->getTenantId());
            }
        });
    }

    /**
     * Relationship to tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
