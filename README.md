# A8CSP Plugin Scaffold

A scaffold for A8C Special Projects / Team 51 WordPress plugins.

This repository is a template plugin, not a finished product plugin. It contains
the PHP bootstrap, block and asset build setup, automated test suite, and
GitHub Actions workflow used to turn the scaffold into a new plugin repository.

## What is in this repository

- `team51-plugin-scaffold.php` is the scaffold plugin bootstrap. It defines the
  plugin header, constants, translation loading, WooCommerce HPOS compatibility,
  autoloader check, and requirement validation.
- `functions-bootstrap.php` contains metadata, version compatibility, and admin
  notice helpers that are available before the full plugin loads.
- `functions.php` exposes the main plugin singleton. PHP helper files under
  `includes/` (currently `includes/assets.php`) are loaded through Composer's
  `autoload.files` entry rather than a runtime glob.
- `src/` contains the PSR-4 classes under `A8C\SpecialProjects\Scaffold`,
  including the main `Plugin`, block registration, and integration coordinator.
- `src/Integrations/WC_Subscriptions.php` is an example optional integration
  with placeholder hook and filter methods.
- `includes/` (Composer `files`-autoloaded helper functions) and `languages/`
  (translations) are extension points.
- `blocks/src/foobar/` is the source for an example block. `blocks/build/foobar/`
  is the tracked build output registered by `src/Blocks.php`.
- `assets/js/src/editor.js` defines the shared editor hook entry point.
  `assets/js/build/` contains the tracked build output used in the editor.
- `tests/` contains the automated test suite. See `tests/README.md` for the
  local test workflow.
- `.github/workflows/` contains PHP, JavaScript, CSS, syntax, and
  scaffold-fill workflows.

## Scaffold generation

The `.github/workflows/fill-in-scaffold.yml` workflow runs on
`repository_dispatch` with the `fill_scaffold` type, or manually through
`workflow_dispatch`. It is guarded so it does not run on this scaffold
repository itself.

For generated repositories, the workflow:

1. Renames `README.scaffold.md` to `README.md`.
2. Renames `team51-plugin-scaffold.php` to the generated repository name.
3. Runs `.github/workflows/fill-in-scaffold.mjs` to replace scaffold strings.
4. Commits and pushes the renamed and filled files.

The replacement script uses the GitHub repository name, repository description,
and these repository custom properties:

- `human-title` for the human-readable plugin title.
- `php-globals-short-prefix` for the PHP global function and constant prefix.

The script replaces the following tracked template values:

- `EXAMPLE_REPO_NAME` and `EXAMPLE_REPO_DESCRIPTION` in the generated
  `README.md`.
- `A8CSP Plugin Scaffold`, `A scaffold for A8C Special Projects plugins.`,
  `team51-plugin-scaffold`, and `a8csp-scaffold` outside the generated README.
- `A8C\SpecialProjects\Scaffold` with a title-derived namespace.
- `a8csp_scaffold` and `A8CSP_SCAFFOLD` with the configured PHP prefix.

After generation, review the remaining example identifiers that the script does
not replace, including the example block copy (block title, description, and
sample text) and the placeholder WooCommerce Subscriptions hook methods.

## Runtime requirements

The tracked scaffold files declare these runtime targets:

- WordPress `7.0` in the plugin header.
- PHP `>=8.5` in `composer.json` and `8.5` in `.wp-env.json`.
- WooCommerce `10.9` in the plugin header and `wpackagist-plugin/woocommerce`
  `10.9.*` as a development dependency.
- Composer for PHP dependency installation and autoload generation.
- Node.js `>=26` and npm `>=11` for JavaScript, CSS, block, and markdown
  tooling.
- Docker for the `wp-env` local environment.

The plugin checks for WooCommerce before initializing its components, and the
main bootstrap declares compatibility with WooCommerce custom order tables.

## Development

Install PHP dependencies:

```sh
composer run-script packages-install
```

Install JavaScript dependencies:

```sh
npm ci
```

Build blocks and editor assets:

```sh
npm run build
```

Run watch builds:

```sh
npm start
```

Run the local WordPress environment:

```sh
npm run wp-env:start
```

Stop the local WordPress environment:

```sh
npm run wp-env:stop
```

Generate translation files:

```sh
composer run-script internationalize
```

## Quality checks

PHP checks are configured through `.phpcs.xml`, `.phpcs.tests.xml`, `.phpstan.neon`,
`.composer-require-checker.json`, and the shared `a8cteam51/team51-configs` package:

```sh
composer run-script lint:php
```

JavaScript, CSS, package metadata, and README markdown checks are defined in
`package.json`:

```sh
npm run lint:scripts
npm run lint:styles
npm run lint:pkg-json
npm run lint:readme-md
```

The GitHub workflows, including the JavaScript/CSS and PHP syntax workflows, run
on `trunk` pushes and on pull requests.

## Tests

The suite has three PHPUnit tiers (Unit, Integration, Requirements) plus a Playwright end-to-end
suite. See `tests/README.md` for how to run each suite, the wp-env ports involved, and why the rig
runs PHPUnit 13 against plain `TestCase` instead of `WP_UnitTestCase`.

```sh
composer test:unit
npm run wp-env:tests:start && composer test:integration
composer test:requirements
npm run test:e2e
```

## Maintenance notes

- Customize source files under `src/`, `includes/`, `blocks/src/`, `assets/js/src/`,
  `assets/css/src/`, and `languages/`.
- Rebuild generated assets after changing block or editor sources. The tracked
  generated outputs live in `blocks/build/` and `assets/js/build/`.
- Do not commit dependency directories such as `vendor/` or `node_modules/`.
