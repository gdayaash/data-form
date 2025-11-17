<?php
namespace DataForm\Sheets;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DataForm\Includes as Helpers;

function generateConsultationDataSheet(Spreadsheet $spreadsheet, array $months, array $allData) {

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

        foreach ($months as $m => $month) {
            $col = Coordinate::stringFromColumnIndex(3 + $m);
            $sheet->setCellValue("{$col}{$row}", rand(10, 100));
        }

        $first = Coordinate::stringFromColumnIndex(3);
        $last  = Coordinate::stringFromColumnIndex(2 + count($months));
        $total = Coordinate::stringFromColumnIndex(3 + count($months));

        $sheet->setCellValue("{$total}{$row}", "=SUM({$first}{$row}:{$last}{$row})");
    }

    $sheet->freezePane('C2');
}
