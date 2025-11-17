<?php

if (!defined('ABSPATH')) exit;

// Table Names = wp_clients_contracts, wp_contract_headcounts;

/**
 * Auto-detect table names safely
 */
function df_resolve_table($primary, $fallbacks = []) {
    global $wpdb;

    // Check primary name first
    $table = $wpdb->prefix . $primary;
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table) {
        return $table;
    }

    // Check fallback table names
    foreach ($fallbacks as $alt) {
        $altTable = $wpdb->prefix . $alt;
        if ($wpdb->get_var("SHOW TABLES LIKE '{$altTable}'") === $altTable) {
            return $altTable;
        }
    }

    return null; // No table found
}

/**
 * Fetches all monthly metrics for a client.
 * Safe, automatic, and accurate.
 */
function df_get_monthly_data(int $client_id, DateTime $monthStart, DateTime $monthEnd) {
    global $wpdb;

    $startStr = $monthStart->format('Y-m-d 00:00:00');
    $endStr   = $monthEnd->format('Y-m-d 23:59:59');

    // ============================================================
    // 1. Resolve Table Names
    // ============================================================
    $tbl_contracts = df_resolve_table(
        'clients_contract',
        ['clients_contracts', 'client_contracts']
    );

    $tbl_headcounts = df_resolve_table(
        'contract_headcounts',
        ['clients_contract_headcounts', 'wp_contract_headcounts']
    );

    if (!$tbl_contracts || !$tbl_headcounts) {
        return [
            'workforce_count'     => 0,
            'total_registrations' => 0,
            'total_usage'         => 0,
            'unique_users'        => 0,
            'quarterly_average'   => 0,
            'six_month_average'   => 0,
        ];
    }

    // ============================================================
    // 2. Initialize Response Structure
    // ============================================================
    $results = [
        'workforce_count'     => 0,
        'total_registrations' => 0,
        'total_usage'         => 0,
        'unique_users'        => 0,
        'quarterly_average'   => 0,
        'six_month_average'   => 0,
    ];

    // ============================================================
    // 3. Fetch Latest Contract for This Client (up to month end)
    // ============================================================
    $contract = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT client_contract_id
             FROM {$tbl_contracts}
             WHERE client_id = %d
               AND datetime <= %s
             ORDER BY datetime DESC
             LIMIT 1",
            $client_id,
            $endStr
        )
    );

    if (!$contract) {
        return $results;
    }

    $contract_id = intval($contract->client_contract_id);

    // ============================================================
    // 4. Fetch Headcount for this Month
    // ============================================================
    $headcount = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT total_headcount
             FROM {$tbl_headcounts}
             WHERE client_contract_id = %d
               AND datetime BETWEEN %s AND %s
             ORDER BY datetime DESC
             LIMIT 1",
            $contract_id,
            $startStr,
            $endStr
        )
    );

    // If no monthly record → fallback to latest before month end
    if (!$headcount) {
        $headcount = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT total_headcount
                 FROM {$tbl_headcounts}
                 WHERE client_contract_id = %d
                   AND datetime <= %s
                 ORDER BY datetime DESC
                 LIMIT 1",
                $contract_id,
                $endStr
            )
        );
    }

    $results['workforce_count'] = $headcount ? intval($headcount->total_headcount) : 0;

    // ============================================================
    // 5. Placeholder Metrics (Add Your SQL Later)
    // ============================================================
    $results['total_registrations'] = 0;
    $results['total_usage'] = 0;
    $results['unique_users'] = 0;

    return $results;
}
