<?php
/**
 * Admin UI for free-tier rule limits (notices, disabled actions).
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

/**
 * Renders rule-limit notices and disables rule creation UI when gated.
 *
 * @package EventLayer\Admin
 * @since 1.0.0
 */
class GatingUi {

	/**
	 * Initialize the gating UI.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'maybe_show_rule_limit_notice' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'maybe_disable_add_new_button' ) );
		add_filter( 'post_row_actions', array( $this, 'maybe_limit_row_actions' ), 10, 2 );
	}

	/**
	 * Show rule limit notice on relevant admin pages.
	 *
	 * @return void
	 */
	public function maybe_show_rule_limit_notice() {
		// Only show on event rule pages.
		$screen = get_current_screen();
		if ( ! $screen || 'event_rule' !== $screen->post_type ) {
			return;
		}

		// Don't show if pro features are enabled.
		if ( ! \EventLayer\Pro\ProManager::should_enforce_limits() ) {
			return;
		}

		$current_count = \EventLayer\Pro\ProManager::get_current_rule_count();
		$max_rules     = \EventLayer\Pro\ProManager::get_max_rules();

		// Show warning when approaching limit.
		if ( $current_count >= $max_rules - 1 && $current_count < $max_rules ) {
			?>
			<div class="notice notice-warning">
				<p>
					<strong><?php esc_html_e( 'EventLayer:', 'eventlayer' ); ?></strong>
					<?php
					printf(
						/* translators: 1: maximum number of event rules, 2: number of event rules remaining. */
						esc_html__(
							'You are approaching the limit of %1$d event rules. You have %2$d rules remaining.',
							'eventlayer'
						),
						$max_rules,
						$max_rules - $current_count
					);
					?>
					<a href="<?php echo esc_url( \EventLayer\Pro\ProManager::get_upgrade_url() ); ?>" target="_blank">
						<?php esc_html_e( 'Upgrade to EventLayer Pro for unlimited rules', 'eventlayer' ); ?>
					</a>
				</p>
			</div>
			<?php
		}

		// Show error when limit reached.
		if ( $current_count >= $max_rules ) {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e( 'EventLayer:', 'eventlayer' ); ?></strong>
					<?php
					printf(
						/* translators: %d: maximum number of event rules. */
						esc_html__( 'You have reached the limit of %d event rules.', 'eventlayer' ),
						$max_rules
					);
					?>
					<a href="<?php echo esc_url( \EventLayer\Pro\ProManager::get_upgrade_url() ); ?>" target="_blank">
						<?php esc_html_e( 'Upgrade to EventLayer Pro for unlimited rules', 'eventlayer' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Maybe disable the "Add New" button when limit reached.
	 *
	 * @return void
	 */
	public function maybe_disable_add_new_button() {
		if ( ! \EventLayer\Pro\ProManager::can_create_rule() ) {
			add_action( 'admin_head', array( $this, 'hide_add_new_button' ) );
		}
	}

	/**
	 * Hide the "Add New" button with CSS.
	 *
	 * @return void
	 */
	public function hide_add_new_button() {
		$screen = get_current_screen();
		if ( $screen && 'event_rule' === $screen->post_type ) {
			?>
			<style>
				.page-title-action,
				.row-actions .inline,
				.row-actions .duplicate {
					display: none !important;
				}
			</style>
			<?php
		}
	}

	/**
	 * Maybe limit row actions when at rule limit.
	 *
	 * @param array    $actions Row actions.
	 * @param \WP_Post $post    Post object.
	 * @return array
	 */
	public function maybe_limit_row_actions( $actions, $post ) {
		if ( 'event_rule' !== $post->post_type ) {
			return $actions;
		}

		if ( ! \EventLayer\Pro\ProManager::can_create_rule() ) {
			// Remove actions that create new posts.
			unset( $actions['inline'], $actions['duplicate'] );
		}

		return $actions;
	}
}
