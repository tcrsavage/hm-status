<?php


function tea_tally_ajax() {

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
add_action('wp_ajax_tea_tally_ajax', 'tea_tally_ajax' );
add_action('wp_ajax_nopriv_tea_tally_ajax', 'tea_tally_ajax' );