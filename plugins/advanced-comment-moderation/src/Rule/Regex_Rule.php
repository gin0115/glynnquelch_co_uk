<?php

/**
 * Regex
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Team51\Advanced_Plugin_Moderation\Rule\Rule;
use Team51\Advanced_Plugin_Moderation\Moderation\Comment;

class Regex_Rule extends Rule {

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

		// Authors name.
		if ( $this->author === true
		&& is_string( $comment->author )
		&& \mb_strlen( $comment->author ) > 0 // May not be set.
		&& (bool) \preg_match( stripslashes( $this->expression ), $comment->author )
		) {
			return false;
		}

		// Authors email.
		if ( $this->email === true
		&& is_string( $comment->email )
		&& \mb_strlen( $comment->email ) > 0 // May not be set.
		&& (bool) \preg_match( stripslashes( $this->expression ), $comment->email )
		) {
			return false;
		}

		// Authors url.
		if ( $this->url === true
		&& is_string( $comment->url )
		&& \mb_strlen( $comment->url ) > 0 // May not be set.
		&& (bool) \preg_match( stripslashes( $this->expression ), $comment->url )
		) {
			return false;
		}

		// Authors agent.
		if ( $this->agent === true
		&& is_string( $comment->agent )
		&& (bool) \preg_match( stripslashes( $this->expression ), $comment->agent )
		) {
			return false;
		}

		// Comment content.
		if ( $this->content === true
		&& is_string( $comment->content )
		&& (bool) \preg_match( stripslashes( $this->expression ), $comment->content )
		) {
			return false;
		}

		return true;

	}

		/**
	 * Returns the rule type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return 'regex';
	}
}
