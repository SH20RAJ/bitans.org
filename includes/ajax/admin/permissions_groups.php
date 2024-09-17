<?php

/**
 * ajax -> admin -> permissions groups
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

// handle permissions groups
try {

  switch ($_GET['do']) {
    case 'add':
      /* valid inputs */
      if (is_empty($_POST['title'])) {
        throw new Exception(__("Please enter a valid group name"));
      }
      /* prepare */
      $_POST['pages_permission'] = (isset($_POST['pages_permission'])) ? '1' : '0';
      $_POST['groups_permission'] = (isset($_POST['groups_permission'])) ? '1' : '0';
      $_POST['events_permission'] = (isset($_POST['events_permission'])) ? '1' : '0';
      $_POST['blogs_permission'] = (isset($_POST['blogs_permission'])) ? '1' : '0';
      $_POST['market_permission'] = (isset($_POST['market_permission'])) ? '1' : '0';
      $_POST['offers_permission'] = (isset($_POST['offers_permission'])) ? '1' : '0';
      $_POST['jobs_permission'] = (isset($_POST['jobs_permission'])) ? '1' : '0';
      $_POST['forums_permission'] = (isset($_POST['forums_permission'])) ? '1' : '0';
      $_POST['movies_permission'] = (isset($_POST['movies_permission'])) ? '1' : '0';
      $_POST['games_permission'] = (isset($_POST['games_permission'])) ? '1' : '0';
      $_POST['gifts_permission'] = (isset($_POST['gifts_permission'])) ? '1' : '0';
      $_POST['posts_permission'] = (isset($_POST['posts_permission'])) ? '1' : '0';
      $_POST['blogs_permission_read'] = (isset($_POST['blogs_permission_read'])) ? '1' : '0';
      $_POST['videos_permission_read'] = (isset($_POST['videos_permission_read'])) ? '1' : '0';
      $_POST['stories_permission'] = (isset($_POST['stories_permission'])) ? '1' : '0';
      $_POST['colored_posts_permission'] = (isset($_POST['colored_posts_permission'])) ? '1' : '0';
      $_POST['activity_posts_permission'] = (isset($_POST['activity_posts_permission'])) ? '1' : '0';
      $_POST['polls_posts_permission'] = (isset($_POST['polls_posts_permission'])) ? '1' : '0';
      $_POST['geolocation_posts_permission'] = (isset($_POST['geolocation_posts_permission'])) ? '1' : '0';
      $_POST['gif_posts_permission'] = (isset($_POST['gif_posts_permission'])) ? '1' : '0';
      $_POST['anonymous_posts_permission'] = (isset($_POST['anonymous_posts_permission'])) ? '1' : '0';
      $_POST['invitation_permission'] = (isset($_POST['invitation_permission'])) ? '1' : '0';
      $_POST['audio_call_permission'] = (isset($_POST['audio_call_permission'])) ? '1' : '0';
      $_POST['video_call_permission'] = (isset($_POST['video_call_permission'])) ? '1' : '0';
      $_POST['live_permission'] = (isset($_POST['live_permission'])) ? '1' : '0';
      $_POST['videos_upload_permission'] = (isset($_POST['videos_upload_permission'])) ? '1' : '0';
      $_POST['audios_upload_permission'] = (isset($_POST['audios_upload_permission'])) ? '1' : '0';
      $_POST['files_upload_permission'] = (isset($_POST['files_upload_permission'])) ? '1' : '0';
      $_POST['ads_permission'] = (isset($_POST['ads_permission'])) ? '1' : '0';
      $_POST['fundings_permission'] = (isset($_POST['fundings_permission'])) ? '1' : '0';
      $_POST['monetization_permission'] = (isset($_POST['monetization_permission'])) ? '1' : '0';
      $_POST['tips_permission'] = (isset($_POST['tips_permission'])) ? '1' : '0';
      /* insert */
      $db->query(sprintf(
        "INSERT INTO permissions_groups (
        permissions_group_title,
        pages_permission,
        groups_permission,
        events_permission,
        blogs_permission,
        market_permission,
        offers_permission,
        jobs_permission,
        forums_permission,
        movies_permission,
        games_permission,
        gifts_permission,
        posts_permission,
        blogs_permission_read,
        videos_permission_read,
        stories_permission,
        colored_posts_permission,
        activity_posts_permission,
        polls_posts_permission,
        geolocation_posts_permission,
        gif_posts_permission,
        anonymous_posts_permission,
        invitation_permission,
        audio_call_permission,
        video_call_permission,
        live_permission,
        videos_upload_permission,
        audios_upload_permission,
        files_upload_permission,
        ads_permission,
        fundings_permission,
        monetization_permission,
        tips_permission
        ) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
        secure($_POST['title']),
        secure($_POST['pages_permission'], 'int'),
        secure($_POST['groups_permission'], 'int'),
        secure($_POST['events_permission'], 'int'),
        secure($_POST['blogs_permission'], 'int'),
        secure($_POST['market_permission'], 'int'),
        secure($_POST['offers_permission'], 'int'),
        secure($_POST['jobs_permission'], 'int'),
        secure($_POST['forums_permission'], 'int'),
        secure($_POST['movies_permission'], 'int'),
        secure($_POST['games_permission'], 'int'),
        secure($_POST['gifts_permission'], 'int'),
        secure($_POST['posts_permission'], 'int'),
        secure($_POST['blogs_permission_read'], 'int'),
        secure($_POST['videos_permission_read'], 'int'),
        secure($_POST['stories_permission'], 'int'),
        secure($_POST['colored_posts_permission'], 'int'),
        secure($_POST['activity_posts_permission'], 'int'),
        secure($_POST['polls_posts_permission'], 'int'),
        secure($_POST['geolocation_posts_permission'], 'int'),
        secure($_POST['gif_posts_permission'], 'int'),
        secure($_POST['anonymous_posts_permission'], 'int'),
        secure($_POST['invitation_permission'], 'int'),
        secure($_POST['audio_call_permission'], 'int'),
        secure($_POST['video_call_permission'], 'int'),
        secure($_POST['live_permission'], 'int'),
        secure($_POST['videos_upload_permission'], 'int'),
        secure($_POST['audios_upload_permission'], 'int'),
        secure($_POST['files_upload_permission'], 'int'),
        secure($_POST['ads_permission'], 'int'),
        secure($_POST['fundings_permission'], 'int'),
        secure($_POST['monetization_permission'], 'int'),
        secure($_POST['tips_permission'], 'int')
      )) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['callback' => 'window.location = "' . $system['system_url'] . '/' . $control_panel['url'] . '/permissions_groups";']);
      break;

    case 'edit':
      /* valid inputs */
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      if (is_empty($_POST['title'])) {
        throw new Exception(__("Please enter a valid group name"));
      }
      if (($_GET['id'] == '1' && $_POST['title'] != 'Users Permissions') || ($_GET['id'] == '2' && $_POST['title'] != 'Verified Permissions')) {
        throw new Exception(__("You can't edit this permissions group title because it's a default group"));
      }
      /* prepare */
      $_POST['pages_permission'] = (isset($_POST['pages_permission'])) ? '1' : '0';
      $_POST['groups_permission'] = (isset($_POST['groups_permission'])) ? '1' : '0';
      $_POST['events_permission'] = (isset($_POST['events_permission'])) ? '1' : '0';
      $_POST['blogs_permission'] = (isset($_POST['blogs_permission'])) ? '1' : '0';
      $_POST['market_permission'] = (isset($_POST['market_permission'])) ? '1' : '0';
      $_POST['offers_permission'] = (isset($_POST['offers_permission'])) ? '1' : '0';
      $_POST['jobs_permission'] = (isset($_POST['jobs_permission'])) ? '1' : '0';
      $_POST['forums_permission'] = (isset($_POST['forums_permission'])) ? '1' : '0';
      $_POST['movies_permission'] = (isset($_POST['movies_permission'])) ? '1' : '0';
      $_POST['games_permission'] = (isset($_POST['games_permission'])) ? '1' : '0';
      $_POST['gifts_permission'] = (isset($_POST['gifts_permission'])) ? '1' : '0';
      $_POST['posts_permission'] = (isset($_POST['posts_permission'])) ? '1' : '0';
      $_POST['blogs_permission_read'] = (isset($_POST['blogs_permission_read'])) ? '1' : '0';
      $_POST['videos_permission_read'] = (isset($_POST['videos_permission_read'])) ? '1' : '0';
      $_POST['stories_permission'] = (isset($_POST['stories_permission'])) ? '1' : '0';
      $_POST['colored_posts_permission'] = (isset($_POST['colored_posts_permission'])) ? '1' : '0';
      $_POST['activity_posts_permission'] = (isset($_POST['activity_posts_permission'])) ? '1' : '0';
      $_POST['polls_posts_permission'] = (isset($_POST['polls_posts_permission'])) ? '1' : '0';
      $_POST['geolocation_posts_permission'] = (isset($_POST['geolocation_posts_permission'])) ? '1' : '0';
      $_POST['gif_posts_permission'] = (isset($_POST['gif_posts_permission'])) ? '1' : '0';
      $_POST['anonymous_posts_permission'] = (isset($_POST['anonymous_posts_permission'])) ? '1' : '0';
      $_POST['invitation_permission'] = (isset($_POST['invitation_permission'])) ? '1' : '0';
      $_POST['audio_call_permission'] = (isset($_POST['audio_call_permission'])) ? '1' : '0';
      $_POST['video_call_permission'] = (isset($_POST['video_call_permission'])) ? '1' : '0';
      $_POST['live_permission'] = (isset($_POST['live_permission'])) ? '1' : '0';
      $_POST['videos_upload_permission'] = (isset($_POST['videos_upload_permission'])) ? '1' : '0';
      $_POST['audios_upload_permission'] = (isset($_POST['audios_upload_permission'])) ? '1' : '0';
      $_POST['files_upload_permission'] = (isset($_POST['files_upload_permission'])) ? '1' : '0';
      $_POST['ads_permission'] = (isset($_POST['ads_permission'])) ? '1' : '0';
      $_POST['fundings_permission'] = (isset($_POST['fundings_permission'])) ? '1' : '0';
      $_POST['monetization_permission'] = (isset($_POST['monetization_permission'])) ? '1' : '0';
      $_POST['tips_permission'] = (isset($_POST['tips_permission'])) ? '1' : '0';
      /* update */
      $db->query(sprintf(
        "UPDATE permissions_groups SET
        permissions_group_title = %s,
        pages_permission = %s,
        groups_permission = %s,
        events_permission = %s,
        blogs_permission = %s,
        market_permission = %s,
        offers_permission = %s,
        jobs_permission = %s,
        forums_permission = %s,
        movies_permission = %s,
        games_permission = %s,
        gifts_permission = %s,
        posts_permission = %s,
        blogs_permission_read = %s,
        videos_permission_read = %s,
        stories_permission = %s,
        colored_posts_permission = %s,
        activity_posts_permission = %s,
        polls_posts_permission = %s,
        geolocation_posts_permission = %s,
        gif_posts_permission = %s,
        anonymous_posts_permission = %s,
        invitation_permission = %s,
        audio_call_permission = %s,
        video_call_permission = %s,
        live_permission = %s,
        videos_upload_permission = %s,
        audios_upload_permission = %s,
        files_upload_permission = %s,
        ads_permission = %s,
        fundings_permission = %s,
        monetization_permission = %s,
        tips_permission = %s
        WHERE permissions_group_id = %s",
        secure($_POST['title']),
        secure($_POST['pages_permission'], 'int'),
        secure($_POST['groups_permission'], 'int'),
        secure($_POST['events_permission'], 'int'),
        secure($_POST['blogs_permission'], 'int'),
        secure($_POST['market_permission'], 'int'),
        secure($_POST['offers_permission'], 'int'),
        secure($_POST['jobs_permission'], 'int'),
        secure($_POST['forums_permission'], 'int'),
        secure($_POST['movies_permission'], 'int'),
        secure($_POST['games_permission'], 'int'),
        secure($_POST['gifts_permission'], 'int'),
        secure($_POST['posts_permission'], 'int'),
        secure($_POST['blogs_permission_read'], 'int'),
        secure($_POST['videos_permission_read'], 'int'),
        secure($_POST['stories_permission'], 'int'),
        secure($_POST['colored_posts_permission'], 'int'),
        secure($_POST['activity_posts_permission'], 'int'),
        secure($_POST['polls_posts_permission'], 'int'),
        secure($_POST['geolocation_posts_permission'], 'int'),
        secure($_POST['gif_posts_permission'], 'int'),
        secure($_POST['anonymous_posts_permission'], 'int'),
        secure($_POST['invitation_permission'], 'int'),
        secure($_POST['audio_call_permission'], 'int'),
        secure($_POST['video_call_permission'], 'int'),
        secure($_POST['live_permission'], 'int'),
        secure($_POST['videos_upload_permission'], 'int'),
        secure($_POST['audios_upload_permission'], 'int '),
        secure($_POST['files_upload_permission'], 'int'),
        secure($_POST['ads_permission'], 'int'),
        secure($_POST['fundings_permission'], 'int'),
        secure($_POST['monetization_permission'], 'int'),
        secure($_POST['tips_permission'], 'int'),
        secure($_GET['id'], 'int')
      )) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(array('success' => true, 'message' => __("Permission group have been updated")));
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
