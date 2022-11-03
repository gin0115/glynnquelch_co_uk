<?php

/**
 * Includes all assets for WP-Admin
 *
 * @package Team51_Advanced_Comment_Moderation\Admin
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Admin;

use Team51\Advanced_Plugin_Moderation\Admin\Settings_Ajax_Handler;

class Asset_Loader {

	/**
	 * Holds the path to the asset loader
	 *
	 * @var string
	 */
	protected $asset_url;

	/**
	 * Holds the current plugin version based on the plugin file.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Construct Asset_Loader
	 */
	public function __construct() {
		$this->asset_url = \plugins_url( '/assets', \dirname( __DIR__, 1 ) );
		$this->set_plugin_version();
	}

	/**
	 * Sets the plugin version.
	 *
	 * @return void
	 */
	public function set_plugin_version(): void {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_data = get_plugin_data( \dirname( __DIR__, 2 ) . '/team51-advanced-comment-moderation.php' );

		// If version is set in plugin data, set to $version.
		$this->version = \array_key_exists( 'Version', $plugin_data )
			? $plugin_data['Version']
			: '1.0.0';

	}

	/**
	 * Get holds the path to the asset loader
	 *
	 * @return string
	 */
	public function get_asset_url(): string {
		return $this->asset_url;
	}

	/**
	 * Get holds the current plugin version based on the plugin file.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return $this->version;
	}

	/**
	 * Enqueue the admin assets.
	 *
	 * @param string $hook_suffix The admin page being loaded.
	 * @return void
	 */
	public function enqueue_assets( string $hook_suffix ) {
		if ( 'settings_page_adv_comment_moderation' === $hook_suffix ) {

			// Admin settings page styles
			\wp_enqueue_style(
				'adv_comment_moderation',
				$this->get_asset_url() . '/admin/settings.css',
				array(),
				$this->get_version()
			);

			// Admin settings page scripts
			\wp_register_script(
				'adv_comment_moderation_scripts',
				$this->get_asset_url() . '/admin/settings.js',
				array( 'jquery' ),
				$this->get_version(),
				false
			);

			\wp_localize_script(
				'adv_comment_moderation_scripts',
				'advCommentModeration',
				array(
					'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
					'ajaxAction' => Settings_Ajax_Handler::AJAX_ACTION,
					'ajaxNonce'  => \wp_create_nonce( Settings_Ajax_Handler::NONCE_KEY ),
				)
			);

			\wp_enqueue_script( 'adv_comment_moderation_scripts' );
		}
	}
}
