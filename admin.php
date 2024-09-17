<?php

/**
 * admin
 * 
 * @package Sngine
 * @author Zamblek
 */

// set override_shutdown
$override_shutdown = true;

// fetch bootloader
require('bootloader.php');

// user access
user_access();

// check admin|moderator permission
if (!$user->_is_admin && !$user->_is_moderator) {
  _error(__('System Message'), __("You don't have the right permission to access this"));
}

// check moderator mode
if ($user->_is_moderator && !$moderator_mode) {
  /* moderator try to access admin panel */
  _error(__('System Message'), __("You don't have the right permission to access this"));
}

try {

  // get view content
  switch ($_GET['view']) {
    case '':
      // update view
      $_GET['view'] = 'dashboard';

      // page header
      page_header($control_panel['title'] . " " . __("Panel"));

      // get insights
      $insights = [];
      /* total users */
      $get_users = $db->query("SELECT COUNT(*) as count FROM users");
      $insights['users'] = $get_users->fetch_assoc()['count'];
      /* banned */
      $get_banned = $db->query("SELECT COUNT(*) as count FROM users WHERE user_banned = '1'");
      $insights['banned'] = $get_banned->fetch_assoc()['count'];
      /* not activated */
      $get_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
      $insights['not_activated'] = $get_not_activated->fetch_assoc()['count'];
      /* online */
      $get_online = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($system['offline_time'], 'int', false)));
      $insights['online'] = $get_online->fetch_assoc()['count'];
      /* get total vistors (log_sessions) */
      $get_vistors = $db->query("SELECT COUNT(*) as count FROM log_sessions");
      $insights['vistors'] = $get_vistors->fetch_assoc()['count'];
      /* get total visitors today (log_sessions) */
      $get_vistors_today = $db->query("SELECT COUNT(*) as count FROM log_sessions WHERE DATE(session_date) = CURDATE()");
      $insights['vistors_today'] = $get_vistors_today->fetch_assoc()['count'];
      /* get total visitors this month (log_sessions) */
      $get_vistors_month = $db->query("SELECT COUNT(*) as count FROM log_sessions WHERE YEAR(session_date) = YEAR(CURRENT_DATE()) AND MONTH(session_date) = MONTH(CURRENT_DATE())");
      $insights['vistors_month'] = $get_vistors_month->fetch_assoc()['count'];
      /* posts */
      $get_posts = $db->query("SELECT COUNT(*) as count FROM posts");
      $insights['posts'] = $get_posts->fetch_assoc()['count'];
      /* comments */
      $get_comments = $db->query("SELECT COUNT(*) as count FROM posts_comments");
      $insights['comments'] = $get_comments->fetch_assoc()['count'];
      /* pages */
      $get_pages = $db->query("SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id");
      $insights['pages'] = $get_pages->fetch_assoc()['count'];
      /* groups */
      $get_groups = $db->query("SELECT COUNT(*) as count FROM `groups`");
      $insights['groups'] = $get_groups->fetch_assoc()['count'];
      /* events */
      $get_events = $db->query("SELECT COUNT(*) as count FROM `events`");
      $insights['events'] = $get_events->fetch_assoc()['count'];
      /* messages */
      $get_messages = $db->query("SELECT COUNT(*) as count FROM conversations_messages");
      $insights['messages'] = $get_messages->fetch_assoc()['count'];
      /* notifications */
      $get_notifications = $db->query("SELECT COUNT(*) as count FROM notifications");
      $insights['notifications'] = $get_notifications->fetch_assoc()['count'];

      // get chart data
      for ($i = 1; $i <= 12; $i++) {
        /* get users */
        $get_monthly_users = $db->query("SELECT COUNT(*) as count FROM users WHERE YEAR(user_registered) = YEAR(CURRENT_DATE()) AND MONTH(user_registered) = $i");
        $chart['users'][$i] = $get_monthly_users->fetch_assoc()['count'];
        /* get pages */
        $get_monthly_pages = $db->query("SELECT COUNT(*) as count FROM pages WHERE YEAR(page_date) = YEAR(CURRENT_DATE()) AND MONTH(page_date) = $i");
        $chart['pages'][$i] = $get_monthly_pages->fetch_assoc()['count'];
        /* get groups */
        $get_monthly_groups = $db->query("SELECT COUNT(*) as count FROM `groups` WHERE YEAR(group_date) = YEAR(CURRENT_DATE()) AND MONTH(group_date) = $i");
        $chart['groups'][$i] = $get_monthly_groups->fetch_assoc()['count'];
        /* get events */
        $get_monthly_events = $db->query("SELECT COUNT(*) as count FROM `events` WHERE YEAR(event_date) = YEAR(CURRENT_DATE()) AND MONTH(event_date) = $i");
        $chart['events'][$i] = $get_monthly_events->fetch_assoc()['count'];
        /* get posts */
        $get_monthly_posts = $db->query("SELECT COUNT(*) as count FROM posts WHERE YEAR(time) = YEAR(CURRENT_DATE()) AND MONTH(time) = $i");
        $chart['posts'][$i] = $get_monthly_posts->fetch_assoc()['count'];
      }

      // assign variables
      $smarty->assign('insights', $insights);
      $smarty->assign('chart', $chart);
      break;

    case 'settings':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("System Settings"));

          // get currencies
          $smarty->assign('system_currencies', $user->get_currencies());
          break;

        case 'posts':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts Settings"));
          break;

        case 'registration':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Registration Settings"));

          // get users groups
          $smarty->assign('user_groups', $user->get_users_groups());
          break;

        case 'accounts':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Accounts Settings"));
          break;

        case 'email':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Email Settings"));
          break;

        case 'sms':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("SMS Settings"));
          break;

        case 'notifications':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Notifications Settings"));
          break;

        case 'chat':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Chat Settings"));
          break;

        case 'live':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Live Stream Settings"));
          break;

        case 'uploads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Uploads Settings"));

          // get PHP upload_max_filesize
          $max_upload_size = ini_get("upload_max_filesize");
          /* assign variables */
          $smarty->assign('max_upload_size', $max_upload_size);
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Payments Settings"));
          break;

        case 'security':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Security Settings"));
          break;

        case 'limits':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Limits Settings"));
          break;

        case 'analytics':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Analytics Settings"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'themes':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Themes"));

          // get data
          $get_rows = $db->query("SELECT * FROM system_themes");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_themes WHERE theme_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Themes") . " &rsaquo; " . $data['name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Themes") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'design':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Design"));
      break;

    case 'languages':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Languages"));

          // get data
          $get_rows = $db->query("SELECT * FROM system_languages");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['flag'] = get_picture($row['flag'], 'flag');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_languages WHERE language_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Languages") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Languages") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'countries':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Countries"));

          // get data
          $get_rows = $db->query("SELECT * FROM system_countries");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_countries WHERE country_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Countries") . " &rsaquo; " . $data['country_name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Countries") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'currencies':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Currencies"));

          // get data
          $rows = $user->get_currencies(true);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_currencies WHERE currency_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Currencies") . " &rsaquo; " . $data['name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Currencies") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'genders':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Genders"));

          // get data
          $rows = $user->get_genders(false);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_genders WHERE gender_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Genders") . " &rsaquo; " . $data['gender_name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Genders") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'users':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users"));

          // check cug
          $cug_where_query = "";
          $cug_where_and_query = "";
          $query_string = "";
          if (isset($_GET['cug'])) {
            $cug = $user->get_user_group($_GET['cug']);
            if (!$cug) {
              _error(404);
            }
            $cug_where_query = " WHERE user_group_custom = " . secure($_GET['cug'], 'int') . " ";
            $cug_where_and_query = " AND user_group_custom =  " . secure($_GET['cug'], 'int') . " ";
            $query_string = "&cug=" . $_GET['cug'];
            $smarty->assign('cug', $cug);
          } elseif (isset($_GET['ncug'])) {
            $cug_where_query = " WHERE user_group = '3' AND user_group_custom = '0' ";
            $cug_where_and_query = " AND user_group = '3' AND user_group_custom =  '0' ";
            $query_string = "&ncug=true";
            $smarty->assign('ncug', true);
          }

          // get insights
          $insights = [];
          /* total users */
          $get_users = $db->query("SELECT COUNT(*) as count FROM users" . $cug_where_query);
          $insights['users'] = $get_users->fetch_assoc()['count'];
          $get_banned = $db->query("SELECT COUNT(*) as count FROM users WHERE user_banned = '1'" . $cug_where_and_query);
          $insights['banned'] = $get_banned->fetch_assoc()['count'];
          $get_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'" . $cug_where_and_query);
          $insights['not_activated'] = $get_not_activated->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users" . $cug_where_query);
          $params['total_items'] = $insights['users'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users?page=%s' . $query_string;
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users " . $cug_where_query . "ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'admins':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Admins"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '1'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/admins?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_group = '1' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'moderators':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Moderators"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '2'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/moderators?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_group = '2' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'online':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Online"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($system['offline_time'], 'int', false)));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/online?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf("SELECT * FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s)) ORDER BY user_id ASC " . $limit_query, secure($system['offline_time'], 'int', false)));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'banned':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Banned"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_banned = '1'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/banned?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_banned = '1' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'not_activated':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Not Activated"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/not_activated?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM users WHERE user_activated = '0' ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'stats':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . __("Stats"));

          // prepare where query
          $where_query = "";
          $query_string = "";
          /* check from & to */
          $from = (isset($_GET['from'])) ? date('Y-m-d', strtotime($_GET['from'])) : null;
          $to = (isset($_GET['to'])) ? date('Y-m-d', strtotime($_GET['to'])) : null;
          if ($from && $to) {
            $where_query = sprintf(" AND (DATE(posts.time) BETWEEN %s AND %s)", secure($from, 'datetime'), secure($to, 'datetime'));
            $query_string = "&from=" . $_GET['from'] . "&to=" . $_GET['to'];
          }
          /* check query */
          if (isset($_GET['query'])) {
            $where_users_query = sprintf(' WHERE (users.user_name LIKE %1$s OR users.user_firstname LIKE %1$s OR users.user_lastname LIKE %1$s OR CONCAT(users.user_firstname,  " ", users.user_lastname) LIKE %1$s OR users.user_email LIKE %1$s OR users.user_phone LIKE %1$s)', secure($_GET['query'], 'search'));
            $query_string .= "&query=" . $_GET['query'];
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM users");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/stats?page=%s' . $query_string;
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT users.*, (SELECT COUNT(*) FROM posts WHERE posts.user_id = users.user_id AND posts.user_type = 'user' " . $where_query . ") AS posts_count FROM users " . $where_users_query . " ORDER BY user_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], null, 1);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          $smarty->assign('from', $from);
          $smarty->assign('to', $to);
          $smarty->assign('query', ($_GET['query']) ? htmlentities($_GET['query'], ENT_QUOTES, 'utf-8') : null);
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM users WHERE (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s OR user_email LIKE %1$s OR user_phone LIKE %1$s) ORDER BY user_firstname ASC', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/users/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT * FROM users WHERE (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s OR user_email LIKE %1$s OR user_phone LIKE %1$s) ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM users LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id WHERE users.user_id = %s ", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get user full name */
          $data['user_fullname'] = ($system['show_usernames_enabled']) ? $data['user_name'] : $data['user_firstname'] . " " . $data['user_lastname'];
          /* get users groups */
          $data['user_groups'] = $user->get_users_groups();
          /* get user picture */
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get user's friends */
          $data['friends'] = $user->get_friends_count($data['user_id']);
          $data['followings'] = $user->get_followings_count($data['user_id']);
          $data['followers'] = $user->get_followers_count($data['user_id']);
          /* parse birthdate */
          $data['user_birthdate_parsed'] = ($data['user_birthdate']) ? date_parse($data['user_birthdate']) : null;
          /* get user sessions */
          $get_sessions = $db->query(sprintf("SELECT * FROM users_sessions WHERE user_id = %s", secure($_GET['id'], 'int')));
          if ($get_sessions->num_rows > 0) {
            while ($session = $get_sessions->fetch_assoc()) {
              $data['sessions'][] = $session;
            }
          }
          /* prepare packages */
          if ($system['packages_enabled']) {
            /* prepare user package */
            if ($data['user_subscribed']) {
              switch ($data['period']) {
                case 'day':
                  $duration = 86400;
                  break;

                case 'week':
                  $duration = 604800;
                  break;

                case 'month':
                  $duration = 2629743;
                  break;

                case 'year':
                  $duration = 31556926;
                  break;
              }
              $data['subscription_end'] = strtotime($data['user_subscription_date']) + ($data['period_num'] * $duration);
              $data['subscription_timeleft'] = ceil(($data['subscription_end'] - time()) / (60 * 60 * 24));
            }
            /* get packages */
            $packages = $user->get_packages();
            $smarty->assign('packages', $packages);
          }
          /* prepare monetization */
          $data['can_monetize_content'] = $system['monetization_enabled'] && $user->check_user_permission($data['user_id'], 'monetization_permission');
          // get monetozaion plans
          if ($data['can_monetize_content']) {
            $smarty->assign('monetization_plans', $user->get_monetization_plans($data['user_id']));
          }
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "user", "get" => "settings", "node_id" => $_GET['id']]));

          // get genders
          $smarty->assign('genders',  $user->get_genders());

          // get countries
          if (!$countries) {
            $smarty->assign('countries', $user->get_countries());
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users") . " &rsaquo; " . $data['user_firstname'] . " " . $data['user_lastname']);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'users_groups':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users Groups"));

          // get data
          /* get system groups counts */
          /* get admins count */
          $get_admins = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '1'");
          $counters['admins_count'] = $get_admins->fetch_assoc()['count'];
          /* get moderators count */
          $get_moderators = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '2'");
          $counters['moderators_count'] = $get_moderators->fetch_assoc()['count'];
          /* get users count */
          $get_users = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group = '3' AND user_group_custom = '0'");
          $counters['users_count'] = $get_users->fetch_assoc()['count'];
          /* get custom groups */
          $rows = $user->get_users_groups();

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('counters', $counters);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $data = $user->get_user_group($_GET['id']);
          if (!$data) {
            _error(404);
          }
          /* get permissions groups */
          $data['permissions_groups'] = $user->get_permissions_groups();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users Groups") . " &rsaquo; " . $data['user_group_title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Users Groups") . " &rsaquo; " . __("Add New"));

          // get permissions groups
          $smarty->assign('permissions_groups', $user->get_permissions_groups());
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'permissions_groups':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Permissions Groups"));

          // get data
          $rows = $user->get_permissions_groups();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || (!in_array($_GET['id'], ['users', 'verified']) && !is_numeric($_GET['id']))) {
            _error(404);
          }
          if ($_GET['id'] == "users") {
            $_GET['id'] = '1';
          } elseif ($_GET['id'] == "verified") {
            $_GET['id'] = '2';
          }

          // get data
          $data = $user->get_permissions_group($_GET['id']);
          if (!$data) {
            _error(404);
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Permissions Groups") . " &rsaquo; " . $data['permissions_group_title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Permissions Groups") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'posts':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts"));

          // get insights
          $insights = [];
          $get_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '1' OR posts.has_approved = '1') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['posts'] = $get_posts->fetch_assoc()['count'];
          $get_pending_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '0' AND posts.has_approved = '0') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['pending_posts'] = $get_pending_posts->fetch_assoc()['count'];
          $get_posts_comments = $db->query("SELECT COUNT(*) as count FROM posts_comments");
          $insights['posts_comments'] = $get_posts_comments->fetch_assoc()['count'];
          $get_posts_likes = $db->query("SELECT COUNT(*) as count FROM posts_reactions");
          $insights['posts_likes'] = $get_posts_likes->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $params['total_items'] = $insights['posts'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '1' OR posts.has_approved = '1') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND (posts.post_id = %1$s OR posts.text LIKE %2$s)', secure($_GET['query'], 'int'), secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND (posts.post_id = %1$s OR posts.text LIKE %2$s) ' . $limit_query, secure($_GET['query'], 'int'), secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'pending':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Pending"));

          // get insights
          $insights = [];
          $get_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '1' OR posts.has_approved = '1') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['posts'] = $get_posts->fetch_assoc()['count'];
          $get_pending_posts = $db->query("SELECT COUNT(*) as count FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '0' AND posts.has_approved = '0') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $insights['pending_posts'] = $get_pending_posts->fetch_assoc()['count'];
          $get_posts_comments = $db->query("SELECT COUNT(*) as count FROM posts_comments");
          $insights['posts_comments'] = $get_posts_comments->fetch_assoc()['count'];
          $get_posts_likes = $db->query("SELECT COUNT(*) as count FROM posts_reactions");
          $insights['posts_likes'] = $get_posts_likes->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $params['total_items'] = $insights['pending_posts'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts/pending?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE (posts.pre_approved = '0' AND posts.has_approved = '0') AND NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'videos_categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Videos Categories"));

          // get data
          $rows = $user->get_categories("posts_videos_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_videos_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM posts_videos_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("posts_videos_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Videos Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_videos_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Posts") . " &rsaquo; " . __("Videos Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("posts_videos_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'pages':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages"));

          // get insights
          $insights = [];
          $get_pages = $db->query("SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id");
          $insights['pages'] = $get_pages->fetch_assoc()['count'];
          $get_pages_verified = $db->query("SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_verified = '1'");
          $insights['pages_verified'] = $get_pages_verified->fetch_assoc()['count'];
          $get_pages_likes = $db->query("SELECT COUNT(*) as count FROM pages_likes INNER JOIN users ON pages_likes.user_id = users.user_id INNER JOIN pages ON pages_likes.page_id = pages.page_id");
          $insights['pages_likes'] = $get_pages_likes->fetch_assoc()['count'];

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $params['total_items'] = $insights['pages'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/pages?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT pages.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM pages INNER JOIN users ON pages.page_admin = users.user_id ORDER BY pages.page_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('insights', $insights);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_name LIKE %1$s OR pages.page_title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/pages/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT pages.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_name LIKE %1$s OR pages.page_title LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit_page':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT pages.*, users.* FROM pages INNER JOIN users ON pages.page_admin = users.user_id WHERE pages.page_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          $data['page_picture'] = get_picture($data['page_picture'], 'page');
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get categories */
          $data['categories'] = $user->get_categories("pages_categories");
          /* get countries */
          $data['countries'] = (!$countries) ? $user->get_countries() : $countries;
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "page", "get" => "settings", "node_id" => $_GET['id']]));
          /* prepare monetization */
          $data['can_monetize_content'] = $system['monetization_enabled'] && $user->check_user_permission($data['page_admin'], 'monetization_permission');
          // get monetozaion plans
          if ($data['can_monetize_content']) {
            $smarty->assign('monetization_plans', $user->get_monetization_plans($data['page_id'], 'page'));
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . $data['page_title']);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("pages_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM pages_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("pages_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pages") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("pages_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'groups':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/groups?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT `groups`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id ORDER BY `groups`.group_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['group_picture'] = get_picture($row['group_picture'], 'group');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id WHERE `groups`.group_name LIKE %1$s OR `groups`.group_title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/groups/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT `groups`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id WHERE `groups`.group_name LIKE %1$s OR `groups`.group_title LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['group_picture'] = get_picture($row['group_picture'], 'group');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit_group':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT `groups`.*, users.* FROM `groups` INNER JOIN users ON `groups`.group_admin = users.user_id WHERE `groups`.group_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          $data['group_picture'] = get_picture($data['group_picture'], 'group');
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get categories */
          $data['categories'] = $user->get_categories("groups_categories");
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "group", "get" => "settings", "node_id" => $_GET['id']]));
          /* prepare monetization */
          $data['can_monetize_content'] = $system['monetization_enabled'] && $user->check_user_permission($data['group_admin'], 'monetization_permission');
          if ($data['can_monetize_content']) {
            // get monetozaion plans
            $smarty->assign('monetization_plans', $user->get_monetization_plans($data['group_id'], 'group'));
          }

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . $data['group_title']);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("groups_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM groups_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("groups_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Groups") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("groups_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'events':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/events?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT `events`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id ORDER BY `events`.event_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id WHERE `events`.event_title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/events/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT `events`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id WHERE `events`.event_title LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'edit_event':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT `events`.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM `events` INNER JOIN users ON `events`.event_admin = users.user_id WHERE `events`.event_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          $data['event_picture'] = get_picture($data['event_cover'], 'event');
          $data['user_picture'] = get_picture($data['user_picture'], $data['user_gender']);
          /* get categories */
          $data['categories'] = $user->get_categories("events_categories");
          /* get custom fields */
          $smarty->assign('custom_fields', $user->get_custom_fields(["for" => "event", "get" => "settings", "node_id" => $_GET['id']]));

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . $data['event_title']);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("events_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM events_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("events_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Events") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("events_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'blogs':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = 'article' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/blogs?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_articles.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = 'article' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['article_title_url'] = get_url_text($row['title']);
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['article_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['article_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['article_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['article_author_picture'] = get_picture($row['page_picture'], "page");
                $row['article_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['article_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = "article" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_articles.title LIKE %1$s OR posts_articles.text LIKE %1$s OR posts_articles.tags LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/blogs/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_articles.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = "article" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_articles.title LIKE %1$s OR posts_articles.text LIKE %1$s OR posts_articles.tags LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['article_title_url'] = get_url_text($row['title']);
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['article_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['article_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['article_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['article_author_picture'] = get_picture($row['page_picture'], "page");
                $row['article_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['article_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("blogs_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM blogs_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("blogs_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blogs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("blogs_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'offers':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = 'offer' INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/offers?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_offers.title, pages.page_name, pages.page_title, pages.page_picture FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = 'offer' INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = "offer" INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE posts.text LIKE %1$s OR posts_offers.title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/offers/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_offers.title, pages.page_name, pages.page_title, pages.page_picture FROM posts INNER JOIN posts_offers ON posts.post_id = posts_offers.post_id AND posts.post_type = "offer" INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE posts.text LIKE %1$s OR posts_offers.title LIKE %1$s' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("offers_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM offers_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("offers_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Offers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("offers_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'jobs':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = 'job' INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/jobs?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_jobs.title, pages.page_name, pages.page_title, pages.page_picture FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = 'job' INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = "job" INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE posts.text LIKE %1$s OR posts_jobs.title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/jobs/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_jobs.title, pages.page_name, pages.page_title, pages.page_picture FROM posts INNER JOIN posts_jobs ON posts.post_id = posts_jobs.post_id AND posts.post_type = "job" INNER JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE posts.text LIKE %1$s OR posts_jobs.title LIKE %1$s' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("jobs_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM jobs_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("jobs_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Jobs") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("jobs_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'forums':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums"));

          // get data
          $rows = $user->get_forums();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_forum':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $data = $user->get_forum($_GET['id']);
          if (!$data) {
            _error(404);
          }
          /* get sections */
          $data['sections'] = $user->get_forums();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . $data['forum_name']);
          break;

        case 'add_forum':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Add New Forum"));

          // get data
          $forums = $user->get_forums();

          // assign variables
          $smarty->assign('forums', $forums);
          break;

        case 'threads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Threads"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/threads?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT forums_threads.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id ORDER BY forums_threads.thread_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['thread_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['thread_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['thread_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find_threads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Threads"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.title LIKE %1$s OR forums_threads.text LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/find_threads?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT forums_threads.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.title LIKE %1$s OR forums_threads.text LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['thread_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['thread_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['thread_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'replies':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Replies"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/replies?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT forums_replies.*, forums_threads.title, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id ORDER BY forums_replies.reply_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['reply_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['reply_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['reply_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find_replies':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Forums") . " &rsaquo; " . __("Replies"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id WHERE forums_replies.text LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/forums/find_replies?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT forums_replies.*, forums_threads.title, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id WHERE forums_replies.text LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['thread_title_url'] = get_url_text($row['title']);
              $row['reply_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['reply_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['reply_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'movies':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies"));

          // get data
          $get_rows = $db->query("SELECT * FROM movies");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['movie_url'] = get_url_text($row['title']);
              $row['poster'] = get_picture($row['poster'], 'movie');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_movie':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM movies WHERE movie_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get movie genres */
          $data['genres'] = ($data['genres']) ? explode(',', $data['genres']) : [];

          /* get genres */
          $data['movies_genres'] = $user->get_movies_genres();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . $data['title']);
          break;

        case 'add_movie':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Add New Movie"));

          // get data
          $genres = $user->get_movies_genres();

          // assign variables
          $smarty->assign('genres', $genres);
          break;

        case 'genres':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Genres"));

          // get data
          $rows = $user->get_movies_genres(false);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_genre':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM movies_genres WHERE genre_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Genres") . " &rsaquo; " . $data['genre_name']);
          break;

        case 'add_genre':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Movies") . " &rsaquo; " . __("Genres") . " &rsaquo; " . __("Add New Genre"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'games':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games"));

          // get data
          $get_rows = $db->query("SELECT * FROM games");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['title_url'] = get_url_text($row['title']);
              $row['thumbnail'] = get_picture($row['thumbnail'], 'game');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM games WHERE game_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get game genres */
          $data['genres'] = ($data['genres']) ? explode(',', $data['genres']) : [];

          /* get genres */
          $data['games_genres'] = $user->get_games_genres();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Add New"));

          // get data
          $genres = $user->get_games_genres();

          // assign variables
          $smarty->assign(
            'genres',
            $genres
          );
          break;

        case 'genres':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Genres"));

          // get data
          $rows = $user->get_games_genres(false);

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_genre':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM games_genres WHERE genre_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Genres") . " &rsaquo; " . $data['genre_name']);
          break;

        case 'add_genre':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Games") . " &rsaquo; " . __("Genres") . " &rsaquo; " . __("Add New Genre"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'earnings':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Earnings") . " &rsaquo; " . __("Payments"));

          // get chart data
          /* payment methods chart */
          $payment_methods_chart = [];
          $payment_methods = [];
          $get_payment_methods = $db->query("SELECT DISTINCT method FROM log_payments");
          while ($method = $get_payment_methods->fetch_assoc()) {
            $payment_methods[] = $method['method'];
          }
          for ($i = 1; $i <= 12; $i++) {
            $payment_methods_chart[$i] = [];
            foreach ($payment_methods as $method) {
              $result = $db->query("SELECT SUM(amount) as total_amount FROM log_payments WHERE YEAR(time) = YEAR(CURRENT_DATE()) AND MONTH(time) = $i AND method = '$method'");
              $row = $result->fetch_assoc();
              /* round to 2 decimal places */
              $payment_methods_chart[$i][$method] = ($row['total_amount']) ? round($row['total_amount'], 2) : 0;
            }
          }
          /* payment handle chart */
          $payment_handles_chart = [];
          $payment_handles = [];
          $get_payment_handles = $db->query("SELECT DISTINCT handle FROM log_payments");
          while ($handle = $get_payment_handles->fetch_assoc()) {
            $payment_handles[] = $handle['handle'];
          }
          for ($i = 1; $i <= 12; $i++) {
            $payment_handles_chart[$i] = [];
            foreach ($payment_handles as $handle) {
              $result = $db->query("SELECT SUM(amount) as total_amount FROM log_payments WHERE YEAR(time) = YEAR(CURRENT_DATE()) AND MONTH(time) = $i AND handle = '$handle'");
              $row = $result->fetch_assoc();
              /* round to 2 decimal places */
              $payment_handles_chart[$i][$handle] = ($row['total_amount']) ? round($row['total_amount'], 2) : 0;
            }
          }

          // calculate payins
          /* calculate total payins */
          $total_payin = 0;
          $get_total_payin = $db->query("SELECT SUM(amount) as total_payin FROM log_payments");
          if ($get_total_payin->num_rows > 0) {
            $total_payin = $get_total_payin->fetch_assoc()['total_payin'];
          }
          /* calculate this month payins */
          $month_payin = 0;
          $get_month_payin = $db->query("SELECT SUM(amount) as month_payin FROM log_payments WHERE MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          if ($get_month_payin->num_rows > 0) {
            $month_payin = $get_month_payin->fetch_assoc()['month_payin'];
          }

          // calculate payouts (withdrawals) (total & this month)
          /* get monetiztaion withdrawals */
          $get_total_pending_monetization_withdrawals = $db->query("SELECT SUM(amount) as total_monetization_withdrawals FROM monetization_payments WHERE status = '0'");
          $total_pending_monetization_withdrawals = $get_total_pending_monetization_withdrawals->fetch_assoc()['total_monetization_withdrawals'];
          $get_total_approved_monetization_withdrawals = $db->query("SELECT SUM(amount) as total_pending_monetization_withdrawals FROM monetization_payments WHERE status = '1'");
          $total_approved_monetization_withdrawals = $get_total_approved_monetization_withdrawals->fetch_assoc()['total_pending_monetization_withdrawals'];
          $get_total_pending_month_monetization_withdrawals = $db->query("SELECT SUM(amount) as month_monetization_withdrawals FROM monetization_payments WHERE status = '0' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_pending_monetization_withdrawals = $get_total_pending_month_monetization_withdrawals->fetch_assoc()['month_monetization_withdrawals'];
          $get_total_approved_month_monetization_withdrawals = $db->query("SELECT SUM(amount) as month_pending_monetization_withdrawals FROM monetization_payments WHERE status = '1' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_approved_monetization_withdrawals = $get_total_approved_month_monetization_withdrawals->fetch_assoc()['month_pending_monetization_withdrawals'];
          /* get affiliate withdrawals (affiliates_payments) */
          $get_total_pending_affiliate_withdrawals = $db->query("SELECT SUM(amount) as total_affiliate_withdrawals FROM affiliates_payments WHERE status = '0'");
          $total_pending_affiliate_withdrawals = $get_total_pending_affiliate_withdrawals->fetch_assoc()['total_affiliate_withdrawals'];
          $get_total_approved_affiliate_withdrawals = $db->query("SELECT SUM(amount) as total_pending_affiliate_withdrawals FROM affiliates_payments WHERE status = '1'");
          $total_approved_affiliate_withdrawals = $get_total_approved_affiliate_withdrawals->fetch_assoc()['total_pending_affiliate_withdrawals'];
          $get_total_pending_month_affiliate_withdrawals = $db->query("SELECT SUM(amount) as month_affiliate_withdrawals FROM affiliates_payments WHERE status = '0' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_pending_affiliate_withdrawals = $get_total_pending_month_affiliate_withdrawals->fetch_assoc()['month_affiliate_withdrawals'];
          $get_total_approved_month_affiliate_withdrawals = $db->query("SELECT SUM(amount) as month_pending_affiliate_withdrawals FROM affiliates_payments WHERE status = '1' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_approved_affiliate_withdrawals = $get_total_approved_month_affiliate_withdrawals->fetch_assoc()['month_pending_affiliate_withdrawals'];
          /* get points withdrawals (points_payments) */
          $get_total_pending_points_withdrawals = $db->query("SELECT SUM(amount) as total_points_withdrawals FROM points_payments WHERE status = '0'");
          $total_pending_points_withdrawals = $get_total_pending_points_withdrawals->fetch_assoc()['total_points_withdrawals'];
          $get_total_approved_points_withdrawals = $db->query("SELECT SUM(amount) as total_pending_points_withdrawals FROM points_payments WHERE status = '1'");
          $total_approved_points_withdrawals = $get_total_approved_points_withdrawals->fetch_assoc()['total_pending_points_withdrawals'];
          $get_total_pending_month_points_withdrawals = $db->query("SELECT SUM(amount) as month_points_withdrawals FROM points_payments WHERE status = '0' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_pending_points_withdrawals = $get_total_pending_month_points_withdrawals->fetch_assoc()['month_points_withdrawals'];
          $get_total_approved_month_points_withdrawals = $db->query("SELECT SUM(amount) as month_pending_points_withdrawals FROM points_payments WHERE status = '1' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_approved_points_withdrawals = $get_total_approved_month_points_withdrawals->fetch_assoc()['month_pending_points_withdrawals'];
          /* get market withdrawals (market_payments) */
          $get_total_pending_market_withdrawals = $db->query("SELECT SUM(amount) as total_market_withdrawals FROM market_payments WHERE status = '0'");
          $total_pending_market_withdrawals = $get_total_pending_market_withdrawals->fetch_assoc()['total_market_withdrawals'];
          $get_total_approved_market_withdrawals = $db->query("SELECT SUM(amount) as total_pending_market_withdrawals FROM market_payments WHERE status = '1'");
          $total_approved_market_withdrawals = $get_total_approved_market_withdrawals->fetch_assoc()['total_pending_market_withdrawals'];
          $get_total_pending_month_market_withdrawals = $db->query("SELECT SUM(amount) as month_market_withdrawals FROM market_payments WHERE status = '0' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_pending_market_withdrawals = $get_total_pending_month_market_withdrawals->fetch_assoc()['month_market_withdrawals'];
          $get_total_approved_month_market_withdrawals = $db->query("SELECT SUM(amount) as month_pending_market_withdrawals FROM market_payments WHERE status = '1' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_approved_market_withdrawals = $get_total_approved_month_market_withdrawals->fetch_assoc()['month_pending_market_withdrawals'];
          /* get funding withdrawals (funding_payments) */
          $get_total_pending_fundings_withdrawals = $db->query("SELECT SUM(amount) as total_fundings_withdrawals FROM funding_payments WHERE status = '0'");
          $total_pending_fundings_withdrawals = $get_total_pending_fundings_withdrawals->fetch_assoc()['total_fundings_withdrawals'];
          $get_total_approved_fundings_withdrawals = $db->query("SELECT SUM(amount) as total_pending_fundings_withdrawals FROM funding_payments WHERE status = '1'");
          $total_approved_fundings_withdrawals = $get_total_approved_fundings_withdrawals->fetch_assoc()['total_pending_fundings_withdrawals'];
          $get_total_pending_month_fundings_withdrawals = $db->query("SELECT SUM(amount) as month_fundings_withdrawals FROM funding_payments WHERE status = '0' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_pending_fundings_withdrawals = $get_total_pending_month_fundings_withdrawals->fetch_assoc()['month_fundings_withdrawals'];
          $get_total_approved_month_fundings_withdrawals = $db->query("SELECT SUM(amount) as month_pending_fundings_withdrawals FROM funding_payments WHERE status = '1' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_approved_fundings_withdrawals = $get_total_approved_month_fundings_withdrawals->fetch_assoc()['month_pending_fundings_withdrawals'];
          /* get wallet withdrawals (wallet_payments) */
          $get_total_pending_wallet_withdrawals = $db->query("SELECT SUM(amount) as total_wallet_withdrawals FROM wallet_payments WHERE status = '0'");
          $total_pending_wallet_withdrawals = $get_total_pending_wallet_withdrawals->fetch_assoc()['total_wallet_withdrawals'];
          $get_total_approved_wallet_withdrawals = $db->query("SELECT SUM(amount) as total_pending_wallet_withdrawals FROM wallet_payments WHERE status = '1'");
          $total_approved_wallet_withdrawals = $get_total_approved_wallet_withdrawals->fetch_assoc()['total_pending_wallet_withdrawals'];
          $get_total_pending_month_wallet_withdrawals = $db->query("SELECT SUM(amount) as month_wallet_withdrawals FROM wallet_payments WHERE status = '0' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_pending_wallet_withdrawals = $get_total_pending_month_wallet_withdrawals->fetch_assoc()['month_wallet_withdrawals'];
          $get_total_approved_month_wallet_withdrawals = $db->query("SELECT SUM(amount) as month_pending_wallet_withdrawals FROM wallet_payments WHERE status = '1' AND MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          $month_approved_wallet_withdrawals = $get_total_approved_month_wallet_withdrawals->fetch_assoc()['month_pending_wallet_withdrawals'];
          /* calculate total pending payouts */
          $total_pending_payout = $total_pending_monetization_withdrawals + $total_pending_affiliate_withdrawals + $total_pending_points_withdrawals + $total_pending_market_withdrawals + $total_pending_fundings_withdrawals + $total_pending_wallet_withdrawals;
          /* calculate this month pending payouts */
          $month_pending_payout = $month_pending_monetization_withdrawals + $month_pending_affiliate_withdrawals + $month_pending_points_withdrawals + $month_pending_market_withdrawals + $month_pending_fundings_withdrawals + $month_pending_wallet_withdrawals;
          /* calculate total approved payouts */
          $total_approved_payout = $total_approved_monetization_withdrawals + $total_approved_affiliate_withdrawals + $total_approved_points_withdrawals + $total_approved_market_withdrawals + $total_approved_fundings_withdrawals + $total_approved_wallet_withdrawals;
          /* calculate this month approved payouts */
          $month_approved_payout = $month_approved_monetization_withdrawals + $month_approved_affiliate_withdrawals + $month_approved_points_withdrawals + $month_approved_market_withdrawals + $month_approved_fundings_withdrawals + $month_approved_wallet_withdrawals;

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM log_payments INNER JOIN users ON log_payments.user_id = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/earnings?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM log_payments INNER JOIN users ON log_payments.user_id = users.user_id ORDER BY log_payments.payment_id DESC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('payment_methods', $payment_methods);
          $smarty->assign('payment_methods_chart', $payment_methods_chart);
          $smarty->assign('payment_handles', $payment_handles);
          $smarty->assign('payment_handles_chart', $payment_handles_chart);
          $smarty->assign('total_payin', $total_payin);
          $smarty->assign('month_payin', $month_payin);
          $smarty->assign('total_pending_payout', $total_pending_payout);
          $smarty->assign('month_pending_payout', $month_pending_payout);
          $smarty->assign('total_approved_payout', $total_approved_payout);
          $smarty->assign('month_approved_payout', $month_approved_payout);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'commissions':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Earnings") . " &rsaquo; " . __("Commissions"));

          // get chart data
          $commissions_handles_chart = [];
          $commissions_handles = [];
          $get_commissions_handles = $db->query("SELECT DISTINCT handle FROM log_commissions");
          while ($handle = $get_commissions_handles->fetch_assoc()) {
            $commissions_handles[] = $handle['handle'];
          }
          for ($i = 1; $i <= 12; $i++) {
            $commissions_handles_chart[$i] = [];
            foreach ($commissions_handles as $handle) {
              $result = $db->query("SELECT SUM(amount) as total_amount FROM log_commissions WHERE YEAR(time) = YEAR(CURRENT_DATE()) AND MONTH(time) = $i AND handle = '$handle'");
              $row = $result->fetch_assoc();
              /* round to 2 decimal places */
              $commissions_handles_chart[$i][$handle] = ($row['total_amount']) ? round($row['total_amount'], 2) : 0;
            }
          }

          // calculate commissions
          /* calculate total commissions */
          $total_commissions = 0;
          $get_total_commissions = $db->query("SELECT SUM(amount) as total_commissions FROM log_commissions");
          if ($get_total_commissions->num_rows > 0) {
            $total_commissions = $get_total_commissions->fetch_assoc()['total_commissions'];
          }
          /* calculate this month commissions */
          $month_commissions = 0;
          $get_month_commissions = $db->query("SELECT SUM(amount) as month_commissions FROM log_commissions WHERE MONTH(time) = MONTH(CURRENT_DATE()) AND YEAR(time) = YEAR(CURRENT_DATE())");
          if ($get_month_commissions->num_rows > 0) {
            $month_commissions = $get_month_commissions->fetch_assoc()['month_commissions'];
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM log_commissions INNER JOIN users ON log_commissions.user_id = users.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/earnings?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM log_commissions INNER JOIN users ON log_commissions.user_id = users.user_id ORDER BY log_commissions.payment_id DESC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('commissions_handles', $commissions_handles);
          $smarty->assign('commissions_handles_chart', $commissions_handles_chart);
          $smarty->assign('total_commissions', $total_commissions);
          $smarty->assign('month_commissions', $month_commissions);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'packages':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Earnings") . " &rsaquo; " . __("Packages"));

          // get data
          $total_earnings = 0;
          $month_earnings = 0;
          $get_rows = $db->query("SELECT * FROM packages_payments");
          $rows = [];
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row_month = date("n", strtotime($row['payment_date']));
              $row_year = date("Y", strtotime($row['payment_date']));
              if ($rows[$row['package_name']]) {
                $rows[$row['package_name']]['sales']++;
                if ($rows[$row['package_name']]['months_sales'][$row_month]) {
                  $rows[$row['package_name']]['months_sales'][$row_month]++;
                } else {
                  $rows[$row['package_name']]['months_sales'][$row_month] = 1;
                }
                $rows[$row['package_name']]['earnings'] += $row['package_price'];
                /* add to current month earnings */
                if ($row_month == date('n') && $row_year == date('Y')) {
                  $month_earnings += $row['package_price'];
                }
                /* add to total earnings */
                $total_earnings += $row['package_price'];
              } else {
                $rows[$row['package_name']]['sales'] = 1;
                $rows[$row['package_name']]['months_sales'][$row_month] = 1;
                $rows[$row['package_name']]['earnings'] = $row['package_price'];
                /* add to current month earnings */
                if ($row_month == date('n') && $row_year == date('Y')) {
                  $month_earnings += $row['package_price'];
                }
                /* add to total earnings */
                $total_earnings += $row['package_price'];
              }
            }
          }
          /* prepare months sales */
          if ($rows) {
            foreach ($rows as $key => $value) {
              for ($i = 1; $i <= 12; $i++) {
                if ($rows[$key]['months_sales'][$i]) {
                  continue;
                } else {
                  $rows[$key]['months_sales'][$i] = 0;
                }
              }
              ksort($rows[$key]['months_sales']);
            }
          }

          // assign variables
          $smarty->assign('total_earnings', $total_earnings);
          $smarty->assign('month_earnings', $month_earnings);
          $smarty->assign('rows', $rows);
          break;


        case 'movies':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Earnings") . " &rsaquo; " . __("Movies"));

          // calculate earnings
          $total_earnings = 0;
          $get_total_earnings = $db->query("SELECT SUM(price) as total_earnings FROM movies_payments INNER JOIN users ON movies_payments.user_id = users.user_id INNER JOIN movies ON movies_payments.movie_id = movies.movie_id");
          if ($get_total_earnings->num_rows > 0) {
            $total_earnings = $get_total_earnings->fetch_assoc()['total_earnings'];
          }
          $month_earnings = 0;
          $get_month_earnings = $db->query("SELECT SUM(price) as month_earnings FROM movies_payments INNER JOIN users ON movies_payments.user_id = users.user_id INNER JOIN movies ON movies_payments.movie_id = movies.movie_id WHERE MONTH(payment_time) = MONTH(CURRENT_DATE()) AND YEAR(payment_time) = YEAR(CURRENT_DATE())");
          if ($get_month_earnings->num_rows > 0) {
            $month_earnings = $get_month_earnings->fetch_assoc()['month_earnings'];
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM movies_payments INNER JOIN users ON movies_payments.user_id = users.user_id INNER JOIN movies ON movies_payments.movie_id = movies.movie_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/earnings/movies?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT * FROM movies_payments INNER JOIN users ON movies_payments.user_id = users.user_id INNER JOIN movies ON movies_payments.movie_id = movies.movie_id ORDER BY movies_payments.id DESC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['movie_url'] = get_url_text($row['title']);
              $row['poster'] = get_picture($row['poster'], 'movie');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('total_earnings', $total_earnings);
          $smarty->assign('month_earnings', $month_earnings);
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'ads':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("Settings"));
          break;

        case 'users_ads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("Users Ads"));

          // get data
          $get_rows = $db->query("SELECT ads_campaigns.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM ads_campaigns INNER JOIN users ON ads_campaigns.campaign_user_id = users.user_id WHERE ads_campaigns.campaign_is_declined = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              if ($row['campaign_is_approved']) {
                $rows["approved"][] = $row;
              } else {
                $rows["pending"][] = $row;
              }
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'system_ads':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("System Ads"));

          // get data
          $get_rows = $db->query("SELECT * FROM ads_system");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM ads_system WHERE ads_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Ads") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'wallet':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Wallet"));
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Wallet") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT wallet_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM wallet_payments INNER JOIN users ON wallet_payments.user_id = users.user_id WHERE wallet_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'moneypoolscash':
                  $row['method_color'] = "success";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['wallet_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'pro':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pro System"));
          break;

        case 'packages':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pro System") . " &rsaquo; " . __("Packages"));

          // get data
          $get_rows = $db->query("SELECT packages.*, permissions_groups.permissions_group_title FROM packages LEFT JOIN permissions_groups ON packages.package_permissions_group_id = permissions_groups.permissions_group_id");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['icon'] = get_picture($row['icon'], 'package');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT packages.*, permissions_groups.permissions_group_title FROM packages LEFT JOIN permissions_groups ON packages.package_permissions_group_id = permissions_groups.permissions_group_id WHERE packages.package_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get permissions groups */
          $data['permissions_groups'] = $user->get_permissions_groups();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pro System") . " &rsaquo; " . $data['name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pro System") . " &rsaquo; " . __("Add New"));

          // get permissions groups
          $smarty->assign('permissions_groups', $user->get_permissions_groups());
          break;

        case 'subscribers':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Pro System") . " &rsaquo; " . __("Subscribers"));

          // get data
          $get_rows = $db->query("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscription_date, packages.* FROM users INNER JOIN packages ON users.user_package = packages.package_id WHERE users.user_subscribed = '1'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['icon'] = get_picture($row['icon'], 'package');
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['period']) {
                case 'day':
                  $duration = 86400;
                  break;

                case 'week':
                  $duration = 604800;
                  break;

                case 'month':
                  $duration = 2629743;
                  break;

                case 'year':
                  $duration = 31556926;
                  break;
              }
              $row['subscription_end'] = strtotime($row['user_subscription_date']) + ($row['period_num'] * $duration);
              $row['subscription_timeleft'] = ceil(($row['subscription_end'] - time()) / (60 * 60 * 24));
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'affiliates':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Affiliates"));
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Affiliates") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT affiliates_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM affiliates_payments INNER JOIN users ON affiliates_payments.user_id = users.user_id WHERE affiliates_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'moneypoolscash':
                  $row['method_color'] = "success";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['affiliate_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'points':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Points System"));
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Points System") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT points_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM points_payments INNER JOIN users ON points_payments.user_id = users.user_id WHERE points_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'moneypoolscash':
                  $row['method_color'] = "success";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['points_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'market':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace"));
          break;

        case 'products':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Products"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = 'product' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL)");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/products?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_products.name, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = 'product' LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Products"));

          // valid inputs
          if (!isset($_GET['query']) || is_empty($_GET['query'])) {
            _error(404);
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = "product" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_products.name LIKE %1$s OR posts_products.location LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_products.name, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.* FROM posts INNER JOIN posts_products ON posts.post_id = posts_products.post_id AND posts.post_type = "product" LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = "page" WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.text LIKE %1$s OR posts_products.name LIKE %1$s OR posts_products.location LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* check the post author type */
              if ($row['user_type'] == "user") {
                /* user */
                $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
                $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              } else {
                /* page */
                $row['post_author_picture'] = get_picture($row['page_picture'], "page");
                $row['post_author_url'] = $system['system_url'] . '/pages/' . $row['page_name'];
                $row['post_author_name'] = $row['page_title'];
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'orders':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Orders"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/orders?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT orders.*, u1.user_name AS buyer_username, u1.user_firstname AS buyer_firstname, u1.user_lastname AS buyer_lastname, u1.user_picture AS buyer_user_picture, u1.user_gender AS buyer_user_gender, u2.user_name AS seller_username, u2.user_firstname AS seller_firstname, u2.user_lastname AS seller_lastname, u2.user_picture AS seller_user_picture, u2.user_gender AS seller_user_gender FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id ORDER BY orders.order_id DESC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* prepare buyer's info */
              $row['buyer_fullname'] = ($system['show_usernames_enabled']) ? $row['buyer_username'] : $row['buyer_firstname'] . ' ' . $row['buyer_lastname'];
              $row['buyer_picture'] = get_picture($row['buyer_user_picture'], $row['buyer_user_gender']);
              /* prepare seller's info */
              $row['seller_fullname'] = ($system['show_usernames_enabled']) ? $row['seller_username'] : $row['seller_firstname'] . ' ' . $row['seller_lastname'];
              $row['seller_picture'] = get_picture($row['seller_user_picture'], $row['seller_user_gender']);
              /* prepare total commission */
              $row['total_commission'] = $row['sub_total'] * ($row['commission'] / 100);
              /* prepare final price */
              $row['final_price'] = $row['sub_total'] - $row['total_commission'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'find_orders':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Orders"));

          // valid inputs
          if (!isset($_GET['query']) || is_empty($_GET['query'])) {
            _error(404);
          }

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id WHERE orders.order_hash LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/find_orders?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT orders.*, u1.user_name AS buyer_username, u1.user_firstname AS buyer_firstname, u1.user_lastname AS buyer_lastname, u1.user_picture AS buyer_user_picture, u1.user_gender AS buyer_user_gender, u2.user_name AS seller_username, u2.user_firstname AS seller_firstname, u2.user_lastname AS seller_lastname, u2.user_picture AS seller_user_picture, u2.user_gender AS seller_user_gender FROM orders INNER JOIN users u1 ON orders.buyer_id = u1.user_id INNER JOIN users u2 ON orders.seller_id = u2.user_id WHERE orders.order_hash LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* prepare buyer's info */
              $row['buyer_fullname'] = ($system['show_usernames_enabled']) ? $row['buyer_username'] : $row['buyer_firstname'] . ' ' . $row['buyer_lastname'];
              $row['buyer_picture'] = get_picture($row['buyer_user_picture'], $row['buyer_user_gender']);
              /* prepare seller's info */
              $row['seller_fullname'] = ($system['show_usernames_enabled']) ? $row['seller_username'] : $row['seller_firstname'] . ' ' . $row['seller_lastname'];
              $row['seller_picture'] = get_picture($row['seller_user_picture'], $row['seller_user_gender']);
              /* prepare total commission */
              $row['total_commission'] = $row['sub_total'] * ($row['commission'] / 100);
              /* prepare final price */
              $row['final_price'] = $row['sub_total'] - $row['total_commission'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("market_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM market_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("market_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("market_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Marketplace") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT market_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM market_payments INNER JOIN users ON market_payments.user_id = users.user_id WHERE market_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'moneypoolscash':
                  $row['method_color'] = "success";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['market_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'funding':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Funding"));
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Funding") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT funding_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM funding_payments INNER JOIN users ON funding_payments.user_id = users.user_id WHERE funding_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'moneypoolscash':
                  $row['method_color'] = "success";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['funding_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'requests':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Funding") . " &rsaquo; " . __("Funding Requests"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query("SELECT COUNT(*) as count FROM posts INNER JOIN posts_funding ON posts.post_id = posts_funding.post_id AND posts.post_type = 'funding' INNER JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user'");
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/funding/requests?page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query("SELECT posts.*, posts_funding.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM posts INNER JOIN posts_funding ON posts.post_id = posts_funding.post_id AND posts.post_type = 'funding' INNER JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' ORDER BY posts.post_id ASC " . $limit_query);
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        case 'find':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Funding") . " &rsaquo; " . __("Funding Requests"));

          // get data
          require('includes/class-pager.php');
          $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
          $total = $db->query(sprintf('SELECT COUNT(*) as count FROM posts INNER JOIN posts_funding ON posts.post_id = posts_funding.post_id AND posts.post_type = "funding" INNER JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" WHERE posts.text LIKE %1$s OR posts_funding.title LIKE %1$s', secure($_GET['query'], 'search')));
          $params['total_items'] = $total->fetch_assoc()['count'];
          $params['items_per_page'] = $system['max_results'];
          $params['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/funding/find?query=' . $_GET['query'] . '&page=%s';
          $pager = new Pager($params);
          $limit_query = $pager->getLimitSql();
          $get_rows = $db->query(sprintf('SELECT posts.*, posts_funding.title, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM posts INNER JOIN posts_funding ON posts.post_id = posts_funding.post_id AND posts.post_type = "funding" INNER JOIN users ON posts.user_id = users.user_id AND posts.user_type = "user" WHERE posts.text LIKE %1$s OR posts_funding.title LIKE %1$s ' . $limit_query, secure($_GET['query'], 'search')));
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['post_author_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $row['post_author_url'] = $system['system_url'] . '/' . $row['user_name'];
              $row['post_author_name'] = ($system['show_usernames_enabled']) ? $row['user_name'] : $row['user_firstname'] . " " . $row['user_lastname'];
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          $smarty->assign('pager', $pager->getPager());
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'monetization':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Monetization"));
          break;

        case 'payments':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Monetization") . " &rsaquo; " . __("Payment Requests"));

          // get data
          $get_rows = $db->query("SELECT monetization_payments.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM monetization_payments INNER JOIN users ON monetization_payments.user_id = users.user_id WHERE monetization_payments.status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              switch ($row['method']) {
                case 'paypal':
                  $row['method_color'] = "info";
                  break;

                case 'skrill':
                  $row['method_color'] = "primary";
                  break;

                case 'moneypoolscash':
                  $row['method_color'] = "success";
                  break;

                case 'bank':
                  $row['method_color'] = "danger";
                  break;

                case 'custom':
                  $row['method'] = $system['monetization_payment_method_custom'];
                  $row['method_color'] = "warning";
                  break;
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'tips':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Tips"));
      break;

    case 'coinpayments':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("CoinPayments Transactions"));

      // get data
      $coinpayments_transactions = $user->get_coinpayments_transactions(true);

      // assign variables
      $smarty->assign('coinpayments_transactions', $coinpayments_transactions);
      break;

    case 'bank':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Bank Receipts"));

      // get data
      $get_rows = $db->query("SELECT bank_transfers.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, packages.name as package_name, packages.price as package_price FROM bank_transfers INNER JOIN users ON bank_transfers.user_id = users.user_id LEFT JOIN packages ON bank_transfers.package_id = packages.package_id WHERE bank_transfers.status = '0'");
      if ($get_rows->num_rows > 0) {
        while ($row = $get_rows->fetch_assoc()) {
          $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
          $rows[] = $row;
        }
      }

      // assign variables
      $smarty->assign('rows', $rows);
      break;

    case 'developers':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Settings"));
          break;

        case 'apps':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Apps"));

          // get data
          $get_rows = $db->query("SELECT developers_apps.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM developers_apps INNER JOIN users ON developers_apps.app_user_id = users.user_id");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("developers_apps_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM developers_apps_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("developers_apps_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Developers") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("developers_apps_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'reports':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports"));

          // get data
          $get_rows = $db->query("SELECT reports.*, reports_categories.category_name, users.user_name, users.user_firstname, users.user_lastname, users.user_picture, users.user_gender FROM reports INNER JOIN users ON reports.user_id = users.user_id LEFT JOIN reports_categories ON reports.category_id = reports_categories.category_id");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              /* get reported node */
              if ($row['node_type'] == "user") {
                $get_node = $db->query(sprintf("SELECT user_name, user_firstname, user_lastname, user_gender, user_picture FROM users WHERE user_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['user_picture'] = get_picture($node['user_picture'], $node['user_gender']);
                $node['color'] = 'primary';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'page') {
                $get_node = $db->query(sprintf("SELECT page_name, page_title, page_picture FROM pages WHERE page_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['page_picture'] = get_picture($node['page_picture'], 'page');
                $node['color'] = 'info';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'group') {
                $get_node = $db->query(sprintf("SELECT group_name, group_title, group_picture FROM `groups` WHERE group_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['group_picture'] = get_picture($node['group_picture'], 'group');
                $node['color'] = 'warning';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'event') {
                $get_node = $db->query(sprintf("SELECT event_title, event_cover FROM `events` WHERE event_id = %s", secure($row['node_id'], 'int')));
                if ($get_node->num_rows == 0) continue;
                $node = $get_node->fetch_assoc();
                $node['event_picture'] = get_picture($node['event_cover'], 'event');
                $node['color'] = 'success';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'comment') {
                $comment = $user->get_comment($row['node_id']);
                if (!$comment) continue;
                if ($comment['node_type'] == "post") {
                  $_handle = '/posts/';
                  $_node_id = $comment['node_id'];
                } elseif ($comment['node_type'] == "photo") {
                  $_handle = '/photos/';
                  $_node_id = $comment['node_id'];
                } elseif ($comment['node_type'] == "comment") {
                  $_handle = ($comment['parent_comment']['node_type'] == "post") ? '/posts/' : '/photos/';
                  $_node_id = $comment['parent_comment']['node_id'];
                }
                $row['url'] = $system['system_url'] . $_handle . $_node_id . '?notify_id=comment_' . $row['node_id'];
                $node['color'] = 'secondary';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'post') {
                $node['color'] = 'danger';
                $node['name'] = $row['node_type'];
              } elseif ($row['node_type'] == 'forum_thread') {
                $thread = $user->get_forum_thread($row['node_id']);
                if (!$thread) continue;
                $row['url'] = $system['system_url'] . '/forums/thread/' . $thread['thread_id'] . '/' . $thread['title_url'];
                $node['color'] = 'secondary';
                $node['name'] = __("Forum Thread");
              } elseif ($row['node_type'] == 'forum_reply') {
                $reply = $user->get_forum_reply($row['node_id']);
                if (!$reply) continue;
                $row['url'] = $system['system_url'] . '/forums/thread/' . $reply['thread']['thread_id'] . '/' . $reply['thread']['title_url'] . '/#reply-' . $reply['reply_id'];
                $node['color'] = 'secondary';
                $node['name'] = __("Forum Reply");
              }
              $row['node'] = $node;
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'categories':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports") . " &rsaquo; " . __("Categories"));

          // get data
          $rows = $user->get_categories("reports_categories");

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit_category':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM reports_categories WHERE category_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();
          /* get categories */
          $data['categories'] = $user->get_categories("reports_categories");

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports") . " &rsaquo; " . __("Categories") . " &rsaquo; " . $data['category_name']);
          break;

        case 'add_category':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reports") . " &rsaquo; " . __("Categories") . " &rsaquo; " . __("Add New Category"));

          // get data
          $categories = $user->get_categories("reports_categories");

          // assign variables
          $smarty->assign('categories', $categories);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'blacklist':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blacklist"));

          // get data
          $get_rows = $db->query("SELECT * FROM blacklist");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Blacklist") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'verification':
      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Verification") . " &rsaquo; " . __("Requests"));

          // get data
          $get_rows = $db->query("SELECT verification_requests.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, pages.page_name, pages.page_title, pages.page_picture FROM verification_requests LEFT JOIN users ON verification_requests.node_type = 'user' AND verification_requests.node_id = users.user_id LEFT JOIN pages ON verification_requests.node_type = 'page' AND verification_requests.node_id = pages.page_id WHERE status = '0'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              /* get node */
              if ($row['node_type'] == "user") {
                $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
                $row['color'] = 'primary';
              } elseif ($row['node_type'] == 'page') {
                $row['page_picture'] = get_picture($row['page_picture'], 'page');
                $row['color'] = 'info';
              }
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'users':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Verification") . " &rsaquo; " . __("Verified Users"));

          // get data
          $get_rows = $db->query("SELECT * FROM users WHERE user_verified = '1'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['user_picture'] = get_picture($row['user_picture'], $row['user_gender']);
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'pages':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Verification") . " &rsaquo; " . __("Verified Pages"));

          // get data
          $get_rows = $db->query("SELECT * FROM pages WHERE page_verified = '1'");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $row['page_picture'] = get_picture($row['page_picture'], 'page');
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'tools':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case 'faker':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Fake Generator"));

          // get countries if not defined
          if (!$countries) {
            $smarty->assign('countries', $user->get_countries());
          }

          // get pages categories
          $smarty->assign('pages_categories', $user->get_categories("pages_categories"));

          // get groups categories
          $smarty->assign('groups_categories', $user->get_categories("groups_categories"));
          break;

        case 'auto-connect':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Auto Connect"));

          // get all custom auto-connect
          $get_rows = $db->query("SELECT * FROM auto_connect");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[$row['type']][] = $row;
            }
          }
          /* assign variables */
          $smarty->assign('rows', $rows);

          // get countries if not defined
          if (!$countries) {
            $smarty->assign('countries', $user->get_countries());
          }
          break;

        case 'garbage-collector':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Garbage Collector"));

          // prepare counts
          /* users not activated */
          $get_users_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
          $insights['users_not_activated'] = $get_users_not_activated->fetch_assoc()['count'];
          /* users not logged in 1 week */
          $get_users_not_logged_week = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 WEEK");
          $insights['users_not_logged_week'] = $get_users_not_logged_week->fetch_assoc()['count'];
          /* users not logged in 1 month */
          $get_users_not_logged_month = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 MONTH");
          $insights['users_not_logged_month'] = $get_users_not_logged_month->fetch_assoc()['count'];
          /* users not logged in 3 month3 */
          $get_users_not_logged_3_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 3 MONTH");
          $insights['users_not_logged_3_months'] = $get_users_not_logged_3_months->fetch_assoc()['count'];
          /* users not logged in 1 month */
          $get_users_not_logged_6_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 6 MONTH");
          $insights['users_not_logged_6_months'] = $get_users_not_logged_6_months->fetch_assoc()['count'];
          /* users not logged in 1 month */
          $get_users_not_logged_9_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 9 MONTH");
          $insights['users_not_logged_9_months'] = $get_users_not_logged_9_months->fetch_assoc()['count'];
          /* users not logged in 1 year */
          $get_users_not_logged_year = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 YEAR");
          $insights['users_not_logged_year'] = $get_users_not_logged_year->fetch_assoc()['count'];
          /* posts longer than 1 week */
          $get_posts_longer_week = $db->query("SELECT COUNT(*) as count FROM posts WHERE time < NOW() - INTERVAL 1 WEEK");
          $insights['posts_longer_week'] = $get_posts_longer_week->fetch_assoc()['count'];
          /* posts longer than 1 month */
          $get_posts_longer_month = $db->query("SELECT COUNT(*) as count FROM posts WHERE posts.time < NOW() - INTERVAL 1 MONTH");
          $insights['posts_longer_month'] = $get_posts_longer_month->fetch_assoc()['count'];
          /* posts longer than 1 year */
          $get_posts_longer_year = $db->query("SELECT COUNT(*) as count FROM posts WHERE posts.time < NOW() - INTERVAL 1 YEAR");
          $insights['posts_longer_year'] = $get_posts_longer_year->fetch_assoc()['count'];

          // assign variables
          $smarty->assign('insights', $insights);
          break;

        case 'backups':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Backup Database & Files"));
          break;

        case 'reset':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Tools") . " &rsaquo; " . __("Factory Reset"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'custom_fields':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Custom Fields"));

          // get data
          $get_rows = $db->query("SELECT * FROM custom_fields");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM custom_fields WHERE field_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Custom Fields") . " &rsaquo; " . $data['label']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Custom Fields") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'static':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Static Pages"));

          // get data
          $get_rows = $db->query("SELECT * FROM static_pages");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM static_pages WHERE page_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Static Pages") . " &rsaquo; " . $data['page_title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Static Pages") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'colored_posts':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Colored Posts"));

          // get data
          $rows = $user->get_posts_colored_patterns();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM posts_colored_patterns WHERE pattern_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Colored Posts") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Colored Posts") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'widgets':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Widgets"));

          // get data
          $get_rows = $db->query("SELECT * FROM widgets");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM widgets WHERE widget_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Widgets") . " &rsaquo; " . $data['title']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Widgets") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'reactions':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reactions"));

          // get data
          $rows = $user->get_reactions();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM system_reactions WHERE reaction_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Reactions") . " &rsaquo; " . __("Edit Reaction"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'emojis':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Emojis"));

          // get data
          $rows = $emojis;

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM emojis WHERE emoji_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Emojis") . " &rsaquo; " . __("Edit Emoji"));
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Emojis") . " &rsaquo; " . __("Add New Emoji"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'stickers':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Stickers"));

          // get data
          $rows = $user->get_stickers();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM stickers WHERE sticker_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Stickers") . " &rsaquo; " . __("Edit Sticker"));
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Stickers") . " &rsaquo; " . __("Add New Sticker"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'gifts':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Gifts"));

          // get data
          $rows = $user->get_gifts();

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM gifts WHERE gift_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Gifts") . " &rsaquo; " . __("Edit Gift"));
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Gifts") . " &rsaquo; " . __("Add New Gift"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'announcements':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // get nested view content
      switch ($_GET['sub_view']) {
        case '':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Announcements"));

          // get data
          $get_rows = $db->query("SELECT * FROM announcements");
          if ($get_rows->num_rows > 0) {
            while ($row = $get_rows->fetch_assoc()) {
              $rows[] = $row;
            }
          }

          // assign variables
          $smarty->assign('rows', $rows);
          break;

        case 'edit':
          // valid inputs
          if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            _error(404);
          }

          // get data
          $get_data = $db->query(sprintf("SELECT * FROM announcements WHERE announcement_id = %s", secure($_GET['id'], 'int')));
          if ($get_data->num_rows == 0) {
            _error(404);
          }
          $data = $get_data->fetch_assoc();

          // assign variables
          $smarty->assign('data', $data);

          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Announcements") . " &rsaquo; " . $data['name']);
          break;

        case 'add':
          // page header
          page_header($control_panel['title'] . " &rsaquo; " . __("Announcements") . " &rsaquo; " . __("Add New"));
          break;

        default:
          _error(404);
          break;
      }
      break;

    case 'notifications':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Mass Notifications"));
      break;

    case 'newsletter':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Newsletter"));

      // prepare counts
      /* all users */
      $get_users_all = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '1'");
      $insights['users_all'] = $get_users_all->fetch_assoc()['count'];
      /* users activated */
      $get_users_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '1'");
      $insights['users_activated'] = $get_users_activated->fetch_assoc()['count'];
      /* users not activated */
      $get_users_not_activated = $db->query("SELECT COUNT(*) as count FROM users WHERE user_activated = '0'");
      $insights['users_not_activated'] = $get_users_not_activated->fetch_assoc()['count'];
      /* users not logged in from 1 week */
      $get_users_not_logged_week = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 WEEK");
      $insights['users_not_logged_week'] = $get_users_not_logged_week->fetch_assoc()['count'];
      /* users not logged in from 1 month */
      $get_users_not_logged_month = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 MONTH");
      $insights['users_not_logged_month'] = $get_users_not_logged_month->fetch_assoc()['count'];
      /* users not logged in from 3 month */
      $get_users_not_logged_3_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 3 MONTH");
      $insights['users_not_logged_3_months'] = $get_users_not_logged_3_months->fetch_assoc()['count'];
      /* users not logged in from 6 month */
      $get_users_not_logged_6_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 6 MONTH");
      $insights['users_not_logged_6_months'] = $get_users_not_logged_6_months->fetch_assoc()['count'];
      /* users not logged in from 9 month */
      $get_users_not_logged_9_months = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 9 MONTH");
      $insights['users_not_logged_9_months'] = $get_users_not_logged_9_months->fetch_assoc()['count'];
      /* not logged in 1 year */
      $get_users_not_logged_year = $db->query("SELECT COUNT(*) as count FROM users WHERE user_last_seen < NOW() - INTERVAL 1 YEAR");
      $insights['users_not_logged_year'] = $get_users_not_logged_year->fetch_assoc()['count'];

      // assign variables
      $smarty->assign('insights', $insights);
      break;

    case 'changelog':
      // check admin|moderator permission
      if ($user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }

      // page header
      page_header($control_panel['title'] . " &rsaquo; " . __("Changelog"));
      break;

    default:
      // check admin|moderator permission
      if (!$user->_is_admin || !$user->_is_moderator) {
        _error(__('System Message'), __("You don't have the right permission to access this"));
      }
      _error(404);
  }
  /* assign variables */
  $smarty->assign('view', $_GET['view']);
  $smarty->assign('sub_view', $_GET['sub_view']);
  $smarty->assign('control_panel', $control_panel);

  // global insights
  if ($user->_is_admin) {
    /* wallet payments insights */
    $get_wallet_payments = $db->query("SELECT COUNT(*) as count FROM wallet_payments INNER JOIN users ON wallet_payments.user_id = users.user_id WHERE wallet_payments.status = '0'");
    $wallet_payments_insights = $get_wallet_payments->fetch_assoc()['count'];
    $smarty->assign('wallet_payments_insights', $wallet_payments_insights);
    /* affiliates payments insights */
    $get_affiliates_payments = $db->query("SELECT COUNT(*) as count FROM affiliates_payments INNER JOIN users ON affiliates_payments.user_id = users.user_id WHERE affiliates_payments.status = '0'");
    $affiliates_payments_insights = $get_affiliates_payments->fetch_assoc()['count'];
    $smarty->assign('affiliates_payments_insights', $affiliates_payments_insights);
    /* points payments insights */
    $get_points_payments = $db->query("SELECT COUNT(*) as count FROM points_payments INNER JOIN users ON points_payments.user_id = users.user_id WHERE points_payments.status = '0'");
    $points_payments_insights = $get_points_payments->fetch_assoc()['count'];
    $smarty->assign('points_payments_insights', $points_payments_insights);
    /* funding payments insights */
    $get_funding_payments = $db->query("SELECT COUNT(*) as count FROM funding_payments INNER JOIN users ON funding_payments.user_id = users.user_id WHERE funding_payments.status = '0'");
    $funding_payments_insights = $get_funding_payments->fetch_assoc()['count'];
    $smarty->assign('funding_payments_insights', $funding_payments_insights);
    /* monetization payments insights */
    $get_monetization_payments = $db->query("SELECT COUNT(*) as count FROM monetization_payments INNER JOIN users ON monetization_payments.user_id = users.user_id WHERE monetization_payments.status = '0'");
    $monetization_payments_insights = $get_monetization_payments->fetch_assoc()['count'];
    $smarty->assign('monetization_payments_insights', $monetization_payments_insights);
    /* bank transfers insights */
    $get_bank_transfers = $db->query("SELECT COUNT(*) as count FROM bank_transfers INNER JOIN users ON bank_transfers.user_id = users.user_id LEFT JOIN packages ON bank_transfers.package_id = packages.package_id WHERE bank_transfers.status = '0'");
    $bank_transfers_insights = $get_bank_transfers->fetch_assoc()['count'];
    $smarty->assign('bank_transfers_insights', $bank_transfers_insights);
  }
  /* reports insights */
  $get_reports = $db->query("SELECT COUNT(*) as count FROM reports INNER JOIN users ON reports.user_id = users.user_id");
  $reports_insights = $get_reports->fetch_assoc()['count'];
  $smarty->assign('reports_insights', $reports_insights);
  /* verification requests insights */
  $get_verification_requests = $db->query("SELECT COUNT(*) as count FROM verification_requests WHERE status = '0'");
  $verification_requests_insights = $get_verification_requests->fetch_assoc()['count'];
  $smarty->assign('verification_requests_insights', $verification_requests_insights);
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page footer
page_footer('admin');
