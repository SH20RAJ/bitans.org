<?php

/**
 * ajax -> modules -> share
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
if (!isset($_GET['node_username'])) {
  _error(400);
}
if (!isset($_GET['node_type']) || !in_array($_GET['node_type'], ['user', 'page', 'group', 'event'])) {
  _error(400);
}

try {

  // initialize the return array
  $return = [];

  // prepare share link
  switch ($_GET['node_type']) {
    case 'user':
      $share_link = $system['system_url'] . '/' . $_GET['node_username'];
      break;

    case 'page':
      $share_link = $system['system_url'] . '/pages/' . $_GET['node_username'];
      break;

    case 'group':
      $share_link = $system['system_url'] . '/groups/' . $_GET['node_username'];
      break;

    case 'event':
      $share_link = $system['system_url'] . '/events/' . $_GET['node_username'];
      break;
  }
  /* assign variables */
  $smarty->assign('share_link', $share_link);

  // return
  $return['share'] = $smarty->fetch("ajax.share.tpl");
  $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.share); initialize_modal();";

  // return & exit
  return_json($return);
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
