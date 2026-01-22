<?php
/**
 * Template Name: Video Interview Page
 * Template Post Type: page, aiddata_interview
 *
 * A custom template for displaying video interviews with transcript and bio.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

get_header();

// Enqueue authentication-specific styles (retained from front-page)
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
?>

<style>
    /* Webkit browsers */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    ::-webkit-scrollbar-thumb { background: #026447; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #004E38; }
    /* Firefox */
    * { scrollbar-width: thin; scrollbar-color: #026447 #f1f1f1; }

    /* Template Specific Styles */
    .interview-hero {
        padding: 8rem 2rem 4rem; /* Adjusted top padding for fixed header */
        background: linear-gradient(135deg, rgba(17, 87, 64, 0.05) 0%, rgba(26, 128, 95, 0.08) 100%);
        text-align: center;
    }

    .interview-container {
        max-width: 1000px;
        margin: -3rem auto 0;
        padding: 0 2rem;
        position: relative;
        z-index: 10;
    }

    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        margin-bottom: 2rem;
    }

    .video-wrapper iframe,
    .video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .interview-content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 4rem;
        max-width: 1200px;
        margin: 4rem auto;
        padding: 0 2rem;
    }

    .bio-card {
        background: var(--card-bg, #fff);
        padding: 2rem;
        border-radius: 12px;
        border: 1px solid var(--border-color, #e9ecef);
        position: sticky;
        top: 100px;
    }

    .bio-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
    }

    /* Dark mode overrides */
    body.dark-mode .bio-card {
        background: var(--dark-card-bg);
        border-color: var(--dark-border-color);
    }
    
    @media (max-width: 768px) {
        .interview-content-grid { grid-template-columns: 1fr; gap: 2rem; }
        .bio-card { position: relative; top: 0; }
    }
</style>

<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="<?php echo esc_url( home_url('/') ); ?>">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
            </a>
            <h1>Training Hub</h1>
        </div>
        
        <div class="header-actions">
            <div class="auth-only" style="display: none;">
                <div class="header-icons">
                    <button class="header-button" id="notificationsButton" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </button>
                    <div class="profile-dropdown-trigger">
                         <button class="header-button menu-button">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12h18M3 6h18M3 18h18"/>
                            </svg>
                         </button>
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

    <section class="interview-hero">
        <div class="welcome-content">
            <span class="welcome-badge" style="opacity: 1; transform: none;">Video Interview</span>
            
            <h2 style="opacity: 1; transform: none;"><?php the_title(); ?></h2>
            
            <?php if (has_excerpt()) : ?>
                <p style="opacity: 1; transform: none; max-width: 700px; margin: 0 auto;"><?php echo get_the_excerpt(); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <div class="interview-container">
        <div class="video-wrapper">
            <?php 
                $video_url = get_post_meta( get_the_ID(), 'video_url', true ); 
                if ($video_url) : 
            ?>
                <video src="<?php echo esc_url($video_url); ?>" controls controlsList="nodownload" style="width:100%; height:100%;"></video>
            <?php else : ?>
                <video 
                    src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/videos/AidData Training Overview Video.mp4" 
                    poster="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/training_overview_thumbnail.png"
                    controls
                    preload="metadata">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>
        </div>
    </div>

    <div class="interview-content-grid">
        
        <div class="interview-main-text">
            <h3>About this Discussion</h3>
            <div class="content-body">
                <?php 
                // Display the main page content
                if ( have_posts() ) {
                    while ( have_posts() ) {
                        the_post();
                        the_content(); 
                    }
                }
                ?>
            </div>
        </div>

        <aside class="interview-sidebar">
            <div class="bio-card">
                <h3>The Speaker</h3>
                <div class="contact-person" style="display:flex; align-items:center; gap:1rem; margin-top:1.5rem;">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/sethu_nguna.png" alt="Speaker" class="bio-avatar">
                    <div>
                        <h5 style="margin:0; font-size:1.1rem; color:var(--primary-color);">Guest Name</h5>
                        <p style="margin:0; font-size:0.9rem; color:var(--light-text);">Title / Organization</p>
                    </div>
                </div>
                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">
                
                <h4>Key Topics</h4>
                <ul style="padding-left: 1.2rem; color: var(--text-color); margin-bottom: 2rem;">
                    <li>Data Driven Policy</li>
                    <li>Global Development</li>
                    <li>Financial Reporting</li>
                </ul>

                <button class="primary-button" style="width:100%; justify-content:center;">Download Transcript</button>
            </div>
        </aside>
    </div>

    <section class="featured-content" style="background: var(--footer-bg); padding: 4rem 0;">
        <div class="header-content" style="display:block; text-align:center; margin-bottom:3rem;">
            <h3>More Interviews</h3>
        </div>
        
        <div class="featured-grid" style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; padding: 0 2rem;">
            <div class="featured-course">
                <div class="preview-container">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_journalism_pakistan.jpg" alt="Related" class="preview-image">
                    <div class="preview-overlay"></div>
                </div>
                <div class="preview-content">
                    <div class="course-tags">
                        <span class="tag">Interview</span>
                    </div>
                    <h3>Data Journalism in Pakistan</h3>
                    <p>A conversation with local journalists.</p>
                </div>
            </div>

            <div class="featured-course">
                <div class="preview-container">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/seth_goodman_workshop.jpg" alt="Related" class="preview-image">
                    <div class="preview-overlay"></div>
                </div>
                <div class="preview-content">
                    <div class="course-tags">
                        <span class="tag">Interview</span>
                    </div>
                    <h3>Remote Sensing Workshop</h3>
                    <p>Insights from Dr. Seth Goodman.</p>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
