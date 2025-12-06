<?php
if (!defined('ABSPATH')) exit;

class SPAM_Product_Logger {
    public static function log($product_id, $action) {
        global $wpdb;
        $table = $wpdb->prefix . 'spam_logs';
        $user_id = get_current_user_id() ?: 0;
        $wpdb->insert($table, [
            'product_id' => $product_id,
            'action' => sanitize_text_field($action),
            'user_id' => $user_id,
            'created_at' => current_time('mysql', 1)
        ]);
    }

    public static function recent_logs($limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'spam_logs';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d", $limit));
    }
}
