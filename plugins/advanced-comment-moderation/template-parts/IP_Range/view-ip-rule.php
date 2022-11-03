<?php

/**
 * The IP Range Rule template for the rule list
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 * @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, strings escaped in helper class.
 */

use Team51\Advanced_Plugin_Moderation\Helper\Translation;
use Team51\Advanced_Plugin_Moderation\Helper\Template_Helper;

?>

<div class="acm_rule__row">
	<div class="acm_rule__type">
		<h3><?php echo Translation::rule_name( 'ip_range' ); ?></h3>
		<div class="acm_rule__response <?php echo esc_html( $response ); ?>">
			<p><span class='dashicons <?php echo esc_html( Template_Helper::rule_row_response_dashicon( $response ) ); ?>''></span>
				<?php echo Translation::response_label( $response ); ?></p>
		</div>
	</div>

	<div class="acm_rule__details">
		<p class="acm_rule__description">
			<?php
			printf(
				/* translators: %1$s: IP Range staring IP address, %1$s: IP Range ending IP address */
				__( 'IP Range Between %1$s and %2$s', 'team51-advanced-comment-moderation' ),
				$start_ip,
				$end_ip
			);
			?>
				</p>
		<div class="acm_rule__footer">
			<ul class="acm_rule__fields">
				<li class="not_selected"><span class="dashicons dashicons-no-alt"></span> <?php echo Translation::comment_field_label( 'author' ); ?></li>
				<li class="not_selected"><span class="dashicons dashicons-no-alt"></span> <?php echo Translation::comment_field_label( 'email' ); ?></li>
				<li class="not_selected"><span class="dashicons dashicons-no-alt"></span> <?php echo Translation::comment_field_label( 'url' ); ?></li>
				<li class="not_selected"><span class="dashicons dashicons-no-alt"></span> <?php echo Translation::comment_field_label( 'agent' ); ?></li>
				<li class="selected"><span class="dashicons dashicons-saved"></span> <?php echo Translation::comment_field_label( 'ip_address' ); ?></li>
				<li class="not_selected"><span class="dashicons dashicons-no-alt"></span> <?php echo Translation::comment_field_label( 'content' ); ?></li>
			</ul>
			<div class="acm_rule__actions">
				<button class="button button-primary acm_action" data-acm_action="edit_rule" data-acm_rule_type="ip_range" data-acm_rule_id="<?php echo esc_html( $rule_id ); ?>" ><?php echo Translation::settings_view_button( 'edit' ); ?></button>
				<button class="button button-primary acm_action" data-acm_action="delete_rule" data-acm_rule_type="ip_range" data-acm_rule_id="<?php echo esc_html( $rule_id ); ?>"><?php echo Translation::settings_view_button( 'delete' ); ?></button>
			</div>
		</div>
	</div>
</div>
