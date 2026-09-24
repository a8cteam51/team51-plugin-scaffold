<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles;

use A8C\SpecialProjects\Scaffold\Component;

/**
 * Records component construction and initialization for boot-tree tests.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class ComponentBootLedger {
	/**
	 * Component lifecycle events in execution order.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array<int, array{event: 'constructed'|'initialized', component: class-string<Component>}>
	 */
	public static array $events = array();

	/**
	 * Constructed component classes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array<class-string<Component>, true>
	 */
	public static array $constructed = array();

	/**
	 * Initialized component classes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array<class-string<Component>, true>
	 */
	public static array $initialized = array();

	/**
	 * Clears every recorded event.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public static function reset(): void {
		self::$events      = array();
		self::$constructed = array();
		self::$initialized = array();
	}

	/**
	 * Records a component's construction.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string $component_class The constructed component class.
	 *
	 * @return  void
	 */
	public static function record_construction( string $component_class ): void {
		self::$events[]                        = array(
			'event'     => 'constructed',
			'component' => $component_class,
		);
		self::$constructed[ $component_class ] = true;
	}

	/**
	 * Records a component's initialization.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string $component_class The initialized component class.
	 *
	 * @return  void
	 */
	public static function record_initialization( string $component_class ): void {
		self::$events[]                        = array(
			'event'     => 'initialized',
			'component' => $component_class,
		);
		self::$initialized[ $component_class ] = true;
	}
}
