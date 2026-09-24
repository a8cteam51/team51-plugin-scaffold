<?php declare( strict_types=1 );
/**
 * Uninstall handler. WordPress runs this file directly when the plugin is deleted, in a cold
 * bootstrap where only `WP_UNINSTALL_PLUGIN` is defined — no Composer autoloader, no Plugin class,
 * no Component registry — so the plugin's footprint stays inline below instead of living in a
 * separately-requirable file: nothing here may reference plugin code.
 *
 * @since       1.0.0
 * @version     1.0.0
 * @package     A8C\SpecialProjects\Plugins
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/*
 * The plugin's persisted footprint. Every option and user-meta key any component writes is
 * listed here, in the same change that introduces the write — grouped by owning component
 * so ownership stays reviewable. This file runs in WordPress's cold uninstall bootstrap
 * (no autoloader, no Plugin or Component classes), so the arrays stay inline: nothing here
 * may reference plugin code.
 */
$a8csp_scaffold_footprint = array(
	'options'   => array(
		// Component\Example_Settings owns:
		// 'a8csp_scaffold_example_option',
	),
	'user_meta' => array(),
);

foreach ( $a8csp_scaffold_footprint['options'] as $a8csp_scaffold_uninstall_option ) {
	delete_option( $a8csp_scaffold_uninstall_option );
}

foreach ( $a8csp_scaffold_footprint['user_meta'] as $a8csp_scaffold_uninstall_meta_key ) {
	delete_metadata( 'user', 0, $a8csp_scaffold_uninstall_meta_key, '', true );
}
