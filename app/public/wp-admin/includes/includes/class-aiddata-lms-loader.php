<?php
/**
 * Hook Loader
 *
 * Register all actions and filters for the plugin
 *
 * Maintains a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
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
 * Register all actions and filters for the plugin
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @since 2.0.0
 */
class AidData_LMS_Loader {

	/**
	 * The array of actions registered with WordPress
	 *
	 * @since  2.0.0
	 * @var    array
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress
	 *
	 * @since  2.0.0
	 * @var    array
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress
	 *
	 * @since  2.0.0
	 * @param  string $hook          The name of the WordPress action that is being registered
	 * @param  object $component     A reference to the instance of the object on which the action is defined
	 * @param  string $callback      The name of the function definition on the $component
	 * @param  int    $priority      Optional. The priority at which the function should be fired. Default is 10
	 * @param  int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
	 * @return void
	 */
	public function add_action( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress
	 *
	 * @since  2.0.0
	 * @param  string $hook          The name of the WordPress filter that is being registered
	 * @param  object $component     A reference to the instance of the object on which the filter is defined
	 * @param  string $callback      The name of the function definition on the $component
	 * @param  int    $priority      Optional. The priority at which the function should be fired. Default is 10
	 * @param  int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
	 * @return void
	 */
	public function add_filter( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection
	 *
	 * @since  2.0.0
	 * @param  array  $hooks         The collection of hooks that is being registered (that is, actions or filters)
	 * @param  string $hook          The name of the WordPress filter that is being registered
	 * @param  object $component     A reference to the instance of the object on which the filter is defined
	 * @param  string $callback      The name of the function definition on the $component
	 * @param  int    $priority      The priority at which the function should be fired
	 * @param  int    $accepted_args The number of arguments that should be passed to the $callback
	 * @return array The collection of actions and filters registered with WordPress
	 */
	private function add( array $hooks, string $hook, $component, string $callback, int $priority, int $accepted_args ): array {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}

	/**
	 * Get all registered actions
	 *
	 * @since  2.0.0
	 * @return array Array of registered actions
	 */
	public function get_actions(): array {
		return $this->actions;
	}

	/**
	 * Get all registered filters
	 *
	 * @since  2.0.0
	 * @return array Array of registered filters
	 */
	public function get_filters(): array {
		return $this->filters;
	}

	/**
	 * Get the total count of registered hooks (actions + filters)
	 *
	 * @since  2.0.0
	 * @return int Total number of registered hooks
	 */
	public function get_hook_count(): int {
		return count( $this->actions ) + count( $this->filters );
	}

	/**
	 * Remove all actions from the collection
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function clear_actions(): void {
		$this->actions = array();
	}

	/**
	 * Remove all filters from the collection
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function clear_filters(): void {
		$this->filters = array();
	}

	/**
	 * Remove all hooks (actions and filters) from the collection
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function clear_all(): void {
		$this->clear_actions();
		$this->clear_filters();
	}
}

