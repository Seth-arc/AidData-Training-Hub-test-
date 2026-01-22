<?php
/**
 * Template Name: Standard Page Template
 * Template Post Type: page
 *
 * A custom template for regular WordPress pages using the AidData Training Hub styling
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

// Inline CSS for page template
?>
<style>
    /* Import Inter font from Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
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
    .page-body,
    .site-footer,
    .footer-content {
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .page-content,
    .page-header {
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
    
    /* Page header and hero */
    main.wp-site-blocks {
        padding-top: 0;
        margin-top: 0;
        overflow-x: hidden;
        width: 100%;
        position: relative;
        top: 0;
    }
    
    /* Make hero image flush with header */
    .page-hero {
        margin-top: 70px; /* Same as header height */
    }
    
    .page-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 0 40px;
        overflow-x: visible;
    }
    
    .page-header {
        margin-bottom: 40px;
        margin-top: 0;
    }
    
    .page-title {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: #333;
        font-family: 'Inter', sans-serif;
    }
    
    /* Hero image styles */
    .page-hero {
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
    }
    
    /* WordPress admin bar adjustment to fix hero positioning */
    .admin-bar .page-hero {
        margin-top: 102px; /* 70px for header + 32px for admin bar */
    }
    
    @media screen and (max-width: 782px) {
        .admin-bar .page-hero {
            margin-top: 116px; /* 70px for header + 46px for admin bar on mobile */
        }
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
    
    .page-hero-content {
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
    
    .page-hero-title {
        font-size: 3rem;
        margin-bottom: 15px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        color: white;
        max-width: 80%;
        font-weight: 400;
        font-family: 'Inter', sans-serif;
    }
    
    .page-hero-description {
        max-width: 800px;
        font-size: 1.2rem;
        line-height: 1.5;
        margin-bottom: 0;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        text-align: center;
        font-family: 'Inter', sans-serif;
    }
    
    .page-hero-description p {
        margin: 0;
        font-family: 'Inter', sans-serif;
    }
    
    @media (max-width: 992px) {
        .page-hero {
            height: 350px;
            width: 100%;
        }
        
        .page-hero-title {
            font-size: 2.6rem;
            max-width: 90%;
        }
        
        .page-hero-description {
            font-size: 1.1rem;
            max-width: 90%;
        }
    }
    
    @media (max-width: 768px) {
        .page-hero {
            height: 300px;
            width: 100%;
        }
        
        .page-hero-title {
            font-size: 2.2rem;
            max-width: 95%;
        }
        
        .page-hero-description {
            font-size: 1rem;
            max-width: 95%;
        }
    }
    
    @media (max-width: 576px) {
        .page-hero {
            height: 250px;
            width: 100%;
        }
        
        .page-hero-content {
            padding: 20px;
        }
        
        .page-hero-title {
            font-size: 1.8rem;
            margin-bottom: 10px;
            max-width: 100%;
        }
        
        .page-hero-description {
            max-width: 100%;
        }
    }
    
    /* Header Styles */
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
    
    /* Main content area styling */
    .main-content {
        padding: 40px 20px;
        background-color: #fff;
        border-radius: 8px;
        margin-top: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .main-content h2 {
        color: #026447;
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
    }
    
    .main-content p {
        color: #333;
        line-height: 1.6;
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
    }
    
    .main-content ul, 
    .main-content ol {
        margin-bottom: 20px;
        color: #333;
        line-height: 1.6;
        padding-left: 20px;
    }
    
    .main-content li {
        margin-bottom: 10px;
    }
    
    /* Loading screen adjustment to work with hero */
    .loading-screen {
        z-index: 2000;
    }

    /* Ensure all content sections stay within bounds */
    .main-content {
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Adding primary color variable */
    :root {
        --primary-color: #fff;
    }
    
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
<!-- Loading Screen -->
<div class="loading-screen">
    <div class="loading-content">
        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="loading-logo">
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
        </div>
        <p class="loading-text">Loading Page</p>
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
        <section class="page-content wp-block-template-part">
            <div class="page-header">
                <?php if (has_post_thumbnail()) : ?>
                <div class="page-hero">
                    <?php the_post_thumbnail('full', array('class' => 'hero-image')); ?>
                    
                    <div class="page-hero-content">
                        <h1 class="page-hero-title"><?php the_title(); ?></h1>
                        <?php if (has_excerpt()) : ?>
                        <div class="page-hero-description">
                            <?php the_excerpt(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else : ?>
                    <h1 class="page-title"><?php the_title(); ?></h1>
                    <?php if (has_excerpt()) : ?>
                        <div class="page-description">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="page-body">
                <div class="main-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
    </div>
</main>

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo.png" alt="AidData Logo" class="footer-logo">
            <
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
                <a href="https://www.aiddata.org/newsletter" target="_blank" rel="noopener noreferrer" class="newsletter-button" aria-label="Get our Newsletter">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span>Newsletter</span>
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
    });
    
    // Header functionality
    ready(function() {
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

<?php get_footer(); ?> 
