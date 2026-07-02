# EventLayer Free/Pro Gating

## Overview

Feature gating routes through a single provider resolved by
`EventLayer\Gating\Gating::provider()`:

- The free core ships `FreeGatingProvider` — no license logic, Pro features
  gated, published rules capped at 5.
- The **EventLayer-Pro** add-on (separate private repo) replaces the provider
  via the `eventlayer_gating_provider` filter with a licensed implementation.
  Installing Pro requires zero changes to this repo.

```php
use EventLayer\Gating\Gating;

Gating::provider()->has_feature( 'scheduling' );  // bool
Gating::provider()->get_max_rules();              // 0 = unlimited
Gating::provider()->get_upgrade_url();
```

Gated UI renders an upsell box via `EventLayer\Admin\FeatureGate::render()`,
and preserves stored values with hidden fields so a Pro downgrade never
erases data.

## Feature split

### ✅ Free
- Basic click event tracking, fires on **all** elements matching a selector
- Static parameter values
- Element Text extraction
- Element Attribute extraction (kept free so `href`/`data-*` tracking works)
- Basic CSS selector targeting
- Debug mode

### 🔒 Pro (feature slugs)
| Feature | Slug |
|---|---|
| Site location targeting | `site_location` |
| Trigger delay | `trigger_delay` |
| Stop propagation | `stop_propagation` |
| Child selectors | `child_selectors` |
| Per-instance element control | `multiple_toggle` |
| URL Parameter extraction | `url_parameter` |
| Scheduling | `scheduling` |
| Import / export | `import_export` |
| Advanced selectors | `advanced_selectors` |
| Unlimited rules (free capped at 5) | `unlimited_rules` |

## Contracts

- `EventLayer\Gating\GatingProvider` — `has_feature()`, `get_max_rules()`,
  `get_upgrade_url()`. Replace via `eventlayer_gating_provider`.
- `EventLayer\Gating\LicenseProvider` — `is_active()`, `get_license_key()`.
  No implementation in core; Pro provides `RemoteLicenseProvider`.

The full extension API (activation, frontend config, and meta box hooks) is
documented in the README under "Hooks & Filters".

## Testing the free experience

The free tier is simply this plugin without Pro installed. To test the
unlocked experience during development, install EventLayer-Pro and use its
WP_DEBUG admin-bar sandbox toggle (or `EVENTLAYER_DEV_LICENSE_PASS`).
