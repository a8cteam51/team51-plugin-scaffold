<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold;

defined( 'ABSPATH' ) || exit;

/**
 * A component that owns child components. The plugin's boot walks the children itself —
 * a parent never boots its own children — and prunes the whole subtree when this
 * component's `is_needed()` returns false, so a gated-off group can never leave a
 * descendant running. Static so the graph is walkable before any instance exists.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
interface ComponentContainer {
	/**
	 * Class names of the child components this component owns, booted in this order
	 * after the parent initializes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array<int, class-string<Component>>
	 */
	public static function get_child_component_classes(): array;
}
