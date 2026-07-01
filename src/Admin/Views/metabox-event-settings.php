<?php
/**
 * Event Settings meta box template.
 *
 * Included from MetaBoxes::event_settings_callback(); expects:
 * $event_type, $site_location, $trigger_delay, $stop_propagation,
 * $start_date, $start_time, $end_date, $end_time.
 *
 * @package EventLayer
 */

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
	<?php if ( \EventLayer\Pro\ProManager::has_feature( 'site_location' ) ) : ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Site Location', 'eventlayer' ); ?></th>
		<td>
			<fieldset>
				<label>
					<input type="radio" name="site_location" value="all_pages"
						<?php checked( $site_location, 'all_pages' ); ?> />
					<?php esc_html_e( 'All Pages', 'eventlayer' ); ?>
				</label><br>
				<label>
					<input type="radio" name="site_location" value="homepage"
						<?php checked( $site_location, 'homepage' ); ?> />
					<?php esc_html_e( 'Homepage Only', 'eventlayer' ); ?>
				</label><br>
				<label>
					<input type="radio" name="site_location" value="specific_pages"
						<?php checked( $site_location, 'specific_pages' ); ?> />
					<?php esc_html_e( 'Specific Pages', 'eventlayer' ); ?>
				</label>
			</fieldset>
			<p class="description">
				<?php esc_html_e( 'Choose where this event rule should be active.', 'eventlayer' ); ?>
			</p>
		</td>
	</tr>
	<?php else : ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Site Location', 'eventlayer' ); ?></th>
		<td>
			<?php
			\EventLayer\Pro\ProManager::render_feature_gate(
				'site_location',
				'Site Location Targeting',
				'Target specific pages or sections of your site with EventLayer Pro.'
			);
			?>
			<input type="hidden" name="site_location" value="all_pages" />
		</td>
	</tr>
	<?php endif; ?>

	<?php if ( \EventLayer\Pro\ProManager::has_feature( 'trigger_delay' ) ) : ?>
	<tr>
		<th scope="row">
			<label for="trigger_delay"><?php esc_html_e( 'Event Trigger Delay', 'eventlayer' ); ?></label>
		</th>
		<td>
			<input type="number" id="trigger_delay" name="trigger_delay"
				value="<?php echo esc_attr( $trigger_delay ); ?>" min="0" step="100" class="small-text" />
			<span><?php esc_html_e( 'milliseconds', 'eventlayer' ); ?></span>
			<p class="description">
				<?php esc_html_e( 'Delay before the event is triggered (0 = immediate).', 'eventlayer' ); ?>
			</p>
		</td>
	</tr>
	<?php else : ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Event Trigger Delay', 'eventlayer' ); ?></th>
		<td>
			<?php
			\EventLayer\Pro\ProManager::render_feature_gate(
				'trigger_delay',
				'Event Trigger Delay',
				'Add delays to event triggers for better tracking accuracy with EventLayer Pro.'
			);
			?>
			<input type="hidden" name="trigger_delay" value="0" />
		</td>
	</tr>
	<?php endif; ?>

	<?php if ( \EventLayer\Pro\ProManager::has_feature( 'stop_propagation' ) ) : ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Options', 'eventlayer' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="stop_propagation" value="1"
					<?php checked( $stop_propagation, 1 ); ?> />
				<?php esc_html_e( 'Stop Propagation', 'eventlayer' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'Prevent the event from bubbling up to parent elements.', 'eventlayer' ); ?>
			</p>
		</td>
	</tr>
	<?php else : ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Options', 'eventlayer' ); ?></th>
		<td>
			<?php
			\EventLayer\Pro\ProManager::render_feature_gate(
				'stop_propagation',
				'Event Propagation Control',
				'Control event bubbling behavior with EventLayer Pro.'
			);
			?>
			<input type="hidden" name="stop_propagation" value="0" />
		</td>
	</tr>
	<?php endif; ?>

	<?php $has_scheduling = \EventLayer\Pro\ProManager::has_feature( 'scheduling' ); ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Schedule', 'eventlayer' ); ?></th>
		<td>
			<?php if ( $has_scheduling ) : ?>
				<div style="display:flex; gap:8px; flex-wrap:wrap;">
					<div style="flex:1; min-width:140px;">
						<label for="schedule_start_date" class="screen-reader-text">
							<?php esc_html_e( 'Start Date', 'eventlayer' ); ?>
						</label>
						<input type="date" id="schedule_start_date" name="schedule_start_date"
							value="<?php echo esc_attr( $start_date ); ?>"
							class="small-text" style="width: 100%;" />
					</div>
					<div style="flex:1; min-width:120px;">
						<label for="schedule_start_time" class="screen-reader-text">
							<?php esc_html_e( 'Start Time', 'eventlayer' ); ?>
						</label>
						<input type="time" id="schedule_start_time" name="schedule_start_time"
							value="<?php echo esc_attr( $start_time ); ?>"
							class="small-text" style="width: 100%;" />
					</div>
				</div>
				<div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:6px;">
					<div style="flex:1; min-width:140px;">
						<label for="schedule_end_date" class="screen-reader-text">
							<?php esc_html_e( 'End Date', 'eventlayer' ); ?>
						</label>
						<input type="date" id="schedule_end_date" name="schedule_end_date"
							value="<?php echo esc_attr( $end_date ); ?>"
							class="small-text" style="width: 100%;" />
					</div>
					<div style="flex:1; min-width:120px;">
						<label for="schedule_end_time" class="screen-reader-text">
							<?php esc_html_e( 'End Time', 'eventlayer' ); ?>
						</label>
						<input type="time" id="schedule_end_time" name="schedule_end_time"
							value="<?php echo esc_attr( $end_time ); ?>"
							class="small-text" style="width: 100%;" />
					</div>
				</div>
				<p class="description">
					<?php
					esc_html_e(
						'If set, the rule will only be active between the start and end times (site timezone). Leave blank for always on.',
						'eventlayer'
					);
					?>
				</p>
			<?php else : ?>
				<?php
				\EventLayer\Pro\ProManager::render_feature_gate(
					'scheduling',
					__( 'Scheduling', 'eventlayer' ),
					__(
						'Schedule event rules to start and stop automatically with EventLayer Pro.',
						'eventlayer'
					)
				);
				?>
				<input type="hidden" name="schedule_start_date"
					value="<?php echo esc_attr( $start_date ); ?>" />
				<input type="hidden" name="schedule_start_time"
					value="<?php echo esc_attr( $start_time ); ?>" />
				<input type="hidden" name="schedule_end_date"
					value="<?php echo esc_attr( $end_date ); ?>" />
				<input type="hidden" name="schedule_end_time"
					value="<?php echo esc_attr( $end_time ); ?>" />
			<?php endif; ?>
		</td>
	</tr>

</table>
