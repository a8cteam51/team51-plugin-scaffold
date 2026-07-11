<?php declare( strict_types=1 );
/**
 * The A8CSP Plugin Scaffold bootstrap file.
 *
 * This file must remain parsable on PHP versions below the plugin's declared floor, since it
 * runs before the requirements check can report a friendly error; a dedicated CI job lints it
 * directly against the older PHP versions.
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

// Bootstrap helper functions: plugin metadata, version compatibility checks, and the
// requirements gate. They share this file's below-floor parse constraint.

/**
 * Returns the plugin's metadata.
 *
 * @template PluginMetaKey of key-of<PluginMetaData>
 *
 * @param   PluginMetaKey|null $property Optional. The property to return. Default all.
 *
 * @return  ($property is null ? PluginMetaData : ($property is PluginMetaKey ? PluginMetaData[PluginMetaKey] : null))
 */
function a8csp_scaffold_get_plugin_metadata( $property = null ) {
	static $plugin_data = array();

	$can_translate = 0 < did_action( 'init' );
	$cache_key     = $can_translate ? 'translated' : 'raw';

	if ( ! isset( $plugin_data[ $cache_key ] ) ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_file               = trailingslashit( WP_PLUGIN_DIR ) . constant( 'A8CSP_SCAFFOLD_BASENAME' );
		$plugin_data[ $cache_key ] = get_plugin_data( $plugin_file, false, $can_translate );
	}

	$metadata = $plugin_data[ $cache_key ];
	if ( null === $property ) {
		return $metadata;
	}

	if ( is_string( $property ) && isset( $metadata[ $property ] ) ) {
		return $metadata[ $property ];
	}

	return null;
}

/**
 * Returns the plugin's slug.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function a8csp_scaffold_get_plugin_slug() {
	$text_domain = a8csp_scaffold_get_plugin_metadata( 'TextDomain' );
	return sanitize_key( $text_domain );
}

/**
 * Returns the plugin's name.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function a8csp_scaffold_get_plugin_name() {
	return a8csp_scaffold_get_plugin_metadata( 'Name' );
}

/**
 * Returns the plugin's version.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function a8csp_scaffold_get_plugin_version() {
	return a8csp_scaffold_get_plugin_metadata( 'Version' );
}

/**
 * Checks compatibility with the current WordPress version.
 *
 * @param   string $min_wp_version The minimum WP version required to run.
 *
 * @return  bool
 */
function a8csp_scaffold_is_wp_version_compatible( $min_wp_version ) {
	if ( ! function_exists( 'is_wp_version_compatible' ) ) {
		return false;
	}

	return is_wp_version_compatible( $min_wp_version );
}

/**
 * Checks compatibility with the current PHP version.
 *
 * @param   string $min_php_version The minimum PHP version required to run.
 *
 * @return  bool
 */
function a8csp_scaffold_is_php_version_compatible( $min_php_version ) {
	if ( ! function_exists( 'is_php_version_compatible' ) ) {
		return false;
	}

	return is_php_version_compatible( $min_php_version );
}

/**
 * Validates the plugin requirements.
 *
 * @return  true|\WP_Error
 */
function a8csp_scaffold_validate_requirements() {
	$plugin_metadata = a8csp_scaffold_get_plugin_metadata();
	if ( ! isset( $plugin_metadata['RequiresPHP'] ) || '' === $plugin_metadata['RequiresPHP'] ) {
		$plugin_metadata['RequiresPHP'] = '8.5';
	}
	if ( ! isset( $plugin_metadata['RequiresWP'] ) || '' === $plugin_metadata['RequiresWP'] ) {
		$plugin_metadata['RequiresWP'] = '7.0';
	}

	$is_php_compatible = a8csp_scaffold_is_php_version_compatible( $plugin_metadata['RequiresPHP'] );
	$is_wp_compatible  = a8csp_scaffold_is_wp_version_compatible( $plugin_metadata['RequiresWP'] );

	$wp_error = new \WP_Error();
	if ( ! $is_wp_compatible ) {
		$wp_error->add( 'plugin_wp_incompatible', '', array( 'requires_wp' => $plugin_metadata['RequiresWP'] ) );
	}
	if ( ! $is_php_compatible ) {
		$wp_error->add( 'plugin_php_incompatible', '', array( 'requires_php' => $plugin_metadata['RequiresPHP'] ) );
	}

	return $wp_error->has_errors() ? $wp_error : true;
}

/**
 * Outputs an error that the system requirements weren't met.
 *
 * @param   \WP_Error $error          The error message to display.
 *
 * @return  void
 */
function a8csp_scaffold_output_requirements_error( $error ) {
	add_action(
		'admin_notices',
		static function () use ( $error ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$requirements_error = \wp_sprintf(
				/* translators: 1: Plugin name, 2: Plugin version */
				__( '<strong>%1$s (version %2$s)</strong> could not be initialized.', 'a8csp-scaffold' ),
				a8csp_scaffold_get_plugin_metadata( 'Name' ),
				a8csp_scaffold_get_plugin_metadata( 'Version' )
			);

			if ( $error->has_errors() ) {
				$requirements_error .= ' ' . \__( 'Your environment does not meet all the system requirements listed below:', 'a8csp-scaffold' );
				$requirements_error .= '<ul class="ul-disc">';

				foreach ( $error->get_error_codes() as $error_code ) {
					$error_data = $error->get_error_data( $error_code );
					if ( ! is_array( $error_data ) ) {
						$error_data = array();
					}

					switch ( $error_code ) {
						case 'plugin_wp_incompatible':
							$error_message = wp_sprintf(
								/* translators: 1: Current WP version, 2: Minimum WP version */
								__( 'Current <em>WordPress version (%1$s)</em> does not meet minimum required version of %2$s.', 'a8csp-scaffold' ),
								get_bloginfo( 'version' ),
								$error_data['requires_wp']
							);
							break;
						case 'plugin_php_incompatible':
							$error_message = wp_sprintf(
								/* translators: 1: Current PHP version, 2: Minimum PHP version */
								__( 'Current <em>PHP version (%1$s)</em> does not meet minimum required version of %2$s.', 'a8csp-scaffold' ),
								PHP_VERSION,
								$error_data['requires_php']
							);
							break;
						case 'missing_autoloader':
							$error_message = __( 'The autoloader file is missing. Please run <code>composer install</code> to generate it.', 'a8csp-scaffold' );
							break;
						default:
							$error_message = $error->get_error_message( $error_code );
					}

					$requirements_error .= "<li>$error_message</li>";
				}

				$requirements_error .= '</ul>';
			}

			wp_admin_notice( $requirements_error, array( 'type' => 'error' ) );
		}
	);
}

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
	add_action( 'plugins_loaded', 'a8csp_scaffold_boot_plugin' );
}
