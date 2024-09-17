<?php

/**
 * webhooks -> stripe subscriptions webhook
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('../bootloader.php');

try {

  // check if stripe is enabled
  if (!$system['creditcard_enabled'] && !$system['alipay_enabled']) {
    throw new Exception(__("Stripe is not enabled"));
  }

  // init stripe
  $stripe = new \Stripe\StripeClient(($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret']);

  // get the webhook notification data
  $payload = file_get_contents('php://input');

  // construct the event
  $event = \Stripe\Webhook::constructEvent(
    $payload,
    $_SERVER['HTTP_STRIPE_SIGNATURE'],
    $system['stripe_webhook']
  );

  // Handle the event
  switch ($event->type) {
    case 'checkout.session.completed':

      // get the recurring payment details
      $recurring_payment = $user->get_recurring_payment('stripe', $event->data->object->customer);
      if (!$recurring_payment) {
        throw new Exception('Invalid recurring payment');
      }

      // check payment
      switch ($recurring_payment['handle']) {
        case 'packages':
          // get package
          $package = $user->get_package($recurring_payment['handle_id']);
          if (!$package) {
            throw new Exception('Package has been deleted');
          }
          // update user package
          $user->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled'], $recurring_payment['user_id']);
          // log payment
          $user->log_payment($recurring_payment['user_id'], $package['price'], 'stripe', 'packages');
          break;

        case 'subscribe':
          // get monetization plan
          $monetization_plan = $user->get_monetization_plan($recurring_payment['handle_id'], true);
          if (!$monetization_plan) {
            throw new Exception('Monetization plan has been deleted');
          }
          // subscribe
          $user->subscribe($recurring_payment['handle_id'], $recurring_payment['user_id']);
          // log payment
          $user->log_payment($recurring_payment['user_id'], $monetization_plan['price'], 'stripe', 'subscribe');
          break;
      }
      break;

    default:
      throw new Exception('Unhandled event type: ' . $event->type);
  }
} catch (Exception $e) {
  // return error with 200 status code
  header("HTTP/1.1 200 OK");
  header('Content-Type: application/json');
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
