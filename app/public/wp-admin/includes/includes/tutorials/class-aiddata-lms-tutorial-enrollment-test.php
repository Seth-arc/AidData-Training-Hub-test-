<?php
/**
 * Enrollment Manager Test Suite
 *
 * Tests for the AidData_LMS_Tutorial_Enrollment class.
 *
 * @package AidData_LMS
 * @subpackage Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Tutorial_Enrollment_Test
 *
 * Test suite for enrollment manager functionality.
 *
 * @since 1.0.0
 */
class AidData_LMS_Tutorial_Enrollment_Test {

	/**
	 * Enrollment manager instance.
	 *
	 * @var AidData_LMS_Tutorial_Enrollment
	 */
	private $enrollment_manager;

	/**
	 * Test results.
	 *
	 * @var array
	 */
	private $results = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
	}

	/**
	 * Run all tests.
	 *
	 * @since 1.0.0
	 * @return array Test results.
	 */
	public function run_all_tests(): array {
		$this->results = array();

		// Basic functionality tests.
		$this->test_class_instantiation();
		$this->test_table_name_initialization();
		
		// Enrollment tests.
		$this->test_enroll_user_success();
		$this->test_enroll_user_duplicate();
		$this->test_enroll_invalid_user();
		$this->test_enroll_invalid_tutorial();
		
		// Unenrollment tests.
		$this->test_unenroll_user_success();
		$this->test_unenroll_not_enrolled();
		
		// Query tests.
		$this->test_get_user_enrollments();
		$this->test_get_tutorial_enrollments();
		$this->test_is_user_enrolled();
		$this->test_get_enrollment();
		$this->test_get_enrollment_count();
		
		// Status management tests.
		$this->test_update_enrollment_status();
		$this->test_mark_completed();
		
		// Metadata tests.
		$this->test_enrollment_metadata();
		
		// Validation tests.
		$this->test_validation_checks();

		return $this->results;
	}

	/**
	 * Test class instantiation.
	 *
	 * @since 1.0.0
	 */
	private function test_class_instantiation(): void {
		$test_name = 'Class Instantiation';
		
		try {
			$manager = new AidData_LMS_Tutorial_Enrollment();
			$this->add_result( $test_name, true, 'Class instantiated successfully' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, 'Failed to instantiate class: ' . $e->getMessage() );
		}
	}

	/**
	 * Test table name initialization.
	 *
	 * @since 1.0.0
	 */
	private function test_table_name_initialization(): void {
		$test_name = 'Table Name Initialization';
		
		if ( ! empty( $this->enrollment_manager->table_name ) ) {
			global $wpdb;
			$expected_table = $wpdb->prefix . AidData_LMS_Database::TABLE_ENROLLMENTS;
			
			if ( $this->enrollment_manager->table_name === $expected_table ) {
				$this->add_result( $test_name, true, 'Table name initialized correctly' );
			} else {
				$this->add_result( $test_name, false, 'Table name mismatch' );
			}
		} else {
			$this->add_result( $test_name, false, 'Table name not initialized' );
		}
	}

	/**
	 * Test successful user enrollment.
	 *
	 * @since 1.0.0
	 */
	private function test_enroll_user_success(): void {
		$test_name = 'Enroll User - Success';
		
		// Create test user and tutorial.
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$result = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $result ) && $result > 0 ) {
				$this->add_result( $test_name, true, 'User enrolled successfully, ID: ' . $result );
				
				// Cleanup.
				$this->cleanup_test_enrollment( $result );
			} else {
				$error_message = is_wp_error( $result ) ? $result->get_error_message() : 'Unknown error';
				$this->add_result( $test_name, false, 'Enrollment failed: ' . $error_message );
			}
			
			// Cleanup.
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test user or tutorial' );
		}
	}

	/**
	 * Test duplicate enrollment prevention.
	 *
	 * @since 1.0.0
	 */
	private function test_enroll_user_duplicate(): void {
		$test_name = 'Enroll User - Duplicate Prevention';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			// First enrollment.
			$result1 = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			// Attempt duplicate enrollment.
			$result2 = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_wp_error( $result2 ) && 'enrollment_invalid' === $result2->get_error_code() ) {
				$this->add_result( $test_name, true, 'Duplicate enrollment prevented correctly' );
			} else {
				$this->add_result( $test_name, false, 'Duplicate enrollment not prevented' );
			}
			
			// Cleanup.
			if ( is_int( $result1 ) ) {
				$this->cleanup_test_enrollment( $result1 );
			}
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test enrollment with invalid user.
	 *
	 * @since 1.0.0
	 */
	private function test_enroll_invalid_user(): void {
		$test_name = 'Enroll User - Invalid User';
		
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $tutorial_id ) {
			$result = $this->enrollment_manager->enroll_user( 999999, $tutorial_id, 'test' );
			
			if ( is_wp_error( $result ) ) {
				$this->add_result( $test_name, true, 'Invalid user rejected correctly' );
			} else {
				$this->add_result( $test_name, false, 'Invalid user not rejected' );
			}
			
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test tutorial' );
		}
	}

	/**
	 * Test enrollment with invalid tutorial.
	 *
	 * @since 1.0.0
	 */
	private function test_enroll_invalid_tutorial(): void {
		$test_name = 'Enroll User - Invalid Tutorial';
		
		$user_id = $this->create_test_user();
		
		if ( $user_id ) {
			$result = $this->enrollment_manager->enroll_user( $user_id, 999999, 'test' );
			
			if ( is_wp_error( $result ) ) {
				$this->add_result( $test_name, true, 'Invalid tutorial rejected correctly' );
			} else {
				$this->add_result( $test_name, false, 'Invalid tutorial not rejected' );
			}
			
			$this->cleanup_test_user( $user_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test user' );
		}
	}

	/**
	 * Test successful unenrollment.
	 *
	 * @since 1.0.0
	 */
	private function test_unenroll_user_success(): void {
		$test_name = 'Unenroll User - Success';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			// Enroll first.
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $enrollment_id ) ) {
				// Unenroll.
				$result = $this->enrollment_manager->unenroll_user( $user_id, $tutorial_id );
				
				if ( true === $result ) {
					$this->add_result( $test_name, true, 'User unenrolled successfully' );
				} else {
					$error_message = is_wp_error( $result ) ? $result->get_error_message() : 'Unknown error';
					$this->add_result( $test_name, false, 'Unenrollment failed: ' . $error_message );
				}
				
				$this->cleanup_test_enrollment( $enrollment_id );
			} else {
				$this->add_result( $test_name, false, 'Failed to enroll user for test' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test unenrollment when not enrolled.
	 *
	 * @since 1.0.0
	 */
	private function test_unenroll_not_enrolled(): void {
		$test_name = 'Unenroll User - Not Enrolled';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$result = $this->enrollment_manager->unenroll_user( $user_id, $tutorial_id );
			
			if ( is_wp_error( $result ) && 'not_enrolled' === $result->get_error_code() ) {
				$this->add_result( $test_name, true, 'Not enrolled error returned correctly' );
			} else {
				$this->add_result( $test_name, false, 'Expected WP_Error not returned' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test get user enrollments.
	 *
	 * @since 1.0.0
	 */
	private function test_get_user_enrollments(): void {
		$test_name = 'Get User Enrollments';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $enrollment_id ) ) {
				$enrollments = $this->enrollment_manager->get_user_enrollments( $user_id, 'active' );
				
				if ( is_array( $enrollments ) && count( $enrollments ) > 0 ) {
					$this->add_result( $test_name, true, 'User enrollments retrieved successfully' );
				} else {
					$this->add_result( $test_name, false, 'No enrollments found' );
				}
				
				$this->cleanup_test_enrollment( $enrollment_id );
			} else {
				$this->add_result( $test_name, false, 'Failed to create enrollment' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test get tutorial enrollments.
	 *
	 * @since 1.0.0
	 */
	private function test_get_tutorial_enrollments(): void {
		$test_name = 'Get Tutorial Enrollments';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $enrollment_id ) ) {
				$enrollments = $this->enrollment_manager->get_tutorial_enrollments( $tutorial_id, 'active' );
				
				if ( is_array( $enrollments ) && count( $enrollments ) > 0 ) {
					$this->add_result( $test_name, true, 'Tutorial enrollments retrieved successfully' );
				} else {
					$this->add_result( $test_name, false, 'No enrollments found' );
				}
				
				$this->cleanup_test_enrollment( $enrollment_id );
			} else {
				$this->add_result( $test_name, false, 'Failed to create enrollment' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test is user enrolled.
	 *
	 * @since 1.0.0
	 */
	private function test_is_user_enrolled(): void {
		$test_name = 'Is User Enrolled';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			// Check before enrollment.
			$is_enrolled_before = $this->enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );
			
			// Enroll.
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			// Check after enrollment.
			$is_enrolled_after = $this->enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );
			
			if ( ! $is_enrolled_before && $is_enrolled_after ) {
				$this->add_result( $test_name, true, 'Enrollment status check working correctly' );
			} else {
				$this->add_result( $test_name, false, 'Enrollment status check failed' );
			}
			
			if ( is_int( $enrollment_id ) ) {
				$this->cleanup_test_enrollment( $enrollment_id );
			}
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test get enrollment.
	 *
	 * @since 1.0.0
	 */
	private function test_get_enrollment(): void {
		$test_name = 'Get Enrollment';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $enrollment_id ) ) {
				$enrollment = $this->enrollment_manager->get_enrollment( $user_id, $tutorial_id );
				
				if ( is_object( $enrollment ) && $enrollment->id === $enrollment_id ) {
					$this->add_result( $test_name, true, 'Enrollment retrieved successfully' );
				} else {
					$this->add_result( $test_name, false, 'Failed to retrieve enrollment' );
				}
				
				$this->cleanup_test_enrollment( $enrollment_id );
			} else {
				$this->add_result( $test_name, false, 'Failed to create enrollment' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test get enrollment count.
	 *
	 * @since 1.0.0
	 */
	private function test_get_enrollment_count(): void {
		$test_name = 'Get Enrollment Count';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$count_before = $this->enrollment_manager->get_enrollment_count( $tutorial_id, 'active' );
			
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			$count_after = $this->enrollment_manager->get_enrollment_count( $tutorial_id, 'active' );
			
			if ( $count_after === ( $count_before + 1 ) ) {
				$this->add_result( $test_name, true, 'Enrollment count accurate' );
			} else {
				$this->add_result( $test_name, false, 'Enrollment count mismatch' );
			}
			
			if ( is_int( $enrollment_id ) ) {
				$this->cleanup_test_enrollment( $enrollment_id );
			}
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test update enrollment status.
	 *
	 * @since 1.0.0
	 */
	private function test_update_enrollment_status(): void {
		$test_name = 'Update Enrollment Status';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $enrollment_id ) ) {
				$result = $this->enrollment_manager->update_enrollment_status( $enrollment_id, 'completed' );
				
				if ( true === $result ) {
					$this->add_result( $test_name, true, 'Enrollment status updated successfully' );
				} else {
					$error_message = is_wp_error( $result ) ? $result->get_error_message() : 'Unknown error';
					$this->add_result( $test_name, false, 'Status update failed: ' . $error_message );
				}
				
				$this->cleanup_test_enrollment( $enrollment_id );
			} else {
				$this->add_result( $test_name, false, 'Failed to create enrollment' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test mark completed.
	 *
	 * @since 1.0.0
	 */
	private function test_mark_completed(): void {
		$test_name = 'Mark Completed';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $enrollment_id ) ) {
				$result = $this->enrollment_manager->mark_completed( $user_id, $tutorial_id );
				
				if ( true === $result ) {
					// Verify completion.
					$enrollment = $this->enrollment_manager->get_enrollment( $user_id, $tutorial_id );
					
					if ( $enrollment && 'completed' === $enrollment->status && ! empty( $enrollment->completed_at ) ) {
						$this->add_result( $test_name, true, 'Enrollment marked as completed successfully' );
					} else {
						$this->add_result( $test_name, false, 'Completion not verified in database' );
					}
				} else {
					$error_message = is_wp_error( $result ) ? $result->get_error_message() : 'Unknown error';
					$this->add_result( $test_name, false, 'Mark completed failed: ' . $error_message );
				}
				
				$this->cleanup_test_enrollment( $enrollment_id );
			} else {
				$this->add_result( $test_name, false, 'Failed to create enrollment' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test enrollment metadata.
	 *
	 * @since 1.0.0
	 */
	private function test_enrollment_metadata(): void {
		$test_name = 'Enrollment Metadata';
		
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial();
		
		if ( $user_id && $tutorial_id ) {
			$enrollment_id = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_int( $enrollment_id ) ) {
				// Add metadata.
				$add_result = $this->enrollment_manager->add_enrollment_meta( $enrollment_id, 'test_key', 'test_value' );
				
				// Get metadata.
				$meta_value = $this->enrollment_manager->get_enrollment_meta( $enrollment_id, 'test_key' );
				
				// Update metadata.
				$update_result = $this->enrollment_manager->update_enrollment_meta( $enrollment_id, 'test_key', 'updated_value' );
				
				// Get updated metadata.
				$updated_value = $this->enrollment_manager->get_enrollment_meta( $enrollment_id, 'test_key' );
				
				if ( $add_result && 'test_value' === $meta_value && $update_result && 'updated_value' === $updated_value ) {
					$this->add_result( $test_name, true, 'Enrollment metadata working correctly' );
				} else {
					$this->add_result( $test_name, false, 'Enrollment metadata operations failed' );
				}
				
				$this->cleanup_test_enrollment( $enrollment_id );
			} else {
				$this->add_result( $test_name, false, 'Failed to create enrollment' );
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Test validation checks.
	 *
	 * @since 1.0.0
	 */
	private function test_validation_checks(): void {
		$test_name = 'Validation Checks';
		
		// Test with unpublished tutorial.
		$user_id     = $this->create_test_user();
		$tutorial_id = $this->create_test_tutorial( 'draft' );
		
		if ( $user_id && $tutorial_id ) {
			$result = $this->enrollment_manager->enroll_user( $user_id, $tutorial_id, 'test' );
			
			if ( is_wp_error( $result ) ) {
				$this->add_result( $test_name, true, 'Validation prevents enrollment in unpublished tutorial' );
			} else {
				$this->add_result( $test_name, false, 'Validation did not prevent enrollment in unpublished tutorial' );
				if ( is_int( $result ) ) {
					$this->cleanup_test_enrollment( $result );
				}
			}
			
			$this->cleanup_test_user( $user_id );
			$this->cleanup_test_tutorial( $tutorial_id );
		} else {
			$this->add_result( $test_name, false, 'Failed to create test data' );
		}
	}

	/**
	 * Create test user.
	 *
	 * @since 1.0.0
	 * @return int|false User ID on success, false on failure.
	 */
	private function create_test_user() {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'testuser_' . time() . '_' . wp_rand( 1000, 9999 ),
				'user_pass'  => wp_generate_password(),
				'user_email' => 'test_' . time() . '@example.com',
			)
		);

		return is_wp_error( $user_id ) ? false : $user_id;
	}

	/**
	 * Create test tutorial.
	 *
	 * @since 1.0.0
	 * @param string $status Post status. Default 'publish'.
	 * @return int|false Tutorial ID on success, false on failure.
	 */
	private function create_test_tutorial( string $status = 'publish' ) {
		$tutorial_id = wp_insert_post(
			array(
				'post_title'   => 'Test Tutorial ' . time(),
				'post_content' => 'Test tutorial content',
				'post_status'  => $status,
				'post_type'    => 'aiddata_tutorial',
			)
		);

		return is_wp_error( $tutorial_id ) ? false : $tutorial_id;
	}

	/**
	 * Cleanup test user.
	 *
	 * @since 1.0.0
	 * @param int $user_id User ID.
	 */
	private function cleanup_test_user( int $user_id ): void {
		wp_delete_user( $user_id );
	}

	/**
	 * Cleanup test tutorial.
	 *
	 * @since 1.0.0
	 * @param int $tutorial_id Tutorial ID.
	 */
	private function cleanup_test_tutorial( int $tutorial_id ): void {
		wp_delete_post( $tutorial_id, true );
	}

	/**
	 * Cleanup test enrollment.
	 *
	 * @since 1.0.0
	 * @param int $enrollment_id Enrollment ID.
	 */
	private function cleanup_test_enrollment( int $enrollment_id ): void {
		global $wpdb;
		$wpdb->delete(
			$this->enrollment_manager->table_name,
			array( 'id' => $enrollment_id ),
			array( '%d' )
		);
	}

	/**
	 * Add test result.
	 *
	 * @since 1.0.0
	 * @param string $test_name Test name.
	 * @param bool   $passed    Whether test passed.
	 * @param string $message   Result message.
	 */
	private function add_result( string $test_name, bool $passed, string $message ): void {
		$this->results[] = array(
			'test'    => $test_name,
			'passed'  => $passed,
			'message' => $message,
		);
	}

	/**
	 * Display test results.
	 *
	 * @since 1.0.0
	 */
	public function display_results(): void {
		$total_tests  = count( $this->results );
		$passed_tests = count( array_filter( $this->results, fn( $r ) => $r['passed'] ) );
		$failed_tests = $total_tests - $passed_tests;

		echo '<div class="wrap">';
		echo '<h1>Enrollment Manager Test Results</h1>';
		echo '<p><strong>Total Tests:</strong> ' . esc_html( $total_tests ) . '</p>';
		echo '<p><strong>Passed:</strong> <span style="color: green;">' . esc_html( $passed_tests ) . '</span></p>';
		echo '<p><strong>Failed:</strong> <span style="color: red;">' . esc_html( $failed_tests ) . '</span></p>';

		echo '<table class="widefat" style="margin-top: 20px;">';
		echo '<thead><tr><th>Test</th><th>Status</th><th>Message</th></tr></thead>';
		echo '<tbody>';

		foreach ( $this->results as $result ) {
			$status_color = $result['passed'] ? 'green' : 'red';
			$status_text  = $result['passed'] ? '✓ PASS' : '✗ FAIL';

			echo '<tr>';
			echo '<td>' . esc_html( $result['test'] ) . '</td>';
			echo '<td style="color: ' . esc_attr( $status_color ) . '; font-weight: bold;">' . esc_html( $status_text ) . '</td>';
			echo '<td>' . esc_html( $result['message'] ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody>';
		echo '</table>';
		echo '</div>';
	}
}

