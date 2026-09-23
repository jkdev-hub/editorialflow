<?php
/**
 * Plugin Name:       EditorialFlow
 * Plugin URI:        /#
 * Description:       Lightweight editorial workflow tracking for WordPress posts.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Jaimin
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       editorialflow
 * 
 * @package EditorialFlow
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EDITORIALFLOW_VERSION', '0.1.0' );
define( 'EDITORIALFLOW_FILE', __FILE__ );
define( 'EDITORIALFLOW_PATH', plugin_dir_path( __FILE__ ) );
define( 'EDITORIALFLOW_URL', plugin_dir_url( __FILE__ ) );

spl_autoload_register(
	function ( $class_name ) {

		$prefix = 'EditorialFlow_';

		if ( strpos( $class_name, $prefix ) !== 0 ) {
			return;
		}

		$class_name = str_replace( $prefix, '', $class_name );
		$class_name = strtolower( str_replace( '_', '-', $class_name ) );

		$file = EDITORIALFLOW_PATH . 'includes/class-' . $class_name . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);


/**
 * Runs when the plugin is activated.
 *
 *
 * @return void
 */
function editorialflow_activate() {
	global $wpdb;

	update_option( 'editorialflow_version', EDITORIALFLOW_VERSION );
	
	$administrator = get_role( 'administrator' );
	$editor        = get_role( 'editor' );

	if ( $administrator ) {
		$administrator->add_cap( 'manage_editorialflow' );
	}

	if ( $editor ) {
		$editor->add_cap( 'manage_editorialflow' );
	}

	$table_name      = $wpdb->prefix . 'editorialflow_history';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		post_id bigint(20) unsigned NOT NULL,
		old_status varchar(50) NOT NULL,
		new_status varchar(50) NOT NULL,
		user_id bigint(20) unsigned NOT NULL,
		changed_at datetime NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}

/**
 * Runs when the plugin is deactivated.
 *
 *
 * @return void
 */
function editorialflow_deactivate() {
	// Reserved for cleanup of scheduled tasks or temporary runtime state.
}

register_activation_hook( __FILE__, 'editorialflow_activate' );
register_deactivation_hook( __FILE__, 'editorialflow_deactivate' );

EditorialFlow_Plugin::instance()->run();