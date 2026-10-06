<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'module', 'description', 'ip_address', 'user_agent'];

    public static function log(string $action, ?string $module = null, ?string $description = null, ?int $userId = null): self
    {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 250),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
