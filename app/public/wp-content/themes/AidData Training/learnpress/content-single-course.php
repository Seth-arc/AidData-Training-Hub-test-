<?php
/**
 * Template for displaying content of single course.
 * WITH CONSISTENT TYPOGRAPHY SYSTEM
 */

defined( 'ABSPATH' ) || exit();

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

$course = learn_press_get_course();

// Get custom learning objectives
$learning_objectives = get_post_meta(get_the_ID(), '_aiddata_learning_objectives', true);

// Fallback: Try LearnPress key features
if (empty($learning_objectives) || !is_array($learning_objectives)) {
	$key_features = $course ? $course->get_extra_info( 'key_features' ) : array();
	
	if (is_array($key_features) && !empty($key_features)) {
		$learning_objectives = array();
		foreach ($key_features as $feature) {
			$title = '';
			$description = $feature;
			
			if (strpos($feature, ':') !== false) {
				$parts = explode(':', $feature, 2);
				if (strlen(trim($parts[0])) < 50) {
					$title = trim($parts[0]);
					$description = trim($parts[1]);
				}
			}
			
			$learning_objectives[] = array(
				'title' => $title,
				'description' => $description
			);
		}
	}
}

$user = learn_press_get_current_user();
$can_view_content = $course && ( $user->has_enrolled_or_finished( $course->get_id() ) || $user->is_admin() );

// TYPOGRAPHY SYSTEM - Used consistently throughout
$font_family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif";
$section_heading = "font-size: 1.5rem; font-weight: 700; color: #004E38; margin-bottom: 1rem; font-family: {$font_family}; line-height: 1.3;";
$card_heading = "font-size: 1.125rem; font-weight: 600; color: #004E38; margin-bottom: 0.75rem; font-family: {$font_family}; line-height: 1.4;";
$body_text = "font-size: 1rem; color: #555; font-family: {$font_family}; line-height: 1.6;";
$curriculum_heading = "font-size: 1rem; font-weight: 600; color: #333; font-family: {$font_family};";
?>

<div class="content-column">
	<div class="content-section">
		<h2 class="section-title" style="<?php echo $section_heading; ?>">Tutorial Overview</h2>

		<div class="section-content" style="<?php echo $body_text; ?>">
			<?php learn_press_get_template( 'single-course/tabs/overview.php' ); ?>
		</div>
	</div>

	<div class="content-section">
		<h2 class="section-title" style="<?php echo $section_heading; ?>">What You'll Learn</h2>

		<div class="section-content">
			<style>
				.learning-objectives-grid {
					display: grid;
					grid-template-columns: repeat(2, 1fr);
					gap: 1.5rem;
					margin-top: 1.5rem;
				}
				@media (max-width: 768px) {
					.learning-objectives-grid {
						grid-template-columns: 1fr;
					}
				}
			</style>
			
			<div class="learning-objectives-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-top: 1.5rem;">
				<?php
				$icons = array(
					'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="11" cy="11" r="8"/>
						<path d="m21 21-4.35-4.35"/>
					</svg>',
					'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<line x1="18" y1="20" x2="18" y2="10"/>
						<line x1="12" y1="20" x2="12" y2="4"/>
						<line x1="6" y1="20" x2="6" y2="14"/>
					</svg>',
					'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
						<polyline points="14,2 14,8 20,8"/>
						<line x1="16" y1="13" x2="8" y2="13"/>
						<line x1="16" y1="17" x2="8" y2="17"/>
					</svg>',
					'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
						<rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
					</svg>'
				);
				
				$has_objectives = is_array($learning_objectives) && !empty($learning_objectives);
				
				if ($has_objectives) :
					$icon_index = 0;
					foreach ($learning_objectives as $objective) :
						$title = isset($objective['title']) ? $objective['title'] : '';
						$description = isset($objective['description']) ? $objective['description'] : '';
						
						if (empty($title) && empty($description)) continue;
						
						$current_icon = $icons[$icon_index % count($icons)];
						$icon_index++;
					?>
						<div class="learning-card" style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem; transition: transform 0.2s ease, box-shadow 0.2s ease;">
							<?php if (!empty($title)) : ?>
								<h4 style="<?php echo $card_heading; ?> margin: 0 0 0.75rem 0;"><?php echo esc_html($title); ?></h4>
							<?php endif; ?>
							<?php if (!empty($description)) : ?>
								<p style="<?php echo $body_text; ?> margin: 0;"><?php echo wp_kses_post($description); ?></p>
							<?php endif; ?>
						</div>
					<?php 
					endforeach;
				else : 
				?>
					<div class="learning-card" style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem;">
						<h4 style="<?php echo $card_heading; ?> margin: 0 0 0.75rem 0;">Core Concepts</h4>
						<p style="<?php echo $body_text; ?> margin: 0;">Master the fundamental concepts and principles that form the foundation of this subject.</p>
					</div>
					
					<div class="learning-card" style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem;">
						<h4 style="<?php echo $card_heading; ?> margin: 0 0 0.75rem 0;">Practical Skills</h4>
						<p style="<?php echo $body_text; ?> margin: 0;">Develop hands-on skills through interactive exercises and real-world applications.</p>
					</div>
					
					<div class="learning-card" style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem;">
						<h4 style="<?php echo $card_heading; ?> margin: 0 0 0.75rem 0;">Professional Application</h4>
						<p style="<?php echo $body_text; ?> margin: 0;">Learn how to apply your knowledge in professional settings and real-world scenarios.</p>
					</div>
					
					<div class="learning-card" style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem;">
						<h4 style="<?php echo $card_heading; ?> margin: 0 0 0.75rem 0;">Best Practices</h4>
						<p style="<?php echo $body_text; ?> margin: 0;">Discover industry best practices and tips for achieving optimal results in your work.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="content-section">
		<h2 class="section-title" style="<?php echo $section_heading; ?>">Course Curriculum</h2>

		<div class="section-content">
			<?php
			$curriculum = $course->get_curriculum();
			
			if ( $curriculum && is_array($curriculum) && count($curriculum) > 0 ) {
				$section_count = 0;
				foreach ( $curriculum as $section ) {
					$section_count++;
					$items = $section->get_items();
					$section_title = $section->get_title();
					?>
					<div class="curriculum-item" style="border: 1px solid #e0e0e0; border-radius: 6px; margin-bottom: 0.75rem; overflow: hidden;" data-section="<?php echo $section_count; ?>" data-open="false">
						<div class="curriculum-header" style="padding: 1rem 1.25rem; background-color: #f8f8f8; cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none; transition: background-color 0.2s;">
							<span class="curriculum-title" style="<?php echo $curriculum_heading; ?>"><?php echo esc_html( $section_title ); ?></span>
							<div class="curriculum-toggle" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; color: #004E38; font-size: 16px; transition: transform 0.3s;">▼</div>
						</div>
						<div class="curriculum-content">
							<ul class="lesson-list" style="padding: 1rem 1.25rem; list-style: none; margin: 0; background-color: #fafafa;">
								<?php if ( empty( $items ) ) : ?>
									<li style="padding: 0.5rem 0; <?php echo $body_text; ?>">No lessons in this section.</li>
								<?php else : ?>
									<?php foreach ( $items as $item ) : ?>
										<li style="padding: 0.75rem 1rem; padding-left: 2.5rem; margin-bottom: 0.5rem; background: white; border: 1px solid #e0e0e0; border-radius: 4px; <?php echo $body_text; ?> box-shadow: 0 1px 3px rgba(0,0,0,0.05); position: relative;">
											<span style="position: absolute; left: 1rem; color: #004E38; font-weight: bold;">▸</span>
											<?php echo esc_html( $item->get_title() ); ?>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						</div>
					</div>
					<?php
				}
			} else {
				echo '<p style="color: #666; font-style: italic;">Curriculum is empty or not yet configured.</p>';
			}
			?>
			
			<script>
			(function() {
				function initCurriculumAccordion() {
					const curriculumItems = document.querySelectorAll('.curriculum-item');
					
					console.log('[Accordion] Found', curriculumItems.length, 'curriculum sections');
					
					if (curriculumItems.length === 0) return;
					
					// Set initial closed state
					curriculumItems.forEach(function(item) {
						const content = item.querySelector('.curriculum-content');
						if (content) {
							content.style.height = '0px';
							content.style.opacity = '0';
							content.style.overflow = 'hidden';
							content.style.transition = 'height 0.3s ease, opacity 0.3s ease';
						}
					});
					
					curriculumItems.forEach(function(item, index) {
						const header = item.querySelector('.curriculum-header');
						const content = item.querySelector('.curriculum-content');
						const toggle = item.querySelector('.curriculum-toggle');
						
						if (!header || !content || !toggle) return;
						
						header.addEventListener('click', function(e) {
							e.preventDefault();
							e.stopPropagation();
							
							const isOpen = item.getAttribute('data-open') === 'true';
							
							console.log('[Accordion] Clicked section', index, '| Currently:', isOpen ? 'OPEN' : 'CLOSED');
							
							// Close ALL sections first
							document.querySelectorAll('.curriculum-item').forEach(function(i) {
								const c = i.querySelector('.curriculum-content');
								const t = i.querySelector('.curriculum-toggle');
								i.setAttribute('data-open', 'false');
								if (c) {
									c.style.height = '0px';
									c.style.opacity = '0';
								}
								if (t) t.style.transform = 'rotate(0deg)';
							});
							
							// Open this section if it was closed
							if (!isOpen) {
								content.style.display = 'block';
								const fullHeight = content.scrollHeight;
								content.style.display = '';
								
								item.setAttribute('data-open', 'true');
								content.style.height = fullHeight + 'px';
								content.style.opacity = '1';
								toggle.style.transform = 'rotate(180deg)';
								
								console.log('[Accordion] OPENED section', index, '| Height set to:', fullHeight + 'px');
							}
						});
						
						header.addEventListener('mouseenter', function() {
							this.style.backgroundColor = '#efefef';
						});
						header.addEventListener('mouseleave', function() {
							this.style.backgroundColor = '#f8f8f8';
						});
					});
					
					console.log('[Accordion] Successfully initialized!');
				}
				
				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', initCurriculumAccordion);
				} else {
					initCurriculumAccordion();
				}
				
				setTimeout(initCurriculumAccordion, 200);
			})();
			</script>
		</div>
	</div>

</div>

<aside class="sidebar">
	<div class="cta-card">
		<?php if ( $course ) : ?>
            
            <div class="price" style="text-align: center; color: #004E38; font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; <?php echo $font_family; ?>">
                <?php echo $course->get_price_html() ? $course->get_price_html() : 'Free'; ?>
            </div>
            
             <p class="price-note" style="text-align: center; color: #888; margin-bottom: 1.5rem; <?php echo $body_text; ?>">Self-paced Course</p>

			<?php LearnPress::instance()->template( 'course' )->course_buttons(); ?>

            <button class="button button-secondary" style="display: block; width: 100%; padding: 1rem; border-radius: 6px; <?php echo $body_text; ?> font-weight: 600; text-align: center; border: 2px solid #004E38; background-color: white; color: #004E38; cursor: pointer; transition: all 0.3s ease; margin-top: 0.75rem;">
                Watch Trailer
            </button>
            
            <div class="whats-included" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
                 <h4 style="<?php echo $body_text; ?> font-weight: 600; margin-bottom: 1rem; color: #333;">What's Included:</h4>
                 <ul class="included-list" style="list-style: none; padding: 0; margin: 0;">
                    <li style="padding: 0.5rem 0; <?php echo $body_text; ?>">Full lifetime access ✓</li>
                    <li style="padding: 0.5rem 0; <?php echo $body_text; ?>">Access on mobile and tablet ✓</li>
                    <li style="padding: 0.5rem 0; <?php echo $body_text; ?>">Certificate of Completion ✓</li>
                 </ul>
            </div>
            
		<?php endif; ?>
	</div>

	<?php if ( $can_view_content ) : ?>
		<div class="cta-card">
			<h3 class="widget-title" style="font-size: 1.125rem; font-weight: 700; color: #004E38; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; <?php echo $font_family; ?>">Course Progress</h3>
			<div class="course-progress-wrapper" style="<?php echo $body_text; ?>">
				<?php LearnPress::instance()->template( 'course' )->user_progress(); ?>
			</div>
		</div>
	<?php endif; ?>

</aside>
<?php
// Note: Removed do_action( 'learn-press/after-single-course' )