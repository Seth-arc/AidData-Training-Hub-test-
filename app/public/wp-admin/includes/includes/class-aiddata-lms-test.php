<?php
/**
 * Base Test Class for Autoloader
 *
 * Simple test class to verify base-level autoloader functionality.
 *
 * @package    AidData_LMS
 * @subpackage Core
 * @since      2.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Test
 *
 * Base test class for verifying autoloader works at the root includes directory.
 *
 * @since 2.0.0
 */
class AidData_LMS_Test {

	/**
	 * Test message
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $message = 'Base autoloader test successful';

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
	 * Run test
	 *
	 * Static method to verify autoloader functionality.
	 *
	 * @since 2.0.0
	 * @return bool True if test passes.
	 */
	public static function run_test(): bool {
		$test = new self();
		return ! empty( $test->get_message() );
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
			'class'   => __CLASS__,
			'file'    => __FILE__,
			'status'  => 'success',
			'message' => 'Base level class autoloaded successfully',
		);
	}
}

