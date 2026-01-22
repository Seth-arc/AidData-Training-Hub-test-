/**
 * AidData LMS Notifications JavaScript
 *
 * @package AidData_LMS
 * @since 1.1.0
 */

(function($) {
    'use strict';

    class AidDataNotifications {
        constructor() {
            this.currentPage = 1;
            this.loading = false;
            this.hasMore = true;
            
            this.init();
        }

        init() {
            this.createNotificationPanel();
            this.bindEvents();
            this.startPolling();
            this.addStyles();
        }

        createNotificationPanel() {
            // Create notification panel HTML
            const panelHTML = `
                <div id="aiddata-notification-panel" class="aiddata-notification-panel">
                    <div class="notification-header">
                        <h3>${aiddata_lms_notifications.strings.notifications || 'Notifications'}</h3>
                        <div class="notification-actions">
                            <button class="mark-all-read-btn" title="${aiddata_lms_notifications.strings.mark_all_read}">
                                <span class="dashicons dashicons-yes-alt"></span>
                            </button>
                            <button class="close-panel-btn" title="Close">
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                        </div>
                    </div>
                    <div class="notification-content">
                        <div class="notification-list"></div>
                        <div class="notification-loading" style="display: none;">
                            ${aiddata_lms_notifications.strings.loading}
                        </div>
                        <div class="notification-load-more" style="display: none;">
                            <button class="load-more-btn">Load More</button>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(panelHTML);
        }

        bindEvents() {
            // Notification bell click
            $(document).on('click', '.aiddata-notification-bell', (e) => {
                e.preventDefault();
                this.toggleNotificationPanel();
            });

            // Close panel
            $(document).on('click', '.close-panel-btn', () => {
                this.hideNotificationPanel();
            });

            // Mark all as read
            $(document).on('click', '.mark-all-read-btn', () => {
                this.markAllAsRead();
            });

            // Mark single notification as read
            $(document).on('click', '.notification-item:not(.read)', (e) => {
                const notificationId = $(e.currentTarget).data('notification-id');
                this.markAsRead(notificationId);
            });

            // Delete notification
            $(document).on('click', '.delete-notification', (e) => {
                e.stopPropagation();
                const notificationId = $(e.currentTarget).closest('.notification-item').data('notification-id');
                this.deleteNotification(notificationId);
            });

            // Load more notifications
            $(document).on('click', '.load-more-btn', () => {
                this.loadMoreNotifications();
            });

            // Click outside to close
            $(document).on('click', (e) => {
                if (!$(e.target).closest('#aiddata-notification-panel, .aiddata-notification-bell').length) {
                    this.hideNotificationPanel();
                }
            });

            // Action button clicks
            $(document).on('click', '.notification-action', (e) => {
                e.stopPropagation();
                const url = $(e.currentTarget).attr('href');
                const notificationId = $(e.currentTarget).closest('.notification-item').data('notification-id');
                
                // Mark as read when action is clicked
                this.markAsRead(notificationId);
                
                if (url && url !== '#') {
                    window.open(url, '_blank');
                }
            });
        }

        toggleNotificationPanel() {
            const panel = $('#aiddata-notification-panel');
            
            if (panel.hasClass('show')) {
                this.hideNotificationPanel();
            } else {
                this.showNotificationPanel();
            }
        }

        showNotificationPanel() {
            const panel = $('#aiddata-notification-panel');
            panel.addClass('show');
            
            // Load notifications if not already loaded
            if (this.currentPage === 1) {
                this.loadNotifications();
            }
        }

        hideNotificationPanel() {
            $('#aiddata-notification-panel').removeClass('show');
        }

        loadNotifications(reset = true) {
            if (this.loading) return;
            
            this.loading = true;
            
            if (reset) {
                this.currentPage = 1;
                $('.notification-list').empty();
            }

            $('.notification-loading').show();

            $.ajax({
                url: aiddata_lms_notifications.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_get_notifications',
                    nonce: aiddata_lms_notifications.nonce,
                    page: this.currentPage
                },
                success: (response) => {
                    if (response.success) {
                        this.renderNotifications(response.data.notifications, reset);
                        this.updateUnreadCount(response.data.unread_count);
                        this.hasMore = response.data.has_more;
                        
                        if (this.hasMore) {
                            $('.notification-load-more').show();
                        } else {
                            $('.notification-load-more').hide();
                        }
                    }
                },
                complete: () => {
                    this.loading = false;
                    $('.notification-loading').hide();
                }
            });
        }

        loadMoreNotifications() {
            if (this.loading || !this.hasMore) return;
            
            this.currentPage++;
            this.loadNotifications(false);
        }

        renderNotifications(notifications, reset = true) {
            const container = $('.notification-list');
            
            if (reset) {
                container.empty();
            }

            if (notifications.length === 0 && reset) {
                container.html(`
                    <div class="no-notifications">
                        <span class="dashicons dashicons-bell"></span>
                        <p>${aiddata_lms_notifications.strings.no_notifications}</p>
                    </div>
                `);
                return;
            }

            notifications.forEach(notification => {
                const notificationHTML = this.createNotificationHTML(notification);
                container.append(notificationHTML);
            });
        }

        createNotificationHTML(notification) {
            const isRead = notification.is_read == 1;
            const typeClass = `notification-${notification.type}`;
            const readClass = isRead ? 'read' : 'unread';
            const priorityClass = `priority-${notification.priority}`;
            
            const actionButton = notification.action_url ? 
                `<a href="${notification.action_url}" class="notification-action">${notification.action_text || 'View'}</a>` : '';
            
            const timeAgo = this.timeAgo(notification.created_date);
            
            return `
                <div class="notification-item ${typeClass} ${readClass} ${priorityClass}" 
                     data-notification-id="${notification.id}">
                    <div class="notification-icon">
                        ${this.getNotificationIcon(notification.type)}
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-meta">
                            <span class="notification-time">${timeAgo}</span>
                            ${actionButton}
                        </div>
                    </div>
                    <div class="notification-actions">
                        ${!isRead ? '<span class="unread-indicator"></span>' : ''}
                        <button class="delete-notification" title="${aiddata_lms_notifications.strings.delete}">
                            <span class="dashicons dashicons-dismiss"></span>
                        </button>
                    </div>
                </div>
            `;
        }

        getNotificationIcon(type) {
            const icons = {
                'success': 'dashicons-yes-alt',
                'info': 'dashicons-info',
                'warning': 'dashicons-warning',
                'error': 'dashicons-dismiss',
                'achievement': 'dashicons-awards'
            };
            
            const iconClass = icons[type] || 'dashicons-bell';
            return `<span class="dashicons ${iconClass}"></span>`;
        }

        markAsRead(notificationId) {
            $.ajax({
                url: aiddata_lms_notifications.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_mark_notification_read',
                    nonce: aiddata_lms_notifications.nonce,
                    notification_id: notificationId
                },
                success: (response) => {
                    if (response.success) {
                        $(`.notification-item[data-notification-id="${notificationId}"]`)
                            .removeClass('unread').addClass('read')
                            .find('.unread-indicator').remove();
                        
                        this.updateUnreadCount(response.data.unread_count);
                    }
                }
            });
        }

        markAllAsRead() {
            $.ajax({
                url: aiddata_lms_notifications.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_mark_all_notifications_read',
                    nonce: aiddata_lms_notifications.nonce
                },
                success: (response) => {
                    if (response.success) {
                        $('.notification-item.unread')
                            .removeClass('unread').addClass('read')
                            .find('.unread-indicator').remove();
                        
                        this.updateUnreadCount(0);
                    }
                }
            });
        }

        deleteNotification(notificationId) {
            $.ajax({
                url: aiddata_lms_notifications.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_delete_notification',
                    nonce: aiddata_lms_notifications.nonce,
                    notification_id: notificationId
                },
                success: (response) => {
                    if (response.success) {
                        $(`.notification-item[data-notification-id="${notificationId}"]`).fadeOut(() => {
                            $(this).remove();
                            
                            // Check if we need to show "no notifications" message
                            if ($('.notification-item').length === 0) {
                                $('.notification-list').html(`
                                    <div class="no-notifications">
                                        <span class="dashicons dashicons-bell"></span>
                                        <p>${aiddata_lms_notifications.strings.no_notifications}</p>
                                    </div>
                                `);
                            }
                        });
                        
                        this.updateUnreadCount(response.data.unread_count);
                    }
                }
            });
        }

        updateUnreadCount(count) {
            const bellElement = $('.aiddata-notification-bell');
            const countElement = bellElement.find('.aiddata-notification-count');
            
            if (count > 0) {
                if (countElement.length) {
                    countElement.text(count);
                } else {
                    bellElement.append(`<span class="aiddata-notification-count">${count}</span>`);
                }
                bellElement.addClass('has-notifications');
            } else {
                countElement.remove();
                bellElement.removeClass('has-notifications');
            }
        }

        startPolling() {
            // Poll for new notifications every 30 seconds
            setInterval(() => {
                this.checkForNewNotifications();
            }, 30000);
        }

        checkForNewNotifications() {
            // Check for new notifications in transient
            $.ajax({
                url: aiddata_lms_notifications.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_check_new_notifications',
                    nonce: aiddata_lms_notifications.nonce
                },
                success: (response) => {
                    if (response.success && response.data.has_new) {
                        // Refresh notifications if panel is open
                        if ($('#aiddata-notification-panel').hasClass('show')) {
                            this.loadNotifications();
                        } else {
                            // Just update the count
                            this.updateUnreadCount(response.data.unread_count);
                        }
                        
                        // Show desktop notification if supported
                        this.showDesktopNotification(response.data.latest_notification);
                    }
                }
            });
        }

        showDesktopNotification(notification) {
            if (!('Notification' in window) || !notification) return;
            
            if (Notification.permission === 'granted') {
                new Notification(notification.title, {
                    body: notification.message,
                    icon: aiddata_lms_notifications.plugin_url + '/assets/images/aiddata-icon.png',
                    tag: 'aiddata-lms-notification'
                });
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        this.showDesktopNotification(notification);
                    }
                });
            }
        }

        timeAgo(dateString) {
            const now = new Date();
            const date = new Date(dateString);
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
            if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + 'd ago';
            
            return date.toLocaleDateString();
        }

        addStyles() {
            // Add notification count styles to admin bar
            if (!$('#aiddata-notification-styles').length) {
                $('head').append(`
                    <style id="aiddata-notification-styles">
                        .aiddata-notification-count {
                            background: #dc3232;
                            color: white;
                            border-radius: 50%;
                            padding: 2px 6px;
                            font-size: 11px;
                            line-height: 1;
                            margin-left: 5px;
                            position: relative;
                            top: -2px;
                        }
                        
                        .aiddata-notification-bell.has-notifications .ab-icon {
                            color: #dc3232;
                        }
                        
                        @keyframes notification-pulse {
                            0% { opacity: 1; }
                            50% { opacity: 0.5; }
                            100% { opacity: 1; }
                        }
                        
                        .aiddata-notification-bell.has-notifications {
                            animation: notification-pulse 2s infinite;
                        }
                    </style>
                `);
            }
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        if (typeof aiddata_lms_notifications !== 'undefined') {
            new AidDataNotifications();
        }
    });

})(jQuery);
