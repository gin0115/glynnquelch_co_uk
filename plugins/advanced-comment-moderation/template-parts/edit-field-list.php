<?php
use Team51\Advanced_Plugin_Moderation\Helper\Translation;
?>
<ul class="acm_rule__fields">
    <li><input type="checkbox" <?php echo true === $author ? 'CHECKED' : ''; ?> name="validate_field" id="validate_field" value="author"> <?php echo Translation::comment_field_label( 'author' ); ?></li>
    <li><input type="checkbox" <?php echo true === $email ? 'CHECKED' : ''; ?> name="validate_field" id="validate_field" value="email"> <?php echo Translation::comment_field_label( 'email' ); ?></li>
    <li><input type="checkbox" <?php echo true === $url ? 'CHECKED' : ''; ?> name="validate_field" id="validate_field" value="url"> <?php echo Translation::comment_field_label( 'url' ); ?></li>
    <li><input type="checkbox" <?php echo true === $agent ? 'CHECKED' : ''; ?> name="validate_field" id="validate_field" value="agent"> <?php echo Translation::comment_field_label( 'agent' ); ?></li>
    <li><input type="checkbox" <?php echo true === $ip_address ? 'CHECKED' : ''; ?> name="validate_field" id="validate_field" value="ip_address"> <?php echo Translation::comment_field_label( 'ip_address' ); ?></li>
    <li><input type="checkbox" <?php echo true === $content ? 'CHECKED' : ''; ?> name="validate_field" id="validate_field" value="content"> <?php echo Translation::comment_field_label( 'content' ); ?></li>
</ul>