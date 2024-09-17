<?php

/**
 * ajax -> core -> activation phone reset
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check user logged in
if (!$user->_logged_in) {
  modal('LOGIN');
}

// check user activated
if (!$system['activation_enabled'] || $user->_data['user_activated']) {
  modal("SUCCESS", __("Activated"), __("Your account already activated!"));
}

try {

  // activation phone reset
  $user->activation_phone_reset($_POST['phone']);

  // return
  modal("SUCCESS", __("Your phone has been changed"), __("Please check your phone and copy the verification code to complete the verification process"));
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
