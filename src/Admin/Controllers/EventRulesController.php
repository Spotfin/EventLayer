<?php

namespace EventLayer\Admin\Controllers;

/**
 * Controller for managing event rules in the admin.
 *
 * @package EventLayer\Admin\Controllers
 * @since 1.0.0
 */
class EventRulesController extends BaseController {

	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function init() {
		$this->register_hooks();
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	protected function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'maybe_show_rule_limit_notice' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'maybe_disable_add_new_button' ) );
		add_filter( 'post_row_actions', array( $this, 'maybe_limit_row_actions' ), 10, 2 );
	}

	/**
	 * Add admin menu pages.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		// Main EventLayer menu page
		add_menu_page(
			__( 'EventLayer', 'eventlayer' ),
			__( 'EventLayer', 'eventlayer' ),
			'manage_options',
			'eventlayer',
			array( $this, 'admin_page' ),
			'dashicons-analytics',
			30
		);

		// Event Rules submenu (points to CPT list)
		add_submenu_page(
			'eventlayer',
			__( 'Event Rules', 'eventlayer' ),
			__( 'Event Rules', 'eventlayer' ),
			'manage_options',
			'edit.php?post_type=' . \EventLayer\Admin\CPT\EventRulePostType::POST_TYPE
		);

		// Add New Event Rule submenu
		add_submenu_page(
			'eventlayer',
			__( 'Add New Event Rule', 'eventlayer' ),
			__( 'Add New', 'eventlayer' ),
			'manage_options',
			'post-new.php?post_type=' . \EventLayer\Admin\CPT\EventRulePostType::POST_TYPE
		);

		// Settings submenu
		add_submenu_page(
			'eventlayer',
			__( 'Settings', 'eventlayer' ),
			__( 'Settings', 'eventlayer' ),
			'manage_options',
			'eventlayer-settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		// Register settings group
		register_setting(
			'eventlayer_settings',
			'eventlayer_debug_mode',
			array(
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
			)
		);

		register_setting(
			'eventlayer_settings',
			'eventlayer_auto_pageview',
			array(
				'type'              => 'boolean',
				'default'           => true,
				'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
			)
		);

		   // Add settings section
		   add_settings_section(
			   'eventlayer_general_settings',
			   __( 'General Settings', 'eventlayer' ),
			   null,
			   'eventlayer_settings'
		   );

		   // Add settings fields
		   add_settings_field(
			   'eventlayer_debug_mode',
			   __( 'Debug Mode', 'eventlayer' ),
			   array( $this, 'debug_mode_field_callback' ),
			   'eventlayer_settings',
			   'eventlayer_general_settings'
		   );

		   add_settings_field(
			   'eventlayer_auto_pageview',
			   __( 'Auto Page View Tracking', 'eventlayer' ),
			   array( $this, 'auto_pageview_field_callback' ),
			   'eventlayer_settings',
			   'eventlayer_general_settings'
		   );

		   // Register license key option
		   register_setting(
			   'eventlayer_settings',
			   'eventlayer_license_key',
			   array(
				   'type'              => 'string',
				   'default'           => '',
				   'sanitize_callback' => 'sanitize_text_field',
			   )
		   );

		   // Add license key field
		   add_settings_field(
			   'eventlayer_license_key',
			   __( 'Pro License Key', 'eventlayer' ),
			   array( $this, 'license_key_field_callback' ),
			   'eventlayer_settings',
			   'eventlayer_general_settings'
		   );

	   }

	   /**
		* License key field callback.
		*
		* @return void
		*/
	   public function license_key_field_callback() {
		   $value = get_option( 'eventlayer_license_key', '' );
		   echo '<input type="text" name="eventlayer_license_key" value="' . esc_attr( $value ) . '" class="regular-text" autocomplete="off" />';
		   echo '<p class="description">' . esc_html__( 'Enter your EventLayer Pro license key to enable Pro features and updates.', 'eventlayer' ) . '</p>';
	   }


	/**
	 * Sanitize checkbox input.
	 *
	 * @param mixed $input Input value.
	 * @return int
	 */
	public function sanitize_checkbox( $input ) {
		return ! empty( $input ) ? 1 : 0;
	}

	/**
	 * Debug mode field callback.
	 *
	 * @return void
	 */
	public function debug_mode_field_callback() {
		$value = get_option( 'eventlayer_debug_mode', 0 );
		echo '<label>';
		echo '<input type="checkbox" name="eventlayer_debug_mode" value="1" ' . checked( $value, 1, false ) . ' />';
		echo ' ' . esc_html__( 'Enable debug logging in browser console', 'eventlayer' );
		echo '</label>';
		echo '<p class="description">' . esc_html__( 'When enabled, EventLayer will log events to the browser console for debugging.', 'eventlayer' ) . '</p>';
	}

	/**
	 * Auto page view field callback.
	 *
	 * @return void
	 */
	public function auto_pageview_field_callback() {
		$value = get_option( 'eventlayer_auto_pageview', 1 );
		echo '<label>';
		echo '<input type="checkbox" name="eventlayer_auto_pageview" value="1" ' . checked( $value, 1, false ) . ' />';
		echo ' ' . esc_html__( 'Automatically track page views', 'eventlayer' );
		echo '</label>';
		echo '<p class="description">' . esc_html__( 'Automatically push page_view events to the dataLayer on page load.', 'eventlayer' ) . '</p>';
	}

	/**
	 * Display main admin page.
	 *
	 * @return void
	 */
	public function admin_page() {
		include __DIR__ . '/../Views/admin-main.php';
	}

	/**
	 * Display settings page.
	 *
	 * @return void
	 */
	public function settings_page() {
		include __DIR__ . '/../Views/admin-settings.php';
	}

	/**
	 * Show rule limit notice on relevant admin pages.
	 *
	 * @return void
	 */
	public function maybe_show_rule_limit_notice() {
		// Only show on event rule pages
		$screen = get_current_screen();
		if ( ! $screen || 'event_rule' !== $screen->post_type ) {
			return;
		}

		// Don't show if pro features are enabled
		if ( ! \EventLayer\Pro\ProManager::should_enforce_limits() ) {
			return;
		}

		$current_count = \EventLayer\Pro\ProManager::get_current_rule_count();
		$max_rules     = \EventLayer\Pro\ProManager::get_max_rules();

		// Show warning when approaching limit
		if ( $current_count >= $max_rules - 1 && $current_count < $max_rules ) {
			?>
			<div class="notice notice-warning">
				<p>
					<strong><?php esc_html_e( 'EventLayer:', 'eventlayer' ); ?></strong>
					<?php
					printf(
						esc_html__( 'You are approaching the limit of %d event rules. You have %d rules remaining.', 'eventlayer' ),
						$max_rules,
						$max_rules - $current_count
					);
					?>
					<a href="<?php echo esc_url( \EventLayer\Pro\ProManager::get_upgrade_url() ); ?>" target="_blank">
						<?php esc_html_e( 'Upgrade to EventLayer Pro for unlimited rules', 'eventlayer' ); ?>
					</a>
				</p>
			</div>
			<?php
		}

		// Show error when limit reached
		if ( $current_count >= $max_rules ) {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e( 'EventLayer:', 'eventlayer' ); ?></strong>
					<?php
					printf(
						esc_html__( 'You have reached the limit of %d event rules.', 'eventlayer' ),
						$max_rules
					);
					?>
					<a href="<?php echo esc_url( \EventLayer\Pro\ProManager::get_upgrade_url() ); ?>" target="_blank">
						<?php esc_html_e( 'Upgrade to EventLayer Pro for unlimited rules', 'eventlayer' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Maybe disable the "Add New" button when limit reached.
	 *
	 * @return void
	 */
	public function maybe_disable_add_new_button() {
		if ( ! \EventLayer\Pro\ProManager::can_create_rule() ) {
			add_action( 'admin_head', array( $this, 'hide_add_new_button' ) );
		}
	}

	/**
	 * Hide the "Add New" button with CSS.
	 *
	 * @return void
	 */
	public function hide_add_new_button() {
		$screen = get_current_screen();
		if ( $screen && 'event_rule' === $screen->post_type ) {
			?>
			<style>
				.page-title-action,
				.row-actions .inline,
				.row-actions .duplicate {
					display: none !important;
				}
			</style>
			<?php
		}
	}

	/**
	 * Maybe limit row actions when at rule limit.
	 *
	 * @param array    $actions Row actions.
	 * @param \WP_Post $post    Post object.
	 * @return array
	 */
	public function maybe_limit_row_actions( $actions, $post ) {
		if ( 'event_rule' !== $post->post_type ) {
			return $actions;
		}

		if ( ! \EventLayer\Pro\ProManager::can_create_rule() ) {
			// Remove actions that create new posts
			unset( $actions['inline'], $actions['duplicate'] );
		}

		return $actions;
	}
}
