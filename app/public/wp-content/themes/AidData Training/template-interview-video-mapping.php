<?php
/**
 * Template Name: Interview - Mapping China's Port Investments (Single Video)
 * Template Post Type: page
 *
 * A single interview video page modeled after the Mapping China's Port Investments tutorial layout.
 */

// Bootstrap WordPress header
get_header();

// Enqueue styles consistent with the front page header & video player
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');
wp_enqueue_style('video-player', get_template_directory_uri() . '/assets/css/video-player.css', array(), '1.0.0');

// Front page navbar behaviors
wp_enqueue_script('lms-script', get_template_directory_uri() . '/assets/js/lms.js', array(), '1.0.0', true);
wp_enqueue_script('modals-script', get_template_directory_uri() . '/assets/js/modals.js', array(), '1.0.0', true);
wp_enqueue_script('page-transitions', get_template_directory_uri() . '/assets/js/page-transitions.js', array(), '1.0.0', true);
wp_enqueue_script('video-player', get_template_directory_uri() . '/assets/js/video-player.js', array(), '1.0.0', true);

global $post;
$video_url = get_post_meta($post->ID, 'video_url', true);
$subtitle = get_post_meta($post->ID, 'subtitle', true);
$duration = get_post_meta($post->ID, 'duration', true);
$recorded_date = get_post_meta($post->ID, 'recorded_date', true);
$experts = get_post_meta($post->ID, 'experts', true);
if (empty($experts)) { $experts = array(); }
// Optional transcript custom field
$transcript = get_post_meta($post->ID, 'transcript', true);

// Fallback to local video if no custom URL provided
if (empty($video_url)) {
    $video_url = get_template_directory_uri() . '/assets/videos/HARBORING GLOBAL AMBITIONS.mp4';
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    body,
    h1, h2, h3, h4, h5, h6,
    p, span, div, a, button,
    input, textarea, select, label,
    li, td, th {
        font-family: 'Inter' !important;
    }

    .hero-interview {
        background: linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.25)),
                    url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/interview_background.png');
        background-size: cover;
        background-position: center;
        color: #ffffff;
        padding: 4rem 2rem;
    }
    .hero-interview .hero-content { max-width: 1200px; margin: 0 auto; }
    .hero-interview .breadcrumb { display: flex; gap: .5rem; margin-bottom: 1.5rem; font-size: .9rem; opacity: .95; }
    .hero-interview .breadcrumb a { color: #ffffff; text-decoration: none; }
    .hero-interview .breadcrumb a:hover { text-decoration: underline; }
    .hero-interview .title { font-size: 2.25rem; font-weight: 700; margin-bottom: .5rem; color: #ffffff; }
    .hero-interview .subtitle { font-size: 1.1rem; opacity: .96; max-width: 800px; }

    .interview-main { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; display: grid; grid-template-columns: 1fr 340px; gap: 2.5rem; }
    .content-card { background: #ffffff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
    .section-heading { font-size: 1.25rem; font-weight: 700; color: #004E38; margin-bottom: 1rem; border-bottom: 2px solid #e6e6e6; padding-bottom: .6rem; }
    .meta-row { display: flex; gap: 1rem; color: #6b7280; font-size: .95rem; margin: .75rem 0 1rem; }

    .player-wrapper { position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #000; }
    .player-16x9 { padding-bottom: 56.25%; }
    .player-wrapper iframe,
    .player-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .player-wrapper video { object-fit: cover; object-position: center; }

    .expert-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; }
    .expert-card { background: #f8fafc; border: 1px solid #e8ecf1; border-radius: 10px; padding: 1rem; text-align: center; }
    .expert-photo { width: 96px; height: 96px; border-radius: 50%; overflow: hidden; margin: 0 auto .75rem; background: linear-gradient(135deg, #026447, #04a971); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; }
    .expert-photo img { width: 100%; height: 100%; object-fit: cover; }
    .expert-name { font-weight: 700; color: #026447; margin-bottom: .25rem; }
    .expert-title { color: #6b7280; font-size: .9rem; margin-bottom: .5rem; }

    @media (max-width: 968px) { .interview-main { grid-template-columns: 1fr; } }

    /* Scrollbar Styling (match front page) */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    ::-webkit-scrollbar-thumb { background: #026447; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #004E38; }
    * { scrollbar-width: thin; scrollbar-color: #026447 #f1f1f1; }

    /* Transcript Drawer */
    .transcript-toggle {
        background: #f8fafc;
        border: 1px solid #e8ecf1;
        color: #026447;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        width: 100%;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
    }
    .transcript-toggle:hover { background: #e8ecf1; border-color: #026447; }
    .transcript-toggle-icon { transition: transform 0.3s ease; font-size: 1.1rem; }
    .transcript-toggle.active .transcript-toggle-icon { transform: rotate(180deg); }
    .transcript-drawer {
        background: #f8fafc;
        border: 1px solid #e8ecf1;
        border-top: none;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.4s ease;
    }
    .transcript-drawer.active { max-height: 800px; }
    .transcript-content { padding: 18px; max-height: 700px; overflow-y: auto; color: #4b5563; line-height: 1.7; }
</style>

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

<section class="hero-interview">
    <div class="hero-content">
        <nav class="breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Training Hub</a>
            <span>/</span>
            <a href="<?php echo esc_url(home_url('/interviews/')); ?>">Interviews</a>
            <span>/</span>
            <span><?php echo esc_html(get_the_title()); ?></span>
        </nav>
        <h1 class="title">Harboring Global Ambitions</h1>
        <p class="subtitle">Using Development Finance Data to Analyze Military Expansion with Alex Wooley</p>
    </div>
    </section>

<main class="interview-main">
    <div class="content-column">
        <div class="content-card">
            <div class="meta-row">
                <?php if (!empty($duration)) : ?><span><?php echo esc_html($duration); ?></span><?php endif; ?>
                <?php if (!empty($duration) && !empty($recorded_date)) : ?><span>•</span><?php endif; ?>
                <?php if (!empty($recorded_date)) : ?><span>Recorded <?php echo esc_html($recorded_date); ?></span><?php endif; ?>
            </div>

            <div class="player-wrapper player-16x9">
                <?php if (!empty($video_url)) : ?>
                    <?php
                    if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $video_url, $match);
                        if (!empty($match[1])) {
                            echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($match[1]) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                        }
                    } elseif (strpos($video_url, 'vimeo.com') !== false) {
                        preg_match('/vimeo\.com\/(?:channels\/(?:\\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $video_url, $match);
                        if (!empty($match[1])) {
                            echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($match[1]) . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                        }
                    } else {
                        echo '<video controls preload="metadata" poster="' . esc_url( get_template_directory_uri() ) . '/assets/images/mapping_chinas_port_investments_page_interview_image.PNG"><source src="' . esc_url($video_url) . '" type="video/mp4">Your browser does not support the video tag.</video>';
                    }
                    ?>
                <?php else : ?>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;">Add a valid video URL in Custom Fields.</div>
                <?php endif; ?>
            </div>

            <!-- Transcript Drawer (directly below the video, not inside it) -->
            <div class="transcript-section">
                <button class="transcript-toggle" id="transcriptToggle">
                    <span>Transcript</span>
                    <span class="transcript-toggle-icon">▼</span>
                </button>
                <div class="transcript-drawer" id="transcriptDrawer">
                    <div class="transcript-content">
                        <?php if (!empty($transcript)) : ?>
                            <?php echo wp_kses_post($transcript); ?>
                        <?php else : ?>
                            <p>Transcript coming soon.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="section-content" style="margin-top:1rem; color:#4b5563;">
                <?php the_content(); ?>
            </div>
        </div>

        <?php if (!empty($experts)) : ?>
        <div class="content-card">
            <h2 class="section-heading">About the Expert<?php echo count($experts) > 1 ? 's' : ''; ?></h2>
            <div class="expert-grid">
                <?php foreach ($experts as $expert) : ?>
                    <div class="expert-card">
                        <div class="expert-photo">
                            <?php if (!empty($expert['photo'])) : ?>
                                <img src="<?php echo esc_url($expert['photo']); ?>" alt="<?php echo esc_attr($expert['name']); ?>">
                            <?php else : ?>
                                <?php 
                                $initials = '';
                                if (!empty($expert['name'])) {
                                    $parts = explode(' ', $expert['name']);
                                    foreach ($parts as $p) { $initials .= strtoupper(substr($p, 0, 1)); if (strlen($initials) >= 2) break; }
                                }
                                echo esc_html($initials ?: '');
                                ?>
                            <?php endif; ?>
                        </div>
                        <div class="expert-name"><?php echo !empty($expert['name']) ? esc_html($expert['name']) : 'Expert'; ?></div>
                        <?php if (!empty($expert['title'])) : ?><div class="expert-title"><?php echo esc_html($expert['title']); ?></div><?php endif; ?>
                        <?php if (!empty($expert['bio'])) : ?><div class="expert-bio"><?php echo wp_kses_post($expert['bio']); ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <aside class="sidebar">
        <div class="content-card" style="margin-bottom: 1rem;">
            <h3 class="section-heading" style="margin-bottom: .75rem;">AidData Research</h3>
            <div style="margin-bottom: .5rem;">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/Harboring Global Ambitions_policy_report_cover.png" alt="Harboring Global Ambitions Report Cover" style="width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            </div>
            <p style="font-size:.9rem;color:#6b7280;">To learn more, read the report <a href="https://docs.aiddata.org/reports/harboring-global-ambitions/Harboring_Global_Ambitions.html" target="_blank" rel="noopener noreferrer" style="color:#004E38; text-decoration: underline;">here</a>.</p>
        </div>
    </aside>
</main>


<?php get_footer(); ?>

 <script>
 document.addEventListener('DOMContentLoaded', function() {
     const toggle = document.getElementById('transcriptToggle');
     const drawer = document.getElementById('transcriptDrawer');
     if (toggle && drawer) {
         toggle.addEventListener('click', function() {
             toggle.classList.toggle('active');
             drawer.classList.toggle('active');
         });
     }
 });
 </script>

