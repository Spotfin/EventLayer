<?php

namespace EventLayer\Data;

use \wpdb;
use \dbDelta;

class Installer
{
    public static function activate()
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'eventlayer_rules';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_type VARCHAR(100) NOT NULL,
            triggers LONGTEXT NOT NULL,
            parameters LONGTEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql);
    }
}
