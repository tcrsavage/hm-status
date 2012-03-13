<?php get_header(); ?>

<div class="content">
	
	<?php hm_the_messages(); ?>
	
	<?php while ( have_posts() ): the_post(); ?>

		<h2><?php the_title(); ?></h2>
	
		<?php the_content(); ?>

	<?php endwhile; ?>
	
</div>

<?php get_footer(); ?>