=== EventLayer ===
Contributors: spotfincreative
Tags: analytics, google analytics, gtm, data layer, events
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

EventLayer is a WordPress Plugin for managing custom DataLayer and GTM Events.

== Description ==

EventLayer allows site administrators to define custom GA4-style DataLayer events directly from the WordPress admin panel. The plugin stores event rules and automatically injects JavaScript to push events to window.dataLayer for Google Tag Manager and Google Analytics 4 integration.

**Key Features:**

* Define custom DataLayer events from WordPress admin
* Store event rules in the database
* Automatic JavaScript injection for event tracking
* Modern PHP architecture with PSR-4 autoloading
* Clean, extensible codebase

**Perfect for:**

* Marketing teams who need custom event tracking
* Developers building analytics-heavy websites
* Agencies managing multiple client sites
* Anyone who wants better control over their DataLayer events

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/eventlayer` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the EventLayer settings screen to configure your event rules.

== Frequently Asked Questions ==

= What is the DataLayer? =

The DataLayer is a JavaScript object used by Google Tag Manager and Google Analytics to collect and manage data about user interactions on your website.

= Do I need Google Tag Manager to use this plugin? =

While the plugin is designed to work seamlessly with Google Tag Manager, it pushes events to the standard window.dataLayer object, so it can work with any analytics system that reads from the DataLayer.

= Is this plugin GDPR compliant? =

The plugin itself doesn't collect personal data. However, the events you create may collect data, so you should ensure your event configurations comply with GDPR and other privacy regulations.

== Screenshots ==

1. Event rules management interface
2. Event configuration screen
3. DataLayer output in browser console

== Changelog ==

= 1.0.0 =
* Initial release
* Event rules management
* Database storage for event configurations
* Automatic JavaScript injection

== Upgrade Notice ==

= 1.0.0 =
Initial release of EventLayer plugin.
