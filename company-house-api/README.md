# Companies House Lookup

A WordPress plugin that integrates the Companies House API with Gravity Forms, enabling users to search for UK companies and their directors.

## Description

This plugin provides a seamless integration between Gravity Forms and the Companies House API, allowing form users to:

- Search for companies by name with real-time results
- View company details including registration number and status
- Automatically populate a directors dropdown based on the selected company

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Gravity Forms plugin (active)
- Companies House API key

## Installation

1. Download the plugin zip file
2. Navigate to **Plugins → Add New → Upload Plugin** in WordPress admin
3. Upload the zip file and click **Install Now**
4. Activate the plugin
5. Go to **Settings → Companies House** to configure your API key

## Plugin Architecture

```
companies-house-lookup/
├── companies-house-lookup.php    # Main plugin file, initialization
├── includes/
│   ├── class-api-handler.php     # Companies House API communication
│   ├── class-admin-settings.php  # WordPress Settings API implementation
│   ├── class-gravity-forms.php   # GF integration and AJAX handlers
│   └── class-shortcode.php       # Front-end shortcode output
├── public/
│   └── js/
│       └── company-search.js     # Front-end AJAX functionality
└── README.md
```

### Key Classes

- **Companies_House_Lookup**: Main plugin class, singleton pattern, loads dependencies
- **CHL_API_Handler**: Handles all API requests to Companies House
- **CHL_Admin_Settings**: Creates settings page using WordPress Settings API
- **CHL_Gravity_Forms**: Registers AJAX handlers for form interactions
- **CHL_Shortcode**: Provides `[companies_house_setting]` shortcode

## Configuration

### API Key Setup

1. Navigate to **Settings → Companies House**
2. Enter your Companies House API key
3. Click **Save Settings**

### Creating the Gravity Form

1. Go to **Forms → New Form**
2. Add a **Text Field** for Company Name:
   - Add CSS class: `gf-company-search`
3. Add a **Dropdown Field** for Directors:
   - Add CSS class: `gf-directors-dropdown`
4. Save the form

## Testing the Plugin

### Step 1: Verify Installation
- Confirm plugin is active under **Plugins**
- Check **Settings → Companies House** page loads

### Step 2: Configure API
- Enter the provided API key: `99093e98-7350-427b-883c-75cdacdb4c22`
- Save settings

### Step 3: Create Test Form
- Create a new Gravity Form with the fields described above
- Embed the form on a test page

### Step 4: Test Functionality
- Enter a company name (e.g., "Tesco")
- Verify dropdown appears with matching companies
- Select a company
- Verify directors dropdown populates

### Step 5: Test Shortcode
- Add `[companies_house_setting]` to any page
- Verify it displays the masked API key

## Shortcode Usage

Display the configured API key (masked):

```
[companies_house_setting]
```

With custom label:

```
[companies_house_setting label="API Status:"]
```

## Security Features

- All inputs sanitized using `sanitize_text_field()`
- All outputs escaped using `esc_html()` and `esc_attr()`
- AJAX requests protected with nonce verification
- Admin pages protected with `manage_options` capability check
- API key masked when displayed publicly

## Hooks Reference

### Actions
- `wp_ajax_chl_search_companies` - Company search AJAX
- `wp_ajax_chl_get_directors` - Directors fetch AJAX

### Filters
None currently implemented.

## Changelog

### 1.0.0
- Initial release
- Companies House API integration
- Gravity Forms field population
- Admin settings page
- Front-end shortcode

## Author

Marcel Brown - [Elemental](https://elemental.co.za)

## License

GPL-2.0+