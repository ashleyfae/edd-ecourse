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

			<?php foreach ( edd_ecourse_get_modules() as $module ) : ?>

				<div class="ecourse-module-group">
					<h2><?php echo esc_html( $module->title ); ?></h2>
					<ul>
						
					</ul>
				</div>

			<?php endforeach; ?>

		</div>

	</div>
<?php

/**
 * Include the footer.
 */
edd_get_template_part( 'ecourse', 'footer' );