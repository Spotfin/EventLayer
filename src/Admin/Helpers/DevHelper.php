<?php
/**
 * Development Helper
 *
 * Helper functions for development and testing
 *
 * @package EventLayer
 */

namespace EventLayer\Admin\Helpers;

/**
 * Development Helper Class
 */
class DevHelper {

	/**
	 * Initialize development helper
	 */
	public static function init() {
		// Only add development features in development environment.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'add_admin_bar_menu' ), 100 );
			add_action( 'wp_ajax_eventlayer_toggle_pro', array( __CLASS__, 'toggle_pro_features' ) );
		}
	}

	/**
	 * Add EventLayer Pro toggle to admin bar
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar object.
	 */
	public static function add_admin_bar_menu( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$pro_status = \EventLayer\Pro\ProManager::are_pro_features_enabled() ? 'Enabled' : 'Disabled';
		$action     = \EventLayer\Pro\ProManager::are_pro_features_enabled() ? 'disable' : 'enable';

		$wp_admin_bar->add_node(
			array(
				'id'    => 'eventlayer-pro-toggle',
				'title' => sprintf( 'EventLayer Pro: %s', $pro_status ),
				'href'  => wp_nonce_url(
					admin_url( 'admin-ajax.php?action=eventlayer_toggle_pro&toggle=' . $action ),
					'eventlayer_toggle_pro'
				),
			)
		);
	}

	/**
	 * Toggle pro features via AJAX
	 */
	public static function toggle_pro_features() {
		check_ajax_referer( 'eventlayer_toggle_pro' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}

		$toggle = sanitize_text_field( $_GET['toggle'] ?? '' );

		if ( 'enable' === $toggle ) {
			\EventLayer\Pro\ProManager::set_pro_features_enabled( true );
			$message = 'Pro features enabled (all features unlocked)';
		} elseif ( 'disable' === $toggle ) {
			\EventLayer\Pro\ProManager::set_pro_features_enabled( false );
			$message = 'Pro features disabled (gating enabled)';
		} else {
			wp_die( 'Invalid action' );
		}

		// Redirect back with message.
		$redirect_url = add_query_arg(
			array(
				'eventlayer_message' => urlencode( $message ),
			),
			wp_get_referer() ?: admin_url()
		);

		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Show development notices
	 */
	public static function show_dev_notices() {
		if ( isset( $_GET['eventlayer_message'] ) ) {
			$message = sanitize_text_field( $_GET['eventlayer_message'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p><strong>EventLayer Dev:</strong> <?php echo esc_html( $message ); ?></p>
			</div>
			<?php
		}
	}
}
