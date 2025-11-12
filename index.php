<?php
/**
 * Plugin Name: Data Picker
 * Description: A client report filter plugin with date ranges.
 * Author: Dayaash G
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

//Including autoload
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';

// Include admin logic
require_once plugin_dir_path(__FILE__) . 'controllers/functions.php';

function dp_render_admin_page() {
    include plugin_dir_path(__FILE__) . 'views/form.php';
}

// === MENU & ASSETS ===
function dp_register_menu_page() {
    add_menu_page(
        'Client Report Filter',
        'Data Picker',
        'manage_options',
        'data-picker',
        'dp_render_admin_page',
        'dashicons-filter',
        26
    );
}
add_action('admin_menu', 'dp_register_menu_page');

function dp_enqueue_admin_assets($hook) {
    if ($hook !== 'toplevel_page_data-picker') return;

    wp_enqueue_style('bootstrap-cdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css', [], '5.3.8');
    wp_enqueue_style('custom-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css', [], '1.0');
    wp_enqueue_script('bootstrap-cdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', ['jquery'], '5.3.8', true);
    wp_enqueue_script('custom-js', plugin_dir_url(__FILE__) . 'assets/js/main.js', [], '1.0');
}
add_action('admin_enqueue_scripts', 'dp_enqueue_admin_assets');



