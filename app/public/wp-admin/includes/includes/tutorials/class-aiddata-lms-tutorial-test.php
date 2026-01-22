<?php
/**
 * Tutorial Test Class for Autoloader
 *
 * Test class to verify subdirectory autoloader functionality (tutorials).
 *
 * @package    AidData_LMS
 * @subpackage Tutorials
 * @since      2.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Tutorial_Test
 *
 * Test class for verifying autoloader works with the tutorials subdirectory.
 *
 * @since 2.0.0
 */
class AidData_LMS_Tutorial_Test {

	/**
	 * Test message
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $message = 'Tutorial subdirectory autoloader test successful';

	/**
	 * Test type
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $type = 'tutorial';

	/**
	 * Test data
	 *
	 * @since 2.0.0
	 * @var array<string, mixed>
	 */
	private $data = array();

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->data = array(
			'subdirectory' => 'tutorials',
			'prefix'       => 'Tutorial',
			'mapped'       => true,
		);
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
	 * Get test data
	 *
	 * Returns test data array.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Test data.
	 */
	public function get_data(): array {
		return $this->data;
	}

	/**
	 * Run test
	 *
	 * Static method to verify autoloader functionality for tutorial classes.
	 *
	 * @since 2.0.0
	 * @return bool True if test passes.
	 */
	public static function run_test(): bool {
		$test = new self();
		$data = $test->get_data();
		return ! empty( $test->get_message() ) 
			&& 'tutorial' === $test->get_type()
			&& isset( $data['mapped'] ) && true === $data['mapped'];
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
		$test = new self();
		return array(
			'class'        => __CLASS__,
			'file'         => __FILE__,
			'subdirectory' => 'tutorials',
			'status'       => 'success',
			'message'      => 'Tutorial subdirectory class autoloaded successfully',
			'data'         => $test->get_data(),
		);
	}
}

