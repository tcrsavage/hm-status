<div class="grid-4 grid-x-4 component color-b">

  <h3>Tea</h3>

  <div class="tea-chart" style="margin-left: 3em;">

    <ol class="graph horz">

      <?php
        $elements = array(
          'Tom' => 8,
          'Joe' => 6,
          'Theo' => 4,
          'Matt' => 4,
          'Owain' => 1
        );

        foreach( $elements as $key => $value ) :
          $width = $value / reset( $elements ) * 100;
      ?>

        <li><span class="tea-avatar"></span><a style="width:<?php echo $width; ?>%"><span class="tooltip"><span><?php echo $key; ?> (<?php echo $value; ?>)</span></span></a></li>

    <?php

      endforeach;

    ?>

      </ol>

    </div>

</div>