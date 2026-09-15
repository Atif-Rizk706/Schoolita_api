<?php

namespace App\Http\Resources\Concerns;

use Illuminate\Http\Request;

/**
 * TranslatableResource
 *
 * Provides translatable helpers for JSON resources that work with
 * spatie/laravel-translatable models.
 *
 * Behaviour:
 *   - Admin / Tenant routes (path contains api/v1/tenant, api/tenant, admin) -> return the full translations array {"ar":"...", "en":"..."}
 *   - Public / Website routes (public portal) -> return the string for the resolved locale
 *
 * Locale resolution order (public only):
 *   1. `lang` query-string param     -> ?lang=ar
 *   2. `Accept-Language` HTTP header -> Accept-Language: en
 *   3. Laravel app locale fallback   -> config('app.locale') (default: ar)
 */
trait TranslatableResource
{
    /**
     * Resolve the active locale for the current request.
     *
     * Returns null when the caller is an Admin / Tenant route (meaning: return all langs).
     */
    protected function resolveLocale(Request $request): ?string
    {
        // Admin / Tenant routes -> return full object (unless explicit ?lang= is forced)
        $path = ltrim($request->path(), '/');
        $isAdminOrTenant = str_contains($path, 'tenant') || str_contains($path, 'admin');

        if ($isAdminOrTenant && ! $request->has('lang')) {
            return null;
        }

        // 1. Explicit query param  ?lang=ar
        if ($lang = $request->query('lang')) {
            return strtolower(trim($lang));
        }

        // 2. Accept-Language header  (take the first tag before comma/semicolon)
        if ($header = $request->header('Accept-Language')) {
            $primary = strtolower(trim(explode(',', explode(';', $header)[0])[0]));
            // Accept "ar-SA" -> "ar", "en-US" -> "en"
            return substr($primary, 0, 2);
        }

        // 3. Laravel app locale
        return app()->getLocale() ?: 'ar';
    }

    /**
     * Translate a field that belongs to a spatie/laravel-translatable model.
     *
     * Usage in a resource:
     *   $this->translatable('name', $locale)
     *
     * @param  string      $field   Attribute name on the underlying model.
     * @param  string|null $locale  null = return full translations array, string = return single locale value.
     * @return mixed
     */
    protected function translatable(string $field, ?string $locale): mixed
    {
        $model = $this->resource;

        if (! $model) {
            return null;
        }

        // Admin / Tenant: return the complete translations array {"ar":"...", "en":"..."}
        if ($locale === null) {
            if (method_exists($model, 'getTranslations')) {
                return $model->getTranslations($field);
            }

            $raw = method_exists($model, 'getRawOriginal') ? $model->getRawOriginal($field) : ($model->{$field} ?? null);
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            return is_array($decoded) ? $decoded : $raw;
        }

        // Public / Website: return the requested locale
        if (method_exists($model, 'getTranslation')) {
            return $model->getTranslation($field, $locale, true);
        }

        $raw = method_exists($model, 'getRawOriginal') ? $model->getRawOriginal($field) : ($model->{$field} ?? null);
        $data = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : null);

        if (is_array($data)) {
            return $data[$locale] ?? $data['ar'] ?? reset($data);
        }

        return $raw;
    }

    /**
     * Translate a plain JSON field that is NOT managed by spatie/laravel-translatable
     *
     * @param  mixed       $field   The raw value (array or string).
     * @param  string|null $locale  null = full array, string = single locale value.
     * @return mixed
     */
    protected function trans(mixed $field, ?string $locale): mixed
    {
        if ($field === null) {
            return null;
        }

        $data = is_array($field) ? $field : (is_string($field) ? json_decode($field, true) : null);

        if (! is_array($data)) {
            return $field;
        }

        if ($locale === null) {
            return $data;
        }

        return $data[$locale] ?? $data['ar'] ?? reset($data) ?: null;
    }
}
