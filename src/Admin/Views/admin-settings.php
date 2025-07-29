<?php
/**
 * Settings admin page template.
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
    
    <div class="eventlayer-settings-content">
        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e( 'General Settings', 'eventlayer' ); ?></h2>
            <div class="inside">
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'eventlayer_settings' );
                    do_settings_sections( 'eventlayer_settings' );
                    submit_button();
                    ?>
                </form>
            </div>
        </div>

        <div class="postbox">
            <h2 class="hndle"><?php esc_html_e( 'Information', 'eventlayer' ); ?></h2>
            <div class="inside">
                <h3><?php esc_html_e( 'Plugin Information', 'eventlayer' ); ?></h3>
                <p><strong><?php esc_html_e( 'Version:', 'eventlayer' ); ?></strong> 1.0.0</p>
                <p><strong><?php esc_html_e( 'Active Event Rules:', 'eventlayer' ); ?></strong> 
                    <?php 
                    $active_rules = wp_count_posts( 'event_rule' );
                    echo esc_html( $active_rules->publish ?? 0 );
                    ?>
                </p>
                
                <h3><?php esc_html_e( 'Documentation', 'eventlayer' ); ?></h3>
                <p><?php esc_html_e( 'For help and documentation, visit:', 'eventlayer' ); ?> 
                    <a href="https://eventlayerpro.com/docs" target="_blank">https://eventlayerpro.com/docs</a>
                </p>
            </div>
        </div>
    </div>
</div>
