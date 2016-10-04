<?php
/**
 * E-Course Archive
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

/**
 * Include the header.
 */
edd_get_template_part( 'ecourse', 'header' );

echo wpautop('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas laoreet ultrices risus nec iaculis. Aenean luctus semper sem, et bibendum ligula iaculis a. Ut eu elementum dolor. Vivamus faucibus neque purus, at imperdiet ex tempus eu. Donec eget tortor sapien. Nulla id turpis eu sem vehicula elementum. Aenean eget tellus imperdiet, semper eros sed, viverra velit. Proin sed ante quam. Suspendisse eu mi a arcu gravida lacinia. Nulla facilisi. Curabitur aliquet nulla ut orci pharetra, nec cursus velit ornare. Phasellus iaculis ante augue, id consequat magna aliquet quis. Donec sit amet posuere ipsum. Ut ultrices viverra interdum. Nullam mauris odio, convallis id est eu, malesuada efficitur erat. Aenean viverra vel ante sed varius.

Vivamus euismod mi nec dolor aliquam euismod. Nam ut ullamcorper nisl, sit amet placerat erat. Duis dictum vulputate elit eget consequat. Nunc quis aliquam orci. Maecenas venenatis blandit urna, sed blandit diam scelerisque ac. Vivamus ut tortor consectetur, varius ante ut, feugiat massa. Fusce suscipit lobortis metus, gravida molestie quam sagittis quis. Duis luctus, erat in porttitor consequat, quam quam pretium urna, at ultrices dolor ante vel libero. Nam sodales felis mattis nisi tincidunt mattis quis consequat neque. Maecenas congue ex neque, eu mollis orci euismod et. Vestibulum erat massa, feugiat eget dignissim at, facilisis ac diam. Nulla sagittis dolor sit amet facilisis dignissim. Phasellus dictum nisl augue, nec egestas nisi semper sed. Mauris condimentum semper urna, in iaculis quam efficitur id. Aliquam nisi turpis, lobortis ut malesuada at, viverra eu quam.

Donec rutrum, nibh efficitur iaculis posuere, turpis purus tincidunt dui, sed pharetra nulla tellus ac libero. Etiam eget risus a purus facilisis blandit. In aliquam fringilla justo, eget auctor eros sodales eget. Integer vehicula dictum mauris in lobortis. Nulla sagittis condimentum risus, eget viverra mi accumsan ut. Integer eu ligula malesuada, volutpat libero lobortis, placerat justo. Fusce euismod faucibus pulvinar. Donec non sagittis diam. Nunc ultricies magna eu sapien pretium auctor. Sed at ante felis. Curabitur lacinia dictum rutrum. Duis mattis sollicitudin velit nec maximus. Aenean gravida fringilla augue vel mollis. Duis ultrices, libero vitae consequat fringilla, sapien nunc tempus elit, in sagittis ligula dui id tortor. Maecenas tellus augue, mollis sit amet porta ultricies, cursus vel felis. Duis euismod, augue vel finibus tincidunt, elit nisi eleifend urna, id pretium massa ante eu urna.');

/**
 * Include the footer.
 */
edd_get_template_part( 'ecourse', 'footer' );