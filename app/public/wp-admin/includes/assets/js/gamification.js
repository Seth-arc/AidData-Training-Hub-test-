/**
 * AidData LMS Gamification JavaScript
 *
 * @package AidData_LMS
 * @since 1.1.0
 */

(function($) {
    'use strict';

    class AidDataGamification {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            this.checkForCelebrations();
            this.initProgressBars();
        }

        bindEvents() {
            // Claim badge buttons
            $(document).on('click', '.claim-badge-btn', (e) => {
                e.preventDefault();
                const achievementId = $(e.currentTarget).data('achievement-id');
                this.claimBadge(achievementId);
            });

            // Load more achievements
            $(document).on('click', '.load-more-achievements', () => {
                this.loadMoreAchievements();
            });

            // Refresh leaderboard
            $(document).on('click', '.refresh-leaderboard', () => {
                this.refreshLeaderboard();
            });

            // Show achievement details
            $(document).on('click', '.achievement-badge', (e) => {
                const badge = $(e.currentTarget);
                this.showAchievementDetails(badge);
            });
        }

        checkForCelebrations() {
            const celebrationPopup = $('#aiddata-celebration-popup');
            
            if (celebrationPopup.length && celebrationPopup.data('celebration')) {
                const celebrationData = celebrationPopup.data('celebration');
                this.showCelebration(celebrationData);
            }
        }

        showCelebration(data) {
            const { type, data: celebrationInfo } = data;
            
            let celebrationHTML = '';
            
            switch (type) {
                case 'achievement':
                    celebrationHTML = this.createAchievementCelebration(celebrationInfo);
                    break;
                case 'points':
                    celebrationHTML = this.createPointsCelebration(celebrationInfo);
                    break;
                case 'level_up':
                    celebrationHTML = this.createLevelUpCelebration(celebrationInfo);
                    break;
                default:
                    return;
            }
            
            this.displayCelebrationPopup(celebrationHTML);
        }

        createAchievementCelebration(data) {
            const { achievement, points_reward } = data;
            
            return `
                <div class="celebration-content achievement-celebration">
                    <div class="celebration-header">
                        <h2>🎉 Achievement Unlocked!</h2>
                    </div>
                    <div class="celebration-body">
                        <div class="achievement-badge-large">
                            <div class="badge-icon" style="background-color: ${achievement.badge_color}">
                                ${achievement.badge_icon}
                            </div>
                        </div>
                        <h3>${achievement.title}</h3>
                        <p>${achievement.description}</p>
                        ${points_reward > 0 ? `<div class="points-reward">+${points_reward} points earned!</div>` : ''}
                    </div>
                    <div class="celebration-footer">
                        <button class="celebration-close-btn">Awesome!</button>
                    </div>
                </div>
            `;
        }

        createPointsCelebration(data) {
            const { points, reason } = data;
            
            return `
                <div class="celebration-content points-celebration">
                    <div class="celebration-header">
                        <h2>💎 Points Earned!</h2>
                    </div>
                    <div class="celebration-body">
                        <div class="points-display">
                            <span class="points-amount">+${points}</span>
                            <span class="points-label">points</span>
                        </div>
                        <p>${reason}</p>
                    </div>
                    <div class="celebration-footer">
                        <button class="celebration-close-btn">Continue</button>
                    </div>
                </div>
            `;
        }

        createLevelUpCelebration(data) {
            const { new_level, points_needed_for_next } = data;
            
            return `
                <div class="celebration-content level-up-celebration">
                    <div class="celebration-header">
                        <h2>🚀 Level Up!</h2>
                    </div>
                    <div class="celebration-body">
                        <div class="level-display">
                            <span class="level-number">${new_level}</span>
                            <span class="level-label">Level</span>
                        </div>
                        <p>Congratulations! You've reached Level ${new_level}!</p>
                        ${points_needed_for_next ? `<p>Only ${points_needed_for_next} more points to reach Level ${new_level + 1}!</p>` : ''}
                    </div>
                    <div class="celebration-footer">
                        <button class="celebration-close-btn">Amazing!</button>
                    </div>
                </div>
            `;
        }

        displayCelebrationPopup(content) {
            // Remove existing celebration popup
            $('.aiddata-celebration-overlay').remove();
            
            const overlay = $(`
                <div class="aiddata-celebration-overlay">
                    <div class="aiddata-celebration-popup">
                        ${content}
                    </div>
                </div>
            `);
            
            $('body').append(overlay);
            
            // Animate in
            setTimeout(() => {
                overlay.addClass('show');
            }, 100);
            
            // Bind close events
            overlay.on('click', '.celebration-close-btn, .aiddata-celebration-overlay', (e) => {
                if (e.target === e.currentTarget) {
                    this.closeCelebrationPopup();
                }
            });
            
            // Auto close after 5 seconds
            setTimeout(() => {
                this.closeCelebrationPopup();
            }, 5000);
        }

        closeCelebrationPopup() {
            const overlay = $('.aiddata-celebration-overlay');
            overlay.removeClass('show');
            
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }

        initProgressBars() {
            $('.progress-bar').each(function() {
                const progressBar = $(this);
                const progressFill = progressBar.find('.progress-fill');
                const targetWidth = progressFill.data('width') || progressFill.css('width');
                
                // Animate progress bar
                progressFill.css('width', '0%');
                setTimeout(() => {
                    progressFill.css('width', targetWidth);
                }, 500);
            });
        }

        claimBadge(achievementId) {
            $.ajax({
                url: aiddata_lms_gamification.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_claim_badge',
                    nonce: aiddata_lms_gamification.nonce,
                    achievement_id: achievementId
                },
                success: (response) => {
                    if (response.success) {
                        $(`.claim-badge-btn[data-achievement-id="${achievementId}"]`)
                            .removeClass('claim-badge-btn')
                            .addClass('badge-claimed')
                            .text('Claimed')
                            .prop('disabled', true);
                        
                        this.showNotification('Badge claimed successfully!', 'success');
                    } else {
                        this.showNotification('Failed to claim badge: ' + response.data, 'error');
                    }
                }
            });
        }

        loadMoreAchievements() {
            // Implementation for loading more achievements
            const button = $('.load-more-achievements');
            button.text('Loading...').prop('disabled', true);
            
            // This would typically load more achievements via AJAX
            setTimeout(() => {
                button.text('Load More').prop('disabled', false);
            }, 1000);
        }

        refreshLeaderboard() {
            const leaderboard = $('.aiddata-leaderboard');
            leaderboard.addClass('loading');
            
            $.ajax({
                url: aiddata_lms_gamification.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_get_leaderboard',
                    nonce: aiddata_lms_gamification.nonce,
                    limit: 10
                },
                success: (response) => {
                    if (response.success) {
                        this.updateLeaderboard(response.data);
                    }
                },
                complete: () => {
                    leaderboard.removeClass('loading');
                }
            });
        }

        updateLeaderboard(data) {
            const leaderboardList = $('.leaderboard-list');
            leaderboardList.empty();
            
            data.forEach((user, index) => {
                const item = $(`
                    <div class="leaderboard-item">
                        <span class="rank">#${index + 1}</span>
                        <span class="user-name">${user.display_name}</span>
                        <span class="user-level">Level ${user.level}</span>
                        <span class="user-points">${this.formatNumber(user.total_points)} pts</span>
                    </div>
                `);
                
                leaderboardList.append(item);
            });
        }

        showAchievementDetails(badge) {
            const title = badge.find('h4').text();
            const description = badge.find('p').text();
            const earnedDate = badge.find('.earned-date').text();
            
            const modal = $(`
                <div class="aiddata-modal-overlay">
                    <div class="aiddata-modal">
                        <div class="modal-header">
                            <h3>${title}</h3>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>${description}</p>
                            <p><strong>${earnedDate}</strong></p>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            
            modal.on('click', '.modal-close, .aiddata-modal-overlay', (e) => {
                if (e.target === e.currentTarget) {
                    modal.remove();
                }
            });
        }

        showNotification(message, type = 'info') {
            const notification = $(`
                <div class="aiddata-notification ${type}">
                    ${message}
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(() => {
                notification.addClass('show');
            }, 100);
            
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        }

        // Public methods for triggering celebrations
        triggerPointsEarned(points, reason) {
            this.showCelebration({
                type: 'points',
                data: { points, reason }
            });
        }

        triggerAchievementUnlocked(achievement, pointsReward) {
            this.showCelebration({
                type: 'achievement',
                data: { achievement, points_reward: pointsReward }
            });
        }

        triggerLevelUp(newLevel, pointsNeeded) {
            this.showCelebration({
                type: 'level_up',
                data: { new_level: newLevel, points_needed_for_next: pointsNeeded }
            });
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        if (typeof aiddata_lms_gamification !== 'undefined') {
            window.aidDataGamification = new AidDataGamification();
        }
    });

    // Progress tracking helpers
    window.aidDataLMSProgress = {
        updateProgress: function(type, data) {
            // This would be called when progress events occur
            // e.g., aidDataLMSProgress.updateProgress('lesson_completed', {lesson_id: 123});
            
            $.ajax({
                url: aiddata_lms_gamification.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_track_progress',
                    nonce: aiddata_lms_gamification.nonce,
                    progress_type: type,
                    progress_data: data
                },
                success: (response) => {
                    if (response.success && response.data.celebration) {
                        window.aidDataGamification.showCelebration(response.data.celebration);
                    }
                }
            });
        }
    };

})(jQuery);
