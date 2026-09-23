<?php
/**
 * Main plugin bootstrap class.
 *
 * @package EditorialFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates EditorialFlow components and WordPress hooks.
 */
class EditorialFlow_Plugin {

	/**
	 * The single plugin instance.
	 *
	 * @var EditorialFlow_Plugin|null
	 */
	private static $instance = null;

	private $editorial_status;
	private $admin;
	private $settings;
	private $history;

	/**
	 * Prevent direct construction outside this class.
	 */
	private function __construct() {
		$this->history = new EditorialFlow_History();

		$this->editorial_status = new EditorialFlow_Editorial_Status(
			$this->history
		);

		$this->admin = new EditorialFlow_Admin(
			$this->editorial_status
		);
		$this->settings = new EditorialFlow_Settings();
	}

	/**
	 * Get the shared plugin instance.
	 *
	 * @return EditorialFlow_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register the plugin's runtime hooks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		$this->admin->register_hooks();
		$this->settings->register_hooks();
	}

	/**
	 * Load translated strings for the plugin.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'editorialflow',
			false,
			dirname( plugin_basename( EDITORIALFLOW_FILE ) ) . '/languages'
		);
	}
}