<?php

/**
 * ajax -> monetization -> controller
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

// check if monetization is enabled
if ($_GET['do'] != "get_plans" && !$user->_data['can_monetize_content']) {
  modal("ERROR", __("Error"), __("This feature has been disabled by the admin"));
}

try {

  // initialize the return array
  $return = [];

  switch ($_GET['do']) {
    case 'get_plans':

      // valid inputs
      if (!isset($_GET['node_id']) || !is_numeric($_GET['node_id'])) {
        _error(400);
      }
      if (!isset($_GET['node_type']) || !in_array($_GET['node_type'], ['profile', 'page', 'group'])) {
        _error(400);
      }
      $smarty->assign('node_id', $_GET['node_id']);
      $smarty->assign('node_type', $_GET['node_type']);

      // get monetization plans
      $smarty->assign('monetization_plans', $user->get_monetization_plans($_GET['node_id'], $_GET['node_type']));

      // return
      $return['template'] = $smarty->fetch("ajax.monetization.plans.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template); initialize_modal();";
      break;

    case 'add':

      // valid inputs
      if (!isset($_GET['node_id']) || !is_numeric($_GET['node_id'])) {
        _error(400);
      }
      if (!isset($_GET['node_type']) || !in_array($_GET['node_type'], ['profile', 'page', 'group'])) {
        _error(400);
      }
      $smarty->assign('node_id', $_GET['node_id']);
      $smarty->assign('node_type', $_GET['node_type']);

      // return
      $return['template'] = $smarty->fetch("ajax.monetization.add.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template); initialize_modal();";
      break;

    case 'edit':

      // get monetization plan
      $monetization_plan = $user->get_monetization_plan($_GET['id']);
      if (!$monetization_plan) {
        _error(400);
      }
      $smarty->assign('monetization_plan', $monetization_plan);

      // return
      $return['template'] = $smarty->fetch("ajax.monetization.edit.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template); initialize_modal();";
      break;

    case 'insert':
      // insert monetization plan
      $user->insert_monetization_plan($_POST['node_id'], $_POST['node_type'], $_POST['title'], $_POST['price'], $_POST['period_num'], $_POST['period'], $_POST['custom_description'], $_POST['plan_order']);

      // return
      $return['callback'] = 'window.location.reload();';
      break;

    case 'update':

      // update monetization plan
      $user->update_monetization_plan($_POST['plan_id'], $_POST['title'], $_POST['price'], $_POST['period_num'], $_POST['period'], $_POST['custom_description'], $_POST['plan_order']);

      // return
      $return['callback'] = 'window.location.reload();';
      break;

    case 'delete':

      // delete monetization plan
      $user->delete_monetization_plan($_GET['id']);
      break;

    default:
      _error(400);
      break;
  }

  // return & exit
  return_json($return);
} catch (ValidationException $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
