<?php

/**
 * Renders the response select for add/editing rules.
 */

?>
<label for="rule_response">
	<p>Response</p>
	<select name="rule_response" id="rule_response">
		<option value="pending"<?php echo 'pending' === $selected ? ' SELECTED' : ''; ?>>Pending</option>
		<option value="spam"<?php echo 'spam' === $selected ? ' SELECTED' : ''; ?>>Spam</option>
		<option value="trash"<?php echo 'trash' === $selected ? ' SELECTED' : ''; ?>>Trash</option>
	</select>
</label>
