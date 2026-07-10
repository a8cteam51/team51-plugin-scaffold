<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit;

use A8C\SpecialProjects\Scaffold\Integrations\WC_Subscriptions;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\RecordingWCSubscriptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Exercises WC_Subscriptions::is_active() and maybe_initialize(), the plugin's smallest
 * branching, WP-light seam: its only WordPress dependency is the ABSPATH boot guard, so it
 * models the house pattern of hand-rolled recording doubles for Unit-suite tests instead of
 * Mockery or Brain-Monkey.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
#[CoversClass( WC_Subscriptions::class )]
final class WCSubscriptionsTest extends TestCase {
	/**
	 * Satisfies the production file's `ABSPATH` boot guard before its class is first
	 * autoloaded.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void {
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', __DIR__ . '/' );
		}
	}

	/**
	 * Without the real WooCommerce Subscriptions plugin loaded, the integration reports itself
	 * as inactive.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_is_active_is_false_without_the_real_plugin(): void {
		self::assertFalse( ( new WC_Subscriptions() )->is_active() );
	}

	/**
	 * An inactive integration never runs its initialization.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_maybe_initialize_skips_initialization_when_inactive(): void {
		$integration = new RecordingWCSubscriptions( false );

		$integration->maybe_initialize();

		self::assertFalse( $integration->initialized );
	}

	/**
	 * An active integration runs its initialization.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_maybe_initialize_runs_initialization_when_active(): void {
		$integration = new RecordingWCSubscriptions( true );

		$integration->maybe_initialize();

		self::assertTrue( $integration->initialized );
	}
}
