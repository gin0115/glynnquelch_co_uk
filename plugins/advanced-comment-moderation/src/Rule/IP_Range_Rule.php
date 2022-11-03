<?php

/**
 * IP Range Rule
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Team51\Advanced_Plugin_Moderation\Rule\Rule;
use Team51\Advanced_Plugin_Moderation\Moderation\Comment;
use Team51\Advanced_Plugin_Moderation\Rule\IP_Rule_Trait;

class IP_Range_Rule extends Rule {

	use IP_Rule_Trait;

	/**
	 * The start of the IP range
	 *
	 * @var string
	 * @since 0.1.0
	 */
	public $start_ip;

	/**
	 * The end of the IP Range
	 *
	 * @var string
	 * @since 0.1.0
	 */
	public $end_ip;

	/**
	 * Validate the commenters IP Address.
	 *
	 * @var bool
	 * @since 0.1.0
	 */
	public $ip_address = true;

	/**
	 * Validates the comment against the rule.
	 *
	 * @param Comment $comment
	 * @return bool
	 * @since 0.1.0
	 */
	public function is_valid_comment( Comment $comment ): bool {
		return $this->ip_outside_range( $this->start_ip, $this->end_ip, $comment->ip );
	}

	/**
	 * Returns the rule type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'ip_range';
	}
}
