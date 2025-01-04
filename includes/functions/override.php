<?php

/**
 * Override core functions to add random post ordering
 */

// Include original functions
require_once(ABSPATH.'includes/functions/posts.php');

// Override the order query in the main get_posts function
add_filter('posts_order_query', function($order_query) {
    return get_posts_order_query();
});
