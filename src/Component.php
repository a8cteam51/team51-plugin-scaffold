<?php declare( strict_types=1 );

namespace A8C\SpecialProjects\Scaffold;

defined( 'ABSPATH' ) || exit;

/**
 * Contract for a self-contained plugin component.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
interface Component {
	/**
	 * Returns true if the component should be initialized on the current site.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_needed(): bool;

	/**
	 * Wires the component into WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public function initialize(): void;
}
