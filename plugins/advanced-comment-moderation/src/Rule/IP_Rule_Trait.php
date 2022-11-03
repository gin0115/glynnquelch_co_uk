<?php

/**
 * IP Range Rule Processor
 *
 * Shared by IP and CIDR rules.
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

trait IP_Rule_Trait {

	/**
	 * Checks if a passed subject is outside of a defined range.
	 *
	 * @param string $start_ip
	 * @param string $end_ip
	 * @param string $subject_ip
	 * @return bool
	 */
	public function ip_outside_range( string $start_ip, string $end_ip, string $subject_ip ): bool {
		// If comment IP is invalid, return as passed
		if ( ! filter_var( $subject_ip, FILTER_VALIDATE_IP ) ) {
			return true;
		}

		$comment_ip_parts = \explode( '.', $subject_ip );
		$start_parts      = explode( '.', $start_ip );
		$end_parts        = explode( '.', $end_ip );

		// If network isn't match, just pass.
		if ( $comment_ip_parts[0] !== $start_parts[0]
		|| $comment_ip_parts[1] !== $start_parts[1]
		|| $comment_ip_parts[2] !== $start_parts[2]
		) {
			return true;
		}

		// Check the comments Host ID if not within the defined range.
		if ( $comment_ip_parts[3] >= $start_parts[3]
		&& $comment_ip_parts[3] <= $end_parts[3]
		) {
			return false;
		}

		return true;
	}
}
