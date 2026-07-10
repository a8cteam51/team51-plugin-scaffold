import { createHooks } from '@wordpress/hooks';
import domReady from '@wordpress/dom-ready';

window.a8csp_scaffold = window.a8csp_scaffold || {};
window.a8csp_scaffold.hooks = createHooks();

domReady( () => {
	window.a8csp_scaffold.hooks.doAction( 'editor.ready' );
} );
