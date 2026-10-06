<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = ['user_id', 'title', 'body', 'icon', 'url', 'is_read'];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function notify(User $user, string $title, ?string $body = null, string $icon = 'bell', ?string $url = null): self
    {
        return static::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'icon' => $icon,
            'url' => $url,
        ]);
    }

    public static function notifyRole(string|array $roles, string $title, ?string $body = null, string $icon = 'bell', ?string $url = null): void
    {
        $users = User::where('is_active', true)
            ->whereIn('role', (array) $roles)
            ->get();

        foreach ($users as $user) {
            static::notify($user, $title, $body, $icon, $url);
        }
    }
}
