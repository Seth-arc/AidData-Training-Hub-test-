<?php
/**
 * Autoloader for AidData LMS Plugin
 *
 * Implements PSR-4 compliant autoloading for all plugin classes.
 * Maps the AidData_LMS namespace to the /includes/ directory.
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
 * Class AidData_LMS_Autoloader
 *
 * PSR-4 compliant autoloader for the AidData LMS plugin.
 * Handles automatic loading of classes based on namespace and file naming conventions.
 *
 * Mapping Logic:
 * - AidData_LMS_Tutorial → /includes/class-aiddata-lms-tutorial.php
 * - AidData_LMS_Tutorial_Enrollment → /includes/tutorials/class-aiddata-lms-tutorial-enrollment.php
 * - AidData_LMS_Video_Tracker → /includes/video/class-aiddata-lms-video-tracker.php
 *
 * @since 2.0.0
 */
class AidData_LMS_Autoloader {

	/**
	 * Base namespace for the plugin
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private static $namespace = 'AidData_LMS';

	/**
	 * Base directory for the plugin classes
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private static $base_dir = '';

	/**
	 * Subdirectory mapping for class prefixes
	 *
	 * Maps class name prefixes to their subdirectories for more efficient loading.
	 *
	 * @since 2.0.0
	 * @var array<string, string>
	 */
	private static $subdir_map = array(
		'Admin'        => 'admin',
		'Tutorial'     => 'tutorials',
		'Video'        => 'video',
		'Quiz'         => 'quiz',
		'Certificate'  => 'certificates',
		'Email'        => 'email',
		'Analytics'    => 'analytics',
		'API'          => 'api',
		'REST'         => 'api',
		'Gutenberg'    => 'gutenberg',
		'Block'        => 'gutenberg',
	);

	/**
	 * Register the autoloader
	 *
	 * Registers the autoload method with PHP's SPL autoloader.
	 *
	 * @since 2.0.0
	 * @return bool True if registration successful, false otherwise.
	 */
	public static function register(): bool {
		// Set the base directory
		// Use plugin_dir_path if available (WordPress context), otherwise use __DIR__
		if ( function_exists( 'plugin_dir_path' ) ) {
			self::$base_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/';
		} else {
			self::$base_dir = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
		}

		// Register the autoload function
		return spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoload class files
	 *
	 * PSR-4 compliant autoloading that converts class names to file paths.
	 * Handles nested namespaces and converts underscores to directory separators.
	 *
	 * @since 2.0.0
	 * @param string $class The fully-qualified class name.
	 * @return void
	 */
	public static function autoload( string $class ): void {
		// Check if the class belongs to this plugin's namespace
		if ( strpos( $class, self::$namespace ) !== 0 ) {
			return;
		}

		// Remove the base namespace (with underscore separator)
		$class_name = substr( $class, strlen( self::$namespace ) + 1 );

		// Handle empty class name
		if ( empty( $class_name ) ) {
			return;
		}

		// Build the file path
		$file_path = self::build_file_path( $class_name );

		// Load the file if it exists
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}

	/**
	 * Build file path from class name
	 *
	 * Converts a class name to a file path following WordPress naming conventions:
	 * - Splits class name by underscores
	 * - Maps first segment to subdirectory if applicable
	 * - Converts class name to lowercase with hyphens
	 * - Adds 'class-' prefix
	 *
	 * @since 2.0.0
	 * @param string $class_name The class name (without base namespace).
	 * @return string The full file path.
	 */
	private static function build_file_path( string $class_name ): string {
		// Split the class name by underscores
		$parts = explode( '_', $class_name );

		// Determine subdirectory based on first part(s)
		$subdir = '';
		if ( count( $parts ) > 1 ) {
			$first_part = $parts[0];
			
			// Check if the first part maps to a subdirectory
			if ( isset( self::$subdir_map[ $first_part ] ) ) {
				$subdir = self::$subdir_map[ $first_part ] . '/';
			}
		}

		// Convert class name to file name (lowercase with hyphens)
		$file_name = 'class-aiddata-lms-' . self::class_name_to_file_name( $class_name ) . '.php';

		// Build the full path
		$file_path = self::$base_dir . $subdir . $file_name;

		return $file_path;
	}

	/**
	 * Convert class name to file name
	 *
	 * Converts a class name from Class_Name format to class-name format.
	 *
	 * @since 2.0.0
	 * @param string $class_name The class name.
	 * @return string The file name (without extension or prefix).
	 */
	private static function class_name_to_file_name( string $class_name ): string {
		// Convert underscores to hyphens and lowercase
		return strtolower( str_replace( '_', '-', $class_name ) );
	}

	/**
	 * Get the base directory for classes
	 *
	 * @since 2.0.0
	 * @return string The base directory path.
	 */
	public static function get_base_dir(): string {
		return self::$base_dir;
	}

	/**
	 * Get the subdirectory map
	 *
	 * @since 2.0.0
	 * @return array<string, string> The subdirectory mapping.
	 */
	public static function get_subdir_map(): array {
		return self::$subdir_map;
	}

	/**
	 * Add a custom subdirectory mapping
	 *
	 * Allows adding custom mappings for class prefixes to subdirectories.
	 *
	 * @since 2.0.0
	 * @param string $prefix The class name prefix (e.g., 'Tutorial').
	 * @param string $subdir The subdirectory name (e.g., 'tutorials').
	 * @return void
	 */
	public static function add_subdir_mapping( string $prefix, string $subdir ): void {
		self::$subdir_map[ $prefix ] = $subdir;
	}
}

