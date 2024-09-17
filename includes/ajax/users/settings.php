<?php

/**
 * ajax -> users -> settings
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// user access
user_access(true);

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

try {

  switch ($_GET['edit']) {
    case 'account':
      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your account settings have been updated")]);
      break;

    case 'basic':
      // valid inputs
      if ((!$system['genders_disabled'] && !isset($_POST['gender'])) || !isset($_POST['birth_month']) || !isset($_POST['birth_day']) || !isset($_POST['birth_year'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your profile info have been updated")]);
      break;

    case 'work':
      // valid inputs
      if (!isset($_POST['work_title']) || !isset($_POST['work_place'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your profile info have been updated")]);
      break;

    case 'location':
      // valid inputs
      if (!isset($_POST['city']) || !isset($_POST['hometown'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your profile info have been updated")]);
      break;

    case 'education':
      // valid inputs
      if (!isset($_POST['edu_major']) || !isset($_POST['edu_school']) || !isset($_POST['edu_class'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your profile info have been updated")]);
      break;

    case 'other':
      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your profile info have been updated")]);
      break;

    case 'social':
      // valid inputs
      if (!isset($_POST['facebook']) || !isset($_POST['twitter']) || !isset($_POST['youtube']) || !isset($_POST['instagram']) || !isset($_POST['linkedin']) || !isset($_POST['vkontakte'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your profile info have been updated")]);
      break;

    case 'design':
      // valid inputs
      if (!isset($_POST['user_profile_background'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your profile info have been updated")]);
      break;

    case 'password':
      // valid inputs
      if (!isset($_POST['current']) || !isset($_POST['new']) || !isset($_POST['confirm'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your password has been updated")]);
      break;

    case 'two-factor':
      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your two-factor authentication settings have been updated")]);
      break;

    case 'privacy':
      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your privacy settings have been updated")]);
      break;

    case 'notifications':
      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your notifications settings have been updated")]);
      break;

    case 'notifications_sound':
      // valid inputs
      if (!isset($_GET['notifications_sound'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_GET);

      // return
      return_json();
      break;

    case 'chat':
      // valid inputs
      if (!isset($_GET['user_chat_enabled'])) {
        _error(400);
      }

      // change settings
      $user->settings($_GET['edit'], $_GET);

      // return
      return_json(['success' => true, 'message' => __("Your privacy settings have been updated")]);
      break;

    case 'membership':
      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your membership settings have been updated")]);
      break;

    case 'unsubscribe_package':
      // unsubscribe user package
      $user->unsubscribe_user_package();

      // return
      return_json();
      break;

    case 'monetization':
      // change settings
      $user->settings($_GET['edit'], $_POST);

      // return
      return_json(['success' => true, 'message' => __("Your monetization settings have been updated")]);
      break;

    case 'unsubscribe_plan':
      // valid inputs
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      // remove user plan
      $user->unsubscribe($_GET['id']);

      // return
      return_json();
      break;

    case 'clear_search_log':
      $user->clear_search_log();

      // return
      return_json();
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
