<?php
/**
 * Access Denied
 *
 * This message is displayed if a *logged in* user tries to view
 * a page they don't have permission to view -- like an e-course
 * they haven't signed up for.
 *
 * @todo      Make this so much better.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

wp_die( __( 'You must purchase this e-course before you can view the lessons.', 'edd-ecourse' ) );