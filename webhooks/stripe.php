<?php

/**
 * webhooks -> stripe
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('../bootloader.php');

// user access (simple)
if (!$user->_logged_in) {
  user_login();
}

try {
  if ($_GET['status'] == 'success') {
    switch ($_GET['handle']) {
      case 'packages':
        // valid inputs
        if (!isset($_GET['package_id']) || !is_numeric($_GET['package_id'])) {
          _error(404);
        }

        // get package
        $package = $user->get_package($_GET['package_id']);
        if (!$package) {
          _error(404);
        }

        // check payment
        $payment = stripe_payment_check();
        if ($payment !== false) {
          /* check if the payment is recurring */
          if ($payment->mode == 'subscription' && $package['stripe_billing_plan']) {
            /* insert the recurring payments */
            $user->insert_recurring_payments('stripe', 'packages', $_GET['package_id'], $payment->customer);
          }
          /* update user package */
          $user->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled']);
          /* log payment */
          $user->log_payment($user->_data['user_id'], $package['price'], 'stripe', 'packages');
          /* redirect */
          redirect("/upgraded");
        }
        break;

      case 'wallet':
        // check payment
        $payment = stripe_payment_check();
        if ($payment !== false) {
          /* update user wallet balance */
          $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($_SESSION['wallet_replenish_amount']), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* wallet transaction */
          $user->wallet_set_transaction($user->_data['user_id'], 'recharge', 0, $_SESSION['wallet_replenish_amount'], 'in');
          /* log payment */
          $user->log_payment($user->_data['user_id'], $_SESSION['wallet_replenish_amount'], 'stripe', 'wallet');
          /* redirect */
          redirect("/wallet?wallet_replenish_succeed");
        }
        break;

      case 'donate':
        // valid inputs
        if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
          _error(404);
        }

        // check payment
        $payment = stripe_payment_check();
        if ($payment !== false) {
          /* funding donation */
          $user->funding_donation($_GET['post_id'], $_SESSION['donation_amount']);
          /* log payment */
          $user->log_payment($user->_data['user_id'], $_SESSION['donation_amount'], 'stripe', 'donate');
          /* redirect */
          redirect("/posts/" . $_GET['post_id']);
        }
        break;

      case 'subscribe':
        // valid inputs
        if (!isset($_GET['plan_id']) || !is_numeric($_GET['plan_id'])) {
          _error(404);
        }

        // get monetization plan
        $monetization_plan = $user->get_monetization_plan($_GET['plan_id'], true);
        if (!$monetization_plan) {
          _error(404);
        }

        // check payment
        $payment = stripe_payment_check();
        if ($payment !== false) {
          /* check if the payment is recurring */
          if ($payment->mode == 'subscription' && $monetization_plan['stripe_billing_plan']) {
            /* insert the recurring payments */
            $user->insert_recurring_payments('stripe', 'subscribe', $_GET['plan_id'], $payment->customer);
          }
          /* subscribe to node */
          $node_link = $user->subscribe($_GET['plan_id']);
          /* log payment */
          $user->log_payment($user->_data['user_id'], $monetization_plan['price'], 'stripe', 'subscribe');
          /* redirect */
          redirect($node_link);
        }
        break;

      case 'paid_post':
        // valid inputs
        if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
          _error(404);
        }

        // get post
        $post = $user->get_post($_GET['post_id'], false, false, true);
        if (!$post) {
          _error(404);
        }

        // check payment
        $payment = stripe_payment_check();
        if ($payment !== false) {
          /* unlock paid post */
          $post_link = $user->unlock_paid_post($_GET['post_id']);
          /* log payment */
          $user->log_payment($user->_data['user_id'], $post['post_price'], 'stripe', 'paid_post');
          /* redirect */
          redirect($post_link);
        }
        break;

      case 'movies':
        // valid inputs
        if (!isset($_GET['movie_id']) || !is_numeric($_GET['movie_id'])) {
          _error(404);
        }

        // get movie
        $movie = $user->get_movie($_GET['movie_id']);
        if (!$movie) {
          _error(404);
        }

        // check payment
        $payment = stripe_payment_check();
        if ($payment !== false) {
          /* movie payment */
          $movie_link = $user->movie_payment($_GET['movie_id']);
          /* log payment */
          $user->log_payment($user->_data['user_id'], $movie['price'], 'stripe', 'movies');
          /* redirect */
          redirect($movie_link);
        }
        break;

      default:
        _error(404);
        break;
    }
  }
  redirect();
} catch (Exception $e) {
  _error('System Message', $e->getMessage());
}
