<?php
/**
 * E-Course Dashboard Page
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 */

/**
 * Include the header.
 */
edd_get_template_part( 'ecourse', 'header' );

/**
 * Lesson content.
 */
while ( have_posts() ) : the_post(); ?>

	<article id="ecourse-dashboard">

		<header>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<?php if ( get_the_content() ) : ?>
			<div class="entry-content box">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'ecourse-dashboard' ) ) : ?>
			<div id="ecourse-dashboard-widgets">
				<?php dynamic_sidebar( 'ecourse-dashboard' ); ?>
			</div>
		<?php endif; ?>

	</article>

<?php endwhile;

/**
 * Include the footer.
 */
edd_get_template_part( 'ecourse', 'footer' );