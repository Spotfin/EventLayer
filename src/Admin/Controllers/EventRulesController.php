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
        // Register settings here
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
