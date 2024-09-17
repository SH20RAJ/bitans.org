<?php

/**
 * ajax -> posts -> share
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

// valid inputs
if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
  _error(400);
}

try {

  // initialize the return array
  $return = [];

  switch ($_REQUEST['do']) {
    case 'publish':
      // publish
      $user->share($_GET['post_id'], $_POST);

      // return
      modal("SUCCESS", __("Success"),  __("This has been shared Successfully"));
      break;

    case 'create':
      // prepare publisher
      /* get post */
      $post = $user->get_post($_GET['post_id']);
      if (!$post) {
        _error(404);
      }
      $smarty->assign('post', $post);
      /* check if photo id exists */
      if (isset($_GET['photo_id']) && is_numeric($_GET['photo_id'])) {
        $photo = $user->get_photo($_GET['photo_id']);
        if (!$photo) {
          _error(404);
        }
        $smarty->assign('photo', $photo);
      }
      /* get user pages */
      $pages = $user->get_pages(array('managed' => true, 'user_id' => $user->_data['user_id']));
      $smarty->assign('pages', $pages);
      /* get user groups */
      $groups = $user->get_groups(array('get_all' => true, 'user_id' => $user->_data['user_id']));
      $smarty->assign('groups', $groups);
      /* get user events */
      $events = $user->get_events(array('get_all' => true, 'user_id' => $user->_data['user_id']));
      $smarty->assign('events', $events);

      // return
      $return['share_publisher'] = $smarty->fetch("ajax.share.publisher.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.share_publisher); initialize_modal();";
      break;

    default:
      _error(400);
      break;
  }

  // return & exit
  return_json($return);
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
