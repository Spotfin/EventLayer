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
 * Text Domain: eventlayer
 * Domain Path: /languages
 *
 * @package EventLayer
 * @since 1.0.0
 */

// Block direct access to this file.
defined( 'ABSPATH' ) or die();

// Load Composer’s autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Define the main plugin class.
class EventLayer_Plugin {

    public function __construct() {
        // Hook into plugins_loaded action hook to initialize this class.
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function init() {
        // Your initialization code goes here...
    }
}

// Initialize the class on plugins_loaded action.
new EventLayer_Plugin();

register_activation_hook( __FILE__, 'EventLayer\Data\Installer::activate' );
