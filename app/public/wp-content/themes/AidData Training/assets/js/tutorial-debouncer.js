/**
 * Tutorial Progress Debouncer
 * 
 * Optimizes progress updates by:
 * - Debouncing AJAX requests
 * - Local storage caching
 * - Batch updates
 * - Offline support
 */

(function($) {
    'use strict';

    window.TutorialDebouncer = {
        // Configuration
        config: {
            debounceDelay: 3000,        // 3 seconds
            maxBatchSize: 10,           // Max updates to batch
            offlineRetryDelay: 5000,    // 5 seconds
            localStorageKey: 'aiddata_tutorial_progress_queue'
        },

        // State
        updateQueue: [],
        debounceTimer: null,
        isOnline: navigator.onLine,
        isSending: false,

        /**
         * Initialize debouncer
         */
        init: function() {
            this.loadQueueFromStorage();
            this.setupOnlineListener();
            this.setupBeforeUnload();
            this.processOfflineQueue();
        },

        /**
         * Queue progress update
         */
        queueUpdate: function(progressData) {
            // Add to queue
            this.updateQueue.push({
                data: progressData,
                timestamp: Date.now()
            });

            // Limit queue size
            if (this.updateQueue.length > this.config.maxBatchSize) {
                this.updateQueue.shift();
            }

            // Save to local storage
            this.saveQueueToStorage();

            // Trigger debounced send
            this.debounceSend();
        },

        /**
         * Debounce send function
         */
        debounceSend: function() {
            clearTimeout(this.debounceTimer);
            
            this.debounceTimer = setTimeout(function() {
                TutorialDebouncer.sendQueuedUpdates();
            }, this.config.debounceDelay);
        },

        /**
         * Send queued updates
         */
        sendQueuedUpdates: function() {
            if (this.isSending || this.updateQueue.length === 0) {
                return;
            }

            if (!this.isOnline) {
                console.log('[TutorialDebouncer] Offline - updates queued');
                return;
            }

            this.isSending = true;

            // Get latest update (most recent state)
            var latestUpdate = this.updateQueue[this.updateQueue.length - 1];
            var progressData = latestUpdate.data;

            console.log('[TutorialDebouncer] Sending update:', progressData);

            $.ajax({
                url: tutorialData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'update_tutorial_progress',
                    nonce: tutorialData.nonce,
                    tutorial_id: progressData.tutorial_id,
                    user_id: progressData.user_id,
                    current_step: progressData.current_step,
                    completed_steps: progressData.completed_steps,
                    progress_percent: progressData.progress_percent
                },
                success: function(response) {
                    if (response.success) {
                        console.log('[TutorialDebouncer] Update successful');
                        TutorialDebouncer.clearQueue();
                        
                        // Trigger success event
                        $(document).trigger('tutorial:progress:saved', [progressData]);
                    } else {
                        console.error('[TutorialDebouncer] Update failed:', response.data);
                        TutorialDebouncer.handleError(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('[TutorialDebouncer] AJAX error:', error);
                    
                    // If offline, keep in queue
                    if (status === 'timeout' || xhr.status === 0) {
                        TutorialDebouncer.isOnline = false;
                    } else {
                        TutorialDebouncer.handleError({
                            message: 'Network error occurred',
                            code: xhr.status
                        });
                    }
                },
                complete: function() {
                    TutorialDebouncer.isSending = false;
                }
            });
        },

        /**
         * Handle error
         */
        handleError: function(error) {
            $(document).trigger('tutorial:progress:error', [error]);
        },

        /**
         * Clear queue
         */
        clearQueue: function() {
            this.updateQueue = [];
            this.clearStoredQueue();
        },

        /**
         * Save queue to local storage
         */
        saveQueueToStorage: function() {
            try {
                localStorage.setItem(
                    this.config.localStorageKey,
                    JSON.stringify(this.updateQueue)
                );
            } catch (e) {
                console.warn('[TutorialDebouncer] Could not save to localStorage:', e);
            }
        },

        /**
         * Load queue from local storage
         */
        loadQueueFromStorage: function() {
            try {
                var stored = localStorage.getItem(this.config.localStorageKey);
                if (stored) {
                    this.updateQueue = JSON.parse(stored);
                    console.log('[TutorialDebouncer] Loaded queue from storage:', this.updateQueue.length, 'items');
                }
            } catch (e) {
                console.warn('[TutorialDebouncer] Could not load from localStorage:', e);
            }
        },

        /**
         * Clear stored queue
         */
        clearStoredQueue: function() {
            try {
                localStorage.removeItem(this.config.localStorageKey);
            } catch (e) {
                console.warn('[TutorialDebouncer] Could not clear localStorage:', e);
            }
        },

        /**
         * Process offline queue
         */
        processOfflineQueue: function() {
            if (this.isOnline && this.updateQueue.length > 0) {
                console.log('[TutorialDebouncer] Processing offline queue');
                this.sendQueuedUpdates();
            }
        },

        /**
         * Setup online/offline listener
         */
        setupOnlineListener: function() {
            var self = this;

            window.addEventListener('online', function() {
                console.log('[TutorialDebouncer] Connection restored');
                self.isOnline = true;
                self.processOfflineQueue();
            });

            window.addEventListener('offline', function() {
                console.log('[TutorialDebouncer] Connection lost');
                self.isOnline = false;
            });
        },

        /**
         * Setup before unload handler
         */
        setupBeforeUnload: function() {
            var self = this;

            window.addEventListener('beforeunload', function(e) {
                // If there are unsaved updates, send immediately
                if (self.updateQueue.length > 0 && self.isOnline) {
                    // Use sendBeacon for guaranteed delivery
                    if (navigator.sendBeacon) {
                        var latestUpdate = self.updateQueue[self.updateQueue.length - 1];
                        var formData = new FormData();
                        
                        formData.append('action', 'update_tutorial_progress');
                        formData.append('nonce', tutorialData.nonce);
                        formData.append('tutorial_id', latestUpdate.data.tutorial_id);
                        formData.append('user_id', latestUpdate.data.user_id);
                        formData.append('current_step', latestUpdate.data.current_step);
                        formData.append('completed_steps', JSON.stringify(latestUpdate.data.completed_steps));
                        formData.append('progress_percent', latestUpdate.data.progress_percent);

                        navigator.sendBeacon(tutorialData.ajaxUrl, formData);
                        self.clearQueue();
                    }
                }
            });
        },

        /**
         * Force send now
         */
        forceSend: function() {
            clearTimeout(this.debounceTimer);
            this.sendQueuedUpdates();
        },

        /**
         * Get queue status
         */
        getStatus: function() {
            return {
                queueLength: this.updateQueue.length,
                isOnline: this.isOnline,
                isSending: this.isSending,
                hasUnsavedChanges: this.updateQueue.length > 0
            };
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        if (typeof tutorialData !== 'undefined') {
            TutorialDebouncer.init();
        }
    });

    // Expose to window
    window.TutorialDebouncer = TutorialDebouncer;

})(jQuery);

