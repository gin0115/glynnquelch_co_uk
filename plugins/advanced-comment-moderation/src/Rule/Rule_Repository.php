<?php

/**
 * Repository for all rules in the options table.
 *
 * @package Team51_Advanced_Comment_Moderation\Rule
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

declare(strict_types=1);

namespace Team51\Advanced_Plugin_Moderation\Rule;

use Team51\Advanced_Plugin_Moderation\Helper\Template_Helper;

class Rule_Repository {

	/**
	 * The option key for all rules.
	 */
	public const OPTION_KEY = 't51_comment_moderation_rules';

	/**
	 * Gets all rules from the options table.
	 *
	 * @return Rule[]
	 */
	public function get_rules(): array {
		return get_option( self::OPTION_KEY, array() );
	}

	/**
	 * Updates all rules in options table.
	 *
	 * @param Rule[] $rules
	 * @return void
	 */
	public function update_rules( array $rules ): void {
		\update_option( self::OPTION_KEY, $rules );
	}

	/**
	 * Either adds or updates a rule in options table
	 *
	 * @param Rule $rule
	 * @return void
	 */
	public function upsert_rule( Rule $rule ): void {

		// Get all current rules.
		$rules = $this->get_rules();

		// Attempt to find current rule in options table.
		$index = $this->find_key_from_id( $rule->rule_id );

		// If we don't have an index, just add or replace.
		if ( is_null( $index ) ) {
			$rules[] = $rule;
		} else {
			$rules[ $index ] = $rule;
		}

		$this->update_rules( $rules );
	}

	/**
	 * Find a rule based on its rule_id
	 *
	 * @param string $rule_id
	 * @return Rule|null
	 */
	public function find( string $rule_id ): ?Rule {
		$matching = array_filter(
			$this->get_rules(),
			function( Rule $saved_rule ) use ( $rule_id ): bool {
				return $saved_rule->rule_id === $rule_id;
			}
		);

		return ! empty( $matching ) ? reset( $matching ) : null;
	}

	/**
	 * Attempt to the find the index for a rule based in its ID.
	 *
	 * @param string $rule_id
	 * @return int|null
	 */
	protected function find_key_from_id( string $rule_id ): ?int {
		$matching = array_filter(
			$this->get_rules(),
			function( Rule $saved_rule ) use ( $rule_id ): bool {
				return $saved_rule->rule_id === $rule_id;
			}
		);

		$index = array_key_first( $matching );

		return \is_numeric( $index ) ? (int) $index : null;
	}

	/**
	 * Deletes an rule based on its ID.
	 *
	 * @param string $rule_id
	 * @return void
	 */
	public function delete_rule( string $rule_id ): void {
		$index = $this->find_key_from_id( $rule_id );

		// If the rule exists, remove and update.
		if ( ! is_null( $index ) ) {
			$rules = $this->get_rules();
			unset( $rules[ $index ] );
			$this->update_rules( $rules );
		}
	}

	/**
	 * Resets all rules.
	 *
	 * @return void
	 */
	public function reset_rules(): void {
		$this->update_rules( array() );
	}

	/**
	 * Gets a count of all rules.
	 *
	 * @since 1.0.1
	 * @return int
	 */
	public function total_rules(): int {
		return count( $this->get_rules() );
	}

	/**
	 * Returns all rules, paginated with a limit and offset.
	 *
	 * @since 1.0.1
	 * @param int $limit
	 * @param int $offset
	 * @return Rule[]
	 */
	public function get_all_paginated( int $limit = Template_Helper::RULES_PER_PAGE, int $offset = 0 ): array {
		return array_slice( $this->get_rules(), $offset, $limit, true );
	}

	/**
	 * Filter all rules based on the passed criteria.
	 *
	 * @since 1.0.1
	 * @param string $search
	 * @param array<string, bool> $types
	 * @param array<string, bool> $fields
	 * @param array<string, bool> $response
	 * @return Rule[]
	 */
	public function filter_rules(
		string $search,
		array $types,
		array $fields,
		array $response
	): array {
		return ( new Rule_Filter( $this->get_rules() ) )
			->filter_expression( $search )
			->filter_rule_types( $types )
			->filter_fields( $fields )
			->filter_response( $response )
			->get_rules();
	}

	/**
	 * Paginates an array of rules passed.
	 *
	 * @since 1.0.1
	 * @param Rule[] $rules
	 * @param int $limit
	 * @param int $offset
	 * @return Rule[]
	 */
	public function paginate_rules( array $rules, int $limit = Template_Helper::RULES_PER_PAGE, int $offset = 0 ): array {
		return array_slice( $rules, $offset, $limit, true );
	}

}
