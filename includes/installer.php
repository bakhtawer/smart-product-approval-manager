<?php
if (!defined('ABSPATH')) exit;

class SPAM_Installer {
    public static function install() {
        global $wpdb;
        $table = $wpdb->prefix . 'spam_logs';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id BIGINT NOT NULL,
            action VARCHAR(20) NOT NULL,
            user_id BIGINT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
