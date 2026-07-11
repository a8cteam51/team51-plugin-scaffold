<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Integrations;

use A8C\SpecialProjects\Scaffold\Component;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the integration with WooCommerce Subscriptions.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
class WC_Subscriptions implements Component {
	// region METHODS

	/**
	 * Returns true if WooCommerce Subscriptions is active on a WooCommerce version that meets the
	 * `WC requires at least` floor declared in the plugin header.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_needed(): bool {
		if ( ! \class_exists( 'WC_Subscriptions' ) || ! \class_exists( 'WooCommerce' ) || ! \defined( 'WC_VERSION' ) ) {
			return false;
		}

		return self::meets_minimum_wc_version( WC_VERSION, a8csp_scaffold_get_plugin_metadata( 'WC requires at least' ) );
	}

	/**
	 * Compares an installed WooCommerce version against the header-declared floor. A pure value
	 * comparison with no WordPress or WooCommerce calls, so the Unit suite can exercise the
	 * branching directly instead of stubbing globals.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $installed_version The installed WooCommerce version.
	 * @param   string|null $minimum_version    The minimum WooCommerce version declared in the plugin
	 *                                          header, or null/empty if the header doesn't declare one.
	 *
	 * @return  bool
	 */
	public static function meets_minimum_wc_version( string $installed_version, ?string $minimum_version ): bool {
		if ( null === $minimum_version || '' === $minimum_version ) {
			return true;
		}

		return \version_compare( $installed_version, $minimum_version, '>=' );
	}

	/**
	 * Initializes the integration.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		// HOOKS AND FILTERS HERE
	}

	// endregion

	// region HOOKS

	// ADD HOOK AND FILTER METHODS HERE

	// endregion
}
