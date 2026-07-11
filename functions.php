<?php declare( strict_types=1 );

use A8C\SpecialProjects\Scaffold\Plugin;

defined( 'ABSPATH' ) || exit;

// region META

/**
 * Returns the plugin instance, booting it on first access.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  Plugin
 */
function a8csp_scaffold_plugin(): Plugin {
	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new Plugin();
		$plugin->boot();
	}

	return $plugin;
}

// endregion

// region OTHER

$a8csp_scaffold_includes = glob( constant( 'A8CSP_SCAFFOLD_DIR_PATH' ) . 'includes/*.php' );
if ( false !== $a8csp_scaffold_includes ) {
	sort( $a8csp_scaffold_includes ); // Glob order is filesystem-dependent, so sort for a deterministic load order.
	foreach ( $a8csp_scaffold_includes as $a8csp_scaffold_include ) {
		if ( str_starts_with( basename( $a8csp_scaffold_include ), '_' ) ) {
			continue; // An underscore prefix opts a file out of automatic loading.
		}

		require_once $a8csp_scaffold_include;
	}
}

// endregion
