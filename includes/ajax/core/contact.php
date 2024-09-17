<?php

/**
 * ajax -> core -> contact
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

try {

  // contact us
  $user->contact_us($_POST['name'], $_POST['email'], $_POST['subject'], $_POST['message'], $_POST['g-recaptcha-response'], true);

  // return
  return_json(['success' => true, 'message' => __("Your message has been sent! Thanks a lot and will be back to you soon")]);
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
