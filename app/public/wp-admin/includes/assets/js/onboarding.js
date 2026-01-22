/**
 * AidData LMS Onboarding Module
 * 
 * Handles the interactive onboarding popup with slides, videos, and navigation.
 * 
 * @package AidData_LMS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Onboarding Module
     */
    var AidDataOnboarding = {
        
        // Configuration
        config: {
            overlay: null,
            popup: null,
            currentSlide: 0,
            totalSlides: 0,
            contentId: null,
            contentType: null,
            slides: [],
            isInitialized: false
        },

        /**
         * Initialize the onboarding module
         */
        init: function() {
            if (this.config.isInitialized) {
                return;
            }

            this.config.overlay = $('#aiddata-onboarding-overlay');
            
            if (this.config.overlay.length === 0) {
                return;
            }

            this.config.popup = this.config.overlay.find('.aiddata-onboarding-popup');
            this.config.contentId = this.config.overlay.data('content-id');
            this.config.contentType = this.config.overlay.data('content-type');
            this.config.totalSlides = this.config.overlay.find('.onboarding-slide').length;
            this.config.currentSlide = 0;

            this.bindEvents();
            this.showOnboarding();
            
            this.config.isInitialized = true;
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Skip button
            this.config.overlay.on('click', '.onboarding-skip', function(e) {
                e.preventDefault();
                self.skipOnboarding();
            });

            // Next button
            this.config.overlay.on('click', '.btn-next', function(e) {
                e.preventDefault();
                self.nextSlide();
            });

            // Previous button
            this.config.overlay.on('click', '.btn-previous', function(e) {
                e.preventDefault();
                self.previousSlide();
            });

            // Finish button
            this.config.overlay.on('click', '.btn-finish', function(e) {
                e.preventDefault();
                self.completeOnboarding();
            });

            // Close on overlay click (outside popup)
            this.config.overlay.on('click', function(e) {
                if (e.target === self.config.overlay[0]) {
                    self.skipOnboarding();
                }
            });

            // Keyboard navigation
            $(document).on('keydown.onboarding', function(e) {
                if (!self.config.overlay.hasClass('show')) {
                    return;
                }

                switch(e.which) {
                    case 27: // Escape
                        self.skipOnboarding();
                        break;
                    case 37: // Left arrow
                        if (self.config.currentSlide > 0) {
                            self.previousSlide();
                        }
                        break;
                    case 39: // Right arrow
                        if (self.config.currentSlide < self.config.totalSlides - 1) {
                            self.nextSlide();
                        } else {
                            self.completeOnboarding();
                        }
                        break;
                }
            });

            // Pause videos when switching slides
            this.config.overlay.on('click', '.btn-next, .btn-previous', function() {
                self.pauseCurrentSlideVideos();
            });
        },

        /**
         * Show the onboarding overlay
         */
        showOnboarding: function() {
            var self = this;
            
            // Prevent body scrolling
            $('body').addClass('onboarding-active').css('overflow', 'hidden');
            
            // Show overlay with animation
            setTimeout(function() {
                self.config.overlay.addClass('show');
            }, 100);

            // Update navigation for first slide
            this.updateNavigation();
            this.updateProgress();
        },

        /**
         * Hide the onboarding overlay
         */
        hideOnboarding: function() {
            var self = this;
            
            // Hide overlay
            this.config.overlay.removeClass('show');
            
            // Re-enable body scrolling after animation
            setTimeout(function() {
                $('body').removeClass('onboarding-active').css('overflow', '');
                $(document).off('keydown.onboarding');
            }, 300);
        },

        /**
         * Go to next slide
         */
        nextSlide: function() {
            if (this.config.currentSlide < this.config.totalSlides - 1) {
                this.goToSlide(this.config.currentSlide + 1);
            }
        },

        /**
         * Go to previous slide
         */
        previousSlide: function() {
            if (this.config.currentSlide > 0) {
                this.goToSlide(this.config.currentSlide - 1);
            }
        },

        /**
         * Go to specific slide
         */
        goToSlide: function(slideIndex) {
            if (slideIndex < 0 || slideIndex >= this.config.totalSlides) {
                return;
            }

            // Hide current slide
            this.config.overlay.find('.onboarding-slide').removeClass('active');
            
            // Show target slide
            this.config.overlay.find('.onboarding-slide').eq(slideIndex).addClass('active');
            
            // Update current slide index
            this.config.currentSlide = slideIndex;
            
            // Update navigation and progress
            this.updateNavigation();
            this.updateProgress();
            
            // Auto-play videos if present
            this.autoPlaySlideVideos();
        },

        /**
         * Update navigation buttons
         */
        updateNavigation: function() {
            var prevBtn = this.config.overlay.find('.btn-previous');
            var nextBtn = this.config.overlay.find('.btn-next');
            var finishBtn = this.config.overlay.find('.btn-finish');
            
            // Previous button
            if (this.config.currentSlide === 0) {
                prevBtn.prop('disabled', true);
            } else {
                prevBtn.prop('disabled', false);
            }
            
            // Next/Finish buttons
            if (this.config.currentSlide === this.config.totalSlides - 1) {
                nextBtn.hide();
                finishBtn.show();
            } else {
                nextBtn.show();
                finishBtn.hide();
            }
        },

        /**
         * Update progress bar and text
         */
        updateProgress: function() {
            var progressPercent = ((this.config.currentSlide + 1) / this.config.totalSlides) * 100;
            
            this.config.overlay.find('.progress-fill').css('width', progressPercent + '%');
            this.config.overlay.find('.current-slide').text(this.config.currentSlide + 1);
            this.config.overlay.find('.total-slides').text(this.config.totalSlides);
        },

        /**
         * Pause videos in current slide
         */
        pauseCurrentSlideVideos: function() {
            var currentSlide = this.config.overlay.find('.onboarding-slide.active');
            
            // Pause HTML5 videos
            currentSlide.find('video').each(function() {
                this.pause();
            });
            
            // Pause YouTube iframes (if accessible)
            currentSlide.find('iframe[src*="youtube.com"]').each(function() {
                try {
                    this.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
                } catch(e) {
                    // Cross-origin restriction - ignore
                }
            });
        },

        /**
         * Auto-play videos in current slide (if user interaction occurred)
         */
        autoPlaySlideVideos: function() {
            var currentSlide = this.config.overlay.find('.onboarding-slide.active');
            
            // Only auto-play if user has interacted (browser policy)
            currentSlide.find('video[autoplay]').each(function() {
                var promise = this.play();
                if (promise !== undefined) {
                    promise.catch(function(error) {
                        // Auto-play was prevented - this is normal
                        console.log('Video autoplay prevented:', error);
                    });
                }
            });
        },

        /**
         * Complete onboarding
         */
        completeOnboarding: function() {
            var self = this;
            
            this.hideOnboarding();
            
            // Send completion to server
            $.ajax({
                url: aiddata_lms_onboarding.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_onboarding_complete',
                    nonce: aiddata_lms_onboarding.nonce,
                    content_id: this.config.contentId,
                    content_type: this.config.contentType
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotification(response.data.message, 'success');
                    } else {
                        console.error('Failed to mark onboarding as complete:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });
        },

        /**
         * Skip onboarding
         */
        skipOnboarding: function() {
            var self = this;
            
            if (!confirm('Are you sure you want to skip this tutorial? You can always access it later from the help menu.')) {
                return;
            }
            
            this.hideOnboarding();
            
            // Send skip to server
            $.ajax({
                url: aiddata_lms_onboarding.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_onboarding_skip',
                    nonce: aiddata_lms_onboarding.nonce,
                    content_id: this.config.contentId,
                    content_type: this.config.contentType
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotification('Tutorial skipped', 'info');
                    } else {
                        console.error('Failed to mark onboarding as skipped:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });
        },

        /**
         * Show notification message
         */
        showNotification: function(message, type) {
            type = type || 'info';
            
            // Remove existing notifications
            $('.onboarding-notification').remove();
            
            // Create notification
            var notification = $('<div class="onboarding-notification onboarding-notification-' + type + '">' + 
                                 '<span>' + message + '</span>' +
                                 '<button type="button" class="notification-close">&times;</button>' +
                                 '</div>');
            
            // Add to page
            $('body').append(notification);
            
            // Show with animation
            setTimeout(function() {
                notification.addClass('show');
            }, 100);
            
            // Auto-hide after 3 seconds
            setTimeout(function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 3000);
            
            // Manual close
            notification.on('click', '.notification-close', function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            });
        },

        /**
         * Reset onboarding state
         */
        reset: function() {
            this.config.currentSlide = 0;
            this.goToSlide(0);
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        // Check if onboarding overlay exists
        if ($('#aiddata-onboarding-overlay').length > 0) {
            AidDataOnboarding.init();
        }
    });

    /**
     * Make module globally accessible for debugging
     */
    window.AidDataOnboarding = AidDataOnboarding;

})(jQuery);

/**
 * Notification styles (injected via JavaScript to avoid CSS conflicts)
 */
jQuery(document).ready(function($) {
    if ($('.onboarding-notification').length === 0) {
        $('<style type="text/css">' +
          '.onboarding-notification { ' +
            'position: fixed; top: 20px; right: 20px; z-index: 10001; ' +
            'background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; ' +
            'padding: 16px 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); ' +
            'display: flex; align-items: center; gap: 10px; ' +
            'transform: translateX(100%); transition: transform 0.3s ease; ' +
            'min-width: 300px; max-width: 400px; ' +
          '} ' +
          '.onboarding-notification.show { transform: translateX(0); } ' +
          '.onboarding-notification-success { border-left: 4px solid #10b981; } ' +
          '.onboarding-notification-info { border-left: 4px solid #3b82f6; } ' +
          '.onboarding-notification-warning { border-left: 4px solid #f59e0b; } ' +
          '.onboarding-notification-error { border-left: 4px solid #ef4444; } ' +
          '.notification-close { ' +
            'background: none; border: none; font-size: 18px; cursor: pointer; ' +
            'color: #9ca3af; padding: 0; margin-left: auto; ' +
          '} ' +
          '.notification-close:hover { color: #6b7280; } ' +
          '@media (max-width: 480px) { ' +
            '.onboarding-notification { ' +
              'top: 10px; right: 10px; left: 10px; min-width: auto; ' +
            '} ' +
          '}' +
          '</style>').appendTo('head');
    }
});
