<?php
/**
 * Single Tutorial Template
 * 
 * Displays a single tutorial with step-by-step content, video player, and progress tracking
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 * @since 1.0.0
 */

// Prevent infinite loop - guard against multiple template loads
if (defined('AIDDATA_SINGLE_TUTORIAL_LOADED')) {
    return;
}
define('AIDDATA_SINGLE_TUTORIAL_LOADED', true);

// Get current tutorial
global $post;

// Check if this tutorial should use the page builder template
$use_page_builder_template = get_post_meta($post->ID, '_use_page_builder_template', true);
if ($use_page_builder_template === '1' || $use_page_builder_template === 'yes') {
    // Load the page builder template instead
    $template_file = get_template_directory() . '/template-tutorial-page-builder.php';
    if (file_exists($template_file)) {
        // Set up the tutorial ID for the page builder template
        $tutorial_id = $post->ID;
        include($template_file);
        // Exit to prevent the classic template from loading
        exit;
    }
}

// Validate post exists
if (!$post || !isset($post->ID)) {
    wp_die(
        '<h1>Tutorial Not Found</h1><p>The tutorial you\'re looking for doesn\'t exist or has been removed.</p>',
        'Tutorial Not Found',
        array('response' => 404, 'back_link' => true)
    );
}

$tutorial = new AidData_LMS_Tutorial($post->ID);
$user_id = get_current_user_id();
$is_logged_in = is_user_logged_in();

// Validate tutorial loaded successfully
if (!$tutorial || !method_exists($tutorial, 'get_id') || !$tutorial->get_id()) {
    error_log('Tutorial load failed for post ID: ' . $post->ID);
    wp_die(
        '<h1>Tutorial Error</h1><p>This tutorial could not be loaded. Please contact support if this problem persists.</p>',
        'Tutorial Error',
        array('response' => 500, 'back_link' => true)
    );
}

// Apply onboarding filter
$onboarding_content = apply_filters('aiddata_lms_before_content', '', $post);

// Get tutorial data with error handling
try {
    $steps = $tutorial->get_steps();
    $steps = is_array($steps) ? $steps : array();
    
    $instructors = $tutorial->get_instructors();
    $instructors = is_array($instructors) ? $instructors : array();
    
    $quiz_id = $tutorial->get_quiz_id();
    $quiz_id = is_numeric($quiz_id) ? (int) $quiz_id : 0;
    
    $show_progress = $tutorial->get_show_progress();
    $introduction = $tutorial->get_introduction();
    $has_introduction = $tutorial->has_introduction();
    $tutorial_type = $tutorial->get_tutorial_type();
    $completion_tracking = $tutorial->get_completion_tracking();
    $allow_downloads = $tutorial->get_allow_downloads();
    $certificate_available = $tutorial->get_certificate_available();
    
    $resources = $tutorial->get_resources();
    $resources = is_array($resources) ? $resources : array();
    
    $category = $tutorial->get_category();
    $difficulty = $tutorial->get_difficulty();
    $featured = $tutorial->get_featured();
} catch (Exception $e) {
    error_log('Tutorial data retrieval error for post ID ' . $post->ID . ': ' . $e->getMessage());
    wp_die(
        '<h1>Tutorial Error</h1><p>There was an error loading this tutorial\'s data. Error: ' . esc_html($e->getMessage()) . '</p>',
        'Tutorial Error',
        array('response' => 500, 'back_link' => true)
    );
}

// Get user progress
$progress = $tutorial->get_user_progress($user_id);

// Check for step parameter in URL, otherwise use saved progress
// Step -1 represents the introduction
// Step count($steps) represents the quiz (if exists)
$total_steps_with_quiz = count($steps) + ($quiz_id > 0 ? 1 : 0);

if (isset($_GET['step'])) {
    $step_param = $_GET['step'];
    if ($step_param === 'intro' || $step_param === '-1') {
        $current_step = -1; // Introduction
    } elseif ($step_param === 'quiz') {
        $current_step = count($steps); // Quiz step (after all regular steps)
    } elseif (is_numeric($step_param)) {
        // Ensure step is within valid range (including quiz if present)
        $max_step = ($quiz_id > 0) ? count($steps) : (count($steps) - 1);
        $current_step = max(0, min((int) $step_param, $max_step));
    } else {
        $current_step = $has_introduction ? -1 : 0; // Default to intro if available
    }
} else {
    // If no step parameter, start with introduction if available, otherwise step 0
    $current_step = $has_introduction ? -1 : (isset($progress['current_step']) ? (int) $progress['current_step'] : 0);
}

$completed_steps = isset($progress['completed_steps']) && is_array($progress['completed_steps']) ? $progress['completed_steps'] : array();
$progress_percent = $tutorial->get_progress_percentage($user_id);

// Enqueue necessary styles and scripts
// Note: In template files, we can enqueue directly since we're before wp_head()
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');
wp_enqueue_style('modals-styles', get_template_directory_uri() . '/assets/css/modals.css', array(), '1.0.0');
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_script('lms-script', get_template_directory_uri() . '/assets/js/lms.js', array(), '1.0.0', true);
wp_enqueue_script('modals-script', get_template_directory_uri() . '/assets/js/modals.js', array(), '1.0.0', true);
wp_enqueue_script('tutorial-script', get_template_directory_uri() . '/assets/js/tutorial.js', array('jquery'), '1.0.0', true);

// Localize script with tutorial data including all steps
wp_localize_script('tutorial-script', 'tutorialData', array(
    'tutorialId' => $post->ID,
    'userId' => $user_id,
    'isLoggedIn' => $is_logged_in,
    'currentStep' => $current_step,
    'completedSteps' => $completed_steps,
    'totalSteps' => count($steps),
    'totalStepsWithQuiz' => $total_steps_with_quiz,
    'hasIntroduction' => $has_introduction,
    'introduction' => $introduction,
    'quizId' => $quiz_id,
    'quizUrl' => $quiz_id > 0 ? get_permalink($quiz_id) : '',
    'quizStepIndex' => $quiz_id > 0 ? count($steps) : -1,
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('tutorial_progress_nonce'),
    'steps' => array_map(function($step) {
        return array(
            'title' => $step['title'],
            'content' => wpautop($step['content']), // Apply WordPress paragraph formatting
            'video_url' => isset($step['video_url']) ? $step['video_url'] : '',
            'type' => isset($step['type']) ? $step['type'] : 'video',
            'duration' => isset($step['duration']) ? $step['duration'] : ''
        );
    }, $steps)
    ));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($tutorial->get_name()); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        color: #2c3e50;
        line-height: 1.6;
        min-height: 100vh;
        padding-top: 70px; /* Account for fixed header */
    }

    /* Styled scrollbar - Webkit browsers (Chrome, Safari, newer Opera and Edge) */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #026447;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #004E38; /* Darker green for hover state */
    }
    
    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #026447 #f1f1f1;
    }

    /* Hide WordPress default header */
    .site-header, #masthead, header.wp-block-template-part {
        display: none !important;
    }

    /* WordPress admin bar adjustment */
    .admin-bar body {
        padding-top: 102px; /* 70px header + 32px admin bar */
    }

    @media screen and (max-width: 782px) {
        .admin-bar body {
            padding-top: 116px; /* 70px header + 46px admin bar on mobile */
        }
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .main-content {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 30px;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .video-section {
        padding: 20px;
    }

    .video-player {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .video-player video,
    .video-player iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* WordPress video shortcode wrapper styling */
    .video-player .wp-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
    }

    .video-player .mejs-container,
    .video-player .mejs-mediaelement {
        position: absolute !important;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
    }

    .video-player .mejs-container video {
        object-fit: contain;
    }

    .video-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #026447 0%, #04a971 100%);
        color: white;
        font-size: 1.2rem;
    }

    /* Glossary Toggle Button and Drawer */
    .glossary-toggle {
        background: #f8fafc;
        border: 1px solid #e8ecf1;
        color: #026447;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        width: 100%;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0;
    }

    .glossary-toggle:hover {
        background: #e8ecf1;
        border-color: #026447;
    }

    .glossary-toggle-icon {
        transition: transform 0.3s ease;
        font-size: 1.2rem;
    }

    .glossary-toggle.active .glossary-toggle-icon {
        transform: rotate(180deg);
    }

    .glossary-drawer {
        background: #f8fafc;
        border: 1px solid #e8ecf1;
        border-top: none;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.4s ease;
    }

    .glossary-drawer.active {
        max-height: 600px;
    }

    .glossary-content {
        padding: 24px;
        overflow-y: auto;
        max-height: 480px;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    /* Hide scrollbar for webkit browsers */
    .glossary-content::-webkit-scrollbar {
        display: none;
    }

    .glossary-item {
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid #e8ecf1;
    }

    .glossary-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .glossary-item.hidden {
        display: none;
    }

    .glossary-term {
        font-size: 1.05rem;
        font-weight: 600;
        color: #026447;
        margin-bottom: 8px;
    }

    .glossary-definition {
        color: #5a6c7d;
        line-height: 1.7;
        font-size: 0.9rem;
    }

    .video-info h1 {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        font-size: 1.4rem;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .video-meta {
        display: flex;
        gap: 20px;
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .video-description {
        color: #5a6c7d;
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .sidebar {
        background: #f8fafc;
        padding: 40px 30px;
        border-left: 1px solid #e8ecf1;
        overflow-y: auto;
        max-height: 800px;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    /* Hide scrollbar for webkit browsers */
    .sidebar::-webkit-scrollbar {
        display: none;
    }

    .sidebar h2 {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        font-size: 1.1rem;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 25px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .tutorial-list {
        list-style: none;
    }

    .tutorial-item {
        background: white;
        border-radius: 8px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        overflow: hidden;
    }

    .tutorial-item:hover {
        transform: translateX(4px);
        border-color: #026447;
    }

    .tutorial-item.active {
        border-color: #026447;
        background: #f0f7ff;
    }

    .tutorial-item.completed {
        opacity: 0.8;
    }

    .tutorial-item.completed:hover {
        opacity: 1;
    }

    .tutorial-item.completed .tutorial-title {
        color: #04a971;
    }

    .tutorial-content {
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tutorial-number {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        background: #026447;
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 28px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .tutorial-item.active .tutorial-number {
        background: #04a971;
    }

    .tutorial-info {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .tutorial-title {
        font-weight: 500;
        color: #2c3e50;
        font-size: 0.95rem;
        flex: 1;
    }

    .tutorial-duration {
        font-size: 0.8rem;
        color: #7f8c8d;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .step-type-icon {
        font-size: 0.85rem;
        margin-left: 8px;
        opacity: 0.7;
    }

    .resource-item:hover {
        border-color: #026447 !important;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(2, 100, 71, 0.1);
    }


    .step-navigation {
        display: flex;
        justify-content: flex-end;
        margin-top: 30px;
        gap: 15px;
    }
    
    .step-navigation .btn-secondary {
        margin-right: auto;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: #026447;
        color: white;
    }

    .btn-primary:hover {
        background: #04a971;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 90, 142, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #026447;
        border: 2px solid #026447;
    }

    .btn-secondary:hover {
        background: #f8fafc;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .completion-message {
        background: #f0f7ff;
        padding: 3rem;
        border-radius: 8px;
        text-align: center;
        margin-top: 2rem;
        border: 2px solid #026447;
    }

    .completion-title {
        font-size: 2rem;
        font-weight: 500;
        color: #026447;
        margin-bottom: 1rem;
    }

    .completion-icon {
        font-size: 4rem;
        color: #026447;
        margin-bottom: 1rem;
    }


    /* Smooth sidebar item transitions */
    .tutorial-item {
        transition: all 0.3s ease;
    }

    .tutorial-item.active {
        transition: all 0.3s ease;
    }

    @media (max-width: 1024px) {
        .main-content {
            grid-template-columns: 1fr;
        }

        .sidebar {
            border-left: none;
            border-top: 1px solid #e8ecf1;
            max-height: 500px;
            order: -1;
        }
    }

    @media (max-width: 640px) {
        .container {
            padding: 20px 10px;
        }

        .video-section {
            padding: 20px;
        }

        .sidebar {
            padding: 20px 15px;
        }

        .logo {
            font-size: 2rem;
    }

     .glossary-content {
            max-height: 400px;
        }
    }
</style>

<!-- LMS Header -->
<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
            </a>
        </div>
        
        <div class="header-actions">
            <div class="auth-only" style="display: <?php echo $is_logged_in ? 'flex' : 'none'; ?>;">
                <div class="header-icons">
                    <button class="header-button" id="notificationsButton" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notification-badge"></span>
                    </button>
                    <button class="header-button menu-button" aria-label="Menu" aria-haspopup="true">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18"/>
                        </svg>
                    </button>
                    <div class="profile-dropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-user-info">
                                <?php if ($is_logged_in): 
                                    $current_user = wp_get_current_user();
                                ?>
                                    <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
                                    <span class="user-email"><?php echo esc_html($current_user->user_email); ?></span>
                                <?php else: ?>
                                    <span class="user-name">Your Name</span>
                                    <span class="user-email">your.email@example.com</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php echo esc_url(home_url('/lp-profile/')); ?>" class="dropdown-item">My Account</a>
                        <button class="dropdown-item logout-button">Sign Out</button>
                    </div>
                </div>
            </div>
            <div class="guest-only" style="display: <?php echo $is_logged_in ? 'none' : 'flex'; ?>;">
                <button class="header-button login-button">Log In</button>
                <button class="header-button signup-button">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<?php 
// Output onboarding popup if available
if (!empty($onboarding_content)) {
    echo $onboarding_content;
}
?>

<div class="container">

    <div class="main-content tutorial-main">
        <div class="video-section">
            <!-- Video Player -->
            <?php 
            // Get current step data - handle introduction (step -1), quiz (step = count($steps)), or regular steps
            if ($current_step === -1 && $has_introduction) {
                $current_step_data = $introduction;
            } elseif ($current_step === count($steps) && $quiz_id > 0) {
                // Quiz step - create virtual step data
                $quiz_post = get_post($quiz_id);
                $current_step_data = array(
                    'title' => $quiz_post ? $quiz_post->post_title : 'Tutorial Quiz',
                    'content' => 'Complete the quiz below to finish the tutorial.',
                    'type' => 'quiz_embed',
                    'video_url' => '',
                    'duration' => ''
                );
            } else {
                $current_step_data = isset($steps[$current_step]) ? $steps[$current_step] : (isset($steps[0]) ? $steps[0] : null);
            }
            ?>
            
            <div class="video-player">
                <?php 
                $step_type = isset($current_step_data['type']) ? $current_step_data['type'] : 'video';
                $has_video = !empty($current_step_data['video_url']);
                
                // Display video for video types or introduction with video
                if ($current_step_data && (($step_type === 'video' || $step_type === 'introduction') && $has_video)): ?>
                    <?php 
                    $video_url = $current_step_data['video_url'];
                    
                    // Check if it's a Panopto URL (either share or embed)
                    if (strpos($video_url, 'panopto.com') !== false) {
                        // Extract video ID from URL
                        if (preg_match('/[?&]id=([a-f0-9-]+)/', $video_url, $matches)) {
                            $video_id = $matches[1];
                            $embed_url = preg_replace('/\/Viewer\.aspx/', '/Embed.aspx', $video_url);
                            // Ensure it has the right parameters
                            if (strpos($embed_url, 'Embed.aspx') !== false) {
                                $video_url = $embed_url;
                            } else {
                                // Build embed URL from ID
                                preg_match('/https?:\/\/([^\/]+)/', $video_url, $domain_matches);
                                $domain = isset($domain_matches[1]) ? $domain_matches[1] : 'wmedu.hosted.panopto.com';
                                $video_url = "https://{$domain}/Panopto/Pages/Embed.aspx?id={$video_id}&autoplay=false&offerviewer=false&showtitle=false&showbrand=false&captions=true&interactivity=none";
                            }
                            // Display as iframe
                            echo '<iframe src="' . esc_url($video_url) . '" 
                                    style="border: 1px solid #464646; position: absolute; top: 0; left: 0; width: 100%; height: 100%; box-sizing: border-box;" 
                                    allowfullscreen 
                                    allow="autoplay" 
                                    aria-label="Panopto Embedded Video Player"></iframe>';
                        } else {
                            echo '<div class="video-placeholder"><span>Invalid Panopto URL</span></div>';
                        }
                    }
                    // Check if it's a YouTube URL
                    elseif (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches)) {
                        $youtube_id = $matches[1];
                        echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($youtube_id) . '" 
                                frameborder="0" 
                                allowfullscreen 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>';
                    }
                    // Check if it's a Vimeo URL
                    elseif (preg_match('/vimeo\.com\/(\d+)/', $video_url, $matches)) {
                        $vimeo_id = $matches[1];
                        echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '" 
                                frameborder="0" 
                                allowfullscreen 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>';
                    }
                    // Check if it's already an iframe embed code
                    elseif (strpos($video_url, '<iframe') !== false) {
                        // Extract and sanitize iframe
                        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $video_url, $matches)) {
                            echo '<iframe src="' . esc_url($matches[1]) . '" 
                                    allowfullscreen 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 1px solid #464646;"></iframe>';
                        }
                    }
                    // Otherwise treat as direct video file
                    else {
                        echo do_shortcode('[video src="' . esc_url($video_url) . '"]');
                    }
                    ?>
                <?php elseif ($step_type === 'instruction'): ?>
                    <div class="video-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div style="text-align: center;">
                            <div style="font-size: 4rem; margin-bottom: 15px;">📖</div>
                            <div style="font-size: 1.3rem; font-weight: 600;">Instruction Step</div>
                            <div style="font-size: 0.9rem; margin-top: 8px; opacity: 0.9;">Read the content below to continue</div>
                        </div>
                    </div>
                <?php elseif ($step_type === 'exercise'): ?>
                    <div class="video-placeholder" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div style="text-align: center;">
                            <div style="font-size: 4rem; margin-bottom: 15px;">✏️</div>
                            <div style="font-size: 1.3rem; font-weight: 600;">Hands-on Exercise</div>
                            <div style="font-size: 0.9rem; margin-top: 8px; opacity: 0.9;">Complete the exercise below</div>
                        </div>
                    </div>
                <?php elseif ($step_type === 'quiz'): ?>
                    <div class="video-placeholder" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div style="text-align: center;">
                            <div style="font-size: 4rem; margin-bottom: 15px;">❓</div>
                            <div style="font-size: 1.3rem; font-weight: 600;">Quiz Step</div>
                            <div style="font-size: 0.9rem; margin-top: 8px; opacity: 0.9;">Answer the questions below</div>
                        </div>
                    </div>
                <?php elseif ($step_type === 'quiz_embed' && $quiz_id > 0): ?>
                    <!-- Embedded Quiz as Final Step -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: white; overflow: auto;">
                        <iframe src="<?php echo esc_url(get_permalink($quiz_id) . '?embedded=1&tutorial_id=' . $post->ID); ?>" 
                                style="width: 100%; height: 100%; border: none; display: block;"
                                frameborder="0"
                                allowfullscreen
                                id="quizEmbed"></iframe>
                    </div>
                <?php else: ?>
                    <div class="video-placeholder">
                        <span>Video Player</span>
                </div>
            <?php endif; ?>
        </div>

            <!-- Video Info -->
            <div class="video-info">
                <?php if ($current_step_data): ?>
                    <h1 id="videoTitle"><?php echo esc_html($current_step_data['title']); ?></h1>
                    
                    <!-- Tutorial Metadata -->
                    <div class="tutorial-metadata" style="display: flex; gap: 20px; margin: 15px 0; flex-wrap: wrap;">
                        <?php if ($tutorial_type): ?>
                            <span class="metadata-badge" style="background: #e8f5e9; color: #2e7d32; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                                <?php 
                                $type_labels = array(
                                    'video' => '🎥 Video Tutorial',
                                    'interactive' => '⚡ Interactive',
                                    'step_by_step' => '📋 Step-by-Step',
                                    'hands_on' => '🛠️ Hands-on'
                                );
                                echo isset($type_labels[$tutorial_type]) ? $type_labels[$tutorial_type] : ucfirst(str_replace('_', ' ', $tutorial_type));
                                ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($difficulty): ?>
                            <span class="metadata-badge" style="background: <?php echo $difficulty === 'beginner' ? '#e3f2fd' : ($difficulty === 'intermediate' ? '#fff3e0' : '#fce4ec'); ?>; color: <?php echo $difficulty === 'beginner' ? '#1565c0' : ($difficulty === 'intermediate' ? '#ef6c00' : '#c2185b'); ?>; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                                <?php echo ucfirst($difficulty); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($category): ?>
                            <span class="metadata-badge" style="background: #f3e5f5; color: #6a1b9a; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                                <?php echo esc_html(ucwords(str_replace('_', ' ', $category))); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($featured): ?>
                            <span class="metadata-badge" style="background: #fff8e1; color: #f57f17; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                                ⭐ Featured
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($certificate_available): ?>
                            <span class="metadata-badge" style="background: #e0f2f1; color: #00695c; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                                🎓 Certificate Available
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="video-description" id="videoDescription">
                        <?php echo wpautop($current_step_data['content']); ?>
            </p>
        <?php endif; ?>
    </div>

            <!-- Instructors Section -->
            <?php if (!empty($instructors) && is_array($instructors)): ?>
            <div class="instructors-section" style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e8ecf1;">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #2c3e50; margin-bottom: 20px;">Tutorial Instructors</h3>
                <div class="instructors-list" style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($instructors as $instructor): ?>
                        <div class="instructor-card" style="display: flex; gap: 15px; align-items: flex-start;">
                            <div class="instructor-avatar" style="flex-shrink: 0;">
                                <?php if (!empty($instructor['image_id'])): ?>
                                    <?php 
                                    $image_url = wp_get_attachment_image_url($instructor['image_id'], 'thumbnail');
                                    if ($image_url): ?>
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($instructor['name']); ?>" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #026447;">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #026447; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600;">
                                        <?php echo esc_html(substr($instructor['name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="instructor-info" style="flex: 1;">
                                <h4 style="font-size: 1rem; font-weight: 600; color: #2c3e50; margin-bottom: 4px;"><?php echo esc_html($instructor['name']); ?></h4>
                                <?php if (!empty($instructor['title'])): ?>
                                    <p style="font-size: 0.9rem; color: #026447; margin-bottom: 8px; font-weight: 500;"><?php echo esc_html($instructor['title']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($instructor['bio'])): ?>
                                    <p style="font-size: 0.85rem; color: #5a6c7d; line-height: 1.6;"><?php echo esc_html($instructor['bio']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Resources Section -->
            <?php if ($allow_downloads && !empty($resources) && is_array($resources)): ?>
            <div class="resources-section" style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e8ecf1;">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #2c3e50; margin-bottom: 20px;">📥 Downloadable Resources</h3>
                <div class="resources-list" style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($resources as $resource_id): ?>
                        <?php 
                        $resource_post = get_post($resource_id);
                        if ($resource_post):
                            $resource_url = get_post_meta($resource_id, '_resource_url', true);
                            $resource_type = get_post_meta($resource_id, '_resource_type', true);
                        ?>
                            <a href="<?php echo esc_url($resource_url); ?>" class="resource-item" target="_blank" rel="noopener" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; background: white; border-radius: 6px; border: 1px solid #e8ecf1; text-decoration: none; color: inherit; transition: all 0.3s ease;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 1.5rem;">
                                        <?php 
                                        $type_icons = array(
                                            'pdf' => '📄',
                                            'document' => '📝',
                                            'spreadsheet' => '📊',
                                            'presentation' => '📽️',
                                            'video' => '🎥',
                                            'link' => '🔗'
                                        );
                                        echo isset($type_icons[$resource_type]) ? $type_icons[$resource_type] : '📁';
                                        ?>
                                    </span>
                                    <div>
                                        <h4 style="font-size: 0.95rem; font-weight: 600; color: #2c3e50; margin-bottom: 2px;"><?php echo esc_html($resource_post->post_title); ?></h4>
                                        <?php if ($resource_type): ?>
                                            <p style="font-size: 0.8rem; color: #7f8c8d; margin: 0;"><?php echo esc_html(ucfirst($resource_type)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span style="color: #026447; font-weight: 500; font-size: 0.9rem;">Download →</span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Glossary Drawer -->
            <?php
            $glossary = $tutorial->get_glossary();
            if (!empty($glossary)):
            ?>
            <div style="margin-top: 30px; margin-bottom: 20px;">
                <button class="glossary-toggle" id="glossaryToggle">
                    <span>Glossary</span>
                    <span class="glossary-toggle-icon">▼</span>
                </button>
                <div class="glossary-drawer" id="glossaryDrawer">
                    <div class="glossary-content" id="glossaryContent">
                        <?php foreach ($glossary as $item): ?>
                            <?php if (isset($item['term']) && isset($item['definition'])): ?>
                                <div class="glossary-item" data-term="<?php echo esc_attr(strtolower($item['term'])); ?>">
                                    <div class="glossary-term"><?php echo esc_html($item['term']); ?></div>
                                    <div class="glossary-definition"><?php echo wp_kses_post($item['definition']); ?></div>
                                </div>
                            <?php endif; ?>
                <?php endforeach; ?>
        </div>
                    </div>
                        </div>
                    <?php endif; ?>

            <!-- Step Navigation -->
                    <div class="step-navigation">
                <?php if ($current_step > 0 || ($current_step === 0 && $has_introduction)): ?>
                    <button class="btn btn-secondary prev-step">
                            ← Previous <?php echo ($current_step === 0 && $has_introduction) ? 'Introduction' : 'Step'; ?>
                        </button>
                <?php endif; ?>
                        
                <?php if ($current_step === -1): ?>
                    <button class="btn btn-primary next-step">
                        Start Tutorial →
                    </button>
                <?php elseif ($current_step === count($steps) && $quiz_id > 0): ?>
                    <!-- On quiz step - show complete button -->
                    <button class="btn btn-primary complete-tutorial">
                        Complete Tutorial ✓
                    </button>
                <?php elseif ($current_step < count($steps) - 1): ?>
                    <button class="btn btn-primary next-step">
                        Next Step →
                    </button>
                <?php elseif ($current_step === count($steps) - 1 && $quiz_id > 0): ?>
                    <!-- Last regular step with quiz - go to quiz step -->
                    <button class="btn btn-primary next-step">
                        Take Quiz →
                    </button>
                <?php elseif ($current_step === count($steps) - 1 && !$quiz_id): ?>
                    <!-- Last step with no quiz - complete -->
                    <button class="btn btn-primary complete-tutorial">
                        Complete Tutorial ✓
                    </button>
                <?php endif; ?>
                    </div>
                </div>

        <!-- Sidebar with Tutorial Steps -->
        <aside class="sidebar">
            <h2><?php echo esc_html($tutorial->get_name()); ?></h2>
            
            <!-- Progress Indicator -->
            <?php if ($show_progress && $is_logged_in): ?>
            <div class="tutorial-progress-section" style="margin-bottom: 25px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #e8ecf1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 0.85rem; font-weight: 600; color: #2c3e50;">Your Progress</span>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #026447;"><?php echo round($progress_percent); ?>%</span>
                </div>
                <div style="width: 100%; height: 8px; background: #e8ecf1; border-radius: 4px; overflow: hidden;">
                    <div style="width: <?php echo $progress_percent; ?>%; height: 100%; background: linear-gradient(90deg, #026447 0%, #04a971 100%); transition: width 0.3s ease;"></div>
                </div>
                <div style="margin-top: 8px; font-size: 0.75rem; color: #7f8c8d;">
                    <?php 
                    echo count($completed_steps) . ' of ' . $total_steps_with_quiz . ' steps completed';
                    if ($completion_tracking) {
                        $tracking_labels = array(
                            'time_based' => '⏱️ Time-based tracking',
                            'step_based' => '✓ Step-based tracking',
                            'quiz_based' => '❓ Quiz-based completion',
                            'manual' => '👤 Manual tracking'
                        );
                        if (isset($tracking_labels[$completion_tracking])) {
                            echo ' • ' . $tracking_labels[$completion_tracking];
                        }
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <ul class="tutorial-list" id="tutorialList">
                <?php if ($has_introduction): ?>
                    <li class="tutorial-item <?php echo $current_step === -1 ? 'active' : ''; ?>" data-step="-1">
                        <div class="tutorial-content">
                            <span class="tutorial-number" style="background: #04a971;">▶</span>
                            <div class="tutorial-info">
                                <span class="tutorial-title"><?php echo esc_html($introduction['title'] ?: 'Introduction'); ?></span>
                                <?php if (!empty($introduction['duration'])): ?>
                                    <span class="tutorial-duration"><?php echo esc_html($introduction['duration']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                
                <?php foreach ($steps as $index => $step): ?>
                    <?php 
                    $is_completed = in_array($index, $completed_steps);
                    $is_current = $index === $current_step;
                    ?>
                    <li class="tutorial-item <?php echo $is_current ? 'active' : ''; ?> <?php echo $is_completed ? 'completed' : ''; ?>" data-step="<?php echo $index; ?>">
                        <div class="tutorial-content">
                            <span class="tutorial-number" style="<?php echo $is_completed ? 'background: #04a971;' : ''; ?>">
                                <?php echo $is_completed ? '✓' : ($index + 1); ?>
                            </span>
                            <div class="tutorial-info">
                                <span class="tutorial-title">
                                    <?php echo esc_html($step['title']); ?>
                                    <?php 
                                    // Show step type icon
                                    $step_type = isset($step['type']) ? $step['type'] : 'instruction';
                                    $type_icon = '';
                                    switch ($step_type) {
                                        case 'video':
                                            $type_icon = '🎥';
                                            break;
                                        case 'exercise':
                                            $type_icon = '✏️';
                                            break;
                                        case 'quiz':
                                            $type_icon = '❓';
                                            break;
                                        case 'instruction':
                                        default:
                                            $type_icon = '📖';
                                            break;
                                    }
                                    ?>
                                    <span class="step-type-icon"><?php echo $type_icon; ?></span>
                                </span>
                                <?php if (!empty($step['duration'])): ?>
                                    <span class="tutorial-duration"><?php echo esc_html($step['duration']); ?></span>
            <?php endif; ?>
        </div>
                        </div>
                    </li>
            <?php endforeach; ?>
            
            <?php if ($quiz_id > 0): ?>
                <?php 
                $quiz_step_index = count($steps);
                $is_quiz_completed = in_array($quiz_step_index, $completed_steps);
                $is_quiz_current = $quiz_step_index === $current_step;
                $quiz_post = get_post($quiz_id);
                ?>
                <li class="tutorial-item <?php echo $is_quiz_current ? 'active' : ''; ?> <?php echo $is_quiz_completed ? 'completed' : ''; ?>" data-step="<?php echo $quiz_step_index; ?>">
                    <div class="tutorial-content">
                        <span class="tutorial-number" style="<?php echo $is_quiz_completed ? 'background: #04a971;' : ''; ?>">
                            <?php echo $is_quiz_completed ? '✓' : ($quiz_step_index + 1); ?>
                        </span>
                        <div class="tutorial-info">
                            <span class="tutorial-title">
                                <?php echo $quiz_post ? esc_html($quiz_post->post_title) : 'Tutorial Quiz'; ?>
                                <span class="step-type-icon">❓</span>
                            </span>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
        </aside>
    </div>
</div>

<!-- Notification Modal -->
<div class="notification-modal" id="notificationModal">
    <div class="notification-container">
        <button class="close-notifications" aria-label="Close">&times;</button>
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="mark-all-read">Mark all as read</button>
        </div>
        <div class="notification-list">
            <?php
            // If user is logged in, get their notifications from database
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $notifications = get_user_meta($user_id, 'notifications', true);
                
                if (!empty($notifications) && is_array($notifications)) {
                    foreach ($notifications as $notification) {
                        $is_unread = isset($notification['read']) && !$notification['read'];
                        $time_ago = isset($notification['timestamp']) ? human_time_diff(strtotime($notification['timestamp']), current_time('timestamp')) . ' ago' : '';
                        ?>
                        <div class="notification-item <?php echo $is_unread ? 'unread' : ''; ?>">
                            <div class="notification-content">
                                <p><?php echo esc_html($notification['message']); ?></p>
                                <span class="notification-time"><?php echo esc_html($time_ago); ?></span>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="notification-item">
                        <div class="notification-content">
                            <p>Welcome to AidData Training Hub</p>
                            <span class="notification-time">Just now</span>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="notification-item unread">
                    <div class="notification-content">
                        <p>Your grade for "Data Analysis Fundamentals" has been updated</p>
                        <span class="notification-time">2 hours ago</span>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="notification-content">
                        <p>New course available: "Advanced Development Finance"</p>
                        <span class="notification-time">1 day ago</span>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="notification-content">
                        <p>Your certificate for "Global Development Finance" is ready</p>
                        <span class="notification-time">3 days ago</span>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="notification-footer">
            <a href="#" class="view-all-notifications">View all notifications</a>
        </div>
    </div>
</div>

<!-- Sign Up Modal -->
<div class="signup-modal" id="signupModal" role="dialog" aria-hidden="true" aria-labelledby="signupModalTitle">
    <div class="signup-container">
        <button class="close-signup" aria-label="Close">×</button>
        <div class="signup-header">
            <h3 id="signupModalTitle">Create Your Account</h3>
            <p>Join AidData Training Hub to access all courses and track your progress</p>
        </div>
        <form class="signup-form" id="signupForm">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required aria-required="true" 
                       aria-describedby="passwordRequirements">
                <p id="passwordRequirements" class="password-requirements">Password must be at least 8 characters long and include a number and special character</p>
            </div>
            <div class="form-group">
                <label for="organization">Organization (Optional)</label>
                <input type="text" id="organization" name="organization" aria-required="false">
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="newsletter" name="newsletter">
                <label for="newsletter">Subscribe to the AidData newsletter to receive updates about new courses, research insights, and development finance news</label>
            </div>
            <div class="form-message" style="display: none; margin: 10px 0; padding: 10px; border-radius: 4px; color: #fff; background-color: #d32f2f;"></div>
            <div class="form-footer">
                <button type="submit" class="create-account-button">Create Account</button>
                <p>Already have an account? <a href="#" class="login-link">Log in</a></p>
            </div>
        </form>
    </div>
</div>

<!-- Login Modal -->
<div class="login-modal" id="loginModal" role="dialog" aria-hidden="true" aria-labelledby="loginModalTitle">
    <div class="login-container">
        <button class="close-login" aria-label="Close">&times;</button>
        <div class="login-header">
            <h3 id="loginModalTitle">Welcome Back</h3>
            <p>Please log in to continue</p>
        </div>
        <form class="login-form" id="loginForm">
            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input type="email" id="loginEmail" name="email" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="password" required aria-required="true">
            </div>
            <div class="forgot-password">
                <a href="#" class="forgot-password-link">Forgot Password?</a>
            </div>
            <div class="form-message" style="display: none; margin: 10px 0; padding: 10px; border-radius: 4px; color: #fff; background-color: #d32f2f;"></div>
            <button type="submit" class="login-button-submit">Log In</button>
            <div class="form-footer">
                <p>Need an account? <a href="#" class="signup-link">Sign up</a></p>
            </div>
        </form>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="forgot-password-modal" id="forgotPasswordModal" role="dialog" aria-hidden="true" aria-labelledby="forgotPasswordModalTitle">
    <div class="forgot-password-container">
        <button class="close-forgot-password" aria-label="Close">&times;</button>
        <div class="forgot-password-header">
            <h3 id="forgotPasswordModalTitle">Reset Your Password</h3>
            <p>Enter your email address and we'll send you instructions to reset your password</p>
        </div>
        <form class="forgot-password-form" id="forgotPasswordForm">
            <div class="form-group">
                <label for="resetEmail">Email Address</label>
                <input type="email" id="resetEmail" name="email" required aria-required="true">
            </div>
            <div class="form-message" style="display: none; margin: 10px 0; padding: 10px; border-radius: 4px;"></div>
            <button type="submit" class="reset-password-button">Send Reset Link</button>
            <div class="form-footer">
                <p>Remembered your password? <a href="#" class="login-link">Back to login</a></p>
            </div>
        </form>
    </div>
</div>

<script>
// Glossary drawer toggle
document.addEventListener('DOMContentLoaded', function() {
    const glossaryToggle = document.getElementById('glossaryToggle');
    const glossaryDrawer = document.getElementById('glossaryDrawer');

    if (glossaryToggle && glossaryDrawer) {
        glossaryToggle.addEventListener('click', function() {
            glossaryToggle.classList.toggle('active');
            glossaryDrawer.classList.toggle('active');
        });
    }
});
</script>

<?php wp_footer(); ?>
</body>
</html>


