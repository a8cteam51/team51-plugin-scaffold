<?php declare( strict_types=1 );

use A8C\SpecialProjects\Scaffold\Plugin;

defined( 'ABSPATH' ) || exit;

// region META

/**
 * Boots the plugin's component registry.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  void
 */
function a8csp_scaffold_boot_plugin(): void {
	Plugin::get_instance()->boot();
}

// endregion

// region LOADER

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
