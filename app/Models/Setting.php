<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('vms.settings');
    }

    public static function allCached(): array
    {
        return Cache::rememberForever('vms.settings', function () {
            try {
                return static::query()->pluck('value', 'key')->toArray();
            } catch (\Throwable) {
                // tabel belum ada (mis. saat testing awal) — kembali kosong
                return [];
            }
        });
    }

    public static function smtpConfig(): array
    {
        return [
            'transport' => 'smtp',
            'host' => (string) static::get('smtp_host', ''),
            'port' => (int) static::get('smtp_port', 587),
            'encryption' => (string) static::get('smtp_encryption', 'tls'),
            'username' => (string) static::get('smtp_username', ''),
            'password' => (string) static::get('smtp_password', ''),
            'timeout' => 10,
        ];
    }
}
