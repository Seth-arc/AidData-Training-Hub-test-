<?php
/**
 * Single Quiz Template
 * 
 * Displays a single quiz with questions and handles submissions
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 * @since 1.0.0
 */

get_header();

// Get current quiz
global $post;
$quiz = new AidData_LMS_Quiz($post->ID);
$user_id = get_current_user_id();
$is_logged_in = is_user_logged_in();

// Apply onboarding filter
$onboarding_content = apply_filters('aiddata_lms_before_content', '', $post);

// Get quiz data
$questions = $quiz->get_questions();
$passing_grade = $quiz->get_passing_grade();
$time_limit = $quiz->get_time_limit();
$attempts_allowed = $quiz->get_attempts_allowed();
$randomize_questions = $quiz->get_randomize_questions();
$show_correct_answers = $quiz->get_show_correct_answers();
$show_results_immediately = $quiz->get_show_results_immediately();
$retake_allowed = $quiz->get_retake_allowed();

// Debug: Check if questions are loading - SHOW ON SCREEN FOR DEBUGGING
if (current_user_can('administrator')) {
    $raw_json = get_post_meta($quiz->get_id(), '_quiz_questions', true);
    $decoded = json_decode($raw_json, true);
    
    echo '<div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; margin: 20px; border-radius: 8px;">';
    echo '<h3 style="color: #856404;">DEBUG INFO (Admin Only)</h3>';
    echo '<p><strong>Quiz ID:</strong> ' . $quiz->get_id() . '</p>';
    echo '<p><strong>Questions is array:</strong> ' . (is_array($questions) ? 'YES' : 'NO') . '</p>';
    echo '<p><strong>Questions count:</strong> ' . (is_array($questions) ? count($questions) : 'N/A') . '</p>';
    
    echo '<p><strong>Raw JSON keys:</strong> ' . (is_array($decoded) ? implode(', ', array_keys($decoded)) : 'N/A') . '</p>';
    
    echo '<p><strong>Per-question analysis:</strong></p>';
    if (is_array($decoded)) {
        foreach ($decoded as $key => $q) {
            $has_type = is_array($q) && isset($q['type']);
            echo '<div style="margin: 5px 0; padding: 5px; background: ' . ($has_type ? '#d4edda' : '#f8d7da') . ';">';
            echo "Key: <code>$key</code>, ";
            echo "is_numeric: " . (is_numeric($key) ? '✓' : '✗') . ", ";
            echo "is_array: " . (is_array($q) ? '✓' : '✗') . ", ";
            echo "has_type: " . ($has_type ? '✓' : '✗');
            if ($has_type) echo " (type: " . $q['type'] . ")";
            echo '</div>';
        }
    }
    
    echo '<p><strong>Decoded questions from get_questions():</strong></p>';
    echo '<pre style="background: #f8f9fa; padding: 10px; overflow-x: auto; max-height: 300px;">' . htmlspecialchars(print_r($questions, true)) . '</pre>';
    echo '</div>';
}

// Check if user has already taken the quiz
global $wpdb;
$table_name = $wpdb->prefix . 'aiddata_lms_quiz_attempts';
$attempts = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$table_name} WHERE user_id = %d AND quiz_id = %d ORDER BY started_date DESC",
    $user_id,
    $quiz->get_id()
));

$attempts_taken = count($attempts);
$can_take_quiz = ($attempts_allowed == 0 || $attempts_taken < $attempts_allowed || $retake_allowed);
$last_attempt = $attempts_taken > 0 ? $attempts[0] : null;

// Randomize questions if needed
if ($randomize_questions && is_array($questions)) {
    shuffle($questions);
}

// Calculate total points
$total_points = 0;
if (is_array($questions)) {
    foreach ($questions as $question) {
        $total_points += isset($question['points']) ? intval($question['points']) : 1;
    }
}
?>

<?php 
// Output onboarding popup if available
if (!empty($onboarding_content)) {
    echo $onboarding_content;
}
?>

<div class="quiz-container">
    <div class="quiz-header">
        <div class="quiz-breadcrumb">
            <?php
            $course_id = $quiz->get_course_id();
            if ($course_id) {
                echo '<a href="' . get_permalink($course_id) . '">' . get_the_title($course_id) . '</a> / ';
            }
            ?>
            <span><?php the_title(); ?></span>
        </div>
        
        <h1 class="quiz-title"><?php the_title(); ?></h1>
        
        <?php if ($quiz->get_description()): ?>
            <div class="quiz-description">
                <?php echo wpautop($quiz->get_description()); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$is_logged_in): ?>
        <!-- Login Required Message -->
        <div class="quiz-login-required">
            <div class="notice notice-warning">
                <h3><?php _e('Login Required', 'aiddata-lms'); ?></h3>
                <p><?php _e('You must be logged in to take this quiz.', 'aiddata-lms'); ?></p>
                <a href="<?php echo wp_login_url(get_permalink()); ?>" class="button button-primary">
                    <?php _e('Log In', 'aiddata-lms'); ?>
                </a>
            </div>
        </div>

    <?php elseif (!$can_take_quiz && !$retake_allowed): ?>
        <!-- No More Attempts Message -->
        <div class="quiz-no-attempts">
            <div class="notice notice-error">
                <h3><?php _e('No Attempts Remaining', 'aiddata-lms'); ?></h3>
                <p><?php printf(__('You have used all %d attempts for this quiz.', 'aiddata-lms'), $attempts_allowed); ?></p>
            </div>
        </div>

    <?php else: ?>
        <?php if (empty($questions) || !is_array($questions) || count($questions) === 0): ?>
            <!-- No Questions Message -->
            <div class="quiz-no-questions">
                <div class="notice notice-warning">
                    <h3><?php _e('Quiz Not Ready', 'aiddata-lms'); ?></h3>
                    <p><?php _e('This quiz does not have any questions yet. Please contact the administrator.', 'aiddata-lms'); ?></p>
                    <?php if (current_user_can('manage_options')): ?>
                        <p><strong><?php _e('Administrator:', 'aiddata-lms'); ?></strong> <a href="<?php echo admin_url('admin.php?page=aiddata-lms-quiz-builder&quiz_id=' . $quiz->get_id()); ?>" class="button button-primary"><?php _e('Add Questions to Quiz', 'aiddata-lms'); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
        <!-- Quiz Content -->
        <div class="quiz-content">
            
            <!-- Quiz Info Sidebar -->
            <div class="quiz-sidebar">
                <div class="quiz-info-card">
                    <h3><?php _e('Quiz Information', 'aiddata-lms'); ?></h3>
                    
                    <div class="quiz-info-item">
                        <span class="info-label"><?php _e('Questions:', 'aiddata-lms'); ?></span>
                        <span class="info-value"><?php echo count($questions); ?></span>
                    </div>
                    
                    <div class="quiz-info-item">
                        <span class="info-label"><?php _e('Total Points:', 'aiddata-lms'); ?></span>
                        <span class="info-value"><?php echo $total_points; ?></span>
                    </div>
                    
                    <div class="quiz-info-item">
                        <span class="info-label"><?php _e('Passing Grade:', 'aiddata-lms'); ?></span>
                        <span class="info-value"><?php echo $passing_grade; ?>%</span>
                    </div>
                    
                    <?php if ($time_limit > 0): ?>
                    <div class="quiz-info-item">
                        <span class="info-label"><?php _e('Time Limit:', 'aiddata-lms'); ?></span>
                        <span class="info-value"><?php echo $time_limit; ?> <?php _e('minutes', 'aiddata-lms'); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="quiz-info-item">
                        <span class="info-label"><?php _e('Attempts:', 'aiddata-lms'); ?></span>
                        <span class="info-value">
                            <?php 
                            if ($attempts_allowed == 0) {
                                _e('Unlimited', 'aiddata-lms');
                            } else {
                                echo $attempts_taken . ' / ' . $attempts_allowed;
                            }
                            ?>
                        </span>
                    </div>
                    
                    <?php if ($last_attempt): ?>
                    <div class="quiz-info-item">
                        <span class="info-label"><?php _e('Last Score:', 'aiddata-lms'); ?></span>
                        <span class="info-value <?php echo $last_attempt->score >= $passing_grade ? 'score-pass' : 'score-fail'; ?>">
                            <?php echo round($last_attempt->score, 1); ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($time_limit > 0): ?>
                <div class="quiz-timer-card" id="quiz-timer" style="display:none;">
                    <h3><?php _e('Time Remaining', 'aiddata-lms'); ?></h3>
                    <div class="timer-display">
                        <span id="timer-minutes">00</span>:<span id="timer-seconds">00</span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Question Navigation -->
                <div class="quiz-navigation-card" id="quiz-nav" style="display:none;">
                    <h3><?php _e('Questions', 'aiddata-lms'); ?></h3>
                    <div class="question-nav-grid">
                        <?php foreach ($questions as $index => $question): ?>
                            <button type="button" class="question-nav-btn" data-question="<?php echo $index; ?>">
                                <?php echo $index + 1; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Quiz Main Area -->
            <div class="quiz-main">
                
                <!-- Pre-Quiz Instructions -->
                <div id="quiz-instructions" class="quiz-instructions-panel">
                    <?php if ($quiz->get_instructions()): ?>
                        <div class="instructions-content">
                            <?php echo wpautop($quiz->get_instructions()); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="instructions-actions">
                        <button type="button" class="button button-primary button-large" id="start-quiz">
                            <?php _e('Start Quiz', 'aiddata-lms'); ?>
                        </button>
                    </div>
                </div>

                <!-- Quiz Form -->
                <form id="quiz-form" class="quiz-form" style="display:none;">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz->get_id(); ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="attempt_id" id="attempt-id" value="">
                    
                    <div id="quiz-questions">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="quiz-question" data-question-index="<?php echo $index; ?>" style="<?php echo $index > 0 ? 'display:none;' : ''; ?>">
                                <div class="question-header">
                                    <span class="question-number">
                                        <?php printf(__('Question %d of %d', 'aiddata-lms'), $index + 1, count($questions)); ?>
                                    </span>
                                    <span class="question-points">
                                        <?php printf(__('%d points', 'aiddata-lms'), isset($question['points']) ? $question['points'] : 1); ?>
                                    </span>
                                </div>
                                
                                <div class="question-text">
                                    <?php echo wpautop(esc_html($question['question'])); ?>
                                </div>
                                
                                <div class="question-answer">
                                    <?php echo render_question_input($question, $index); ?>
                                </div>
                                
                                <div class="question-actions">
                                    <?php if ($index > 0): ?>
                                        <button type="button" class="button prev-question" data-question="<?php echo $index; ?>">
                                            <?php _e('Previous', 'aiddata-lms'); ?>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($index < count($questions) - 1): ?>
                                        <button type="button" class="button button-primary next-question" data-question="<?php echo $index; ?>">
                                            <?php _e('Next', 'aiddata-lms'); ?>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="button button-primary" id="review-quiz">
                                            <?php _e('Review Answers', 'aiddata-lms'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Review Screen -->
                    <div id="quiz-review" style="display:none;">
                        <h2><?php _e('Review Your Answers', 'aiddata-lms'); ?></h2>
                        <p><?php _e('Please review your answers before submitting. You can go back and change any answer.', 'aiddata-lms'); ?></p>
                        
                        <div class="review-list">
                            <?php foreach ($questions as $index => $question): ?>
                                <div class="review-item" data-question="<?php echo $index; ?>">
                                    <div class="review-question">
                                        <strong><?php printf(__('Question %d:', 'aiddata-lms'), $index + 1); ?></strong>
                                        <?php echo esc_html(wp_trim_words($question['question'], 10)); ?>
                                    </div>
                                    <div class="review-status">
                                        <span class="status-indicator" data-question="<?php echo $index; ?>">
                                            <?php _e('Not answered', 'aiddata-lms'); ?>
                                        </span>
                                        <button type="button" class="button-link goto-question" data-question="<?php echo $index; ?>">
                                            <?php _e('Edit', 'aiddata-lms'); ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="review-actions">
                            <button type="button" class="button" id="back-to-quiz">
                                <?php _e('Back to Quiz', 'aiddata-lms'); ?>
                            </button>
                            <button type="submit" class="button button-primary button-large" id="submit-quiz">
                                <?php _e('Submit Quiz', 'aiddata-lms'); ?>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Results Screen -->
                <div id="quiz-results" style="display:none;">
                    <!-- Results will be loaded via AJAX -->
                </div>

            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
var quizData = {
    quizId: <?php echo $quiz->get_id(); ?>,
    userId: <?php echo $user_id; ?>,
    timeLimit: <?php echo $time_limit; ?>,
    totalQuestions: <?php echo count($questions); ?>,
    showCorrectAnswers: <?php echo $show_correct_answers ? 'true' : 'false'; ?>,
    showResultsImmediately: <?php echo $show_results_immediately ? 'true' : 'false'; ?>,
    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('aiddata_lms_quiz_nonce'); ?>'
};
</script>

<?php
get_footer();

// Helper function to render question inputs
function render_question_input($question, $index) {
    $type = $question['type'];
    $name = "answers[{$index}]";
    
    ob_start();
    
    switch ($type) {
        case 'multiple_choice':
            if (isset($question['options']) && is_array($question['options'])) {
                echo '<div class="answer-options">';
                foreach ($question['options'] as $opt_index => $option) {
                    echo '<label class="answer-option">';
                    echo '<input type="radio" name="' . esc_attr($name) . '" value="' . esc_attr($opt_index) . '" required>';
                    echo '<span>' . esc_html($option) . '</span>';
                    echo '</label>';
                }
                echo '</div>';
            }
            break;
            
        case 'true_false':
            echo '<div class="answer-options">';
            echo '<label class="answer-option">';
            echo '<input type="radio" name="' . esc_attr($name) . '" value="true" required>';
            echo '<span>' . __('True', 'aiddata-lms') . '</span>';
            echo '</label>';
            echo '<label class="answer-option">';
            echo '<input type="radio" name="' . esc_attr($name) . '" value="false" required>';
            echo '<span>' . __('False', 'aiddata-lms') . '</span>';
            echo '</label>';
            echo '</div>';
            break;
            
        case 'short_answer':
            $char_limit = isset($question['char_limit']) ? intval($question['char_limit']) : 500;
            echo '<textarea name="' . esc_attr($name) . '" class="quiz-textarea" rows="4" required';
            if ($char_limit > 0) {
                echo ' maxlength="' . $char_limit . '"';
            }
            echo '></textarea>';
            if ($char_limit > 0) {
                echo '<div class="char-counter"><span class="current">0</span> / ' . $char_limit . '</div>';
            }
            break;
            
        case 'essay':
            $min_words = isset($question['min_words']) ? intval($question['min_words']) : 200;
            echo '<textarea name="' . esc_attr($name) . '" class="quiz-textarea essay-textarea" rows="10" required></textarea>';
            echo '<div class="word-counter">' . sprintf(__('Minimum %d words required', 'aiddata-lms'), $min_words) . ' - <span class="current">0</span> ' . __('words', 'aiddata-lms') . '</div>';
            break;
            
        case 'fill_blank':
            $blank_count = substr_count($question['question'], '{blank}');
            echo '<div class="fill-blank-inputs">';
            for ($i = 0; $i < $blank_count; $i++) {
                echo '<input type="text" name="' . esc_attr($name) . '[' . $i . ']" class="fill-blank-input" placeholder="' . sprintf(__('Blank %d', 'aiddata-lms'), $i + 1) . '" required>';
            }
            echo '</div>';
            break;
            
        case 'matching':
            if (isset($question['pairs']) && is_array($question['pairs'])) {
                // Get left and right items
                $left_items = array_column($question['pairs'], 'left');
                $right_items = array_column($question['pairs'], 'right');
                shuffle($right_items); // Randomize right items
                
                echo '<div class="matching-question">';
                foreach ($left_items as $pair_index => $left_item) {
                    echo '<div class="matching-pair">';
                    echo '<span class="left-item">' . esc_html($left_item) . '</span>';
                    echo '<select name="' . esc_attr($name) . '[' . $pair_index . ']" required>';
                    echo '<option value="">' . __('Select match...', 'aiddata-lms') . '</option>';
                    foreach ($right_items as $right_index => $right_item) {
                        echo '<option value="' . esc_attr($right_index) . '">' . esc_html($right_item) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                }
                echo '</div>';
            }
            break;
            
        case 'ordering':
            if (isset($question['items']) && is_array($question['items'])) {
                $items = $question['items'];
                shuffle($items); // Randomize for user
                
                echo '<div class="ordering-question" data-question="' . $index . '">';
                echo '<p class="ordering-instructions">' . __('Drag and drop to reorder:', 'aiddata-lms') . '</p>';
                echo '<ul class="ordering-list">';
                foreach ($items as $item_index => $item) {
                    echo '<li class="ordering-item" draggable="true" data-item="' . esc_attr($item) . '">';
                    echo '<span class="drag-handle">☰</span>';
                    echo '<span class="item-text">' . esc_html($item) . '</span>';
                    echo '</li>';
                }
                echo '</ul>';
                echo '<input type="hidden" name="' . esc_attr($name) . '" class="ordering-value">';
                echo '</div>';
            }
            break;
    }
    
    return ob_get_clean();
}
?>

