# AidData Home Catalog

Custom WordPress plugin for managing the homepage catalog cards, filter terms, and info modals.

## Compatibility

This plugin is expected to load on WordPress environments that may not yet be on PHP 8. Runtime-loaded files should stay compatible with PHP 7.x.

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
- Open the `Home Catalog` admin screen and confirm cards still render and save normally.
