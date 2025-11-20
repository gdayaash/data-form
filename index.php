<?php
/**
 * Plugin Name: Data Picker
 * Description: Monthly client reporting tool.
 * Author: Dayaash G
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';


// Load dependencies
require_once plugin_dir_path(__FILE__) . 'controllers/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/data-fetcher.php';
require_once plugin_dir_path(__FILE__) . 'sheets/active-users.php';
require_once plugin_dir_path(__FILE__) . 'sheets/consultation-data.php';

// Menu UI
add_action('admin_menu', function () {
    add_menu_page(
        'Client Report Filter',
        'Data Picker',
        'manage_options',
        'data-picker',
        ['DataForm\Controllers\AdminPageController', 'renderAdminPage'],
        'dashicons-filter',
        26
    );
});

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_data-picker') return;

    wp_enqueue_style('bootstrap-cdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css');
    wp_enqueue_style('custom-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('bootstrap-cdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('custom-js', plugin_dir_url(__FILE__) . 'assets/js/main.js');
});

// Handle form submit
add_action('admin_post_dp_process_form', ['DataForm\Controllers\AdminPageController', 'handlePost']);
