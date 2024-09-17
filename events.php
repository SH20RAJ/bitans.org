<?php

/**
 * events
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// events enabled
if (!$system['events_enabled']) {
  _error(404);
}

try {

  // get view content
  switch ($_GET['view']) {
    case '':
      // user access
      if ($user->_logged_in || !$system['system_public']) {
        user_access();
      }

      // page header
      page_header(__("Events") . ' | ' . __($system['system_title']), __($system['system_description_events']));

      // get events categories
      $smarty->assign('categories', $user->get_categories("events_categories"));

      // get new events
      $events = $user->get_events(['suggested' => true]);
      /* assign variables */
      $smarty->assign('events', $events);
      $smarty->assign('get', "suggested_events");
      break;

    case 'category':
      // user access
      if ($user->_logged_in || !$system['system_public']) {
        user_access();
      }

      // get category
      $current_category = $user->get_category("events_categories", $_GET['category_id'], true);
      if (!$current_category) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('current_category', $current_category);

      // page header
      page_header(__("Events") . ' &rsaquo; ' . __($current_category['category_name']) . ' | ' . __($system['system_title']), __($current_category['category_description']));

      // get events categories (only sub-categories)
      if (!$current_category['sub_categories'] && !$current_category['parent']) {
        $categories = $user->get_categories("events_categories");
      } else {
        $categories = $user->get_categories("events_categories", $current_category['category_id']);
      }
      /* assign variables */
      $smarty->assign('categories', $categories);

      // get category events
      $events = $user->get_events(['suggested' => true, 'category_id' => $_GET['category_id']]);
      /* assign variables */
      $smarty->assign('events', $events);
      $smarty->assign('get', "category_events");

      break;

    case 'going':
      // user access
      user_access();

      // page header
      page_header(__("Going Events") . ' | ' . __($system['system_title']));

      // get going events
      $events = $user->get_events(['filter' => 'going']);
      /* assign variables */
      $smarty->assign('events', $events);
      $smarty->assign('get', "going_events");
      break;

    case 'interested':
      // user access
      user_access();

      // page header
      page_header(__("Interested Events") . ' | ' . __($system['system_title']));

      // get interested events
      $events = $user->get_events(['filter' => 'interested']);
      /* assign variables */
      $smarty->assign('events', $events);
      $smarty->assign('get', "interested_events");
      break;

    case 'invited':
      // user access
      user_access();

      // page header
      page_header(__("Invited Events") . ' | ' . __($system['system_title']));

      // get invited events
      $events = $user->get_events(['filter' => 'invited']);
      /* assign variables */
      $smarty->assign('events', $events);
      $smarty->assign('get', "invited_events");
      break;

    case 'manage':
      // user access
      user_access();

      // page header
      page_header(__("My Events") . ' | ' . __($system['system_title']));

      // get events
      $events = $user->get_events(['managed' => true, 'user_id' => $user->_data['user_id']]);
      /* assign variables */
      $smarty->assign('events', $events);
      $smarty->assign('get', "events");
      break;

    default:
      _error(404);
      break;
  }
  /* assign variables */
  $smarty->assign('view', $_GET['view']);
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page footer
page_footer('events');
