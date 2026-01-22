<?php
/**
 * Single Interview Template
 * 
 * Displays a single interview with video player, expert information, and navigation
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 * @since 1.0.0
 */

// Get current interview
global $post;
$user_id = get_current_user_id();
$is_logged_in = is_user_logged_in();

// Get interview data from custom fields
$video_url = get_post_meta($post->ID, '_interview_video_url', true);
$duration = get_post_meta($post->ID, '_interview_duration', true);
$recorded_date = get_post_meta($post->ID, '_interview_recorded_date', true);
$interviewees = get_post_meta($post->ID, '_interview_experts', true);

if (empty($interviewees)) {
    $interviewees = array();
}

// Enqueue necessary styles and scripts
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');
wp_enqueue_style('modals-styles', get_template_directory_uri() . '/assets/css/modals.css', array(), '1.0.0');
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_script('lms-script', get_template_directory_uri() . '/assets/js/lms.js', array(), '1.0.0', true);
wp_enqueue_script('modals-script', get_template_directory_uri() . '/assets/js/modals.js', array(), '1.0.0', true);

// Localize script with interview data
wp_localize_script('lms-script', 'interviewData', array(
    'interviewId' => $post->ID,
    'userId' => $user_id,
    'isLoggedIn' => $is_logged_in,
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('interview_nonce')
));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(get_the_title()); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- Header matching tutorial pages -->
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

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    :root {
        --primary-color: #026447;
        --primary-dark: #004E38;
        --accent-color: #04a971;
        --dark-text: #2c3e50;
        --light-text: #7f8c8d;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        color: #2c3e50;
    line-height: 1.6;
        min-height: 100vh;
        padding-top: 90px; /* Account for fixed header */
    }

    .interview-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .main-content {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .video-section {
        padding: 40px;
    }

    .video-player {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
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
    object-fit: cover;
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

    .experts-toggle-section {
        margin: 20px 0;
    }

    .experts-toggle {
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

    .experts-toggle:hover {
        background: #e8ecf1;
        border-color: #026447;
    }

    .experts-toggle-icon {
        transition: transform 0.3s ease;
        font-size: 1.2rem;
    }

    .experts-toggle.active .experts-toggle-icon {
        transform: rotate(180deg);
    }

    .experts-drawer {
        background: #f8fafc;
        border: 1px solid #e8ecf1;
        border-top: none;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.4s ease;
    }

    .experts-drawer.active {
        max-height: 800px;
    }

    .experts-content {
        padding: 32px;
        overflow-y: auto;
        max-height: 700px;
    display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 32px;
    }

    .expert-card {
        text-align: center;
    }

    .expert-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #026447 0%, #04a971 100%);
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        font-weight: 300;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .expert-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .expert-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #026447;
        margin-bottom: 12px;
    }

    .expert-title {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .expert-bio {
        color: #5a6c7d;
        line-height: 1.7;
        font-size: 0.9rem;
        text-align: left;
    }

    .video-info {
        margin-top: 1.25rem;
    }

    .video-info h1 {
        font-size: 1.75rem;
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

    .video-meta span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .video-description {
        color: #5a6c7d;
    font-size: 0.95rem;
        line-height: 1.7;
    }

    @media (max-width: 768px) {
        .interview-container {
            padding: 20px 10px;
        }

        .video-section {
            padding: 20px;
        }

        .experts-content {
            grid-template-columns: 1fr;
            gap: 24px;
            padding: 20px;
            max-height: 500px;
        }

        .expert-image {
            width: 100px;
            height: 100px;
            font-size: 2rem;
        }

        .video-info h1 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 640px) {
        body {
            padding-top: 70px;
        }

        .video-info h1 {
            font-size: 1.25rem;
        }

        .interview-container {
            padding: 20px 10px;
        }
    }
</style>

<div class="interview-container">
    <div class="main-content">
        <div class="video-section">
            <div class="video-player">
                <?php if (!empty($video_url)): ?>
                    <?php
                    // Check if it's a YouTube or Vimeo URL
                    if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                        // Extract YouTube video ID
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $video_url, $match);
                        if (!empty($match[1])) {
                            echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($match[1]) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                        }
                    } elseif (strpos($video_url, 'vimeo.com') !== false) {
                        // Extract Vimeo video ID
                        preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $video_url, $match);
                        if (!empty($match[1])) {
                            echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($match[1]) . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                        }
                    } else {
                        // Direct video file
                        echo '<video controls><source src="' . esc_url($video_url) . '" type="video/mp4">Your browser does not support the video tag.</video>';
                    }
                    ?>
                <?php else: ?>
                    <div class="video-placeholder">
                        <span>Video Player</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($interviewees)): ?>
            <div class="experts-toggle-section">
                <button class="experts-toggle" id="expertsToggle">
                    <span>About the Expert<?php echo count($interviewees) > 1 ? 's' : ''; ?></span>
                    <span class="experts-toggle-icon">▼</span>
                </button>
                <div class="experts-drawer" id="expertsDrawer">
                    <div class="experts-content">
                        <?php foreach ($interviewees as $expert): ?>
                            <div class="expert-card">
                                <div class="expert-image">
                                    <?php if (!empty($expert['photo'])): ?>
                                        <img src="<?php echo esc_url($expert['photo']); ?>" alt="<?php echo esc_attr($expert['name']); ?>">
                                    <?php else: ?>
                                        <?php 
                                        // Generate initials from name
                                        $name_parts = explode(' ', $expert['name']);
                                        $initials = '';
                                        foreach ($name_parts as $part) {
                                            $initials .= strtoupper(substr($part, 0, 1));
                                            if (strlen($initials) >= 2) break;
                                        }
                                        echo esc_html($initials);
                                        ?>
                                    <?php endif; ?>
                                </div>
                                <div class="expert-name"><?php echo esc_html($expert['name']); ?></div>
                                <?php if (!empty($expert['title'])): ?>
                                    <div class="expert-title"><?php echo esc_html($expert['title']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($expert['bio'])): ?>
                                    <div class="expert-bio"><?php echo wp_kses_post($expert['bio']); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="video-info">
                <h1><?php echo esc_html(get_the_title()); ?></h1>
                <div class="video-meta">
                    <?php if (!empty($duration)): ?>
                        <span><?php echo esc_html($duration); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($duration) && !empty($recorded_date)): ?>
                        <span>•</span>
                    <?php endif; ?>
                    <?php if (!empty($recorded_date)): ?>
                        <span>Recorded <?php echo esc_html($recorded_date); ?></span>
                    <?php endif; ?>
                </div>
                <div class="video-description">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const expertsToggle = document.getElementById('expertsToggle');
    const expertsDrawer = document.getElementById('expertsDrawer');

    if (expertsToggle && expertsDrawer) {
        expertsToggle.addEventListener('click', function() {
            expertsToggle.classList.toggle('active');
            expertsDrawer.classList.toggle('active');
        });
    }
});
</script>

<!-- Notification Modal -->
<div class="notification-modal" id="notificationModal">
    <div class="notification-container">
        <button class="close-notifications" aria-label="Close">&times;</button>
        <div class="notification-header">
            <h3>Notifications</h3>
        </div>
        <div class="notification-list">
            <div class="notification-item">
                <div class="notification-content">
                    <p>Welcome to AidData Training Hub</p>
                    <span class="notification-time">Just now</span>
                </div>
            </div>
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

<?php wp_footer(); ?>
</body>
</html>

