<?php

/**
 * ajax -> admin -> games
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check admin|moderator permission
if (!$user->_is_admin && !$user->_is_moderator) {
  modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
}

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle games
try {

  switch ($_GET['do']) {
    case 'add':
      /* validate game genres */
      if (!isset($_POST['game_genres']) || !is_array($_POST['game_genres'])) {
        throw new Exception(__("You have to select genre for the game"));
      }
      $_POST['game_genres'] = implode(',', $_POST['game_genres']);
      /* insert */
      $db->query(sprintf("INSERT INTO games (title, description, genres, source, thumbnail) VALUES (%s, %s, %s, %s, %s)", secure($_POST['title']), secure($_POST['description']), secure($_POST['game_genres']), secure($_POST['source']), secure($_POST['thumbnail']))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['callback' => 'window.location = "' . $system['system_url'] . '/' . $control_panel['url'] . '/games";']);
      break;

    case 'edit':
      /* valid inputs */
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      /* validate game genres */
      if (!isset($_POST['game_genres']) || !is_array($_POST['game_genres'])) {
        throw new Exception(__("You have to select genre for the game"));
      }
      $_POST['game_genres'] = implode(',', $_POST['game_genres']);
      /* update */
      $db->query(sprintf("UPDATE games SET title = %s, description = %s, genres = %s, source = %s, thumbnail = %s WHERE game_id = %s", secure($_POST['title']), secure($_POST['description']), secure($_POST['game_genres']), secure($_POST['source']), secure($_POST['thumbnail']), secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['success' => true, 'message' => __("Game info have been updated")]);
      break;

    case 'add_genre':
      /* valid inputs */
      if (is_empty($_POST['genre_name'])) {
        throw new Exception(__("Please enter a valid genre name"));
      }
      if (!is_empty($_POST['genre_order']) && !is_numeric($_POST['genre_order'])) {
        throw new Exception(__("Please enter a valid genre order"));
      }
      /* insert */
      $db->query(sprintf("INSERT INTO games_genres (genre_name, genre_order, genre_description) VALUES (%s, %s, %s)", secure($_POST['genre_name']), secure($_POST['genre_order'], 'int'), secure($_POST['genre_description']))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['callback' => 'window.location = "' . $system['system_url'] . '/' . $control_panel['url'] . '/games/genres";']);
      break;

    case 'edit_genre':
      /* valid inputs */
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      if (is_empty($_POST['genre_name'])) {
        throw new Exception(__("Please enter a valid genre name"));
      }
      if (!is_empty($_POST['genre_order']) && !is_numeric($_POST['genre_order'])) {
        throw new Exception(__("Please enter a valid genre order"));
      }
      /* update */
      $db->query(sprintf("UPDATE games_genres SET genre_name = %s, genre_order = %s, genre_description = %s WHERE genre_id = %s", secure($_POST['genre_name']), secure($_POST['genre_order'], 'int'), secure($_POST['genre_description']), secure($_GET['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return_json(['success' => true, 'message' => __("Genre info have been updated")]);
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
