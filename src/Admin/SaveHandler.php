<?php
/**
 * Save handler for Event Rule post meta.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

use EventLayer\Data\EventRuleRepository;
use EventLayer\Model\EventRule;
use EventLayer\Model\Parameter;
use EventLayer\Model\ParameterTargetType;
use EventLayer\Model\SiteLocation;

/**
 * Save handler for Event Rule meta data.
 *
 * @package EventLayer\Admin
 * @since 1.0.0
 */
class SaveHandler {

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
	 * Initialize the save handler.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'save_post_' . EventRulePostType::POST_TYPE, array( $this, 'save_meta_data' ) );
	}

	/**
	 * Save meta data when an event rule is saved.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_meta_data( $post_id ) {
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

		$existing = $this->repository->find( $post_id );
		if ( null === $existing ) {
			return;
		}

		$rule = new EventRule(
			id: $post_id,
			title: $existing->title,
			status: $existing->status,
			event_type: isset( $_POST['event_type'] )
				? sanitize_text_field( wp_unslash( $_POST['event_type'] ) )
				: $existing->event_type,
			site_location: $this->posted_site_location() ?? $existing->site_location,
			trigger_delay: isset( $_POST['trigger_delay'] )
				? absint( $_POST['trigger_delay'] )
				: $existing->trigger_delay,
			stop_propagation: isset( $_POST['stop_propagation'] ),
			parent_selector: isset( $_POST['parent_selector'] )
				? sanitize_text_field( wp_unslash( $_POST['parent_selector'] ) )
				: $existing->parent_selector,
			multiple_toggle: isset( $_POST['multiple_toggle'] ),
			child_selectors: $this->posted_child_selectors(),
			parameters: $this->posted_parameters(),
			schedule_start: $this->posted_schedule( 'schedule_start_date', 'schedule_start_time' ),
			schedule_end: $this->posted_schedule( 'schedule_end_date', 'schedule_end_time' ),
		);

		$this->repository->save( $rule, $post_id );
	}

	/**
	 * Read a valid site location from the request, or null.
	 *
	 * @return SiteLocation|null
	 */
	private function posted_site_location(): ?SiteLocation {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		if ( ! isset( $_POST['site_location'] ) ) {
			return null;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		return SiteLocation::tryFrom( sanitize_text_field( wp_unslash( $_POST['site_location'] ) ) );
	}

	/**
	 * Read non-empty child selectors from the request.
	 *
	 * @return string[]
	 */
	private function posted_child_selectors(): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		if ( ! isset( $_POST['child_selectors'] ) || ! is_array( $_POST['child_selectors'] ) ) {
			return array();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		$raw = array_map( 'sanitize_text_field', wp_unslash( $_POST['child_selectors'] ) );

		$selectors = array();
		foreach ( $raw as $selector ) {
			if ( ! empty( $selector ) ) {
				$selectors[] = $selector;
			}
		}

		return $selectors;
	}

	/**
	 * Read parameters from the request, skipping rows without a name.
	 *
	 * @return Parameter[]
	 */
	private function posted_parameters(): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		if ( ! isset( $_POST['parameters'] ) || ! is_array( $_POST['parameters'] ) ) {
			return array();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		$raw = map_deep( wp_unslash( $_POST['parameters'] ), 'sanitize_text_field' );

		$parameters = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) || empty( $row['name'] ) ) {
				continue; // Skip empty parameter names.
			}

			$parameters[] = new Parameter(
				sanitize_text_field( $row['name'] ),
				sanitize_text_field( $row['default_value'] ?? '' ),
				ParameterTargetType::from_meta( sanitize_text_field( $row['target_type'] ?? 'static' ) ),
				sanitize_text_field( $row['target_selector'] ?? '' ),
			);
		}

		return $parameters;
	}

	/**
	 * Combine posted date and time fields into a datetime-local string.
	 *
	 * @param string $date_key Request key for the date part.
	 * @param string $time_key Request key for the time part.
	 * @return string YYYY-MM-DDTHH:MM, date-only, or ''.
	 */
	private function posted_schedule( string $date_key, string $time_key ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		$date = isset( $_POST[ $date_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $date_key ] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_meta_data().
		$time = isset( $_POST[ $time_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $time_key ] ) ) : '';

		if ( $date && $time ) {
			return $date . 'T' . $time;
		}

		return $date ? $date : '';
	}
}
