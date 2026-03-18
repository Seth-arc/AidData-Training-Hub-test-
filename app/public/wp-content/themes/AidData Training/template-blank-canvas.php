<?php
/**
 * Template Name: Blank Canvas
 * Template Post Type: page
 *
 * A blank template with front-page header and footer.
 * Everything between can be customized with your own HTML/CSS/JS.
 *
 * @package WordPress
 * @subpackage AidData_Training
 * @since 1.0
 */

get_header();

// Enqueue necessary styles
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');
wp_enqueue_style('modals-styles', get_template_directory_uri() . '/assets/css/modals.css', array(), '1.0.0');
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');

// Enqueue necessary scripts
wp_enqueue_script('lms-script', get_template_directory_uri() . '/assets/js/lms.js', array('jquery'), '1.0.0', true);
wp_enqueue_script('modals-script', get_template_directory_uri() . '/assets/js/modals.js', array('jquery'), '1.0.0', true);
?>

<!-- Scrollbar Styling -->
<style>
    :root {
        --blank-canvas-header-offset: 96px;
    }

    /* Webkit browsers (Chrome, Safari, newer versions of Opera and Edge) */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #ffffff;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #026447;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #004E38;
    }

    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #026447 #ffffff;
    }

    html {
        scroll-padding-top: var(--blank-canvas-header-offset);
    }

    .admin-bar .lms-header {
        top: 32px;
    }

    @media screen and (max-width: 782px) {
        .admin-bar .lms-header {
            top: 46px;
        }
    }

    /* Ensure content is not hidden under the fixed header */
    .lms-main {
        padding-top: var(--blank-canvas-header-offset);
        min-height: calc(100vh - var(--blank-canvas-header-offset));
        background: #ffffff;
    }
</style>

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
    <!-- ========================================
         PASTE YOUR CUSTOM CONTENT HERE
         ======================================== -->

    <?php
    // This displays the WordPress page content if you want to use it
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>

    <!-- ========================================
         END CUSTOM CONTENT AREA
         ======================================== -->
</main>


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

<script>
    (function() {
        function updateBlankCanvasHeaderOffset() {
            const header = document.querySelector('.lms-header');

            if (!header) {
                return;
            }

            const headerTop = parseFloat(window.getComputedStyle(header).top) || 0;
            const totalOffset = Math.ceil(header.offsetHeight + headerTop);

            document.documentElement.style.setProperty('--blank-canvas-header-offset', totalOffset + 'px');
        }

        function initBlankCanvasHeaderOffset() {
            updateBlankCanvasHeaderOffset();

            window.addEventListener('resize', updateBlankCanvasHeaderOffset);

            if (typeof ResizeObserver !== 'undefined') {
                const header = document.querySelector('.lms-header');

                if (header) {
                    const resizeObserver = new ResizeObserver(updateBlankCanvasHeaderOffset);
                    resizeObserver.observe(header);
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBlankCanvasHeaderOffset);
        } else {
            initBlankCanvasHeaderOffset();
        }
    })();
</script>

<?php
get_footer();
?>
