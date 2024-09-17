<?php

/**
 * ajax -> admin -> currencies
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

// handle currencies
try {

  switch ($_GET['do']) {
    case 'edit':
      /* valid inputs */
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      /* prepare */
      $_POST['enabled'] = (isset($_POST['enabled'])) ? '1' : '0';
      /* update */
      $db->query(sprintf("UPDATE system_currencies SET enabled = %s, name = %s, code = %s, symbol = %s, dir = %s WHERE currency_id = %s", secure($_POST['enabled']), secure($_POST['name']), secure($_POST['code']), secure($_POST['symbol']), secure($_POST['dir']), secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['success' => true, 'message' => __("Language info have been updated")]);
      break;

    case 'add':
      /* prepare */
      $_POST['enabled'] = (isset($_POST['enabled'])) ? '1' : '0';
      /* insert */
      $db->query(sprintf("INSERT INTO system_currencies (enabled, name, code, symbol, dir) VALUES (%s, %s, %s, %s, %s)", secure($_POST['enabled']), secure($_POST['name']), secure($_POST['code']), secure($_POST['symbol']), secure($_POST['dir']))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['callback' => 'window.location = "' . $system['system_url'] . '/' . $control_panel['url'] . '/currencies";']);
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
