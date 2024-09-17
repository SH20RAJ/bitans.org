<?php

/**
 * ajax -> data -> upload
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// user access
user_access(true, true);

// check demo account
if ($user->_data['user_demo']) {
  if ($_POST['handle'] == 'tinymce') {
    return_json(["error" => __("You can't do this with demo account")]);
  } else {
    modal("ERROR", __("Error"), __("You can't do this with demo account"));
  }
}

try {

  // upload file
  $response = upload_file(true);

  // return
  return_json([($_POST["multiple"] == "true") ? "files" : "file" => $response]);
} catch (Exception $e) {
  if ($_POST['handle'] == 'tinymce') {
    return_json(["error" => $e->getMessage()]);
  } else {
    modal("ERROR", __("Error"), $e->getMessage());
  }
}
