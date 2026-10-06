<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenderedPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_dashboard_renders_charts_and_stats(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();

        $html = $this->actingAs($admin)->get('/dashboard')->getContent();

        $this->assertStringContainsString('id="barChart"', $html);
        $this->assertStringContainsString('id="vennDiagram"', $html);
        $this->assertStringContainsString('id="statusChart"', $html);
        $this->assertStringContainsString('data-count', $html);
        $this->assertStringContainsString('chart.umd.min.js', $html);
    }

    public function test_visitor_show_renders_qr_code(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();
        $visitor = \App\Models\Visitor::first();

        $html = $this->actingAs($admin)->get("/visitors/{$visitor->id}")->getContent();

        $this->assertStringContainsString('<svg', $html); // QR svg
        $this->assertStringContainsString($visitor->code, $html);
        $this->assertStringContainsString('QR Badge Tamu', $html);
    }

    public function test_visitor_registration_and_checkin_flow(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();

        // create
        $response = $this->actingAs($admin)->post('/visitors', [
            'full_name' => 'Test Tamu Flow',
            'visit_type' => 'pre_registered',
            'purpose' => 'Testing flow',
            'visit_date' => now()->toDateString(),
            'visit_time' => '10:00',
            'person_count' => 2,
            'host_id' => User::where('email', 'host@vms.test')->first()->id,
        ]);
        $response->assertRedirect();

        $visitor = \App\Models\Visitor::where('full_name', 'Test Tamu Flow')->first();
        $this->assertNotNull($visitor);
        $this->assertEquals('pending', $visitor->status);

        // blacklist guard
        $blocked = $this->actingAs($admin)->post('/visitors', [
            'full_name' => 'Rudi Ditemukan',
            'visit_type' => 'walk_in',
            'purpose' => 'Mencoba masuk',
            'visit_date' => now()->toDateString(),
        ]);
        $blocked->assertSessionHas('error');

        // approve
        $approve = $this->actingAs($admin)->post("/approvals/{$visitor->id}/decide", ['decision' => 'approve']);
        $approve->assertRedirect();
        $this->assertEquals('approved', $visitor->fresh()->status);

        // check-in
        $this->actingAs($admin)->post("/visitors/{$visitor->id}/check-in");
        $this->assertEquals('checked_in', $visitor->fresh()->status);
        $this->assertNotNull($visitor->fresh()->checked_in_at);

        // check-out
        $this->actingAs($admin)->post("/visitors/{$visitor->id}/check-out");
        $this->assertEquals('checked_out', $visitor->fresh()->status);
        $this->assertNotNull($visitor->fresh()->checked_out_at);
    }

    public function test_settings_logo_upload_and_smtp(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();

        // general
        $file = \Illuminate\Http\Testing\File::image('logo.png', 100, 100);
        $this->actingAs($admin)->post('/settings/general', [
            'app_name' => 'VMS Kita',
            'company_name' => 'PT Kita',
            'app_logo' => $file,
        ])->assertRedirect();

        $this->assertEquals('VMS Kita', \App\Models\Setting::get('app_name'));
        $this->assertNotNull(\App\Models\Setting::get('app_logo'));
        $this->assertFileExists(storage_path('app/public/' . \App\Models\Setting::get('app_logo')));

        // logo muncul di landing
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString(\App\Models\Setting::get('app_logo'), $html);

        // smtp
        $this->actingAs($admin)->post('/settings/smtp', [
            'smtp_host' => 'smtp.test.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'smtp_username' => 'user@test.com',
            'smtp_password' => 'secret',
            'smtp_from_address' => 'noreply@test.com',
            'smtp_from_name' => 'VMS Test',
        ])->assertRedirect();

        $this->assertEquals('smtp.test.com', \App\Models\Setting::get('smtp_host'));
    }

    public function test_host_role_restriction(): void
    {
        $host = User::where('email', 'host@vms.test')->first();

        // host tidak boleh akses settings & users
        $this->actingAs($host)->get('/settings')->assertStatus(403);
        $this->actingAs($host)->get('/users')->assertStatus(403);

        // host bisa akses visitors
        $this->actingAs($host)->get('/visitors')->assertStatus(200);
    }

    public function test_notifications_poll(): void
    {
        $admin = User::where('email', 'admin@vms.test')->first();
        $response = $this->actingAs($admin)->getJson('/notifications/poll');

        $response->assertStatus(200);
        $this->assertArrayHasKey('unread', $response->json());
        $this->assertGreaterThan(0, $response->json('unread'));
    }
}
