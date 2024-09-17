<?php

/**
 * ajax -> modules -> create|edit
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
if (!in_array($_GET['do'], ['create', 'edit'])) {
  _error(400);
}

try {

  // initialize the return array
  $return = [];
  $return['callback'] = 'window.location.replace(response.path);';

  switch ($_GET['type']) {
    case 'page':
      switch ($_GET['do']) {
        case 'create':
          // page create
          $user->create_page($_POST);

          // return
          $return['path'] = $system['system_url'] . '/pages/' . $_POST['username'];
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(400);
          }
          if (!in_array($_GET['edit'], ['settings', 'info', 'action', 'social', 'monetization'])) {
            _error(400);
          }

          // page edit
          $page_name = $user->edit_page($_GET['id'], $_GET['edit'], $_POST);

          // return
          $return['path'] = $system['system_url'] . '/pages/' . $page_name;
          break;
      }
      break;

    case 'group':
      switch ($_GET['do']) {
        case 'create':
          // group create
          $user->create_group($_POST);

          // return
          $return['path'] = $system['system_url'] . '/groups/' . $_POST['username'];
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(400);
          }
          if (!in_array($_GET['edit'], ['settings', 'monetization'])) {
            _error(400);
          }

          // group edit
          $group_name = $user->edit_group($_GET['id'], $_GET['edit'], $_POST);

          // return
          $return['path'] = $system['system_url'] . '/groups/' . $group_name;
          break;
      }
      break;

    case 'event':
      switch ($_GET['do']) {
        case 'create':
          // event create
          $event_id = $user->create_event($_POST);

          // return
          $return['path'] = $system['system_url'] . '/events/' . $event_id;
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(400);
          }

          // event edit
          $user->edit_event($_GET['id'], $_POST);

          // return
          $return['path'] = $system['system_url'] . '/events/' . $_GET['id'];
          break;
      }
      break;

    default:
      _error(400);
      break;
  }

  // return & exit
  return_json($return);
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
