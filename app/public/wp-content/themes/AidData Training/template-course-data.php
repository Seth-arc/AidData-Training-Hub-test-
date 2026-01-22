<?php
/**
 * Template Name: LifterLMS Course Template Data
 * Template Post Type: course, llms_course, page
 *
 * A custom template for LifterLMS course pages in the AidData Training Hub
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

get_header();

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');

// Enqueue loading screen styles
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');

// Inline CSS for course template
?>
<style>
    /* Import Inter font from Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    /* Custom Scrollbar Styling */
    /* For WebKit browsers (Chrome, Safari, Edge) */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #026447;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #015336;
    }
    
    /* For Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #026447 #f1f1f1;
    }
    
    /* Force proper box sizing for all elements */
    *, *:before, *:after {
        box-sizing: border-box;
        margin-bottom: 0; /* Prevent any bottom margins from causing whitespace */
    }
    
    /* Set font family globally */
    * {
        font-family: 'Inter', sans-serif;
    }
    
    /* Top-level elements */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        overflow-x: hidden; /* Prevent horizontal scrolling */
        font-family: 'Inter', sans-serif;
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Use viewport height to ensure it takes full height */
    }
    
    /* Control overflow for all major containers */
    .wp-site-blocks, 
    .course-body,
    .site-footer,
    .footer-content {
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .course-content,
    .course-header {
        max-width: 100%;
        overflow-x: hidden;
    }
    
    /* Make main content area flexible to push footer down */
    .wp-site-blocks {
        flex: 1 0 auto; /* Grow to fill available space */
        display: flex;
        flex-direction: column;
        min-height: auto; /* Allow content to determine height */
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
    
    /* Remove any top margins from theme header */
    .site-header, #masthead, header.wp-block-template-part {
        margin-top: 0 !important;
        padding-top: 0 !important;
        display: none; /* Hide the theme's default header */
    }
    
    /* Make our custom header flush with top */
    .lms-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        margin: 0;
        padding: 0;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        height: 70px; /* Set explicit height */
        width: 100%;
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        max-width: 1200px;
        margin: 0 auto;
        height: 100%; /* Fill header height */
    }
    
    /* Course header and hero */
    main.wp-site-blocks {
        padding-top: 0;
        margin-top: 0;
        overflow-x: hidden;
        width: 100%;
        position: relative;
        top: 0;
    }
    
    /* Make hero image flush with header */
    .course-hero {
        margin-top: 70px; /* Same as header height */
    }
    
    .course-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 0 40px;
        overflow-x: visible;
    }
    
    .course-header {
        margin-bottom: 40px;
        margin-top: 0;
    }
    
    .course-title {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: #333;
        font-family: 'Inter', sans-serif;
    }
    
    /* Hero image styles */
    .course-hero {
        margin: 0 0 30px; 
        position: relative;
        height: 400px;
        overflow: hidden;
        background-color: #f5f5f5;
        width: 100vw;
        box-sizing: border-box;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        border-bottom: 4px solid #026447;
    }
    
    /* WordPress admin bar adjustment to fix hero positioning */
    .admin-bar .course-hero {
        margin-top: 102px; /* 70px for header + 32px for admin bar */
    }
    
    @media screen and (max-width: 782px) {
        .admin-bar .course-hero {
            margin-top: 116px; /* 70px for header + 46px for admin bar on mobile */
        }
    }
    
    .course-video {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .course-video iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .hero-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    .hero-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #2980b9, #6dd5fa);
    }
    
    .course-hero-content {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 30px;
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.3), rgba(0,0,0,0.5));
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        max-width: 1200px;
        margin: 0 auto;
        left: 0;
        right: 0;
    }
    
    .course-hero-title {
        font-size: 3rem;
        margin-bottom: 15px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        color: white;
        max-width: 80%;
        font-weight: 400;
        font-family: 'Inter', sans-serif;
    }
    
    .course-hero-description {
        max-width: 800px;
        font-size: 1.2rem;
        line-height: 1.5;
        margin-bottom: 0;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        text-align: center;
        font-family: 'Inter', sans-serif;
    }
    
    .course-hero-description p {
        margin: 0;
        font-family: 'Inter', sans-serif;
    }
    
    @media (max-width: 992px) {
        .course-hero {
            height: 350px;
            width: 100%;
        }
        
        .course-hero-title {
            font-size: 2.6rem;
            max-width: 90%;
        }
        
        .course-hero-description {
        font-size: 1.1rem;
            max-width: 90%;
        }
    }
    
    @media (max-width: 768px) {
        .course-hero {
            height: 300px;
            width: 100%;
        }
        
        .course-hero-title {
            font-size: 2.2rem;
            max-width: 95%;
        }
        
        .course-hero-description {
            font-size: 1rem;
            max-width: 95%;
        }
    }
    
    @media (max-width: 576px) {
        .course-hero {
            height: 250px;
            width: 100%;
        }
        
        .course-hero-content {
            padding: 20px;
        }
        
        .course-hero-title {
            font-size: 1.8rem;
            margin-bottom: 10px;
            max-width: 100%;
        }
        
        .course-hero-description {
            max-width: 100%;
        }
    }
    
    /* Course meta adjustments */
    .course-meta {
        display: block;
        margin-bottom: 40px;
    }
    
    .course-details {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding-bottom: 20px;
    }
    
    .course-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 0;
        margin-bottom: 25px;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .course-info-item {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .info-label {
        font-size: 0.9rem;
        color: #777;
        font-weight: 500;
    }
    
    .info-value {
        font-size: 1.1rem;
        color: #333;
        font-weight: 600;
    }
    
    .course-enrollment {
        margin-top: 30px;
        margin-bottom: 30px;
        padding: 25px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #58cc02;
        text-align: center;
    }
    
    .tab-focus-button {
        display: inline-block;
        padding: 14px 30px;
        background-color: #58cc02;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .tab-focus-button:hover {
        background-color: #4caf02;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .enrolled-status {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .enrolled-status span {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    
    .continue-button {
        display: inline-block;
        padding: 12px 24px;
        background-color: #58cc02;
        color: white;
        font-weight: 600;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s ease;
        margin-top: 10px;
    }
    
    .continue-button:hover {
        background-color: #4caf02;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .course-progress {
        margin-top: 15px;
        width: 100%;
    }
    
    .progress-bar {
        height: 10px;
        background-color: #e0e0e0;
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    
    .progress-complete {
        height: 100%;
        background-color: #58cc02;
        border-radius: 5px;
        transition: width 0.3s ease;
    }
    
    .progress-text {
        display: block;
        margin-bottom: 15px;
        font-weight: 500;
        color: #555;
    }
    
    /* Course navigation improvements */
    .course-navigation {
        margin-top: 20px;
    }
    
    .course-tabs {
        display: flex;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 0;
        overflow-x: auto;
        padding-bottom: 2px;
        width: 100%;
    }
    
    .tab-button {
        padding: 12px 20px;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        color: #777;
        cursor: pointer;
        position: relative;
        white-space: nowrap;
        transition: color 0.3s ease;
        font-family: 'Inter', sans-serif;
    }
    
    .tab-button:hover {
        color: #115740;
    }
    
    .tab-button:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: transparent;
        transition: background-color 0.3s ease;
    }
    
    .tab-button.active {
        color: white;
        background-color: #026447;
        border-radius: 6px 6px 0 0;
    }
    
    .tab-button.active:after {
        background-color: #026447;
    }
    
    /* Tab content with transparent background */
    .tab-content {
        display: none;
        animation: fadeIn 0.5s ease;
        padding: 30px 10px;
        background-color: transparent;
        min-height: 500px;
        overflow-y: auto;
    }
    
    .tab-contents {
        margin-top: 0;
        border: none;
        padding: 0;
        background-color: transparent;
    }
    
    /* Enhance content readability with transparent background */
    .tab-content h2, 
    .tab-content h3, 
    .tab-content h4 {
        color: #333;
        margin-top: 1.5em;
        margin-bottom: 0.75em;
        font-family: 'Inter', sans-serif;
    }
    
    .tab-content p, 
    .tab-content ul, 
    .tab-content ol {
        color: #555;
        line-height: 1.6;
        margin-bottom: 1em;
        font-family: 'Inter', sans-serif;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Curriculum */
    .course-curriculum {
        margin-top: 20px;
    }
    
    .llms-syllabus-wrapper {
        margin: 0;
        padding: 0;
    }
    
    .llms-section-title {
        font-size: 1.3rem;
        color: #333;
        margin: 30px 0 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
        font-family: 'Inter', sans-serif;
    }
    
    .llms-lesson-preview {
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 6px;
        background-color: rgba(248, 249, 250, 0.7);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .llms-lesson-preview:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: rgba(248, 249, 250, 0.9);
    }
    
    .llms-lesson-link {
        text-decoration: none;
        color: inherit;
    }
    
    .llms-lesson-title {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 5px;
        font-family: 'Inter', sans-serif;
    }
    
    .llms-lesson-counter {
        font-size: 0.9rem;
        color: #777;
    }
    
    /* Instructor Tab */
    .instructor-profile {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 20px;
        background-color: transparent;
        padding: 20px;
        border-radius: 8px;
        border: none;
    }
    
    @media (min-width: 768px) {
        .instructor-profile {
            flex-direction: row;
        }
    }
    
    .instructor-avatar {
        flex: 0 0 150px;
    }
    
    .instructor-avatar img {
        width: 100%;
        border-radius: 50%;
    }
    
    .instructor-info {
        flex: 1;
    }
    
    .instructor-name {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #333;
    }
    
    .instructor-bio {
        color: #555;
        line-height: 1.6;
    }
    
    /* Enroll Tab */
    .enroll-section {
        padding: 30px;
        background-color: transparent;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .enroll-section h3 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }
    
    .enroll-section p {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 20px;
        color: #555;
    }
    
    .enroll-section .course-pricing {
        margin-top: 20px;
    }
    
    .enroll-section .price {
        font-size: 24px;
        font-weight: bold;
        color: #58cc02;
    }
    
    .enroll-section h4 {
        font-size: 20px;
        margin-bottom: 15px;
        color: #333;
    }
    
    .enroll-section .enroll-button {
        display: inline-block;
        background-color: #58cc02;
        color: #fff;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        margin-top: 10px;
        transition: background-color 0.3s, transform 0.2s;
    }
    
    .enroll-section .enroll-button:hover {
        background-color: #4caf02;
        transform: translateY(-2px);
    }
    
    .enroll-section .course-progress {
        margin-top: 20px;
    }
    
    .enroll-section .progress-bar {
        width: 100%;
        height: 10px;
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        margin: 15px 0;
        overflow: hidden;
    }
    
    .enroll-section .progress-complete {
        height: 100%;
        background-color: #58cc02;
        border-radius: 5px;
    }
    
    .enroll-section .progress-text {
        font-size: 14px;
        color: #58cc02;
        font-weight: bold;
    }
    
    .enroll-section .enrolled-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    
    .enroll-section .dashboard-button {
        padding: 10px 20px;
        background-color: rgba(0, 0, 0, 0.1);
        color: #333;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    
    .enroll-section .continue-button {
        padding: 10px 20px;
        background-color: #58cc02;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    
    .enroll-section .dashboard-button:hover {
        background-color: rgba(0, 0, 0, 0.2);
    }
    
    .enroll-section .continue-button:hover {
        background-color: #4caf02;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .course-title {
            font-size: 2rem;
        }
        
        .course-info-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
        
        .course-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
        }
    }

    /* Header Styles from front-page.php */
    .logo-section img {
        height: 40px;
        width: auto;
    }

    .header-actions {
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
        cursor: pointer;
        color: #333;
        margin-left: 1rem;
        position: relative;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
    }

    .header-button svg {
        width: 24px;
        height: 24px;
    }

    .signup-button {
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 0.5rem 1rem;
        margin-left: 1rem;
        font-weight: 500;
    }

    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 8px;
        height: 8px;
        font-size: 10px;
        display: none;
    }

    .notification-badge.active {
        display: block;
    }

    .profile-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 220px;
        padding: 1rem 0;
        z-index: 1000;
        display: none;
    }

    .dropdown-header {
        padding: 0 1rem 0.5rem;
        border-bottom: 1px solid #eee;
        margin-bottom: 0.5rem;
    }

    .dropdown-user-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .user-name {
        font-weight: 600;
        color: #333;
    }

    .user-email {
        font-size: 0.9rem;
        color: #666;
    }

    .dropdown-item {
        display: block;
        padding: 0.75rem 1rem;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #2980b9;
    }

    .logout-button {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        margin-top: 0.5rem;
        color: #e74c3c;
    }
    
    /* Footer Styles */
    .site-footer {
        background-color: #115740;
        color: white;
        padding: 3rem 1rem 0;
        flex-shrink: 0; /* Prevent footer from shrinking */
        margin-top: 0; /* Remove margin since we're using flex */
        margin-bottom: 0; /* Ensure no margin at the bottom */
        width: 100%;
        border-bottom: 0;
    }
    
    /* Hide any default WordPress footers */
    .wp-site-footer, .site-footer-container, footer.wp-block-template-part {
        display: none !important;
    }
    
    .footer-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    @media (min-width: 768px) {
        .footer-content {
            grid-template-columns: 2fr 1fr 1fr 1fr;
        }
    }
    
    .footer-section h4 {
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        text-transform: uppercase;
        color: #d2bb93;
    }
    
    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-section ul li {
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif;
    }
    
    .footer-section a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: color 0.2s;
        font-family: 'Inter', sans-serif;
    }
    
    .footer-section a:hover {
        color: white;
        text-decoration: underline;
    }
    
    .footer-logo {
        height: 40px;
        margin-bottom: 1rem;
    }

    .footer-logo p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0 0 1.5rem;
    font-family: 'Inter', sans-serif;
    }
    
    .social-links {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .social-links a {
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        transition: background-color 0.2s;
    }
    
    .social-links a:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .social-links svg {
        width: 20px;
        height: 20px;
    }
    
    .newsletter-button {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        color: white;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    
    .newsletter-button:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .footer-bottom {
        margin-top: 2rem;
        text-align: center;
        margin-bottom: 0;
        padding-bottom: 1rem;
    }
    
    .footer-bottom-content {
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Inter', sans-serif;
    }
    
    address {
        font-style: normal;
        line-height: 1.6;
        font-family: 'Inter', sans-serif;
    }

    /* Make sure images and media don't cause overflow */
    img, video, iframe, embed, object {
        max-width: 100%;
        height: auto;
    }
    
    /* Make tables responsive */
    table {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        display: block;
    }
    
    /* Ensure all content sections stay within bounds */
    .course-meta, 
    .course-details,
    .course-info-grid,
    .course-enrollment,
    .course-tabs,
    .tab-content {
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Force proper formatting on lessons and curriculum */
    .llms-syllabus-wrapper,
    .llms-lesson-preview,
    .instructor-profile,
    .course-video,
    .course-featured-image,
    .course-progress,
    .course-pricing {
        max-width: 100%;
        word-wrap: break-word;
    }
    
    /* Ensure progress bar stays contained */
    .progress-bar, 
    .progress-complete {
        max-width: 100%;
    }
    
    /* Ensure no fixed widths that might cause overflow */
    .wp-block-group.alignwide {
        width: 100%;
        max-width: 1200px; /* Max width with padding accounted for */
        margin-left: auto;
        margin-right: auto;
        padding-left: 0;
        padding-right: 0;
    }

    /* Loading screen adjustment to work with hero */
    .loading-screen {
        z-index: 2000;
    }

    .course-description {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #555;
        margin-bottom: 15px;
    }

    /* Course details in overview tab */
    .course-details-summary {
        margin-bottom: 30px;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background-color: rgba(248, 249, 250, 0.6);
    }
    
    .course-details-summary h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1.3rem;
        color: #333;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding-bottom: 10px;
    }
    
    .course-info-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-around;
    }
    
    .info-summary-item {
        flex: 0 1 auto;
        min-width: 180px;
        display: flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.5);
        padding: 10px 15px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .info-summary-item svg {
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .summary-label {
        font-weight: 600;
        margin-right: 8px;
        color: #555;
    }
    
    .summary-value {
        color: #333;
        font-weight: 500;
    }
    
    @media (max-width: 576px) {
        .info-summary-item {
            flex: 1 0 100%;
        }
    }

    .course-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .course-badge {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        font-family: 'Inter', sans-serif;
    }
    
    @media (max-width: 576px) {
        .course-badges {
            gap: 6px;
            margin-bottom: 15px;
        }
        
        .course-badge {
            padding: 3px 6px;
            font-size: 0.7rem;
        }
    }

    /* Adding primary color variable */
    :root {
        --primary-color: #fff;
    }
    
    .why-course-button {
        margin: 2rem auto 0;
        padding: 0.75rem 2rem;
        background: transparent;
        border: 1.5px solid var(--primary-color);
        color: var(--primary-color);
        font-size: 0.85rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-radius: 3px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 160px;
        box-shadow: none;
        outline: none;
        font-family: 'Inter', sans-serif;
    }
    
    .why-course-button:hover {
        background-color: rgba(255, 255, 255, 0.15);
        transform: none;
        box-shadow: none;
    }
    
    .why-course-button:active {
        transform: none;
        box-shadow: none;
    }
    
    .why-course-button svg {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-top: 0;
    }
    
    .why-course-button:hover svg {
        transform: translateY(2px);
    }
    
    /* Info drawer styles */
    .info-drawer {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #fff;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        overflow-y: auto;
        font-family: 'Inter', sans-serif;
    }
    
    /* Ensure all elements in the info drawer use Inter font */
    .info-drawer *,
    .drawer-content *,
    .drawer-header *,
    .drawer-grid *,
    .drawer-left *,
    .drawer-right *,
    .info-block *,
    .contact-section *,
    .contact-details *,
    .related-news *,
    .news-items *,
    .news-content *,
    .scenario-examples *,
    .scenario-card * {
        font-family: 'Inter', sans-serif;
    }
    
    .info-drawer.active {
        opacity: 1;
        pointer-events: all;
    }
    
    .drawer-content {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        padding: 40px 20px;
        color: #333;
        position: relative;
    }
    
    .drawer-logo {
        height: 40px;
        width: auto;
        margin-right: 20px;
    }
    
    .drawer-header {
        display: flex;
        align-items: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        position: relative;
        justify-content: center;
    }
    
    .drawer-logo {
        position: absolute;
        left: 0;
    }
    
    .drawer-heading {
        font-size: 28px;
        margin: 0;
        color: #333;
        font-family: 'Inter', sans-serif;
        text-align: center;
    }
    
    .drawer-grid {
        display: grid;
        grid-template-columns: 1fr 1.8fr;
        gap: 30px;
    }
    
    .drawer-left {
        padding-right: 0;
        min-width: 320px;
    }
    
    .close-drawer {
        background: transparent;
        border: none;
        color: #333;
        cursor: pointer;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
        position: absolute;
        top: 20px;
        right: 20px;
    }
    
    .close-drawer:hover {
        transform: scale(1.1);
    }
    
    /* Contact section styling */
    .contact-section {
        margin-bottom: 40px;
        background-color: #ffffff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }
    
    .contact-section h4 {
        color: #026447;
        margin-top: 0;
        font-size: 20px;
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 10px;
    }
    
    .contact-person {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    
    .contact-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f0f0f0;
    }
    
    .contact-details p {
        margin: 0 0 10px;
        font-size: 14px;
        line-height: 1.5;
        color: #555;
        font-family: 'Inter', sans-serif;
    }
    
    .contact-details a {
        color: #026447;
        text-decoration: none;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
    }
    
    .contact-details a:hover {
        text-decoration: underline;
    }
    
    /* Related news styling */
    .related-news {
        margin-bottom: 30px;
        background-color: #ffffff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }
    
    .related-news h4 {
        color: #026447;
        font-size: 20px;
        margin-top: 0;
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 10px;
    }
    
    .news-items {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .news-item {
        display: flex;
        flex-direction: column;
        background-color: #f9f9f9;
        border-radius: 10px;
        overflow: hidden;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    .news-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-color: rgba(2, 100, 71, 0.1);
    }
    
    .news-item img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    .news-content {
        padding: 15px;
    }
    
    .news-content h5 {
        margin: 0 0 8px;
        font-size: 15px;
        font-weight: 600;
        color: #333;
        font-family: 'Inter', sans-serif;
        line-height: 1.4;
    }
    
    .news-content p {
        margin: 0 0 8px;
        font-size: 13px;
        color: #666;
        line-height: 1.5;
        font-family: 'Inter', sans-serif;
    }
    
    .news-date {
        display: block;
        font-size: 12px;
        color: #026447;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        margin-top: 5px;
    }
    
    /* Info block styling */
    .info-block {
        background-color: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    
    .info-block h2 {
        color: #026447;
        font-size: 28px;
        margin-top: 0;
        margin-bottom: 20px;
        line-height: 1.3;
        font-family: 'Inter', sans-serif;
    }
    
    .info-block p {
        color: #555;
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 25px;
        font-family: 'Inter', sans-serif;
    }
    
    .info-block h3 {
        color: #026447;
        font-size: 22px;
        margin-top: 30px;
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
    }
    
    /* Scenario examples styling */
    .scenario-examples {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 30px 0;
    }
    
    .scenario-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(2, 100, 71, 0.1);
        transition: all 0.3s ease;
    }
    
    .scenario-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(2, 100, 71, 0.15);
    }
    
    .scenario-image {
        height: 180px;
        border-radius: 8px;
        margin-bottom: 15px;
        overflow: hidden;
    }
    
    .scenario-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .scenario-card:hover .scenario-image img {
        transform: scale(1.05);
    }
    
    .scenario-card h4 {
        color: #026447;
        font-size: 18px;
        margin-top: 0;
        margin-bottom: 10px;
        font-family: 'Inter', sans-serif;
    }
    
    .scenario-card p {
        font-size: 14px;
        color: #555;
        line-height: 1.5;
        margin: 0;
        font-family: 'Inter', sans-serif;
    }
    
    .drawer-footer {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        text-align: left;
    }
    
    .drawer-footer h3 {
        font-size: 20px;
        color: #026447;
        font-weight: 600;
        margin: 0 0 20px 0;
        font-family: 'Inter', sans-serif;
    }
    
    .drawer-footer h4 {
        font-size: 16px;
        color: #333;
        font-weight: 600;
        margin: 15px 0 5px 0;
        font-family: 'Inter', sans-serif;
    }
    
    .drawer-footer p {
        font-size: 14px;
        color: #555;
        font-weight: 400;
        line-height: 1.5;
        margin: 0 0 15px 0;
        font-family: 'Inter', sans-serif;
    }
    
    .drawer-footer section {
        margin-bottom: 25px;
    }
    
    /* Media queries */
    @media (max-width: 768px) {
        .drawer-content {
            padding: 30px 20px 50px;
        }
        
        .drawer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .scenario-examples {
            grid-template-columns: 1fr;
        }
        
        .close-drawer {
            top: 15px;
            right: 15px;
        }
        
        .drawer-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .drawer-logo {
            margin-right: 0;
        }
        
        .drawer-heading {
            font-size: 22px;
        }
        
        .info-block h2 {
            font-size: 24px;
        }
        
        .info-block h3 {
            font-size: 20px;
        }
    }
</style>
<!-- Loading Screen -->
<div class="loading-screen">
    <div class="loading-content">
        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="loading-logo">
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
        </div>
        <p class="loading-text">Loading Course</p>
    </div>
</div>
<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="https://www.aiddata.org" target="_blank">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
            </a>
        </div>
        
        <div class="header-actions">
            <div class="auth-only" style="display: none;">
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
                                <span class="user-name">Your Name</span>
                                <span class="user-email">your.email@example.com</span>
                            </div>
                        </div>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="dropdown-item">Home</a>
                        <a href="<?php echo esc_url(home_url('/lp-profile/')); ?>" class="dropdown-item">My Account</a>
                        <button class="dropdown-item logout-button">Sign Out</button>
                    </div>
                </div>
            </div>
            <div class="guest-only" style="display: flex;">
                <button class="header-button login-button">Log In</button>
                <button class="header-button signup-button">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<main class="wp-site-blocks is-layout-flow" style="padding-top: 0;">
    <div class="wp-block-group alignwide is-layout-constrained">
        <section class="course-content wp-block-template-part">
        <div class="course-header">
            <?php if ( is_singular( 'course' ) || is_singular( 'llms_course' ) ) : ?>
                <?php if ( function_exists( 'llms_get_post' ) ) : 
                    $course = llms_get_post( get_the_ID() );
                ?>
                        <div class="course-hero">
                            <?php 
                            $video_embed = get_post_meta(get_the_ID(), '_llms_video_embed', true);
                            if (!empty($video_embed)) : ?>
                            <div class="course-video">
                                    <?php echo wp_oembed_get($video_embed); ?>
                            </div>
                                        <?php elseif ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'full', array('class' => 'hero-image') ); ?>
                    <?php else: ?>
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_analysis.png" alt="Data Analysis" class="hero-image">
                <?php endif; ?>
                        
                            <div class="course-hero-content">
                                <h1 class="course-hero-title">Critical Data Analysis and Visualization</h1>
                                <div class="course-badges">
                                    <span class="course-badge">Course</span>
                                    <span class="course-badge">Digital Badge</span>
                                    <span class="course-badge">Multimodal</span>
                                </div>
                                <div style="display: flex; gap: 30px; justify-content: center; margin: 15px 0 25px; color: rgba(255, 255, 255, 0.85);">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="min-width: 16px;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <span style="font-size: 14px; font-weight: 400;">12 - 16 hours</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="min-width: 16px;">
                                            <path d="M2 20h.01M7 20v-4"></path>
                                            <path d="M12 20v-8"></path>
                                            <path d="M17 20v-12"></path>
                                            <path d="M22 20V8"></path>
                                        </svg>
                                        <span style="font-size: 14px; font-weight: 400;">Intermediate</span>
                                    </div>
                                </div>
                                <button class="why-course-button" id="why-course-button">Why this course? <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg></button>
                                <div class="info-drawer" id="info-drawer">
                                    <div class="drawer-content">
                                        <button class="close-drawer" id="drawer-close" aria-label="Close drawer">
                                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 6L6 18M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        <div class="drawer-header">
                                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="drawer-logo">
                                            <h2 class="drawer-heading">Training and Professional Development</h2>
                                </div>
                                        <div class="drawer-grid">
                                            <div class="drawer-left">
                                                <div class="contact-section" style="background: #f9f9f9; border-radius: 12px; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08); padding: 25px; margin-bottom: 30px;">
                                                    <h4 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.05); padding-bottom: 10px;">Contact Information</h4>
                                                    <div class="contact-person" style="display: flex; align-items: center; gap: 20px;">
                                                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/sethu_nguna.png" alt="Sethu Nguna" class="contact-avatar" style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);">
                                                        <div class="contact-details">
                                                            <p style="margin: 0 0 8px; font-size: 12px; color: #666; line-height: 1.4;">Questions about our learning approach? Want to discuss custom training solutions?</p>
                                                            <p style="margin: 0; font-size: 13px; line-height: 1.5;">
                                                                <strong style="color: #333; font-size: 14px; display: block; margin-bottom: 3px;">Sethu Nguna</strong>
                                                                <span style="color: #555; display: block; margin-bottom: 3px; font-size: 12px;">Manager, Training & Instructional Design</span>
                                                                <a href="mailto:snguna@aiddata.org" style="color: #026447; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 12px;">snguna@aiddata.org</a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="related-news" style="background: #f9f9f9; border-radius: 12px; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08); padding: 25px;">
                                                    <h4 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.05); padding-bottom: 10px;">Related News</h4>
                                                    <div class="news-items">
                                                        <a href="https://www.aiddata.org/blog/hands-on-training-empowers-journalists-in-pakistan-for-data-driven-reporting" class="news-item" target="_blank" style="display: block; margin-bottom: 20px; background: white; border-radius: 8px; overflow: hidden; text-decoration: none; transition: all 0.3s ease;">
                                                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_journalism_pakistan.jpg" alt="Trainers and organizing staff huddle before student-journalists' capstone project presentations" style="width: 100%; height: 160px; object-fit: cover;">
                                                            <div class="news-content" style="padding: 15px;">
                                                                <h5 style="margin: 0 0 8px 0; font-size: 16px; color: #026447; line-height: 1.4;">Hands-on training empowers journalists in Pakistan for data-driven reporting</h5>
                                                                <p style="margin: 0 0 8px 0; font-size: 14px; color: #555; line-height: 1.5;">The AidData-led training responds to the media's need for better data and evidence to understand how foreign lending and government economic policies are impacting citizens.</p>
                                                                <span class="news-date" style="display: block; font-size: 12px; color: #666;">January 30, 2023</span>
                                                            </div>
                                                        </a>
                                                        <a href="https://www.aiddata.org/blog/aiddata-and-rappler-data-journalism-course-explores-chinas-influence-on-global-development" class="news-item" target="_blank" style="display: block; background: white; border-radius: 8px; overflow: hidden; text-decoration: none; transition: all 0.3s ease;">
                                                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_journalism_bootcamp.jpg" alt="Attendees of an AidData and Rappler data journalism bootcamp work during a session" style="width: 100%; height: 160px; object-fit: cover;">
                                                            <div class="news-content" style="padding: 15px;">
                                                                <h5 style="margin: 0 0 8px 0; font-size: 16px; color: #026447; line-height: 1.4;">AidData and Rappler data journalism course explores China's influence on global development</h5>
                                                                <p style="margin: 0 0 8px 0; font-size: 14px; color: #555; line-height: 1.5;">The training equipped practicing journalists with critical tools for analyzing economic and financial data to uncover trends in foreign funding to the Philippines.</p>
                                                                <span class="news-date" style="display: block; font-size: 12px; color: #666;">February 6, 2025</span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="drawer-right">
                                                <div class="info-block" style="padding: 25px;">
                                                    <h2 style="font-size: 20px; color: #026447; font-weight: 600; margin: 0 0 20px 0; font-family: 'Inter', sans-serif; text-align: left;">Mastering Data Analysis for Development Finance</h2>
                                                    
                                                    <p style="font-size: 14px;">In an era where data drives decision-making, the ability to analyze and visualize development finance data effectively is crucial. This course equips you with advanced skills to transform complex datasets into clear, actionable insights.</p>

                                                    <div class="scenario-examples" style="margin-bottom: 20px;">
                                                        <div class="scenario-card">
                                                            <div class="scenario-image" style="height: 200px; border-radius: 12px; margin-bottom: 16px; position: relative; overflow: hidden;">
                                                                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_analysis_techniques.png" alt="Data Analysis in Practice" style="width: 100%; height: 100%; object-fit: cover;">
                                                                <!-- Overlay gradient -->
                                                                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                                                            </div>
                                                            <h4 style="color: #026447; font-size: 18px; margin-bottom: 12px;">Advanced Analysis Techniques</h4>
                                                            <p style="font-size: 14px; color: #555; line-height: 1.5;">Master cutting-edge data analysis methods specifically tailored for development finance datasets. Learn to identify patterns, trends, and insights that drive informed decision-making.</p>
                                                        </div>
                                                        <div class="scenario-card">
                                                            <div class="scenario-image" style="height: 200px; border-radius: 12px; margin-bottom: 16px; position: relative; overflow: hidden;">
                                                                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_visualization.png" alt="Data Visualization Examples" style="width: 100%; height: 100%; object-fit: cover;">
                                                                <!-- Overlay gradient -->
                                                                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                                                            </div>
                                                            <h4 style="color: #026447; font-size: 18px; margin-bottom: 12px;">Impactful Visualizations</h4>
                                                            <p style="font-size: 14px; color: #555; line-height: 1.5;">Create compelling visual narratives that effectively communicate complex financial data to diverse audiences, from policymakers to the public.</p>
                                                        </div>
                                                    </div>



                                                    <h3 style="font-size: 20px; color: #026447; font-weight: 600; margin: 30px 0 20px 0; font-family: 'Inter', sans-serif;">Course Features</h3>
                                                    <ul class="complexity-challenges" style="list-style: none; padding: 0; margin: 0 0 30px 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                                                        <li class="complexity-challenge" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid rgba(2, 100, 71, 0.1);">
                                                            <strong style="color: #026447; display: block; margin-bottom: 8px; font-size: 16px; font-weight: 500;">Practical Exercises</strong>
                                                            <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0;">Work with real development finance datasets to create professional-grade visualizations and analysis.</p>
                                                        </li>
                                                        <li class="complexity-challenge" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid rgba(2, 100, 71, 0.1);">
                                                            <strong style="color: #026447; display: block; margin-bottom: 8px; font-size: 16px; font-weight: 500;">Expert Guidance</strong>
                                                            <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0;">Learn from experienced data visualization specialists who bring both technical expertise and practical field experience.</p>
                                                        </li>
                                                        <li class="complexity-challenge" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid rgba(2, 100, 71, 0.1);">
                                                            <strong style="color: #026447; display: block; margin-bottom: 8px; font-size: 16px; font-weight: 500;">Interactive Learning</strong>
                                                            <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0;">Engage with hands-on exercises and receive personalized feedback on your visualization projects.</p>
                                                        </li>
                                                        <li class="complexity-challenge" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid rgba(2, 100, 71, 0.1);">
                                                            <strong style="color: #026447; display: block; margin-bottom: 8px; font-size: 16px; font-weight: 500;">Professional Portfolio</strong>
                                                            <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0;">Build a portfolio of data visualization projects that demonstrate your expertise to potential employers or clients.</p>
                                                        </li>
                                                    </ul>

                                                    <div style="margin-top: 30px; padding: 20px; background: rgba(2, 100, 71, 0.05); border-radius: 12px;">
                                                        <h4 style="color: #026447; margin-top: 0; font-size: 18px;">Ready to Start?</h4>
                                                        <p style="margin-bottom: 0; font-size: 14px;">Join data professionals worldwide in mastering the art and science of development finance data visualization. Whether you're an analyst, researcher, or development practitioner, this course will enhance your ability to create impactful data visualizations that drive decision-making.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                <?php if ( has_excerpt() ) : ?>
                                    <div class="course-hero-description">
                        <?php the_excerpt(); ?>
                    </div>
            <?php endif; ?>
                            </div>
        </div>

                        <div class="course-meta">
                            <div class="course-details">
                <div class="course-navigation">
                    <div class="course-tabs">
                        <button class="tab-button active" data-tab="overview">Overview</button>
                        <button class="tab-button" data-tab="curriculum">Curriculum</button>
                        <button class="tab-button" data-tab="instructor">Instructor</button>
                                        <button class="tab-button" data-tab="enroll">Enroll</button>
                    </div>
                    
                                    <div class="tab-contents">
                    <div class="tab-content" id="overview" style="display: block;">
                                            <div style="display: flex; flex-direction: column; gap: 25px; margin-bottom: 30px;">
                                                <!-- Video removed -->
                                                
                                                <!-- Partnership banner -->
                                                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);">
                                                    <div style="display: flex; flex-direction: row; align-items: center; justify-content: flex-start; gap: 20px;">
                                                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_full_horizontal.svg" alt="William & Mary Logo" style="height: 30px; width: auto;">
                                                        <div>
                                                            <span style="font-size: 14px; color: #555; line-height: 1.4; font-family: 'Inter', sans-serif;">Delivered in partnership with the W&M Studio for Teaching and Learning Innovation.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div style="background-color: rgba(248, 249, 250, 0.7); padding: 20px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                                    <h3 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 15px; font-family: 'Inter', sans-serif; text-align: left;">Course Description</h3>
                                                    <p style="font-size: 14px; color: #333; line-height: 1.6; margin-bottom: 15px; font-family: 'Inter', sans-serif;">
                                                        In an era where data drives decision-making, the ability to analyze and visualize development finance data effectively is crucial. This course equips you with advanced skills to transform complex datasets into clear, actionable insights.
                                                    </p>
                                                    <p style="font-size: 14px; color: #333; line-height: 1.6; font-family: 'Inter', sans-serif;">
                                                        Master cutting-edge data analysis methods specifically tailored for development finance datasets. Learn to identify patterns, trends, and insights that drive informed decision-making while creating compelling visual narratives that effectively communicate complex financial data to diverse audiences, from policymakers to the public.
                                                    </p>
                                                </div>


                                                <div>
                                                    <h3 style="color: #026447; font-size: 20px; margin-bottom: 20px; font-family: 'Inter', sans-serif; text-align: left;">Key Learning Outcomes</h3>
                                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                                    <div style="background-color: rgba(248, 249, 250, 0.7); padding: 15px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#026447" stroke-width="2" style="min-width: 22px; margin-top: 3px;">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                            <div>
                                                <h4 style="font-size: 16px; margin-top: 0; margin-bottom: 8px; color: #026447; font-family: 'Inter', sans-serif;">Design Principles</h4>
                                                <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0; font-family: 'Inter', sans-serif;">Master fundamental design principles and their practical application in creating effective data visualizations for development finance.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="background-color: rgba(248, 249, 250, 0.7); padding: 15px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#026447" stroke-width="2" style="min-width: 22px; margin-top: 3px;">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                            <div>
                                                <h4 style="font-size: 16px; margin-top: 0; margin-bottom: 8px; color: #026447; font-family: 'Inter', sans-serif;">Accessibility Standards</h4>
                                                <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0; font-family: 'Inter', sans-serif;">Learn to create inclusive visualizations that effectively communicate to all audiences, regardless of visual abilities or technical background.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="background-color: rgba(248, 249, 250, 0.7); padding: 15px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#026447" stroke-width="2" style="min-width: 22px; margin-top: 3px;">
                                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                            </svg>
                                            <div>
                                                <h4 style="font-size: 16px; margin-top: 0; margin-bottom: 8px; color: #026447; font-family: 'Inter', sans-serif;">Data Translation</h4>
                                                <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0; font-family: 'Inter', sans-serif;">Develop expertise in transforming complex financial data into clear, actionable insights through effective visualization techniques.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="background-color: rgba(248, 249, 250, 0.7); padding: 15px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#026447" stroke-width="2" style="min-width: 22px; margin-top: 3px;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                            </svg>
                                            <div>
                                                <h4 style="font-size: 16px; margin-top: 0; margin-bottom: 8px; color: #026447; font-family: 'Inter', sans-serif;">Analytical Frameworks</h4>
                                                <p style="font-size: 14px; color: #555; line-height: 1.5; margin: 0; font-family: 'Inter', sans-serif;">Apply various analytical perspectives to interpret and present development finance data in meaningful ways.</p>
                                            </div>
                                        </div>
                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="course-overview-content">
                        <?php the_content(); ?>
                        </div>
                    </div>
                    
                    <div class="tab-content" id="curriculum" style="display: none;">
                        <div class="course-curriculum">
                            <div class="course-modules" style="margin-bottom: 40px;">
                                <div class="module-item" style="margin-bottom: 30px; background-color: rgba(248, 249, 250, 0.7); padding: 20px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <h3 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 15px; font-family: 'Inter', sans-serif;">Module 1: Design Foundations</h3>
                                    <p style="color: #555; line-height: 1.6; font-family: 'Inter', sans-serif;">Master fundamental design principles and their application in data visualization for development finance. Learn how to create effective visual hierarchies, utilize space efficiently, and structure information for maximum clarity and impact.</p>
                                </div>
                                
                                <div class="module-item" style="margin-bottom: 30px; background-color: rgba(248, 249, 250, 0.7); padding: 20px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <h3 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 15px; font-family: 'Inter', sans-serif;">Module 2: Color and Accessibility</h3>
                                    <p style="color: #555; line-height: 1.6; font-family: 'Inter', sans-serif;">Learn to create inclusive visualizations with effective color schemes that communicate clearly to all audiences. Explore color theory, contrast principles, and accessibility standards to ensure your visualizations are comprehensible regardless of viewers' visual abilities.</p>
                                </div>
                                
                                <div class="module-item" style="margin-bottom: 30px; background-color: rgba(248, 249, 250, 0.7); padding: 20px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <h3 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 15px; font-family: 'Inter', sans-serif;">Module 3: Data Fluency</h3>
                                    <p style="color: #555; line-height: 1.6; font-family: 'Inter', sans-serif;">Develop expertise in translating complex development finance data into clear, actionable visual insights. Learn advanced techniques for simplifying complex datasets while maintaining analytical integrity and creating visualizations that effectively communicate your findings.</p>
                                </div>
                                
                                <div class="module-item" style="margin-bottom: 30px; background-color: rgba(248, 249, 250, 0.7); padding: 20px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <h3 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 15px; font-family: 'Inter', sans-serif;">Module 4: Analytical Lenses</h3>
                                    <p style="color: #555; line-height: 1.6; font-family: 'Inter', sans-serif;">Apply different analytical perspectives to interpret and present development finance data effectively. Explore various frameworks for understanding and presenting financial data, enabling you to uncover deeper insights and create more meaningful visualizations for diverse stakeholders.</p>
                                </div>
                                
                                <div class="module-item" style="margin-bottom: 30px; background-color: rgba(248, 249, 250, 0.7); padding: 20px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05); border-left: 4px solid #026447;">
                                    <h3 style="color: #026447; font-size: 20px; margin-top: 0; margin-bottom: 15px; font-family: 'Inter', sans-serif; display: flex; align-items: center; gap: 10px;">
                                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#026447" stroke-width="2" style="min-width: 22px;">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                        Capstone Project
                                    </h3>
                                    <p style="color: #555; line-height: 1.6; font-family: 'Inter', sans-serif;">
                                        Apply all the skills and knowledge you've gained throughout the course in a comprehensive capstone project. Working with AidData datasets and tools, you'll analyze funding patterns, create visually appealing data visualizations, and craft a compelling narrative about a significant aspect of global development finance. The project concludes with a reflection on your methodology and learning journey, allowing you to synthesize your experience and articulate your professional growth.
                                    </p>
                                </div>
                            </div>
                            
                            <?php if ( function_exists( 'lifterlms_get_course_syllabus' ) ) : ?>
                                <?php lifterlms_get_course_syllabus(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="tab-content" id="instructor" style="display: none;">
                        
                        <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 15px;">
                            <?php 
                            // Get the primary instructor data if available
                            $instructor_name = '';
                            $instructor_bio = 'No instructor bio available.';
                            $instructor_avatar = '';
                            
                            if (function_exists('llms_get_post')) : 
                                $course = llms_get_post(get_the_ID());
                                $instructors = $course->get_instructors();
                                
                                if (!empty($instructors) && is_array($instructors)) :
                                    $instructor_data = $instructors[0];
                                    $instructor_id = isset($instructor_data['id']) ? $instructor_data['id'] : 0;
                                    $instructor_name = isset($instructor_data['name']) ? $instructor_data['name'] : '';
                                    
                                    if (empty($instructor_name) && $instructor_id) {
                                        $user = get_userdata($instructor_id);
                                        if ($user) {
                                            $instructor_name = $user->display_name;
                                        }
                                    }
                                    
                                    if ($instructor_id) {
                                        $instructor_avatar = get_avatar($instructor_id, 150);
                                        $bio = get_user_meta($instructor_id, 'description', true);
                                        $instructor_bio = !empty($bio) ? wpautop($bio) : 'No instructor bio available.';
                                    }
                                endif;
                            endif;
                            
                            // If instructor name is empty, set a default
                            if (empty($instructor_name)) {
                                $instructor_name = 'Dr. Emma Chen';
                            }
                            
                            // Instructor avatars and info
                            $instructors = [
                                [
                                    'name' => 'John Custer',
                                    'title' => 'Deputy Director, Communications & Data Analytics',
                                    'bio' => 'John specializes in translating complex data into compelling communications. With his dual expertise in data analytics and strategic communications, he helps participants develop the skills to interpret and effectively communicate insights from development finance data.',
                                    'avatar' => '',
                                    'image' => 'john_custer.svg'
                                ]
                            ];
                            
                            foreach ($instructors as $i => $instructor) :
                            ?>
                            <div class="instructor-profile" style="background-color: rgba(248, 249, 250, 0.7); padding: 15px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05); display: flex; flex-direction: column; gap: 0;">
                                <div style="display: flex; flex-direction: row; gap: 15px; align-items: flex-start;">
                                    <div class="instructor-avatar" style="flex: 0 0 100px;">
                                        <?php if (!empty($instructor['avatar'])) : ?>
                                            <?php echo $instructor['avatar']; ?>
                                        <?php else : ?>
                                            <?php
                                            // Use John Custer's image
                                            $image_filename = 'john_custer.svg';
                                            echo '<img src="' . esc_url(get_template_directory_uri()) . '/assets/images/' . $image_filename . '" alt="' . esc_attr($instructor['name']) . '" style="width:100px;height:100px;object-fit:cover;border-radius:50%;">';
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="instructor-info" style="flex: 1;">
                                        <h3 class="instructor-name" style="font-size: 18px; color: #026447; margin-top: 0; margin-bottom: 3px; font-family: 'Inter', sans-serif;"><?php echo esc_html($instructor['name']); ?></h3>
                                        <h4 style="font-size: 14px; color: #666; margin-top: 0; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 500;"><?php echo esc_html($instructor['title']); ?></h4>
                                        <div class="instructor-bio" style="color: #555; line-height: 1.4; font-size: 14px; font-family: 'Inter', sans-serif;">
                                            <?php 
                                            echo $instructor['bio'];
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                                        <div class="tab-content" id="enroll" style="display: none; background-color: transparent; border-radius: 8px; padding: 20px; border: none;">
                                            <div style="display: flex; flex-direction: column; gap: 25px; max-width: 600px; margin: 0 auto;">
                                                
                                                <!-- Pricing card -->
                                                <div style="background-color: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); overflow: hidden; border: 1px solid rgba(0, 0, 0, 0.04);">
                                                    <!-- Pricing header -->
                                                    <div style="background-color: #026447; padding: 25px; color: white; text-align: center;">
                                                                                                                 <!-- Course type heading removed -->
                                                        <div style="display: flex; justify-content: center; align-items: baseline; gap: 5px;">
                                                            <span style="font-size: 28px; font-weight: 700; line-height: 1;">$1150</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Course features -->
                                                    <div style="padding: 30px;">
                                                        <h4 style="margin: 0 0 20px 0; font-size: 18px; color: #333; font-family: 'Inter', sans-serif; font-weight: 600; border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding-bottom: 15px;">What's Included</h4>
                                                        
                                                        <ul style="list-style-type: none; padding: 0; margin: 0 0 25px 0; font-family: 'Inter', sans-serif;">
                                                            <li style="padding: 12px 0; display: flex; align-items: center; color: #444; font-size: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.04);">
                                                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#026447" stroke-width="2" style="margin-right: 15px; min-width: 20px;">
                                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                                </svg>
                                                                <span style="display: flex; flex-direction: column;">
                                                                    <strong style="font-weight: 600;">Full course access for 6 months</strong>
                                                                    <span style="font-size: 13px; color: #666; margin-top: 2px;">Complete the course at your own pace</span>
                                                                </span>
                                                            </li>
                                                            <li style="padding: 12px 0; display: flex; align-items: center; color: #444; font-size: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.04);">
                                                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#026447" stroke-width="2" style="margin-right: 15px; min-width: 20px;">
                                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                                </svg>
                                                                <span style="display: flex; flex-direction: column;">
                                                                    <strong style="font-weight: 600;">Instructor feedback and support</strong>
                                                                    <span style="font-size: 13px; color: #666; margin-top: 2px;">Get guidance from industry experts</span>
                                                                </span>
                                                            </li>
                                                            <li style="padding: 12px 0; display: flex; align-items: center; color: #444; font-size: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.04);">
                                                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#026447" stroke-width="2" style="margin-right: 15px; min-width: 20px;">
                                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                                </svg>
                                                                <span style="display: flex; flex-direction: column;">
                                                                    <strong style="font-weight: 600;">Professional Capstone Project</strong>
                                                                    <span style="font-size: 13px; color: #666; margin-top: 2px;">Apply your skills to a real-world challenge</span>
                                                                </span>
                                                            </li>
                                                            <li style="padding: 12px 0; display: flex; align-items: center; color: #444; font-size: 15px;">
                                                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#026447" stroke-width="2" style="margin-right: 15px; min-width: 20px;">
                                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                                </svg>
                                                                <span style="display: flex; flex-direction: column;">
                                                                    <strong style="font-weight: 600;">Digital badge upon completion</strong>
                                                                    <span style="font-size: 13px; color: #666; margin-top: 2px;">
                                                                        <a href="https://www.credly.com/org/stli/badge/navigating-global-development-finance" target="_blank" style="color: #026447; text-decoration: none; border-bottom: 1px dotted #026447;">Shareable credential for your profile</a>
                                                                    </span>
                                                                </span>
                                                            </li>
                                                        </ul>
                                                        
                                                                                                                 <a href="https://academy.wm.edu/product?catalog=NavigatingGlobalDevelopmentFinance_AID" target="_blank" rel="noopener noreferrer" class="enroll-button" style="display: inline-block; background-color: #026447; color: #fff; padding: 16px 20px; font-size: 16px; font-weight: 600; text-align: center; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; margin-top: 10px; margin-bottom: 5px; transition: all 0.3s ease; width: 100%; font-family: 'Inter', sans-serif; box-shadow: 0 4px 6px rgba(2, 100, 71, 0.2);">Start Learning</a>
                                                        <!-- Enrollment note removed -->
                                                    </div>
                                                </div>
                                                
                                                <!-- Scholarships section -->
                                                <div style="background-color: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid rgba(0, 0, 0, 0.05); margin-top: 5px;">
                                                    <div style="display: flex; align-items: flex-start;">
                                                        <div>
                                                            <h5 style="color: #026447; font-size: 16px; margin: 0 0 10px 0; font-family: 'Inter', sans-serif; font-weight: 600;">Scholarships Available</h5>
                                                            <p style="font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 15px; font-family: 'Inter', sans-serif;">Full and partial scholarships are available for participants from qualifying regions, with priority given to applicants from low and middle-income countries.</p>
                                                            <a href="#" id="scholarship-inquiry-btn" style="color: #026447; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-family: 'Inter', sans-serif;">
                                                                Inquire about scholarships
                                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                                    <polyline points="12 5 19 12 12 19"></polyline>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                <?php endif; ?>
                

                    
            <?php else : ?>
                <h1><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                    <div class="course-description">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="course-body">
            <?php if ( is_singular( 'course' ) || is_singular( 'llms_course' ) ) : ?>
                    <!-- Content for course pages is now displayed in the tabs within course-details -->
            <?php else : ?>
                <?php the_content(); ?>
            <?php endif; ?>
        </div>
    </section>
    </div>
</main>

<script>
    // More reliable document ready function
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    // Hide loading screen when the page has finished loading
    window.addEventListener('load', function() {
        // Check if element exists before trying to modify it
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            setTimeout(function() {
                loadingScreen.style.opacity = '0';
                loadingScreen.style.transition = 'opacity 0.5s ease';
                
                setTimeout(function() {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 500);
        }
        
        // Info drawer functionality
        const whyCourseButton = document.getElementById('why-course-button');
        const infoDrawer = document.getElementById('info-drawer');
        const drawerClose = document.getElementById('drawer-close');
        
        if (whyCourseButton && infoDrawer && drawerClose) {
            // Open drawer when clicking the "Why this course?" button
            whyCourseButton.addEventListener('click', function() {
                infoDrawer.classList.add('active');
                // Prevent scrolling on body when drawer is open
                document.body.style.overflow = 'hidden';
            });
            
            // Close drawer when clicking close button
            drawerClose.addEventListener('click', function() {
                infoDrawer.classList.remove('active');
                // Restore scrolling
                document.body.style.overflow = '';
            });
            
            // Close drawer when clicking outside of content
            infoDrawer.addEventListener('click', function(e) {
                if (e.target === infoDrawer) {
                    infoDrawer.classList.remove('active');
                    // Restore scrolling
                    document.body.style.overflow = '';
                }
            });
            
            // Close drawer when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && infoDrawer.classList.contains('active')) {
                    infoDrawer.classList.remove('active');
                    // Restore scrolling
                    document.body.style.overflow = '';
                }
            });
        }
    });
    
    // Course tabs functionality
    ready(function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        const tabFocusButtons = document.querySelectorAll('.tab-focus-button');
        
        function activateTab(tabId) {
                    // Store the current scroll position
                    const scrollPosition = window.scrollY || window.pageYOffset;
                    
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to the button with matching data-tab
                    const buttonToActivate = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
                    if (buttonToActivate) {
                        buttonToActivate.classList.add('active');
                    }
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        if (content) content.style.display = 'none';
                    });
                    
                    // Show the selected tab content
                    const selectedTab = document.getElementById(tabId);
                    if (selectedTab) {
                        selectedTab.style.display = 'block';
                    }
                    
                    // Restore the scroll position
                    window.scrollTo({
                        top: scrollPosition,
                        behavior: "instant"
                    });
                }
        
        if (tabButtons.length && tabContents.length) {
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    activateTab(tabId);
                });
            });
            
            // Add functionality to the tab focus buttons
            if (tabFocusButtons.length) {
                tabFocusButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');
                        activateTab(tabId);
                });
            });
            }
        }
        
        // Helper function to check if element is in viewport
        function isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
        
        // Auth functionality - this assumes there's auth functionality elsewhere
        const authOnlyElements = document.querySelectorAll('.auth-only');
        const guestOnlyElements = document.querySelectorAll('.guest-only');
        
        // Check if user is logged in - this is a placeholder
        // You should replace this with your actual auth check
        const isLoggedIn = false; // Set to true when user is logged in
        
        if (isLoggedIn) {
            authOnlyElements.forEach(el => el.style.display = 'block');
            guestOnlyElements.forEach(el => el.style.display = 'none');
        } else {
            authOnlyElements.forEach(el => el.style.display = 'none');
            guestOnlyElements.forEach(el => el.style.display = 'flex');
        }
    });
</script>

<script>
    // Header functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Menu dropdown functionality
        const menuButton = document.querySelector('.menu-button');
        const profileDropdown = document.querySelector('.profile-dropdown');
        const notificationsButton = document.getElementById('notificationsButton');
        
        if (menuButton && profileDropdown) {
            menuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileDropdown.contains(e.target) && e.target !== menuButton) {
                    profileDropdown.style.display = 'none';
                }
            });
        }
        
        // Authentication buttons
        const loginButtons = document.querySelectorAll('.login-button');
        const signupButtons = document.querySelectorAll('.signup-button');
        
        loginButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Handle login action - can be expanded based on actual auth system
                console.log('Login clicked');
            });
        });
        
        signupButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Handle signup action - can be expanded based on actual auth system
                console.log('Signup clicked');
            });
        });
        
        // Notifications button functionality
        if (notificationsButton) {
            notificationsButton.addEventListener('click', function() {
                // Handle notifications - can be expanded based on actual notification system
                console.log('Notifications clicked');
            });
        }
    });
</script>

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
                    <svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
            </div>
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
  
    <div class="footer-bottom" style="background: transparent; margin-bottom: 0; padding-bottom: 0;">
        <div class="footer-bottom-content" style="margin-bottom: 0; padding-bottom: 0;">
            <a href="https://www.wm.edu" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo" class="footer-bottom-logo" style="max-height: 60px; margin-bottom: 0;">
            </a>
        </div>
    </div>
</footer>

<style>
/* Target specific WordPress elements that might be creating whitespace */
.wp-site-blocks > *:last-child,
#page > *:last-child,
body > *:last-child {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Remove any WordPress-added margins */
footer.site-footer + * {
    display: none !important;
}
</style>

<?php get_footer(); ?> 

<!-- Custom Video Player Scripts and Styles -->
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/css/video-player.css">
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/js/video-player.js"></script> 

<!-- Scholarship Inquiry Modal -->
<div id="scholarship-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; font-family: 'Inter', sans-serif;">
    <div style="background-color: white; width: 90%; max-width: 550px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); position: relative; max-height: 90vh; overflow-y: auto;">
        <button id="close-modal" style="position: absolute; top: 15px; right: 15px; background: none; border: none; cursor: pointer; font-size: 24px; color: #666;">&times;</button>
        
        <div style="padding: 30px;">
            <h3 style="color: #026447; font-size: 22px; margin: 0 0 20px 0; font-family: 'Inter', sans-serif; font-weight: 600;">Scholarship Inquiry</h3>
            <p style="color: #555; margin-bottom: 25px; font-size: 15px; line-height: 1.5;">Please provide the following information to inquire about scholarship opportunities for this course. Priority is given to applicants from low and middle-income countries.</p>
            
            <form id="scholarship-form">
                <style>
                    /* Custom radio button styling with green color */
                    .inquiry-radio-container input[type="radio"] {
                        appearance: none;
                        -webkit-appearance: none;
                        width: 18px;
                        height: 18px;
                        border: 2px solid #ccc;
                        border-radius: 50%;
                        outline: none;
                        position: relative;
                        margin-right: 8px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }
                    
                    .inquiry-radio-container input[type="radio"]:checked {
                        border-color: #026447;
                        background-color: white;
                    }
                    
                    .inquiry-radio-container input[type="radio"]:checked:after {
                        content: '';
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 10px;
                        height: 10px;
                        border-radius: 50%;
                        background-color: #026447;
                    }
                    
                    .inquiry-radio-container label {
                        display: flex;
                        align-items: center;
                        font-weight: 500;
                    }
                    
                    .inquiry-radio-container label:hover input[type="radio"]:not(:checked) {
                        border-color: #026447;
                    }
                </style>
                
                <div style="margin-bottom: 20px; display: flex; gap: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px;" class="inquiry-radio-container">
                    <label for="inquiry-individual" style="color: #444; font-size: 16px; cursor: pointer; display: flex; align-items: center;">
                        <input type="radio" id="inquiry-individual" name="inquiry-type" value="individual" checked>
                        Individual Inquiry
                    </label>
                    <label for="inquiry-group" style="color: #444; font-size: 16px; cursor: pointer; display: flex; align-items: center;">
                        <input type="radio" id="inquiry-group" name="inquiry-type" value="group">
                        Group Inquiry
                    </label>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="full-name" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Full Name *</label>
                    <input type="text" id="full-name" name="full-name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="email" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Email Address *</label>
                    <input type="email" id="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="country" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Country of Residence *</label>
                    <input type="text" id="country" name="country" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="organization" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Organization</label>
                    <input type="text" id="organization" name="organization" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif;">
                </div>
                
                <div id="group-details" style="display: none; background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #eee;">
                    <h5 style="margin-top: 0; margin-bottom: 15px; color: #026447; font-size: 16px; font-weight: 600;">Group Details</h5>
                    <div style="margin-bottom: 15px;">
                        <label for="group-size" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Number of Participants *</label>
                        <input type="number" id="group-size" name="group-size" min="2" placeholder="E.g., 5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label for="group-type" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Type of Group *</label>
                        <select id="group-type" name="group-type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif; background-color: white;">
                            <option value="">Please select</option>
                            <option value="Journalism Team">Journalism Team</option>
                            <option value="Research Group">Research Group</option>
                            <option value="NGO Team">NGO Team</option>
                            <option value="Government Agency">Government Agency</option>
                            <option value="Academic Department">Academic Department</option>
                            <option value="Other">Other (please specify in message)</option>
                        </select>
                    </div>
                </div>
                
                <style>
                    /* Custom checkbox styling for scholarship form */
                    .custom-checkbox {
                        position: relative;
                        display: flex;
                        align-items: center;
                    }
                    .custom-checkbox input[type="checkbox"] {
                        position: absolute;
                        opacity: 0;
                        cursor: pointer;
                        height: 0;
                        width: 0;
                    }
                    .checkmark {
                        height: 18px;
                        width: 18px;
                        background-color: #fff;
                        border: 1px solid #ccc;
                        border-radius: 3px;
                        margin-right: 8px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .custom-checkbox input[type="checkbox"]:checked ~ .checkmark {
                        background-color: #026447;
                        border-color: #026447;
                    }
                    .checkmark:after {
                        content: "";
                        display: none;
                        width: 5px;
                        height: 10px;
                        border: solid white;
                        border-width: 0 2px 2px 0;
                        transform: rotate(45deg);
                    }
                    .custom-checkbox input[type="checkbox"]:checked ~ .checkmark:after {
                        display: block;
                    }
                </style>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Current Profession *</label>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                        <label class="custom-checkbox" for="profession-journalist" style="color: #444; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" id="profession-journalist" name="profession" value="Journalist">
                            <span class="checkmark"></span>
                            Journalist
                        </label>
                        <label class="custom-checkbox" for="profession-program-manager" style="color: #444; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" id="profession-program-manager" name="profession" value="Program Manager">
                            <span class="checkmark"></span>
                            Program Manager
                        </label>
                        <label class="custom-checkbox" for="profession-analyst" style="color: #444; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" id="profession-analyst" name="profession" value="Analyst">
                            <span class="checkmark"></span>
                            Analyst
                        </label>
                        <label class="custom-checkbox" for="profession-researcher" style="color: #444; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" id="profession-researcher" name="profession" value="Researcher">
                            <span class="checkmark"></span>
                            Researcher
                        </label>
                        <label class="custom-checkbox" for="profession-student" style="color: #444; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" id="profession-student" name="profession" value="Student">
                            <span class="checkmark"></span>
                            Student
                        </label>
                        <label class="custom-checkbox" for="profession-other" style="color: #444; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" id="profession-other" name="profession" value="Other">
                            <span class="checkmark"></span>
                            Other
                        </label>
                    </div>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label for="message" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Why are you interested in this course? *</label>
                    <textarea id="message" name="message" rows="4" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif;"></textarea>
                </div>
                
                <button type="submit" style="background-color: #026447; color: white; border: none; border-radius: 6px; padding: 12px 20px; font-family: 'Inter', sans-serif; font-weight: 600; cursor: pointer; width: 100%;">Submit Inquiry</button>
            </form>
        </div>
    </div>
</div>

<script>
    // More reliable document ready function
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    // Hide loading screen when the page has finished loading
    window.addEventListener('load', function() {
        // Check if element exists before trying to modify it
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            setTimeout(function() {
                loadingScreen.style.opacity = '0';
                loadingScreen.style.transition = 'opacity 0.5s ease';
                
                setTimeout(function() {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 500);
        }
        
        // Info drawer functionality
        const whyCourseButton = document.getElementById('why-course-button');
        const infoDrawer = document.getElementById('info-drawer');
        const drawerClose = document.getElementById('drawer-close');
        
        if (whyCourseButton && infoDrawer && drawerClose) {
            // Open drawer when clicking the "Why this course?" button
            whyCourseButton.addEventListener('click', function() {
                infoDrawer.classList.add('active');
                // Prevent scrolling on body when drawer is open
                document.body.style.overflow = 'hidden';
            });
            
            // Close drawer when clicking close button
            drawerClose.addEventListener('click', function() {
                infoDrawer.classList.remove('active');
                // Restore scrolling
                document.body.style.overflow = '';
            });
            
            // Close drawer when clicking outside of content
            infoDrawer.addEventListener('click', function(e) {
                if (e.target === infoDrawer) {
                    infoDrawer.classList.remove('active');
                    // Restore scrolling
                    document.body.style.overflow = '';
                }
            });
            
            // Close drawer when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && infoDrawer.classList.contains('active')) {
                    infoDrawer.classList.remove('active');
                    // Restore scrolling
                    document.body.style.overflow = '';
                }
            });
        }
    });
    
    // Course tabs functionality
    ready(function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        const tabFocusButtons = document.querySelectorAll('.tab-focus-button');
        
        function activateTab(tabId) {
                    // Store the current scroll position
                    const scrollPosition = window.scrollY || window.pageYOffset;
                    
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to the button with matching data-tab
                    const buttonToActivate = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
                    if (buttonToActivate) {
                        buttonToActivate.classList.add('active');
                    }
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        if (content) content.style.display = 'none';
                    });
                    
                    // Show the selected tab content
                    const selectedTab = document.getElementById(tabId);
                    if (selectedTab) {
                        selectedTab.style.display = 'block';
                    }
                    
                    // Restore the scroll position
                    window.scrollTo({
                        top: scrollPosition,
                        behavior: "instant"
                    });
                }
        
        if (tabButtons.length && tabContents.length) {
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    activateTab(tabId);
                });
            });
            
            // Add functionality to the tab focus buttons
            if (tabFocusButtons.length) {
                tabFocusButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');
                        activateTab(tabId);
                });
            });
            }
        }
        
        // Helper function to check if element is in viewport
        function isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
        
        // Auth functionality - this assumes there's auth functionality elsewhere
        const authOnlyElements = document.querySelectorAll('.auth-only');
        const guestOnlyElements = document.querySelectorAll('.guest-only');
        
        // Check if user is logged in - this is a placeholder
        // You should replace this with your actual auth check
        const isLoggedIn = false; // Set to true when user is logged in
        
        if (isLoggedIn) {
            authOnlyElements.forEach(el => el.style.display = 'block');
            guestOnlyElements.forEach(el => el.style.display = 'none');
        } else {
            authOnlyElements.forEach(el => el.style.display = 'none');
            guestOnlyElements.forEach(el => el.style.display = 'flex');
        }
    });

    // Scholarship inquiry modal functionality
    ready(function() {
        const scholarshipBtn = document.getElementById('scholarship-inquiry-btn');
        const scholarshipModal = document.getElementById('scholarship-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const scholarshipForm = document.getElementById('scholarship-form');
        
        if (scholarshipBtn && scholarshipModal) {
            // Open modal when clicking the scholarship inquiry button
            scholarshipBtn.addEventListener('click', function(e) {
                e.preventDefault();
                scholarshipModal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });
            
            // Close modal when clicking the close button
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function() {
                    scholarshipModal.style.display = 'none';
                    document.body.style.overflow = ''; // Restore scrolling
                });
            }
            
            // Close modal when clicking outside the modal content
            scholarshipModal.addEventListener('click', function(e) {
                if (e.target === scholarshipModal) {
                    scholarshipModal.style.display = 'none';
                    document.body.style.overflow = ''; // Restore scrolling
                }
            });
            
            // Handle form submission
            if (scholarshipForm) {
                // Toggle group details section based on inquiry type selection
                const inquiryRadios = document.querySelectorAll('input[name="inquiry-type"]');
                const groupDetailsSection = document.getElementById('group-details');
                
                inquiryRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'group') {
                            groupDetailsSection.style.display = 'block';
                            // Add required attribute to group fields when group option is selected
                            document.getElementById('group-size').setAttribute('required', 'required');
                            document.getElementById('group-type').setAttribute('required', 'required');
                        } else {
                            groupDetailsSection.style.display = 'none';
                            // Remove required attribute when individual option is selected
                            document.getElementById('group-size').removeAttribute('required');
                            document.getElementById('group-type').removeAttribute('required');
                        }
                    });
                });
                
                scholarshipForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const inquiryType = document.querySelector('input[name="inquiry-type"]:checked').value;
                    const fullName = document.getElementById('full-name').value;
                    const email = document.getElementById('email').value;
                    const country = document.getElementById('country').value;
                    const organization = document.getElementById('organization').value;
                    const message = document.getElementById('message').value;
                    
                    // Get group-specific data if group inquiry
                    let groupSize = '';
                    let groupType = '';
                    if (inquiryType === 'group') {
                        groupSize = document.getElementById('group-size').value;
                        groupType = document.getElementById('group-type').value;
                    }
                    
                    // Get profession selections
                    const professionCheckboxes = document.querySelectorAll('input[name="profession"]:checked');
                    let selectedProfessions = [];
                    professionCheckboxes.forEach((checkbox) => {
                        selectedProfessions.push(checkbox.value);
                    });
                    const professionsText = selectedProfessions.length > 0 ? selectedProfessions.join(', ') : 'None selected';
                    
                    // Create email body with form data
                    let emailBody = `
Inquiry Type: ${inquiryType.charAt(0).toUpperCase() + inquiryType.slice(1)}
Name: ${fullName}
Email: ${email}
Country: ${country}
Organization: ${organization}
Profession: ${professionsText}
`;

                    // Add group details if applicable
                    if (inquiryType === 'group') {
                        emailBody += `
Group Size: ${groupSize} participants
Group Type: ${groupType}
`;
                    }
                    
                    // Add interest/message
                    emailBody += `
Interest: ${message}
`;
                    
                    // Create subject line based on inquiry type
                    const subjectLine = inquiryType === 'group' 
                        ? `Group Scholarship Inquiry - ${organization || fullName} (${groupSize} participants)`
                        : `Scholarship Inquiry - ${fullName}`;
                    
                    // Create mailto link with form data
                    const mailtoLink = `mailto:ssnguna@wm.edu?subject=${encodeURIComponent(subjectLine)}&body=${encodeURIComponent(emailBody)}`;
                    
                    // Open email client with pre-filled email
                    window.location.href = mailtoLink;
                    
                    // Show confirmation message
                    alert('Thank you for your inquiry! Your default email client will now open with the scholarship inquiry details.');
                    
                    // Close modal
                    scholarshipModal.style.display = 'none';
                    document.body.style.overflow = ''; // Restore scrolling
                    
                    // Reset form
                    scholarshipForm.reset();
                    // Hide group details section
                    groupDetailsSection.style.display = 'none';
                });
            }
        }
    });
</script>
