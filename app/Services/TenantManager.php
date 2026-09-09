<?php

namespace App\Services;

use App\Models\Tenant;

class TenantManager
{
    protected ?Tenant $tenant = null;

    /**
     * Set the active tenant for current request.
     */
    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * Get the active tenant.
     */
    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Get active tenant ID.
     */
    public function getTenantId(): ?int
    {
        return $this->tenant?->id;
    }

    /**
     * Check if a tenant is currently resolved.
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }
}
