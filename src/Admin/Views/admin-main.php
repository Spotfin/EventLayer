<?php
/**
 * Main admin page template.
 *
 * @package EventLayer
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'EventLayer Settings', 'eventlayer' ); ?></h1>
	<style>
	.eventlayer-hero {
		margin: 16px 0 24px;
		display: flex;
		align-items: center;
		gap: 16px;
		flex-wrap: wrap;
	}
	.eventlayer-hero-logo {
		background: linear-gradient(135deg, #1f2937 0%, #0f172a 100%);
		border: 1px solid rgba(255,255,255,0.06);
		border-radius: 10px;
		padding: 14px 16px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		box-shadow: 0 2px 6px rgba(0,0,0,0.12);
	}
	.eventlayer-hero-logo img {
		display: block;
		max-width: 260px;
		height: auto;
	}
	</style>

	<div class="eventlayer-hero">
		<div class="eventlayer-hero-logo">
			<img src="<?php echo esc_url( \EventLayer\Plugin::get_instance()->get_plugin_url( 'src/Assets/images/eventlayerpro_logo.png' ) ); ?>" alt="<?php esc_attr_e( 'EventLayer Pro', 'eventlayer' ); ?>" />
		</div>
	</div>
	
	<div class="eventlayer-admin-content">
		<div class="postbox">
			<div class="inside">
				<h2 class=""><?php esc_html_e( 'Welcome to EventLayer', 'eventlayer' ); ?></h2>
				<p><?php esc_html_e( 'EventLayer allows you to create custom DataLayer events for Google Analytics 4 and Google Tag Manager.', 'eventlayer' ); ?></p>
				
				<h3><?php esc_html_e( 'Getting Started', 'eventlayer' ); ?></h3>
				<ol>
					<li><?php esc_html_e( 'Click "Add New" to create your first event rule', 'eventlayer' ); ?></li>
					<li><?php esc_html_e( 'Configure the event type, triggers, and parameters', 'eventlayer' ); ?></li>
					<li><?php esc_html_e( 'Publish the event rule to activate it', 'eventlayer' ); ?></li>
					<li><?php esc_html_e( 'Events will automatically be pushed to window.dataLayer', 'eventlayer' ); ?></li>
				</ol>

				<h3><?php esc_html_e( 'Quick Actions', 'eventlayer' ); ?></h3>
				<p>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=event_rule' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Add New Event Rule', 'eventlayer' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=event_rule' ) ); ?>" class="button">
						<?php esc_html_e( 'View All Event Rules', 'eventlayer' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eventlayer-settings' ) ); ?>" class="button">
						<?php esc_html_e( 'Settings', 'eventlayer' ); ?>
					</a>
				</p>

				<h3><?php esc_html_e( 'Recent Event Rules', 'eventlayer' ); ?></h3>
				<?php
				$recent_rules = get_posts(
					array(
						'post_type'      => 'event_rule',
						'posts_per_page' => 5,
						'post_status'    => array( 'publish', 'draft' ),
					)
				);

				if ( $recent_rules ) :
					?>
					<ul>
						<?php foreach ( $recent_rules as $rule ) : ?>
							<li>
								<a href="<?php echo esc_url( get_edit_post_link( $rule->ID ) ); ?>">
									<?php echo esc_html( $rule->post_title ?: __( '(no title)', 'eventlayer' ) ); ?>
								</a>
								- <?php echo esc_html( get_post_meta( $rule->ID, '_event_type', true ) ?: __( 'No event type', 'eventlayer' ) ); ?>
								<small>(<?php echo esc_html( $rule->post_status ); ?>)</small>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php esc_html_e( 'No event rules found. Create your first one!', 'eventlayer' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
