jQuery(document).ready(function($) {
    var currentQuestion = 0;
    var attemptId = null;
    var timerInterval = null;
    var timeRemaining = quizData.timeLimit * 60; // Convert to seconds

    // Start quiz
    $('#start-quiz').on('click', function() {
        startQuiz();
    });

    function startQuiz() {
        // Create quiz attempt
        $.ajax({
            url: quizData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'start_quiz_attempt',
                quiz_id: quizData.quizId,
                user_id: quizData.userId,
                nonce: quizData.nonce
            },
            success: function(response) {
                if (response.success) {
                    attemptId = response.data.attempt_id;
                    $('#attempt-id').val(attemptId);
                    
                    // Hide instructions, show quiz
                    $('#quiz-instructions').fadeOut(300, function() {
                        $('#quiz-form').fadeIn(300);
                        $('#quiz-nav').fadeIn(300);
                        
                        // Start timer if needed
                        if (quizData.timeLimit > 0) {
                            startTimer();
                        }
                        
                        // Show first question
                        showQuestion(0);
                    });
                } else {
                    alert('Error starting quiz: ' + response.data.message);
                }
            },
            error: function() {
                alert('Error connecting to server. Please try again.');
            }
        });
    }

    // Timer functionality
    function startTimer() {
        $('#quiz-timer').fadeIn(300);
        
        timerInterval = setInterval(function() {
            timeRemaining--;
            
            var minutes = Math.floor(timeRemaining / 60);
            var seconds = timeRemaining % 60;
            
            $('#timer-minutes').text(String(minutes).padStart(2, '0'));
            $('#timer-seconds').text(String(seconds).padStart(2, '0'));
            
            // Warning when 5 minutes left
            if (timeRemaining === 300) {
                alert('5 minutes remaining!');
                $('#quiz-timer').addClass('timer-warning');
            }
            
            // Warning when 1 minute left
            if (timeRemaining === 60) {
                alert('1 minute remaining!');
                $('#quiz-timer').addClass('timer-critical');
            }
            
            // Time's up
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                alert('Time is up! Submitting your quiz...');
                submitQuiz();
            }
        }, 1000);
    }

    // Question navigation
    function showQuestion(index) {
        $('.quiz-question').hide();
        $('.quiz-question[data-question-index="' + index + '"]').show();
        currentQuestion = index;
        
        // Update navigation buttons
        $('.question-nav-btn').removeClass('active current');
        $('.question-nav-btn[data-question="' + index + '"]').addClass('active current');
        
        // Update answered status
        updateQuestionStatus();
        
        // Scroll to top
        $('.quiz-main').animate({ scrollTop: 0 }, 300);
    }

    // Next question
    $(document).on('click', '.next-question', function() {
        var questionIndex = $(this).data('question');
        if (questionIndex < quizData.totalQuestions - 1) {
            showQuestion(questionIndex + 1);
        }
    });

    // Previous question
    $(document).on('click', '.prev-question', function() {
        var questionIndex = $(this).data('question');
        if (questionIndex > 0) {
            showQuestion(questionIndex - 1);
        }
    });

    // Question navigation buttons
    $(document).on('click', '.question-nav-btn', function() {
        var questionIndex = $(this).data('question');
        showQuestion(questionIndex);
    });

    // Review quiz
    $('#review-quiz').on('click', function() {
        showReviewScreen();
    });

    function showReviewScreen() {
        $('#quiz-questions').hide();
        $('#quiz-review').fadeIn(300);
        updateReviewStatus();
    }

    // Back to quiz from review
    $('#back-to-quiz').on('click', function() {
        $('#quiz-review').hide();
        $('#quiz-questions').fadeIn(300);
        showQuestion(currentQuestion);
    });

    // Go to question from review
    $(document).on('click', '.goto-question', function() {
        var questionIndex = $(this).data('question');
        $('#quiz-review').hide();
        $('#quiz-questions').fadeIn(300);
        showQuestion(questionIndex);
    });

    // Update question answered status
    function updateQuestionStatus() {
        $('.quiz-question').each(function() {
            var questionIndex = $(this).data('question-index');
            var isAnswered = isQuestionAnswered($(this));
            
            var navBtn = $('.question-nav-btn[data-question="' + questionIndex + '"]');
            if (isAnswered) {
                navBtn.addClass('answered');
            } else {
                navBtn.removeClass('answered');
            }
        });
    }

    function isQuestionAnswered($question) {
        // Check for radio buttons
        if ($question.find('input[type="radio"]:checked').length > 0) {
            return true;
        }
        
        // Check for textareas
        if ($question.find('textarea').length > 0) {
            return $question.find('textarea').val().trim() !== '';
        }
        
        // Check for text inputs
        if ($question.find('input[type="text"]').length > 0) {
            var allFilled = true;
            $question.find('input[type="text"]').each(function() {
                if ($(this).val().trim() === '') {
                    allFilled = false;
                    return false;
                }
            });
            return allFilled;
        }
        
        // Check for selects
        if ($question.find('select').length > 0) {
            var allSelected = true;
            $question.find('select').each(function() {
                if ($(this).val() === '') {
                    allSelected = false;
                    return false;
                }
            });
            return allSelected;
        }
        
        // Check for ordering
        if ($question.find('.ordering-value').length > 0) {
            return $question.find('.ordering-value').val() !== '';
        }
        
        return false;
    }

    function updateReviewStatus() {
        $('.quiz-question').each(function() {
            var questionIndex = $(this).data('question-index');
            var isAnswered = isQuestionAnswered($(this));
            
            var statusIndicator = $('.status-indicator[data-question="' + questionIndex + '"]');
            if (isAnswered) {
                statusIndicator.text('Answered').removeClass('not-answered').addClass('answered');
            } else {
                statusIndicator.text('Not answered').removeClass('answered').addClass('not-answered');
            }
        });
    }

    // Auto-update status on input change
    $(document).on('change input', '.quiz-question input, .quiz-question textarea, .quiz-question select', function() {
        updateQuestionStatus();
    });

    // Character counter for short answer
    $(document).on('input', '.quiz-textarea:not(.essay-textarea)', function() {
        var current = $(this).val().length;
        $(this).siblings('.char-counter').find('.current').text(current);
    });

    // Word counter for essay
    $(document).on('input', '.essay-textarea', function() {
        var text = $(this).val().trim();
        var words = text === '' ? 0 : text.split(/\s+/).length;
        $(this).siblings('.word-counter').find('.current').text(words);
    });

    // Drag and drop for ordering questions
    var draggedItem = null;

    $(document).on('dragstart', '.ordering-item', function(e) {
        draggedItem = this;
        $(this).addClass('dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
    });

    $(document).on('dragend', '.ordering-item', function() {
        $(this).removeClass('dragging');
        draggedItem = null;
    });

    $(document).on('dragover', '.ordering-item', function(e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        
        var $this = $(this);
        if (draggedItem && draggedItem !== this) {
            var rect = this.getBoundingClientRect();
            var midpoint = rect.top + rect.height / 2;
            
            if (e.originalEvent.clientY < midpoint) {
                $(draggedItem).insertBefore($this);
            } else {
                $(draggedItem).insertAfter($this);
            }
        }
    });

    $(document).on('drop', '.ordering-item', function(e) {
        e.preventDefault();
        updateOrderingValue($(this).closest('.ordering-question'));
        updateQuestionStatus();
    });

    function updateOrderingValue($orderingQuestion) {
        var items = [];
        $orderingQuestion.find('.ordering-item').each(function() {
            items.push($(this).data('item'));
        });
        $orderingQuestion.find('.ordering-value').val(JSON.stringify(items));
    }

    // Submit quiz
    $('#submit-quiz').on('click', function(e) {
        e.preventDefault();
        
        // Check if all questions are answered
        var unansweredCount = 0;
        $('.quiz-question').each(function() {
            if (!isQuestionAnswered($(this))) {
                unansweredCount++;
            }
        });
        
        if (unansweredCount > 0) {
            if (!confirm('You have ' + unansweredCount + ' unanswered question(s). Are you sure you want to submit?')) {
                return;
            }
        }
        
        submitQuiz();
    });

    function submitQuiz() {
        // Clear timer
        if (timerInterval) {
            clearInterval(timerInterval);
        }
        
        // Disable submit button
        $('#submit-quiz').prop('disabled', true).text('Submitting...');
        
        // Collect answers
        var formData = new FormData($('#quiz-form')[0]);
        formData.append('action', 'submit_quiz');
        formData.append('nonce', quizData.nonce);
        
        $.ajax({
            url: quizData.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showResults(response.data);
                } else {
                    alert('Error submitting quiz: ' + response.data.message);
                    $('#submit-quiz').prop('disabled', false).text('Submit Quiz');
                }
            },
            error: function() {
                alert('Error connecting to server. Please try again.');
                $('#submit-quiz').prop('disabled', false).text('Submit Quiz');
            }
        });
    }

    function showResults(data) {
        // Hide quiz form and review
        $('#quiz-form').hide();
        $('#quiz-review').hide();
        $('#quiz-nav').hide();
        $('#quiz-timer').hide();
        
        // Build results HTML
        var resultsHtml = '<div class="quiz-results-panel">';
        resultsHtml += '<div class="results-header">';
        resultsHtml += '<h2>Quiz Results</h2>';
        resultsHtml += '</div>';
        
        resultsHtml += '<div class="results-summary">';
        resultsHtml += '<div class="result-card ' + (data.passed ? 'passed' : 'failed') + '">';
        resultsHtml += '<div class="result-label">Your Score</div>';
        resultsHtml += '<div class="result-score">' + data.score.toFixed(1) + '%</div>';
        resultsHtml += '<div class="result-points">' + data.earned_points + ' / ' + data.total_points + ' points</div>';
        resultsHtml += '</div>';
        
        resultsHtml += '<div class="result-card">';
        resultsHtml += '<div class="result-label">Passing Grade</div>';
        resultsHtml += '<div class="result-value">' + data.passing_grade + '%</div>';
        resultsHtml += '</div>';
        
        resultsHtml += '<div class="result-card">';
        resultsHtml += '<div class="result-label">Status</div>';
        resultsHtml += '<div class="result-status ' + (data.passed ? 'passed' : 'failed') + '">';
        resultsHtml += data.passed ? 'Passed' : 'Failed';
        resultsHtml += '</div>';
        resultsHtml += '</div>';
        resultsHtml += '</div>';
        
        // Show detailed results if enabled
        if (quizData.showCorrectAnswers && data.questions) {
            resultsHtml += '<div class="results-details">';
            resultsHtml += '<h3>Question Details</h3>';
            
            data.questions.forEach(function(question, index) {
                resultsHtml += '<div class="result-question ' + (question.correct ? 'correct' : 'incorrect') + '">';
                resultsHtml += '<div class="result-question-header">';
                resultsHtml += '<span class="result-question-number">Question ' + (index + 1) + '</span>';
                resultsHtml += '<span class="result-question-points">' + question.earned + ' / ' + question.points + ' points</span>';
                resultsHtml += '</div>';
                
                resultsHtml += '<div class="result-question-text">' + question.question + '</div>';
                
                resultsHtml += '<div class="result-answer-section">';
                resultsHtml += '<div class="result-answer"><strong>Your Answer:</strong> ' + question.user_answer + '</div>';
                
                if (!question.correct && question.correct_answer) {
                    resultsHtml += '<div class="result-correct-answer"><strong>Correct Answer:</strong> ' + question.correct_answer + '</div>';
                }
                
                if (question.explanation) {
                    resultsHtml += '<div class="result-explanation"><strong>Explanation:</strong> ' + question.explanation + '</div>';
                }
                resultsHtml += '</div>';
                resultsHtml += '</div>';
            });
            
            resultsHtml += '</div>';
        }
        
        // Action buttons
        resultsHtml += '<div class="results-actions">';
        if (data.can_retake) {
            resultsHtml += '<button type="button" class="button button-primary" onclick="location.reload()">Retake Quiz</button>';
        }
        resultsHtml += '<a href="' + data.back_url + '" class="button">Back to Course</a>';
        resultsHtml += '</div>';
        
        resultsHtml += '</div>';
        
        // Display results
        $('#quiz-results').html(resultsHtml).fadeIn(300);
    }

    // Prevent accidental page close
    var quizStarted = false;
    $('#start-quiz').on('click', function() {
        quizStarted = true;
    });

    $(window).on('beforeunload', function() {
        if (quizStarted && $('#quiz-form').is(':visible')) {
            return 'Are you sure you want to leave? Your quiz progress will be lost.';
        }
    });

    // Auto-save progress every 30 seconds
    setInterval(function() {
        if (attemptId && $('#quiz-form').is(':visible')) {
            saveProgress();
        }
    }, 30000);

    function saveProgress() {
        var formData = new FormData($('#quiz-form')[0]);
        formData.append('action', 'save_quiz_progress');
        formData.append('nonce', quizData.nonce);
        
        $.ajax({
            url: quizData.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Progress saved');
            }
        });
    }
});

