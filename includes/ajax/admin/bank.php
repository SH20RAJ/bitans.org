<?php

/**
 * ajax -> admin -> bank
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

// valid inputs
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
  _error(400);
}

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle bank transfers
try {

  /* get the request */
  $get_transfer_request = $db->query(sprintf("SELECT * FROM bank_transfers WHERE transfer_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  if ($get_transfer_request->num_rows == 0) {
    _error(400);
  }
  $transfer_request = $get_transfer_request->fetch_assoc();

  switch ($_POST['action']) {
    case 'accept':
      /* process the request */
      switch ($transfer_request['handle']) {
        case 'packages':
          /* check package */
          $package = $user->get_package($transfer_request['package_id']);
          if (!$package) {
            _error(400);
          }
          /* update user package */
          $user->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled'], $transfer_request['user_id']);
          /* log payment */
          $user->log_payment($transfer_request['user_id'], $package['price'], 'bank', 'packages');
          break;

        case 'wallet':
          /* update user wallet balance */
          $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($transfer_request['price']), secure($transfer_request['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* wallet transaction */
          $user->wallet_set_transaction($transfer_request['user_id'], 'recharge', 0, $transfer_request['price'], 'in');
          /* log payment */
          $user->log_payment($transfer_request['user_id'], $transfer_request['price'], 'bank', 'wallet');
          break;

        case 'donate':
          /* funding donation */
          $user->funding_donation($transfer_request['post_id'], $transfer_request['price'], $transfer_request['user_id']);
          /* log payment */
          $user->log_payment($transfer_request['user_id'], $transfer_request['price'], 'bank', 'donate');
          break;

        case 'subscribe':
          /* get monetization plan */
          $monetization_plan = $user->get_monetization_plan($transfer_request['plan_id'], true);
          if (!$monetization_plan) {
            _error(400);
          }
          /* subscribe to node */
          $user->subscribe($transfer_request['plan_id'], $transfer_request['user_id']);
          /* log payment */
          $user->log_payment($transfer_request['user_id'], $transfer_request['price'], 'bank', 'subscribe');
          break;

        case 'paid_post':
          /* unlock paid post */
          $user->unlock_paid_post($transfer_request['post_id'], $transfer_request['user_id']);
          /* log payment */
          $user->log_payment($transfer_request['user_id'], $transfer_request['price'], 'bank', 'paid_post');
          break;

        case 'movies':
          /* get movie */
          $movie = $user->get_movie($transfer_request['movie_id']);
          /* movie payment */
          $user->movie_payment($transfer_request['movie_id'], $transfer_request['user_id']);
          /* log payment */
          $user->log_payment($transfer_request['user_id'], $transfer_request['price'], 'bank', 'movies');
          break;

        default:
          _error(400);
          break;
      }
      /* approve request */
      $db->query(sprintf("UPDATE bank_transfers SET status = '1' WHERE transfer_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* notify the user */
      $user->post_notification(['to_user_id' => $transfer_request['user_id'], 'action' => 'bank_transfer_approved']);
      break;

    case 'decline':
      /* decline request */
      $db->query(sprintf("UPDATE bank_transfers SET status = '-1' WHERE transfer_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* notify the user */
      $user->post_notification(['to_user_id' => $transfer_request['user_id'], 'action' => 'bank_transfer_declined']);
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
