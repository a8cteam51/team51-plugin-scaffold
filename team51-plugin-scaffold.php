<?php declare( strict_types=1 );
/**
 * The A8CSP Plugin Scaffold bootstrap file.
 *
 * This file and functions-bootstrap.php must remain parsable on PHP versions below the plugin's
 * declared floor, since they run before the requirements check can report a friendly error; a
 * dedicated CI job lints both files directly against the older PHP versions.
 *
 * @since       1.0.0
 * @version     1.0.0
 * @package     A8C\SpecialProjects\Plugins
 * @author      WordPress.com Special Projects
 * @license     GPL-2.0-or-later
 *
 * @noinspection    ALL
 *
 * @wordpress-plugin
 * Plugin Name:             A8CSP Plugin Scaffold
 * Plugin URI:              https://wpspecialprojects.wordpress.com
 * Description:             A scaffold for A8C Special Projects plugins.
 * Version:                 1.0.0
 * Requires at least:       7.0
 * Tested up to:            7.0
 * Requires PHP:            8.5
 * Author:                  WordPress.com Special Projects
 * Author URI:              https://wpspecialprojects.wordpress.com
 * License:                 GPL v2 or later
 * License URI:             https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:             a8csp-scaffold
 * Domain Path:             /languages
 * WC requires at least:    10.0
 * WC tested up to:         10.9
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'A8CSP_SCAFFOLD_BASENAME', plugin_basename( __FILE__ ) );
define( 'A8CSP_SCAFFOLD_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'A8CSP_SCAFFOLD_DIR_URL', plugin_dir_url( __FILE__ ) );

// Load the rest of the bootstrap functions.
require_once A8CSP_SCAFFOLD_DIR_PATH . '/functions-bootstrap.php';

// Translations for the /languages directory declared via the Domain Path header above are
// resolved just-in-time: WordPress registers this plugin's language directory from its header
// before the plugin loads, and the first call to a translation function for this text domain
// triggers loading the matching translation file for the current locale.

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
	// A void wrapper, not the accessor directly: a8csp_scaffold_plugin() returns Plugin for its
	// other callers (e.g. tests), and WordPress's action-callback contract requires void.
	add_action(
		'plugins_loaded',
		static function (): void {
			a8csp_scaffold_plugin();
		}
	);
}
