<?php
/**
 * EDD Actions
 *
 * @todo      Integrate with recurring payments.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 * @since     1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Grant Course Access after Purchase
 *
 * If the purchase contains an e-course then grant the user
 * access to that e-course.
 *
 * @param int $payment_id Payment ID.
 *
 * @since 1.0
 * @return void
 */
function edd_ecourse_grant_access_after_purchase( $payment_id ) {

	$payment     = new EDD_Payment( $payment_id );
	$downloads   = $payment->downloads;
	$product_ids = array();

	// If the user doesn't have an account, we can't change anything.. bail.
	if ( $payment->user_id == 0 ) {
		return;
	}

	// Add all the download IDs to an array.
	foreach ( $downloads as $item ) {
		$product_ids[] = $item['id'];
	}

	foreach ( $product_ids as $download_id ) {
		$course = get_post_meta( $download_id, 'ecourse', true );

		// No course selected - bail.
		if ( ! $course || ! is_numeric( $course ) ) {
			continue;
		}

		edd_ecourse_grant_course_access( $course, $payment->user_id );

		do_action( 'edd_ecourse_grant_course_access_after_purchase' );
	}

}

add_action( 'edd_complete_purchase', 'edd_ecourse_grant_access_after_purchase' );

/**
 * Update Payment Status
 *
 * @param int    $payment_id Payment ID number.
 * @param string $new_status New payment status.
 * @param string $old_status Old payment status.
 *
 * @since 1.0
 * @return void
 */
function edd_ecourse_remove_access_after_refund( $payment_id, $new_status, $old_status ) {

	$payment     = new EDD_Payment( $payment_id );
	$downloads   = $payment->downloads;
	$product_ids = array();

	// If the user doesn't have an account, we can't change anything.. bail.
	if ( $payment->user_id == 0 ) {
		return;
	}

	// Add all the download IDs to an array.
	foreach ( $downloads as $item ) {
		$product_ids[] = $item['id'];
	}

	foreach ( $product_ids as $download_id ) {

		$course = get_post_meta( $download_id, 'ecourse', true );

		// No course selected - bail.
		if ( ! $course || ! is_numeric( $course ) ) {
			continue;
		}

		// Adjust course permissions.
		if ( ( $new_status == 'refunded' || $new_status == 'revoked' ) ) {
			edd_ecourse_revoke_course_access( $course, $payment->user_id );
		} elseif ( $new_status != 'refunded' && $new_status != 'revoked' ) {
			edd_ecourse_grant_course_access( $course, $payment->user_id );
		}

	}

}

add_action( 'edd_update_payment_status', 'edd_ecourse_remove_access_after_refund', 10, 3 );

/**
 * Add link to course material instead of "no files found".
 *
 * @param string $text        Existing text that reads "No downloadable files found".
 * @param int    $download_id Download ID.
 *
 * @since 1.0
 * @return string
 */
function edd_ecourse_view_course_material_link( $text, $download_id ) {

	$course_id = get_post_meta( $download_id, 'ecourse', true );

	// Not a course - bail.
	if ( empty( $course_id ) ) {
		return $text;
	}

	$course_url  = get_permalink( $course_id );
	$course_link = '<a href="' . esc_url( $course_url ) . '">' . __( 'View course material', 'edd-ecourse' ) . '</a>';

	/**
	 * Filters the view course material link and HTML.
	 *
	 * @param string $course_link Course link HTML.
	 * @param string $text        Original text that reads "No downloadable files found".
	 * @param int    $download_id ID of the download.
	 * @param int    $course_id   ID of the corresponding e-course.
	 *
	 * @since 1.0
	 */
	return apply_filters( 'edd_ecourse_view_course_material_link', $course_link, $text, $download_id, $course_id );

}

add_filter( 'edd_receipt_no_files_found_text', 'edd_ecourse_view_course_material_link', 10, 2 );