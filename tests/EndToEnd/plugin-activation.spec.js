const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Plugin activation', () => {
	test( 'the scaffold plugin is listed as active', async ( { admin, page } ) => {
		await admin.visitAdminPage( 'plugins.php' );

		const pluginRow = page.locator( 'tr[data-slug="a8csp-plugin-scaffold"]' );
		await expect( pluginRow ).toHaveClass( /active/ );
	} );
} );
