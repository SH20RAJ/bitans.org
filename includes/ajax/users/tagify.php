<?php

/**
 * ajax -> users -> tagify
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

// handle tagify
try {

  // initialize the return array
  $return = [];

  switch ($_POST['handle']) {
    case 'video_categories':
      /* get video categories */
      $get_video_categories = $db->query(sprintf('SELECT category_id, category_name FROM posts_videos_categories WHERE category_name LIKE %1$s ORDER BY category_name ASC LIMIT %2$s', secure($_POST['query'], 'search'), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      if ($get_video_categories->num_rows > 0) {
        while ($_category = $get_video_categories->fetch_assoc()) {
          $result['value'] = html_entity_decode($_category['category_name']);
          $result['id'] = $_category['category_id'];
          $list[] = $result;
        }
        $return['list'] = json_encode($list);
      }
      break;

    case 'blogs_categories':
      /* get blogs categories */
      $get_blogs_categories = $db->query(sprintf('SELECT category_id, category_name FROM blogs_categories WHERE category_name LIKE %1$s ORDER BY category_name ASC LIMIT %2$s', secure($_POST['query'], 'search'), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      if ($get_blogs_categories->num_rows > 0) {
        while ($_category = $get_blogs_categories->fetch_assoc()) {
          $result['value'] = html_entity_decode($_category['category_name']);
          $result['id'] = $_category['category_id'];
          $list[] = $result;
        }
        $return['list'] = json_encode($list);
      }
      break;

    default:
      _error(400);
      break;
  }

  // return & exit
  return_json($return);
} catch (Exception $e) {
  modal("ERROR", __("Error"), $e->getMessage());
}
