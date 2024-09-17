<?php

/**
 * ajax -> payments -> trial
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

  switch ($_POST['type']) {
    case 'package':
      // valid inputs
      if (!isset($_POST['package_id']) || !is_numeric($_POST['package_id'])) {
        _error(400);
      }

      // check package
      $package = $user->get_package($_POST['package_id']);
      if (!$package) {
        _error(400);
      }
      /* check if user already subscribed to this package */
      if ($user->_data['user_subscribed'] && $user->_data['user_package'] == $package['package_id']) {
        modal("SUCCESS", __("Subscribed"), __("You already subscribed to this package, Please select different package"));
      }
      /* check if user has subscribed to this package before */
      if ($user->_data['user_free_tried']) {
        modal("ERROR", __("Sorry"), __("You already subscribed to this free trial package, Please select different package"));
      }
      /* check if this package not free */
      if ($package['price'] != 0) {
        modal("ERROR", __("Error"), __("Sorry this package is not free!"));
      }

      // update user package
      $user->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled']);

      // return
      return_json(array('callback' => 'window.location.href = "' . $system['system_url'] . '/upgraded";'));
      break;

    case 'monetization_plan':
      // valid inputs
      if (!isset($_POST['plan_id']) || !is_numeric($_POST['plan_id'])) {
        _error(400);
      }

      // get plan
      $monetization_plan = $user->get_monetization_plan($_POST['plan_id'], true);
      if (!$monetization_plan) {
        _error(400);
      }
      /* check if this plan not free */
      if ($monetization_plan['price'] != 0) {
        modal("ERROR", __("Error"), __("Sorry this plan is not free!"));
      }
      /* check if user already sneak peaked to this node */
      if ($user->has_sneak_peak($monetization_plan['node_id'], $monetization_plan['node_type'])) {
        modal("ERROR", __("Error"), __("You already sneak peaked to this free plan, Please select different plan"));
      }

      // sneak peak
      $user->sneak_peak($monetization_plan['node_id'], $monetization_plan['node_type']);

      // subscribe to node
      $node_link = $user->subscribe($monetization_plan['plan_id']);

      // return
      return_json(array('callback' => 'window.location.href = "' . $node_link . '";'));
      break;
  }
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
