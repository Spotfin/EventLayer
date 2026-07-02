<?php
/**
 * Trigger Elements meta box template.
 *
 * Included from MetaBoxes::trigger_elements_callback(); expects:
 * $parent_selector, $multiple_toggle, $child_selectors, $parent_selector_placeholder.
 *
 * @package EventLayer
 */

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
					placeholder="<?php echo esc_attr( $parent_selector_placeholder ); ?>" />
			<p class="description">
				<?php
				esc_html_e(
					'CSS selector for the element(s) that should trigger this event.',
					'eventlayer'
				);
				?>
			</p>
		</td>
	</tr>
	<?php if ( \EventLayer\Gating\Gating::provider()->has_feature( 'multiple_toggle' ) ) : ?>
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
				<?php
				esc_html_e( 'Enable if the selector matches multiple elements on the page.', 'eventlayer' );
				?>
			</p>
		</td>
	</tr>
	<?php else : ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Multiple Elements', 'eventlayer' ); ?></th>
		<td>
			<p class="description">
				<?php
				esc_html_e(
					'Events fire on every element matching the selector. Upgrade to Pro for per-instance control.',
					'eventlayer'
				);
				?>
			</p>
			<?php
			\EventLayer\Admin\FeatureGate::render(
				'multiple_toggle',
				__( 'Per-Instance Element Control', 'eventlayer' ),
				__( 'Fine-tune how individual matching elements are tracked with EventLayer Pro.', 'eventlayer' )
			);
			// Preserve the stored value so a Pro downgrade does not erase it.
			if ( $multiple_toggle ) :
				?>
				<input type="hidden" name="multiple_toggle" value="1" />
			<?php endif; ?>
		</td>
	</tr>
	<?php endif; ?>
	<?php if ( \EventLayer\Gating\Gating::provider()->has_feature( 'child_selectors' ) ) : ?>
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
				<?php
				esc_html_e( 'Optional: Additional selectors for more specific targeting.', 'eventlayer' );
				?>
			</p>
		</td>
	</tr>
	<?php else : ?>
	<tr>
		<th scope="row">
			<label><?php esc_html_e( 'Child Selectors', 'eventlayer' ); ?></label>
		</th>
		<td>
			<?php
			\EventLayer\Admin\FeatureGate::render(
				'child_selectors',
				__( 'Child Selectors', 'eventlayer' ),
				__( 'Target nested elements with additional selectors using EventLayer Pro.', 'eventlayer' )
			);
			// Preserve stored values so a Pro downgrade does not erase them.
			foreach ( $child_selectors as $selector ) :
				?>
				<input type="hidden" name="child_selectors[]" value="<?php echo esc_attr( $selector ); ?>" />
			<?php endforeach; ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php
	/**
	 * Extension point: add rows to the Trigger Elements meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param \EventLayer\Model\EventRule $rule Current rule.
	 * @param \WP_Post                    $post Current post.
	 */
	do_action( 'eventlayer_trigger_elements_fields', $rule, $post );
	?>
</table>
