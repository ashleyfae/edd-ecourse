<?php
/**
 * EDD Actions
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
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
 * @since 1.0.0
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
 * @since 1.0.0
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