<?php
/**
 * Pro Manager
 *
 * Handles pro feature gating and licensing
 *
 * @package EventLayer
 */

namespace EventLayer\Pro;

/**
 * Pro Manager Class
 */
class ProManager {

	/**
	 * Whether pro features are enabled
	 * Set to true to disable all gating (everything free)
	 * Set to false to enable pro gating
	 *
	 * @var bool
	 */
	private static $pro_features_enabled = true; // Change to false to enable gating

	/**
	 * Whether the pro version is installed and active
	 *
	 * @var bool
	 */
	private static $pro_version_active = false;

	/**
	 * Initialize the pro manager
	 */
	public static function init() {
		// Check if pro version is active (placeholder for future)
		self::$pro_version_active = self::check_pro_license();
	}

	/**
	 * Check if pro features should be available
	 *
	 * @return bool True if pro features should be available
	 */
	public static function is_pro_active() {
		// If pro features are globally enabled, return true
		if ( self::$pro_features_enabled ) {
			return true;
		}

		// Otherwise check if pro version is active
		return self::$pro_version_active;
	}

	/**
	 * Check if a specific feature is available
	 *
	 * @param string $feature The feature to check.
	 * @return bool True if feature is available
	 */
	public static function has_feature( $feature ) {
		// If pro features are globally enabled, all features are available
		if ( self::$pro_features_enabled ) {
			return true;
		}

		// Define which features require pro
		$pro_features = array(
			'site_location',
			'trigger_delay',
			'stop_propagation',
			'child_selectors',
			'multiple_toggle',
			'element_attribute',
			'url_parameter',
			'import_export',
			'unlimited_rules',
			'advanced_selectors',
		);

		// If not a pro feature, it's always available
		if ( ! in_array( $feature, $pro_features, true ) ) {
			return true;
		}

		// Check if pro is active for pro features
		return self::is_pro_active();
	}

	/**
	 * Get the upgrade URL
	 *
	 * @return string The upgrade URL
	 */
	public static function get_upgrade_url() {
		return 'https://eventlayerpro.com/upgrade';
	}

	/**
	 * Check if we should enforce rule limits
	 *
	 * @return bool True if limits should be enforced
	 */
	public static function should_enforce_limits() {
		return ! self::$pro_features_enabled && ! self::is_pro_active();
	}

	/**
	 * Get the maximum number of rules allowed
	 *
	 * @return int Maximum rules (0 = unlimited)
	 */
	public static function get_max_rules() {
		if ( self::is_pro_active() ) {
			return 0; // Unlimited
		}

		return self::should_enforce_limits() ? 5 : 0;
	}

	/**
	 * Check current rule count
	 *
	 * @return int Current number of published event rules
	 */
	public static function get_current_rule_count() {
		$rules = get_posts(
			array(
				'post_type'      => 'event_rule',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		return count( $rules );
	}

	/**
	 * Check if user can create more rules
	 *
	 * @return bool True if user can create more rules
	 */
	public static function can_create_rule() {
		$max_rules = self::get_max_rules();

		// If unlimited (0), always allow
		if ( 0 === $max_rules ) {
			return true;
		}

		return self::get_current_rule_count() < $max_rules;
	}

	/**
	 * Render a pro feature gate
	 *
	 * @param string $feature The feature name.
	 * @param string $title The gate title.
	 * @param string $description The gate description.
	 */
	public static function render_feature_gate( $feature, $title, $description ) {
		// If feature is available, don't show gate
		if ( self::has_feature( $feature ) ) {
			return;
		}

		?>
		<div class="eventlayer-pro-gate" style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px;">
			<h4 style="margin-top: 0; color: #d63638;">🔒 <?php echo esc_html( $title ); ?></h4>
			<p style="margin-bottom: 10px;"><?php echo esc_html( $description ); ?></p>
			<a href="<?php echo esc_url( self::get_upgrade_url() ); ?>" 
			   class="button button-primary" 
			   target="_blank">
				Upgrade to EventLayer Pro
			</a>
		</div>
		<?php
	}

	/**
	 * Check pro license (placeholder for future implementation)
	 *
	 * @return bool True if pro license is valid
	 */
	private static function check_pro_license() {
		// Placeholder for future pro license checking
		// Could check for:
		// - License key in options
		// - Pro plugin activation
		// - Remote license validation
		return false;
	}

	/**
	 * Enable or disable pro features globally
	 * Useful for development and testing
	 *
	 * @param bool $enabled Whether to enable pro features.
	 */
	public static function set_pro_features_enabled( $enabled ) {
		self::$pro_features_enabled = $enabled;
	}

	/**
	 * Get pro features status
	 *
	 * @return bool Whether pro features are globally enabled
	 */
	public static function are_pro_features_enabled() {
		return self::$pro_features_enabled;
	}
}
