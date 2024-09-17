<?php

/**
 * ajax -> core -> theme
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// set theme mode (day|night)
if (isset($_POST['mode'])) {
  set_cookie('s_night_mode', ($_POST['mode'] == "night") ? '1' : '0');
}
