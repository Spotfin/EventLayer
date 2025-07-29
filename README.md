# EventLayer

[![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-green.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0%2B-red.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A modern WordPress plugin for managing custom GA4-style DataLayer events. EventLayer provides a user-friendly interface for creating sophisticated event tracking rules that automatically inject JavaScript for Google Tag Manager and Google Analytics 4 integration.

## 🚀 Features

- **Native WordPress Integration**: Uses custom post types for event rules with full revision history
- **Advanced Event Configuration**: Define event types, triggers, parameters, and conditions
- **Dynamic Parameter Extraction**: Extract values from element text, attributes, or URL parameters
- **Site Location Targeting**: Control where events are active (all pages, homepage, specific pages)
- **Event Control**: Configure trigger delays and event propagation
- **Debug Mode**: Comprehensive logging for troubleshooting
- **Modern Architecture**: PSR-4 autoloading, WordPress coding standards compliant
- **Performance Optimized**: Efficient JavaScript injection and event handling

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern browser with JavaScript enabled

## 🔧 Installation

### Via WordPress Admin
1. Download the plugin zip file
2. Go to **Plugins → Add New** in your WordPress admin
3. Click **Upload Plugin** and select the zip file
4. Click **Install Now** and then **Activate Plugin**

### Manual Installation
1. Upload the `eventlayer` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress

### Composer Installation
```bash
composer require spotfin/eventlayer
```

## 🎯 Quick Start

1. **Navigate** to **EventLayer** in your WordPress admin menu
2. **Click** "Add New" to create your first event rule
3. **Configure** your event:
   - **Event Type**: GA4 event name (e.g., `button_click`)
   - **Parent Selector**: CSS selector for trigger element (e.g., `.cta-button`)
   - **Parameters**: Data to send with the event
4. **Publish** the event rule to activate it
5. **Test** by enabling debug mode in EventLayer → Settings

## 📖 Usage Examples

### Basic Button Click Tracking
```
Event Type: button_click
Parent Selector: .download-button
Parameters:
  - button_text: Element Text → (uses clicked element's text)
  - section: Static Value → "hero"
```

### Form Submission Tracking
```
Event Type: form_submit
Parent Selector: form.contact-form
Parameters:
  - form_name: Element Attribute → data-form-name
  - page_section: URL Parameter → section
```

### Advanced Link Tracking
```
Event Type: link_click
Parent Selector: a[href*="external"]
Site Location: All Pages
Trigger Delay: 100ms
Stop Propagation: Yes
Parameters:
  - link_url: Element Attribute → href
  - link_text: Element Text
  - click_source: Static Value → "navigation"
```

## � Implementation Examples

### External Link Button Tracking

For a button like this:
```html
<a class="test-button" href="https://google.com" target="_blank">Button Text</a>
```

**EventLayer Configuration:**
- **Event Type**: `link_click`
- **Parent Selector**: `a.test-button`
- **Site Location**: All Pages
- **Trigger Delay**: 100ms
- **Stop Propagation**: Yes

**Parameters:**
| Parameter Name | Target Type | Target Selector | Default Value |
|----------------|-------------|-----------------|---------------|
| `button_text` | Element Text | *(clicked element)* | - |
| `button_url` | Element Attribute | `href` | - |
| `button_target` | Element Attribute | `target` | - |
| `button_class` | Static Value | - | `test-button` |

**Result in dataLayer:**
```javascript
{
  event: 'link_click',
  button_text: 'Change This',
  button_url: 'https://google.com',
  button_target: '_blank',
  button_class: 'test-button'
}
```

### Alternative Selectors for Different Use Cases

**Track all external links:**
```
Parent Selector: a[href^="http"]:not([href*="yourdomain.com"])
```

**Track specific URL:**
```
Parent Selector: a[href="https://google.com"]
```

**Track by multiple attributes:**
```
Parent Selector: a.test-button[target="_blank"]
```

## �🛠️ Event Configuration

### Event Settings
- **Event Type**: The GA4 event name sent to dataLayer
- **Site Location**: Where the event should be active
- **Trigger Delay**: Delay before event fires (in milliseconds)
- **Stop Propagation**: Prevent event bubbling

### Trigger Elements
- **Parent Selector**: CSS selector for the trigger element
- **Multiple Toggle**: Handle multiple instances of the selector
- **Child Selectors**: Additional selectors for more specific targeting

### Parameters
Each parameter can extract values using different target types:
- **Static Value**: Uses the default value
- **Element Text**: Extracts text content from target element
- **Element Attribute**: Extracts attribute value
- **URL Parameter**: Extracts value from query string

## 🎛️ Settings

Access plugin settings via **EventLayer → Settings**:

- **Debug Mode**: Enable console logging for troubleshooting
- **Auto Page View Tracking**: Automatically track page views

## 🧪 Development

### Prerequisites
- PHP 7.4+
- Composer
- Node.js (for asset building)

### Setup
```bash
# Clone the repository
git clone https://github.com/Spotfin/EventLayer.git
cd EventLayer

# Install PHP dependencies
composer install

# Install and configure coding standards
composer run phpcs:setup
```

### Code Quality

The project follows WordPress coding standards. Use these commands for code quality:

```bash
# Check all files
composer run phpcs

# Fix all auto-fixable issues
composer run phpcbf

# Check just the src/ directory
composer run phpcs:src

# Fix just the src/ directory
composer run phpcbf:src

# Get a summary report
composer run lint
```

### Available Composer Scripts
```bash
composer run phpcs        # Run PHP CodeSniffer
composer run phpcbf       # Run PHP Code Beautifier and Fixer
composer run phpcs:src    # Check src/ directory only
composer run phpcbf:src   # Fix src/ directory only
composer run phpcs:main   # Check main plugin file only
composer run phpcbf:main  # Fix main plugin file only
composer run lint         # Quick summary report
composer run fix          # Fix all issues
```

### File Structure
```
eventlayer/
├── eventlayer.php           # Main plugin file
├── composer.json            # Composer configuration
├── phpcs.xml               # PHP CodeSniffer rules
├── README.md               # This file
├── readme.txt              # WordPress plugin readme
├── src/                    # Source code
│   ├── Plugin.php          # Main plugin class
│   ├── Admin/              # Admin functionality
│   │   ├── Controllers/    # Admin controllers
│   │   ├── CPT/           # Custom Post Type handling
│   │   ├── Views/         # Admin templates
│   │   └── Helpers/       # Admin helper functions
│   ├── Public/            # Frontend functionality
│   ├── Assets/            # CSS and JavaScript
│   └── Tests/             # Test files
├── languages/             # Translation files
└── vendor/               # Composer dependencies
```

### Architecture

EventLayer uses a modern architecture with:

- **PSR-4 Autoloading**: Organized namespace structure
- **Custom Post Types**: Native WordPress data storage
- **Meta Boxes**: Rich admin interface
- **Hook-based System**: Extensible via WordPress actions/filters
- **Singleton Pattern**: Single plugin instance
- **Repository Pattern**: Clean data access layer

### Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run code quality checks (`composer run lint`)
5. Fix any issues (`composer run fix`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Coding Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Use PHP 7.4+ features appropriately
- Maintain backwards compatibility
- Add inline documentation for all public methods
- Write unit tests for new functionality

## 🐛 Troubleshooting

### Debug Mode
Enable debug mode in **EventLayer → Settings** to see console output:
- Event initialization messages
- Trigger confirmations
- Parameter extraction details
- Error messages

### Common Issues

**Events not firing:**
- Check CSS selectors are correct
- Verify elements exist on the page
- Ensure event rule is published
- Check browser console for errors

**Parameters not working:**
- Verify target selectors
- Check parameter target types
- Test with static values first

**JavaScript errors:**
- Enable debug mode
- Check browser console
- Verify jQuery is loaded

## 📚 Hooks & Filters

EventLayer provides several hooks for customization:

### Actions
```php
// Before event rule processing
do_action( 'eventlayer_before_rule_processing', $rule );

// After event injection
do_action( 'eventlayer_after_script_injection', $rules );
```

### Filters
```php
// Filter event rules
$rules = apply_filters( 'eventlayer_event_rules', $rules );

// Filter JavaScript config
$config = apply_filters( 'eventlayer_js_config', $config );

// Filter debug mode
$debug = apply_filters( 'eventlayer_debug_mode', $debug );
```

## 📄 License

This project is licensed under the GPL-2.0+ License - see the [LICENSE](LICENSE) file for details.

## 🏢 About Spotfin Creative

EventLayer is developed by [Spotfin Creative](https://spotfincreative.com), a WordPress development agency specializing in custom solutions and performance optimization.

## 🔗 Links

- [Plugin Homepage](https://eventlayerpro.com)
- [Documentation](https://eventlayerpro.com/docs)
- [Support](https://eventlayerpro.com/support)
- [GitHub Repository](https://github.com/Spotfin/EventLayer)

## 📸 Screenshots

### Event Rules Management
![Event Rules List](screenshots/event-rules-list.png)

### Event Configuration
![Event Configuration](screenshots/event-config.png)

### Debug Console Output
![Debug Console](screenshots/debug-console.png)

---

Made with ❤️ by [Spotfin Creative](https://spotfincreative.com)
