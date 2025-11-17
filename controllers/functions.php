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


    $client = intval($_POST['client'] ?? 0);
    $startDateInput = sanitize_text_field($_POST['start_date'] ?? '');
    $endDateInput   = sanitize_text_field($_POST['end_date'] ?? '');

    if (!$client || !$startDateInput || !$endDateInput) {
        wp_die("Invalid input.");
    }

    $startDateObj = new DateTime($startDateInput . '-01');
    $endDateObj   = new DateTime($endDateInput . '-01');
    $endDateObj->modify('last day of this month');

    // Build month labels
    $months = [];
    $temp = clone $startDateObj;

    while ($temp <= $endDateObj) {
        $months[] = $temp->format("M'y");
        $temp->modify('+1 month');
    }

    // Data storage
    $allData = [];

    
    // Fetch real DB data per month
    foreach ($months as $monthLabel) {
        
        $dateObj = DateTime::createFromFormat("M'y", $monthLabel);
        if (!$dateObj) {
            $dateObj = DateTime::createFromFormat("M y", str_replace("'", " ", $monthLabel));
        }
        
        $start = (clone $dateObj)->modify('first day of this month')->setTime(0,0,0);
        $end   = (clone $dateObj)->modify('last day of this month')->setTime(23,59,59);
        
        $allData[$monthLabel] = df_get_monthly_data($client, $start, $end);
    }
    
    error_log("Data of All" . print_r($allData, true));
    
    $spreadsheet = new Spreadsheet();

    // Generate sheets
    Sheets\generateActiveUsersSheet($spreadsheet, $months, $allData);
    Sheets\generateConsultationDataSheet($spreadsheet, $months, $allData);

    // CLEAR ALL EXISTING OUTPUT BUFFERS
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Download Excel
    $filename = 'client-report-' . sanitize_title($client) . '-' . date('Ymd-His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');


    $writer = new Xlsx($spreadsheet);


    $writer->save('php://output');
    exit;
}

add_action('admin_post_dp_process_form', __NAMESPACE__ . '\\dp_handle_form_submissions');
