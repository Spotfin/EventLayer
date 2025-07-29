<?php

namespace EventLayer\Admin\CPT;

/**
 * Event Rule Custom Post Type registration and management.
 *
 * @package EventLayer\Admin\CPT
 * @since 1.0.0
 */
class EventRulePostType {

	/**
	 * Post type slug.
	 */
	const POST_TYPE = 'event_rule';

	/**
	 * Initialize the custom post type.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_meta_fields' ) );
		add_filter( 'enter_title_here', array( $this, 'change_title_placeholder' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'custom_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
	}

	/**
	 * Register the event_rule custom post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Event Rules', 'Post type general name', 'eventlayer' ),
			'singular_name'         => _x( 'Event Rule', 'Post type singular name', 'eventlayer' ),
			'menu_name'             => _x( 'Event Rules', 'Admin Menu text', 'eventlayer' ),
			'name_admin_bar'        => _x( 'Event Rule', 'Add New on Toolbar', 'eventlayer' ),
			'add_new'               => __( 'Add New', 'eventlayer' ),
			'add_new_item'          => __( 'Add New Event Rule', 'eventlayer' ),
			'new_item'              => __( 'New Event Rule', 'eventlayer' ),
			'edit_item'             => __( 'Edit Event Rule', 'eventlayer' ),
			'view_item'             => __( 'View Event Rule', 'eventlayer' ),
			'all_items'             => __( 'All Event Rules', 'eventlayer' ),
			'search_items'          => __( 'Search Event Rules', 'eventlayer' ),
			'parent_item_colon'     => __( 'Parent Event Rules:', 'eventlayer' ),
			'not_found'             => __( 'No event rules found.', 'eventlayer' ),
			'not_found_in_trash'    => __( 'No event rules found in Trash.', 'eventlayer' ),
			'featured_image'        => _x( 'Event Rule Cover Image', 'Overrides the "Featured Image" phrase', 'eventlayer' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase', 'eventlayer' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase', 'eventlayer' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase', 'eventlayer' ),
			'archives'              => _x( 'Event Rule archives', 'The post type archive label', 'eventlayer' ),
			'insert_into_item'      => _x( 'Insert into event rule', 'Overrides the "Insert into post" phrase', 'eventlayer' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this event rule', 'Overrides the "Uploaded to this post" phrase', 'eventlayer' ),
			'filter_items_list'     => _x( 'Filter event rules list', 'Screen reader text for the filter links', 'eventlayer' ),
			'items_list_navigation' => _x( 'Event rules list navigation', 'Screen reader text for the pagination', 'eventlayer' ),
			'items_list'            => _x( 'Event rules list', 'Screen reader text for the items list', 'eventlayer' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false, // We'll add it manually under EventLayer menu
			'show_in_nav_menus'  => false,
			'show_in_admin_bar'  => false,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-analytics',
			'supports'           => array( 'title' ), // Only title support
			'show_in_rest'       => true, // Enable REST API for potential future use
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Register meta fields for the event rule post type.
	 *
	 * @return void
	 */
	public function register_meta_fields() {
		// Event Type
		register_post_meta(
			self::POST_TYPE,
			'_event_type',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
				'default'      => '',
			)
		);

		// Site Location
		register_post_meta(
			self::POST_TYPE,
			'_site_location',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
				'default'      => 'all_pages',
			)
		);

		// Event Trigger Delay
		register_post_meta(
			self::POST_TYPE,
			'_trigger_delay',
			array(
				'type'         => 'number',
				'single'       => true,
				'show_in_rest' => true,
				'default'      => 0,
			)
		);

		// Stop Propagation
		register_post_meta(
			self::POST_TYPE,
			'_stop_propagation',
			array(
				'type'         => 'boolean',
				'single'       => true,
				'show_in_rest' => true,
				'default'      => false,
			)
		);

		// Trigger Elements - Parent Selector
		register_post_meta(
			self::POST_TYPE,
			'_parent_selector',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
				'default'      => '',
			)
		);

		// Trigger Elements - Multiple Toggle
		register_post_meta(
			self::POST_TYPE,
			'_multiple_toggle',
			array(
				'type'         => 'boolean',
				'single'       => true,
				'show_in_rest' => true,
				'default'      => false,
			)
		);

		// Trigger Elements - Child Selectors (serialized array)
		register_post_meta(
			self::POST_TYPE,
			'_child_selectors',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => '',
			)
		);

		// Parameters (serialized array)
		register_post_meta(
			self::POST_TYPE,
			'_parameters',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => '',
			)
		);
	}

	/**
	 * Change the title placeholder for event rules.
	 *
	 * @param string $title Current title placeholder.
	 * @return string
	 */
	public function change_title_placeholder( $title ) {
		$screen = get_current_screen();
		if ( $screen && $screen->post_type === self::POST_TYPE ) {
			$title = __( 'Enter event rule name (e.g., "Button Click Tracking")', 'eventlayer' );
		}
		return $title;
	}

	/**
	 * Add custom columns to the event rules list table.
	 *
	 * @param array $columns Current columns.
	 * @return array
	 */
	public function custom_columns( $columns ) {
		// Remove date column
		unset( $columns['date'] );

		// Add custom columns
		$columns['event_type'] = __( 'Event Type', 'eventlayer' );
		$columns['location']   = __( 'Location', 'eventlayer' );
		$columns['selector']   = __( 'Selector', 'eventlayer' );
		$columns['date']       = __( 'Date', 'eventlayer' ); // Re-add date at the end

		return $columns;
	}

	/**
	 * Display content for custom columns.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function custom_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'event_type':
				$event_type = get_post_meta( $post_id, '_event_type', true );
				echo esc_html( $event_type ?: '—' );
				break;

			case 'location':
				$location  = get_post_meta( $post_id, '_site_location', true );
				$locations = array(
					'all_pages'      => __( 'All Pages', 'eventlayer' ),
					'specific_pages' => __( 'Specific Pages', 'eventlayer' ),
					'homepage'       => __( 'Homepage Only', 'eventlayer' ),
				);
				echo esc_html( $locations[ $location ] ?? $location );
				break;

			case 'selector':
				$selector = get_post_meta( $post_id, '_parent_selector', true );
				echo esc_html( $selector ? wp_trim_words( $selector, 5 ) : '—' );
				break;
		}
	}
}
