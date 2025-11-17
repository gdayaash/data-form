<?php
namespace DataForm\Sheets;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use DataForm\Includes as Helpers;

function generateActiveUsersSheet(Spreadsheet $spreadsheet, array $months, array $allData) {

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Active Users');

    $activeLabels = [
        'Categories',
        'Workforce count',
        'Total registrations',
        'Total usage',
        'Unique users',
        '',
        'Quarterly average',
        '',
        'Six months average'
    ];

    $categoryMap = [
        'Workforce count'     => 'workforce_count',
        'Total registrations' => 'total_registrations',
        'Total usage'         => 'total_usage',
        'Unique users'        => 'unique_users',
        'Quarterly average'   => 'quarterly_average',
        'Six months average'  => 'six_month_average',
    ];

    Helpers\writeHeaders($sheet, 1, 2, $months);
    Helpers\writeColumn($sheet, 'A', 1, $activeLabels);

    $rowIndex = 2;

    foreach ($activeLabels as $label) {

        if ($label === 'Categories') continue;

        if ($label === '') {
            $rowIndex++;
            continue;
        }

        foreach ($months as $mIndex => $monthLabel) {

            $col = Coordinate::stringFromColumnIndex(2 + $mIndex);
            $value = "";

            if (isset($categoryMap[$label])) {
                $key = $categoryMap[$label];
                $value = $allData[$monthLabel][$key] ?? '';
            }

            $sheet->setCellValue("{$col}{$rowIndex}", $value);
        }

        $rowIndex++;
    }

    $lastCol = Coordinate::stringFromColumnIndex(1 + count($months));

    Helpers\styleHeader($sheet, 'A1', "{$lastCol}1", 20, '0088CC');
    Helpers\autoSizeColumns($sheet, 1 + count($months));

    $sheet->freezePane('B2');
}
