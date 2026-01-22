/**
 * Course Page JavaScript
 * Interactive functionality for course pages
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        /**
         * Tab Navigation
         */
        $('.course-tab').on('click', function() {
            var tab = $(this).data('tab');
            
            // Update tab buttons
            $('.course-tab').removeClass('active');
            $(this).addClass('active');
            
            // Update tab content
            $('.course-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');
        });

        /**
         * Module Accordion
         */
        $('.module-header').on('click', function() {
            var $module = $(this).closest('.course-module');
            var $content = $module.find('.module-content');
            var $toggle = $(this).find('.module-toggle');
            
            // Toggle content
            $content.slideToggle(300);
            
            // Toggle button state
            $toggle.toggleClass('active');
            
            // Update aria-expanded
            var expanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !expanded);
        });

        /**
         * FAQ Accordion
         */
        $('.faq-question').on('click', function() {
            var $faqItem = $(this).closest('.faq-item');
            var $answer = $faqItem.find('.faq-answer');
            var expanded = $(this).attr('aria-expanded') === 'true';
            
            // Close all other FAQs
            $('.faq-answer').not($answer).slideUp(300).removeClass('active');
            $('.faq-question').not(this).attr('aria-expanded', 'false');
            
            // Toggle this FAQ
            $answer.slideToggle(300).toggleClass('active');
            $(this).attr('aria-expanded', !expanded);
        });

        /**
         * Video Player Modal
         */
        $('.play-lesson').on('click', function() {
            var videoUrl = $(this).data('video-url');
            var lessonTitle = $(this).data('lesson-title');
            var $lessonItem = $(this).closest('.lesson-item');
            var lessonIndex = $lessonItem.data('lesson-index');
            var moduleIndex = $lessonItem.data('module-index');
            
            // Set lesson title
            $('#video-lesson-title').text(lessonTitle);
            
            // Load video
            var videoHtml = getVideoEmbed(videoUrl);
            $('#video-player-container').html(videoHtml);
            
            // Show modal
            $('#video-player-modal').fadeIn(300);
            
            // Show mark complete button if enrolled
            if (aiddataCoursePage.is_enrolled) {
                $('.mark-complete-btn').show().data({
                    'lesson-index': lessonIndex,
                    'module-index': moduleIndex
                });
            }
            
            // Prevent body scroll
            $('body').css('overflow', 'hidden');
        });

        /**
         * Close Video Modal
         */
        $('.close-video-modal, .video-modal-overlay').on('click', function() {
            closeVideoModal();
        });

        /**
         * Mark Lesson as Complete
         */
        $('.mark-complete-btn').on('click', function() {
            var $btn = $(this);
            var lessonIndex = $btn.data('lesson-index');
            var moduleIndex = $btn.data('module-index');
            
            $btn.prop('disabled', true).text('Marking...');
            
            $.ajax({
                url: aiddataCoursePage.ajax_url,
                type: 'POST',
                data: {
                    action: 'mark_lesson_complete',
                    nonce: aiddataCoursePage.nonce,
                    page_id: aiddataCoursePage.page_id,
                    user_id: aiddataCoursePage.user_id,
                    module_index: moduleIndex,
                    lesson_index: lessonIndex
                },
                success: function(response) {
                    if (response.success) {
                        $btn.html('<span class="dashicons dashicons-yes"></span> Completed!');
                        setTimeout(function() {
                            closeVideoModal();
                            // Refresh page to update progress
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Error: ' + (response.data || 'Could not mark lesson complete'));
                        $btn.prop('disabled', false).html('<span class="dashicons dashicons-yes"></span> Mark as Complete');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-yes"></span> Mark as Complete');
                }
            });
        });

        /**
         * Get Video Embed HTML
         */
        function getVideoEmbed(url) {
            if (!url) return '<p>No video URL provided.</p>';
            
            // YouTube
            if (url.match(/youtube\.com\/watch\?v=([^&]+)/) || url.match(/youtu\.be\/([^?]+)/)) {
                var videoId = url.match(/youtube\.com\/watch\?v=([^&]+)/) ? 
                    RegExp.$1 : url.match(/youtu\.be\/([^?]+)/) ? RegExp.$1 : '';
                return '<iframe src="https://www.youtube.com/embed/' + videoId + '?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            }
            
            // Vimeo
            if (url.match(/vimeo\.com\/(\d+)/)) {
                var videoId = RegExp.$1;
                return '<iframe src="https://player.vimeo.com/video/' + videoId + '?autoplay=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
            }
            
            // Panopto
            if (url.match(/panopto/i)) {
                return '<iframe src="' + url + '" frameborder="0" allowfullscreen allow="autoplay"></iframe>';
            }
            
            // Direct video file (mp4, webm, ogg)
            if (url.match(/\.(mp4|webm|ogg)$/i)) {
                return '<video controls autoplay><source src="' + url + '" type="video/' + url.split('.').pop() + '">Your browser does not support the video tag.</video>';
            }
            
            // Generic iframe embed
            return '<iframe src="' + url + '" frameborder="0" allowfullscreen allow="autoplay"></iframe>';
        }

        /**
         * Close Video Modal
         */
        function closeVideoModal() {
            $('#video-player-modal').fadeOut(300);
            $('#video-player-container').html(''); // Stop video
            $('body').css('overflow', '');
        }

        // Close modal on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#video-player-modal').is(':visible')) {
                closeVideoModal();
            }
        });

        /**
         * Enrollment Button
         */
        $('.enroll-button').on('click', function() {
            var $button = $(this);
            var pageId = $button.data('page-id');
            
            if (!aiddataCoursePage.user_id) {
                alert('Please log in to enroll in this course.');
                return;
            }
            
            $button.prop('disabled', true).text('Enrolling...');
            
            $.ajax({
                url: aiddataCoursePage.ajax_url,
                type: 'POST',
                data: {
                    action: 'enroll_in_course_page',
                    nonce: aiddataCoursePage.nonce,
                    page_id: pageId,
                    user_id: aiddataCoursePage.user_id
                },
                success: function(response) {
                    if (response.success) {
                        alert('Successfully enrolled in the course!');
                        location.reload();
                    } else {
                        alert('Enrollment failed: ' + response.data);
                        $button.prop('disabled', false).text('Enroll in This Course');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Enroll in This Course');
                }
            });
        });

        /**
         * Smooth Scroll for Anchor Links
         */
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });

        /**
         * Lazy Load Images (if present)
         */
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            $('.course-page-container img[data-src]').each(function() {
                imageObserver.observe(this);
            });
        }

        /**
         * Track User Progress (if enrolled)
         */
        if (aiddataCoursePage.is_enrolled) {
            // Track page views
            trackPageView();
            
            // Track time spent
            var startTime = Date.now();
            
            $(window).on('beforeunload', function() {
                var timeSpent = Math.floor((Date.now() - startTime) / 1000); // in seconds
                trackTimeSpent(timeSpent);
            });
        }

        /**
         * Track Page View
         */
        function trackPageView() {
            $.ajax({
                url: aiddataCoursePage.ajax_url,
                type: 'POST',
                data: {
                    action: 'track_course_page_view',
                    nonce: aiddataCoursePage.nonce,
                    page_id: aiddataCoursePage.page_id,
                    user_id: aiddataCoursePage.user_id
                }
            });
        }

        /**
         * Track Time Spent
         */
        function trackTimeSpent(seconds) {
            navigator.sendBeacon(aiddataCoursePage.ajax_url, new URLSearchParams({
                action: 'track_course_time_spent',
                nonce: aiddataCoursePage.nonce,
                page_id: aiddataCoursePage.page_id,
                user_id: aiddataCoursePage.user_id,
                time_spent: seconds
            }));
        }

        /**
         * Print Certificate Button (if available)
         */
        $('.print-certificate').on('click', function(e) {
            e.preventDefault();
            window.print();
        });

        /**
         * Share Course
         */
        $('.share-course').on('click', function(e) {
            e.preventDefault();
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    text: 'Check out this course!',
                    url: window.location.href
                }).catch(function(error) {
                    console.log('Error sharing:', error);
                });
            } else {
                // Fallback: Copy to clipboard
                var dummy = document.createElement('input');
                document.body.appendChild(dummy);
                dummy.value = window.location.href;
                dummy.select();
                document.execCommand('copy');
                document.body.removeChild(dummy);
                alert('Link copied to clipboard!');
            }
        });

        /**
         * Rate Course
         */
        $('.course-rating').on('click', '.star', function() {
            var rating = $(this).data('rating');
            var $stars = $(this).parent().find('.star');
            
            // Update visual rating
            $stars.removeClass('active');
            $stars.slice(0, rating).addClass('active');
            
            // Submit rating
            $.ajax({
                url: aiddataCoursePage.ajax_url,
                type: 'POST',
                data: {
                    action: 'rate_course_page',
                    nonce: aiddataCoursePage.nonce,
                    page_id: aiddataCoursePage.page_id,
                    user_id: aiddataCoursePage.user_id,
                    rating: rating
                },
                success: function(response) {
                    if (response.success) {
                        alert('Thank you for your rating!');
                    }
                }
            });
        });

        /**
         * Bookmark Course
         */
        $('.bookmark-course').on('click', function() {
            var $button = $(this);
            
            $.ajax({
                url: aiddataCoursePage.ajax_url,
                type: 'POST',
                data: {
                    action: 'bookmark_course_page',
                    nonce: aiddataCoursePage.nonce,
                    page_id: aiddataCoursePage.page_id,
                    user_id: aiddataCoursePage.user_id
                },
                success: function(response) {
                    if (response.success) {
                        $button.toggleClass('bookmarked');
                        $button.find('.text').text(
                            $button.hasClass('bookmarked') ? 'Bookmarked' : 'Bookmark'
                        );
                    }
                }
            });
        });

    });

})(jQuery);

