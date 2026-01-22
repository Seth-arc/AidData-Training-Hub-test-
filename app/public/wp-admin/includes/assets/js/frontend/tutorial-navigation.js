/**
 * Tutorial Navigation
 * 
 * Handles step navigation, progress tracking, and user interactions
 * in the active tutorial interface.
 * 
 * @package AidData_LMS
 * @since 1.0.0
 */

(function($) {
	'use strict';
	
	const TutorialNavigation = {
		tutorialId: null,
		currentStepIndex: 0,
		totalSteps: 0,
		timeTrackingInterval: null,
		
		/**
		 * Initialize tutorial navigation
		 */
		init: function() {
			this.tutorialId = $('.active-tutorial-container').data('tutorial-id');
			if (!this.tutorialId) {
				return;
			}
			
			this.currentStepIndex = parseInt($('.step-content').data('step-index')) || 0;
			this.totalSteps = $('.steps-list .step-item').length;
			
			this.bindEvents();
			this.initVideoTracking();
			this.startTimeTracking();
			this.updateNavigationButtons();
		},
		
		/**
		 * Bind event handlers
		 */
		bindEvents: function() {
			// Step navigation from sidebar
			$(document).on('click', '.step-link:not([disabled])', (e) => {
				const stepIndex = parseInt($(e.currentTarget).data('step-index'));
				this.navigateToStep(stepIndex);
			});
			
			// Previous/Next buttons
			$('.nav-previous, .nav-previous-mob').on('click', () => {
				if (this.currentStepIndex > 0) {
					this.navigateToStep(this.currentStepIndex - 1);
				}
			});
			
			$('.nav-next, .nav-next-mob').on('click', () => {
				if (this.currentStepIndex < this.totalSteps - 1) {
					this.navigateToStep(this.currentStepIndex + 1);
				}
			});
			
			// Mark as complete
			$(document).on('click', '.mark-complete', () => {
				this.markStepComplete(this.currentStepIndex);
			});
			
			// Finish tutorial
			$(document).on('click', '.finish-tutorial', () => {
				this.finishTutorial();
			});
			
			// Sidebar toggle
			$('#sidebar-toggle, .sidebar-toggle-mob').on('click', () => {
				this.toggleSidebar();
			});
			
			// Close sidebar when clicking outside on mobile
			$(document).on('click', (e) => {
				if ($(window).width() <= 768) {
					if (!$(e.target).closest('.tutorial-sidebar, .sidebar-toggle-mob').length) {
						if ($('#tutorial-sidebar').hasClass('mobile-open')) {
							this.toggleSidebar();
						}
					}
				}
			});
			
			// Keyboard navigation
			$(document).on('keydown', (e) => {
				// Only handle if not typing in input
				if ($(e.target).is('input, textarea')) {
					return;
				}
				
				if (e.ctrlKey || e.metaKey) {
					if (e.key === 'ArrowLeft') {
						e.preventDefault();
						$('.nav-previous').click();
					} else if (e.key === 'ArrowRight') {
						e.preventDefault();
						$('.nav-next').click();
					}
				}
			});
			
			// Handle browser back/forward
			$(window).on('popstate', () => {
				const urlParams = new URLSearchParams(window.location.search);
				const step = urlParams.get('step');
				if (step !== null) {
					this.navigateToStep(parseInt(step), false);
				}
			});
		},
		
		/**
		 * Navigate to specific step
		 * 
		 * @param {number} stepIndex Step index to navigate to
		 * @param {boolean} updateHistory Whether to update browser history
		 */
		navigateToStep: function(stepIndex, updateHistory = true) {
			if (stepIndex < 0 || stepIndex >= this.totalSteps) {
				return;
			}
			
			// Check if step is accessible
			const $stepItem = $(`.steps-list .step-item[data-step-index="${stepIndex}"]`);
			if ($stepItem.hasClass('locked')) {
				this.showError('This step is locked. Complete previous steps first.');
				return;
			}
			
			// Show loading
			this.showLoading();
			
			// AJAX load step content
			$.ajax({
				url: aiddataLMS.ajaxUrl,
				type: 'POST',
				data: {
					action: 'aiddata_lms_load_step',
					tutorial_id: this.tutorialId,
					step_index: stepIndex,
					nonce: aiddataLMS.progressNonce
				},
				success: (response) => {
					if (response.success) {
						this.loadStepContent(response.data.html, stepIndex);
						if (updateHistory) {
							this.updateURL(stepIndex);
						}
					} else {
						this.showError(response.data.message || 'Failed to load step.');
					}
				},
				error: () => {
					this.showError('Failed to load step. Please try again.');
				},
				complete: () => {
					this.hideLoading();
				}
			});
		},
		
		/**
		 * Load step content into DOM
		 * 
		 * @param {string} html Step HTML content
		 * @param {number} stepIndex Step index
		 */
		loadStepContent: function(html, stepIndex) {
			const $content = $('#step-content .step-content');
			
			// Fade out
			$content.fadeOut(200, () => {
				$content.html(html);
				$content.data('step-index', stepIndex);
				
				// Update current step index
				TutorialNavigation.currentStepIndex = stepIndex;
				
				// Update sidebar
				TutorialNavigation.updateSidebarState();
				
				// Update navigation buttons
				TutorialNavigation.updateNavigationButtons();
				
				// Re-initialize components
				TutorialNavigation.initVideoTracking();
				
				// Fade in
				$content.fadeIn(200);
				
				// Scroll to top
				window.scrollTo({ top: 0, behavior: 'smooth' });
				
				// Close sidebar on mobile after navigation
				if ($(window).width() <= 768) {
					$('#tutorial-sidebar').removeClass('mobile-open');
					$('body').removeClass('sidebar-open');
				}
			});
		},
		
		/**
		 * Update sidebar navigation state
		 */
		updateSidebarState: function() {
			$('.steps-list .step-item').removeClass('current');
			$(`.steps-list .step-item[data-step-index="${this.currentStepIndex}"]`).addClass('current');
			
			// Update progress text
			$('.progress-text').text(`Step ${this.currentStepIndex + 1} of ${this.totalSteps}`);
		},
		
		/**
		 * Update navigation button visibility
		 */
		updateNavigationButtons: function() {
			// Show/hide previous button
			if (this.currentStepIndex === 0) {
				$('.nav-previous, .nav-previous-mob').hide();
			} else {
				$('.nav-previous, .nav-previous-mob').show();
			}
			
			// Show/hide next vs finish button
			if (this.currentStepIndex === this.totalSteps - 1) {
				$('.nav-next, .nav-next-mob').hide();
				$('.finish-tutorial').show();
			} else {
				$('.nav-next, .nav-next-mob').show();
				$('.finish-tutorial').hide();
			}
		},
		
		/**
		 * Mark current step as complete
		 * 
		 * @param {number} stepIndex Step index to mark complete
		 */
		markStepComplete: function(stepIndex) {
			const $button = $('.mark-complete');
			$button.prop('disabled', true);
			
			$.ajax({
				url: aiddataLMS.ajaxUrl,
				type: 'POST',
				data: {
					action: 'aiddata_lms_update_step_progress',
					tutorial_id: this.tutorialId,
					step_index: stepIndex,
					nonce: aiddataLMS.progressNonce
				},
				success: (response) => {
					if (response.success) {
						// Mark step as completed in sidebar
						$(`.steps-list .step-item[data-step-index="${stepIndex}"]`).addClass('completed');
						
						// Unlock next step
						const nextIndex = stepIndex + 1;
						if (nextIndex < this.totalSteps) {
							$(`.steps-list .step-item[data-step-index="${nextIndex}"]`).removeClass('locked');
							$(`.steps-list .step-item[data-step-index="${nextIndex}"] .step-link`).prop('disabled', false);
						}
						
						// Update progress bar
						if (response.data.progress && response.data.progress.percent !== undefined) {
							const progressPercent = response.data.progress.percent;
							$('.progress-fill').css('width', progressPercent + '%');
							$('.progress-percent').text(Math.round(progressPercent) + '%');
						}
						
						// Show success message
						this.showSuccess('Step marked as complete!');
						
						// Hide the complete button
						$button.fadeOut();
						
						// Auto-advance to next step after delay
						setTimeout(() => {
							if (this.currentStepIndex < this.totalSteps - 1) {
								this.navigateToStep(this.currentStepIndex + 1);
							}
						}, 1500);
					} else {
						this.showError(response.data.message || 'Failed to update progress.');
						$button.prop('disabled', false);
					}
				},
				error: () => {
					this.showError('Failed to update progress. Please try again.');
					$button.prop('disabled', false);
				}
			});
		},
		
		/**
		 * Finish tutorial
		 */
		finishTutorial: function() {
			if (confirm(aiddataLMS.strings.confirmFinish || 'Mark this tutorial as complete?')) {
				// Mark last step as complete
				this.markStepComplete(this.currentStepIndex);
				
				// Redirect after delay
				setTimeout(() => {
					const url = new URL(window.location.href);
					url.searchParams.set('finished', '1');
					url.searchParams.delete('action');
					url.searchParams.delete('step');
					window.location.href = url.toString();
				}, 2000);
			}
		},
		
		/**
		 * Toggle sidebar visibility
		 */
		toggleSidebar: function() {
			if ($(window).width() <= 768) {
				// Mobile: slide overlay
				$('#tutorial-sidebar').toggleClass('mobile-open');
				$('body').toggleClass('sidebar-open');
			} else {
				// Desktop: collapse
				$('#tutorial-sidebar').toggleClass('collapsed');
				$('.tutorial-main-content').toggleClass('sidebar-collapsed');
			}
		},
		
		/**
		 * Initialize video tracking (placeholder for Phase 3)
		 */
		initVideoTracking: function() {
			const $videoContainer = $('.video-container');
			if ($videoContainer.length) {
				const platform = $videoContainer.data('platform');
				const videoUrl = $videoContainer.data('video-url');
				
				// Video player initialization will be implemented in Phase 3
				// For now, just ensure the video element is visible
				console.log('Video step detected:', platform, videoUrl);
			}
		},
		
		/**
		 * Start time tracking
		 */
		startTimeTracking: function() {
			// Track time spent every 30 seconds
			this.timeTrackingInterval = setInterval(() => {
				$.ajax({
					url: aiddataLMS.ajaxUrl,
					type: 'POST',
					data: {
						action: 'aiddata_lms_update_time_spent',
						tutorial_id: this.tutorialId,
						seconds: 30,
						nonce: aiddataLMS.progressNonce
					},
					// Silent - don't show errors for time tracking
					error: () => {}
				});
			}, 30000);
		},
		
		/**
		 * Update browser URL with current step
		 * 
		 * @param {number} stepIndex Step index
		 */
		updateURL: function(stepIndex) {
			const url = new URL(window.location);
			url.searchParams.set('step', stepIndex);
			window.history.pushState({ step: stepIndex }, '', url);
		},
		
		/**
		 * Show loading overlay
		 */
		showLoading: function() {
			if (!$('.loading-overlay').length) {
				$('#step-content').append('<div class="loading-overlay"><div class="spinner"></div></div>');
			}
		},
		
		/**
		 * Hide loading overlay
		 */
		hideLoading: function() {
			$('.loading-overlay').fadeOut(200, function() {
				$(this).remove();
			});
		},
		
		/**
		 * Show success notification
		 * 
		 * @param {string} message Success message
		 */
		showSuccess: function(message) {
			this.showNotification(message, 'success');
		},
		
		/**
		 * Show error notification
		 * 
		 * @param {string} message Error message
		 */
		showError: function(message) {
			this.showNotification(message, 'error');
		},
		
		/**
		 * Show notification
		 * 
		 * @param {string} message Notification message
		 * @param {string} type Notification type (success|error|info)
		 */
		showNotification: function(message, type = 'info') {
			const $notification = $(`
				<div class="tutorial-notification ${type}" role="alert">
					<span class="notification-icon dashicons ${this.getNotificationIcon(type)}"></span>
					<span class="notification-message">${message}</span>
					<button class="notification-close" aria-label="Close">&times;</button>
				</div>
			`);
			
			$('body').append($notification);
			
			// Show notification
			setTimeout(() => {
				$notification.addClass('show');
			}, 10);
			
			// Close button
			$notification.find('.notification-close').on('click', function() {
				$(this).closest('.tutorial-notification').removeClass('show');
				setTimeout(() => {
					$(this).closest('.tutorial-notification').remove();
				}, 300);
			});
			
			// Auto-hide after 5 seconds
			setTimeout(() => {
				$notification.removeClass('show');
				setTimeout(() => {
					$notification.remove();
				}, 300);
			}, 5000);
		},
		
		/**
		 * Get notification icon class
		 * 
		 * @param {string} type Notification type
		 * @return {string} Dashicons class
		 */
		getNotificationIcon: function(type) {
			const icons = {
				success: 'dashicons-yes-alt',
				error: 'dashicons-warning',
				info: 'dashicons-info'
			};
			return icons[type] || icons.info;
		},
		
		/**
		 * Cleanup on page unload
		 */
		destroy: function() {
			if (this.timeTrackingInterval) {
				clearInterval(this.timeTrackingInterval);
			}
		}
	};
	
	// Initialize on document ready
	$(document).ready(function() {
		if ($('.active-tutorial-container').length) {
			TutorialNavigation.init();
		}
	});
	
	// Cleanup on page unload
	$(window).on('beforeunload', function() {
		TutorialNavigation.destroy();
	});
	
})(jQuery);
