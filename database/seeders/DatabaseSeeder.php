<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\Blacklist;
use App\Models\Department;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------- Settings default ---------- */
        Setting::set('app_name', 'TriVMS');
        Setting::set('company_name', 'PT Trijaya Solution');

        /* ---------- Departments ---------- */
        $departments = [];
        foreach ([
            ['name' => 'Information Technology', 'code' => 'IT'],
            ['name' => 'Finance & Accounting', 'code' => 'FIN'],
            ['name' => 'Human Resources', 'code' => 'HRD'],
            ['name' => 'Operations', 'code' => 'OPS'],
            ['name' => 'General Affairs', 'code' => 'GA'],
        ] as $d) {
            $departments[$d['code']] = Department::create($d);
        }

        /* ---------- Users ---------- */
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@vms.test',
            'password' => 'password',
            'role' => User::ROLE_SUPER_ADMIN,
            'department_id' => $departments['IT']->id,
            'position' => 'IT Manager',
            'phone' => '081200000001',
        ]);

        $receptionist = User::create([
            'name' => 'Dewi Receptionist',
            'email' => 'receptionist@vms.test',
            'password' => 'password',
            'role' => User::ROLE_RECEPTIONIST,
            'department_id' => $departments['GA']->id,
            'position' => 'Front Office',
            'phone' => '081200000002',
        ]);

        $security = User::create([
            'name' => 'Andi Security',
            'email' => 'security@vms.test',
            'password' => 'password',
            'role' => User::ROLE_SECURITY,
            'department_id' => $departments['GA']->id,
            'position' => 'Security Officer',
            'phone' => '081200000003',
        ]);

        $host = User::create([
            'name' => 'Budi Santoso',
            'email' => 'host@vms.test',
            'password' => 'password',
            'role' => User::ROLE_HOST,
            'department_id' => $departments['IT']->id,
            'position' => 'Software Engineer',
            'phone' => '081200000004',
        ]);

        $host2 = User::create([
            'name' => 'Sari Wijaya',
            'email' => 'sari@vms.test',
            'password' => 'password',
            'role' => User::ROLE_HOST,
            'department_id' => $departments['FIN']->id,
            'position' => 'Finance Analyst',
            'phone' => '081200000005',
        ]);

        User::create([
            'name' => 'Auditor Internal',
            'email' => 'auditor@vms.test',
            'password' => 'password',
            'role' => User::ROLE_VIEWER,
            'department_id' => $departments['HRD']->id,
            'position' => 'Internal Audit',
        ]);

        /* ---------- Blacklist contoh ---------- */
        Blacklist::create([
            'full_name' => 'Rudi Ditemukan',
            'id_number' => '3201234567890001',
            'company' => 'PT Lama Sekali',
            'reason' => 'Terlibat insiden pencurian aset kantor tahun lalu',
            'type' => 'blacklist',
            'created_by' => $admin->id,
        ]);

        /* ---------- Visitors sample (7 hari terakhir) ---------- */
        $sample = [
            ['John Doe', 'PT XYZ Indonesia', 'pre_registered', 'Diskusi proyek', 'approved', -1, $host->id, 'IT', 10],
            ['Alice Kumala', 'CV Maju Bersama', 'walk_in', 'Menemui HRD', 'checked_in', 0, $host2->id, 'FIN', 9],
            ['Ahmad Fauzi', 'PT Sinar Abadi', 'vendor', 'Maintenance AC', 'checked_out', -2, $host->id, 'OPS', 13],
            ['Maria Fransiska', 'PT Global Niaga', 'pre_registered', 'Presentasi produk', 'pending', 0, $host2->id, 'FIN', 14],
            ['Budi Hartono', 'PT Bangun Jaya', 'delivery', 'Antar dokumen', 'checked_out', -1, $host->id, 'GA', 11],
            ['Cindy Lestari', 'PT Media Kreatif', 'pre_registered', 'Interview kerja', 'approved', 0, $host2->id, 'HRD', 9],
            ['Deni Saputra', 'PT Teknik Utama', 'vendor', 'Instalasi jaringan', 'pending', 0, $host->id, 'IT', 15],
            ['Eka Putri', 'Perorangan', 'walk_in', 'Konsultasi', 'checked_out', -3, $host2->id, 'OPS', 10],
            ['Fajar Nugroho', 'PT Logistik Prima', 'delivery', 'Kurir paket', 'checked_in', -1, $host->id, 'GA', 16],
            ['Gita Anggraini', 'PT Asuransi Sejahtera', 'pre_registered', 'Meeting klaim', 'approved', -2, $host2->id, 'FIN', 10],
            ['Hendra Wijaya', 'PT Konstruksi Kita', 'vendor', 'Survey lokasi', 'pending', 1, $host->id, 'OPS', 9],
            ['Indah Permata', 'CV Digital Kreatif', 'pre_registered', 'Review desain', 'approved', 1, $host2->id, 'IT', 13],
            ['Joko Susilo', 'PT Elektronik Nusantara', 'walk_in', 'Service unit', 'checked_out', -4, $host->id, 'GA', 14],
            ['Kartika Sari', 'PT Farmasi Sehat', 'pre_registered', 'Audit mutual', 'checked_in', -2, $host2->id, 'FIN', 11],
            ['Lukman Hakim', 'PT Pangan Utama', 'vendor', 'Pengecekan gudang', 'rejected', -5, $host->id, 'OPS', 10],
            ['Maya Rahayu', 'Perorangan', 'walk_in', 'Menemui keluarga', 'checked_out', -6, $host2->id, 'HRD', 10],
            ['Nanda Pratama', 'PT Auto Prima', 'delivery', 'Antar sparepart', 'checked_out', -3, $host->id, 'OPS', 9],
            ['Oscar Wijaya', 'PT Reksa Dana', 'pre_registered', 'Investor meeting', 'pending', 2, $host2->id, 'FIN', 10],
        ];

        foreach ($sample as $i => [$name, $company, $type, $purpose, $status, $dayOffset, $hostId, $deptCode, $hour]) {
            $date = now()->addDays($dayOffset);
            $visitor = Visitor::create([
                'code' => 'VIS-' . $date->format('Ymd') . '-' . str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT),
                'full_name' => $name,
                'phone' => '0813' . str_pad((string) random_int(1000000, 9999999), 8, '0'),
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'company' => $company,
                'id_type' => 'KTP',
                'id_number' => '32' . random_int(1000000000000, 9999999999999),
                'person_count' => random_int(1, 3),
                'visit_type' => $type,
                'purpose' => $purpose,
                'visit_date' => $date->toDateString(),
                'visit_time' => sprintf('%02d:00', $hour),
                'duration_minutes' => [30, 60, 90, 120][array_rand([30, 60, 90, 120])],
                'host_id' => $hostId,
                'department_id' => $departments[$deptCode]->id,
                'status' => $status,
                'badge_code' => 'BDG-' . strtoupper(bin2hex(random_bytes(4))),
                'approved_at' => in_array($status, ['approved', 'checked_in', 'checked_out']) ? $date->copy()->subHours(3) : null,
                'approved_by' => in_array($status, ['approved', 'checked_in', 'checked_out']) ? $hostId : null,
                'checked_in_at' => in_array($status, ['checked_in', 'checked_out']) ? $date->copy()->setHour($hour) : null,
                'checked_in_by' => in_array($status, ['checked_in', 'checked_out']) ? $receptionist->id : null,
                'checked_out_at' => $status === 'checked_out' ? $date->copy()->setHour($hour + 2) : null,
                'checked_out_by' => $status === 'checked_out' ? $security->id : null,
                'reject_reason' => $status === 'rejected' ? 'Tidak relevan dengan kebutuhan kantor' : null,
            ]);
        }

        /* ---------- Notifikasi contoh ---------- */
        AppNotification::notify($admin, 'Selamat Datang 🎉', 'Sistem Visitor Management berhasil di-setup. Jangan lupa ganti password default.', 'shield', route('profile.index'));
        AppNotification::notify($host, 'Approval Dibutuhkan', 'Maria Fransiska (PT Global Niaga) menunggu persetujuan Anda.', 'user', route('approvals.index'));
        AppNotification::notify($host, 'Tamu Telah Tiba', 'Alice Kumala dari CV Maju Bersama sudah check-in di reception.', 'check', '#');
        AppNotification::notify($receptionist, 'Registrasi Baru', 'Hendra Wijaya (PT Konstruksi Kita) terdaftar untuk besok.', 'user', route('visitors.index'));
    }
}
