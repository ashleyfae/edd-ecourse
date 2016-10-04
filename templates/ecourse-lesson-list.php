<?php
/**
 * E-Course Lesson List
 *
 * Displays a list of modules and lessons in the current course.
 *
 * @global object $edd_ecourse DB object for the currently displayed e-course.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

/**
 * The following properties are available:
 *      `id` - ID of the course.
 *      `title` - Title of the course.
 *      `slug` - Course slug.
 *      `description` - Course description.
 */
global $edd_ecourse;