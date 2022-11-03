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

class Comment {

	/**
	 * The post the comment is made against.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * The id of user if logged in.
	 *
	 * @var int|null
	 */
	public $user_id;

	/**
	 * The commenter IP
	 *
	 * @var string
	 */
	public $ip;

	/**
	 * commenter name.
	 *
	 * @var string|null
	 */
	public $author;

	/**
	 * commenter emails
	 *
	 * @var string|null
	 */
	public $email;

	/**
	 * commenter URL
	 *
	 * @var string|null
	 */
	public $url;

	/**
	 * commenter device agent.
	 *
	 * @var string
	 */
	public $agent;

	/**
	 * Comment content.
	 *
	 * @var string
	 */
	public $content;

}
