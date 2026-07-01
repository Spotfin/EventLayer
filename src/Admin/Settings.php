<?php
/**
 * Plugin settings registration and field rendering.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

/**
 * Registers EventLayer settings, sections, and fields.
 *
 * @package EventLayer\Admin
 * @since 1.0.0
 */
class Settings {

	/**
	 * Initialize the settings.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		// Register settings group.
		register_setting(
			'eventlayer_settings',
			'eventlayer_debug_mode',
			array(
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
			)
		);

		register_setting(
			'eventlayer_settings',
			'eventlayer_auto_pageview',
			array(
				'type'              => 'boolean',
				'default'           => true,
				'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
			)
		);

		// Add settings section.
		add_settings_section(
			'eventlayer_general_settings',
			__( 'General Settings', 'eventlayer' ),
			null,
			'eventlayer_settings'
		);

		// Add settings fields.
		add_settings_field(
			'eventlayer_debug_mode',
			__( 'Debug Mode', 'eventlayer' ),
			array( $this, 'debug_mode_field_callback' ),
			'eventlayer_settings',
			'eventlayer_general_settings'
		);

		add_settings_field(
			'eventlayer_auto_pageview',
			__( 'Auto Page View Tracking', 'eventlayer' ),
			array( $this, 'auto_pageview_field_callback' ),
			'eventlayer_settings',
			'eventlayer_general_settings'
		);
	}

	/**
	 * Sanitize checkbox input.
	 *
	 * @param mixed $input Input value.
	 * @return int
	 */
	public function sanitize_checkbox( $input ) {
		return ! empty( $input ) ? 1 : 0;
	}

	/**
	 * Debug mode field callback.
	 *
	 * @return void
	 */
	public function debug_mode_field_callback() {
		$value = get_option( 'eventlayer_debug_mode', 0 );
		echo '<label>';
		echo '<input type="checkbox" name="eventlayer_debug_mode" value="1" ' . checked( $value, 1, false ) . ' />';
		echo ' ' . esc_html__( 'Enable debug logging in browser console', 'eventlayer' );
		echo '</label>';
		echo '<p class="description">'
			. esc_html__(
				'When enabled, EventLayer will log events to the browser console for debugging.',
				'eventlayer'
			)
			. '</p>';
	}

	/**
	 * Auto page view field callback.
	 *
	 * @return void
	 */
	public function auto_pageview_field_callback() {
		$value = get_option( 'eventlayer_auto_pageview', 1 );
		echo '<label>';
		echo '<input type="checkbox" name="eventlayer_auto_pageview" value="1" ' . checked( $value, 1, false ) . ' />';
		echo ' ' . esc_html__( 'Automatically track page views', 'eventlayer' );
		echo '</label>';
		echo '<p class="description">'
			. esc_html__( 'Automatically push page_view events to the dataLayer on page load.', 'eventlayer' )
			. '</p>';
	}
}
