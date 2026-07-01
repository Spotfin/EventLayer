<?php
/**
 * Save handler for Event Rule post meta.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin\CPT;

/**
 * Save handler for Event Rule meta data.
 *
 * @package EventLayer\Admin\CPT
 * @since 1.0.0
 */
class SaveHandler {

	/**
	 * Initialize the save handler.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'save_post_' . EventRulePostType::POST_TYPE, array( $this, 'save_meta_data' ), 10, 2 );
	}

	/**
	 * Save meta data when an event rule is saved.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return void
	 */
	public function save_meta_data( $post_id, $post ) {
		// Verify nonce.
		$nonce = isset( $_POST['eventlayer_meta_nonce'] )
			? sanitize_text_field( wp_unslash( $_POST['eventlayer_meta_nonce'] ) )
			: '';
		if ( ! wp_verify_nonce( $nonce, 'eventlayer_save_meta' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save Event Type.
		if ( isset( $_POST['event_type'] ) ) {
			update_post_meta( $post_id, '_event_type', sanitize_text_field( wp_unslash( $_POST['event_type'] ) ) );
		}

		// Save Site Location.
		if ( isset( $_POST['site_location'] ) ) {
			$allowed_locations = array( 'all_pages', 'homepage', 'specific_pages' );
			$site_location     = sanitize_text_field( wp_unslash( $_POST['site_location'] ) );
			if ( in_array( $site_location, $allowed_locations, true ) ) {
				update_post_meta( $post_id, '_site_location', $site_location );
			}
		}

		// Save Event Trigger Delay.
		if ( isset( $_POST['trigger_delay'] ) ) {
			$trigger_delay = absint( $_POST['trigger_delay'] );
			update_post_meta( $post_id, '_trigger_delay', $trigger_delay );
		}

		// Save Stop Propagation.
		$stop_propagation = isset( $_POST['stop_propagation'] ) ? 1 : 0;
		update_post_meta( $post_id, '_stop_propagation', $stop_propagation );

		// Save Parent Selector.
		if ( isset( $_POST['parent_selector'] ) ) {
			update_post_meta(
				$post_id,
				'_parent_selector',
				sanitize_text_field( wp_unslash( $_POST['parent_selector'] ) )
			);
		}

		// Save Multiple Toggle.
		$multiple_toggle = isset( $_POST['multiple_toggle'] ) ? 1 : 0;
		update_post_meta( $post_id, '_multiple_toggle', $multiple_toggle );

		// Save Child Selectors.
		$child_selectors = array();
		if ( isset( $_POST['child_selectors'] ) && is_array( $_POST['child_selectors'] ) ) {
			$raw_child_selectors = array_map( 'sanitize_text_field', wp_unslash( $_POST['child_selectors'] ) );
			foreach ( $raw_child_selectors as $selector ) {
				if ( ! empty( $selector ) ) {
					$child_selectors[] = $selector;
				}
			}
		}
		update_post_meta( $post_id, '_child_selectors', maybe_serialize( $child_selectors ) );

		// Save Parameters.
		$parameters = array();
		if ( isset( $_POST['parameters'] ) && is_array( $_POST['parameters'] ) ) {
			$raw_parameters = map_deep( wp_unslash( $_POST['parameters'] ), 'sanitize_text_field' );
			foreach ( $raw_parameters as $index => $param ) {
				if ( empty( $param['name'] ) ) {
					continue; // Skip empty parameter names.
				}

				$parameter = array(
					'name'            => sanitize_text_field( $param['name'] ),
					'default_value'   => sanitize_text_field( $param['default_value'] ?? '' ),
					'target_type'     => sanitize_text_field( $param['target_type'] ?? 'static' ),
					'target_selector' => sanitize_text_field( $param['target_selector'] ?? '' ),
				);

				// Validate target type.
				$allowed_types = array( 'static', 'element_text', 'element_attribute', 'url_parameter' );
				if ( ! in_array( $parameter['target_type'], $allowed_types, true ) ) {
					$parameter['target_type'] = 'static';
				}

				$parameters[] = $parameter;
			}
		}
		update_post_meta( $post_id, '_parameters', maybe_serialize( $parameters ) );

		// Save Scheduling (Pro feature; values persist even if not active).
		$sd = isset( $_POST['schedule_start_date'] )
			? sanitize_text_field( wp_unslash( $_POST['schedule_start_date'] ) )
			: '';
		$st = isset( $_POST['schedule_start_time'] )
			? sanitize_text_field( wp_unslash( $_POST['schedule_start_time'] ) )
			: '';
		$ed = isset( $_POST['schedule_end_date'] )
			? sanitize_text_field( wp_unslash( $_POST['schedule_end_date'] ) )
			: '';
		$et = isset( $_POST['schedule_end_time'] )
			? sanitize_text_field( wp_unslash( $_POST['schedule_end_time'] ) )
			: '';

		$start_raw = ( $sd && $st ) ? ( $sd . 'T' . $st ) : ( $sd ?: '' );
		$end_raw   = ( $ed && $et ) ? ( $ed . 'T' . $et ) : ( $ed ?: '' );

		update_post_meta( $post_id, '_schedule_start', $start_raw );
		update_post_meta( $post_id, '_schedule_end', $end_raw );
	}
}
