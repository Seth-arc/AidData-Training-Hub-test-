<?php
/**
 * Template for displaying header of single course popup.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/header.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.4
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $course ) || ! isset( $user ) || ! isset( $percentage ) ||
	! isset( $completed_items ) || ! isset( $total_items ) ) {
	return;
}
?>

<div id="popup-header" style="background-color: white; box-shadow: none !important; -webkit-box-shadow: none !important; display: flex; align-items: center;">
	<div class="popup-header__logo-toggle" style="display: flex; align-items: center;">
		<?php
		/**
		 * @since 4.0.6
		 * @see single-button-toggle-sidebar - 5
		 */
		do_action( 'learn-press/single-button-toggle-sidebar' );
		?>
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logodark.png' ); ?>"
			 alt="AidData Logo"
			 class="aiddata-header-logo"
			 style="height: 30px; margin-left: 15px; display: block;" />
	</div>
	<div class="popup-header__inner" style="margin-left: 15px; flex: 1;">
		<h2 class="course-title" style="margin: 0; font-size: 1.25rem; color: #333333;">
			<?php echo wp_kses_post( $course->get_title() ); ?>
		</h2>
	</div>
	<a href="<?php echo esc_url_raw( $course->get_permalink() ); ?>"
		class="back-course exit-course-button"
		aria-label="<?php esc_attr_e( 'Exit Course', 'learnpress' ); ?>"
		style="display: inline-flex !important; align-items: center; justify-content: center; gap: 8px; padding: 0.75rem 1.5rem !important; margin-right: 24px; background-color: #026447 !important; color: white; border-radius: 4px; text-decoration: none; font-size: 0.95rem; font-weight: 500; line-height: normal !important; height: auto !important; transition: all 0.3s ease;"
		onmouseover="this.style.backgroundColor='#004E38'; this.style.transform='translateY(-1px)'"
		onmouseout="this.style.backgroundColor='#026447'; this.style.transform='translateY(0)'"
	>
		<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
			<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
			<polyline points="16 17 21 12 16 7"></polyline>
			<line x1="21" y1="12" x2="9" y2="12"></line>
		</svg>
		<span><?php esc_html_e( 'Exit Course', 'learnpress' ); ?></span>
	</a>
</div>

<script>
// Style sidebar toggle button with AidData green
document.addEventListener('DOMContentLoaded', function() {
	const sidebarToggle = document.getElementById('sidebar-toggle');
	if (sidebarToggle) {
		sidebarToggle.style.setProperty('color', '#026447', 'important');
		sidebarToggle.style.setProperty('background-color', 'rgba(2, 100, 71, 0.1)', 'important');
	}

	// Smooth lesson switching
	const contentArea = document.getElementById('learn-press-content-item');
	const popupContent = document.getElementById('popup-content');

	// Add loading class when clicking lesson links
	document.addEventListener('click', function(e) {
		const lessonLink = e.target.closest('.course-item');
		if (lessonLink && !lessonLink.classList.contains('current')) {
			if (contentArea) {
				contentArea.classList.add('loading');
			}
			if (popupContent) {
				popupContent.classList.add('loading');
			}
		}
	});

	// Remove loading class when content is loaded
	if (window.lpGlobalSettings) {
		const originalAjaxComplete = jQuery(document).ajaxComplete;
		jQuery(document).ajaxComplete(function(event, xhr, settings) {
			if (settings.url && settings.url.includes('lp-ajax')) {
				setTimeout(function() {
					if (contentArea) {
						contentArea.classList.remove('loading');
					}
					if (popupContent) {
						popupContent.classList.remove('loading');
						popupContent.scrollTop = 0;
					}
				}, 100);
			}
		});
	}

	// Smooth scroll to top on lesson change
	const observer = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			if (mutation.addedNodes.length && popupContent) {
				popupContent.scrollTo({
					top: 0,
					behavior: 'smooth'
				});
			}
		});
	});

	if (contentArea) {
		observer.observe(contentArea, { childList: true, subtree: true });
	}

	// Replace "Failed" with "Try again" in quiz results
	function replaceFailedText() {
		// Replace in all error/failed messages
		const selectors = [
			'.learn-press-message.error',
			'.learn-press-message.error .message-content',
			'.learn-press-message.failed',
			'.lp-quiz-result .result-grade.failed',
			'.quiz-result .result-grade.failed',
			'#learn-press-content-item .learn-press-message.error'
		];

		selectors.forEach(function(selector) {
			const elements = document.querySelectorAll(selector);
			elements.forEach(function(el) {
				// Replace text content
				if (el.textContent && el.textContent.toLowerCase().includes('failed')) {
					el.textContent = el.textContent.replace(/Failed/gi, 'Try again');
				}

				// Also check innerHTML for nested content
				if (el.innerHTML && el.innerHTML.toLowerCase().includes('failed')) {
					el.innerHTML = el.innerHTML.replace(/Failed/gi, 'Try again');
				}
			});
		});
	}

	// Run on load
	replaceFailedText();

	// Watch for new content with MutationObserver
	const resultObserver = new MutationObserver(function() {
		replaceFailedText();
	});

	if (contentArea) {
		resultObserver.observe(contentArea, { childList: true, subtree: true });
	}

	// Also run periodically to catch any delayed content
	setInterval(replaceFailedText, 500);

	// Run after AJAX calls complete
	jQuery(document).ajaxComplete(function() {
		setTimeout(replaceFailedText, 100);
	});
});
</script>
