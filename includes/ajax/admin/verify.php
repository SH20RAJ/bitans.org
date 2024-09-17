<?php

/**
 * ajax -> admin -> verify
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check admin|moderator permission
if (!$user->_is_admin && !$user->_is_moderator) {
  modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
}

// valid inputs
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
  _error(400);
}

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle verify
try {

  switch ($_POST['do']) {

    case 'approve':
      switch ($_POST['handle']) {
        case 'user':
          /* get the node */
          $node = $user->get_user($_POST['id']);
          if (!$node) {
            throw new Exception(__("User not found"));
          }
          /* approve request */
          $db->query(sprintf("UPDATE verification_requests SET status = '1' WHERE node_type = 'user' AND node_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* update user */
          $db->query(sprintf("UPDATE users SET user_verified = '1' WHERE user_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* send notification to request author */
          $user->post_notification(['to_user_id' => $node['user_id'], 'action' => 'verification_request_approved']);
          break;

        case 'page':
          /* get the node */
          $node = $user->get_page($_POST['id']);
          if (!$node) {
            throw new Exception(__("Page not found"));
          }
          /* approve request */
          $db->query(sprintf("UPDATE verification_requests SET status = '1' WHERE node_type = 'page' AND node_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* update page */
          $db->query(sprintf("UPDATE pages SET page_verified = '1' WHERE page_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* send notification to request author */
          $user->post_notification(['to_user_id' => $node['page_admin'], 'action' => 'verification_request_page_approved', 'node_url' => $node['page_name']]);
          break;

        default:
          _error(400);
          break;
      }
      break;

    case 'decline':
      switch ($_POST['handle']) {
        case 'user':
          /* get node */
          $node = $user->get_user($_POST['id']);
          if (!$node) {
            throw new Exception(__("User not found"));
          }
          /* decline request */
          $db->query(sprintf("UPDATE verification_requests SET status = '-1' WHERE node_type = 'user' AND node_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* send notification to request author */
          $user->post_notification(['to_user_id' => $node['user_id'], 'action' => 'verification_request_declined']);
          break;

        case 'page':
          /* get node */
          $node = $user->get_page($_POST['id']);
          if (!$node) {
            throw new Exception(__("Page not found"));
          }
          /* decline request */
          $db->query(sprintf("UPDATE verification_requests SET status = '-1' WHERE node_type = 'page' AND node_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* send notification to request author */
          $user->post_notification(['to_user_id' => $node['page_admin'], 'action' => 'verification_request_page_declined', 'node_url' => $node['page_name']]);
          break;

        default:
          _error(400);
          break;
      }
      break;

    default:
      _error(400);
      break;
  }

  // return & exist
  return_json();
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
