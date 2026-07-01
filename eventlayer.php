<?php
/**
 * Plugin Name: EventLayer
 * Plugin URI:  https://eventlayerpro.com
 * Description: EventLayer is a WordPress Plugin for managing custom DataLayer and GTM Events
 * Version:     1.0.0
 * Author:      Spotfin Creative
 * Author URI:  https://spotfincreative.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Text Domain: eventlayer
 * Domain Path: /languages
 *
 * @package EventLayer
 * @since 1.0.0
 */

// Block direct access to this file.
defined( 'ABSPATH' ) || exit;

// Load Composer's autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Initialize the plugin.
EventLayer\Plugin::get_instance( __FILE__ );
