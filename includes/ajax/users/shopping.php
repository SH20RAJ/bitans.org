<?php

/**
 * ajax -> user -> shopping
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

  switch ($_POST['do']) {
    case 'add':

      // add product
      $user->add_to_cart($_POST['id']);
      break;

    case 'remove':

      // remove product
      $user->remove_from_cart($_POST['id']);
      break;

    case 'update':

      // update product
      $user->update_cart($_POST['id'], $_POST['quantity']);
      break;

    case 'checkout':

      // checkout
      $user->checkout_cart($_POST['shipping_address']);
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
