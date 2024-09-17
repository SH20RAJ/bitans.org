<?php

/**
 * ajax -> admin -> delete
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// valid inputs
if ($_POST['handle'] != "reports" && $_POST['handle'] != "user_points_reset") {
  if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    _error(400);
  }
}

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle delete
try {

  switch ($_POST['handle']) {

    case 'theme':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      /* check if this theme is the default one */
      $check_themes = $db->query(sprintf("SELECT COUNT(*) as count FROM system_themes WHERE system_themes.default = '1' and theme_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check_themes->fetch_assoc()['count'] > 0) {
        throw new Exception(__("This is your only default theme you need to mark other theme as default before change/delete this one"));
      }
      $db->query(sprintf("DELETE FROM system_themes WHERE theme_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'language':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM system_languages WHERE language_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'country':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM system_countries WHERE country_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'currency':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM system_currencies WHERE currency_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'gender':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM system_genders WHERE gender_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'user':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // check if changing the super admin user
      if ($user->_data['user_id'] != '1' && $_POST['id'] == '1') {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to edit this user"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete user */
      $user->delete_user($_POST['id']);
      break;

    case 'user_posts':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // check if changing the super admin user
      if ($user->_data['user_id'] != '1' && $_POST['id'] == '1') {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to edit this user"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete user posts */
      $user->delete_posts($_POST['id']);
      break;

    case 'session':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM users_sessions WHERE session_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'user_package':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // unsubscribe user package
      $user->unsubscribe_user_package($_POST['id']);
      break;

    case 'user_group':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      /* update all users with such user group */
      $db->query(sprintf("UPDATE users SET user_group_custom = '0' WHERE user_group_custom = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete group */
      $db->query(sprintf("DELETE FROM users_groups WHERE user_group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update system default custom user group */
      if ($system['default_custom_user_group'] == $_POST['id']) {
        /* update system */
        update_system_options([
          'default_custom_user_group' => '0'
        ]);
      }
      break;

    case 'permissions_group':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      /* check if there is any users group using this permissions group */
      $check_users_groups = $db->query(sprintf("SELECT COUNT(*) as count FROM users_groups WHERE permissions_group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check_users_groups->fetch_assoc()['count'] > 0) {
        throw new Exception(__("There are users groups using this permissions group"));
      }
      /* update all packages with this permissions group */
      $db->query(sprintf("UPDATE packages SET package_permissions_group_id = '0' WHERE package_permissions_group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete permissions group */
      $db->query(sprintf("DELETE FROM permissions_groups WHERE permissions_group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'video_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("posts_videos_categories", $_POST['id']);
      break;

    case 'page':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete page */
      $user->delete_page($_POST['id']);
      break;

    case 'page_posts':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete page posts */
      $user->delete_posts($_POST['id'], 'page');
      break;

    case 'page_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("pages_categories", $_POST['id']);
      break;

    case 'group':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete group */
      $user->delete_group($_POST['id']);
      break;

    case 'group_posts':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete group posts */
      $user->delete_posts($_POST['id'], 'group');
      break;

    case 'group_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("groups_categories", $_POST['id']);
      break;

    case 'event':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete event */
      $user->delete_event($_POST['id']);
      break;

    case 'event_posts':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete event posts */
      $user->delete_posts($_POST['id'], 'event');
      break;

    case 'event_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("events_categories", $_POST['id']);
      break;

    case 'blogs_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("blogs_categories", $_POST['id']);
      break;

    case 'market_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("market_categories", $_POST['id']);
      break;

    case 'order':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      /* delete order */
      $db->query(sprintf("DELETE FROM orders WHERE order_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete order items */
      $db->query(sprintf("DELETE FROM orders_items WHERE order_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'offers_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("offers_categories", $_POST['id']);
      break;

    case 'jobs_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("jobs_categories", $_POST['id']);
      break;

    case 'forum':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete forum */
      $user->delete_forum($_POST['id']);
      break;

    case 'forum_thread':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete forum thread */
      $user->delete_forum_thread($_POST['id']);
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        /* delete report */
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      break;

    case 'forum_reply':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete forum reply */
      $user->delete_forum_reply($_POST['id']);
      break;

    case 'movie':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM movies WHERE movie_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'movie_genre':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM movies_genres WHERE genre_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'game':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM games WHERE game_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'game_genre':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM games_genres WHERE genre_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'ads_system':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM ads_system WHERE ads_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'package':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      /* get the package */
      $package = $user->get_package($_POST['id']);
      /* update all users who have this package */
      $db->query(sprintf("UPDATE users SET user_subscribed = '0', user_package = null, user_subscription_date = null, user_boosted_posts = '0', user_boosted_pages = '0' WHERE user_package = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove all users recurring payments */
      $db->query(sprintf("DELETE FROM users_recurring_payments WHERE handle = 'packages' AND handle_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete package */
      $db->query(sprintf("DELETE FROM packages WHERE package_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* deactivate PayPal billing plan */
      if ($package['paypal_billing_plan']) {
        paypal_deactivate_billing_plan($package['paypal_billing_plan']);
      }
      /* deactivate Stripe plan */
      if ($package['stripe_plan_id']) {
        stripe_deactivate_plan($package['stripe_plan_id']);
      }
      break;

    case 'apps_category':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("developers_apps_categories", $_POST['id']);
      break;

    case 'report':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'reports':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query("DELETE FROM reports") or _error('SQL_ERROR_THROWEN');
      break;

    case 'report_category':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $user->delete_category("reports_categories", $_POST['id']);
      break;

    case 'post':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete post */
      $user->delete_post($_POST['id'], false);
      break;

    case 'comment':
      // check admin|moderator permission
      if (!$user->_is_admin && !$user->_is_moderator) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async();
      /* delete report */
      if (isset($_POST['node']) && is_numeric($_POST['node'])) {
        $db->query(sprintf("DELETE FROM reports WHERE report_id = %s", secure($_POST['node'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* delete comment */
      $user->delete_comment($_POST['id']);
      break;

    case 'blacklist_node':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM blacklist WHERE node_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'custom_field':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM custom_fields WHERE field_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("DELETE FROM custom_fields_values WHERE field_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'static_page':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM static_pages WHERE page_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'pattern':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM posts_colored_patterns WHERE pattern_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'widget':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM widgets WHERE widget_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'emoji':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM emojis WHERE emoji_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'sticker':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM stickers WHERE sticker_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'gift':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM gifts WHERE gift_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'announcement':
      // check admin|moderator permission
      if (!$user->_is_admin) {
        modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
      }
      $db->query(sprintf("DELETE FROM announcements WHERE announcement_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    default:
      _error(400);
      break;
  }

  // return & exist
  return_json();
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
