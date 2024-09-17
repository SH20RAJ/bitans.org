<?php

/**
 * ajax -> admin -> monetization
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

// handle pro
try {

  switch ($_GET['do']) {
    case 'settings':
      /* valid inputs */
      if (!is_numeric($_POST['monetization_commission']) || $_POST['monetization_commission'] < 0 || $_POST['monetization_commission'] >= 100) {
        throw new Exception(__("Please enter valid commission >= 0 and < 100"));
      }
      /* prepare */
      $_POST['monetization_enabled'] = (isset($_POST['monetization_enabled'])) ? '1' : '0';
      $_POST['monetization_wallet_payment_enabled'] = (isset($_POST['monetization_wallet_payment_enabled'])) ? '1' : '0';
      $_POST['monetization_money_withdraw_enabled'] = (isset($_POST['monetization_money_withdraw_enabled'])) ? '1' : '0';
      $_POST['monetization_money_transfer_enabled'] = (isset($_POST['monetization_money_transfer_enabled'])) ? '1' : '0';
      if (!$_POST['monetization_money_withdraw_enabled'] && !$_POST['monetization_money_transfer_enabled']) {
        throw new Exception(__("You must enable one method at least, either payments withdrawal requests or wallet transfer"));
      }
      $monetization_payment_methods = [];
      if (isset($_POST['method_paypal'])) {
        $monetization_payment_methods[] = "paypal";
      }
      if (isset($_POST['method_skrill'])) {
        $monetization_payment_methods[] = "skrill";
      }
      if (isset($_POST['method_moneypoolscash'])) {
        $monetization_payment_methods[] = "moneypoolscash";
      }
      if (isset($_POST['method_bank'])) {
        $monetization_payment_methods[] = "bank";
      }
      if (isset($_POST['method_custom'])) {
        $monetization_payment_methods[] = "custom";
      }
      if ($_POST['monetization_money_withdraw_enabled'] && count($monetization_payment_methods) == 0) {
        throw new Exception(__("You must select one withdrawal payment method at least"));
      }
      $monetization_payment_method = implode(",", $monetization_payment_methods);
      /* update */
      update_system_options([
        'monetization_enabled' => secure($_POST['monetization_enabled']),
        'monetization_wallet_payment_enabled' => secure($_POST['monetization_wallet_payment_enabled']),
        'monetization_money_withdraw_enabled' => secure($_POST['monetization_money_withdraw_enabled']),
        'monetization_payment_method' => secure($monetization_payment_method),
        'monetization_payment_method_custom' => secure($_POST['monetization_payment_method_custom']),
        'monetization_min_withdrawal' => secure($_POST['monetization_min_withdrawal']),
        'monetization_money_transfer_enabled' => secure($_POST['monetization_money_transfer_enabled']),
        'monetization_commission' => secure($_POST['monetization_commission'])
      ]);
      /* return */
      return_json(['success' => true, 'message' => __("Settings have been updated")]);
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
