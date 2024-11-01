<?php

if( defined( 'ABSPATH') && defined('WP_UNINSTALL_PLUGIN') ) {

	// Remove the plugin's settings
	if ( get_option( 'suc_active' ) ) delete_option( 'suc_active' );
	if ( get_option( 'suc_by_role' ) ) delete_option( 'suc_by_role' );
	if ( get_option( 'suc_ip_whitelist' ) ) delete_option( 'suc_ip_whitelist' );

	if ( get_option( 'suc_active' ) ) delete_site_option( 'suc_active' );
	if ( get_option( 'suc_by_role' ) ) delete_site_option( 'suc_by_role' );
	if ( get_option( 'suc_ip_whitelist' ) ) delete_site_option( 'suc_ip_whitelist' );

}

?>