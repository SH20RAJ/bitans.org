<?php

/**
 * ajax -> admin -> pro
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
      /* prepare */
      $_POST['packages_enabled'] = (isset($_POST['packages_enabled']) || $_POST['registration_type'] == "paid") ? '1' : '0';
      $_POST['packages_wallet_payment_enabled'] = (isset($_POST['packages_wallet_payment_enabled'])) ? '1' : '0';
      $_POST['packages_ads_free_enabled'] = (isset($_POST['packages_ads_free_enabled'])) ? '1' : '0';
      /* update */
      update_system_options([
        'packages_enabled' => secure($_POST['packages_enabled']),
        'packages_wallet_payment_enabled' => secure($_POST['packages_wallet_payment_enabled']),
        'packages_ads_free_enabled' => secure($_POST['packages_ads_free_enabled'])
      ]);
      /* return */
      return_json(['success' => true, 'message' => __("Settings have been updated")]);
      break;

    case 'edit':
      /* get the package */
      $package = $user->get_package($_GET['id']);
      /* prepare */
      $_POST['verification_badge_enabled'] = (isset($_POST['verification_badge_enabled'])) ? '1' : '0';
      $_POST['boost_posts_enabled'] = (isset($_POST['boost_posts_enabled'])) ? '1' : '0';
      $_POST['boost_pages_enabled'] = (isset($_POST['boost_pages_enabled'])) ? '1' : '0';
      /* valid inputs */
      if (is_empty($_POST['name'])) {
        throw new Exception(__("You have to enter the package name"));
      }
      if (is_empty($_POST['price']) || !is_numeric($_POST['price']) || $_POST['price'] < 0) {
        throw new Exception(__("You have to enter valid package price"));
      }
      if ($_POST['period'] != "life" && (is_empty($_POST['period_num']) || !is_numeric($_POST['period_num']) || $_POST['period_num'] == '0')) {
        throw new Exception(__("You have to enter valid period number"));
      }
      if ($_POST['permissions_group'] != '0' && !$user->check_permissions_group($_POST['permissions_group'])) {
        throw new Exception(__("You must select valid permissions group"));
      }
      if ($_POST['boost_posts_enabled']) {
        if (is_empty($_POST['boost_posts']) || !is_numeric($_POST['boost_posts'])) {
          throw new Exception(__("You have to enter valid boost posts number"));
        }
      } else {
        $_POST['boost_posts'] = 0;
      }
      if ($_POST['boost_pages_enabled']) {
        if (is_empty($_POST['boost_pages']) || !is_numeric($_POST['boost_pages'])) {
          throw new Exception(__("You have to enter valid boost pages number"));
        }
      } else {
        $_POST['boost_pages'] = 0;
      }
      /* PayPal billing plan */
      $paypal_billing_plan = $package['paypal_billing_plan'];
      $paypal_recurring_enabled = $system['paypal_enabled'] && !is_empty($system['paypal_webhook']);
      if ($paypal_recurring_enabled && $_POST['price'] > 0 && $_POST['period'] != "life") {
        /* check if PayPal billing plan is not created */
        if (is_empty($paypal_billing_plan)) {
          /* create PayPal billing plan */
          $paypal_billing_plan = paypal_create_billing_plan($_POST['name'], $_POST['custom_description'], $_POST['period_num'], $_POST['period'], $_POST['price']);
        } else {
          /* check if the (name || description) is edited */
          if ($package['name'] != $_POST['name'] || $package['custom_description'] != $_POST['custom_description']) {
            /* update the plan */
            paypal_edit_billing_plan($package['paypal_billing_plan'], $_POST['name'], $_POST['custom_description']);
          }
          /* check if the (period || period_num || price) is edited */
          if ($package['period'] != $_POST['period'] || $package['period_num'] != $_POST['period_num'] || $package['price'] != $_POST['price']) {
            /* replace the plan */
            $paypal_billing_plan = paypal_replace_billing_plan($package['paypal_billing_plan'], $_POST['name'], $_POST['custom_description'], $_POST['period_num'], $_POST['period'], $_POST['price']);
          }
        }
      } else {
        $paypal_billing_plan = '';
        if ($paypal_recurring_enabled && $package['paypal_billing_plan']) {
          paypal_deactivate_billing_plan($package['paypal_billing_plan']);
        }
      }
      /* Stripe billing plan */
      $stripe_billing_plan = $package['stripe_billing_plan'];
      $stripe_recurring_enabled = ($system['creditcard_enabled'] || $system['alipay_enabled']) && !is_empty($system['stripe_webhook']);
      if ($stripe_recurring_enabled && $_POST['price'] > 0 && $_POST['period'] != "life") {
        /* check if Stripe billing plan is not created */
        if (is_empty($stripe_billing_plan)) {
          /* create Stripe billing plan */
          $stripe_billing_plan = stripe_create_billing_plan($_POST['name'], $_POST['custom_description'], $_POST['period_num'], $_POST['period'], $_POST['price']);
        } else {
          /* check if the (description) is edited */
          if ($package['custom_description'] != $_POST['custom_description']) {
            /* update the plan */
            stripe_edit_billing_plan($package['stripe_billing_plan'], $_POST['custom_description']);
          }
          /* check if the (name || period || period_num || price) is edited */
          if ($package['name'] != $_POST['name'] || $package['period'] != $_POST['period'] || $package['period_num'] != $_POST['period_num'] || $package['price'] != $_POST['price']) {
            /* replace the plan */
            $stripe_billing_plan = stripe_replace_billing_plan($package['stripe_billing_plan'], $_POST['name'], $_POST['custom_description'], $_POST['period_num'], $_POST['period'], $_POST['price']);
          }
        }
      } else {
        $stripe_billing_plan = '';
        if ($stripe_recurring_enabled && $package['stripe_billing_plan']) {
          stripe_deactivate_billing_plan($package['stripe_billing_plan']);
        }
      }
      /* remove all users recurring payments */
      if ($paypal_billing_plan == '' && $stripe_billing_plan == '') {
        $db->query(sprintf("DELETE FROM users_recurring_payments WHERE handle = 'packages' AND handle_id = %s", secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* update */
      $db->query(sprintf("UPDATE packages SET name = %s, price = %s, period_num = %s, period = %s, color = %s, icon = %s, package_permissions_group_id = %s, allowed_videos_categories = %s, allowed_blogs_categories = %s, allowed_products = %s, verification_badge_enabled = %s, boost_posts_enabled = %s, boost_posts = %s, boost_pages_enabled = %s, boost_pages = %s, custom_description = %s, package_order = %s, paypal_billing_plan = %s, stripe_billing_plan = %s WHERE package_id = %s", secure($_POST['name']), secure($_POST['price']), secure($_POST['period_num']), secure($_POST['period']), secure($_POST['color']), secure($_POST['icon']), secure($_POST['permissions_group']), secure($_POST['allowed_videos_categories'], 'int'), secure($_POST['allowed_blogs_categories'], 'int'), secure($_POST['allowed_products'], 'int'), secure($_POST['verification_badge_enabled']), secure($_POST['boost_posts_enabled']), secure($_POST['boost_posts'], 'int'), secure($_POST['boost_pages_enabled']), secure($_POST['boost_pages'], 'int'), secure($_POST['custom_description']), secure($_POST['package_order'], 'int'), secure($paypal_billing_plan), secure($stripe_billing_plan), secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['success' => true, 'message' => __("Package info have been updated")]);
      break;

    case 'add':
      /* prepare */
      $_POST['verification_badge_enabled'] = (isset($_POST['verification_badge_enabled'])) ? '1' : '0';
      $_POST['boost_posts_enabled'] = (isset($_POST['boost_posts_enabled'])) ? '1' : '0';
      $_POST['boost_pages_enabled'] = (isset($_POST['boost_pages_enabled'])) ? '1' : '0';
      /* valid inputs */
      if (is_empty($_POST['name'])) {
        throw new Exception(__("You have to enter the package name"));
      }
      if (is_empty($_POST['price']) || !is_numeric($_POST['price']) || $_POST['price'] < 0) {
        throw new Exception(__("You have to enter valid package price"));
      }
      if ($_POST['period'] != "life" && (is_empty($_POST['period_num']) || !is_numeric($_POST['period_num']) || $_POST['period_num'] == '0')) {
        throw new Exception(__("You have to enter valid period number"));
      }
      if ($_POST['permissions_group'] != '0' && !$user->check_permissions_group($_POST['permissions_group'])) {
        throw new Exception(__("You must select valid permissions group"));
      }
      if ($_POST['boost_posts_enabled']) {
        if (is_empty($_POST['boost_posts']) || !is_numeric($_POST['boost_posts'])) {
          throw new Exception(__("You have to enter valid boost posts number"));
        }
      } else {
        $_POST['boost_posts'] = 0;
      }
      if ($_POST['boost_pages_enabled']) {
        if (is_empty($_POST['boost_pages']) || !is_numeric($_POST['boost_pages'])) {
          throw new Exception(__("You have to enter valid boost pages number"));
        }
      } else {
        $_POST['boost_pages'] = 0;
      }
      /* PayPal billing plan */
      $paypal_billing_plan = '';
      $paypal_recurring_enabled = $system['paypal_enabled'] && !is_empty($system['paypal_webhook']);
      if ($paypal_recurring_enabled && $_POST['price'] > 0 && $_POST['period'] != "life") {
        /* create PayPal billing plan */
        $paypal_billing_plan = paypal_create_billing_plan($_POST['name'], $_POST['custom_description'], $_POST['period_num'], $_POST['period'], $_POST['price']);
      }
      /* Stripe billing plan */
      $stripe_billing_plan = '';
      $stripe_recurring_enabled = ($system['creditcard_enabled'] || $system['alipay_enabled']) && !is_empty($system['stripe_webhook']);
      if ($stripe_recurring_enabled && $_POST['price'] > 0 && $_POST['period'] != "life") {
        /* create Stripe billing plan */
        $stripe_billing_plan = stripe_create_billing_plan($_POST['name'], $_POST['custom_description'], $_POST['period_num'], $_POST['period'], $_POST['price']);
      }
      /* insert */
      $db->query(sprintf("INSERT INTO packages (name, price, period_num, period, color, icon, package_permissions_group_id, allowed_videos_categories, allowed_blogs_categories, allowed_products, verification_badge_enabled, boost_posts_enabled, boost_posts, boost_pages_enabled, boost_pages, custom_description, package_order, paypal_billing_plan, stripe_billing_plan) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($_POST['name']), secure($_POST['price']), secure($_POST['period_num']), secure($_POST['period']), secure($_POST['color']), secure($_POST['icon']), secure($_POST['permissions_group'], 'int'), secure($_POST['allowed_videos_categories'], 'int'), secure($_POST['allowed_blogs_categories'], 'int'), secure($_POST['allowed_products'], 'int'), secure($_POST['verification_badge_enabled']), secure($_POST['boost_posts_enabled']), secure($_POST['boost_posts'], 'int'), secure($_POST['boost_pages_enabled']), secure($_POST['boost_pages'], 'int'), secure($_POST['custom_description']), secure($_POST['package_order'], 'int'), secure($paypal_billing_plan), secure($stripe_billing_plan))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['callback' => 'window.location = "' . $system['system_url'] . '/' . $control_panel['url'] . '/pro/packages";']);
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
