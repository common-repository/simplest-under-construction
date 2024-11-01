<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// Register Settings
function suc_register_settings() {
	register_setting( 'suc_settings' , 'suc_active' );
	register_setting( 'suc_settings' , 'suc_by_role' );
	register_setting( 'suc_settings' , 'suc_role_to_redirect' );
	register_setting( 'suc_settings' , 'suc_ip_whitelist' );
	//register_setting( 'suc_settings' , 'suc_status_code' );
	//register_setting( 'suc_settings' , 'suc_display' );
}
add_action( 'admin_init', 'suc_register_settings' );



// Admin Menu
function suc_admin_menu() {

	add_submenu_page(
		'options-general.php',  		// admin page slug
		'Under Construction Page', 		// page title
		'Under Construction', 			// menu title
		'administrator', 	// capability required to see the page
		'simplest-under-construction',  // admin page slug, e.g. options-general.php?page=suc_options
		'suc_options'            		// callback function to display the options page
	);

}
add_action( 'admin_menu', 'suc_admin_menu' );




// Call Settings
$suc_active = get_option( 'suc_active', 'false' ) == "true" ? true : false;
$suc_by_role = get_option( 'suc_by_role') != "" ? get_option( 'suc_by_role', array() ) : array();
$suc_role_to_redirect = get_option( 'suc_role_to_redirect') != "" ? get_option( 'suc_role_to_redirect', array() ) : array();
$suc_ip_whitelist = get_option( 'suc_ip_whitelist', '' );
$current_user_allowed = false;
$redirect_current_user = false;
$allowed_ip = false;


// BLOCK FOR ADMIN SIDE
function suc_block_admin_page() {
	global $suc_ip_whitelist, $current_user_allowed, $suc_by_role, $suc_role_to_redirect, $redirect_current_user, $allowed_ip;


	// ALLOW ADMIN
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

		// DON'T REDIRECT ADMIN
		if ( current_user_can('administrator') ) $redirect_current_user = false;

		if ( $redirect_current_user && !(defined( 'DOING_AJAX' ) && DOING_AJAX) ) wp_redirect(get_bloginfo('url'));

	}


	// IP CHECK
	$allowed_ips = trim($suc_ip_whitelist);
	$allowed_ips = preg_split('/\r\n|[\r\n]/', $allowed_ips);
	if ( in_array($_SERVER["REMOTE_ADDR"], $allowed_ips) ) {
		$current_user_allowed = true;
	}


	if(!$current_user_allowed){
		wp_logout();
		wp_redirect(get_bloginfo('url'));
	}

}
if ($suc_active) add_action( 'admin_init', 'suc_block_admin_page' );




// Admin Options
function suc_options() {
	global $suc_active, $suc_by_role, $suc_role_to_redirect, $suc_ip_whitelist, $suc_status_code, $wp_roles;

    if ( ! isset( $_REQUEST['settings-updated'] ) )
        $_REQUEST['settings-updated'] = false;
?>
    <div class="wrap">

        <h2>Under Construction Page Options</h2>


        <div id="poststuff">
               <div id="post-body">
                    <div id="post-body-content">
                         <form method="post" id="suc_form" action="options.php" enctype="multipart/form-data" autocomplete="off">
                              <?php settings_fields( 'suc_settings' ); ?>
							  <?php do_settings_sections( 'suc_settings' ); ?>
                              <table class="form-table">
                                   <tr valign="top">
	                                    <th scope="row"><?php _e( "Activate", 'suc' ); ?></th>
                                        <td>

	                                        <label><input type="radio" name="suc_active" value="true" <?=$suc_active ? "checked" : ""?>> On</label><br>
	                                        <label><input type="radio" name="suc_active" value="false" <?=!$suc_active ? "checked" : ""?>> Off</label>

                                        </td>
                                   </tr>
                                   <tr valign="top">
	                                    <th scope="row"><?php _e( "Roles to Disable <br/>(Optional)", 'suc' ); ?></th>
                                        <td>
<?php

	$suc_roles_list = array();
	$suc_current_user_roles = array();
	$suc_selected_roles = array();
	foreach ( $wp_roles->roles as $role => $role_details ) {

		// Extract the admin
		if ( $role == "administrator" ) continue;

		// Extract the current roles
		if ( !current_user_can("administrator") && current_user_can($role) ) {
			$suc_current_user_roles[] = $role; // Record current roles
			continue;
		}

		// Already recorded?
		$selected = in_array($role, $suc_by_role) ? "selected" : "";
		$selected_to_redirect = in_array($role, $suc_role_to_redirect) ? "selected" : "";
		if ( in_array($role, $suc_by_role) ) {
			$suc_selected_roles[$role] = array(
				'name' => $role_details['name'],
				'selected' => $selected_to_redirect
			);
		}

		$suc_roles_list[$role] = array(
			'name' => $role_details['name'],
			'selected' => $selected
		);

	}


	// SHOW THE ROLE SELECTOR
	echo '<select id="suc_roles" name="suc_by_role[]" size="'.count($suc_roles_list).'" multiple>';
		foreach ( $suc_roles_list as $role => $role_details ) {

			echo '<option value="'.$role.'" '.$role_details['selected'].'>'.$role_details['name'].'</option>';

		}
	echo '</select>';


	// INCLUDE THE CURRENT USER ROLES AS HIDDEN
	foreach ( $suc_current_user_roles as $role) {

		// Include the current and selected roles
		if ( $suc_active && in_array($role, $suc_by_role) ) echo '<input type="hidden" name="suc_by_role[]" value="'.$role.'">';

		// Allow himself if not active
		if ( !$suc_active ) {

			if ( $role == "administrator" ) continue;

			echo '<input type="hidden" name="suc_by_role[]" value="'.$role.'">';

		}

	}


?>

                                        </td>
                                   </tr>
                                   <tr valign="top">
	                                    <th scope="row"><?php _e( "Auto-redirect to front-end <br/>(Optional)", 'suc' ); ?></th>
                                        <td>


<?php
	// SHOW THE ROLE SELECTOR
	echo '<select id="suc_role_to_redirect" name="suc_role_to_redirect[]" size="'.count($suc_selected_roles).'" multiple>';
		foreach ( $suc_selected_roles as $role => $role_details ) {

			echo '<option value="'.$role.'" '.$role_details['selected'].'>'.$role_details['name'].'</option>';

		}
	echo '</select>';
?><br/>

First, you need to add roles from "Roles to Disable" section and save them.<br/>
The roles selected above will be automatically redirected to the frontend to preview after they log in.

                                        </td>
                                   </tr>
                                   <tr valign="top">
	                                    <th scope="row"><?php _e( "IP Addresses to Disable <br/>(Optional)", 'suc' ); ?></th>
                                        <td>

											<textarea name="suc_ip_whitelist" rows="4"><?=$suc_ip_whitelist?></textarea><br>
											One IP per line. Current IP Address: <b><?=$_SERVER["REMOTE_ADDR"]?></b><br/>
											The IP addresses listed above will be able to preview the site without logging in.

                                        </td>
                                   </tr>
                                   <tr valign="top">
	                                    <th scope="row"></th>
                                        <td>
												<?php submit_button( __( 'Save Changes', 'suc' ), 'primary', 'submit', true ); ?>
                                        </td>
                                   </tr>
                              </table>
                         </form>
                    </div> <!-- end post-body-content -->
               </div> <!-- end post-body -->
          </div> <!-- end poststuff -->
     </div> <!-- end wrap -->

	 <script type="text/javascript">
		jQuery(document).ready(function($){

			// AUTO-REDIRECT FILL
			$('#suc_roles').on('change', function() {

				$('#suc_role_to_redirect option').remove();

				$('option:selected').each(function() {

					$('#suc_role_to_redirect').append('<option value="'+ $(this).val() +'">'+ $(this).text() +'</option>');

				});


				$('#suc_role_to_redirect').attr('size', $('option:selected').length);



			});


			// AUTO SELECTS
			$('#suc_roles').on('change', function() {
			    $('option:disabled').prop('selected', true);
			});

			$('#suc_form').submit(function() {
				$('option:disabled').removeAttr('disabled');
			});

		}); // document ready
	 </script>
<?php
}