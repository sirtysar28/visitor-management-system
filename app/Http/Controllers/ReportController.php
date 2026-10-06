<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Support\NativeXlsx;
use App\Support\Vms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportController extends Controller
{
    /** Halaman laporan + filter tanggal/status. */
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $visitors = $this->baseQuery($request, $from, $to)
            ->orderBy('visit_date')
            ->orderBy('visit_time')
            ->paginate(10)
            ->withQueryString();

        // ringkasan untuk kartu info
        $summary = $this->summary($request, $from, $to);

        return view('reports.index', [
            'visitors' => $visitors,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'statuses' => Visitor::STATUSES,
            'summary' => $summary,
        ]);
    }

    /** Export PDF / Excel sesuai filter aktif. */
    public function export(Request $request, string $format)
    {
        $request->merge(['format' => $format]);

        $data = $request->validate([
            'format' => ['required', 'in:pdf,excel'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:' . implode(',', array_keys(Visitor::STATUSES))],
        ]);

        [$from, $to] = $this->range($request);

        $visitors = $this->baseQuery($request, $from, $to)
            ->orderBy('visit_date')
            ->orderBy('visit_time')
            ->get();

        $fileName = 'laporan-kunjungan_' . $from->format('Ymd') . '-' . $to->format('Ymd');

        Vms::audit('export', 'report', 'Export laporan kunjungan (' . strtoupper($data['format']) . ') ' . $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y'));

        if ($data['format'] === 'pdf') {
            // Pakai DomPDF bila terinstall; fallback: halaman cetak (Save as PDF dari browser)
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = Pdf::loadView('reports.pdf', [
                    'visitors' => $visitors,
                    'from' => $from,
                    'to' => $to,
                    'summary' => $this->summary($request, $from, $to),
                ])->setPaper('a4', 'landscape');

                return $pdf->download($fileName . '.pdf');
            }

            return view('reports.print', [
                'visitors' => $visitors,
                'from' => $from,
                'to' => $to,
                'summary' => $this->summary($request, $from, $to),
            ]);
        }

        return $this->excel($visitors, $from, $to, $fileName . '.xlsx');
    }

    /* ------------------------------------------------- */

    private function range(Request $request): array
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }
        // pastikan inclusive sampai akhir hari
        $to = $to->copy()->endOfDay();

        return [$from, $to];
    }

    private function baseQuery(Request $request, Carbon $from, Carbon $to)
    {
        return Visitor::with(['host', 'department'])
            ->whereBetween('visit_date', [$from->toDateString(), $to->toDateString()])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v));
    }

    private function summary(Request $request, Carbon $from, Carbon $to): array
    {
        $base = $this->baseQuery($request, $from, $to);

        return [
            'total' => (clone $base)->count(),
            'inside' => (clone $base)->where('status', Visitor::STATUS_CHECKED_IN)->count(),
            'done' => (clone $base)->where('status', Visitor::STATUS_CHECKED_OUT)->count(),
            'pending' => (clone $base)->where('status', Visitor::STATUS_PENDING)->count(),
            'rejected' => (clone $base)->where('status', Visitor::STATUS_REJECTED)->count(),
        ];
    }

    private function excel($visitors, Carbon $from, Carbon $to, string $fileName)
    {
        $rows = $visitors->map(fn ($v, $i) => [
            $i + 1,
            $v->code,
            $v->full_name,
            $v->company ?: '-',
            $v->visitTypeLabel(),
            $v->purpose ?: '-',
            $v->visit_date->format('d/m/Y'),
            $v->visit_time?->format('H:i') ?? '-',
            $v->host?->name ?? '-',
            $v->checked_in_at?->format('d/m H:i') ?? '-',
            $v->statusLabel(),
        ])->all();

        $headers = ['No', 'Kode', 'Nama Tamu', 'Perusahaan', 'Tipe', 'Tujuan', 'Tanggal', 'Jam', 'Host', 'Check-In', 'Status'];

        // Pakai PhpSpreadsheet bila terinstall; fallback: penulis XLSX native (zero-dependency)
        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->excelPackage($rows, $headers, $from, $to, $fileName);
        }

        return NativeXlsx::create('LAPORAN KUNJUNGAN — ' . strtoupper(Vms::appName()))
            ->meta([
                'Periode: ' . $from->translatedFormat('d F Y') . ' s/d ' . $to->translatedFormat('d F Y'),
                'Dicetak: ' . now()->translatedFormat('d F Y H:i') . ' oleh ' . auth()->user()->name,
            ])
            ->headers($headers)
            ->rows($rows)
            ->footer('© ' . date('Y') . ' ' . Vms::appName() . ' — Developed by Trijaya Solution (trijayasolution.com)')
            ->response($fileName);
    }

    /** Export XLSX memakai phpoffice/phpspreadsheet (tampilan lebih kaya). */
    private function excelPackage(array $rows, array $headers, Carbon $from, Carbon $to, string $fileName)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kunjungan');

        /* ---------- Judul ---------- */
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN — ' . strtoupper(Vms::appName()));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'Periode: ' . $from->translatedFormat('d F Y') . ' s/d ' . $to->translatedFormat('d F Y'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        $sheet->mergeCells('A3:K3');
        $sheet->setCellValue('A3', 'Dicetak: ' . now()->translatedFormat('d F Y H:i') . ' oleh ' . auth()->user()->name);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        /* ---------- Header tabel ---------- */
        $row = 5;
        foreach (range('A', 'K') as $i => $col) {
            $sheet->setCellValue($col . $row, $headers[$i]);
        }
        $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        /* ---------- Baris data ---------- */
        $row = 5;
        foreach ($rows as $dataRow) {
            $row++;
            $sheet->fromArray($dataRow, null, 'A' . $row);
        }

        /* ---------- Style tabel ---------- */
        $lastRow = $row;
        if ($lastRow > 5) {
            $sheet->getStyle('A5:K' . $lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $sheet->getStyle('A5:K' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            // zebra stripe
            for ($r = 7; $r <= $lastRow; $r += 2) {
                $sheet->getStyle('A' . $r . ':K' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                ]);
            }
        }

        /* ---------- Auto width ---------- */
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getRowDimension(5)->setRowHeight(20);
        $sheet->freezePane('A6');

        /* ---------- Footer credit ---------- */
        $footerRow = $lastRow + 2;
        $sheet->mergeCells('A' . $footerRow . ':K' . $footerRow);
        $sheet->setCellValue('A' . $footerRow, '© ' . date('Y') . ' ' . Vms::appName() . ' — Developed by Trijaya Solution (trijayasolution.com)');
        $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF94A3B8'));

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
