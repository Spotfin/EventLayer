<?php
/**
 * Admin UI for free-tier rule limits (notices, disabled actions).
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

use EventLayer\Data\EventRuleRepository;
use EventLayer\Gating\Gating;

/**
 * Renders rule-limit notices and disables rule creation UI when gated.
 *
 * @since 1.0.0
 */
class GatingUi {

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
	 * Initialize the gating UI.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'maybe_show_rule_limit_notice' ) );
		add_action( 'admin_head', array( $this, 'maybe_hide_add_new_button' ) );
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
		if ( ! $screen || EventRulePostType::POST_TYPE !== $screen->post_type ) {
			return;
		}

		// No notice when rules are unlimited.
		$max_rules = Gating::provider()->get_max_rules();
		if ( 0 === $max_rules ) {
			return;
		}

		$current_count = $this->repository->count_published();

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
					<a href="<?php echo esc_url( Gating::provider()->get_upgrade_url() ); ?>"
						target="_blank" rel="noopener noreferrer">
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
					<a href="<?php echo esc_url( Gating::provider()->get_upgrade_url() ); ?>"
						target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Upgrade to EventLayer Pro for unlimited rules', 'eventlayer' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Hide the "Add New" button with CSS on event rule screens when the
	 * limit is reached. Runs on admin_head so it covers the list and edit
	 * screens (the old wp_dashboard_setup hook only fired on the Dashboard).
	 *
	 * @return void
	 */
	public function maybe_hide_add_new_button() {
		$screen = get_current_screen();
		if ( ! $screen || EventRulePostType::POST_TYPE !== $screen->post_type ) {
			return;
		}

		if ( $this->can_create_rule() ) {
			return;
		}

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

	/**
	 * Maybe limit row actions when at rule limit.
	 *
	 * @param array    $actions Row actions.
	 * @param \WP_Post $post    Post object.
	 * @return array
	 */
	public function maybe_limit_row_actions( $actions, $post ) {
		if ( EventRulePostType::POST_TYPE !== $post->post_type ) {
			return $actions;
		}

		if ( ! $this->can_create_rule() ) {
			// Remove actions that create new posts.
			unset( $actions['inline'], $actions['duplicate'] );
		}

		return $actions;
	}

	/**
	 * Whether another rule can be created under the current limit.
	 *
	 * @return bool
	 */
	private function can_create_rule(): bool {
		$max_rules = Gating::provider()->get_max_rules();

		// If unlimited (0), always allow.
		if ( 0 === $max_rules ) {
			return true;
		}

		return $this->repository->count_published() < $max_rules;
	}
}
