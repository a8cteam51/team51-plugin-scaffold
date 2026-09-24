<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of blocks.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Blocks implements Component {
	// region METHODS

	/**
	 * Blocks have no environmental dependency, so the component always runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_needed(): bool {
		return true;
	}

	/**
	 * Initializes the blocks.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		\add_action( 'init', array( $this, 'register_blocks' ) );
		\add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	// endregion

	// region HOOKS

	/**
	 * Registers all blocks from the build's metadata manifest collection: one filesystem
	 * read for the whole collection instead of one per block directory.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function register_blocks(): void {
		\wp_register_block_types_from_metadata_collection(
			\constant( 'A8CSP_SCAFFOLD_DIR_PATH' ) . 'blocks/build',
			\constant( 'A8CSP_SCAFFOLD_DIR_PATH' ) . 'blocks/build/blocks-manifest.php'
		);
	}

	/**
	 * Registers a plugin-level script for the block editor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function enqueue_block_editor_assets(): void {
		$asset_meta = a8csp_scaffold_get_asset_meta( 'assets/js/build/editor.js' );
		if ( \is_null( $asset_meta ) ) {
			return;
		}

		$plugin_slug = a8csp_scaffold_get_plugin_slug();
		\wp_register_script(
			"$plugin_slug-editor",
			\constant( 'A8CSP_SCAFFOLD_DIR_URL' ) . 'assets/js/build/editor.js',
			$asset_meta['dependencies'],
			$asset_meta['version'],
			false
		);
		\wp_set_script_translations( "$plugin_slug-editor", 'a8csp-scaffold' );
	}

	// endregion
}
