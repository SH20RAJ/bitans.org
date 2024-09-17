<?php

/**
 * ajax -> user -> addresses
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
    case 'add':

      // return
      $return['template'] = $smarty->fetch("ajax.addresses.add.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template); initialize_modal();";
      break;

    case 'edit':

      // get address
      $smarty->assign('address', $user->get_address($_GET['id']));

      // return
      $return['template'] = $smarty->fetch("ajax.addresses.edit.tpl");
      $return['callback'] = "$('#modal').modal('show'); $('.modal-content:last').html(response.template); initialize_modal();";
      break;

    case 'insert':
      // insert address
      $user->insert_address($_POST['title'], $_POST['country'], $_POST['city'], $_POST['zip_code'], $_POST['phone'], $_POST['address']);

      // return
      $return['callback'] = 'window.location.reload();';
      break;

    case 'update':

      // update address
      $user->update_address($_POST['address_id'], $_POST['title'], $_POST['country'], $_POST['city'], $_POST['zip_code'], $_POST['phone'], $_POST['address']);

      // return
      $return['callback'] = 'window.location.reload();';
      break;

    case 'delete':

      // delete address
      $user->delete_address($_GET['id']);
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
