<?php
/**
 * E-Course Main Template
 *
 * This is the main template that gets loaded and checks to see which
 * piece of content should actually be loaded. We have this main template
 * mainly so we can perform permission checks.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

if ( edd_ecourse_user_can_view_page() ) {

	edd_ecourse_load_page_template();

} else {

}