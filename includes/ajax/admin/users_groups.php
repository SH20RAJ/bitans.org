<?php

/**
 * ajax -> admin -> users groups
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check admin|moderator permission
if (!$user->_is_admin) {
  modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
}

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle users groups
try {

  switch ($_GET['do']) {
    case 'add':
      /* valid inputs */
      if (is_empty($_POST['title'])) {
        throw new Exception(__("Please enter a valid group name"));
      }
      /* check the permissions group */
      if (is_empty($_POST['permissions_group'])) {
        throw new Exception(__("You must select valid permissions group"));
      } else {
        if (!$user->check_permissions_group($_POST['permissions_group'])) {
          throw new Exception(__("You must select valid permissions group"));
        }
      }
      /* insert */
      $db->query(sprintf("INSERT INTO users_groups (user_group_title, permissions_group_id) VALUES (%s, %s)", secure($_POST['title']), secure($_POST['permissions_group']))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['callback' => 'window.location = "' . $system['system_url'] . '/' . $control_panel['url'] . '/users_groups";']);
      break;

    case 'edit':
      /* valid inputs */
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      if (is_empty($_POST['title'])) {
        throw new Exception(__("Please enter a valid group name"));
      }
      /* check the permissions group */
      if (is_empty($_POST['permissions_group'])) {
        throw new Exception(__("You must select valid permissions group"));
      } else {
        if (!$user->check_permissions_group($_POST['permissions_group'])) {
          throw new Exception(__("You must select valid permissions group"));
        }
      }
      /* update */
      $db->query(sprintf("UPDATE users_groups SET user_group_title = %s, permissions_group_id = %s WHERE user_group_id = %s", secure($_POST['title']), secure($_POST['permissions_group']), secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['success' => true, 'message' => __("User group have been updated")]);
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
