<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** Dropdown / badge data (JSON, polled). */
    public function poll(Request $request)
    {
        $user = $request->user();

        $unread = $user->unreadNotifications()->count();

        $pendingApproval = Visitor::where('status', Visitor::STATUS_PENDING);
        if ($user->hasRole(User::ROLE_HOST) && ! $user->isSuperAdmin()) {
            $pendingApproval->where('host_id', $user->id);
        }

        $latest = $user->notifications()
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'icon' => $n->icon,
                'url' => $n->url,
                'is_read' => $n->is_read,
                'time' => $n->created_at?->locale('id')->translatedFormat('d M H:i'),
            ]);

        return response()->json([
            'unread' => $unread,
            'notifications' => $latest,
            'pending_approvals' => $pendingApproval->count(),
        ]);
    }

    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest('id')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
