<?php

/**
 * live
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// live enabled
if (!$system['live_enabled']) {
  _error(404);
}

// live permission
if (!$user->_data['can_go_live']) {
  _error('PERMISSION');
}

// check demo account
if ($user->_data['user_demo']) {
  _error('PERMISSION');
}

// check approval system
if ($user->check_posts_needs_approval($user->_data['user_id'])) {
  _error('PERMISSION', __("Posts approval system is enabled and you need to verify your account before you can go live"));
}

try {

  // page header
  page_header(__("Live") . ' | ' . __($system['system_title']));

  // get agora (uid|token|channel_name)
  $agora = $user->agora_token_builder(true);
  /* assign variables */
  $smarty->assign('agora', $agora);

  // check node (page|group|event|user)
  if (isset($_GET['page_id']) && is_numeric($_GET['page_id'])) {
    /* get page */
    $page = $user->get_page($_GET['page_id']);
    if (!$page) {
      _error(404);
    }
    /* check if the viewer is page admin */
    if (!$user->check_page_adminship($user->_data['user_id'], $page['page_id'])) {
      _error(404);
    }
    /* get node_monetization_enabled */
    $node_monetization_enabled = $page['page_monetization_enabled'];
    /* get node_author */
    $node_author = $page['page_admin'];
    /* get node_monetization_plans */
    $node_monetization_plans = $page['page_monetization_plans'];
    /* assign variables */
    $smarty->assign('page_id', $_GET['page_id']);
  } elseif (isset($_GET['group_id']) && is_numeric($_GET['group_id'])) {
    /* get group */
    $group = $user->get_group($_GET['group_id']);
    if (!$group) {
      _error(404);
    }
    /* check if the viewer joined the group */
    if (!$user->check_group_membership($user->_data['user_id'], $group['group_id'])) {
      _error(404);
    }
    /* get node_monetization_enabled */
    $node_monetization_enabled = $group['group_monetization_enabled'];
    /* get node_author */
    $node_author = $group['group_admin'];
    /* get node_monetization_plans */
    $node_monetization_plans = $group['group_monetization_plans'];
    /* assign variables */
    $smarty->assign('group_id', $_GET['group_id']);
  } elseif (isset($_GET['event_id']) && is_numeric($_GET['event_id'])) {
    /* assign variables */
    $smarty->assign('event_id', $_GET['event_id']);
  } else {
    /* get node_monetization_enabled */
    $node_monetization_enabled = $user->_data['user_monetization_enabled'];
    /* get node_author */
    $node_author = $user->_data['user_id'];
    /* get node_monetization_plans */
    $node_monetization_plans = $user->_data['user_monetization_plans'];
  }

  // check if post can be shared with subscribers only
  $can_be_for_subscriptions = false;
  if ($system['monetization_enabled']) {
    if ($node_monetization_enabled && $user->check_user_permission($node_author, 'monetization_permission') && $node_monetization_plans > 0) {
      $can_be_for_subscriptions = true;
    }
  }
  /* assign variables */
  $smarty->assign('can_be_for_subscriptions', $can_be_for_subscriptions);
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page footer
page_footer('live');
