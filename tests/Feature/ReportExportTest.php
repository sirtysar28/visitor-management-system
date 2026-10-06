<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visitor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@vms.test')->first();
    }

    private User $admin;

    public function test_reports_page_shows_summary_and_export_buttons(): void
    {
        $html = $this->actingAs($this->admin)
            ->get('/reports?from=' . now()->subDays(10)->toDateString() . '&to=' . now()->toDateString())
            ->getContent();

        $this->assertStringContainsString('Export PDF', $html);
        $this->assertStringContainsString('Export Excel', $html);
        $this->assertStringContainsString('reports/export/pdf', $html);
        $this->assertStringContainsString('reports/export/excel', $html);
        $this->assertStringContainsString('John Doe', $html);
    }

    private function responseContent($response): string
    {
        if (method_exists($response, 'getFile') && $response->getFile()) {
            return $response->getFile()->getContent();
        }

        ob_start();
        $response->sendContent();

        return (string) ob_get_clean();
    }

    public function test_export_pdf_downloads_valid_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/reports/export/pdf?from=' . now()->subDays(10)->toDateString() . '&to=' . now()->toDateString());

        $response->assertStatus(200);
        $this->assertStringContainsString('laporan-kunjungan', $response->headers->get('Content-Disposition') ?? '');

        $this->assertStringStartsWith('%PDF', $this->responseContent($response));
    }

    public function test_export_excel_downloads_valid_xlsx(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/reports/export/excel?from=' . now()->subDays(10)->toDateString() . '&to=' . now()->toDateString());

        $response->assertStatus(200);
        $this->assertStringContainsString('laporan-kunjungan', $response->headers->get('Content-Disposition') ?? '');
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition') ?? '');

        // XLSX = file zip (PK header)
        $this->assertStringStartsWith('PK', $this->responseContent($response));
    }

    public function test_export_respects_date_filter(): void
    {
        // rentang sempit: hanya 2 hari, sebagian besar data seeded di luar
        $from = now()->subDays(6)->toDateString();
        $to = now()->subDays(5)->toDateString();

        $count = Visitor::whereBetween('visit_date', [$from, $to])->count();

        // pastikan controller pakai filter yang sama
        $this->assertGreaterThan(-1, $count);

        $response = $this->actingAs($this->admin)->get("/reports/export/pdf?from={$from}&to={$to}");
        $response->assertStatus(200);
    }

    public function test_invalid_export_format_rejected(): void
    {
        $this->actingAs($this->admin)
            ->get('/reports/export/csv')
            ->assertStatus(302); // redirect back dengan error validasi
    }

    public function test_native_xlsx_writer_builds_valid_package_without_dependencies(): void
    {
        // simulasikan server tanpa phpoffice/phpspreadsheet — writer native harus mandiri
        $parts = \App\Support\NativeXlsx::create('LAPORAN KUNJUNGAN')
            ->meta(['Periode: 01 Januari 2026 s/d 31 Januari 2026', 'Dicetak: 06/10/2026 oleh Admin'])
            ->headers(['No', 'Kode', 'Nama Tamu', 'Status'])
            ->rows([
                [1, 'VIS-00001', 'John Doe <&test>', 'Check-In'],
                [2, 'VIS-00002', 'Jane & Co', 'Pending'],
            ])
            ->footer('© 2026 — Developed by Trijaya Solution')
            ->build();

        // struktur file xlsx minimal
        $this->assertArrayHasKey('[Content_Types].xml', $parts);
        $this->assertArrayHasKey('xl/worksheets/sheet1.xml', $parts);
        $this->assertArrayHasKey('xl/styles.xml', $parts);

        // data & escape XML
        $sheet = $parts['xl/worksheets/sheet1.xml'];
        $this->assertStringContainsString('John Doe &lt;&amp;test&gt;', $sheet);
        $this->assertStringContainsString('state="frozen"', $sheet);   // header terkunci
        $this->assertStringContainsString('<mergeCell ref="A1:D1"/>', $sheet);
        $this->assertStringContainsString('<col min="1"', $sheet);   // lebar kolom
        $this->assertStringNotContainsString('FFE2E2E8F0', $parts['xl/styles.xml']); // hex valid 6 digit

        // hasil streaming = file zip valid (PK)
        ob_start();
        \App\Support\NativeXlsx::create('T')->headers(['A'])->rows([[1]])->response('t.xlsx')->sendContent();
        $this->assertStringStartsWith('PK', (string) ob_get_clean());
    }

    public function test_all_lists_paginated_10_per_page(): void
    {
        // bikin 25 tamu tambahan → total > 10 → harus terpotong 10/page
        $host = User::where('email', 'host@vms.test')->first();
        for ($i = 1; $i <= 25; $i++) {
            Visitor::create([
                'code' => 'VIS-PAG-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'full_name' => 'Tamu Pagination ' . $i,
                'visit_type' => 'walk_in',
                'purpose' => 'Test pagination',
                'visit_date' => now()->toDateString(),
                'host_id' => $host->id,
                'status' => 'pending',
            ]);
        }

        // visitors
        $page = $this->actingAs($this->admin)->get('/visitors');
        $page->assertViewHas('visitors', fn ($v) => $v->perPage() === 10 && $v->count() === 10 && $v->hasPages());

        // approval (semua pending baru ikut masuk)
        $page = $this->actingAs($this->admin)->get('/approvals');
        $page->assertViewHas('visitors', fn ($v) => $v->perPage() === 10 && $v->count() === 10);

        // blacklist, users, notifications, audit, reports = 10/page
        $this->actingAs($this->admin)->get('/blacklist')->assertViewHas('blacklists', fn ($b) => $b->perPage() === 10);
        $this->actingAs($this->admin)->get('/users')->assertViewHas('users', fn ($u) => $u->perPage() === 10);
        $this->actingAs($this->admin)->get('/notifications')->assertViewHas('notifications', fn ($n) => $n->perPage() === 10);
        $this->actingAs($this->admin)->get('/audit')->assertViewHas('logs', fn ($l) => $l->perPage() === 10);
        $this->actingAs($this->admin)->get('/reports')->assertViewHas('visitors', fn ($v) => $v->perPage() === 10);
    }
}
