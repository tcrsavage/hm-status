<?php 
if ( !is_user_logged_in() ){
	
	wp_redirect( site_url('hm-holiday/login/') );
	exit;
}

$user_id = ( isset( $_GET['user_id'] ) ) ? $_GET['user_id'] : get_current_user_id();
		
?>

<html>

	<head>
		<link href="<?php echo HMH_URL . 'hmh-styles.css'; ?>" rel="stylesheet" type="text/css">
	</head>
		
	<body class="hmh-body">
	
		<div class="hmh-header-wrap">
			<div class="hmh-header" ><h2 class="hmh-h2"><?php echo get_userdata( $user_id )->display_name; ?></h2></div>
		</div>
		
		<div class="hmh-main-wrap">
		
			<div class="hmh-content">
					
					<?php hmh_show_single_user_holidays( $user_id ); ?>
							
			</div>
		
		</div>
	
	</body>

</html>

