<?php
/**
 * Template for displaying content of single course.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

// DEBUG: Remove this line after confirming template loads
echo '<!-- CUSTOM SINGLE-COURSE.PHP TEMPLATE LOADED -->';

// Enqueue styles from GIE template logic
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');

// Get current course data
$course_id = get_the_ID();
$course = learn_press_get_course($course_id);

// Load WordPress header
get_header();
?>

<!-- Critical CSS to hide default header immediately -->
<style>
    /* CRITICAL: Hide default header immediately to prevent flash */
    header:not(.lms-header),
    header.wp-block-template-part,
    .wp-block-template-part header,
    body > header:not(.lms-header) {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        z-index: -9999 !important;
        height: 0 !important;
        overflow: hidden !important;
    }
</style>

<!-- Inline Styles for GIE Template Design -->
<style>
    /* Import Inter font from Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    /* Webkit browsers (Chrome, Safari, newer versions of Opera and Edge) */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #004E38 ;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #004E38 ;
    }
    
    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #004E38  #f1f1f1;
    }

    /* Loading screen styles */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f5f5f5;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease;
    }

    .loading-content {
        text-align: center;
    }

    .loading-logo {
        width: 120px;
        height: auto;
        margin-bottom: 20px;
    }

    .loading-spinner {
        margin: 20px auto;
    }

    .spinner-ring {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #004E38;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        color: #666;
        font-size: 14px;
        margin-top: 10px;
    }

    /* Global Reset from GIE Template */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
        color: #333;
        line-height: 1.6;
        background-color: #f5f5f5;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Remove margins from WordPress containers to eliminate gap */
    #page,
    .wp-site-blocks,
    .wp-block-group {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    /* Hide legacy WordPress header elements that create gap */
    #headerimg,
    #page > div:first-child:not(.hero):not(.lms-header),
    #page > hr,
    body > hr,
    .wp-site-blocks > hr {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .headerimg {
        color: #ffffff !important;
        
    }

    /* Force Inter font on all text elements */
    h1, h2, h3, h4, h5, h6, p, span, div, a, button, input, textarea, select, label, li, td, th {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
    }

    /* Hide Default Theme Header and all related elements */
    header:not(.lms-header),
    .site-header, 
    #masthead, 
    header.wp-block-template-part,
    .wp-block-template-part header,
    body > header:not(.lms-header),
    header[class*="wp-block-template-part"]:not(.lms-header),
    .wp-block-template-part:has(header),
    .site-branding,
    .custom-logo,
    .site-title,
    .site-description,
    #site-navigation,
    .main-navigation,
    .header-image,
    .custom-header,
    .wp-custom-header {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }
    
    /* Ensure OUR custom header is always visible and on top */
    header.lms-header {
        display: block !important;
        visibility: visible !important;
        height: auto !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 10000 !important;
        opacity: 1 !important;
    }

    /* Header */
    .lms-header {
        background-color: white;
        padding: 1rem 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .header-content {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.25rem;
        font-weight: 600;
        color: #004E38;
    }

    /* Profile Dropdown */
    .profile-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .profile-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .dropdown-user-info .user-name {
        font-weight: 600;
        color: #004E38;
        display: block;
    }

    .dropdown-user-info .user-email {
        font-size: 0.875rem;
        color: #666;
        display: block;
        margin-top: 0.25rem;
    }

    .dropdown-item {
        display: block;
        padding: 0.75rem 1rem;
        color: #333;
        text-decoration: none;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .header-actions {
        position: relative;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                    url('<?php echo get_the_post_thumbnail_url() ? get_the_post_thumbnail_url() : esc_url( get_template_directory_uri() . '/assets/images/GIE_coursethumbnail.jpg' ); ?>');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 4rem 2rem;
        margin-top: 70px; /* Account for fixed header height */
    }

    .hero-content {
        max-width: 1200px;
        margin: 0 auto;
    }

    .breadcrumb {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .breadcrumb a {
        color: white;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .tutorial-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: white;
    }

    .tutorial-subtitle {
        font-size: 1.25rem;
        opacity: 0.95;
        margin-bottom: 2rem;
        max-width: 800px;
    }

    .tutorial-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }

    .tag {
        background-color: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 0.375rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tutorial-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        font-size: 0.875rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Main Content Grid */
    .main-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 2rem;
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 3rem;
    }

    /* Sidebar and Content Styles needed for inner templates */
    .content-section {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .content-column {
        min-width: 0;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #004E38;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .section-content p {
        margin-bottom: 1rem;
        color: #555;
    }

    .section-content ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .section-content li {
        padding: 0.5rem 0;
        padding-left: 1.25rem;
        position: relative;
        color: #555;
    }

    .section-content li:before {
        content: "-";
        position: absolute;
        left: 0;
        color: #004E38;
        font-weight: 700;
    }

    /* Learning Objectives Grid - Exact match to AidData Tutorial Template */
    .learning-objectives-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .learning-card {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .learning-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .learning-icon {
        width: 48px;
        height: 48px;
        background: #004E38;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        color: white;
    }

    .learning-icon svg {
        width: 24px;
        height: 24px;
    }

    .learning-card h4 {
        margin: 0 0 0.75rem 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #004E38;
        line-height: 1.3;
    }

    .learning-card p {
        margin: 0;
        font-size: 0.9rem;
        color: #555;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .learning-objectives-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .learning-card {
            padding: 1.25rem;
        }
    }

    .lp-course-buttons {
        display: grid;
        gap: 0.75rem;
    }

    .lp-course-buttons .button,
    .lp-course-buttons button,
    .lp-course-buttons input[type="submit"],
    .lp-course-buttons .course-button,
    .lp-course-buttons form {
        display: block !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .lp-course-buttons .button,
    .lp-course-buttons button,
    .lp-course-buttons input[type="submit"] {
        padding: 1rem;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        text-align: center;
        border: none;
        background-color: #004E38;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 !important; /* Reset margins */
    }

    .lp-course-buttons .button:hover,
    .lp-course-buttons button:hover,
    .lp-course-buttons input[type="submit"]:hover {
        background-color: #164d40;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 95, 79, 0.3);
    }

    .lp-course-progress-wrapper {
        margin: 0;
    }

    .cta-card .course-price {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        color: #004E38;
        margin: 1rem 0;
    }

    .sidebar {
        position: sticky;
        top: 100px;
        align-self: start;
    }

    .cta-card {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        margin-bottom: 1.5rem;
    }

    .course-media img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        display: block;
    }

    /* Footer */
    .site-footer {
        background-color: #004E38;
        color: white;
        padding: 4rem 2rem 2rem;
        position: relative;
        z-index: 100;
    }

    /* Course Curriculum - Exact match to AidData Tutorial Template */
    /* Using both .tutorial-step and .curriculum-item for compatibility */
    .tutorial-step,
    .curriculum-item {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 0.75rem;
        overflow: hidden;
    }

    .step-header,
    .curriculum-header {
        padding: 1rem 1.25rem;
        background-color: #f8f8f8;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.3s;
    }

    .step-header:hover,
    .curriculum-header:hover {
        background-color: #efefef;
    }

    .step-title,
    .curriculum-title {
        font-weight: 600;
        color: #333;
    }

    .step-toggle,
    .curriculum-toggle {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #004E38;
        transition: transform 0.3s;
    }

    .tutorial-step.active .step-toggle,
    .curriculum-item.active .curriculum-toggle {
        transform: rotate(180deg);
    }

    .step-content,
    .curriculum-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .tutorial-step.active .step-content,
    .curriculum-item.active .curriculum-content {
        max-height: 500px;
    }

    .step-details,
    .curriculum-details {
        padding: 1rem 1.25rem;
    }

    .step-details p,
    .curriculum-details p {
        margin-bottom: 0.75rem;
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    ul.lesson-list,
    .lesson-list {
        padding: 1rem 1.25rem;
        list-style: none !important;
        margin: 0;
    }

    ul.lesson-list li,
    .lesson-list li {
        padding: 0.5rem 0;
        padding-left: 0 !important;
        color: #666;
        font-size: 0.9rem;
        list-style: none !important;
        position: relative;
    }

    ul.lesson-list li:before,
    .lesson-list li:before,
    .curriculum-content .lesson-list li:before,
    .section-content .lesson-list li:before,
    .step-details li:before {
        content: none !important;
        display: none !important;
    }

    .lesson-list p {
        padding: 0;
        margin-bottom: 0.75rem;
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .lesson-list p:last-child {
        margin-bottom: 0;
    }

    /* Footer */

    
    /* Aggressively hide the WordPress block template footer that appears after custom footer */
    footer.wp-block-template-part,
    .wp-block-template-part footer,
    body footer.wp-block-template-part,
    footer[class*="wp-block-template-part"],
    .site-footer ~ footer,
    .site-footer ~ .wp-block-template-part,
    footer.site-footer ~ footer {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }
    
    /* Also hide any groups or blocks that come after the custom footer */
    .site-footer ~ .wp-block-group,
    footer.site-footer ~ div,
    footer.site-footer ~ section {
        display: none !important;
    }


    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
    }

    .footer-section h4 {
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        color: white;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 0.75rem;
    }

    .footer-section a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: color 0.2s;
    }

    .footer-section a:hover {
        color: white;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .main-content {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
            order: -1;
        }
    }

    /* Hide WordPress block elements (comments, latest posts, widgets) */
    .wp-block-latest-comments,
    .wp-block-latest-posts__list,
    .wp-block-latest-posts,
    .widget.widget_block.widget_search,
    .widget_block,
    .widget_search,
    .widget_categories,
    #comments,
    .comments-area,
    .comment-list,
    .comment-form {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }


    /* Hide LearnPress course meta-primary section */
    .course-meta.course-meta-primary,
    .course-meta-primary,
    .course-detail-info .course-meta {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }

    /* Hide default LearnPress course content (tabs, sidebar) that appears outside our custom template */
    .lp-single-course,
    .learn-press-content-item,
    .course-summary,
    .lp-content-area,
    .learn-press-message,
    #learn-press-course,
    .lp-archive-courses,
    .course-detail-info,
    .lp-single-course-main,
    .course-tabs,
    .learn-press-tabs,
    .course-tab-panels,
    #tab-course-tab-overview,
    #tab-course-tab-curriculum,
    #tab-course-tab-instructor,
    .lp-course-tabs,
    body.single-lp_course .entry-content > *:not(.main-content):not(.hero):not(.lms-header):not(.site-footer):not(style):not(script) {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }

    /* Ensure our custom content is visible */
    .main-content,
    .main-content * {
        display: revert;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        position: relative !important;
        left: auto !important;
        top: auto !important;
        z-index: auto !important;
        pointer-events: auto !important;
    }

    .main-content {
        display: grid !important;
    }

    /* Hide LearnPress course title in content area (already shown in hero) */
    .course-title,
    .entry-title.course-title,
    .course-detail-info .course-title,
    .lp-content-area .course-title,
    h1.course-title {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }
    
    /* Fix: Hide empty ajax message container to prevent empty green button border */
    .lp-ajax-message:empty,
    .lp-ajax-message:not(.error):not(.success):not(.warning) {
        display: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Style actual messages properly */
    .lp-ajax-message {
        margin-top: 10px !important;
        padding: 10px 15px !important;
        border-radius: 4px !important;
        font-size: 0.9rem !important;
        text-align: center !important;
    }

    .lp-ajax-message.error {
        background-color: #fde8e8 !important;
        color: #c53030 !important;
        border: 1px solid #fecaca !important;
    }

    .lp-ajax-message.success {
        background-color: #def7ec !important;
        color: #03543f !important;
        border: 1px solid #bcf0da !important;
    }

    /* Course Progress Bar Styling */
    .course-results-progress .course-progress {
        margin-top: 0.5rem;
    }

    .course-results-progress .items-progress {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        color: #555;
    }

    .course-results-progress .items-progress__heading {
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0;
        color: #555;
    }

    .course-results-progress .lp-course-status {
        margin-bottom: 0.25rem;
    }

    .course-results-progress .lp-course-status .number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #004E38;
    }

    .course-results-progress .lp-course-status .percentage-sign {
        font-size: 0.875rem;
        font-weight: 600;
        color: #004E38;
    }

    /* Progress Bar Track */
    .learn-press-progress,
    .course-results-progress .lp-progress-bar {
        height: 10px !important;
        background-color: #f1f1f1 !important;
        border-radius: 5px !important;
        overflow: hidden !important;
        margin-bottom: 0.5rem !important;
        position: relative !important;
        width: 100% !important;
        display: block !important; /* Ensure it takes space */
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.1) !important;
    }

    /* Progress Bar Fill */
    .course-results-progress .lp-progress-active,
    .course-results-progress .lp-progress-value {
        background-color: #004E38 !important;
        height: 100% !important;
        border-radius: 5px !important;
        transition: width 0.5s ease;
        display: block !important;
        /* Removed position: absolute to allow natural flow if needed, but keeping standard bar behavior */
        position: relative !important; 
        min-width: 0;
    }

    /* Hide the passing condition marker if it looks messy */
    .lp-passing-conditional {
        display: none !important;
    }

</style>

<!-- Loading Screen -->
<div class="loading-screen">
    <div class="loading-triangles-container">
        <div class="loading-triangle triangle-move-1"></div>
        <div class="loading-triangle triangle-move-2"></div>
        <div class="loading-triangle triangle-move-3"></div>
    </div>
    
    <div class="loading-content">
        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="loading-logo">
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
        </div>
        <p class="loading-text">Loading Training Hub</p>
    </div>
</div>

<!-- Header -->
<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="https://www.aiddata.org" target="_blank">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
            </a>
        </div>
        
        <div class="header-actions">
            <?php 
            $is_logged_in = is_user_logged_in();
            $current_user = wp_get_current_user();
            ?>
            <div class="auth-only" style="display: <?php echo $is_logged_in ? 'block' : 'none'; ?>;">
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
                                <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
                                <span class="user-email"><?php echo esc_html($current_user->user_email); ?></span>
                            </div>
                        </div>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="dropdown-item">Home</a>
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

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">

        
        <h1 class="tutorial-title"><?php the_title(); ?></h1>
        <p class="tutorial-subtitle"><?php echo get_the_excerpt(); ?></p>
        
        <div class="tutorial-tags">
            <?php
            $terms = get_the_terms( get_the_ID(), 'course_category' );
            if( !empty($terms) && !is_wp_error($terms) ){
                foreach( $terms as $term ){
                    echo '<span class="tag">' . esc_html( $term->name ) . '</span>';
                }
            }
            ?>
        </div>
        
        <div class="tutorial-meta">
            <div class="meta-item">
                <div class="meta-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <span><?php echo get_post_meta(get_the_ID(), '_lp_duration', true) ?: 'Self-paced'; ?></span>
            </div>
            <div class="meta-item">
                 <div class="meta-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <span><?php echo get_post_meta(get_the_ID(), '_lp_level', true) ?: 'All Levels'; ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <?php
    // content-single-course.php will handle the inner grid (Content Column + Sidebar)
    while ( have_posts() ) {
        the_post();
        learn_press_get_template( 'content-single-course' );
    }
    ?>
</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo.png" alt="AidData Logo" class="footer-logo">
            <p> </p>
            <div class="social-links">
                <a href="https://twitter.com/AidData" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                </a>
                <a href="https://www.linkedin.com/company/aiddata" target="_blank" rel="noopener noreferrer" aria-label="Connect with us on LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </a>
                <a href="https://github.com/aiddata" target="_blank" rel="noopener noreferrer" aria-label="View our code on GitHub">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                </a>
                <a href="https://www.instagram.com/aiddata?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Instagram">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
            </div>
            <button onclick="window.open('https://www.aiddata.org/newsletter', '_blank')" class="newsletter-button" aria-label="Get our Newsletter" style="display: block; background-color: #004E38; color: white; padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; margin-top: 20px; font-weight: 600; transition: all 0.3s ease; font-family: inherit; font-size: 13px; width: fit-content; letter-spacing: 0.3px; text-transform: uppercase;">
                <span>Subscribe to Newsletter</span>
            </button>
        </div>

        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="https://www.aiddata.org/about" target="_blank">About Us</a></li>
                <li><a href="https://www.aiddata.org/data" target="_blank">Data</a></li>
                <li><a href="https://www.aiddata.org/publications" target="_blank">Publications</a></li>
                <li><a href="https://www.aiddata.org/blog" target="_blank">Blog</a></li>
                <li><a href="https://www.aiddata.org/careers" target="_blank">Careers</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Resources</h4>
            <ul>
                <li><a href="https://www.aiddata.org/methods" target="_blank">Methods</a></li>
                <li><a href="https://www.aiddata.org/datasets" target="_blank">Datasets</a></li>
                <li><a href="https://www.aiddata.org/geoquery" target="_blank">GeoQuery</a></li>
                <li><a href="https://www.aiddata.org/china-research-lab" target="_blank">China Research Lab</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contact</h4>
            <address>
                If you're looking to partner up for online, hybrid or in-person trainings, please reach out to<br>
                <a href="mailto:training@aiddata.org" style="color: #ffffff;">training@aiddata.org</a>
            </address>
        </div>
    </div>
  
    <hr style="border: 0; height: 1px; background-color: white; width: 100%; margin: 20px 0;">
  
    <div class="footer-bottom" style="background: transparent;">
        <div class="footer-bottom-content">
            <a href="https://www.wm.edu" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo" class="footer-bottom-logo" style="max-height: 60px;">
            </a>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script>
    // Header menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.querySelector('.menu-button');
        const profileDropdown = document.querySelector('.profile-dropdown');

        if (menuButton && profileDropdown) {
            menuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            document.addEventListener('click', function(e) {
                if (!menuButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });
        }

        // Loading Screen
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            setTimeout(function() {
                loadingScreen.style.opacity = '0';
                setTimeout(function() {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 1000);
        }

        // Curriculum accordion functionality - supports both class names
        const curriculumHeaders = document.querySelectorAll('.curriculum-header, .step-header');

        curriculumHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const item = this.parentElement;
                const isActive = item.classList.contains('active');

                // Close all items
                document.querySelectorAll('.curriculum-item, .tutorial-step').forEach(i => {
                    i.classList.remove('active');
                });

                // Open clicked item if it wasn't active
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });

    });
</script>

<?php
// Footer already included above, no need for get_footer()
wp_footer(); // Required for WordPress to inject necessary scripts
?>