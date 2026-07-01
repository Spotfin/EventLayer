<?php
/**
 * Default free-tier gating provider.
 *
 * @package EventLayer
 */

namespace EventLayer\Gating;

/**
 * Free tier: everything runs without a license; Pro features are gated.
 *
 * @since 1.0.0
 */
final class FreeGatingProvider implements GatingProvider {

	/**
	 * Feature slugs that require EventLayer Pro.
	 *
	 * Note: element_attribute is deliberately free so href/data-* link
	 * tracking works out of the box.
	 */
	private const PRO_FEATURES = array(
		'site_location',
		'trigger_delay',
		'stop_propagation',
		'child_selectors',
		'multiple_toggle',
		'url_parameter',
		'import_export',
		'unlimited_rules',
		'advanced_selectors',
		'scheduling',
	);

	/**
	 * Free tier rule cap.
	 */
	private const MAX_RULES = 5;

	/**
	 * Whether a feature is available.
	 *
	 * @param string $feature Feature slug.
	 * @return bool
	 */
	public function has_feature( string $feature ): bool {
		return ! in_array( $feature, self::PRO_FEATURES, true );
	}

	/**
	 * Maximum number of published event rules.
	 *
	 * @return int
	 */
	public function get_max_rules(): int {
		return self::MAX_RULES;
	}

	/**
	 * URL of the upgrade page.
	 *
	 * @return string
	 */
	public function get_upgrade_url(): string {
		return 'https://eventlayerpro.com/upgrade';
	}
}
