<?php

/**
 * The template for adding/editing Regex Rule
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 * @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, strings escaped in helper class.
 */

use Team51\Advanced_Plugin_Moderation\Helper\Translation;
use Team51\Advanced_Plugin_Moderation\Helper\Template_Helper;
use Team51\Advanced_Plugin_Moderation\Admin\Settings_Ajax_Handler;

?>

<div id="<?php echo esc_html( $action_type_id ); ?>" class="acm_card full-width">
	<div class="acm_card__header vertical">
		<input type="hidden" name="acm_rule_id" id="acm_rule_id" value="<?php echo esc_html( $rule_id ); ?>">
		<h2><?php echo esc_html( $title ); ?></h2>
		<div>
			<button class="button button-primary action acm_action"
				data-acm_action="<?php echo esc_html( Settings_Ajax_Handler::UPSERT_RULE_ACTION ); ?>"
				data-acm_rule_type="Wildcard_Rule"
				data-acm_rule_id="<?php echo esc_html( $rule_id ); ?>">
				<?php echo Translation::settings_view_button( 'save' ); ?>
			</button>
			<button class="button action acm_action"
				data-acm_action="<?php echo esc_html( Settings_Ajax_Handler::CANCEL_RULE_ACTION ); ?>">
				<?php echo Translation::settings_view_button( 'cancel' ); ?>
			</button>
		</div>
	</div>

	<div class="acm_card__body">
		<div class="acm_rule_description">
			<p><?php echo Translation::rule_description( 'wildcard' ); ?></p>
		</div>
		<div class="acm_new_rule wildcard">
			<div class="single_input">
				<label for="wildcard_expression">
					<p><?php echo Translation::rule_input_labels( 'wildcard_expression' ); ?></p>
					<input type="text" name="wildcard_expression" id="wildcard_expression"
						value="<?php echo $rule->expression; ?>">
				</label>
				<?php echo ( new Template_Helper() )->generate_rule_response_dropdown( $selected ?? '' ); ?>
			</div>
		</div>
	</div>
	<div class="acm_rule__footer">
		<?php echo ( new Template_Helper() )->generate_edit_rule_field_list( $rule ); ?>
		<div class="acm_rule__actions"></div>
	</div>
