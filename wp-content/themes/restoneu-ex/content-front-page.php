<?php
/**
 * Template part for displaying posts
 *
 */

?>

<main id="main" class="site-main" role="main">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'restoneu-ex' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- /.blog-post -->
	</article><!-- /article -->
</main><!-- /.site-main -->