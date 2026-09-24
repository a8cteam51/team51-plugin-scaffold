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
	 * Add the plugin's top-level components here; they boot in registration order. A component
	 * implementing `ComponentContainer` boots its declared children immediately after itself.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array<int, class-string<Component>>
	 */
	private const COMPONENTS = array(
		Blocks::class,
		Integrations::class,
	);

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

	// region METHODS

	/**
	 * Returns true if the plugin should boot on the current site.
	 *
	 * A plugin that is gated as a whole — e.g. one that requires WooCommerce for everything it
	 * does — expresses that check here once instead of in every component.
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
	 * Boots one component and recurses into its declared children: gate, initialize, descend. A
	 * component whose `is_needed()` returns false prunes its whole subtree unconstructed. A class
	 * reached twice anywhere in the graph is a developer error.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   class-string<Component>              $component_class The component class to boot.
	 * @param   array<class-string<Component>, true> $seen             Component classes already
	 *                                                                 reached in the graph.
	 *
	 * @throws  \LogicException When a component class appears more than once in the graph.
	 *
	 * @return  void
	 */
	private function boot_component( string $component_class, array &$seen ): void {
		if ( isset( $seen[ $component_class ] ) ) {
			// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Registered class names are developer-controlled identifiers in a LogicException.
			throw new \LogicException(
				\sprintf(
					'Component %s is registered more than once; a component may belong to a single parent.',
					$component_class
				)
			);
			// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
		$seen[ $component_class ] = true;

		$component = new $component_class();
		if ( ! $component->is_needed() ) {
			return;
		}

		$component->initialize();

		if ( $component instanceof ComponentContainer ) {
			foreach ( $component::get_child_component_classes() as $child_class ) {
				$this->boot_component( $child_class, $seen );
			}
		}
	}

	// endregion

	// region HOOKS

	/**
	 * Boots the plugin's component tree when the plugin reports itself as needed. Idempotent: only
	 * the first eligible call has any effect.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function boot(): void {
		if ( $this->booted || ! $this->is_needed() ) {
			return;
		}

		$this->booted = true;

		$seen = array();
		foreach ( self::COMPONENTS as $component_class ) {
			$this->boot_component( $component_class, $seen );
		}
	}

	// endregion
}
