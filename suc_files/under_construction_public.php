<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// Call Settings
$suc_active = get_option( 'suc_active', 'false' ) == "true" ? true : false;
$suc_by_role = get_option( 'suc_by_role') != "" ? get_option( 'suc_by_role', array() ) : array();
$suc_role_to_redirect = get_option( 'suc_role_to_redirect') != "" ? get_option( 'suc_role_to_redirect', array() ) : array();
$suc_ip_whitelist = get_option( 'suc_ip_whitelist', '' );
$current_user_allowed = false;
$redirect_current_user = false;
$allowed_ip = false;


function suc_show_under_construction() {
	global $current_user_allowed, $suc_ip_whitelist, $suc_by_role, $suc_ip_whitelist, $suc_role_to_redirect, $redirect_current_user, $allowed_ip;


	// ADMIN CHECK
	if ( current_user_can('administrator') ) $current_user_allowed = true;


	// ALLOWED ROLE CHECK
	if ( is_array($suc_by_role) ) {
		foreach ($suc_by_role as $allowed_role) {
			if ( current_user_can($allowed_role) ) {
				$current_user_allowed = true;
				break;
			}
		}
	}


	// REDIRECT ROLE CHECK
	if ( $current_user_allowed && is_array($suc_role_to_redirect) ) {
		foreach ($suc_role_to_redirect as $redirect_role) {
			if ( current_user_can($redirect_role) ) {
				$redirect_current_user = true;
				break;
			}
		}

		if ($redirect_current_user) {
			show_admin_bar(false);
			add_filter('show_admin_bar', '__return_false'); // CHECK THIS

			// Remove the admin bar margins
			add_action('get_header', 'suc_filter_head');
			function suc_filter_head() {
				remove_action('wp_head', '_admin_bar_bump_cb');
			}


		}

	}


	// IP CHECK
	$allowed_ips = trim($suc_ip_whitelist);
	$allowed_ips = preg_split('/\r\n|[\r\n]/', $allowed_ips);
	if ( in_array($_SERVER["REMOTE_ADDR"], $allowed_ips) ) {
		$current_user_allowed = true;
	}




	if (!$current_user_allowed) {
		wp_logout();

		require_once( dirname(__file__).'/under_construction_default_page.php' );
		die();

	}

}
if ($suc_active) add_action('template_redirect', 'suc_show_under_construction');