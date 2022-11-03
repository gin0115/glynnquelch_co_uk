<?php

/**
 * Holds all translated values.
 *
 * @package Team51_Advanced_Comment_Moderation\Admin
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Helper;

class Translation {

	/**
	 * Returns the translated name of a rule.
	 * Escapes the value before returning.
	 *
	 * @param string $rule
	 * @return string
	 */
	public static function rule_name( string $rule ): string {
		switch ( $rule ) {
			case 'ip_range':
				$label = _x( 'IP Range', 'IP Range Rule name', 'team51-advanced-comment-moderation' );
				break;

			case 'regex':
				$label = _x( 'Regex', 'Regex Rule name', 'team51-advanced-comment-moderation' );
				break;

			case 'wildcard':
				$label = _x( 'Wildcard', 'Wildcard Rule name', 'team51-advanced-comment-moderation' );
				break;

			case 'cidr':
				$label = _x( 'CIDR', 'CIDR Rule name', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown Rule', 'Unknown rule name', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}

	/**
	 * Returns the translated description for all rule types
	 *
	 * @param string $rule
	 * @return string
	 */
	public static function rule_description( string $rule ): string {
		switch ( $rule ) {
			case 'ip_range':
				$label = _x( 'You can filter all IP addresses between a range. Entering <code>1.2.3.4</code> and <code>1.2.3.7</code> will block the following IP addresses<code>1.2.3.4</code> &<code>1.2.3.5</code> &<code>1.2.3.6</code> &<code>1.2.3.7</code>. Please note only the final nodes are checked and the first three much match with the starting and ending addresses.', 'IP Range Rule description', 'team51-advanced-comment-moderation' );
				break;

			case 'regex':
				$label = _x( 'You can enter and valid Regular Expression to be used for the filtering of comments. These must be entered as valid REGEX expression, with delimiters both opening and closing the rule<code>/ca[kf]e/</code>', 'Regex Rule description', 'team51-advanced-comment-moderation' );
				break;

			case 'wildcard':
				$label = _x( 'You can use shell style wildcards such as<code>*</code> and<code>?</code> in your expressions to be excluded such as<code>*@email.com</code> would catch all email addresses with the<code>@email.com</code> domain.', 'Wildcard Rule description', 'team51-advanced-comment-moderation' );
				break;

			case 'cidr':
				$label = _x( 'You can filter entire IP ranges using this rule. A valid CIDR expression will allow the blocks of entire ranges of IP addresses<code>10.0.0.0/24</code> would cover<code>10.0.0.0</code> and <code>10.0.0.255</code>', 'CIDR Rule description', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown Rule', 'Unknown rule description', 'team51-advanced-comment-moderation' );
				break;
		}

		return wp_kses( $label, array( 'code' => array() ) );
	}

	/**
	 * General labels
	 *
	 * @param string $key
	 * @return string
	 */
	public static function assorted( string $key ): string {

		switch ( $key ) {
			case 'new':
				$label = _x( 'New', 'Label for a "NEW" rule.', 'team51-advanced-comment-moderation' );
				break;

			case 'edit':
				$label = _x( 'Edit', 'Label for a "EDIT" rule.', 'team51-advanced-comment-moderation' );
				break;

			case 'loading':
				$label = _x( 'Loading', 'Loading overlay label', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown', 'Unknown label (assorted)', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}

	/**
	 * All options page translations.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function options_page( string $key ): string {

		switch ( $key ) {
			case 'page_title':
				$label = _x( 'Advanced Comment Moderation', 'Settings page title', 'team51-advanced-comment-moderation' );
				break;

			case 'menu_title':
				$label = _x( 'Comment Moderation', 'Settings page title', 'team51-advanced-comment-moderation' );
				break;

			case 'select_placeholder':
				$label = _x( 'Select Rule Type', 'Placeholder value from rule type dropdown.', 'team51-advanced-comment-moderation' );
				break;

			case 'rule_list_title':
				$label = _x( 'Rules', 'Title for the list of rules.', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown', 'Unknown label (assorted)', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}

	/**
	 * Returns the translated comment field labels.
	 * Escapes the value before returning.
	 *
	 * @param string $field
	 * @return string
	 */
	public static function comment_field_label( string $field ): string {
		switch ( $field ) {
			case 'author':
				$label = _x( 'Name', 'Author name field label for a comment.', 'team51-advanced-comment-moderation' );
				break;

			case 'email':
				$label = _x( 'Email', 'Author email field label for a comment.', 'team51-advanced-comment-moderation' );
				break;

			case 'url':
				$label = _x( 'URL', 'Author url field label for a comment.', 'team51-advanced-comment-moderation' );
				break;

			case 'agent':
				$label = _x( 'User Agent', 'Authors browser (user agent) field label for a comment.', 'team51-advanced-comment-moderation' );
				break;

			case 'ip_address':
				$label = _x( 'IP Address', 'Authors IP Address field label for a comment.', 'team51-advanced-comment-moderation' );
				break;

			case 'content':
				$label = _x( 'Comment Content', 'The Content field label for a comment.', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Invalid Field', 'Invalid comment field for a comment.', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}

	/**
	 * Settings view button labels
	 * Escapes the value before returning.
	 *
	 * @param string $label
	 * @return string
	 */
	public static function settings_view_button( string $label ): string {
		switch ( $label ) {
			case 'add_rule':
				$label = _x( 'Add Rule', 'Add Rule button button label', 'team51-advanced-comment-moderation' );
				break;

			case 'clear_rules':
				$label = _x( 'Clear All Rules', 'Clear All Rules button label', 'team51-advanced-comment-moderation' );
				break;

			case 'save':
				$label = _x( 'Save', 'Save new rule button label', 'team51-advanced-comment-moderation' );
				break;

			case 'update':
				$label = _x( 'Update', 'Update new rule button label', 'team51-advanced-comment-moderation' );
				break;

			case 'cancel':
				$label = _x( 'Cancel', 'Cancel creating or updating rule button label', 'team51-advanced-comment-moderation' );
				break;

			case 'edit':
				$label = _x( 'Edit', 'Edit a rule button label', 'team51-advanced-comment-moderation' );
				break;

			case 'delete':
				$label = _x( 'Delete', 'Delete a rule button label', 'team51-advanced-comment-moderation' );
				break;

			case 'show_more':
				$label = _x( 'Show More Rules', 'Show more rules button label', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown', 'Unknown buttons label.', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}

	/**
	 * Response flag labels
	 * Escapes the value before returning.
	 *
	 * @param string $response
	 * @return string
	 */
	public static function response_label( string $response ): string {
		switch ( $response ) {
			case 'pending':
				$label = _x( 'Pending', 'Pending response flag on rule list.', 'team51-advanced-comment-moderation' );
				break;

			case 'trash':
				$label = _x( 'Trash', 'Trash response flag on rule list.', 'team51-advanced-comment-moderation' );
				break;

			case 'spam':
				$label = _x( 'Spam', 'Spam response flag on rule list.', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown', 'Unknown response flag on rule list', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}

	/**
	 * All rule input labels.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function rule_input_labels( string $key ): string {
		switch ( $key ) {
			case 'regex_expression':
				$label = _x( 'Expression (including delimiters)', 'Label for the regex expression input', 'team51-advanced-comment-moderation' );
				break;

			case 'ip_start':
				$label = _x( 'Starting IP Address', 'Start of IP range', 'team51-advanced-comment-moderation' );
				break;

			case 'ip_end':
				$label = _x( 'Ending IP Address', 'End of IP Range', 'team51-advanced-comment-moderation' );
				break;

			case 'wildcard_expression':
				$label = _x( 'Expression with optional wildcards', 'Label for the wildcard expression input', 'team51-advanced-comment-moderation' );
				break;

			case 'cidr_expression':
				$label = _x( 'CIDR Expression', 'Label for the CIDR expression input', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown', 'Unknown response flag on rule list', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}

	/**
	 * All filter labels
	 *
	 * @since 1.0.1
	 * @param string $key
	 * @return string
	 */
	public static function filter_labels( string $key ): string {
		switch ( $key ) {
			case 'showing_template':
				// translators: %1$d is replaced with total count of displayed rules, %2$d is replaced with the total of all rules.
				$label = _x( '<p>Showing <span id="acm_filter__showing_current">%1$d</span> of <span id="acm_filter__showing_total">%2$d</span> rules.</p>', 'Sprintf template for the shown x of y rules.', 'team51-advanced-comment-moderation' ); // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
				break;

			case 'toggle_filters':
				$label = _x( 'Show/Hide Filters', 'Toggle filters button label', 'team51-advanced-comment-moderation' );
				break;

			case 'filter_title':
				$label = _x( 'Filter Rules', 'Filters H2 header', 'team51-advanced-comment-moderation' );
				break;

			case 'search':
				$label = _x( 'Search', 'Label for the search field in rule filters', 'team51-advanced-comment-moderation' );
				break;

			case 'rule_type':
				$label = _x( 'Rule Type', 'Label for the rule types checkboxes in rule filters', 'team51-advanced-comment-moderation' );
				break;

			case 'fields':
				$label = _x( 'Fields', 'Label for the fields checkboxes in rule filters', 'team51-advanced-comment-moderation' );
				break;

			case 'response':
				$label = _x( 'Response', 'Label for the response checkboxes in rule filters', 'team51-advanced-comment-moderation' );
				break;

			case 'apply_filter_button':
				$label = _x( 'Apply Filters', 'Label for the apply filters button', 'team51-advanced-comment-moderation' );
				break;

			case 'clear_filter_button':
				$label = _x( 'Clear Filters', 'Label for the clear filters button', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown', 'Unknown label in rule filters', 'team51-advanced-comment-moderation' );
				break;
		}

		return wp_kses(
			$label,
			array(
				'p'    => array(),
				'span' => array( 'id' => array() ),
			)
		);
	}

	/**
	 * Ajax notification messages.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function ajax_notifications( string $key ): string {
		switch ( $key ) {
			case 'invalid_payload':
				$label = _x( 'Invalid payload', 'Response when the payload for upserting a rule is not fully populated with required values', 'team51-advanced-comment-moderation' );
				break;

			case 'rules_filtered':
				$label = _x( 'Rules filtered', 'Notification to denote rule filter applied', 'team51-advanced-comment-moderation' );
				break;

			case 'rule_filters_reset':
				$label = _x( 'Rule filtered reset', 'Notification to denote rule filters reset', 'team51-advanced-comment-moderation' );
				break;

			case 'unknown_operation':
				$label = _x( 'Error processing request.', 'Error returned when no operation passed to ajax', 'team51-advanced-comment-moderation' );
				break;

			case 'failed_validation':
				$label = _x( 'Something went wrong, please reload the page and try again.', 'Unauthorised use determined by failing nonce check.', 'team51-advanced-comment-moderation' );
				break;

			case 'no_rule_selected':
				$label = _x( 'No rule type selected', 'Notification returned if trying to add a new rule, but no rule type selected.', 'team51-advanced-comment-moderation' );
				break;

			case 'rule_not_found':
				$label = _x( 'Rule no longer exists.', 'Response returned when a rule being updated, no longer exists', 'team51-advanced-comment-moderation' );
				break;

			case 'rule_deleted':
				$label = _x( 'Rule Deleted', 'Notification for a rule deleted.', 'team51-advanced-comment-moderation' );
				break;

			case 'all_rules_deleted':
				$label = _x( 'All rules cleared', 'Response when all rules are cleared.', 'team51-advanced-comment-moderation' );
				break;

			case 'rule_created':
				$label = _x( 'Rule created', 'Response when new rules is created.', 'team51-advanced-comment-moderation' );
				break;

			case 'rule_updated':
				$label = _x( 'Rule updated', 'Response when new rules is updated.', 'team51-advanced-comment-moderation' );
				break;

			default:
				$label = _x( 'Unknown', 'Unknown response flag on rule list', 'team51-advanced-comment-moderation' );
				break;
		}

		return esc_html( $label );
	}
}
