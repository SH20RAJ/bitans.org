<?php

/**
 * ajax -> users -> image change
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

// validate inputs
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
  _error(400);
}
if (!isset($_POST['handle']) || !in_array($_POST['handle'], ['avatar', 'cover'])) {
  _error(400);
}
if (!isset($_POST['type']) || !in_array($_POST['type'], ['user', 'page', 'group', 'event'])) {
  _error(400);
}
if (!isset($_POST['image'])) {
  _error(400);
}

try {

  switch ($_POST['handle']) {
    case 'avatar':
      switch ($_POST['type']) {
        case 'user':
          /* check for profile pictures album */
          if (!$user->_data['user_album_pictures']) {
            /* create new profile pictures album */
            $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'user', 'Profile Pictures', 'public')", secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            $user->_data['user_album_pictures'] = $db->insert_id;
            /* update user profile picture album id */
            $db->query(sprintf("UPDATE users SET user_album_pictures = %s WHERE user_id = %s", secure($user->_data['user_album_pictures'], 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* insert updated profile picture post */
          $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'user', 'profile_picture', %s, 'public')", secure($user->_data['user_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
          $post_id = $db->insert_id;
          /* insert new profile picture to album */
          $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($user->_data['user_album_pictures'], 'int'), secure($_POST['image']))) or _error('SQL_ERROR_THROWEN');
          $photo_id = $db->insert_id;
          /* delete old cropped picture from uploads folder */
          delete_uploads_file($user->_data['user_picture_raw']);
          /* update user profile picture */
          $db->query(sprintf("UPDATE users SET user_picture = %s, user_picture_id = %s WHERE user_id = %s", secure($_POST['image']), secure($photo_id, 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'page':
          /* check if page id is set */
          if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            /* return error 403 */
            _error(403);
          }
          /* check the page */
          $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_page->num_rows == 0) {
            /* return error 403 */
            _error(403);
          }
          $page = $get_page->fetch_assoc();
          /* check if the user is the page admin */
          if (!$user->check_page_adminship($user->_data['user_id'], $page['page_id'])) {
            /* return error 403 */
            _error(403);
          }
          /* check for page pictures album */
          if (!$page['page_album_pictures']) {
            /* create new page pictures album */
            $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'page', 'Profile Pictures', 'public')", secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            $page['page_album_pictures'] = $db->insert_id;
            /* update page profile picture album id */
            $db->query(sprintf("UPDATE pages SET page_album_pictures = %s WHERE page_id = %s", secure($page['page_album_pictures'], 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* insert updated page picture post */
          $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'page', 'page_picture', %s, 'public')", secure($page['page_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
          $post_id = $db->insert_id;
          /* insert new page picture to album */
          $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($page['page_album_pictures'], 'int'), secure($_POST['image']))) or _error('SQL_ERROR_THROWEN');
          $photo_id = $db->insert_id;
          /* delete old cropped picture from uploads folder */
          delete_uploads_file($page['page_picture']);
          /* update page picture */
          $db->query(sprintf("UPDATE pages SET page_picture = %s, page_picture_id = %s WHERE page_id = %s", secure($_POST['image']), secure($photo_id, 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'group':
          /* check if group id is set */
          if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            /* return error 403 */
            _error(403);
          }
          /* check the group */
          $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_group->num_rows == 0) {
            /* return error 403 */
            _error(403);
          }
          $group = $get_group->fetch_assoc();
          /* check if the user is the group admin */
          if (!$user->check_group_adminship($user->_data['user_id'], $group['group_id'])) {
            /* return error 403 */
            _error(403);
          }
          /* check for group pictures album */
          if (!$group['group_album_pictures']) {
            /* create new group pictures album */
            $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_group, group_id, title, privacy) VALUES (%s, 'user', '1', %s, 'Profile Pictures', 'public')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            $group['group_album_pictures'] = $db->insert_id;
            /* update group profile picture album id */
            $db->query(sprintf("UPDATE `groups` SET group_album_pictures = %s WHERE group_id = %s", secure($group['group_album_pictures'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* insert updated group picture post */
          $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, in_group, group_id, time, privacy) VALUES (%s, 'user', 'group_picture', '1', %s, %s, 'custom')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
          $post_id = $db->insert_id;
          /* insert new group picture to album */
          $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($group['group_album_pictures'], 'int'), secure($_POST['image']))) or _error('SQL_ERROR_THROWEN');
          $photo_id = $db->insert_id;
          /* delete old cropped picture from uploads folder */
          delete_uploads_file($group['group_picture']);
          /* update group picture */
          $db->query(sprintf("UPDATE `groups` SET group_picture = %s, group_picture_id = %s WHERE group_id = %s", secure($_POST['image']), secure($photo_id, 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;
      }
      break;

    case 'cover':
      // switch types
      switch ($_POST['type']) {
        case 'user':
          /* check for cover album */
          if (!$user->_data['user_album_covers']) {
            /* create new cover album */
            $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'user', 'Cover Photos', 'public')", secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            $user->_data['user_album_covers'] = $db->insert_id;
            /* update user cover album id */
            $db->query(sprintf("UPDATE users SET user_album_covers = %s WHERE user_id = %s", secure($user->_data['user_album_covers'], 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* insert updated cover photo post */
          $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'user', 'profile_cover', %s, 'public')", secure($user->_data['user_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
          $post_id = $db->insert_id;
          /* insert new cover photo to album */
          $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($user->_data['user_album_covers'], 'int'), secure($_POST['image']))) or _error('SQL_ERROR_THROWEN');
          $photo_id = $db->insert_id;
          /* update user cover */
          $db->query(sprintf("UPDATE users SET user_cover = %s, user_cover_id = %s WHERE user_id = %s", secure($_POST['image']), secure($photo_id, 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'page':
          /* check if page id is set */
          if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          /* check the page */
          $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_page->num_rows == 0) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          $page = $get_page->fetch_assoc();
          /* check if the user is the page admin */
          if (!$user->check_page_adminship($user->_data['user_id'], $page['page_id'])) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          /* check for cover album */
          if (!$page['page_album_covers']) {
            /* create new cover album */
            $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'page', 'Cover Photos', 'public')", secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            $page['page_album_covers'] = $db->insert_id;
            /* update page cover album id */
            $db->query(sprintf("UPDATE pages SET page_album_covers = %s WHERE page_id = %s", secure($page['page_album_covers'], 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* insert updated cover photo post */
          $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'page', 'page_cover', %s, 'public')", secure($page['page_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
          $post_id = $db->insert_id;
          /* insert new cover photo to album */
          $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($page['page_album_covers'], 'int'), secure($_POST['image']))) or _error('SQL_ERROR_THROWEN');
          $photo_id = $db->insert_id;
          /* update page cover */
          $db->query(sprintf("UPDATE pages SET page_cover = %s, page_cover_id = %s WHERE page_id = %s", secure($_POST['image']), secure($photo_id, 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'group':
          /* check if group id is set */
          if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          /* check the group */
          $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_group->num_rows == 0) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          $group = $get_group->fetch_assoc();
          /* check if the user is the group admin */
          if (!$user->check_group_adminship($user->_data['user_id'], $group['group_id'])) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          /* check for group covers album */
          if (!$group['group_album_covers']) {
            /* create new group covers album */
            $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_group, group_id, title, privacy) VALUES (%s, 'user', '1', %s, 'Cover Photos', 'public')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            $group['group_album_covers'] = $db->insert_id;
            /* update group cover album id */
            $db->query(sprintf("UPDATE `groups` SET group_album_covers = %s WHERE group_id = %s", secure($group['group_album_covers'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* insert updated group cover post */
          $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, in_group, group_id, time, privacy) VALUES (%s, 'user', 'group_cover', '1', %s, %s, 'custom')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
          $post_id = $db->insert_id;
          /* insert new group cover to album */
          $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($group['group_album_covers'], 'int'), secure($_POST['image']))) or _error('SQL_ERROR_THROWEN');
          $photo_id = $db->insert_id;
          /* update group cover */
          $db->query(sprintf("UPDATE `groups` SET group_cover = %s, group_cover_id = %s WHERE group_id = %s", secure($_POST['image']), secure($photo_id, 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'event':
          /* check if event id is set */
          if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          /* check the event */
          $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_event->num_rows == 0) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          $event = $get_event->fetch_assoc();
          /* check if the user is the event admin */
          if (!$user->check_event_adminship($user->_data['user_id'], $event['event_id'])) {
            /* delete the uploaded image & return error 403 */
            unlink($path);
            _error(403);
          }
          /* check for event covers album */
          if (!$event['event_album_covers']) {
            /* create new event covers album */
            $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_event, event_id, title, privacy) VALUES (%s, 'user', '1', %s, 'Cover Photos', 'public')", secure($user->_data['user_id'], 'int'), secure($event['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            $event['event_album_covers'] = $db->insert_id;
            /* update event cover album id */
            $db->query(sprintf("UPDATE `events` SET event_album_covers = %s WHERE event_id = %s", secure($event['event_album_covers'], 'int'), secure($event['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* insert updated event cover post */
          $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, in_event, event_id, time, privacy) VALUES (%s, 'user', 'event_cover', '1', %s, %s, 'custom')", secure($user->_data['user_id'], 'int'), secure($event['event_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
          $post_id = $db->insert_id;
          /* insert new event cover to album */
          $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($event['event_album_covers'], 'int'), secure($_POST['image']))) or _error('SQL_ERROR_THROWEN');
          $photo_id = $db->insert_id;
          /* update event cover */
          $db->query(sprintf("UPDATE `events` SET event_cover = %s, event_cover_id = %s WHERE event_id = %s", secure($_POST['image']), secure($photo_id, 'int'), secure($event['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;
      }
      break;
  }

  // return & exit
  return_json();
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
