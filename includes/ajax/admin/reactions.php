<?php

/**
 * ajax -> admin -> reactions
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

// handle reactions
try {

  switch ($_GET['do']) {
    case 'edit':
      /* valid inputs */
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      if (is_empty($_POST['title'])) {
        throw new Exception(__("You must enter reaction title"));
      }
      if (is_empty($_POST['color'])) {
        throw new Exception(__("You must enter reaction color"));
      }
      if (is_empty($_POST['image'])) {
        throw new Exception(__("You must upload reaction image"));
      }
      /* prepare */
      $_POST['enabled'] = (isset($_POST['enabled'])) ? '1' : '0';
      /*  check if all reactions are disabled */
      $check_reactions = $db->query(sprintf("SELECT COUNT(*) as count FROM system_reactions WHERE enabled = '1' and reaction_id != %s", secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check_reactions->fetch_assoc()['count'] == 0 && !$_POST['enabled']) {
        throw new Exception(__("You must enable at least one reaction"));
      }
      /* update */
      $db->query(sprintf("UPDATE system_reactions SET title = %s, color = %s, image = %s, reaction_order = %s, enabled = %s WHERE reaction_id = %s", secure($_POST['title']), secure($_POST['color']), secure($_POST['image']), secure($_POST['reaction_order']), secure($_POST['enabled']), secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['success' => true, 'message' => __("Reaction info have been updated")]);
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
