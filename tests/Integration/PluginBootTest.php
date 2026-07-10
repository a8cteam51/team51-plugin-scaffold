<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Verifies the plugin boots on a supported runtime inside wp-env: the requirements gate
 * passes, the component registry runs, and a component's hooks actually fire.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class PluginBootTest extends TestCase {
	/**
	 * On an at-floor runtime the requirements gate passes, the boot hook is registered on
	 * `plugins_loaded`, and by request time the registry has run the `Blocks` component far
	 * enough for its block to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_plugin_boots_on_supported_runtime(): void {
		self::assertNotInstanceOf( \WP_Error::class, A8CSP_SCAFFOLD_REQUIREMENTS );
		self::assertTrue( \function_exists( 'a8csp_scaffold_boot_plugin' ) );
		self::assertNotFalse( \has_action( 'plugins_loaded', 'a8csp_scaffold_boot_plugin' ) );

		$block_metadata = \json_decode(
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local filesystem read of a tracked build artifact, not a remote resource.
			(string) \file_get_contents( \constant( 'A8CSP_SCAFFOLD_DIR_PATH' ) . 'blocks/build/foobar/block.json' ),
			true,
			512,
			JSON_THROW_ON_ERROR
		);

		self::assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( $block_metadata['name'] ) );
	}
}
