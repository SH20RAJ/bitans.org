<?php

/**
 * ajax -> admin -> export
 * 
 * @package Sngine
 * @author Zamblek
 */

// set execution time
set_time_limit(0); /* unlimited max execution time */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check admin|moderator permission
if (!$user->_is_admin) {
  modal("MESSAGE", __("System Message"), __("You don't have the right permission to access this"));
}

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle export
try {

  // valid inputs
  /* valid from_row */
  if (!isset($_POST['from_row']) || !is_numeric($_POST['from_row']) || $_POST['from_row'] < 0) {
    throw new Exception(__("You must enter a valid from row value"));
  }
  /* valid results */
  if (!isset($_POST['results']) || !is_numeric($_POST['results']) || $_POST['results'] < 0) {
    throw new Exception(__("You must enter a valid results value"));
  }
  /* valid dates */
  $from = (isset($_POST['from']) && $_POST['from'] != "") ? date('Y-m-d', strtotime($_POST['from'])) : null;
  $to = (isset($_POST['to']) && $_POST['to'] != "") ? date('Y-m-d', strtotime($_POST['to'])) : null;
  if ($from && $to && $from > $to) {
    throw new Exception(__("The from date must be less than the to date"));
  }

  switch ($_POST['handle']) {
    case 'users':
      // get users
      /* where query */
      $where_query = "";
      $from_to_query = "";
      if ($from && $to) {
        $where_query = sprintf(" WHERE (users.user_registered BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
        $from_to_query = $from . " - " . $to;
      } else {
        $from_to_query = __("All Time");
      }
      /* limit query */
      $limit_query = ($_POST['results'] != 0) ? " LIMIT " . $_POST['from_row'] . ", " . $_POST['results'] : "";
      /* get results */
      $results = $db->query("SELECT * FROM users " . $where_query . " ORDER BY user_id ASC " . $limit_query);
      /* output the CSV data */
      $csv_data = "";
      $csv_data .= "User ID,User Name,Email,Joined Date,Activated\n";
      while ($result = $results->fetch_assoc()) {
        $csv_data .= $result['user_id'] . "," . $result['user_name'] . "," . $result['user_email'] . "," . $result['user_registered'] . "," . ($result['user_activated'] ? __("Yes") : __("No")) . "\n";
      }
      /* retrun */
      return_json(['download' => true, 'data' => $csv_data, 'filename' => 'users.csv', 'type' => 'text/csv']);
      break;

    case 'admins':
      // get admins
      /* where query */
      $where_query = "";
      $from_to_query = "";
      if ($from && $to) {
        $where_query = sprintf(" AND (users.user_registered BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
        $from_to_query = $from . " - " . $to;
      } else {
        $from_to_query = __("All Time");
      }
      /* limit query */
      $limit_query = ($_POST['results'] != 0) ? " LIMIT " . $_POST['from_row'] . ", " . $_POST['results'] : "";
      /* get results */
      $results = $db->query("SELECT * FROM users WHERE user_group = '1' " . $where_query . " ORDER BY user_id ASC " . $limit_query);
      /* output the CSV data */
      $csv_data = "";
      $csv_data .= "User ID,User Name,Email,Joined Date,Activated\n";
      while ($result = $results->fetch_assoc()) {
        $csv_data .= $result['user_id'] . "," . $result['user_name'] . "," . $result['user_email'] . "," . $result['user_registered'] . "," . ($result['user_activated'] ? __("Yes") : __("No")) . "\n";
      }
      /* retrun */
      return_json(['download' => true, 'data' => $csv_data, 'filename' => 'admins.csv', 'type' => 'text/csv']);
      break;

    case 'moderators':
      // get moderators
      /* where query */
      $where_query = "";
      $from_to_query = "";
      if ($from && $to) {
        $where_query = sprintf(" AND (users.user_registered BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
        $from_to_query = $from . " - " . $to;
      } else {
        $from_to_query = __("All Time");
      }
      /* limit query */
      $limit_query = ($_POST['results'] != 0) ? " LIMIT " . $_POST['from_row'] . ", " . $_POST['results'] : "";
      /* get results */
      $results = $db->query("SELECT * FROM users WHERE user_group = '2' " . $where_query . " ORDER BY user_id ASC " . $limit_query);
      /* output the CSV data */
      $csv_data = "";
      $csv_data .= "User ID,User Name,Email,Joined Date,Activated\n";
      while ($result = $results->fetch_assoc()) {
        $csv_data .= $result['user_id'] . "," . $result['user_name'] . "," . $result['user_email'] . "," . $result['user_registered'] . "," . ($result['user_activated'] ? __("Yes") : __("No")) . "\n";
      }
      /* retrun */
      return_json(['download' => true, 'data' => $csv_data, 'filename' => 'moderators.csv', 'type' => 'text/csv']);
      break;

    case 'users_stats':
      // get users stats
      /* where query */
      $where_query = "";
      $from_to_query = "";
      if ($from && $to) {
        $where_query = sprintf(" AND (DATE(posts.time) BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
        $from_to_query = $from . " - " . $to;
      } else {
        $from_to_query = __("All Time");
      }
      /* limit query */
      $limit_query = ($_POST['results'] != 0) ? " LIMIT " . $_POST['from_row'] . ", " . $_POST['results'] : "";
      /* get results */
      $results = $db->query("SELECT users.*, (SELECT COUNT(*) FROM posts WHERE posts.user_id = users.user_id AND posts.user_type = 'user' " . $where_query . ") AS posts_count FROM users ORDER BY user_id ASC " . $limit_query);
      /* output the CSV data */
      $csv_data = "";
      $csv_data .= "User ID,User Name,Date Range,Posts Count\n";
      while ($result = $results->fetch_assoc()) {
        /* get users posts count on each day from that date range */
        $posts_counter_string = __("Total") . ": " . $result['posts_count'];
        if ($from && $to) {
          $current_date = $from;
          while ($current_date <= $to) {
            /* get posts count */
            $posts_count = $db->query(sprintf("SELECT COUNT(*) AS count FROM posts WHERE posts.user_id = %s AND posts.user_type = 'user' AND DATE(posts.time) = %s", secure($result['user_id'], 'int'), secure($current_date, 'datetime')));
            $posts_count = $posts_count->fetch_assoc();
            $posts_counter_string .= "; " . $current_date . ": " . $posts_count['count'];
            /* next day */
            $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
          }
        }
        $csv_data .= $result['user_id'] . "," . $result['user_name'] . "," . $from_to_query . "," . $posts_counter_string . "\n";
      }
      /* retrun */
      return_json(['download' => true, 'data' => $csv_data, 'filename' => 'users_stats.csv', 'type' => 'text/csv']);
      break;

    case 'earnings':
      // get earnings stats
      /* where query */
      $where_query = "";
      $from_to_query = "";
      if ($from && $to) {
        $where_query = sprintf(" WHERE (log_payments.time BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
        $from_to_query = $from . " - " . $to;
      } else {
        $from_to_query = __("All Time");
      }
      /* limit query */
      $limit_query = ($_POST['results'] != 0) ? " LIMIT " . $_POST['from_row'] . ", " . $_POST['results'] : "";
      /* get results */
      $results = $db->query("SELECT * FROM log_payments INNER JOIN users ON log_payments.user_id = users.user_id " . $where_query . " ORDER BY payment_id ASC " . $limit_query);
      /* output the CSV data */
      $csv_data = "";
      $csv_data .= "ID,User ID,Amount,Payment Method,Type,Date\n";
      while ($result = $results->fetch_assoc()) {
        $csv_data .= $result['payment_id'] . "," . $result['user_id'] . "," . print_money(number_format($result['amount'])) . "," . $result['method'] . "," . $result['handle'] . "," . $result['time'] . "\n";
      }
      /* retrun */
      return_json(['download' => true, 'data' => $csv_data, 'filename' => 'earnings.csv', 'type' => 'text/csv']);
      break;

    case 'commissions':
      // get commissions stats
      /* where query */
      $where_query = "";
      $from_to_query = "";
      if ($from && $to) {
        $where_query = sprintf(" WHERE (log_commissions.time BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
        $from_to_query = $from . " - " . $to;
      } else {
        $from_to_query = __("All Time");
      }
      /* limit query */
      $limit_query = ($_POST['results'] != 0) ? " LIMIT " . $_POST['from_row'] . ", " . $_POST['results'] : "";
      /* get results */
      $results = $db->query("SELECT * FROM log_commissions INNER JOIN users ON log_commissions.user_id = users.user_id " . $where_query . " ORDER BY payment_id ASC " . $limit_query);
      /* output the CSV data */
      $csv_data = "";
      $csv_data .= "ID,User ID,Amount,Type,Date\n";
      while ($result = $results->fetch_assoc()) {
        $csv_data .= $result['payment_id'] . "," . $result['user_id'] . "," . print_money(number_format($result['amount'])) . "," . $result['handle'] . "," . $result['time'] . "\n";
      }
      /* retrun */
      return_json(['download' => true, 'data' => $csv_data, 'filename' => 'commissions.csv', 'type' => 'text/csv']);
      break;

    case 'movies':
      // get movies_payments stats
      /* where query */
      $where_query = "";
      $from_to_query = "";
      if ($from && $to) {
        $where_query = sprintf(" WHERE (movies_payments.payment_time BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
        $from_to_query = $from . " - " . $to;
      } else {
        $from_to_query = __("All Time");
      }
      /* limit query */
      $limit_query = ($_POST['results'] != 0) ? " LIMIT " . $_POST['from_row'] . ", " . $_POST['results'] : "";
      /* get results */
      $results = $db->query("SELECT * FROM movies_payments INNER JOIN users ON movies_payments.user_id = users.user_id INNER JOIN movies ON movies_payments.movie_id = movies.movie_id " . $where_query . " ORDER BY id ASC " . $limit_query);
      /* output the CSV data */
      $csv_data = "";
      $csv_data .= "ID,User ID,Movie ID,Price,Date\n";
      while ($result = $results->fetch_assoc()) {
        $csv_data .= $result['id'] . "," . $result['user_id'] . "," . $result['movie_id'] . "," . print_money(number_format($result['price'])) . "," . $result['payment_time'] . "\n";
      }
      /* retrun */
      return_json(['download' => true, 'data' => $csv_data, 'filename' => 'movies_payments.csv', 'type' => 'text/csv']);
      break;

    default:
      _error(400);
      break;
  }

  // return & exist
  return_json();
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
