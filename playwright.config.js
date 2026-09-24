// Match `port` in .wp-env.json. Set BEFORE requires so @wordpress/scripts
// picks it up (it derives use.baseURL + webServer.port + globalSetup from this).
process.env.WP_BASE_URL = 'http://localhost:8893';

// Keep Playwright outputs (storage states, test-results) out of the repo root.
const path = require( 'path' );
process.env.WP_ARTIFACTS_PATH = path.join( __dirname, 'tests', '.cache', 'artifacts' );

const { defineConfig } = require( '@playwright/test' );
const baseConfig = require( '@wordpress/scripts/config/playwright.config.js' );

module.exports = defineConfig( {
	...baseConfig,
	testDir: './tests/EndToEnd',
	webServer: {
		...baseConfig.webServer,
		command: 'npm run wp-env:start',
	},
} );
