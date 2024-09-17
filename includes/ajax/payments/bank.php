<?php

/**
 * ajax -> payments -> bank
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

// check if bank transfers enabled
if (!$system['bank_transfers_enabled']) {
  modal("MESSAGE", __("Error"), __("This feature has been disabled by the admin"));
}

try {

  switch ($_POST['handle']) {
    case 'packages':
      // valid inputs
      if (!isset($_POST['bank_receipt']) || is_empty($_POST['bank_receipt'])) {
        throw new Exception(__("Please attach your bank receipt"));
      }
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }

      // get package
      $package = $user->get_package($_POST['id']);
      if (!$package) {
        _error(400);
      }
      /* check if user already subscribed to this package */
      if ($user->_data['user_subscribed'] && $user->_data['user_package'] == $package['package_id']) {
        modal("SUCCESS", __("Subscribed"), __("You already subscribed to this package, Please select different package"));
      }

      // process
      $db->query(sprintf("INSERT INTO bank_transfers (user_id, handle, package_id, bank_receipt, time) VALUES (%s, 'packages', %s, %s, %s)", secure($user->_data['user_id'], 'int'), secure($_POST['id'], 'int'), secure($_POST['bank_receipt']), secure($date))) or _error('SQL_ERROR_THROWEN');

      // send notification to admins
      $user->notify_system_admins("bank_transfer");

      // return
      modal("SUCCESS", __("Thanks"), __("Your request has been successfully sent, we will notify you once it's approved"));
      break;

    case 'wallet':
      // valid inputs
      if (!isset($_POST['bank_receipt']) || is_empty($_POST['bank_receipt'])) {
        throw new Exception(__("Please attach your bank receipt"));
      }
      if (!isset($_POST['price']) || !is_numeric($_POST['price'])) {
        _error(400);
      }

      // process
      $db->query(sprintf("INSERT INTO bank_transfers (user_id, handle, price, bank_receipt, time) VALUES (%s, 'wallet', %s, %s, %s)", secure($user->_data['user_id'], 'int'), secure($_POST['price']), secure($_POST['bank_receipt']), secure($date))) or _error('SQL_ERROR_THROWEN');

      // send notification to admins
      $user->notify_system_admins("bank_transfer");

      // return
      modal("SUCCESS", __("Thanks"), __("Your request has been successfully sent, we will notify you once it's approved"));
      break;

    case 'donate':
      // valid inputs
      if (!isset($_POST['bank_receipt']) || is_empty($_POST['bank_receipt'])) {
        throw new Exception(__("Please attach your bank receipt"));
      }
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }
      if (!isset($_POST['price']) || !is_numeric($_POST['price'])) {
        _error(400);
      }

      // process
      $db->query(sprintf("INSERT INTO bank_transfers (user_id, handle, post_id, price, bank_receipt, time) VALUES (%s, 'donate', %s, %s, %s, %s)", secure($user->_data['user_id'], 'int'), secure($_POST['id'], 'int'), secure($_POST['price']), secure($_POST['bank_receipt']), secure($date))) or _error('SQL_ERROR_THROWEN');

      // send notification to admins
      $user->notify_system_admins("bank_transfer");

      // return
      modal("SUCCESS", __("Thanks"), __("Your request has been successfully sent, we will notify you once it's approved"));
      break;

    case 'subscribe':
      // valid inputs
      if (!isset($_POST['bank_receipt']) || is_empty($_POST['bank_receipt'])) {
        throw new Exception(__("Please attach your bank receipt"));
      }
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }

      // get plan
      $monetization_plan = $user->get_monetization_plan($_POST['id'], true);
      if (!$monetization_plan) {
        _error(400);
      }
      /* check if user already subscribed to this node */
      if ($user->is_subscribed($monetization_plan['node_id'], $monetization_plan['node_type'])) {
        modal("SUCCESS", __("Subscribed"), __("You already subscribed to this") . " " . __($monetization_plan['node_type']));
      }

      // process
      $db->query(sprintf("INSERT INTO bank_transfers (user_id, handle, plan_id, price, bank_receipt, time) VALUES (%s, 'subscribe', %s, %s, %s, %s)", secure($user->_data['user_id'], 'int'), secure($_POST['id'], 'int'), secure($monetization_plan['price']), secure($_POST['bank_receipt']), secure($date))) or _error('SQL_ERROR_THROWEN');

      // send notification to admins
      $user->notify_system_admins("bank_transfer");

      // return
      modal("SUCCESS", __("Thanks"), __("Your request has been successfully sent, we will notify you once it's approved"));
      break;

    case 'paid_post':
      // valid inputs
      if (!isset($_POST['bank_receipt']) || is_empty($_POST['bank_receipt'])) {
        throw new Exception(__("Please attach your bank receipt"));
      }
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }

      // get post
      $post = $user->get_post($_POST['id'], false, false, true);
      if (!$post) {
        throw new Exception(__("This post is not available"));
      }
      if (!$post['needs_payment']) {
        throw new Exception(__("This post doesn't need payment"));
      }

      // process
      $db->query(sprintf("INSERT INTO bank_transfers (user_id, handle, post_id, price, bank_receipt, time) VALUES (%s, 'paid_post', %s, %s, %s, %s)", secure($user->_data['user_id'], 'int'), secure($_POST['id'], 'int'), secure($post['post_price']), secure($_POST['bank_receipt']), secure($date))) or _error('SQL_ERROR_THROWEN');

      // send notification to admins
      $user->notify_system_admins("bank_transfer");

      // return
      modal("SUCCESS", __("Thanks"), __("Your request has been successfully sent, we will notify you once it's approved"));
      break;

    case 'movies':
      // valid inputs
      if (!isset($_POST['bank_receipt']) || is_empty($_POST['bank_receipt'])) {
        throw new Exception(__("Please attach your bank receipt"));
      }
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }

      // get movie
      $movie = $user->get_movie($_POST['id']);
      /* check if user already paid to this movie */
      if ($movie['can_watch']) {
        modal("SUCCESS", __("Paid"), __("You already paid to this movie"));
      }

      // process
      $db->query(sprintf("INSERT INTO bank_transfers (user_id, handle, movie_id, price, bank_receipt, time) VALUES (%s, 'movies', %s, %s, %s, %s)", secure($user->_data['user_id'], 'int'), secure($_POST['id'], 'int'), secure($movie['price']), secure($_POST['bank_receipt']), secure($date))) or _error('SQL_ERROR_THROWEN');

      // send notification to admins
      $user->notify_system_admins("bank_transfer");

      // return
      modal("SUCCESS", __("Thanks"), __("Your request has been successfully sent, we will notify you once it's approved"));
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
