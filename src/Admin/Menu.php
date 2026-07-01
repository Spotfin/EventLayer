<?php
/**
 * Admin menu registration and page rendering.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

use EventLayer\Data\EventRuleRepository;

/**
 * Registers the EventLayer admin menu and renders its pages.
 *
 * @package EventLayer\Admin
 * @since 1.0.0
 */
class Menu {

	/**
	 * Event rule repository.
	 *
	 * @var EventRuleRepository
	 */
	private EventRuleRepository $repository;

	/**
	 * Constructor.
	 *
	 * @param EventRuleRepository|null $repository Repository (optional, for testing).
	 */
	public function __construct( ?EventRuleRepository $repository = null ) {
		$this->repository = $repository ?? new EventRuleRepository();
	}

	/**
	 * Initialize the menu.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Add admin menu pages.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		// Main EventLayer menu page.
		add_menu_page(
			__( 'EventLayer', 'eventlayer' ),
			__( 'EventLayer', 'eventlayer' ),
			'manage_options',
			'eventlayer',
			array( $this, 'admin_page' ),
			'dashicons-analytics',
			30
		);

		// Event Rules submenu (points to CPT list).
		add_submenu_page(
			'eventlayer',
			__( 'Event Rules', 'eventlayer' ),
			__( 'Event Rules', 'eventlayer' ),
			'manage_options',
			'edit.php?post_type=' . EventRulePostType::POST_TYPE
		);

		// Add New Event Rule submenu.
		add_submenu_page(
			'eventlayer',
			__( 'Add New Event Rule', 'eventlayer' ),
			__( 'Add New', 'eventlayer' ),
			'manage_options',
			'post-new.php?post_type=' . EventRulePostType::POST_TYPE
		);

		// Settings submenu.
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
	 * Display main admin page.
	 *
	 * @return void
	 */
	public function admin_page() {
		$recent_rules = $this->repository->find_recent( 5 );
		include __DIR__ . '/Views/admin-main.php';
	}

	/**
	 * Display settings page.
	 *
	 * @return void
	 */
	public function settings_page() {
		include __DIR__ . '/Views/admin-settings.php';
	}
}
