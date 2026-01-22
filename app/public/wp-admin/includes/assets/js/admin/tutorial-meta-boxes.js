/**
 * Tutorial Meta Boxes JavaScript
 *
 * Handles client-side functionality for tutorial meta boxes including:
 * - Character counter for short description
 * - Date picker for enrollment deadline
 * - Prerequisites AJAX search
 * - Learning outcomes repeater field
 * - Form validation
 *
 * @package AidData_LMS
 * @since 2.0.0
 */

(function($) {
  'use strict';

  const TutorialMetaBoxes = {
    /**
     * Initialize all components
     */
    init: function() {
      this.initCharacterCounter();
      this.initDatePicker();
      this.initPrerequisitesSearch();
      this.initLearningOutcomes();
      this.initFormValidation();
    },

    /**
     * Initialize character counter for short description
     */
    initCharacterCounter: function() {
      const $shortDesc = $('#tutorial_short_description');
      const $charCount = $('#char-count');

      if ($shortDesc.length && $charCount.length) {
        $shortDesc.on('input', function() {
          const length = $(this).val().length;
          $charCount.text(length);

          if (length > 250) {
            $charCount.addClass('over-limit');
            $charCount.parent().addClass('error');
          } else {
            $charCount.removeClass('over-limit');
            $charCount.parent().removeClass('error');
          }
        });

        // Trigger on load
        $shortDesc.trigger('input');
      }
    },

    /**
     * Initialize jQuery UI date picker
     */
    initDatePicker: function() {
      const $datePicker = $('.aiddata-date-picker');

      if ($datePicker.length && typeof $.fn.datepicker !== 'undefined') {
        $datePicker.datepicker({
          dateFormat: 'yy-mm-dd',
          minDate: 0,
          changeMonth: true,
          changeYear: true
        });
      }
    },

    /**
     * Initialize prerequisites search and selection
     */
    initPrerequisitesSearch: function() {
      const $searchInput = $('#prerequisites-search');
      const $resultsDiv = $('#prerequisites-results');
      const $selectedDiv = $('#selected-prerequisites');
      let searchTimeout = null;

      if ($searchInput.length) {
        // Search on input
        $searchInput.on('input', function() {
          clearTimeout(searchTimeout);
          const query = $(this).val().trim();

          if (query.length < 2) {
            $resultsDiv.hide().empty();
            return;
          }

          searchTimeout = setTimeout(function() {
            TutorialMetaBoxes.searchTutorials(query, $resultsDiv);
          }, 300);
        });

        // Add prerequisite on click
        $resultsDiv.on('click', '.tutorial-result', function() {
          const tutorialId = $(this).data('id');
          const tutorialTitle = $(this).data('title');

          TutorialMetaBoxes.addPrerequisite(tutorialId, tutorialTitle, $selectedDiv);
          $searchInput.val('');
          $resultsDiv.hide().empty();
        });

        // Remove prerequisite
        $selectedDiv.on('click', '.remove-prerequisite', function() {
          $(this).closest('.prerequisite-item').remove();
        });

        // Make prerequisites sortable
        if (typeof $.fn.sortable !== 'undefined') {
          $selectedDiv.sortable({
            handle: '.dashicons-menu',
            placeholder: 'prerequisite-placeholder',
            axis: 'y'
          });
        }
      }
    },

    /**
     * Search tutorials via AJAX
     *
     * @param {string} query Search query
     * @param {jQuery} $resultsDiv Results container
     */
    searchTutorials: function(query, $resultsDiv) {
      $.ajax({
        url: ajaxurl,
        type: 'GET',
        data: {
          action: 'aiddata_search_tutorials',
          query: query,
          exclude: TutorialMetaBoxes.getSelectedPrerequisites()
        },
        beforeSend: function() {
          $resultsDiv.html('<div class="loading">' + aiddataTutorialMeta.strings.loading + '</div>').show();
        },
        success: function(response) {
          if (response.success && response.data.tutorials.length > 0) {
            let html = '';
            response.data.tutorials.forEach(function(tutorial) {
              html += '<div class="tutorial-result" data-id="' + tutorial.id + '" data-title="' + tutorial.title + '">';
              html += '<strong>' + tutorial.title + '</strong>';
              if (tutorial.excerpt) {
                html += '<span class="tutorial-excerpt">' + tutorial.excerpt + '</span>';
              }
              html += '</div>';
            });
            $resultsDiv.html(html).show();
          } else {
            $resultsDiv.html('<div class="no-results">' + aiddataTutorialMeta.strings.noResults + '</div>').show();
          }
        },
        error: function() {
          $resultsDiv.html('<div class="error">' + aiddataTutorialMeta.strings.searchError + '</div>').show();
        }
      });
    },

    /**
     * Get array of selected prerequisite IDs
     *
     * @return {Array} Array of prerequisite IDs
     */
    getSelectedPrerequisites: function() {
      const ids = [];
      $('.prerequisite-item').each(function() {
        ids.push($(this).data('id'));
      });
      return ids;
    },

    /**
     * Add a prerequisite to the selected list
     *
     * @param {number} id Tutorial ID
     * @param {string} title Tutorial title
     * @param {jQuery} $container Container element
     */
    addPrerequisite: function(id, title, $container) {
      // Check if already added
      if ($container.find('.prerequisite-item[data-id="' + id + '"]').length > 0) {
        return;
      }

      const html = '<div class="prerequisite-item" data-id="' + id + '">' +
        '<span class="dashicons dashicons-menu"></span>' +
        '<span class="prerequisite-title">' + title + '</span>' +
        '<button type="button" class="button-link remove-prerequisite">' +
        '<span class="dashicons dashicons-no-alt"></span>' +
        '</button>' +
        '<input type="hidden" name="tutorial_prerequisites[]" value="' + id + '">' +
        '</div>';

      $container.append(html);
    },

    /**
     * Initialize learning outcomes repeater
     */
    initLearningOutcomes: function() {
      const $list = $('#learning-outcomes-list');
      const $addButton = $('#add-outcome');

      if ($addButton.length) {
        // Add outcome
        $addButton.on('click', function() {
          const html = '<div class="outcome-item">' +
            '<span class="dashicons dashicons-menu"></span>' +
            '<input type="text" name="tutorial_outcomes[]" value="" class="regular-text" ' +
            'placeholder="' + aiddataTutorialMeta.strings.outcomePlaceholder + '">' +
            '<button type="button" class="button-link remove-outcome">' +
            '<span class="dashicons dashicons-no-alt"></span>' +
            '</button>' +
            '</div>';

          $list.append(html);
        });

        // Remove outcome
        $list.on('click', '.remove-outcome', function() {
          const $item = $(this).closest('.outcome-item');

          // Don't remove if it's the only one
          if ($list.find('.outcome-item').length > 1) {
            $item.remove();
          } else {
            $item.find('input').val('');
          }
        });

        // Make outcomes sortable
        if (typeof $.fn.sortable !== 'undefined') {
          $list.sortable({
            handle: '.dashicons-menu',
            placeholder: 'outcome-placeholder',
            axis: 'y'
          });
        }
      }
    },

    /**
     * Initialize form validation
     */
    initFormValidation: function() {
      $('#post').on('submit', function(e) {
        const errors = [];

        // Validate short description
        const shortDesc = $('#tutorial_short_description').val().trim();
        if (shortDesc.length === 0) {
          errors.push(aiddataTutorialMeta.strings.errorShortDesc);
        } else if (shortDesc.length > 250) {
          errors.push(aiddataTutorialMeta.strings.errorShortDescLength);
        }

        // Validate duration
        const duration = parseInt($('#tutorial_duration').val(), 10);
        if (!duration || duration <= 0) {
          errors.push(aiddataTutorialMeta.strings.errorDuration);
        }

        // Validate enrollment deadline date format
        const deadline = $('#tutorial_enrollment_deadline').val().trim();
        if (deadline && !TutorialMetaBoxes.isValidDate(deadline)) {
          errors.push(aiddataTutorialMeta.strings.errorDateFormat);
        }

        // Display errors if any
        if (errors.length > 0) {
          e.preventDefault();
          alert(errors.join('\n'));
          return false;
        }
      });
    },

    /**
     * Validate date format (YYYY-MM-DD)
     *
     * @param {string} dateString Date string to validate
     * @return {boolean} True if valid, false otherwise
     */
    isValidDate: function(dateString) {
      const regex = /^\d{4}-\d{2}-\d{2}$/;
      if (!regex.test(dateString)) {
        return false;
      }

      const date = new Date(dateString);
      const timestamp = date.getTime();

      if (typeof timestamp !== 'number' || Number.isNaN(timestamp)) {
        return false;
      }

      return dateString === date.toISOString().split('T')[0];
    }
  };

  // Initialize on document ready
  $(document).ready(function() {
    TutorialMetaBoxes.init();
  });

})(jQuery);

