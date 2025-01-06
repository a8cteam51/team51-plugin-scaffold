<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
class Plugin {
	// region FIELDS AND CONSTANTS

	/**
	 * The blocks component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     Blocks|null
	 */
	public ?Blocks $blocks = null;

	/**
	 * The integrations component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     Integrations|null
	 */
	public ?Integrations $integrations = null;

	// endregion

	// region MAGIC METHODS

	/**
	 * Plugin constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function __construct() {
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
	 * @return  void
	 */
	public function __wakeup() {
		/* Empty on purpose. */
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
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
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
		$minimum_wc_version = a8csp_scaffold_get_plugin_metadata( \WC_Plugin_Updates::VERSION_REQUIRED_HEADER );
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
	 * Initializes the plugin components.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	protected function initialize(): void {
		$this->blocks = new Blocks();
		$this->blocks->initialize();

		$this->integrations = new Integrations();
		$this->integrations->initialize();
	}

	// endregion

	// region HOOKS

	/**
	 * Initializes the plugin components if WooCommerce is activated.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function maybe_initialize(): void {
		$is_active = $this->is_active();
		if ( is_wp_error( $is_active ) ) {
			a8csp_scaffold_output_requirements_error( $is_active );
			return;
		}

		$this->initialize();
	}

	// endregion
}
