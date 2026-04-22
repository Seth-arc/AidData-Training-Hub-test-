# AidData Home Catalog

Custom WordPress plugin for managing the homepage catalog cards, filter terms, and info modals.

## Compatibility

This plugin is expected to load on WordPress environments that may not yet be on PHP 8. Runtime-loaded files should stay compatible with PHP 7.x.

The runtime is currently fail-open disabled in [aiddata-home-catalog.php](<c:\Users\ssnguna\Local Sites\AidData-Training-Hub-test-\app\public\wp-content\plugins\aiddata-home-catalog\aiddata-home-catalog.php>) by default:

- `AIDDATA_HOME_CATALOG_ENABLE_RUNTIME` defaults to `false`
- the bootstrap leaves behind a harmless `aiddata_home_catalog_render_front_page()` stub so the theme can still include the file without crashing

This is an intentional recovery state to restore `wp-admin` and the public site after the `418c682` catalog rollout.

Avoid introducing these constructs into the plugin runtime without an explicit PHP floor increase:

- `str_starts_with()` and `str_ends_with()`
- Typed class properties
- Arrow functions in files loaded on every request

Use `AidData_Home_Catalog_Compat` for string prefix/suffix checks instead.

## Verification

Human-run checks for this plugin:

- `php app/public/wp-content/plugins/aiddata-home-catalog/tests/compatibility-smoke.php`
  Pass: prints `AidData home catalog compatibility smoke test passed`
- Load the site homepage in Local and confirm the WordPress critical-error screen no longer appears.
- Open `wp-admin` and confirm the dashboard loads again.
- If recovery is successful, the homepage should fall back to the theme’s static catalog section until the plugin runtime is explicitly re-enabled and re-verified.
