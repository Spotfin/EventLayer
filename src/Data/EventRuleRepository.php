<?php
/**
 * Repository owning all event-rule meta and query access.
 *
 * @package EventLayer
 */

namespace EventLayer\Data;

use EventLayer\Admin\EventRulePostType;
use EventLayer\Model\EventRule;
use EventLayer\Model\Parameter;
use EventLayer\Model\SiteLocation;

/**
 * All reads and writes of event rule posts/meta go through this class.
 *
 * @since 1.0.0
 */
class EventRuleRepository {

	/**
	 * Find a single event rule by post ID.
	 *
	 * @param int $post_id Post ID.
	 * @return EventRule|null Null when the post does not exist.
	 */
	public function find( int $post_id ): ?EventRule {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return null;
		}

		return $this->hydrate( $post );
	}

	/**
	 * Find all published event rules with usable data.
	 *
	 * @return EventRule[]
	 */
	public function find_published(): array {
		$posts = get_posts(
			array(
				'post_type'      => EventRulePostType::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- bounded rule set; repository is the single query point.
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

		$rules = array();
		foreach ( $posts as $post ) {
			$rule = $this->hydrate( $post );

			// Skip if essential data is missing.
			if ( '' === $rule->event_type || '' === $rule->parent_selector ) {
				continue;
			}

			$rules[] = $rule;
		}

		return $rules;
	}

	/**
	 * Find the most recent event rules (published and draft), newest first.
	 *
	 * @param int $limit Maximum number of rules.
	 * @return EventRule[]
	 */
	public function find_recent( int $limit ): array {
		$posts = get_posts(
			array(
				'post_type'      => EventRulePostType::POST_TYPE,
				'posts_per_page' => $limit,
				'post_status'    => array( 'publish', 'draft' ),
			)
		);

		return array_map( fn ( object $post ): EventRule => $this->hydrate( $post ), $posts );
	}

	/**
	 * Persist an event rule's meta.
	 *
	 * @param EventRule $rule    The rule to save.
	 * @param int       $post_id Target post ID.
	 * @return void
	 */
	public function save( EventRule $rule, int $post_id ): void {
		update_post_meta( $post_id, '_event_type', $rule->event_type );
		update_post_meta( $post_id, '_site_location', $rule->site_location->value );

		// Null means "never saved"; keep it that way rather than writing 0.
		if ( null !== $rule->trigger_delay ) {
			update_post_meta( $post_id, '_trigger_delay', $rule->trigger_delay );
		}

		update_post_meta( $post_id, '_stop_propagation', $rule->stop_propagation ? 1 : 0 );
		update_post_meta( $post_id, '_parent_selector', $rule->parent_selector );
		update_post_meta( $post_id, '_multiple_toggle', $rule->multiple_toggle ? 1 : 0 );
		update_post_meta( $post_id, '_child_selectors', maybe_serialize( array_values( $rule->child_selectors ) ) );
		update_post_meta(
			$post_id,
			'_parameters',
			maybe_serialize(
				array_map(
					static fn ( Parameter $parameter ): array => $parameter->to_meta(),
					array_values( $rule->parameters )
				)
			)
		);
		update_post_meta( $post_id, '_schedule_start', $rule->schedule_start );
		update_post_meta( $post_id, '_schedule_end', $rule->schedule_end );
	}

	/**
	 * Build an EventRule from a post and its meta.
	 *
	 * @param \WP_Post|object $post Post object.
	 * @return EventRule
	 */
	private function hydrate( object $post ): EventRule {
		$trigger_delay   = get_post_meta( $post->ID, '_trigger_delay', true );
		$child_selectors = maybe_unserialize( get_post_meta( $post->ID, '_child_selectors', true ) );
		$raw_parameters  = maybe_unserialize( get_post_meta( $post->ID, '_parameters', true ) );

		$parameters = array();
		if ( is_array( $raw_parameters ) ) {
			foreach ( $raw_parameters as $row ) {
				$parameter = Parameter::from_meta( $row );
				if ( null !== $parameter ) {
					$parameters[] = $parameter;
				}
			}
		}

		return new EventRule(
			id: (int) $post->ID,
			title: sanitize_text_field( $post->post_title ),
			status: (string) ( $post->post_status ?? '' ),
			event_type: sanitize_text_field( get_post_meta( $post->ID, '_event_type', true ) ),
			site_location: SiteLocation::from_meta( (string) get_post_meta( $post->ID, '_site_location', true ) ),
			trigger_delay: '' === $trigger_delay ? null : absint( $trigger_delay ),
			stop_propagation: (bool) get_post_meta( $post->ID, '_stop_propagation', true ),
			parent_selector: sanitize_text_field( get_post_meta( $post->ID, '_parent_selector', true ) ),
			multiple_toggle: (bool) get_post_meta( $post->ID, '_multiple_toggle', true ),
			child_selectors: is_array( $child_selectors )
				? array_map( 'sanitize_text_field', array_values( $child_selectors ) )
				: array(),
			parameters: $parameters,
			schedule_start: (string) get_post_meta( $post->ID, '_schedule_start', true ),
			schedule_end: (string) get_post_meta( $post->ID, '_schedule_end', true ),
		);
	}
}
