<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Integration;

use A8C\SpecialProjects\Scaffold\Plugin;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the plugin boots on a supported runtime inside wp-env.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class PluginBootTest extends TestCase {
	/**
	 * On an at-floor runtime the requirements gate passes and the plugin registers itself.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_plugin_boots_on_supported_runtime(): void {
		self::assertNotInstanceOf( \WP_Error::class, A8CSP_SCAFFOLD_REQUIREMENTS );
		self::assertTrue( \function_exists( 'a8csp_scaffold_get_plugin_instance' ) );
		self::assertInstanceOf( Plugin::class, a8csp_scaffold_get_plugin_instance() );
		self::assertNotFalse( has_action( 'plugins_loaded', array( a8csp_scaffold_get_plugin_instance(), 'maybe_initialize' ) ) );
	}
}
