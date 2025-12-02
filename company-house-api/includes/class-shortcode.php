<?php
/**
 * Shortcode Handler Class
 * 
 * Provides shortcode functionality to display plugin settings
 * on the front-end of the website.
 */

if (!defined('ABSPATH')) {
    exit;
}

class CHL_Shortcode {

    /**
     * Register the shortcode with WordPress
     */
    public function __construct() {
        add_shortcode('companies_house_setting', array($this, 'render_shortcode'));
    }

    /**
     * Render the shortcode output
     * 
     * Displays the API key in a masked format for security.
     * 
     */
    public function render_shortcode($atts) {
        // Set default attributes
        $atts = shortcode_atts(array(
            'label' => '',
        ), $atts, 'companies_house_setting');

        $api_key = get_option('chl_api_key', '');

        if (empty($api_key)) {
            return '<p>' . esc_html__('No API key configured.', 'api-plugin') . '</p>';
        }

        // Mask the API key - show first 8 chars + **** + last 4 chars
        $masked_key = substr($api_key, 0, 8) . '****' . substr($api_key, -4);

        $output = '<div class="chl-setting-display">';
        
        if (!empty($atts['label'])) {
            $output .= '<span class="chl-label">' . esc_html($atts['label']) . '</span> ';
        }
        
        $output .= '<span class="chl-value">' . esc_html($masked_key) . '</span>';
        $output .= '</div>';

        return $output;
    }
}