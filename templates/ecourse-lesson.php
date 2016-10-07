<?php
/**
 * E-Course Lesson
 *
 * Displays the contents of an individual lesson.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
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

	<article id="ecourse-<?php echo esc_attr( edd_ecourse_get_id() ); ?>"<?php edd_ecourse_lesson_class( get_post() ); ?>>

		<header>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<div class="entry-content box">
			<?php the_content(); ?>
		</div>

	</article>

<?php endwhile;

/**
 * Include the footer.
 */
edd_get_template_part( 'ecourse', 'footer' );