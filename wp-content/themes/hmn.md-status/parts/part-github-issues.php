<?php

	return;

	function hm_logg( $var ) {

		error_log( var_export( $var, true ) );

	}

	require_once TEMPLATEPATH . '/functions/class.github-api/lib/Github/Autoloader.php';
	Github_Autoloader::register();

	$user = array( 'username' => 'willmot', 'api-key' => '8b8be0cc70e485f28c018f356b6db991' );

	$github = new Github_Client();
 	$github->authenticate( $user['username'], $user['api-key'], AUTH_HTTP_TOKEN );

?>
<div class="grid-8 grid-x-2 component color-d">

<?php

	$repos = $github->getRepoApi()->getUserRepos( 'humanmade' );
	/*
	foreach( $repos as $repo ) {



		$issues = $github->getIssueApi()->getList( 'humanmade', $repo['name'], 'open', 'assigned' );

		$r[ $repo['name'] ] = count( $issues );

	}
	*/

	$r = $github->getIssueApi()->getList( 'humanmade', 'backupwordpress', 'open', 'assigned' );

	hm_logg( count($r) );

?>




</div>