<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Blacklist;
use App\Models\Visitor;
use App\Models\Department;
use App\Models\User;
use App\Support\Vms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitor::with(['host', 'department'])
            ->when($request->search, fn ($q, $v) => $q->where(fn ($q2) => $q2
                ->where('full_name', 'like', "%{$v}%")
                ->orWhere('code', 'like', "%{$v}%")
                ->orWhere('company', 'like', "%{$v}%")))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->visit_type, fn ($q, $v) => $q->where('visit_type', $v))
            ->when($request->date, fn ($q, $v) => $q->whereDate('visit_date', $v));

        // Host only sees their own guests
        if ($request->user()->hasRole(User::ROLE_HOST) && ! $request->user()->isSuperAdmin()) {
            $query->where('host_id', $request->user()->id);
        }

        $visitors = $query->latest('visit_date')->paginate(10)->withQueryString();

        return view('visitors.index', [
            'visitors' => $visitors,
            'statuses' => Visitor::STATUSES,
            'visitTypes' => Visitor::VISIT_TYPES,
            'filters' => $request->only(['search', 'status', 'visit_type', 'date']),
        ]);
    }

    public function create()
    {
        return view('visitors.form', [
            'visitor' => new Visitor([
                'visit_date' => now()->toDateString(),
                'visit_type' => 'pre_registered',
                'person_count' => 1,
                'host_id' => auth()->id(),
                'id_type' => 'KTP',
            ]),
            'hosts' => User::where('is_active', true)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Blacklist guard
        if ($blocked = $this->findBlacklisted($data)) {
            return back()->withInput()->with('error', "Registrasi ditolak: {$blocked->full_name} ada di daftar blacklist ({$blocked->reason}).");
        }

        $data['code'] = Visitor::generateCode();
        $data['badge_code'] = 'BDG-' . strtoupper(bin2hex(random_bytes(4)));
        $data['status'] = Visitor::STATUS_PENDING;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('visitors', 'public');
        }

        $visitor = Visitor::create($data);

        // Approval trail -> notify host
        if ($visitor->host_id && $host = User::find($visitor->host_id)) {
            AppNotification::notify($host, 'Approval Dibutuhkan', "Registrasi tamu {$visitor->full_name} menunggu persetujuan Anda.", 'user', route('approvals.index'));
        }
        AppNotification::notifyRole([User::ROLE_RECEPTIONIST, User::ROLE_SECURITY, User::ROLE_ADMIN], 'Registrasi Baru', "Tamu {$visitor->full_name} ({$visitor->code}) terdaftar.", 'user', route('visitors.show', $visitor->id));

        Vms::audit('create', 'visitor', "Membuat registrasi tamu {$visitor->full_name} ({$visitor->code})");

        return redirect()->route('visitors.show', $visitor)->with('success', 'Registrasi tamu berhasil dibuat.');
    }

    public function show(Visitor $visitor)
    {
        return view('visitors.show', [
            'visitor' => $visitor->load(['host', 'department', 'approvals.approver']),
        ]);
    }

    public function edit(Visitor $visitor)
    {
        return view('visitors.form', [
            'visitor' => $visitor,
            'hosts' => User::where('is_active', true)->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Visitor $visitor)
    {
        $data = $this->validated($request, $visitor);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('visitors', 'public');
        }

        $visitor->update($data);

        Vms::audit('update', 'visitor', "Memperbarui registrasi tamu {$visitor->full_name} ({$visitor->code})");

        return redirect()->route('visitors.show', $visitor)->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function destroy(Visitor $visitor)
    {
        $name = $visitor->full_name;
        if ($visitor->photo) {
            Storage::disk('public')->delete($visitor->photo);
        }
        $visitor->delete();

        Vms::audit('delete', 'visitor', "Menghapus registrasi tamu {$name}");

        return redirect()->route('visitors.index')->with('success', 'Registrasi tamu dihapus.');
    }

    public function checkIn(Request $request, Visitor $visitor)
    {
        if ($visitor->status !== Visitor::STATUS_APPROVED && $visitor->status !== Visitor::STATUS_PENDING) {
            return back()->with('error', 'Tamu tidak dapat check-in dengan status saat ini.');
        }

        // Auto-approve on check-in (receptionist flows)
        $visitor->update([
            'status' => Visitor::STATUS_CHECKED_IN,
            'approved_at' => $visitor->approved_at ?? now(),
            'approved_by' => $visitor->approved_by ?? auth()->id(),
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
        ]);

        if ($visitor->host) {
            AppNotification::notify($visitor->host, 'Tamu Telah Tiba', $visitor->full_name . ' dari ' . ($visitor->company ?: '-') . ' sudah check-in di reception.', 'check', route('visitors.show', $visitor->id));
        }

        Vms::audit('check_in', 'visitor', "Check-in tamu {$visitor->full_name} ({$visitor->code})");

        return back()->with('success', "Tamu {$visitor->full_name} berhasil check-in.");
    }

    public function checkOut(Request $request, Visitor $visitor)
    {
        if ($visitor->status !== Visitor::STATUS_CHECKED_IN) {
            return back()->with('error', 'Tamu sedang tidak berada di dalam gedung.');
        }

        $visitor->update([
            'status' => Visitor::STATUS_CHECKED_OUT,
            'checked_out_at' => now(),
            'checked_out_by' => auth()->id(),
        ]);

        Vms::audit('check_out', 'visitor', "Check-out tamu {$visitor->full_name} ({$visitor->code})");

        return back()->with('success', "Tamu {$visitor->full_name} berhasil check-out.");
    }

    private function validated(Request $request, ?Visitor $visitor = null): array
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'position' => ['nullable', 'string', 'max:100'],
            'id_type' => ['nullable', 'string', 'max:30'],
            'id_number' => ['nullable', 'string', 'max:60'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'person_count' => ['nullable', 'integer', 'min:1', 'max:50'],
            'visit_type' => ['required', 'in:pre_registered,walk_in,vendor,delivery'],
            'purpose' => ['required', 'string', 'max:255'],
            'visit_date' => ['required', 'date'],
            'visit_time' => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:960'],
            'vehicle_number' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'host_id' => ['nullable', 'exists:users,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'meeting_room' => ['nullable', 'string', 'max:100'],
            'access_area' => ['nullable', 'string', 'max:150'],
        ]);

        $data['person_count'] = $data['person_count'] ?? 1;
        $data['visit_time'] = $data['visit_time'] ?? now()->format('H:i');
        $data['duration_minutes'] = $data['duration_minutes'] ?? 60;

        return $data;
    }

    private function findBlacklisted(array $data): ?Blacklist
    {
        return Blacklist::where('is_active', true)
            ->where(fn ($q) => $q
                ->where('full_name', 'like', $data['full_name'])
                ->when(! empty($data['id_number']), fn ($q2) => $q2->orWhere('id_number', $data['id_number'])))
            ->first();
    }
}
