jQuery(document).ready(function($) {
    var currentStep = tutorialData.currentStep || 0;
    var completedSteps = tutorialData.completedSteps || [];
    var totalSteps = tutorialData.totalSteps || 0;
    var steps = tutorialData.steps || [];
    var hasIntroduction = tutorialData.hasIntroduction || false;
    var isNavigating = false; // Prevent multiple rapid clicks

    // Function to navigate to a specific step
    function showStep(stepIndex) {
        // Allow introduction (-1) and valid step indices
        var minStep = hasIntroduction ? -1 : 0;
        if (stepIndex < minStep || stepIndex >= totalSteps) return;
        if (isNavigating) return; // Prevent multiple clicks
        
        isNavigating = true;
        
        // Save progress if logged in before navigating (but not for introduction)
        if (tutorialData.isLoggedIn && stepIndex >= 0) {
            updateProgress();
        }
        
        // Navigate to the step with a page reload for proper video rendering
        var url = new URL(window.location.href);
        if (stepIndex === -1) {
            url.searchParams.set('step', 'intro');
        } else {
            url.searchParams.set('step', stepIndex);
        }
        window.location.href = url.toString();
    }
    
    // Update navigation buttons visibility and state
    function updateNavigationButtons(stepIndex) {
        console.log('Updating navigation buttons for step:', stepIndex);
        
        var $stepNav = $('.step-navigation');
        if ($stepNav.length === 0) return;
        
        // Handle Previous button
        var $prevBtn = $('.prev-step');
        var minStep = hasIntroduction ? -1 : 0;
        
        if (stepIndex > minStep) {
            // Show or create Previous button
            if ($prevBtn.length === 0) {
                var prevText = (stepIndex === 0 && hasIntroduction) ? '← Previous Introduction' : '← Previous Step';
                $stepNav.prepend('<button class="btn btn-secondary prev-step">' + prevText + '</button>');
                $prevBtn = $('.prev-step');
            }
            $prevBtn.show().prop('disabled', false);
        } else {
            // Hide Previous button on first item (intro or step 0)
            $prevBtn.hide();
        }
        
        // Handle Next/Quiz/Complete buttons
        var $nextBtn = $('.next-step');
        var $quizBtn = $('a.btn-primary[href*="quiz"]');
        var $completeBtn = $('.complete-tutorial');
        
        // Hide all primary action buttons first
        $nextBtn.hide();
        $quizBtn.hide();
        $completeBtn.hide();
        
        // Check if we're on introduction
        if (stepIndex === -1) {
            // On introduction - show "Start Tutorial" or "Next Step" button
            if ($nextBtn.length === 0) {
                $stepNav.append('<button class="btn btn-primary next-step">Start Tutorial →</button>');
            } else {
                $nextBtn.text('Start Tutorial →').show();
            }
        } else if (stepIndex < totalSteps - 1) {
            // Not on last step - show Next button
            if ($nextBtn.length === 0) {
                // Create Next button if it doesn't exist
                $stepNav.append('<button class="btn btn-primary next-step">Next Step →</button>');
            } else {
                $nextBtn.text('Next Step →').show();
            }
        } else {
            // On last step - show Quiz or Complete button
            if (tutorialData.quizId > 0 && tutorialData.quizUrl) {
                // Show Quiz button
                if ($quizBtn.length === 0) {
                    var quizUrl = tutorialData.quizUrl + '?tutorial_id=' + tutorialData.tutorialId;
                    $stepNav.append('<a href="' + quizUrl + '" class="btn btn-primary">Take Quiz →</a>');
                } else {
                    $quizBtn.show();
                }
            } else {
                // Show Complete button
                if ($completeBtn.length === 0) {
                    $stepNav.append('<button class="btn btn-primary complete-tutorial">Complete Tutorial ✓</button>');
                } else {
                    $completeBtn.show();
                }
            }
        }
        
        console.log('Navigation buttons updated. Visible:', {
            prev: $prevBtn.is(':visible'),
            next: $nextBtn.is(':visible'),
            quiz: $quizBtn.is(':visible'),
            complete: $completeBtn.is(':visible')
        });
    }

    // Step navigation from sidebar
    $('.tutorial-item').on('click', function(e) {
        e.preventDefault();
        var stepIndex = parseInt($(this).data('step'));
        showStep(stepIndex);
    });

    // Previous step button
    $(document).on('click', '.prev-step', function(e) {
        e.preventDefault();
        var minStep = hasIntroduction ? -1 : 0;
        if (currentStep > minStep) {
            showStep(currentStep - 1);
        }
    });

    // Next step button
    $(document).on('click', '.next-step', function(e) {
        e.preventDefault();
        // Don't mark introduction as completed
        if (currentStep >= 0) {
            markStepCompleted(currentStep);
        }
        // Navigate to next step (from intro goes to step 0)
        if (currentStep === -1) {
            showStep(0); // From intro to first step
        } else if (currentStep < totalSteps - 1) {
            showStep(currentStep + 1);
        }
    });

    // Complete tutorial button (using delegated event since button may be dynamically created)
    $(document).on('click', '.complete-tutorial', function(e) {
        e.preventDefault();
        markStepCompleted(currentStep);
        completeTutorial();
    });
    
    // Initialize navigation buttons on page load
    updateNavigationButtons(currentStep);

    /**
     * Show a specific step (not used - using URL navigation instead)
     */
    /*
    function showStep(stepIndex) {
        // Hide all steps
        $('.step-content-container').removeClass('active');
        $('.tutorial-item').removeClass('active');

        // Show target step
        $('.step-content-container[data-step="' + stepIndex + '"]').addClass('active');
        $('.tutorial-item[data-step="' + stepIndex + '"]').addClass('active');

        // Update current step
        currentStep = stepIndex;

        // Update navigation buttons
        updateNavigationButtons();

        // Scroll to top - check if element exists
        var $tutorialMain = $('.tutorial-main');
        if ($tutorialMain.length) {
            $('html, body').animate({ scrollTop: $tutorialMain.offset().top - 100 }, 300);
        }

        // Update progress on server
        if (tutorialData.isLoggedIn) {
            updateProgress();
        }
    }
    */

    /**
     * Mark a step as completed
     */
    function markStepCompleted(stepIndex) {
        if (!completedSteps.includes(stepIndex)) {
            completedSteps.push(stepIndex);
            $('.tutorial-item[data-step="' + stepIndex + '"]').addClass('completed');
            
            // Update progress bar
            updateProgressBar();

            // Save to server
            if (tutorialData.isLoggedIn) {
                updateProgress();
            }
        }
    }


    /**
     * Update progress bar
     */
    function updateProgressBar() {
        var progressPercent = (completedSteps.length / totalSteps) * 100;
        $('.progress-bar').css('width', progressPercent + '%');
        $('.progress-bar-container').next('p').text(Math.round(progressPercent) + '% Complete');
    }

        /**
         * Update progress on server via AJAX (with debouncing)
         */
        function updateProgress() {
            // Use debouncer if available
            if (window.TutorialDebouncer) {
                window.TutorialDebouncer.queueUpdate({
                    tutorial_id: tutorialData.tutorialId,
                    user_id: tutorialData.userId,
                    current_step: currentStep,
                    completed_steps: completedSteps,
                    progress_percent: Math.round((completedSteps.length / totalSteps) * 100)
                });
            } else {
                // Fallback to direct AJAX
                var data = {
                    action: 'update_tutorial_progress',
                    nonce: tutorialData.nonce,
                    tutorial_id: tutorialData.tutorialId,
                    user_id: tutorialData.userId,
                    current_step: currentStep,
                    completed_steps: completedSteps,
                    progress_percent: Math.round((completedSteps.length / totalSteps) * 100)
                };

                $.ajax({
                    url: tutorialData.ajaxUrl,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            console.log('Progress updated successfully');
                        } else {
                            console.error('Error updating progress:', response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }
        }

    /**
     * Complete tutorial
     */
    function completeTutorial() {
        if (!tutorialData.isLoggedIn) {
            alert('Please log in to save your progress.');
            return;
        }

        var data = {
            action: 'complete_tutorial',
            nonce: tutorialData.nonce,
            tutorial_id: tutorialData.tutorialId,
            user_id: tutorialData.userId,
            completed_steps: completedSteps
        };

        $.ajax({
            url: tutorialData.ajaxUrl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    showCompletionMessage(response.data);
                } else {
                    alert('Error completing tutorial: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Error completing tutorial. Please try again.');
            }
        });
    }

    /**
     * Show completion message
     */
    function showCompletionMessage(certificateData) {
        var messageHtml = '<div class="completion-message">' +
            '<div class="completion-icon">🎉</div>' +
            '<h2 class="completion-title">Congratulations!</h2>' +
            '<p>You have successfully completed this tutorial.</p>';
        
        // Show certificate if available
        if (certificateData && certificateData.certificate_id) {
            messageHtml += '<div class="certificate-section" style="margin: 2rem 0; padding: 1.5rem; background: #f0f7f4; border: 2px solid #026447; border-radius: 8px;">' +
                '<h3 style="color: #026447; margin-top: 0;">🏆 Certificate Earned!</h3>' +
                '<p>You have earned a certificate for completing this tutorial.</p>' +
                '<a href="' + certificateData.certificate_url + '" class="btn btn-success" style="display: inline-block; margin-top: 1rem; background: #026447; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 6px;">Download Certificate</a>' +
                '</div>';
        }
        
        if (tutorialData.quizId > 0 && (!certificateData || !certificateData.certificate_id)) {
            var quizUrl = tutorialData.quizUrl ? tutorialData.quizUrl + '?tutorial_id=' + tutorialData.tutorialId : '';
            messageHtml += '<p>Take the quiz to test your knowledge and earn your certificate.</p>' +
                '<a href="' + quizUrl + '" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Take Quiz</a>';
        } else if (!tutorialData.quizId) {
            messageHtml += '<a href="/dashboard" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Back to Dashboard</a>';
        }
        
        messageHtml += '</div>';

        $('.tutorial-main').html(messageHtml);
    }

    /**
     * Handle video completion (if using custom video player)
     */
    $(document).on('videoComplete', function(e, videoElement) {
        // Automatically mark step as completed when video ends
        if ($(videoElement).closest('.step-content-container').hasClass('active')) {
            markStepCompleted(currentStep);
        }
    });

    /**
     * Glossary functionality
     */
    // Toggle glossary term
    $(document).on('click', '.glossary-term', function(e) {
        e.preventDefault();
        var $item = $(this).closest('.glossary-item');
        var wasActive = $item.hasClass('active');
        
        // Close all other items
        $('.glossary-item').removeClass('active');
        
        // Toggle this item
        if (!wasActive) {
            $item.addClass('active');
        }
    });

    // Search glossary
    $('#glossary-search').on('input', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        var $items = $('.glossary-item');
        var $noResults = $('.glossary-no-results');
        var visibleCount = 0;

        if (searchTerm === '') {
            // Show all items
            $items.removeClass('hidden').removeClass('active');
            $noResults.hide();
        } else {
            // Filter items
            $items.each(function() {
                var term = $(this).data('term');
                var $def = $(this).find('.glossary-definition');
                var defText = $def.text().toLowerCase();
                
                if (term.indexOf(searchTerm) !== -1 || defText.indexOf(searchTerm) !== -1) {
                    $(this).removeClass('hidden');
                    visibleCount++;
                } else {
                    $(this).addClass('hidden').removeClass('active');
                }
            });

            // Show/hide no results message
            if (visibleCount === 0) {
                $noResults.show();
            } else {
                $noResults.hide();
            }
        }
    });

    // Close glossary when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.tutorial-glossary').length) {
            $('.glossary-item').removeClass('active');
        }
    });
});

