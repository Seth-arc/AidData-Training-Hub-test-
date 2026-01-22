<?php
/**
 * Template Name: Course Archive
 * Description: Archive page for AidData courses
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 * @since Twenty Twenty-Four 1.0
 */

get_header();

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
?>

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
        <p class="loading-text">Loading Courses</p>
    </div>
</div>

<style>
    /* Import Inter font from Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    /* Global font family override for all elements */
    *, *::before, *::after {
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Additional specific overrides for common elements */
    body, html, div, span, h1, h2, h3, h4, h5, h6, p, a, button, input, textarea, select, label {
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Webkit browsers (Chrome, Safari, newer versions of Opera and Edge) */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #115740;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #0a3d2c;
    }
    
    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #115740 #f1f1f1;
        font-family: 'Inter', sans-serif !important;
    }
    
    body, html {
        margin: 0;
        padding: 0;
        background: #ffffff;
        min-height: 100vh;
        color: #1a1a1a;
        line-height: 1.5;
        letter-spacing: -0.01em;
        font-family: 'Inter', sans-serif !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Hide WordPress default header */
    .site-header, #masthead, header.wp-block-template-part {
        display: none !important;
    }
    
    /* Remove any hr elements or horizontal lines */
    hr {
        display: none !important;
    }
    
    /* Exception for footer hr */
    .site-footer hr {
        display: block !important;
        border: 0;
        height: 1px;
        background-color: white;
        width: 100%;
        margin: 20px 0;
    }
    
    /* Dashboard Header - Exact match to dashboard */
    .lms-header {
        background: rgba(255, 255, 255, 0.98);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        padding: 1.5rem 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }
    
    .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 4rem;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 4rem;
        align-items: center;
    }
    
    .logo-section {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    
    .logo {
        height: 30px;
        width: auto;
        opacity: 0.95;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 1px 2px rgba(17, 87, 64, 0.1));
    }
    
    .logo:hover {
        opacity: 1;
        transform: translateY(-1px);
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-left: auto;
        gap: 1rem;
    }
    
    .header-icons {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
    }
    
    .header-button {
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
        position: relative;
    }
    
    .header-button svg {
        width: 20px;
        height: 20px;
    }
    
    .header-button:hover {
        background: rgba(17, 87, 64, 0.08);
        transform: translateY(-1px);
    }
    
    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: #dc3545;
        border-radius: 50%;
        display: none;
    }
    
    .notification-badge.active {
        display: block;
    }
    
    .profile-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        min-width: 280px;
        padding: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }
    
    .profile-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .dropdown-header {
        padding: 20px;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .dropdown-user-info .user-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }
    
    .dropdown-user-info .user-email {
        font-size: 14px;
        color: #6c757d;
    }
    
    .dropdown-item {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    
    .dropdown-item:hover {
        background: #f8f9fa;
        color: #115740;
    }
    
    .dropdown-item.logout-button {
        color: #dc3545;
        border-top: 1px solid #f1f3f5;
    }
    
    .dropdown-item.logout-button:hover {
        background: rgba(220, 53, 69, 0.05);
    }
    
    .archive-content {
        margin-top: 0;
        padding: 0;
        min-height: 100vh;
        padding-top: 100px;
        font-family: 'Inter', sans-serif !important;
    }
    
    .archive-hero {
        background: #ffffff;
        color: #1a1a1a;
        padding: 2rem 0 3rem;
        font-family: 'Inter', sans-serif !important;
    }
    
    .archive-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        text-align: center;
    }
    
    .archive-title {
        font-size: 2.25rem;
        font-weight: 300;
        margin: 0 0 1rem 0;
        letter-spacing: -0.03em;
        color: #1a1a1a;
        line-height: 1.2;
        font-family: 'Inter', sans-serif !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }
    
    .archive-description {
        font-size: 1rem;
        color: #666;
        margin: 0;
        font-weight: 400;
        font-family: 'Inter', sans-serif !important;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }
    
    .main-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem 4rem;
    }
    
    .filter-bar {
        background: white;
        padding: 2rem;
        border: 1px solid #f0f0f0;
        margin-bottom: 2rem;
        display: flex;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filter-group label {
        font-weight: 500;
        color: #1a1a1a;
        font-size: 0.875rem;
    }
    
    .filter-group select {
        padding: 10px 15px;
        border: 1px solid #e0e0e0;
        background: white;
        font-size: 0.875rem;
        transition: border-color 0.2s ease;
        font-family: 'Inter', sans-serif !important;
    }
    
    .filter-group select:focus {
        outline: none;
        border-color: #115740;
    }
    
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .course-card {
        background: #ffffff;
        border: 1px solid #f0f0f0;
        padding: 0;
        transition: border-color 0.2s ease;
        overflow: hidden;
    }
    
    .course-card:hover {
        border-color: #e0e0e0;
    }
    
    .course-image {
        height: 200px;
        background: #115740;
        position: relative;
        overflow: hidden;
    }
    
    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .course-price {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.95);
        color: #115740;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 12px;
    }
    
    .course-content {
        padding: 2rem;
    }
    
    .course-title {
        font-size: 1.125rem;
        font-weight: 500;
        color: #1a1a1a;
        margin: 0 0 1rem 0;
        font-family: 'Inter', sans-serif !important;
        line-height: 1.4;
    }
    
    .course-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .course-title a:hover {
        color: #115740;
    }
    
    .course-excerpt {
        color: #666;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-size: 0.875rem;
    }
    
    .course-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        color: #666;
    }
    
    .course-meta span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .course-button {
        display: inline-block;
        background: #115740;
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }
    
    .course-button:hover {
        background: #0a3d2c;
        color: white;
    }
    
    .pagination {
        text-align: center;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #f0f0f0;
    }
    
    .pagination .page-numbers {
        display: inline-block;
        padding: 12px 18px;
        margin: 0 5px;
        background: white;
        color: #115740;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid #e0e0e0;
    }
    
    .pagination .page-numbers:hover,
    .pagination .page-numbers.current {
        background: #115740;
        color: white;
        border-color: #115740;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #666;
        background: #ffffff;
        border: 1px solid #f0f0f0;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: #1a1a1a;
        margin-bottom: 1rem;
        font-weight: 500;
    }
    
    .empty-state p {
        color: #666;
        font-size: 1rem;
    }
    
    /* Footer Styles */
    .site-footer {
        background-color: #115740;
        color: white;
        padding: 3rem 1rem 0;
        margin-top: 4rem;
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
        
        .header-content {
            padding: 0 2rem;
            gap: 2rem;
        }
        
        .archive-content {
            padding-top: 80px;
        }
        
        .archive-hero {
            padding: 1rem 0 2.5rem;
        }
        
        .archive-title {
            font-size: 2rem;
        }
        
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .lms-header {
            padding: 1rem 0;
        }
        
        .header-content {
            padding: 0 1.5rem;
        }
        
        .archive-content {
            padding-top: 60px;
        }
        
        .archive-hero {
            padding: 1rem 0 2rem;
        }
        
        .archive-hero-content {
            padding: 0 1.5rem;
        }
        
        .archive-title {
            font-size: 1.75rem;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .main-content {
            padding: 0 1rem 2rem;
        }
        
        .filter-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .courses-grid {
            gap: 1.5rem;
        }
        
        .course-content {
            padding: 1.5rem;
        }
    }
</style>

<!-- Header - Exact match to dashboard -->
<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="https://www.aiddata.org" target="_blank">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
            </a>
        </div>
        
        <div class="header-actions">
            <div class="auth-only visible">
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
                        <a href="<?php echo esc_url(home_url('/lp-profile/')); ?>" class="dropdown-item">My Dashboard</a>
                        <button class="dropdown-item logout-button" onclick="location.href='<?php echo wp_logout_url(home_url()); ?>'">Sign Out</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="archive-content">
    <div class="archive-hero">
        <div class="archive-hero-content">
            <h1 class="archive-title">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                Courses
            </h1>
            <p class="archive-description">
                Explore our comprehensive collection of courses designed to enhance your understanding of global development finance, data analysis, and policy research.
            </p>
        </div>
    </div>

    <div class="main-content">
        <div class="filter-bar">
            <div class="filter-group">
                <label for="difficulty-filter">Difficulty:</label>
                <select id="difficulty-filter">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="price-filter">Price:</label>
                <select id="price-filter">
                    <option value="">All Prices</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort-filter">Sort by:</label>
                <select id="sort-filter">
                    <option value="date">Newest First</option>
                    <option value="title">Title A-Z</option>
                    <option value="price">Price Low-High</option>
                    <option value="popularity">Most Popular</option>
                </select>
            </div>
        </div>

        <?php if (have_posts()) : ?>
            <div class="courses-grid">
                <?php while (have_posts()) : the_post(); 
                    $course_price = get_post_meta(get_the_ID(), '_course_price', true);
                    $course_duration = get_post_meta(get_the_ID(), '_course_duration', true);
                    $course_difficulty = get_post_meta(get_the_ID(), '_course_difficulty', true);
                    $course_type = get_post_meta(get_the_ID(), '_course_type', true);
                    
                    $price_display = empty($course_price) || $course_price == '0' ? 'Free' : '$' . number_format(floatval($course_price), 2);
                ?>
                    <article class="course-card">
                        <div class="course-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php endif; ?>
                            <div class="course-price"><?php echo esc_html($price_display); ?></div>
                        </div>
                        
                        <div class="course-content">
                            <h2 class="course-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="course-meta">
                                <?php if ($course_duration) : ?>
                                    <span>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12,6 12,12 16,14"></polyline>
                                        </svg>
                                        <?php echo esc_html($course_duration); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($course_difficulty) : ?>
                                    <span>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"></polygon>
                                        </svg>
                                        <?php echo esc_html($course_difficulty); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="course-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="course-button">
                                View Course
                                <svg width="16" height="16" style="margin-left: 8px; vertical-align: middle;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                    'type' => 'plain',
                ));
                ?>
            </div>
            
        <?php else : ?>
            <div class="empty-state">
                <h3>No Courses Found</h3>
                <p>We're currently working on adding more courses. Please check back soon!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide loading screen after content is loaded
    setTimeout(function() {
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            loadingScreen.style.opacity = '0';
            setTimeout(function() {
                loadingScreen.style.display = 'none';
            }, 500);
        }
    }, 1000);
    
    // Profile dropdown functionality
    const menuButton = document.querySelector('.menu-button');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (menuButton && profileDropdown) {
        menuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
        
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Simple filter functionality
    const difficultyFilter = document.getElementById('difficulty-filter');
    const priceFilter = document.getElementById('price-filter');
    const sortFilter = document.getElementById('sort-filter');
    
    function applyFilters() {
        // This would typically make AJAX calls to filter results
        // For now, we'll just reload the page with query parameters
        const params = new URLSearchParams();
        
        if (difficultyFilter.value) params.set('difficulty', difficultyFilter.value);
        if (priceFilter.value) params.set('price', priceFilter.value);
        if (sortFilter.value) params.set('orderby', sortFilter.value);
        
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.location.href = newUrl;
    }
    
    difficultyFilter.addEventListener('change', applyFilters);
    priceFilter.addEventListener('change', applyFilters);
    sortFilter.addEventListener('change', applyFilters);
    
    // Set current values from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('difficulty')) difficultyFilter.value = urlParams.get('difficulty');
    if (urlParams.get('price')) priceFilter.value = urlParams.get('price');
    if (urlParams.get('orderby')) sortFilter.value = urlParams.get('orderby');
});
</script>
</script>

<?php get_footer(); ?>

