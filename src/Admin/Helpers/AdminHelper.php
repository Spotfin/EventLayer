<?php
/**
 * Admin helper utilities for EventLayer.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin\Helpers;

/**
 * Helper functions for admin functionality.
 *
 * @package EventLayer\Admin\Helpers
 * @since 1.0.0
 */
class AdminHelper {

	/**
	 * Render a WordPress admin notice.
	 *
	 * @param string $message The notice message.
	 * @param string $type The notice type (success, error, warning, info).
	 * @param bool   $dismissible Whether the notice is dismissible.
	 * @return void
	 */
	public static function render_notice( $message, $type = 'info', $dismissible = true ) {
		$classes = array( 'notice', 'notice-' . $type );

		if ( $dismissible ) {
			$classes[] = 'is-dismissible';
		}

		printf(
			'<div class="%s"><p>%s</p></div>',
			esc_attr( implode( ' ', $classes ) ),
			esc_html( $message )
		);
	}

	/**
	 * Check if current user can manage EventLayer.
	 *
	 * @return bool
	 */
	public static function current_user_can_manage() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get EventLayer admin page URL.
	 *
	 * @param string $page The page slug.
	 * @param array  $args Additional URL arguments.
	 * @return string
	 */
	public static function get_admin_url( $page = 'eventlayer', $args = array() ) {
		$url = admin_url( 'admin.php?page=' . $page );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}
}
