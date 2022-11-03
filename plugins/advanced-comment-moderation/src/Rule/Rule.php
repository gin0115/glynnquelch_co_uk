<?php

/**
 * Rule Interface
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Team51\Advanced_Plugin_Moderation\Moderation\Comment;

abstract class Rule {

	/**
	 * If the comment should be marked as Pending.
	 * @var string
	 * @since 0.1.0
	 */
	public const MARK_PENDING = 'pending';

	/**
	 * If the comment should be marked as approved.
	 * @var string
	 * @since 0.1.0
	 */
	public const MARK_APPROVED = 'approved';

	/**
	 * If the comment should be marked as spam
	 * @var string
	 * @since 0.1.0
	 */
	public const MARK_SPAM = 'spam';

	/**
	 * If the comment should be marked as trashed
	 * @var string
	 * @since 0.1.0
	 */
	public const MARK_TRASH = 'trash';

	/**
	 * Holds the rules ID.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	public $rule_id;

	/**
	 * If failed validation.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	public $response = self::MARK_PENDING;

		/**
	 * Validate the commenters name.
	 *
	 * @var bool
	 * @since 0.1.0
	 */
	public $author = false;

	/**
	 * Validate the commenters emails
	 *
	 * @var bool
	 * @since 0.1.0
	 */
	public $email = false;

	/**
	 * Validate the commenters URL
	 *
	 * @var bool
	 * @since 0.1.0
	 */
	public $url = false;

	/**
	 * Validate the commenters device agent.
	 *
	 * @var bool
	 * @since 0.1.0
	 */
	public $agent = false;

	/**
	 * Validate the commenters IP Address.
	 *
	 * @var bool
	 * @since 0.1.0
	 */
	public $ip_address = false;

	/**
	 * Validate the Comments content.
	 *
	 * @var bool
	 * @since 0.1.0
	 */
	public $content = false;

	final public function __construct( string $response ) {
		$this->response = $response;
		$this->rule_id  = wp_generate_uuid4();
	}

	/**
	 * Returns an array of all field keys.
	 *
	 * @return array<int, string>
	 */
	public function get_fields(): array {
		return array( 'author', 'email', 'url', 'agent', 'ip_address', 'content' );
	}

	/**
	 * Validates the comment against the rule.
	 *
	 * @param Comment $comment
	 * @return bool
	 * @since 0.1.0
	 */
	abstract public function is_valid_comment( Comment $comment): bool;

	/**
	 * Returns the result if fails validation
	 *
	 * @return string|int
	 * @since 0.1.0
	 */
	final public function get_failed_response() {
		switch ( $this->response ) {
			case self::MARK_PENDING:
				return 0;

			case self::MARK_SPAM:
			case self::MARK_TRASH:
				return $this->response;

			default:
				return 1;
		}
	}

	/**
	 * Returns the rule type.
	 *
	 * @return string
	 */
	abstract public function get_type(): string;

}
