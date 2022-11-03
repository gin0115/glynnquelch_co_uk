<?php

/**
 * Handles the settings page.
 *
 * @package Team51_Advanced_Comment_Moderation\Admin
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 * @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, strings escaped in helper class.
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Admin;

use Team51\Advanced_Plugin_Moderation\Helper\Translation;
use Team51\Advanced_Plugin_Moderation\Rule\Rule_Repository;
use Team51\Advanced_Plugin_Moderation\Helper\Template_Helper;

class Settings_Page {

	/**
	 * Rule data store.
	 *
	 * @var Rule_Repository
	 */
	protected $rule_repository;

	/**
	 * Helper for generating the settings template.
	 *
	 * @var Template_Helper
	 */
	protected $template_helper;

	public function __construct(
		Rule_Repository $rule_repository,
		Template_Helper $template_helper
	) {
		$this->rule_repository = $rule_repository;
		$this->template_helper = $template_helper;
	}

	/**
	 * Registers the options page
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function option_page(): void {
		add_options_page(
			Translation::options_page( 'page_title' ),
			Translation::options_page( 'menu_title' ),
			'manage_options',
			'adv_comment_moderation',
			array( $this, 'render_page_content' )
		);
	}

	/**
	 * Checks if the load more rules button should be shown.
	 *
	 * @return bool
	 */
	public function show_load_more(): bool {
		$count    = $this->rule_repository->total_rules();
		$per_page = $this->template_helper::RULES_PER_PAGE;

		return $count === 0
			? false
			: $count > $per_page;
	}

	/**
	 * Renders the page content.
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function render_page_content() {
		?>
<div id="acm_main" class="wrap">

	<h1> <?php echo Translation::options_page( 'page_title' ); ?> </h1>
	<div id="acm_notification"></div>
	<div id="acm_partial"></div>
	<div id="acm_loading"><span class="spinner is-active"></span><?php echo Translation::assorted( 'loading' ); ?></div>


	<div id="acm_rules" class="acm_card full-width">
		<div id="acm_loading_overlay"></div>
		<div class="acm_card__header">
			<h2><?php echo Translation::options_page( 'rule_list_title' ); ?></h2>
			<div>
				<label for="bulk-action-selector-top"></label><select
					name="action" id="bulk-action-selector-top">
					<option value="-1"><?php echo Translation::options_page( 'select_placeholder' ); ?></option>
					<option value="regex"><?php echo Translation::rule_name( 'regex' ); ?></option>
					<option value="ip_range"><?php echo Translation::rule_name( 'ip_range' ); ?></option>
					<option value="wildcard"><?php echo Translation::rule_name( 'wildcard' ); ?></option>
					<option value="cidr"><?php echo Translation::rule_name( 'cidr' ); ?></option>
				</select>
				<button class="button action acm_action" data-acm_action="new"><?php echo Translation::settings_view_button( 'add_rule' ); ?></button>
				<button class="button action acm_action" data-acm_action="clear_all"><?php echo Translation::settings_view_button( 'clear_rules' ); ?></button>
			</div>
		</div>

		<?php
			// Show rule filters
			$this->template_helper->rule_filters();
		?>

		<div class="acm_card__body">
			<?php foreach ( $this->rule_repository->get_all_paginated() as $rule ) : ?>
				<?php $this->template_helper->print_rule_row( $rule ); ?>
			<?php endforeach; ?>
		</div>
		<div id="acm_card__more">
			<?php if ( $this->show_load_more() ) : ?>
				<button class="button action acm_action" data-acm_action="load_more"><?php echo Translation::settings_view_button( 'show_more' ); ?></button>
			<?php endif; ?>
		</div>
	</div>
</div>
		<?php
	}
}
