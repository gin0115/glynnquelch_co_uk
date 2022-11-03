<?php

/**
 * Filters rules based on various parameters.
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 1.0.1
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Team51\Advanced_Plugin_Moderation\Rule\Rule;

class Rule_Filter {

	/**
	 * Holds the filtered rules.
	 *
	 * @var Rule[]
	 */
	protected $rules = array();

	/**
	 * @param Rule[] $rules
	 */
	public function __construct( array $rules ) {
		$this->rules = $rules;
	}

	/**
	 * Returns the rules.
	 *
	 * @return Rule[]
	 */
	public function get_rules(): array {
		return $this->rules;
	}

	/**
	 * Filters all rules for those containing the passed expression.
	 *
	 * @param string $expression
	 * @return self
	 */
	public function filter_expression( string $expression ): self {
		if ( \mb_strlen( $expression ) === 0 ) {
			return $this;
		}

		$this->rules = array_filter(
			$this->rules,
			function( Rule $rule ) use ( $expression ): bool {
				return \property_exists( $rule, 'expression' )
					? mb_strpos( $rule->expression, $expression ) !== false
					: true; // If rules doesn't have an expression, just pass (applies to IP Range only.)
			}
		);

		return $this;
	}

	/**
	 * Filters the rules based on the type.
	 *
	 * @param array<string, bool> $types
	 * @return self
	 */
	public function filter_rule_types( array $types ): self {
		$types = array_keys( $this->remove_false( $types ) );

		// If we have no rules selected, skip.
		if ( empty( $types ) ) {
			return $this;
		}

		$this->rules = array_filter(
			$this->rules,
			function( Rule $rule ) use ( $types ): bool {
				return in_array( $rule->get_type(), $types, true );
			}
		);

		return $this;
	}

	/**
	 * Filters rules based on fields.
	 *
	 * @param array<string, bool> $fields
	 * @return self
	 */
	public function filter_fields( array $fields ): self {
		$fields = array_keys( $this->remove_false( $fields ) );

		// If we have no fields selected, skip.
		if ( empty( $fields ) ) {
			return $this;
		}

		$this->rules = array_filter(
			$this->rules,
			function( Rule $rule ) use ( $fields ): bool {
				// Loop through each field, if any exists and are defined as true, return true.
				foreach ( $fields as $field ) {
					if ( \property_exists( $rule, $field )
					&& true === $rule->{$field} ) {
						return true;
					}
				}
				return false;
			}
		);
		return $this;
	}

	/**
	 * Filters the rules based on the response
	 *
	 * @param array<string, bool> $responses
	 * @return self
	 */
	public function filter_response( array $responses ): self {
		$responses = array_keys( $this->remove_false( $responses ) );

		// If we have no responses selected, skip.
		if ( empty( $responses ) ) {
			return $this;
		}

		$this->rules = array_filter(
			$this->rules,
			function( Rule $rule ) use ( $responses ): bool {
				return in_array( $rule->response, $responses, true );
			}
		);

		return $this;
	}

	/**
	 * Removes all values from an array which are not true.
	 *
	 * @param array<string, bool|string> $array
	 * @return array<string, bool>
	 */
	protected function remove_false( array $array ): array {
		/* @phpstan-ignore-next-line */
		return array_filter(
			$array,
			function( $e ): bool {
				return is_string( $e )
					? 'true' === $e
					: true === (bool) $e;
			}
		);
	}
}
