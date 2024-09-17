<?php

/**
 * groups
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// groups enabled
if (!$system['groups_enabled']) {
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
      page_header(__("Groups") . ' | ' . __($system['system_title']), __($system['system_description_groups']));

      // get groups categories
      $smarty->assign('categories', $user->get_categories("groups_categories"));

      // get new groups
      $groups = $user->get_groups(['suggested' => true]);
      /* assign variables */
      $smarty->assign('groups', $groups);
      $smarty->assign('get', "suggested_groups");
      break;

    case 'category':
      // user access
      if ($user->_logged_in || !$system['system_public']) {
        user_access();
      }

      // get category
      $current_category = $user->get_category("groups_categories", $_GET['category_id'], true);
      if (!$current_category) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('current_category', $current_category);

      // page header
      page_header(__("Groups") . ' &rsaquo; ' . __($current_category['category_name']) . ' | ' . __($system['system_title']), __($current_category['category_description']));

      // get groups categories (only sub-categories)
      if (!$current_category['sub_categories'] && !$current_category['parent']) {
        $categories = $user->get_categories("groups_categories");
      } else {
        $categories = $user->get_categories("groups_categories", $current_category['category_id']);
      }
      /* assign variables */
      $smarty->assign('categories', $categories);

      // get category groups
      $groups = $user->get_groups(['suggested' => true, 'category_id' => $_GET['category_id']]);
      /* assign variables */
      $smarty->assign('groups', $groups);
      $smarty->assign('get', "category_groups");

      break;

    case 'joined':
      // user access
      user_access();

      // page header
      page_header(__("Joined Groups") . ' | ' . __($system['system_title']));

      // get joined groups
      $groups = $user->get_groups(['user_id' => $user->_data['user_id']]);
      /* assign variables */
      $smarty->assign('groups', $groups);
      $smarty->assign('get', "joined_groups");
      break;

    case 'manage':
      // user access
      user_access();

      // page header
      page_header(__("My Groups") . ' | ' . __($system['system_title']));

      // get managed groups
      $groups = $user->get_groups(['managed' => true, 'user_id' => $user->_data['user_id']]);
      /* assign variables */
      $smarty->assign('groups', $groups);
      $smarty->assign('get', "groups");
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
page_footer('groups');
