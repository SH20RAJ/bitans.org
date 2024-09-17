<?php

/**
 * ajax -> user -> orders
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

  switch ($_GET['do']) {
    case 'view':

      // check permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        throw new Exception(__("You don't have the right permission to access this"));
      }

      // get order
      $smarty->assign('order', $user->get_order($_GET['id'], false, true));

      // return
      $return['template'] = $smarty->fetch("ajax.orders.view.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template); initialize_modal();";
      break;

    case 'edit':

      // get order
      $for_admin = ($user->_is_admin || $user->_is_moderator) ? true : false;
      $smarty->assign('order', $user->get_order($_GET['id'], false, $for_admin));

      // return
      $return['template'] = $smarty->fetch("ajax.orders.edit.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template); initialize_modal();";
      break;

    case 'update':

      // update order
      $for_admin = ($user->_is_admin || $user->_is_moderator) ? true : false;
      $user->update_order($_POST['order_id'], $_POST['status'], $_POST['tracking_link'], $_POST['tracking_number'], $for_admin);

      // return
      $return['callback'] = 'window.location.reload();';
      break;

    case 'invoice':

      // get order
      $for_admin = ($user->_is_admin || $user->_is_moderator) ? true : false;
      $smarty->assign('order', $user->get_order($_GET['id'], true, $for_admin));

      // return
      $return['invoice'] = $smarty->fetch("pdf/invoice.tpl");
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
