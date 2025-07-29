<?php

namespace EventLayer\Admin\CPT;

/**
 * Meta boxes for Event Rule custom post type.
 *
 * @package EventLayer\Admin\CPT
 * @since 1.0.0
 */
class MetaBoxes {

	/**
	 * Initialize meta boxes.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add meta boxes to the event rule edit screen.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'eventlayer_event_settings',
			__( 'Event Settings', 'eventlayer' ),
			array( $this, 'event_settings_callback' ),
			EventRulePostType::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'eventlayer_trigger_elements',
			__( 'Trigger Elements', 'eventlayer' ),
			array( $this, 'trigger_elements_callback' ),
			EventRulePostType::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'eventlayer_parameters',
			__( 'Parameters', 'eventlayer' ),
			array( $this, 'parameters_callback' ),
			EventRulePostType::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== EventRulePostType::POST_TYPE ) {
			return;
		}

		wp_enqueue_script(
			'eventlayer-admin',
			plugin_dir_url( __FILE__ ) . '../../Assets/js/admin.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_enqueue_style(
			'eventlayer-admin',
			plugin_dir_url( __FILE__ ) . '../../Assets/css/admin.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Event Settings meta box callback.
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function event_settings_callback( $post ) {
		// Add nonce field
		wp_nonce_field( 'eventlayer_save_meta', 'eventlayer_meta_nonce' );

		// Get current values
		$event_type       = get_post_meta( $post->ID, '_event_type', true );
		$site_location    = get_post_meta( $post->ID, '_site_location', true ) ?: 'all_pages';
		$trigger_delay    = get_post_meta( $post->ID, '_trigger_delay', true );
		$stop_propagation = get_post_meta( $post->ID, '_stop_propagation', true );
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="event_type"><?php esc_html_e( 'Event Type', 'eventlayer' ); ?></label>
				</th>
				<td>
					<input type="text" 
							id="event_type" 
							name="event_type" 
							value="<?php echo esc_attr( $event_type ); ?>" 
							class="regular-text" 
							placeholder="<?php esc_attr_e( 'e.g., button_click, form_submit', 'eventlayer' ); ?>" />
					<p class="description">
						<?php esc_html_e( 'The GA4 event name that will be sent to the dataLayer.', 'eventlayer' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Site Location', 'eventlayer' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="radio" 
									name="site_location" 
									value="all_pages" 
									<?php checked( $site_location, 'all_pages' ); ?> />
							<?php esc_html_e( 'All Pages', 'eventlayer' ); ?>
						</label><br>
						<label>
							<input type="radio" 
									name="site_location" 
									value="homepage" 
									<?php checked( $site_location, 'homepage' ); ?> />
							<?php esc_html_e( 'Homepage Only', 'eventlayer' ); ?>
						</label><br>
						<label>
							<input type="radio" 
									name="site_location" 
									value="specific_pages" 
									<?php checked( $site_location, 'specific_pages' ); ?> />
							<?php esc_html_e( 'Specific Pages', 'eventlayer' ); ?>
						</label>
					</fieldset>
					<p class="description">
						<?php esc_html_e( 'Choose where this event rule should be active.', 'eventlayer' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="trigger_delay"><?php esc_html_e( 'Event Trigger Delay', 'eventlayer' ); ?></label>
				</th>
				<td>
					<input type="number" 
							id="trigger_delay" 
							name="trigger_delay" 
							value="<?php echo esc_attr( $trigger_delay ); ?>" 
							min="0" 
							step="100" 
							class="small-text" /> 
					<span><?php esc_html_e( 'milliseconds', 'eventlayer' ); ?></span>
					<p class="description">
						<?php esc_html_e( 'Delay before the event is triggered (0 = immediate).', 'eventlayer' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Options', 'eventlayer' ); ?></th>
				<td>
					<label>
						<input type="checkbox" 
								name="stop_propagation" 
								value="1" 
								<?php checked( $stop_propagation, 1 ); ?> />
						<?php esc_html_e( 'Stop Propagation', 'eventlayer' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'Prevent the event from bubbling up to parent elements.', 'eventlayer' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Trigger Elements meta box callback.
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function trigger_elements_callback( $post ) {
		// Get current values
		$parent_selector = get_post_meta( $post->ID, '_parent_selector', true );
		$multiple_toggle = get_post_meta( $post->ID, '_multiple_toggle', true );
		$child_selectors = get_post_meta( $post->ID, '_child_selectors', true );
		$child_selectors = $child_selectors ? maybe_unserialize( $child_selectors ) : array();
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="parent_selector"><?php esc_html_e( 'Parent Selector', 'eventlayer' ); ?></label>
				</th>
				<td>
					<input type="text" 
							id="parent_selector" 
							name="parent_selector" 
							value="<?php echo esc_attr( $parent_selector ); ?>" 
							class="regular-text" 
							placeholder="<?php esc_attr_e( 'e.g., .cta-button, #header-nav, [data-track]', 'eventlayer' ); ?>" />
					<p class="description">
						<?php esc_html_e( 'CSS selector for the element(s) that should trigger this event.', 'eventlayer' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Multiple Elements', 'eventlayer' ); ?></th>
				<td>
					<label>
						<input type="checkbox" 
								id="multiple_toggle" 
								name="multiple_toggle" 
								value="1" 
								<?php checked( $multiple_toggle, 1 ); ?> />
						<?php esc_html_e( 'Track multiple instances of this selector', 'eventlayer' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'Enable if the selector matches multiple elements on the page.', 'eventlayer' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'Child Selectors', 'eventlayer' ); ?></label>
				</th>
				<td>
					<div id="child-selectors-container">
						<?php if ( ! empty( $child_selectors ) ) : ?>
							<?php foreach ( $child_selectors as $index => $selector ) : ?>
								<div class="child-selector-row" style="margin-bottom: 10px;">
									<input type="text" 
											name="child_selectors[]" 
											value="<?php echo esc_attr( $selector ); ?>" 
											placeholder="<?php esc_attr_e( 'Child selector', 'eventlayer' ); ?>" 
											class="regular-text" />
									<button type="button" class="button remove-child-selector">
										<?php esc_html_e( 'Remove', 'eventlayer' ); ?>
									</button>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<div class="child-selector-row" style="margin-bottom: 10px;">
								<input type="text" 
										name="child_selectors[]" 
										value="" 
										placeholder="<?php esc_attr_e( 'Child selector (optional)', 'eventlayer' ); ?>" 
										class="regular-text" />
								<button type="button" class="button remove-child-selector">
									<?php esc_html_e( 'Remove', 'eventlayer' ); ?>
								</button>
							</div>
						<?php endif; ?>
					</div>
					<button type="button" id="add-child-selector" class="button">
						<?php esc_html_e( 'Add Child Selector', 'eventlayer' ); ?>
					</button>
					<p class="description">
						<?php esc_html_e( 'Optional: Additional selectors for more specific targeting.', 'eventlayer' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Parameters meta box callback.
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function parameters_callback( $post ) {
		// Get current values
		$parameters = get_post_meta( $post->ID, '_parameters', true );
		$parameters = $parameters ? maybe_unserialize( $parameters ) : array();
		?>
		<div id="parameters-container">
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Parameter Name', 'eventlayer' ); ?></th>
						<th><?php esc_html_e( 'Default Value', 'eventlayer' ); ?></th>
						<th><?php esc_html_e( 'Target Type', 'eventlayer' ); ?></th>
						<th><?php esc_html_e( 'Target Selector', 'eventlayer' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'eventlayer' ); ?></th>
					</tr>
				</thead>
				<tbody id="parameters-tbody">
					<?php if ( ! empty( $parameters ) ) : ?>
						<?php foreach ( $parameters as $index => $param ) : ?>
							<tr class="parameter-row">
								<td>
									<input type="text" 
											name="parameters[<?php echo $index; ?>][name]" 
											value="<?php echo esc_attr( $param['name'] ?? '' ); ?>" 
											placeholder="<?php esc_attr_e( 'parameter_name', 'eventlayer' ); ?>" 
											class="regular-text" />
								</td>
								<td>
									<input type="text" 
											name="parameters[<?php echo $index; ?>][default_value]" 
											value="<?php echo esc_attr( $param['default_value'] ?? '' ); ?>" 
											placeholder="<?php esc_attr_e( 'Default value', 'eventlayer' ); ?>" 
											class="regular-text" />
								</td>
								<td>
									<select name="parameters[<?php echo $index; ?>][target_type]">
										<option value="static" <?php selected( $param['target_type'] ?? '', 'static' ); ?>>
											<?php esc_html_e( 'Static Value', 'eventlayer' ); ?>
										</option>
										<option value="element_text" <?php selected( $param['target_type'] ?? '', 'element_text' ); ?>>
											<?php esc_html_e( 'Element Text', 'eventlayer' ); ?>
										</option>
										<option value="element_attribute" <?php selected( $param['target_type'] ?? '', 'element_attribute' ); ?>>
											<?php esc_html_e( 'Element Attribute', 'eventlayer' ); ?>
										</option>
										<option value="url_parameter" <?php selected( $param['target_type'] ?? '', 'url_parameter' ); ?>>
											<?php esc_html_e( 'URL Parameter', 'eventlayer' ); ?>
										</option>
									</select>
								</td>
								<td>
									<input type="text" 
											name="parameters[<?php echo $index; ?>][target_selector]" 
											value="<?php echo esc_attr( $param['target_selector'] ?? '' ); ?>" 
											placeholder="<?php esc_attr_e( 'CSS selector or attribute name', 'eventlayer' ); ?>" 
											class="regular-text" />
								</td>
								<td>
									<button type="button" class="button remove-parameter">
										<?php esc_html_e( 'Remove', 'eventlayer' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr class="parameter-row">
							<td>
								<input type="text" 
										name="parameters[0][name]" 
										value="" 
										placeholder="<?php esc_attr_e( 'parameter_name', 'eventlayer' ); ?>" 
										class="regular-text" />
							</td>
							<td>
								<input type="text" 
										name="parameters[0][default_value]" 
										value="" 
										placeholder="<?php esc_attr_e( 'Default value', 'eventlayer' ); ?>" 
										class="regular-text" />
							</td>
							<td>
								<select name="parameters[0][target_type]">
									<option value="static"><?php esc_html_e( 'Static Value', 'eventlayer' ); ?></option>
									<option value="element_text"><?php esc_html_e( 'Element Text', 'eventlayer' ); ?></option>
									<option value="element_attribute"><?php esc_html_e( 'Element Attribute', 'eventlayer' ); ?></option>
									<option value="url_parameter"><?php esc_html_e( 'URL Parameter', 'eventlayer' ); ?></option>
								</select>
							</td>
							<td>
								<input type="text" 
										name="parameters[0][target_selector]" 
										value="" 
										placeholder="<?php esc_attr_e( 'CSS selector or attribute name', 'eventlayer' ); ?>" 
										class="regular-text" />
							</td>
							<td>
								<button type="button" class="button remove-parameter">
									<?php esc_html_e( 'Remove', 'eventlayer' ); ?>
								</button>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<p>
				<button type="button" id="add-parameter" class="button">
					<?php esc_html_e( 'Add Parameter', 'eventlayer' ); ?>
				</button>
			</p>
		</div>
		<?php
	}
}
