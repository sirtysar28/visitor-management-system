<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Vms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allCached();

        return view('settings.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'app_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        Setting::set('app_name', $data['app_name']);
        Setting::set('company_name', $data['company_name'] ?? '');

        if ($request->hasFile('app_logo')) {
            // Remove old file
            $old = Setting::get('app_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('app_logo')->store('branding', 'public');
            Setting::set('app_logo', $path);
        }

        // Optional logo removal
        if ($request->boolean('remove_logo')) {
            $old = Setting::get('app_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('app_logo', null);
        }

        Vms::audit('update', 'settings', 'Memperbarui pengaturan umum / logo');

        return back()->with('success', 'Pengaturan umum berhasil disimpan.');
    }

    public function updateSmtp(Request $request)
    {
        $data = $request->validate([
            'smtp_host' => ['required', 'string', 'max:150'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_encryption' => ['required', 'in:tls,ssl,none'],
            'smtp_username' => ['nullable', 'string', 'max:150'],
            'smtp_password' => ['nullable', 'string', 'max:150'],
            'smtp_from_address' => ['required', 'email'],
            'smtp_from_name' => ['required', 'string', 'max:100'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Vms::audit('update', 'settings', 'Memperbarui pengaturan SMTP');

        return back()->with('success', 'Pengaturan SMTP berhasil disimpan.');
    }

    public function testSmtp(Request $request)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            Config::set('mail.mailers.smtp', Setting::smtpConfig());
            Config::set('mail.from.address', (string) Setting::get('smtp_from_address', config('mail.from.address')));
            Config::set('mail.from.name', (string) Setting::get('smtp_from_name', config('mail.from.name')));

            Mail::raw(
                "Ini adalah email uji coba dari " . Vms::appName() . ".\n\nJika Anda menerima email ini, konfigurasi SMTP sudah benar. ✅",
                function ($message) use ($data) {
                    $message->to($data['test_email'])->subject('Test SMTP - ' . Vms::appName());
                }
            );

            Vms::audit('test_smtp', 'settings', 'Uji coba SMTP ke ' . $data['test_email']);

            return back()->with('success', 'Email uji coba berhasil dikirim ke ' . $data['test_email'] . '.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
