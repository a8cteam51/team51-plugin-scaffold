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
