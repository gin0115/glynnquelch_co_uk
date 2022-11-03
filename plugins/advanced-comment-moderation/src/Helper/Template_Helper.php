<?php

/**
 * Helper class for creating parts of the settings page.
 *
 * @package Team51_Advanced_Comment_Moderation\Admin
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Helper;

use Team51\Advanced_Plugin_Moderation\Rule\CIDR_Rule;
use Team51\Advanced_Plugin_Moderation\Rule\IP_Range_Rule;
use Team51\Advanced_Plugin_Moderation\Rule\Regex_Rule;
use Team51\Advanced_Plugin_Moderation\Rule\Rule;
use Team51\Advanced_Plugin_Moderation\Rule\Rule_Repository;
use Team51\Advanced_Plugin_Moderation\Rule\Wildcard_Rule;

class Template_Helper {

	/**
	 * Number of rules to show per "page".
	 * @since 1.0.1
	 */
	public const RULES_PER_PAGE = 25;

	/**
	 * The path to the template parts.
	 *
	 * @var string
	 */
	protected $template_parts_path;

	public function __construct() {
		$this->template_parts_path = dirname( __DIR__, 2 ) . '/template-parts';
	}

	/**
	 * Returns the responses dashicon based on response type.
	 *
	 * @param string $response
	 * @return string
	 */
	public static function rule_row_response_dashicon( string $response ): string {
		switch ( $response ) {
			case 'trash':
				return 'dashicons-trash';

			case 'spam':
				return 'dashicons-warning';

			default: // used for pending.
				return 'dashicons-backup';
		}
	}

	/**
	 * Prints a rules row, based on its type.
	 *
	 * @param Rule $rule
	 * @return void
	 */
	public function print_rule_row( Rule $rule ): void {
		switch ( get_class( $rule ) ) {
			case IP_Range_Rule::class:
				$this->render_template_part(
					$this->template_parts_path . '/IP_Range/view-ip-rule.php',
					(array) $rule
				);
				break;

			case Regex_Rule::class:
				$this->render_template_part(
					$this->template_parts_path . '/Regex/view-regex-rule.php',
					(array) $rule
				);
				break;

			case Wildcard_Rule::class:
				$this->render_template_part(
					$this->template_parts_path . '/Wildcard/view-wildcard-rule.php',
					(array) $rule
				);
				break;

			case CIDR_Rule::class:
				$this->render_template_part(
					$this->template_parts_path . '/CIDR/view-cidr-rule.php',
					(array) $rule
				);
				break;

			default:
				# code...
				break;
		}
	}

	/**
	 * Creates a rule row and returns the string representation.
	 *
	 * @param Rule $rule
	 * @return string
	 */
	public function create_rule_row( Rule $rule ): string {
		ob_start();
		$this->print_rule_row( $rule ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}

	/**
	 * Renders a template partial.
	 *
	 * @param string $template_path
	 * @param array<string, mixed> $args
	 * @return void
	 */
	public function render_template_part( string $template_path, array $args ): void {
		extract( $args ); //phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		include $template_path;
	}

	/**
	 * Generates the HTML used to the render the new rule partial.
	 *
	 * @param string $type
	 * @return string
	 */
	public function generate_new_rule_partial( string $type ) : string {
		ob_start();
		$this->render_template_part(
			$this->get_partial_file_path( $type ),
			array(
				'action_type_id' => 'acm_create',
				'rule_id'        => '',
				'selected'       => '',
				'rule'           => null,
				'title'          => sprintf( '%s %s', Translation::assorted( 'new' ), Translation::rule_name( $type ) ),
			)
		); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}

	/**
	 * Generates the HTML used to render the edit rule partial.
	 *
	 * @param Rule $rule
	 * @return string
	 */
	public function generate_edit_rule_partial( Rule $rule ): string {
		ob_start();
		$this->render_template_part(
			$this->get_partial_file_path( $rule->get_type() ),
			array(
				'action_type_id' => 'acm_edit',
				'rule_id'        => $rule->rule_id,
				'selected'       => $rule->response,
				'rule'           => $rule,
				'title'          => sprintf( '%s %s', Translation::assorted( 'edit' ), Translation::rule_name( $rule->get_type() ) ),
			)
		); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}

	/**
	 * Returns the path to the partial for upserting a rule based on its type.
	 *
	 * @param string $type
	 * @return string
	 */
	protected function get_partial_file_path( string $type ): string {
		switch ( $type ) {
			case 'ip_range':
				return $this->template_parts_path . '/IP_Range/edit-ip-rule.php';

			case 'regex':
				return $this->template_parts_path . '/Regex/edit-regex-rule.php';

			case 'wildcard':
				return $this->template_parts_path . '/Wildcard/edit-wildcard-rule.php';

			case 'cidr':
				return $this->template_parts_path . '/CIDR/edit-cidr-rule.php';

			default:
				return '';
		}
	}

	/**
	 * Generates the drop down used for settings a rules response.
	 *
	 * @param string $selected
	 * @return string
	 */
	public function generate_rule_response_dropdown( string $selected = '' ): string {
		ob_start();
		$this->render_template_part(
			$this->template_parts_path . '/rule-response-dropdown.php',
			array(
				'selected' => $selected,
			)
		); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}

	/**
	 * Generates the HTML used for add/edit rule field list.
	 *
	 * @param Rule $rule
	 * @return string
	 */
	public function generate_edit_rule_field_list( ?Rule $rule ): string {
		ob_start();
		$this->render_template_part(
			$this->template_parts_path . '/edit-field-list.php',
			array(
				'author'     => ! is_null( $rule ) ? $rule->author : false,
				'email'      => ! is_null( $rule ) ? $rule->email : false,
				'url'        => ! is_null( $rule ) ? $rule->url : false,
				'agent'      => ! is_null( $rule ) ? $rule->agent : false,
				'ip_address' => ! is_null( $rule ) ? $rule->ip_address : false,
				'content'    => ! is_null( $rule ) ? $rule->content : false,
			)
		); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$output = ob_get_contents();
		ob_end_clean();
		return $output ?: '';
	}

	/**
	 * Renders the rule filters.
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function rule_filters(): void {
		?>
		<div id="acm_result_count">
			<?php printf( Translation::filter_labels( 'showing_template' ), \absint( self::RULES_PER_PAGE ), \absint( $this->get_rule_count() ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
			<button id="acm_toggle_filters" class="button button-primary"><?php echo Translation::filter_labels( 'toggle_filters' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></div>
		<div id="acm_filter" style="display:none">
			<h2><?php echo Translation::filter_labels( 'filter_title' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<div id="acm_filter_form">
				<input type="hidden" name="acm_filter__limit" id="acm_filter__limit" value=<?php echo \absint( self::RULES_PER_PAGE ); ?>>
				<input type="hidden" name="acm_filter__offset" id="acm_filter__offset" value="0">
				<fieldset id="acm_filters_field__term">
					<legend><?php echo Translation::filter_labels( 'search' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></legend>
					<input type="search" name="acm_filter[search]" id="acm_filter__search">
				</fieldset>
				<fieldset id="acm_filters_field__types">
					<legend><?php echo Translation::filter_labels( 'rule_type' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></legend>
					<label for="acm_filter_type__regex">
						<input type="checkbox" name="acm_filter_type[regex]" id="acm_filter_type__regex">
						<?php echo Translation::rule_name( 'regex' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_type__ip_range">
						<input type="checkbox" name="acm_filter_type[ip_range]" id="acm_filter_type__ip_range">
						<?php echo Translation::rule_name( 'ip_range' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_type__wildcard">
						<input type="checkbox" name="acm_filter_type[wildcard]" id="acm_filter_type__wildcard">
						<?php echo Translation::rule_name( 'wildcard' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_type__cidr">
						<input type="checkbox" name="acm_filter_type[cidr]" id="acm_filter_type__cidr">
						<?php echo Translation::rule_name( 'cidr' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
				</fieldset>

				<fieldset id="acm_filters_field__fields">
					<legend><?php echo Translation::filter_labels( 'fields' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></legend>
					<label for="acm_filter_fields__author">
						<input type="checkbox" name="acm_filter_fields[author]" id="acm_filter_fields__author">
						<?php echo Translation::comment_field_label( 'author' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_fields__email">
						<input type="checkbox" name="acm_filter_fields[email]" id="acm_filter_fields__email">
						<?php echo Translation::comment_field_label( 'email' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_fields__url">
						<input type="checkbox" name="acm_filter_fields[url]" id="acm_filter_fields__url">
						<?php echo Translation::comment_field_label( 'url' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_fields__ip_address">
						<input type="checkbox" name="acm_filter_fields[ip_address]" id="acm_filter_fields__ip_address">
						<?php echo Translation::comment_field_label( 'ip_address' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_fields__agent">
						<input type="checkbox" name="acm_filter_fields[agent]" id="acm_filter_fields__agent">
						<?php echo Translation::comment_field_label( 'agent' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_fields__content">
						<input type="checkbox" name="acm_filter_fields[content]" id="acm_filter_fields__content">
						<?php echo Translation::comment_field_label( 'content' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
				</fieldset>

				<fieldset id="acm_filters_field__responses">
					<legend><?php echo Translation::filter_labels( 'response' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></legend>
					<label for="acm_filter_response__pending">
						<input type="checkbox" name="acm_filter_response[pending]" id="acm_filter_response__pending">
						<?php echo Translation::response_label( 'pending' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_response__spam">
						<input type="checkbox" name="acm_filter_response[spam]" id="acm_filter_response__spam">
						<?php echo Translation::response_label( 'spam' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
					<label for="acm_filter_response__trash">
						<input type="checkbox" name="acm_filter_response[trash]" id="acm_filter_response__trash">
						<?php echo Translation::response_label( 'trash' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, escaped in translations class. ?>
					</label>
				</fieldset>
				<fieldset>
				</fieldset>
				<fieldset id="acm_filters_field__actions">
					<button class="button action acm_action" data-acm_action="filter"><?php echo Translation::filter_labels( 'apply_filter_button' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<button class="button action acm_action" data-acm_action="reset_filter"><?php echo Translation::filter_labels( 'clear_filter_button' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				</fieldset>
			</div>
		</div>

		<?php
	}


	/**
	 * Gets the total rule count.
	 *
	 * @return int
	 */
	public function get_rule_count(): int {
		return ( new Rule_Repository() )->total_rules();
	}
}
