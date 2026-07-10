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
	 * Returns true if WooCommerce Subscriptions is active.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_needed(): bool {
		return \class_exists( 'WC_Subscriptions' );
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
