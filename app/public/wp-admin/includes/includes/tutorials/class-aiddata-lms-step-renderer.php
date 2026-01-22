<?php
/**
 * Step Content Renderer
 *
 * Handles rendering of different step types in active tutorial interface.
 *
 * @package AidData_LMS
 * @subpackage Tutorials
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Step Renderer Class
 */
class AidData_LMS_Step_Renderer {

	/**
	 * Render step content based on type
	 *
	 * @param array $step Step data array.
	 * @param int   $step_index Step index.
	 * @param int   $tutorial_id Tutorial post ID.
	 * @return string Rendered HTML content.
	 */
	public function render_step_content( array $step, int $step_index, int $tutorial_id ): string {
		ob_start();
		
		echo '<div class="step-inner">';
		
		// Step header
		echo '<div class="step-header">';
		echo '<h2 class="step-title">' . esc_html( $step['title'] ) . '</h2>';
		if ( ! empty( $step['description'] ) ) {
			echo '<p class="step-description">' . esc_html( $step['description'] ) . '</p>';
		}
		echo '</div>';
		
		// Step content based on type
		echo '<div class="step-body">';
		
		$step_type = isset( $step['type'] ) ? $step['type'] : 'text';
		
		switch ( $step_type ) {
			case 'video':
				$this->render_video_step( $step );
				break;
			
			case 'text':
				$this->render_text_step( $step );
				break;
			
			case 'interactive':
				$this->render_interactive_step( $step );
				break;
			
			case 'resource':
				$this->render_resource_step( $step );
				break;
			
			case 'quiz':
				$this->render_quiz_step( $step, $tutorial_id );
				break;
			
			default:
				echo '<p>' . esc_html__( 'Unknown step type.', 'aiddata-lms' ) . '</p>';
		}
		
		echo '</div>'; // .step-body
		echo '</div>'; // .step-inner
		
		return ob_get_clean();
	}

	/**
	 * Render video step
	 *
	 * @param array $step Step data.
	 */
	private function render_video_step( array $step ): void {
		$content = isset( $step['content'] ) ? $step['content'] : array();
		$platform = isset( $content['platform'] ) ? $content['platform'] : 'html5';
		$video_url = isset( $content['video_url'] ) ? $content['video_url'] : '';
		
		if ( empty( $video_url ) ) {
			echo '<p>' . esc_html__( 'No video URL provided.', 'aiddata-lms' ) . '</p>';
			return;
		}
		
		echo '<div class="video-container" data-platform="' . esc_attr( $platform ) . '" data-video-url="' . esc_url( $video_url ) . '">';
		
		// Video player will be initialized by JavaScript
		echo '<div class="video-player-wrapper">';
		
		// Fallback for direct video links
		if ( 'html5' === $platform ) {
			echo '<video controls width="100%">';
			echo '<source src="' . esc_url( $video_url ) . '">';
			echo esc_html__( 'Your browser does not support the video tag.', 'aiddata-lms' );
			echo '</video>';
		} else {
			// Placeholder for JavaScript-initialized players (YouTube, Vimeo, Panopto)
			echo '<div id="video-player" class="video-player-placeholder">';
			echo '<p>' . esc_html__( 'Loading video player...', 'aiddata-lms' ) . '</p>';
			echo '</div>';
		}
		
		echo '</div>'; // .video-player-wrapper
		echo '</div>'; // .video-container
		
		if ( ! empty( $content['description'] ) ) {
			echo '<div class="video-description">' . wp_kses_post( $content['description'] ) . '</div>';
		}
		
		if ( ! empty( $content['transcript'] ) ) {
			echo '<details class="video-transcript">';
			echo '<summary>' . esc_html__( 'View Transcript', 'aiddata-lms' ) . '</summary>';
			echo '<div class="transcript-content">' . wp_kses_post( $content['transcript'] ) . '</div>';
			echo '</details>';
		}
	}

	/**
	 * Render text step
	 *
	 * @param array $step Step data.
	 */
	private function render_text_step( array $step ): void {
		$content = isset( $step['content'] ) ? $step['content'] : array();
		$text_content = isset( $content['content'] ) ? $content['content'] : '';
		
		if ( empty( $text_content ) ) {
			echo '<p>' . esc_html__( 'No content available.', 'aiddata-lms' ) . '</p>';
			return;
		}
		
		echo '<div class="text-content">';
		echo wp_kses_post( $text_content );
		echo '</div>';
		
		// Show attachments if any
		if ( ! empty( $content['attachments'] ) && is_array( $content['attachments'] ) ) {
			echo '<div class="step-attachments">';
			echo '<h3>' . esc_html__( 'Attachments', 'aiddata-lms' ) . '</h3>';
			echo '<ul class="attachments-list">';
			foreach ( $content['attachments'] as $attachment_id ) {
				$file_url = wp_get_attachment_url( $attachment_id );
				$file_name = basename( get_attached_file( $attachment_id ) );
				if ( $file_url ) {
					echo '<li>';
					echo '<a href="' . esc_url( $file_url ) . '" target="_blank" class="attachment-link">';
					echo '<span class="dashicons dashicons-media-default"></span>';
					echo esc_html( $file_name );
					echo '</a>';
					echo '</li>';
				}
			}
			echo '</ul>';
			echo '</div>';
		}
	}

	/**
	 * Render interactive step
	 *
	 * @param array $step Step data.
	 */
	private function render_interactive_step( array $step ): void {
		$content = isset( $step['content'] ) ? $step['content'] : array();
		$interaction_type = isset( $content['interaction_type'] ) ? $content['interaction_type'] : 'iframe';
		
		if ( 'iframe' === $interaction_type || 'embed' === $interaction_type ) {
			if ( ! empty( $content['embed_code'] ) ) {
				echo '<div class="interactive-embed">';
				echo wp_kses_post( $content['embed_code'] );
				echo '</div>';
			} elseif ( ! empty( $content['url'] ) ) {
				$height = ! empty( $content['height'] ) ? absint( $content['height'] ) : 600;
				echo '<div class="interactive-iframe">';
				echo '<iframe src="' . esc_url( $content['url'] ) . '" width="100%" height="' . esc_attr( $height ) . '" frameborder="0" allowfullscreen></iframe>';
				echo '</div>';
			}
		}
		
		if ( ! empty( $content['instructions'] ) ) {
			echo '<div class="interactive-instructions">' . wp_kses_post( $content['instructions'] ) . '</div>';
		}
	}

	/**
	 * Render resource download step
	 *
	 * @param array $step Step data.
	 */
	private function render_resource_step( array $step ): void {
		$content = isset( $step['content'] ) ? $step['content'] : array();
		$resources = isset( $content['resources'] ) ? $content['resources'] : array();
		
		if ( empty( $resources ) ) {
			echo '<p>' . esc_html__( 'No resources available.', 'aiddata-lms' ) . '</p>';
			return;
		}
		
		if ( ! empty( $content['instructions'] ) ) {
			echo '<div class="resource-instructions">' . wp_kses_post( $content['instructions'] ) . '</div>';
		}
		
		echo '<div class="resources-list">';
		foreach ( $resources as $resource ) {
			$file_id = isset( $resource['file_id'] ) ? absint( $resource['file_id'] ) : 0;
			$file_url = wp_get_attachment_url( $file_id );
			
			if ( ! $file_url ) {
				continue;
			}
			
			$file_type = isset( $resource['file_type'] ) ? $resource['file_type'] : '';
			$icon_class = $this->get_file_icon_class( $file_type );
			
			echo '<div class="resource-item">';
			echo '<div class="resource-icon"><span class="dashicons ' . esc_attr( $icon_class ) . '"></span></div>';
			echo '<div class="resource-details">';
			echo '<h4>' . esc_html( isset( $resource['title'] ) ? $resource['title'] : __( 'Untitled', 'aiddata-lms' ) ) . '</h4>';
			if ( ! empty( $resource['description'] ) ) {
				echo '<p>' . esc_html( $resource['description'] ) . '</p>';
			}
			if ( ! empty( $resource['file_size'] ) ) {
				echo '<span class="file-size">' . esc_html( size_format( $resource['file_size'] ) ) . '</span>';
			}
			echo '<a href="' . esc_url( $file_url ) . '" class="button button-secondary download-resource" download>';
			echo '<span class="dashicons dashicons-download"></span> ';
			echo esc_html__( 'Download', 'aiddata-lms' );
			echo '</a>';
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Render quiz step (placeholder for Phase 4)
	 *
	 * @param array $step Step data.
	 * @param int   $tutorial_id Tutorial ID.
	 */
	private function render_quiz_step( array $step, int $tutorial_id ): void {
		echo '<div class="quiz-placeholder">';
		echo '<div class="placeholder-icon"><span class="dashicons dashicons-list-view"></span></div>';
		echo '<h3>' . esc_html__( 'Quiz Step', 'aiddata-lms' ) . '</h3>';
		echo '<p>' . esc_html__( 'Quiz functionality will be available in a future update.', 'aiddata-lms' ) . '</p>';
		echo '<p class="placeholder-note">' . esc_html__( 'For now, you can mark this step as complete to continue.', 'aiddata-lms' ) . '</p>';
		echo '</div>';
	}

	/**
	 * Get file icon class based on file type
	 *
	 * @param string $file_type File type/extension.
	 * @return string Dashicons class.
	 */
	private function get_file_icon_class( string $file_type ): string {
		$icons = array(
			'pdf'  => 'dashicons-pdf',
			'doc'  => 'dashicons-media-document',
			'docx' => 'dashicons-media-document',
			'xls'  => 'dashicons-media-spreadsheet',
			'xlsx' => 'dashicons-media-spreadsheet',
			'zip'  => 'dashicons-media-archive',
			'rar'  => 'dashicons-media-archive',
			'jpg'  => 'dashicons-format-image',
			'jpeg' => 'dashicons-format-image',
			'png'  => 'dashicons-format-image',
			'gif'  => 'dashicons-format-image',
			'mp4'  => 'dashicons-media-video',
			'mp3'  => 'dashicons-media-audio',
		);
		
		$file_type = strtolower( $file_type );
		
		return isset( $icons[ $file_type ] ) ? $icons[ $file_type ] : 'dashicons-media-default';
	}
}

