<?php
/**
 * Plugin Name: WPElite Plugins Updater
 * Plugin URI: http://wpeliteplugins.com/
 * Description: WPElitePlugins Updater - The license and updater plugin for all WPElitePlugins products
 * Version: 1.0.2
 * Author: WPElitePlugins
 * Network: true
 * Author URI: http://wpeliteplugins.com/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions 
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
if( !defined( 'WPELITEPLUGINS_UPD_VERSION' ) ) {
	define( 'WPELITEPLUGINS_UPD_VERSION', '1.0.2' ); // plugin version
}
if( !defined( 'WPELITEPLUGINS_UPD_DIR' ) ) {
	define( 'WPELITEPLUGINS_UPD_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'WPELITEPLUGINS_UPD_PLUGINS_DIR' ) ) {
	define( 'WPELITEPLUGINS_UPD_PLUGINS_DIR', dirname( dirname( __FILE__ ) ) ); // plugin dir
}
if( !defined( 'WPELITEPLUGINS_UPD_URL' ) ) {
	define( 'WPELITEPLUGINS_UPD_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}
if( !defined( 'WPELITEPLUGINS_UPD_ADMIN' ) ) {
	define( 'WPELITEPLUGINS_UPD_ADMIN', WPELITEPLUGINS_UPD_DIR . '/includes/admin' ); // plugin admin dir
}
if ( ! defined( 'WPELITEPLUGINS_DOMAIN' ) ) { // define seller domain
	define( 'WPELITEPLUGINS_DOMAIN', 'http://wpeliteplugins.com' );
}

//Include misc functions file
require_once( WPELITEPLUGINS_UPD_DIR . '/includes/wpeliteplugins-upd-misc-functions.php' );

/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
load_plugin_textdomain( 'wpelitepluginsupd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if( is_admin() ) {
	
	//Include admin class file
	require_once( WPELITEPLUGINS_UPD_DIR . '/includes/admin/class-wpeliteplugins-upd-admin.php' );
	
	$wpeliteplugins_upd_admin = new WPElitePlugins_Upd_Admin();
	$wpeliteplugins_upd_admin->add_hooks();
	
	include_once( WPELITEPLUGINS_UPD_DIR . '/updates/class-plugin-update-checker.php' );
	
	$WPEliteUpdateChecker = new WPElitePluginsUpdateChecker(
		WPELITEPLUGINS_DOMAIN . '/Updates/WPELITEPUPD/info.json',
		__FILE__,
		'wpelitepupd'
	);
}

/**
 * Change plugin load order
 * 
 * Loads Updater plugin first
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
function wpeliteplugins_upd_plugin_first() {
	
	// ensure path to this file is via main wp plugin path
	$wp_path_to_this_file	= preg_replace( '/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__ );
	$this_plugin			= plugin_basename( trim( $wp_path_to_this_file ) );
	$active_plugins			= get_option( 'active_plugins' );
	$this_plugin_key		= array_search( $this_plugin, $active_plugins );
	
	if( $this_plugin_key ) { // if it's 0 it's the first plugin already, no need to continue
		
		array_splice( $active_plugins, $this_plugin_key, 1 );
		array_unshift( $active_plugins, $this_plugin );
		update_option( 'active_plugins', $active_plugins );
	}
}
add_action( 'activated_plugin', 'wpeliteplugins_upd_plugin_first' );