<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Approval;
use App\Models\User;
use App\Models\Visitor;
use App\Support\Vms;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitor::with(['host', 'department'])
            ->where('status', Visitor::STATUS_PENDING);

        // Host only approves their own guests
        if ($request->user()->hasRole(User::ROLE_HOST) && ! $request->user()->isSuperAdmin()) {
            $query->where('host_id', $request->user()->id);
        }

        $visitors = $query->orderBy('visit_date')->paginate(10)->withQueryString();

        return view('approvals.index', ['visitors' => $visitors]);
    }

    public function decide(Request $request, Visitor $visitor)
    {
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($visitor->status !== Visitor::STATUS_PENDING) {
            return back()->with('error', 'Registrasi ini sudah diproses sebelumnya.');
        }

        $approved = $data['decision'] === 'approve';

        $visitor->update([
            'status' => $approved ? Visitor::STATUS_APPROVED : Visitor::STATUS_REJECTED,
            'reject_reason' => $approved ? null : ($data['note'] ?? 'Ditolak oleh host/PIC'),
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        Approval::create([
            'visitor_id' => $visitor->id,
            'approver_id' => auth()->id(),
            'level' => 'host',
            'status' => $approved ? 'approved' : 'rejected',
            'note' => $data['note'] ?? null,
            'acted_at' => now(),
        ]);

        // Notify reception & security
        $label = $approved ? 'DISETUJUI' : 'DITOLAK';
        AppNotification::notifyRole(
            [User::ROLE_RECEPTIONIST, User::ROLE_SECURITY, User::ROLE_ADMIN],
            "Registrasi {$label}",
            "Kunjungan {$visitor->full_name} ({$visitor->code}) {$label} oleh " . auth()->user()->name . '.',
            $approved ? 'check' : 'x',
            route('visitors.show', $visitor->id)
        );

        Vms::audit($approved ? 'approve' : 'reject', 'approval', "Registrasi {$visitor->code} {$label}");

        return back()->with('success', "Registrasi tamu berhasil {$label}.");
    }
}
