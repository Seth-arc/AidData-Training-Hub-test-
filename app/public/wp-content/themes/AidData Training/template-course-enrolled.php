<?php
/**
 * Template Name: LifterLMS Course Template - Enrolled (LinkedIn Learning Style)
 * Template Post Type: course, llms_course, page
 *
 * A custom template for LifterLMS course pages showing the enrolled state with LinkedIn Learning design
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

get_header();

// Get current course ID
$course_id = get_the_ID();

// Get current user ID
$user_id = get_current_user_id();

// Get student object
$student = llms_get_student($user_id);

// Get course progress
$course_progress = 0;
$completed_lessons = array();

if ($student) {
    // Get the student's progress in this course
    $course_progress = $student->get_progress($course_id, 'course', false);
    
    // Get course lessons
    $course = new LLMS_Course($course_id);
    $lessons = $course->get_lessons('ids');
    
    // Get completed lessons
    foreach ($lessons as $lesson_id) {
        if ($student->is_complete($lesson_id, 'lesson')) {
            $completed_lessons[] = $lesson_id;
        }
    }
}

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
?>

<style>
    /* Import LinkedIn-style fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    /* Elegant Design System */
    :root {
        /* Sophisticated Color Palette */
        --primary-blue:rgb(3, 47, 1);
        --primary-blue-hover:rgb(1, 36, 3);
        --background-color: #fafafa;
        --background-gradient: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
        --white: #ffffff;
        --white-elevated: #ffffff;
        --text-primary: #1a1a1a;
        --text-secondary: #4a5568;
        --text-tertiary: #718096;
        --text-muted: #a0aec0;
        --border-color: #e2e8f0;
        --border-light: #f7fafc;
        --success-color: #026447;
        --success-light: #f0fff4;
        --progress-color: #026447;
        --accent-color: #667eea;
        --hover-bg: #f8fafc;
        
        /* Typography */
        --font-family: 'Inter', sans-serif;
        --font-size-xs: 12px;
        --font-size-sm: 14px;
        --font-size-base: 16px;
        --font-size-lg: 18px;
        --font-size-xl: 20px;
        --font-size-2xl: 24px;
        --font-size-3xl: 28px;
        
        /* Spacing */
        --spacing-xs: 4px;
        --spacing-sm: 8px;
        --spacing-md: 16px;
        --spacing-lg: 24px;
        --spacing-xl: 32px;
        --spacing-2xl: 48px;
        
        /* Elegant Shadows */
        --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1), 0 10px 10px rgba(0, 0, 0, 0.04);
        --shadow-card: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        --shadow-elevated: 0 4px 12px rgba(0, 0, 0, 0.15);
        
        /* Border radius */
        --radius-sm: 4px;
        --radius-md: 8px;
        --radius-lg: 12px;
    }
    
    /* Custom Scrollbar Styling */
    /* Webkit browsers (Chrome, Safari, newer versions of Opera and Edge) */
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
    
    /* Reset and base styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body, html {
        font-family: var(--font-family);
        font-size: var(--font-size-base);
        line-height: 1.6;
        color: var(--text-primary);
        background: var(--background-gradient);
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }
    
    /* Force Inter font on all elements */
    *, *::before, *::after {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif !important;
    }
    
    /* Ensure all text elements use Inter */
    h1, h2, h3, h4, h5, h6,
    p, span, div, a, button, input, textarea, select,
    .wp-block-heading, .wp-block-paragraph,
    .course-title, .course-instructor, .module-title, .lesson-title,
    .tab-button, .video-placeholder, .lesson-duration,
    .progress-label, .progress-percentage, .contents-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif !important;
    }
    
    /* Hide default WordPress header */
    .site-header, #masthead, header.wp-block-template-part {
        display: none !important;
    }
    
    /* WordPress admin bar adjustment */
    .admin-bar .lms-header {
        top: 32px; /* For logged-in admin users */
    }
    
    @media screen and (max-width: 782px) {
        .admin-bar .lms-header {
            top: 46px; /* For admin bar on mobile */
        }
    }
    
    /* LMS Header matching project style with green footer styling */
    .lms-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #026447 0%, #014336 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        height: 70px; /* Set explicit height */
        width: 100%;
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        width: 100%;
        height: 100%; /* Fill header height */
        position: relative;
    }
    
    .logo-section {
        margin-left: var(--spacing-xl); /* Align with sidebar content padding */
    }

    .header-actions {
        display: flex;
        align-items: center;
        margin-right: 2rem; /* Keep original right padding */
    }
    
    .logo-section img {
        height: 35px;
        width: auto;
    }

    .auth-only, .guest-only {
        display: flex;
        align-items: center;
    }

    .header-icons {
        display: flex;
        align-items: center;
        position: relative;
    }

    .header-button {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.9);
        cursor: pointer;
        margin-left: 10px;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .login-button, .signup-button {
        background-color: transparent;
        border: 1px solid rgba(255, 255, 255, 0.8);
        color: rgba(255, 255, 255, 0.9);
    }
    
    .signup-button {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .header-button:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
    }
    
    .login-button:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: white;
        color: white;
    }
    
    .signup-button:hover {
        background-color: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
    }
    
    #notificationsButton {
        position: relative;
    }
    
    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        height: 8px;
        width: 8px;
        background-color: #ef5350;
        border-radius: 50%;
    }
    
    /* Profile dropdown styling */
    .profile-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 250px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 15px 0;
        z-index: 1000;
        display: none;
        margin-top: 10px;
    }

    .profile-dropdown.active {
        display: block;
    }

    .profile-dropdown .dropdown-header {
        padding: 0 15px 10px;
        margin-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
    }

    .dropdown-user-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-name {
        font-weight: 600;
        color: #333;
    }

    .user-email {
        font-size: 13px;
        color: #666;
    }
    
    /* Profile button styling for green header */
    .header-icons .header-button {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .header-icons .header-button:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.15);
    }

    .dropdown-item {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f5f5f5;
    }

    .logout-button {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        cursor: pointer;
        font-size: inherit;
        color: #ef5350;
    }

    /* Main Course Layout */
    .course-layout {
        display: flex;
        min-height: 100vh;
        margin-top: 70px;
    }
    
    /* WordPress admin bar adjustments for layout */
    .admin-bar .course-layout {
        margin-top: 102px; /* 70px header + 32px admin bar */
    }
    
    @media screen and (max-width: 782px) {
        .admin-bar .course-layout {
            margin-top: 116px; /* 70px header + 46px admin bar */
        }
    }
    
    /* Left Sidebar - Course Navigation */
    .course-sidebar {
        width: 320px;
        background: var(--white-elevated);
        border-right: 1px solid var(--border-color);
        height: calc(100vh - 70px);
        overflow-y: auto;
        position: fixed;
        left: 0;
        top: 70px;
        box-shadow: var(--shadow-lg);
        backdrop-filter: blur(10px);
        z-index: 100;
    }
    
    .admin-bar .course-sidebar {
        top: 102px; /* 70px header + 32px admin bar */
        height: calc(100vh - 102px);
    }
    
    @media screen and (max-width: 782px) {
        .admin-bar .course-sidebar {
            top: 116px; /* 70px header + 46px admin bar */
            height: calc(100vh - 116px);
        }
    }
    
    .course-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--border-light);
        background: linear-gradient(135deg, var(--white) 0%, var(--border-light) 100%);
        position: relative;
    }
    
    .course-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, var(--border-color) 50%, transparent 100%);
    }
    
    .course-title {
        font-size: var(--font-size-xl);
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: var(--spacing-sm);
        line-height: 1.2;
        letter-spacing: -0.025em;
        background: linear-gradient(135deg, var(--text-primary) 0%, var(--success-color) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .course-instructor {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin-bottom: var(--spacing-md);
    }
    
    /* Enhanced Progress Section */
    .progress-container {
        margin-bottom: var(--spacing-lg);
        padding: var(--spacing-lg);
        background: linear-gradient(135deg, var(--success-light) 0%, rgba(2, 100, 71, 0.05) 100%);
        border-radius: var(--radius-lg);
        border: 2px solid rgba(2, 100, 71, 0.1);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    
    .progress-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--success-color) 0%, #38a169 50%, var(--success-color) 100%);
    }
    
    .progress-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-md);
        font-size: var(--font-size-base);
        color: var(--text-primary);
        font-weight: 600;
    }
    
    .progress-percentage {
        font-weight: 700;
        color: var(--success-color);
        font-size: var(--font-size-lg);
        background: linear-gradient(135deg, var(--success-color) 0%, #38a169 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .progress-bar {
        width: 100%;
        height: 8px;
        background-color: var(--border-light);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    
    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, transparent 50%, rgba(255,255,255,0.3) 100%);
        border-radius: 12px;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--success-color) 0%, #38a169 100%);
        border-radius: 12px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        width: <?php echo esc_attr($course_progress); ?>%;
        position: relative;
        box-shadow: 0 2px 4px rgba(2, 100, 71, 0.3);
    }
    
    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(90deg, rgba(255,255,255,0.4) 0%, transparent 50%, rgba(255,255,255,0.2) 100%);
        border-radius: 12px;
    }
    
    /* Course Contents */
    .course-contents {
        padding: var(--spacing-lg);
    }
    
    .contents-title {
        font-size: var(--font-size-base);
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: var(--spacing-md);
    }
    
    /* Module Sections */
    .module-section {
        margin-bottom: var(--spacing-lg);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        transition: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .module-section:hover {
        box-shadow: var(--shadow-lg);
    }
    
    .module-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: var(--spacing-lg);
        background: linear-gradient(135deg, var(--white) 0%, var(--border-light) 100%);
        cursor: pointer;
        margin-bottom: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid var(--border-color);
    }
    
    .module-header:hover {
        background: linear-gradient(135deg, var(--hover-bg) 0%, var(--border-light) 100%);
        transform: translateY(-1px);
    }
    
    .module-title {
        font-size: var(--font-size-base);
        font-weight: 600;
        color: var(--text-primary);
        letter-spacing: -0.015em;
    }
    
    .module-toggle {
        width: 16px;
        height: 16px;
        transition: transform 0.2s ease;
    }
    
    .module-section.expanded .module-toggle {
        transform: rotate(180deg);
    }
    
    /* Lesson List */
    .lesson-list {
        padding: var(--spacing-md) 0;
        background: var(--white);
    }
    
    .lesson-item {
        display: flex;
        align-items: center;
        padding: var(--spacing-md) var(--spacing-lg);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid var(--border-light);
        position: relative;
    }
    
    .lesson-item:last-child {
        border-bottom: none;
    }
    
    .lesson-item:hover {
        background: linear-gradient(135deg, var(--hover-bg) 0%, var(--border-light) 100%);
        transform: translateX(4px);
        box-shadow: var(--shadow-sm);
    }
    
    .lesson-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: transparent;
        transition: background-color 0.2s ease;
    }
    
    .lesson-item:hover::before {
        background: var(--success-color);
    }
    
    .lesson-item.current::before {
        background: var(--success-color);
    }
    
    .lesson-status {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid var(--border-color);
        margin-right: var(--spacing-md);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .lesson-status::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.5) 50%, transparent 70%);
        transform: rotate(45deg);
        transition: transform 0.6s;
        opacity: 0;
    }
    
    .lesson-item:hover .lesson-status::before {
        transform: rotate(45deg) translate(50%, 50%);
        opacity: 1;
    }
    
    .lesson-item.completed .lesson-status {
        background: linear-gradient(135deg, var(--success-color) 0%, #38a169 100%);
        border-color: var(--success-color);
        box-shadow: 0 2px 8px rgba(2, 100, 71, 0.3);
        transform: scale(1.05);
    }
    
    .lesson-item.completed .lesson-status::after {
        content: '✓';
        color: var(--white);
        font-size: 14px;
        font-weight: 700;
    }
    
    .lesson-item.current .lesson-status {
        border-color: var(--success-color);
        border-width: 3px;
        box-shadow: 0 0 0 2px rgba(2, 100, 71, 0.2);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(2, 100, 71, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(2, 100, 71, 0); }
        100% { box-shadow: 0 0 0 0 rgba(2, 100, 71, 0); }
    }
    
    .lesson-title {
        flex: 1;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
        line-height: 1.4;
    }
    
    .lesson-item:hover .lesson-title {
        color: var(--success-color);
    }
    
    .lesson-item.current .lesson-title {
        color: var(--success-color);
        font-weight: 600;
    }
    
    .lesson-duration {
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        margin-left: var(--spacing-sm);
        font-weight: 500;
        padding: 2px 8px;
        background: var(--border-light);
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .lesson-item:hover .lesson-duration {
        background: rgba(2, 100, 71, 0.1);
        color: var(--success-color);
    }
    
    /* Main Content Area */
    .course-main {
        flex: 1;
        margin-left: 320px;
        background: var(--white-elevated);
        min-height: calc(100vh - 70px);
        box-shadow: var(--shadow-sm);
        position: relative;
    }
    
    .course-main::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--success-color) 0%, var(--accent-color) 50%, var(--success-color) 100%);
    }
    
    /* Video Player Area */
    .video-container {
        position: relative;
        width: 100%;
        height: 600px;
        background: linear-gradient(145deg, #1a1a1a 0%, #2d3748 100%);
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        border-radius: var(--radius-lg);
    }
    
    .video-container video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: var(--radius-lg);
    }
    
    .video-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at center, rgba(2, 100, 71, 0.1) 0%, transparent 70%);
        pointer-events: none;
        z-index: 1;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .video-container:hover::before {
        opacity: 1;
    }
    
    .video-fallback {
        color: var(--white);
        font-size: var(--font-size-xl);
        text-align: center;
        font-weight: 500;
        opacity: 0.9;
        z-index: 1;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    /* Content Tabs */
    .content-tabs {
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(135deg, var(--white) 0%, var(--border-light) 100%);
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .content-tabs::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, var(--border-color) 50%, transparent 100%);
    }
    
    .tab-list {
        display: flex;
        padding: 0 var(--spacing-lg);
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .tab-button {
        background: none;
        border: none;
        padding: var(--spacing-lg) var(--spacing-xl);
        font-size: var(--font-size-sm);
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        letter-spacing: -0.01em;
    }
    
    .tab-button::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--success-color) 0%, var(--accent-color) 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(-50%);
        border-radius: 2px;
    }
    
    .tab-button.active {
        color: var(--success-color);
        transform: translateY(-1px);
    }
    
    .tab-button.active::after {
        width: 100%;
    }
    
    .tab-button:hover:not(.active) {
        color: var(--text-primary);
        transform: translateY(-1px);
    }
    
    .tab-button:hover:not(.active)::after {
        width: 50%;
        opacity: 0.5;
    }
    
    /* Tab Content */
    .tab-content {
        display: none;
        padding: var(--spacing-xl) var(--spacing-lg);
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .tab-content h2 {
        font-size: var(--font-size-xl);
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: var(--spacing-lg);
    }
    
    .tab-content h3 {
        font-size: var(--font-size-lg);
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: var(--spacing-md);
    }
    
    .tab-content p {
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: var(--spacing-md);
    }
    
    /* Course Description */
    .course-description {
        line-height: 1.6;
        color: var(--text-secondary);
    }
    
    /* Skills Section */
    .skills-container {
        margin-top: var(--spacing-xl);
    }
    
    .skills-list {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-sm);
        margin-top: var(--spacing-md);
    }
    
    .skill-tag {
        background: var(--border-light);
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-lg);
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }
    
    /* Certificate Section */
    .certificate-section {
        background: var(--border-light);
        border-radius: var(--radius-md);
        padding: var(--spacing-lg);
        margin-top: var(--spacing-xl);
    }
    
    .certificate-icon {
        width: 48px;
        height: 48px;
        background: #026447;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: var(--spacing-md);
    }
    
    /* Quiz Section */
    .quiz-section {
        padding: var(--spacing-lg);
        margin-top: var(--spacing-md);
        border-top: 1px solid var(--border-color);
    }
    
    .quiz-item {
        padding: var(--spacing-md);
    }
    
    .quiz-button {
        width: 100%;
        padding: 12px 16px;
        background: linear-gradient(135deg, #026447 0%, #014336 100%);
        color: white !important;
        border: none;
        border-radius: var(--radius-md);
        font-family: 'Inter', sans-serif !important;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(2, 100, 71, 0.2);
    }
    
    .quiz-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(2, 100, 71, 0.3);
        background: linear-gradient(135deg, #037952 0%, #015d43 100%);
    }
    
    .quiz-button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(2, 100, 71, 0.2);
    }
    
    /* Quiz Info Text */
    .quiz-info-text {
        font-family: 'Inter', sans-serif !important;
        font-size: 12px;
        font-weight: 500;
        color: var(--text-secondary);
        text-align: center;
        margin: 0 0 var(--spacing-sm) 0;
        line-height: 1.4;
        opacity: 0.8;
    }
    
    /* Minimalistic Learning Content Styles */
    .objectives-list {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-lg);
    }
    
    .objective-item {
        padding: var(--spacing-sm) var(--spacing-md);
        background: var(--border-light);
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--success-color);
    }
    
    .objective-item p {
        font-family: 'Inter', sans-serif !important;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.5;
        font-size: var(--font-size-sm);
    }
    
    /* Lesson Meta */
    .lesson-meta {
        display: flex;
        gap: var(--spacing-lg);
        padding: var(--spacing-md);
        background: var(--border-light);
        border-radius: var(--radius-sm);
        margin-top: var(--spacing-md);
    }
    
    .meta-item {
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }
    
    /* Key Takeaways */
    .takeaway-section {
        margin-bottom: var(--spacing-lg);
    }
    
    .takeaway-section h3 {
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-md);
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: var(--spacing-sm);
    }
    
    .key-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .key-list li {
        font-family: 'Inter', sans-serif !important;
        padding: var(--spacing-xs) 0;
        border-bottom: 1px solid var(--border-color);
        line-height: 1.5;
        font-size: var(--font-size-sm);
    }
    
    .key-list li:last-child {
        border-bottom: none;
    }
    
    /* Insight Cards */
    .insight-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-sm);
        margin-top: var(--spacing-sm);
    }
    
    .insight-card {
        padding: var(--spacing-md);
        background: var(--border-light);
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--success-color);
    }
    
    .insight-card h4 {
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-sm);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 var(--spacing-xs) 0;
    }
    
    .insight-card p {
        font-family: 'Inter', sans-serif !important;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.4;
        font-size: var(--font-size-sm);
    }
    

    
    /* Practice Exercise */
    .exercise-intro {
        background: var(--border-light);
        padding: var(--spacing-md);
        border-radius: var(--radius-sm);
        margin-bottom: var(--spacing-lg);
        border-left: 3px solid var(--success-color);
    }
    
    .exercise-intro p {
        font-family: 'Inter', sans-serif !important;
        margin: 0;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }
    
    .exercise-steps {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-lg);
    }
    
    .step-item {
        display: flex;
        gap: var(--spacing-md);
        align-items: flex-start;
    }
    
    .step-number {
        width: 24px;
        height: 24px;
        background: var(--success-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif !important;
        font-weight: 600;
        font-size: var(--font-size-sm);
        flex-shrink: 0;
    }
    
    .step-content {
        flex: 1;
    }
    
    .step-content h4 {
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-md);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 var(--spacing-xs) 0;
    }
    
    .step-content p {
        font-family: 'Inter', sans-serif !important;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: var(--spacing-sm);
        font-size: var(--font-size-sm);
    }
    
    .action-button {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-sm);
        background: var(--success-color);
        color: white !important;
        text-decoration: none;
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-sm);
        font-weight: 500;
    }
    
    .action-button:hover {
        background: var(--accent-color);
    }
    
    .practice-questions {
        background: var(--border-light);
        padding: var(--spacing-sm);
        border-radius: var(--radius-sm);
        margin-top: var(--spacing-sm);
    }
    
    .practice-questions p {
        font-family: 'Inter', sans-serif !important;
        font-weight: 600;
        margin-bottom: var(--spacing-xs);
        font-size: var(--font-size-sm);
    }
    
    .practice-questions ul {
        margin: 0;
        padding-left: var(--spacing-md);
    }
    
    .practice-questions li {
        font-family: 'Inter', sans-serif !important;
        color: var(--text-secondary);
        margin-bottom: var(--spacing-xs);
        font-size: var(--font-size-sm);
    }
    
    .analysis-framework {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
        margin-top: var(--spacing-sm);
    }
    
    .framework-item {
        padding: var(--spacing-xs);
        background: var(--border-light);
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }
    
    .completion-note {
        padding: var(--spacing-md);
        background: var(--border-light);
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--success-color);
        margin-top: var(--spacing-lg);
    }
    
    .note-content h4 {
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-sm);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 var(--spacing-xs) 0;
    }
    
    .note-content p {
        font-family: 'Inter', sans-serif !important;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.5;
        font-size: var(--font-size-sm);
    }
    
    /* Quiz Styles */
    .quiz-container {
        max-width: 600px;
    }
    
    .quiz-intro {
        background: var(--border-light);
        padding: var(--spacing-lg);
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--success-color);
        margin-bottom: var(--spacing-lg);
    }
    
    .quiz-intro h3 {
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-lg);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 var(--spacing-sm) 0;
    }
    
    .quiz-intro p {
        font-family: 'Inter', sans-serif !important;
        color: var(--text-secondary);
        margin: 0 0 var(--spacing-md) 0;
        line-height: 1.5;
    }
    
    .quiz-meta {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
    }
    
    .quiz-actions {
        text-align: center;
    }
    
    .start-quiz-btn {
        display: inline-block;
        padding: var(--spacing-md) var(--spacing-xl);
        background: var(--success-color);
        color: white !important;
        text-decoration: none;
        border: none;
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-md);
        font-weight: 600;
        cursor: pointer;
        margin-bottom: var(--spacing-sm);
    }
    
    .start-quiz-btn:hover {
        background: var(--accent-color);
    }
    
    .quiz-note {
        font-family: 'Inter', sans-serif !important;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: 0;
        font-style: italic;
    }
    
    .enrollment-required {
        font-family: 'Inter', sans-serif !important;
        color: var(--text-secondary);
        text-align: center;
        font-style: italic;
    }
    

    
    /* Responsive Design */
    @media (max-width: 768px) {
        .course-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .course-sidebar.open {
            transform: translateX(0);
        }
        
        .course-main {
            margin-left: 0;
        }
        
        .video-container {
            height: 200px;
        }
        
        .tab-content {
            padding: var(--spacing-lg) var(--spacing-md);
        }
    }
    
    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-radius: 50%;
        border-top-color: var(--primary-blue);
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<!-- Project Header with AidData Branding -->
<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="https://www.aiddata.org" target="_blank">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo.png" alt="AidData Logo" class="logo">
            </a>
        </div>
        
        <div class="header-actions">
            <div class="auth-only" style="display: <?php echo is_user_logged_in() ? 'flex' : 'none'; ?>;">
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
                                <?php if (is_user_logged_in()): 
                                    $current_user = wp_get_current_user();
                                ?>
                                <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
                                <span class="user-email"><?php echo esc_html($current_user->user_email); ?></span>
                                <?php else: ?>
                                <span class="user-name">Guest User</span>
                                <span class="user-email">guest@example.com</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="dropdown-item">Home</a>
                        <a href="<?php echo esc_url(home_url('/lp-profile/')); ?>" class="dropdown-item">My Account</a>
                        <button class="dropdown-item logout-button">Sign Out</button>
                    </div>
                </div>
            </div>
            <div class="guest-only" style="display: <?php echo is_user_logged_in() ? 'none' : 'flex'; ?>;">
                <button class="header-button login-button">Log In</button>
                <button class="header-button signup-button">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<div class="course-layout">
    <!-- Left Sidebar Navigation -->
    <aside class="course-sidebar">
        <div class="course-header">
            <h1 class="course-title"><?php echo esc_html(get_the_title($course_id)); ?></h1>
            <div class="course-instructor">
                        <?php
                if (function_exists('llms_get_post') && isset($course)):
                    $instructors = $course->get_instructors();
                    if (!empty($instructors)):
                        echo 'By ';
                        $instructor_names = array();
                        foreach ($instructors as $instructor) {
                            // Safely get instructor name with fallback
                            $name = '';
                            if (isset($instructor['name']) && !empty($instructor['name'])) {
                                $name = $instructor['name'];
                            } elseif (isset($instructor['id']) && !empty($instructor['id'])) {
                                // Fallback to getting name from user data
                                $user = get_userdata($instructor['id']);
                                if ($user) {
                                    $name = $user->display_name;
                                }
                            }
                            
                            if (!empty($name)) {
                                $instructor_names[] = $name;
                            }
                        }
                        
                        if (!empty($instructor_names)) {
                            echo esc_html(implode(', ', $instructor_names));
                        } else {
                            echo 'AidData Training';
                        }
                    else:
                        echo 'By AidData Training';
                    endif;
                else:
                    echo 'By AidData Training';
                endif;
                ?>
                    </div>
            
            <div class="progress-container">
                <div class="progress-label">
                    <span>Course progress</span>
                    <span class="progress-percentage"><?php echo esc_html(round($course_progress)); ?>%</span>
                    </div>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                    </div>
                                        </div>
                                    </div>
                                    
        <div class="course-contents">
            <h2 class="contents-title">Contents</h2>
            
            <!-- Module 1: Research Overview -->
            <div class="module-section expanded">
                <div class="module-header">
                    <div class="module-title">1. Research Overview</div>
                    <svg class="module-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    </svg>
                                    </div>
                <div class="lesson-list">
                                    <?php
                    // Get real LifterLMS course and lessons
                    if ($student && isset($course)):
                        $sections = $course->get_sections('ids');
                        if (!empty($sections)):
                            $first_section = $sections[0];
                            $section_lessons = $course->get_lessons('ids', array('section' => $first_section));
                            
                            if (!empty($section_lessons)):
                                foreach ($section_lessons as $lesson_id):
                                    $lesson = llms_get_post($lesson_id);
                                    if (!$lesson) continue;
                                    
                                    $is_completed = $student->is_complete($lesson_id, 'lesson');
                                    $lesson_url = get_permalink($lesson_id);
                                    
                                    // Estimate duration based on content or use default
                                    $duration = get_post_meta($lesson_id, '_llms_video_duration', true);
                                    if (empty($duration)) {
                                        $duration = '5m'; // Default duration
                                    }
                                    
                                    $class = '';
                                    if ($is_completed) $class .= ' completed';
                                    
                                    // Check if this is the current lesson being viewed
                                    $current_lesson_param = get_query_var('lesson', '');
                                    $lesson_order = array_search($lesson_id, $section_lessons) + 1;
                                    if ($current_lesson_param == $lesson_order) {
                                        $class .= ' current';
                                    }
                                ?>
                                <div class="lesson-item<?php echo esc_attr($class); ?>" data-lesson-id="<?php echo esc_attr($lesson_id); ?>">
                                    <div class="lesson-status"></div>
                                    <a href="<?php echo esc_url($lesson_url); ?>" class="lesson-title"><?php echo esc_html($lesson->get('title')); ?></a>
                                    <span class="lesson-duration"><?php echo esc_html($duration); ?></span>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <p style="padding: 10px; color: #666; font-size: 14px;">No lessons found in this section.</p>
                                <?php endif; ?>
                        <?php else: ?>
                            <!-- Fallback to demo lessons if no sections found -->
                            <?php
                            $demo_lessons = array(
                                array('title' => 'Introduction to the Research', 'duration' => '3m 45s', 'completed' => true),
                                array('title' => 'Methodology & Data Collection', 'duration' => '5m 12s', 'completed' => true),
                                array('title' => 'Key Findings', 'duration' => '4m 28s', 'completed' => false, 'current' => true)
                            );
                            
                            foreach ($demo_lessons as $index => $lesson):
                                $class = '';
                                if (isset($lesson['completed']) && $lesson['completed']) $class .= ' completed';
                                if (isset($lesson['current']) && $lesson['current']) $class .= ' current';
                            ?>
                            <div class="lesson-item<?php echo esc_attr($class); ?>">
                                <div class="lesson-status"></div>
                                <a href="#" class="lesson-title"><?php echo esc_html($lesson['title']); ?></a>
                                <span class="lesson-duration"><?php echo esc_html($lesson['duration']); ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="padding: 10px; color: #666; font-size: 14px;">Please log in to view course content.</p>
                    <?php endif; ?>
                    </div>
                </div>
                
                                    <?php
            // Display remaining sections and their lessons
            if ($student && isset($course) && !empty($sections)):
                $section_count = 1;
                foreach ($sections as $section_id):
                    $section_count++;
                    if ($section_count > 4) break; // Limit to 4 sections for display
                    
                    $section = llms_get_post($section_id);
                    if (!$section) continue;
                    
                    $section_lessons = $course->get_lessons('ids', array('section' => $section_id));
                    $section_title = $section->get('title');
                    
                    // Skip first section as it's already displayed above
                    if ($section_count == 2) continue;
            ?>
            <!-- Section <?php echo $section_count - 1; ?>: <?php echo esc_html($section_title); ?> -->
            <div class="module-section">
                <div class="module-header">
                    <div class="module-title"><?php echo $section_count - 1; ?>. <?php echo esc_html($section_title); ?></div>
                    <svg class="module-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                                        </div>
                <div class="lesson-list" style="display: none;">
                    <?php if (!empty($section_lessons)): ?>
                        <?php foreach ($section_lessons as $lesson_id): 
                            $lesson = llms_get_post($lesson_id);
                            if (!$lesson) continue;
                            
                            $is_completed = $student->is_complete($lesson_id, 'lesson');
                            $lesson_url = get_permalink($lesson_id);
                            $duration = get_post_meta($lesson_id, '_llms_video_duration', true);
                            if (empty($duration)) {
                                $duration = '5m';
                            }
                        ?>
                        <div class="lesson-item<?php echo $is_completed ? ' completed' : ''; ?>" data-lesson-id="<?php echo esc_attr($lesson_id); ?>">
                            <div class="lesson-status"></div>
                            <a href="<?php echo esc_url($lesson_url); ?>" class="lesson-title"><?php echo esc_html($lesson->get('title')); ?></a>
                            <span class="lesson-duration"><?php echo esc_html($duration); ?></span>
                                </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="padding: 10px; color: #666; font-size: 14px;">No lessons in this section.</p>
                    <?php endif; ?>
                            </div>
                                </div>
            <?php endforeach; ?>
            
            <?php else: ?>
            <!-- Fallback sections when no real data available -->
            <!-- Module 2: Contract Search Tools -->
            <div class="module-section">
                <div class="module-header">
                    <div class="module-title">2. Contract Search Tools</div>
                    <svg class="module-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                                </div>
                <div class="lesson-list" style="display: none;">
                                    <div class="lesson-item">
                        <div class="lesson-status"></div>
                        <a href="#" class="lesson-title">Navigating the Interface</a>
                        <span class="lesson-duration">6m 15s</span>
                                    </div>
                                    <div class="lesson-item">
                        <div class="lesson-status"></div>
                        <a href="#" class="lesson-title">Advanced Search Techniques</a>
                        <span class="lesson-duration">8m 32s</span>
                                    </div>
                                    <div class="lesson-item">
                        <div class="lesson-status"></div>
                        <a href="#" class="lesson-title">Filtering Results</a>
                        <span class="lesson-duration">4m 18s</span>
                                    </div>
                                </div>
                            </div>
                            
            <!-- Module 3: Comparative Analysis -->
            <div class="module-section">
                <div class="module-header">
                    <div class="module-title">3. Comparative Analysis</div>
                    <svg class="module-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                                </div>
                <div class="lesson-list" style="display: none;">
                                    <div class="lesson-item">
                        <div class="lesson-status"></div>
                        <a href="#" class="lesson-title">Comparing Multiple Contracts</a>
                        <span class="lesson-duration">7m 22s</span>
                                    </div>
                                    <div class="lesson-item">
                        <div class="lesson-status"></div>
                        <a href="#" class="lesson-title">Trend Analysis Tools</a>
                        <span class="lesson-duration">9m 45s</span>
                                    </div>
                                    <div class="lesson-item">
                        <div class="lesson-status"></div>
                        <a href="#" class="lesson-title">Visualization Features</a>
                        <span class="lesson-duration">5m 33s</span>
                        </div>
                    </div>
                </div>
                
            <!-- Module 4: Assessment -->
            <div class="module-section">
                <div class="module-header">
                    <div class="module-title">4. Assessment</div>
                    <svg class="module-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
                <div class="lesson-list" style="display: none;">
                    <div class="quiz-item">
                        <p class="quiz-info-text">Receive more than 80% to receive a completion certificate</p>
                        <button class="quiz-button" onclick="loadQuiz()">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Take the Quiz
                        </button>
                    </div>
                </div>
            </div>
                            
            <?php endif; ?>
                                </div>
    </aside>
    
    <!-- Main Content Area -->
    <main class="course-main">
        <!-- Video Player -->
        <div class="video-container">
            <video controls preload="metadata" poster="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/coming_soon.png">
                <source src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/videos/data_journalism.mp4" type="video/mp4">
                <div class="video-fallback">
                    <div>📹 Key Findings</div>
                    <div style="font-size: 14px; margin-top: 8px; opacity: 0.7;">Your browser does not support the video element.</div>
                                </div>
            </video>
                            </div>
                            
        <!-- Content Tabs -->
        <div class="content-tabs">
            <div class="tab-list">
                <button class="tab-button active" data-tab="objectives">Learning Objectives</button>
                <button class="tab-button" data-tab="keypoints">Key Takeaways</button>
                <button class="tab-button" data-tab="practice">Practice Exercise</button>
                    </div>
                </div>
                
        <!-- Tab Contents -->
        <div class="tab-content active" id="objectives">
            <h2>Learning Objectives</h2>
            <?php
            // Get current lesson or use fallback content
            $current_lesson_content = array(
                'objectives' => array(
                    'Navigate the dashboard interface and understand the main components',
                    'Use basic search functions to find specific contracts and data',
                    'Interpret data visualizations and charts effectively',
                    'Apply systematic research methods to analyze lending patterns',
                    'Extract meaningful insights from Chinese development finance data'
                ),
                'duration' => '15-20 minutes',
                'level' => 'Beginner to Intermediate'
            );
            
            // Check if we're viewing a specific lesson and customize content
            $lesson_param = get_query_var('lesson', '');
            if (!empty($lesson_param)) {
                // Customize content based on lesson
                switch($lesson_param) {
                    case '1':
                        $current_lesson_content['objectives'] = array(
                            'Understand the overall structure of Chinese development finance',
                            'Identify key data sources and their reliability',
                            'Navigate the How China Lends dashboard interface',
                            'Recognize different types of financial instruments used'
                        );
                        break;
                    case '2':
                        $current_lesson_content['objectives'] = array(
                            'Master advanced search techniques and filters',
                            'Use geographic and sector-based search parameters',
                            'Apply date range filters effectively',
                            'Export search results for further analysis'
                        );
                        break;
                    case '3':
                        $current_lesson_content['objectives'] = array(
                            'Compare lending patterns across different countries',
                            'Analyze trends over time using visualization tools',
                            'Create comparative charts and graphs',
                            'Identify patterns in lending terms and conditions'
                        );
                        break;
                }
            }
            ?>
            
            <div class="objectives-list">
                <?php foreach ($current_lesson_content['objectives'] as $objective): ?>
                    <div class="objective-item">
                        <p><?php echo esc_html($objective); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="lesson-meta">
                <div class="meta-item">
                    <strong>Duration:</strong> <?php echo esc_html($current_lesson_content['duration']); ?>
                </div>
                <div class="meta-item">
                    <strong>Level:</strong> <?php echo esc_html($current_lesson_content['level']); ?>
                </div>
            </div>
        </div>
                
        <div class="tab-content" id="keypoints">
            <h2>Key Takeaways</h2>
            <?php
            // Lesson-specific key takeaways
            $lesson_takeaways = array(
                'concepts' => array(
                    'Dashboard Structure: The interface is organized into main navigation, search tools, and results display areas',
                    'Search Methodology: Combine multiple filters and parameters for precise data retrieval',
                    'Data Interpretation: Chinese lending data reveals patterns in regional focus, sector preferences, and loan terms',
                    'Research Context: Understanding geopolitical implications of development finance flows'
                ),
                'insights' => array(
                    'Geographic Patterns' => 'Chinese lending concentrates in specific regions based on strategic partnerships and infrastructure needs',
                    'Sector Analysis' => 'Infrastructure projects dominate Chinese development finance, particularly in transportation and energy',
                    'Terms & Conditions' => 'Loan terms vary significantly based on recipient country risk profiles and project types'
                )
            );
            
            // Customize based on lesson
            if (!empty($lesson_param)) {
                switch($lesson_param) {
                    case '1':
                        $lesson_takeaways['concepts'] = array(
                            'Data Sources: Multiple databases track Chinese development finance with varying levels of detail',
                            'Financial Instruments: Includes loans, grants, export credits, and investment commitments',
                            'Reporting Standards: Inconsistent reporting makes comprehensive analysis challenging',
                            'Research Methodology: Triangulation across sources improves data reliability'
                        );
                        break;
                    case '2':
                        $lesson_takeaways['concepts'] = array(
                            'Filter Combinations: Multiple search parameters can be combined for precise results',
                            'Geographic Scope: Search by country, region, or global patterns',
                            'Temporal Analysis: Date ranges reveal lending trends over time',
                            'Export Functions: Data can be downloaded for offline analysis'
                        );
                        break;
                    case '3':
                        $lesson_takeaways['concepts'] = array(
                            'Comparative Methods: Side-by-side analysis reveals different lending approaches',
                            'Trend Identification: Time-series data shows evolving priorities',
                            'Pattern Recognition: Common themes emerge across different recipient countries',
                            'Contextual Factors: Political and economic conditions influence lending patterns'
                        );
                        break;
                }
            }
            ?>
            
            <div class="takeaway-section">
                <h3>Essential Concepts</h3>
                <ul class="key-list">
                    <?php foreach ($lesson_takeaways['concepts'] as $concept): ?>
                        <li><?php echo esc_html($concept); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="takeaway-section">
                <h3>Key Insights</h3>
                <div class="insight-cards">
                    <?php foreach ($lesson_takeaways['insights'] as $title => $description): ?>
                        <div class="insight-card">
                            <h4><?php echo esc_html($title); ?></h4>
                            <p><?php echo esc_html($description); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
                                
        <div class="tab-content" id="practice">
            <?php
            // Check if this is a quiz lesson (lesson 4 = assessment)
            $is_quiz_lesson = ($lesson_param == '4' || (isset($_GET['quiz']) && $_GET['quiz'] == '1'));
            
            if ($is_quiz_lesson):
                // Get LifterLMS quiz data
                $quiz_id = null;
                if (function_exists('llms_get_post') && isset($course)):
                    $quizzes = $course->get_quizzes();
                    if (!empty($quizzes)) {
                        $quiz_id = $quizzes[0]; // Get first quiz
                    }
                endif;
            ?>
            
            <h2>Assessment Quiz</h2>
            <div class="quiz-container">
                <?php if ($quiz_id && function_exists('llms_get_post')): 
                    $quiz = llms_get_post($quiz_id);
                    if ($quiz):
                ?>
                
                <div class="quiz-intro">
                    <h3><?php echo esc_html($quiz->get('title')); ?></h3>
                    <p><?php echo esc_html($quiz->get('content')); ?></p>
                    <div class="quiz-meta">
                        <div class="meta-item">
                            <strong>Questions:</strong> <?php echo $quiz->get_question_count(); ?>
                        </div>
                        <div class="meta-item">
                            <strong>Time Limit:</strong> <?php echo $quiz->get('time_limit') ? $quiz->get('time_limit') . ' minutes' : 'No limit'; ?>
                        </div>
                        <div class="meta-item">
                            <strong>Passing Grade:</strong> <?php echo $quiz->get('passing_percent'); ?>%
                        </div>
                    </div>
                </div>
                
                <?php if ($student && $student->is_enrolled($course_id)): ?>
                    <div class="quiz-actions">
                        <a href="<?php echo get_permalink($quiz_id); ?>" class="start-quiz-btn">
                            Start Quiz
                        </a>
                        <p class="quiz-note">You need to score 80% or higher to receive your completion certificate.</p>
                    </div>
                <?php else: ?>
                    <p class="enrollment-required">Please enroll in the course to take the quiz.</p>
                <?php endif; ?>
                
                <?php else: ?>
                    <!-- Fallback quiz content -->
                    <div class="quiz-intro">
                        <h3>Course Assessment</h3>
                        <p>Test your knowledge of Chinese development finance research methods and dashboard usage.</p>
                        <div class="quiz-meta">
                            <div class="meta-item">
                                <strong>Questions:</strong> 10
                            </div>
                            <div class="meta-item">
                                <strong>Time Limit:</strong> 20 minutes
                            </div>
                            <div class="meta-item">
                                <strong>Passing Grade:</strong> 80%
                            </div>
                        </div>
                    </div>
                    
                    <div class="quiz-actions">
                        <button class="start-quiz-btn" onclick="alert('Quiz functionality requires LifterLMS integration.')">
                            Start Quiz
                        </button>
                        <p class="quiz-note">You need to score 80% or higher to receive your completion certificate.</p>
                    </div>
                <?php endif; ?>
                
                <?php endif; ?>
            </div>
            
            <?php else: ?>
            
            <h2>Practice Exercise</h2>
            <div class="practice-content">
                <div class="exercise-intro">
                    <p>Apply what you've learned with this hands-on exercise. Complete each step to reinforce your understanding.</p>
                </div>
                
                <?php
                // Lesson-specific practice exercises
                $practice_steps = array(
                    array(
                        'title' => 'Dashboard Exploration',
                        'description' => 'Open the How China Lends Dashboard and spend 5 minutes familiarizing yourself with the main interface elements.',
                        'action_text' => 'Open Dashboard',
                        'action_url' => 'https://china.aiddata.org/'
                    ),
                    array(
                        'title' => 'Search Practice',
                        'description' => 'Perform a search for infrastructure projects in Sub-Saharan Africa. Note the number of results and key patterns you observe.',
                        'questions' => array(
                            'How many projects were found?',
                            'Which countries received the most funding?',
                            'What sectors are most represented?'
                        )
                    ),
                    array(
                        'title' => 'Data Analysis',
                        'description' => 'Select one specific project and examine its details. Document your findings using the framework provided.',
                        'framework' => array(
                            'Project Details: Name, location, amount, timeline',
                            'Strategic Context: Why might China have funded this project?',
                            'Impact Assessment: What are the potential benefits and risks?'
                        )
                    )
                );
                
                // Customize steps based on lesson
                if (!empty($lesson_param)) {
                    switch($lesson_param) {
                        case '2':
                            $practice_steps[1]['description'] = 'Practice using advanced search filters: try searching by date range, amount thresholds, and specific sectors.';
                            break;
                        case '3':
                            $practice_steps[2]['description'] = 'Compare lending patterns between two different countries in the same region.';
                            break;
                    }
                }
                ?>
                
                <div class="exercise-steps">
                    <?php foreach ($practice_steps as $index => $step): ?>
                        <div class="step-item">
                            <div class="step-number"><?php echo $index + 1; ?></div>
                            <div class="step-content">
                                <h4><?php echo esc_html($step['title']); ?></h4>
                                <p><?php echo esc_html($step['description']); ?></p>
                                
                                <?php if (isset($step['action_url'])): ?>
                                    <div class="step-action">
                                        <a href="<?php echo esc_url($step['action_url']); ?>" target="_blank" class="action-button">
                                            <?php echo esc_html($step['action_text']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($step['questions'])): ?>
                                    <div class="practice-questions">
                                        <p><strong>Questions to consider:</strong></p>
                                        <ul>
                                            <?php foreach ($step['questions'] as $question): ?>
                                                <li><?php echo esc_html($question); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($step['framework'])): ?>
                                    <div class="analysis-framework">
                                        <?php foreach ($step['framework'] as $framework_item): ?>
                                            <div class="framework-item">
                                                <?php echo esc_html($framework_item); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="completion-note">
                    <div class="note-content">
                        <h4>Exercise Complete</h4>
                        <p>Once you've completed these steps, you'll have practical experience with the core dashboard functions. Consider taking notes on your findings for future reference.</p>
                    </div>
                </div>
            </div>
            
            <?php endif; ?>
        </div>
                                

    </div>
</main>
                                        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Header dropdown functionality
        const menuButton = document.querySelector('.menu-button');
        const profileDropdown = document.querySelector('.profile-dropdown');
        const notificationsButton = document.getElementById('notificationsButton');

        if (menuButton && profileDropdown) {
        menuButton.addEventListener('click', function() {
                profileDropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!menuButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                    profileDropdown.classList.remove('active');
                }
            });
        }
        
    // Logout functionality
    const logoutButton = document.querySelector('.logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function() {
            // This would normally be handled by WordPress wp_logout_url()
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '<?php echo wp_logout_url(home_url()); ?>';
            }
        });
    }

    // Module toggle functionality
    const moduleHeaders = document.querySelectorAll('.module-header');
    moduleHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const section = this.parentElement;
            const lessonList = section.querySelector('.lesson-list');
            
            section.classList.toggle('expanded');
            
            if (section.classList.contains('expanded')) {
                lessonList.style.display = 'block';
            } else {
                lessonList.style.display = 'none';
            }
        });
    });
    
    // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Lesson click functionality with LifterLMS integration
    const lessonItems = document.querySelectorAll('.lesson-item');
    lessonItems.forEach(item => {
        const lessonLink = item.querySelector('.lesson-title');
        
        // Handle lesson navigation
        lessonLink.addEventListener('click', function(e) {
            // Let the normal link navigation work - this will go to the lesson page
            // LifterLMS will handle the lesson progression automatically
            
            // Update UI state
            lessonItems.forEach(lesson => lesson.classList.remove('current'));
            item.classList.add('current');
            
            // Update video element (optional - you could load different videos per lesson)
            const videoElement = document.querySelector('.video-container video');
            if (videoElement) {
                // For now, all lessons use the same video
                // You could add lesson-specific video URLs here
                videoElement.load(); // Reload the current video
                        }
                    });
                });

    // Lesson completion tracking (if needed for AJAX updates)
    function markLessonComplete(lessonId) {
        if (!lessonId) return;
        
        // This would be used for AJAX lesson completion if needed
        // LifterLMS handles this through its own mechanisms normally
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'llms_mark_complete',
                lesson_id: lessonId,
                nonce: '<?php echo wp_create_nonce('llms_lesson_complete'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI to show completion
                const lessonItem = document.querySelector(`[data-lesson-id="${lessonId}"]`);
                if (lessonItem) {
                    lessonItem.classList.add('completed');
                }
                
                // Update progress bar
                updateProgressBar();
            }
        })
        .catch(error => {
            console.error('Error marking lesson complete:', error);
        });
    }

    // Update progress bar
    function updateProgressBar() {
        const completedLessons = document.querySelectorAll('.lesson-item.completed').length;
        const totalLessons = document.querySelectorAll('.lesson-item').length;
        const progress = totalLessons > 0 ? (completedLessons / totalLessons) * 100 : 0;
        
        const progressFill = document.querySelector('.progress-fill');
        const progressPercentage = document.querySelector('.progress-percentage');
        
        if (progressFill) {
            progressFill.style.width = progress + '%';
        }
        
        if (progressPercentage) {
            progressPercentage.textContent = Math.round(progress) + '%';
        }
    }
});

// Quiz loading function
function loadQuiz() {
    // Add quiz parameter to current URL and reload
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('quiz', '1');
    window.location.href = currentUrl.toString();
}

// Auto-switch to Practice Exercise tab if quiz parameter is present
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('quiz') === '1') {
        // Switch to practice tab to show the quiz
        const practiceTab = document.querySelector('[data-tab="practice"]');
        const practiceContent = document.getElementById('practice');
        
        if (practiceTab && practiceContent) {
            // Remove active from all tabs
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Activate practice tab
            practiceTab.classList.add('active');
            practiceContent.classList.add('active');
            
            // Scroll to the quiz content
            setTimeout(() => {
                practiceContent.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    }
});
</script>

<?php get_footer(); ?> 
