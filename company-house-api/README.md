# Companies House API Plugin - Companies House Integration

A WordPress plugin that integrates the Companies House API with Gravity Forms, enabling users to search for UK companies and their directors.

## Description

This plugin provides a seamless integration between Gravity Forms and the UK Companies House Public Data API, allowing form users to:

- Search for companies by name with real-time autocomplete
- View matching company names in a dropdown list
- Automatically populate form fields with selected company data (registration number and incorporation date)
- Fetch and select directors associated with the chosen company

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
api-plugin/
├── api-plugin.php             	 	# Main plugin file - initialization and hooks
├── includes/
│   ├── class-api-handler.php   	# Companies House API communication
│   ├── class-admin-settings.php	# WordPress Settings API implementation
│   ├── class-gravity-forms.php  	# AJAX handlers for Gravity Forms integration
│   └── class-shortcode.php      	# Front-end shortcode functionality
├── public/
│   └── js/
│       └── company-search.js    	# Front-end JavaScript for autocomplete
└── README.md                   	# This documentation file
```

### Class Descriptions

#### `api-plugin.php` (Main Plugin File)
- Defines plugin constants (version, paths)
- Loads all required class files
- Registers activation and deactivation hooks
- Enqueues front-end JavaScript with localized AJAX data

#### `CHL_API_Handler` (class-api-handler.php)
- Handles all communication with Companies House API
- Methods:
  - `search_companies($company_name)` 	- Searches for companies using `/search/companies` endpoint
  - `get_directors($company_number)` 	- Fetches officers using `/company/{company_number}/officers` endpoint
  - `make_request($endpoint, $params)`	- Private method for authenticated API requests

#### `CHL_Admin_Settings` (class-admin-settings.php)
- Creates settings page under WordPress Settings menu
- Uses WordPress Settings API for secure option handling
- Provides text input field for API key storage

#### `CHL_Gravity_Forms` (class-gravity-forms.php)
- Registers AJAX action hooks for company search and director fetch
- Handles both logged-in and non-logged-in user requests
- Validates nonces and sanitizes all input data

#### `CHL_Shortcode` (class-shortcode.php)
- Registers `[companies_house_setting]` shortcode
- Displays masked API key on front-end pages
- Supports optional `label` attribute

## Configuration

### API Key Setup

1. Obtain an API key from [Companies House Developer Hub](https://developer.company-information.service.gov.uk/)
2. Navigate to **Settings → Companies House** in WordPress admin
3. Enter your Companies House API key
4. Click **Save Settings**

### Creating the Gravity Form

Create a Gravity Form with the following four fields:

1. **Company Name** (Single Line Text field):
   - Set the label to "Company Name"
   - Go to **Appearance** tab
   - Add CSS class: `gf-company-search`

2. **Company Registration Number** (Single Line Text field):
   - Set the label to "Company Registration Number"
   - Go to **Appearance** tab
   - Add CSS class: `gf-company-registration`
   - This field will auto-populate and become readonly when a company is selected

3. **Date of Incorporation** (Single Line Text field):
   - Set the label to "Date of Incorporation"
   - Go to **Appearance** tab
   - Add CSS class: `gf-company-incorporation`
   - This field will auto-populate and become readonly when a company is selected

4. **Company Directors** (Dropdown field):
   - Set the label to "Company Directors"
   - Go to **Appearance** tab
   - Add CSS class: `gf-directors-dropdown`
   - This field will be disabled until a company is selected

5. Save the form
6. Embed the form on a page using the Gravity Forms block or shortcode

## How to use the Plugin

### Step 1: Verify Installation
- Confirm the plugin is active under **Plugins**
- Check that **Settings → Companies House** page loads correctly

### Step 2: Configure API Key
- Enter your Companies House API key
- Save settings

### Step 3: Create Test Form
- Create a new Gravity Form with the four fields described above
- Ensure CSS classes are correctly applied to each field
- Embed the form on the required page

### Step 4: Test Company Search
1. Navigate to the page with the form
2. Type a company name (e.g. "") in the Company Name field
3. Wait for the dropdown to appear with matching companies
4. Verify that only companies starting with your search term are shown
5. Click on a company to select it

### Step 5: Test Auto-Population
1. After selecting a company, verify that:
   - The Company Registration Number field is populated and readonly
   - The Date of Incorporation field is populated and readonly
   - Both fields display the correct information

### Step 6: Test Directors Dropdown
1. After selecting a company, the Directors dropdown should become enabled
2. Verify that directors are populated in the dropdown
3. Only active directors (not resigned) should be shown
4. If there's only one active director, it should be auto-selected
5. If there are multiple directors, a placeholder shows the count (e.g., "3 directors found")

### Step 7: Test Shortcode
1. Add `[companies_house_setting]` to any page or post
2. Verify it displays the masked API key (first 8 + last 4 characters)
3. Test with label: `[companies_house_setting label="API Status:"]`

### Step 8: Test Form Submission
1. Complete the form with a selected company and director
2. Submit the form
3. Verify all entries are saved correctly in Gravity Forms

## API Endpoints Used

| Endpoint | Purpose |
|----------|---------|
| `GET /search/companies` | Search for companies by name |
| `GET /company/{company_number}/officers` | Fetch officers/directors for a specific company |

## Security Features

- **Input Sanitization**:	 All user inputs are sanitized using `sanitize_text_field()`
- **Output Escaping**: 		 All outputs are escaped using `esc_html()` and `esc_attr()`
- **Nonce Verification**:	 AJAX requests are protected with WordPress nonces
- **Capability Checks**:	 Admin pages require `manage_options` capability
- **API Key Protection**:	 Key is masked when displayed publicly via shortcode

## Shortcode Usage

Display the configured API key (masked for security):

```
[companies_house_setting]
```

With a custom label:

```
[companies_house_setting label="API Status:"]
```

## Troubleshooting

### Companies not appearing when typing
- Verify the API key is correctly configured in Settings
- Check browser console for JavaScript errors
- Ensure the input field has the `gf-company-search` CSS class

### Directors not loading
- Confirm a company was properly selected
- Check browser console for AJAX errors
- Verify the dropdown has the `gf-directors-dropdown` CSS class

### Auto-populated fields not appearing
- Ensure fields have the correct CSS classes (`gf-company-registration` and `gf-company-incorporation`)
- Check that a company was successfully selected from the dropdown

### Settings page not appearing
- Ensure the plugin is activated
- Check that you have `manage_options` capability (Administrator role)

## Form Styling

The plugin includes basic styling for the form:

- Form is centered on the page with a subtle grey background (#f5f5f5)
- Input fields have white backgrounds
- Proper spacing and padding for readability
- Readonly fields are visually indicated
- Additional custom CSS can be applied through WordPress Customizer (Appearance → Customize → Additional CSS)

## Changelog

### 1.0.0
- Initial release
- Companies House API integration
- Gravity Forms field population
- Auto-populating readonly fields for company registration number and incorporation date
- Director filtering (active directors only)
- Auto-selection when only one director is available
- Admin settings page with Settings API
- Front-end shortcode for displaying settings
- Full documentation

## Author

Marcel Brown

## License

GPL-2.0+