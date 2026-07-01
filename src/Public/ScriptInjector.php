<?php
/**
 * Frontend script injector for EventLayer event rules.
 *
 * @package EventLayer
 */

namespace EventLayer\Public;

use EventLayer\Admin\CPT\EventRulePostType;

/**
 * Script injector for EventLayer frontend functionality.
 *
 * @package EventLayer\Public
 * @since 1.0.0
 */
class ScriptInjector {

	/**
	 * Initialize the script injector.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and localize event rules.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Enqueue frontend script.
		wp_enqueue_script(
			'eventlayer-frontend',
			plugin_dir_url( __FILE__ ) . '../Assets/js/frontend.js',
			array(),
			'1.0.0',
			true
		);

		// Get all published event rules.
		$event_rules = $this->get_event_rules();

		// Localize script with settings and event rules.
		wp_localize_script(
			'eventlayer-frontend',
			'eventLayerSettings',
			array(
				'debug'             => (bool) get_option( 'eventlayer_debug_mode', 0 ),
				'autoTrackPageView' => (bool) get_option( 'eventlayer_auto_pageview', 1 ),
			)
		);

		wp_localize_script( 'eventlayer-frontend', 'eventLayerConfig', $event_rules );

		// Enqueue frontend styles.
		wp_enqueue_style(
			'eventlayer-frontend',
			plugin_dir_url( __FILE__ ) . '../Assets/css/frontend.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Get all published event rules with their meta data.
	 *
	 * @return array
	 */
	private function get_event_rules() {
		$rules = array();

		// Query published event rules.
		$posts = get_posts(
			array(
				'post_type'      => EventRulePostType::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_event_type',
						'value'   => '',
						'compare' => '!=',
					),
					array(
						'key'     => '_parent_selector',
						'value'   => '',
						'compare' => '!=',
					),
				),
			)
		);

		foreach ( $posts as $post ) {
			// Get all meta data.
			$event_type       = get_post_meta( $post->ID, '_event_type', true );
			$site_location    = get_post_meta( $post->ID, '_site_location', true );
			$trigger_delay    = absint( get_post_meta( $post->ID, '_trigger_delay', true ) );
			$stop_propagation = (bool) get_post_meta( $post->ID, '_stop_propagation', true );
			$parent_selector  = get_post_meta( $post->ID, '_parent_selector', true );
			$multiple_toggle  = (bool) get_post_meta( $post->ID, '_multiple_toggle', true );
			$child_selectors  = get_post_meta( $post->ID, '_child_selectors', true );
			$parameters       = get_post_meta( $post->ID, '_parameters', true );
			$schedule_start   = get_post_meta( $post->ID, '_schedule_start', true );
			$schedule_end     = get_post_meta( $post->ID, '_schedule_end', true );

			// Skip if essential data is missing.
			if ( empty( $event_type ) || empty( $parent_selector ) ) {
				continue;
			}

			// Check if this rule should be active on current page and within schedule.
			if ( ! $this->should_rule_be_active( $site_location ) || ! $this->is_within_schedule( $schedule_start, $schedule_end ) ) {
				continue;
			}

			// Unserialize arrays.
			$child_selectors = $child_selectors ? maybe_unserialize( $child_selectors ) : array();
			$parameters      = $parameters ? maybe_unserialize( $parameters ) : array();

			// Sanitize and prepare the rule.
			$rule = array(
				'id'              => $post->ID,
				'title'           => sanitize_text_field( $post->post_title ),
				'eventType'       => sanitize_text_field( $event_type ),
				'siteLocation'    => sanitize_text_field( $site_location ),
				'triggerDelay'    => $trigger_delay,
				'stopPropagation' => $stop_propagation,
				'parentSelector'  => sanitize_text_field( $parent_selector ),
				'multipleToggle'  => $multiple_toggle,
				'childSelectors'  => array_map( 'sanitize_text_field', $child_selectors ),
				'parameters'      => $this->sanitize_parameters( $parameters ),
				'start'           => $schedule_start,
				'end'             => $schedule_end,
			);

			$rules[] = $rule;
		}

		return $rules;
	}

	/**
	 * Check if a rule should be active on the current page.
	 *
	 * @param string $site_location Site location setting.
	 * @return bool
	 */
	private function should_rule_be_active( $site_location ) {
		switch ( $site_location ) {
			case 'homepage':
				return is_front_page();

			case 'specific_pages':
				// For now, treat as all pages. Later we can add page selection meta.
				return true;

			case 'all_pages':
			default:
				return true;
		}
	}

	/**
	 * Sanitize parameters array.
	 *
	 * @param array $parameters Raw parameters array.
	 * @return array
	 */
	private function sanitize_parameters( $parameters ) {
		if ( ! is_array( $parameters ) ) {
			return array();
		}

		$sanitized = array();
		foreach ( $parameters as $param ) {
			if ( ! is_array( $param ) || empty( $param['name'] ) ) {
				continue;
			}

			$sanitized[] = array(
				'name'           => sanitize_text_field( $param['name'] ),
				'defaultValue'   => sanitize_text_field( $param['default_value'] ?? '' ),
				'targetType'     => sanitize_text_field( $param['target_type'] ?? 'static' ),
				'targetSelector' => sanitize_text_field( $param['target_selector'] ?? '' ),
			);
		}

		return $sanitized;
	}

	/**
	 * Check if current time is within schedule window.
	 * Accepts HTML5 datetime-local values (YYYY-MM-DDTHH:MM) in site timezone.
	 *
	 * @param string $start Start datetime-local value.
	 * @param string $end   End datetime-local value.
	 * @return bool
	 */
	private function is_within_schedule( $start, $end ) {
		$now = current_time( 'timestamp' ); // site timezone.

		$start_ok = true;
		$end_ok   = true;

		if ( ! empty( $start ) ) {
			$start_ts = strtotime( str_replace( 'T', ' ', $start ) );
			if ( $start_ts && $now < $start_ts ) {
				$start_ok = false;
			}
		}

		if ( ! empty( $end ) ) {
			$end_ts = strtotime( str_replace( 'T', ' ', $end ) );
			if ( $end_ts && $now > $end_ts ) {
				$end_ok = false;
			}
		}

		return ( $start_ok && $end_ok );
	}
}
