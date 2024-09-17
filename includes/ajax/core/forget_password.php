<?php

/**
 * ajax -> core -> forget password
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check user logged in
if ($user->_logged_in) {
  return_json(['callback' => 'window.location.reload();']);
}

// check secret
if ($_SESSION['secret'] != $_POST['secret']) {
  _error(403);
}

try {

  // forget password
  $user->forget_password($_POST['email'], $_POST['g-recaptcha-response'], true);

  // return
  modal("#forget-password-confirm", "{email: '" . $_POST['email'] . "'}");
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
