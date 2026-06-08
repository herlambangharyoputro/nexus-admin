# Changelog

Semua perubahan penting pada package ini didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/),
dan project ini menganut [Semantic Versioning](https://semver.org/).

## [0.1.0] - 2026-06-07

Rilis awal Nexus Admin sebagai Composer package (presentation layer only).

### Added
- Package scaffold: `composer.json` (`type: library`, MIT), PSR-4 autoload
  `Nexus\Admin\` → `src/`, dependency `php: ^8.2` + `illuminate/support: ^12.0`
  (target Laravel 12).
- `NexusServiceProvider`: `loadViewsFrom` (namespace `nexus`),
  `publishes` tag `nexus-assets`, `publishes` tag `nexus-views`.
  Auto-discovery via `extra.laravel.providers`.
- View namespace `nexus::` untuk seluruh view package (layout, partials,
  components, icons).
- Layout master `nexus::layouts.app` — yields: `title`, `content`,
  `page-title`, `breadcrumb`; stacks: `styles`, `scripts`.
- Partials: `nexus::partials.sidebar` (nav `url()` hardcoded + active state
  via `request()->is()`), `nexus::partials.topbar` (dummy user data +
  dropdown), `nexus::partials.toasts`.
- Anonymous components ber-API (`<x-nexus::...>`): `card`, `stat-card`,
  `button`, `avatar`, `modal`, `alert`, `badge`. Kontrak props/slots
  terdokumentasi di `GUIDELINES.md`.
- Icon set sebagai anonymous components: `<x-nexus::icons.*>`.
- CSS utility classes non-component (dipakai langsung via markup):
  `data-table`, `pagination`, `form-*`, `toggle`, dll.
- Pre-compiled assets (publish via `nexus-assets` → `public/nexus/assets`):
  CSS (`tokens`, `base`, `utilities`, `layout`, `components`, master `app.css`
  dengan import order terkunci), JS (`app.js` + `vendor/jquery-3.7.1.min.js`).
- Override mechanism: `vendor:publish --tag=nexus-views` →
  `resources/views/vendor/nexus` untuk kustomisasi view oleh consumer.

[0.1.0]: https://github.com/herlambangharyoputro/nexus-admin/releases/tag/v0.1.0