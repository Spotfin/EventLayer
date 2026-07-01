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
	 * Default license validation endpoint.
	 */
	private const VALIDATION_ENDPOINT = 'https://eventlayerpro.com/wp-json/eventlayer/v1/validate';

	/**
	 * Whether pro features are enabled
	 * Set to true to disable all gating (everything free)
	 * Set to false to enable pro gating
	 *
	 * @var bool
	 */
	private static $pro_features_enabled = false; // Change to false to enable gating

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

		// Inject Pro-only parameter target types into the editor when Pro is active.
		add_filter( 'eventlayer_parameter_target_types', array( __CLASS__, 'filter_parameter_target_types' ) );
	}

	/**
	 * Filter to add Pro-only parameter target types to the dropdown when Pro is active.
	 *
	 * @param array $types Associative array slug => label.
	 * @return array
	 */
	public static function filter_parameter_target_types( $types ) {
		if ( self::has_feature( 'element_attribute' ) ) {
			$types['element_attribute'] = __( 'Element Attribute', 'eventlayer' );
		}
		if ( self::has_feature( 'url_parameter' ) ) {
			$types['url_parameter'] = __( 'URL Parameter', 'eventlayer' );
		}
		return $types;
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
			'scheduling',
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
		// Get license key from settings.
		$license_key = trim( (string) get_option( 'eventlayer_license_key', '' ) );

		// No key saved, not active.
		if ( '' === $license_key ) {
			return false;
		}

		// Temporary local rule: allow non-empty key to pass when local mode is enabled.
		// Enable via define('EVENTLAYER_DEV_LICENSE_PASS', true); in wp-config.php or filter below.
		$local_mode = apply_filters(
			'eventlayer_license_local_mode',
			defined( 'EVENTLAYER_DEV_LICENSE_PASS' ) ? (bool) EVENTLAYER_DEV_LICENSE_PASS : false,
			$license_key
		);
		if ( $local_mode ) {
			$valid = true;
			$data  = array( 'mode' => 'local' );
			update_option(
				'eventlayer_license_cache',
				array(
					'valid'      => $valid,
					'checked_at' => time(),
					'data'       => $data,
				),
				false
			);
			do_action( 'eventlayer_license_checked', $valid, $data );
			return true;
		}

		// Use cached result if recent to avoid frequent network calls.
		$cache = get_option( 'eventlayer_license_cache', array() );
		if ( is_array( $cache ) && isset( $cache['valid'], $cache['checked_at'] ) ) {
			$age = time() - (int) $cache['checked_at'];
			if ( $age < DAY_IN_SECONDS ) {
				return (bool) $cache['valid'];
			}
		}

		// Build validation request.
		$endpoint = apply_filters( 'eventlayer_license_validation_endpoint', self::VALIDATION_ENDPOINT );
		$args     = array(
			'timeout' => 8,
			'headers' => array( 'Accept' => 'application/json' ),
			'body'    => array(
				'license_key' => $license_key,
				'site_url'    => home_url(),
				'plugin'      => 'eventlayer',
				'version'     => \EventLayer\Plugin::get_instance()->get_version(),
			),
		);
		$args     = apply_filters( 'eventlayer_license_request_args', $args, $license_key );

		// Remote validation.
		$response = wp_remote_post( $endpoint, $args );

		$valid = false;
		$data  = array();

		if ( is_wp_error( $response ) ) {
			// Fall back to previous cached validity if available.
			if ( is_array( $cache ) && isset( $cache['valid'] ) ) {
				return (bool) $cache['valid'];
			}
			return false;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = (string) wp_remote_retrieve_body( $response );

		if ( $code >= 200 && $code < 300 ) {
			$json = json_decode( $body, true );
			if ( is_array( $json ) && array_key_exists( 'valid', $json ) ) {
				$valid = (bool) $json['valid'];
				$data  = $json;
			}
		}

		// Cache the result for a day.
		update_option(
			'eventlayer_license_cache',
			array(
				'valid'      => $valid,
				'checked_at' => time(),
				'data'       => $data,
			),
			false
		);

		do_action( 'eventlayer_license_checked', $valid, $data );

		return $valid;
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
