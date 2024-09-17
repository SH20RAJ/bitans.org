<?php

/**
 * ajax -> users -> photos
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

// valid inputs
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  _error(400);
}
if (!isset($_GET['filter']) || !in_array($_GET['filter'], ['avatar', 'cover'])) {
  _error(400);
}
if (!isset($_GET['type']) || !in_array($_GET['type'], ['user', 'page', 'group', 'event'])) {
  _error(400);
}

try {

  // initialize the return array
  $return = [];

  // get photos
  $photos = $user->get_photos($_GET['id'], $_GET['type']);
  /* assign variables */
  $smarty->assign('photos', $photos);
  $smarty->assign('id', $_GET['id']);
  $smarty->assign('filter', $_GET['filter']);
  $smarty->assign('type', $_GET['type']);
  /* return */
  $return['profile_photos'] = $smarty->fetch("ajax.profile_photos.tpl");
  $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.profile_photos);";

  // return & exit
  return_json($return);
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
