<?php

add_action( 'init', 'pte_upgrade' );
function pte_upgrade(){

	$old_version = get_option( 'pte_theme_version' );

	$theme_data = get_theme_data( get_stylesheet_uri() );
	$new_version = $theme_data['Version'];

	if( version_compare( $new_version, $old_version ) == 0 )
		return;

	//Make a note that an update is in progress.
	update_option( 'pte_theme_version', 'UPDATING: ' . $old_version . ' to ' . $new_version );

	//Let plugins hook in here.
	do_action( 'pte_theme_update', $old_version, $new_version );

	//Update the current version to the New version.
	update_option( 'pte_theme_version', $new_version );

}

add_action( 'pte_theme_update', 'pte_defaults', 1, 2 );
function pte_defaults( $new_version, $old_version ){

}
