<?php

/**
 * ajax -> payments -> razorpay
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

// check if Razorpay enabled
if (!$system['razorpay_enabled']) {
  modal("MESSAGE", __("Error"), __("This feature has been disabled by the admin"));
}

try {

  switch ($_POST['handle']) {
    case 'packages':
      // valid inputs
      if (!isset($_POST['razorpay_payment_id'])) {
        _error(400);
      }
      if (!isset($_POST['package_id']) || !is_numeric($_POST['package_id'])) {
        _error(400);
      }

      // get package
      $package = $user->get_package($_POST['package_id']);
      if (!$package) {
        _error(400);
      }

      // check payment
      $payment = razorpay_check($_POST['razorpay_payment_id'], $package['price']);
      if ($payment) {
        /* update user package */
        $user->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $package['price'], 'razorpay', 'packages');
        /* return */
        return_json(array('callback' => 'window.location.href = "' . $system['system_url'] . '/upgraded";'));
      }
      break;

    case 'wallet':
      // valid inputs
      if (!isset($_POST['razorpay_payment_id'])) {
        _error(400);
      }
      if (!isset($_POST['price']) || !is_numeric($_POST['price'])) {
        _error(400);
      }

      // check payment
      $payment = razorpay_check($_POST['razorpay_payment_id'], $_POST['price']);
      if ($payment) {
        /* update user wallet balance */
        $_SESSION['wallet_replenish_amount'] = $_POST['price'];
        $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($_SESSION['wallet_replenish_amount']), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* wallet transaction */
        $user->wallet_set_transaction($user->_data['user_id'], 'recharge', 0, $_SESSION['wallet_replenish_amount'], 'in');
        /* log payment */
        $user->log_payment($user->_data['user_id'], $_SESSION['wallet_replenish_amount'], 'razorpay', 'wallet');
        /* redirect*/
        return_json(array('callback' => 'window.location.href = "' . $system['system_url'] . '/wallet?wallet_replenish_succeed";'));
      }
      break;

    case 'donate':
      // valid inputs
      if (!isset($_POST['razorpay_payment_id'])) {
        _error(400);
      }
      if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        _error(400);
      }
      if (!isset($_POST['price']) || !is_numeric($_POST['price'])) {
        _error(400);
      }

      // check payment
      $payment = razorpay_check($_POST['razorpay_payment_id'], $_POST['price']);
      if ($payment) {
        /* funding donation */
        $user->funding_donation($_POST['post_id'], $_POST['price']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $_POST['price'], 'razorpay', 'donate');
        /* redirect */
        return_json(array('callback' => 'window.location.href = "' . $system['system_url'] . '/posts/' . $_POST['post_id'] . '";'));
      }
      break;

    case 'subscribe':
      // valid inputs
      if (!isset($_POST['razorpay_payment_id'])) {
        _error(400);
      }

      // get monetization plan
      $monetization_plan = $user->get_monetization_plan($_POST['plan_id'], true);
      if (!$monetization_plan) {
        _error(400);
      }

      // check payment
      $payment = razorpay_check($_POST['razorpay_payment_id'], $monetization_plan['price']);
      if ($payment) {
        /* subscribe to node */
        $node_link = $user->subscribe($_POST['plan_id']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $monetization_plan['price'], 'razorpay', 'subscribe');
        /* redirect */
        return_json(array('callback' => 'window.location.href = "' . $system['system_url'] . $node_link . '";'));
      }
      break;

    case 'paid_post':
      // valid inputs
      if (!isset($_POST['razorpay_payment_id'])) {
        _error(400);
      }

      // get post
      $post = $user->get_post($_POST['post_id'], false, false, true);
      if (!$post) {
        _error(400);
      }

      // check payment
      $payment = razorpay_check($_POST['razorpay_payment_id'], $post['post_price']);
      if ($payment) {
        /* unlock paid post */
        $post_link = $user->unlock_paid_post($_POST['post_id']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $post['post_price'], 'razorpay', 'paid_post');
        /* redirect */
        return_json(array('callback' => 'window.location.href = "' . $system['system_url'] . $post_link . '";'));
      }
      break;

    case 'movies':
      // valid inputs
      if (!isset($_POST['razorpay_payment_id'])) {
        _error(400);
      }

      // get movie
      $movie = $user->get_movie($_POST['movie_id']);
      if (!$movie) {
        _error(400);
      }

      // check payment
      $payment = razorpay_check($_POST['razorpay_payment_id'], $movie['price']);
      if ($payment) {
        /* movie payment */
        $movie_link = $user->movie_payment($movie['movie_id']);
        /* log payment */
        $user->log_payment($user->_data['user_id'], $movie['price'], 'razorpay', 'movies');
        /* redirect */
        return_json(array('callback' => 'window.location.href = "' . $system['system_url'] . $movie_link . '";'));
      }
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
