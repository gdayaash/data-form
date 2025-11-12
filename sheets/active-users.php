<?php
namespace DataForm\Sheets;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use DataForm\Includes as Helpers;

function generateActiveUsersSheet(Spreadsheet $spreadsheet, array $months) {
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Active Users');

    $activeLabels = [
        'Categories', 'Workforce count', 'Total registrations',
        'Total usage', 'Unique users', '', 'Quarterly average', '', 'Six months average'
    ];

    Helpers\writeHeaders($sheet, 1, 2, $months);
    Helpers\writeColumn($sheet, 'A', 1, $activeLabels);
    Helpers\fillDummyData($sheet, 2, 2, count($activeLabels), count($months));

    $lastCol = Coordinate::stringFromColumnIndex(1 + count($months));
    Helpers\styleHeader($sheet, 'A1', "{$lastCol}1", 16, '0088CC');
    $sheet->freezePane('B2');
    Helpers\autoSizeColumns($sheet, 1 + count($months));

    return $sheet;
}
