# EventLayer Pro Gating System

## Overview

EventLayer includes a flexible pro gating system that allows you to:
- **Enable all features** (no gating) - default for development
- **Enable pro gating** - restricts certain features to "pro" users
- **Easy toggle** between modes for testing

## Quick Toggle

**Current Status:** All pro features are **ENABLED** by default (no gating active)

### To Enable Pro Gating:

1. **Edit ProManager.php** (line 20):
   ```php
   private static $pro_features_enabled = false; // Change to false to enable gating
   ```

2. **Or use the development toggle** (when WP_DEBUG is true):
   - Look for "EventLayer Pro: Enabled" in the admin bar
   - Click to toggle between enabled/disabled

## Features That Get Gated

When gating is **enabled** (`$pro_features_enabled = false`), these features become "Pro only":

### ❌ Restricted (Pro Only)
- **Site Location Targeting** - Homepage, specific pages
- **Event Trigger Delays** - Timing control
- **Stop Propagation** - Event bubbling control
- **Element Attribute Extraction** - Get values from HTML attributes
- **URL Parameter Extraction** - Get values from query strings
- **Rule Limits** - Free users limited to 5 rules

### ✅ Always Available (Free)
- **Basic Event Tracking** - Click, submit events
- **Static Values** - Hardcoded parameter values
- **Element Text Extraction** - Get text content
- **CSS Selectors** - Basic targeting
- **Debug Mode** - Console logging

## User Experience

### Free Users See:
- 🔒 Pro upgrade prompts instead of gated features
- "Upgrade to EventLayer Pro" buttons
- Rule limit warnings (4/5 rules used)
- Disabled "Add New" button when limit reached

### Pro Users See:
- All features unlocked
- No upgrade prompts
- Unlimited rules
- Full feature set

## Development Workflow

### 1. Development (All Features)
```php
private static $pro_features_enabled = true; // Everything unlocked
```
- Build and test all features
- No restrictions
- Full functionality

### 2. Testing Pro Gating
```php
private static $pro_features_enabled = false; // Enable gating
```
- Test free user experience
- Verify upgrade prompts show
- Test rule limits
- Ensure gated features are hidden

### 3. Production Deploy
- **Free Version:** `$pro_features_enabled = false`
- **Pro Version:** Hook into license system in `check_pro_license()`

## Code Structure

### Core Files
- `src/Pro/ProManager.php` - Central gating logic
- `src/Admin/CPT/MetaBoxes.php` - UI gating implementation
- `src/Admin/Controllers/EventRulesController.php` - Rule limits
- `src/Admin/Helpers/DevHelper.php` - Development tools

### Key Methods
```php
// Check if pro features are available
ProManager::is_pro_active()

// Check specific feature
ProManager::has_feature('site_location')

// Check rule limits
ProManager::can_create_rule()

// Render upgrade prompts
ProManager::render_feature_gate($feature, $title, $description)
```

## Future Pro License Integration

When ready to implement actual pro licensing:

1. Update `check_pro_license()` method
2. Add license key validation
3. Connect to remote licensing server
4. Keep the same gating structure

The current system is designed to easily integrate with any licensing solution while maintaining the same user experience.
