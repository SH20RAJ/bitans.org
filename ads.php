<?php

/**
 * ads
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// check if ads enabled
if (!$system['ads_enabled']) {
  _error(404);
}

// user access
user_access();

try {

  // get view content
  switch ($_GET['view']) {
    case '':
      // page header
      page_header(__("Ads Manager") . ' | ' . __($system['system_title']));

      // get campaigns
      $smarty->assign('campaigns', $user->get_campaigns());
      break;

    case 'new':
      // page header
      page_header(__("New Campaign"));

      // get viewer all managed pages
      $smarty->assign('pages', $user->get_pages(['managed' => true, 'user_id' => $user->_data['user_id']]));

      // get viewer all managed groups
      $smarty->assign('groups', $user->get_groups(['managed' => true, 'user_id' => $user->_data['user_id']]));

      // get viewer all managed events
      $smarty->assign('events', $user->get_events(['managed' => true, 'user_id' => $user->_data['user_id']]));

      // get genders
      $smarty->assign('genders', $user->get_genders());

      // get countries if not defined
      if (!$countries) {
        $smarty->assign('countries', $user->get_countries());
      }

      // get campaign potential reach
      $smarty->assign('potential_reach', $user->campaign_potential_reach());
      break;

    case 'edit':
      // page header
      page_header(__("Edit Campaign"));

      // get campaign
      $campaign = $user->get_campaign($_GET['campaign_id']);
      if (!$campaign) {
        _error(404);
      }
      /* check permission */
      if (!($user->_data['user_group'] < 3 || $user->_data['user_id'] == $campaign['campaign_user_id'])) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('campaign', $campaign);

      // get target all managed pages
      $smarty->assign('pages', $user->get_pages(['managed' => true, 'user_id' => $campaign['campaign_user_id']]));

      // get target all managed groups
      $smarty->assign('groups', $user->get_groups(['managed' => true, 'user_id' => $campaign['campaign_user_id']]));

      // get target all managed events
      $smarty->assign('events', $user->get_events(['managed' => true, 'user_id' => $campaign['campaign_user_id']]));

      // get genders
      $smarty->assign('genders', $user->get_genders());

      // get countries if not defined
      if (!$countries) {
        $smarty->assign('countries', $user->get_countries());
      }
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
page_footer('ads');
