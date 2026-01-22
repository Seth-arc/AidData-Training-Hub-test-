<?php
/**
 * Admin Test Class for Autoloader
 *
 * Test class to verify subdirectory autoloader functionality (admin).
 *
 * @package    AidData_LMS
 * @subpackage Admin
 * @since      2.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Admin_Test
 *
 * Test class for verifying autoloader works with the admin subdirectory.
 *
 * @since 2.0.0
 */
class AidData_LMS_Admin_Test {

	/**
	 * Test message
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $message = 'Admin subdirectory autoloader test successful';

	/**
	 * Test type
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $type = 'admin';

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		// Initialization code here
	}

	/**
	 * Get test message
	 *
	 * Returns a success message to verify the class loaded correctly.
	 *
	 * @since 2.0.0
	 * @return string Success message.
	 */
	public function get_message(): string {
		return $this->message;
	}

	/**
	 * Get test type
	 *
	 * Returns the type of test being run.
	 *
	 * @since 2.0.0
	 * @return string Test type.
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Run test
	 *
	 * Static method to verify autoloader functionality for admin classes.
	 *
	 * @since 2.0.0
	 * @return bool True if test passes.
	 */
	public static function run_test(): bool {
		$test = new self();
		return ! empty( $test->get_message() ) && 'admin' === $test->get_type();
	}

	/**
	 * Get test result
	 *
	 * Returns formatted test result with class information.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Test result data.
	 */
	public static function get_test_result(): array {
		return array(
			'class'       => __CLASS__,
			'file'        => __FILE__,
			'subdirectory' => 'admin',
			'status'      => 'success',
			'message'     => 'Admin subdirectory class autoloaded successfully',
		);
	}
}

