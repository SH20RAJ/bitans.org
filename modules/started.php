<?php

/**
 * modules -> started
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('../bootloader.php');

// user access (simple)
if (!$user->_logged_in) {
  user_login();
}

// check registration type
if ($system['registration_type'] == "paid" && !$user->_data['user_subscribed']) {
  redirect('/packages');
}

// check if already getted started
if (!$system['getting_started'] || $user->_data['user_started']) {
  redirect();
}

try {

  // get countries
  if (!$countries) {
    $smarty->assign('countries', $user->get_countries());
  }

  // get suggested peopel
  $smarty->assign('new_people', $user->get_new_people(0, true));
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page header
page_header(__($system['system_title']) . " &rsaquo; " . __("Getting Started"));

// page footer
page_footer('started');
