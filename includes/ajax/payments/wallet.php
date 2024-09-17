<?php

/**
 * ajax -> payments -> wallet
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// user access
user_access(true, true);

try {

  switch ($_REQUEST['do']) {
    case 'wallet_transfer':
      // process
      $user->wallet_transfer($_POST['send_to_id'], $_POST['amount']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_transfer_succeed"'));
      break;

    case 'send_tip':
      // process
      $user->wallet_send_tip($_POST['send_to_id'], $_POST['amount']);

      // return
      modal("SUCCESS", __("Thanks"), __("Tip Sent Successfully"));
      break;

    case 'wallet_replenish':
      // valid inputs
      if (!isset($_POST['amount']) || !is_numeric($_POST['amount'])) {
        throw new Exception(__("Enter valid amount of money for example '50'"));
      }

      // return
      modal("#payment", "{'handle': 'wallet', 'price': '" . $_POST['amount'] . "', 'vat': '" . get_payment_vat_value($_POST['amount']) . "', 'fees': '" . get_payment_fees_value($_POST['amount']) . "', 'total': '" . get_payment_total_value($_POST['amount']) . "', 'total_printed': '" . get_payment_total_value($_POST['amount'], true) . "'}");
      break;

    case 'wallet_withdraw_affiliates':
      // process
      $user->wallet_withdraw_affiliates($_POST['amount']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_withdraw_affiliates_succeed"'));
      break;

    case 'wallet_withdraw_points':
      // process
      $user->wallet_withdraw_points($_POST['amount']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_withdraw_points_succeed"'));
      break;

    case 'wallet_withdraw_market':
      // process
      $user->wallet_withdraw_market($_POST['amount']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_withdraw_market_succeed"'));
      break;

    case 'wallet_withdraw_funding':
      // process
      $user->wallet_withdraw_funding($_POST['amount']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_withdraw_funding_succeed"'));
      break;

    case 'wallet_withdraw_monetization':
      // process
      $user->wallet_withdraw_monetization($_POST['amount']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_withdraw_monetization_succeed"'));
      break;

    case 'wallet_package_payment':
      // process
      $user->wallet_package_payment($_POST['package_id']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_package_payment_succeed"'));
      break;

    case 'wallet_monetization_payment':
      // process
      $user->wallet_monetization_payment($_POST['plan_id']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_monetization_payment_succeed"'));
      break;

    case 'wallet_paid_post':
      // process
      $user->wallet_paid_post($_POST['post_id']);

      // return
      return_json(array('callback' => 'window.location = site_path + "/wallet?wallet_paid_post_succeed"'));
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
