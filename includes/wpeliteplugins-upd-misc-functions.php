<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Misc Functions
 * 
 * All misc functions handles to 
 * different functions 
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */

/**
 * Queue updates for the wpeliteplugins Updater
 * 
 * Handle to add plugin into queue for get update
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
if ( ! function_exists( 'wpeliteplugins_queue_update' ) ) {
	
	function wpeliteplugins_queue_update( $file, $plugin_key = '' ) {
		
		global $wpeliteplugins_queued_updates;
		
		if ( ! isset( $wpeliteplugins_queued_updates ) ) {
			$wpeliteplugins_queued_updates = array();
		}
		
		$plugin				= new stdClass();
		$plugin->file		= $file;
		$plugin->plugin_key	= $plugin_key;
		
		$wpeliteplugins_queued_updates[$file]	= $plugin;
	}
}

/**
 * Get Plugin Purchase Code
 * 
 * Handle to add plugin into queue for get update
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
function wpeliteplugins_get_plugin_purchase_code( $plugin_key = '' ) {
	
	if( !empty( $plugin_key ) ) {//plugin key is not empty
		
		//wpeliteplugins all purchase code
		$purchase_codes	= wpeliteplugins_all_plugins_purchase_code();
		
		return isset( $purchase_codes[$plugin_key] ) ? $purchase_codes[$plugin_key] : '';
	}
	
	return apply_filters( 'wpeliteplugins_get_plugin_purchase_code', false, $plugin_key );
}

/**
 * Get All Plugins Purchase Code
 * 
 * Handle to get all plugins purchase code
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
function wpeliteplugins_all_plugins_purchase_code() {
	
	if( is_multisite() ) {
		$purchase_codes	= get_site_option( 'wpelitepluginsupd_lickey' );
	} else {
		$purchase_codes	= get_option( 'wpelitepluginsupd_lickey' );
	}
	
	return apply_filters( 'wpeliteplugins_all_plugins_purchase_code', $purchase_codes );
}

/**
 * Save All Plugins Purchase Code
 * 
 * Handle to save all plugins purchase code
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
function wpeliteplugins_save_plugins_purchase_code( $purchase_codes = array() ) {
	
	$purchase_codes	= apply_filters( 'wpeliteplugins_save_plugins_purchase_code', $purchase_codes );
	
	if( is_multisite() ) {
		update_site_option( 'wpelitepluginsupd_lickey', $purchase_codes );
	} else {
		update_option( 'wpelitepluginsupd_lickey', $purchase_codes );
	}
}

/**
 * Get Plugin Purchase Email
 * 
 * Handle to add plugin into queue for get update using email
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
function wpeliteplugins_get_plugin_purchase_email( $plugin_key = '' ) {
	
	$return	= false;
	
	if( !empty( $plugin_key ) ) {//plugin key is not empty
		
		//Wpweb all purchase code
		$purchase_emails	= wpeliteplugins_all_plugins_purchase_email();
		
		// return plugin email id
		$return	= isset( $purchase_emails[$plugin_key] ) ? $purchase_emails[$plugin_key] : '';
	}
	
	return apply_filters( 'wpeliteplugins_get_plugin_purchase_email', $return, $plugin_key );
}

/**
 * Get All Plugins Purchase Email
 * 
 * Handle to get all plugins purchase email
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
function wpeliteplugins_all_plugins_purchase_email() {
	
	if( is_multisite() ) {
		$purchase_emails	= get_site_option( 'wpelitepluginsupd_email' );
	} else {
		$purchase_emails	= get_option( 'wpelitepluginsupd_email' );
	}
	
	return apply_filters( 'wpeliteplugins_all_plugins_purchase_email', $purchase_emails );
}

/**
 * Save All Plugins Purchased Product Email
 * 
 * Handle to save all plugins purchase email
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
function wpeliteplugins_save_plugins_purchase_email( $purchase_emails = array() ) {
	
	$purchase_emails	= apply_filters( 'wpeliteplugins_save_plugins_purchase_email', $purchase_emails );
	
	if( is_multisite() ) {
		update_site_option( 'wpelitepluginsupd_email', $purchase_emails );
	} else {
		update_option( 'wpelitepluginsupd_email', $purchase_emails );
	}
}