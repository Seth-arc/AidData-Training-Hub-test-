/**
 * Course Template JavaScript
 * 
 * This file contains JS functionality specific to the course template.
 */

(function($) {
    'use strict';

    // Initialize all template functionality when DOM is ready
    $(document).ready(function() {
        initDrawerFunctionality();
        initNotifications();
        initSmoothScroll();
    });

    /**
     * Initialize the info drawer functionality
     */
    function initDrawerFunctionality() {
        const $learnMoreBtn = $('.learn-more-btn');
        const $infoDrawer = $('.info-drawer');
        const $closeDrawerBtn = $('.close-drawer');
        let scrollPosition = 0;

        // Open drawer on button click
        $learnMoreBtn.on('click', function() {
            scrollPosition = window.scrollY;
            $infoDrawer.addClass('open');
            $('body').css('overflow', 'hidden');
            
            // Scroll the drawer to the top
            setTimeout(function() {
                $('.drawer-content').scrollTop(0);
            }, 100);
        });

        // Close drawer on X button click
        $closeDrawerBtn.on('click', function() {
            closeDrawer();
        });

        // Close drawer when clicking outside content
        $infoDrawer.on('click', function(e) {
            if ($(e.target).closest('.drawer-content').length === 0) {
                closeDrawer();
            }
        });

        // Close drawer on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $infoDrawer.hasClass('open')) {
                closeDrawer();
            }
        });

        // Helper function to close the drawer
        function closeDrawer() {
            $infoDrawer.removeClass('open');
            $('body').css('overflow', '');
        }
    }

    /**
     * Initialize notification functionality
     */
    function initNotifications() {
        const $notificationBtn = $('#notificationsButton');
        const $notificationModal = $('#notificationModal');
        const $closeBtn = $('.close-notifications');
        const $markAllReadBtn = $('.mark-all-read');
        const $notificationItems = $('.notification-item');

        // Toggle notification modal
        $notificationBtn.on('click', function(e) {
            e.stopPropagation();
            $notificationModal.toggleClass('active');
            
            if ($notificationModal.hasClass('active')) {
                $('body').css('overflow', 'hidden');
                updateNotificationBadge();
            } else {
                $('body').css('overflow', '');
            }
        });

        // Close notification modal
        $closeBtn.on('click', function() {
            $notificationModal.removeClass('active');
            $('body').css('overflow', '');
        });

        // Mark all notifications as read
        $markAllReadBtn.on('click', function() {
            $notificationItems.removeClass('unread');
            updateNotificationBadge();
        });

        // Mark individual notification as read
        $notificationItems.on('click', function() {
            $(this).removeClass('unread');
            updateNotificationBadge();
        });

        // Close modal when clicking outside
        $(document).on('click', function(e) {
            if ($notificationModal.hasClass('active') && 
                !$(e.target).closest('.notification-container').length && 
                !$(e.target).closest('#notificationsButton').length) {
                $notificationModal.removeClass('active');
                $('body').css('overflow', '');
            }
        });

        // Close modal with Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $notificationModal.hasClass('active')) {
                $notificationModal.removeClass('active');
                $('body').css('overflow', '');
            }
        });

        // Update notification badge
        function updateNotificationBadge() {
            const unreadCount = $('.notification-item.unread').length;
            const $badge = $notificationBtn.find('.notification-badge');
            
            if (unreadCount > 0) {
                $badge.show().addClass('has-notifications');
            } else {
                $badge.hide().removeClass('has-notifications');
            }
        }

        // Initial badge update
        updateNotificationBadge();
    }

    /**
     * Initialize smooth scrolling for anchor links
     */
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    }

})(jQuery); 