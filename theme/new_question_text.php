<?php
/**
 * New question plain text template
 */
$author_name = ap_user_display_name($args->post_author);

printf(__("Hello!\r\n A new question is posted by %s\r\n\r\nTitle: %s\r\n Description:\r\n%s", "ap"), $author_name, $args->post_title, $args->post_content);

