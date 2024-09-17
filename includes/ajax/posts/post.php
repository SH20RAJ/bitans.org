<?php

/**
 * ajax -> posts -> post
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

// valid inputs
if (!in_array($_POST['handle'], ['me', 'user', 'page', 'group', 'event'])) {
  _error(400);
}
/* filter link */
if (isset($_POST['link'])) {
  $_POST['link'] = json_decode($_POST['link']);
  if (!is_object($_POST['link'])) {
    _error(400);
  }
}
/* filter photos */
$photos = [];
if (isset($_POST['photos'])) {
  $_POST['photos'] = json_decode($_POST['photos']);
  if (!is_object($_POST['photos'])) {
    _error(400);
  }
  foreach ($_POST['photos'] as $photo) {
    $photos[] = (array) $photo;
  }
  if (count($photos) == 0) {
    _error(400);
  }
}
/* filter voice_notes */
if (isset($_POST['voice_notes'])) {
  $_POST['voice_notes'] = json_decode($_POST['voice_notes']);
  if (!is_object($_POST['voice_notes'])) {
    _error(400);
  }
}
/* filter poll options */
if (isset($_POST['poll_options'])) {
  $_POST['poll_options'] = json_decode($_POST['poll_options']);
  if (!is_array($_POST['poll_options'])) {
    _error(400);
  }
  /* check the options */
  $options = [];
  foreach ($_POST['poll_options'] as $option) {
    if (strlen($option) > 255) {
      modal("MESSAGE", __("Poll option too long"), __("The poll option you provided is too long. Please try again"));
    }
    if (in_array($option, $options)) {
      modal("MESSAGE", __("Poll Option Already Added"), __("This option was already added to the poll"));
    }
    if (!is_empty($option)) {
      $options[] = $option;
    }
  }
  /* check the question */
  if (is_empty($_POST['message'])) {
    modal("MESSAGE", __("Poll Text Needed"), __("Ask a question so people know what your poll is about"));
  }
}
/* filter video */
if (isset($_POST['video'])) {
  $_POST['video'] = json_decode($_POST['video']);
  if (!is_object($_POST['video'])) {
    _error(400);
  }
}
/* filter audio */
if (isset($_POST['audio'])) {
  $_POST['audio'] = json_decode($_POST['audio']);
  if (!is_object($_POST['audio'])) {
    _error(400);
  }
}
/* filter file */
if (isset($_POST['file'])) {
  $_POST['file'] = json_decode($_POST['file']);
  if (!is_object($_POST['file'])) {
    _error(400);
  }
}

try {

  // initialize the return array
  $return = $inputs = [];

  // publisher
  /* check permissions */
  /* check colored posts permission */
  if (!$user->_data['can_add_colored_posts'] && isset($_POST['colored_pattern'])) {
    modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
  }
  /* check activity posts permission */
  if (!$user->_data['can_add_activity_posts'] && isset($_POST['feeling_action'])) {
    modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
  }
  /* check poll posts permission */
  if (!$user->_data['can_add_polls_posts'] && isset($options)) {
    modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
  }
  /* check geolocation posts permission */
  if (!$user->_data['can_add_geolocation_posts'] && isset($_POST['location']) && !empty($_POST['location'])) {
    modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
  }
  /* check anonymous posts permission */
  if (!$user->_data['can_add_anonymous_posts'] && $_POST['is_anonymous'] == "true") {
    modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
  }
  /* check adult posts permission */
  if ($system['verification_for_adult_content'] && !$user->_data['user_verified'] && $_POST['for_adult'] == "true") {
    modal("ERROR", __("Not Allowed"), __("Your account must be verified to share adult content"));
  }
  /* check tips permission */
  if (!$user->_data['can_receive_tip'] && $_POST['tips_enabled'] == "true") {
    modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
  }
  /* check paid posts permission */
  if (!$user->_data['can_monetize_content'] && !$user->_data['user_monetization_enabled'] && $_POST['is_paid'] == "true") {
    modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
  }
  /* valid inputs */
  switch ($_POST['handle']) {
    case 'page':
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }
      $inputs['id'] = $_POST['id'];
      $inputs['privacy'] = 'public';
      $_get = 'posts_page';
      break;

    case 'group':
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }
      $inputs['id'] = $_POST['id'];
      $inputs['privacy'] = 'custom';
      $_get = 'posts_group';
      break;

    case 'event':
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }
      $inputs['id'] = $_POST['id'];
      $inputs['privacy'] = 'custom';
      $_get = 'posts_event';
      break;

    case 'user':
      if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        _error(400);
      }
      $inputs['id'] = $_POST['id'];
      /* if privacy set and not valid */
      $_POST['privacy'] = ($system['newsfeed_source'] == "all_posts") ? "public" : $_POST['privacy'];
      if (!isset($_POST['privacy']) || !in_array($_POST['privacy'], ['friends', 'public'])) {
        _error(400);
      }
      $inputs['privacy'] = $_POST['privacy'];
      $_get = 'posts_profile';
      break;

    default:
      /* if privacy set and not valid */
      $_POST['privacy'] = ($system['newsfeed_source'] == "all_posts") ? "public" : $_POST['privacy'];
      if (!isset($_POST['privacy']) || !in_array($_POST['privacy'], ['me', 'friends', 'public'])) {
        _error(400);
      }
      $inputs['privacy'] = $_POST['privacy'];
      $_get = 'newfeed';
      break;
  }
  /* check is_anonymous */
  $inputs['is_anonymous'] = ($_POST['handle'] == 'me' && $_POST['is_anonymous'] == "true") ? '1' : '0';
  if ($inputs['is_anonymous']) {
    $_POST['album'] = "";
    $inputs['privacy'] = 'public';
  }
  /* check for_adult */
  $inputs['for_adult'] = ($_POST['for_adult'] == "true") ? '1' : '0';
  /* check tips_enabled */
  $inputs['tips_enabled'] = ($_POST['handle'] != 'page' && $_POST['tips_enabled'] == "true") ? '1' : '0';
  /* check for_subscriptions */
  $inputs['for_subscriptions'] = ($_POST['handle'] != 'user' && $_POST['for_subscriptions'] == "true") ? '1' : '0';
  /* check is_paid */
  $inputs['is_paid'] = ($_POST['handle'] != 'user' && $_POST['is_paid'] == "true") ? '1' : '0';
  if ($inputs['is_paid']) {
    $inputs['post_price'] = $_POST['post_price'];
    if (!is_numeric($inputs['post_price']) || $inputs['post_price'] <= 0) {
      modal("ERROR", __("Error"), __("Please enter a valid price"));
    }
    $inputs['paid_text'] = $_POST['paid_text'];
    if (strlen($inputs['paid_text']) > 1000) {
      modal("ERROR", __("Error"), __("Paid post description is more than 1000 characters"));
    }
  }
  /* prepare inputs */
  $inputs['handle'] = $_POST['handle'];
  $inputs['message'] = $_POST['message'];
  $inputs['link'] = $_POST['link'];
  $inputs['photos'] = $photos;
  $inputs['album'] = $_POST['album'];
  $inputs['feeling_action'] = $_POST['feeling_action'];
  $inputs['feeling_value'] = $_POST['feeling_value'];
  $inputs['location'] = $_POST['location'];
  $inputs['colored_pattern'] = $_POST['colored_pattern'];
  $inputs['poll_options'] = $options;
  $inputs['video'] = $_POST['video'];
  $inputs['video_thumbnail'] = $_POST['video_thumbnail'];
  $inputs['video_category'] = $_POST['video_category'];
  $inputs['audio'] = ($_POST['voice_notes']) ? $_POST['voice_notes'] : $_POST['audio'];
  $inputs['file'] = $_POST['file'];
  $inputs['post_as_page'] = $_POST['post_as_page'];
  /* publish */
  $post = $user->publisher($inputs);
  /* assign variables */
  $smarty->assign('post', $post);
  $smarty->assign('_get', $_get);
  /* return */
  $return['post'] = $smarty->fetch("__feeds_post.tpl");

  /* check if approval required */
  if ($post['has_approved'] == '0') {
    // [BACKGROUND PROCESS]
    /* return async */
    return_json_async(['approval' => true]);
    /* check if post is video && ffmpeg enabled */
    if ($system['ffmpeg_enabled'] && $post['video']['source']) {
      /* start ffmpeg converting */
      ffmpeg_convert($post['post_id'], $post['author_id'], $post['video']['source'], $post['video']['thumbnail']);
    }
    // return & exit
    return_json(['approval' => true]);
  } else {
    /* check if post is video && ffmpeg enabled */
    if ($system['ffmpeg_enabled'] && $post['video']['source']) {
      // [BACKGROUND PROCESS]
      /* return async */
      return_json_async(['processing' => true]);
      /* start ffmpeg converting */
      ffmpeg_convert($post['post_id'], $post['author_id'], $post['video']['source'], $post['video']['thumbnail']);
      // return & exit
      return_json(['processing' => true]);
    } else {
      // return & exit
      return_json($return);
    }
  }
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
