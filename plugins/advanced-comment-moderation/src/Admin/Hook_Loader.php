<?php

/**
 * Primary admin hook loader.
 *
 * @package Team51_Advanced_Comment_Moderation\Admin
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Admin;

use Team51\Advanced_Plugin_Moderation\Rule\Rule_Factory;
use Team51\Advanced_Plugin_Moderation\Admin\Asset_Loader;
use Team51\Advanced_Plugin_Moderation\Rule\Rule_Repository;
use Team51\Advanced_Plugin_Moderation\Helper\Template_Helper;
use Team51\Advanced_Plugin_Moderation\Moderation\Comment_Moderator;

class Hook_Loader {

	/**
	 * Handles loading all assets
	 *
	 * @var Asset_Loader
	 */
	protected $asset_loader;

	/**
	 * Static loader.
	 *
	 * @return void
	 */
	public static function init(): void {
		self::settings_page_init();
		self::register_assets();
		self::register_ajax();
		self::validate_comments();
	}

	/**
	 * Registers all settings page hooks.
	 *
	 * @return void
	 * @since 0.1.0
	 */
	protected static function settings_page_init(): void {
		$settings_page = new Settings_Page(
			new Rule_Repository(),
			new Template_Helper()
		);
		add_action( 'admin_menu', array( $settings_page, 'option_page' ), 20 );
	}

	/**
	 * Register all admin scripts and styles
	 *
	 * @return void
	 * @since 0.1.0
	 */
	protected static function register_assets(): void {
		$asset_loader = new Asset_Loader();
		add_action( 'admin_enqueue_scripts', array( $asset_loader, 'enqueue_assets' ) );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected static function register_ajax(): void {
		$ajax_handler = new Settings_Ajax_Handler(
			new Rule_Factory(),
			new Rule_Repository()
		);
		add_action( 'wp_ajax_' . $ajax_handler::AJAX_ACTION, array( $ajax_handler, 'ajax_callback' ) );
	}

	protected static function validate_comments(): void {
		$moderator = new Comment_Moderator();
		add_filter( 'pre_comment_approved', array( $moderator, 'validate_comment' ), 10, 2 );
	}
}
