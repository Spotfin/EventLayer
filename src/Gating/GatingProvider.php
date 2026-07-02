<?php
/**
 * Gating provider contract.
 *
 * @package EventLayer
 */

namespace EventLayer\Gating;

/**
 * Decides which features are available and what limits apply.
 *
 * The default implementation is FreeGatingProvider. Add-ons (EventLayer Pro)
 * replace it via the `eventlayer_gating_provider` filter.
 *
 * @since 1.0.0
 */
interface GatingProvider {

	/**
	 * Whether a feature is available.
	 *
	 * @param string $feature Feature slug (e.g. 'trigger_delay').
	 * @return bool
	 */
	public function has_feature( string $feature ): bool;

	/**
	 * Maximum number of published event rules.
	 *
	 * @return int 0 means unlimited.
	 */
	public function get_max_rules(): int;

	/**
	 * URL of the upgrade page shown in gate UI.
	 *
	 * @return string
	 */
	public function get_upgrade_url(): string;
}
