<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit;

use A8C\SpecialProjects\Scaffold\Integrations\WC_Subscriptions;
use A8C\SpecialProjects\Scaffold\Plugin;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\RecordingWCSubscriptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Exercises WC_Subscriptions::is_needed(), WC_Subscriptions::meets_minimum_wc_version(), and
 * the private Plugin::boot_component(): the negative integration gate, its pure version comparison,
 * and the registry's component gate invoked through Reflection. The production classes require
 * only the ABSPATH boot guard for these Unit tests, which use hand-rolled recording doubles instead
 * of Mockery or Brain-Monkey.
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
		RecordingWCSubscriptions::reset();
		RecordingWCSubscriptions::$needed = false;

		$seen = array();
		( new \ReflectionMethod( Plugin::class, 'boot_component' ) )
			->invokeArgs( new Plugin(), array( RecordingWCSubscriptions::class, &$seen ) );

		self::assertFalse( RecordingWCSubscriptions::$initialized );
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
		RecordingWCSubscriptions::reset();

		$seen = array();
		( new \ReflectionMethod( Plugin::class, 'boot_component' ) )
			->invokeArgs( new Plugin(), array( RecordingWCSubscriptions::class, &$seen ) );

		self::assertTrue( RecordingWCSubscriptions::$initialized );
	}

	/**
	 * Without a header-declared floor, every installed WooCommerce version meets the requirement.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_minimum_wc_version_accepts_a_missing_floor(): void {
		self::assertTrue( WC_Subscriptions::meets_minimum_wc_version( '1.0.0', null ) );
	}

	/**
	 * An installed WooCommerce version below the header-declared floor fails the requirement.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_minimum_wc_version_rejects_a_version_below_the_floor(): void {
		self::assertFalse( WC_Subscriptions::meets_minimum_wc_version( '9.9.0', '10.0.0' ) );
	}

	/**
	 * An installed WooCommerce version equal to the header-declared floor meets the requirement.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_minimum_wc_version_accepts_a_version_equal_to_the_floor(): void {
		self::assertTrue( WC_Subscriptions::meets_minimum_wc_version( '10.0.0', '10.0.0' ) );
	}

	/**
	 * An installed WooCommerce version above the header-declared floor meets the requirement.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_minimum_wc_version_accepts_a_version_above_the_floor(): void {
		self::assertTrue( WC_Subscriptions::meets_minimum_wc_version( '10.0.1', '10.0.0' ) );
	}
}
