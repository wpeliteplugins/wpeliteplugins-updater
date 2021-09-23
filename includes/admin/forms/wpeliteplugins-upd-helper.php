<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * WPElitePlugins Helper Page
 * 
 * Handle to display helper page
 * 
 * @package wpeliteplugins Updater
 * @since 1.0.0
 */

global $wpeliteplugins_queued_updates;

$count = 0;
if( !empty($wpeliteplugins_queued_updates ) ) {
	$count = count( $wpeliteplugins_queued_updates );
}

?>
<div class="wrap">
	<h2><?php echo __( 'Welcome to WPElitePlugins Updater', 'wpelitepluginsupd' );?></h2><?php 
	
	if( isset( $_GET['message'] ) && !empty( $_GET['message'] ) ) {
		
		echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Licence key has been updated successfully.', 'wpelitepluginsupd' ) . '</p></div>' . "\n";
	}
	
	echo '<div class="notice notice-success is-dismissible">' . wpautop( sprintf( __( 'See below for a list of the WPElitePlugins products in use on %s. You can view our %s on how this works.', 'wpelitepluginsupd' ), get_bloginfo( 'name' ), '<a target="_blank" href="http://documents.wpeliteplugins.com/updater/wpeliteplugins-updater/">'.__( 'documentation', 'wpelitepluginsupd' ).'</a>' ) ) . '</div>' . "\n";
	?>
	<form action="" method="post" id="wpelitepluginsupd-conf" enctype="multipart/form-data">
		<div class="tablenav top">
			<div class="tablenav-pages one-page"><span class="displaying-num"><?php echo $count . ' ' . __( 'item', 'wpelitepluginsupd' );?></span></div>
		</div>
		<table class="wp-list-table widefat fixed wpeliteplugins-licenses">
			<thead>
				<tr>					
					<th width="20%"><?php echo __( 'Product', 'wpelitepluginsupd' );?></th>
					<th width="10%"><?php echo __( 'Version', 'wpelitepluginsupd' );?></th>
					<th width="35%"><?php echo __( 'Email', 'wpelitepluginsupd' );?></th>
					<th width="35%"><?php echo __( 'Item Purchase Code', 'wpelitepluginsupd' );?></th>
				</tr>
			</thead>
			<tbody><?php 
				
				if( !empty( $wpeliteplugins_queued_updates ) ) { // 
					
					$plugins_license	= wpeliteplugins_all_plugins_purchase_code();					
					$plugins_email		= wpeliteplugins_all_plugins_purchase_email();
					$counter			= 1;
					
					foreach ( $wpeliteplugins_queued_updates as $wpeliteplugins_queue ) { 
						
						$plugin_file	= isset( $wpeliteplugins_queue->file ) ? $wpeliteplugins_queue->file : '';
						$plugin_key		= isset( $wpeliteplugins_queue->plugin_key ) ? $wpeliteplugins_queue->plugin_key : '';
						
						$plugin_dir		= WP_PLUGIN_DIR . '/' . $plugin_file;
						$plugin_data	= get_plugin_data( $plugin_dir );
						
						$alternate		= ( $counter%2 == 1 ) ? 'alternate' : '';
						
						$licence		= isset( $plugins_license[$plugin_key] ) ? $plugins_license[$plugin_key] : '';
						$email			= isset( $plugins_email[$plugin_key] ) ? $plugins_email[$plugin_key] : '';
						
						?>
						<tr class="<?php echo $alternate;?>">
							<td><strong><?php echo $plugin_data['Name'];?></strong></td>
							<td><?php echo $plugin_data['Version'];?></td>
							<td>
								<input class="wpelitepluginsupd-email-field" size="40" type="text" value="<?php echo $email;?>" name="wpelitepluginsupd_email[<?php echo $plugin_key;?>]" placeholder="Place your email here" /><img src="<?php echo WPELITEPLUGINS_UPD_URL.'includes/images/invalidemail.png'; ?>" class="wpelitepluginsupd-invalid-email"><img src="<?php echo WPELITEPLUGINS_UPD_URL.'includes/images/done.png'; ?>" class="wpelitepluginsupd-done-email">
							</td>
							<td>
								<input class="wpelitepluginsupd-key-field" size="40" type="text" value="<?php echo $licence;?>" name="wpelitepluginsupd_lickey[<?php echo $plugin_key;?>]" placeholder="<?php echo __( 'Place', 'wpelitepluginsupd' ) . ' ' . $plugin_data['Name'] . ' ' . __( 'item purchase code here', 'wpelitepluginsupd' );?>" />
							</td>
						</tr><?php 
						
						$counter++;
					}
				} else { ?>
					<tr><td colspan="3"><?php echo __( 'There is no product available for update.', 'wpelitepluginsupd' );?></td></tr><?php
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php echo __( 'Product', 'wpelitepluginsupd' );?></th>
					<th><?php echo __( 'Version', 'wpelitepluginsupd' );?></th>					
					<th><?php echo __( 'Email', 'wpelitepluginsupd' );?></th>
					<th><?php echo __( 'Item Purchase Code', 'wpelitepluginsupd' );?></th>
				</tr>
			</tfoot>
		</table>
		<div class="tablenav bottom">
			<div class="tablenav-pages one-page"><span class="displaying-num"><?php echo $count . ' ' . __( 'item', 'wpelitepluginsupd' );?></span></div>
		</div><?php 
		
		if( !empty( $wpeliteplugins_queued_updates ) ) { ?>
			<p class="submit">
				<input id="submit" class="button button-primary wpeliteplugins-upd-submit-button" type="submit" value="<?php echo __( 'Activate Products', 'wpelitepluginsupd' );?>" name="wpeliteplugins_upd_submit">
			</p><?php 
		}?>
	</form>
</div><!-- .wrap -->