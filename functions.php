<?php declare( strict_types=1 );

use A8C\SpecialProjects\Scaffold\Plugin;

defined( 'ABSPATH' ) || exit;

// region META

/**
 * Returns the plugin's main class instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  Plugin
 */
function a8csp_scaffold_get_plugin_instance(): Plugin {
	return Plugin::get_instance();
}

// endregion

// region OTHERS

$a8csp_scaffold_files = glob( constant( 'A8CSP_SCAFFOLD_DIR_PATH' ) . 'includes/*.php' );
if ( false !== $a8csp_scaffold_files ) {
	foreach ( $a8csp_scaffold_files as $a8csp_scaffold_file ) {
		if ( 1 === preg_match( '#/includes/_#i', $a8csp_scaffold_file ) ) {
			continue; // Ignore files prefixed with an underscore.
		}

		require_once $a8csp_scaffold_file;
	}
}

// endregion
