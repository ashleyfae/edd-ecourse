<?php
/**
 * E-Course Header
 *
 * The header information that's displayed on every course page.
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

<header id="masthead">
	<h1 class="site-title">
		<a href="<?php echo esc_url( edd_ecourse_get_dashboard_url() ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
	</h1>

	<nav id="primary-menu" class="menu" role="navigation">
		<ul>
			<?php if ( $dashboard_url = edd_ecourse_get_dashboard_url() ) : ?>
				<li>
					<a href="<?php echo esc_url( $dashboard_url ); ?>"><?php _e( 'Dashboard', 'edd-ecourse' ); ?></a>
				</li>
			<?php endif; ?>
			<li>
				<a href="#"><?php _e( 'Courses', 'edd-ecourse' ); ?></a>
				<ul>
					<li><a href="#">All</a></li>
					<li><a href="#">My First Course</a></li>
				</ul>
			</li>
		</ul>
	</nav>

	<div id="top-search">
		<form action="" method="POST">
			<input type="search" id="lesson-search" name="s" placeholder="<?php esc_attr_e( 'Search for a lesson', 'edd-ecourse' ); ?>">
		</form>
	</div>
</header>

<div id="wrapper">

	<?php
	/**
	 * Include the sidebar. Depending on the page we're on, this will
	 * either include:
	 *
	 *      `/templates/ecourse-sidebar.php` - Main dashboard sidebar navigation.
	 *      or
	 *      `/templates/ecourse-sidebar-lesson.php` - Full lesson list displayed on single lesson pages.
	 */
	edd_ecourse_get_sidebar();
	?>

	<main id="main" role="main">
