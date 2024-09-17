<?php

/**
 * webhooks -> moneypoolscash
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

// Get the query string
parse_str(str_replace('?', '&', $_SERVER['QUERY_STRING']), $QUERY_ARRAY);

try {
  switch ($QUERY_ARRAY['handle']) {
    case 'packages':
      // valid inputs
      if (!isset($QUERY_ARRAY['package_id']) || !is_numeric($QUERY_ARRAY['package_id'])) {
        _error(404);
      }

      // get package
      $package = $user->get_package($QUERY_ARRAY['package_id']);
      if (!$package) {
        _error(404);
      }

      // check payment
      $payment = moneypoolscash_check();
      if ($payment) {
        /* update user package */
        $user->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $package['price'], 'moneypoolscash', 'packages');
        /* redirect */
        redirect("/upgraded");
      }
      break;

    case 'wallet':
      // check payment
      $payment = moneypoolscash_check();
      if ($payment) {
        /* update user wallet balance */
        $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($_SESSION['wallet_replenish_amount']), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* wallet transaction */
        $user->wallet_set_transaction($user->_data['user_id'], 'recharge', 0, $_SESSION['wallet_replenish_amount'], 'in');
        /* log payment */
        $user->log_payment($user->_data['user_id'], $_SESSION['wallet_replenish_amount'], 'moneypoolscash', 'wallet');
        /* redirect */
        redirect("/wallet?wallet_replenish_succeed");
      }
      break;

    case 'donate':
      // valid inputs
      if (!isset($QUERY_ARRAY['post_id']) || !is_numeric($QUERY_ARRAY['post_id'])) {
        _error(404);
      }

      // check payment
      $payment = moneypoolscash_check();
      if ($payment) {
        /* funding donation */
        $user->funding_donation($QUERY_ARRAY['post_id'], $_SESSION['donation_amount']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $_SESSION['donation_amount'], 'moneypoolscash', 'donate');
        /* redirect */
        redirect("/posts/" . $QUERY_ARRAY['post_id']);
      }
      break;

    case 'subscribe':
      // valid inputs
      if (!isset($QUERY_ARRAY['plan_id']) || !is_numeric($QUERY_ARRAY['plan_id'])) {
        _error(404);
      }

      // get monetization plan
      $monetization_plan = $user->get_monetization_plan($QUERY_ARRAY['plan_id'], true);
      if (!$monetization_plan) {
        _error(404);
      }

      // check payment
      $payment = moneypoolscash_check();
      if ($payment) {
        /* subscribe to node */
        $node_link = $user->subscribe($QUERY_ARRAY['plan_id']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $monetization_plan['price'], 'moneypoolscash', 'subscribe');
        /* redirect */
        redirect($node_link);
      }
      break;

    case 'paid_post':
      // valid inputs
      if (!isset($QUERY_ARRAY['post_id']) || !is_numeric($QUERY_ARRAY['post_id'])) {
        _error(404);
      }

      // get paid post
      $post = $user->get_paid_post($QUERY_ARRAY['post_id'], false, false, true);
      if (!$post) {
        _error(404);
      }

      // check payment
      $payment = moneypoolscash_check();
      if ($payment) {
        /* unlock paid post */
        $post_link = $user->unlock_paid_post($QUERY_ARRAY['post_id']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $post['paid_price'], 'moneypoolscash', 'paid_post');
        /* redirect */
        redirect($post_link);
      }
      break;

    case 'movies':
      // valid inputs
      if (!isset($QUERY_ARRAY['movie_id']) || !is_numeric($QUERY_ARRAY['movie_id'])) {
        _error(404);
      }

      // get movie
      $movie = $user->get_movie($QUERY_ARRAY['movie_id']);
      if (!$movie) {
        _error(404);
      }

      // check payment
      $payment = moneypoolscash_check();
      if ($payment) {
        /* movie payment */
        $movie_link = $user->movie_payment($QUERY_ARRAY['movie_id']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $movie['price'], 'moneypoolscash', 'movies');
        /* redirect */
        redirect($movie_link);
      }
      break;

    default:
      _error(404);
      break;
  }
  redirect();
} catch (Exception $e) {
  _error('System Message', $e->getMessage());
}
