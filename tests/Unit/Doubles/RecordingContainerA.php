<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles;

use A8C\SpecialProjects\Scaffold\Component;
use A8C\SpecialProjects\Scaffold\ComponentContainer;

/**
 * Configurable recording component container for boot-tree tests.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class RecordingContainerA implements Component, ComponentContainer {
	/**
	 * The configured return value for `is_needed()`.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     bool
	 */
	public static bool $needed = true;

	/**
	 * The configured child component classes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array<int, class-string<Component>>
	 */
	public static array $child_component_classes = array();

	/**
	 * Records construction.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function __construct() {
		ComponentBootLedger::record_construction( self::class );
	}

	/**
	 * Restores the double's default state.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public static function reset(): void {
		self::$needed                  = true;
		self::$child_component_classes = array();
	}

	/**
	 * Returns the configured needed-state.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_needed(): bool {
		return self::$needed;
	}

	/**
	 * Records initialization.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		ComponentBootLedger::record_initialization( self::class );
	}

	/**
	 * Returns the configured child component classes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array<int, class-string<Component>>
	 */
	public static function get_child_component_classes(): array {
		return self::$child_component_classes;
	}
}
