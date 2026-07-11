<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Integration;

use A8C\SpecialProjects\Scaffold\Blocks;
use A8C\SpecialProjects\Scaffold\Plugin;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the plugin boots on a supported runtime inside wp-env: the requirements gate
 * passes, the component registry runs, and a component's hooks actually fire. The cached
 * accessor and plugin boot are idempotent.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class PluginBootTest extends TestCase {
	/**
	 * On an at-floor runtime the requirements gate passes, `plugins_loaded` has already run the
	 * accessor (it's wired via a void wrapper, since the accessor itself returns `Plugin`), and by
	 * request time the registry has run the `Blocks` component far enough for its block to be
	 * registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_plugin_boots_on_supported_runtime(): void {
		self::assertNotInstanceOf( \WP_Error::class, A8CSP_SCAFFOLD_REQUIREMENTS );
		self::assertTrue( \function_exists( 'a8csp_scaffold_plugin' ) );
		self::assertInstanceOf( Plugin::class, \a8csp_scaffold_plugin() );

		$block_metadata = \json_decode(
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local filesystem read of a tracked build artifact, not a remote resource.
			(string) \file_get_contents( \constant( 'A8CSP_SCAFFOLD_DIR_PATH' ) . 'blocks/build/foobar/block.json' ),
			true,
			512,
			JSON_THROW_ON_ERROR
		);

		self::assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( $block_metadata['name'] ) );
	}

	/**
	 * `Plugin::boot()` is idempotent: the `plugins_loaded` boot has already run, so a second
	 * call must not construct or wire the components again — the `Blocks` component's callback
	 * stays registered on `init` exactly once.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_second_boot_does_not_rewire_components(): void {
		self::assertSame( 1, $this->count_blocks_init_registrations() );

		$plugin = \a8csp_scaffold_plugin();
		self::assertInstanceOf( Plugin::class, $plugin );
		$plugin->boot();

		self::assertSame( 1, $this->count_blocks_init_registrations() );
	}

	/**
	 * Counts the `init` hook registrations that target a `Blocks` instance's `register_blocks`
	 * method, across all priorities.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  int
	 */
	private function count_blocks_init_registrations(): int {
		$hook = $GLOBALS['wp_filter']['init'] ?? null;
		if ( ! $hook instanceof \WP_Hook ) {
			return 0;
		}

		$count = 0;
		foreach ( $hook->callbacks as $priority_callbacks ) {
			foreach ( $priority_callbacks as $registration ) {
				$callback = $registration['function'];
				if ( \is_array( $callback ) && ( $callback[0] ?? null ) instanceof Blocks && 'register_blocks' === ( $callback[1] ?? null ) ) {
					++$count;
				}
			}
		}

		return $count;
	}
}
