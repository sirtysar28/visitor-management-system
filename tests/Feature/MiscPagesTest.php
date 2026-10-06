<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiscPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_blacklist_page_renders_seeded_data(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();
        $html = $this->actingAs($admin)->get('/blacklist')->getContent();

        $this->assertStringContainsString('Rudi Ditemukan', $html);
        $this->assertStringContainsString('Tambah Blacklist', $html);
    }

    public function test_blacklist_store_and_toggle_and_destroy(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();

        $this->actingAs($admin)->post('/blacklist', [
            'full_name' => 'Test Blacklist',
            'reason' => 'Uji coba',
            'type' => 'watchlist',
        ])->assertRedirect();

        $entry = \App\Models\Blacklist::where('full_name', 'Test Blacklist')->first();
        $this->assertNotNull($entry);

        $this->actingAs($admin)->post("/blacklist/{$entry->id}/toggle")->assertRedirect();
        $this->assertFalse($entry->fresh()->is_active);

        $this->actingAs($admin)->delete("/blacklist/{$entry->id}")->assertRedirect();
        $this->assertNull($entry->fresh());
    }

    public function test_notifications_page_renders(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();
        $html = $this->actingAs($admin)->get('/notifications')->getContent();

        $this->assertStringContainsString('Selamat Datang', $html);
    }

    public function test_users_crud(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();

        $this->actingAs($admin)->get('/users')->assertStatus(200)->assertSee('Budi Santoso');

        $this->actingAs($admin)->post('/users', [
            'name' => 'User Baru',
            'email' => 'baru@vms.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'host',
        ])->assertRedirect();

        $user = User::where('email', 'baru@vms.test')->first();
        $this->assertNotNull($user);

        // edit tanpa ganti password
        $this->actingAs($admin)->put("/users/{$user->id}", [
            'name' => 'User Baru Edit',
            'email' => 'baru@vms.test',
            'role' => 'security',
        ])->assertRedirect();
        $this->assertEquals('User Baru Edit', $user->fresh()->name);

        // tidak bisa hapus akun sendiri
        $this->actingAs($admin)->delete("/users/{$admin->id}")->assertSessionHas('error');
    }

    public function test_audit_log_records_activity(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();
        $this->actingAs($admin)->post('/logout');

        $html = $this->actingAs($admin)->get('/audit')->getContent();
        $this->assertStringContainsString('logout', $html);
    }

    public function test_reports_page_filters(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();
        $html = $this->actingAs($admin)->get('/reports?from=' . now()->subDays(10)->toDateString() . '&to=' . now()->toDateString())->getContent();

        $this->assertStringContainsString('John Doe', $html);
    }

    public function test_pwa_assets_exist(): void
    {
        // file statis dilayani webserver (artisan serve / nginx / apache)
        foreach (['manifest.json', 'sw.js', 'assets/img/icons/icon-192.png', 'assets/img/icons/icon-512.png'] as $file) {
            $this->assertFileExists(public_path($file), "Missing: {$file}");
        }

        // halaman offline adalah route Laravel
        $this->get('/offline')->assertStatus(200);
    }
}
