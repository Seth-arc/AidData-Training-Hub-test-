<?php
/**
 * Main Plugin Class - Singleton Pattern
 *
 * This is the core plugin class that initializes all components,
 * registers hooks, and manages the plugin lifecycle.
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The core plugin class
 *
 * This class defines all code necessary to run during the plugin's activation,
 * initialization, and defines all of the hooks related to both the admin area
 * and the public-facing side of the site.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since 2.0.0
 */
class AidData_LMS {

	/**
	 * The single instance of the class
	 *
	 * @since  2.0.0
	 * @var    AidData_LMS|null
	 */
	private static $instance = null;

	/**
	 * The loader that's responsible for maintaining and registering all hooks
	 *
	 * @since  2.0.0
	 * @var    AidData_LMS_Loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin
	 *
	 * @since  2.0.0
	 * @var    string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin
	 *
	 * @since  2.0.0
	 * @var    string
	 */
	protected $version;

	/**
	 * Dependency injection container
	 *
	 * @since  2.0.0
	 * @var    array
	 */
	private $container = array();

	/**
	 * Main plugin instance
	 *
	 * Ensures only one instance of the plugin is loaded or can be loaded
	 *
	 * @since  2.0.0
	 * @return AidData_LMS Main plugin instance
	 */
	public static function instance(): AidData_LMS {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Clone
	 *
	 * Cloning instances of the class is forbidden
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'aiddata-lms' ), '2.0.0' );
	}

	/**
	 * Wakeup
	 *
	 * Unserializing instances of the class is forbidden
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances is forbidden.', 'aiddata-lms' ), '2.0.0' );
	}

	/**
	 * Constructor
	 *
	 * Define the core functionality of the plugin
	 *
	 * @since 2.0.0
	 */
	private function __construct() {
		$this->version     = AIDDATA_LMS_VERSION;
		$this->plugin_name = 'aiddata-lms';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin
	 *
	 * Include the following files that make up the plugin:
	 * - AidData_LMS_Loader: Orchestrates the hooks of the plugin
	 * - AidData_LMS_i18n: Defines internationalization functionality
	 * - AidData_LMS_Post_Types: Registers custom post types
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress
	 *
	 * @since  2.0.0
	 * @return void
	 */
	private function load_dependencies(): void {
		// The class responsible for orchestrating the actions and filters of the core plugin
		$this->loader = new AidData_LMS_Loader();

		// Register taxonomies (before post types)
		new AidData_LMS_Taxonomies();

		// Register post types
		new AidData_LMS_Post_Types();

		// Initialize AJAX handlers
		if ( wp_doing_ajax() ) {
			new AidData_LMS_Tutorial_AJAX();
		}

		// Initialize email queue system
		new AidData_LMS_Email_Queue();

		// Initialize email notification system
		new AidData_LMS_Email_Notifications();

		// Initialize analytics tracking system
		new AidData_LMS_Analytics();
	}

	/**
	 * Define the locale for this plugin for internationalization
	 *
	 * Uses the AidData_LMS_i18n class in order to set the domain and to register the hook
	 * with WordPress
	 *
	 * @since  2.0.0
	 * @return void
	 */
	private function set_locale(): void {
		$plugin_i18n = new AidData_LMS_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain', 10, 0 );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin
	 *
	 * @since  2.0.0
	 * @return void
	 */
	private function define_admin_hooks(): void {
		// Admin hooks will be registered here as modules are implemented
		// Example: Admin menu, admin scripts/styles, meta boxes, etc.
		
		// Register Phase 0 Validation page
		if ( is_admin() ) {
			new AidData_LMS_Admin_Validation_Page();
			
			// Initialize dashboard widgets (Prompt 8)
			new AidData_LMS_Admin_Dashboard();
			
			// Initialize reports page (Prompt 8)
			new AidData_LMS_Admin_Reports();
			
			// Initialize tutorial meta boxes (Phase 2, Prompt 1)
			new AidData_LMS_Tutorial_Meta_Boxes();
		}
		
		/**
		 * Allow other plugins/modules to add admin hooks
		 *
		 * @since 2.0.0
		 * @param AidData_LMS       $this   The main plugin instance
		 * @param AidData_LMS_Loader $loader The loader instance
		 */
		do_action( 'aiddata_lms_admin_hooks', $this, $this->loader );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin
	 *
	 * @since  2.0.0
	 * @return void
	 */
	private function define_public_hooks(): void {
		// Public hooks will be registered here as modules are implemented
		// Example: Frontend scripts/styles, shortcodes, custom queries, etc.
		
		// Initialize frontend assets
		new AidData_LMS_Frontend_Assets();
		
		/**
		 * Allow other plugins/modules to add public hooks
		 *
		 * @since 2.0.0
		 * @param AidData_LMS       $this   The main plugin instance
		 * @param AidData_LMS_Loader $loader The loader instance
		 */
		do_action( 'aiddata_lms_public_hooks', $this, $this->loader );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function run(): void {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality
	 *
	 * @since  2.0.0
	 * @return string The name of the plugin
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin
	 *
	 * @since  2.0.0
	 * @return AidData_LMS_Loader Orchestrates the hooks of the plugin
	 */
	public function get_loader(): AidData_LMS_Loader {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin
	 *
	 * @since  2.0.0
	 * @return string The version number of the plugin
	 */
	public function get_version(): string {
		return $this->version;
	}

	/**
	 * Set a value in the dependency injection container
	 *
	 * @since  2.0.0
	 * @param  string $key   The container key
	 * @param  mixed  $value The value to store
	 * @return void
	 */
	public function set( string $key, $value ): void {
		$this->container[ $key ] = $value;
	}

	/**
	 * Get a value from the dependency injection container
	 *
	 * @since  2.0.0
	 * @param  string $key The container key
	 * @return mixed|null The stored value or null if not found
	 */
	public function get( string $key ) {
		return $this->has( $key ) ? $this->container[ $key ] : null;
	}

	/**
	 * Check if a key exists in the dependency injection container
	 *
	 * @since  2.0.0
	 * @param  string $key The container key
	 * @return bool True if the key exists, false otherwise
	 */
	public function has( string $key ): bool {
		return isset( $this->container[ $key ] );
	}

	/**
	 * Remove a value from the dependency injection container
	 *
	 * @since  2.0.0
	 * @param  string $key The container key
	 * @return void
	 */
	public function remove( string $key ): void {
		if ( $this->has( $key ) ) {
			unset( $this->container[ $key ] );
		}
	}

	/**
	 * Get all container keys
	 *
	 * @since  2.0.0
	 * @return array Array of all container keys
	 */
	public function get_container_keys(): array {
		return array_keys( $this->container );
	}

	/**
	 * Clear all values from the dependency injection container
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function clear_container(): void {
		$this->container = array();
	}
}

