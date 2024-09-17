<?php

/**
 * developers
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// developers (apps & share plugin) enabled
if (!$system['developers_apps_enabled'] && !$system['developers_share_enabled']) {
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

      // check if developers (apps) enabled
      if (!$system['developers_apps_enabled']) {
        _error(404);
      }

      // page header
      page_header(__("Developers") . ' | ' . __($system['system_title']));
      break;

    case 'apps':
      // user access
      user_access();

      // check if developers (apps) enabled
      if (!$system['developers_apps_enabled']) {
        _error(404);
      }

      // page header
      page_header(__("My Apps") . ' | ' . __($system['system_title']));

      // get apps
      $apps = $user->get_apps();
      /* assign variables */
      $smarty->assign('apps', $apps);
      break;

    case 'new':
      // user access
      user_access();

      // check if developers (apps) enabled
      if (!$system['developers_apps_enabled']) {
        _error(404);
      }

      // page header
      page_header(__("Create New App") . ' | ' . __($system['system_title']));

      // get apps categories
      $categories = $user->get_categories("developers_apps_categories");
      /* assign variables */
      $smarty->assign('categories', $categories);
      break;

    case 'edit':
      // user access
      user_access();

      // check if developers (apps) enabled
      if (!$system['developers_apps_enabled']) {
        _error(404);
      }

      // page header
      page_header(__("Edit App") . ' | ' . __($system['system_title']));

      // get apps categories
      $categories = $user->get_categories("developers_apps_categories");
      /* assign variables */
      $smarty->assign('categories', $categories);

      // get app
      $app = $user->get_app($_GET['app_auth_id']);
      if (!$app) {
        _error(404);
      }
      /* check permission */
      if (!($user->_data['user_group'] < 3 || $user->_data['user_id'] == $app['app_user_id'])) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('app', $app);
      break;

    case 'share':
      // user access
      if ($user->_logged_in || !$system['system_public']) {
        user_access();
      }

      // check if developers (share plugin) enabled
      if (!$system['developers_share_enabled']) {
        _error(404);
      }

      // page header
      page_header(__("Share Plugin") . ' | ' . __($system['system_title']));
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
page_footer('developers');
