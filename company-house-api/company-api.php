<?php
/**
 * Plugin Name: Company House API
 * Plugin URI: https://github.com/
 * Description: Integrates Companies House API with Gravity Forms to search companies and directors.
 * Version: 1.0.0
 * Author: Marcel Brown
 * Author URI: https://elemental.co.za
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: api-plugin
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Set up plugin constants for paths and versioning
define('CHL_VERSION', '1.0.0');
define('CHL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CHL_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load all the plugin classes
require_once CHL_PLUGIN_DIR . 'includes/class-api-handler.php';
require_once CHL_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once CHL_PLUGIN_DIR . 'includes/class-gravity-forms.php';
require_once CHL_PLUGIN_DIR . 'includes/class-shortcode.php';

// Boot up the admin settings page when we're in the admin area
if (is_admin()) {
    new CHL_Admin_Settings();
}

// Hook everything else into WordPress init
add_action('init', function() {
    new CHL_Gravity_Forms();
    new CHL_Shortcode();
});

// Enqueue the company search JavaScript on the front-end
add_action('wp_enqueue_scripts', 'chl_load_scripts', 999);
function chl_load_scripts() {
    wp_enqueue_script(
        'chl-company-search',
        CHL_PLUGIN_URL . 'public/js/company-search.js',
        array('jquery'),
        CHL_VERSION . '.' . time(),
        true
    );
    
    // Make the AJAX URL and nonce available to our JS
    wp_localize_script('chl-company-search', 'chl_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('chl_nonce')
    ));
}

// Create the API key option when plugin is activated
register_activation_hook(__FILE__, function() {
    add_option('chl_api_key', '');
});

// Tidy up rewrite rules when deactivated
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});