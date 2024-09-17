<?php

/**
 * ajax -> users -> switch
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
/* check uid */
if (!in_array($_POST['do'], ['signin', 'revoke']) && isset($_POST['uid']) && !is_numeric($_POST['uid'])) {
  _error(400);
}

try {

  switch ($_POST['do']) {
    case 'signin':
      // signin connected account
      $user->connected_account_signin($_POST['username_email'], $_POST['password']);
      break;

    case 'switch':
      // switch connected account
      $user->connected_account_switch($_POST['uid']);
      break;

    case 'remove':
      // remove connected account
      $user->connected_account_remove($_POST['uid']);
      break;

    case 'revoke':
      // revoke connected account
      $user->connected_account_revoke();
      break;

    default:
      _error(400);
      break;
  }

  // return & exit
  return_json(['callback' => 'window.location.reload();']);
} catch (Exception $e) {
  if ($_POST['do'] == 'signin') {
    return_json(['error' => true, 'message' => $e->getMessage()]);
  } else {
    modal("ERROR", __("Error"), $e->getMessage());
  }
}
