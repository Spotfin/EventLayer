<?php
/**
 * Meta boxes for the Event Rule custom post type.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

use EventLayer\Data\EventRuleRepository;
use EventLayer\Model\Parameter;

/**
 * Meta boxes for Event Rule custom post type.
 *
 * @package EventLayer\Admin
 * @since 1.0.0
 */
class MetaBoxes {

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
			\EventLayer\Plugin::get_instance()->get_plugin_url( 'src/Assets/js/admin.js' ),
			array( 'jquery' ),
			\EventLayer\Plugin::VERSION,
			true
		);

		wp_enqueue_style(
			'eventlayer-admin',
			\EventLayer\Plugin::get_instance()->get_plugin_url( 'src/Assets/css/admin.css' ),
			array(),
			\EventLayer\Plugin::VERSION
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
		$rule             = $this->repository->find( $post->ID );
		$event_type       = $rule->event_type;
		$site_location    = $rule->site_location->value;
		$trigger_delay    = $rule->trigger_delay;
		$stop_propagation = $rule->stop_propagation ? 1 : 0;

		$start_date = $rule->schedule_start ? substr( $rule->schedule_start, 0, 10 ) : '';
		$start_time = $rule->schedule_start ? substr( $rule->schedule_start, 11, 5 ) : '';
		$end_date   = $rule->schedule_end ? substr( $rule->schedule_end, 0, 10 ) : '';
		$end_time   = $rule->schedule_end ? substr( $rule->schedule_end, 11, 5 ) : '';

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
		$rule            = $this->repository->find( $post->ID );
		$parent_selector = $rule->parent_selector;
		$multiple_toggle = $rule->multiple_toggle ? 1 : 0;
		$child_selectors = $rule->child_selectors;

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
		// Get current values (legacy array shape expected by the view).
		$rule       = $this->repository->find( $post->ID );
		$parameters = array_map(
			static fn ( Parameter $parameter ): array => $parameter->to_meta(),
			$rule->parameters
		);

		$target_selector_placeholder = __( 'CSS selector or attribute name', 'eventlayer' );

		// Base target types with filter to allow Pro (and 3rd parties) to add more.
		$target_types = apply_filters(
			'eventlayer_parameter_target_types',
			array(
				'static'            => __( 'Static Value', 'eventlayer' ),
				'element_text'      => __( 'Element Text', 'eventlayer' ),
				'element_attribute' => __( 'Element Attribute', 'eventlayer' ),
			)
		);
		include __DIR__ . '/Views/metabox-parameters.php';
	}
}
