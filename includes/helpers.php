<?php

namespace DataForm\Includes;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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

function styleHeader($sheet, $startCell, $endCell, $fontSize, $bgColor) {
    $sheet->getStyle("$startCell:$endCell")->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => $fontSize],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
}

function styleData($sheet, $startCell, $endCell, $size) {
    $sheet->getStyle("$startCell:$endCell")->applyFromArray([
        'font' => ['size' => $size]
    ]);
}

function addBorders($sheet, $range) {
    $sheet->getStyle($range)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
}

function autoSizeColumns($sheet, $count) {
    for ($i = 1; $i <= $count; $i++) {
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
    }
}
