<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Verifies the requirements gate degrades gracefully on a below-floor runtime.
 *
 * Runs in both matrix entries: at-floor it must pass, below-floor (WP 6.9.4)
 * it must yield a WP_Error without loading the plugin proper.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class RequirementsCheckTest extends TestCase {
	/**
	 * The requirements constant reflects the runtime it booted on.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_requirements_gate_matches_runtime(): void {
		self::assertTrue( \defined( 'A8CSP_SCAFFOLD_REQUIREMENTS' ) );

		if ( \version_compare( $GLOBALS['wp_version'], '7.0', '<' ) ) {
			self::assertInstanceOf( \WP_Error::class, A8CSP_SCAFFOLD_REQUIREMENTS );
			self::assertFalse( \function_exists( 'a8csp_scaffold_boot_plugin' ) );
		} else {
			self::assertNotInstanceOf( \WP_Error::class, A8CSP_SCAFFOLD_REQUIREMENTS );
		}
	}
}
