/**
 * AidData LMS Frontend JavaScript
 * 
 * Handles frontend interactions for courses, lessons, quizzes, and simulations.
 * 
 * @package AidData_LMS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Frontend Module
     */
    var AidDataFrontend = {
        
        /**
         * Initialize the frontend module
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Mark lesson as complete
            $(document).on('click', '.mark-lesson-complete', this.markLessonComplete);
            
            // Submit quiz
            $(document).on('click', '.submit-quiz', this.submitQuiz);
            
            // Update simulation progress
            $(document).on('simulation-progress-update', this.updateSimulationProgress);
        },

        /**
         * Mark lesson as complete
         */
        markLessonComplete: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var lessonId = $button.data('lesson-id');
            
            if (!lessonId) {
                return;
            }
            
            $.ajax({
                url: aiddata_lms_frontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_lesson_complete',
                    nonce: aiddata_lms_frontend.nonce,
                    lesson_id: lessonId
                },
                beforeSend: function() {
                    $button.prop('disabled', true).text(aiddata_lms_frontend.strings.loading || 'Loading...');
                },
                success: function(response) {
                    if (response.success) {
                        $button.text(aiddata_lms_frontend.strings.lesson_completed);
                        // Trigger custom event for other modules to listen to
                        $(document).trigger('lesson-completed', [lessonId]);
                    } else {
                        alert(response.data || aiddata_lms_frontend.strings.error);
                        $button.prop('disabled', false).text('Mark Complete');
                    }
                },
                error: function() {
                    alert(aiddata_lms_frontend.strings.error);
                    $button.prop('disabled', false).text('Mark Complete');
                }
            });
        },

        /**
         * Submit quiz
         */
        submitQuiz: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $form = $button.closest('form');
            var quizId = $form.data('quiz-id');
            
            if (!quizId) {
                return;
            }
            
            // Collect answers
            var answers = {};
            $form.find('input[type="radio"]:checked, input[type="checkbox"]:checked, select').each(function() {
                var name = $(this).attr('name');
                var value = $(this).val();
                
                if (name && value) {
                    // Handle multiple answers (checkboxes)
                    if (answers[name]) {
                        if (!Array.isArray(answers[name])) {
                            answers[name] = [answers[name]];
                        }
                        answers[name].push(value);
                    } else {
                        answers[name] = value;
                    }
                }
            });
            
            // Confirm submission
            if (!confirm(aiddata_lms_frontend.strings.confirm_submit)) {
                return;
            }
            
            $.ajax({
                url: aiddata_lms_frontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_quiz_submit',
                    nonce: aiddata_lms_frontend.nonce,
                    quiz_id: quizId,
                    answers: JSON.stringify(answers)
                },
                beforeSend: function() {
                    $button.prop('disabled', true).text(aiddata_lms_frontend.strings.loading || 'Submitting...');
                },
                success: function(response) {
                    if (response.success) {
                        var message = aiddata_lms_frontend.strings.quiz_submitted;
                        if (response.data.score !== undefined) {
                            message += ' Score: ' + response.data.score + '%';
                            if (response.data.passed) {
                                message += ' (Passed)';
                            } else {
                                message += ' (Failed)';
                            }
                        }
                        alert(message);
                        
                        // Trigger custom event
                        $(document).trigger('quiz-completed', [quizId, response.data]);
                        
                        // Optionally reload or redirect
                        location.reload();
                    } else {
                        alert(response.data || aiddata_lms_frontend.strings.error);
                        $button.prop('disabled', false).text('Submit Quiz');
                    }
                },
                error: function() {
                    alert(aiddata_lms_frontend.strings.error);
                    $button.prop('disabled', false).text('Submit Quiz');
                }
            });
        },

        /**
         * Update simulation progress
         */
        updateSimulationProgress: function(e, simulationId, sessionData, currentStep, completionPercent) {
            if (!simulationId) {
                return;
            }
            
            $.ajax({
                url: aiddata_lms_frontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiddata_lms_simulation_update',
                    nonce: aiddata_lms_frontend.nonce,
                    simulation_id: simulationId,
                    session_data: JSON.stringify(sessionData || {}),
                    current_step: currentStep || 0,
                    completion_percent: completionPercent || 0
                },
                success: function(response) {
                    if (response.success) {
                        // Trigger custom event
                        $(document).trigger('simulation-progress-updated', [simulationId, sessionData, currentStep, completionPercent]);
                    } else {
                        console.error('Failed to update simulation progress:', response.data);
                    }
                },
                error: function() {
                    console.error('Error updating simulation progress');
                }
            });
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        AidDataFrontend.init();
    });

    /**
     * Make module globally accessible
     */
    window.AidDataFrontend = AidDataFrontend;

})(jQuery);
