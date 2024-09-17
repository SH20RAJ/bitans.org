<?php

/**
 * page
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// user access
if ($user->_logged_in || !$system['system_public']) {
  user_access();
}

// check username
if (is_empty($_GET['username']) || !valid_username($_GET['username'])) {
  _error(404);
}

try {

  // [1] get main page info
  $get_page = $db->query(sprintf("SELECT pages.*, picture_photo.source as page_picture_full, cover_photo.source as page_cover_full, pages_categories.category_name as page_category_name FROM pages LEFT JOIN posts_photos as picture_photo ON pages.page_picture_id = picture_photo.photo_id LEFT JOIN posts_photos as cover_photo ON pages.page_cover_id = cover_photo.photo_id LEFT JOIN pages_categories ON pages.page_category = pages_categories.category_id WHERE pages.page_name = %s", secure($_GET['username'])));
  if ($get_page->num_rows == 0) {
    _error(404);
  }
  $spage = $get_page->fetch_assoc();
  /* check username case */
  if (strtolower($_GET['username']) == strtolower($spage['page_name']) && $_GET['username'] != $spage['page_name']) {
    redirect('/pages/' . $spage['page_name']);
  }
  /* get page picture */
  $spage['page_picture_default'] = ($spage['page_picture']) ? false : true;
  $spage['page_picture'] = get_picture($spage['page_picture'], 'page');
  $spage['page_picture_full'] = ($spage['page_picture_full']) ? $system['system_uploads'] . '/' . $spage['page_picture_full'] : $spage['page_picture_full'];
  /* get page cover */
  $spage['page_cover'] = ($spage['page_cover']) ? $system['system_uploads'] . '/' . $spage['page_cover'] : $spage['page_cover'];
  $spage['page_cover_full'] = ($spage['page_cover_full']) ? $system['system_uploads'] . '/' . $spage['page_cover_full'] : $spage['page_cover_full'];
  /* check page category */
  $spage['page_category_name'] = (!$spage['page_category_name']) ? __('N/A') : $spage['page_category_name']; /* in case deleted by admin */
  /* get the connection */
  $spage['i_admin'] = $user->check_page_adminship($user->_data['user_id'], $spage['page_id']);
  $spage['i_like'] = $user->check_page_membership($user->_data['user_id'], $spage['page_id']);
  /* get page posts count */
  $spage['posts_count'] = $user->get_posts_count($spage['page_id'], 'page');
  /* get page photos count */
  $spage['photos_count'] = $user->get_photos_count($spage['page_id'], 'page');
  /* get page videos count */
  if ($system['videos_enabled']) {
    $spage['videos_count'] = $user->get_videos_count($spage['page_id'], 'page');
  }
  /* get page reviews count */
  if ($system['pages_reviews_enabled']) {
    $spage['reviews_count'] = $user->get_reviews_count($spage['page_id']);
  }
  /* get page events */
  if ($system['events_enabled'] && $system['pages_events_enabled']) {
    $spage['events'] = $user->get_events(['page_id' => $spage['page_id'], 'results' => $system['min_results']]);
  }
  /* check if can receivce tips */
  $spage['can_receive_tips'] = $system['tips_enabled'] && $user->check_user_permission($spage['page_admin'], 'tips_permission');
  /* check if page's admin can monetize content */
  $spage['can_monetize_content'] = $system['monetization_enabled'] && $user->check_user_permission($spage['page_admin'], 'monetization_permission');
  /* check if page has monetization enabled && subscriptions plans */
  $spage['has_subscriptions_plans'] = $spage['can_monetize_content'] && $spage['page_monetization_enabled'] && $spage['page_monetization_plans'] > 0;
  /* check if the page needs subscription (exclude: admins & mods & page's admin) */
  $spage['needs_subscription'] = false;
  if ($spage['has_subscriptions_plans']) {
    if ($user->_logged_in) {
      if ($user->_data['user_group'] == 3 && !$spage['i_admin']) {
        if (!$user->is_subscribed($spage['page_id'], 'page')) {
          $spage['needs_subscription'] = true;
        }
      }
    } else {
      $spage['needs_subscription'] = true;
    }
  }


  // [2] get view content
  switch ($_GET['view']) {
    case '':
      /* get custom fields */
      $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "page", "get" => "profile", "node_id" => $spage['page_id']]));

      /* get photos */
      if (!$spage['needs_subscription']) {
        $spage['photos'] = $user->get_photos($spage['page_id'], 'page');
      }

      /* get subscribers */
      if ($spage['has_subscriptions_plans']) {
        /* get subscribers count */
        $spage['subscribers_count'] = $user->get_subscribers_count($spage['page_id'], 'page');
        /* get subscribers */
        if ($spage['subscribers_count'] > 0) {
          $spage['subscribers'] = $user->get_subscribers($spage['page_id'], 'page');
        }
      }

      /* get invites */
      $spage['invites'] = $user->get_page_invites($spage['page_id']);

      /* get pinned post */
      $pinned_post = $user->get_post($spage['page_pinned_post'], true, false, true);
      $smarty->assign('pinned_post', $pinned_post);

      /* prepare publisher */
      $smarty->assign('feelings', get_feelings());
      $smarty->assign('feelings_types', get_feelings_types());
      if ($system['colored_posts_enabled']) {
        $smarty->assign('colored_patterns', $user->get_posts_colored_patterns());
      }
      if ($user->_data['can_upload_videos']) {
        $smarty->assign('videos_categories', $user->get_categories("posts_videos_categories"));
      }

      /* get posts */
      $posts = $user->get_posts(['get' => 'posts_page', 'id' => $spage['page_id']]);
      /* assign variables */
      $smarty->assign('posts', $posts);

      /* get ads */
      $smarty->assign('ads', $user->ads('pages', $spage['page_id']));
      break;

    case 'photos':
      /* get content */
      if (!$spage['needs_subscription']) {
        /* get photos */
        $spage['photos'] = $user->get_photos($spage['page_id'], 'page');
      }
      break;

    case 'albums':
      /* get content */
      if (!$spage['needs_subscription']) {
        /* get albums */
        $spage['albums'] = $user->get_albums($spage['page_id'], 'page');
      }
      break;

    case 'album':
      /* get content */
      if (!$spage['needs_subscription']) {
        /* get album */
        $album = $user->get_album($_GET['id']);
        if (!$album || $album['in_group'] || $album['user_type'] == "user" || ($album['user_type'] == "page" && $album['page_id'] != $spage['page_id'])) {
          _error(404);
        }
        /* assign variables */
        $smarty->assign('album', $album);
      }
      break;

    case 'videos':
      /* check if videos enabled */
      if (!$system['videos_enabled']) {
        _error(404);
      }
      /* get content */
      if (!$spage['needs_subscription']) {
        /* get videos */
        $spage['videos'] = $user->get_videos($spage['page_id'], 'page');
      }
      break;

    case 'reviews':
      /* check if reviews enabled */
      if (!$system['pages_reviews_enabled']) {
        _error(404);
      }
      /* get reviews */
      if ($spage['reviews_count'] > 0) {
        $spage['reviews'] = $user->get_reviews($spage['page_id']);
      }
      break;

    case 'events':
      /* check if pages events enabled */
      if (!$system['events_enabled'] && $system['pages_events_enabled']) {
        _error(404);
      }
      /* get content */
      $spage['events'] = $user->get_events(['page_id' => $spage['page_id'], 'results' => $system['max_results_even']]);
      break;

    case 'subscribers':
      /* check if has subscriptions plans */
      if (!$spage['has_subscriptions_plans']) {
        _error(404);
      }
      /* get subscribers count */
      $spage['subscribers_count'] = $user->get_subscribers_count($spage['page_id'], 'page');
      /* get subscribers */
      if ($spage['subscribers_count'] > 0) {
        $spage['subscribers'] = $user->get_subscribers($spage['page_id'], 'page');
      }
      break;

    case 'invites':
      /* check if the viewer is a page member */
      if (!$spage['i_like']) {
        _error(404);
      }
      /* get invites */
      $spage['invites'] = $user->get_page_invites($spage['page_id']);
      break;

    case 'settings':
      /* check if the viewer is the admin */
      if (!$spage['i_admin']) {
        _error(404);
      }

      /* get sub_view content */
      $sub_view = $_GET['id'];
      switch ($sub_view) {
        case '':
          // get pages categories
          $smarty->assign('categories', $user->get_categories("pages_categories"));
          break;

        case 'info':
          // get countries if not defined
          if (!$countries) {
            $smarty->assign('countries', $user->get_countries());
          }

          // get custom fields
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "page", "get" => "settings", "node_id" => $spage['page_id']]));
          break;

        case 'admins':
          /* get admins */
          $spage['page_admins_count'] = count($user->get_page_admins_ids($spage['page_id']));
          $spage['page_admins'] = $user->get_page_admins($spage['page_id']);

          /* get members */
          if ($spage['page_likes'] > 0) {
            $spage['members'] = $user->get_page_members($spage['page_id']);
          }
          break;

        case 'monetization':
          // check monetization permission (only page's super admin can do this)
          if (!$user->_data['can_monetize_content']) {
            _error(404);
          }

          // get monetozaion plans
          $smarty->assign('monetization_plans', $user->get_monetization_plans($spage['page_id'], 'page'));

          // get subscribers count
          $smarty->assign('subscribers_count', $user->get_subscribers_count($spage['page_id'], 'page'));
          break;

        case 'verification':
          if (!$system['verification_requests']) {
            _error(404);
          }
          // verification
          if ($spage['page_verified']) {
            $case = "verified";
            $user->delete_all_verification_requests($spage['page_id'], 'page');
          } else {
            /* check last verification request */
            $get_last_request = $db->query(sprintf("SELECT * FROM verification_requests WHERE node_id = %s AND node_type = 'page' ORDER BY request_id DESC LIMIT 1", secure($spage['page_id'], 'int')));
            if ($get_last_request->num_rows > 0) {
              $last_request = $get_last_request->fetch_assoc();
              if ($last_request['status'] == '1') {
                $case = "request";
                $user->delete_all_verification_requests($spage['page_id'], 'page');
              } elseif ($last_request['status'] == '0') {
                $case = "pending";
              } else {
                $case = "declined";
              }
            } else {
              $case = "request";
            }
          }
          /* assign variables */
          $smarty->assign('case', $case);
          break;

        case 'delete':
          /* check if the viewer not the super admin */
          if ($user->_data['user_id'] != $spage['page_admin']) {
            _error(404);
          }
          break;

        default:
          _error(404);
          break;
      }
      /* assign variables */
      $smarty->assign('sub_view', $sub_view);

      break;

    default:
      _error(404);
      break;
  }

  // recent rearches
  if (isset($_GET['ref']) && $_GET['ref'] == "qs") {
    $user->set_search_log($spage['page_id'], 'page');
  }
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page header
page_header($spage['page_title'], $spage['page_description'], $spage['page_picture']);

// assign variables
$smarty->assign('spage', $spage);
$smarty->assign('view', $_GET['view']);

// page footer
page_footer('page');
