<?php

/**
 * ajax -> users -> image delete
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

  switch ($_POST['handle']) {
    case 'cover-user':
    case 'picture-user':
    case 'cover-page':
    case 'picture-page':
    case 'cover-group':
    case 'picture-group':
    case 'cover-event':
      /* delete avatar/cover image */
      $response = delete_avatar_cover_image($_POST['handle'], $_POST['id']);

      /* return */
      $return['file'] = $response;
      break;

    case 'x-image':
      /* delete x image */
      delete_uploads_file($_POST['image']);
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
