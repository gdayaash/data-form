<?php
namespace DataForm\Service;

if (!defined('ABSPATH')) exit;

use DateTime;

/*********************************
 * CONTRACTS REPOSITORY
 *********************************/
class ContractsRepository {
    public function getLatest(int $clientId, DateTime $before): ?object {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}clients_contracts
             WHERE client_id = %d AND datetime <= %s
             ORDER BY datetime DESC LIMIT 1",
            $clientId,
            $before->format('Y-m-d H:i:s')
        ));
    }
}

/*********************************
 * HEADCOUNT REPOSITORY
 *********************************/
class HeadcountRepository {

    public function getInMonth(int $contractId, DateTime $s, DateTime $e): ?object {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT total_headcount FROM {$wpdb->prefix}contract_headcounts
             WHERE client_contract_id = %d
             AND datetime BETWEEN %s AND %s
             ORDER BY datetime DESC LIMIT 1",
            $contractId,
            $s->format('Y-m-d H:i:s'),
            $e->format('Y-m-d H:i:s')
        ));
    }

    public function getLatestBefore(int $contractId, DateTime $before): ?object {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT total_headcount FROM {$wpdb->prefix}contract_headcounts
             WHERE client_contract_id = %d
             AND datetime <= %s
             ORDER BY datetime DESC LIMIT 1",
            $contractId,
            $before->format('Y-m-d H:i:s')
        ));
    }
}

/*********************************
 * REPORT SERVICE
 *********************************/
class ReportService {

    public function buildMonthlyData(int $client, array $months): array {

        $contractsRepo = new ContractsRepository();
        $headcountRepo = new HeadcountRepository();

        $results = [];

        foreach ($months as $label) {

            $date = DateTime::createFromFormat("M'y", $label);
            $start = (clone $date)->modify('first day of this month')->setTime(0,0,0);
            $end   = (clone $date)->modify('last day of this month')->setTime(23,59,59);

            // default placeholders
            $data = [
                "workforce_count" => 0,
                "total_registrations" => 0,
                "total_usage" => 0,
                "unique_users" => 0,
                "quarterly_average" => 0,
                "six_month_average" => 0,
            ];

            $contract = $contractsRepo->getLatest($client, $end);
            if (!$contract) {
                $results[$label] = $data;
                continue;
            }

            $inMonth = $headcountRepo->getInMonth($contract->client_contract_id, $start, $end);
            if (!$inMonth) {
                $inMonth = $headcountRepo->getLatestBefore($contract->client_contract_id, $end);
            }

            $data["workforce_count"] = $inMonth ? intval($inMonth->total_headcount) : 0;

            $results[$label] = $data;
        }

        return $results;
    }
}
