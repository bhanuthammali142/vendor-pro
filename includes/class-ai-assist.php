<?php
/**
 * AI Assist Module
 * Generates content using OpenAI
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_AI_Assist
{
    /**
     * Instance
     */
    protected static $_instance = null;

    /**
     * Get instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('wp_ajax_vendorpro_ai_generate_description', array($this, 'ajax_generate_description'));
    }

    /**
     * AJAX Generate Description
     */
    public function ajax_generate_description()
    {
        check_ajax_referer('vendorpro_dashboard_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Permission denied', 'vendorpro')));
        }

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $keywords = isset($_POST['keywords']) ? sanitize_text_field($_POST['keywords']) : '';

        if (empty($title)) {
            wp_send_json_error(array('message' => __('Product title is required', 'vendorpro')));
        }

        $api_key = get_option('vendorpro_openai_api_key');
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('Admin has not configured OpenAI API key', 'vendorpro')));
        }

        $description = $this->call_openai($title, $category, $keywords, $api_key);

        if ($description) {
            wp_send_json_success(array('description' => $description));
        } else {
            wp_send_json_error(array('message' => __('Failed to generate content', 'vendorpro')));
        }
    }

    /**
     * Call OpenAI API
     */
    private function call_openai($title, $category, $keywords, $api_key)
    {
        $model = get_option('vendorpro_ai_model', 'gpt-3.5-turbo');

        $prompt = "Write a compelling and SEO-friendly product description for an e-commerce product.\n\n";
        $prompt .= "Product Title: $title\n";
        if ($category)
            $prompt .= "Category: $category\n";
        if ($keywords)
            $prompt .= "Keywords: $keywords\n";
        $prompt .= "\nDescription:";

        $body = array(
            'model' => $model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'You are a professional e-commerce copywriter.'
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'max_tokens' => 500,
            'temperature' => 0.7
        );

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($body),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['choices'][0]['message']['content'])) {
            return trim($data['choices'][0]['message']['content']);
        }

        return false;
    }
}
