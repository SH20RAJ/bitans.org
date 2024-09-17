<?php

/**
 * ajax -> posts -> article
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

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

try {

  // initialize the return array
  $return = [];
  $return['callback'] = 'window.location.replace(response.path);';

  switch ($_GET['do']) {
    case 'create':
      // create article
      $_POST['tips_enabled'] = (isset($_POST['tips_enabled'])) ? '1' : '0';
      $_POST['subscribers_only'] = (isset($_POST['subscribers_only'])) ? '1' : '0';
      $_POST['paid_post'] = (isset($_POST['paid_post'])) ? '1' : '0';
      $post_id = $user->post_article($_POST['publish_to'], $_POST['page_id'], $_POST['group_id'], $_POST['event_id'], $_POST['title'], $_POST['text'], $_POST['cover'], $_POST['category'], $_POST['tags'], $_POST['tips_enabled'], $_POST['subscribers_only'], $_POST['paid_post'], $_POST['paid_post_price'], $_POST['paid_post_text']);

      // return
      $return['path'] = $system['system_url'] . '/blogs/' . $post_id . '/' . get_url_text($_POST['title']);
      break;

    case 'edit':
      // valid inputs
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }

      // edit article
      $_POST['tips_enabled'] = (isset($_POST['tips_enabled'])) ? '1' : '0';
      $_POST['subscribers_only'] = (isset($_POST['subscribers_only'])) ? '1' : '0';
      $_POST['paid_post'] = (isset($_POST['paid_post'])) ? '1' : '0';
      $user->edit_article($_GET['id'], $_POST['title'], $_POST['text'], $_POST['cover'], $_POST['category'], $_POST['tags'], $_POST['tips_enabled'], $_POST['subscribers_only'], $_POST['paid_post'], $_POST['paid_post_price'], $_POST['paid_post_text']);

      // return
      $return['path'] = $system['system_url'] . '/blogs/' . $_GET['id'] . '/' . get_url_text($_POST['title']);
      break;

    default:
      _error(400);
      break;
  }

  // return & exit
  return_json($return);
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
