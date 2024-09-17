<?php

/**
 * webhooks -> coinpayments
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../bootstrap.php');

try {

  // handle the transaction
  $transaction = $user->get_coinpayments_transaction($_POST['custom'], $_POST['txn_id']);
  if ($transaction) {
    switch ($_GET['handle']) {
      case 'packages':
        // valid inputs
        if (!isset($_GET['package_id']) || !is_numeric($_GET['package_id'])) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [package_id is not set]", '-1');
        }

        // get package
        $package = $user->get_package($_GET['package_id']);
        if (!$package) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [Package is invalid or not exist]", '-1');
        }

        // check payment
        $payment = $user->check_coinpayments_payment($transaction['transaction_id']);
        if ($payment) {
          /* update user package */
          $user->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled'], $transaction['user_id']);
          /* update coinpayments transaction */
          $user->update_coinpayments_transaction($transaction['transaction_id'], __("Transaction complete successfully"), '2');
          /* notify the user */
          $user->post_notification(['to_user_id' => $transaction['user_id'], 'from_user_id' => '1', 'action' => 'coinpayments_complete']);
          /* log payment */
          $user->log_payment($transaction['user_id'], $package['price'], 'coinpayments', 'packages');
        }
        break;

      case 'wallet':
        // check payment
        $payment = $user->check_coinpayments_payment($transaction['transaction_id']);
        if ($payment) {
          /* update user wallet balance */
          $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($transaction['amount']), secure($transaction['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* wallet transaction */
          $user->wallet_set_transaction($transaction['user_id'], 'recharge', 0, $transaction['amount'], 'in');
          /* update coinpayments transaction */
          $user->update_coinpayments_transaction($transaction['transaction_id'], __("Transaction complete successfully"), '2');
          /* notify the user */
          $user->post_notification(['to_user_id' => $transaction['user_id'], 'from_user_id' => '1', 'action' => 'coinpayments_complete']);
          /* log payment */
          $user->log_payment($transaction['user_id'], $transaction['amount'], 'coinpayments', 'wallet');
        }
        break;

      case 'donate':
        // valid inputs
        if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [post_id is not set]", '-1');
        }

        // check payment
        $payment = $user->check_coinpayments_payment($transaction['transaction_id']);
        if ($payment) {
          /* funding donation */
          $user->funding_donation($_GET['post_id'], $transaction['amount'], $transaction['user_id']);
          /* update coinpayments transaction */
          $user->update_coinpayments_transaction($transaction['transaction_id'], __("Transaction complete successfully"), '2');
          /* notify the user */
          $user->post_notification(['to_user_id' => $transaction['user_id'], 'from_user_id' => '1', 'action' => 'coinpayments_complete']);
          /* log payment */
          $user->log_payment($transaction['user_id'], $transaction['amount'], 'coinpayments', 'donate');
        }
        break;

      case 'subscribe':
        // valid inputs
        if (!isset($_GET['plan_id']) || !is_numeric($_GET['plan_id'])) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [node_id is not set]", '-1');
        }

        // get monetization plan
        $monetization_plan = $user->get_monetization_plan($_GET['plan_id'], true);
        if (!$monetization_plan) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [monetization plan is invalid or not exist]", '-1');
        }

        // check payment
        $payment = $user->check_coinpayments_payment($transaction['transaction_id']);
        if ($payment) {
          /* subscribe to node */
          $user->subscribe($_GET['plan_id'], $transaction['user_id']);
          /* update coinpayments transaction */
          $user->update_coinpayments_transaction($transaction['transaction_id'], __("Transaction complete successfully"), '2');
          /* notify the user */
          $user->post_notification(['to_user_id' => $transaction['user_id'], 'from_user_id' => '1', 'action' => 'coinpayments_complete']);
          /* log payment */
          $user->log_payment($transaction['user_id'], $monetization_plan['amount'], 'coinpayments', 'subscribe');
        }
        break;

      case 'paid_post':
        // valid inputs
        if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [node_id is not set]", '-1');
        }

        // get post
        $post = $user->get_post($_GET['post_id'], false, true, true);
        if (!$post) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [monetization plan is invalid or not exist]", '-1');
        }

        // check payment
        $payment = $user->check_coinpayments_payment($transaction['transaction_id']);
        if ($payment) {
          /* unlock paid post */
          $post_link = $user->unlock_paid_post($_GET['post_id'], $transaction['user_id']);
          /* update coinpayments transaction */
          $user->update_coinpayments_transaction($transaction['transaction_id'], __("Transaction complete successfully"), '2');
          /* notify the user */
          $user->post_notification(['to_user_id' => $transaction['user_id'], 'from_user_id' => '1', 'action' => 'coinpayments_complete']);
          /* log payment */
          $user->log_payment($transaction['user_id'], $post['paid_price'], 'coinpayments', 'paid_post');
        }
        break;

      case 'movies':
        // valid inputs
        if (!isset($_GET['movie_id']) || !is_numeric($_GET['movie_id'])) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [node_id is not set]", '-1');
        }

        // get movie
        $movie = $user->get_movie($_GET['movie_id']);
        if (!$movie) {
          $user->update_coinpayments_transaction($transaction['transaction_id'], "Error (400): Bad Reuqeust [movie is invalid or not exist]", '-1');
        }

        // check payment
        $payment = $user->check_coinpayments_payment($transaction['transaction_id']);
        if ($payment) {
          /* movie payment */
          $user->movie_payment($_GET['movie_id']);
          /* update coinpayments transaction */
          $user->update_coinpayments_transaction($transaction['transaction_id'], __("Transaction complete successfully"), '2');
          /* notify the user */
          $user->post_notification(['to_user_id' => $transaction['user_id'], 'from_user_id' => '1', 'action' => 'coinpayments_complete']);
          /* log payment */
          $user->log_payment($transaction['user_id'], $movie['price'], 'coinpayments', 'movies');
        }
        break;
    }
  }
} catch (Exception $e) {
  /* do nothing */
}
