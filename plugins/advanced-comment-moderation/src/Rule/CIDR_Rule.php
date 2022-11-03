<?php

/**
 * CIDR (IP Range)
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Team51\Advanced_Plugin_Moderation\Rule\Rule;
use Team51\Advanced_Plugin_Moderation\Moderation\Comment;

class CIDR_Rule extends Rule {

	/**
	 * The expression to check against.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	public $expression;

	/**
	 * Validates the comment against the rule.
	 *
	 * @param Comment $comment
	 * @return bool
	 * @since 0.1.0
	 */
	public function is_valid_comment( Comment $comment ): bool {

		// Extract the CIDR range
		try {
			$range = $this->cidr_to_range( $this->expression );
		} catch ( \Throwable $th ) {
			// Returns true if error in creating range.
			return true;
		}

		// Check we have a valid range of longs.
		if ( ! \is_array( $range )
		|| 2 !== \count( $range )
		|| ! \is_numeric( $range[0] )
		|| ! \is_numeric( $range[1] ) ) {
			return true;
		}

		$ip_as_long = \ip2long( $comment->ip );
		// Pass if the commenters IP isnt valid.
		if ( ! \is_numeric( $ip_as_long ) ) {
			return true;
		}

		// If inside range, fail.
		if ( $ip_as_long >= $range[0]
		&& $ip_as_long <= $range[1]
		) {
			return false;
		}

		return true;

	}

	/**
	 * Converts a CIDR range into an array of starting and ending ip address as
	 *
	 * @param string $cidr
	 * @return array<int|false>
	 */
	protected function cidr_to_range( string $cidr ): array {
		$range    = array();
		$cidr     = explode( '/', $cidr );
		$range[0] = long2ip( ( ip2long( $cidr[0] ) ) & ( ( -1 << ( 32 - (int) $cidr[1] ) ) ) );
		$range[1] = long2ip( ( ip2long( $range[0] ) ) + pow( 2, ( 32 - (int) $cidr[1] ) ) - 1 ); // @phpstan-ignore-line
		return array_map( 'ip2long', $range );
	}


	/**
	 * Returns the rule type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'cidr';
	}
}
