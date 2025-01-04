<?php

/**
 * get_posts_order_query
 * 
 * @return string
 */
function get_posts_order_query() {
  // Get order from session, default to random
  $order = isset($_SESSION['posts_order']) ? $_SESSION['posts_order'] : 'random';
  
  if ($order == 'random') {
    return "ORDER BY RAND()";
  }
  return "ORDER BY posts.post_id DESC";
}
