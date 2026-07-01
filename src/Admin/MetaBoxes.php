<?php
/**
 * Meta boxes for the Event Rule custom post type.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

/**
 * Meta boxes for Event Rule custom post type.
 *
 * @package EventLayer\Admin
 * @since 1.0.0
 */
class MetaBoxes {

	/**
	 * Initialize meta boxes.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add meta boxes to the event rule edit screen.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'eventlayer_event_settings',
			__( 'Event Settings', 'eventlayer' ),
			array( $this, 'event_settings_callback' ),
			EventRulePostType::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'eventlayer_trigger_elements',
			__( 'Trigger Elements', 'eventlayer' ),
			array( $this, 'trigger_elements_callback' ),
			EventRulePostType::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'eventlayer_parameters',
			__( 'Parameters', 'eventlayer' ),
			array( $this, 'parameters_callback' ),
			EventRulePostType::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || EventRulePostType::POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_script(
			'eventlayer-admin',
			plugin_dir_url( __FILE__ ) . '../Assets/js/admin.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		// Pass Pro feature flags to JS so UI can gate options consistently.
		wp_localize_script(
			'eventlayer-admin',
			'eventLayerAdminConfig',
			array(
				'features' => array(
					'element_attribute' => \EventLayer\Pro\ProManager::has_feature( 'element_attribute' ),
					'url_parameter'     => \EventLayer\Pro\ProManager::has_feature( 'url_parameter' ),
				),
			)
		);

		wp_enqueue_style(
			'eventlayer-admin',
			plugin_dir_url( __FILE__ ) . '../Assets/css/admin.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Event Settings meta box callback.
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function event_settings_callback( $post ) {
		// Add nonce field.
		wp_nonce_field( 'eventlayer_save_meta', 'eventlayer_meta_nonce' );

		// Get current values.
		$event_type       = get_post_meta( $post->ID, '_event_type', true );
		$site_location    = get_post_meta( $post->ID, '_site_location', true );
		$site_location    = $site_location ? $site_location : 'all_pages';
		$trigger_delay    = get_post_meta( $post->ID, '_trigger_delay', true );
		$stop_propagation = get_post_meta( $post->ID, '_stop_propagation', true );
		$start            = get_post_meta( $post->ID, '_schedule_start', true );
		$end              = get_post_meta( $post->ID, '_schedule_end', true );

		$start_date = $start ? substr( $start, 0, 10 ) : '';
		$start_time = $start ? substr( $start, 11, 5 ) : '';
		$end_date   = $end ? substr( $end, 0, 10 ) : '';
		$end_time   = $end ? substr( $end, 11, 5 ) : '';

		include __DIR__ . '/Views/metabox-event-settings.php';
	}

	/**
	 * Trigger Elements meta box callback.
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function trigger_elements_callback( $post ) {
		// Get current values.
		$parent_selector = get_post_meta( $post->ID, '_parent_selector', true );
		$multiple_toggle = get_post_meta( $post->ID, '_multiple_toggle', true );
		$child_selectors = get_post_meta( $post->ID, '_child_selectors', true );
		$child_selectors = $child_selectors ? maybe_unserialize( $child_selectors ) : array();

		$parent_selector_placeholder = __( 'e.g., .cta-button, #header-nav, [data-track]', 'eventlayer' );
		include __DIR__ . '/Views/metabox-trigger-elements.php';
	}

	/**
	 * Parameters meta box callback.
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function parameters_callback( $post ) {
		// Get current values.
		$parameters = get_post_meta( $post->ID, '_parameters', true );
		$parameters = $parameters ? maybe_unserialize( $parameters ) : array();

		$target_selector_placeholder = __( 'CSS selector or attribute name', 'eventlayer' );

		// Base target types with filter to allow Pro (and 3rd parties) to add more.
		$target_types = apply_filters(
			'eventlayer_parameter_target_types',
			array(
				'static'       => __( 'Static Value', 'eventlayer' ),
				'element_text' => __( 'Element Text', 'eventlayer' ),
			)
		);
		include __DIR__ . '/Views/metabox-parameters.php';
	}
}
