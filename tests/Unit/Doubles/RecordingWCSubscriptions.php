<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles;

use A8C\SpecialProjects\Scaffold\Integrations\WC_Subscriptions;

/**
 * Hand-rolled recording double for WC_Subscriptions: reports a canned active-state and records
 * whether `initialize()` ran, since the real method is a protected no-op with no other
 * observable effect.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class RecordingWCSubscriptions extends WC_Subscriptions {
	/**
	 * Whether `initialize()` was invoked.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     bool
	 */
	public bool $initialized = false;

	/**
	 * The canned return value for `is_active()`.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     bool
	 */
	private bool $active;

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool $active The canned return value for `is_active()`.
	 */
	public function __construct( bool $active ) {
		$this->active = $active;
	}

	/**
	 * Returns the canned active-state instead of checking for the real plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_active(): bool {
		return $this->active;
	}

	/**
	 * Records that initialization ran instead of registering real hooks.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	protected function initialize(): void {
		$this->initialized = true;
	}
}
