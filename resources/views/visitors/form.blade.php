@extends('layouts.app')

@section('title', $visitor->exists ? 'Edit Tamu' : 'Registrasi Tamu')
@section('page-title', $visitor->exists ? 'Edit Tamu' : 'Registrasi Tamu')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">{{ $visitor->exists ? 'Edit Data Tamu' : 'Registrasi Tamu Baru' }}</h1>
        <p class="page-sub">{{ $visitor->exists ? 'Perbarui informasi registrasi ' . $visitor->code : 'Lengkapi data tamu sesuai kebutuhan kunjungan.' }}</p>
    </div>
    <a href="{{ route('visitors.index') }}" class="btn btn-outline">← Kembali</a>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $visitor->exists ? route('visitors.update', $visitor) : route('visitors.store') }}">
    @csrf
    @if ($visitor->exists)
        @method('PUT')
    @endif

    <div class="grid" style="grid-template-columns: 1.7fr 1fr" id="formRow">
        <div>
            <div class="card anim-rise">
                <div class="card-title">Data Tamu</div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="full_name" class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}" value="{{ old('full_name', $visitor->full_name) }}" required>
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" name="company" class="form-control" value="{{ old('company', $visitor->company) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP</label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone', $visitor->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $visitor->email) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position', $visitor->position) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Orang</label>
                        <input type="number" name="person_count" min="1" max="50" class="form-control" value="{{ old('person_count', $visitor->person_count) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Identitas</label>
                        <select name="id_type" class="form-select">
                            @foreach (['KTP' => 'KTP', 'Paspor' => 'Paspor', 'SIM' => 'SIM', 'Kartu Pelajar' => 'Kartu Pelajar', 'Lainnya' => 'Lainnya'] as $k => $l)
                                <option value="{{ $k }}" {{ old('id_type', $visitor->id_type) == $k ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Identitas</label>
                        <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $visitor->id_number) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Tamu (opsional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    @if ($visitor->photo)
                        <div class="form-hint">Foto saat ini: {{ basename($visitor->photo) }}</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-title">Detail Kunjungan</div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Tipe Kunjungan <span class="req">*</span></label>
                        <select name="visit_type" class="form-select" required>
                            @foreach (\App\Models\Visitor::VISIT_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('visit_type', $visitor->visit_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tujuan Kunjungan <span class="req">*</span></label>
                        <input type="text" name="purpose" class="form-control {{ $errors->has('purpose') ? 'is-invalid' : '' }}" value="{{ old('purpose', $visitor->purpose) }}" required>
                        @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal <span class="req">*</span></label>
                        <input type="date" name="visit_date" class="form-control" value="{{ old('visit_date', $visitor->visit_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Datang</label>
                        <input type="time" name="visit_time" class="form-control" value="{{ old('visit_time', $visitor->visit_time?->format('H:i')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estimasi Durasi (menit)</label>
                        <input type="number" name="duration_minutes" min="15" max="960" step="15" class="form-control" value="{{ old('duration_minutes', $visitor->duration_minutes) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Kendaraan</label>
                        <input type="text" name="vehicle_number" class="form-control" placeholder="B 1234 XYZ" value="{{ old('vehicle_number', $visitor->vehicle_number) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $visitor->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div>
            <div class="card anim-rise d1">
                <div class="card-title">Host & Lokasi</div>

                <div class="form-group">
                    <label class="form-label">Host / PIC</label>
                    <select name="host_id" class="form-select">
                        <option value="">— Pilih Host —</option>
                        @foreach ($hosts as $host)
                            <option value="{{ $host->id }}" {{ old('host_id', $visitor->host_id) == $host->id ? 'selected' : '' }}>{{ $host->name }} ({{ $host->roleLabel() }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">— Pilih Department —</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $visitor->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Meeting Room</label>
                    <input type="text" name="meeting_room" class="form-control" placeholder="Ruang Rapat A" value="{{ old('meeting_room', $visitor->meeting_room) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Area Akses</label>
                    <input type="text" name="access_area" class="form-control" placeholder="Lobby, Lantai 2" value="{{ old('access_area', $visitor->access_area) }}">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">
                    {{ $visitor->exists ? 'Simpan Perubahan' : 'Buat Registrasi' }}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>
                </button>
            </div>
        </div>
    </div>
</form>

<style>
@media (max-width: 991px) { #formRow { grid-template-columns: 1fr !important; } }
</style>
@endsection
