<?php
/*
Plugin Name: HMH Holidays
Description: A simple Holiday Tracker
Version: 0.1
Author: Human Made Limited
Author URI: http://hmn.md/
*/

define( 'HMH_PATH', dirname( __FILE__ ) . '/' );
define( 'HMH_URL', str_replace( ABSPATH, site_url( '/' ), HMH_PATH ) );


/**
 * hmh_show_user_holidays function.
 * lets test some outputting!
 * @access public
 * @return void
 */
function hmh_show_single_user_holidays( $user_id = 0 ) {
	
	if ( ! $user_id )
		$user_id = get_current_user_id();
		
	if ( ! $user_id )
		return;		

	$user = new HMH_User( $user_id );
	
	$user->get_holiday_time_avaliable();
		
	?>
		<table class="hmh-user-details">
		
			<tbody>
				
				<tr>
					<td><h2>Holidays available:</h2></td>
					<td><h2><?php echo hmh_in_days( $user->get_holiday_time_avaliable() ); ?> Days</h2></td>
				</tr>
				
				<tr>
					<td><h3>Holidays taken so far:</h3></td>
					<td><h3><?php echo hmh_in_days ( $user->get_total_time_taken() ); ?></h3></td>
				</tr>
				
				<tr>
					<td><h3>Net holidays earned:<h3></td>
					<td><h3><?php echo hmh_in_days( $user->get_total_holidays_earned() ); ?></h3></td>
				</tr>
						
			</tbody>
		
		</table>
		
		<div class="hmh-chart-wrap"><?php hmh_show_pie_chart( $user->get_holiday_time_avaliable(), $user->get_total_time_taken(), $user_id ); ?></div>

		<div class="clearfix"></div>
		
	<?php
	
	return $user;
}

function hmh_my_holidays_page () {

	?>
	<div class="wrap">
	
		<div id="icon-users" class="icon32"><br></div><h2>My Holidays</h2>
		<div class="clearfix"></div>	
		
		<div class="widefat hmh">
			<?php hmh_show_single_user_holidays(); ?>
		</div>
		
		<div class="widefat hmh">
			
			<form method="post">
				
				<table class="form-table">
					<tr>
						<td colspan="2"><h2 class="block">Book a Holiday</h2></td>
					</tr>
					
					<tr>
						<th>
							<label for="hmh_start_date">The Date
						</label></th>
						<td>
							<input type="text" placeholder="yyyy-mm-dd" name="hmh_holiday_start_date" id="hmh_holiday_start_date" value="" class="regular-text" /><br />
							<span class="description">The date that you wish to start your holiday, don't include weekends</span>
						</td>
					</tr>
					
					<tr>
						<th>
							<label for="hmh_offset">How Long
						</label></th>
						<td>
							<input type="text" placeholder="days" name="hmh_holiday_duration" id="hmh_holiday_duration" value="" class="regular-text" /><br />
							<span class="description">The amount of time you wish to take off (in days), don't include weekends</span>
						</td>
					</tr>
				</table>
				
				<input class="button-primary hmh" type="submit" value="Book it" />
				<div class="clearfix"></div>
			</form>
		</div>		
	</div>
	<?php
}

function hmh_add_holiday() {

	if ( ! isset( $_POST ) || ! isset( $_POST['hmh_holiday_start_date'] ) || ! isset( $_POST['hmh_holiday_duration'] ) || ! $_POST['hmh_holiday_start_date'] || ! $_POST['hmh_holiday_duration'] )
		return;

	$user_id = ( isset( $_POST['hmh_user_id'] ) ) ? (int) $_POST['hmh_user_id'] : get_current_user_id();

	$user = new HMH_User ( $user_id );
	
	$user->book_holiday( $_POST['hmh_holiday_start_date'], $_POST['hmh_holiday_duration'] . ' days' );	

}
add_action( 'admin_init', 'hmh_add_holiday' );


function hmh_all_holidays_page() {
	
	$users = get_users( array(
		
		'meta_key' => 'hmh_employment_start',
		'meta_compare' => '>',
		'meta_value' => 0,
	
	) );

	?>
	<div class="wrap">
	
		<div id="icon-users" class="icon32"><br></div><h2>All User Holidays</h2>
		<div class="clearfix"></div>
		
		<?php foreach ( $users as $user ): ?>	
			
			<div class="widefat hmh">
				<h1><?php echo $user->display_name; ?></h1>
				<?php hmh_show_single_user_holidays( $user->ID ); ?>
			</div>
		
		<?php endforeach; ?>
	
	</div>
	<?php

}

/**
 * hmh_prepare_plugin function.
 * 
 * @access public
 * @return void
 */
function hmh_prepare_plugin() {
	
		register_post_type( 'holiday',
			array(
				'labels' => array(
					'name' => __( 'holiday' ),
					'singular_name' => __( 'Holiday' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __( 'Add New Holiday' ),
					'edit' => __( 'Holiday' ),
					'edit_item' => __( 'Edit Holiday' ),
					'new_item' => __( 'New Holiday' ),
					'view' => __( 'View Holiday' ),
					'view_item' => __( 'View Holiday' ),
					'search_items' => __( 'Search Holiday' ),
					'not_found' => __( 'No Holidays Found' ),
					'not_found_in_trash' => __( 'No Holidays found in Trash' ),
					'parent' => __( 'Holidays' ),
				),
				'show_ui' => true,
				'has_archive' => false
			)
		);
		
		require_once( HMH_PATH . 'hmh-user-class.php' );
		
		add_action( 'admin_menu', function() { 
			
			add_menu_page( 'Holidays', 'Holidays', 'read', 'holidays', 'hmh_my_holidays_page' );
			add_submenu_page( 'holidays', 'Users', 'Users', 'administrator', 'users', 'hmh_all_holidays_page' );
		} );	
		
		add_action( 'load-toplevel_page_holidays', 'hmh_enqueue_styles');
		add_action ( 'load-holidays_page_users', 'hmh_enqueue_styles' );
		
}
add_action( 'init', 'hmh_prepare_plugin' );

function hmh_enqueue_styles() {

	wp_enqueue_style( 'hmh-styles', HMH_URL . 'hmh-styles.css' );
}

function hmh_add_admin_user_edit_fields( $user ) {
	
	if ( ! current_user_can( 'administrator' ) )
		return false;
	
	?>
	<h3>HM Holidays Settings</h3>
	<table class="form-table">
		<tr>
			<th>
				<label for="hmh_start_date">Employment Started
			</label></th>
			<td>
				<input type="text" placeholder="yyyy-mm-dd" name="hmh_start_date" id="hmh_start_date" value="<?php echo date( 'Y-m-d', get_the_author_meta( 'hmh_employment_start', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">The date that they started working for the company</span>
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="hmh_offset">Holidays Already taken
			</label></th>
			<td>
				<input type="text" placeholder="days" name="hmh_offset" id="hmh_offset" value="<?php echo hmh_in_days( get_the_author_meta( 'hmh_holidays_offset', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Holidays they have already taken before being added to HMH (in days)</span>
			</td>
		</tr>

		<tr>
			<th>
				<label for="hmh_per_year">Holidays Per Year
			</label></th>
			<td>
				<input type="text" placeholder="days" name="hmh_per_year" id="hmh_per_year" value="<?php echo hmh_in_days( get_the_author_meta( 'hmh_holidays_per_year', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">The amount of holidays they are given per year (in days)</span>
			</td>
		</tr>
		
	</table>
	<?php
}
add_action( 'edit_user_profile', 'hmh_add_admin_user_edit_fields' );
add_action( 'show_user_profile', 'hmh_add_admin_user_edit_fields' );


function hmh_save_admin_user_edit_fields( $user_id ) {
	
	if ( ! current_user_can( 'administrator', $user_id ) )
		return false;
		
	if ( isset( $_POST['hmh_start_date'] ) )	
		update_user_meta( $user_id, 'hmh_employment_start', strtotime( $_POST['hmh_start_date'] ) );
		
	if ( isset( $_POST['hmh_offset'] ) )	
		update_user_meta( $user_id, 'hmh_holidays_offset', strtotime( $_POST['hmh_offset'] . ' days', 0 ) );		

	if ( isset( $_POST['hmh_per_year'] ) )	
		update_user_meta( $user_id, 'hmh_holidays_per_year', strtotime( $_POST['hmh_per_year'] . ' days', 0 ) );		
}
add_action( 'edit_user_profile_update', 'hmh_save_admin_user_edit_fields' );
add_action( 'personal_options_update', 'hmh_save_admin_user_edit_fields' );

function hmh_show_pie_chart( $value1, $value2, $user_id = 0 ) {
	
	$value1 = ( hmh_in_days( $value1 ) > 0 ) ? hmh_in_days( $value1 ): 0;
	$value2 = ( hmh_in_days( $value2 ) > 0 ) ? hmh_in_days( $value2 ) : 0;
	
	?>
			
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows([
          ['Avaliable',    <?php echo $value1; ?>],
          ['Used',         <?php echo $value2; ?>]
        ]);

        var options = {
        
        	legend: { position: 'none' },
        
        	chartArea: { width: '90%', height: '90%'  },
        	
        	pieSliceText: 'label',
        	
        	backgroundColor: { fill: '#F9F9F9' }
        
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_div_<?php echo $user_id; ?>'));
        chart.draw(data, options);
      }
    </script>
    
    <div id="chart_div_<?php echo $user_id; ?>" style="width: 200px; height: 200px;"></div>
	<?php 
}

function hmh_in_days( $timestamp ) {
	
	return round ( ( $timestamp / strtotime( '1 day', 0 ) ), 1 );
	
}
