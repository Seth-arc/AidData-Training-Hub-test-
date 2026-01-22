<?php
/**
 * Archive Interview Template
 * 
 * Displays a list of all interviews
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 * @since 1.0.0
 */

$user_id = get_current_user_id();
$is_logged_in = is_user_logged_in();

// Enqueue necessary styles and scripts
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');
wp_enqueue_style('modals-styles', get_template_directory_uri() . '/assets/css/modals.css', array(), '1.0.0');
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_script('lms-script', get_template_directory_uri() . '/assets/js/lms.js', array(), '1.0.0', true);
wp_enqueue_script('modals-script', get_template_directory_uri() . '/assets/js/modals.js', array(), '1.0.0', true);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Interviews - <?php bloginfo('name'); ?></title>
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
        --dark-text: #1a1a1a;
        --light-text: #666;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }

    body {
        background-color: #f5f5f5;
        color: #333;
        line-height: 1.6;
        padding-top: 90px;
    }

    .interviews-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #026447;
        margin-bottom: 1rem;
    }

    .page-description {
        font-size: 1.1rem;
        color: #666;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .interviews-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .interview-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    
    .interview-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(2, 100, 71, 0.15);
    }
    
    .interview-thumbnail {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        background: linear-gradient(135deg, #026447 0%, #04a971 100%);
        overflow: hidden;
    }
    
    .interview-thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .play-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .interview-card:hover .play-overlay {
        background: white;
        transform: translate(-50%, -50%) scale(1.1);
    }

    .play-overlay svg {
        width: 24px;
        height: 24px;
        fill: #026447;
        margin-left: 3px;
    }
    
    .interview-content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .interview-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #026447;
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }
    
    .interview-title a {
        color: #026447;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .interview-title a:hover {
        color: #04a971;
    }
    
    .interview-meta {
        display: flex;
        gap: 1rem;
        color: #7f8c8d;
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
    }
    
    .interview-meta span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .interview-excerpt {
        color: #5a6c7d;
        font-size: 0.9rem;
        line-height: 1.6;
        flex: 1;
    }

    .interview-experts-list {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e0e0e0;
    }

    .experts-label {
        font-size: 0.8rem;
        color: #7f8c8d;
        margin-bottom: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .expert-avatars {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .expert-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #026447 0%, #04a971 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .expert-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-interviews {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .no-interviews h2 {
        font-size: 1.5rem;
        color: #026447;
        margin-bottom: 1rem;
    }
    
    .no-interviews p {
        color: #666;
        font-size: 1rem;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 3rem;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        color: #026447;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #026447;
        color: white;
        border-color: #026447;
    }

    .pagination .current {
        background: #026447;
        color: white;
        border-color: #026447;
    }

    @media (max-width: 768px) {
        .interviews-container {
            padding: 1.25rem 0.625rem;
        }

        .page-title {
            font-size: 2rem;
        }
        
        .interviews-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    @media (max-width: 640px) {
        body {
            padding-top: 70px;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .page-description {
            font-size: 1rem;
        }
    }
</style>

<div class="interviews-container">
    <div class="page-header">
        <h1 class="page-title">Expert Interview Series</h1>
        <p class="page-description">
            Explore in-depth conversations with leading experts in development finance, data analysis, 
            and international development. Gain valuable insights and perspectives from practitioners 
            and researchers in the field.
        </p>
    </div>

    <?php if (have_posts()): ?>
            <div class="interviews-grid">
            <?php while (have_posts()): the_post(); 
                $video_url = get_post_meta(get_the_ID(), '_interview_video_url', true);
                $duration = get_post_meta(get_the_ID(), '_interview_duration', true);
                $recorded_date = get_post_meta(get_the_ID(), '_interview_recorded_date', true);
                $interviewees = get_post_meta(get_the_ID(), '_interview_experts', true);
                
                if (empty($interviewees)) {
                    $interviewees = array();
                }
                ?>
                    <article class="interview-card">
                    <a href="<?php the_permalink(); ?>" class="interview-thumbnail">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large'); ?>
                            <?php endif; ?>
                            <div class="play-overlay">
                            <svg viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                    </a>
                        
                        <div class="interview-content">
                            <h2 class="interview-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                        <?php if (!empty($duration) || !empty($recorded_date)): ?>
                            <div class="interview-meta">
                                <?php if (!empty($duration)): ?>
                                    <span><?php echo esc_html($duration); ?></span>
                                    <?php endif; ?>
                                <?php if (!empty($duration) && !empty($recorded_date)): ?>
                                    <span>•</span>
                                    <?php endif; ?>
                                <?php if (!empty($recorded_date)): ?>
                                    <span><?php echo esc_html($recorded_date); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                        <div class="interview-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </div>
                        
                        <?php if (!empty($interviewees)): ?>
                            <div class="interview-experts-list">
                                <div class="experts-label">Featured Expert<?php echo count($interviewees) > 1 ? 's' : ''; ?></div>
                                <div class="expert-avatars">
                                    <?php foreach ($interviewees as $expert): ?>
                                        <div class="expert-avatar" title="<?php echo esc_attr($expert['name']); ?>">
                                            <?php if (!empty($expert['photo'])): ?>
                                                <img src="<?php echo esc_url($expert['photo']); ?>" alt="<?php echo esc_attr($expert['name']); ?>">
                                            <?php else: ?>
                                                <?php 
                                                // Generate initials
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
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
        <?php
        // Pagination
        $big = 999999999;
        $pagination = paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages,
            'prev_text' => '← Previous',
            'next_text' => 'Next →',
            'type' => 'list'
        ));
        
        if ($pagination) {
            echo '<div class="pagination">' . $pagination . '</div>';
        }
        ?>
    <?php else: ?>
        <div class="no-interviews">
            <h2>No Interviews Found</h2>
            <p>Check back soon for expert interviews and insights.</p>
            </div>
        <?php endif; ?>
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

<?php wp_footer(); ?>
</body>
</html>

