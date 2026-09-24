<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit\Doubles;

use A8C\SpecialProjects\Scaffold\Component;

/**
 * Second configurable recording leaf component for boot-tree tests.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class RecordingLeafB implements Component {
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
		self::$needed = true;
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
}
