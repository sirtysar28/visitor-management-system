<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Penulis file .xlsx native (tanpa package apa pun) — hanya ZipArchive bawaan PHP.
 * Digunakan sebagai fallback bila phpoffice/phpspreadsheet belum terinstall di server.
 */
class NativeXlsx
{
    private array $meta = [];
    private array $headers = [];
    private array $rows = [];
    private string $footer = '';

    public static function create(string $title = 'Laporan'): self
    {
        return new self($title);
    }

    public function __construct(private string $title = 'Laporan')
    {
    }

    public function meta(array $lines): self
    {
        $this->meta = $lines;

        return $this;
    }

    public function headers(array $headers): self
    {
        $this->headers = array_values($headers);

        return $this;
    }

    public function rows(array $rows): self
    {
        $this->rows = array_values($rows);

        return $this;
    }

    public function footer(string $text): self
    {
        $this->footer = $text;

        return $this;
    }

    /* ------------------------------------------------- */

    public function response(string $fileName): StreamedResponse
    {
        $parts = $this->build();

        return response()->streamDownload(function () use ($parts) {
            $zip = new \ZipArchive();
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx');

            $zip->open($tmp, \ZipArchive::OVERWRITE);
            foreach ($parts as $name => $xml) {
                $zip->addFromString($name, $xml);
            }
            $zip->close();

            readfile($tmp);
            @unlink($tmp);
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /** Kembalikan seluruh part XML (untuk testing). @return array<string,string> */
    public function build(): array
    {
        $cols = max(count($this->headers), 1);
        $lastCol = self::colLetter($cols);

        $sheetRows = [];
        $rowNum = 0;

        /* ---- Judul + meta (merge semua kolom) ---- */
        $metaAll = array_merge([$this->title], $this->meta);
        $mergeCells = [];
        foreach ($metaAll as $line) {
            $rowNum++;
            $style = $rowNum === 1 ? '2' : '3'; // judul / meta
            $sheetRows[] = '<row r="' . $rowNum . '" ht="' . ($rowNum === 1 ? '22' : '16') . '" customHeight="1">'
                . $this->cell(1, $rowNum, $line, $style)
                . '</row>';
            $mergeCells[] = '<mergeCell ref="A' . $rowNum . ':' . $lastCol . $rowNum . '"/>';
        }

        /* ---- Baris kosong pemisah ---- */
        $rowNum++;

        /* ---- Header tabel ---- */
        $rowNum++;
        $headerRow = $rowNum;
        $cells = '';
        foreach ($this->headers as $i => $h) {
            $cells .= $this->cell($i + 1, $rowNum, (string) $h, '1');
        }
        $sheetRows[] = '<row r="' . $rowNum . '" ht="20" customHeight="1">' . $cells . '</row>';

        /* ---- Data (zebra stripe) ---- */
        foreach ($this->rows as $ri => $row) {
            $rowNum++;
            $style = ($ri % 2 === 1) ? '4' : '5'; // zebra / normal
            $cells = '';
            foreach (array_values($row) as $ci => $value) {
                $cells .= $this->cell($ci + 1, $rowNum, $value, $style);
            }
            $sheetRows[] = '<row r="' . $rowNum . '">' . $cells . '</row>';
        }

        /* ---- Footer credit ---- */
        if ($this->footer !== '') {
            $rowNum += 2;
            $sheetRows[] = '<row r="' . $rowNum . '">' . $this->cell(1, $rowNum, $this->footer, '6') . '</row>';
            $mergeCells[] = '<mergeCell ref="A' . $rowNum . ':' . $lastCol . $rowNum . '"/>';
        }

        /* ---- Lebar kolom ---- */
        $widths = [];
        foreach ($this->headers as $i => $h) {
            $w = mb_strlen($h);
            foreach ($this->rows as $row) {
                $v = $row[$i] ?? '';
                $w = max($w, mb_strlen((string) $v));
            }
            $widths[$i + 1] = min(max($w + 3, 8), 42);
        }
        $colsXml = '<cols>';
        foreach ($widths as $idx => $w) {
            $colsXml .= '<col min="' . $idx . '" max="' . $idx . '" width="' . $w . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0">'
            . '<pane ySplit="' . $headerRow . '" topLeftCell="A' . ($headerRow + 1) . '" activePane="bottomLeft" state="frozen"/>'
            . '</sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15"/>'
            . $colsXml
            . '<sheetData>' . implode('', $sheetRows) . '</sheetData>'
            . ($mergeCells ? '<mergeCells count="' . count($mergeCells) . '">' . implode('', $mergeCells) . '</mergeCells>' : '')
            . '</worksheet>';

        return [
            '[Content_Types].xml' => $this->contentTypes(),
            '_rels/.rels' => $this->rootRels(),
            'xl/workbook.xml' => $this->workbook(),
            'xl/_rels/workbook.xml.rels' => $this->workbookRels(),
            'xl/styles.xml' => $this->styles(),
            'xl/worksheets/sheet1.xml' => $sheetXml,
        ];
    }

    /* ------------------------------------------------- */

    private function cell(int $colIdx, int $rowNum, $value, string $style): string
    {
        $ref = self::colLetter($colIdx) . $rowNum;

        // angka murni → numeric cell (biar bisa dihitung di Excel)
        if (is_int($value) || is_float($value)) {
            return '<c r="' . $ref . '" s="' . $style . '"><v>' . $value . '</v></c>';
        }

        $str = (string) $value;
        if ($str !== '' && preg_match('/^-?\d+(\.\d+)?$/', $str) && !str_starts_with($str, '0')) {
            return '<c r="' . $ref . '" s="' . $style . '"><v>' . $str . '</v></c>';
        }

        $t = htmlspecialchars($str, ENT_XML1 | ENT_COMPAT, 'UTF-8');

        return '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">' . $t . '</t></is></c>';
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . htmlspecialchars(mb_substr($this->title, 0, 28), ENT_XML1) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="5">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'                                   // 0 normal
            . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'          // 1 header putih bold
            . '<font><b/><sz val="14"/><color rgb="FF0F172A"/><name val="Calibri"/></font>'          // 2 judul
            . '<font><sz val="10"/><color rgb="FF64748B"/><name val="Calibri"/></font>'              // 3 meta
            . '<font><i/><sz val="9"/><color rgb="FF94A3B8"/><name val="Calibri"/></font>'           // 4 footer
            . '</fonts>'
            . '<fills count="4">'
            . '<fill><patternFill patternType="none"/></fill>'                                       // 0 (wajib)
            . '<fill><patternFill patternType="gray125"/></fill>'                                    // 1 (wajib)
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF2563EB"/><bgColor indexed="64"/></patternFill></fill>'   // 2 biru
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/><bgColor indexed="64"/></patternFill></fill>'   // 3 zebra
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFE2E8F0"/></left>'
            . '<right style="thin"><color rgb="FFE2E8F0"/></right>'
            . '<top style="thin"><color rgb="FFE2E8F0"/></top>'
            . '<bottom style="thin"><color rgb="FFE2E8F0"/></bottom>'
            . '<diagonal/>'
            . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="7">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'                                                      // 0 default
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 1 header
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'  // 2 judul
            . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center"/></xf>'                   // 3 meta
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"/><alignment vertical="center"/></xf>'                       // 4 zebra
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'                   // 5 normal
            . '<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"/></xf>'                                                                    // 6 footer
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    /** 1 → A, 2 → B, ... 27 → AA. */
    public static function colLetter(int $n): string
    {
        $s = '';
        while ($n > 0) {
            $mod = ($n - 1) % 26;
            $s = chr(65 + $mod) . $s;
            $n = intdiv($n - 1 - $mod, 26);
        }

        return $s;
    }
}
