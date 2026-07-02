<?php
/**
 * Parameters meta box template.
 *
 * Included from MetaBoxes::parameters_callback(); expects:
 * $parameters, $target_types, $target_selector_placeholder.
 *
 * @package EventLayer
 */

?>
<div id="parameters-container">
	<!-- Hidden template for adding parameters (cloned by JS) -->
	<table style="display:none;">
		<tbody>
			<tr id="parameter-template-row" class="parameter-row">
				<td>
					<input type="text" name="__NAME__[name]" value=""
						placeholder="<?php esc_attr_e( 'parameter_name', 'eventlayer' ); ?>"
						class="regular-text" />
				</td>
				<td>
					<input type="text" name="__NAME__[default_value]" value=""
						placeholder="<?php esc_attr_e( 'Default value', 'eventlayer' ); ?>"
						class="regular-text" />
				</td>
				<td>
					<select name="__NAME__[target_type]">
						<?php foreach ( $target_types as $slug => $label ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>">
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<input type="text" name="__NAME__[target_selector]" value=""
						placeholder="<?php esc_attr_e( 'CSS selector or attribute name', 'eventlayer' ); ?>"
						class="regular-text" />
				</td>
				<td>
					<button type="button" class="button remove-parameter">
						<?php esc_html_e( 'Remove', 'eventlayer' ); ?>
					</button>
				</td>
			</tr>
		</tbody>
	</table>
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
								<?php foreach ( $target_types as $slug => $label ) : ?>
									<option value="<?php echo esc_attr( $slug ); ?>"
										<?php selected( $param['target_type'] ?? '', $slug ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
						<td>
							<input type="text"
									name="parameters[<?php echo $index; ?>][target_selector]"
									value="<?php echo esc_attr( $param['target_selector'] ?? '' ); ?>"
									placeholder="<?php echo esc_attr( $target_selector_placeholder ); ?>"
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
							<?php foreach ( $target_types as $slug => $label ) : ?>
								<option value="<?php echo esc_attr( $slug ); ?>">
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input type="text"
								name="parameters[0][target_selector]"
								value=""
								placeholder="<?php echo esc_attr( $target_selector_placeholder ); ?>"
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
	<?php if ( ! \EventLayer\Gating\Gating::provider()->has_feature( 'url_parameter' ) ) : ?>
	<p class="description" style="margin-top: 8px; font-size: 12px; color: #666;">
		🔒 <?php esc_html_e( 'Upgrade to Pro', 'eventlayer' ); ?>
		<a href="<?php echo esc_url( \EventLayer\Gating\Gating::provider()->get_upgrade_url() ); ?>"
			target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'for URL Parameter extraction', 'eventlayer' ); ?>
		</a>
	</p>
	<?php endif; ?>
</div>
