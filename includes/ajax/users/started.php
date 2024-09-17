<?php

/**
 * ajax -> users -> started
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

try {

  switch ($_GET['do']) {
    case 'update':
      // update getting started
      $user->getting_satrted_update($_POST);

      // return
      return_json(array('success' => true, 'message' => __("Your info has been updated")));
      break;

    case 'finish':
      // finish getting started
      $user->getting_satrted_finish();

      // return
      return_json(array('callback' => 'window.location = "' . $system['system_url'] . '";'));
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
