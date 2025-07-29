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
    
    <div class="eventlayer-admin-content">
        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e( 'Welcome to EventLayer', 'eventlayer' ); ?></h2>
            <div class="inside">
                <p><?php esc_html_e( 'EventLayer allows you to create custom DataLayer events for Google Analytics 4 and Google Tag Manager.', 'eventlayer' ); ?></p>
                
                <h3><?php esc_html_e( 'Getting Started', 'eventlayer' ); ?></h3>
                <ol>
                    <li><?php esc_html_e( 'Navigate to Event Rules to create your first event', 'eventlayer' ); ?></li>
                    <li><?php esc_html_e( 'Define triggers and parameters for your events', 'eventlayer' ); ?></li>
                    <li><?php esc_html_e( 'Events will automatically be pushed to window.dataLayer', 'eventlayer' ); ?></li>
                </ol>

                <p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=eventlayer-rules' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Manage Event Rules', 'eventlayer' ); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
