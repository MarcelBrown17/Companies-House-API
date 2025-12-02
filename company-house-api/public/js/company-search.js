/**
 * Public-facing JavaScript for Company API plugin.
 * Handles company search autocomplete and director dropdown functionality.
 */

(function($) {
	'use strict';

	var selectedCompany = null;

	$(document).ready(function() {
		initCompanySearch();
		initDirectorsDropdown();
	});

	/**
	 * Set up the directors dropdown on page load
	 * Makes it readonly and disabled until a company is selected
	 */
	function initDirectorsDropdown() {
		var $directorsField = $('.gf-directors-dropdown select');

		// Fallback to finding by field ID if class isn't present
		if ($directorsField.length === 0) {
			$directorsField = $('select[id^="input_"][id$="_3"]').first();
		}

		if ($directorsField.length > 0) {
			$directorsField.html('<option value="">Select a company first</option>');
			$directorsField.prop('disabled', true);
		}

		// Make registration number field readonly
		var $registrationField = $('.gf-company-registration input[type="text"]');
		if ($registrationField.length > 0) {
			$registrationField.prop('readonly', true);
		}

		// Make incorporation date field readonly
		var $incorporationField = $('.gf-company-incorporation input[type="text"]');
		if ($incorporationField.length > 0) {
			$incorporationField.prop('readonly', true);
		}
	}

	/**
	 * Set up the company search field with autocomplete
	 * Creates a dropdown that appears below the input as you type
	 */
	function initCompanySearch() {
		var $companyField = $('.gf-company-search input[type="text"]');

		// Fallback to finding by field ID if class isn't present
		if ($companyField.length === 0) {
			$companyField = $('input[id^="input_"][id$="_1"]').first();
		}

		if ($companyField.length === 0) {
			return;
		}

		// Create the dropdown element
		var $dropdown = $('<div class="company-api-dropdown" style="position:absolute;background:#fff;border:1px solid #ccc;max-height:200px;overflow-y:auto;width:100%;z-index:9999;display:none;"></div>');
		$companyField.parent().css('position', 'relative').append($dropdown);

		var debounceTimer;

		// Search as user types (there is a 300ms delay to avoid too many requests)
		$companyField.on('input', function() {
			var searchTerm = $(this).val();
			clearTimeout(debounceTimer);

			if (searchTerm.length < 2) {
				$dropdown.hide().empty();
				return;
			}

			debounceTimer = setTimeout(function() {
				searchCompanies(searchTerm, $dropdown, $companyField);
			}, 300);
		});

		// Hide dropdown when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.company-api-dropdown').length && !$(e.target).is($companyField)) {
				$dropdown.hide();
			}
		});
	}

	/**
	 * Send AJAX request to search for companies
	 */
	function searchCompanies(searchTerm, $dropdown, $companyField) {
		$.ajax({
			url: chl_ajax.ajax_url,
			type: 'POST',
			data: {
				action: 'chl_search_companies',
				nonce: chl_ajax.nonce,
				search: searchTerm
			},
			beforeSend: function() {
				$dropdown.html('<div style="padding:10px;">Searching...</div>').show();
			},
			success: function(response) {
				if (response.success && response.data.length > 0) {
					renderCompanyDropdown(response.data, $dropdown, $companyField);
				} else {
					$dropdown.html('<div style="padding:10px;">No companies found</div>').show();
				}
			},
			error: function() {
				$dropdown.html('<div style="padding:10px;">Search failed. Please try again.</div>').show();
			}
		});
	}

	/**
	 * Display the list of companies in the dropdown
	 */
	function renderCompanyDropdown(companies, $dropdown, $companyField) {
		var html = '<ul style="list-style:none;margin:0;padding:0;">';

		// Store company data in data attributes for later use
		$.each(companies, function(index, company) {
			html += '<li class="company-api-item" ' +
					'data-number="' + company.company_number + '" ' +
					'data-name="' + company.company_name + '" ' +
					'data-incorporation="' + (company.date_of_creation || '') + '" ' +
					'style="padding:10px;cursor:pointer;border-bottom:1px solid #eee;">';
			html += company.company_name;
			html += '</li>';
		});

		html += '</ul>';
		$dropdown.html(html).show();

		// Handle company selection
		$dropdown.find('.company-api-item').on('click', function() {
			var companyNumber = $(this).data('number');
			var companyName   = $(this).data('name');
			var companyIncorporation = $(this).data('incorporation');

			// Populate the company name field
			$companyField.val(companyName);

			// Populate and lock the registration number field
			var $registrationField = $('.gf-company-registration input[type="text"]');
			if ($registrationField.length > 0) {
				$registrationField.val(companyNumber);
				$registrationField.prop('readonly', true);
			}

			// Populate and lock the incorporation date field
			var $incorporationField = $('.gf-company-incorporation input[type="text"]');
			if ($incorporationField.length > 0) {
				$incorporationField.val(formatDate(companyIncorporation));
				$incorporationField.prop('readonly', true);
			}

			selectedCompany = {
				number: companyNumber,
				name: companyName
			};

			$dropdown.hide();
			fetchDirectors(companyNumber);
		});

		// Add hover effect to dropdown items
		$dropdown.find('.company-api-item').on('mouseenter', function() {
			$(this).css('background-color', '#f5f5f5');
		}).on('mouseleave', function() {
			$(this).css('background-color', '#fff');
		});
	}

	/**
	 * Fetch directors for the selected company
	 */
	function fetchDirectors(companyNumber) {
		var $directorsField = $('.gf-directors-dropdown select');

		if ($directorsField.length === 0) {
			$directorsField = $('select[id^="input_"][id$="_3"]').first();
		}

		if ($directorsField.length === 0) {
			return;
		}

		$.ajax({
			url: chl_ajax.ajax_url,
			type: 'POST',
			data: {
				action: 'chl_get_directors',
				nonce: chl_ajax.nonce,
				company_number: companyNumber
			},
			beforeSend: function() {
				$directorsField.html('<option value="">Loading directors...</option>');
			},
			success: function(response) {
				if (response.success && response.data.length > 0) {
					renderDirectorsDropdown(response.data, $directorsField);
					$directorsField.prop('disabled', false);
				} else {
					$directorsField.html('<option value="">No directors found</option>');
					$directorsField.prop('disabled', true);
				}
			},
			error: function() {
				$directorsField.html('<option value="">Failed to load directors</option>');
				$directorsField.prop('disabled', true);
			}
		});
	}

	/**
	 * Populate the directors dropdown
	 * Filters out resigned directors and auto-selects if only one active director
	 */
	function renderDirectorsDropdown(directors, $directorsField) {
		$directorsField.empty();

		// Filter to only active directors
		var activeDirectors = [];
		
		$.each(directors, function(index, director) {
			if (!director.resigned_on || director.resigned_on === '' || director.resigned_on === null) {
				activeDirectors.push(director);
			}
		});

		if (activeDirectors.length === 0) {
			$directorsField.append('<option value="">No directors found</option>');
		} else if (activeDirectors.length === 1) {
			// Auto-select if only one director
			var option = $('<option></option>')
				.val(activeDirectors[0].name)
				.text(activeDirectors[0].name + ' (' + activeDirectors[0].role + ')');
			$directorsField.append(option);
			$directorsField.val(activeDirectors[0].name);
		} else {
			// Show placeholder with count if multiple directors
			$directorsField.append('<option value="">' + activeDirectors.length + ' directors found</option>');
			$.each(activeDirectors, function(index, director) {
				var option = $('<option></option>')
					.val(director.name)
					.text(director.name + ' (' + director.role + ')');
				$directorsField.append(option);
			});
		}

		$directorsField.trigger('change');
	}

	/**
	 * Format date from YYYY-MM-DD to "1 January 2020" format
	 */
	function formatDate(dateString) {
		if (!dateString || dateString === '') {
			return '';
		}

		var date = new Date(dateString);
		var options = { year: 'numeric', month: 'long', day: 'numeric' };
		return date.toLocaleDateString('en-GB', options);
	}

})(jQuery);