<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit;

use A8C\SpecialProjects\Scaffold\Component;
use A8C\SpecialProjects\Scaffold\Integrations;
use A8C\SpecialProjects\Scaffold\Integrations\WC_Subscriptions;
use A8C\SpecialProjects\Scaffold\Plugin;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\ComponentBootLedger;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\RecordingContainerA;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\RecordingContainerB;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\RecordingLeafA;
use A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles\RecordingLeafB;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Exercises recursive component booting, subtree pruning, and graph validation.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
#[CoversClass( Plugin::class )]
#[CoversClass( Integrations::class )]
final class ComponentContainerBootTest extends TestCase {
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
	 * Resets static double state before each test.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	protected function setUp(): void {
		parent::setUp();

		ComponentBootLedger::reset();
		RecordingContainerA::reset();
		RecordingContainerB::reset();
		RecordingLeafA::reset();
		RecordingLeafB::reset();
	}

	/**
	 * A container initializes before its children, which boot in declaration order.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_container_boots_parent_then_children_in_declared_order(): void {
		RecordingContainerA::$child_component_classes = array(
			RecordingLeafA::class,
			RecordingLeafB::class,
		);

		$seen = array();
		$this->boot_component( new Plugin(), RecordingContainerA::class, $seen );

		self::assertSame(
			array(
				array(
					'event'     => 'constructed',
					'component' => RecordingContainerA::class,
				),
				array(
					'event'     => 'initialized',
					'component' => RecordingContainerA::class,
				),
				array(
					'event'     => 'constructed',
					'component' => RecordingLeafA::class,
				),
				array(
					'event'     => 'initialized',
					'component' => RecordingLeafA::class,
				),
				array(
					'event'     => 'constructed',
					'component' => RecordingLeafB::class,
				),
				array(
					'event'     => 'initialized',
					'component' => RecordingLeafB::class,
				),
			),
			ComponentBootLedger::$events
		);
	}

	/**
	 * A false container gate prunes its subtree before any child is constructed.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_false_container_gate_prunes_child_before_construction(): void {
		RecordingContainerA::$needed                  = false;
		RecordingContainerA::$child_component_classes = array( RecordingLeafA::class );

		$seen = array();
		$this->boot_component( new Plugin(), RecordingContainerA::class, $seen );

		// Plugin::is_needed() applies the same false-gate short circuit to the top-level registry.
		self::assertArrayHasKey( RecordingContainerA::class, ComponentBootLedger::$constructed );
		self::assertArrayNotHasKey( RecordingContainerA::class, ComponentBootLedger::$initialized );
		self::assertArrayNotHasKey( RecordingLeafA::class, ComponentBootLedger::$constructed );
	}

	/**
	 * A child class owned by two top-level containers is rejected across the shared graph.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_duplicate_child_across_top_level_containers_throws(): void {
		RecordingContainerA::$child_component_classes = array( RecordingLeafA::class );
		RecordingContainerB::$child_component_classes = array( RecordingLeafA::class );

		$plugin = new Plugin();
		$seen   = array();
		$this->boot_component( $plugin, RecordingContainerA::class, $seen );

		self::expectException( \LogicException::class );
		self::expectExceptionMessage(
			\sprintf(
				'Component %s is registered more than once; a component may belong to a single parent.',
				RecordingLeafA::class
			)
		);

		$this->boot_component( $plugin, RecordingContainerB::class, $seen );
	}

	/**
	 * A cycle is rejected when recursion reaches an already-seen component class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_cycle_throws_when_class_is_revisited(): void {
		RecordingContainerA::$child_component_classes = array( RecordingContainerB::class );
		RecordingContainerB::$child_component_classes = array( RecordingContainerA::class );

		self::expectException( \LogicException::class );
		self::expectExceptionMessage(
			\sprintf(
				'Component %s is registered more than once; a component may belong to a single parent.',
				RecordingContainerA::class
			)
		);

		$seen = array();
		$this->boot_component( new Plugin(), RecordingContainerA::class, $seen );
	}

	/**
	 * The plugin-wide gate is open by default.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_plugin_is_needed_returns_true_by_default(): void {
		self::assertTrue( ( new Plugin() )->is_needed() );
	}

	/**
	 * The real `Integrations` container declares `WC_Subscriptions` as its one child, the fleet's
	 * worked example of the container pattern in the flesh.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_integrations_children_include_wc_subscriptions(): void {
		self::assertSame(
			array( WC_Subscriptions::class ),
			Integrations::get_child_component_classes()
		);
	}

	/**
	 * Booting a registry through the real `Integrations` container reaches its real
	 * `WC_Subscriptions` leaf: no double at either node, since the graph walk marks every
	 * component it constructs as seen regardless of that component's own `is_needed()` gate,
	 * which is enough to prove the container path was walked all the way down.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_booting_integrations_reaches_the_wc_subscriptions_leaf(): void {
		$seen = array();
		$this->boot_component( new Plugin(), Integrations::class, $seen );

		self::assertArrayHasKey( Integrations::class, $seen );
		self::assertArrayHasKey( WC_Subscriptions::class, $seen );
	}

	/**
	 * Invokes the private registry gate with a shared graph ledger.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   Plugin                               $plugin          The plugin instance.
	 * @param   string                               $component_class The component class to boot.
	 * @param   array<class-string<Component>, true> $seen             Component classes already
	 *                                                                 reached in the graph.
	 *
	 * @return  void
	 */
	private function boot_component( Plugin $plugin, string $component_class, array &$seen ): void {
		( new \ReflectionMethod( Plugin::class, 'boot_component' ) )
			->invokeArgs( $plugin, array( $component_class, &$seen ) );
	}
}
