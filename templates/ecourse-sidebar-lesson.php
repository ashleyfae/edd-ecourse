<?php
/**
 * E-Course Lesson Sidebar
 *
 * The sidebar shown on single lesson pages.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

?>
<aside id="secondary">

	<h2 class="edd-ecourse-title">
		<a href="<?php edd_ecourse_permalink(); ?>"><?php edd_ecourse_title(); ?></a>
	</h2>

	<?php foreach ( edd_ecourse_get_modules() as $module ) :
		$lessons = edd_ecourse_get_module_lessons( $module->id );

		if ( $lessons ) :
			?>
			<div class="ecourse-module-group">
				<h2><?php echo esc_html( $module->title ); ?></h2>
				<ul>
					<?php foreach ( $lessons as $lesson ) :
						$extra_class = ( $lesson->ID == get_the_ID() ) ? 'is-current-lesson' : '';
						?>
						<li id="lesson-<?php echo esc_attr( $lesson->ID ); ?>"<?php edd_ecourse_lesson_class( $lesson, $extra_class ); ?>>
							<a href="<?php echo esc_url( get_permalink( $lesson ) ); ?>">
								<span class="ecourse-lesson-status"><?php edd_ecourse_lesson_completion_icon( $lesson ); ?></span>
								<span class="ecourse-lesson-title"><?php echo esc_html( get_the_title( $lesson ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		endif;
	endforeach; ?>

</aside>
