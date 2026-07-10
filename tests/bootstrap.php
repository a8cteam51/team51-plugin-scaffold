<?php declare( strict_types=1 );

/**
 * PHPUnit bootstrap. Inside wp-env's `cli` container, also loads WP and the plugin
 * entry file — require_once is a no-op when WP already include_once'd the active plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package A8C\SpecialProjects\Scaffold
 */

require_once __DIR__ . '/../vendor/autoload.php';

$a8csp_scaffold_wp_load = '/var/www/html/wp-load.php';
if ( file_exists( $a8csp_scaffold_wp_load ) ) {
	require_once $a8csp_scaffold_wp_load;
	require_once __DIR__ . '/../team51-plugin-scaffold.php';
}
