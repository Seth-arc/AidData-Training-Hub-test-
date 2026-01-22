<?php
/**
 * Template Name: Course Page Template
 * Template Post Type: page
 *
 * A comprehensive course page template that follows the front-page theme
 * Designed to match the AidData Training Hub design patterns and styling
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

// Helper function to get field values with ACF fallback
function course_get_field($field_name, $post_id = false, $default = '') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // If ACF is active, use get_field
    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        return $value !== '' ? $value : $default;
    }
    
    // Fall back to post meta
    $value = get_post_meta($post_id, $field_name, true);
    return $value !== '' ? $value : $default;
}

// Helper function for repeater fields
function course_has_rows($field_name, $post_id = false) {
    if (function_exists('have_rows')) {
        return have_rows($field_name, $post_id);
    }
    
    // Basic fallback for repeaters
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $value = get_post_meta($post_id, $field_name, true);
    return !empty($value);
}

// Helper function to get sub field value
function course_get_sub_field($field_name, $default = '') {
    if (function_exists('get_sub_field')) {
        $value = get_sub_field($field_name);
        return $value !== '' ? $value : $default;
    }
    
    return $default;
}
?>

<!-- Scrollbar Styling -->
<style>
    /* Import Inter font from Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
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
        font-family: 'Inter', sans-serif;
    }

    /* Force proper box sizing for all elements */
    *, *:before, *:after {
        box-sizing: border-box;
        margin-bottom: 0;
    }
    
    /* Top-level elements */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        overflow-x: hidden;
        font-family: 'Inter', sans-serif;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    /* Course Page Specific Styles */
    .course-hero {
        background: linear-gradient(135deg, rgba(17, 87, 64, 0.05) 0%, rgba(0, 179, 136, 0.05) 100%);
        padding: 3rem 0 2rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .course-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 100%;
        height: 200%;
        background: linear-gradient(45deg, transparent 30%, rgba(0, 179, 136, 0.03) 50%, transparent 70%);
        transform: rotate(15deg);
        pointer-events: none;
    }
    
    .course-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 2;
    }
    
    .course-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .course-breadcrumb a {
        color: #026447;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .course-breadcrumb a:hover {
        color: #004E38;
    }
    
    .course-breadcrumb svg {
        width: 12px;
        height: 12px;
        opacity: 0.5;
    }
    
    .course-hero-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        align-items: start;
    }
    
    .course-hero-left h1 {
        font-size: 2.75rem;
        font-weight: 700;
        color: #026447;
        margin: 0 0 1rem 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }
    
    .course-subtitle {
        font-size: 1.25rem;
        color: #6c757d;
        margin-bottom: 2rem;
        line-height: 1.6;
        font-weight: 400;
    }
    
    .course-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9375rem;
        color: #495057;
    }
    
    .meta-item svg {
        width: 18px;
        height: 18px;
        color: #026447;
    }
    
    .course-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }
    
    .course-tag {
        background: linear-gradient(135deg, rgba(0, 179, 136, 0.1) 0%, rgba(120, 157, 74, 0.1) 100%);
        color: #026447;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8125rem;
        font-weight: 600;
        border: 1px solid rgba(0, 179, 136, 0.2);
    }
    
    .course-hero-right {
        position: sticky;
        top: 2rem;
    }
    
    .course-preview-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .course-preview-image {
        width: 100%;
        height: 200px;
        border-radius: 12px;
        object-fit: cover;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #026447 0%, #004E38 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
    
    .course-price {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .price-amount {
        font-size: 2.5rem;
        font-weight: 700;
        color: #026447;
        margin-bottom: 0.5rem;
    }
    
    .price-note {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .course-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #026447 0%, #004E38 100%);
        color: white;
        padding: 0.875rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9375rem;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(2, 100, 71, 0.3);
    }
    
    .btn-secondary {
        background: transparent;
        color: #026447;
        padding: 0.875rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        border: 2px solid #026447;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9375rem;
    }
    
    .btn-secondary:hover {
        background: #026447;
        color: white;
        transform: translateY(-1px);
    }
    
    .course-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .course-features li {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
        font-size: 0.875rem;
        color: #495057;
    }
    
    .course-features li::before {
        content: '✓';
        color: #026447;
        font-weight: 700;
        width: 16px;
        text-align: center;
    }
    
    .course-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 20px;
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
    }
    
    .content-main {
        background: white;
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    
    .content-sidebar {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .sidebar-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #026447;
        margin: 0 0 1.5rem 0;
        letter-spacing: -0.02em;
    }
    
    .section-subtitle {
        font-size: 1.25rem;
        font-weight: 600;
        color: #026447;
        margin: 2rem 0 1rem 0;
    }
    
    .content-text {
        font-size: 1.0625rem;
        line-height: 1.7;
        color: #495057;
        margin-bottom: 1.5rem;
    }
    
    .highlight-box {
        background: linear-gradient(135deg, rgba(0, 179, 136, 0.05) 0%, rgba(120, 157, 74, 0.05) 100%);
        border-left: 4px solid #00b388;
        padding: 1.5rem;
        margin: 2rem 0;
        border-radius: 0 8px 8px 0;
    }
    
    .highlight-box h4 {
        color: #026447;
        margin: 0 0 1rem 0;
        font-size: 1.125rem;
        font-weight: 600;
    }
    
    .highlight-box p {
        margin: 0;
        color: #495057;
        line-height: 1.6;
    }
    
    .learning-outcomes {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .learning-outcomes li {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: rgba(0, 179, 136, 0.03);
        border-radius: 8px;
        border-left: 3px solid #00b388;
    }
    
    .learning-outcomes li::before {
        content: '🎯';
        font-size: 1.2rem;
        margin-top: 0.1rem;
    }
    
    .instructor-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: rgba(0, 179, 136, 0.03);
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    
    .instructor-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .instructor-info h4 {
        margin: 0 0 0.25rem 0;
        color: #026447;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .instructor-info p {
        margin: 0;
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .course-modules {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .module-item {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }
    
    .module-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        border-color: rgba(2, 100, 71, 0.2);
    }
    
    .module-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .module-number {
        background: linear-gradient(135deg, #026447 0%, #004E38 100%);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
    }
    
    .module-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #026447;
        margin: 0;
    }
    
    .module-duration {
        color: #6c757d;
        font-size: 0.875rem;
        margin-left: auto;
    }
    
    .module-description {
        color: #495057;
        line-height: 1.6;
        margin: 0;
    }
    
    /* Partnership Section */
    .partnership-section {
        text-align: center;
        padding: 1.5rem;
        background: rgba(17, 87, 64, 0.03);
        border-radius: 12px;
        border: 1px solid rgba(17, 87, 64, 0.1);
    }
    
    .partnership-logo {
        height: 40px;
        margin-bottom: 0.75rem;
    }
    
    .partnership-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .course-hero-grid,
        .content-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .course-hero-right {
            position: static;
        }
        
        .course-hero-left h1 {
            font-size: 2.25rem;
        }
    }
    
    @media (max-width: 768px) {
        .course-hero {
            padding: 2rem 0 1.5rem 0;
        }
        
        .course-hero-content,
        .course-content {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .course-hero-left h1 {
            font-size: 1.875rem;
        }
        
        .course-subtitle {
            font-size: 1.125rem;
        }
        
        .content-main,
        .sidebar-card {
            padding: 1.5rem;
        }
        
        .course-meta {
            flex-direction: column;
            gap: 1rem;
        }
        
        .course-actions {
            flex-direction: column;
        }
    }
    
    @media (max-width: 480px) {
        .course-hero-left h1 {
            font-size: 1.625rem;
        }
        
        .content-main,
        .sidebar-card {
            padding: 1rem;
        }
        
        .instructor-card {
            flex-direction: column;
            text-align: center;
        }
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
        <p class="loading-text">Loading Course</p>
    </div>
</div>

<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="<?php echo esc_url(home_url('/')); ?>">
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

<main class="lms-main">
    <!-- Course Hero Section -->
    <section class="course-hero">
        <div class="course-hero-content">
            <nav class="course-breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Training Hub</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
                <span><?php echo esc_html(course_get_field('course_category', false, 'Courses')); ?></span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
                <span><?php the_title(); ?></span>
            </nav>
            
            <div class="course-hero-grid">
                <div class="course-hero-left">
                    <h1><?php the_title(); ?></h1>
                    <p class="course-subtitle"><?php echo esc_html(course_get_field('course_subtitle', false, get_the_excerpt())); ?></p>
                    
                    <div class="course-meta">
                        <?php if (course_get_field('course_duration')) : ?>
                        <div class="meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span><?php echo esc_html(course_get_field('course_duration')); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (course_get_field('course_level')) : ?>
                        <div class="meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                            <span><?php echo esc_html(course_get_field('course_level')); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (course_get_field('course_language')) : ?>
                        <div class="meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            <span><?php echo esc_html(course_get_field('course_language')); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (course_get_field('course_students')) : ?>
                        <div class="meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span><?php echo esc_html(course_get_field('course_students')); ?> students</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="course-tags">
                        <?php if (course_get_field('course_type')) : ?>
                        <span class="course-tag"><?php echo esc_html(course_get_field('course_type')); ?></span>
                        <?php endif; ?>
                        
                        <?php if (course_get_field('badge_type')) : ?>
                        <span class="course-tag"><?php echo esc_html(course_get_field('badge_type')); ?></span>
                        <?php endif; ?>
                        
                        <?php if (course_get_field('course_format')) : ?>
                        <span class="course-tag"><?php echo esc_html(course_get_field('course_format')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="course-hero-right">
                    <div class="course-preview-card">
                        <?php if (course_get_field('course_image')) : ?>
                        <img src="<?php echo esc_url(course_get_field('course_image')); ?>" alt="<?php the_title(); ?>" class="course-preview-image">
                        <?php else : ?>
                        <div class="course-preview-image">
                            Course Preview
                        </div>
                        <?php endif; ?>
                        
                        <div class="course-price">
                            <?php if (course_get_field('course_price') && course_get_field('course_price') > 0) : ?>
                            <div class="price-amount">$<?php echo esc_html(course_get_field('course_price')); ?></div>
                            <div class="price-note"><?php echo esc_html(course_get_field('price_note', false, 'One-time payment')); ?></div>
                            <?php else : ?>
                            <div class="price-amount">Free</div>
                            <div class="price-note">Full access included</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="course-actions">
                            <?php if (course_get_field('enroll_button_url')) : ?>
                            <a href="<?php echo esc_url(course_get_field('enroll_button_url')); ?>" class="btn-primary">
                                <?php echo esc_html(course_get_field('enroll_button_text', false, 'Enroll Now')); ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (course_get_field('preview_button_enabled')) : ?>
                            <button class="btn-secondary trailer-button" data-video="<?php echo esc_url(course_get_field('course_video')); ?>">
                                Preview Course
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <ul class="course-features">
                            <?php 
                            $default_features = array(
                                'Lifetime access',
                                'Certificate of completion',
                                'Mobile and desktop access',
                                'Community support'
                            );
                            
                            if (course_has_rows('course_features_list')) :
                                if (function_exists('have_rows')) :
                                    while (have_rows('course_features_list')) : the_row();
                            ?>
                            <li><?php echo esc_html(get_sub_field('feature')); ?></li>
                            <?php 
                                    endwhile;
                                else:
                                    foreach ($default_features as $feature) :
                            ?>
                            <li><?php echo esc_html($feature); ?></li>
                            <?php 
                                    endforeach;
                                endif;
                            else:
                                foreach ($default_features as $feature) :
                            ?>
                            <li><?php echo esc_html($feature); ?></li>
                            <?php endforeach; endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Course Content Section -->
    <section class="course-content">
        <div class="content-grid">
            <div class="content-main">
                <h2 class="section-title">About This Course</h2>
                
                <?php if (get_the_content()) : ?>
                <div class="content-text">
                    <?php the_content(); ?>
                </div>
                <?php endif; ?>
                
                <?php if (course_get_field('course_overview')) : ?>
                <div class="content-text">
                    <?php echo wp_kses_post(course_get_field('course_overview')); ?>
                </div>
                <?php endif; ?>
                
                <?php if (course_has_rows('learning_outcomes')) : ?>
                <h3 class="section-subtitle">What You'll Learn</h3>
                <ul class="learning-outcomes">
                    <?php 
                    if (function_exists('have_rows')) :
                        while (have_rows('learning_outcomes')) : the_row(); 
                    ?>
                    <li>
                        <div>
                            <strong><?php echo esc_html(get_sub_field('outcome_title')); ?></strong>
                            <?php if (get_sub_field('outcome_description')) : ?>
                            <p style="margin: 0.5rem 0 0 0; color: #6c757d;"><?php echo esc_html(get_sub_field('outcome_description')); ?></p>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </ul>
                <?php endif; ?>
                
                <?php if (course_get_field('course_requirements')) : ?>
                <div class="highlight-box">
                    <h4>Prerequisites</h4>
                    <p><?php echo wp_kses_post(course_get_field('course_requirements')); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (course_has_rows('course_modules')) : ?>
                <h3 class="section-subtitle">Course Content</h3>
                <ul class="course-modules">
                    <?php 
                    $module_count = 1;
                    if (function_exists('have_rows')) :
                        while (have_rows('course_modules')) : the_row(); 
                    ?>
                    <li class="module-item">
                        <div class="module-header">
                            <div class="module-number"><?php echo $module_count; ?></div>
                            <h4 class="module-title"><?php echo esc_html(get_sub_field('module_title')); ?></h4>
                            <?php if (get_sub_field('module_duration')) : ?>
                            <span class="module-duration"><?php echo esc_html(get_sub_field('module_duration')); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (get_sub_field('module_description')) : ?>
                        <p class="module-description"><?php echo esc_html(get_sub_field('module_description')); ?></p>
                        <?php endif; ?>
                    </li>
                    <?php 
                        $module_count++;
                        endwhile;
                    endif;
                    ?>
                </ul>
                <?php endif; ?>
            </div>
            
            <div class="content-sidebar">
                <!-- Instructors -->
                <?php if (course_has_rows('course_instructors')) : ?>
                <div class="sidebar-card">
                    <h3 class="section-subtitle" style="margin-top: 0;">Instructors</h3>
                    <?php 
                    if (function_exists('have_rows')) :
                        while (have_rows('course_instructors')) : the_row();
                            $instructor_image = get_sub_field('instructor_image');
                    ?>
                    <div class="instructor-card">
                        <?php if ($instructor_image) : ?>
                        <img src="<?php echo esc_url($instructor_image); ?>" alt="<?php echo esc_attr(get_sub_field('instructor_name')); ?>" class="instructor-avatar">
                        <?php endif; ?>
                        <div class="instructor-info">
                            <h4><?php echo esc_html(get_sub_field('instructor_name')); ?></h4>
                            <p><?php echo esc_html(get_sub_field('instructor_title')); ?></p>
                            <?php if (get_sub_field('instructor_bio')) : ?>
                            <p style="margin-top: 0.5rem; font-size: 0.8125rem; line-height: 1.4;"><?php echo esc_html(get_sub_field('instructor_bio')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </div>
                <?php endif; ?>
                
                <!-- Partnership -->
                <?php if (course_get_field('partnership_logo') || course_get_field('partnership_text')) : ?>
                <div class="sidebar-card">
                    <div class="partnership-section">
                        <?php if (course_get_field('partnership_logo')) : ?>
                        <img src="<?php echo esc_url(course_get_field('partnership_logo')); ?>" alt="<?php echo esc_attr(course_get_field('partnership_name', false, 'Partner')); ?>" class="partnership-logo">
                        <?php endif; ?>
                        <?php if (course_get_field('partnership_text')) : ?>
                        <p class="partnership-text"><?php echo esc_html(course_get_field('partnership_text')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Additional Info -->
                <?php if (course_get_field('additional_info')) : ?>
                <div class="sidebar-card">
                    <h3 class="section-subtitle" style="margin-top: 0;">Additional Information</h3>
                    <div class="content-text">
                        <?php echo wp_kses_post(course_get_field('additional_info')); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div class="video-modal" id="courseTrailer" role="dialog" aria-hidden="true" aria-labelledby="courseTrailerTitle">
        <button class="close-modal" aria-label="Close video">×</button>
        <h3 id="courseTrailerTitle" class="sr-only">Course Preview</h3>
        <div class="video-container">
            <iframe
                src="about:blank"
                allow="fullscreen; encrypted-media"
                allowfullscreen
                title=""
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                frameborder="0">
            </iframe>
        </div>
    </div>
</main>

<?php
get_footer();
?>

<!-- Custom Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Video modal functionality
    const trailerButtons = document.querySelectorAll('.trailer-button');
    const videoModal = document.getElementById('courseTrailer');
    const closeModal = document.querySelector('.close-modal');
    const iframe = videoModal.querySelector('iframe');

    trailerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const videoUrl = this.getAttribute('data-video');
            if (videoUrl) {
                iframe.src = videoUrl;
                videoModal.style.display = 'flex';
                videoModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    closeModal.addEventListener('click', function() {
        videoModal.style.display = 'none';
        videoModal.setAttribute('aria-hidden', 'true');
        iframe.src = 'about:blank';
        document.body.style.overflow = 'auto';
    });

    // Close modal on outside click
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            closeModal.click();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && videoModal.style.display === 'flex') {
            closeModal.click();
        }
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Loading screen
    window.addEventListener('load', function() {
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            loadingScreen.style.opacity = '0';
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        }
    });
});
</script>

