<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Trijaya Solution');
    }

    public function test_login_page_loads_with_inline_captcha(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('captcha-row');
        $response->assertSee('Kode Keamanan');
        $response->assertSee('id="captchaInput"', false);
    }

    public function test_pass_eye_toggle_wired_on_every_page(): void
    {
        // 1) halaman login memuat pass-eye.js & tombol dgn data-target input password
        $html = $this->get('/login')->getContent();
        $this->assertStringContainsString('assets/js/pass-eye.js', $html);
        $this->assertStringContainsString('class="pass-eye" data-target="password"', $html);
        $this->assertStringContainsString('id="password"', $html);

        // 2) layout admin juga memuat pass-eye.js
        $admin = User::factory()->create(['role' => 'admin']);
        foreach (['profile', 'users/create', 'settings'] as $uri) {
            $page = $this->actingAs($admin)->get("/{$uri}")->getContent();
            $this->assertStringContainsString(
                'assets/js/pass-eye.js', $page, "pass-eye.js hilang di /{$uri}"
            );

            // setiap .pass-eye harus punya pasangan input id yang valid
            preg_match_all('/data-target="([^"]+)"/', $page, $targets);
            foreach ($targets[1] as $id) {
                $this->assertStringContainsString(
                    'id="' . $id . '"', $page, "Input #{$id} tidak ditemukan di /{$uri}"
                );
            }
        }
    }

    public function test_captcha_endpoint_returns_png(): void
    {
        $response = $this->get('/captcha');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertNotNull(session('captcha_code'));
        $this->assertEquals(5, strlen(session('captcha_code')));
    }

    public function test_login_fails_with_wrong_captcha(): void
    {
        $user = User::factory()->create([
            'email' => 'captchatest@vms.test',
            'password' => 'password',
        ]);

        $this->get('/captcha'); // generate code

        $response = $this->post('/login', [
            'email' => 'captchatest@vms.test',
            'password' => 'password',
            'captcha' => 'ZZZZZ',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest();
    }

    public function test_login_succeeds_with_correct_captcha(): void
    {
        $user = User::factory()->create([
            'email' => 'okcaptcha@vms.test',
            'password' => 'password',
        ]);

        $this->get('/captcha');
        $code = session('captcha_code');

        $response = $this->post('/login', [
            'email' => 'okcaptcha@vms.test',
            'password' => 'password',
            'captcha' => strtolower($code), // case-insensitive
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_pages_load(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        foreach (['dashboard', 'visitors', 'visitors/create', 'approvals', 'blacklist', 'settings', 'users', 'notifications', 'audit', 'reports', 'profile'] as $uri) {
            $response = $this->actingAs($admin)->get("/{$uri}");
            $response->assertStatus(200, "Failed on /{$uri}");
        }
    }

    public function test_footer_contains_current_year_and_trijaya(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertSee(date('Y'));
        $response->assertSee('https://trijayasolution.com');
        $response->assertSee('Developed by');
    }
}
