<?php

namespace DataForm\Includes;

if (!defined('ABSPATH')) exit;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Helper Functions for Spreadsheet Styling and Writing
 */

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

function styleHeader($sheet, $startCell, $endCell, $fontSize, $bgColor) {
    $range = "{$startCell}:{$endCell}";
    $sheet->getStyle($range)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => $fontSize,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => $bgColor],
        ],
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
            'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        ],
    ]);
}

function addBorders($sheet, $range) {
    $sheet->getStyle($range)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC'],
            ],
        ],
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
