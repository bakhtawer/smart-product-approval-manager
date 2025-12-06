<?php
if (!defined('ABSPATH')) exit;

class SPAM_Approval_Service {

    public static function approve_product($product_id) {
        // If WooCommerce available, update product status
        if (function_exists('wc_get_product')) {
            $product = wc_get_product($product_id);
            if ($product) {
                try {
                    $product->set_status('publish');
                    $product->save();
                } catch (Exception $e) {
                    // ignore
                }
            }
        }
        // Log action
        SPAM_Product_Logger::log($product_id, 'approved');

        // Send email to author if possible
        $author_email = self::get_author_email($product_id);
        if ($author_email) {
            wp_mail($author_email, 'Your product was approved', 'Congratulations! Your product was approved by admin.');
        }

        return rest_ensure_response(['success' => true, 'action' => 'approved', 'product_id' => $product_id]);
    }

    public static function reject_product($product_id) {
        if (function_exists('wc_get_product')) {
            $product = wc_get_product($product_id);
            if ($product) {
                try {
                    $product->set_status('draft');
                    $product->save();
                } catch (Exception $e) {
                    // ignore
                }
            }
        }
        SPAM_Product_Logger::log($product_id, 'rejected');

        $author_email = self::get_author_email($product_id);
        if ($author_email) {
            wp_mail($author_email, 'Your product was rejected', 'Your product was rejected by admin. Please review and resubmit.');
        }

        return rest_ensure_response(['success' => true, 'action' => 'rejected', 'product_id' => $product_id]);
    }

    private static function get_author_email($product_id) {
        // Try to get post author email
        $post = get_post($product_id);
        if ($post) {
            $user = get_user_by('id', $post->post_author);
            if ($user) return $user->user_email;
        }
        return null;
    }
}
