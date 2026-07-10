<?php declare( strict_types=1 );
/**
 * Uninstall handler. WordPress runs this file directly when the plugin is deleted, in a cold
 * bootstrap where only `WP_UNINSTALL_PLUGIN` is defined — no Composer autoloader, no Plugin class,
 * no Component registry — so it deletes the plugin's footprint straight off the manifest instead
 * of going through the framework.
 *
 * @since       1.0.0
 * @version     1.0.0
 * @package     A8C\SpecialProjects\Plugins
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$a8csp_scaffold_uninstall_footprint = require __DIR__ . '/includes/_uninstall-footprint.php';

foreach ( $a8csp_scaffold_uninstall_footprint['options'] as $a8csp_scaffold_uninstall_option ) {
	delete_option( $a8csp_scaffold_uninstall_option );
}

foreach ( $a8csp_scaffold_uninstall_footprint['user_meta'] as $a8csp_scaffold_uninstall_meta_key ) {
	delete_metadata( 'user', 0, $a8csp_scaffold_uninstall_meta_key, '', true );
}
