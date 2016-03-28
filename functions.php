<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get the email ids of all subscribers of question
 * @param  integer $post_id
 * @return array
 * @deprecated 1.3
 */
function ap_get_question_subscribers_data($post_id, $question_subsciber = true) {
	_deprecated_function( 'ap_get_question_subscribers_data', '1.3', '' );
}

/**
 * @deprecated 1.3
 */
function ap_get_comments_subscribers_data($post_id) {
	_deprecated_function( 'ap_get_comments_subscribers_data', '1.3', '' );
}

if ( ! function_exists( 'ap_in_array_r' ) ) {
	function ap_in_array_r($needle, $haystack, $strict = false) {
		foreach ( $haystack as $item ) {
			if ( ($strict ? $item === $needle : $item == $needle) || (is_array( $item ) && in_array_r( $needle, $item, $strict )) ) {
				return true;
			}
		}
		return false;
	}
}