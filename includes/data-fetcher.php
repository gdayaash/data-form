<?php
namespace DataForm\Service;

if (!defined('ABSPATH')) exit;

use DateTime;

/*********************************
 * SINGLE QUERY REPOSITORY
 * (Replaces all 3 previous repos)
 *********************************/
class HeadcountRepository {

    /**
     * Fetch latest contract + latest headcount for the month
     * using ONE optimized query.
     */
    public function getLatestHeadcountForMonth(int $clientId, DateTime $end): ?object {
        global $wpdb;

        $endStr = $end->format('Y-m-d H:i:s');

        $sql = "
            SELECT client_contracts.*, headcounts.*
            FROM {$wpdb->prefix}clients_contracts AS client_contracts
            LEFT JOIN {$wpdb->prefix}contract_headcounts AS headcounts
                ON headcounts.client_contract_id = client_contracts.client_contract_id
                AND headcounts.datetime <= %s
            WHERE client_contracts.client_id = %d
              AND client_contracts.datetime <= %s
            ORDER BY client_contracts.datetime DESC, headcounts.datetime DESC
            LIMIT 1
        ";

        return $wpdb->get_row(
            $wpdb->prepare($sql, $endStr, $clientId, $endStr)
        );
    }
}

/*********************************
 * REPORT SERVICE USING ONE QUERY
 *********************************/
class ReportService {

    public function buildMonthlyData(int $clientId, array $months): array {

        $repo = new HeadcountRepository();
        $results = [];

        foreach ($months as $label) {

            // Convert month label to dates
            $date = DateTime::createFromFormat("M'y", $label);

            $start = (clone $date)->modify('first day of this month')->setTime(0, 0, 0);
            $end   = (clone $date)->modify('last day of this month')->setTime(23, 59, 59);

            // Defaults
            $data = [
                "workforce_count"     => 0,
                "total_registrations" => 0,
                "total_usage"         => 0,
                "unique_users"        => 0,
                "quarterly_average"   => 0,
                "six_month_average"   => 0,
            ];

            // --------------------------------------------------------------------
            // 🔥 ONE QUERY ONLY — returns contract + headcount together
            // --------------------------------------------------------------------
            $row = $repo->getLatestHeadcountForMonth($clientId, $end);
            // If found, extract headcount
            if ($row && isset($row->total_headcount)) {
                $data["workforce_count"] = intval($row->total_headcount);
            }

            // Save
            $results[$label] = $data;
        }

        return $results;
    }
}
