<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Plugin {
	// region FIELDS AND CONSTANTS

	/**
	 * Add the plugin's components here; they boot in registration order.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array<int, class-string<Component>>
	 */
	private const COMPONENTS = array(
		Blocks::class,
		Integrations\WC_Subscriptions::class,
	);

	/**
	 * The singleton instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     Plugin|null
	 */
	private static ?self $instance = null;

	/**
	 * Whether `boot()` has already run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     bool
	 */
	private bool $booted = false;

	// endregion

	// region MAGIC METHODS

	/**
	 * Plugin constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	private function __construct() {
		/* Empty on purpose. */
	}

	/**
	 * Prevent cloning.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	private function __clone() {
		/* Empty on purpose. */
	}

	/**
	 * Prevent unserializing.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  \LogicException When unserialization is attempted.
	 *
	 * @return  void
	 */
	public function __wakeup() {
		throw new \LogicException( 'Cannot unserialize a singleton.' );
	}

	// endregion

	// region METHODS

	/**
	 * Returns the singleton instance of the plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  Plugin
	 */
	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Returns true if all the plugin's dependencies are met.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  true|\WP_Error
	 */
	public function is_active(): bool|\WP_Error {
		// Check if WooCommerce is active.
		if ( ! \class_exists( 'WooCommerce' ) || ! \defined( 'WC_VERSION' ) ) {
			return new \WP_Error( 'woocommerce_not_active', 'WooCommerce is not active.' );
		}

		// Get the minimum WooCommerce version required from the plugin's header, if needed.
		$minimum_wc_version = a8csp_scaffold_get_plugin_metadata( 'WC requires at least' );
		if ( \is_null( $minimum_wc_version ) ) {
			return true;
		}

		// Check if WooCommerce version is supported.
		if ( ! \version_compare( WC_VERSION, $minimum_wc_version, '>=' ) ) {
			return new \WP_Error( 'woocommerce_version_not_supported', \sprintf( 'WooCommerce version %s or newer is required.', $minimum_wc_version ) );
		}

		return true;
	}

	/**
	 * Initializes a component if it reports itself as needed.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   Component $component The component to gate and initialize.
	 *
	 * @return  void
	 */
	public static function boot_component( Component $component ): void {
		if ( ! $component->is_needed() ) {
			return;
		}

		$component->initialize();
	}

	// endregion

	// region HOOKS

	/**
	 * Boots the component registry if the plugin's dependencies are met. Idempotent: only the
	 * first call has any effect.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		$is_active = $this->is_active();
		if ( \is_wp_error( $is_active ) ) {
			a8csp_scaffold_output_requirements_error( $is_active );
			return;
		}

		foreach ( self::COMPONENTS as $component_class ) {
			self::boot_component( new $component_class() );
		}
	}

	// endregion
}
