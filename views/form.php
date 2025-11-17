<?php

global $wpdb;
$table_name = $wpdb->prefix . 'clients';
$clients = $wpdb->get_results("SELECT client_id, client_name FROM {$table_name} ORDER BY client_name ASC");

// $query = "SELECT 
//     client_contracts.*, 
//     headcounts.*
// FROM wp_clients_contracts AS client_contracts
// LEFT JOIN wp_contract_headcounts AS headcounts
//        ON headcounts.client_contract_id = client_contracts.client_contract_id
//        AND headcounts.datetime <= '2025-10-31 00:00:00'
// WHERE client_contracts.client_id = 47
//   AND client_contracts.datetime <= '2025-10-31 00:00:00'
// ORDER BY client_contracts.datetime DESC, headcounts.datetime DESC
// LIMIT 1;";

// $get_headcounts = $wpdb->get_results("$query", ARRAY_A);

// print_r($get_headcounts);

// echo "<br>";
// echo "<br>";

// echo "<br>";

// echo $get_headcounts[0]['total_headcount'];

?>

<section class="plugin-ui-area">

    <section class="form-title">
        <main class="container-fluid">
            <!-- Heading  -->
            <article class="row">
                <!-- <h1 class="plugin-heading">WoW Quarterly Reports</h1> -->
            </article>
            <!-- Heading  -->
        </main>
    </section>
    
    <section class="form-section">

        <section class="response-area">
           <article class="">
            <p class="text-danger fs-6 hidden">Error</p>
           </article>
        </section>
    
        <section class="form-area">

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="dp_process_form">
        
                <!-- Form Starts  -->
                <main class="container-fluid">

                    <article class="row">
                        <h4 class="text-primary fs-3">Active Users</h4>
                    </article>

                    <article class="row align-items-center">
                        <aside class="col-6">
    
                            <article class="d-flex flex-column gap-3">
                                <aside class="col">
                                    <label for="client">Select Client</label>
                                    <select name="client" class="form-control" id="client" required>
                                        <option value="">-- Select Client --</option>
                                        <?php if (!empty($clients)): ?>
                                            <?php foreach ($clients as $client): ?>
                                                <option value="<?php echo esc_attr($client->client_id); ?>">
                                                    <?php echo esc_html($client->client_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">No clients found</option>
                                        <?php endif; ?>
                                    </select>
                                </aside>
                                        
                                <aside class="col">
                                    <div class="form-group">
                                        <label for="startDate">Start Month</label>
                                        <input type="month" name="start_date" class="form-control" id="startDate" required>
                                    </div>
                                </aside>
                                        
                                <aside class="col">
                                    <div class="form-group">
                                        <label for="endDate">End Month</label>
                                        <input type="month" name="end_date" class="form-control" id="endDate" required>
                                    </div>
                                </aside>
                                        
                                <aside class="col">
                                    <button type="submit" class="bg-primary text-white border-0 px-2 py-1 dbtn">Download Report</button>
                                </aside>
                            </article>    
                                        
                        </aside>
                                        
                        <aside class="col-6 text-center">
                           <img class="w-75" src="<?php echo esc_url( plugins_url( 'assets/images/report.gif', dirname( __FILE__, 1 ) ) ); ?>" alt="Wow-report">
                        </aside>
                    </article>
                    
                    <article class="row">
                        <h4 class="text-success copyRight">Powered by Happiest Health Tech Team</h4>
                    </article>
                                        
                </main>
                <!-- Form Ends  -->
            </form>    

        </section>
                                        
    </section>      

</section>