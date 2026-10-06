<?php

namespace App\Support;

use App\Models\AuditLog;

class Vms
{
    /** Application name from settings (fallback: env). */
    public static function appName(): string
    {
        return \App\Models\Setting::get('app_name', config('app.name'));
    }

    /** Logo URL: custom from settings or default SVG. */
    public static function logoUrl(): string
    {
        $logo = \App\Models\Setting::get('app_logo');

        return $logo ? asset('storage/' . $logo) : asset('assets/img/logo.svg');
    }

    public static function audit(string $action, ?string $module = null, ?string $description = null): void
    {
        try {
            AuditLog::log($action, $module, $description);
        } catch (\Throwable) {
            // never break the request because of logging
        }
    }
}
