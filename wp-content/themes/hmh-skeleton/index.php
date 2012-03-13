<?php get_header(); ?>

<div class="content">

	<?php hmh_show_user_holidays( get_current_user_id() ); ?>
	
	<?php hm_the_messages(); ?>
	
	<?php while ( have_posts() ): the_post(); ?>

		<h2><?php the_title(); ?></h2>
	
		<?php the_content(); ?>

	<?php endwhile; ?>
	
</div>

<?php get_footer(); ?>