<?php

/**
 * Exceptions for invalid rule creation
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Exception;

class Rule_Exception extends Exception {

	/**
	 * Network values from IP address do not match.
	 *
	 * @code 100
	 * @param string $start_ip The start IP
	 * @param string $end_ip The end IP
	 * @return Rule_Exception
	 * @since 0.1.0
	 */
	public static function ip_network_mismatch( string $start_ip, string $end_ip ): Rule_Exception {
		$message = sprintf(
			/* translators: %1$s: Start IP, %2$s: End IP,  */
			__( 'The network of the IP address (first 3 parts) must match (%1$s & %2$s passed)', 'team51-advanced-comment-moderation' ),
			$start_ip,
			$end_ip
		);
		return new Rule_Exception( $message, 100 );
	}

	/**
	 * If invalid IP address passed
	 *
	 * @code 101
	 * @param string $ip_address IP Address passed.
	 * @param bool $is_start
	 * @return Rule_Exception
	 * @since 0.1.0
	 */
	public static function invalid_ip_address( string $ip_address, bool $is_start = true ): Rule_Exception {
		$message = sprintf(
			/* translators: %1$s: Start or end IP address passed, %2$s: IP Address */
			__( 'The %1$s IP address (%2$s) passed is not a valid IP address', 'team51-advanced-comment-moderation' ),
			$is_start ? 'start' : 'end',
			$ip_address
		);
		return new Rule_Exception( $message, 101 );
	}

	/**
	 * Invalid regex expression.
	 *
	 * @code 102
	 * @param string $expression
	 * @param string $reason
	 * @return Rule_Exception
	 * @since 0.1.0
	 */
	public static function invalid_regex_expression( string $expression, string $reason ): Rule_Exception {
		$message = sprintf(
			/* translators: %1$s: the regex expression which is not valid, %2$s: IP Address. */
			__( '"%1$s" is not a valid regex expression (%2$s)', 'team51-advanced-comment-moderation' ),
			$expression,
			$reason
		);
		return new Rule_Exception( $message, 102 );
	}

	/**
	 * No fields selected to apply rule to.
	 *
	 * @code 103
	 * @param string $rule_type
	 * @return Rule_Exception
	 * @since 0.1.0
	 */
	public static function no_fields_selected( string $rule_type ): Rule_Exception {
		$message = sprintf(
			/* translators: %1$s: the rule type which has no fields */
			__( '%1$s rules has no fields selected.', 'team51-advanced-comment-moderation' ),
			$rule_type
		);
		return new Rule_Exception( $message, 103 );
	}

	/**
	 * Invalid args from ajax calls to populate Rule in rule factory
	 *
	 * @code 104
	 * @param string $rule_type
	 * @return Rule_Exception
	 * @since 0.1.0
	 */
	public static function invalid_ajax_rule_values( string $rule_type ): Rule_Exception {
		$message = sprintf(
			/* translators: %1$s: the rule type which has an invalid ajax payload. */
			__( '%1$s invalid payload from ajax call.', 'team51-advanced-comment-moderation' ),
			$rule_type
		);
		return new Rule_Exception( $message, 104 );
	}

	/**
	 * Invalid wildcard expression.
	 *
	 * @code 105
	 * @param string $expression
	 * @param string $reason
	 * @return Rule_Exception
	 * @since 0.1.0
	 */
	public static function invalid_wildcard_expression( string $expression, string $reason ): Rule_Exception {
		$message = sprintf(
			/* translators: %1$s: the wildcard expression which is not valid, %2$s: IP Address. */
			__( '"%1$s" is not a valid wildcard expression (%2$s)', 'team51-advanced-comment-moderation' ),
			$expression,
			$reason
		);
		return new Rule_Exception( $message, 105 );
	}

	/**
	 * Invalid CIDR expression.
	 *
	 * @code 106
	 * @param string $expression
	 * @return Rule_Exception
	 * @since 0.1.0
	 */
	public static function invalid_cidr_expression( string $expression ): Rule_Exception {
		$message = sprintf(
			/* translators: %1$s: the CIDR expression which is not valid */
			__( '"%1$s" is not a valid CIDR expression', 'team51-advanced-comment-moderation' ),
			$expression
		);
		return new Rule_Exception( $message, 106 );
	}
}
