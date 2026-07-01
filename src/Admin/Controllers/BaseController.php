<?php
/**
 * Base controller for EventLayer admin functionality.
 *
 * @package EventLayer
 */

namespace EventLayer\Admin\Controllers;

/**
 * Base controller for admin functionality.
 *
 * @package EventLayer\Admin\Controllers
 * @since 1.0.0
 */
abstract class BaseController {

	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	abstract protected function register_hooks();
}
