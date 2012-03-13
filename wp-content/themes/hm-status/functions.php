<?php

function hmh_prepare_theme() {

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'menus' );
	
	register_nav_menu( 'reviewhub_main_nav', 'Main Navigation Menu' );	
	
	add_action( 'init', function() { 
		wp_enqueue_style( 'main-stylesheet', get_bloginfo('template_directory') . '/style.css', '1.0' );
	} );
}
add_action( 'after_setup_theme', 'hmh_prepare_theme' );

function hmh_redirect_to_login() {
	
	if ( ! is_admin() && ! is_user_logged_in() && ! hma_is_login() && ! is_page('help') ) {
		wp_redirect( hma_get_login_url(), 303 );
		exit;
	}

}
add_action( 'template_redirect', 'hmh_redirect_to_login' );
add_action( 'hm_load_custom_template', 'hmh_redirect_to_login' );

// init hook, logs user in if they have submitted login form from the login page
add_action( 'init', function() {

	if ( ! isset( $_POST['user_login'] ) || ! isset( $_POST['user_password'] ) || $_POST['username'] )
		return;

	hma_log_user_in( array(

		'username' => (string) $_POST['user_login'],
		'password' => (string) $_POST['user_password'],
		'redirect_to' => site_url('/')
	) );

} );