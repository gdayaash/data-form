<?php
namespace DataForm\Sheets;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use DataForm\Includes as Helpers;

class ActiveUsersSheetBuilder {

    public function build(Spreadsheet $spreadsheet, array $months, array $data) {

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Active Users');

        $labels = [
            "Categories",
            "Workforce count",
            "Total registrations",
            "Total usage",
            "Unique users",
            "",
            "Quarterly average",
            "",
            "Six months average"
        ];

        $map = [
            "Workforce count" => "workforce_count",
            "Total registrations" => "total_registrations",
            "Total usage" => "total_usage",
            "Unique users" => "unique_users",
            "Quarterly average" => "quarterly_average",
            "Six months average" => "six_month_average",
        ];

        Helpers\writeHeaders($sheet, 1, 2, $months);
        Helpers\writeColumn($sheet, 'A', 1, $labels);

        $row = 2;

        foreach ($labels as $label) {
            if ($label === "Categories") continue;
            if ($label === "") { $row++; continue; }

            foreach ($months as $mIndex => $month) {

                $col = Coordinate::stringFromColumnIndex(2 + $mIndex);

                $key = $map[$label] ?? null;
                $val = $key ? ($data[$month][$key] ?? 0) : "";

                $sheet->setCellValue("{$col}{$row}", $val);
            }

            $row++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(1 + count($months));
        Helpers\styleHeader($sheet, 'A1', "{$lastCol}1", 20, '0088CC');
        Helpers\autoSizeColumns($sheet, 1 + count($months));

        $sheet->freezePane("B2");
    }
}
