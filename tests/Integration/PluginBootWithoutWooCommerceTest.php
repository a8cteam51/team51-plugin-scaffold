<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold\Tests\Integration;

use A8C\SpecialProjects\Scaffold\Integrations\WC_Subscriptions;
use PHPUnit\Framework\TestCase;

/**
 * Proves Plugin::boot() boots the whole component registry independently of WooCommerce: a
 * WooCommerce-independent component (Blocks) registers itself when WooCommerce is inactive,
 * while the WooCommerce-dependent component (WC_Subscriptions) correctly reports itself as not
 * needed. Only meaningful with WooCommerce deactivated, so it self-skips in the standard
 * `composer test:integration` run (WooCommerce is active there) — see tests/README.md for how to
 * exercise it against a WooCommerce-less runtime.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class PluginBootWithoutWooCommerceTest extends TestCase {
	/**
	 * With WooCommerce inactive, the boot hook runs, the Blocks component registers
	 * its block, and the WC-dependent component correctly reports itself as not needed.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function test_plugin_boots_and_blocks_register_without_woocommerce(): void {
		if ( \class_exists( 'WooCommerce' ) ) {
			self::markTestSkipped( 'This proof only runs against a WooCommerce-less runtime; WooCommerce is active in this run.' );
		}

		self::assertNotInstanceOf( \WP_Error::class, A8CSP_SCAFFOLD_REQUIREMENTS );
		self::assertTrue( \function_exists( 'a8csp_scaffold_boot_plugin' ) );

		$block_metadata = \json_decode(
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local filesystem read of a tracked build artifact, not a remote resource.
			(string) \file_get_contents( \constant( 'A8CSP_SCAFFOLD_DIR_PATH' ) . 'blocks/build/foobar/block.json' ),
			true,
			512,
			JSON_THROW_ON_ERROR
		);

		self::assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( $block_metadata['name'] ) );
		self::assertFalse( ( new WC_Subscriptions() )->is_needed() );
	}
}
