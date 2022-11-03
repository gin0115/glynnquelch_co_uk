<?php

/**
 * Ajax handler
 *
 * @package Team51_Advanced_Comment_Moderation\Admin
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Admin;

use Team51\Advanced_Plugin_Moderation\Rule\Rule;
use Team51\Advanced_Plugin_Moderation\Rule\Rule_Factory;
use Team51\Advanced_Plugin_Moderation\Helper\Translation;
use Team51\Advanced_Plugin_Moderation\Rule\Rule_Repository;
use Team51\Advanced_Plugin_Moderation\Helper\Template_Helper;

class Settings_Ajax_Handler {

	/**
	 * Rule management actions.
	 */
	public const NEW_RULE_ACTION    = 'new';
	public const CLEAR_RULES_ACTION = 'clear_all';

	/**
	 * New/Edit rule actions.
	 */
	public const UPSERT_RULE_ACTION = 'upsert';
	public const CANCEL_RULE_ACTION = 'cancel';

	/**
	 * Rule row actions.
	 */
	public const EDIT_RULE_ACTION   = 'edit';
	public const DELETE_RULE_ACTION = 'delete';

	/**
	 * Primary ajax action.
	 */
	public const AJAX_ACTION = 'adv_comment_moderation';

	/**
	 * Ajax action nonce.
	 */
	public const NONCE_KEY = 'adv_comment_moderation';

	/**
	 * Factory for creating rules
	 *
	 * @var Rule_Factory
	 */
	protected $rule_factory;

	/**
	 * Data store for all rule
	 *
	 * @var Rule_Repository
	 */
	protected $rule_repository;

	/**
	 * Template helper
	 *
	 * @var Template_Helper
	 */
	protected $template_helper;

	public function __construct( Rule_Factory $rule_factory, Rule_Repository $rule_repository ) {
		$this->rule_factory    = $rule_factory;
		$this->rule_repository = $rule_repository;
		$this->template_helper = new Template_Helper();
	}

	/**
	 * Primary entry point for all ajax calls.
	 *
	 * @return void
	 */
	public function ajax_callback(): void {

		// Validate the request.
		if ( ! $this->validate_request() ) {
			$this->emit_response(
				$this->generate_response_array(
					Translation::ajax_notifications( 'failed_validation' ),
					true
				)
			);
		}

		// Extract request params.
		$request = $this->extract_params();

		try {
			switch ( $request['operation'] ) {

				// Create/Update rule operations
				case 'upsert':
					$response = $this->upsert_rule( $request );
					break;

				case 'cancel':
				case 'filter':
					$response = $this->generate_response_array( Translation::ajax_notifications( 'rules_filtered' ) );
					break;

				case 'reset_filter':
					$response = $this->generate_response_array( Translation::ajax_notifications( 'rule_filters_reset' ) );
					break;

				// Rule menu operations
				case 'new':
					$response = $this->new_rule( $request );
					break;

				case 'clear_all':
					$response = $this->clear_rules( $request );
					break;

				// Rule Row operations
				case 'edit_rule':
					$response = $this->edit_rule( $request );
					break;

				case 'delete_rule':
					$response = $this->delete_rule( $request );
					break;

				case 'load_more':
					$response = $this->generate_response_array( '' );
					break;

				default:
					$response = $this->generate_response_array(
						Translation::ajax_notifications( 'unknown_operation' ),
						true
					);
					break;
			}

			// Filter and parse rules
			$rules       = $this->maybe_filter_rules( $request );
			$total_count = count( $rules );
			$rules       = $this->maybe_paginate_rules( $rules, $request );

			// Set the count of rules.
			$current_page_total = $this->existing_rule_count( $request ) + count( $rules );

			// Populate the rule rows.
			$response['rules']      = $this->parse_rules( $rules );
			$response['pagination'] = array(
				'showing' => $current_page_total,
				'total'   => $total_count,
				'page'    => $this->existing_rule_count( $request ),
			);

			$this->emit_response( $response, 200 );
		} catch ( \Throwable $th ) {
			$this->emit_response( $this->generate_response_array( $th->getMessage(), true ), 200 );
		}

	}

	/**
	 * Extracts the params from the request.
	 * Sanitizes the values as string using sanitize_text_field().
	 *
	 * @return array{operation:string,rule_type:string,rule_values?:string[],filters?:array{search:string,type:string[],fields:string[],response:string[]}}
	 */
	protected function extract_params(): array {
		// If we have no payload, return an invalid operation.
		if ( ! array_key_exists( 'payload', $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
			return array(
				'operation' => 'ERROR',
				'rule_type' => 'ERROR',
			);
		}

		// Return the request params sanitised as strings.
		return array(
			'operation'   => array_key_exists( 'operation', $_POST['payload'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				? \sanitize_text_field( $_POST['payload']['operation'] )        // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				: 'ERROR',
			'rule_type'   => array_key_exists( 'ruleType', $_POST['payload'] )  // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				? \sanitize_text_field( $_POST['payload']['ruleType'] )         // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				: 'ERROR',
			'rule_values' => array_key_exists( 'ruleValue', $_POST['payload'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				? $_POST['payload']['ruleValue']                                // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				: array(),
			'filters'     => array_key_exists( 'filter', $_POST['payload'] )    // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				? $_POST['payload']['filter']                                   // phpcs:ignore WordPress.Security.NonceVerification.Missing, request already run through nonce validation.
				: array(),
		);
	}

	/**
	 * Validates the post request for every action.
	 *
	 * @return bool
	 */
	protected function validate_request(): bool {

		// Fail if nonce not in POST.
		if ( ! array_key_exists( 'nonce', $_POST ) ) {
			return false;
		}

		// Verify the nonce.
		return (bool) \wp_verify_nonce( \sanitize_text_field( $_POST['nonce'] ), self::NONCE_KEY );
	}

	/**
	 * Filters all rules if filters defined.
	 *
	 * @param array<mixed> $request
	 * @return Rule[]
	 */
	public function maybe_filter_rules( array $request ): array {
		return array_key_exists( 'filters', $request )
			? $this->rule_repository->filter_rules(
				\array_key_exists( 'search', $request['filters'] ) ? \sanitize_text_field( $request['filters']['search'] ) : '',
				\array_key_exists( 'type', $request['filters'] ) ? $request['filters']['type'] : array(),
				\array_key_exists( 'fields', $request['filters'] ) ? $request['filters']['fields'] : array(),
				\array_key_exists( 'response', $request['filters'] ) ? $request['filters']['response'] : array()
			) : $this->rule_repository->get_rules();
	}

	/**
	 * Attempts to paginate the results, using limit and offset as defined in request.
	 *
	 * @param Rule[] $rules
	 * @param array<mixed> $request
	 * @return Rule[]
	 */
	public function maybe_paginate_rules( array $rules, array $request ): array {
		$limit  = \array_key_exists( 'limit', $request['filters'] ) ? (int) $request['filters']['limit'] : Template_Helper::RULES_PER_PAGE;
		$offset = \array_key_exists( 'offset', $request['filters'] ) ? (int) $request['filters']['offset'] : 0;

		return array_key_exists( 'filters', $request )
			? $this->rule_repository->paginate_rules(
				$rules,
				$limit,
				$offset <= 0 ? 0 : $limit * $offset
			) : $rules;
	}

	/**
	 * Parses an array of rules into a string.
	 *
	 * @param Rule[] $rules
	 * @return string
	 */
	public function parse_rules( array $rules ): string {
		return join( array_map( array( $this->template_helper, 'create_rule_row' ), $rules ) );
	}

	/**
	 * Multiplies the limit and offset to get the initially displayed.
	 *
	 * @param array<mixed> $request
	 * @return int
	 */
	public function existing_rule_count( array $request ): int {
		$limit  = \array_key_exists( 'limit', $request['filters'] ) ? (int) $request['filters']['limit'] : Template_Helper::RULES_PER_PAGE;
		$offset = \array_key_exists( 'offset', $request['filters'] ) ? (int) $request['filters']['offset'] : 0;

		return $offset <= 0 ? 0 : $limit * $offset;
	}

	/**
	 * Returns the HTML for a new rule to be created.
	 *
	 * @param array{operation:string,rule_type:string,rule_values?:string[]} $request
	 * @return array{notification:string,error:bool,rules:string,partial:string}
	 */
	public function new_rule( array $request ) : array {

		// Ensure we have a valid rule type.
		if ( $request['rule_type'] === '-1' ) {
			return $this->generate_response_array(
				Translation::ajax_notifications( 'no_rule_selected' ),
				true
			);
		}

		// Return markup for new rule, based on rule type
		return $this->generate_response_array(
			'',
			false,
			( new Template_Helper() )->generate_new_rule_partial( $request['rule_type'] )
		);
	}

	/**
	 * Returns the HTML for editing an existing rule.
	 *
	 * @param array{operation:string,rule_type?:string,rule_values?:string[]} $request
	 * @return array{notification:string,error:bool,rules:string,partial:string}
	 */
	public function edit_rule( array $request ): array {

		// Ensure we have valid data.
		if ( ! array_key_exists( 'rule_values', $request ) // Must have rule values.
		|| ! is_array( $request['rule_values'] ) // Rule values must be an array
		|| ! array_key_exists( 'ruleID', $request['rule_values'] ) ) { // Must have rule ID, even if empty.

			return $this->generate_response_array( Translation::ajax_notifications( 'invalid_payload' ), true );
		}

		$rule = $this->rule_repository->find( \sanitize_text_field( $request['rule_values']['ruleID'] ) );

		// If we don't find the rule.
		if ( is_null( $rule ) ) {
			return $this->generate_response_array( Translation::ajax_notifications( 'rule_not_found' ), true );
		}

		$rule_partial = ( new Template_Helper() )->generate_edit_rule_partial( $rule );
		return $this->generate_response_array(
			'',
			false,
			$rule_partial
		);
	}

	/**
	 * Deletes a rule from request.
	 *
	 * @param array{operation:string,rule_type?:string,rule_values?:string[]} $request
	 * @return array{notification:string,error:bool,rules:string,partial:string}
	 */
	public function delete_rule( array $request ): array {
		// Ensure we have valid data.
		if ( ! array_key_exists( 'rule_values', $request ) // Must have rule values.
		|| ! is_array( $request['rule_values'] ) // Rule values must be an array
		|| ! array_key_exists( 'ruleID', $request['rule_values'] ) ) { // Must have rule ID, even if empty.
			return $this->generate_response_array( Translation::ajax_notifications( 'invalid_payload' ), true );
		}

		$this->rule_repository->delete_rule( \sanitize_text_field( $request['rule_values']['ruleID'] ) );

		return $this->generate_response_array( Translation::ajax_notifications( 'rule_deleted' ), false, '' );
	}

	/**
	 * Upsert's a rule based on the passed request
	 *
	 * @param array{operation:string,rule_type?:string,rule_values?:string[]} $request
	 * @return array{notification:string,error:bool,rules:string,partial:string}
	 */
	public function upsert_rule( array $request ): array {

		// Check we have all required values in the request.
		if ( ! array_key_exists( 'rule_type', $request )                // Must have rule type.
		|| ! array_key_exists( 'rule_values', $request )                // Must have rule values.
		|| ! is_array( $request['rule_values'] )                        // Rule values must be an array
		|| ! array_key_exists( 'ruleID', $request['rule_values'] ) ) {  // Must have rule ID, even if empty.

			return $this->generate_response_array( Translation::ajax_notifications( 'invalid_payload' ), true );
		}

		// If new rule.
		if ( '' === $request['rule_values']['ruleID'] ) {

			// Create new rule.
			$rule = $this->rule_factory->from_ajax_request( $request );
			$this->rule_repository->upsert_rule( $rule );

			return $this->generate_response_array( Translation::ajax_notifications( 'rule_created' ) );
		} else {

			// Create new rule and set existing ID.
			$rule          = $this->rule_factory->from_ajax_request( $request );
			$rule->rule_id = $request['rule_values']['ruleID'];

			$this->rule_repository->upsert_rule( $rule );

			return $this->generate_response_array( Translation::ajax_notifications( 'rule_updated' ) );

		}
	}

	/**
	 * Clears all existing rules.
	 *
	 * @param array{operation:string,rule_type?:string,rule_values?:string[]} $request
	 * @return array{notification:string,error:bool,rules:string,partial:string}
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	public function clear_rules( array $request ): array {
		$this->rule_repository->reset_rules();
		return $this->generate_response_array( Translation::ajax_notifications( 'all_rules_deleted' ) );
	}

	/**
	 * Emits the response.
	 *
	 * @param array{notification:string,error:bool,rules:string,partial:string} $response
	 * @param int $code
	 * @return no-return
	 */
	public function emit_response( array $response, int $code = 200 ): void {
		\wp_send_json( $response, $code );
	}

	/**
	 * Generates the response array
	 *
	 * @param string $message
	 * @return array{notification:string,error:bool,rules:string,partial:string}
	 */
	public function generate_response_array( string $message, bool $is_error = false, string $partial = '' ): array {
		return array(
			'notification' => esc_html( $message ),
			'error'        => $is_error,
			'rules'        => '', //,
			'partial'      => $partial,
		);
	}


}
