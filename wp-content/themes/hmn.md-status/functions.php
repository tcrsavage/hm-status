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


/**
 *	pte_register_assets function
 *
 *	Register & Enqueue Styles
 *  Do this separately to allow for deregistering/modifying them from a child theme.
 *
 *  @return null
 */
function pte_register_assets() {

	if ( is_admin() )
		return;

	$theme = get_theme_data( get_bloginfo( 'stylesheet_directory' ) . '/style.css' );

	//Modernizr
	wp_register_script( 'modernizr', get_bloginfo( 'template_directory' ) . '/js/libs/modernizr-1.7.min.js', null, '1.7' );

	//jQuery
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', get_bloginfo( 'template_directory' ) . '/js/libs/jquery.min.js', null, '1.7.1', true );

	wp_register_script( 'backstretch', get_bloginfo( 'template_directory' ) . '/js/jquery.backstretch/jquery.backstretch.min.js', null, '1.2.5', true );

   	//Register my misc js functions/plugins & behaviour
    wp_register_script( 'pte_functions', get_bloginfo( 'template_directory' ) . '/js/functions.js', 'jquery', $theme['Version'], true );
	wp_register_script( 'pte_behaviour', get_bloginfo( 'template_directory' ) . '/js/behaviour.js', 'jquery', $theme['Version'], true );

    //Reset/boilerplate and base typography.
    wp_register_style( 'reset', get_bloginfo( 'template_directory' ) . '/css/normalize.css' );
    wp_register_style( 'pte_type', get_bloginfo( 'template_directory' ) . '/css/type_16-24.css' );

    // Enqueue the main style at the end.
    wp_register_style( 'pte_forms', get_bloginfo( 'template_directory' ) . '/css/forms.css' );
    wp_register_style( 'pte_style', get_bloginfo( 'template_directory' ) . '/css/main.css' );

}
add_action( 'init', 'pte_register_assets' );


/**
 * pte_enqueue_scripts description
 *
 * @return null
 */
function pte_enqueue_scripts () {

	if ( is_admin() )
		return;

	wp_enqueue_script( 'modernizr' );
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'backstretch' );

	// Theme Plugins & Functions
	wp_enqueue_script( 'pte_functions' );

	wp_enqueue_script( 'comment-reply' );

	// Theme Behaviour.
	wp_enqueue_script( 'pte_behaviour' );

}
add_action( 'wp_enqueue_scripts', 'pte_enqueue_scripts' );

/**
 * pte_print_styles
 *
 * @return null
 */
function pte_print_styles () {

	if ( is_admin() )
		return;

	wp_enqueue_style( 'reset' );

	wp_enqueue_style( 'pte_type' );
	wp_enqueue_style( 'pte_forms' );
	wp_enqueue_style( 'pte_style' );

}
add_action( 'wp_print_styles', 'pte_print_styles' );


/**
 * pte_setup.
 * Setup everything this theme needs.
 */
function pte_setup() {

	register_nav_menus(
		array(
		  'pte_menu_main' => 'Main Menu',
		  'pte_menu_foot' => 'Footer Menu'
		)
	);

	add_theme_support( 'post-thumbnails' );

	//Remove some unused stuff from the head.
	remove_action('wp_head', 'wlwmanifest_link');

}
add_action( 'after_setup_theme', 'pte_setup' );

/**
 * pte_grid_admin_bar_button function.
 *
 * Grid Overlay Development Tool
 * Add the show grid button to the menu bar if the current user is admin.
 *
 * @access public
 * @return null
 */
function pte_grid_admin_bar_button() {

	if ( is_admin() || ! current_user_can( 'manage_options' ) )
		return;

	global $wp_admin_bar;

    $wp_admin_bar->add_menu(
    	array(
    		'id' => 'show-grid',
			'parent' => 'top-secondary',
    		'title' => 'Show Grid',
    		'href' => '#',
			'meta'   => array( 'class' => 'hide-no-js' ),
    	)
    );

}
add_action('admin_bar_menu', 'pte_grid_admin_bar_button', 1000 );


/**
 * pte_excerpt_length function.
 *
 * Filter the excerpt length.
 * Different lengths can be used in different places.
 *
 * @access public
 * @param int $length
 * @return int
 */
function pte_excerpt_length( $length ) {

	global $template;

	// Can adjust excerpt based on template file like this.
	if ( in_array( basename( $template ), array( 'index-grid/php', 'category-featured-image.php' ) ) )
		return 25;

	if ( has_post_thumbnail() )
		return 50;

	return 100;

}
add_filter( 'excerpt_length', 'pte_excerpt_length' );


/**
 * Add favicon link to the head.
 *
 * If there is a favicon.ico in the theme images directory, use that.
 *
 * @return null
 */
function pte_favicon() {

	$favicon = get_bloginfo( 'stylesheet_derectory' ) . '/images/favicon.ico';

?>

	<link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>" />

	<?php
}
add_action( 'wp_head', 'pte_favicon' );

add_filter('show_admin_bar', create_function( '', 'if( ! is_admin() ) return false;' ) );

add_action( 'wp_head', create_function( '' , 'echo "<script>var ajaxurl = \"" . get_bloginfo(\'url\') . "/wp-admin/admin-ajax.php\";</script>";') );


function my_action_callback() {

	$round = $_POST['round'];

	try {

		$tally = new HM_Tea_Tally();

		$tally->do_a_round( (int) $round['currentUser'], (array) $round['currentRound'] );

	} catch ( Exception $e ) {

		error_log( $e );

		add_action( 'toplevel_page_tea-tally', function () use ( $e ) {

			?>
				<div class="updated message"><p>Error: <?php var_export( $e ); ?></p></div>
			<?php
		} );

		return;

	}

	//error_log( var_export( $tally->users, true ) );

	foreach( $tally->users as $user ) {

		$state[] = array(
			'userID' => $user->ID,
			'userTotal' => $user->hmtt_total
		);

	}

	$round['currentRound'] = array();

	//error_log( $round );
	//error_log( $state );

	$round['graphInfo'] = $state;



    echo json_encode( $round );

	die();
}
add_action('wp_ajax_my_action', 'my_action_callback');
add_action('wp_ajax_nopriv_my_action', 'my_action_callback');