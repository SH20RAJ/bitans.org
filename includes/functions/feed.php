<?php

/**
 * get_feed_posts_query
 * 
 * @param array $args
 * @return string
 */
function get_feed_posts_query($args = []) {
    global $db, $system, $user;

    // prepare where statement
    $where = "";
    $where .= "WHERE (";
    
    // get all public posts
    $where .= "(posts.privacy = 'public'";
    
    // exclude group posts
    $where .= " AND posts.in_group = '0'";
    
    // exclude event posts
    $where .= " AND posts.in_event = '0'";
    
    // exclude wall posts
    $where .= " AND posts.in_wall = '0'";
    
    $where .= ")";

    // check if posts needs approval
    if($system['posts_approval_enabled']) {
        $where .= " AND (posts.pre_approved = '1' OR posts.has_approved = '1')";
    }

    // exclude processing posts
    $where .= " AND (posts.processing != '1')";

    // return query
    return sprintf("SELECT DISTINCT posts.post_id FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' %s ORDER BY RAND() LIMIT %s", $where, $system['newsfeed_results']);
}
