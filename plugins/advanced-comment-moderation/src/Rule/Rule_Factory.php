<?php

/**
 * Validates and constructs rules.
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Team51\Advanced_Plugin_Moderation\Rule\Regex_Rule;
use Team51\Advanced_Plugin_Moderation\Rule\IP_Range_Rule;

class Rule_Factory {

	/**
	 * Static constructor
	 *
	 * @return Rule_Factory
	 */
	public static function get_instance(): Rule_Factory {
		return new self();
	}

	/**
	 * Creates a rule based on a start and end IP addresses.
	 *
	 * @param string $response
	 * @param string $start_ip
	 * @param string $end_ip
	 * @return \Team51\Advanced_Plugin_Moderation\Rule\IP_Range_Rule
	 * @throws Rule_Exception (code 100 or code 101)
	 */
	public function ip_range( string $response, string $start_ip, string $end_ip ): Rule {
		// Check valid IP addresses.
		if ( ! filter_var( $start_ip, FILTER_VALIDATE_IP ) ) {
			throw Rule_Exception::invalid_ip_address( $start_ip, true );
		}

		if ( ! filter_var( $end_ip, FILTER_VALIDATE_IP ) ) {
			throw Rule_Exception::invalid_ip_address( $end_ip, false );
		}

		// Check network nodes match (first 2 parts of IPv4)
		$start_parts = explode( '.', $start_ip );
		$end_parts   = explode( '.', $end_ip );
		if ( $start_parts[0] !== $end_parts[0]
		|| $start_parts[1] !== $end_parts[1]
		|| $start_parts[2] !== $end_parts[2]
		) {
			throw Rule_Exception::ip_network_mismatch( $start_ip, $end_ip );
		}

		$rule           = new IP_Range_Rule( $response );
		$rule->start_ip = $start_ip;
		$rule->end_ip   = $end_ip;
		return $rule;

	}

	/**
	 * Create a regex rule.
	 *
	 * @param string $response
	 * @param string $expression
	 * @param callable(Regex_Rule):Regex_Rule $fields
	 * @return \Team51\Advanced_Plugin_Moderation\Rule\Regex_Rule
	 * @throws Rule_Exception (code 102)
	 */
	public function regex( string $response, string $expression, callable $fields ): Regex_Rule {
		// Validate the expression.
		if ( \mb_strlen( $expression ) === 0 ) {
			throw Rule_Exception::invalid_regex_expression( $expression, 'empty expression' );
		}

		// Check valid regex expression.
		try {
			preg_match( $expression, 'test' );
		} catch ( \Throwable $th ) {
			throw Rule_Exception::invalid_regex_expression( $expression, $th->getMessage() );
		}

		// Create rule.
		$rule             = new Regex_Rule( $response );
		$rule->expression = $expression;
		$rule             = $fields( $rule );

		// Check at least one field is selected to be checked.
		$valid_fields = array_filter(
			\get_object_vars( $rule ),
			function( $field ) {
				return is_bool( $field ) && true === $field;
			}
		);
		if ( empty( $valid_fields ) ) {
			throw Rule_Exception::no_fields_selected( 'Regex' );
		}

		return $rule;
	}

	/**
	 * Create a wildcard rule.
	 *
	 * @param string $response
	 * @param string $expression
	 * @param callable $fields
	 * @return \Team51\Advanced_Plugin_Moderation\Rule\Wildcard_Rule
	 * @throws Rule_Exception (code 105)
	 */
	public function wildcard( string $response, string $expression, callable $fields ): Wildcard_Rule {
		// Validate the expression.
		if ( \mb_strlen( $expression ) === 0 ) {
			throw Rule_Exception::invalid_wildcard_expression( $expression, 'empty expression' );
		}

		// Create rule.
		$rule             = new Wildcard_Rule( $response );
		$rule->expression = $expression;
		$rule             = $fields( $rule );

		// Check at least one field is selected to be checked.
		$valid_fields = array_filter(
			\get_object_vars( $rule ),
			function( $field ) {
				return is_bool( $field ) && true === $field;
			}
		);
		if ( empty( $valid_fields ) ) {
			throw Rule_Exception::no_fields_selected( 'Wildcard' );
		}

		return $rule;
	}

	/**
	 * Creates a rule based on a start and end IP addresses.
	 *
	 * @param string $response
	 * @param string $expression
	 * @return \Team51\Advanced_Plugin_Moderation\Rule\CIDR_Rule
	 * @throws Rule_Exception (code 106)
	 */
	public function cidr( string $response, string $expression ): Rule {

		if ( ! (bool) \preg_match( '^([0-9]{1,3}\.){3}[0-9]{1,3}(\/([0-9]|[1-2][0-9]|3[0-2]))?$^', $expression ) ) {
			throw Rule_Exception::invalid_cidr_expression( $expression );
		}

		$rule             = new CIDR_Rule( $response );
		$rule->expression = $expression;

		return $rule;

	}

	/**
	 * Creates a rule from an ajax request.
	 *
	 * @param array{operation:string,rule_type:string,rule_values:array<mixed>} $request
	 * @return \Team51\Advanced_Plugin_Moderation\Rule\Rule
	 * @throws Rule_Exception (code 104)
	 */
	public function from_ajax_request( array $request ): Rule {
		// Based on the Rule type, create Rule.
		switch ( $request['rule_type'] ) {
			case 'IP_Range_Rule':
				return $this->ip_range(
					$request['rule_values']['response'],
					$request['rule_values']['startIP'] ?? '',
					$request['rule_values']['endIP'] ?? ''
				);

			case 'CIDR_Rule':
				return $this->cidr(
					$request['rule_values']['response'],
					$request['rule_values']['expression'] ?? ''
				);

			case 'Regex_Rule':
				return $this->regex(
					$request['rule_values']['response'],
					$request['rule_values']['expression'] ?? '',
					function( $rule ) use ( $request ) {
						// If we have rule fields.
						if ( isset( $request['rule_values']['ruleFields'] ) && is_array( $request['rule_values']['ruleFields'] ) ) {
							foreach ( $rule->get_fields() as $field ) {
								$rule->{$field} = in_array( $field, $request['rule_values']['ruleFields'], true );
							}
						}
						return $rule;
					}
				);

			case 'Wildcard_Rule':
				return $this->wildcard(
					$request['rule_values']['response'],
					$request['rule_values']['expression'] ?? '',
					function( $rule ) use ( $request ) {
						// If we have rule fields.
						if ( isset( $request['rule_values']['ruleFields'] ) && is_array( $request['rule_values']['ruleFields'] ) ) {
							foreach ( $rule->get_fields() as $field ) {
								$rule->{$field} = in_array( $field, $request['rule_values']['ruleFields'], true );
							}
						}
						return $rule;
					}
				);

			default:
				throw Rule_Exception::invalid_ajax_rule_values( $request['rule_type'] );
		}
	}



}
