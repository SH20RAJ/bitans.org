<?php

/**
 * ajax -> posts -> who reviews
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// user access
user_access(true);

// valid inputs
if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
  _error(400);
}

// check if posts reviews enabled
if (!$system['posts_reviews_enabled']) {
  throw new Exception(__("Posts reviews disabled by admin"));
}

try {

  // initialize the return array
  $return = [];

  // get post
  $post = $user->get_post($_GET['post_id']);
  if (!$post) {
    _error(404);
  }
  /* get reviews */
  $post['reviews'] = $user->get_reviews($_GET['post_id'], 'post');
  /* assign variables */
  $smarty->assign('post', $post);
  /* return */
  $return['template'] = $smarty->fetch("ajax.who_reviews.tpl");
  $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template);";

  // return & exit
  return_json($return);
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
