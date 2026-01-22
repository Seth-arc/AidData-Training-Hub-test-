/**
 * Admin Tutorial List JavaScript
 *
 * Handles quick edit functionality for tutorial posts in the admin list table.
 * Populates quick edit fields with existing tutorial meta data when quick edit is activated.
 *
 * @package AidData_LMS
 * @subpackage Admin/JavaScript
 * @since 2.0.0
 */

(function($) {
	'use strict';

	/**
	 * Tutorial List Quick Edit Handler
	 */
	const TutorialListQuickEdit = {
		/**
		 * Initialize quick edit functionality
		 */
		init: function() {
			// Clone the WordPress quick edit function
			const wpInlineEdit = inlineEditPost.edit;

			// Override the function
			inlineEditPost.edit = function(id) {
				// Call the original WP edit function
				wpInlineEdit.apply(this, arguments);

				// Get the post ID
				let postId = 0;
				if (typeof(id) === 'object') {
					postId = parseInt(this.getId(id));
				}

				if (postId > 0) {
					// Get the edit row
					const editRow = $('#edit-' + postId);
					const postRow = $('#post-' + postId);

					// Populate duration
					const duration = $('.column-duration', postRow).text();
					$('input[name="tutorial_duration"]', editRow).val(duration);

					// Populate enrollment limit
					const enrollmentLimit = $('.enrollment-limit', postRow).attr('data-value');
					if (enrollmentLimit) {
						$('input[name="tutorial_enrollment_limit"]', editRow).val(enrollmentLimit);
					}

					// Populate allow enrollment checkbox
					const allowEnrollment = $('.allow-enrollment', postRow).hasClass('enabled');
					$('input[name="tutorial_allow_enrollment"]', editRow).prop('checked', allowEnrollment);

					// Populate show in catalog checkbox
					const showInCatalog = $('.show-in-catalog', postRow).hasClass('enabled');
					$('input[name="tutorial_show_in_catalog"]', editRow).prop('checked', showInCatalog);
				}
			};
		}
	};

	/**
	 * Tutorial List Table Enhancements
	 */
	const TutorialListEnhancements = {
		/**
		 * Initialize enhancements
		 */
		init: function() {
			this.addColumnToggles();
			this.enhanceFilters();
			this.addBulkActionConfirmations();
		},

		/**
		 * Add data attributes to columns for quick edit
		 */
		addColumnToggles: function() {
			// Add data attributes to make meta data accessible
			$('.column-steps').each(function() {
				const stepCount = $(this).find('.step-count').text();
				$(this).closest('tr').attr('data-step-count', stepCount);
			});

			$('.column-enrollments').each(function() {
				const enrollments = $(this).find('a').text();
				$(this).closest('tr').attr('data-enrollments', enrollments);
			});

			$('.column-completion_rate').each(function() {
				const rate = $(this).find('.completion-rate').text();
				$(this).closest('tr').attr('data-completion-rate', rate);
			});
		},

		/**
		 * Enhance admin filters with better UX
		 */
		enhanceFilters: function() {
			// Add clear filters button
			const $filters = $('.tablenav.top .actions');
			if ($filters.find('select').length > 0 && !$filters.find('.clear-filters').length) {
				$filters.append('<button type="button" class="button clear-filters">' + 
					'<span class="dashicons dashicons-no-alt"></span> Clear Filters</button>');
			}

			// Handle clear filters click
			$('.clear-filters').on('click', function() {
				$('.tablenav.top .actions select').val('');
				$('#posts-filter').submit();
			});

			// Show filter count
			const activeFilters = $('.tablenav.top .actions select').filter(function() {
				return $(this).val() !== '';
			}).length;

			if (activeFilters > 0) {
				$('.subsubsub').prepend(
					'<li class="active-filters">' +
					'<span class="count">(' + activeFilters + ' filter' + (activeFilters > 1 ? 's' : '') + ' active)</span>' +
					'</li>'
				);
			}
		},

		/**
		 * Add confirmations for bulk actions
		 */
		addBulkActionConfirmations: function() {
			// Intercept bulk action form submission
			$('#posts-filter').on('submit', function(e) {
				const action = $('#bulk-action-selector-top').val();
				
				if (action === '-1') {
					return true; // No action selected
				}

				const selectedPosts = $('input[name="post[]"]:checked').length;
				
				if (selectedPosts === 0) {
					e.preventDefault();
					alert('Please select at least one tutorial.');
					return false;
				}

				let confirmMessage = '';

				switch (action) {
					case 'duplicate':
						confirmMessage = 'Are you sure you want to duplicate ' + selectedPosts + ' tutorial(s)? ' +
							'Duplicates will be created as drafts.';
						break;

					case 'export_data':
						confirmMessage = 'Export data for ' + selectedPosts + ' tutorial(s) to CSV?';
						break;

					case 'toggle_enrollment':
						confirmMessage = 'Toggle enrollment status for ' + selectedPosts + ' tutorial(s)?';
						break;

					case 'trash':
						confirmMessage = 'Move ' + selectedPosts + ' tutorial(s) to trash?';
						break;

					case 'delete':
						confirmMessage = 'PERMANENTLY DELETE ' + selectedPosts + ' tutorial(s)? ' +
							'This action cannot be undone!';
						break;

					default:
						return true; // Allow other actions
				}

				if (confirmMessage && !confirm(confirmMessage)) {
					e.preventDefault();
					return false;
				}
			});
		}
	};

	/**
	 * Column Sorting Enhancements
	 */
	const ColumnSorting = {
		/**
		 * Initialize sorting enhancements
		 */
		init: function() {
			this.addSortIndicators();
		},

		/**
		 * Add visual sort indicators
		 */
		addSortIndicators: function() {
			const $sortableHeaders = $('.wp-list-table thead th.sortable, .wp-list-table thead th.sorted');

			$sortableHeaders.each(function() {
				const $header = $(this);
				const $link = $header.find('a');

				if ($link.length === 0) {
					return;
				}

				// Add aria labels for accessibility
				if ($header.hasClass('asc')) {
					$link.attr('aria-label', $link.text() + ' - sorted ascending');
				} else if ($header.hasClass('desc')) {
					$link.attr('aria-label', $link.text() + ' - sorted descending');
				} else {
					$link.attr('aria-label', 'Sort by ' + $link.text());
				}
			});
		}
	};

	/**
	 * Row Actions Enhancements
	 */
	const RowActions = {
		/**
		 * Initialize row actions enhancements
		 */
		init: function() {
			this.addViewEnrollments();
			this.enhanceRowHover();
		},

		/**
		 * Add "View Enrollments" link to row actions
		 */
		addViewEnrollments: function() {
			$('.wp-list-table tbody tr').each(function() {
				const $row = $(this);
				const postId = $row.attr('id').replace('post-', '');
				const $rowActions = $row.find('.row-actions');

				// Add view enrollments link
				if ($rowActions.length > 0 && !$rowActions.find('.view-enrollments').length) {
					const enrollmentsUrl = 'admin.php?page=aiddata-lms-enrollments&tutorial_id=' + postId;
					const enrollmentLink = '<span class="view-enrollments">' +
						'<a href="' + enrollmentsUrl + '">' +
						'View Enrollments' +
						'</a> | </span>';

					$rowActions.prepend(enrollmentLink);
				}
			});
		},

		/**
		 * Enhance row hover states
		 */
		enhanceRowHover: function() {
			$('.wp-list-table tbody tr').hover(
				function() {
					$(this).find('.row-actions').addClass('visible');
				},
				function() {
					$(this).find('.row-actions').removeClass('visible');
				}
			);
		}
	};

	/**
	 * Responsive Table Handling
	 */
	const ResponsiveTable = {
		/**
		 * Initialize responsive handling
		 */
		init: function() {
			this.handleMobileView();
			$(window).on('resize', this.handleMobileView.bind(this));
		},

		/**
		 * Handle mobile view adjustments
		 */
		handleMobileView: function() {
			const isMobile = $(window).width() < 782; // WordPress mobile breakpoint

			if (isMobile) {
				// Hide less important columns on mobile
				$('.column-active, .column-completion_rate').hide();
			} else {
				$('.column-active, .column-completion_rate').show();
			}
		}
	};

	/**
	 * Initialize all functionality on document ready
	 */
	$(document).ready(function() {
		// Only run on tutorial edit screen
		if ($('body').hasClass('edit-php') && $('body').hasClass('post-type-aiddata_tutorial')) {
			TutorialListQuickEdit.init();
			TutorialListEnhancements.init();
			ColumnSorting.init();
			RowActions.init();
			ResponsiveTable.init();
		}
	});

	/**
	 * Re-initialize after AJAX operations
	 */
	$(document).ajaxComplete(function(event, xhr, settings) {
		// Check if it's a quick edit operation
		if (settings.data && settings.data.indexOf('action=inline-save') !== -1) {
			setTimeout(function() {
				TutorialListEnhancements.addColumnToggles();
				RowActions.addViewEnrollments();
			}, 100);
		}
	});

})(jQuery);

