<?php
if (!defined('ABSPATH')) exit;

class SPAM_Rest_Controller {

    public static function init() {
        register_rest_route('spam/v1', '/pending-products', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_pending'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);

        register_rest_route('spam/v1', '/approve/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'approve'],
            'permission_callback' => function () {
                return current_user_can('manage_options') && wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'] ?? '', 'wp_rest');
            }
        ]);

        register_rest_route('spam/v1', '/reject/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'reject'],
            'permission_callback' => function () {
                return current_user_can('manage_options') && wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'] ?? '', 'wp_rest');
            }
        ]);
    }

    public static function get_pending() {
        // Try to use WooCommerce if available
        if (function_exists('wc_get_products')) {
            $products = wc_get_products(['status' => 'pending', 'limit' => -1]);
            $out = [];
            foreach ($products as $p) {
                $out[] = [
                    'id' => $p->get_id(),
                    'name' => $p->get_name(),
                    'author_id' => $p->get_post_data()->post_author ?? 0
                ];
            }
            return rest_ensure_response($out);
        }

        // Fallback sample data
        $sample = [
            ['id'=>101,'name'=>'Sample Product A','author_id'=>1],
            ['id'=>102,'name'=>'Sample Product B','author_id'=>1],
            ['id'=>103,'name'=>'Sample Product C','author_id'=>2],
        ];
        return rest_ensure_response($sample);
    }

    public static function approve($data) {
        $id = intval($data['id']);
        return SPAM_Approval_Service::approve_product($id);
    }

    public static function reject($data) {
        $id = intval($data['id']);
        return SPAM_Approval_Service::reject_product($id);
    }
}
