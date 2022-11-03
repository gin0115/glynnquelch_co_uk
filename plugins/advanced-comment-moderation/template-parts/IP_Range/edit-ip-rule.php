<?php

/**
 * The template for adding/editing IP Range Rules.
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
				data-acm_rule_type="IP_Range_Rule"
				data-acm_rule_id="<?php echo esc_html( $rule_id ); ?>">
				<?php echo Translation::settings_view_button( 'save' ); ?>
			</button>
			<button class="button action acm_action" data-acm_action="<?php echo esc_html( Settings_Ajax_Handler::CANCEL_RULE_ACTION ); ?>">
				<?php echo Translation::settings_view_button( 'cancel' ); ?>
			</button>
		</div>

	</div>
	<div class="acm_card__body">
		<div class="acm_rule_description">
			<p><?php echo Translation::rule_description( 'ip_range' ); ?></p>
		</div>
		<div class="acm_new_rule ip_range">
			<label for="">
				<p><?php echo Translation::rule_input_labels( 'ip_start' ); ?></p>
				<input type="text" 
					minlength="7" maxlength="15" size="15"
					pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$"
					id="rule_start_ip"
					name="rule_start_ip"
					value="<?php echo esc_html( $rule ? $rule->start_ip : '' ); ?>"
					placeholder="127.2.6.xx"
					>
			</label>
			<label for="">
				<p><?php echo Translation::rule_input_labels( 'ip_start' ); ?></p>
				<input type="text" 
					minlength="7" maxlength="15" size="15"
					pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$"
					id="rule_end_ip"
					name="rule_end_ip"
					value="<?php echo esc_html( $rule ? $rule->end_ip : '' ); ?>"
					placeholder="127.2.6.xx"
					>
			</label>
			<?php echo ( new Template_Helper() )->generate_rule_response_dropdown( $selected ?? '' ); ?>
		</div>
	</div>
</div>
