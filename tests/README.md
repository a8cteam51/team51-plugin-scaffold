# Tests

The test rig has three PHPUnit suites plus a Playwright end-to-end suite, run against wp-env
fixtures at three WordPress-version tiers.

## Suites

- **Unit** (`tests/Unit/`) — no WordPress, no wp-env. Runs against plain PHPUnit `TestCase` with
  hand-rolled recording doubles instead of Mockery or Brain Monkey (see `tests/Unit/Doubles/`).
  Fast; this is the suite `composer quality-check` runs on every push.
- **Integration** (`tests/Integration/`) — boots inside wp-env against a supported WordPress
  version and exercises the plugin's real boot path. `UninstallTest` runs the real
  `uninstall.php` end-to-end (seeds sentinels, defines `WP_UNINSTALL_PLUGIN`, asserts its
  footprint is gone and a canary key survives) inside `#[RunInSeparateProcess]`, since that
  constant must not leak into the rest of the suite.
- **Requirements** (`tests/Integration/RequirementsCheckTest.php`, run as its own suite) — boots
  inside wp-env against a below-floor WordPress version to verify the requirements gate degrades
  gracefully instead of fataling.
- **End-to-End** (`tests/EndToEnd/`) — Playwright, driving a real browser against the dev wp-env
  instance.

## WooCommerce-less boot proof

`PluginBootWithoutWooCommerceTest` verifies that the plugin and its WooCommerce-independent Blocks
component boot when WooCommerce is inactive. It self-skips whenever WooCommerce is active. To
exercise the proof, deactivate WooCommerce in the tests wp-env instance, run that test directly,
then reactivate WooCommerce before continuing with the Integration suite:

```sh
npm run wp-env:tests:start
wp-env --config .wp-env.tests.json run cli wp plugin deactivate woocommerce
wp-env --config .wp-env.tests.json run cli --env-cwd=wp-content/plugins/a8csp-plugin-scaffold vendor/bin/phpunit --filter=PluginBootWithoutWooCommerceTest
wp-env --config .wp-env.tests.json run cli wp plugin activate woocommerce
composer test:integration
npm run wp-env:tests:stop
```

## Running the suites

Unit (no wp-env required):

```sh
composer test:unit
```

Integration (start the tests wp-env instance first):

```sh
npm run wp-env:tests:start
composer test:integration
npm run wp-env:tests:stop
```

Requirements (start the below-floor wp-env instance first):

```sh
npm run wp-env:belowfloor:start
composer test:requirements
npm run wp-env:belowfloor:stop
```

End-to-end (Playwright starts and stops the dev wp-env instance itself via its `webServer` config):

```sh
npm run test:e2e
```

## Ports

| Environment | Config                    | Port |
| ----------- | ------------------------- | ---- |
| Dev / E2E   | `.wp-env.json`            | 8893 |
| Tests       | `.wp-env.tests.json`      | 8890 |
| Below-floor | `.wp-env.belowfloor.json` | 8891 |

## Why plain `TestCase`, not `WP_UnitTestCase`

WordPress core's own PHPUnit scaffold still caps at PHPUnit <=9, and core's migration plan
(#62004) only targets PHPUnit 11.1+ over several future releases — there is no core-provided
`WP_UnitTestCase` path onto a current PHPUnit today. This rig runs PHPUnit 13 directly, against
plain `TestCase`, inside wp-env, rather than waiting on that migration or pinning to an old
PHPUnit.

That trade gives up `$this->factory` fixture helpers, `go_to()` routing simulation, and
`WP_UnitTestCase`'s per-test transaction rollback. The first two exist for content- and
query-heavy plugins exercising post/term/user fixtures and template routing — this scaffold's
Integration suite is narrower (boot path, requirements gating), so their absence costs little.
Transaction rollback specifically would be counterproductive here: the Integration and
Requirements suites exist to observe persistence and boot-time side effects, and auto-rolling back
every test would mask exactly the behavior they're written to catch.

## Mutation testing

`composer test:unit:mutation` runs Infection against the Unit suite's source. It sits outside the
default `composer quality-check` target (only `quality-check:all` pulls it in) and does not gate
pull requests — it runs on its own weekly schedule in CI (`.github/workflows/tests-mutation.yml`),
since mutation testing is slow. Local runs on macOS are unreliable: a race in Infection's
coverage-XML tmpdir handling can produce zero generated mutants or a hang, independent of anything
in this repo's own configuration. Treat the CI job, not a local run, as authoritative for mutation
results.
