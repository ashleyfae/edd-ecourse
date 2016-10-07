<?php
/**
 * E-Course Archive
 *
 * Displays a list of modules and lessons in the current course.
 *
 * @global object $edd_ecourse DB object for the currently displayed e-course.
 *
 * @todo      :
 *      E-Course class attribute class.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

/**
 * Include the header.
 */
edd_get_template_part( 'ecourse', 'header' );

?>
	<div id="ecourse-<?php echo esc_attr( edd_ecourse_get_id() ); ?>" class="ecourse-archive-list">

		<header>
			<h1 class="entry-title"><?php edd_ecourse_title(); ?></h1>
		</header>

		<div class="entry-content">

			<?php foreach ( edd_ecourse_get_modules() as $module ) :
				$lessons = edd_ecourse_get_module_lessons( $module->id );

				if ( $lessons ) :
					?>
					<div class="ecourse-module-group">
						<h2><?php echo esc_html( $module->title ); ?></h2>
						<ul>
							<?php foreach ( $lessons as $lesson ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $lesson ) ); ?>">
										<span class="ecourse-lesson-status"><?php edd_ecourse_lesson_completion( $lesson ); ?></span>
										<span class="ecourse-lesson-title"><?php echo esc_html( get_the_title( $lesson ) ); ?></span>
										<span class="ecourse-lesson-type"><?php edd_ecourse_lesson_type_icon( $lesson ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php
				endif;
			endforeach; ?>

		</div>

	</div>
<?php

/**
 * Include the footer.
 */
edd_get_template_part( 'ecourse', 'footer' );