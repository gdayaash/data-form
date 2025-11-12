<?php

namespace DataForm\Controllers;

if (!defined('ABSPATH')) exit;

use DataForm\Sheets;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once plugin_dir_path(__FILE__) . '../sheets/active-users.php';
require_once plugin_dir_path(__FILE__) . '../sheets/consultation-data.php';

function dp_handle_form_submissions() {
    if ($_SERVER["REQUEST_METHOD"] !== 'POST') return;

    $client = sanitize_text_field($_POST['client'] ?? '');
    $startDateInput = sanitize_text_field($_POST['start_date'] ?? '');
    $endDateInput = sanitize_text_field($_POST['end_date'] ?? '');

    $startDateObj = new DateTime($startDateInput . '-01');
    $endDateObj = new DateTime($endDateInput . '-01');
    $endDateObj->modify('last day of this month');

    $months = [];
    $temp = clone $startDateObj;
    while ($temp <= $endDateObj) {
        $months[] = $temp->format("M'y");
        $temp->modify('+1 month');
    }

    $spreadsheet = new Spreadsheet();

    // Generate individual sheets
    Sheets\generateActiveUsersSheet($spreadsheet, $months);
    Sheets\generateConsultationDataSheet($spreadsheet, $months);

    // Output file
    $filename = 'client-report-' . sanitize_title($client) . '-' . date('Ymd-His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

add_action('admin_post_dp_process_form', __NAMESPACE__ . '\\dp_handle_form_submissions');
