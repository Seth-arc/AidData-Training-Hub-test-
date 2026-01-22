<?php
/**
 * Tutorial Step Renderer
 *
 * Handles rendering of different tutorial step types
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tutorial Step Renderer Class
 */
class AidData_LMS_Tutorial_Step_Renderer {

	/**
	 * Render step content based on type
	 *
	 * @param array $step Step data.
	 * @param int   $step_index Step index.
	 * @param int   $tutorial_id Tutorial ID.
	 * @return string Rendered HTML.
	 */
	public function render_step_content( $step, $step_index, $tutorial_id ) {
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
	private function render_video_step( $step ) {
		$content   = isset( $step['content'] ) ? $step['content'] : array();
		$platform  = isset( $content['platform'] ) ? $content['platform'] : 'html5';
		$video_url = isset( $content['video_url'] ) ? $content['video_url'] : '';
		
		if ( empty( $video_url ) ) {
			echo '<p>' . esc_html__( 'No video URL provided.', 'aiddata-lms' ) . '</p>';
			return;
		}
		
		echo '<div class="video-container" data-platform="' . esc_attr( $platform ) . '" data-video-url="' . esc_url( $video_url ) . '">';
		echo '<div id="video-player"></div>';
		echo '</div>';
		
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
	private function render_text_step( $step ) {
		$content      = isset( $step['content'] ) ? $step['content'] : array();
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
			echo '<ul>';
			foreach ( $content['attachments'] as $attachment_id ) {
				$file_url  = wp_get_attachment_url( $attachment_id );
				$file_name = basename( get_attached_file( $attachment_id ) );
				if ( $file_url ) {
					echo '<li><a href="' . esc_url( $file_url ) . '" target="_blank">' . esc_html( $file_name ) . '</a></li>';
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
	private function render_interactive_step( $step ) {
		$content          = isset( $step['content'] ) ? $step['content'] : array();
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
	 * Render resource step
	 *
	 * @param array $step Step data.
	 */
	private function render_resource_step( $step ) {
		$content   = isset( $step['content'] ) ? $step['content'] : array();
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
			$file_id  = isset( $resource['file_id'] ) ? $resource['file_id'] : 0;
			$file_url = wp_get_attachment_url( $file_id );
			
			if ( ! $file_url ) {
				continue;
			}
			
			echo '<div class="resource-item">';
			echo '<div class="resource-icon"><span class="dashicons dashicons-download"></span></div>';
			echo '<div class="resource-details">';
			echo '<h4>' . esc_html( isset( $resource['title'] ) ? $resource['title'] : 'Untitled' ) . '</h4>';
			if ( ! empty( $resource['description'] ) ) {
				echo '<p>' . esc_html( $resource['description'] ) . '</p>';
			}
			echo '<a href="' . esc_url( $file_url ) . '" class="button button-secondary" download>' . esc_html__( 'Download', 'aiddata-lms' ) . '</a>';
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
	private function render_quiz_step( $step, $tutorial_id ) {
		echo '<div class="quiz-placeholder">';
		echo '<p>' . esc_html__( 'Quiz functionality will be available in a future update.', 'aiddata-lms' ) . '</p>';
		echo '</div>';
	}
}

