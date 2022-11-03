<?php

/**
 * Comment model
 *
 * @package Team51_Advanced_Comment_Moderation\Admin
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Moderation;

use Team51\Advanced_Plugin_Moderation\Rule\Rule;
use Team51\Advanced_Plugin_Moderation\Rule\Rule_Repository;

class Comment_Moderator {

	/**
	 * All rules
	 *
	 * @var Rule[]
	 */
	protected $rules;

	public function __construct() {
		$this->rules = ( new Rule_Repository() )->get_rules();
	}

	/**
	 * Moderates the comment.
	 * Returns false if failed any rules.
	 *
	 * @param Comment $comment
	 * @return int|string
	 */
	protected function process_comment( Comment $comment ) {
		foreach ( $this->rules as $rule ) {
			$result = $rule->is_valid_comment( $comment );

			// If we have an invalid rule, return.
			if ( false === $result ) {
				return $this->parse_failed_response( $rule );
			}
		}
		return 1;
	}

	/**
	 * Validates a WP Comment array
	 *
	 * @param int|string $approved
	 * @param array<mixed> $comment_data
	 * @return int|string
	 */
	public function validate_comment( $approved, array $comment_data ) {

		// Bail if we have other comment type.
		if ( 'comment' !== $comment_data['comment_type'] ) {
			return $approved;
		}

		// Construct comment.
		$comment          = new Comment();
		$comment->post_id = $comment_data['comment_post_ID'];
		$comment->user_id = $comment_data['user_id'];
		$comment->author  = $comment_data['comment_author'];
		$comment->email   = $comment_data['comment_author_email'];
		$comment->url     = $comment_data['comment_author_url'];
		$comment->ip      = $comment_data['comment_author_IP'];
		$comment->agent   = $comment_data['comment_agent'];
		$comment->content = $comment_data['comment_content'];

		return $this->process_comment( $comment );
	}

	/**
	 * Based on the rule, return the correct response.
	 *
	 * @param \Team51\Advanced_Plugin_Moderation\Rule\Rule $rule
	 * @return string|int
	 */
	public function parse_failed_response( Rule $rule ) {
		switch ( $rule->response ) {
			case Rule::MARK_PENDING:
				return 0;

			case Rule::MARK_SPAM:
				return 'spam';

			case Rule::MARK_TRASH:
				return 'trash';

			default:
				return 1; // If other type, just approve.
		}
	}
}
