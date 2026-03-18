<?php
/**
 * Template Name: Interview Template
 * Template Post Type: page
 *
 * A custom page template for interview content
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

get_header();

// Enqueue LMS styles
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', [], '1.0.0');

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', [], '1.0.0');

// Enqueue loading screen styles
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', [], '1.0.0');

// Enqueue modals styles
wp_enqueue_style('modals-styles', get_template_directory_uri() . '/assets/css/modals.css', [], '1.0.0');

// Enqueue LMS JavaScript
wp_enqueue_script('lms-script', get_template_directory_uri() . '/assets/js/lms.js', [], '1.0.0', true);

// Enqueue modals JavaScript
wp_enqueue_script('modals-script', get_template_directory_uri() . '/assets/js/modals.js', [], '1.0.0', true);

?>

<!-- Scrollbar Styling -->
<style>
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
    }
</style>

<!-- Loading Screen -->
<div class="loading-screen">
    <!-- Streamlined Triangles Background -->
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

    <!-- ==================== HARBORING GLOBAL AMBITIONS: INTERVIEW ==================== -->
    <div style="max-width: 800px; margin: 0 auto 100px; padding: 40px 20px; position: relative;">
        <div style="margin-bottom: 32px; position: relative;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
                <span style="font-size: 12px; font-weight: 500; color: #6c757d; text-transform: uppercase; letter-spacing: 1px;">Interview</span>
            </div>
            <h1 style="font-size: 32px; font-weight: 400; color: #115740; line-height: 1.2; letter-spacing: -0.02em; margin: 0;">Harboring Global Ambitions</h1>
            <p style="font-size: 19px; line-height: 1.5; color: #556070; margin: 12px 0 0 0;">Using Development Finance Data to Analyze Military Expansion with Alex Wooley</p>
        </div>

        <!-- Video Section -->
        <div style="margin-bottom: 40px; position: relative; background: #f8f9fa; border: 1px solid #e9ecef; border-bottom: 3px solid #115740; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; padding: 40px;">
                <div style="font-size: 14px; color: #6c757d; margin-bottom: 8px;">Interview Video</div>
                <div style="font-size: 13px; color: #adb5bd;">[Video embed will be placed here]</div>
            </div>
        </div>

        <!-- Interview Description -->
        <p style="font-size: 17px; line-height: 1.7; color: #333333; margin: 0 0 24px 0;">In this interview, we explore how AidData's Global Chinese Development Finance Dataset enables analysis of China's strategic military expansion through port infrastructure investments worldwide. Learn how development finance data can reveal patterns in dual-use infrastructure and inform national security assessments.</p>

        <!-- Interviewee Section -->
        <div style="position: relative; margin-bottom: 48px;">
            <div style="font-size: 12px; font-weight: 500; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 24px;">About the Interviewee</div>
            <div style="background: #f8f9fa; border-left: 3px solid #115740; padding: 24px; display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap;">
                <div style="flex: 0 0 150px; min-width: 150px;">
                    <img style="width: 100%; height: auto; border-radius: 8px; border: 1px solid #e9ecef;" src="https://cdn.prod.website-files.com/591515887c47180f72a5c58e/5a25ca8ba92a63000100713f_IMG_1809--large-cropped-3x2.jpg" alt="Alex Wooley" />
                </div>
                <div style="flex: 1 1 300px;">
                    <div style="font-size: 18px; font-weight: 500; color: #115740; margin-bottom: 4px;">Alex Wooley</div>
                    <div style="font-size: 14px; color: #6c757d; margin-bottom: 12px;">Director of Partnerships and Communications</div>
                    <p style="font-size: 15px; line-height: 1.6; color: #333333; margin: 0;">Alex is Director of Partnerships and Communications at AidData, responsible for building strategic partnerships, external communications, and fundraising. He is the lead author of a recent AidData report, Harboring Global Ambitions, that examines China's global ports footprint and the implications for future naval bases.</p>
                </div>
            </div>
        </div>

        <!-- Related Report -->
        <div style="position: relative; margin-bottom: 48px;">
            <div style="font-size: 12px; font-weight: 500; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 24px;">Related Research</div>
            <div style="background: #f8f9fa; border-left: 3px solid #115740; padding: 20px; margin-bottom: 20px; display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                <div style="flex: 1 1 300px;">
                    <div style="font-size: 14px; font-weight: 500; color: #115740; margin-bottom: 8px;">Harboring Global Ambitions Report</div>
                    <p style="font-size: 15px; line-height: 1.6; color: #333333; margin: 0 0 12px 0;">This comprehensive analysis examines China's strategic use of port infrastructure investments as dual-use facilities that serve both commercial and military purposes. Using the GCDF dataset, researchers mapped patterns of Chinese investment in strategically located ports and their potential implications for global military positioning.</p>
                    <a style="font-size: 14px; color: #115740; text-decoration: none; font-weight: 500;" href="https://www.aiddata.org/publications/harboring-global-ambitions" target="_blank" rel="noopener">Read Harboring Global Ambitions →</a>
                </div>
                <div style="flex: 0 0 200px; min-width: 200px;">
                    <img style="width: 100%; height: auto; border-radius: 8px; border: 1px solid #e9ecef;" src="https://cdn.prod.website-files.com/591515887c47180f72a5c58e/64bf6eb0b12d7b719415ec98_Harboring%20Global%20Ambitions%20hero.png" alt="Harboring Global Ambitions report visualization" />
                </div>
            </div>
        </div>

        <!-- Related Blog Posts -->
        <div style="position: relative; margin-bottom: 48px;">
            <div style="font-size: 12px; font-weight: 500; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 24px;">Related Blog Posts</div>

            <div style="background: #ffffff; border: 1px solid #e9ecef; padding: 20px; margin-bottom: 16px; display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                <div style="flex: 1 1 300px;">
                    <div style="font-size: 15px; font-weight: 500; color: #115740; margin-bottom: 8px;">China's divorce from Venezuela wasn't caused by the US. It already happened a decade ago.</div>
                    <p style="font-size: 14px; line-height: 1.6; color: #556070; margin: 0 0 10px 0;">After a brief period of closer ties with Caracas, Beijing sought to de-risk and diversify its state-backed lending away from Venezuela, long before Maduro's capture.</p>
                    <a style="font-size: 14px; color: #115740; text-decoration: none; font-weight: 500;" href="https://www.aiddata.org/blog/chinas-divorce-from-venezuela-wasnt-caused-by-the-us" target="_blank" rel="noopener">Read more →</a>
                </div>
                <div style="flex: 0 0 150px; min-width: 150px;">
                    <img style="width: 100%; height: auto; border-radius: 8px; border: 1px solid #e9ecef;" src="https://cdn.prod.website-files.com/591515887c47180f72a5c58e/696ad12b9c7ac546e2007680_2026_01_16_China%27s%20divorce%20with%20Venezuela.jpeg" alt="China's divorce from Venezuela wasn't caused by the US. It already happened a decade ago." />
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e9ecef; padding: 20px; margin-bottom: 16px; display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                <div style="flex: 1 1 300px;">
                    <div style="font-size: 15px; font-weight: 500; color: #115740; margin-bottom: 8px;">Terrorist designations tend to mobilize foreign aid—but not in Yemen, study finds</div>
                    <p style="font-size: 14px; line-height: 1.6; color: #556070; margin: 0 0 10px 0;">Understanding why Yemen is an exception to this counterintuitive global pattern is crucial for balancing security with survival.</p>
                    <a style="font-size: 14px; color: #115740; text-decoration: none; font-weight: 500;" href="https://www.aiddata.org/blog/terrorist-designations-tend-to-mobilize-foreign-aid--but-not-in-yemen-study-finds" target="_blank" rel="noopener">Read more →</a>
                </div>
                <div style="flex: 0 0 150px; min-width: 150px;">
                    <img style="width: 100%; height: auto; border-radius: 8px; border: 1px solid #e9ecef;" src="https://cdn.prod.website-files.com/591515887c47180f72a5c58e/690171c8d83647bfeb9bdce2_2025_10_Terrorism%20designations%20mobilize%20aid-p-500.png" alt="Terrorist designations tend to mobilize foreign aid—but not in Yemen, study finds" />
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e9ecef; padding: 20px; margin-bottom: 16px; display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                <div style="flex: 1 1 300px;">
                    <div style="font-size: 15px; font-weight: 500; color: #115740; margin-bottom: 8px;">How China is financing its domestic shipbuilding and vessel acquisition industry</div>
                    <p style="font-size: 14px; line-height: 1.6; color: #556070; margin: 0 0 10px 0;">As Beijing leads in building commercial and military ships worldwide, it is using development finance as a tool to export its ships to low- and middle-income countries.</p>
                    <a style="font-size: 14px; color: #115740; text-decoration: none; font-weight: 500;" href="https://www.aiddata.org/blog/how-china-is-financing-its-domestic-shipbuilding-and-vessel-acquisition-industry" target="_blank" rel="noopener">Read more →</a>
                </div>
                <div style="flex: 0 0 150px; min-width: 150px;">
                    <img style="width: 100%; height: auto; border-radius: 8px; border: 1px solid #e9ecef;" src="https://cdn.prod.website-files.com/591515887c47180f72a5c58e/680a6505758f21e7100c332d_2025_04_24%20How%20China%20is%20financing%20its%20domestic%20shipbuilding-p-500.png" alt="How China is financing its domestic shipbuilding and vessel acquisition industry" />
                </div>
            </div>
        </div>

        <!-- Topics/Tags -->
        <div style="position: relative; margin-bottom: 24px;">
            <div style="font-size: 12px; font-weight: 500; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">Topics</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span style="background: #f8f9fa; color: #115740; padding: 6px 14px; border-radius: 4px; font-size: 13px; font-weight: 500; border: 1px solid #e9ecef;">Sovereign Finance</span>
                <span style="background: #f8f9fa; color: #115740; padding: 6px 14px; border-radius: 4px; font-size: 13px; font-weight: 500; border: 1px solid #e9ecef;">National Security</span>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div class="signup-modal" id="signupModal" role="dialog" aria-hidden="true" aria-labelledby="signupModalTitle">
        <div class="signup-container">
            <button class="close-signup" aria-label="Close">&times;</button>
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
</main>


<?php
get_footer();
?>

<!-- Loading Screen Control -->
<script>
// Hide loading screen smoothly when page is ready
(function() {
    function hideLoadingScreen() {
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            // Add fade-out class
            loadingScreen.classList.add('fade-out');

            // Add loaded class to body to show main content
            document.body.classList.add('loaded');

            // Remove loading screen from DOM after transition
            setTimeout(function() {
                loadingScreen.remove();
            }, 500);
        }
    }

    // Hide when page is fully loaded
    if (document.readyState === 'complete') {
        hideLoadingScreen();
    } else {
        window.addEventListener('load', hideLoadingScreen);
    }

    // Fallback: hide after 3 seconds maximum
    setTimeout(hideLoadingScreen, 3000);
})();
</script>
