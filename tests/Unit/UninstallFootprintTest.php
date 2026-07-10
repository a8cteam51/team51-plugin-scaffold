<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Exercises the uninstall footprint manifest's shape. `uninstall.php` requires this file cold,
 * with no autoloader and no WordPress functions available, so its only contract is the plain
 * array shape `uninstall.php` iterates over.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class UninstallFootprintTest extends TestCase {
	/**
	 * The manifest exposes exactly the two lists `uninstall.php` iterates, each a list of
	 * non-empty strings with no duplicates.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_footprint_has_the_shape_uninstall_php_expects(): void {
		$footprint = require \dirname( __DIR__, 2 ) . '/includes/_uninstall-footprint.php';

		self::assertIsArray( $footprint );
		self::assertSame( array( 'options', 'user_meta' ), \array_keys( $footprint ) );

		foreach ( $footprint as $list ) {
			self::assertIsArray( $list );
			self::assertSame( \array_values( $list ), $list, 'must be a list, not an associative array' );
			self::assertCount( \count( \array_unique( $list ) ), $list, 'must contain no duplicate entries' );

			foreach ( $list as $entry ) {
				self::assertIsString( $entry );
				self::assertNotSame( '', $entry );
			}
		}
	}

	/**
	 * The scaffold persists no options or user meta yet, so both lists are honestly empty
	 * instead of seeded with placeholder entries.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_footprint_is_honestly_empty_for_the_unmodified_scaffold(): void {
		$footprint = require \dirname( __DIR__, 2 ) . '/includes/_uninstall-footprint.php';

		self::assertSame( array(), $footprint['options'] );
		self::assertSame( array(), $footprint['user_meta'] );
	}
}
