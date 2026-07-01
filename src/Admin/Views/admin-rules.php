<?php
/**
 * Event Rules admin page template.
 *
 * @package EventLayer
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EventLayer\Data\EventRuleRepository;

// Handle form submission.
$eventlayer_nonce = isset( $_POST['eventlayer_nonce'] )
	? sanitize_text_field( wp_unslash( $_POST['eventlayer_nonce'] ) )
	: '';

if ( isset( $_POST['submit_event_rule'] ) && wp_verify_nonce( $eventlayer_nonce, 'eventlayer_add_rule' ) ) {
	$repository = new EventRuleRepository();

	$event_type = isset( $_POST['event_type'] ) ? sanitize_text_field( wp_unslash( $_POST['event_type'] ) ) : '';
	$triggers   = isset( $_POST['triggers'] ) ? sanitize_textarea_field( wp_unslash( $_POST['triggers'] ) ) : '';
	$parameters = isset( $_POST['parameters'] ) ? sanitize_textarea_field( wp_unslash( $_POST['parameters'] ) ) : '';

	if ( $repository->create( $event_type, $triggers, $parameters ) ) {
		echo '<div class="notice notice-success"><p>'
			. esc_html__( 'Event rule created successfully!', 'eventlayer' )
			. '</p></div>';
	} else {
		echo '<div class="notice notice-error"><p>'
			. esc_html__( 'Error creating event rule.', 'eventlayer' )
			. '</p></div>';
	}
}

$repository = new EventRuleRepository();
$rules      = $repository->getAll();
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Event Rules', 'eventlayer' ); ?></h1>
	
	<div class="eventlayer-rules-content">
		<div class="postbox">
			<h2 class="hndle"><?php esc_html_e( 'Manage Event Rules', 'eventlayer' ); ?></h2>
			<div class="inside">
				<p><?php esc_html_e( 'Create and manage your DataLayer event rules here.', 'eventlayer' ); ?></p>
				
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'ID', 'eventlayer' ); ?></th>
							<th><?php esc_html_e( 'Event Type', 'eventlayer' ); ?></th>
							<th><?php esc_html_e( 'Triggers', 'eventlayer' ); ?></th>
							<th><?php esc_html_e( 'Parameters', 'eventlayer' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'eventlayer' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $rules ) ) : ?>
							<tr>
								<td colspan="5">
									<?php
									esc_html_e( 'No event rules found. Create your first rule!', 'eventlayer' );
									?>
								</td>
							</tr>
						<?php else : ?>
							<?php foreach ( $rules as $rule ) : ?>
								<tr>
									<td><?php echo esc_html( $rule->id ); ?></td>
									<td><?php echo esc_html( $rule->event_type ); ?></td>
									<td><?php echo esc_html( wp_trim_words( $rule->triggers, 10 ) ); ?></td>
									<td><?php echo esc_html( wp_trim_words( $rule->parameters, 10 ) ); ?></td>
									<td>
										<a href="#" class="button button-small">
											<?php esc_html_e( 'Edit', 'eventlayer' ); ?>
										</a>
										<a href="#" class="button button-small button-link-delete">
											<?php esc_html_e( 'Delete', 'eventlayer' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>

				<h3><?php esc_html_e( 'Add New Event Rule', 'eventlayer' ); ?></h3>
				
				<form method="post" action="">
					<?php wp_nonce_field( 'eventlayer_add_rule', 'eventlayer_nonce' ); ?>
					
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="event_type"><?php esc_html_e( 'Event Type', 'eventlayer' ); ?></label>
							</th>
							<td>
								<input type="text" id="event_type" name="event_type" class="regular-text"
										placeholder="e.g., button_click, form_submit" required />
								<p class="description">
									<?php esc_html_e( 'The name of the event to track', 'eventlayer' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="triggers"><?php esc_html_e( 'Triggers (JSON)', 'eventlayer' ); ?></label>
							</th>
							<td>
								<textarea id="triggers" name="triggers" rows="3" class="large-text"
										required>{"selector": ".test-button"}</textarea>
								<p class="description">
									<?php
									esc_html_e( 'JSON object defining when this event should fire', 'eventlayer' );
									?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="parameters">
									<?php esc_html_e( 'Parameters (JSON)', 'eventlayer' ); ?>
								</label>
							</th>
							<td>
								<textarea id="parameters" name="parameters" rows="3" class="large-text"
										required>{"button_text": "Test Button", "section": "header"}</textarea>
								<p class="description">
									<?php esc_html_e( 'JSON object with data to send with the event', 'eventlayer' ); ?>
								</p>
							</td>
						</tr>
					</table>

					<p>
						<button type="submit" name="submit_event_rule" class="button button-primary">
							<?php esc_html_e( 'Add Event Rule', 'eventlayer' ); ?>
						</button>
					</p>
				</form>

				<div class="postbox" style="margin-top: 20px;">
					<h3 class="hndle"><?php esc_html_e( 'Test Your Events', 'eventlayer' ); ?></h3>
					<div class="inside">
						<p><?php esc_html_e( 'Add this button to any page to test your events:', 'eventlayer' ); ?></p>
						<code>&lt;button class="test-button"&gt;Test EventLayer&lt;/button&gt;</code>
						<br><br>
						<button class="test-button button">
							<?php esc_html_e( 'Test EventLayer (Click and check browser console)', 'eventlayer' ); ?>
						</button>
					</div>
				</div>

				<p>
					<a href="#" class="button"><?php esc_html_e( 'Add Another Rule', 'eventlayer' ); ?></a>
				</p>
			</div>
		</div>
	</div>
</div>
