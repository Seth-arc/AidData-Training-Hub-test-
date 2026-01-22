<?php
/**
 * Template Name: Course Template
 * 
 * Description: A template for course pages based on the Navigating Development Finance layout.
 */

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
    
    // Basic fallback for repeaters - simply check if the meta exists
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
    
    // Simple fallback - this is limited since we can't properly handle repeaters without ACF
    // In a real implementation, you would need to store and manage the current row index
    return $default;
}

get_header();
?>

<main class="lms-main">
    <section class="welcome-section">
        <div class="welcome-content">
            <h2><?php the_title(); ?></h2>
            <div class="course-categories" style="margin-bottom: 20px;">
                <?php if (course_get_field('course_type')) : ?>
                <span class="category-tag tutorial"><?php echo esc_html(course_get_field('course_type')); ?></span>
                <?php endif; ?>
                <?php if (course_get_field('badge_type')) : ?>
                <span class="category-tag badge"><?php echo esc_html(course_get_field('badge_type')); ?></span>
                <?php endif; ?>
            </div>
            <div class="course-stats" style="margin-bottom: 20px;">
                <?php if (course_get_field('course_duration')) : ?>
                <span class="stat">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                        <polyline points="12 6 12 12 16 14" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <?php echo esc_html(course_get_field('course_duration')); ?>
                </span>
                <?php endif; ?>
                <?php if (course_get_field('course_level')) : ?>
                <span class="stat">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <?php echo esc_html(course_get_field('course_level')); ?>
                </span>
                <?php endif; ?>
            </div>
            <button class="learn-more-btn">
                <span>Why This Course?</span>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
        <div class="welcome-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </section>

    <!-- Course Details Section -->
    <section class="course-details">
        <div class="course-container">
            <div class="course-overview">
                <h3><?php echo esc_html__('About This Course', 'twentytwentyfour'); ?></h3>
                
                <!-- Course Trailer Video -->
                <?php if (course_get_field('course_video')) : ?>
                <div class="video-section">
                    <div class="video-container" style="position: relative; width: 100%; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
                        <video 
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                            controls
                            preload="none"
                            poster="<?php echo esc_url(course_get_field('course_poster')); ?>"
                            controlsList="nodownload"
                            oncontextmenu="return false;">
                            <source src="<?php echo esc_url(course_get_field('course_video')); ?>" type="video/mp4">
                            <?php esc_html_e('Your browser does not support the video tag.', 'twentytwentyfour'); ?>
                        </video>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="key-features">
                    <h4><?php echo esc_html__('Who is this Course for?', 'twentytwentyfour'); ?></h4>
                    <?php if (course_get_field('course_audience')) : ?>
                    <ul>
                        <?php 
                        $audience_items = course_get_field('course_audience');
                        if (is_array($audience_items)) {
                            foreach ($audience_items as $item) {
                                echo '<li>' . esc_html($item) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <div class="simulation-structure">
                    <h4><?php echo esc_html__('Course Structure', 'twentytwentyfour'); ?></h4>
                    <div class="structure-grid" style="grid-template-columns: 1fr;">
                        <?php
                        if (course_has_rows('course_modules')) :
                            // If ACF is active, this will work normally
                            if (function_exists('have_rows')) :
                                while (have_rows('course_modules')) : the_row();
                        ?>
                        <div class="structure-item">
                            <h5><?php echo esc_html(get_sub_field('module_title')); ?></h5>
                            <p><?php echo esc_html(get_sub_field('module_description')); ?></p>
                        </div>
                        <?php
                                endwhile;
                            else:
                                // Basic fallback for non-ACF - just display a placeholder
                        ?>
                        <div class="structure-item">
                            <h5><?php echo esc_html__('Module 1', 'twentytwentyfour'); ?></h5>
                            <p><?php echo esc_html__('Please install Advanced Custom Fields plugin for full functionality.', 'twentytwentyfour'); ?></p>
                        </div>
                        <?php
                            endif;
                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <aside class="course-sidebar">
                <div class="instructors-section">
                    <h4><?php echo esc_html__('Course Instructors', 'twentytwentyfour'); ?></h4>
                    <?php
                    if (course_has_rows('course_instructors')) :
                        if (function_exists('have_rows')) :
                            while (have_rows('course_instructors')) : the_row();
                                $instructor_image = get_sub_field('instructor_image');
                    ?>
                    <div class="instructor-card">
                        <?php if ($instructor_image) : ?>
                        <img src="<?php echo esc_url($instructor_image); ?>" alt="<?php echo esc_attr(get_sub_field('instructor_name')); ?>" class="instructor-image">
                        <?php endif; ?>
                        <div class="instructor-info">
                            <h5><?php echo esc_html(get_sub_field('instructor_name')); ?></h5>
                            <p class="instructor-title"><?php echo esc_html(get_sub_field('instructor_title')); ?></p>
                        </div>
                    </div>
                    <?php
                            endwhile;
                        else:
                    ?>
                    <div class="instructor-card">
                        <div class="instructor-info">
                            <h5><?php echo esc_html__('Course Instructor', 'twentytwentyfour'); ?></h5>
                            <p class="instructor-title"><?php echo esc_html__('Please install Advanced Custom Fields plugin for full functionality.', 'twentytwentyfour'); ?></p>
                        </div>
                    </div>
                    <?php
                        endif;
                    endif;
                    ?>
                </div>

                <?php if (course_get_field('partnership_logo') || course_get_field('partnership_text')) : ?>
                <div class="inline-partnership" style="text-align: center; margin: 20px 0;">
                    <?php if (course_get_field('partnership_logo')) : ?>
                    <img src="<?php echo esc_url(course_get_field('partnership_logo')); ?>" alt="<?php echo esc_attr(course_get_field('partnership_name')); ?>" style="height: 40px;">
                    <?php endif; ?>
                    <?php if (course_get_field('partnership_text')) : ?>
                    <p style="margin-top: 10px;"><?php echo esc_html(course_get_field('partnership_text')); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="pricing-card">
                    <div class="price-tag">
                        <span class="currency" style="font-size: 24px;">$</span>
                        <span class="amount" style="font-size: 48px; font-weight: bold;"><?php echo esc_html(course_get_field('course_price', false, '0')); ?></span>
                    </div>
                    <?php if (course_has_rows('price_features')) : ?>
                    <ul class="price-features" style="font-size: 14px;">
                        <?php 
                        if (function_exists('have_rows')) : 
                            while (have_rows('price_features')) : the_row(); 
                        ?>
                        <li><?php echo esc_html(get_sub_field('feature')); ?></li>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <li><?php echo esc_html__('Full course access', 'twentytwentyfour'); ?></li>
                        <li><?php echo esc_html__('Certificate of completion', 'twentytwentyfour'); ?></li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                    
                    <?php if (course_get_field('enroll_button_url')) : ?>
                    <a href="<?php echo esc_url(course_get_field('enroll_button_url')); ?>" target="_blank" rel="noopener noreferrer" class="enroll-button" style="font-size: 14px; text-decoration: none; text-align: center; display: block;">
                        <?php echo esc_html(course_get_field('enroll_button_text') ? course_get_field('enroll_button_text') : 'Start Learning'); ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (course_get_field('scholarship_info')) : ?>
                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(2, 100, 71, 0.1);">
                        <p style="font-size: 14px; color: #555; text-align: center; margin: 0;">
                            <strong style="color: #026447; font-size: 14px;"><?php echo esc_html__('Scholarships Available', 'twentytwentyfour'); ?></strong><br>
                            <?php echo esc_html(course_get_field('scholarship_info')); ?>
                            <br>
                            <?php if (course_get_field('scholarship_email')) : ?>
                            <a href="mailto:<?php echo esc_attr(course_get_field('scholarship_email')); ?>?subject=Scholarship%20Inquiry%20-%20<?php echo esc_attr(get_the_title()); ?>" style="display: inline-block; margin-top: 8px; font-size: 14px;">
                                <?php echo esc_html__('Inquire about scholarships →', 'twentytwentyfour'); ?>
                            </a>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </section>

    <!-- Info Drawer -->
    <div class="info-drawer">
        <div class="drawer-content">
            <button class="close-drawer" aria-label="Close drawer">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
            <div class="drawer-header">
                <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/logo.png')); ?>" alt="Logo" class="drawer-logo">
                <h2 class="drawer-heading"><?php echo esc_html__('Training and Professional Development', 'twentytwentyfour'); ?></h2>
            </div>
            <div class="drawer-grid">
                <div class="drawer-left">
                    <?php if (course_get_field('contact_info_enabled')) : ?>
                    <div class="contact-section">
                        <h4><?php echo esc_html__('Contact Information', 'twentytwentyfour'); ?></h4>
                        <div class="contact-person">
                            <?php if (course_get_field('contact_avatar')) : ?>
                            <img src="<?php echo esc_url(course_get_field('contact_avatar')); ?>" alt="<?php echo esc_attr(course_get_field('contact_name')); ?>" class="contact-avatar">
                            <?php endif; ?>
                            <div class="contact-details">
                                <p><?php echo esc_html(course_get_field('contact_intro')); ?></p>
                                <p><strong><?php echo esc_html(course_get_field('contact_name')); ?></strong>
                                <?php echo esc_html(course_get_field('contact_title')); ?><br>
                                <a href="mailto:<?php echo esc_attr(course_get_field('contact_email')); ?>"><?php echo esc_html(course_get_field('contact_email')); ?></a></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (course_get_field('related_news_enabled')) : ?>
                    <div class="related-news">
                        <h4><?php echo esc_html__('Related News', 'twentytwentyfour'); ?></h4>
                        <div class="news-items">
                            <?php
                            if (course_has_rows('related_news_items')) :
                                if (function_exists('have_rows')) :
                                    while (have_rows('related_news_items')) : the_row();
                            ?>
                            <a href="<?php echo esc_url(get_sub_field('news_url')); ?>" class="news-item" target="_blank">
                                <?php if (get_sub_field('news_image')) : ?>
                                <img src="<?php echo esc_url(get_sub_field('news_image')); ?>" alt="<?php echo esc_attr(get_sub_field('news_title')); ?>">
                                <?php endif; ?>
                                <div class="news-content">
                                    <h5><?php echo esc_html(get_sub_field('news_title')); ?></h5>
                                    <p><?php echo esc_html(get_sub_field('news_excerpt')); ?></p>
                                    <span class="news-date"><?php echo esc_html(get_sub_field('news_date')); ?></span>
                                </div>
                            </a>
                            <?php
                                    endwhile;
                                else:
                            ?>
                            <a href="#" class="news-item">
                                <div class="news-content">
                                    <h5><?php echo esc_html__('Sample News Title', 'twentytwentyfour'); ?></h5>
                                    <p><?php echo esc_html__('Please install Advanced Custom Fields plugin for full functionality.', 'twentytwentyfour'); ?></p>
                                    <span class="news-date"><?php echo esc_html(date_i18n(get_option('date_format'))); ?></span>
                                </div>
                            </a>
                            <?php
                                endif;
                            endif;
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="drawer-right">
                    <div class="info-block">
                        <?php if (course_get_field('course_detailed_title')) : ?>
                        <h2><?php echo esc_html(course_get_field('course_detailed_title')); ?></h2>
                        <?php endif; ?>
                        
                        <?php if (course_get_field('course_detailed_description')) : ?>
                        <p><?php echo esc_html(course_get_field('course_detailed_description')); ?></p>
                        <?php endif; ?>

                        <?php if (course_has_rows('scenario_examples')) : ?>
                        <div class="scenario-examples">
                            <?php 
                            if (function_exists('have_rows')) :
                                while (have_rows('scenario_examples')) : the_row(); 
                            ?>
                            <div class="scenario-card">
                                <?php if (get_sub_field('scenario_image')) : ?>
                                <div class="scenario-image" style="height: 200px; border-radius: 12px; margin-bottom: 16px; position: relative; overflow: hidden;">
                                    <img src="<?php echo esc_url(get_sub_field('scenario_image')); ?>" alt="<?php echo esc_attr(get_sub_field('scenario_title')); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                                </div>
                                <?php endif; ?>
                                <h4 style="color: #026447; font-size: 18px; margin-bottom: 12px;"><?php echo esc_html(get_sub_field('scenario_title')); ?></h4>
                                <p style="font-size: 14px; color: #555; line-height: 1.5;"><?php echo esc_html(get_sub_field('scenario_description')); ?></p>
                            </div>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <div class="scenario-card">
                                <h4 style="color: #026447; font-size: 18px; margin-bottom: 12px;"><?php echo esc_html__('Sample Scenario', 'twentytwentyfour'); ?></h4>
                                <p style="font-size: 14px; color: #555; line-height: 1.5;"><?php echo esc_html__('Please install Advanced Custom Fields plugin for full functionality.', 'twentytwentyfour'); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if (course_get_field('learning_outcomes_title')) : ?>
                        <h3><?php echo esc_html(course_get_field('learning_outcomes_title')); ?></h3>
                        <?php endif; ?>
                        
                        <?php if (course_has_rows('learning_outcomes')) : ?>
                        <ul class="complexity-challenges">
                            <?php 
                            if (function_exists('have_rows')) :
                                while (have_rows('learning_outcomes')) : the_row(); 
                            ?>
                            <li class="complexity-challenge">
                                <strong><?php echo esc_html(get_sub_field('outcome_title')); ?></strong>
                                <p><?php echo esc_html(get_sub_field('outcome_description')); ?></p>
                            </li>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <li class="complexity-challenge">
                                <strong><?php echo esc_html__('Learning Outcome', 'twentytwentyfour'); ?></strong>
                                <p><?php echo esc_html__('Please install Advanced Custom Fields plugin for full functionality.', 'twentytwentyfour'); ?></p>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>

                        <?php if (course_get_field('course_features_title')) : ?>
                        <h3><?php echo esc_html(course_get_field('course_features_title')); ?></h3>
                        <?php endif; ?>
                        
                        <?php if (course_has_rows('course_features')) : ?>
                        <ul class="complexity-challenges">
                            <?php 
                            if (function_exists('have_rows')) :
                                while (have_rows('course_features')) : the_row(); 
                            ?>
                            <li class="complexity-challenge">
                                <strong><?php echo esc_html(get_sub_field('feature_title')); ?></strong>
                                <p><?php echo esc_html(get_sub_field('feature_description')); ?></p>
                            </li>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <li class="complexity-challenge">
                                <strong><?php echo esc_html__('Course Feature', 'twentytwentyfour'); ?></strong>
                                <p><?php echo esc_html__('Please install Advanced Custom Fields plugin for full functionality.', 'twentytwentyfour'); ?></p>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>

                        <?php if (course_get_field('course_cta_enabled')) : ?>
                        <div style="margin-top: 30px; padding: 20px; background: rgba(2, 100, 71, 0.05); border-radius: 12px;">
                            <h4 style="color: #026447; margin-top: 0;"><?php echo esc_html(course_get_field('course_cta_title')); ?></h4>
                            <p style="margin-bottom: 0;"><?php echo esc_html(course_get_field('course_cta_description')); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (course_get_field('display_empty_section')) : ?>
    <section class="featured-content">
        <div class="featured-grid" style="margin-top: 0;">
            <div class="empty-state">
                <div class="empty-state-animation">
                    <div class="thinking-animation">
                        <div class="bubble"></div>
                        <div class="bubble"></div>
                        <div class="bubble"></div>
                    </div>
                    <svg class="hero-character" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="7"/>
                        <path d="M12 15v6"/>
                        <path d="M8 21h8"/>
                        <circle cx="9" cy="7" r="1" fill="currentColor"/>
                        <circle cx="15" cy="7" r="1" fill="currentColor"/>
                        <path d="M8 12s2 1 4 1 4-1 4-1"/>
                    </svg>
                </div>
                <h3 class="empty-message"><?php echo esc_html(course_get_field('empty_section_message', false, 'Hmm... This section is as empty as a developer\'s coffee cup at 9 AM!')); ?></h3>
                <p class="empty-description"><?php echo esc_html(course_get_field('empty_section_description', false, 'Don\'t worry, we\'re brewing up some amazing content for this category.')); ?></p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Notification Modal -->
    <div class="notification-modal" id="notificationModal">
        <div class="notification-container">
            <button class="close-notifications" aria-label="Close">&times;</button>
            <div class="notification-header">
                <h3><?php echo esc_html__('Notifications', 'twentytwentyfour'); ?></h3>
                <button class="mark-all-read"><?php echo esc_html__('Mark all as read', 'twentytwentyfour'); ?></button>
            </div>
            <div class="notification-list">
                <?php
                if (course_has_rows('notifications', 'option')) :
                    if (function_exists('have_rows')) :
                        while (have_rows('notifications', 'option')) : the_row();
                            $is_unread = get_sub_field('is_unread');
                ?>
                <div class="notification-item <?php echo $is_unread ? 'unread' : ''; ?>">
                    <div class="notification-content">
                        <p><?php echo esc_html(get_sub_field('notification_text')); ?></p>
                        <span class="notification-time"><?php echo esc_html(get_sub_field('notification_time')); ?></span>
                    </div>
                </div>
                <?php
                        endwhile;
                    endif;
                else :
                ?>
                <div class="notification-item unread">
                    <div class="notification-content">
                        <p><?php echo esc_html__('Welcome to the Course Template!', 'twentytwentyfour'); ?></p>
                        <span class="notification-time"><?php echo esc_html__('Just now', 'twentytwentyfour'); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="notification-footer">
                <a href="<?php echo esc_url(course_get_field('all_notifications_url', 'option', '#')); ?>" class="view-all-notifications">
                    <?php echo esc_html__('View all notifications', 'twentytwentyfour'); ?>
                </a>
            </div>
        </div>
    </div>
</main>

<?php
// Enqueue custom scripts
wp_enqueue_script('lms-js', get_theme_file_uri('/assets/js/lms.js'), array('jquery'), null, true);
wp_enqueue_script('course-template-js', get_theme_file_uri('/assets/js/course-template.js'), array('jquery'), null, true);
?>

<?php
get_footer(); 