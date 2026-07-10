<?php declare( strict_types=1 );

/**
 * Single source of the plugin's persisted footprint: every wp_options row and user-meta key any
 * component writes. Framework-free and autoload-free on purpose — `uninstall.php` requires this
 * file directly, with no Composer autoloader and no Plugin/Component classes loaded, so the two
 * can never desync. The underscore prefix opts this file out of the includes/ drop-in loader in
 * `functions.php`; it must never run during a normal request.
 *
 * The scaffold ships with no persisted state, so both lists start empty. Add an entry here in the
 * same change that introduces an option or user-meta write, e.g. a component that persists a
 * setting would add its option name to the `options` list below.
 * Other persisted storage (transients, cron events, custom tables, and site options) follows the
 * same pattern: list each key in a matching manifest entry and delete it in `uninstall.php`
 * alongside the existing loops.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @package A8C\SpecialProjects\Plugins
 */

return array(
	'options'   => array(),
	'user_meta' => array(),
);
