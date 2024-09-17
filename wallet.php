<?php

/**
 * wallet
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// check if wallet enabled
if (!$system['wallet_enabled']) {
  _error(404);
}

// user access
user_access();

try {

  // get view content
  switch ($_GET['view']) {
    case '':
      // page header
      page_header(__("Wallet") . ' | ' . __($system['system_title']));

      // get wallet notifications
      if (isset($_GET['wallet_transfer_succeed']) && isset($_SESSION['wallet_transfer_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_transfer_amount', $_SESSION['wallet_transfer_amount']);
        /* unset session */
        unset($_SESSION['wallet_transfer_amount']);
      }
      if (isset($_GET['wallet_replenish_succeed']) && isset($_SESSION['wallet_replenish_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_replenish_amount', $_SESSION['wallet_replenish_amount']);
        /* unset session */
        unset($_SESSION['wallet_replenish_amount']);
      }
      if (isset($_GET['wallet_withdraw_affiliates_succeed']) && isset($_SESSION['wallet_withdraw_affiliates_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_withdraw_affiliates_amount', $_SESSION['wallet_withdraw_affiliates_amount']);
        /* unset session */
        unset($_SESSION['wallet_withdraw_affiliates_amount']);
      }
      if (isset($_GET['wallet_withdraw_points_succeed']) && isset($_SESSION['wallet_withdraw_points_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_withdraw_points_amount', $_SESSION['wallet_withdraw_points_amount']);
        /* unset session */
        unset($_SESSION['wallet_withdraw_points_amount']);
      }
      if (isset($_GET['wallet_withdraw_market_succeed']) && isset($_SESSION['wallet_withdraw_market_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_withdraw_market_amount', $_SESSION['wallet_withdraw_market_amount']);
        /* unset session */
        unset($_SESSION['wallet_withdraw_market_amount']);
      }
      if (isset($_GET['wallet_withdraw_funding_succeed']) && isset($_SESSION['wallet_withdraw_funding_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_withdraw_funding_amount', $_SESSION['wallet_withdraw_funding_amount']);
        /* unset session */
        unset($_SESSION['wallet_withdraw_funding_amount']);
      }
      if (isset($_GET['wallet_withdraw_monetization_succeed']) && isset($_SESSION['wallet_withdraw_monetization_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_withdraw_monetization_amount', $_SESSION['wallet_withdraw_monetization_amount']);
        /* unset session */
        unset($_SESSION['wallet_withdraw_monetization_amount']);
      }
      if (isset($_GET['wallet_package_payment_succeed']) && isset($_SESSION['wallet_package_payment_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_package_payment_amount', $_SESSION['wallet_package_payment_amount']);
        /* unset session */
        unset($_SESSION['wallet_package_payment_amount']);
      }
      if (isset($_GET['wallet_monetization_payment_succeed']) && isset($_SESSION['wallet_monetization_payment_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_monetization_payment_amount', $_SESSION['wallet_monetization_payment_amount']);
        /* unset session */
        unset($_SESSION['wallet_monetization_payment_amount']);
      }
      if (isset($_GET['wallet_paid_post_succeed']) && isset($_SESSION['wallet_paid_post_amount'])) {
        /* assign variables */
        $smarty->assign('wallet_paid_post_amount', $_SESSION['wallet_paid_post_amount']);
        /* unset session */
        unset($_SESSION['wallet_paid_post_amount']);
      }

      // get wallet transactions
      $transactions = $user->wallet_get_transactions();
      /* assign variables */
      $smarty->assign('transactions', $transactions);
      break;

    case 'payments':
      // check if wallet withdrawal enabled
      if (!$system['wallet_withdrawal_enabled']) {
        _error(404);
      }

      // page header
      page_header(__("Wallet Payments") . ' | ' . __($system['system_title']));

      // get payments
      $payments = $user->wallet_get_payments();
      /* assign variables */
      $smarty->assign('payments', $payments);
      break;

    default:
      _error(404);
      break;
  }
  /* assign variables */
  $smarty->assign('view', $_GET['view']);
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page footer
page_footer('wallet');
