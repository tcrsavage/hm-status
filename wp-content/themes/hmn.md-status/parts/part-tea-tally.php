<div class="grid-4 grid-x-6 component color-b">

  <?php

    $tally = new HM_Tea_Tally();
    foreach( $tally->users as $user ) {

        $users[ $user->ID ] = array(
            'display_name' => $user->display_name,
            'total' => $user->hmtt_total
        );

        //$max = 8;
        if( ! isset( $max ) || isset( $max ) && $max < $user->hmtt_total )
        	$max = $user->hmtt_total;


    }


  ?>


 <div class="tea-chart" style="margin-left: 3em;">

    <ol class="graph blocks horz">

	<?php
		foreach( $users as $user_id => $info ) :

			$width = $info['total'] / $max * 100;

			if( $info['total'] < 0 )
				$width = 1 / $max * 100;

			$separators = ( $info['total'] >= 0 ) ? $info['total'] : 0;

			unset( $li_class );
			$li_class[] = ( $info['total'] >= $max ) ? 'alert' : null;
			$li_class[] = ( $info['total'] <= 0 ) ? 'negative' : null;
			$li_class[] = ( $user_id == get_current_user_id() ) ? 'current-user' : null;
			$li_class = array_filter( $li_class );

	?>

		<li
      <?php if( ! empty( $li_class ) ) echo 'class="' . implode( ' ', $li_class ) . '"'; ?>
      data-userid="<?php echo $user_id; ?>"
      data-total="<?php echo $info['total']; ?>"
      title="<?php echo $info['display_name']; ?>"
      >
			<span class="tea-avatar"><?php echo get_avatar( $user_id, '54' ); ?></span>
			<span class="separators">
				<?php for ( $i = 1; $i <= $separators; $i++ ) { echo '<span class="separator" data-total="' . $info['total'] . '" style="width:' . ( 100 / $max - 2 ) . '%; margin-right: 2%;"></span>'; } ?>
			</span>
        </li>

    <?php

      endforeach;

    ?>

      </ol>

    </div>

</div>