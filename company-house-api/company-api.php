<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://elemental.co.za
 * @since             1.0.0
 * @package           Company_Api
 *
 * @wordpress-plugin
 * Plugin Name:       Company API
 * Plugin URI:        https://github.com/marcelb/company-api
 * Description:       Integrates Companies House API with Gravity Forms to search companies and directors.
 * Version:           1.0.0
 * Author:            Marcel Brown
 * Author URI:        https://elemental.co.za
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       company-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'COMPANY_API_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'COMPANY_API_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'COMPANY_API_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-company-api-activator.php
 */
function activate_company_api() {
	require_once COMPANY_API_PLUGIN_DIR . 'includes/class-company-api-activator.php';
	Company_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-company-api-deactivator.php
 */
function deactivate_company_api() {
	require_once COMPANY_API_PLUGIN_DIR . 'includes/class-company-api-deactivator.php';
	Company_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_company_api' );
register_deactivation_hook( __FILE__, 'deactivate_company_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require COMPANY_API_PLUGIN_DIR . 'includes/class-company-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_company_api() {

	$plugin = new Company_Api();
	$plugin->run();

}
run_company_api();