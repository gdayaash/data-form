<?php
namespace DataForm\Sheets;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DataForm\Includes as Helpers;

function generateConsultationDataSheet(Spreadsheet $spreadsheet, array $months) {
    $sheet = new Worksheet($spreadsheet, 'Consultation Data');
    $spreadsheet->addSheet($sheet);

    $consultations = ['Counsellor', 'Doctor', 'Dietitian', 'Financial'];
    $headers = array_merge(['Sr No.', 'Consultation Taken'], $months, ['Total']);
    Helpers\writeRow($sheet, 1, 1, $headers);

    $startRow = 2;
    foreach ($consultations as $i => $consultation) {
        $row = $startRow + $i;
        $sheet->setCellValue("A{$row}", $i + 1);
        $sheet->setCellValue("B{$row}", $consultation);

        for ($m = 0; $m < count($months); $m++) {
            $col = Coordinate::stringFromColumnIndex(3 + $m);
            $sheet->setCellValue("{$col}{$row}", rand(10, 100));
        }

        $firstMonthCol = Coordinate::stringFromColumnIndex(3);
        $lastMonthCol = Coordinate::stringFromColumnIndex(2 + count($months));
        $totalCol = Coordinate::stringFromColumnIndex(3 + count($months));
        $sheet->setCellValue("{$totalCol}{$row}", "=SUM({$firstMonthCol}{$row}:{$lastMonthCol}{$row})");
    }

    $endRow = $startRow + count($consultations) - 1;
    $grandTotalRow = $endRow + 1;
    $sheet->setCellValue("B{$grandTotalRow}", 'Grand Total');

    for ($m = 0; $m < count($months) + 1; $m++) {
        $col = Coordinate::stringFromColumnIndex(3 + $m);
        $sheet->setCellValue("{$col}{$grandTotalRow}", "=SUM({$col}{$startRow}:{$col}{$endRow})");
    }

    $lastCol = Coordinate::stringFromColumnIndex(3 + count($months));
    Helpers\styleHeader($sheet, 'A1', "{$lastCol}1", 14, '4F81BD');
    Helpers\styleGrandTotal($sheet, "B{$grandTotalRow}:{$lastCol}{$grandTotalRow}");
    Helpers\addBorders($sheet, "A1:{$lastCol}{$grandTotalRow}");
    Helpers\alternateRowColor($sheet, 2, $endRow, $lastCol);
    Helpers\autoSizeColumns($sheet, 3 + count($months));
    $sheet->freezePane('C2');

    return $sheet;
}
