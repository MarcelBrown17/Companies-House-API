<?php
/**
 * Admin Settings Class
 * 
 * Creates and manages the plugin settings page in WordPress admin.
 * Uses the WordPress Settings API for secure option handling.
 */

if (!defined('ABSPATH')) {
    exit;
}

class CHL_Admin_Settings {

    /**
     * Set up the settings page and register our hooks
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add the settings page under the Settings menu
     * Only admins with 'manage_options' capability can access it
     */
    public function add_settings_page() {
        add_options_page(
            __('Companies House Lookup', 'api-plugin'),
            __('Companies House', 'api-plugin'),
            'manage_options',
            'companies-house-lookup',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register our settings, sections, and fields with WordPress
     */
    public function register_settings() {
        // Register the API key option
        register_setting(
            'chl_settings_group',
            'chl_api_key',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            )
        );

        // Add the main settings section
        add_settings_section(
            'chl_main_section',
            __('API Configuration', 'api-plugin'),
            array($this, 'section_callback'),
            'companies-house-lookup'
        );

        // Add the API key field
        add_settings_field(
            'chl_api_key',
            __('API Key', 'api-plugin'),
            array($this, 'api_key_field_callback'),
            'companies-house-lookup',
            'chl_main_section'
        );
    }

    /**
     * Output description text for the settings section
     */
    public function section_callback() {
        echo '<p>' . esc_html__('Enter your Companies House API key below.', 'api-plugin') . '</p>';
    }

    /**
     * Render the API key input field
     */
    public function api_key_field_callback() {
        $api_key = get_option('chl_api_key', '');
        ?>
        <input 
            type="text" 
            id="chl_api_key" 
            name="chl_api_key" 
            value="<?php echo esc_attr($api_key); ?>" 
            class="regular-text"
        />
        <p class="description">
            <?php esc_html_e('Your Companies House API key for authentication.', 'api-plugin'); ?>
        </p>
        <?php
    }

    /**
     * Render the complete settings page
     */
    public function render_settings_page() {
        // Only admins can see this page
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form action="options.php" method="post">
                <?php
                // Security fields and form setup
                settings_fields('chl_settings_group');
                do_settings_sections('companies-house-lookup');
                submit_button(__('Save Settings', 'api-plugin'));
                ?>
            </form>
        </div>
        <?php
    }
}