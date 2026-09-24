<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold;

defined( 'ABSPATH' ) || exit;

/**
 * Groups the plugin's third-party integrations under one `ComponentContainer` node. This is the
 * fleet's worked example of the container pattern: a container may also do real work in its own
 * `initialize()` (e.g. wire a filter shared by all children), but this one groups only — each
 * child gates and wires itself.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
final class Integrations implements Component, ComponentContainer {
	// region METHODS

	/**
	 * The group itself is unconditional; each child gates itself via its own `is_needed()`.
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
	 * Empty on purpose: this container is a pure grouping node with no wiring of its own. A
	 * container that also does real work would put it here, ahead of its children.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void {}

	/**
	 * Class names of the integrations this container groups, booted in this order.
	 *
	 * Add your integrations here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array<int, class-string<Component>>
	 */
	public static function get_child_component_classes(): array {
		return array(
			Integrations\WC_Subscriptions::class,
		);
	}

	// endregion
}
