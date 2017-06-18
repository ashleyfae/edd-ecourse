<?php
/**
 * E-Course Main Template
 *
 * This is the main template that gets loaded and checks to see which
 * piece of content should actually be loaded. We have this main template
 * mainly so we can perform permission checks.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 */

if ( edd_ecourse_user_can_view_page() ) {

	edd_ecourse_load_page_template();

} else {

	// User is logged in, but doesn't have course access. Show an error message.
	if ( is_user_logged_in() ) {
		edd_get_template_part( 'ecourse', 'denied' );
	} else {
		// User isn't logged in, so show login form.
		edd_get_template_part( 'ecourse', 'login' );
	}

}