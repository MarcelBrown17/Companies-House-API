<?php
/**
 * API Handler Class
 *
 * Handles all communication with the Companies House API.
 * Provides methods for searching companies and fetching directors.
 */

if (!defined('ABSPATH')) {
	exit;
}

class CHL_API_Handler {

	private $api_base = 'https://api.company-information.service.gov.uk';

	/**
	 * Get the API key from WordPress settings
	 */
	private function get_api_key() {
		return get_option('chl_api_key', '');
	}

	/**
	 * Make an authenticated request to the Companies House API
	 * Returns the decoded JSON response or WP_Error on failure
	 */
	private function make_request($endpoint, $params = array()) {

		$api_key = $this->get_api_key();

		if (empty($api_key)) {
			return new WP_Error('no_api_key', __('API key not configured.', 'api-plugin'));
		}

		$url = $this->api_base . $endpoint;

		// Add query parameters to the URL if provided
		if (!empty($params)) {
			$url = add_query_arg($params, $url);
		}

		// Make GET request with Basic Auth
		$response = wp_remote_get($url, array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode($api_key . ':'),
			),
			'timeout' => 30,
		));

		if (is_wp_error($response)) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code($response);
		$body = wp_remote_retrieve_body($response);

		if ($code !== 200) {
			return new WP_Error('api_error', __('API request failed with code: ', 'api-plugin') . $code);
		}

		// Decode JSON response and return as array
		return json_decode($body, true);
	}

	/**
	 * Search for companies by name
	 * Only returns companies where the name starts with the search term
	 */
	public function search_companies($company_name) {

		if (empty($company_name)) {
			return array();
		}

		$search_term = sanitize_text_field($company_name);

		$params = array(
			'q' => $search_term,
		);

		$result = $this->make_request('/search/companies', $params);

		if (is_wp_error($result)) {
			return array();
		}

		$companies = array();

		if (isset($result['items']) && is_array($result['items'])) {
			foreach ($result['items'] as $item) {

				$name = isset($item['title']) ? $item['title'] : '';

				// Check if company name starts with search term
				if (stripos($name, $search_term) === 0) {
					$companies[] = array(
						'company_number' => isset($item['company_number']) ? $item['company_number'] : '',
						'company_name'   => $name,
						'company_status' => isset($item['company_status']) ? $item['company_status'] : '',
						'address'        => isset($item['address_snippet']) ? $item['address_snippet'] : '',
						'date_of_creation' => isset($item['date_of_creation']) ? $item['date_of_creation'] : '',
					);
				}
			}
		}

		return $companies;
	}

	/**
	 * Get directors for a specific company
	 * Only returns officers with 'director' in their role title
	 */
	public function get_directors($company_number) {

		if (empty($company_number)) {
			return array();
		}

		$company_number = sanitize_text_field($company_number);

		$result = $this->make_request('/company/' . $company_number . '/officers');

		if (is_wp_error($result)) {
			return array();
		}

		$directors = array();

		if (isset($result['items']) && is_array($result['items'])) {
			foreach ($result['items'] as $item) {

				$role = isset($item['officer_role']) ? strtolower($item['officer_role']) : '';

				// Only include officers whose role contains 'director'
				if (strpos($role, 'director') !== false) {
					$directors[] = array(
						'name'         => isset($item['name']) ? $item['name'] : '',
						'role'         => isset($item['officer_role']) ? $item['officer_role'] : '',
						'appointed_on' => isset($item['appointed_on']) ? $item['appointed_on'] : '',
						'resigned_on'  => isset($item['resigned_on']) ? $item['resigned_on'] : '',
					);
				}
			}
		}

		return $directors;
	}
}