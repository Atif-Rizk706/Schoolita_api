<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantIdentifier = $this->resolveTenantIdentifier($request);

        $tenant = null;

        if ($tenantIdentifier) {
            $tenant = Tenant::where('is_active', true)
                ->where(function ($query) use ($tenantIdentifier) {
                    $query->where('slug', $tenantIdentifier)
                        ->orWhere('domain', $tenantIdentifier);
                })
                ->first();
        }

        // Fallback: If no tenant specified or found, fallback to first active tenant (default/demo)
        if (!$tenant) {
            $tenant = Tenant::where('is_active', true)->first();
        }

        if ($tenant) {
            $this->tenantManager->setTenant($tenant);
            $request->attributes->set('tenant', $tenant);
        }

        return $next($request);
    }

    /**
     * Resolve identifier from Header, Subdomain, or Query parameter.
     */
    protected function resolveTenantIdentifier(Request $request): ?string
    {
        // 1. From X-Tenant Header (Preferred for React / Next.js / Mobile)
        if ($request->hasHeader('X-Tenant')) {
            return trim($request->header('X-Tenant'));
        }

        // 2. From Query parameter (?tenant=slug)
        if ($request->has('tenant')) {
            return trim($request->query('tenant'));
        }

        // 3. From Subdomain or Domain
        $host = $request->getHost();
        $parts = explode('.', $host);

        // If it's a subdomain like: elnour.schoolita.com or elnour.localhost
        if (count($parts) >= 2 && !in_array($parts[0], ['www', 'api', 'localhost', '127'])) {
            return $parts[0];
        }

        // 4. Custom domain matching
        return $host;
    }
}
