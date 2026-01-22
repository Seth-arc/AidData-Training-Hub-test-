/**
 * Enrollment Frontend JavaScript
 *
 * Handles enrollment interactions, progress updates, and UI management
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/assets/js/frontend
 * @since      2.0.0
 */

(function($) {
	'use strict';
	
	/**
	 * Enrollment Manager
	 *
	 * Main object for handling enrollment-related functionality
	 */
	const EnrollmentManager = {
		
		/**
		 * Initialize enrollment handlers
		 *
		 * @since 2.0.0
		 */
		init: function() {
			this.bindEvents();
			this.checkEnrollmentStatus();
		},
		
		/**
		 * Bind click events
		 *
		 * @since 2.0.0
		 */
		bindEvents: function() {
			$(document).on('click', '.aiddata-lms-enroll-btn', this.handleEnroll.bind(this));
			$(document).on('click', '.aiddata-lms-unenroll-btn', this.handleUnenroll.bind(this));
			$(document).on('click', '.aiddata-lms-continue-btn', this.handleContinue.bind(this));
		},
		
		/**
		 * Handle enrollment click
		 *
		 * @since 2.0.0
		 * @param {Event} e Click event
		 */
		handleEnroll: function(e) {
			e.preventDefault();
			
			const $button = $(e.currentTarget);
			const tutorialId = $button.data('tutorial-id');
			
			if (!tutorialId) {
				this.showError('Invalid tutorial ID');
				return;
			}
			
			this.enrollUser(tutorialId, $button);
		},
		
		/**
		 * Enroll user in tutorial
		 *
		 * @since 2.0.0
		 * @param {number} tutorialId Tutorial ID
		 * @param {jQuery} $button Button element
		 */
		enrollUser: function(tutorialId, $button) {
			// Set loading state
			const originalText = $button.html();
			$button.prop('disabled', true)
			       .html('<span class="spinner-border spinner-border-sm"></span> Enrolling...');
			
			$.ajax({
				url: aiddataLMS.ajaxUrl,
				type: 'POST',
				data: {
					action: 'aiddata_lms_enroll_tutorial',
					tutorial_id: tutorialId,
					nonce: aiddataLMS.enrollmentNonce
				},
				success: (response) => {
					if (response.success) {
						this.showSuccess(response.data.message);
						
						// Update UI
						this.updateEnrollmentUI(tutorialId, true, response.data);
						
						// Redirect if URL provided
						if (response.data.redirect_url) {
							setTimeout(() => {
								window.location.href = response.data.redirect_url;
							}, 1000);
						}
					} else {
						this.showError(response.data.message || 'Enrollment failed');
						$button.prop('disabled', false).html(originalText);
					}
				},
				error: (xhr, status, error) => {
					console.error('Enrollment error:', error);
					this.showError('Network error. Please try again.');
					$button.prop('disabled', false).html(originalText);
				}
			});
		},
		
		/**
		 * Handle unenrollment click
		 *
		 * @since 2.0.0
		 * @param {Event} e Click event
		 */
		handleUnenroll: function(e) {
			e.preventDefault();
			
			const $button = $(e.currentTarget);
			const tutorialId = $button.data('tutorial-id');
			
			if (!tutorialId) {
				this.showError('Invalid tutorial ID');
				return;
			}
			
			// Confirm unenrollment
			if (confirm(aiddataLMS.confirmUnenroll || 'Are you sure you want to unenroll? Your progress will be saved.')) {
				this.unenrollUser(tutorialId, $button);
			}
		},
		
		/**
		 * Unenroll user from tutorial
		 *
		 * @since 2.0.0
		 * @param {number} tutorialId Tutorial ID
		 * @param {jQuery} $button Button element
		 */
		unenrollUser: function(tutorialId, $button) {
			const originalText = $button.html();
			$button.prop('disabled', true)
			       .html('<span class="spinner-border spinner-border-sm"></span> Unenrolling...');
			
			$.ajax({
				url: aiddataLMS.ajaxUrl,
				type: 'POST',
				data: {
					action: 'aiddata_lms_unenroll_tutorial',
					tutorial_id: tutorialId,
					confirm: 'yes',
					nonce: aiddataLMS.enrollmentNonce
				},
				success: (response) => {
					if (response.success) {
						this.showSuccess(response.data.message);
						this.updateEnrollmentUI(tutorialId, false);
					} else {
						this.showError(response.data.message || 'Unenrollment failed');
						$button.prop('disabled', false).html(originalText);
					}
				},
				error: (xhr, status, error) => {
					console.error('Unenrollment error:', error);
					this.showError('Network error. Please try again.');
					$button.prop('disabled', false).html(originalText);
				}
			});
		},
		
		/**
		 * Check current enrollment status
		 *
		 * @since 2.0.0
		 */
		checkEnrollmentStatus: function() {
			const tutorialId = this.getTutorialId();
			
			if (!tutorialId) {
				return;
			}
			
			$.ajax({
				url: aiddataLMS.ajaxUrl,
				type: 'GET',
				data: {
					action: 'aiddata_lms_check_enrollment_status',
					tutorial_id: tutorialId
				},
				success: (response) => {
					if (response.success) {
						this.updateEnrollmentUI(tutorialId, response.data.enrolled, response.data);
					}
				}
			});
		},
		
		/**
		 * Update UI based on enrollment status
		 *
		 * @since 2.0.0
		 * @param {number} tutorialId Tutorial ID
		 * @param {boolean} isEnrolled Is user enrolled
		 * @param {Object} data Response data
		 */
		updateEnrollmentUI: function(tutorialId, isEnrolled, data = {}) {
			const $container = $('.aiddata-lms-enrollment-container[data-tutorial-id="' + tutorialId + '"]');
			
			if (!$container.length) {
				return;
			}
			
			if (isEnrolled) {
				// User is enrolled
				$container.find('.aiddata-lms-enroll-btn').hide();
				$container.find('.aiddata-lms-enrolled-state').show();
				$container.find('.aiddata-lms-continue-btn').show();
				
				// Update progress if available
				if (data.progress) {
					this.updateProgressDisplay(tutorialId, data.progress);
				}
			} else {
				// User is not enrolled
				$container.find('.aiddata-lms-enroll-btn').show();
				$container.find('.aiddata-lms-enrolled-state').hide();
				$container.find('.aiddata-lms-continue-btn').hide();
			}
		},
		
		/**
		 * Update progress display
		 *
		 * @since 2.0.0
		 * @param {number} tutorialId Tutorial ID
		 * @param {Object} progress Progress data
		 */
		updateProgressDisplay: function(tutorialId, progress) {
			const $progress = $('.aiddata-lms-progress[data-tutorial-id="' + tutorialId + '"]');
			
			if (!$progress.length) {
				return;
			}
			
			// Update progress bar
			const percent = parseFloat(progress.percent) || 0;
			$progress.find('.progress-bar')
			        .css('width', percent + '%')
			        .attr('aria-valuenow', percent)
			        .text(Math.round(percent) + '%');
			
			// Update status text
			const statusText = this.getStatusText(progress.status, percent);
			$progress.find('.status-text').text(statusText);
		},
		
		/**
		 * Get status text
		 *
		 * @since 2.0.0
		 * @param {string} status Progress status
		 * @param {number} percent Progress percentage
		 * @return {string} Status text
		 */
		getStatusText: function(status, percent) {
			if (status === 'completed') {
				return 'Completed';
			} else if (status === 'in_progress') {
				return 'In Progress - ' + Math.round(percent) + '%';
			} else {
				return 'Not Started';
			}
		},
		
		/**
		 * Get tutorial ID from page
		 *
		 * @since 2.0.0
		 * @return {number|null} Tutorial ID or null
		 */
		getTutorialId: function() {
			// Try to get from enrollment container
			const $container = $('.aiddata-lms-enrollment-container');
			if ($container.length) {
				return $container.data('tutorial-id');
			}
			
			// Try to get from body class
			const bodyClasses = $('body').attr('class');
			const match = bodyClasses && bodyClasses.match(/postid-(\d+)/);
			if (match && match[1]) {
				return parseInt(match[1]);
			}
			
			return null;
		},
		
		/**
		 * Show success message
		 *
		 * @since 2.0.0
		 * @param {string} message Success message
		 */
		showSuccess: function(message) {
			this.showNotification(message, 'success');
		},
		
		/**
		 * Show error message
		 *
		 * @since 2.0.0
		 * @param {string} message Error message
		 */
		showError: function(message) {
			this.showNotification(message, 'error');
		},
		
		/**
		 * Show notification
		 *
		 * @since 2.0.0
		 * @param {string} message Notification message
		 * @param {string} type Notification type
		 */
		showNotification: function(message, type = 'info') {
			// Create notification element
			const $notification = $('<div>', {
				'class': 'aiddata-lms-notification aiddata-lms-notification-' + type,
				'text': message
			});
			
			// Append to body
			$('body').append($notification);
			
			// Fade in
			setTimeout(() => {
				$notification.addClass('show');
			}, 100);
			
			// Auto-hide after 5 seconds
			setTimeout(() => {
				$notification.removeClass('show');
				setTimeout(() => {
					$notification.remove();
				}, 300);
			}, 5000);
		},
		
		/**
		 * Handle continue button
		 *
		 * @since 2.0.0
		 * @param {Event} e Click event
		 */
		handleContinue: function(e) {
			e.preventDefault();
			
			const $button = $(e.currentTarget);
			const continueUrl = $button.attr('href');
			
			if (continueUrl) {
				window.location.href = continueUrl;
			}
		}
	};
	
	// Initialize on document ready
	$(document).ready(function() {
		EnrollmentManager.init();
	});
	
})(jQuery);

