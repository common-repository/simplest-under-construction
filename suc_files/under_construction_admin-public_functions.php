<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// ADMIN BAR MENU
function suc_toolbar_under_construction( $wp_admin_bar ) {
	if ( current_user_can('administrator') ) {

		$args = array(
			'id'    => 'suc_toolbar_under_construction',
			'title' => '<span class="ab-icon"><img src="'.plugin_dir_url( SUC_FILE ).'dev_icon.png"></span>
						<span class="ab-label">Under Construction</span>',
			'href'  => admin_url('admin.php?page=simplest-under-construction'),
			'meta'  => array( 'class' => 'suc-toolbar-custom-codes' )
		);
		$wp_admin_bar->add_node( $args );

	}
}
if ($suc_active) add_action( 'admin_bar_menu', 'suc_toolbar_under_construction', 9999999999999999 );


// STYLES
function suc_toolbar_uc_style() { // INLINE CODES

	if ( current_user_can('administrator') ) {

		echo "
<style type='text/css'>

	#wp-admin-bar-suc_toolbar_under_construction .ab-item .ab-icon {
		-webkit-filter: grayscale(80%);
		filter: grayscale(80%);
	}
	#wp-admin-bar-suc_toolbar_under_construction .ab-item:hover .ab-icon {
		-webkit-filter: grayscale(0%);
		filter: grayscale(0%);
	}

</style>
		";

	}
}
if ($suc_active) add_action( 'wp_head', 'suc_toolbar_uc_style' );
if ($suc_active) add_action( 'admin_head', 'suc_toolbar_uc_style' );

?>