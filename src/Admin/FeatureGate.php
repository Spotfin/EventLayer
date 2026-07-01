<?php
/**
 * Pro feature gate UI.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin;

use EventLayer\Gating\Gating;

/**
 * Renders the locked-feature upsell box shown in place of gated UI.
 *
 * @since 1.0.0
 */
final class FeatureGate {

	/**
	 * Render a pro feature gate. No-op when the feature is available.
	 *
	 * @param string $feature     Feature slug.
	 * @param string $title       Gate title.
	 * @param string $description Gate description.
	 * @return void
	 */
	public static function render( string $feature, string $title, string $description ): void {
		if ( Gating::provider()->has_feature( $feature ) ) {
			return;
		}

		?>
		<div class="eventlayer-pro-gate"
			style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px;">
			<h4 style="margin-top: 0; color: #d63638;">🔒 <?php echo esc_html( $title ); ?></h4>
			<p style="margin-bottom: 10px;"><?php echo esc_html( $description ); ?></p>
			<a href="<?php echo esc_url( Gating::provider()->get_upgrade_url() ); ?>"
				class="button button-primary"
				target="_blank">
				<?php esc_html_e( 'Upgrade to EventLayer Pro', 'eventlayer' ); ?>
			</a>
		</div>
		<?php
	}
}
