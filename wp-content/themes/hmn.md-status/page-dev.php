<?php get_header(); ?>

<div class="grid-6 grid-x-6 component color-a">

	<h1>Component Title</h1>
	<p>Mauris iaculis porttitor posuere. Praesent id metus massa, ut blandit odio. Proin quis tortor orci. Etiam at risus et justo dignissim congue. Donec congue lacinia dui, a porttitor lectus condimentum laoreet. Nunc eu ullamcorper orci. Quisque eget odio ac.</p>

</div>

<div class="grid-6 grid-x-2 component color-d">

	<?php //get_template_part( 'parts/bar-chart' ); ?>

	<ol id="commit-chart" class="graph vert multi">

		<?php
        	$elements = array(
        		'Item 1' => array(
                    'total' => 170,
                    'split' => array( 10, 40, 80, 20, 20 )
                    ),
                'Item 2' => array(
                    'total' => 110,
                    'split' => array( '30', '20', '60' )
                    ),
                'Item 3' => array(
                    'total' => 140,
                    'split' => array( '110', '10', '20' )
                    ),
                'Item 4' => array(
                    'total' => 80,
                    'split' => array( '20', '20', '40' )
                    ),
                'Item 5' => array(
                    'total' => 40,
                    'split' => array( '10', '10', '20' )
                    ),
                'Item 6' => array(
                    'total' => 140,
                    'split' => array( '40', '60', '10', '30' )
                    )
        	);

			$width = 100 / count( $elements );

            $total = 0;
            foreach( $elements as $value )
                if( $value['total'] > $total )
                    $total = $value['total'];

        	foreach( $elements as $value ) :

        		$height = (int) $value['total'] / $total * 100;

                //echo 'height: ' . $height;

        ?>

        	<li style="width:<?php echo $width; ?>%;">
        		<span class="bar" style="height:<?php echo $height; ?>%;">
                    <?php
                        foreach( $value['split'] as $split ) :
                            $height = (int) $split / $value['total'] * 100;
                    ?>
                    <span class="split" style="height:<?php echo $height; ?>%;"></span>
                    <?php
                        endforeach;
                    ?>
                </span>
        	</li>

		<?php endforeach; ?>

	</ol>

</div>

<?php get_template_part( 'parts/part-tea-tally' ); ?>

<div class="grid-2 grid-x-4 component color-e">

	<h4>Messages</h4>
	<ul class="linklist">
		<li><span>Nunc eu ullamcorper orci.</span></li>
		<li><span>Quisque eget odio ac lectus.</span></li>
		<li><span>Vestibulum eget in metus.</span></li>
		<li><span>In faucibus vestibulum.</span></li>
	</ul>

</div>

<div class="grid-2 grid-x-2 component color-c"></div>
<div class="grid-2 grid-x-2 component color-b"></div>

<?php get_footer(); ?>
