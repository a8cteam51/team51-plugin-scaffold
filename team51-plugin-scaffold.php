<?php
/**
 * The A8CSP Plugin Scaffold bootstrap file.
 *
 * @since       1.0.0
 * @version     1.0.0
 * @package     A8C\SpecialProjects\Plugins
 * @author      WordPress.com Special Projects
 * @license     GPL-3.0-or-later
 *
 * @noinspection    ALL
 *
 * @wordpress-plugin
 * Plugin Name:             A8CSP Plugin Scaffold
 * Plugin URI:              https://wpspecialprojects.wordpress.com
 * Description:             A scaffold for A8C Special Projects plugins.
 * Version:                 1.0.0
 * Requires at least:       6.7
 * Tested up to:            6.7
 * Requires PHP:            8.3
 * Author:                  WordPress.com Special Projects
 * Author URI:              https://wpspecialprojects.wordpress.com
 * License:                 GPL v3 or later
 * License URI:             https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:             a8csp-scaffold
 * Domain Path:             /languages
 * WC requires at least:    9.5
 * WC tested up to:         9.5
 **/

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'A8CSP_SCAFFOLD_BASENAME', plugin_basename( __FILE__ ) );
define( 'A8CSP_SCAFFOLD_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'A8CSP_SCAFFOLD_DIR_URL', plugin_dir_url( __FILE__ ) );

// Load the rest of the bootstrap functions.
require_once A8CSP_SCAFFOLD_DIR_PATH . '/functions-bootstrap.php';

// Load plugin translations so they are available even for the error admin notices.
add_action(
	'init',
	static function () {
		load_plugin_textdomain(
			a8csp_scaffold_get_plugin_metadata( 'TextDomain' ),
			false,
			dirname( A8CSP_SCAFFOLD_BASENAME ) . a8csp_scaffold_get_plugin_metadata( 'DomainPath' )
		);
	}
);

// Declare compatibility with WC features.
add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

// Load the autoloader.
if ( ! is_file( A8CSP_SCAFFOLD_DIR_PATH . '/vendor/autoload.php' ) ) {
	a8csp_scaffold_output_requirements_error( new WP_Error( 'missing_autoloader' ) );
	return;
}
require_once A8CSP_SCAFFOLD_DIR_PATH . '/vendor/autoload.php';

// Bootstrap the plugin (maybe)!
define( 'A8CSP_SCAFFOLD_REQUIREMENTS', a8csp_scaffold_validate_requirements() );
if ( is_wp_error( A8CSP_SCAFFOLD_REQUIREMENTS ) ) {
	a8csp_scaffold_output_requirements_error( A8CSP_SCAFFOLD_REQUIREMENTS );
} else {
	require_once A8CSP_SCAFFOLD_DIR_PATH . '/functions.php';
	add_action( 'plugins_loaded', array( a8csp_scaffold_get_plugin_instance(), 'maybe_initialize' ) );
}
