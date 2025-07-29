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
                'type' => 'boolean',
                'default' => false,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' )
            )
        );

        register_setting( 
            'eventlayer_settings', 
            'eventlayer_auto_pageview',
            array(
                'type' => 'boolean',
                'default' => true,
                'sanitize_callback' => array( $this, 'sanitize_checkbox' )
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
        include dirname( __FILE__ ) . '/../Views/admin-main.php';
    }

    /**
     * Display settings page.
     *
     * @return void
     */
    public function settings_page() {
        include dirname( __FILE__ ) . '/../Views/admin-settings.php';
    }
}
