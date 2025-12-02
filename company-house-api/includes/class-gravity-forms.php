<?php
/**
 * Gravity Forms Integration Class
 * 
 * Handles AJAX requests from Gravity Forms for company search
 * and director lookup functionality.
 */

if (!defined('ABSPATH')) {
    exit;
}

class CHL_Gravity_Forms {

    private $api;

    /**
     * Set up the API handler and wire up our AJAX endpoints
     */
    public function __construct() {
        $this->api = new CHL_API_Handler();

        // Register handlers for logged-in users
        add_action('wp_ajax_chl_search_companies', array($this, 'ajax_search_companies'));
        add_action('wp_ajax_chl_get_directors', array($this, 'ajax_get_directors'));

        // Same handlers for public/non-logged-in users
        add_action('wp_ajax_nopriv_chl_search_companies', array($this, 'ajax_search_companies'));
        add_action('wp_ajax_nopriv_chl_get_directors', array($this, 'ajax_get_directors'));
    }

    /**
     * Search for companies based on user input
     * Validates the nonce and search term before hitting the API
     */
    public function ajax_search_companies() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'chl_nonce')) {
            wp_send_json_error(array('message' => 'Invalid security token.'));
        }

        $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        // Require at least 2 characters
        if (empty($search_term) || strlen($search_term) < 2) {
            wp_send_json_error(array('message' => 'Please enter at least 2 characters.'));
        }

        $companies = $this->api->search_companies($search_term);

        if (empty($companies)) {
            wp_send_json_error(array('message' => 'No companies found.'));
        }

        wp_send_json_success($companies);
    }

    /**
     * Fetch directors for a selected company
     * Called after user picks a company from the dropdown
     */
    public function ajax_get_directors() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'chl_nonce')) {
            wp_send_json_error(array('message' => 'Invalid security token.'));
        }

        $company_number = isset($_POST['company_number']) ? sanitize_text_field($_POST['company_number']) : '';

        if (empty($company_number)) {
            wp_send_json_error(array('message' => 'Company number required.'));
        }

        $directors = $this->api->get_directors($company_number);

        if (empty($directors)) {
            wp_send_json_error(array('message' => 'No directors found.'));
        }

        wp_send_json_success($directors);
    }
}