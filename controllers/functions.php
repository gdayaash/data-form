<?php
namespace DataForm\Controllers;

if (!defined('ABSPATH')) exit;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use DataForm\Service\ReportService;
use DataForm\Sheets\ActiveUsersSheetBuilder;
use DataForm\Sheets\ConsultationDataSheetBuilder;

class AdminPageController {

    public static function renderAdminPage() {
        include plugin_dir_path(__FILE__) . '/../views/form.php';
    }

    public static function handlePost() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') wp_die("Invalid Request");

        $client = intval($_POST['client']);
        $start  = sanitize_text_field($_POST['start_date']);
        $end    = sanitize_text_field($_POST['end_date']);

        if (!$client || !$start || !$end) wp_die("Invalid Input");

        $startObj = new DateTime("$start-01");
        $endObj   = new DateTime("$end-01");
        $endObj->modify("last day of this month");

        $months = [];
        $temp = clone $startObj;

        while ($temp <= $endObj) {
            $months[] = $temp->format("M'y");
            $temp->modify('+1 month');
        }

        // Build all data
        $service = new ReportService();
        $allData = $service->buildMonthlyData($client, $months);

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();

        $active = new ActiveUsersSheetBuilder();
        $active->build($spreadsheet, $months, $allData);

        $consult = new ConsultationDataSheetBuilder();
        $consult->build($spreadsheet, $months, $allData);

        // Send file
        while (ob_get_level()) ob_end_clean();

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"client-report-{$client}-" . date('Ymd-His') . ".xlsx\"");
        header("Cache-Control: max-age=0");

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save("php://output");
        exit;
    }
}
