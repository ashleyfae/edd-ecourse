<?php
/**
 * New Module Template
 *
 * This template is used after a newmodule is inserted and we need
 * to add it into the module list.
 *
 * @package   EDD\E-Course\Admin\Courses\Template\NewModule
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script id="tmpl-edd-ecourse-new-module" type="text/html">

	<div class="postbox edd-ecourse-module-group" data-module="{{ data.ID }}">
		<h3 class="hndle">
			<span class="edd-ecourse-module-title">{{{ data.title }}}</span>
			<button class="button edd-ecourse-edit-module-title"><?php _e( 'Edit', 'edd-ecourse' ); ?></button>
			<a href="{{ data.lesson_url }}" class="button button-primary edd-ecourse-add-module-lesson"><?php _e( 'Add Lesson', 'edd-ecourse' ); ?></a>
		</h3>
		<div class="inside">
		</div>
	</div>

</script>