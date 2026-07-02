<?php
/**
 * Event rule model.
 *
 * @package EventLayer
 */

namespace EventLayer\Model;

/**
 * Immutable representation of a single event rule.
 *
 * @since 1.0.0
 */
final class EventRule {

	/**
	 * Constructor.
	 *
	 * @param int          $id               Post ID (0 for unsaved).
	 * @param string       $title            Rule title.
	 * @param string       $status           Post status ('' when unknown).
	 * @param string       $event_type       GA4 event name.
	 * @param SiteLocation $site_location    Where the rule is active.
	 * @param int          $trigger_delay    Delay in ms (registered meta default is 0).
	 * @param bool         $stop_propagation Whether to stop event propagation.
	 * @param string       $parent_selector  CSS selector triggering the event.
	 * @param bool         $multiple_toggle  Track multiple selector matches.
	 * @param string[]     $child_selectors  Optional child selectors.
	 * @param Parameter[]  $parameters       DataLayer parameters.
	 * @param string       $schedule_start   Start (YYYY-MM-DDTHH:MM, site tz) or ''.
	 * @param string       $schedule_end     End (YYYY-MM-DDTHH:MM, site tz) or ''.
	 */
	public function __construct(
		public readonly int $id,
		public readonly string $title,
		public readonly string $status,
		public readonly string $event_type,
		public readonly SiteLocation $site_location,
		public readonly int $trigger_delay,
		public readonly bool $stop_propagation,
		public readonly string $parent_selector,
		public readonly bool $multiple_toggle,
		public readonly array $child_selectors,
		public readonly array $parameters,
		public readonly string $schedule_start,
		public readonly string $schedule_end,
	) {
	}
}
