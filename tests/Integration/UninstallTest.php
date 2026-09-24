<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Integration;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the real `uninstall.php` end-to-end: every option and user-meta key its inline
 * footprint lists is gone after it runs, and a sentinel key NOT in the footprint survives —
 * proving the file deletes what it owns and nothing else.
 *
 * `uninstall.php` guards on `defined( 'WP_UNINSTALL_PLUGIN' )`, a constant WordPress itself
 * only defines during a real plugin-delete request. This test defines it by hand, so the one
 * test method runs `#[RunInSeparateProcess]` — the constant must not leak into the rest of
 * the suite, where its presence would be indistinguishable from an actual uninstall.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class UninstallTest extends TestCase {
	/**
	 * A canary option the footprint never lists. Its survival is what proves the test
	 * exercises "delete only what's owned" rather than "delete everything".
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	private const CANARY_OPTION = 'a8csp_scaffold_test_uninstall_canary';

	/**
	 * Removes the canary regardless of how the test finished, since this suite runs against
	 * a persistent wp-env database with no per-test transaction rollback (see tests/README.md).
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	protected function tearDown(): void {
		\delete_option( self::CANARY_OPTION );

		parent::tearDown();
	}

	/**
	 * Seeds a sentinel for every key the real footprint lists plus the canary, runs the real
	 * `uninstall.php`, then asserts the footprint's keys are gone and the canary survived.
	 * With today's honestly-empty footprint the seed/assert loops below run zero iterations —
	 * the proof today is that `uninstall.php` executes cleanly against a live WordPress and the
	 * canary survives; the loops activate for real the day the first footprint entry lands.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	#[RunInSeparateProcess]
	public function test_uninstall_deletes_only_its_own_footprint(): void {
		$footprint = self::read_inline_footprint();
		$user_id   = self::an_existing_user_id();

		foreach ( $footprint['options'] as $option ) {
			\update_option( $option, 'sentinel' );
		}

		foreach ( $footprint['user_meta'] as $meta_key ) {
			\update_user_meta( $user_id, $meta_key, 'sentinel' );
		}

		\update_option( self::CANARY_OPTION, 'sentinel' );

		\define( 'WP_UNINSTALL_PLUGIN', true );
		require \dirname( __DIR__, 2 ) . '/uninstall.php';

		foreach ( $footprint['options'] as $option ) {
			self::assertFalse( \get_option( $option ), "uninstall.php must delete the '{$option}' option" );
		}

		foreach ( $footprint['user_meta'] as $meta_key ) {
			self::assertSame( '', \get_user_meta( $user_id, $meta_key, true ), "uninstall.php must delete the '{$meta_key}' user-meta key" );
		}

		self::assertSame( 'sentinel', \get_option( self::CANARY_OPTION ), 'uninstall.php must not delete keys outside its footprint' );
	}

	/**
	 * Returns an existing user's ID to seed and verify user-meta deletion against. wp-env's
	 * fixture always provisions the default admin (ID 1); querying for one keeps the test
	 * independent of that assumption instead of hard-coding it.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  int
	 */
	private static function an_existing_user_id(): int {
		$users = \get_users(
			array(
				'number' => 1,
				'fields' => 'ID',
			)
		);

		self::assertNotEmpty( $users, 'wp-env must provision at least one user to seed user-meta against' );

		return (int) $users[0];
	}

	/**
	 * Extracts `$a8csp_scaffold_footprint` from the real `uninstall.php` source without
	 * requiring the file. Requiring it exits unless `WP_UNINSTALL_PLUGIN` is already defined,
	 * and defining that just to read the array would run the delete loops before this test has
	 * seeded anything for them to delete. Locates the array literal by balancing parens from
	 * its own `array(` so the nested `options`/`user_meta` arrays don't confuse the match, then
	 * evaluates only that expression — never uninstall.php's guard or its delete loops.
	 * This eval is safe only because it parses this repository's own version-controlled
	 * `uninstall.php` and must never be generalized to evaluate user input, remote data,
	 * another file, or anything else from outside this repository; if the footprint's shape
	 * grows complex enough that this string-slicing extraction becomes fragile, use a
	 * `token_get_all()`-based reader as the eval-free alternative instead of trying to make
	 * the eval safer.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array{options: list<string>, user_meta: list<string>}
	 */
	private static function read_inline_footprint(): array {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local filesystem read of a tracked source file, not a remote resource.
		$source = (string) \file_get_contents( \dirname( __DIR__, 2 ) . '/uninstall.php' );

		$needle = '$a8csp_scaffold_footprint';
		$assign = \strpos( $source, $needle );
		self::assertIsInt( $assign, "uninstall.php must declare {$needle} inline" );

		$array_start = \strpos( $source, 'array(', $assign );
		self::assertIsInt( $array_start, "could not find the {$needle} array literal" );

		$depth  = 0;
		$end    = null;
		$length = \strlen( $source );

		for ( $i = $array_start; $i < $length; $i++ ) {
			if ( '(' === $source[ $i ] ) {
				++$depth;
			} elseif ( ')' === $source[ $i ] ) {
				--$depth;

				if ( 0 === $depth ) {
					$end = $i;
					break;
				}
			}
		}

		self::assertIsInt( $end, "could not find the end of the {$needle} array literal" );

		$expression = \substr( $source, $array_start, $end - $array_start + 1 );
		$footprint  = eval( "return {$expression};" ); // phpcs:ignore Squiz.PHP.Eval -- evaluates a version-controlled array literal parsed out of this repo's own uninstall.php, never external input.

		self::assertIsArray( $footprint );
		self::assertArrayHasKey( 'options', $footprint );
		self::assertArrayHasKey( 'user_meta', $footprint );

		return $footprint;
	}
}
