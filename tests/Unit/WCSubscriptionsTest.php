<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit;

use A8C\SpecialProjects\Scaffold\Integrations\WC_Subscriptions;
use A8C\SpecialProjects\Scaffold\Plugin;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\RecordingWCSubscriptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Exercises WC_Subscriptions::is_needed() and Plugin::boot_component(), the plugin's smallest
 * branching, WP-light seam: its only WordPress dependency is the ABSPATH boot guard, so it
 * models the house pattern of hand-rolled recording doubles for Unit-suite tests instead of
 * Mockery or Brain-Monkey.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
#[CoversClass( WC_Subscriptions::class )]
#[CoversClass( Plugin::class )]
final class WCSubscriptionsTest extends TestCase {
	/**
	 * Satisfies the production files' `ABSPATH` boot guard before their classes are first
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
	 * as not needed.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_is_needed_is_false_without_the_real_plugin(): void {
		self::assertFalse( ( new WC_Subscriptions() )->is_needed() );
	}

	/**
	 * The registry gate never initializes a component that reports itself as not needed.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_boot_component_skips_initialization_when_not_needed(): void {
		$component = new RecordingWCSubscriptions( false );

		Plugin::boot_component( $component );

		self::assertFalse( $component->initialized );
	}

	/**
	 * The registry gate initializes a component that reports itself as needed.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_boot_component_runs_initialization_when_needed(): void {
		$component = new RecordingWCSubscriptions( true );

		Plugin::boot_component( $component );

		self::assertTrue( $component->initialized );
	}
}
