<?php
if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// === MAIN EXCEL LOGIC ===
function dp_handle_form_submissions() {
    if ($_SERVER["REQUEST_METHOD"] !== 'POST') return;

    $client = sanitize_text_field($_POST['client'] ?? '');
    $startDateInput = sanitize_text_field($_POST['start_date'] ?? '');
    $endDateInput = sanitize_text_field($_POST['end_date'] ?? '');

    try {
        $startDateObj = new DateTime($startDateInput . '-01');
        $endDateObj = new DateTime($endDateInput . '-01');
        $endDateObj->modify('last day of this month');

        // --- Months ---
        $months = [];
        $temp = clone $startDateObj;
        while ($temp <= $endDateObj) {
            $months[] = $temp->format("M'y"); // e.g., Jun'25
            $temp->modify('+1 month');
        }

        $spreadsheet = new Spreadsheet();

        // =========================
        // SHEET 1: Active Users
        // =========================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Active Users');

        $activeLabels = [
            'Categories', 'Workforce count', 'Total registrations',
            'Total usage', 'Unique users', '', 'Quarterly average', '', 'Six months average'
        ];

        writeHeaders($sheet1, 1, 2, $months);
        writeColumn($sheet1, 'A', 1, $activeLabels);
        fillDummyData($sheet1, 2, 2, count($activeLabels), count($months));

        // Style Sheet 1
        styleHeader($sheet1, 'A1', Coordinate::stringFromColumnIndex(1 + count($months)) . '1', 16, '0088CC');
        $sheet1->getRowDimension(1)->setRowHeight(28);
        $sheet1->freezePane('B2');
        autoSizeColumns($sheet1, 1 + count($months));

        // =========================
        // SHEET 2: Consultation Data
        // =========================
        $sheet2 = new Worksheet($spreadsheet, 'Consultation Data');
        $spreadsheet->addSheet($sheet2);

        $consultations = ['Counsellor', 'Doctor', 'Dietitian', 'Financial'];
        $headers = array_merge(['Sr No.', 'Consultation Taken'], $months, ['Total']);

        // Write header row
        writeRow($sheet2, 1, 1, $headers);

        $startDataRow = 2;
        foreach ($consultations as $i => $consultation) {
            $rowNum = $startDataRow + $i;

            $sheet2->setCellValue("A{$rowNum}", $i + 1);
            $sheet2->setCellValue("B{$rowNum}", $consultation);

            // Fill month data
            for ($m = 0; $m < count($months); $m++) {
                $col = Coordinate::stringFromColumnIndex(3 + $m);
                $sheet2->setCellValue("{$col}{$rowNum}", rand(10, 100));
            }

            // Row Total
            $firstMonthCol = Coordinate::stringFromColumnIndex(3);
            $lastMonthCol  = Coordinate::stringFromColumnIndex(2 + count($months));
            $totalCol      = Coordinate::stringFromColumnIndex(3 + count($months));
            $sheet2->setCellValue("{$totalCol}{$rowNum}", "=SUM({$firstMonthCol}{$rowNum}:{$lastMonthCol}{$rowNum})");
        }

        // === GRAND TOTAL ROW ===
        $endDataRow = $startDataRow + count($consultations) - 1;
        $grandTotalRow = $endDataRow + 1;
        $sheet2->setCellValue("B{$grandTotalRow}", 'Grand Total');

        for ($m = 0; $m < count($months) + 1; $m++) {
            $col = Coordinate::stringFromColumnIndex(3 + $m);
            $sheet2->setCellValue("{$col}{$grandTotalRow}", "=SUM({$col}{$startDataRow}:{$col}{$endDataRow})");
        }

        // === FORMATTING ===
        $lastDataCol = Coordinate::stringFromColumnIndex(3 + count($months));

        styleHeader($sheet2, 'A1', "{$lastDataCol}1", 14, '4F81BD');
        styleGrandTotal($sheet2, "B{$grandTotalRow}:{$lastDataCol}{$grandTotalRow}");
        addBorders($sheet2, "A1:{$lastDataCol}{$grandTotalRow}");
        alternateRowColor($sheet2, 2, $endDataRow, $lastDataCol);
        autoSizeColumns($sheet2, 3 + count($months));
        $sheet2->freezePane('C2');

        // --- Output File ---
        $filename = 'client-report-' . date('Ymd-His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        wp_die('Error generating Excel file: ' . $e->getMessage());
    }
}
add_action('admin_post_dp_process_form', 'dp_handle_form_submissions');

// =========================
// HELPER FUNCTIONS
// =========================
function writeHeaders($sheet, $startRow, $startColIndex, array $headers) {
    foreach ($headers as $i => $header) {
        $col = Coordinate::stringFromColumnIndex($startColIndex + $i);
        $sheet->setCellValue("{$col}{$startRow}", $header);
    }
}
function writeColumn($sheet, $colLetter, $startRow, array $values) {
    foreach ($values as $i => $val) {
        $sheet->setCellValue("{$colLetter}" . ($startRow + $i), $val);
    }
}
function writeRow($sheet, $row, $startColIndex, array $values) {
    foreach ($values as $i => $val) {
        $col = Coordinate::stringFromColumnIndex($startColIndex + $i);
        $sheet->setCellValue("{$col}{$row}", $val);
    }
}
function fillDummyData($sheet, $startRow, $startColIndex, $rowCount, $colCount) {
    for ($r = 0; $r < $rowCount; $r++) {
        for ($c = 0; $c < $colCount; $c++) {
            $col = Coordinate::stringFromColumnIndex($startColIndex + $c);
            $row = $startRow + $r;
            $sheet->setCellValue("{$col}{$row}", rand(100, 999));
        }
    }
}

// === Styling Utilities ===
function styleHeader($sheet, $startCell, $endCell, $fontSize, $bgColor) {
    $range = "{$startCell}:{$endCell}";
    $sheet->getStyle($range)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => $fontSize],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(28);
}
function styleGrandTotal($sheet, $range) {
    $sheet->getStyle($range)->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [
            'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
        ],
    ]);
}
function addBorders($sheet, $range) {
    $sheet->getStyle($range)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]
        ]
    ]);
}
function alternateRowColor($sheet, $startRow, $endRow, $lastCol) {
    for ($r = $startRow; $r <= $endRow; $r++) {
        if ($r % 2 === 0) {
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                ->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');
        }
    }
}
function autoSizeColumns($sheet, $colCount) {
    for ($col = 1; $col <= $colCount; $col++) {
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
    }
}
