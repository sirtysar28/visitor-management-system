<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('profile', ['user' => $request->user()]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(6)->mixedCase()->numbers(), 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        \App\Support\Vms::audit('update_password', 'profile', 'Mengubah password');

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function auditIndex()
    {
        $logs = AuditLog::with('user')->latest('id')->paginate(10);

        return view('audit.index', compact('logs'));
    }

    public function exportCsv()
    {
        $fileName = 'audit-log-' . now()->format('Ymd-His') . '.csv';
        $logs = AuditLog::with('user')->latest('id')->limit(5000)->get();

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'User', 'Aksi', 'Modul', 'Deskripsi', 'IP']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->user?->name ?? '-',
                    $log->action,
                    $log->module ?? '-',
                    $log->description ?? '-',
                    $log->ip_address ?? '-',
                ]);
            }
            fclose($handle);
        };

        \App\Support\Vms::audit('export', 'audit', 'Mengexport audit log CSV');

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
