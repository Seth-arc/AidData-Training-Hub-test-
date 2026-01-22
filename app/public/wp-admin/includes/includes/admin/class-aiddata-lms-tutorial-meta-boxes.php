<?php
/**
 * Tutorial Meta Boxes Class
 *
 * Handles the registration and rendering of meta boxes for the tutorial post type.
 * Implements basic information and settings meta boxes with validation.
 *
 * @package AidData_LMS
 * @subpackage Admin
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Tutorial_Meta_Boxes
 *
 * Manages meta boxes for tutorial post type including basic information,
 * settings, and data validation.
 */
class AidData_LMS_Tutorial_Meta_Boxes {

	/**
	 * Constructor
	 *
	 * Hooks into WordPress to register meta boxes and save handlers.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_aiddata_tutorial', array( $this, 'save_tutorial_meta' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register Meta Boxes
	 *
	 * Registers all meta boxes for the tutorial post type.
	 *
	 * @return void
	 */
	public function register_meta_boxes(): void {
		// Basic Information Meta Box
		add_meta_box(
			'aiddata_tutorial_basic_info',
			__( 'Tutorial Information', 'aiddata-lms' ),
			array( $this, 'render_basic_info_meta_box' ),
			'aiddata_tutorial',
			'normal',
			'high'
		);

		// Tutorial Steps Meta Box
		add_meta_box(
			'aiddata_tutorial_steps',
			__( 'Tutorial Steps', 'aiddata-lms' ),
			array( $this, 'render_steps_meta_box' ),
			'aiddata_tutorial',
			'normal',
			'high'
		);

		// Settings Meta Box
		add_meta_box(
			'aiddata_tutorial_settings',
			__( 'Tutorial Settings', 'aiddata-lms' ),
			array( $this, 'render_settings_meta_box' ),
			'aiddata_tutorial',
			'side',
			'default'
		);
	}

	/**
	 * Enqueue Admin Assets
	 *
	 * Loads JavaScript and CSS only on tutorial edit screens.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook ): void {
		// Only load on tutorial edit screen
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'aiddata_tutorial' !== $screen->post_type ) {
			return;
		}

		// Enqueue jQuery UI components
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		// Enqueue JavaScript
		wp_enqueue_script(
			'aiddata-lms-tutorial-meta-boxes',
			AIDDATA_LMS_URL . 'assets/js/admin/tutorial-meta-boxes.js',
			array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ),
			AIDDATA_LMS_VERSION,
			true
		);

		// Enqueue Step Builder JavaScript
		wp_enqueue_script(
			'aiddata-lms-tutorial-step-builder',
			AIDDATA_LMS_URL . 'assets/js/admin/tutorial-step-builder.js',
			array( 'jquery', 'jquery-ui-sortable', 'aiddata-lms-tutorial-meta-boxes' ),
			AIDDATA_LMS_VERSION,
			true
		);

		// Enqueue CSS
		wp_enqueue_style(
			'aiddata-lms-tutorial-meta-boxes',
			AIDDATA_LMS_URL . 'assets/css/admin/tutorial-meta-boxes.css',
			array( 'wp-jquery-ui-dialog' ),
			AIDDATA_LMS_VERSION
		);

		// Localize script with AJAX data and strings
		wp_localize_script(
			'aiddata-lms-tutorial-meta-boxes',
			'aiddataTutorialMeta',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'aiddata-tutorial-meta' ),
				'strings' => array(
					'addOutcome'          => __( 'Add Learning Outcome', 'aiddata-lms' ),
					'removeOutcome'       => __( 'Remove', 'aiddata-lms' ),
					'searchPrerequisites' => __( 'Search tutorials...', 'aiddata-lms' ),
					'loading'             => __( 'Loading...', 'aiddata-lms' ),
					'noResults'           => __( 'No tutorials found', 'aiddata-lms' ),
					'searchError'         => __( 'Error searching tutorials', 'aiddata-lms' ),
					'outcomePlaceholder'  => __( 'What will learners be able to do?', 'aiddata-lms' ),
					'errorShortDesc'      => __( 'Short description is required', 'aiddata-lms' ),
					'errorShortDescLength' => __( 'Short description must be 250 characters or less', 'aiddata-lms' ),
					'errorDuration'       => __( 'Duration must be a positive number', 'aiddata-lms' ),
					'errorDateFormat'     => __( 'Invalid date format. Use YYYY-MM-DD', 'aiddata-lms' ),
					'confirmDelete'       => __( 'Are you sure you want to delete this step?', 'aiddata-lms' ),
					'stepTitle'           => __( 'Untitled Step', 'aiddata-lms' ),
					'saveStep'            => __( 'Save Step', 'aiddata-lms' ),
					'cancel'              => __( 'Cancel', 'aiddata-lms' ),
				),
			)
		);
	}

	/**
	 * Render Basic Information Meta Box
	 *
	 * Displays fields for short description, full description, duration,
	 * prerequisites, and learning outcomes.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_basic_info_meta_box( WP_Post $post ): void {
		// Nonce field for security
		wp_nonce_field( 'aiddata_save_tutorial_meta', 'aiddata_tutorial_meta_nonce' );

		// Get existing values
		$short_description = get_post_meta( $post->ID, '_tutorial_short_description', true );
		$full_description = get_post_meta( $post->ID, '_tutorial_full_description', true );
		$duration = get_post_meta( $post->ID, '_tutorial_duration', true );
		$prerequisites = get_post_meta( $post->ID, '_tutorial_prerequisites', true );
		$outcomes = get_post_meta( $post->ID, '_tutorial_outcomes', true );

		// Check for validation errors
		$errors = get_transient( 'aiddata_tutorial_meta_errors_' . $post->ID );
		if ( $errors ) {
			echo '<div class="notice notice-error"><ul>';
			foreach ( $errors as $error ) {
				echo '<li>' . esc_html( $error ) . '</li>';
			}
			echo '</ul></div>';
			delete_transient( 'aiddata_tutorial_meta_errors_' . $post->ID );
		}

		// Ensure arrays
		if ( ! is_array( $prerequisites ) ) {
			$prerequisites = array();
		}
		if ( ! is_array( $outcomes ) ) {
			$outcomes = array();
		}
		?>

		<div class="aiddata-tutorial-meta-box">
			
			<!-- Short Description -->
			<div class="form-field">
				<label for="tutorial_short_description">
					<?php esc_html_e( 'Short Description', 'aiddata-lms' ); ?>
					<span class="required">*</span>
				</label>
				<textarea 
					id="tutorial_short_description" 
					name="tutorial_short_description"
					maxlength="250"
					rows="3"
					required
					class="large-text"
				><?php echo esc_textarea( $short_description ); ?></textarea>
				<p class="description">
					<span id="char-count"><?php echo strlen( $short_description ); ?></span>/250 <?php esc_html_e( 'characters', 'aiddata-lms' ); ?>
					<br><?php esc_html_e( 'Brief summary for archive display', 'aiddata-lms' ); ?>
				</p>
			</div>

			<!-- Full Description -->
			<div class="form-field">
				<label for="tutorial_full_description">
					<?php esc_html_e( 'Full Description', 'aiddata-lms' ); ?>
				</label>
				<?php
				wp_editor(
					$full_description,
					'tutorial_full_description',
					array(
						'textarea_name' => 'tutorial_full_description',
						'media_buttons' => true,
						'teeny'         => false,
						'textarea_rows' => 10,
						'tinymce'       => true,
					)
				);
				?>
				<p class="description">
					<?php esc_html_e( 'Detailed description for single tutorial page', 'aiddata-lms' ); ?>
				</p>
			</div>

			<!-- Duration -->
			<div class="form-field">
				<label for="tutorial_duration">
					<?php esc_html_e( 'Estimated Duration (minutes)', 'aiddata-lms' ); ?>
					<span class="required">*</span>
				</label>
				<input 
					type="number" 
					id="tutorial_duration" 
					name="tutorial_duration"
					value="<?php echo esc_attr( $duration ); ?>"
					min="0"
					step="1"
					required
					class="small-text"
				>
				<p class="description">
					<?php esc_html_e( 'Estimated time to complete this tutorial', 'aiddata-lms' ); ?>
				</p>
			</div>

			<!-- Prerequisites -->
			<div class="form-field">
				<label for="tutorial_prerequisites">
					<?php esc_html_e( 'Prerequisites', 'aiddata-lms' ); ?>
				</label>
				<div id="prerequisites-selector">
					<input 
						type="text" 
						id="prerequisites-search" 
						class="regular-text"
						placeholder="<?php esc_attr_e( 'Search tutorials...', 'aiddata-lms' ); ?>"
					>
					<div id="prerequisites-results" class="prerequisites-results"></div>
				</div>
				<div id="selected-prerequisites" class="selected-prerequisites">
					<?php if ( ! empty( $prerequisites ) ) : ?>
						<?php foreach ( $prerequisites as $prereq_id ) : ?>
							<?php
							$prereq_post = get_post( $prereq_id );
							if ( $prereq_post ) :
								?>
								<div class="prerequisite-item" data-id="<?php echo esc_attr( $prereq_id ); ?>">
									<span class="dashicons dashicons-menu"></span>
									<span class="prerequisite-title"><?php echo esc_html( $prereq_post->post_title ); ?></span>
									<button type="button" class="button-link remove-prerequisite">
										<span class="dashicons dashicons-no-alt"></span>
									</button>
									<input type="hidden" name="tutorial_prerequisites[]" value="<?php echo esc_attr( $prereq_id ); ?>">
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<p class="description">
					<?php esc_html_e( 'Tutorials that should be completed before this one', 'aiddata-lms' ); ?>
				</p>
			</div>

			<!-- Learning Outcomes -->
			<div class="form-field">
				<label for="tutorial_outcomes">
					<?php esc_html_e( 'Learning Outcomes', 'aiddata-lms' ); ?>
				</label>
				<div id="learning-outcomes-list">
					<?php if ( ! empty( $outcomes ) ) : ?>
						<?php foreach ( $outcomes as $index => $outcome ) : ?>
							<div class="outcome-item">
								<span class="dashicons dashicons-menu"></span>
								<input 
									type="text" 
									name="tutorial_outcomes[]" 
									value="<?php echo esc_attr( $outcome ); ?>"
									class="regular-text"
									placeholder="<?php esc_attr_e( 'What will learners be able to do?', 'aiddata-lms' ); ?>"
								>
								<button type="button" class="button-link remove-outcome">
									<span class="dashicons dashicons-no-alt"></span>
								</button>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="outcome-item">
							<span class="dashicons dashicons-menu"></span>
							<input 
								type="text" 
								name="tutorial_outcomes[]" 
								value=""
								class="regular-text"
								placeholder="<?php esc_attr_e( 'What will learners be able to do?', 'aiddata-lms' ); ?>"
							>
							<button type="button" class="button-link remove-outcome">
								<span class="dashicons dashicons-no-alt"></span>
							</button>
						</div>
					<?php endif; ?>
				</div>
				<button type="button" id="add-outcome" class="button button-secondary">
					<span class="dashicons dashicons-plus"></span>
					<?php esc_html_e( 'Add Learning Outcome', 'aiddata-lms' ); ?>
				</button>
				<p class="description">
					<?php esc_html_e( 'What will learners achieve by completing this tutorial?', 'aiddata-lms' ); ?>
				</p>
			</div>

		</div>
		<?php
	}

	/**
	 * Render Steps Meta Box
	 *
	 * Displays the step builder interface for creating tutorial steps.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_steps_meta_box( WP_Post $post ): void {
		// Get existing steps
		$steps = get_post_meta( $post->ID, '_tutorial_steps', true );
		if ( ! is_array( $steps ) ) {
			$steps = array();
		}

		// Nonce field for security
		wp_nonce_field( 'aiddata_save_tutorial_steps', 'aiddata_tutorial_steps_nonce' );

		// Include the view template
		include AIDDATA_LMS_PATH . 'includes/admin/views/tutorial-step-builder.php';
	}

	/**
	 * Render Settings Meta Box
	 *
	 * Displays settings for tutorial type, access control, enrollment options,
	 * and visibility settings.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_settings_meta_box( WP_Post $post ): void {
		// Get existing values
		$tutorial_type = get_post_meta( $post->ID, '_tutorial_type', true );
		$tutorial_access = get_post_meta( $post->ID, '_tutorial_access', true );
		$allow_enrollment = get_post_meta( $post->ID, '_tutorial_allow_enrollment', true );
		$require_approval = get_post_meta( $post->ID, '_tutorial_require_approval', true );
		$enrollment_limit = get_post_meta( $post->ID, '_tutorial_enrollment_limit', true );
		$enrollment_deadline = get_post_meta( $post->ID, '_tutorial_enrollment_deadline', true );
		$requires_all_steps = get_post_meta( $post->ID, '_tutorial_completion_requires_all_steps', true );
		$requires_quiz = get_post_meta( $post->ID, '_tutorial_completion_requires_quiz', true );
		$generate_certificate = get_post_meta( $post->ID, '_tutorial_generate_certificate', true );
		$show_in_catalog = get_post_meta( $post->ID, '_tutorial_show_in_catalog', true );

		// Set defaults
		if ( empty( $tutorial_type ) ) {
			$tutorial_type = 'self-paced';
		}
		if ( empty( $tutorial_access ) ) {
			$tutorial_access = 'public';
		}
		if ( $allow_enrollment === '' ) {
			$allow_enrollment = '1';
		}
		if ( $show_in_catalog === '' ) {
			$show_in_catalog = '1';
		}
		if ( $requires_all_steps === '' ) {
			$requires_all_steps = '1';
		}
		?>

		<div class="aiddata-tutorial-settings">
			
			<!-- Tutorial Type -->
			<div class="setting-field">
				<label for="tutorial_type">
					<?php esc_html_e( 'Tutorial Type', 'aiddata-lms' ); ?>
				</label>
				<select id="tutorial_type" name="tutorial_type" class="widefat">
					<option value="self-paced" <?php selected( $tutorial_type, 'self-paced' ); ?>>
						<?php esc_html_e( 'Self-Paced', 'aiddata-lms' ); ?>
					</option>
					<option value="guided" <?php selected( $tutorial_type, 'guided' ); ?>>
						<?php esc_html_e( 'Guided', 'aiddata-lms' ); ?>
					</option>
					<option value="workshop" <?php selected( $tutorial_type, 'workshop' ); ?>>
						<?php esc_html_e( 'Workshop', 'aiddata-lms' ); ?>
					</option>
				</select>
			</div>

			<!-- Access Control -->
			<div class="setting-field">
				<label for="tutorial_access">
					<?php esc_html_e( 'Access Control', 'aiddata-lms' ); ?>
				</label>
				<select id="tutorial_access" name="tutorial_access" class="widefat">
					<option value="public" <?php selected( $tutorial_access, 'public' ); ?>>
						<?php esc_html_e( 'Public', 'aiddata-lms' ); ?>
					</option>
					<option value="members-only" <?php selected( $tutorial_access, 'members-only' ); ?>>
						<?php esc_html_e( 'Members Only', 'aiddata-lms' ); ?>
					</option>
					<option value="restricted" <?php selected( $tutorial_access, 'restricted' ); ?>>
						<?php esc_html_e( 'Restricted', 'aiddata-lms' ); ?>
					</option>
				</select>
			</div>

			<hr>

			<!-- Enrollment Options -->
			<div class="setting-field">
				<strong><?php esc_html_e( 'Enrollment Options', 'aiddata-lms' ); ?></strong>
			</div>

			<div class="setting-field">
				<label>
					<input 
						type="checkbox" 
						name="tutorial_allow_enrollment" 
						value="1"
						<?php checked( $allow_enrollment, '1' ); ?>
					>
					<?php esc_html_e( 'Allow Enrollment', 'aiddata-lms' ); ?>
				</label>
			</div>

			<div class="setting-field">
				<label>
					<input 
						type="checkbox" 
						name="tutorial_require_approval" 
						value="1"
						<?php checked( $require_approval, '1' ); ?>
					>
					<?php esc_html_e( 'Require Approval', 'aiddata-lms' ); ?>
				</label>
			</div>

			<div class="setting-field">
				<label for="tutorial_enrollment_limit">
					<?php esc_html_e( 'Enrollment Limit', 'aiddata-lms' ); ?>
				</label>
				<input 
					type="number" 
					id="tutorial_enrollment_limit" 
					name="tutorial_enrollment_limit"
					value="<?php echo esc_attr( $enrollment_limit ); ?>"
					min="0"
					class="small-text"
				>
				<p class="description">
					<?php esc_html_e( '0 = unlimited', 'aiddata-lms' ); ?>
				</p>
			</div>

			<div class="setting-field">
				<label for="tutorial_enrollment_deadline">
					<?php esc_html_e( 'Enrollment Deadline', 'aiddata-lms' ); ?>
				</label>
				<input 
					type="text" 
					id="tutorial_enrollment_deadline" 
					name="tutorial_enrollment_deadline"
					value="<?php echo esc_attr( $enrollment_deadline ); ?>"
					class="aiddata-date-picker widefat"
					placeholder="YYYY-MM-DD"
				>
			</div>

			<hr>

			<!-- Completion Criteria -->
			<div class="setting-field">
				<strong><?php esc_html_e( 'Completion Criteria', 'aiddata-lms' ); ?></strong>
			</div>

			<div class="setting-field">
				<label>
					<input 
						type="checkbox" 
						name="tutorial_completion_requires_all_steps" 
						value="1"
						<?php checked( $requires_all_steps, '1' ); ?>
					>
					<?php esc_html_e( 'All Steps Required', 'aiddata-lms' ); ?>
				</label>
			</div>

			<div class="setting-field">
				<label>
					<input 
						type="checkbox" 
						name="tutorial_completion_requires_quiz" 
						value="1"
						<?php checked( $requires_quiz, '1' ); ?>
					>
					<?php esc_html_e( 'Quiz Passing Required', 'aiddata-lms' ); ?>
				</label>
			</div>

			<div class="setting-field">
				<label>
					<input 
						type="checkbox" 
						name="tutorial_generate_certificate" 
						value="1"
						<?php checked( $generate_certificate, '1' ); ?>
					>
					<?php esc_html_e( 'Generate Certificate', 'aiddata-lms' ); ?>
				</label>
			</div>

			<hr>

			<!-- Visibility Settings -->
			<div class="setting-field">
				<strong><?php esc_html_e( 'Visibility', 'aiddata-lms' ); ?></strong>
			</div>

			<div class="setting-field">
				<label>
					<input 
						type="checkbox" 
						name="tutorial_show_in_catalog" 
						value="1"
						<?php checked( $show_in_catalog, '1' ); ?>
					>
					<?php esc_html_e( 'Show in Catalog', 'aiddata-lms' ); ?>
				</label>
			</div>

		</div>
		<?php
	}

	/**
	 * Save Tutorial Meta Data
	 *
	 * Handles saving of all tutorial meta data with validation and security checks.
	 *
	 * @param int $post_id The post ID being saved.
	 * @return void
	 */
	public function save_tutorial_meta( int $post_id ): void {
		// Verify nonce
		if ( ! isset( $_POST['aiddata_tutorial_meta_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['aiddata_tutorial_meta_nonce'], 'aiddata_save_tutorial_meta' ) ) {
			return;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check post type
		if ( 'aiddata_tutorial' !== get_post_type( $post_id ) ) {
			return;
		}

		// Collect meta data
		$meta_data = $this->collect_meta_from_post( $_POST );

		// Validate
		$validated = $this->validate_tutorial_meta( $meta_data );
		if ( is_wp_error( $validated ) ) {
			// Store errors in transient for display
			set_transient( 'aiddata_tutorial_meta_errors_' . $post_id, $validated->get_error_messages(), 45 );
			return;
		}

		// Sanitize
		$sanitized = $this->sanitize_tutorial_meta( $validated );

		// Save to post meta
		foreach ( $sanitized as $key => $value ) {
			update_post_meta( $post_id, '_tutorial_' . $key, $value );
		}

		// Save tutorial steps separately
		$this->save_tutorial_steps( $post_id );

		// Fire action hook
		do_action( 'aiddata_lms_tutorial_meta_saved', $post_id, $sanitized );
	}

	/**
	 * Collect Meta Data from POST
	 *
	 * Extracts tutorial meta data from the POST array.
	 *
	 * @param array $post_data The POST data array.
	 * @return array Collected meta data.
	 */
	private function collect_meta_from_post( array $post_data ): array {
		$meta = array();

		// Basic information
		$meta['short_description'] = $post_data['tutorial_short_description'] ?? '';
		$meta['full_description'] = $post_data['tutorial_full_description'] ?? '';
		$meta['duration'] = $post_data['tutorial_duration'] ?? 0;
		$meta['prerequisites'] = $post_data['tutorial_prerequisites'] ?? array();
		$meta['outcomes'] = $post_data['tutorial_outcomes'] ?? array();

		// Settings
		$meta['type'] = $post_data['tutorial_type'] ?? 'self-paced';
		$meta['access'] = $post_data['tutorial_access'] ?? 'public';
		$meta['allow_enrollment'] = isset( $post_data['tutorial_allow_enrollment'] ) ? '1' : '0';
		$meta['require_approval'] = isset( $post_data['tutorial_require_approval'] ) ? '1' : '0';
		$meta['enrollment_limit'] = $post_data['tutorial_enrollment_limit'] ?? 0;
		$meta['enrollment_deadline'] = $post_data['tutorial_enrollment_deadline'] ?? '';
		$meta['completion_requires_all_steps'] = isset( $post_data['tutorial_completion_requires_all_steps'] ) ? '1' : '0';
		$meta['completion_requires_quiz'] = isset( $post_data['tutorial_completion_requires_quiz'] ) ? '1' : '0';
		$meta['generate_certificate'] = isset( $post_data['tutorial_generate_certificate'] ) ? '1' : '0';
		$meta['show_in_catalog'] = isset( $post_data['tutorial_show_in_catalog'] ) ? '1' : '0';

		return $meta;
	}

	/**
	 * Validate Tutorial Meta Data
	 *
	 * Validates all tutorial meta data before saving.
	 *
	 * @param array $meta_data The meta data to validate.
	 * @return array|WP_Error Validated data or WP_Error on failure.
	 */
	private function validate_tutorial_meta( array $meta_data ) {
		$errors = array();

		// Short description required
		if ( empty( $meta_data['short_description'] ) ) {
			$errors[] = __( 'Short description is required.', 'aiddata-lms' );
		}

		// Short description length
		if ( strlen( $meta_data['short_description'] ) > 250 ) {
			$errors[] = __( 'Short description must be 250 characters or less.', 'aiddata-lms' );
		}

		// Duration must be positive
		if ( isset( $meta_data['duration'] ) && $meta_data['duration'] < 0 ) {
			$errors[] = __( 'Duration must be a positive number.', 'aiddata-lms' );
		}

		// Duration required
		if ( empty( $meta_data['duration'] ) || $meta_data['duration'] == 0 ) {
			$errors[] = __( 'Duration is required.', 'aiddata-lms' );
		}

		// Validate enrollment deadline date format
		if ( ! empty( $meta_data['enrollment_deadline'] ) ) {
			$date = DateTime::createFromFormat( 'Y-m-d', $meta_data['enrollment_deadline'] );
			if ( ! $date || $date->format( 'Y-m-d' ) !== $meta_data['enrollment_deadline'] ) {
				$errors[] = __( 'Invalid enrollment deadline date format. Use YYYY-MM-DD.', 'aiddata-lms' );
			}
		}

		// Enrollment limit must be non-negative
		if ( isset( $meta_data['enrollment_limit'] ) && $meta_data['enrollment_limit'] < 0 ) {
			$errors[] = __( 'Enrollment limit must be 0 or greater.', 'aiddata-lms' );
		}

		if ( ! empty( $errors ) ) {
			return new WP_Error( 'validation_failed', implode( ' ', $errors ), $errors );
		}

		return $meta_data;
	}

	/**
	 * Sanitize Tutorial Meta Data
	 *
	 * Sanitizes all tutorial meta data for safe storage.
	 *
	 * @param array $meta_data The meta data to sanitize.
	 * @return array Sanitized meta data.
	 */
	private function sanitize_tutorial_meta( array $meta_data ): array {
		return array(
			'short_description'                  => sanitize_textarea_field( $meta_data['short_description'] ?? '' ),
			'full_description'                   => wp_kses_post( $meta_data['full_description'] ?? '' ),
			'duration'                           => absint( $meta_data['duration'] ?? 0 ),
			'prerequisites'                      => array_map( 'absint', (array) ( $meta_data['prerequisites'] ?? array() ) ),
			'outcomes'                           => array_map( 'sanitize_text_field', array_filter( (array) ( $meta_data['outcomes'] ?? array() ) ) ),
			'type'                               => sanitize_text_field( $meta_data['type'] ?? 'self-paced' ),
			'access'                             => sanitize_text_field( $meta_data['access'] ?? 'public' ),
			'allow_enrollment'                   => $meta_data['allow_enrollment'] ?? '0',
			'require_approval'                   => $meta_data['require_approval'] ?? '0',
			'enrollment_limit'                   => absint( $meta_data['enrollment_limit'] ?? 0 ),
			'enrollment_deadline'                => sanitize_text_field( $meta_data['enrollment_deadline'] ?? '' ),
			'completion_requires_all_steps'      => $meta_data['completion_requires_all_steps'] ?? '0',
			'completion_requires_quiz'           => $meta_data['completion_requires_quiz'] ?? '0',
			'generate_certificate'               => $meta_data['generate_certificate'] ?? '0',
			'show_in_catalog'                    => $meta_data['show_in_catalog'] ?? '1',
		);
	}

	/**
	 * Save Tutorial Steps
	 *
	 * Handles saving of tutorial steps with validation and sanitization.
	 *
	 * @param int $post_id The post ID being saved.
	 * @return void
	 */
	private function save_tutorial_steps( int $post_id ): void {
		// Verify nonce for steps
		if ( ! isset( $_POST['aiddata_tutorial_steps_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['aiddata_tutorial_steps_nonce'], 'aiddata_save_tutorial_steps' ) ) {
			return;
		}

		if ( ! isset( $_POST['tutorial_steps'] ) ) {
			// No steps data, don't delete existing steps
			return;
		}

		// Decode JSON
		$steps = json_decode( wp_unslash( $_POST['tutorial_steps'] ), true );

		if ( ! is_array( $steps ) ) {
			$steps = array();
		}

		// Sanitize each step
		$sanitized_steps = array_map( array( $this, 'sanitize_step' ), $steps );

		// Save to post meta
		update_post_meta( $post_id, '_tutorial_steps', $sanitized_steps );

		// Update step count for quick reference
		update_post_meta( $post_id, '_tutorial_step_count', count( $sanitized_steps ) );

		// Fire action
		do_action( 'aiddata_lms_tutorial_steps_saved', $post_id, $sanitized_steps );
	}

	/**
	 * Sanitize Step Data
	 *
	 * Sanitizes a single tutorial step.
	 *
	 * @param array $step The step data to sanitize.
	 * @return array Sanitized step data.
	 */
	private function sanitize_step( array $step ): array {
		return array(
			'id'              => sanitize_text_field( $step['id'] ?? '' ),
			'type'            => sanitize_text_field( $step['type'] ?? 'text' ),
			'title'           => sanitize_text_field( $step['title'] ?? '' ),
			'description'     => sanitize_textarea_field( $step['description'] ?? '' ),
			'content'         => $this->sanitize_step_content( $step['content'] ?? array(), $step['type'] ?? 'text' ),
			'required'        => (bool) ( $step['required'] ?? true ),
			'estimated_time'  => absint( $step['estimated_time'] ?? 0 ),
			'order'           => absint( $step['order'] ?? 0 ),
		);
	}

	/**
	 * Sanitize Step Content
	 *
	 * Sanitizes step content based on step type.
	 *
	 * @param mixed  $content The content to sanitize.
	 * @param string $type The step type.
	 * @return array Sanitized content.
	 */
	private function sanitize_step_content( $content, string $type ): array {
		if ( ! is_array( $content ) ) {
			$content = array();
		}

		switch ( $type ) {
			case 'video':
				return array(
					'platform'             => sanitize_text_field( $content['platform'] ?? '' ),
					'video_url'            => esc_url_raw( $content['video_url'] ?? '' ),
					'video_id'             => sanitize_text_field( $content['video_id'] ?? '' ),
					'thumbnail_url'        => esc_url_raw( $content['thumbnail_url'] ?? '' ),
					'duration'             => absint( $content['duration'] ?? 0 ),
					'autoplay'             => (bool) ( $content['autoplay'] ?? false ),
					'completion_threshold' => absint( $content['completion_threshold'] ?? 90 ),
					'description'          => wp_kses_post( $content['description'] ?? '' ),
					'transcript'           => wp_kses_post( $content['transcript'] ?? '' ),
				);

			case 'text':
				return array(
					'content'         => wp_kses_post( $content['content'] ?? '' ),
					'format'          => sanitize_text_field( $content['format'] ?? 'html' ),
					'attachments'     => array_map( 'absint', $content['attachments'] ?? array() ),
					'allow_comments'  => (bool) ( $content['allow_comments'] ?? false ),
				);

			case 'interactive':
				return array(
					'interaction_type'  => sanitize_text_field( $content['interaction_type'] ?? 'iframe' ),
					'embed_code'        => wp_kses_post( $content['embed_code'] ?? '' ),
					'url'               => esc_url_raw( $content['url'] ?? '' ),
					'height'            => absint( $content['height'] ?? 600 ),
					'instructions'      => wp_kses_post( $content['instructions'] ?? '' ),
					'completion_trigger' => sanitize_text_field( $content['completion_trigger'] ?? 'manual' ),
				);

			case 'resource':
				$resources = array();
				if ( isset( $content['resources'] ) && is_array( $content['resources'] ) ) {
					foreach ( $content['resources'] as $resource ) {
						if ( ! is_array( $resource ) ) {
							continue;
						}
						$resources[] = array(
							'file_id'      => absint( $resource['file_id'] ?? 0 ),
							'title'        => sanitize_text_field( $resource['title'] ?? '' ),
							'description'  => sanitize_textarea_field( $resource['description'] ?? '' ),
							'file_type'    => sanitize_text_field( $resource['file_type'] ?? '' ),
							'file_size'    => absint( $resource['file_size'] ?? 0 ),
							'download_url' => esc_url_raw( $resource['download_url'] ?? '' ),
						);
					}
				}
				return array(
					'resources'          => $resources,
					'instructions'       => wp_kses_post( $content['instructions'] ?? '' ),
					'required_downloads' => array_map( 'absint', $content['required_downloads'] ?? array() ),
				);

			case 'quiz':
				// Quiz content will be handled in Phase 4
				return array(
					'quiz_id'   => absint( $content['quiz_id'] ?? 0 ),
					'questions' => array(), // Placeholder for Phase 4
				);

			default:
				return $content;
		}
	}

	/**
	 * Detect Video Platform
	 *
	 * Detects the video platform from a URL.
	 *
	 * @param string $url Video URL.
	 * @return string Platform identifier.
	 */
	private function detect_video_platform( string $url ): string {
		if ( strpos( $url, 'panopto' ) !== false ) {
			return 'panopto';
		} elseif ( strpos( $url, 'youtube.com' ) !== false || strpos( $url, 'youtu.be' ) !== false ) {
			return 'youtube';
		} elseif ( strpos( $url, 'vimeo.com' ) !== false ) {
			return 'vimeo';
		} else {
			return 'html5';
		}
	}

	/**
	 * Extract Video ID
	 *
	 * Extracts the video ID from a platform URL.
	 *
	 * @param string $url Video URL.
	 * @param string $platform Platform identifier.
	 * @return string|null Extracted video ID or null.
	 */
	private function extract_video_id( string $url, string $platform ): ?string {
		switch ( $platform ) {
			case 'youtube':
				preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches );
				return isset( $matches[1] ) ? $matches[1] : null;

			case 'vimeo':
				preg_match( '/vimeo\.com\/(\d+)/', $url, $matches );
				return isset( $matches[1] ) ? $matches[1] : null;

			case 'panopto':
				// Extract Panopto session ID
				preg_match( '/Viewer\.aspx\?id=([a-f0-9-]+)/i', $url, $matches );
				return isset( $matches[1] ) ? $matches[1] : null;

			default:
				return null;
		}
	}
}

