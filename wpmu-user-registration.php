<?php
/*
Plugin Name: WPMU User Registration
Description: When a user registers to one of the blog in the site,  he/she get registered in only one blog. This plugin registers a user to all of the active blogs in the site.
Plugin URI: http://abiralneupane.com.np
Author: Abiral Neupane
Author URI: http://abiralneupane.com.np
Version: 1.0
License: GPL2
Text Domain: wur
Domain Path: /languages
*/


add_action( 'admin_notices', 'wur_show_notice' );
function wur_show_notice(){
	if ( !is_multisite() ) {
		echo '<div class="update-nag">'.__('Your site isn\'t multisite featured. You need to activate multisite mode in order to make this plugin work','wur').'</div>';
	}
}

add_action( 'user_register', 'wur_add_user_to_blog', 10, 1 );
add_action( 'wpmu_activate_user', 'wur_add_user_to_blog', 10, 1 );
add_action( 'wpmu_new_user', 'wur_add_user_to_blog', 10, 1 );

function wur_add_user_to_blog( $user_id ) {
	if ( !is_multisite() ) {
		/* This isn't a multiste */
		return true;	
	}

	$user = get_userdata( $user_id );
	if($user){
		$role = $user->roles[0];	
	}else{
		$role = 'subscriber';
	}
	
	$role  = apply_filters( 'wur_user_roles', $role );

	global $wpdb;
	$args = array(
	    'network_id' => $wpdb->siteid,
	    'public'     => null,
	    'archived'   => null,
	    'mature'     => null,
	    'spam'       => null,
	    'deleted'    => null,
	    'limit'      => 100,
	    'offset'     => 0,
	);
	$all_sites = wp_get_sites( $args );

	do_action('wur_before_user_add');
	
	if(!empty($all_sites)){
		foreach($all_sites as $site){
			add_user_to_blog( $site['blog_id'], $user_id, $role );
		}
	}

	do_action('wur_before_user_add');
}