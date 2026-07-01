<?php
/**
 * Main plugin bootstrap class for EventLayer.
 *
 * @package EventLayer
 */

namespace EventLayer;

/**
 * Main plugin class for EventLayer.
 *
 * @package EventLayer
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_file Main plugin file path.
	 */
	private function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_url  = plugin_dir_url( $plugin_file );
		$this->plugin_path = plugin_dir_path( $plugin_file );

		$this->init_hooks();
	}

	/**
	 * Get plugin instance.
	 *
	 * @param string $plugin_file Main plugin file path.
	 * @return Plugin
	 */
	public static function get_instance( $plugin_file = '' ) {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_file );
		}
		return self::$instance;
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Register activation hook.
		register_activation_hook( $this->plugin_file, array( $this, 'activate' ) );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init() {
		// Initialize Pro Manager.
		\EventLayer\Pro\ProManager::init();

		// Initialize plugin components.
		$this->init_admin();
		$this->init_public();
	}

	/**
	 * Initialize admin components.
	 *
	 * @return void
	 */
	private function init_admin() {
		if ( is_admin() ) {
			// Initialize Custom Post Type.
			$post_type = new \EventLayer\Admin\CPT\EventRulePostType();
			$post_type->init();

			// Initialize Meta Boxes.
			$meta_boxes = new \EventLayer\Admin\CPT\MetaBoxes();
			$meta_boxes->init();

			// Initialize Save Handler.
			$save_handler = new \EventLayer\Admin\CPT\SaveHandler();
			$save_handler->init();

			// Initialize admin controller (for menu integration).
			$admin_controller = new \EventLayer\Admin\Controllers\AdminController();
			$admin_controller->init();

			// Initialize development helper.
			\EventLayer\Admin\Helpers\DevHelper::init();
		}
	}

	/**
	 * Plugin activation callback.
	 *
	 * @return void
	 */
	public function activate() {
		// Flush rewrite rules to register the new post type.
		flush_rewrite_rules();
	}

	/**
	 * Initialize public components.
	 *
	 * @return void
	 */
	private function init_public() {
		if ( ! is_admin() ) {
			// Initialize script injector.
			$script_injector = new \EventLayer\Public\ScriptInjector();
			$script_injector->init();
		}
	}

	/**
	 * Load plugin textdomain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'eventlayer',
			false,
			dirname( plugin_basename( $this->plugin_file ) ) . '/languages/'
		);
	}

	/**
	 * Get plugin URL.
	 *
	 * @param string $path Path to append.
	 * @return string
	 */
	public function get_plugin_url( $path = '' ) {
		return $this->plugin_url . $path;
	}

	/**
	 * Get plugin path.
	 *
	 * @param string $path Path to append.
	 * @return string
	 */
	public function get_plugin_path( $path = '' ) {
		return $this->plugin_path . $path;
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public function get_version() {
		return self::VERSION;
	}
}
