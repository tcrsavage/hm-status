<?php

/**
 *
 * 		== More Functions & Plugins ==
 *
 * 		* Split off some of the more complicated theme functions.
 * 		* Core Plugins - to be loaded directly the theme. (can't be disabled)
 *
 */

// Update Script - stores an option of the version number & gives us an action that allows us to hook in and do stuff.
get_template_part( 'updates/updates', 'core' );

get_template_part( 'functions/functions-comments' );

get_template_part( 'functions/tea-tally' );

/**
 *	hms_register_assets function
 *
 *	Register & Enqueue Styles
 *  Do this separately to allow for deregistering/modifying them from a child theme.
 *
 *  @return null
 */
function hms_register_assets() {

	if ( is_admin() )
		return;

	$theme = get_theme_data( get_bloginfo( 'stylesheet_directory' ) . '/style.css' );

	//Modernizr
	wp_register_script( 'modernizr', get_bloginfo( 'template_directory' ) . '/js/libs/modernizr-1.7.min.js', null, '1.7' );

	//jQuery
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', get_bloginfo( 'template_directory' ) . '/js/libs/jquery.min.js', null, '1.7.1', true );

   	//Register my misc js functions/plugins & behaviour
    wp_register_script( 'hms_functions', get_bloginfo( 'template_directory' ) . '/js/functions.js', 'jquery', $theme['Version'], true );
	wp_register_script( 'hms_behaviour', get_bloginfo( 'template_directory' ) . '/js/behaviour.js', 'jquery', $theme['Version'], true );

    //Reset/boilerplate and base typography.
    wp_register_style( 'reset', get_bloginfo( 'template_directory' ) . '/css/normalize.css' );
    wp_register_style( 'hms_type', get_bloginfo( 'template_directory' ) . '/css/type_16-24.css' );

    // Enqueue the main style at the end.
    wp_register_style( 'hms_forms', get_bloginfo( 'template_directory' ) . '/css/forms.css' );
    wp_register_style( 'hms_style', get_bloginfo( 'template_directory' ) . '/css/main.css' );

}
add_action( 'init', 'hms_register_assets' );


/**
 * hms_enqueue_scripts description
 *
 * @return null
 */
function hms_enqueue_scripts () {

	if ( is_admin() )
		return;

	wp_enqueue_script( 'modernizr' );
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'backstretch' );

	// Theme Plugins & Functions
	wp_enqueue_script( 'hms_functions' );

	wp_enqueue_script( 'comment-reply' );

	// Theme Behaviour.
	wp_enqueue_script( 'hms_behaviour' );

}
add_action( 'wp_enqueue_scripts', 'hms_enqueue_scripts' );

/**
 * hms_print_styles
 *
 * @return null
 */
function hms_print_styles () {

	if ( is_admin() )
		return;

	wp_enqueue_style( 'reset' );

	wp_enqueue_style( 'hms_type' );
	wp_enqueue_style( 'hms_forms' );
	wp_enqueue_style( 'hms_style' );

}
add_action( 'wp_print_styles', 'hms_print_styles' );

add_filter('show_admin_bar', create_function( '', 'if( ! is_admin() ) return false;' ) );
add_action( 'wp_head', create_function( '' , 'echo "<script>var ajaxurl = \"" . get_bloginfo(\'url\') . "/wp-admin/admin-ajax.php\";</script>";') );

function hms_auth_redirect(){

	if( ! is_user_logged_in() && ! is_login() )
		auth_redirect();
}
add_action( 'template_redirect', 'hms_auth_redirect' );