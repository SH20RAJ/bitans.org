<?php

/**
 * files
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('bootstrap.php');

try {
  // check if user not logged in (except for requests from mobile apps)
  if (!valid_api_key() && !$system['system_public'] && !$user->_logged_in) {
    user_login();
  }

  // check if file exists
  $file = $system['uploads_directory'] . '/' . $_GET['file'];
  if (!file_exists($file)) {
    throw new Exception(__("File not found"));
  }

  // read the file contents and send the response
  $handle = fopen($file, "rb");
  $contents = fread($handle, filesize($file));
  fclose($handle);
  $content_type = mime_content_type($file);
  header("Content-type: " . $content_type);
  header("Content-disposition: inline; filename=" . $file);
  echo $contents;
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}
