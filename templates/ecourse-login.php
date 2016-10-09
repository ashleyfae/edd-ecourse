<?php
/**
 * E-Course Login Form
 *
 * This form is displayed on all pages if the current user is not logged in.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="ecourse-login">
	<?php echo edd_login_form(); ?>
</div>

<?php wp_footer(); ?>

</body>
</html>