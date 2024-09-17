<?php

/**
 * modules -> static
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('../bootloader.php');

// valid inputs
if (!isset($_GET['url'])) {
  _error(404);
}

try {

  // get static page
  $static_page = $user->get_static_page($_GET['url']);
  $static_page['page_text'] = $smarty->fetch('string:' . $static_page['page_text']);
  /* assign variables */
  $smarty->assign('static_page', $static_page);
} catch (NoDataException $e) {
  _error(404);
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page header
page_header($static_page['page_title'] . ' | ' . __($system['system_title']));

// page footer
page_footer('static');
