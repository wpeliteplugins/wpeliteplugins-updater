<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 * 
 * Handles generic Admin functionality and AJAX requests.
 * 
 * @package WPElite Plugins Updater
 * @since 1.0.0
 */
class WPElitePlugins_Upd_Admin {
	
	public function __construct() {
		
	}
	
	/**
	 * wpeliteplugins Plugin Update Notice
	 * 
	 * Handle to show wpeliteplugins plugin active notice
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_upd_display_activation_notice() {
		
		//message should not display on licence page
		if ( isset( $_GET['page'] ) && 'wpeliteplugins-upd-helper' == $_GET['page'] ) return;
		if ( true == get_site_option( 'wpelitepluginsupd_helper_dismiss_activation_notice', false ) ) return;
		if ( ! current_user_can( 'manage_options' ) ) return;
		if ( is_multisite() && ! is_super_admin() ) return;
		
		global $wpeliteplugins_queued_updates;
		
		if( !empty( $wpeliteplugins_queued_updates ) ) { //If plugins are there
			
			$display_notice		= false;
						
			$wpelitepluginsupd_lickey	= wpeliteplugins_all_plugins_purchase_code();
			
			foreach ( $wpeliteplugins_queued_updates as $wpeliteplugins_queued_item ) {
				
				$plugin_key	= isset( $wpeliteplugins_queued_item->plugin_key ) ? $wpeliteplugins_queued_item->plugin_key : '';
				
				if( empty( $wpelitepluginsupd_lickey[$plugin_key] ) ) {
					$display_notice	= true;
				}
			}
			
			if( $display_notice ) {
				$helper_url		= add_query_arg( 'page', 'wpeliteplugins-upd-helper', network_admin_url( 'index.php' ) );
				$dismiss_url	= add_query_arg( 'action', 'wpeliteplugins-upd-helper-dismiss', add_query_arg( 'nonce', wp_create_nonce( 'wpeliteplugins-upd-helper-dismiss' ) ) );
				echo '<div class="notice notice-success is-dismissible"><p class="alignleft">' . sprintf( __( '%sYour WPElitePlugins products are almost ready.%s To get started, %sactivate your product licenses%s.', 'wpelitepluginsupd' ), '<strong>', '</strong>', '<a href="' . esc_url( $helper_url ) . '">', '</a>' ) . '</p><p class="alignright"><a href="' . esc_url( $dismiss_url ) . '">' . __( 'Dismiss', 'wpelitepluginsupd' ) . '</a></p><div class="clear"></div></div>' . "\n";
			}
		}
	}
	
	/**
	 * Add Admin Menu
	 * 
	 * Handles to add admin menus 
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_upd_admin_menu() {
		
		add_dashboard_page( __( 'The WPElite Plugins Updater', 'wpelitepluginsupd' ), __( 'WPElitePlugins Updater', 'wpelitepluginsupd' ), 'manage_options', 'wpeliteplugins-upd-helper', array( $this, 'wpeliteplugins_upd_helper_screen' ) );
	}
	
	/**
	 * wpeliteplugins Helper Page
	 * 
	 * Handles to display wpeliteplugins helper page
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_upd_helper_screen() {
		
		include_once( WPELITEPLUGINS_UPD_ADMIN . '/forms/wpeliteplugins-upd-helper.php' );
	}
	
	/**
	 * Save Product License Key
	 * 
	 * Handle to save product license key
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_upd_save_products_license() {
		
		if( !empty( $_POST['wpeliteplugins_upd_submit'] ) ) {//If click on save button
			
			//$wpelitepluginsupd_lickey	= get_option( 'wpelitepluginsupd_lickey' );
			$wpelitepluginsupd_lickey	= wpeliteplugins_all_plugins_purchase_code();
			$wpelitepluginsupd_email	= wpeliteplugins_all_plugins_purchase_email();			
			
			$post_lickey		= $_POST['wpelitepluginsupd_lickey'];			
			$post_email			= $_POST['wpelitepluginsupd_email'];
			
			foreach ( $post_lickey as $plugin_key => $license_key ) {
				$wpelitepluginsupd_lickey[$plugin_key]	= $license_key;
			}
			wpeliteplugins_save_plugins_purchase_code( $wpelitepluginsupd_lickey );

			foreach ( $post_email as $plugin_key => $email_key ) {
				$wpelitepluginsupd_email[$plugin_key]	= $email_key;
			}
			wpeliteplugins_save_plugins_purchase_email( $wpelitepluginsupd_email );

			wp_redirect( add_query_arg( array( 'message' => '1' ) ) );
		}
		
		if ( isset( $_GET['action'] ) && ( 'wpeliteplugins-upd-helper-dismiss' == $_GET['action'] ) && isset( $_GET['nonce'] ) && check_admin_referer( 'wpeliteplugins-upd-helper-dismiss', 'nonce' ) ) {
			
			update_site_option( 'wpelitepluginsupd_helper_dismiss_activation_notice', true );
			$redirect_url = remove_query_arg( 'action', remove_query_arg( 'nonce', $_SERVER['REQUEST_URI'] ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}		
	
	/**
	 * Add Email Field In Request Query Arguents
	 * 
	 * Handle to add email field in request query arguents
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_request_args_add_email_option( $queryArgs, $slug, $pluginFile ) {
		
		// purchase plugin email
		$wpelitepluginsupd_email		= wpeliteplugins_all_plugins_purchase_email();
		
		// get product email
		$email	= isset( $wpelitepluginsupd_email[$slug] ) ? $wpelitepluginsupd_email[$slug] : '';
		
		if( !empty( $email ) ) { // if email is not empty
			if( is_email( $email ) ) { // if email is correct format
				$queryArgs['email']	= $wpelitepluginsupd_email[$slug];
			}
		}
		
		return $queryArgs;
	}

	/**
	 * Add Site URL To Remote Request
	 * 
	 * Handle to add site URL to remote request
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_request_args_add_site_url( $options, $slug, $pluginFile ) {
		
		$site_url	= site_url();
		$options['cookies']	= array( 'site_url' => $site_url );
		return $options;
	}

	/**
	 * Adding scripts
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_upd_admin_scripts( $hook_suffix = '' ) {
		
		$pages_hook_suffix	= array( 'dashboard_page_wpeliteplugins-upd-helper', 'index_page_wpeliteplugins-upd-helper' );
		
		if( in_array( $hook_suffix, $pages_hook_suffix ) ) {
			
			wp_register_style( 'wpeliteplugins-upd-admin-style', WPELITEPLUGINS_UPD_URL . 'includes/css/wpeliteplugins-upd-style.css', array(), WPELITEPLUGINS_UPD_VERSION );
			wp_enqueue_style( 'wpeliteplugins-upd-admin-style' );
			
			// add js for check code in admin
			wp_register_script( 'wpeliteplugins-upd-admin-script', WPELITEPLUGINS_UPD_URL . 'includes/js/wpeliteplugins-upd-script.js', array( 'jquery' ), WPELITEPLUGINS_UPD_VERSION );
			wp_enqueue_script( 'wpeliteplugins-upd-admin-script' );
		}
	}

	/**
	 * Display Notice For 
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function wpeliteplugins_upd_display_invalid_email_notices() {
		
		//message should not display on licence page
		if ( !isset( $_GET['page'] ) || 'wpeliteplugins-upd-helper' != $_GET['page'] ) return;
		if ( ! current_user_can( 'manage_options' ) ) return;
		if ( is_multisite() && ! is_super_admin() ) return;
		
		global $wpeliteplugins_queued_updates;
		
		if( !empty( $wpeliteplugins_queued_updates ) ) { //If plugins are there
			
			$display_notice		= false;
			$plugin_names		= '';
			$wpelitepluginsupd_email = wpeliteplugins_all_plugins_purchase_email();
			$error_counter			= 1;
			
			foreach ( $wpeliteplugins_queued_updates as $wpeliteplugins_queued_item ) {
				
				//get plugin file
				$plugin_file	= isset( $wpeliteplugins_queued_item->file ) ? $wpeliteplugins_queued_item->file : '';
				
				// get plugin key
				$plugin_key		= isset( $wpeliteplugins_queued_item->plugin_key ) ? $wpeliteplugins_queued_item->plugin_key : '';
				
				if( !empty( $plugin_file ) && !empty( $plugin_key ) ) { // if plugin file and key is not empty
					
					$plugin_data	= get_plugin_data( WPELITEPLUGINS_UPD_PLUGINS_DIR . '/' . $plugin_file );
					
					if( empty( $wpelitepluginsupd_email[$plugin_key] ) || !is_email( $wpelitepluginsupd_email[$plugin_key] ) ) {
						
						$display_notice	= true;
						$prefix			= ( $error_counter == 1 ) ? '' : ', ';
						$plugin_names	.= $prefix . '<strong>' . $plugin_data['Name'] . '</strong>';
						$error_counter++;
					}
				}
			}
			
			if( $display_notice ) {
				echo '<div class="updated fade error"><p>' . sprintf( __( 'You have empty / wrong email in following products : %s .', 'wpelitepluginsupd' ), $plugin_names ) . '</p></div>' . "\n";
			}
		}
	}

	/**
	 * Adding Hooks
	 * 
	 * @package WPElite Plugins Updater
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		// display an admin notice, if there are wpeliteplugins products, eligible for licenses, that are not activated.
		add_action( 'network_admin_notices', array( $this, 'wpeliteplugins_upd_display_activation_notice' ) );
		add_action( 'admin_notices', array( $this, 'wpeliteplugins_upd_display_activation_notice' ) );
		//add_action( 'admin_notices', array( $this, 'wpeliteplugins_upd_display_invalid_email_notices' ) );		
		
		if( is_multisite() && ! is_network_admin() ) { // for multisite
			remove_action( 'admin_notices', array( $this, 'wpeliteplugins_upd_display_activation_notice' ) );
		}
		
		//add admin menu pages
		$menu_hook = is_multisite() ? 'network_admin_menu' : 'admin_menu';
		add_action ( $menu_hook, array( $this, 'wpeliteplugins_upd_admin_menu' ) );
		
		//save wpeliteplugins product license key
		add_action( 'admin_init', array( $this, 'wpeliteplugins_upd_save_products_license' ) );

		// add email field in request query arguents
		add_action( 'wpeliteplugins_modify_request_query_arguments', array( $this, 'wpeliteplugins_request_args_add_email_option' ), 10, 3 );
		add_action( 'wpeliteplugins_modify_request_remote_option', array( $this, 'wpeliteplugins_request_args_add_site_url' ), 10, 3 );
		
		//add scripts for add js css for updater admin page
		add_action( 'admin_enqueue_scripts', array( $this, 'wpeliteplugins_upd_admin_scripts' ) );
	}
}