<?php
namespace DataForm\Sheets;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DataForm\Includes as Helpers;

class ConsultationDataSheetBuilder {

    public function build(Spreadsheet $spreadsheet, array $months, array $data) {

        $sheet = new Worksheet($spreadsheet, "Consultation Data");
        $spreadsheet->addSheet($sheet);

        $consults = ["Counsellor", "Doctor", "Dietitian", "Financial"];

        $headers = array_merge(["Sr No.", "Consultation Taken"], $months, ["Total"]);
        Helpers\writeRow($sheet, 1, 1, $headers);

        $rowStart = 2;

        foreach ($consults as $i => $label) {

            $r = $rowStart + $i;

            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", $label);

            foreach ($months as $mIndex => $month) {
                $col = Coordinate::stringFromColumnIndex(3 + $mIndex);
                $sheet->setCellValue("{$col}{$r}", rand(10, 100)); // Placeholder
            }

            $firstCol = Coordinate::stringFromColumnIndex(3);
            $lastCol  = Coordinate::stringFromColumnIndex(2 + count($months));
            $totalCol = Coordinate::stringFromColumnIndex(3 + count($months));

            $sheet->setCellValue("{$totalCol}{$r}", "=SUM({$firstCol}{$r}:{$lastCol}{$r})");
        }

        $sheet->freezePane("C2");
    }
}
