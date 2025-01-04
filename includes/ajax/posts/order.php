<?php

/**
 * ajax -> posts -> order
 * 
 * @package Sngine
 * @author Shaswat Raj
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// user access
user_access(true);

try {

  // initialize the return array
  $return = array();

  switch ($_POST['do']) {
    case 'set':
      // valid inputs
      if (!isset($_POST['order']) || !in_array($_POST['order'], array('latest', 'random'))) {
        _error(400);
      }

      // store order preference in session
      $_SESSION['posts_order'] = $_POST['order'];

      // return
      $return['callback'] = 'window.location.reload();';
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
