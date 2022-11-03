<?php
/**
 * Plugin Name:     Team51 Advanced Comment Moderation
 * Plugin URI:      https://github.com/a8cteam51/advanced-comment-moderation
 * Description:     Allows for the creation of more advanced comment moderation rules.
 * Author:          Team 51 (Glynn Quelch)
 * Author URI:      https://github.com/a8cteam51
 * Text Domain:     team51-advanced-comment-moderation
 * Version:         1.0.0
 *
 * @package         Team51_Advanced_Comment_Moderation
 */

use Team51\Advanced_Plugin_Moderation\Admin\Hook_Loader;
use Team51\Advanced_Plugin_Moderation\Rule\Rule_Repository;

// Your code starts here.

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	// Include the autoloder.
	require_once __DIR__ . '/vendor/autoload.php';

	// Registers all Hooks.
	Hook_Loader::init();
}

