<?php
/*
Plugin Name: HM Overtime
Description: A simple Overtime Tracker - fork of HM Holidays
Version: 0.1
Author: Human Made Limited
Author URI: http://hmn.md/
*/

define( 'HMOT_PATH', dirname( __FILE__ ) . '/' );
define( 'HMOT_URL', str_replace( ABSPATH, site_url( '/' ), HMOT_PATH ) );

/**
 * hmot_prepare_plugin function.
 * 
 * @access public
 * @return void
 */
function hmot_prepare_plugin() {
		
		register_post_type( 'overtime',
			array(
				'labels' => array(
					'name' => __( 'Overtime' ),
					'singular_name' => __( 'Overtime' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __( 'Add New Overtime' ),
					'edit' => __( 'Overtime' ),
					'edit_item' => __( 'Edit Overtime' ),
					'new_item' => __( 'New Overtime' ),
					'view' => __( 'View Overtime' ),
					'view_item' => __( 'View Overtime' ),
					'search_items' => __( 'Search Overtime' ),
					'not_found' => __( 'No Overtime Found' ),
					'not_found_in_trash' => __( 'No Overtime found in Trash' ),
					'parent' => __( 'Overtime' ),
				),
				'show_ui' => true,
				'has_archive' => false
			)
		);
		
		register_post_type( 'resolved-overtime',
			array(
				'labels' => array(
					'name' => __( 'Resolved Overtime' ),
					'singular_name' => __( 'Resolved Overtime' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __( 'Add New Resolved Overtime' ),
					'edit' => __( 'Resolved Overtime' ),
					'edit_item' => __( 'Edit Resolved Overtime' ),
					'new_item' => __( 'New Resolved Overtime' ),
					'view' => __( 'View Resolved Overtime' ),
					'view_item' => __( 'View Resolved Overtime' ),
					'search_items' => __( 'Search Resolved Overtime' ),
					'not_found' => __( 'No Resolved Overtime Found' ),
					'not_found_in_trash' => __( 'No Resolved Overtime found in Trash' ),
					'parent' => __( 'Resolved Overtime' ),
				),
				'show_ui' => true,
				'has_archive' => false
			)
		);
				
		require_once( HMOT_PATH . 'hmot-user-class.php' );
		
		//Load the plugin page(s)
		add_action( 'admin_menu', function() { 
			
			if ( current_user_can( 'administrator' ) && get_user_meta( get_current_user_id(), 'hmot_active', true ) ) { 
				
				add_menu_page( 'Overtime', 'Overtime', 'read', 'overtime', 'hmot_overtime_page' );
				add_submenu_page( 'overtime', 'Users', 'Users', 'administrator', 'overtime_users', 'hmot_all_overtime_page' );
				
				add_action ( 'load-overtime_page_overtime_users', 'hmot_enqueue_styles' );
			}
			
			elseif ( current_user_can( 'administrator' ) ) {
			
				add_menu_page( 'Overtime', 'Overtime', 'read', 'overtime', 'hmot_all_overtime_page' );
			}
			
			elseif( get_user_meta( get_current_user_id(), 'hmot_active', true ) )  {
				
				add_menu_page( 'Overtime', 'Overtime', 'read', 'overtime', 'hmot_overtime_page' );
			}

		} );	
		
		// Enqueue styles
		add_action( 'load-toplevel_page_overtime', function () {
		
			wp_enqueue_style( 'hmot-styles', HMOT_URL . 'hmot-styles.css' );
		} );
		
}
add_action( 'init', 'hmot_prepare_plugin' );

/**
 * hmot_all_overtime_page function.
 * 
 * @access public
 * @return void
 */
function hmot_all_overtime_page() {
	
	$users = get_users( array(
		
		'meta_key' => 'hmot_active',
		'meta_compare' => '>',
		'meta_value' => 0,
	
	) );

	?>
	<div class="wrap">
	
		<div id="icon-users" class="icon32"><br></div><h2>All Users' Overtime</h2>
		<div class="clearfix"></div>
		
		<?php if ( isset( $_GET['resolving-done'] ) ): ?>
			
			<div class="updated message"><p>Overtime resolved for user: <?php echo get_userdata( (int) $_GET['resolving-done'] )->display_name;?>.</p></div>
		
		<?php endif; ?> 
		
		<?php foreach ( $users as $user ): ?>	
			
			<div class="widefat hmot">
				<h1><?php echo $user->display_name; ?></h1>
				<?php hmot_show_single_user_overtime( $user->ID, true ); ?>
			</div>
		
		<?php endforeach; ?>
	
	</div>
	<?php

}

/**
 * hmot_my_overtime_page function.
 * 
 * @access public
 * @return void
 */
function hmot_overtime_page () {

	?>
	<div class="wrap">
	
		<div id="icon-users" class="icon32"><br></div><h2>My Overtime</h2>
		<div class="clearfix"></div>	
		
		<?php if ( isset( $_GET['logging-done'] ) ): ?>
			
			<div class="updated message"><p>Your Overtime has been successfully Logged!</p></div>
		
		<?php endif; ?> 
		
		<div class="widefat hmot">
			<?php hmot_show_single_user_overtime(); ?>
		</div>
		
		<div class="widefat hmot">
			<?php hmot_booking_form(); ?>
		</div>
		
		<div class="widefat hmot">
			<?php hmot_history( get_current_user_id() ); ?>
		</div>		
	
	</div>
	<?php
}

/**
 * hmot_show_single_user_overtime function.
 * lets test some outputting!
 * @access public
 * @return object
 */
function hmot_show_single_user_overtime( $user_id = 0, $show_resolver = false ) {
	
	if ( ! $user_id )
		$user_id = get_current_user_id();
		
	if ( ! $user_id )
		return false;		

	$user = new HMOT_User( $user_id );
		
	?>	
		<table class="hmot-user-details">
		
			<tbody>
				
				<tr>
					<td><h2>Pending Overtime Hours</h2></td>
					<td><h2><?php echo hmot_in_hours( $user->get_pending_overtime() ); ?> Hours</h2></td>
				</tr>
				
				<tr>
					<td><h3>Pending Overtime Payment</h3></td>
					<td><h3>&pound<?php echo $user->get_pending_payment(); ?></h3></td>
				</tr>
				
				<tr>
					<td><h3>Net Overtime Recorded<h3></td>
					<td><h3><?php echo hmot_in_hours( $user->get_total_overtime() ); ?> hours</h3></td>
				</tr>
				
				<tr>
					<td><h3>Net Overtime Payments<h3></td>
					<td><h3>&pound<?php echo $user->get_total_payment(); ?></h3></td>
					<td>
						<?php if ( $show_resolver ) :?> 	
							<form method="post">
								<input type="hidden" name="hmot_overtime_to_resolve" value="<?php echo $user->ID; ?>"/>
								<input type="submit" class="button-primary hmot" name="overtime_resolved_<?php echo $user->ID; ?>" value="resolve" />
							</form>
						<?php endif; ?>	
					</td>
				</tr>				
						
			</tbody>
		
		</table>
	
		<div class="clearfix"></div>		
	<?php
	
	return $user;
}

/**
 * hmot_booking_form function.
 * 
 * @access public
 * @return void
 */
function hmot_booking_form() {
	
	?>
	 <form method="post">
	     
	     <table class="form-table">
	     	
	     	<tr>
	     		<td colspan="3"><h2 class="block">Log some overtime</h2></td>
	     	</tr>
	     	
	     	<tr>
	     		<th>
	     			<label for="hmot_offset">How Long
	     		</label></th>
	     		<td>
	     			<input type="text" placeholder="1 hour 30 minutes" name="hmot_overtime_duration" id="hmot_overtime_duration" value="" class="regular-text" /><br />
	     			<span class="description">The amount of overtime you have done ( e.g. 1 hour 40 minutes )</span>
	     		</td>
	     		<td></td>
	     	</tr>
	     	
	     	<tr>
	     		<th>
	     			<label for="hmot_offset">The Date
	     		</label></th>
	     		<td>
	     			<input type="text" placeholder="yyyy-dd-mm" name="hmot_overtime_date" id="hmot_overtime_date" value="<?php echo date( 'Y-m-d', time() ); ?>" class="regular-text" /><br />
	     			<span class="description">The date which you did the overtime work. </span>
	     		</td>
	     		<td></td>
	     	</tr>
	     	
	     	<tr>
	     		<th>
	     			<label for="hmot_offset">Description
	     		</label></th>
	     		<td>
	     			<textarea class="widefat" placeholder="Description" name="hmot_overtime_description" id="hmot_overtime_description" value="" class="regular-text"></textarea><br />
	     			<span class="description">(Optional) Make a note of what you did</span>
	     		</td>
	     		<td class="hmot-button">
	     			<input class="button-primary hmot" type="submit" value="Log it" />
	     		</td>
	     	</tr>
	     						
	     </table>		
	 </form>	     
	<?php 
}

/**
 * hmot_history function.
 * 
 * @access public
 * @param mixed $user_id
 * @return void
 */
function hmot_history( $user_id ) {
	
	$posts = get_posts( array(
		
		'post_type' 	=> 'overtime',
		'author'		=> $user_id,
		
	) ); ?>
	
		<table class="hmot_history">
			<tbody>		
				<tr>
					<td clospan="3"><h2 class="block">My Overtime History</h2></td>
					<td></td>
				</tr>
				
				<?php if ( ! $posts ): ?>
					
					<tr>
						<td colspan="2">No History</td>
					</tr>		
				
				<?php endif; ?>
				
				<?php foreach ( (array) $posts as $post ):
		
					$date = date( 'l \t\h\e j\t\h \o\f F Y', (int) get_post_meta( $post->ID, 'hmot_date', true ) );
					$duration = date( 'G \h\o\u\r\s i \m\i\n\s', (int) get_post_meta( $post->ID, 'hmot_duration', true ) )
					?>
			
					<tr>
						<td class="hmot-date"><?php echo $date; ?></td>
						
						<td> 
							<span><?php echo $duration; ?></span><br />
							<span>&quot;<?php echo $post->post_content;?>&quot;</span> <br />
						</td>
					</tr>
			
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php
}

/**
 * hmot_add_overtime function.
 * 
 * @access public
 * @return void
 */
function hmot_add_overtime_from_form_submission() {

	if ( ! isset( $_POST ) || ! isset( $_POST['hmot_overtime_date'] ) || ! isset( $_POST['hmot_overtime_duration'] ) || ! $_POST['hmot_overtime_date'] || ! $_POST['hmot_overtime_duration'] )
		return;

	$user_id = ( isset( $_POST['hmot_user_id'] ) ) ? (int) $_POST['hmot_user_id'] : get_current_user_id();


	try{  
	
		$user = new HMOT_User ( $user_id );
	
		$description = ( $_POST['hmot_overtime_description'] ) ? $_POST['hmot_overtime_description'] : 'No Description Provided';
		
		$user->log_overtime( $_POST['hmot_overtime_date'], $_POST['hmot_overtime_duration'], $description );	
		
	}catch ( Exception $e ) {
		
		add_action( 'toplevel_page_overtime', function () use ( $e ) {
			?>
			
			<div class="updated message"><p>Error: <?php var_export( $e ); ?></p></div>
			
			<?php
		} );
		
		return;		
	}
	
	wp_redirect( add_query_arg( 'logging-done', 'true', wp_get_referer( ) ) );
		
	exit;

}
add_action( 'admin_init', 'hmot_add_overtime_from_form_submission' );


/**
 * hmot_resolve_overtime function.
 * 
 * @access public
 * @return void
 */
function hmot_resolve_overtime_from_form_submission() {

	if ( ! isset( $_POST['hmot_overtime_to_resolve'] ) || ! current_user_can( 'administrator' ) )
		return;
	
	$user = new HMOT_User ( (int) $_POST['hmot_overtime_to_resolve'] );
	
	$user->resolve_overtime( get_current_user_id() );
	
	wp_redirect( add_query_arg( 'resolving-done', $user->ID, wp_get_referer( ) ) );

	exit;
}
add_action( 'admin_init', 'hmot_resolve_overtime_from_form_submission' );

/**
 * hmot_add_admin_user_edit_fields function.
 * 
 * @access public
 * @param mixed $user
 * @return void
 */
function hmot_add_admin_user_edit_fields( $user ) {
	
	if ( ! current_user_can( 'administrator' ) )
		return false;
	
	?>
	<h3>HM Overtime Settings</h3>
	<table class="form-table">
		<tr>
			<th>
				<label for="hmot_start_date">Activate HMOT for this user
			</label></th>
			<td>
				<input type="checkbox" name="hmot_active" id="hmot_active" value="1" class="regular-text" <?php checked( (bool) get_the_author_meta( 'hmot_active', $user->ID ) ); ?> /><br />
				<span class="description">Allow this user to see and log their overtime and display them in the overtime list</span>
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="hmot_offset">User's annual salary
			</label></th>
			<td>
				<input type="text" placeholder="20000" name="hmot_wage" id="hmot_wage" value="<?php echo get_the_author_meta( 'hmot_wage', $user->ID ); ?>" class="regular-text" /><br />
				<span class="description">Their annual salary</span>
			</td>
		</tr>
		
	</table>
	<?php
}
add_action( 'edit_user_profile', 'hmot_add_admin_user_edit_fields' );
add_action( 'show_user_profile', 'hmot_add_admin_user_edit_fields' );

/**
 * hmot_save_admin_user_edit_fields function.
 * 
 * @access public
 * @param mixed $user_id
 * @return void
 */
function hmot_save_admin_user_edit_fields( $user_id ) {
	
	if ( ! current_user_can( 'administrator' ) )
		return false;
		
	if ( isset( $_POST['hmot_active'] ) )	
		update_user_meta( $user_id, 'hmot_active', (int) $_POST['hmot_active'] );
		
	if ( isset( $_POST['hmot_wage'] ) )	
		update_user_meta( $user_id, 'hmot_wage', str_replace( array( '.', '-', ',', ' ' ), '', $_POST['hmot_wage'] ) );			
}
add_action( 'edit_user_profile_update', 'hmot_save_admin_user_edit_fields' );
add_action( 'personal_options_update', 'hmot_save_admin_user_edit_fields' );

/**
 * hmot_hours function.
 * 
 * @access public
 * @param mixed $timestamp
 * @return float
 */
function hmot_in_hours( $timestamp ) {
	
	return round ( ( $timestamp / strtotime( '1 hour', 0 ) ), 1 );
	
}
