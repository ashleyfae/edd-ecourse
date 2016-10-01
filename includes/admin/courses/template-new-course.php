<?php
/**
 * New E-Course Template
 *
 * This template is used after a new e-course is inserted and we need
 * to add it into the course grid.
 *
 * @package   EDD\E-Course\Admin\Courses\Template\NewCourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script id="tmpl-edd-ecourse-new" type="text/html">

	<div class="edd-ecourse" data-course-id="{{ data.ID }}">
		<div class="edd-ecourse-inner">
			<h2>{{{ data.name }}}</h2>

			<div class="edd-ecourse-actions">
				<a href="{{ data.view_lessons_url }}" class="button edd-ecourse-tip edd-ecourse-action-lessons" title="<?php esc_attr_e( 'View Lessons', 'edd-ecourse' ); ?>">
					<span class="dashicons dashicons-list-view"></span>
				</a>

				<a href="{{ data.edit_course_url }}" class="button edd-ecourse-tip edd-ecourse-action-edit" title="<?php esc_attr_e( 'Edit Course', 'edd-ecourse' ); ?>">
					<span class="dashicons dashicons-edit"></span>
				</a>

				<button href="#" class="button edd-ecourse-tip edd-ecourse-action-delete" title="<?php esc_attr_e( 'Delete Course', 'edd-ecourse' ); ?>" data-nonce="{{ data.nonce }}">
					<span class="dashicons dashicons-trash"></span>
				</button>
			</div>
		</div>
	</div>

</script>
