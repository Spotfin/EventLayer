<?php
/**
 * Frontend script injector for EventLayer event rules.
 *
 * @package EventLayer
 */

namespace EventLayer\Frontend;

use EventLayer\Data\EventRuleRepository;
use EventLayer\Model\EventRule;
use EventLayer\Model\Parameter;
use EventLayer\Model\SiteLocation;

/**
 * Script injector for EventLayer frontend functionality.
 *
 * @package EventLayer\Frontend
 * @since 1.0.0
 */
class ScriptInjector {

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

		/**
		 * Filter the frontend settings object.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Settings pushed to JS as eventLayerSettings.
		 */
		$settings = apply_filters(
			'eventlayer_frontend_settings',
			array(
				'debug'             => (bool) get_option( 'eventlayer_debug_mode', 0 ),
				'autoTrackPageView' => (bool) get_option( 'eventlayer_auto_pageview', 1 ),
			)
		);

		// Localize script with settings and event rules.
		wp_localize_script( 'eventlayer-frontend', 'eventLayerSettings', $settings );

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
	 * Get all active event rules as frontend config arrays.
	 *
	 * @return array
	 */
	private function get_event_rules() {
		$rules = array();

		foreach ( $this->repository->find_published() as $rule ) {
			// Check if this rule should be active on current page and within schedule.
			$is_active    = $this->should_rule_be_active( $rule->site_location );
			$is_scheduled = $this->is_within_schedule( $rule->schedule_start, $rule->schedule_end );

			/**
			 * Filter whether a rule is active on the current request.
			 *
			 * @since 1.0.0
			 *
			 * @param bool      $active Whether the rule will be injected.
			 * @param EventRule $rule   The rule being evaluated.
			 */
			if ( ! apply_filters( 'eventlayer_rule_is_active', $is_active && $is_scheduled, $rule ) ) {
				continue;
			}

			$rules[] = $this->to_frontend_config( $rule );
		}

		/**
		 * Filter the full frontend rule config pushed to JS as eventLayerConfig.
		 *
		 * @since 1.0.0
		 *
		 * @param array $rules Array of camelCase rule config arrays.
		 */
		return apply_filters( 'eventlayer_frontend_config', $rules );
	}

	/**
	 * Map an event rule to the camelCase config array pushed to JS.
	 *
	 * @param EventRule $rule Event rule.
	 * @return array
	 */
	private function to_frontend_config( EventRule $rule ): array {
		return array(
			'id'              => $rule->id,
			'title'           => $rule->title,
			'eventType'       => $rule->event_type,
			'siteLocation'    => $rule->site_location->value,
			'triggerDelay'    => null === $rule->trigger_delay ? 0 : $rule->trigger_delay,
			'stopPropagation' => $rule->stop_propagation,
			'parentSelector'  => $rule->parent_selector,
			'multipleToggle'  => $rule->multiple_toggle,
			'childSelectors'  => $rule->child_selectors,
			'parameters'      => array_map(
				static fn ( Parameter $parameter ): array => array(
					'name'           => $parameter->name,
					'defaultValue'   => $parameter->default_value,
					'targetType'     => $parameter->target_type->value,
					'targetSelector' => $parameter->target_selector,
				),
				$rule->parameters
			),
			'start'           => $rule->schedule_start,
			'end'             => $rule->schedule_end,
		);
	}

	/**
	 * Check if a rule should be active on the current page.
	 *
	 * @param SiteLocation $site_location Site location setting.
	 * @return bool
	 */
	private function should_rule_be_active( SiteLocation $site_location ) {
		return match ( $site_location ) {
			SiteLocation::Homepage => is_front_page(),
			// Specific-pages targeting falls back to all pages until page selection meta exists.
			SiteLocation::SpecificPages, SiteLocation::AllPages => true,
		};
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
		// "Now" as a site-local timestamp (equivalent to the discouraged current_time( 'timestamp' )),
		// so it compares correctly against strtotime() of the stored site-local datetime values.
		$now = time() + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

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
