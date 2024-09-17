<?php

/**
 * class -> user
 * 
 * @package Sngine
 * @author Zamblek
 */

/* 
 * Functions Index:
 * ----------------
 * __construct
 * System Countries
 * System Currencies
 * System Genders
 * Static Pages
 * Contact Us
 * User Groups & Permissions
 * Get Ids
 * Get Users
 * Search
 * User & Connections
 * Real-Time
 * Notifications
 * Chat
 * Video/Audio Chat
 * Live Stream
 * @Mentions
 * Popovers
 * Hashtags
 * Stories
 * Publisher
 * Posts
 * Posts Actions
 * Comments
 * Photos & Albums
 * Videos
 * Categories
 * Pages
 * Reviews
 * Groups
 * Events
 * Blogs
 * Jobs
 * Forums
 * Movies
 * Games
 * Ads
 * Wallet
 * CoinPayments
 * Pro Packages
 * Monetization
 * Recurring Payments
 * Logger
 * Affiliates
 * Points System
 * Funding
 * Shopping
 * Developers Apps
 * Reports
 * [Tools] Fake Generator
 * [Tools] Auto Connect
 * [Tools] Backups
 * Custom Fields
 * Widgets
 * Reactions & Emoji & Stickers
 * Gifts
 * Announcements
 * Invitations
 * User Settings
 * Verifications
 * Addresses
 * Download User Information
 * User Sign (in|up|out)
 * Login As
 * Connected Accounts
 * Getting Started
 * Social Login
 * Two-Factor Authentication
 * Password
 * Activation Email
 * Activation Phone
 * Security Checks
 */

class User
{

  public $_login_as = false;
  public $_logged_in = false;
  public $_is_admin = false;
  public $_is_moderator = false;
  public $_is_banned = false;
  public $_data = [];
  public $_master_data = [];

  private $_cookie_user_id = "c_user";
  private $_cookie_user_token = "xs";
  private $_cookie_user_referrer = "ref";
  private $_cookie_user_login_as = "log_as";

  /* ------------------------------- */
  /* __construct ✅ */
  /* ------------------------------- */

  /**
   * __construct
   * 
   * @param string $jwt
   * @return void
   */
  public function __construct($jwt = null)
  {
    global $db, $system;
    if (isset($_COOKIE[$this->_cookie_user_id]) && isset($_COOKIE[$this->_cookie_user_token])) {
      $user_id = $_COOKIE[$this->_cookie_user_id];
      $user_token = $_COOKIE[$this->_cookie_user_token];
    } elseif (isset(_getallheaders()["x-auth-token"]) || $jwt) {
      try {
        $jwt = ($jwt) ? $jwt : _getallheaders()["x-auth-token"];
        $jwt = (array) Firebase\JWT\JWT::decode($jwt, new Firebase\JWT\Key(JWT_SECRET, 'HS256'));
        $user_id = $jwt['uid'];
        $user_token = $jwt['token'];
      } catch (Exception $e) {
      }
    }
    if (isset($user_id) && isset($user_token)) {
      $get_user = $db->query(sprintf("SELECT users.*, users_groups.*, users_sessions.*, posts_photos.source as user_picture_full, packages.*, packages.name as package_name FROM users INNER JOIN users_sessions ON users.user_id = users_sessions.user_id LEFT JOIN posts_photos ON users.user_picture_id = posts_photos.photo_id LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN users_groups ON users.user_group = '3' AND users.user_group_custom != '0' AND users.user_group_custom = users_groups.user_group_id WHERE users_sessions.user_id = %s AND users_sessions.session_token = %s", secure($user_id, 'int'), secure($user_token))) or _error('SQL_ERROR_THROWEN');
      if ($get_user->num_rows > 0) {
        $this->_data = $get_user->fetch_assoc();
        /* check if there is 'login as' cookie */
        if ($this->_data['user_group'] == '1' && isset($_COOKIE[$this->_cookie_user_login_as]) && $_COOKIE[$this->_cookie_user_login_as] != $this->_data['user_id'] && $_COOKIE[$this->_cookie_user_login_as] != '1') {
          $login_as = $_COOKIE[$this->_cookie_user_login_as];
          $get_login_as = $db->query(sprintf("SELECT users.*, users_groups.*, users_sessions.*, posts_photos.source as user_picture_full, packages.*, packages.name as package_name FROM users LEFT JOIN users_sessions ON users.user_id = users_sessions.user_id LEFT JOIN posts_photos ON users.user_picture_id = posts_photos.photo_id LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN users_groups ON users.user_group = '3' AND users.user_group_custom != '0' AND users.user_group_custom = users_groups.user_group_id WHERE users.user_id = %s", secure($login_as, 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_login_as->num_rows > 0) {
            $login_as_user = $get_login_as->fetch_assoc();
            /* check this user is not admin */
            $this->_login_as = true;
            /* transfer old _data to _old_data */
            $this->_master_data = $this->_data;
            /* set new _data */
            $this->_data = $login_as_user;
          }
        }
        /* check if master account is not set */
        if ($this->_data['user_master_account'] == 0) {
          $db->query(sprintf("UPDATE users SET user_master_account = user_id WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          $this->_data['user_master_account'] = $this->_data['user_id'];
        }
        /* get user permissions group */
        if ($this->_data['user_group'] == '3') {
          $user_permissions_group = 1;
          if ($system['packages_enabled'] && $this->_data['user_subscribed']) {
            if ($this->_data['package_permissions_group_id']) {
              $user_permissions_group = $this->_data['package_permissions_group_id'];
            } elseif ($this->_data['user_verified']) {
              $user_permissions_group = 2;
            }
          } else {
            if ($this->_data['user_group_custom'] != '0') {
              $user_permissions_group = $this->_data['permissions_group_id'];
            } elseif ($this->_data['user_verified']) {
              $user_permissions_group = 2;
            }
          }
          $this->_data['user_permissions_group'] = $this->get_permissions_group($user_permissions_group);
        }
        /* get master connected accounts */
        $this->_data['connected_accounts'] = $this->get_connected_accounts($this->_data['user_master_account']);
        /* prepare full name */
        $this->_data['name'] = ($system['show_usernames_enabled']) ? $this->_data['user_name'] : $this->_data['user_firstname'] . " " . $this->_data['user_lastname'];
        /* check unusual login */
        if ($system['unusual_login_enabled']) {
          if ($this->_data['user_browser'] != get_user_browser() || $this->_data['user_os'] != get_user_os() || $this->_data['user_ip'] != get_user_ip()) {
            return;
          }
        }
        $this->_logged_in = true;
        $this->_is_admin = ($this->_data['user_group'] == 1) ? true : false;
        $this->_is_moderator = ($this->_data['user_group'] == 2) ? true : false;
        $this->_is_banned = (!$this->_is_admin && $this->_data['user_banned']) ? true : false;
        $this->_data['user_banned_message'] = ($this->_data['user_banned_message']) ? $this->_data['user_banned_message'] : __("Your account has been banned");
        /* check if user adult */
        $this->_data['user_age'] = get_user_age($this->_data['user_birthdate']);
        $this->_data['user_adult'] = ($this->_data['user_age'] >= 18) ? true : false;
        /* update user language */
        if ($system['current_language'] != $this->_data['user_language']) {
          $db->query(sprintf("UPDATE users SET user_language = %s WHERE user_id = %s", secure($system['current_language']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        /* update user last seen */
        $db->query(sprintf("UPDATE users SET user_last_seen = NOW() WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* active session */
        $this->_data['active_session_id'] = $this->_data['session_id'];
        $this->_data['active_session_token'] = $this->_data['session_token'];
        /* get user picture */
        $this->_data['user_picture_default'] = ($this->_data['user_picture']) ? false : true;
        $this->_data['user_picture_raw'] = $this->_data['user_picture'];
        $this->_data['user_picture'] = get_picture($this->_data['user_picture'], $this->_data['user_gender']);
        $this->_data['user_picture_full'] = ($this->_data['user_picture_full']) ? $system['system_uploads'] . '/' . $this->_data['user_picture_full'] : $this->_data['user_picture_full'];
        /* get all friends ids */
        $this->_data['friends_ids'] = $this->get_friends_ids();
        /* get all followings ids */
        $this->_data['followings_ids'] = $this->get_followings_ids();
        /* get all friend requests ids */
        $this->_data['friend_requests_ids'] = $this->get_friend_requests_ids();
        /* get all friend requests sent ids */
        $this->_data['friend_requests_sent_ids'] = $this->get_friend_requests_sent_ids();
        /* package permissions */
        $this->_data['can_boost_posts'] = false;
        $this->_data['can_boost_pages'] = false;
        $this->_data['can_pick_categories'] = false;
        $this->_data['user_products_limit'] = 0; /* 0 = unlimited */
        if ($system['packages_enabled'] && ($this->_is_admin || $this->_data['user_subscribed'])) {
          if ($this->_is_admin || ($this->_data['boost_posts_enabled'] && ($this->_data['user_boosted_posts'] < $this->_data['boost_posts']))) {
            $this->_data['can_boost_posts'] = true;
          }
          if ($this->_is_admin || ($this->_data['boost_pages_enabled'] && ($this->_data['user_boosted_pages'] < $this->_data['boost_pages']))) {
            $this->_data['can_boost_pages'] = true;
          }
          if ($this->_data['allowed_videos_categories'] > 0 || $this->_data['allowed_blogs_categories'] > 0) {
            $this->_data['can_pick_categories'] = true;
          }
          $this->_data['user_products_limit'] = $this->_data['allowed_products'];
        }
        /* get allowed videos & blogs categories ids */
        $this->_data['user_package_videos_categories_ids'] = ($this->_data['user_package_videos_categories']) ? array_column(json_decode(html_entity_decode($this->_data['user_package_videos_categories']), true), "id") : [];
        $this->_data['user_package_blogs_categories_ids'] = ($this->_data['user_package_blogs_categories']) ? array_column(json_decode(html_entity_decode($this->_data['user_package_blogs_categories']), true), "id") : [];
        /* check pages permission */
        $this->_data['can_create_pages'] = $system['pages_enabled'] && $this->check_module_permission('pages_permission');
        /* check groups permission */
        $this->_data['can_create_groups'] = $system['groups_enabled'] && $this->check_module_permission('groups_permission');
        /* check events permission */
        $this->_data['can_create_events'] = $system['events_enabled'] && $this->check_module_permission('events_permission');
        /* check blogs permission */
        $this->_data['can_write_articles'] = $system['blogs_enabled'] && $this->check_module_permission('blogs_permission');
        /* check market permission */
        $this->_data['can_sell_products'] = $system['market_enabled'] && $this->check_module_permission('market_permission');
        /* check offers permission */
        $this->_data['can_create_offers'] = $system['offers_enabled'] && $this->check_module_permission('offers_permission');
        /* check jobs permission */
        $this->_data['can_create_jobs'] = $system['jobs_enabled'] && $this->check_module_permission('jobs_permission');
        /* check forums permission */
        $this->_data['can_use_forums'] = $system['forums_enabled'] && $this->check_module_permission('forums_permission');
        /* check movies permission */
        $this->_data['can_watch_movies'] = $system['movies_enabled'] && $this->check_module_permission('movies_permission');
        /* check games permission */
        $this->_data['can_play_games'] = $system['games_enabled'] && $this->check_module_permission('games_permission');
        /* check gifts permission */
        $this->_data['can_send_gifts'] = $system['gifts_enabled'] && $this->check_module_permission('gifts_permission');
        /* check posts permission */
        $this->_data['can_publish_posts'] = $this->check_module_permission('posts_permission');
        /* check blogs read permission */
        $this->_data['can_read_articles'] = $system['blogs_enabled'] && $this->check_module_permission('blogs_permission_read');
        /* check videos read permission */
        $this->_data['can_watch_videos'] = $system['videos_enabled'] && $this->check_module_permission('videos_permission_read');
        /* check stories permission */
        $this->_data['can_add_stories'] = $system['stories_enabled'] && $this->check_module_permission('stories_permission');
        /* check colored posts permission */
        $this->_data['can_add_colored_posts'] = $system['colored_posts_enabled'] && $this->check_module_permission('colored_posts_permission');
        /* check activity posts permission */
        $this->_data['can_add_activity_posts'] = $system['activity_posts_enabled'] && $this->check_module_permission('activity_posts_permission');
        /* check polls posts permission */
        $this->_data['can_add_polls_posts'] = $system['polls_enabled'] && $this->check_module_permission('polls_posts_permission');
        /* check geolocation posts permission */
        $this->_data['can_add_geolocation_posts'] = $system['geolocation_enabled'] && $this->check_module_permission('geolocation_posts_permission');
        /* check gif posts permission */
        $this->_data['can_add_gif_posts'] = $system['gif_enabled'] && $this->check_module_permission('gif_posts_permission');
        /* check anonymous posts permission */
        $this->_data['can_add_anonymous_posts'] = $system['anonymous_mode'] && $this->check_module_permission('anonymous_posts_permission');
        /* check invitation permission */
        $this->_data['can_invite_users'] = $system['invitation_enabled'] && $this->check_module_permission('invitation_permission');
        /* check audio call permission */
        $this->_data['can_start_audio_call'] = $system['audio_call_enabled'] && $this->check_module_permission('audio_call_permission');
        /* check video call permission */
        $this->_data['can_start_video_call'] = $system['video_call_enabled'] && $this->check_module_permission('video_call_permission');
        /* check live stream permission */
        $this->_data['can_go_live'] = $system['live_enabled'] && $this->check_module_permission('live_permission');
        /* check videos upload permission */
        $this->_data['can_upload_videos'] = $system['videos_enabled'] && $this->check_module_permission('videos_upload_permission');
        /* check audios upload permission */
        $this->_data['can_upload_audios'] = $system['audio_enabled'] && $this->check_module_permission('audios_upload_permission');
        /* check files upload permission */
        $this->_data['can_upload_files'] = $system['file_enabled'] && $this->check_module_permission('files_upload_permission');
        /* check ads permission */
        $this->_data['can_create_ads'] = $system['ads_enabled'] && $this->check_module_permission('ads_permission');
        /* check funding permission */
        $this->_data['can_raise_funding'] = $system['funding_enabled'] && $this->check_module_permission('fundings_permission');
        /* check monetization permission */
        $this->_data['can_monetize_content'] = $system['monetization_enabled'] && $this->check_module_permission('monetization_permission');
        /* check tip permission */
        $this->_data['can_receive_tip'] = $system['tips_enabled'] && $this->check_module_permission('tips_permission');
        /* check is user ads free */
        $this->_data['ads_free'] = $system['packages_enabled'] && $this->_data['user_subscribed'] && $system['packages_ads_free_enabled'];
      }
    }
  }



  /* ------------------------------- */
  /* System Countries ✅ */
  /* ------------------------------- */

  /**
   * get_countries
   * 
   * @return array
   */
  public function get_countries()
  {
    global $db, $system;
    $countries = [];
    $get_countries = $db->query("SELECT * FROM system_countries WHERE enabled = '1' ORDER BY country_order ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_countries->num_rows > 0) {
      while ($country = $get_countries->fetch_assoc()) {
        $country['country_name_native'] = $country['country_name'];
        $country['country_name'] = __($country['country_name']);
        $countries[] = $country;
      }
    }
    return $countries;
  }


  /**
   * check_country
   * 
   * @param integer $country_id
   * @return boolean
   */
  public function check_country($country_id)
  {
    global $db;
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM system_countries WHERE country_id = %s", secure($country_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }


  /**
   * get_country
   * 
   * @param integer $country_id
   * @return array
   */
  public function get_country($country_id)
  {
    global $db;
    $get_country = $db->query(sprintf("SELECT * FROM system_countries WHERE country_id = %s", secure($country_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_country->num_rows == 0) {
      return null;
    }
    return $get_country->fetch_assoc();
  }


  /**
   * get_country_by_name
   * 
   * @param integer $country_name
   * @return array
   */
  public function get_country_by_name($country_name)
  {
    global $db;
    $get_country = $db->query(sprintf("SELECT * FROM system_countries WHERE country_name = %s", secure($country_name))) or _error('SQL_ERROR_THROWEN');
    if ($get_country->num_rows == 0) {
      return null;
    }
    return $get_country->fetch_assoc();
  }



  /* ------------------------------- */
  /* System Currencies ✅ */
  /* ------------------------------- */

  /**
   * get_currencies
   * 
   * @param boolean $get_all
   * @return array
   */
  public function get_currencies($get_all = false)
  {
    global $db, $system;
    $currencies = [];
    $where_statement = ($get_all) ? "" : "WHERE enabled = '1'";
    $get_currencies = $db->query("SELECT * FROM system_currencies {$where_statement} ORDER BY currency_id ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_currencies->num_rows > 0) {
      while ($currency = $get_currencies->fetch_assoc()) {
        $currencies[] = $currency;
      }
    }
    return $currencies;
  }



  /* ------------------------------- */
  /* System Genders ✅ */
  /* ------------------------------- */

  /**
   * get_genders
   * 
   * @param boolean $sorted
   * @return array
   */
  public function get_genders($sorted = true)
  {
    global $db, $system;
    $genders = [];
    $order_query = ($sorted) ? " ORDER BY gender_order ASC " : " ORDER BY gender_id ASC ";
    $get_genders = $db->query("SELECT * FROM system_genders" . $order_query) or _error('SQL_ERROR_THROWEN');
    if ($get_genders->num_rows > 0) {
      while ($gender = $get_genders->fetch_assoc()) {
        $gender['gender_name'] = __($gender['gender_name']);
        $genders[] = $gender;
      }
    }
    return $genders;
  }


  /**
   * get_gender
   * 
   * @param integer $gender_id
   * @return string
   */
  public function get_gender($gender_id)
  {
    global $db;
    $get_gender = $db->query(sprintf("SELECT gender_name FROM system_genders WHERE gender_id = %s", secure($gender_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_gender->num_rows == 0) {
      return __("N/A");
    }
    return __($get_gender->fetch_assoc()['gender_name']);
  }


  /**
   * check_gender
   * 
   * @param integer $gender_id
   * @return boolean
   */
  public function check_gender($gender_id)
  {
    global $db;
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM system_genders WHERE gender_id = %s", secure($gender_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }



  /* ------------------------------- */
  /* Static Pages ✅ */
  /* ------------------------------- */

  /**
   * get_static_pages
   * 
   * @param boolean $in_footer
   * 
   * @return array
   */
  public function get_static_pages($in_footer = true)
  {
    global $db;
    $static_pages = [];
    $where_statement = ($in_footer) ? "WHERE page_in_footer = '1'" : "";
    $get_static_pages = $db->query("SELECT page_url, page_title FROM static_pages $where_statement ORDER BY page_order ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_static_pages->num_rows > 0) {
      while ($static_page = $get_static_pages->fetch_assoc()) {
        $static_pages[] = $static_page;
      }
    }
    return $static_pages;
  }


  /**
   * get_static_page
   * 
   * @param string $page_url
   * @return array
   */
  public function get_static_page($page_url)
  {
    global $db;
    $get_static_page = $db->query(sprintf("SELECT * FROM static_pages WHERE page_url = %s", secure($page_url))) or _error('SQL_ERROR_THROWEN');
    if ($get_static_page->num_rows == 0) {
      throw new NoDataException(__("No data found"));
    }
    $static_page = $get_static_page->fetch_assoc();
    $static_page['page_text'] = html_entity_decode($static_page['page_text'], ENT_QUOTES);
    return $static_page;
  }



  /* ------------------------------- */
  /* Contact Us ✅ */
  /* ------------------------------- */

  /**
   * contact_us
   * 
   * @param string $name
   * @param string $email
   * @param string $subject
   * @param string $message
   * @param string $recaptcha_response
   * @param bool $from_web
   * @return array
   */
  public function contact_us($name, $email, $subject, $message, $recaptcha_response = null, $from_web = false)
  {
    global $system;
    /* valid inputs */
    if (is_empty($name) || is_empty($email) || is_empty($subject) || is_empty($message)) {
      throw new ValidationException(__("You must fill in all of the fields"));
    }
    if (!valid_email($email)) {
      throw new ValidationException(__("Please enter a valid email address"));
    }
    /* check reCAPTCHA */
    if ($system['reCAPTCHA_enabled'] && $from_web) {
      $recaptcha = new \ReCaptcha\ReCaptcha($system['reCAPTCHA_secret_key'], new \ReCaptcha\RequestMethod\CurlPost());
      $resp = $recaptcha->verify($_POST['g-recaptcha-response'], get_user_ip());
      if (!$resp->isSuccess()) {
        throw new ValidationException(__("The security check is incorrect. Please try again"));
      }
    }
    /* prepare email */
    $subject = __("New email message from") . " " . html_entity_decode(__($system['system_title']), ENT_QUOTES);
    $body = get_email_template("contact_form_email", $subject, ["name" => $name, "email" => $email, "subject" => $subject, "message" => $message]);
    /* send email */
    if (!_email($system['system_email'], $subject, $body['html'], $body['plain'])) {
      throw new Exception(__("Your email could not be sent. Please try again later"));
    }
  }



  /* ------------------------------- */
  /* User Groups & Permissions ✅ */
  /* ------------------------------- */

  /**
   * check_module_permission
   * 
   * @param string $permission
   * @return boolean
   */
  public function check_module_permission($permission)
  {
    global $db, $system;
    /* check if user is admin or moderator */
    if ($this->_is_admin || $this->_is_moderator) {
      return true;
    }
    return ($this->_data['user_permissions_group'][$permission]) ? true : false;
  }


  /**
   * check user permission
   * 
   * @param integer $user_id
   * @param string $permission
   * @return boolean
   */
  public function check_user_permission($user_id, $permission)
  {
    global $db, $system;
    $get_profile = $db->query(sprintf("SELECT users.user_id, users.user_group, users.user_group_custom, users.user_verified, users.user_subscribed , users_groups.permissions_group_id, packages.package_permissions_group_id FROM users LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN users_groups ON users.user_group = '3' AND users.user_group_custom != '0' AND users.user_group_custom = users_groups.user_group_id WHERE users.user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_profile->num_rows == 0) {
      return false;
    }
    $profile = $get_profile->fetch_assoc();
    /* if user is admin or moderator */
    if ($profile['user_group'] < '3') {
      return true;
    }
    /* get profile permissions group */
    $user_permissions_group = 1;
    /* check if pro packages enabled and user subscribed */
    if ($system['packages_enabled'] && $profile['user_subscribed']) {
      if ($profile['package_permissions_group_id']) {
        $user_permissions_group = $profile['package_permissions_group_id'];
      } elseif ($profile['user_verified']) {
        $user_permissions_group = 2;
      }
    } else {
      if ($profile['user_group_custom'] != '0') {
        $user_permissions_group = $profile['permissions_group_id'];
      } elseif ($profile['user_verified']) {
        $user_permissions_group = 2;
      }
    }
    $profile['user_permissions_group'] = $this->get_permissions_group($user_permissions_group);
    return ($profile['user_permissions_group'][$permission]) ? true : false;
  }


  /**
   * get_users_groups
   * 
   * @return array
   */
  public function get_users_groups()
  {
    global $db;
    $users_groups = [];
    $get_users_groups = $db->query("SELECT users_groups.*, permissions_groups.permissions_group_title, (SELECT COUNT(*) as count FROM users WHERE user_group_custom = users_groups.user_group_id) AS users_count FROM users_groups INNER JOIN permissions_groups ON users_groups.permissions_group_id = permissions_groups.permissions_group_id ORDER BY users_groups.user_group_id ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_users_groups->num_rows > 0) {
      while ($group = $get_users_groups->fetch_assoc()) {
        $users_groups[] = $group;
      }
    }
    return $users_groups;
  }


  /**
   * get_user_group
   * 
   * @param integer $user_group_id
   * @return boolean|array
   */
  public function get_user_group($user_group_id)
  {
    global $db;
    $get_user_group = $db->query(sprintf("SELECT users_groups.*, permissions_groups.permissions_group_title, (SELECT COUNT(*) as count FROM users WHERE user_group_custom = users_groups.user_group_id) AS users_count FROM users_groups INNER JOIN permissions_groups ON users_groups.permissions_group_id = permissions_groups.permissions_group_id WHERE user_group_id = %s", secure($user_group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_user_group->num_rows == 0) {
      return false;
    }
    return $get_user_group->fetch_assoc();
  }


  /**
   * check_user_group
   * 
   * @param integer $user_group_id
   * @return boolean
   */
  public function check_user_group($user_group_id)
  {
    global $db;
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM users_groups INNER JOIN permissions_groups ON users_groups.permissions_group_id = permissions_groups.permissions_group_id WHERE user_group_id = %s", secure($user_group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }


  /**
   * get_permissions_groups
   * 
   * @return array
   */
  public function get_permissions_groups()
  {
    global $db;
    $permissions_groups = [];
    $get_permissions_groups = $db->query("SELECT permissions_group_id, permissions_group_title FROM permissions_groups WHERE permissions_group_id > 2 ORDER BY permissions_group_id ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_permissions_groups->num_rows > 0) {
      while ($permission_group = $get_permissions_groups->fetch_assoc()) {
        $permissions_groups[] = $permission_group;
      }
    }
    return $permissions_groups;
  }


  /**
   * get_permissions_group
   * 
   * @param integer $permissions_group_id
   * @return false|array
   */
  public function get_permissions_group($permissions_group_id)
  {
    global $db;
    $get_permissions_group = $db->query(sprintf("SELECT * FROM permissions_groups WHERE permissions_group_id = %s", secure($permissions_group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_permissions_group->num_rows == 0) {
      return false;
    }
    return $get_permissions_group->fetch_assoc();
  }


  /**
   * check_permissions_group
   * 
   * @param integer $permissions_group_id
   * @return boolean
   */
  public function check_permissions_group($permissions_group_id)
  {
    global $db;
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM permissions_groups WHERE permissions_group_id = %s", secure($permissions_group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }



  /* ------------------------------- */
  /* Get Ids */
  /* ------------------------------- */

  /**
   * get_friends_ids
   * 
   * @param integer $user_id
   * @return array
   */
  public function get_friends_ids($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $friends = [];
    $get_friends = $db->query(sprintf('
          SELECT
            user_id
          FROM (
              SELECT
                  f.user_two_id AS friend_id
              FROM friends AS f
              WHERE f.status = 1 AND f.user_one_id = %1$s
              UNION
              SELECT
                  f.user_one_id AS friend_id
              FROM friends AS f
              WHERE f.status = 1 AND f.user_two_id = %1$s
            ) AS t
          INNER JOIN users AS u ON friend_id = u.user_id', secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_friends->num_rows > 0) {
      while ($friend = $get_friends->fetch_assoc()) {
        $friends[] = $friend['user_id'];
      }
    }
    return $friends;
  }


  /**
   * get_followings_ids
   * 
   * @param integer $user_id
   * @return array
   */
  public function get_followings_ids($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $followings = [];
    $get_followings = $db->query(sprintf("SELECT followings.following_id FROM followings INNER JOIN users ON followings.following_id = users.user_id WHERE users.user_banned = '0' AND followings.user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_followings->num_rows > 0) {
      while ($following = $get_followings->fetch_assoc()) {
        $followings[] = $following['following_id'];
      }
    }
    return $followings;
  }


  /**
   * get_followers_ids
   * 
   * @param integer $user_id
   * @return array
   */
  public function get_followers_ids($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $followers = [];
    $get_followers = $db->query(sprintf("SELECT followings.user_id FROM followings INNER JOIN users ON followings.user_id = users.user_id WHERE users.user_banned = '0' AND followings.following_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_followers->num_rows > 0) {
      while ($follower = $get_followers->fetch_assoc()) {
        $followers[] = $follower['user_id'];
      }
    }
    return $followers;
  }


  /**
   * get_friends_followings_ids
   * 
   * @return array
   */
  public function get_friends_followings_ids()
  {
    return array_intersect($this->_data['friends_ids'], $this->_data['followings_ids']);
  }


  /**
   * get_friend_requests_ids
   * 
   * @return array
   */
  public function get_friend_requests_ids()
  {
    global $db;
    $requests = [];
    $get_requests = $db->query(sprintf("SELECT user_one_id FROM friends WHERE status = 0 AND user_two_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_requests->num_rows > 0) {
      while ($request = $get_requests->fetch_assoc()) {
        $requests[] = $request['user_one_id'];
      }
    }
    return $requests;
  }


  /**
   * get_friend_requests_sent_ids
   * 
   * @return array
   */
  public function get_friend_requests_sent_ids()
  {
    global $db;
    $requests = [];
    $get_requests = $db->query(sprintf("SELECT user_two_id FROM friends WHERE status = 0 AND user_one_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_requests->num_rows > 0) {
      while ($request = $get_requests->fetch_assoc()) {
        $requests[] = $request['user_two_id'];
      }
    }
    return $requests;
  }


  /**
   * get_pages_ids
   * 
   * @return array
   */
  public function get_pages_ids()
  {
    global $db;
    $pages = [];
    if ($this->_logged_in) {
      $get_pages = $db->query(sprintf("SELECT page_id FROM pages_likes WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_pages->num_rows > 0) {
        while ($page = $get_pages->fetch_assoc()) {
          $pages[] = $page['page_id'];
        }
      }
    }
    return $pages;
  }


  /**
   * get_groups_ids
   * 
   * @param boolean $only_approved
   * @return array
   */
  public function get_groups_ids($only_approved = false)
  {
    global $db;
    $groups = [];
    if ($this->_logged_in) {
      $where_statement = ($only_approved) ? " approved = '1' AND" : "";
      $get_groups = $db->query(sprintf("SELECT group_id FROM groups_members WHERE " . $where_statement . " user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_groups->num_rows > 0) {
        while ($group = $get_groups->fetch_assoc()) {
          $groups[] = $group['group_id'];
        }
      }
    }
    return $groups;
  }


  /**
   * get_events_ids
   * 
   * @return array
   */
  public function get_events_ids()
  {
    global $db;
    $events = [];
    if ($this->_logged_in) {
      $get_events = $db->query(sprintf("SELECT event_id FROM events_members WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_events->num_rows > 0) {
        while ($event = $get_events->fetch_assoc()) {
          $events[] = $event['event_id'];
        }
      }
    }
    return $events;
  }


  /**
   * spread_ids
   * 
   * @return string
   */
  public function spread_ids($array)
  {
    return (!$array) ? "0" : implode(',', $array);
  }


  /**
   * filter_ids
   * 
   * @return integer
   */
  public function filter_ids($value)
  {
    if (is_numeric($value)) {
      $value = intval($value);
      if ($value != 0) {
        return $value;
      }
    }
  }



  /* ------------------------------- */
  /* Get Users */
  /* ------------------------------- */

  /**
   * get_friends
   * 
   * @param integer $user_id
   * @param integer $offset
   * @param boolean $get_all
   * @return array
   */
  public function get_friends($user_id, $offset = 0, $get_all = false)
  {
    global $db, $system;
    $friends = [];
    /* get the target user's privacy */
    $get_privacy = $db->query(sprintf("SELECT user_privacy_friends FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $privacy = $get_privacy->fetch_assoc();
    /* check the target user's privacy */
    if (!$this->check_privacy($privacy['user_privacy_friends'], $user_id)) {
      return $friends;
    }
    $offset *= $system['max_results_even'];
    $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($system['max_results_even'], 'int', false));
    $get_friends = $db->query(sprintf('SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM friends INNER JOIN users ON (friends.user_one_id = users.user_id AND friends.user_one_id != %1$s) OR (friends.user_two_id = users.user_id AND friends.user_two_id != %1$s) WHERE users.user_banned = "0" AND friends.status = 1 AND (friends.user_one_id = %1$s OR friends.user_two_id = %1$s) ' . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_friends->num_rows > 0) {
      while ($friend = $get_friends->fetch_assoc()) {
        $friend['user_picture'] = get_picture($friend['user_picture'], $friend['user_gender']);
        /* get the connection between the viewer & the target */
        $friend['connection'] = $this->connection($friend['user_id']);
        $friend['top_friend'] = $this->is_top_friend($friend['user_id']);
        $friends[] = $friend;
      }
    }
    return $friends;
  }


  /**
   * get_top_friends
   * 
   * @param integer $user_id
   * @param integer $offset
   * @param boolean $get_all
   * @return array
   */
  public function get_top_friends($user_id, $offset = 0, $get_all = false)
  {
    global $db, $system;
    $top_friends = [];
    $offset *= $system['max_results_even'];
    /* get the target user's privacy */
    $get_privacy = $db->query(sprintf("SELECT user_privacy_friends FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $privacy = $get_privacy->fetch_assoc();
    /* check the target user's privacy */
    if (!$this->check_privacy($privacy['user_privacy_friends'], $user_id)) {
      return $top_friends;
    }
    $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($system['max_results_even'], 'int', false));
    $get_top_friends = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users_top_friends INNER JOIN users ON users_top_friends.friend_id = users.user_id WHERE users.user_banned = '0' AND users_top_friends.user_id = %s " . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_top_friends->num_rows > 0) {
      while ($top_friend = $get_top_friends->fetch_assoc()) {
        $top_friend['user_picture'] = get_picture($top_friend['user_picture'], $top_friend['user_gender']);
        /* get the connection between the viewer & the target */
        $top_friend['connection'] = $this->connection($top_friend['user_id'], false);
        $top_friends[] = $top_friend;
      }
    }
    return $top_friends;
  }


  /**
   * get_friends_count
   * 
   * @param integer $user_id
   * @return integer
   */
  public function get_friends_count($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $get_friends_count = $db->query(sprintf('
          SELECT
            COUNT(*) AS count 
          FROM (
              SELECT
                  f.user_two_id AS friend_id
              FROM friends AS f
              WHERE f.status = 1 AND f.user_one_id = %1$s
              UNION
              SELECT
                  f.user_one_id AS friend_id
              FROM friends AS f
              WHERE f.status = 1 AND f.user_two_id = %1$s
            ) AS t
          INNER JOIN users AS u ON friend_id = u.user_id', secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_friends_count->fetch_assoc()['count'];
  }


  /**
   * get_followings
   * 
   * @param integer $user_id
   * @param integer $offset
   * @param boolean $get_all
   * @return array
   */
  public function get_followings($user_id, $offset = 0, $get_all = false)
  {
    global $db, $system;
    $followings = [];
    $offset *= $system['max_results_even'];
    /* get the target user's privacy */
    $get_privacy = $db->query(sprintf("SELECT user_privacy_followers FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $privacy = $get_privacy->fetch_assoc();
    /* check the target user's privacy */
    if (!$this->check_privacy($privacy['user_privacy_followers'], $user_id)) {
      return $followings;
    }
    $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($system['max_results_even'], 'int', false));
    $get_followings = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM followings INNER JOIN users ON followings.following_id = users.user_id WHERE users.user_banned = '0' AND followings.user_id = %s " . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_followings->num_rows > 0) {
      while ($following = $get_followings->fetch_assoc()) {
        $following['user_picture'] = get_picture($following['user_picture'], $following['user_gender']);
        /* get the connection between the viewer & the target */
        $following['connection'] = $this->connection($following['user_id'], false);
        $followings[] = $following;
      }
    }
    return $followings;
  }


  /**
   * get_followings_count
   * 
   * @param integer $user_id
   * @return integer
   */
  public function get_followings_count($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $get_followings_count = $db->query(sprintf("SELECT COUNT(*) AS count FROM followings INNER JOIN users ON followings.following_id = users.user_id WHERE users.user_banned = '0' AND followings.user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_followings_count->fetch_assoc()['count'];
  }


  /**
   * get_followers
   * 
   * @param integer $user_id
   * @param integer $offset
   * @param boolean $get_all
   * @return array
   */
  public function get_followers($user_id, $offset = 0, $get_all = false)
  {
    global $db, $system;
    $followers = [];
    $offset *= $system['max_results_even'];
    /* get the target user's privacy */
    $get_privacy = $db->query(sprintf("SELECT user_privacy_followers FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $privacy = $get_privacy->fetch_assoc();
    /* check the target user's privacy */
    if (!$this->check_privacy($privacy['user_privacy_followers'], $user_id)) {
      return $followers;
    }
    $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($system['max_results_even'], 'int', false));
    $get_followers = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM followings INNER JOIN users ON followings.user_id = users.user_id WHERE users.user_banned = '0' AND followings.following_id = %s " . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_followers->num_rows > 0) {
      while ($follower = $get_followers->fetch_assoc()) {
        $follower['user_picture'] = get_picture($follower['user_picture'], $follower['user_gender']);
        /* get the connection between the viewer & the target */
        $follower['connection'] = $this->connection($follower['user_id'], false);
        $followers[] = $follower;
      }
    }
    return $followers;
  }


  /**
   * get_followers_count
   * 
   * @param integer $user_id
   * @return integer
   */
  public function get_followers_count($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $get_followers_count = $db->query(sprintf("SELECT COUNT(*) AS count FROM followings INNER JOIN users ON followings.user_id = users.user_id WHERE users.user_banned = '0' AND followings.following_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_followers_count->fetch_assoc()['count'];
  }


  /**
   * get_subscribers
   * 
   * @param integer $node_id
   * @param string $node_type
   * @param integer $offset
   * @param boolean $get_all
   * @return array
   */
  public function get_subscribers($node_id, $node_type = 'profile', $offset = 0, $get_all = false)
  {
    global $db, $system;
    $subscribers = [];
    $offset *= $system['max_results_even'];
    $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($system['max_results_even'], 'int', false));
    $get_subscribers = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM subscribers INNER JOIN users ON subscribers.user_id = users.user_id WHERE users.user_banned = '0' AND subscribers.node_id = %s AND subscribers.node_type = %s " . $limit_statement, secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    if ($get_subscribers->num_rows > 0) {
      while ($subscriber = $get_subscribers->fetch_assoc()) {
        $subscriber['user_picture'] = get_picture($subscriber['user_picture'], $subscriber['user_gender']);
        /* get the connection between the viewer & the target */
        $subscriber['connection'] = $this->connection($subscriber['user_id'], false);
        $subscribers[] = $subscriber;
      }
    }
    return $subscribers;
  }


  /**
   * get_subscribers_count
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return integer
   */
  public function get_subscribers_count($node_id = null, $node_type = 'profile')
  {
    global $db;
    $node_id = (isset($node_id)) ? $node_id : $this->_data['user_id'];
    $get_subscribers_count = $db->query(sprintf("SELECT COUNT(*) AS count FROM subscribers INNER JOIN users ON subscribers.user_id = users.user_id WHERE users.user_banned = '0' AND subscribers.node_id = %s AND subscribers.node_type = %s", secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    return $get_subscribers_count->fetch_assoc()['count'];
  }


  /**
   * get_subscriptions
   * 
   * @param integer $user_id
   * @param integer $offset
   * @param boolean $get_all
   * @return array
   */
  public function get_subscriptions($user_id, $offset = 0, $get_all = false)
  {
    global $db, $system;
    $subscriptions = [];
    /* get the target user's privacy */
    $get_privacy = $db->query(sprintf("SELECT user_privacy_subscriptions FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $privacy = $get_privacy->fetch_assoc();
    /* check the target user's privacy */
    if (!$this->check_privacy($privacy['user_privacy_subscriptions'], $user_id)) {
      return $subscriptions;
    }
    $offset *= $system['max_results_even'];
    $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($system['max_results_even'], 'int', false));
    $get_subscriptions = $db->query(sprintf("SELECT subscribers.plan_id, subscribers.node_type, subscribers.user_id AS plan_user_id, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified, pages.*, `groups`.* FROM subscribers LEFT JOIN users ON subscribers.node_id = users.user_id AND subscribers.node_type = 'profile' AND users.user_banned = '0' LEFT JOIN pages ON subscribers.node_id = pages.page_id AND subscribers.node_type = 'page' LEFT JOIN `groups` ON subscribers.node_id = `groups`.group_id AND subscribers.node_type = 'group' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL AND `groups`.group_name <=> NULL) AND subscribers.user_id = %s" . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_subscriptions->num_rows > 0) {
      while ($subscription = $get_subscriptions->fetch_assoc()) {
        /* get node picture */
        switch ($subscription['node_type']) {
          case 'profile':
            $subscription['user_picture'] = get_picture($subscription['user_picture'], $subscription['user_gender']);
            break;

          case 'page':
            $subscription['page_picture'] = get_picture($subscription['page_picture'], 'page');
            break;

          case 'group':
            $subscription['group_picture'] = get_picture($subscription['group_picture'], 'group');
            break;
        }
        /* get monetization plan */
        $subscription['monetization_plan'] = $this->get_monetization_plan($subscription['plan_id'], true);
        $subscriptions[] = $subscription;
      }
    }
    return $subscriptions;
  }


  /**
   * get_subscriptions_count
   * 
   * @param integer $user_id
   * @return integer
   */
  public function get_subscriptions_count($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $get_subscriptions_count = $db->query(sprintf("SELECT COUNT(*) AS count FROM subscribers LEFT JOIN users ON subscribers.node_id = users.user_id AND subscribers.node_type = 'profile' AND users.user_banned = '0' LEFT JOIN pages ON subscribers.node_id = pages.page_id AND subscribers.node_type = 'page' LEFT JOIN `groups` ON subscribers.node_id = `groups`.group_id AND subscribers.node_type = 'group' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL AND `groups`.group_name <=> NULL) AND subscribers.user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_subscriptions_count->fetch_assoc()['count'];
  }


  /**
   * get_blocked
   * 
   * @param integer $offset
   * @return array
   */
  public function get_blocked($offset = 0)
  {
    global $db, $system;
    $blocks = [];
    $offset *= $system['max_results'];
    $get_blocks = $db->query(sprintf('SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users_blocks INNER JOIN users ON users_blocks.blocked_id = users.user_id WHERE users_blocks.user_id = %s LIMIT %s, %s', secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_blocks->num_rows > 0) {
      while ($block = $get_blocks->fetch_assoc()) {
        $block['user_picture'] = get_picture($block['user_picture'], $block['user_gender']);
        $block['connection'] = 'blocked';
        $blocks[] = $block;
      }
    }
    return $blocks;
  }


  /**
   * check_last_friend_request
   * 
   * @param integer $last_request_id
   * @return boolean
   */
  public function check_last_friend_request($last_request_id)
  {
    global $db, $system;
    /* check if last friend reuqest deleted already */
    $deleted = $db->query(sprintf("SELECT COUNT(*) as count FROM friends WHERE id = %s", secure($last_request_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($deleted->fetch_assoc()['count'] == 0) {
      /* if yes > return true */
      return true;
    }
    return false;
  }


  /**
   * get_friend_requests
   * 
   * @param integer $offset
   * @param integer $last_request_id
   * @return array
   */
  public function get_friend_requests($offset = 0, $last_request_id = null)
  {
    global $db, $system;
    $requests = [];
    $offset *= $system['max_results'];
    if ($last_request_id !== null) {
      $get_requests = $db->query(sprintf("SELECT friends.id, friends.user_one_id as user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM friends INNER JOIN users ON friends.user_one_id = users.user_id WHERE friends.status = 0 AND friends.user_two_id = %s AND friends.id > %s ORDER BY friends.id DESC", secure($this->_data['user_id'], 'int'), secure($last_request_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      $get_requests = $db->query(sprintf("SELECT friends.id, friends.user_one_id as user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM friends INNER JOIN users ON friends.user_one_id = users.user_id WHERE friends.status = 0 AND friends.user_two_id = %s ORDER BY friends.id DESC LIMIT %s, %s", secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_requests->num_rows > 0) {
      while ($request = $get_requests->fetch_assoc()) {
        $request['user_picture'] = get_picture($request['user_picture'], $request['user_gender']);
        $request['mutual_friends_count'] = $this->get_mutual_friends_count($request['user_id']);
        $requests[] = $request;
      }
    }
    return $requests;
  }


  /**
   * get_friend_requests_sent
   * 
   * @param integer $offset
   * @return array
   */
  public function get_friend_requests_sent($offset = 0)
  {
    global $db, $system;
    $requests = [];
    $offset *= $system['max_results'];
    $get_requests = $db->query(sprintf("SELECT friends.user_two_id as user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM friends INNER JOIN users ON friends.user_two_id = users.user_id WHERE friends.status = 0 AND friends.user_one_id = %s LIMIT %s, %s", secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_requests->num_rows > 0) {
      while ($request = $get_requests->fetch_assoc()) {
        $request['user_picture'] = get_picture($request['user_picture'], $request['user_gender']);
        $request['mutual_friends_count'] = $this->get_mutual_friends_count($request['user_id']);
        $requests[] = $request;
      }
    }
    return $requests;
  }


  /**
   * get_friend_requests_sent_total
   * 
   * @return integer
   */
  public function get_friend_requests_sent_total()
  {
    global $db;
    $get_total_requests = $db->query(sprintf("SELECT COUNT(*) as count FROM friends INNER JOIN users ON friends.user_two_id = users.user_id WHERE friends.status = 0 AND friends.user_one_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_total_requests->fetch_assoc()['count'];
  }


  /**
   * get_mutual_friends
   * 
   * @param integer $user_two_id
   * @param integer $offset
   * @return array
   */
  public function get_mutual_friends($user_two_id, $offset = 0)
  {
    global $db, $system;
    $mutual_friends = [];
    $offset *= $system['max_results'];
    $mutual_friends_ids = array_intersect($this->_data['friends_ids'], $this->get_friends_ids($user_two_id));
    $get_mutual_friends = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users WHERE user_id IN (%s) LIMIT %s, %s", $this->spread_ids($mutual_friends_ids), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_mutual_friends->num_rows > 0) {
      while ($mutual_friend = $get_mutual_friends->fetch_assoc()) {
        $mutual_friend['user_picture'] = get_picture($mutual_friend['user_picture'], $mutual_friend['user_gender']);
        $mutual_friends[] = $mutual_friend;
      }
    }
    return $mutual_friends;
  }


  /**
   * get_mutual_friends_count
   * 
   * @param integer $user_two_id
   * @return integer
   */
  public function get_mutual_friends_count($user_two_id)
  {
    return count(array_intersect($this->_data['friends_ids'], $this->get_friends_ids($user_two_id)));
  }


  /**
   * get_new_people ✅
   * 
   * @param integer $offset
   * @param boolean $random
   * @return array
   */
  public function get_new_people($offset = 0, $random = false)
  {
    global $db, $system;
    $results = [];
    $offset *= $system['min_results'];
    // prepare where statement
    $where = "";
    /* user not IN (friends, followings, friend requests & friend requests sent) */
    $old_people_ids = array_unique(array_merge($this->_data['friends_ids'], $this->_data['followings_ids'], $this->_data['friend_requests_ids'], $this->_data['friend_requests_sent_ids']));
    $where .= sprintf("WHERE users.user_id != '1' AND users.user_banned = '0' AND users.user_id != %s AND users.user_id NOT IN (%s)", secure($this->_data['user_id'], 'int'), $this->spread_ids($old_people_ids));
    /* check if activation enabled */
    if ($system['activation_enabled']) {
      $where .= " AND users.user_activated = '1'";
    }
    /* get users */
    if ($system['location_finder_enabled']) {
      $unit = ($system['system_distance'] == "mile") ? 3958 : 6371;
      $distance = 10000;
      if ($random) {
        $get_users = $db->query(sprintf("SELECT * FROM (SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified, (%s * acos(cos(radians(%s)) * cos(radians(user_latitude)) * cos(radians(user_longitude) - radians(%s)) + sin(radians(%s)) * sin(radians(user_latitude))) ) AS distance FROM users " . $where . " HAVING distance < %s ORDER BY distance ASC LIMIT %s) tmp ORDER BY RAND() LIMIT %s", secure($unit, 'int'), secure($this->_data['user_latitude']), secure($this->_data['user_longitude']), secure($this->_data['user_latitude']), secure($distance, 'int'), secure($system['max_results'] * 2, 'int', false), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      } else {
        $get_users = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified, (%s * acos(cos(radians(%s)) * cos(radians(user_latitude)) * cos(radians(user_longitude) - radians(%s)) + sin(radians(%s)) * sin(radians(user_latitude))) ) AS distance FROM users " . $where . " HAVING distance < %s ORDER BY distance ASC LIMIT %s, %s", secure($unit, 'int'), secure($this->_data['user_latitude']), secure($this->_data['user_longitude']), secure($this->_data['user_latitude']), secure($distance, 'int'), secure($offset, 'int', false), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      }
    } else {
      if ($random) {
        $get_users = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users JOIN (SELECT CEIL(RAND() * (SELECT MAX(user_id) FROM users)) AS user_id) AS u " . $where . " AND users.user_id >= u.user_id ORDER BY users.user_id ASC LIMIT %s, %s", secure($offset, 'int', false), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      } else {
        $get_users = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users " . $where . " LIMIT %s, %s", secure($offset, 'int', false), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      }
    }
    if ($get_users->num_rows > 0) {
      while ($user = $get_users->fetch_assoc()) {
        /* check if there is any blocking between the viewer & the target user */
        if ($this->blocked($user['user_id'])) {
          continue;
        }
        $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
        $user['mutual_friends_count'] = $this->get_mutual_friends_count($user['user_id']);
        $results[] = $user;
      }
    }
    return $results;
  }


  /**
   * get_pro_members
   * 
   * @return array
   */
  public function get_pro_members()
  {
    global $db, $system;
    $pro_members = [];
    $get_pro_members = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users WHERE user_subscribed = '1' ORDER BY RAND() LIMIT %s", $system['max_results'])) or _error('SQL_ERROR_THROWEN');
    if ($get_pro_members->num_rows > 0) {
      while ($user = $get_pro_members->fetch_assoc()) {
        $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
        $pro_members[] = $user;
      }
    }
    return $pro_members;
  }


  /**
   * get_users
   * 
   * @param string $query
   * @param array $skipped_array
   * @param boolean $mentioned
   * @return array
   */
  public function get_users($query, $skipped_array = [], $mentioned = false)
  {
    global $db, $system;
    $users = [];
    /* prepare where & order statement */
    if ($system['show_usernames_enabled']) {
      $where_statement = sprintf(" (user_name LIKE %s)", secure($query, 'search'));
      $order_statement = " ORDER BY user_name ";
    } else {
      $where_statement = sprintf(' (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s) ', secure($query, 'search'));
      $order_statement = " ORDER BY user_firstname, user_lastname ";
    }
    /* prepare skipped statment */
    $skipped_array = array_filter($skipped_array, [$this, "filter_ids"]);
    $skipped_statment = ($skipped_array) ? sprintf(" user_id NOT IN (%s) AND ", $this->spread_ids($skipped_array)) : "";
    /* get users */
    $get_users = $db->query("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified, user_monetization_enabled, user_monetization_chat_price, user_monetization_call_price FROM users WHERE user_id != " . secure($this->_data['user_id'], 'int') . " AND " . $skipped_statment . $where_statement . $order_statement . " LIMIT " . secure($system['min_results'], 'int', false)) or _error('SQL_ERROR_THROWEN');
    if ($get_users->num_rows > 0) {
      while ($user = $get_users->fetch_assoc()) {
        /* check if there is any blocking between the viewer & the target user */
        if ($this->blocked($user['user_id'])) {
          continue;
        }
        /* check if paid chat */
        if ($system['monetization_enabled'] && $this->check_user_permission($user['user_id'], 'monetization_permission') && $user['user_monetization_enabled'] && ($user['user_monetization_chat_price'] > 0 || $user['user_monetization_call_price'] > 0)) {
          $user['chat_price'] = $user['user_monetization_chat_price'];
          $user['call_price'] = $user['user_monetization_call_price'];
        }
        $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
        if ($mentioned) {
          $mention_item['id'] = $user['user_id'];
          $mention_item['img'] = $user['user_picture'];
          $mention_item['label'] = ($system['show_usernames_enabled']) ? $user['user_name'] : $user['user_firstname'] . " " . $user['user_lastname'];
          $mention_item['value'] = "[" . $user['user_name'] . "]";
          $users[] = $mention_item;
        } else {
          $users[] = $user;
        }
      }
    }
    return $users;
  }


  /**
   * get_user
   * 
   * @param integer $user_id
   * @return array
   */
  public function get_user($user_id)
  {
    global $db;
    $get_user = $db->query(sprintf("SELECT * FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_user->num_rows == 0) {
      return false;
    }
    return $get_user->fetch_assoc();
  }



  /* ------------------------------- */
  /* Search */
  /* ------------------------------- */

  /**
   * search
   * 
   * @param string $query
   * @param string $tab
   * @param integer $offset
   * @return array
   */
  public function search($query, $tab, $offset = 0)
  {
    global $db, $system;
    $results = [];
    if ($tab != "posts") {
      $offset *= $system['search_results'];
    }
    switch ($tab) {
      case 'posts':
        /* search posts */
        $results = $this->get_posts(['query' => $query, 'offset' => $offset]);
        break;

      case 'articles':
        /* search articles */
        if ($system['blogs_enabled']) {
          $get_articles = $db->query(sprintf('SELECT post_id FROM posts_articles WHERE title LIKE %1$s OR tags LIKE %1$s ORDER BY title ASC LIMIT %2$s, %3$s', secure($query, 'search'), secure($offset, 'int', false), secure($system['search_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
          if ($get_articles->num_rows > 0) {
            while ($article = $get_articles->fetch_assoc()) {
              $article = $this->get_post($article['post_id']);
              if ($article) {
                $results[] = $article;
              }
            }
          }
        }
        break;

      case 'users':
        /* search users */
        $get_users = $db->query(sprintf('SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users WHERE user_banned = "0" AND (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s) ORDER BY user_firstname ASC LIMIT %2$s, %3$s', secure($query, 'search'), secure($offset, 'int', false), secure($system['search_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_users->num_rows > 0) {
          while ($user = $get_users->fetch_assoc()) {
            $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
            /* get the connection between the viewer & the target */
            $user['connection'] = $this->connection($user['user_id']);
            $results[] = $user;
          }
        }
        break;

      case 'pages':
        /* search pages */
        if ($system['pages_enabled']) {
          $get_pages = $db->query(sprintf('SELECT * FROM pages WHERE page_name LIKE %1$s OR page_title LIKE %1$s ORDER BY page_title ASC LIMIT %2$s, %3$s', secure($query, 'search'), secure($offset, 'int', false), secure($system['search_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
          if ($get_pages->num_rows > 0) {
            while ($page = $get_pages->fetch_assoc()) {
              $page['page_picture'] = get_picture($page['page_picture'], 'page');
              /* check if the viewer liked the page */
              $page['i_like'] = $this->check_page_membership($this->_data['user_id'], $page['page_id']);
              $results[] = $page;
            }
          }
        }
        break;

      case 'groups':
        /* search groups */
        if ($system['groups_enabled']) {
          $get_groups = $db->query(sprintf('SELECT * FROM `groups` WHERE group_privacy != "secret" AND (group_name LIKE %1$s OR group_title LIKE %1$s) ORDER BY group_title ASC LIMIT %2$s, %3$s', secure($query, 'search'), secure($offset, 'int', false), secure($system['search_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
          if ($get_groups->num_rows > 0) {
            while ($group = $get_groups->fetch_assoc()) {
              $group['group_picture'] = get_picture($group['group_picture'], 'group');
              /* check if the viewer joined the group */
              $group['i_joined'] = $this->check_group_membership($this->_data['user_id'], $group['group_id']);
              $results[] = $group;
            }
          }
        }
        break;

      case 'events':
        /* search events */
        if ($system['events_enabled']) {
          $get_events = $db->query(sprintf('SELECT * FROM `events` WHERE event_privacy != "secret" AND event_title LIKE %1$s ORDER BY event_title ASC LIMIT %2$s, %3$s', secure($query, 'search'), secure($offset, 'int', false), secure($system['search_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
          if ($get_events->num_rows > 0) {
            while ($event = $get_events->fetch_assoc()) {
              $event['event_picture'] = get_picture($event['event_cover'], 'event');
              /* check if the viewer joined the event */
              $event['i_joined'] = $this->check_event_membership($this->_data['user_id'], $event['event_id']);
              $results[] = $event;
            }
          }
        }
        break;
    }
    return $results;
  }


  /**
   * search_quick
   * 
   * @param string $query
   * @return array
   */
  public function search_quick($query)
  {
    global $db, $system;
    $results = [];
    /* search users */
    $get_users = $db->query(sprintf('SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users WHERE user_banned = "0" AND (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s) LIMIT %2$s', secure($query, 'search'), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_users->num_rows > 0) {
      while ($user = $get_users->fetch_assoc()) {
        $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
        /* get the connection between the viewer & the target */
        $user['connection'] = $this->connection($user['user_id']);
        $user['sort'] = ($system['show_usernames_enabled']) ? $user['user_name'] : $user['user_firstname'];
        $user['type'] = 'user';
        $results[] = $user;
      }
    }
    /* search pages */
    if ($system['pages_enabled']) {
      $get_pages = $db->query(sprintf('SELECT * FROM pages WHERE page_name LIKE %1$s OR page_title LIKE %1$s LIMIT %2$s', secure($query, 'search'), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      if ($get_pages->num_rows > 0) {
        while ($page = $get_pages->fetch_assoc()) {
          $page['page_picture'] = get_picture($page['page_picture'], 'page');
          /* check if the viewer liked the page */
          $page['i_like'] = $this->check_page_membership($this->_data['user_id'], $page['page_id']);
          $page['sort'] = $page['page_title'];
          $page['type'] = 'page';
          $results[] = $page;
        }
      }
    }
    /* search groups */
    if ($system['groups_enabled']) {
      $get_groups = $db->query(sprintf('SELECT * FROM `groups` WHERE group_privacy != "secret" AND (group_name LIKE %1$s OR group_title LIKE %1$s) LIMIT %2$s', secure($query, 'search'), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      if ($get_groups->num_rows > 0) {
        while ($group = $get_groups->fetch_assoc()) {
          $group['group_picture'] = get_picture($group['group_picture'], 'group');
          /* check if the viewer joined the group */
          $group['i_joined'] = $this->check_group_membership($this->_data['user_id'], $group['group_id']);
          $group['sort'] = $group['group_title'];
          $group['type'] = 'group';
          $results[] = $group;
        }
      }
    }
    /* search events */
    if ($system['events_enabled']) {
      $get_events = $db->query(sprintf('SELECT * FROM `events` WHERE event_privacy != "secret" AND event_title LIKE %1$s LIMIT %2$s', secure($query, 'search'), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      if ($get_events->num_rows > 0) {
        while ($event = $get_events->fetch_assoc()) {
          $event['event_picture'] = get_picture($event['event_cover'], 'event');
          /* check if the viewer joined the event */
          $event['i_joined'] = $this->check_event_membership($this->_data['user_id'], $event['event_id']);
          $event['sort'] = $event['event_title'];
          $event['type'] = 'event';
          $results[] = $event;
        }
      }
    }
    /* sort results */
    function sort_results($a, $b)
    {
      return strcmp($a["sort"], $b["sort"]);
    }
    usort($results, 'sort_results');
    return $results;
  }


  /**
   * search_users
   * 
   * @param string $distance
   * @param string $keyword
   * @param string $gender
   * @param string $relationship
   * @param string $status
   * @return array
   */
  public function search_users($distance, $keyword, $gender, $relationship, $status)
  {
    global $db, $system;
    $results = [];
    $offset *= $system['max_results'];
    /* validate gender */
    if ($gender != "any" && !$this->check_gender($gender)) {
      return $results;
    }
    /* validate relationship */
    if (isset($relationship) && !in_array($relationship, ['any', 'single', 'relationship', 'married', "complicated", 'separated', 'divorced', 'widowed'])) {
      return $results;
    }
    /* validate status */
    if (!in_array($status, ['any', 'online', 'offline'])) {
      return $results;
    }
    // prepare where statement
    $where = "";
    /* user not IN (friends, followings, friend requests & friend requests sent) */
    $old_people_ids = array_unique(array_merge($this->_data['friends_ids'], $this->_data['followings_ids'], $this->_data['friend_requests_ids'], $this->_data['friend_requests_sent_ids']));
    $where .= sprintf("WHERE user_banned = '0' AND user_id != %s AND user_id NOT IN (%s)", secure($this->_data['user_id'], 'int'), $this->spread_ids($old_people_ids));
    if ($system['activation_enabled']) {
      $where .= " AND user_activated = '1'";
    }
    /* keyword */
    if ($keyword) {
      $where .= sprintf(' AND (user_name LIKE %1$s OR user_firstname LIKE %1$s OR user_lastname LIKE %1$s OR CONCAT(user_firstname,  " ", user_lastname) LIKE %1$s)', secure($keyword, 'search'));
    }
    /* gender */
    if ($gender != "any") {
      $where .= " AND users.user_gender = '$gender'";
    }
    /* relationship */
    if (isset($relationship) && $relationship != "any") {
      $where .= " AND users.user_relationship = '$relationship'";
    }
    /* status */
    if ($status != "any") {
      if ($status == "online") {
        $where .= sprintf(" AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($system['offline_time'], 'int', false));
      } else {
        $where .= sprintf(" AND user_last_seen < SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($system['offline_time'], 'int', false));
      }
    }
    /* custom fields */
    $join_query = "";
    $distinct_query = "";
    $custom_fields_query = $this->search_custom_fields($_POST);
    if ($custom_fields_query) {
      $where .= " AND (" . $custom_fields_query . ")";
      $distinct_query = "DISTINCT";
      $join_query = " LEFT JOIN custom_fields_values AS cfv ON cfv.node_type = 'user' AND cfv.node_id = users.user_id ";
    }
    /* build query */
    if ($system['location_finder_enabled']) {
      /* validate distance */
      $unit = ($system['system_distance'] == "mile") ? 3958 : 6371;
      $distance = ($distance && is_numeric($distance) && $distance > 0) ? $distance : 25;
      /* prepare query */
      $query = sprintf("SELECT " . $distinct_query . " user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified, (%s * acos(cos(radians(%s)) * cos(radians(user_latitude)) * cos(radians(user_longitude) - radians(%s)) + sin(radians(%s)) * sin(radians(user_latitude))) ) AS distance FROM users ", secure($unit, 'int'), secure($this->_data['user_latitude']), secure($this->_data['user_longitude']), secure($this->_data['user_latitude']));
      $query .= $join_query;
      $query .= $where;
      $query .= sprintf(" HAVING distance < %s ORDER BY distance ASC LIMIT %s", secure($distance, 'int'), secure($system['max_results'], 'int', false));
    } else {
      /* prepare query */
      $query = "SELECT " . $distinct_query . " user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users ";
      $query .= $join_query;
      $query .= $where;
      $query .= sprintf(" LIMIT %s", secure($system['max_results'], 'int', false));
    }
    /* get users */
    $get_users = $db->query($query) or _error('SQL_ERROR_THROWEN');
    if ($get_users->num_rows > 0) {
      while ($user = $get_users->fetch_assoc()) {
        $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
        /* get the connection between the viewer & the target */
        $user['connection'] = $this->connection($user['user_id']);
        $results[] = $user;
      }
    }
    return $results;
  }


  /**
   * get_search_log
   * 
   * @return array
   */
  public function get_search_log()
  {
    global $db, $system;
    $results = [];
    $get_search_log = $db->query(sprintf("SELECT users_searches.log_id, users_searches.node_type, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified, pages.*, `groups`.*, `events`.* FROM users_searches LEFT JOIN users ON users_searches.node_type = 'user' AND users_searches.node_id = users.user_id LEFT JOIN pages ON users_searches.node_type = 'page' AND users_searches.node_id = pages.page_id LEFT JOIN `groups` ON users_searches.node_type = 'group' AND users_searches.node_id = `groups`.group_id LEFT JOIN `events` ON users_searches.node_type = 'event' AND users_searches.node_id = `events`.event_id WHERE users_searches.user_id = %s ORDER BY users_searches.log_id DESC LIMIT %s", secure($this->_data['user_id'], 'int'), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_search_log->num_rows > 0) {
      while ($result = $get_search_log->fetch_assoc()) {
        switch ($result['node_type']) {
          case 'user':
            $result['user_picture'] = get_picture($result['user_picture'], $result['user_gender']);
            /* get the connection between the viewer & the target */
            $result['connection'] = $this->connection($result['user_id']);
            break;

          case 'page':
            $result['page_picture'] = get_picture($result['page_picture'], 'page');
            /* check if the viewer liked the page */
            $result['i_like'] = $this->check_page_membership($this->_data['user_id'], $result['page_id']);
            break;

          case 'group':
            $result['group_picture'] = get_picture($result['group_picture'], 'group');
            /* check if the viewer joined the group */
            $result['i_joined'] = $this->check_group_membership($this->_data['user_id'], $result['group_id']);
            break;

          case 'event':
            $result['event_picture'] = get_picture($result['event_cover'], 'event');
            /* check if the viewer joined the event */
            $result['i_joined'] = $this->check_event_membership($this->_data['user_id'], $result['event_id']);
            break;
        }
        $result['type'] = $result['node_type'];
        $results[] = $result;
      }
    }
    return $results;
  }


  /**
   * set_search_log
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return void
   */
  public function set_search_log($node_id, $node_type)
  {
    global $db, $date;
    /* check if the search log exists */
    $check_search_log = $db->query(sprintf("SELECT log_id FROM users_searches WHERE user_id = %s AND node_id = %s AND node_type = %s", secure($this->_data['user_id'], 'int'), secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    if ($check_search_log->num_rows == 0) {
      /* insert the search log */
      $db->query(sprintf("INSERT INTO users_searches (user_id, node_id, node_type, time) VALUES (%s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($node_id, 'int'), secure($node_type), secure($date)));
    }
  }


  /**
   * clear_search_log
   * 
   * @return void
   */
  public function clear_search_log()
  {
    global $db, $system;
    $db->query(sprintf("DELETE FROM users_searches WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* User & Connections */
  /* ------------------------------- */

  /**
   * connect
   * 
   * @param string $do
   * @param integer $id
   * @param integer $uid
   * @return void
   */
  public function connect($do, $id, $uid = null)
  {
    global $db, $system;
    switch ($do) {
      case 'delete-app':
        /* delete user app */
        $this->delete_user_app($id);
        break;

      case 'block':
        /* check blocking */
        if ($this->blocked($id)) {
          throw new Exception(__("You have already blocked this user before!"));
        }
        /* remove any friendship */
        $this->connect('friend-remove', $id);
        /* delete the target from viewer's followings */
        $this->connect('unfollow', $id);
        /* delete the viewer from target's followings */
        $db->query(sprintf("DELETE FROM followings WHERE user_id = %s AND following_id = %s", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* block the user */
        $db->query(sprintf("INSERT INTO users_blocks (user_id, blocked_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'unblock':
        /* unblock the user */
        $db->query(sprintf("DELETE FROM users_blocks WHERE user_id = %s AND blocked_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'favorite':
        /* check if the user is already in the viewer's favorites */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM users_top_friends WHERE user_id = %s AND friend_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($check->fetch_assoc()['count'] > 0) {
          throw new Exception(__("This user is already in your top friends"));
        }
        /* check if the user is not friend */
        if (!$this->friendship_approved($id)) {
          throw new Exception(__("You must be friends to add this user to your top friends"));
        }
        /* add the user to the viewer's favorites */
        $db->query(sprintf("INSERT INTO users_top_friends (user_id, friend_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'unfavorite':
        /* remove the user from the viewer's favorites */
        $db->query(sprintf("DELETE FROM users_top_friends WHERE user_id = %s AND friend_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'friend-accept':
        /* check if there is a friend request from the target to the viewer */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM friends WHERE user_one_id = %s AND user_two_id = %s AND status = 0", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->fetch_assoc()['count'] == 0) return;
        /* add the target as a friend */
        $db->query(sprintf("UPDATE friends SET status = 1 WHERE user_one_id = %s AND user_two_id = %s", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* post new notification */
        $this->post_notification(['to_user_id' => $id, 'action' => 'friend_accept', 'node_url' => $this->_data['user_name']]);
        /* follow */
        $this->_follow($id);
        break;

      case 'friend-decline':
        /* check if there is a friend request from the target to the viewer */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM friends WHERE user_one_id = %s AND user_two_id = %s AND status = 0", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->fetch_assoc()['count'] == 0) return;
        /* decline this friend request */
        if ($system['disable_declined_friendrequest']) {
          $db->query(sprintf("UPDATE friends SET status = -1 WHERE user_one_id = %s AND user_two_id = %s", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          $db->query(sprintf("DELETE FROM friends WHERE user_one_id = %s AND user_two_id = %s", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        /* unfollow */
        $this->_unfollow($id);
        break;

      case 'friend-add':
        /* check blocking */
        if ($this->blocked($id)) {
          _error(403);
        }
        /* check if there is any relation between the viewer & the target */
        $check_relation = $db->query(sprintf('SELECT COUNT(*) as count FROM friends WHERE (user_one_id = %1$s AND user_two_id = %2$s) OR (user_one_id = %2$s AND user_two_id = %1$s)', secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check_relation->fetch_assoc()['count'] > 0) return;
        /* check max friends/user limit for both the viewer & the target */
        if ($system['max_friends'] > 0) {
          /* get target user group */
          $get_target_user = $db->query(sprintf("SELECT user_group FROM users WHERE user_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_target_user->num_rows == 0) return;
          $target_user = $get_target_user->fetch_assoc();
          /* viewer check */
          $check_viewer_limit = $db->query(sprintf('SELECT COUNT(*) as count FROM friends WHERE (user_one_id = %1$s OR user_two_id = %1$s) AND status = 1', secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($check_viewer_limit->fetch_assoc()['count'] >= $system['max_friends'] && $this->_data['user_group'] >= 3) {
            modal("MESSAGE", __("Maximum Limit Reached"), __("Your have reached the maximum limit of Friends" . " (" . $system['max_friends'] . " " . __("Friends") . ")"));
          }
          /* target check */
          $check_target_limit = $db->query(sprintf('SELECT COUNT(*) as count FROM friends WHERE (user_one_id = %1$s OR user_two_id = %1$s) AND status = 1', secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($check_target_limit->fetch_assoc()['count'] >= $system['max_friends'] && $target_user['user_group'] >= 3) {
            modal("MESSAGE", __("Maximum Limit Reached"), __("This user has reached the maximum limit of Friends" . " (" . $system['max_friends'] . " " . __("Friends") . ")"));
          }
        }
        /* add the friend request */
        $db->query(sprintf("INSERT INTO friends (user_one_id, user_two_id, status) VALUES (%s, %s, '0')", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update requests counter +1 */
        $db->query(sprintf("UPDATE users SET user_live_requests_counter = user_live_requests_counter + 1 WHERE user_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* post new notification */
        $this->post_notification(['to_user_id' => $id, 'action' => 'friend_add', 'node_url' => $this->_data['user_name']]);
        /* follow */
        $this->_follow($id);
        break;

      case 'friend-cancel':
        /* check if there is a request from the viewer to the target */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM friends WHERE user_one_id = %s AND user_two_id = %s AND status = 0", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->fetch_assoc()['count'] == 0) return;
        /* delete the friend request */
        $db->query(sprintf("DELETE FROM friends WHERE user_one_id = %s AND user_two_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update requests counter -1 */
        $db->query(sprintf("UPDATE users SET user_live_requests_counter = IF(user_live_requests_counter=0,0,user_live_requests_counter-1), user_live_notifications_counter = IF(user_live_notifications_counter=0,0,user_live_notifications_counter-1) WHERE user_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* delete notification */
        $this->delete_notification($id, 'friend_add', "", $this->_data['user_name']);
        /* unfollow */
        $this->_unfollow($id);
        break;

      case 'friend-remove':
        /* check if there is any relation between me & him */
        $check = $db->query(sprintf('SELECT COUNT(*) as count FROM friends WHERE (user_one_id = %1$s AND user_two_id = %2$s AND status = 1) OR (user_one_id = %2$s AND user_two_id = %1$s AND status = 1)', secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->fetch_assoc()['count'] == 0) return;
        /* delete this friend */
        $db->query(sprintf('DELETE FROM friends WHERE (user_one_id = %1$s AND user_two_id = %2$s AND status = 1) OR (user_one_id = %2$s AND user_two_id = %1$s AND status = 1)', secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* remove from top friends */
        $db->query(sprintf('DELETE FROM users_top_friends WHERE (user_id =  %1$s AND friend_id = %2$s) OR (user_id =  %2$s AND friend_id = %1$s)', secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'follow':
        $this->_follow($id);
        break;

      case 'unfollow':
        $this->_unfollow($id);
        break;

      case 'poke':
        /* check if the viewer allowed to poke the target */
        $get_target_user = $db->query(sprintf("SELECT user_privacy_poke FROM users WHERE user_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_target_user->num_rows == 0) return;
        $target_user = $get_target_user->fetch_assoc();
        if ($target_user['user_privacy_poke'] == "me" || ($target_user['user_privacy_poke'] == "friends" && !$this->friendship_approved($id))) {
          throw new Exception(__("You can't poke this user"));
        }
        /* check if the viewer poked the target before */
        if ($this->is_poked($id)) {
          throw new Exception(__("You have already poked this user before!"));
        }
        /* poke the target user */
        $db->query(sprintf("INSERT INTO users_pokes (user_id, poked_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* post new notification */
        $this->post_notification(['to_user_id' => $id, 'action' => 'poke', 'node_url' => $this->_data['user_name']]);
        break;

      case 'page-like':
        /* check if the viewer already liked this page */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check->fetch_assoc()['count'] > 0) return;
        /* if no -> like this page */
        $db->query(sprintf("INSERT INTO pages_likes (user_id, page_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update likes counter +1 */
        $db->query(sprintf("UPDATE pages SET page_likes = page_likes + 1  WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page-unlike':
        /* check if the viewer already liked this page */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->fetch_assoc()['count'] == 0) return;
        /* if yes -> unlike this page */
        $db->query(sprintf("DELETE FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update likes counter -1 */
        $db->query(sprintf("UPDATE pages SET page_likes = IF(page_likes=0,0,page_likes-1) WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page-boost':
        if ($this->_is_admin) {
          /* check if the user is the system admin */
          $check = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* check if the user is the page admin */
          $check = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s AND page_admin = %s", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        if ($check->num_rows == 0) {
          _error(403);
        }
        $spage = $check->fetch_assoc();
        /* check if viewer can boost page */
        if (!$this->_data['can_boost_pages']) {
          modal("MESSAGE", __("Sorry"), __("You reached the maximum number of boosted pages! Upgrade your package to get more"));
        }
        /* boost page */
        if (!$spage['page_boosted']) {
          /* boost page */
          $db->query(sprintf("UPDATE pages SET page_boosted = '1', page_boosted_by = %s WHERE page_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          /* update user */
          $db->query(sprintf("UPDATE users SET user_boosted_pages = user_boosted_pages + 1 WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        break;

      case 'page-unboost':
        if ($this->_is_admin) {
          /* check if the user is the system admin */
          $check = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* check if the user is the page admin */
          $check = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s AND page_admin = %s", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        if ($check->num_rows == 0) {
          _error(403);
        }
        $spage = $check->fetch_assoc();
        /* unboost page */
        if ($spage['page_boosted']) {
          /* unboost page */
          $db->query(sprintf("UPDATE pages SET page_boosted = '0', page_boosted_by = NULL WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          /* update user */
          $db->query(sprintf("UPDATE users SET user_boosted_pages = IF(user_boosted_pages=0,0,user_boosted_pages-1) WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        break;

      case 'page-invite':
        if ($uid == null) {
          _error(400);
        }
        /* check if the viewer liked the page */
        $check_viewer = $db->query(sprintf("SELECT pages.* FROM pages INNER JOIN pages_likes ON pages.page_id = pages_likes.page_id WHERE pages_likes.user_id = %s AND pages_likes.page_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check_viewer->num_rows == 0) return;
        /* check if the target already liked this page */
        $check_target = $db->query(sprintf("SELECT * FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check_target->num_rows > 0) return;
        /* check if the viewer already invited to the viewer to this page */
        $check_target = $db->query(sprintf("SELECT * FROM pages_invites WHERE page_id = %s AND user_id = %s AND from_user_id = %s", secure($id, 'int'), secure($uid, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check_target->num_rows > 0) return;
        /* if no -> invite to this page */
        /* get page */
        $page = $check_viewer->fetch_assoc();
        /* insert invitation */
        $db->query(sprintf("INSERT INTO pages_invites (page_id, user_id, from_user_id) VALUES (%s, %s, %s)", secure($id, 'int'), secure($uid, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* send notification (page invitation) to the target user */
        $this->post_notification(['to_user_id' => $uid, 'action' => 'page_invitation', 'node_type' => $page['page_title'], 'node_url' => $page['page_name']]);
        break;

      case 'page-admin-addation':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target already a page member */
        $check = $db->query(sprintf("SELECT * FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        /* check if the target already a page admin */
        $check = $db->query(sprintf("SELECT * FROM pages_admins WHERE user_id = %s AND page_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check->num_rows > 0) return;
        /* get page */
        $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $page = $get_page->fetch_assoc();
        /* check if the viewer is page admin */
        if (!$this->check_page_adminship($this->_data['user_id'], $page['page_id']) && $this->_data['user_id'] != $page['page_admin']) {
          _error(400);
        }
        /* add the target as page admin */
        $db->query(sprintf("INSERT INTO pages_admins (user_id, page_id) VALUES (%s, %s)", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* send notification (page admin addation) to the target user */
        $this->post_notification(['to_user_id' => $uid, 'action' => 'page_admin_addation', 'node_type' => $page['page_title'], 'node_url' => $page['page_name']]);
        break;

      case 'page-admin-remove':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target already a page admin */
        $check = $db->query(sprintf("SELECT * FROM pages_admins WHERE user_id = %s AND page_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        /* get page */
        $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $page = $get_page->fetch_assoc();
        /* check if the viewer is page admin */
        if (!$this->check_page_adminship($this->_data['user_id'], $page['page_id'])) {
          _error(400);
        }
        /* check if the target is the super page admin */
        if ($uid == $page['page_admin']) {
          throw new Exception(__("You can not remove page super admin"));
        }
        /* remove the target as page admin */
        $db->query(sprintf("DELETE FROM pages_admins WHERE user_id = %s AND page_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* delete notification (page admin addation) to the target user */
        $this->delete_notification($uid, 'page_admin_addation', $page['page_title'], $page['page_name']);
        break;

      case 'page-member-remove':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target already a page member */
        $check = $db->query(sprintf("SELECT * FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        /* get page */
        $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $page = $get_page->fetch_assoc();
        /* check if the target is the super page admin */
        if ($uid == $page['page_admin']) {
          throw new Exception(__("You can not remove page super admin"));
        }
        /* remove the target as page admin */
        $db->query(sprintf("DELETE FROM pages_admins WHERE user_id = %s AND page_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* remove the target as page member */
        $db->query(sprintf("DELETE FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update members counter -1 */
        $db->query(sprintf("UPDATE pages SET page_likes = IF(page_likes=0,0,page_likes-1) WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group-join':
        /* check if the viewer already joined (approved||pending) this group */
        $check = $db->query(sprintf("SELECT * FROM groups_members WHERE user_id = %s AND group_id = %s", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check->num_rows > 0) return;
        /* if no -> join this group */
        /* get group */
        $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $group = $get_group->fetch_assoc();
        /* check approved */
        $approved = 0;
        if ($group['group_privacy'] == 'public') {
          /* the group is public */
          $approved = '1';
        } elseif ($this->_data['user_id'] == $group['group_admin']) {
          /* the group admin join his group */
          $approved = '1';
        }
        $db->query(sprintf("INSERT INTO groups_members (user_id, group_id, approved) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'),  secure($id, 'int'), secure($approved))) or _error('SQL_ERROR_THROWEN');
        if ($approved) {
          /* update members counter +1 */
          $db->query(sprintf("UPDATE `groups` SET group_members = group_members + 1  WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* send notification (pending request) to group admin  */
          $this->post_notification(['to_user_id' => $group['group_admin'], 'action' => 'group_join', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
        }
        break;

      case 'group-leave':
        /* check if the viewer already joined (approved||pending) this group */
        $check = $db->query(sprintf("SELECT groups_members.approved, `groups`.* FROM groups_members INNER JOIN `groups` ON groups_members.group_id = `groups`.group_id WHERE groups_members.user_id = %s AND groups_members.group_id = %s", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        /* if yes -> leave this group */
        $group = $check->fetch_assoc();
        $db->query(sprintf("DELETE FROM groups_members WHERE user_id = %s AND group_id = %s", secure($this->_data['user_id'], 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update members counter -1 */
        if ($group['approved']) {
          $db->query(sprintf("UPDATE `groups` SET group_members = IF(group_members=0,0,group_members-1) WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* delete notification (pending request) that sent to group admin */
          $this->delete_notification($group['group_admin'], 'group_join', $group['group_title'], $group['group_name']);
        }
        break;

      case 'group-invite':
        if ($uid == null) {
          _error(400);
        }
        /* check if the viewer is group member (approved) */
        $check_viewer = $db->query(sprintf("SELECT `groups`.* FROM `groups` INNER JOIN groups_members ON `groups`.group_id = groups_members.group_id WHERE groups_members.approved = '1' AND groups_members.user_id = %s AND groups_members.group_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check_viewer->num_rows == 0) return;
        /* check if the target already joined (approved||pending) this group */
        $check_target = $db->query(sprintf("SELECT * FROM groups_members WHERE user_id = %s AND group_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check_target->num_rows > 0) return;
        /* if no -> join this group */
        /* get group */
        $group = $check_viewer->fetch_assoc();
        /* check approved */
        $approved = 0;
        if ($group['group_privacy'] == 'public') {
          /* the group is public */
          $approved = '1';
        } elseif ($this->_data['user_id'] == $group['group_admin']) {
          /* the group admin want to add a user */
          $approved = '1';
        } elseif ($uid == $group['group_admin']) {
          /* the viewer invite group admin to join his group */
          $approved = '1';
        }
        /* add the target user as group member */
        $db->query(sprintf("INSERT INTO groups_members (user_id, group_id, approved) VALUES (%s, %s, %s)", secure($uid, 'int'),  secure($id, 'int'), secure($approved))) or _error('SQL_ERROR_THROWEN');
        if ($approved) {
          /* update members counter +1 */
          $db->query(sprintf("UPDATE `groups` SET group_members = group_members + 1  WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          /* send notification (group addition) to the target user */
          $this->post_notification(['to_user_id' => $uid, 'action' => 'group_add', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
        } else {
          /* send notification (group addition) to the target user */
          if ($group['group_privacy'] != 'secret') {
            $this->post_notification(['to_user_id' => $uid, 'action' => 'group_add', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
          }
          /* send notification (pending request) to group admin [from the target]  */
          $this->post_notification(['to_user_id' => $group['group_admin'], 'from_user_id' => $uid, 'action' => 'group_join', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
        }
        break;

      case 'group-accept':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target has pending request */
        $check = $db->query(sprintf("SELECT `groups`.* FROM `groups` INNER JOIN groups_members ON `groups`.group_id = groups_members.group_id WHERE groups_members.approved = '0' AND groups_members.user_id = %s AND groups_members.group_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        $group = $check->fetch_assoc();
        /* check if the viewer is group admin */
        if (!$this->check_group_adminship($this->_data['user_id'], $group['group_id'])) {
          _error(400);
        }
        /* update request */
        $db->query(sprintf("UPDATE groups_members SET approved = '1' WHERE user_id = %s AND group_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update members counter +1 */
        $db->query(sprintf("UPDATE `groups` SET group_members = group_members + 1  WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* send notification (group acceptance) to the target user */
        $this->post_notification(['to_user_id' => $uid, 'action' => 'group_accept', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
        break;

      case 'group-decline':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target has pending request */
        $check = $db->query(sprintf("SELECT `groups`.* FROM `groups` INNER JOIN groups_members ON `groups`.group_id = groups_members.group_id WHERE groups_members.approved = '0' AND groups_members.user_id = %s AND groups_members.group_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        $group = $check->fetch_assoc();
        /* check if the viewer is group admin */
        if (!$this->check_group_adminship($this->_data['user_id'], $group['group_id'])) {
          _error(400);
        }
        /* delete request */
        $db->query(sprintf("DELETE FROM groups_members WHERE user_id = %s AND group_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group-admin-addation':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target already a group member */
        $check = $db->query(sprintf("SELECT * FROM groups_members WHERE user_id = %s AND group_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        /* check if the target already a group admin */
        $check = $db->query(sprintf("SELECT * FROM groups_admins WHERE user_id = %s AND group_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check->num_rows > 0) return;
        /* get group */
        $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $group = $get_group->fetch_assoc();
        /* check if the viewer is group admin */
        if (!$this->check_group_adminship($this->_data['user_id'], $group['group_id']) && $this->_data['user_id'] != $group['group_admin']) {
          _error(400);
        }
        /* add the target as group admin */
        $db->query(sprintf("INSERT INTO groups_admins (user_id, group_id) VALUES (%s, %s)", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* send notification (group admin addation) to the target user */
        $this->post_notification(['to_user_id' => $uid, 'action' => 'group_admin_addation', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
        break;

      case 'group-admin-remove':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target already a group admin */
        $check = $db->query(sprintf("SELECT * FROM groups_admins WHERE user_id = %s AND group_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        /* get group */
        $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $group = $get_group->fetch_assoc();
        /* check if the viewer is group admin */
        if (!$this->check_group_adminship($this->_data['user_id'], $group['group_id'])) {
          _error(400);
        }
        /* check if the target is the super group admin */
        if ($uid == $group['group_admin']) {
          throw new Exception(__("You can not remove group super admin"));
        }
        /* remove the target as group admin */
        $db->query(sprintf("DELETE FROM groups_admins WHERE user_id = %s AND group_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* delete notification (group admin addation) to the target user */
        $this->delete_notification($uid, 'group_admin_addation', $group['group_title'], $group['group_name']);
        break;

      case 'group-member-remove':
        if ($uid == null) {
          _error(400);
        }
        /* check if the target already a group member */
        $check = $db->query(sprintf("SELECT * FROM groups_members WHERE user_id = %s AND group_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        /* get group */
        $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $group = $get_group->fetch_assoc();
        /* check if the target is the super group admin */
        if ($uid == $group['group_admin']) {
          throw new Exception(__("You can not remove group super admin"));
        }
        /* remove the target as group admin */
        $db->query(sprintf("DELETE FROM groups_admins WHERE user_id = %s AND group_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* remove the target as group member */
        $db->query(sprintf("DELETE FROM groups_members WHERE user_id = %s AND group_id = %s", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update members counter -1 */
        $db->query(sprintf("UPDATE `groups` SET group_members = IF(group_members=0,0,group_members-1) WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event-go':
        /* check if the viewer member to this event */
        $check = $db->query(sprintf("SELECT * FROM events_members WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $invited = false;
        $interested = false;
        if ($check->num_rows > 0) {
          $member = $check->fetch_assoc();
          /* if going -> return */
          if ($member['is_going'] == '1') return;
          $invited = ($member['is_invited'] == '1') ? true : false;
          $interested = ($member['is_interested'] == '1') ? true : false;
        }
        $approved = false;
        if ($invited || $interested) {
          $approved = true;
        } else {
          /* get event */
          $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          $event = $get_event->fetch_assoc();
          if ($event['event_privacy'] == 'public') {
            /* the event is public */
            $approved = true;
          } elseif ($this->check_event_adminship($this->_data['user_id'], $event['event_id'])) {
            /* the event admin going his event */
            $approved = true;
          }
        }
        if ($approved) {
          if ($invited || $interested) {
            $db->query(sprintf("UPDATE events_members SET is_going = '1' WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          } else {
            $db->query(sprintf("INSERT INTO events_members (user_id, event_id, is_going) VALUES (%s, %s, '1')", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* update going counter +1 */
          $db->query(sprintf("UPDATE `events` SET event_going = event_going + 1  WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        break;

      case 'event-ungo':
        /* check if the viewer member to this event */
        $check = $db->query(sprintf("SELECT * FROM events_members WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        $invited = false;
        $interested = false;
        $member = $check->fetch_assoc();
        /* if not going -> return */
        if ($member['is_going'] == '0') return;
        $invited = ($member['is_invited'] == '1') ? true : false;
        $interested = ($member['is_interested'] == '1') ? true : false;
        if (!$invited && !$interested) {
          $db->query(sprintf("DELETE FROM events_members WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          $db->query(sprintf("UPDATE events_members SET is_going = '0' WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        /* update going counter -1 */
        $db->query(sprintf("UPDATE `events` SET event_going = IF(event_going=0,0,event_going-1)  WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event-interest':
        /* check if the viewer member to this event */
        $check = $db->query(sprintf("SELECT * FROM events_members WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $invited = false;
        $going = false;
        if ($check->num_rows > 0) {
          $member = $check->fetch_assoc();
          /* if interested -> return */
          if ($member['is_interested'] == '1') return;
          $invited = ($member['is_invited'] == '1') ? true : false;
          $going = ($member['is_going'] == '1') ? true : false;
        }
        $approved = false;
        if ($invited || $going) {
          $approved = true;
        } else {
          /* get event */
          $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          $event = $get_event->fetch_assoc();
          if ($event['event_privacy'] == 'public') {
            /* the event is public */
            $approved = true;
          } elseif ($this->check_event_adminship($this->_data['user_id'], $event['event_id'])) {
            /* the event admin interested his event */
            $approved = true;
          }
        }
        if ($approved) {
          if ($invited || $going) {
            $db->query(sprintf("UPDATE events_members SET is_interested = '1' WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          } else {
            $db->query(sprintf("INSERT INTO events_members (user_id, event_id, is_interested) VALUES (%s, %s, '1')", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          /* update interested counter +1 */
          $db->query(sprintf("UPDATE `events` SET event_interested = event_interested + 1  WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        break;

      case 'event-uninterest':
        /* check if the viewer member to this event */
        $check = $db->query(sprintf("SELECT * FROM events_members WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check->num_rows == 0) return;
        $invited = false;
        $going = false;
        $member = $check->fetch_assoc();
        /* if not interested -> return */
        if ($member['is_interested'] == '0') return;
        $invited = ($member['is_invited'] == '1') ? true : false;
        $going = ($member['is_going'] == '1') ? true : false;
        if (!$invited && !$going) {
          $db->query(sprintf("DELETE FROM events_members WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          $db->query(sprintf("UPDATE events_members SET is_interested = '0' WHERE user_id = %s AND event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        /* update interested counter -1 */
        $db->query(sprintf("UPDATE `events` SET event_interested = IF(event_interested=0,0,event_interested-1)  WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event-invite':
        if ($uid == null) {
          _error(400);
        }
        /* check if the viewer is event member */
        $check_viewer = $db->query(sprintf("SELECT `events`.* FROM `events` INNER JOIN events_members ON `events`.event_id = events_members.event_id WHERE events_members.user_id = %s AND events_members.event_id = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if no -> return */
        if ($check_viewer->num_rows == 0) return;
        /* check if the target already event member */
        $check_target = $db->query(sprintf("SELECT * FROM events_members WHERE user_id = %s AND event_id = %s", secure($uid, 'int'),  secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* if yes -> return */
        if ($check_target->num_rows > 0) return;
        /* get event */
        $event = $check_viewer->fetch_assoc();
        /* insert invitation */
        $db->query(sprintf("INSERT INTO events_members (user_id, event_id, is_invited) VALUES (%s, %s, '1')", secure($uid, 'int'), secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update invited counter +1 */
        $db->query(sprintf("UPDATE `events` SET event_invited = event_invited + 1  WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* send notification (page invitation) to the target user */
        $this->post_notification(['to_user_id' => $uid, 'action' => 'event_invitation', 'node_type' => $event['event_title'], 'node_url' => $event['event_id']]);
        break;
    }
  }


  /**
   * connection
   * 
   * @param integer $user_id
   * @param boolean $friendship
   * @return string
   */
  public function connection($user_id, $friendship = true)
  {
    /* check which type of connection (friendship|follow) connections to get */
    if ($friendship) {
      /* check if there is a logged user */
      if (!$this->_logged_in) {
        /* no logged user */
        return "add";
      }
      /* check if the viewer is the target */
      if ($user_id == $this->_data['user_id']) {
        return "me";
      }
      /* check if the viewer & the target are friends */
      if ($this->friendship_approved($user_id)) {
        return "remove";
      }
      /* check if the target sent a request to the viewer */
      if ($this->is_friend_request($user_id)) {
        return "request";
      }
      /* check if the viewer sent a request to the target */
      if ($this->is_friend_request_sent($user_id)) {
        return "cancel";
      }
      /* check if the viewer declined the friend request to the target */
      if ($this->friendship_declined($user_id)) {
        return "declined";
      }
      /* there is no relation between the viewer & the target */
      return "add";
    } else {
      /* check if there is a logged user */
      if (!$this->_logged_in) {
        /* no logged user */
        return "follow";
      }
      /* check if the viewer is the target */
      if ($user_id == $this->_data['user_id']) {
        return "me";
      }
      if ($this->is_following($user_id)) {
        /* the viewer follow the target */
        return "unfollow";
      } else {
        /* the viewer not follow the target */
        return "follow";
      }
    }
  }


  /**
   * banned
   *
   * @param integer $user_id
   * @return boolean
   */
  public function banned($user_id)
  {
    global $db;
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_banned = '1' AND user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }


  /**
   * blocked
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function blocked($user_id)
  {
    global $db;
    /* bypass the block if the viewer is admin or moderator  */
    if ($this->_data['user_group'] < 3) {
      return false;
    }
    /* check if there is any blocking between the viewer & the target */
    if ($this->_logged_in) {
      $check = $db->query(sprintf('SELECT COUNT(*) as count FROM users_blocks WHERE (user_id = %1$s AND blocked_id = %2$s) OR (user_id = %2$s AND blocked_id = %1$s)', secure($this->_data['user_id'], 'int'),  secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check->fetch_assoc()['count'] > 0) {
        return true;
      }
    }
    return false;
  }


  /**
   * friendship_approved
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function friendship_approved($user_id)
  {
    /* check if there is any approved friendship request between the viewer & the target */
    if ($this->_logged_in) {
      return (in_array($user_id, $this->_data['friends_ids'])) ? true : false;
    }
    return false;
  }


  /**
   * friendship_declined
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function friendship_declined($user_id)
  {
    global $db;
    /* check if there is any declined friendship request between the viewer & the target */
    if ($this->_logged_in) {
      $check = $db->query(sprintf('SELECT COUNT(*) as count FROM friends WHERE status = -1 AND (user_one_id = %1$s AND user_two_id = %2$s) OR (user_one_id = %2$s AND user_two_id = %1$s)', secure($this->_data['user_id'], 'int'),  secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check->fetch_assoc()['count'] > 0) {
        return true;
      }
    }
    return false;
  }


  /**
   * is_following
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function is_following($user_id)
  {
    if ($this->_logged_in) {
      return (in_array($user_id, $this->_data['followings_ids'])) ? true : false;
    }
    return false;
  }


  /**
   * is_friend_request
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function is_friend_request($user_id)
  {
    if ($this->_logged_in) {
      return (in_array($user_id, $this->_data['friend_requests_ids'])) ? true : false;
    }
    return false;
  }


  /**
   * is_friend_request_sent
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function is_friend_request_sent($user_id)
  {
    if ($this->_logged_in) {
      return (in_array($user_id, $this->_data['friend_requests_sent_ids'])) ? true : false;
    }
    return false;
  }


  public function is_top_friend($user_id)
  {
    global $db;
    if ($this->_logged_in) {
      $check = $db->query(sprintf("SELECT COUNT(*) as count FROM users_top_friends WHERE user_id = %s AND friend_id = %s", secure($this->_data['user_id'], 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check->fetch_assoc()['count'] > 0) {
        return true;
      }
    }
    return false;
  }


  /**
   * poked
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function is_poked($user_id)
  {
    global $db;
    /* check if the viewer poked the target before */
    if ($this->_logged_in) {
      $check = $db->query(sprintf("SELECT COUNT(*) as count FROM users_pokes WHERE user_id = %s AND poked_id = %s ", secure($this->_data['user_id'], 'int'),  secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check->fetch_assoc()['count'] > 0) {
        return true;
      }
    }
    return false;
  }


  /**
   * delete_user
   * 
   * @param integer $user_id
   * @return void
   */
  public function delete_user($user_id)
  {
    global $db;
    /* (check&get) user */
    $get_user = $db->query(sprintf("SELECT user_group FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_user->num_rows == 0) {
      _error(403);
    }
    $user = $get_user->fetch_assoc();
    // delete user
    $can_delete = false;
    /* target is (admin|moderator) */
    if ($user['user_group'] < 3) {
      throw new Exception(__("You can not delete admin/morderator accounts"));
    }
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_delete = true;
    }
    /* viewer is the target */
    if ($this->_data['user_id'] == $user_id) {
      $can_delete = true;
    }
    /* delete the user */
    if ($can_delete) {
      /* delete user posts */
      $this->delete_posts($user_id);
      /* delete the user */
      $db->query(sprintf("DELETE FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete user posts */
      $this->delete_posts($user_id);
      /* delete all user pages */
      $db->query(sprintf("DELETE FROM pages WHERE page_admin = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("DELETE FROM pages_admins WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update all pages likes counter */
      $db->query(sprintf("UPDATE pages SET page_likes = IF(page_likes=0,0,page_likes-1) WHERE page_id IN (SELECT page_id FROM pages_likes WHERE user_id = %s)", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete the user from all liked pages */
      $db->query(sprintf("DELETE FROM pages_likes WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete the user from all invited pages */
      $db->query(sprintf("DELETE FROM pages_invites WHERE user_id = %s OR from_user_id = %s", secure($user_id, 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete all user groups */
      $db->query(sprintf("DELETE FROM `groups` WHERE group_admin = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("DELETE FROM groups_admins WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update all groups members counter */
      $db->query(sprintf("UPDATE `groups` SET group_members = IF(group_members=0,0,group_members-1) WHERE group_id IN (SELECT group_id FROM groups_members WHERE user_id = %s)", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete the user from all joined groups */
      $db->query(sprintf("DELETE FROM groups_members WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete all user events */
      $db->query(sprintf("DELETE FROM `events` WHERE event_admin = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update all events counters */
      $db->query(sprintf("UPDATE `events` SET event_invited = IF(event_invited=0,0,event_invited-1) WHERE event_id IN (SELECT event_id FROM events_members WHERE is_invited = '1' AND user_id = %s)", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("UPDATE `events` SET event_interested = IF(event_interested=0,0,event_interested-1) WHERE event_id IN (SELECT event_id FROM events_members WHERE is_interested = '1' AND user_id = %s)", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("UPDATE `events` SET event_going = IF(event_going=0,0,event_going-1) WHERE event_id IN (SELECT event_id FROM events_members WHERE is_going = '1' AND user_id = %s)", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete the user from all joined events */
      $db->query(sprintf("DELETE FROM events_members WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete all user friends connections */
      $db->query(sprintf('DELETE FROM friends WHERE user_one_id = %1$s OR user_two_id = %1$s', secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete all user following connections */
      $db->query(sprintf('DELETE FROM followings WHERE user_id = %1$s OR following_id = %1$s', secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete all user messages */
      $db->query(sprintf('DELETE FROM conversations_messages WHERE user_id = %s', secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * _follow
   * 
   * @param integer $user_id
   * @return void
   */
  private function _follow($user_id)
  {
    global $db, $date;
    /* check blocking */
    if ($this->blocked($user_id)) {
      _error(403);
    }
    /* check if the viewer already follow the target */
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM followings WHERE user_id = %s AND following_id = %s", secure($this->_data['user_id'], 'int'),  secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* if yes -> return */
    if ($check->fetch_assoc()['count'] > 0) return;
    /* add as following */
    $db->query(sprintf("INSERT INTO followings (user_id, following_id, time) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'),  secure($user_id, 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* post new notification */
    $this->post_notification(['to_user_id' => $user_id, 'action' => 'follow']);
    /* points balance */
    $this->points_balance("add", $user_id, "follow", $this->_data['user_id']);
  }


  /**
   * _unfollow
   * 
   * @param integer $user_id
   * @return void
   */
  private function _unfollow($user_id)
  {
    global $db;
    /* check if the viewer already follow the target */
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM followings WHERE user_id = %s AND following_id = %s", secure($this->_data['user_id'], 'int'),  secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* if no -> return */
    if ($check->fetch_assoc()['count'] == 0) return;
    /* delete from viewer's followings */
    $db->query(sprintf("DELETE FROM followings WHERE user_id = %s AND following_id = %s", secure($this->_data['user_id'], 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete notification */
    $this->delete_notification($user_id, 'follow');
    /* points balance */
    $this->points_balance("delete", $user_id, "follow");
  }



  /* ------------------------------- */
  /* Real-Time */
  /* ------------------------------- */

  /**
   * reset_realtime_counters
   * 
   * @param string $counter
   * @return void
   */
  public function reset_realtime_counters($counter)
  {
    global $db;
    switch ($counter) {
      case 'friend_requests':
        $db->query(sprintf("UPDATE users SET user_live_requests_counter = 0 WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'messages':
        $db->query(sprintf("UPDATE users SET user_live_messages_counter = 0 WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'notifications':
        $db->query(sprintf("UPDATE users SET user_live_notifications_counter = 0 WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        $db->query(sprintf("UPDATE notifications SET seen = '1' WHERE to_user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;
    }
  }



  /* ------------------------------- */
  /* Notifications */
  /* ------------------------------- */

  /**
   * get_notifications
   * 
   * @param integer $offset
   * @param integer $last_notification_id
   * @return array
   */
  public function get_notifications($offset = 0, $last_notification_id = null)
  {
    global $db, $system, $control_panel;
    if (!isset($control_panel)) {
      $control_panel['url'] = ($this->_is_admin) ? "admincp" : "modcp";
    }
    $offset *= $system['max_results'];
    $notifications = [];
    if ($last_notification_id !== null) {
      $get_notifications = $db->query(sprintf("SELECT notifications.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified, pages.page_id, pages.page_title, pages.page_picture FROM notifications LEFT JOIN users ON notifications.from_user_id = users.user_id AND notifications.from_user_type = 'user' LEFT JOIN pages ON notifications.from_user_id = pages.page_id AND notifications.from_user_type = 'page' WHERE (notifications.to_user_id = %s OR notifications.to_user_id = '0') AND notifications.notification_id > %s ORDER BY notifications.notification_id DESC", secure($this->_data['user_id'], 'int'), secure($last_notification_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      $get_notifications = $db->query(sprintf("SELECT notifications.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified, pages.page_id, pages.page_title, pages.page_picture FROM notifications LEFT JOIN users ON notifications.from_user_id = users.user_id AND notifications.from_user_type = 'user' LEFT JOIN pages ON notifications.from_user_id = pages.page_id AND notifications.from_user_type = 'page' WHERE notifications.to_user_id = %s OR notifications.to_user_id = '0' ORDER BY notifications.notification_id DESC LIMIT %s, %s", secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_notifications->num_rows > 0) {
      while ($notification = $get_notifications->fetch_assoc()) {
        /* prepare notification name & picture */
        if ($notification['from_user_type'] == "user") {
          $notification['user_picture'] = get_picture($notification['user_picture'], $notification['user_gender']);
          $notification['name'] = ($system['show_usernames_enabled']) ? $notification['user_name'] : $notification['user_firstname'] . " " . $notification['user_lastname'];
        } else {
          $notification['user_picture'] = get_picture($notification['page_picture'], "page");
          $notification['name'] = $notification['page_title'];
        }
        /* prepare notification notify_id */
        $notification['notify_id'] = ($notification['notify_id']) ? "?notify_id=" . $notification['notify_id'] : "";
        /* prepare notification node_url */
        if (strpos($notification['node_url'], "-[guid=]") !== false) {
          /* Note: GUID in node_url to make the notification record unique */
          $notification['node_url'] = explode("-[guid=]", $notification['node_url'])[0];
        }
        /* parse notification */
        switch ($notification['action']) {
          case 'mass_notification':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $notification['node_url'];
            $notification['message'] = $notification['message'];
            break;

          case 'friend_add':
            $notification['icon'] = "fa fa-user-plus";
            $notification['url'] = $system['system_url'] . '/' . $notification['user_name'];
            $notification['message'] = __("sent you a friend request");
            break;

          case 'friend_accept':
            $notification['icon'] = "fa fa-user-plus";
            $notification['url'] = $system['system_url'] . '/' . $notification['user_name'];
            $notification['message'] = __("accepted your friend request");
            break;

          case 'follow':
            $notification['icon'] = "fa fa-rss";
            $notification['url'] = $system['system_url'] . '/' . $notification['user_name'];
            $notification['message'] = __("now following you");
            break;

          case 'poke':
            $notification['icon'] = "fa fa-hand-point-right";
            $notification['url'] = $system['system_url'] . '/' . $notification['user_name'];
            $notification['message'] = __("poked you");
            break;

          case 'gift':
            $notification['icon'] = "fa fa-gift";
            $notification['url'] = $system['system_url'] . '/' . $this->_data['user_name'] . '?gift=' . $notification['node_url'];
            $notification['message'] = __("Sent you a gift");
            break;

          case 'subscribe_user':
          case 'subscribe_profile':
            $notification['icon'] = "fa fa-rss";
            $notification['url'] = $system['system_url'] . '/' . $notification['user_name'];
            $notification['message'] = __("subscribed to your profile");
            break;

          case 'subscribe_page':
            $notification['icon'] = "fa fa-rss";
            $notification['url'] = $system['system_url'] . '/pages/' . $notification['node_url'];
            $notification['message'] = __("subscribed to your page") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'subscribe_group':
            $notification['icon'] = "fa fa-rss";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'];
            $notification['message'] = __("subscribed to your group") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'paid_post':
            $notification['icon'] = "fa fa-dollar-sign";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("bought your post");
            break;

          case 'react_like':
          case 'react_love':
          case 'react_haha':
          case 'react_yay':
          case 'react_wow':
          case 'react_sad':
          case 'react_angry':
            $notification['reaction'] = substr($notification['action'], strpos($notification['action'], "_") + 1);
            if ($notification['node_type'] == "post") {
              $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
              $notification['message'] = __("reacted to your post");
            } elseif ($notification['node_type'] == "photo") {
              $notification['url'] = $system['system_url'] . '/photos/' . $notification['node_url'];
              $notification['message'] = __("reacted to your photo");
            } elseif ($notification['node_type'] == "post_comment") {
              $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'] . $notification['notify_id'];
              $notification['message'] = __("reacted to your comment");
            } elseif ($notification['node_type'] == "photo_comment") {
              $notification['url'] = $system['system_url'] . '/photos/' . $notification['node_url'] . $notification['notify_id'];
              $notification['message'] = __("reacted to your comment");
            } elseif ($notification['node_type'] == "post_reply") {
              $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'] . $notification['notify_id'];
              $notification['message'] = __("reacted to your reply");
            } elseif ($notification['node_type'] == "photo_reply") {
              $notification['url'] = $system['system_url'] . '/photos/' . $notification['node_url'] . $notification['notify_id'];
              $notification['message'] = __("reacted to your reply");
            }
            break;

          case 'comment':
            $notification['icon'] = "fa fa-comment";
            if ($notification['node_type'] == "post") {
              $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'] . $notification['notify_id'];
              $notification['message'] = __("commented on your post");
            } elseif ($notification['node_type'] == "photo") {
              $notification['url'] = $system['system_url'] . '/photos/' . $notification['node_url'] . $notification['notify_id'];
              $notification['message'] = __("commented on your photo");
            }
            break;

          case 'reply':
            $notification['icon'] = "fa fa-comment";
            if ($notification['node_type'] == "post") {
              $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'] . $notification['notify_id'];
            } elseif ($notification['node_type'] == "photo") {
              $notification['url'] = $system['system_url'] . '/photos/' . $notification['node_url'] . $notification['notify_id'];
            }
            $notification['message'] = __("replied to your comment");
            break;

          case 'share':
            $notification['icon'] = "fa fa-share";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("shared your post");
            break;

          case 'vote':
            $notification['icon'] = "fa fa-check-circle";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("voted on your poll");
            break;

          case 'mention':
            $notification['icon'] = "fa fa-comment";
            switch ($notification['node_type']) {
              case 'post':
                $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
                $notification['message'] = __("mentioned you in a post");
                break;

              case 'comment_post':
                $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'] . $notification['notify_id'];
                $notification['message'] = __("mentioned you in a comment");
                break;

              case 'comment_photo':
                $notification['url'] = $system['system_url'] . '/photos/' . $notification['node_url'] . $notification['notify_id'];
                $notification['message'] = __("mentioned you in a comment");
                break;

              case 'reply_post':
                $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'] . $notification['notify_id'];
                $notification['message'] = __("mentioned you in a reply");
                break;

              case 'reply_photo':
                $notification['url'] = $system['system_url'] . '/photos/' . $notification['node_url'] . $notification['notify_id'];
                $notification['message'] = __("mentioned you in a reply");
                break;
            }
            break;

          case 'profile_visit':
            $notification['icon'] = "fa fa-eye";
            $notification['url'] = $system['system_url'] . '/' . $notification['user_name'];
            $notification['message'] = __("visited your profile");
            break;

          case 'wall':
            $notification['icon'] = "fa fa-comment";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("posted on your wall");
            break;

          case 'page_invitation':
            $notification['icon'] = "fa fa-flag";
            $notification['url'] = $system['system_url'] . '/pages/' . $notification['node_url'];
            $notification['message'] = __("invite you to like a page") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'page_admin_addation':
            $notification['icon'] = "fa fa-users";
            $notification['url'] = $system['system_url'] . '/pages/' . $notification['node_url'];
            $notification['message'] = __("added you as admin to") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "' " . __("page");
            break;

          case 'page_event':
            $notification['icon'] = "fa fa-calendar";
            $notification['url'] = $system['system_url'] . '/events/' . $notification['node_url'];
            $notification['message'] = __("created new event");
            break;

          case 'group_join':
            $notification['icon'] = "fa fa-users";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'] . '/settings/requests';
            $notification['message'] = __("asked to join your group") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'group_add':
            $notification['icon'] = "fa fa-users";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'];
            $notification['message'] = __("added you to") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "' " . __("group");
            break;

          case 'group_accept':
            $notification['icon'] = "fa fa-users";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'];
            $notification['message'] = __("accepted your request to join") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "' " . __("group");
            break;

          case 'group_admin_addation':
            $notification['icon'] = "fa fa-users";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'];
            $notification['message'] = __("added you as admin to") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "' " . __("group");
            break;

          case 'group_post_pending':
            $notification['icon'] = "fa fa-users";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'] . '?pending';
            $notification['message'] = __("added pending post in your group") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'group_post_approval':
            $notification['icon'] = "fa fa-users";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'];
            $notification['message'] = __("approved your your pending post in group") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'event_invitation':
            $notification['icon'] = "fa fa-calendar";
            $notification['url'] = $system['system_url'] . '/events/' . $notification['node_url'];
            $notification['message'] = __("invite you to join an event") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'event_post_pending':
            $notification['icon'] = "fa fa-calendar";
            $notification['url'] = $system['system_url'] . '/events/' . $notification['node_url'] . '?pending';
            $notification['message'] = __("added pending post in your event") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'event_post_approval':
            $notification['icon'] = "fa fa-calendar";
            $notification['url'] = $system['system_url'] . '/events/' . $notification['node_url'];
            $notification['message'] = __("approved your your pending post in event") . " '" . html_entity_decode($notification['node_type'], ENT_QUOTES) . "'";
            break;

          case 'forum_reply':
            $notification['icon'] = "fa fa-comment";
            $notification['url'] = $system['system_url'] . '/forums/thread/' . $notification['node_url'];
            $notification['message'] = __("replied to your thread");
            break;

          case 'money_sent':
            $notification['icon'] = "fa fa-gift";
            $notification['url'] = $system['system_url'] . '/wallet';
            $notification['message'] = __("sent you") . " " . print_money($notification['node_type']);
            break;

          case 'tip_sent':
            $notification['icon'] = "fa fa-dollar-sign";
            $notification['url'] = $system['system_url'] . '/wallet';
            $notification['message'] = __("Tip you with") . " " . print_money($notification['node_type']);
            break;

          case 'coinpayments_complete':
            $notification['icon'] = "fab fa-bitcoin";
            $notification['url'] = $system['system_url'] . '/settings/coinpayments';
            $notification['message'] = __("Your CoinPayments transaction complete successfully");
            break;

          case 'bank_transfer':
            $notification['icon'] = "fa fa-university";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/bank';
            $notification['message'] = __("Sent new bank transfer");
            break;

          case 'bank_transfer_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/bank';
            $notification['message'] = __("Your bank transfer has been approved");
            break;

          case 'bank_transfer_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/bank';
            $notification['message'] = __("Your bank transfer has been declined");
            break;

          case 'wallet_withdrawal':
            $notification['icon'] = "fa fa-exchange-alt";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/wallet/payments';
            $notification['message'] = __("Sent new wallet withdrawal request");
            break;

          case 'wallet_withdrawal_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/wallet/payments';
            $notification['message'] = __("Your wallet withdrawal request has been approved");
            break;

          case 'wallet_withdrawal_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/wallet/payments';
            $notification['message'] = __("Your wallet withdrawal request has been declined");
            break;

          case 'affiliates_withdrawal':
            $notification['icon'] = "fa fa-exchange-alt";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/affiliates/payments';
            $notification['message'] = __("Sent new affiliates withdrawal request");
            break;

          case 'affiliates_withdrawal_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/affiliates/payments';
            $notification['message'] = __("Your affiliates withdrawal request has been approved");
            break;

          case 'affiliates_withdrawal_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/affiliates/payments';
            $notification['message'] = __("Your affiliates withdrawal request has been declined");
            break;

          case 'points_withdrawal':
            $notification['icon'] = "fa fa-piggy-bank";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/points/payments';
            $notification['message'] = __("Sent new points withdrawal request");
            break;

          case 'points_withdrawal_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/points/payments';
            $notification['message'] = __("Your points withdrawal request has been approved");
            break;

          case 'points_withdrawal_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/points/payments';
            $notification['message'] = __("Your points withdrawal request has been declined");
            break;

          case 'market_withdrawal':
            $notification['icon'] = "fa fa-shopping-bag";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/payments';
            $notification['message'] = __("Sent new market withdrawal request");
            break;

          case 'market_withdrawal_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/market';
            $notification['message'] = __("Your market withdrawal request has been approved");
            break;

          case 'market_withdrawal_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/market';
            $notification['message'] = __("Your market withdrawal request has been declined");
            break;

          case 'market_outofstock':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("Your product is out of stock");
            break;

          case 'market_order':
            $notification['icon'] = "fa fa-shopping-bag";
            $notification['url'] = $system['system_url'] . '/market/sales?query=' . $notification['node_url'];
            $notification['message'] = __("You got new order");
            break;

          case 'market_order_tracking_updated':
            $notification['icon'] = "fa fa-shopping-bag";
            $notification['url'] = $system['system_url'] . '/market/orders?query=' . $notification['node_url'];
            $notification['message'] = __("Your order tracking updated");
            break;

          case 'market_order_status_updated':
            $notification['icon'] = "fa fa-shopping-bag";
            $notification['url'] = $system['system_url'] . '/market/orders?query=' . $notification['node_url'];
            $notification['message'] = __("Your order status updated");
            break;

          case 'market_order_delivered':
            $notification['icon'] = "fa fa-shopping-bag";
            $notification['url'] = $system['system_url'] . '/market/sales?query=' . $notification['node_url'];
            $notification['message'] = __("Your order delivered");
            break;

          case 'job_application':
            $notification['icon'] = "fa fa-briefcase";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("applied to your job");
            break;

          case 'funding_withdrawal':
            $notification['icon'] = "fa fa-hand-holding-usd";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/funding/payments';
            $notification['message'] = __("Sent new funding withdrawal request");
            break;

          case 'funding_withdrawal_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/funding';
            $notification['message'] = __("Your funding withdrawal request has been approved");
            break;

          case 'funding_withdrawal_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/funding';
            $notification['message'] = __("Your funding withdrawal request has been declined");
            break;

          case 'funding_donation':
            $notification['icon'] = "fa fa-hand-holding-usd";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("donated to your funding request with") . " " . print_money($notification['node_type']);
            break;

          case 'monetization_withdrawal':
            $notification['icon'] = "fa fa-hand-holding-usd";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/monetization/payments';
            $notification['message'] = __("Sent new monetization withdrawal request");
            break;

          case 'monetization_withdrawal_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/monetization';
            $notification['message'] = __("Your monetization withdrawal request has been approved");
            break;

          case 'monetization_withdrawal_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/monetization';
            $notification['message'] = __("Your monetization withdrawal request has been declined");
            break;

          case 'ads_campaign_added':
            $notification['icon'] = "fa fa-bullseye";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/ads/users_ads';
            $notification['message'] = __("Added new ads campaign");
            break;

          case 'ads_campaign_edited':
            $notification['icon'] = "fa fa-bullseye";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/ads/users_ads';
            $notification['message'] = __("Edited ads campaign");
            break;

          case 'ads_campaign_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/ads';
            $notification['message'] = __("Your ads campaign has been approved");
            break;

          case 'ads_campaign_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/ads';
            $notification['message'] = __("Your ads campaign has been declined");
            break;

          case 'report':
            $notification['icon'] = "fa fa-flag";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/reports';
            $notification['message'] = __("reported new content");
            break;

          case 'verification_request':
            $notification['icon'] = "fa fa-check-circle";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/verification';
            $notification['message'] = __("sent new verification request");
            break;

          case 'verification_request_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/verification';
            $notification['message'] = __("Your verification request has been approved");
            break;

          case 'verification_request_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/settings/verification';
            $notification['message'] = __("Your verification request has been declined");
            break;

          case 'verification_request_page_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/pages/' . $notification['node_url'] . '/settings/verification';
            $notification['message'] = __("Your page verification request has been approved");
            break;

          case 'verification_request_page_declined':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/pages/' . $notification['node_url'] . '/settings/verification';
            $notification['message'] = __("Your page verification request has been declined");
            break;

          case 'live_stream':
            $notification['icon'] = "fa fa-video";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("is live now");
            break;

          case 'async_request':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'];
            $notification['message'] = $notification['message'];
            break;

          case 'video_converted':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("Your video has been converted");
            break;

          case 'page_review':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/pages/' . $notification['node_url'] . '/reviews';
            $notification['message'] = __("reviewed your page");
            break;

          case 'page_review_reply':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/pages/' . $notification['node_url'] . '/reviews';
            $notification['message'] = __("replied on your review");
            break;

          case 'group_review':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'] . '/reviews';
            $notification['message'] = __("reviewed your group");
            break;

          case 'group_review_reply':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/groups/' . $notification['node_url'] . '/reviews';
            $notification['message'] = __("replied on your review");
            break;

          case 'event_review':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/events/' . $notification['node_url'] . '/reviews';
            $notification['message'] = __("reviewed your event");
            break;

          case 'event_review_reply':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/events/' . $notification['node_url'] . '/reviews';
            $notification['message'] = __("replied on your review");
            break;

          case 'post_review':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("reviewed your post");
            break;

          case 'post_review_reply':
            $notification['icon'] = "fa fa-star";
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("replied on your review");
            break;

          case 'pending_post':
            $notification['icon'] = "fa fa-clock";
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts/pending';
            $notification['message'] = __("sent new post for approval");
            break;

          case 'pending_post_approved':
            $notification['system_notification'] = true;
            $notification['icon'] = "fa fa-bell";
            $notification['user_picture'] = get_picture("", "system");
            $notification['name'] = __("System") . " " . __("Message");
            $notification['url'] = $system['system_url'] . '/posts/' . $notification['node_url'];
            $notification['message'] = __("Your post has been approved");
            break;

          case 'chat_message_paid':
            $notification['icon'] = "fa fa-dollar-sign";
            $notification['url'] = $system['system_url'] . '/messages';
            $notification['message'] = __("sent you a paid message");
            break;
        }
        /* prepare message */
        $notification['full_message'] = html_entity_decode($notification['name'], ENT_QUOTES) . " " . html_entity_decode($notification['message'], ENT_QUOTES);
        $notifications[] = $notification;
      }
    }
    return $notifications;
  }


  /**
   * post_notification
   * 
   * @param array $args
   * @return void
   */
  public function post_notification($args = [])
  {
    global $gettextTranslator, $db, $date, $system, $control_panel;
    if (!isset($control_panel)) {
      $control_panel['url'] = ($this->_is_admin) ? "admincp" : "modcp";
    }
    /* initialize arguments */
    $to_user_id = !isset($args['to_user_id']) ? _error(400) : $args['to_user_id'];
    $from_user_id = !isset($args['from_user_id']) ? $this->_data['user_id'] : $args['from_user_id'];
    $from_user_type = !isset($args['from_user_type']) ? 'user' : $args['from_user_type'];
    $action = !isset($args['action']) ? _error(400) : $args['action'];
    $node_type = !isset($args['node_type']) ? '' : $args['node_type'];
    $node_url = !isset($args['node_url']) ? '' : $args['node_url'];
    $message = !isset($args['message']) ? '' : $args['message'];
    $notify_id = !isset($args['notify_id']) ? '' : $args['notify_id'];
    /* if the viewer is the target */
    if ($this->_data['user_id'] == $to_user_id && !in_array($action, ["async_request", "video_converted"])) {
      return;
    }
    /* get receiver user */
    $get_receiver = $db->query(sprintf("SELECT * FROM users WHERE user_id = %s", secure($to_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_receiver->num_rows == 0) {
      return;
    }
    if ($system['email_notifications'] || $system['onesignal_notification_enabled']) {
      /* prepare receiver */
      $receiver = $get_receiver->fetch_assoc();
      $receiver['name'] = ($system['show_usernames_enabled']) ? $receiver['user_name'] : $receiver['user_firstname'] . " " . $receiver['user_lastname'];
    }
    /* insert notification */
    $db->query(sprintf("INSERT INTO notifications (to_user_id, from_user_id, from_user_type, action, node_type, node_url, notify_id, message, time) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($to_user_id, 'int'), secure($from_user_id, 'int'), secure($from_user_type), secure($action), secure($node_type), secure($node_url), secure($notify_id), secure($message), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* update notifications counter +1 */
    $db->query(sprintf("UPDATE users SET user_live_notifications_counter = user_live_notifications_counter + 1 WHERE user_id = %s", secure($to_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* email notifications */
    if ($system['email_notifications']) {
      /* prepare notification notify_id */
      $notify_id = ($notify_id) ? "?notify_id=" . $notify_id : "";
      /* prepare notification node_url */
      if (strpos($node_url, "-[guid=]") !== false) {
        /* Note: GUID in node_url to make the notification record unique */
        $node_url = explode("-[guid=]", $node_url)[0];
      }
      /* parse notification */
      switch ($action) {
        case 'friend_add':
          if ($system['email_friend_requests'] && $receiver['email_friend_requests']) {
            $notification['url'] = $system['system_url'] . '/' . $node_url;
            $notification['message'] = __("sent you a friend request");
          }
          break;

        case 'friend_accept':
          if ($system['email_friend_requests'] && $receiver['email_friend_requests']) {
            $notification['url'] = $system['system_url'] . '/' . $node_url;
            $notification['message'] = __("accepted your friend request");
          }
          break;

        case 'react_like':
        case 'react_love':
        case 'react_haha':
        case 'react_yay':
        case 'react_wow':
        case 'react_sad':
        case 'react_angry':
          if ($system['email_post_likes'] && $receiver['email_post_likes']) {
            if ($node_type == "post") {
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
              $notification['message'] = __("reacted to your post");
            } elseif ($node_type == "photo") {
              $notification['url'] = $system['system_url'] . '/photos/' . $node_url;
              $notification['message'] = __("reacted to your photo");
            } elseif ($node_type == "post_comment") {
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
              $notification['message'] = __("reacted to your comment");
            } elseif ($node_type == "photo_comment") {
              $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
              $notification['message'] = __("reacted to your comment");
            } elseif ($node_type == "post_reply") {
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
              $notification['message'] = __("reacted to your reply");
            } elseif ($node_type == "photo_reply") {
              $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
              $notification['message'] = __("reacted to your reply");
            }
          }
          break;

        case 'comment':
          if ($system['email_post_comments'] && $receiver['email_post_comments']) {
            if ($node_type == "post") {
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
              $notification['message'] = __("commented on your post");
            } elseif ($node_type == "photo") {
              $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
              $notification['message'] = __("commented on your photo");
            }
          }
          break;

        case 'reply':
          if ($system['email_post_comments'] && $receiver['email_post_comments']) {
            if ($node_type == "post") {
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
            } elseif ($node_type == "photo") {
              $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
            }
            $notification['message'] = __("replied to your comment");
          }
          break;

        case 'share':
          if ($system['email_post_shares'] && $receiver['email_post_shares']) {
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
            $notification['message'] = __("shared your post");
          }
          break;

        case 'mention':
          if ($system['email_mentions'] && $receiver['email_mentions']) {
            switch ($node_type) {
              case 'post':
                $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
                $notification['message'] = __("mentioned you in a post");
                break;

              case 'comment_post':
                $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
                $notification['message'] = __("mentioned you in a comment");
                break;

              case 'comment_photo':
                $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
                $notification['message'] = __("mentioned you in a comment");
                break;

              case 'reply_post':
                $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
                $notification['message'] = __("mentioned you in a reply");
                break;

              case 'reply_photo':
                $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
                $notification['message'] = __("mentioned you in a reply");
                break;
            }
          }
          break;

        case 'profile_visit':
          if ($system['email_profile_visits'] && $receiver['email_profile_visits']) {
            $notification['url'] = $system['system_url'] . '/' . $this->_data['user_name'];
            $notification['message'] = __("visited your profile");
          }
          break;

        case 'wall':
          if ($system['email_wall_posts'] && $receiver['email_wall_posts']) {
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
            $notification['message'] = __("posted on your wall");
          }
          break;

        case 'verification_request':
          if ($system['email_admin_verifications'] && $receiver['email_admin_verifications']) {
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/verification';
            $notification['message'] = __("sent new verification request");
          }
          break;

        case 'verification_request_approved':
          if ($system['email_user_verification'] && $receiver['email_user_verification']) {
            $notification['system_notification'] = true;
            $notification['url'] = $system['system_url'] . '/settings/verification';
            $notification['message'] = __("Your verification request has been approved");
          }
          break;

        case 'verification_request_declined':
          if ($system['email_user_verification'] && $receiver['email_user_verification']) {
            $notification['system_notification'] = true;
            $notification['url'] = $system['system_url'] . '/settings/verification';
            $notification['message'] = __("Your verification request has been declined");
          }
          break;

        case 'verification_request_page_approved':
          if ($system['email_user_verification'] && $receiver['email_user_verification']) {
            $notification['system_notification'] = true;
            $notification['url'] = $system['system_url'] . '/pages/' . $node_url . '/settings/verification';
            $notification['message'] = __("Your page verification request has been approved");
          }
          break;

        case 'verification_request_page_declined':
          if ($system['email_user_verification'] && $receiver['email_user_verification']) {
            $notification['system_notification'] = true;
            $notification['url'] = $system['system_url'] . '/pages/' . $node_url . '/settings/verification';
            $notification['message'] = __("Your page verification request has been declined");
          }
          break;

        case 'pending_post':
          if ($system['email_admin_post_approval'] && $receiver['email_admin_post_approval']) {
            $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts/pending';
            $notification['message'] = __("sent new post for approval");
          }
          break;

        case 'pending_post_approved':
          if ($system['email_user_post_approval'] && $receiver['email_user_post_approval']) {
            $notification['system_notification'] = true;
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
            $notification['message'] = __("Your post has been approved");
          }
          break;

        default:
          return;
          break;
      }
      /* prepare notification email */
      if ($notification['message']) {
        $subject = __("Notification from") . " " . html_entity_decode(__($system['system_title']), ENT_QUOTES);
        $body = get_email_template("notification_email", $subject, ["receiver" => $receiver, "notification" => $notification]);
        /* send email */
        _email($receiver['user_email'], $subject, $body['html'], $body['plain']);
      }
    }
    /* onesignal push notifications */
    if ($system['onesignal_notification_enabled'] && $receiver['onesignal_user_id']) {
      /* set notification language to receiver's language */
      if ($receiver['user_language'] != DEFAULT_LOCALE) {
        $gettextTranslator->loadTranslations(Gettext\Translations::fromPoFile(ABSPATH . 'content/languages/locale/' . $receiver['user_language'] . '/LC_MESSAGES/messages.po'));
      }
      /* prepare notification notify_id */
      $notify_id = ($notify_id) ? "?notify_id=" . $notify_id : "";
      /* prepare notification node_url */
      if (strpos($node_url, "-[guid=]") !== false) {
        /* Note: GUID in node_url to make the notification record unique */
        $node_url = explode("-[guid=]", $node_url)[0];
      }
      /* parse notification */
      switch ($action) {
        case 'mass_notification':
          $notification['url'] = $control_panel['url'] . '/mass_notifications';
          $notification['message'] = __("sent new mass notification");
          break;

        case 'friend_add':
          $notification['url'] = $system['system_url'] . '/' . $node_url;
          $notification['message'] = __("sent you a friend request");
          break;

        case 'friend_accept':
          $notification['url'] = $system['system_url'] . '/' . $node_url;
          $notification['message'] = __("accepted your friend request");
          break;

        case 'follow':
          $notification['url'] = $system['system_url'] . '/' . $node_url;
          $notification['message'] = __("now following you");
          break;

        case 'poke':
          $notification['url'] = $system['system_url'] . '/' . $node_url;
          $notification['message'] = __("poked you");
          break;

        case 'gift':
          $notification['url'] = $system['system_url'] . '/' . $this->_data['user_name'] . '?gift=' . $node_url;
          $notification['message'] = __("Sent you a gift");
          break;

        case 'subscribe_user':
        case 'subscribe_profile':
          $notification['url'] = $system['system_url'] . '/' . $node_url;
          $notification['message'] = __("subscribed to your profile");
          break;

        case 'subscribe_page':
          $notification['url'] = $system['system_url'] . '/pages/' . $node_url;
          $notification['message'] = __("subscribed to your page");
          break;

        case 'subscribe_group':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url;
          $notification['message'] = __("subscribed to your group");
          break;

        case 'paid_post':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("paid to see your post");
          break;

        case 'react_like':
        case 'react_love':
        case 'react_haha':
        case 'react_yay':
        case 'react_wow':
        case 'react_sad':
        case 'react_angry':
          if ($node_type == "post") {
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
            $notification['message'] = __("reacted to your post");
          } elseif ($node_type == "photo") {
            $notification['url'] = $system['system_url'] . '/photos/' . $node_url;
            $notification['message'] = __("reacted to your photo");
          } elseif ($node_type == "post_comment") {
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
            $notification['message'] = __("reacted to your comment");
          } elseif ($node_type == "photo_comment") {
            $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
            $notification['message'] = __("reacted to your comment");
          } elseif ($node_type == "post_reply") {
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
            $notification['message'] = __("reacted to your reply");
          } elseif ($node_type == "photo_reply") {
            $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
            $notification['message'] = __("reacted to your reply");
          }
          break;

        case 'comment':
          if ($node_type == "post") {
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
            $notification['message'] = __("commented on your post");
          } elseif ($node_type == "photo") {
            $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
            $notification['message'] = __("commented on your photo");
          }
          break;

        case 'reply':
          if ($node_type == "post") {
            $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
          } elseif ($node_type == "photo") {
            $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
          }
          $notification['message'] = __("replied to your comment");
          break;

        case 'share':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("shared your post");
          break;

        case 'vote':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("voted on your poll");
          break;

        case 'mention':
          switch ($node_type) {
            case 'post':
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
              $notification['message'] = __("mentioned you in a post");
              break;

            case 'comment_post':
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
              $notification['message'] = __("mentioned you in a comment");
              break;

            case 'comment_photo':
              $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
              $notification['message'] = __("mentioned you in a comment");
              break;

            case 'reply_post':
              $notification['url'] = $system['system_url'] . '/posts/' . $node_url . $notify_id;
              $notification['message'] = __("mentioned you in a reply");
              break;

            case 'reply_photo':
              $notification['url'] = $system['system_url'] . '/photos/' . $node_url . $notify_id;
              $notification['message'] = __("mentioned you in a reply");
              break;
          }
          break;

        case 'profile_visit':
          $notification['url'] = $system['system_url'] . '/' . $this->_data['user_name'];
          $notification['message'] = __("visited your profile");
          break;

        case 'wall':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("posted on your wall");
          break;

        case 'page_invitation':
          $notification['url'] = $system['system_url'] . '/pages/' . $node_url;
          $notification['message'] = __("invite you to like a page") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "'";
          break;

        case 'page_admin_addation':
          $notification['url'] = $system['system_url'] . '/pages/' . $node_url;
          $notification['message'] = __("added you as admin to") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "' " . __("page");
          break;

        case 'page_event':
          $notification['url'] = $system['system_url'] . '/events/' . $node_url;
          $notification['message'] = __("created new event");
          break;

        case 'group_join':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url . '/settings/requests';
          $notification['message'] = __("asked to join your group") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "'";
          break;

        case 'group_add':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url;
          $notification['message'] = __("added you to") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "' " . __("group");
          break;

        case 'group_accept':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url;
          $notification['message'] = __("accepted your request to join") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "' " . __("group");
          break;

        case 'group_admin_addation':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url;
          $notification['message'] = __("added you as admin to") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "' " . __("group");
          break;

        case 'group_post_pending':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url . '?pending';
          $notification['message'] = __("added pending post in your group") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "'";
          break;

        case 'group_post_approval':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url;
          $notification['message'] = __("approved your your pending post in group") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "'";
          break;

        case 'event_invitation':
          $notification['url'] = $system['system_url'] . '/events/' . $node_url;
          $notification['message'] = __("invite you to join an event") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "'";
          break;

        case 'event_post_pending':
          $notification['url'] = $system['system_url'] . '/events/' . $node_url . '?pending';
          $notification['message'] = __("added pending post in your event") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "'";
          break;

        case 'event_post_approval':
          $notification['url'] = $system['system_url'] . '/events/' . $node_url;
          $notification['message'] = __("approved your your pending post in event") . " '" . html_entity_decode($node_type, ENT_QUOTES) . "'";
          break;

        case 'forum_reply':
          $notification['url'] = $system['system_url'] . '/forums/thread/' . $node_url;
          $notification['message'] = __("replied to your thread");
          break;

        case 'money_sent':
          $notification['url'] = $system['system_url'] . '/wallet';
          $notification['message'] = __("sent you") . " " . print_money($node_type);
          break;

        case 'tip_sent':
          $notification['url'] = $system['system_url'] . '/wallet';
          $notification['message'] = __("Tip you with") . " " . print_money($node_type);
          break;

        case 'coinpayments_complete':
          $notification['url'] = $system['system_url'] . '/settings/coinpayments';
          $notification['message'] = __("Your CoinPayments transaction complete successfully");
          break;

        case 'bank_transfer':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/bank';
          $notification['message'] = __("Sent new bank transfer");
          break;

        case 'bank_transfer_approved':
          $notification['url'] = $system['system_url'] . '/settings/bank';
          $notification['message'] = __("Approved your bank transfer");
          break;

        case 'bank_transfer_declined':
          $notification['url'] = $system['system_url'] . '/settings/bank';
          $notification['message'] = __("Declined your bank transfer");
          break;

        case 'wallet_withdrawal':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/wallet/payments';
          $notification['message'] = __("Sent new wallet withdrawal request");
          break;

        case 'wallet_withdrawal_approved':
          $notification['url'] = $system['system_url'] . '/wallet/payments';
          $notification['message'] = __("Approved your wallet withdrawal request");
          break;

        case 'wallet_withdrawal_declined':
          $notification['url'] = $system['system_url'] . '/wallet/payments';
          $notification['message'] = __("Declined your wallet withdrawal request");
          break;

        case 'affiliates_withdrawal':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/affiliates/payments';
          $notification['message'] = __("Sent new affiliates withdrawal request");
          break;

        case 'affiliates_withdrawal_approved':
          $notification['url'] = $system['system_url'] . '/settings/affiliates/payments';
          $notification['message'] = __("Approved your affiliates withdrawal request");
          break;

        case 'affiliates_withdrawal_declined':
          $notification['url'] = $system['system_url'] . '/settings/affiliates/payments';
          $notification['message'] = __("Declined your affiliates withdrawal request");
          break;

        case 'points_withdrawal':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/points/payments';
          $notification['message'] = __("Sent new points withdrawal request");
          break;

        case 'points_withdrawal_approved':
          $notification['url'] = $system['system_url'] . '/settings/points/payments';
          $notification['message'] = __("Approved your points withdrawal request");
          break;

        case 'points_withdrawal_declined':
          $notification['url'] = $system['system_url'] . '/settings/points/payments';
          $notification['message'] = __("Declined your points withdrawal request");
          break;

        case 'market_withdrawal':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/market/payments';
          $notification['message'] = __("Sent new market withdrawal request");
          break;

        case 'market_withdrawal_approved':
          $notification['url'] = $system['system_url'] . '/settings/market';
          $notification['message'] = __("Approved your market withdrawal request");
          break;

        case 'market_withdrawal_declined':
          $notification['url'] = $system['system_url'] . '/settings/market';
          $notification['message'] = __("Declined your market withdrawal request");
          break;

        case 'market_outofstock':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("Your product is out of stock");
          break;

        case 'market_order':
          $notification['url'] = $system['system_url'] . '/market/sales?query=' . $node_url;
          $notification['message'] = __("You got new order");
          break;

        case 'market_order_tracking_updated':
          $notification['url'] = $system['system_url'] . '/market/orders?query=' . $node_url;
          $notification['message'] = __("Your order tracking updated");
          break;

        case 'market_order_status_updated':
          $notification['url'] = $system['system_url'] . '/market/orders?query=' . $node_url;
          $notification['message'] = __("Your order status updated");
          break;

        case 'market_order_delivered':
          $notification['url'] = $system['system_url'] . '/market/sales?query=' . $node_url;
          $notification['message'] = __("Your order delivered");
          break;

        case 'job_application':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("applied to your job");
          break;

        case 'funding_withdrawal':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/funding/payments';
          $notification['message'] = __("Sent new funding withdrawal request");
          break;

        case 'funding_withdrawal_approved':
          $notification['url'] = $system['system_url'] . '/settings/funding';
          $notification['message'] = __("Approved your funding withdrawal request");
          break;

        case 'funding_withdrawal_declined':
          $notification['url'] = $system['system_url'] . '/settings/funding';
          $notification['message'] = __("Declined your funding withdrawal request");
          break;

        case 'funding_donation':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("donated to your funding request with") . " " . print_money($node_type);
          break;

        case 'monetization_withdrawal':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/monetization/payments';
          $notification['message'] = __("Sent new monetization withdrawal request");
          break;

        case 'monetization_withdrawal_approved':
          $notification['url'] = $system['system_url'] . '/settings/monetization';
          $notification['message'] = __("Approved your monetization withdrawal request");
          break;

        case 'monetization_withdrawal_declined':
          $notification['url'] = $system['system_url'] . '/settings/monetization';
          $notification['message'] = __("Declined your monetization withdrawal request");
          break;

        case 'ads_campaign_added':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/ads';
          $notification['message'] = __("Added new ads campaign");
          break;

        case 'ads_campaign_edited':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/ads';
          $notification['message'] = __("Edited ads campaign");
          break;

        case 'ads_campaign_approved':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/ads';
          $notification['message'] = __("Approved ads campaign");
          break;

        case 'ads_campaign_declined':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/ads';
          $notification['message'] = __("Declined your ads campaign");
          break;

        case 'report':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/reports';
          $notification['message'] = __("reported new content");
          break;

        case 'verification_request':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/verification';
          $notification['message'] = __("sent new verification request");
          break;
        case 'verification_request_approved':
          $notification['url'] = $system['system_url'] . '/settings/verification';
          $notification['message'] = __("Your verification request has been approved");
          break;
        case 'verification_request_declined':
          $notification['url'] = $system['system_url'] . '/settings/verification';
          $notification['message'] = __("Your verification request has been declined");
          break;
        case 'verification_request_page_approved':
          $notification['url'] = $system['system_url'] . '/pages/' . $node_url . '/settings/verification';
          $notification['message'] = __("Your page verification request has been approved");
          break;
        case 'verification_request_page_declined':
          $notification['url'] = $system['system_url'] . '/pages/' . $node_url . '/settings/verification';
          $notification['message'] = __("Your page verification request has been declined");
          break;
        case 'live_stream':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("is live now");
          break;

        case 'async_request':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'];
          $notification['message'] = $message;
          break;

        case 'video_converted':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("Your video has been converted");
          break;

        case 'page_review':
          $notification['url'] = $system['system_url'] . '/pages/' . $node_url;
          $notification['message'] = __("reviewed your page");
          break;

        case 'page_review_reply':
          $notification['url'] = $system['system_url'] . '/pages/' . $node_url;
          $notification['message'] = __("replied to your review");
          break;

        case 'group_review':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url;
          $notification['message'] = __("reviewed your group");
          break;

        case 'group_review_reply':
          $notification['url'] = $system['system_url'] . '/groups/' . $node_url;
          $notification['message'] = __("replied to your review");
          break;

        case 'event_review':
          $notification['url'] = $system['system_url'] . '/events/' . $node_url;
          $notification['message'] = __("reviewed your event");
          break;

        case 'event_review_reply':
          $notification['url'] = $system['system_url'] . '/events/' . $node_url;
          $notification['message'] = __("replied to your review");
          break;

        case 'post_review':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("reviewed your post");
          break;

        case 'post_review_reply':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("replied to your review");
          break;

        case 'pending_post':
          $notification['url'] = $system['system_url'] . '/' . $control_panel['url'] . '/posts/pending';
          $notification['message'] = __("sent new post for approval");
          break;

        case 'pending_post_approved':
          $notification['url'] = $system['system_url'] . '/posts/' . $node_url;
          $notification['message'] = __("Your post has been approved");
          break;

        case 'chat_message_paid':
          $notification['url'] = $system['system_url'] . '/messages';
          $notification['message'] = __("sent you a paid message");
          break;
      }
      /* prepare message */
      $notification['full_message'] = html_entity_decode($this->_data['name'], ENT_QUOTES) . " " . html_entity_decode($notification['message'], ENT_QUOTES);
      onesignal_notification($receiver['onesignal_user_id'], $notification);
    }
  }


  /**
   * post_notification_async
   * 
   * @param string $message
   * @return void
   */
  public function post_notification_async($message)
  {
    $this->post_notification(['to_user_id' => $this->_data['user_id'], 'action' => "async_request", 'message' => $message]);
  }


  /**
   * post_mass_notification
   * 
   * @param string $url
   * @param string $message
   * @return void
   */
  public function post_mass_notification($url, $message)
  {
    global $db, $date;
    /* insert notification */
    $db->query(sprintf("INSERT INTO notifications (to_user_id, from_user_id, action, node_type, node_url, message, time, seen) VALUES ('0', %s, 'mass_notification', 'notification', %s, %s, %s, '1')", secure($this->_data['user_id'], 'int'), secure($url), secure($message), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* update notifications counter +1 */
    $db->query(sprintf("UPDATE users SET user_live_notifications_counter = user_live_notifications_counter + 1")) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * delete_notification
   * 
   * @param integer $to_user_id
   * @param string $action
   * @param string $node_type
   * @param string $node_url
   * @return void
   */
  public function delete_notification($to_user_id, $action, $node_type = '', $node_url = '')
  {
    global $db;
    /* delete notification */
    $db->query(sprintf("DELETE FROM notifications WHERE to_user_id = %s AND from_user_id = %s AND action = %s AND node_type = %s AND node_url = %s", secure($to_user_id, 'int'), secure($this->_data['user_id'], 'int'), secure($action), secure($node_type), secure($node_url))) or _error('SQL_ERROR_THROWEN');
    /* update notifications counter -1 */
    $db->query(sprintf("UPDATE users SET user_live_notifications_counter = IF(user_live_notifications_counter=0,0,user_live_notifications_counter-1) WHERE user_id = %s", secure($to_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * notify_system_admins
   * 
   * @param string $action
   * @param boolean $notify_moderators
   * @return void
   */
  public function notify_system_admins($action, $notify_moderators = false)
  {
    global $db;
    /* get system admins */
    $where_query = ($notify_moderators) ? "user_group < 3" : "user_group = '1'";
    $get_system_admins = $db->query("SELECT user_id FROM users WHERE " . $where_query) or _error('SQL_ERROR_THROWEN');
    if ($get_system_admins->num_rows == 0) {
      return;
    }
    while ($system_admin = $get_system_admins->fetch_assoc()) {
      /* post notification */
      $this->post_notification(['to_user_id' => $system_admin['user_id'], 'action' => $action]);
    }
  }



  /* ------------------------------- */
  /* Chat */
  /* ------------------------------- */

  /**
   * user_online
   * 
   * @param integer $user_id
   * @return boolean
   */
  public function user_online($user_id)
  {
    global $db, $system;
    /* check if the target user is a friend to the viewer */
    if (!$this->friendship_approved($user_id)) {
      /* if no > return false */
      return false;
    }
    /* check if the target user is online & enable the chat */
    $get_user_status = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s AND user_chat_enabled = '1' AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($user_id, 'int'), secure($system['offline_time'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_user_status->fetch_assoc()['count'] == 0) {
      /* if no > return false */
      return false;
    }
    return true;
  }


  /**
   * get_online_friends
   * 
   * @return array
   */
  public function get_online_friends()
  {
    global $db, $system;
    /* get online friends */
    $online_friends = [];
    $get_online_friends = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s)) AND user_chat_enabled = '1' AND user_id IN (%s)", secure($system['offline_time'], 'int', false), $this->spread_ids($this->_data['friends_ids']))) or _error('SQL_ERROR_THROWEN');
    if ($get_online_friends->num_rows > 0) {
      while ($online_friend = $get_online_friends->fetch_assoc()) {
        $online_friend['user_picture'] = get_picture($online_friend['user_picture'], $online_friend['user_gender']);
        $online_friend['user_is_online'] = '1';
        $online_friends[] = $online_friend;
      }
    }
    return $online_friends;
  }


  /**
   * get_offline_friends
   * 
   * @return array
   */
  public function get_offline_friends()
  {
    global $db, $system;
    $offline_friends = [];
    /* make a list from viewer's friends */
    $get_offline_friends = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified, user_last_seen FROM users WHERE user_last_seen < SUBTIME(NOW(), SEC_TO_TIME(%s)) AND user_id IN (%s) ORDER BY user_last_seen DESC LIMIT 25", secure($system['offline_time'], 'int', false), $this->spread_ids($this->_data['friends_ids']))) or _error('SQL_ERROR_THROWEN');
    if ($get_offline_friends->num_rows > 0) {
      while ($offline_friend = $get_offline_friends->fetch_assoc()) {
        $offline_friend['user_picture'] = get_picture($offline_friend['user_picture'], $offline_friend['user_gender']);
        $offline_friend['user_is_online'] = '0';
        $offline_friends[] = $offline_friend;
      }
    }
    return $offline_friends;
  }


  /**
   * get_conversations_new
   * 
   * @return array
   */
  public function get_conversations_new()
  {
    global $db;
    $conversations = [];
    if (!empty($_SESSION['chat_boxes_opened'])) {
      /* make list from opened chat boxes keys (conversations ids) */
      $chat_boxes_opened_list = $this->spread_ids($_SESSION['chat_boxes_opened']);
      $get_conversations = $db->query(sprintf("SELECT conversations.conversation_id FROM conversations INNER JOIN conversations_users ON conversations.conversation_id = conversations_users.conversation_id WHERE conversations.node_id IS NULL AND conversations_users.user_id = %s AND conversations_users.seen = '0' AND conversations_users.deleted = '0' AND conversations.conversation_id NOT IN (%s)", secure($this->_data['user_id'], 'int'), $chat_boxes_opened_list)) or _error('SQL_ERROR_THROWEN');
    } else {
      $get_conversations = $db->query(sprintf("SELECT conversations.conversation_id FROM conversations INNER JOIN conversations_users ON conversations.conversation_id = conversations_users.conversation_id WHERE conversations.node_id IS NULL AND conversations_users.user_id = %s AND conversations_users.seen = '0' AND conversations_users.deleted = '0'", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_conversations->num_rows > 0) {
      while ($conversation = $get_conversations->fetch_assoc()) {
        $db->query(sprintf("UPDATE conversations_users SET seen = '1' WHERE conversation_id = %s AND user_id = %s", secure($conversation['conversation_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        $conversations[] = $this->get_conversation($conversation['conversation_id']);
      }
    }
    return $conversations;
  }


  /**
   * get_conversations
   * 
   * @param integer $offset
   * @return array
   */
  public function get_conversations($offset = 0)
  {
    global $db, $system;
    $conversations = [];
    $offset *= $system['max_results'];
    $get_conversations = $db->query(sprintf("SELECT conversations.conversation_id FROM conversations INNER JOIN conversations_users ON conversations.conversation_id = conversations_users.conversation_id WHERE conversations_users.deleted = '0' AND conversations_users.user_id = %s ORDER BY conversations.last_message_id DESC LIMIT %s, %s", secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_conversations->num_rows > 0) {
      while ($conversation = $get_conversations->fetch_assoc()) {
        $conversation = $this->get_conversation($conversation['conversation_id']);
        if ($conversation) {
          $conversations[] = $conversation;
        }
      }
    }
    return $conversations;
  }


  /**
   * get_conversation
   * 
   * @param integer $conversation_id
   * @return array
   */
  public function get_conversation($conversation_id)
  {
    global $db, $system;
    $conversation = [];
    $get_conversation = $db->query(sprintf("SELECT conversations.*, conversations_messages.message, conversations_messages.image, conversations_messages.voice_note, conversations_messages.time, conversations_users.seen FROM conversations LEFT JOIN conversations_messages ON conversations.last_message_id = conversations_messages.message_id INNER JOIN conversations_users ON conversations.conversation_id = conversations_users.conversation_id WHERE conversations_users.user_id = %s AND conversations.conversation_id = %s", secure($this->_data['user_id'], 'int'), secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_conversation->num_rows == 0) {
      return false;
    }
    $conversation = $get_conversation->fetch_assoc();
    /* get recipients */
    $get_recipients = $db->query(sprintf("SELECT conversations_users.seen, conversations_users.deleted, conversations_users.typing, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified, users.user_monetization_enabled, users.user_monetization_chat_price, users.user_monetization_call_price FROM conversations_users INNER JOIN users ON conversations_users.user_id = users.user_id WHERE conversations_users.conversation_id = %s AND conversations_users.user_id != %s", secure($conversation['conversation_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    $recipients_num = $get_recipients->num_rows;
    if (!$conversation['node_id'] && $recipients_num == 0) {
      return false;
    }
    $i = 1;
    $conversation['chat_price'] = 0;
    $conversation['call_price'] = 0;
    $conversation['paid_recipients'] = [];
    while ($recipient = $get_recipients->fetch_assoc()) {
      /* check if paid chat */
      if ($system['monetization_enabled'] && $this->check_user_permission($recipient['user_id'], 'monetization_permission') && $recipient['user_monetization_enabled'] && ($recipient['user_monetization_chat_price'] > 0 || $recipient['user_monetization_call_price'] > 0)) {
        $conversation['chat_price'] += $recipient['user_monetization_chat_price'];
        $conversation['call_price'] += $recipient['user_monetization_call_price'];
        $conversation['paid_recipients'][] = $recipient;
      }
      /* get recipient picture */
      $recipient['user_picture'] = get_picture($recipient['user_picture'], $recipient['user_gender']);
      /* add to conversation recipients */
      $conversation['recipients'][] = $recipient;
      /* prepare typing recipients */
      if ($system['chat_typing_enabled'] && $recipient['typing']) {
        /* check if recipient typing but offline */
        $get_recipient_status = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($recipient['user_id'], 'int'), secure($system['offline_time'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_recipient_status->fetch_assoc()['count'] == 0) {
          /* recipient offline -> remove his typing status */
          $db->query(sprintf("UPDATE conversations_users SET typing = '0' WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($recipient['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* recipient online -> return his typing status */
          if ($conversation['typing_name_list']) {
            $conversation['typing_name_list'] .= ", ";
          }
          $conversation['typing_name_list'] .= html_entity_decode(($system['show_usernames_enabled']) ? $recipient['user_name'] : $recipient['user_firstname'], ENT_QUOTES);
        }
      }
      /* prepare seen recipients */
      if ($system['chat_seen_enabled'] && $recipient['seen']) {
        if ($conversation['seen_name_list']) {
          $conversation['seen_name_list'] .= ", ";
        }
        $conversation['seen_name_list'] .= html_entity_decode(($system['show_usernames_enabled']) ? $recipient['user_name'] : $recipient['user_firstname'], ENT_QUOTES);
      }
      /* prepare conversation name_list */
      $conversation['name_list'] .= html_entity_decode(($system['show_usernames_enabled']) ? $recipient['user_name'] : $recipient['user_firstname'], ENT_QUOTES);
      if ($i < $recipients_num) {
        $conversation['name_list'] .= ", ";
      }
      $i++;
    }
    /* prepare conversation with multiple_recipients */
    switch ($conversation['node_type']) {
      case 'group':
        /* get group */
        $group = $this->get_group($conversation['node_id']);
        $conversation['multiple_recipients'] = true;
        $conversation['link'] = 'groups/' . $group['group_name'];
        $conversation['picture'] = get_picture($group['group_picture'], 'group');
        $conversation['name'] = __("Group") . ": " . $group['group_title'];
        break;

      case 'event':
        /* get event */
        $event = $this->get_event($conversation['node_id']);
        $conversation['multiple_recipients'] = true;
        $conversation['link'] = 'events/' . $conversation['node_id'];
        $conversation['picture'] = get_picture($group['event_cover'], 'event');
        $conversation['name'] = __("Event") . ": " . $event['event_title'];
        break;

      default:
        if ($recipients_num > 1) {
          /* multiple recipients */
          $conversation['multiple_recipients'] = true;
          $conversation['link'] = $conversation['conversation_id'];
          $conversation['picture_left'] = $conversation['recipients'][0]['user_picture'];
          $conversation['picture_right'] = $conversation['recipients'][1]['user_picture'];
          if ($recipients_num > 2) {
            $conversation['name'] = html_entity_decode(($system['show_usernames_enabled']) ? $conversation['recipients'][0]['user_name'] : $conversation['recipients'][0]['user_firstname'], ENT_QUOTES) . ", " . html_entity_decode(($system['show_usernames_enabled']) ? $conversation['recipients'][1]['user_name'] : $conversation['recipients'][1]['user_firstname'], ENT_QUOTES) . " & " . ($recipients_num - 2) . " " . __("more");
          } else {
            $conversation['name'] = html_entity_decode(($system['show_usernames_enabled']) ? $conversation['recipients'][0]['user_name'] : $conversation['recipients'][0]['user_firstname'], ENT_QUOTES) . " & " . html_entity_decode(($system['show_usernames_enabled']) ? $conversation['recipients'][1]['user_name'] : $conversation['recipients'][1]['user_firstname'], ENT_QUOTES);
          }
        } else {
          /* one recipient */
          $conversation['multiple_recipients'] = false;
          $conversation['user_id'] = $conversation['recipients'][0]['user_id'];
          $conversation['link'] = $conversation['recipients'][0]['user_name'];
          $conversation['picture'] = $conversation['recipients'][0]['user_picture'];
          $conversation['name'] = html_entity_decode(($system['show_usernames_enabled']) ? $conversation['recipients'][0]['user_name'] : $conversation['recipients'][0]['user_firstname'] . " " . $conversation['recipients'][0]['user_lastname'], ENT_QUOTES);
          $conversation['name_html'] = popover($conversation['recipients'][0]['user_id'], $conversation['recipients'][0]['user_name'], ($system['show_usernames_enabled']) ? $conversation['recipients'][0]['user_name'] : $conversation['recipients'][0]['user_firstname'] . " " . $conversation['recipients'][0]['user_lastname']);
        }
        break;
    }
    /* get total number of messages */
    $conversation['total_messages'] = $this->get_conversation_total_messages($conversation_id);
    /* decode message text */
    $conversation['message_orginal'] = $this->decode_emojis($conversation['message']);
    $conversation['message'] = $this->_parse(["text" => $conversation['message'], "decode_mention" => false, "decode_hashtags" => false]);
    /* return */
    return $conversation;
  }


  /**
   * get_mutual_conversation_id
   * 
   * @param array $recipients
   * @param boolean $check_deleted
   * @return integer
   */
  public function get_mutual_conversation_id($recipients, $check_deleted = false)
  {
    global $db;
    $recipients_array = (array)$recipients;
    $recipients_array = array_filter($recipients_array, [$this, "filter_ids"]);
    if (!$recipients_array) {
      _error(400);
    }
    $recipients_array[] = $this->_data['user_id'];
    $recipients_list = $this->spread_ids($recipients_array);
    $get_mutual_conversations = $db->query(sprintf('SELECT conversations.conversation_id FROM conversations_users INNER JOIN conversations ON conversations_users.conversation_id = conversations.conversation_id WHERE conversations.node_id IS NULL AND conversations_users.user_id IN (%s) GROUP BY conversations.conversation_id HAVING COUNT(conversations.conversation_id) = %s', $recipients_list, count($recipients_array))) or _error('SQL_ERROR_THROWEN');
    if ($get_mutual_conversations->num_rows == 0) {
      return false;
    }
    while ($mutual_conversation = $get_mutual_conversations->fetch_assoc()) {
      /* get recipients */
      $where_statement = ($check_deleted) ? " AND deleted != '1' " : "";
      $get_recipients = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = %s" . $where_statement, secure($mutual_conversation['conversation_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_recipients->fetch_assoc()['count'] == count($recipients_array)) {
        return $mutual_conversation['conversation_id'];
      }
    }
  }


  /**
   * get_conversation_total_messages
   * 
   * @param integer $conversation_id
   * @return integer
   */
  public function get_conversation_total_messages($conversation_id)
  {
    global $db;
    $get_total_messages = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_messages WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_total_messages->fetch_assoc()['count'];
  }


  /**
   * get_conversation_messages
   * 
   * @param integer $conversation_id
   * @param integer $offset
   * @param integer $last_message_id
   * @return array
   */
  public function get_conversation_messages($conversation_id, $offset = 0, $last_message_id = null)
  {
    global $db, $system;
    /* check if user authorized to see this conversation */
    $check_conversation = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_conversation->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You are not authorized to view this"));
    }
    $offset *= $system['max_results'];
    $messages = [];
    if ($last_message_id !== null) {
      /* get all messages after the last_message_id */
      $get_messages = $db->query(sprintf("SELECT conversations_messages.message_id, conversations_messages.message, conversations_messages.image, conversations_messages.voice_note, conversations_messages.time, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM conversations_messages INNER JOIN users ON conversations_messages.user_id = users.user_id WHERE conversations_messages.conversation_id = %s AND conversations_messages.message_id > %s", secure($conversation_id, 'int'), secure($last_message_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      $get_messages = $db->query(sprintf("SELECT * FROM ( SELECT conversations_messages.message_id, conversations_messages.message, conversations_messages.image, conversations_messages.voice_note, conversations_messages.time, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM conversations_messages INNER JOIN users ON conversations_messages.user_id = users.user_id WHERE conversations_messages.conversation_id = %s ORDER BY conversations_messages.message_id DESC LIMIT %s,%s ) messages ORDER BY messages.message_id ASC", secure($conversation_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    }
    while ($message = $get_messages->fetch_assoc()) {
      $message['user_picture'] = get_picture($message['user_picture'], $message['user_gender']);
      $message['message'] = $this->_parse(["text" => $message['message'], "decode_mentions" => false, "decode_hashtags" => false]);
      /* return */
      $messages[] = $message;
    }
    return $messages;
  }


  /**
   * post_conversation_message
   * 
   * @param string $message
   * @param string $image
   * @param string $voice_note
   * @param integer $conversation_id
   * @param array $recipients
   * @return array
   */
  public function post_conversation_message($message, $image, $voice_note, $conversation_id = null, $recipients = null)
  {
    global $db, $system, $date;
    /* check if posting the message to (new || existed) conversation */
    if ($conversation_id == null) {
      /* [first] check previous conversation between (viewer & recipients) */
      $mutual_conversation = $this->get_mutual_conversation_id($recipients);
      if (!$mutual_conversation) {
        /* [1] there is no conversation between viewer and the recipients -> start new one */
        /* check paid conversation */
        $chat_price = 0;
        $paid_recipients = [];
        foreach ($recipients as $recipient) {
          if ($system['monetization_enabled'] && $this->check_user_permission($recipient, 'monetization_permission')) {
            $recipient_info = $this->get_user($recipient);
            if ($recipient_info['user_monetization_enabled'] && $recipient_info['user_monetization_chat_price'] > 0) {
              $chat_price += $recipient_info['user_monetization_chat_price'];
              $paid_recipients[] = $recipient_info;
            }
          }
        }
        if ($chat_price > 0) {
          $this->wallet_chat_payment($chat_price, $paid_recipients);
        }
        /* insert conversation */
        $db->query("INSERT INTO conversations (last_message_id) VALUES ('0')") or _error('SQL_ERROR_THROWEN');
        $conversation_id = $db->insert_id;
        /* insert the sender (viewer) */
        $db->query(sprintf("INSERT INTO conversations_users (conversation_id, user_id, seen) VALUES (%s, %s, '1')", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* insert recipients */
        foreach ($recipients as $recipient) {
          $db->query(sprintf("INSERT INTO conversations_users (conversation_id, user_id) VALUES (%s, %s)", secure($conversation_id, 'int'), secure($recipient, 'int'))) or _error('SQL_ERROR_THROWEN');
        }
      } else {
        /* [2] there is a conversation between the viewer and the recipients */
        /* set the conversation_id */
        $conversation_id = $mutual_conversation;
        /* get conversation */
        $conversation = $this->get_conversation($conversation_id);
        /* check paid conversation */
        if (!$conversation['node_id'] && $conversation['chat_price'] > 0) {
          $this->wallet_chat_payment($conversation['chat_price'], $conversation['paid_recipients']);
        }
      }
    } else {
      /* [3] post the message to -> existed conversation */
      /* check if user authorized */
      $conversation = $this->get_conversation($conversation_id);
      if (!$conversation) {
        throw new Exception(__("You are not authorized to do this"));
      }
      /* check paid conversation */
      if (!$conversation['node_id'] && $conversation['chat_price'] > 0) {
        $this->wallet_chat_payment($conversation['chat_price'], $conversation['paid_recipients']);
      }
      /* check if it's a chatbox */
      if ($conversation['node_id']) {
        switch ($conversation['node_type']) {
          case 'group':
            /* check user membership */
            $membership = $this->check_group_membership($this->_data['user_id'], $conversation['node_id']);
            if ($membership != "approved") {
              throw new Exception(__("You are not authorized to do this"));
            }
            break;

          case 'event':
            /* check user membership */
            $membership = $this->check_event_membership($this->_data['user_id'], $conversation['node_id']);
            if (!$membership) {
              throw new Exception(__("You are not authorized to do this"));
            }
            break;
        }
      }
      foreach ($conversation['recipients'] as $recipient) {
        /* check if the viewer is blocked by any of the recipients but not in a chatbox */
        if (!$conversation['node_id'] && $this->blocked($recipient['user_id'])) {
          $recipient_name = ($system['show_usernames_enabled']) ? $recipient['user_name'] : $recipient['user_firstname'] . " " . $recipient['user_lastname'];
          modal("MESSAGE", __("Message"), __("You aren't allowed to message") . " " . $recipient_name);
        }
      }
      /* update sender (viewer) as seen and not deleted if any */
      $db->query(sprintf("UPDATE conversations_users SET seen = '1', deleted = '0' WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update recipients as not seen and not deleted if any (if not chatbox) */
      if (!$conversation['node_id']) {
        $db->query(sprintf("UPDATE conversations_users SET seen = '0', deleted = '0' WHERE conversation_id = %s AND user_id != %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
    }
    /* insert message */
    $image = ($image != '') ? $image : '';
    $voice_note = ($voice_note != '') ? $voice_note : '';
    $db->query(sprintf("INSERT INTO conversations_messages (conversation_id, user_id, message, image, voice_note, time) VALUES (%s, %s, %s, %s, %s, %s)", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'), secure($message), secure($image), secure($voice_note), secure($date))) or _error('SQL_ERROR_THROWEN');
    $message_id = $db->insert_id;
    /* update the conversation with last message id */
    $db->query(sprintf("UPDATE conversations SET last_message_id = %s WHERE conversation_id = %s", secure($message_id, 'int'), secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update sender (viewer) with last message id */
    $db->query(sprintf("UPDATE users SET user_live_messages_lastid = %s WHERE user_id = %s", secure($message_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* get conversation */
    $conversation = $this->get_conversation($conversation_id);
    /* update all recipients with last message id & only offline recipient messages counter */
    foreach ($conversation['recipients'] as $recipient) {
      if (!$recipient['deleted']) {
        $db->query(sprintf("UPDATE users SET user_live_messages_lastid = %s, user_live_messages_counter = user_live_messages_counter + 1 WHERE user_id = %s", secure($message_id, 'int'), secure($recipient['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
    }
    /* update typing status of the viewer for this conversation */
    $is_typing = '0';
    $db->query(sprintf("UPDATE conversations_users SET typing = %s WHERE conversation_id = %s AND user_id = %s", secure($is_typing), secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* return with conversation */
    return $conversation;
  }


  /**
   * delete_conversation
   * 
   * @param integer $conversation_id
   * @return void
   */
  public function delete_conversation($conversation_id)
  {
    global $db, $system;
    /* check if user authorized */
    $check_conversation = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users INNER JOIN conversations ON conversations_users.conversation_id = conversations.conversation_id WHERE conversations.node_id IS NULL AND conversations.conversation_id = %s AND conversations_users.user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_conversation->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You are not authorized to do this"));
    }
    if ($system['chat_permanently_delete_enabled']) {
      /* delete conversation */
      $db->query(sprintf("DELETE FROM conversations WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete conversation users */
      $db->query(sprintf("DELETE FROM conversations_users WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete conversation messages */
      $db->query(sprintf("DELETE FROM conversations_messages WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      /* update typing status of the viewer for this conversation */
      $is_typing = '0';
      $db->query(sprintf("UPDATE conversations_users SET typing = %s WHERE conversation_id = %s AND user_id = %s", secure($is_typing), secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update conversation as deleted */
      $db->query(sprintf("UPDATE conversations_users SET deleted = '1' WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * leave_conversation
   * 
   * @param integer $conversation_id
   * @return void
   */
  public function leave_conversation($conversation_id)
  {
    global $db;
    /* check if user authorized */
    $check_conversation = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_conversation->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You are not authorized to do this"));
    }
    /* update typing status of the viewer for this conversation */
    $is_typing = '0';
    $db->query(sprintf("UPDATE conversations_users SET typing = %s WHERE conversation_id = %s AND user_id = %s", secure($is_typing), secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update conversation as deleted */
    $db->query(sprintf("UPDATE conversations_users SET deleted = '1' WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * get_conversation_color
   * 
   * @param integer $conversation_id
   * @return string
   */
  public function get_conversation_color($conversation_id)
  {
    global $db;
    $get_conversation = $db->query(sprintf("SELECT color FROM conversations WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_conversation->fetch_assoc()['color'];
  }


  /**
   * set_conversation_color
   * 
   * @param integer $conversation_id
   * @param string $color
   * @return void
   */
  public function set_conversation_color($conversation_id, $color)
  {
    global $db;
    /* check if user authorized */
    $check_conversation = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_conversation->fetch_assoc()['count'] == 0) {
      _error(403);
    }
    $db->query(sprintf("UPDATE conversations SET color = %s WHERE conversation_id = %s", secure($color), secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * update_conversation_typing_status
   * 
   * @param integer $conversation_id
   * @param boolean $is_typing
   * @return void
   */
  public function update_conversation_typing_status($conversation_id, $is_typing)
  {
    global $db;
    /* check if user authorized */
    $check_conversation = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_conversation->fetch_assoc()['count'] == 0) {
      return;
    }
    /* update typing status of the viewer for this conversation */
    $is_typing = ($is_typing) ? '1' : '0';
    $db->query(sprintf("UPDATE conversations_users SET typing = %s WHERE conversation_id = %s AND user_id = %s", secure($is_typing), secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * update_conversation_seen_status
   * 
   * @param array $conversation_ids
   * @return void
   */
  public function update_conversation_seen_status($conversation_ids)
  {
    global $db;
    $conversations_array = [];
    /* check if user authorized */
    foreach ((array)$conversation_ids as $conversation_id) {
      $check_conversation = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check_conversation->fetch_assoc()['count'] > 0) {
        $conversations_array[] = $conversation_id;
      }
    }
    if (!$conversations_array) return;
    $conversations_list = $this->spread_ids($conversations_array);
    /* update seen status of the viewer to these conversation(s) */
    $db->query(sprintf("UPDATE conversations_users SET seen = '1' WHERE conversation_id IN (%s) AND user_id = %s", $conversations_list, secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * create_chatbox
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return integer
   */
  public function create_chatbox($node_id, $node_type)
  {
    global $db, $system, $date;
    /* insert conversation */
    $db->query(sprintf("INSERT INTO conversations (last_message_id, node_id, node_type) VALUES ('0', %s, %s)", secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    $conversation_id = $db->insert_id;
    /* insert the node admin (viewer) */
    $db->query(sprintf("INSERT INTO conversations_users (conversation_id, user_id, seen) VALUES (%s, %s, '1')", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* return conversation_id */
    return $conversation_id;
  }


  /**
   * get_chatbox
   * 
   * @param integer $conversation_id
   * @return array
   */
  public function get_chatbox($conversation_id)
  {
    global $db;
    /* check if the viewer is already in chatbox */
    $check_conversations_users = $db->query(sprintf("SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = %s AND user_id = %s", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_conversations_users->fetch_assoc()['count'] == 0) {
      /* insert the viewer */
      $db->query(sprintf("INSERT INTO conversations_users (conversation_id, user_id, seen) VALUES (%s, %s, '1')", secure($conversation_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* get conversation */
    $conversation = $this->get_conversation($conversation_id);
    /* get conversation messages */
    $conversation['messages'] = $this->get_conversation_messages($conversation_id);
    /* return */
    return $conversation;
  }


  /**
   * delete_chatbox
   * 
   * @param integer $conversation_id
   * @return void
   */
  public function delete_chatbox($conversation_id)
  {
    global $db;
    /* delete conversation */
    $db->query(sprintf("DELETE FROM conversations WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete conversation users */
    $db->query(sprintf("DELETE FROM conversations_users WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete conversation messages */
    $db->query(sprintf("DELETE FROM conversations_messages WHERE conversation_id = %s", secure($conversation_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Video/Audio Chat */
  /* ------------------------------- */

  /**
   * create_call
   * 
   * @param string $type
   * @param integer $to_user_id
   * @return integer|false
   */
  public function create_call($type, $to_user_id)
  {
    global $db, $system, $date;
    /* check call type */
    switch ($type) {
      case 'audio':
        $table = "conversations_calls_audio";
        /* check if audio call enabled */
        if (!$system['audio_call_enabled']) {
          throw new Exception(__("The audio call feature has been disabled by the admin"));
        }
        /* check audio call permission */
        if (!$this->_data['can_start_audio_call']) {
          throw new Exception(__("You don't have the permission to do this"));
        }
        break;

      case 'video':
        $table = "conversations_calls_video";
        /* check if video call enabled */
        if (!$system['video_call_enabled']) {
          throw new Exception(__("The video call feature has been disabled by the admin"));
        }
        /* check video call permission */
        if (!$this->_data['can_start_video_call']) {
          throw new Exception(__("You don't have the permission to do this"));
        }
        break;

      default:
        _error(400);
        break;
    }
    /* check if target user exist */
    $check_target_user = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s", secure($to_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_target_user->fetch_assoc()['count'] == 0) {
      return false;
    }
    /* check blocking */
    if ($this->blocked($to_user_id)) {
      return false;
    }
    /* check if target user offline */
    $check_target_busy_offline = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($to_user_id, 'int'), secure($system['offline_time'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($check_target_busy_offline->fetch_assoc()['count'] == 0) {
      return "recipient_offline";
    }
    /* check if target user busy (someone calling him || he calling someone || in a call) (audio|video) */
    $check_target_busy_audio = $db->query(sprintf('SELECT COUNT(*) as count FROM conversations_calls_audio WHERE (from_user_id = %1$s OR to_user_id = %1$s) AND declined = "0" AND updated_time >= SUBTIME(NOW(), SEC_TO_TIME(40))', secure($to_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_target_busy_audio->fetch_assoc()['count'] > 0) {
      return "recipient_busy";
    }
    $check_target_busy_video = $db->query(sprintf('SELECT COUNT(*) as count FROM conversations_calls_video WHERE (from_user_id = %1$s OR to_user_id = %1$s) AND declined = "0" AND updated_time >= SUBTIME(NOW(), SEC_TO_TIME(40))', secure($to_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_target_busy_video->fetch_assoc()['count'] > 0) {
      return "recipient_busy";
    }
    /* check if the viewer busy (someone calling him || he calling someone || in a call) (audio|video) */
    $check_viewer_busy_audio = $db->query(sprintf('SELECT COUNT(*) as count FROM conversations_calls_audio WHERE (from_user_id = %1$s OR to_user_id = %1$s) AND declined = "0" AND updated_time >= SUBTIME(NOW(), SEC_TO_TIME(40))', secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_viewer_busy_audio->fetch_assoc()['count'] > 0) {
      return "caller_busy";
    }
    $check_viewer_busy_video = $db->query(sprintf('SELECT COUNT(*) as count FROM conversations_calls_audio WHERE (from_user_id = %1$s OR to_user_id = %1$s) AND declined = "0" AND updated_time >= SUBTIME(NOW(), SEC_TO_TIME(40))', secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_viewer_busy_video->fetch_assoc()['count'] > 0) {
      return "caller_busy";
    }
    /* check paid call */
    if ($system['monetization_enabled'] && $this->check_user_permission($to_user_id, 'monetization_permission')) {
      $to_user_info = $this->get_user($to_user_id);
      if ($to_user_info['user_monetization_enabled'] && $to_user_info['user_monetization_call_price'] > 0) {
        $call_price = $to_user_info['user_monetization_call_price'];
        /* check if the viewer has enough balance */
        if ($this->_data['user_wallet_balance'] < $call_price) {
          throw new Exception(__("There is no enough credit in your wallet, Recharge your wallet to continue"));
        }
      }
    }
    /* create a new room */
    $room = get_hash_token();
    // caller
    $caller_id = substr(get_hash_token(), 0, 15);
    $caller_token = new Twilio\Jwt\AccessToken($system['twilio_sid'], $system['twilio_apisid'], $system['twilio_apisecret'], 3600, $caller_id);
    $caller_grant = new Twilio\Jwt\Grants\VideoGrant();
    $caller_grant->setRoom($room);
    $caller_token->addGrant($caller_grant);
    $caller_token_serialized = $caller_token->toJWT();
    /* -- */
    // receiver
    $receiver_id = substr(get_hash_token(), 0, 15);
    $receiver_token = new Twilio\Jwt\AccessToken($system['twilio_sid'], $system['twilio_apisid'], $system['twilio_apisecret'], 3600, $receiver_id);
    $receiver_grant = new Twilio\Jwt\Grants\VideoGrant();
    $receiver_grant->setRoom($room);
    $receiver_token->addGrant($receiver_grant);
    $receiver_token_serialized = $receiver_token->toJWT();
    /* -- */
    /* insert the new call */
    $db->query(sprintf("INSERT INTO %s (from_user_id, from_user_token, to_user_id, to_user_token, room, created_time, updated_time) VALUES (%s, %s, %s, %s, %s, %s, %s)", $table, secure($this->_data['user_id'], 'int'), secure($caller_token_serialized), secure($to_user_id, 'int'), secure($receiver_token_serialized), secure($room), secure($date), secure($date))) or _error('SQL_ERROR_THROWEN');
    return $db->insert_id;
  }


  /**
   * check_new_calls
   * 
   * @param string $type
   * @return array
   */
  public function check_new_calls($type)
  {
    global $db, $system;
    /* check call type */
    switch ($type) {
      case 'video':
        $table = "conversations_calls_video";
        break;

      case 'audio':
        $table = "conversations_calls_audio";
        break;

      default:
        _error(400);
        break;
    }
    /* new call -> [call created from less than 40 seconds and not answered nor declined] */
    $get_new_calls = $db->query(sprintf('SELECT %1$s.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM %1$s INNER JOIN users ON %1$s.from_user_id = users.user_id WHERE to_user_id = %2$s AND created_time >= SUBTIME(NOW(), SEC_TO_TIME(40)) AND answered = "0" AND declined = "0"', $table, secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_new_calls->num_rows == 0) {
      return false;
    }
    $call = $get_new_calls->fetch_assoc();
    $call['caller_name'] = html_entity_decode(($system['show_usernames_enabled']) ? $call['user_name'] : $call['user_firstname'] . " " . $call['user_lastname'], ENT_QUOTES);
    $call['caller_picture'] = get_picture($call['user_picture'], $call['user_gender']);
    return $call;
  }


  /**
   * check_calling_response
   * 
   * @param string $type
   * @param integer $call_id
   * @return void
   */
  public function check_calling_response($type, $call_id)
  {
    global $db, $date;
    /* check call type */
    switch ($type) {
      case 'video':
        $table = "conversations_calls_video";
        break;

      case 'audio':
        $table = "conversations_calls_audio";
        break;

      default:
        _error(400);
        break;
    }
    /* check if user authorized */
    $check_call = $db->query(sprintf("SELECT * FROM %s WHERE call_id = %s AND from_user_id = %s", $table, secure($call_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_call->num_rows == 0) {
      _error(403);
    }
    $call = $check_call->fetch_assoc();
    /* update the call */
    $db->query(sprintf("UPDATE %s SET updated_time = %s WHERE call_id = %s", $table, secure($date), secure($call_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* return */
    if ($call['declined']) {
      return "declined";
    } else if ($call['answered']) {
      return $call;
    } else {
      return "no_answer";
    }
  }


  /**
   * answer_call
   * 
   * @param string $type
   * @param integer $call_id
   * @return void
   */
  public function answer_call($type, $call_id)
  {
    global $db, $system, $date;
    /* check call type */
    switch ($type) {
      case 'video':
        $table = "conversations_calls_video";
        break;

      case 'audio':
        $table = "conversations_calls_audio";
        break;

      default:
        _error(400);
        break;
    }
    /* check if user authorized */
    $check_call = $db->query(sprintf("SELECT * FROM %s WHERE call_id = %s AND to_user_id = %s", $table, secure($call_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_call->num_rows == 0) {
      _error(403);
    }
    $call = $check_call->fetch_assoc();
    /* check paid call */
    if ($this->_data['can_monetize_content'] && $this->_data['user_monetization_enabled'] && $this->_data['user_monetization_call_price'] > 0) {
      $this->wallet_call_payment($this->_data['user_monetization_call_price'], $call['from_user_id'], $call['to_user_id']);
    }
    /* update the call */
    $db->query(sprintf("UPDATE %s SET answered = '1', updated_time = %s WHERE call_id = %s", $table, secure($date), secure($call_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* return */
    return $call;
  }


  /**
   * decline_call
   * 
   * @param string $type
   * @param integer $call_id
   * @return void
   */
  public function decline_call($type, $call_id)
  {
    global $db, $date;
    /* check call type */
    switch ($type) {
      case 'video':
        $table = "conversations_calls_video";
        break;

      case 'audio':
        $table = "conversations_calls_audio";
        break;

      default:
        _error(400);
        break;
    }
    /* check if user authorized */
    $check_call = $db->query(sprintf('SELECT COUNT(*) as count FROM  %1$s WHERE call_id =  %2$s AND (from_user_id = %3$s OR to_user_id = %3$s)', $table, secure($call_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_call->fetch_assoc()['count'] == 0) {
      _error(403);
    }
    /* update the call */
    $db->query(sprintf("UPDATE %s SET declined = '1', updated_time = %s WHERE call_id = %s", $table, secure($date), secure($call_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * update_call
   * 
   * @param string $type
   * @param integer $call_id
   * @return void
   */
  public function update_call($type, $call_id)
  {
    global $db, $date;
    /* check call type */
    switch ($type) {
      case 'video':
        $table = "conversations_calls_video";
        break;

      case 'audio':
        $table = "conversations_calls_audio";
        break;

      default:
        _error(400);
        break;
    }
    /* check if user authorized */
    $check_call = $db->query(sprintf('SELECT COUNT(*) as count FROM  %1$s WHERE call_id =  %2$s AND (from_user_id = %3$s OR to_user_id = %3$s)', $table, secure($call_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_call->fetch_assoc()['count'] == 0) {
      return; /* not 403 error returned as error will not be handled */
    }
    /* update the call */
    $db->query(sprintf("UPDATE %s SET updated_time = %s WHERE call_id = %s", $table, secure($date), secure($call_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Live Stream */
  /* ------------------------------- */

  /**
   * create_live_post
   * 
   * @param string $agora_channel_name
   * @param string $video_thumbnail
   * @param string $node
   * @param integer $node_id
   * @param boolean $tips_enabled
   * @param boolean $for_subscriptions
   * @return integer
   */
  public function create_live_post($agora_channel_name, $video_thumbnail, $node = null, $node_id = null, $tips_enabled = false, $for_subscriptions = false)
  {
    global $db, $system, $date;
    /* check if live enabled */
    if (!$system['live_enabled']) {
      throw new Exception(__("The live feature has been disabled by the admin"));
    }
    /* check live permission */
    if (!$this->_data['can_go_live']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get agora recorder (uid|token) */
    $agora_recorder = $this->agora_token_builder(false, $agora_channel_name);
    $agora_uid = $agora_recorder['uid'];
    $agora_token = $agora_recorder['token'];
    /* prepare live video thumbnail */
    $video_thumbnail = ($video_thumbnail != 'data:,') ? $video_thumbnail : "https://dummyimage.com/16:9x1080/000000/ffffff/?text=Live+Video";
    $video_thumbnail = save_picture_from_url($video_thumbnail);
    /* prepare the post */
    $post['user_id'] = $this->_data['user_id'];
    $post['user_type'] = "user";
    $post['privacy'] = 'public';
    $post['in_group'] = 0;
    $post['group_id'] = null;
    $post['group_approved'] = 0;
    $post['in_event'] = 0;
    $post['event_id'] = null;
    $post['event_approved'] = 0;
    $post['tips_enabled'] = ($node != 'page' && $this->_data['can_receive_tip'] && $tips_enabled == "true") ? '1' : '0';
    $post['for_subscriptions'] = ($for_subscriptions == "true") ? '1' : '0';
    /* check if node is set */
    if (!$node) {
      /* check monetization permission */
      if ($post['for_subscriptions']) {
        /* check if user has monetization enabled && subscriptions plans */
        if (!($this->_data['can_monetize_content'] && $this->_data['user_monetization_enabled'] && $this->_data['user_monetization_plans'] > 0)) {
          modal("ERROR", __("Sorry"), __("You don't have the permission to do this"));
        }
      }
    } else {
      switch ($node) {
        case 'page':
          /* check if the page is valid */
          $page = $this->get_page($node_id);
          if (!$page) {
            _error(400);
          }
          /* check if the viewer is page admin */
          if (!$this->check_page_adminship($this->_data['user_id'], $node_id)) {
            _error(400);
          }
          /* check monetization permission */
          if ($post['for_subscriptions']) {
            /* check if page's admin can monetize content */
            $page['can_monetize_content'] = $system['monetization_enabled'] && $this->check_user_permission($page['page_admin'], 'monetization_permission');
            /* check if page has monetization enabled && subscriptions plans */
            if (!($page['can_monetize_content'] && $page['page_monetization_enabled'] && $page['page_monetization_plans'] > 0)) {
              modal("ERROR", __("Sorry"), __("You don't have the permission to do this"));
            }
          }
          $post['user_id'] = $page['page_id'];
          $post['user_type'] = "page";
          break;

        case 'group':
          /* check if the group is valid */
          $group = $this->get_group($node_id);
          if (!$group) {
            _error(400);
          }
          /* check if the viewer joined the group */
          if (!$this->check_group_membership($this->_data['user_id'], $group['group_id'])) {
            _error(400);
          }
          /* check if the viewer is group admin */
          $group['i_admin'] = $this->check_group_adminship($this->_data['user_id'], $group['group_id']);
          /* check if group publish enabled */
          if (!$group['group_publish_enabled'] && !$group['i_admin']) {
            modal("ERROR", __("Sorry"), __("Publish posts disabled by admin"));
          }
          /* check monetization permission */
          if ($post['for_subscriptions']) {
            /* check if group's admin can monetize content */
            $group['can_monetize_content'] = $system['monetization_enabled'] && $this->check_user_permission($group['group_admin'], 'monetization_permission');
            /* check if group has monetization enabled && subscriptions plans */
            if (!($group['can_monetize_content'] && $group['group_monetization_enabled'] && $group['group_monetization_plans'] > 0)) {
              modal("ERROR", __("Sorry"), __("You don't have the permission to do this"));
            }
          }
          $post['privacy'] = 'custom';
          $post['in_group'] = 1;
          $post['group_id'] = $node_id;
          $post['group_approved'] = ($group['group_publish_approval_enabled'] && !$group['i_admin']) ? '0' : '1';
          break;

        case 'event':
          /* check if the event is valid */
          $event = $this->get_event($event_id);
          if (!$event) {
            _error(400);
          }
          /* check if the viewer is event member */
          if (!$this->check_event_membership($this->_data['user_id'], $event_id)) {
            _error(400);
          }
          /* check if the viewer is event admin */
          $event['i_admin'] = $this->check_event_adminship($this->_data['user_id'], $event['event_id']);
          /* check if event publish enabled */
          if (!$event['event_publish_enabled'] && !$event['i_admin']) {
            modal("ERROR", __("Sorry"), __("Publish posts disabled by admin"));
          }
          $post['privacy'] = 'custom';
          $post['in_event'] = 1;
          $post['event_id'] = $node_id;
          $post['event_approved'] = ($event['event_publish_approval_enabled'] && !$event['i_admin']) ? '0' : '1';
          break;
      }
    }
    /* insert post */
    $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, in_group, group_id, group_approved, in_event, event_id, event_approved, tips_enabled, for_subscriptions, privacy, time) VALUES (%s, %s, 'live', %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($post['user_id'], 'int'), secure($post['user_type']), secure($post['in_group']), secure($post['group_id'], 'int'), secure($post['group_approved']), secure($post['in_event']), secure($post['event_id'], 'int'), secure($post['event_approved']), secure($post['tips_enabled']), secure($post['for_subscriptions']), secure($post['privacy']), secure($date))) or _error('SQL_ERROR_THROWEN');
    $post_id = $db->insert_id;
    /* insert live */
    $db->query(sprintf("INSERT INTO posts_live (post_id, video_thumbnail, agora_uid, agora_channel_name) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($video_thumbnail), secure($agora_uid, 'int'), secure($agora_channel_name))) or _error('SQL_ERROR_THROWEN');
    // [BACKGROUND PROCESS]
    /* return async */
    return_json_async(['post_id' => $post_id]);
    /* start live recording */
    if ($system['save_live_enabled']) {
      $this->start_live_recording($post_id, $agora_uid, $agora_token, $agora_channel_name);
    }
    /* notify all audience */
    switch ($node) {
      case 'page':
        /* notify page memebers */
        foreach ($this->get_page_members_ids($node_id) as $member_id) {
          $this->post_notification(['to_user_id' => $member_id, 'from_user_id' => $node_id, 'from_user_type' => 'page', 'action' => 'live_stream', 'node_type' => 'post', 'node_url' => $post_id]);
        }
        break;

      case 'group':
        /* notify group memebers */
        foreach ($this->get_group_members_ids($node_id) as $member_id) {
          $this->post_notification(['to_user_id' => $member_id, 'action' => 'live_stream', 'node_type' => 'post', 'node_url' => $post_id]);
        }
        break;

      case 'event':
        /* notify event memebers */
        foreach ($this->get_event_members_ids($node_id) as $member_id) {
          $this->post_notification(['to_user_id' => $member_id, 'action' => 'live_stream', 'node_type' => 'post', 'node_url' => $post_id]);
        }
        break;

      default:
        /* notify friends */
        foreach ($this->get_friends_ids() as $friend_id) {
          $this->post_notification(['to_user_id' => $friend_id, 'action' => 'live_stream', 'node_type' => 'post', 'node_url' => $post_id]);
        }
        break;
    }
    return $post_id;
  }


  /**
   * end_live_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function end_live_post($post_id)
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->get_post($post_id, false, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* end live post */
    $db->query(sprintf("UPDATE posts_live SET live_ended = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* remove all audience */
    $db->query(sprintf("DELETE FROM posts_live_users WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* stop live recording */
    if ($system['save_live_enabled']) {
      $this->stop_live_recording($post_id, $post['live']['agora_uid'], $post['live']['agora_channel_name'], $post['live']['agora_resource_id'], $post['live']['agora_sid']);
    }
  }


  /**
   * start_live_recording
   * 
   * @param integer $post_id
   * @param string $agora_uid
   * @param string $agora_token
   * @param string $agora_channel_name
   * @return void
   */
  public function start_live_recording($post_id, $agora_uid, $agora_token, $agora_channel_name)
  {
    global $db, $system;

    /* prepare AWS S3 (agora use keys not values!) */
    $s3_regions = [
      "us-east-1",
      "us-east-2",
      "us-west-1",
      "us-west-2",
      "eu-west-1",
      "eu-west-2",
      "eu-west-3",
      "eu-central-1",
      "ap-southeast-1",
      "ap-southeast-2",
      "ap-northeast-1",
      "ap-northeast-2",
      "sa-east-1",
      "ca-central-1",
      "ap-south-1"
    ];
    $system['agora_s3_region'] = array_search($system['agora_s3_region'], $s3_regions);

    /* generate the base64Credentials */
    $base64Credentials = base64_encode($system['agora_customer_id'] . ":" . $system['agora_customer_certificate']);

    /* get the recording resource */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.agora.io/v1/apps/" . $system['agora_app_id'] . "/cloud_recording/acquire");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $base64Credentials, 'Content-Type: application/json;charset=utf-8']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt(
      $ch,
      CURLOPT_POSTFIELDS,
      '{
              "cname": "' . $agora_channel_name . '",
              "uid": "' . $agora_uid . '",
              "clientRequest":{}
            }'
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response);
    $resourceId = $response->resourceId;
    if (!$resourceId) {
      return;
    }

    /* start cloud recording (vendor = 1 for AWS S3) */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.agora.io/v1/apps/" . $system['agora_app_id'] . "/cloud_recording/resourceid/" . $resourceId . "/mode/mix/start");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $base64Credentials, 'Content-Type: application/json;charset=utf-8']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt(
      $ch,
      CURLOPT_POSTFIELDS,
      '{
                "cname":"' . $agora_channel_name . '",
                "uid":"' . $agora_uid . '",
                "clientRequest":{
                    "token":"' . $agora_token . '",
                    "recordingConfig":{
                        "channelType":1,
                        "streamTypes":2,
                        "audioProfile":1,
                        "maxIdleTime":120,
                        "transcodingConfig":{
                            "width":640,
                            "height":480,
                            "fps":30,
                            "bitrate":1500,
                            "maxResolutionUid":"1"
                            }
                        },
                    "storageConfig":{
                        "vendor": 1,
                        "region":' . $system['agora_s3_region'] . ',
                        "bucket":"' . $system['agora_s3_bucket'] . '",
                        "accessKey":"' . $system['agora_s3_key'] . '",
                        "secretKey":"' . $system['agora_s3_secret'] . '",
                        "fileNamePrefix": [
                            "uploads",
                            "videos",
                            "' . date('Y') . '",
                            "' . date('m') . '"
                          ]
                    }
                }
            } '
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response);
    $sid = $response->sid;
    if (!$sid) {
      return;
    }

    /* update post */
    $db->query(sprintf("UPDATE posts_live SET agora_resource_id = %s, agora_sid = %s WHERE post_id = %s", secure($resourceId), secure($sid), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * stop_live_recording
   * 
   * @param integer $post_id
   * @param string $agora_uid
   * @param string $agora_channel_name
   * @param string $agora_resource_id
   * @param string $agora_sid
   * @return void
   */
  public function stop_live_recording($post_id, $agora_uid, $agora_channel_name, $agora_resource_id, $agora_sid)
  {
    global $db, $system;

    /* generate the base64Credentials */
    $base64Credentials = base64_encode($system['agora_customer_id'] . ":" . $system['agora_customer_certificate']);

    /* stop cloud recording */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.agora.io/v1/apps/" . $system['agora_app_id'] . "/cloud_recording/resourceid/" . $agora_resource_id . "/sid/" . $agora_sid . "/mode/mix/stop");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $base64Credentials, 'Content-Type: application/json;charset=utf-8']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt(
      $ch,
      CURLOPT_POSTFIELDS,
      '{
                "cname": "' . $agora_channel_name . '",
                "uid": "' . $agora_uid . '",
                "clientRequest":{}
            }'
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response);
    $file = $response->serverResponse->fileList;
    if (!$file) {
      return;
    }

    /* update post */
    $db->query(sprintf("UPDATE posts_live SET live_recorded = '1', agora_file = %s WHERE post_id = %s", secure($file), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * join_live_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function join_live_post($post_id)
  {
    global $db;
    /* check if this user already joined the live post */
    $check = $db->query(sprintf("SELECT * FROM posts_live_users WHERE user_id = %s AND post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int')));
    if ($check->num_rows > 0) {
      return;
    }
    /* add new audience */
    $db->query(sprintf("INSERT INTO posts_live_users (user_id, post_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'), secure($post_id, 'int')));
  }


  /**
   * leave_live_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function leave_live_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* remove audience */
    $db->query(sprintf("DELETE FROM posts_live_users WHERE user_id = %s AND post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * get_live_post_stats
   * 
   * @param integer $post_id
   * @return void
   */
  public function get_live_post_stats($post_id, $last_comment_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      return [];
    }
    /* total live users */
    $get_live_users = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_live_users WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');

    /* get comments */
    $comments = $this->get_comments($post_id, 0, true, true, $post, false, $last_comment_id);

    return ["live_count" => $get_live_users->fetch_assoc()['count'], "comments" => $comments];
  }


  /**
   * agora_token_builder
   * 
   * @param boolean $is_host
   * @param string $channel_name
   * @return string
   */
  public function agora_token_builder($is_host = true, $channel_name = null)
  {
    global $system;
    $channel_name = ($channel_name) ? $channel_name : get_hash_token();
    $uid = mt_rand();
    $role = ($is_host) ? TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder::RolePublisher : TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder::RoleAttendee;
    $expire_time = 36000;
    $current_timestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
    $privilege_expired = $current_timestamp + $expire_time;
    $token = TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder::buildTokenWithUid($system['agora_app_id'], $system['agora_app_certificate'], $channel_name, $uid, $role, $privilege_expired);
    return ["uid" => $uid, "token" => $token, "channel_name" => $channel_name];
  }



  /* ------------------------------- */
  /* @Mentions */
  /* ------------------------------- */

  /**
   * decode_mention
   * 
   * @param string $text
   * @return string
   */
  function decode_mention($text)
  {
    global $user;
    $text = ($text) ? preg_replace_callback('/\[([a-z0-9._]+)\]/i', [$this, 'get_mentions'], $text) : $text;
    return $text;
  }


  /**
   * get_mentions
   * 
   * @param array $matches
   * @return string
   */
  public function get_mentions($matches)
  {
    global $db, $system;
    $get_user = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname FROM users WHERE user_name = %s", secure($matches[1]))) or _error('SQL_ERROR_THROWEN');
    if ($get_user->num_rows > 0) {
      $user = $get_user->fetch_assoc();
      $replacement = popover($user['user_id'], $user['user_name'], ($system['show_usernames_enabled']) ? $user['user_name'] : $user['user_firstname'] . " " . $user['user_lastname']);
    } else {
      $replacement = $matches[0];
    }
    return $replacement;
  }


  /**
   * post_mentions
   * 
   * @param string $text
   * @param integer $node_url
   * @param string $node_type
   * @param string $notify_id
   * @param array $excluded_ids
   * @return void
   */
  public function post_mentions($text, $node_url, $node_type = 'post', $notify_id = '', $excluded_ids = [])
  {
    global $db;
    $where_query = "";
    if ($excluded_ids) {
      $excluded_list = $this->spread_ids($excluded_ids);
      $where_query = " user_id NOT IN ($excluded_list) AND ";
    }
    $done = [];
    if (preg_match_all('/\[([a-zA-Z0-9._]+)\]/', $text, $matches)) {
      foreach ($matches[1] as $username) {
        if ($this->_data['user_name'] != $username && !in_array($username, $done)) {
          $get_user = $db->query(sprintf("SELECT user_id FROM users WHERE " . $where_query . " user_name = %s", secure($username))) or _error('SQL_ERROR_THROWEN');
          if ($get_user->num_rows > 0) {
            $_user = $get_user->fetch_assoc();
            $this->post_notification(['to_user_id' => $_user['user_id'], 'action' => 'mention', 'node_type' => $node_type, 'node_url' => $node_url, 'notify_id' => $notify_id]);
            $done[] = $username;
          }
        }
      }
    }
  }



  /* ------------------------------- */
  /* Popovers */
  /* ------------------------------- */

  /**
   * popover
   * 
   * @param integer $id
   * @param string $type
   * @return array
   */
  public function popover($id, $type)
  {
    global $db;
    $profile = [];
    /* check the type to get */
    if ($type == "user") {
      /* get user info */
      $get_profile = $db->query(sprintf("SELECT * FROM users WHERE user_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_profile->num_rows > 0) {
        $profile = $get_profile->fetch_assoc();
        /* get profile picture */
        $profile['user_picture'] = get_picture($profile['user_picture'], $profile['user_gender']);
        /* get followers count */
        $profile['followers_count'] = $this->get_followers_count($profile['user_id']);
        /* get mutual friends count between the viewer and the target */
        if ($this->_logged_in && $this->_data['user_id'] != $profile['user_id']) {
          $profile['mutual_friends_count'] = $this->get_mutual_friends_count($profile['user_id']);
        }
        /* get the connection between the viewer & the target */
        if ($profile['user_id'] != $this->_data['user_id']) {
          $profile['we_friends'] = $this->friendship_approved($profile['user_id']);
          $profile['he_request'] = $this->is_friend_request($profile['user_id']);
          $profile['i_request'] = $this->is_friend_request_sent($profile['user_id']);
          $profile['i_follow'] = $this->is_following($profile['user_id']);
        }
      }
    } else {
      /* get page info */
      $get_profile = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_profile->num_rows > 0) {
        $profile = $get_profile->fetch_assoc();
        $profile['page_picture'] = get_picture($profile['page_picture'], "page");
        /* check if the viewer liked the page */
        $profile['i_like'] = $this->check_page_membership($this->_data['user_id'], $id);
      }
    }
    return $profile;
  }



  /* ------------------------------- */
  /* Hashtags */
  /* ------------------------------- */

  /**
   * get_trending_hashtags
   * 
   * @return array
   */
  public function get_trending_hashtags()
  {
    global $system, $db;
    $hashtags = [];
    $get_trending_hashtags = $db->query(sprintf("SELECT hashtags.hashtag, COUNT(hashtags_posts.id) AS frequency FROM hashtags INNER JOIN hashtags_posts ON hashtags.hashtag_id = hashtags_posts.hashtag_id WHERE hashtags_posts.created_at > DATE_SUB(CURDATE(), INTERVAL 1 %s) GROUP BY hashtags_posts.hashtag_id ORDER BY frequency DESC LIMIT %s", secure($system['trending_hashtags_interval'], "", false), secure($system['trending_hashtags_limit'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_trending_hashtags->num_rows > 0) {
      while ($hashtag = $get_trending_hashtags->fetch_assoc()) {
        $hashtag['hashtag'] = html_entity_decode($hashtag['hashtag'], ENT_QUOTES);
        $hashtags[] = $hashtag;
      }
    }
    return $hashtags;
  }


  /**
   * decode_hashtags
   * 
   * @param string $text
   * @param boolean $trending_hashtags
   * @param integer $post_id
   * @return string
   */
  function decode_hashtags($text, $trending_hashtags, $post_id)
  {
    if ($text) {
      $pattern = '/(\s|^)((#|\x{ff03}){1}([0-9_\p{L}&;]*[_\p{L}&;][0-9_\p{L}&;]*))/u';
      $text = preg_replace_callback($pattern, function ($matches) use ($trending_hashtags, $post_id) {
        global $system, $db, $date;
        if ($trending_hashtags) {
          $get_hashtag = $db->query(sprintf("SELECT hashtag_id FROM hashtags WHERE hashtag = %s", secure($matches[4]))) or _error('SQL_ERROR_THROWEN');
          if ($get_hashtag->num_rows == 0) {
            /* insert the new hashtag */
            $db->query(sprintf("INSERT INTO hashtags (hashtag) VALUES (%s)", secure($matches[4]))) or _error('SQL_ERROR_THROWEN');
            $hashtag_id = $db->insert_id;
          } else {
            $hashtag_id = $get_hashtag->fetch_assoc()['hashtag_id'];
          }
          /* connect hashtag with the post */
          $db->query(sprintf("INSERT INTO hashtags_posts (post_id, hashtag_id, created_at) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($hashtag_id, 'int'), secure($date)));
        }
        return $matches[1] . '<a href="' . $system['system_url'] . '/search/hashtag/' . $matches[4] . '">' . $matches[2] . '</a>';
      }, $text);
    }
    return $text;
  }


  /**
   * delete_hashtags
   * 
   * @param integer $post_id
   * @return void
   */
  public function delete_hashtags($post_id)
  {
    global $db;
    /* unconnect hashtag with the post */
    $db->query(sprintf("DELETE FROM hashtags_posts WHERE post_id = %s", secure($post_id, 'int')));
  }



  /* ------------------------------- */
  /* Stories */
  /* ------------------------------- */

  /**
   * post_story
   * 
   * @param string $message
   * @param array $photos
   * @param array $videos
   * @param int $is_ads
   * @return void
   */
  public function post_story($message, $photos, $videos, $is_ads = 0)
  {
    global $db, $system, $date;
    /* check stories permission */
    if (!$this->_data['can_add_stories']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check latest story */
    $get_last_story = $db->query(sprintf("SELECT story_id FROM stories WHERE time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND stories.user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_last_story->num_rows > 0) {
      /* get story_id */
      $story_id = $get_last_story->fetch_assoc()['story_id'];
      /* update story time */
      $db->query(sprintf("UPDATE stories SET is_ads = %s, time = %s WHERE story_id = %s", secure($is_ads), secure($date), secure($story_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      /* insert new story */
      $db->query(sprintf("INSERT INTO stories (user_id, is_ads, time) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($is_ads), secure($date))) or _error('SQL_ERROR_THROWEN');
      /* get story_id */
      $story_id = $db->insert_id;
    }
    /* prepare message */
    $message = strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
    /* insert story media items */
    foreach ($photos as $photo) {
      $db->query(sprintf("INSERT INTO stories_media (story_id, source, text, time) VALUES (%s, %s, %s, %s)", secure($story_id, 'int'), secure($photo['source']), secure($message), secure($date))) or _error('SQL_ERROR_THROWEN');
    }
    foreach ($videos as $video) {
      $db->query(sprintf("INSERT INTO stories_media (story_id, source, is_photo, text, time) VALUES (%s, %s, '0', %s, %s)", secure($story_id, 'int'), secure($video), secure($message), secure($date))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * get_stories
   * 
   * @return array
   */
  public function get_stories()
  {
    global $db, $system;
    $stories = [];
    /* stories source */
    $source_query = ($system['newsfeed_source'] == "default") ? sprintf(" AND ( is_ads = '1' OR stories.user_id = %s OR stories.user_id IN (%s)) ", $this->_data['user_id'], $this->spread_ids($this->_data['friends_ids'])) : "";
    /* get stories */
    $get_stories = $db->query("SELECT stories.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM stories INNER JOIN users ON stories.user_id = users.user_id WHERE time >= DATE_SUB(NOW(), INTERVAL 1 DAY) $source_query ORDER BY is_ads = '1' DESC, stories.story_id DESC") or _error('SQL_ERROR_THROWEN');
    if ($get_stories->num_rows > 0) {
      while ($_story = $get_stories->fetch_assoc()) {
        $story['id'] = "user_" . $_story['story_id'];
        $story['photo'] = get_picture($_story['user_picture'], $_story['user_gender']);
        $story['name'] = ($system['show_usernames_enabled']) ? $_story['user_name'] : $_story['user_firstname'] . " " . $_story['user_lastname'];
        $story['lastUpdated'] = strtotime($_story['time']);
        $story['items'] = [];
        /* get story media items */
        $get_media_items = $db->query(sprintf("SELECT * FROM stories_media WHERE story_id = %s", secure($_story['story_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        while ($media_item = $get_media_items->fetch_assoc()) {
          $story_item['id'] = "media_" . $media_item['media_id'];
          $story_item['type'] = ($media_item['is_photo']) ? 'photo' : 'video';
          $story_item['src'] = $system['system_uploads'] . '/' . $media_item['source'];
          $story_item['preview'] = ($media_item['is_photo']) ? $system['system_uploads'] . '/' . $media_item['source'] : $story['photo'];
          $story_item['link'] = '.';
          $story_item['linkText'] = strip_tags(html_entity_decode($media_item['text'], ENT_QUOTES, 'UTF-8'));
          $story_item['time'] = strtotime($media_item['time']);
          $story_item['length'] = $system['stories_duration'];
          $story['items'][] = $story_item;
        }
        $stories[] = $story;
      }
    }
    return ["array" => $stories, "json" => json_encode($stories)];
  }


  /**
   * get_my_story
   * 
   * @return boolean
   */
  public function get_my_story()
  {
    global $db, $system;
    $get_my_story = $db->query(sprintf("SELECT COUNT(*) as count FROM stories WHERE time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_my_story->fetch_assoc()['count'] == 0) {
      return false;
    } else {
      return true;
    }
  }


  /**
   * delete_my_story
   * 
   * @return void
   */
  public function delete_my_story()
  {
    global $db, $system;
    $check_story = $db->query(sprintf("DELETE FROM stories WHERE time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Publisher */
  /* ------------------------------- */

  /**
   * publisher
   * 
   * @param array $args
   * @return array
   */
  public function publisher($args = [])
  {
    global $db, $system, $date;

    /* check posts permission */
    if (!$this->_data['can_publish_posts']) {
      throw new Exception(__("You don't have the permission to do this"));
    }

    /* check if verification for posts required */
    if ($system['verification_for_posts'] && !$this->_data['user_verified']) {
      throw new Exception(__("To enable this feature your account must be verified"));
    }

    /* check max posts/hour limit */
    $this->_check_posts_limit($args['handle'], $args['id']);

    /* check post max length */
    if ($system['max_post_length'] > 0 && $this->_data['user_group'] >= 3) {
      if (strlen($args['message']) >= $system['max_post_length']) {
        modal("MESSAGE", __("Text Length Limit Reached"), __("Your message characters length is over the allowed limit") . " (" . $system['max_post_length'] . " " . __("Characters") . ")");
      }
    }

    /* prepare returned post */
    $post = [];
    $post['user_id'] = $this->_data['user_id'];
    $post['user_type'] = "user";
    $post['in_wall'] = 0;
    $post['wall_id'] = null;
    $post['in_group'] = 0;
    $post['group_id'] = null;
    $post['group_approved'] = 0;
    $post['in_event'] = 0;
    $post['event_id'] = null;
    $post['event_approved'] = 0;

    $post['author_id'] = $this->_data['user_id'];
    $post['post_author_picture'] = $this->_data['user_picture'];
    $post['post_author_url'] = $system['system_url'] . '/' . $this->_data['user_name'];
    $post['post_author_name'] = $this->_data['name'];
    $post['post_author_verified'] = $this->_data['user_verified'];
    $post['post_author_online'] = false;

    /* check the handle */
    if ($args['handle'] == "user") {
      /* check if system allow wall posts */
      if (!$system['wall_posts_enabled']) {
        _error(400);
      }
      /* check if the user is valid & the viewer can post on his wall */
      $check_user = $db->query(sprintf("SELECT * FROM users WHERE user_id = %s", secure($args['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check_user->num_rows == 0) {
        _error(400);
      }
      $_user = $check_user->fetch_assoc();
      if ($_user['user_privacy_wall'] == 'me' || ($_user['user_privacy_wall'] == 'friends' && !$this->friendship_approved($args['id']))) {
        _error(400);
      }
      $post['in_wall'] = 1;
      $post['wall_id'] = $args['id'];
      $post['wall_username'] = $_user['user_name'];
      $post['wall_fullname'] = ($system['show_usernames_enabled']) ? $_user['user_name'] : $_user['user_firstname'] . " " . $_user['user_lastname'];
    } elseif ($args['handle'] == "page") {
      /* check if the page is valid */
      $check_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($args['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check_page->num_rows == 0) {
        _error(400);
      }
      $_page = $check_page->fetch_assoc();
      /* check if the viewer is the admin */
      if (!$this->check_page_adminship($this->_data['user_id'], $args['id'])) {
        _error(400);
      }
      $post['user_id'] = $_page['page_id'];
      $post['user_type'] = "page";
      $post['post_author_picture'] = get_picture($_page['page_picture'], "page");
      $post['post_author_url'] = $system['system_url'] . '/pages/' . $_page['page_name'];
      $post['post_author_name'] = $_page['page_title'];
      $post['post_author_verified'] = $this->_data['page_verified'];
    } elseif ($args['handle'] == "group") {
      /* check if the group is valid & the viewer is group member (approved) */
      $check_group = $db->query(sprintf("SELECT `groups`.* FROM `groups` INNER JOIN groups_members ON `groups`.group_id = groups_members.group_id WHERE groups_members.approved = '1' AND groups_members.user_id = %s AND groups_members.group_id = %s", secure($this->_data['user_id'], 'int'), secure($args['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      $_group = $check_group->fetch_assoc();
      if (!$_group) {
        _error(400);
      }
      $_group['i_admin'] = $this->check_group_adminship($this->_data['user_id'], $_group['group_id']);
      /* check if group publish enabled */
      if (!$_group['group_publish_enabled'] && !$_group['i_admin']) {
        modal("MESSAGE", __("Sorry"), __("Publish posts disabled by admin"));
      }
      $post['in_group'] = 1;
      $post['group_id'] = $args['id'];
      $post['group_approved'] = ($_group['group_publish_approval_enabled'] && !$_group['i_admin']) ? '0' : '1';
    } elseif ($args['handle'] == "event") {
      /* check if the event is valid & the viewer is event member */
      $check_event = $db->query(sprintf("SELECT `events`.* FROM `events` INNER JOIN events_members ON `events`.event_id = events_members.event_id WHERE events_members.user_id = %s AND events_members.event_id = %s", secure($this->_data['user_id'], 'int'), secure($args['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      $_event = $check_event->fetch_assoc();
      if (!$_event) {
        _error(400);
      }
      $_event['i_admin'] = $this->check_event_adminship($this->_data['user_id'], $_event['event_id']);
      /* check if event publish enabled */
      if (!$_event['event_publish_enabled'] && !$_event['i_admin']) {
        modal("MESSAGE", __("Sorry"), __("Publish posts disabled by admin"));
      }
      $post['in_event'] = 1;
      $post['event_id'] = $args['id'];
      $post['event_approved'] = ($_event['event_publish_approval_enabled'] && !$_event['i_admin']) ? '0' : '1';
      /* post as page */
      if (isset($args['post_as_page'])) {
        /* check if the page is valid */
        $check_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($args['post_as_page'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($check_page->num_rows == 0) {
          _error(400);
        }
        $_page = $check_page->fetch_assoc();
        /* check if the viewer is the admin */
        if (!$this->check_page_adminship($this->_data['user_id'], $args['post_as_page'])) {
          _error(400);
        }
        $post['user_id'] = $_page['page_id'];
        $post['user_type'] = "page";
        $post['post_author_picture'] = get_picture($_page['page_picture'], "page");
        $post['post_author_url'] = $system['system_url'] . '/pages/' . $_page['page_name'];
        $post['post_author_name'] = $_page['page_title'];
        $post['post_author_verified'] = $this->_data['page_verified'];
      }
    }

    /* check the user_type to set user online or not */
    if ($post['user_type'] == "user") {
      if ($this->_data['user_chat_enabled']) {
        $post['post_author_online'] = true;
      }
    }

    /* prepare approval system */
    $post['pre_approved'] = 1;
    $post['has_approved'] = 1;
    if ($this->check_posts_needs_approval($post['user_id'], $post['user_type'])) {
      $post['pre_approved'] = 0;
      $post['has_approved'] = 0;
    }

    /* prepare post data */
    $post['text'] = $args['message'];
    $post['time'] = $date;
    $post['location'] = $args['location'];
    $post['privacy'] = $args['privacy'];
    $post['is_anonymous'] = (isset($args['is_anonymous'])) ? $args['is_anonymous'] : '0';
    $post['for_adult'] = (isset($args['for_adult'])) ? $args['for_adult'] : '0';
    $post['tips_enabled'] = (isset($args['tips_enabled'])) ? $args['tips_enabled'] : '0';
    $post['for_subscriptions'] = (isset($args['for_subscriptions'])) ? $args['for_subscriptions'] : '0';
    /* check monetization permission */
    if ($post['for_subscriptions']) {
      switch ($args['handle']) {
        case 'me':
          if (!($this->_data['can_monetize_content'] && $this->_data['user_monetization_enabled'] && $this->_data['user_monetization_plans'] > 0)) {
            throw new Exception(__("You don't have the permission to do this"));
          }
          break;

        case 'page':
          /* check if page's admin can monetize content */
          $_page['can_monetize_content'] = $system['monetization_enabled'] && $this->check_user_permission($_page['page_admin'], 'monetization_permission');
          /* check if page has monetization enabled && subscriptions plans */
          $_page['has_subscriptions_plans'] = $_page['can_monetize_content'] && $_page['page_monetization_enabled'] && $_page['page_monetization_plans'] > 0;
          if (!$_page['has_subscriptions_plans']) {
            throw new Exception(__("You don't have the permission to do this"));
          }
          break;

        case 'group':
          /* check if group's admin can monetize content */
          $_group['can_monetize_content'] = $system['monetization_enabled'] && $this->check_user_permission($_group['group_admin'], 'monetization_permission');
          /* check if group has monetization enabled && subscriptions plans */
          $_group['has_subscriptions_plans'] = $_group['can_monetize_content'] && $_group['group_monetization_enabled'] && $_group['group_monetization_plans'] > 0;
          if (!$_group['has_subscriptions_plans']) {
            throw new Exception(__("You don't have the permission to do this"));
          }
          break;

        default:
          throw new Exception(__("You don't have the permission to do this"));
          break;
      }
    }
    $post['is_paid'] = (isset($args['is_paid'])) ? $args['is_paid'] : '0';
    $post['post_price'] = $args['post_price'];
    $post['paid_text'] = $args['paid_text'];
    $post['reaction_like_count'] = 0;
    $post['reaction_love_count'] = 0;
    $post['reaction_haha_count'] = 0;
    $post['reaction_yay_count'] = 0;
    $post['reaction_wow_count'] = 0;
    $post['reaction_sad_count'] = 0;
    $post['reaction_angry_count'] = 0;
    $post['reactions_total_count'] = 0;
    $post['comments'] = 0;
    $post['shares'] = 0;
    $post['views'] = 0;
    $post['processing'] = '0';

    /* post feeling */
    $post['feeling_action'] = '';
    $post['feeling_value'] = '';
    $post['feeling_icon'] = '';
    if (!is_empty($args['feeling_action']) && !is_empty($args['feeling_value'])) {
      if ($args['feeling_action'] != "Feeling") {
        $_feeling_icon = get_feeling_icon($args['feeling_action'], get_feelings());
      } else {
        $_feeling_icon = get_feeling_icon($args['feeling_value'], get_feelings_types());
      }
      if ($_feeling_icon) {
        $post['feeling_action'] = $args['feeling_action'];
        $post['feeling_value'] = $args['feeling_value'];
        $post['feeling_icon'] = $_feeling_icon;
      }
    }

    /* post colored pattern */
    $post['colored_pattern'] = $args['colored_pattern'];

    /* prepare post type */
    if ($args['link']) {
      if ($args['link']->source_type == "link") {
        $post['post_type'] = 'link';
      } else {
        $post['post_type'] = 'media';
      }
    } elseif ($args['product']) {
      $post['post_type'] = 'product';
      $post['privacy'] = "public";
    } elseif ($args['funding']) {
      $post['post_type'] = 'funding';
      $post['privacy'] = "public";
    } elseif ($args['offer']) {
      $post['post_type'] = 'offer';
    } elseif ($args['job']) {
      $post['post_type'] = 'job';
    } elseif ($args['poll_options']) {
      $post['post_type'] = 'poll';
    } elseif (($args['photos'] && count($args['photos']) > 0) || $args['video'] || $args['audio'] || $args['file']) {
      /* post contains video */
      if ($args['video']) {
        $post['post_type'] = 'video';
        /* check if ffmpeg enabled */
        if ($system['ffmpeg_enabled']) {
          $post['processing'] = '1';
        }
      }
      /* post contains audio */
      if ($args['audio']) {
        $post['post_type'] = ($post['post_type'] == 'video') ? 'combo' : 'audio';
      }
      /* post contains file */
      if ($args['file']) {
        $post['post_type'] = (in_array($post['post_type'], ['combo', 'video', 'audio'])) ? 'combo' : 'file';
      }
      /* post contains photos */
      if ($args['photos'] && count($args['photos']) > 0) {
        if (!is_empty($args['album'])) {
          $post['post_type'] = 'album';
        } else {
          $post['post_type'] = (in_array($post['post_type'], ['combo', 'video', 'audio', 'file'])) ? 'combo' : 'photos';
        }
      }
    } else {
      if ($post['location'] != '') {
        $post['post_type'] = 'map';
      } else {
        $post['post_type'] = '';
      }
    }

    /* insert the post */
    $db->query(sprintf("INSERT INTO posts (user_id, user_type, in_wall, wall_id, in_group, group_id, group_approved, in_event, event_id, event_approved, post_type, colored_pattern, time, location, privacy, text, feeling_action, feeling_value, for_adult, is_anonymous, tips_enabled, for_subscriptions, is_paid, post_price, paid_text, processing, pre_approved, has_approved) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($post['user_id'], 'int'), secure($post['user_type']), secure($post['in_wall'], 'int'), secure($post['wall_id'], 'int'), secure($post['in_group']), secure($post['group_id'], 'int'), secure($post['group_approved']), secure($post['in_event']), secure($post['event_id'], 'int'), secure($post['event_approved']), secure($post['post_type']), secure($post['colored_pattern'], 'int'), secure($post['time']), secure($post['location']), secure($post['privacy']), secure($post['text']), secure($post['feeling_action']), secure($post['feeling_value']), secure($post['for_adult']), secure($post['is_anonymous']), secure($post['tips_enabled']), secure($post['for_subscriptions']), secure($post['is_paid']), secure($post['post_price'], 'float'), secure($post['paid_text']), secure($post['processing']), secure($post['pre_approved']), secure($post['has_approved']))) or _error('SQL_ERROR_THROWEN');
    $post['post_id'] = $db->insert_id;

    /* insert the post [link] */
    if ($post['post_type'] == 'link') {
      $db->query(sprintf("INSERT INTO posts_links (post_id, source_url, source_host, source_title, source_text, source_thumbnail) VALUES (%s, %s, %s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($args['link']->source_url), secure($args['link']->source_host), secure($args['link']->source_title), secure($args['link']->source_text), secure($args['link']->source_thumbnail))) or _error('SQL_ERROR_THROWEN');
      $post['link']['link_id'] = $db->insert_id;
      $post['link']['post_id'] = $post['post_id'];
      $post['link']['source_url'] = $args['link']->source_url;
      $post['link']['source_host'] = $args['link']->source_host;
      $post['link']['source_title'] = $args['link']->source_title;
      $post['link']['source_text'] = $args['link']->source_text;
      $post['link']['source_thumbnail'] = $args['link']->source_thumbnail;
    }
    /* insert the post [media] */
    if ($post['post_type'] == 'media') {
      $db->query(sprintf("INSERT INTO posts_media (post_id, source_url, source_provider, source_type, source_title, source_text, source_html, source_thumbnail) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($args['link']->source_url), secure($args['link']->source_provider), secure($args['link']->source_type), secure($args['link']->source_title), secure($args['link']->source_text), secure($args['link']->source_html), secure($args['link']->source_thumbnail))) or _error('SQL_ERROR_THROWEN');
      $post['media']['media_id'] = $db->insert_id;
      $post['media']['post_id'] = $post['post_id'];
      $post['media']['source_url'] = $args['link']->source_url;
      $post['media']['source_type'] = $args['link']->source_type;
      $post['media']['source_provider'] = $args['link']->source_provider;
      $post['media']['source_title'] = $args['link']->source_title;
      $post['media']['source_text'] = $args['link']->source_text;
      $post['media']['source_html'] = $args['link']->source_html;
    }
    /* insert the post [photos] */
    if ($post['post_type'] == 'photos' || ($post['post_type'] == 'combo' && $args['photos'])) {
      if ($args['handle'] == "page") {
        /* check for page timeline album (public by default) */
        if (!$_page['page_album_timeline']) {
          /* create new page timeline album */
          $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title) VALUES (%s, 'page', 'Timeline Photos')", secure($_page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          $_page['page_album_timeline'] = $db->insert_id;
          /* update page */
          $db->query(sprintf("UPDATE pages SET page_album_timeline = %s WHERE page_id = %s", secure($_page['page_album_timeline'], 'int'), secure($_page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        $album_id = $_page['page_album_timeline'];
      } elseif ($args['handle'] == "group") {
        /* check for group timeline album */
        if (!$_group['group_album_timeline']) {
          /* create new group timeline album */
          $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_group, group_id, title, privacy) VALUES (%s, %s, %s, %s, 'Timeline Photos', 'custom')", secure($post['user_id'], 'int'), secure($post['user_type']), secure($post['in_group']), secure($post['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          $_group['group_album_timeline'] = $db->insert_id;
          /* update group */
          $db->query(sprintf("UPDATE `groups` SET group_album_timeline = %s WHERE group_id = %s", secure($_group['group_album_timeline'], 'int'), secure($_group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        $album_id = $_group['group_album_timeline'];
      } elseif ($args['handle'] == "event") {
        /* check for event timeline album */
        if (!$_event['event_album_timeline']) {
          /* create new event timeline album */
          $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_event, event_id, title, privacy) VALUES (%s, %s, %s, %s, 'Timeline Photos', 'custom')", secure($post['user_id'], 'int'), secure($post['user_type']), secure($post['in_event']), secure($post['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          $_event['event_album_timeline'] = $db->insert_id;
          /* update event */
          $db->query(sprintf("UPDATE `events` SET event_album_timeline = %s WHERE event_id = %s", secure($_event['event_album_timeline'], 'int'), secure($_event['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        $album_id = $_event['event_album_timeline'];
      } else {
        /* check for timeline album */
        if (!$this->_data['user_album_timeline']) {
          /* create new timeline album (public by default) */
          $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title) VALUES (%s, 'user', 'Timeline Photos')", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          $this->_data['user_album_timeline'] = $db->insert_id;
          /* update user */
          $db->query(sprintf("UPDATE users SET user_album_timeline = %s WHERE user_id = %s", secure($this->_data['user_album_timeline'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        $album_id = $this->_data['user_album_timeline'];
      }
      foreach ($args['photos'] as $photo) {
        $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($album_id, 'int'), secure($photo['source']), secure($photo['blur']))) or _error('SQL_ERROR_THROWEN');
        $post_photo['photo_id'] = $db->insert_id;
        $post_photo['post_id'] = $post['post_id'];
        $post_photo['source'] = $photo['source'];
        $post_photo['blur'] = $photo['blur'];
        $post_photo['reaction_like_count'] = 0;
        $post_photo['reaction_love_count'] = 0;
        $post_photo['reaction_haha_count'] = 0;
        $post_photo['reaction_yay_count'] = 0;
        $post_photo['reaction_wow_count'] = 0;
        $post_photo['reaction_sad_count'] = 0;
        $post_photo['reaction_angry_count'] = 0;
        $post_photo['reactions_total_count'] = 0;
        $post_photo['comments'] = 0;
        $post['photos'][] = $post_photo;
      }
      $post['photos_num'] = count($post['photos']);
    }
    /* insert the post [album] */
    if ($post['post_type'] == 'album') {
      /* create new album */
      $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_group, group_id, in_event, event_id, title, privacy) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", secure($post['user_id'], 'int'), secure($post['user_type']), secure($post['in_group']), secure($post['group_id'], 'int'), secure($post['in_event']), secure($post['event_id'], 'int'), secure($args['album']), secure($post['privacy']))) or _error('SQL_ERROR_THROWEN');
      $album_id = $db->insert_id;
      foreach ($args['photos'] as $photo) {
        $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($album_id, 'int'), secure($photo['source']), secure($photo['blur']))) or _error('SQL_ERROR_THROWEN');
        $post_photo['photo_id'] = $db->insert_id;
        $post_photo['post_id'] = $post['post_id'];
        $post_photo['source'] = $photo['source'];
        $post_photo['blur'] = $photo['blur'];
        $post_photo['reaction_like_count'] = 0;
        $post_photo['reaction_love_count'] = 0;
        $post_photo['reaction_haha_count'] = 0;
        $post_photo['reaction_yay_count'] = 0;
        $post_photo['reaction_wow_count'] = 0;
        $post_photo['reaction_sad_count'] = 0;
        $post_photo['reaction_angry_count'] = 0;
        $post_photo['reactions_total_count'] = 0;
        $post_photo['comments'] = 0;
        $post['photos'][] = $post_photo;
      }
      $post['album']['album_id'] = $album_id;
      $post['album']['title'] = $args['album'];
      $post['photos_num'] = count($post['photos']);
      /* get album path */
      if ($post['in_group']) {
        $post['album']['path'] = 'groups/' . $_group['group_name'];
      } elseif ($post['in_event']) {
        $post['album']['path'] = 'events/' . $_event['event_id'];
      } elseif ($post['user_type'] == "user") {
        $post['album']['path'] = $this->_data['user_name'];
      } elseif ($post['user_type'] == "page") {
        $post['album']['path'] = 'pages/' . $_page['page_name'];
      }
    }
    /* insert the post [product] */
    if ($post['post_type'] == 'product') {
      /* insert product details */
      /* Note: no need to return any data as publisher will redirect to post link */
      $db->query(sprintf("INSERT INTO posts_products (post_id, name, price, quantity, category_id, status, location, is_digital, product_download_url, product_file_source) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($args['product']->name), secure($args['product']->price, 'float'), secure($args['product']->quantity, 'int'), secure($args['product']->category, 'int'), secure($args['product']->status), secure($args['product']->location), secure($args['product']->is_digital), secure($args['product']->product_url), secure($args['product']->product_file))) or _error('SQL_ERROR_THROWEN');
      /* insert product photos */
      if (count($args['photos']) > 0) {
        foreach ($args['photos'] as $photo) {
          $db->query(sprintf("INSERT INTO posts_photos (post_id, source, blur) VALUES (%s, %s, %s)", secure($post['post_id'], 'int'), secure($photo['source']), secure($photo['blur']))) or _error('SQL_ERROR_THROWEN');
        }
      }
    }
    /* insert the post [funding] */
    if ($post['post_type'] == 'funding') {
      /* insert funding details */
      /* Note: no need to return any data as publisher will redirect to post link */
      $db->query(sprintf("INSERT INTO posts_funding (post_id, title, amount, cover_image) VALUES (%s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($args['funding']->title), secure($args['funding']->amount), secure($args['funding']->cover_image))) or _error('SQL_ERROR_THROWEN');
    }
    /* insert the post [offer] */
    if ($post['post_type'] == 'offer') {
      /* prepare end date (optional) */
      $end_date = ($args['offer']->end_date) ? secure($args['offer']->end_date, 'datetime') : 'NULL';
      /* insert offer details */
      /* Note: no need to return any data as publisher will redirect to post link */
      $db->query(sprintf("INSERT INTO posts_offers (post_id, category_id, title, discount_type, discount_percent, discount_amount, buy_x, get_y, spend_x, amount_y, end_date, price) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($args['offer']->category, 'int'), secure($args['offer']->title), secure($args['offer']->discount_type), secure($args['offer']->discount_percent, 'int'), secure($args['offer']->discount_amount), secure($args['offer']->buy_x), secure($args['offer']->get_y), secure($args['offer']->spend_x), secure($args['offer']->amount_y), $end_date, secure($args['offer']->price, 'float'))) or _error('SQL_ERROR_THROWEN');
      /* insert offer photos */
      if (count($args['photos']) > 0) {
        foreach ($args['photos'] as $photo) {
          $db->query(sprintf("INSERT INTO posts_photos (post_id, source, blur) VALUES (%s, %s, %s)", secure($post['post_id'], 'int'), secure($photo['source']), secure($photo['blur']))) or _error('SQL_ERROR_THROWEN');
        }
      }
    }
    /* insert the post [job] */
    if ($post['post_type'] == 'job') {
      /* insert job details */
      /* Note: no need to return any data as publisher will redirect to post link */
      $db->query(sprintf("INSERT INTO posts_jobs (post_id, category_id, title, location, salary_minimum, salary_maximum, pay_salary_per, type, question_1_type, question_1_title, question_1_choices, question_2_type, question_2_title, question_2_choices, question_3_type, question_3_title, question_3_choices, cover_image) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($args['job']->category, 'int'), secure($args['job']->title), secure($args['job']->location), secure($args['job']->salary_minimum), secure($args['job']->salary_maximum), secure($args['job']->pay_salary_per), secure($args['job']->type), secure($args['job']->question_1_type), secure($args['job']->question_1_title), secure($args['job']->question_1_choices), secure($args['job']->question_2_type), secure($args['job']->question_2_title), secure($args['job']->question_2_choices), secure($args['job']->question_3_type), secure($args['job']->question_3_title), secure($args['job']->question_3_choices), secure($args['job']->cover_image))) or _error('SQL_ERROR_THROWEN');
    }
    /* insert the post [poll] */
    if ($post['post_type'] == 'poll') {
      /* insert poll */
      $db->query(sprintf("INSERT INTO posts_polls (post_id) VALUES (%s)", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      $post['poll']['poll_id'] = $db->insert_id;
      $post['poll']['post_id'] = $post['post_id'];
      $post['poll']['votes'] = '0';
      /* insert poll options */
      foreach ($args['poll_options'] as $option) {
        $db->query(sprintf("INSERT INTO posts_polls_options (poll_id, text) VALUES (%s, %s)", secure($post['poll']['poll_id'], 'int'), secure($option))) or _error('SQL_ERROR_THROWEN');
        $poll_option['option_id'] = $db->insert_id;
        $poll_option['text'] = $option;
        $poll_option['votes'] = 0;
        $poll_option['checked'] = false;
        $post['poll']['options'][] = $poll_option;
      }
    }
    /* insert the post [video] */
    if ($post['post_type'] == 'video' || ($post['post_type'] == 'combo' && $args['video'])) {
      $db->query(sprintf("INSERT INTO posts_videos (post_id, category_id, source, thumbnail) VALUES (%s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($args['video_category']), secure($args['video']->source), secure($args['video_thumbnail']))) or _error('SQL_ERROR_THROWEN');
      $post['video']['video_id'] = $db->insert_id;
      $post['video']['category_id'] = $args['category_id'];
      $post['video']['source'] = $args['video']->source;
      $post['video']['thumbnail'] = $args['video_thumbnail'];
      /* get category name */
      $get_category = $db->query(sprintf("SELECT * FROM posts_videos_categories WHERE category_id = %s", secure($args['video_category'], 'int'))) or _error('SQL_ERROR_THROWEN');
      $post['video']['category_name'] = $get_category->fetch_assoc()['name'];
    }
    /* insert the post [audio] */
    if ($post['post_type'] == 'audio' || ($post['post_type'] == 'combo' && $args['audio'])) {
      $db->query(sprintf("INSERT INTO posts_audios (post_id, source) VALUES (%s, %s)", secure($post['post_id'], 'int'), secure($args['audio']->source))) or _error('SQL_ERROR_THROWEN');
      $post['audio']['audio_id'] = $db->insert_id;
      $post['audio']['source'] = $args['audio']->source;
    }
    /* insert the post [file] */
    if ($post['post_type'] == 'file' || ($post['post_type'] == 'combo' && $args['file'])) {
      $db->query(sprintf("INSERT INTO posts_files (post_id, source) VALUES (%s, %s)", secure($post['post_id'], 'int'), secure($args['file']->source))) or _error('SQL_ERROR_THROWEN');
      $post['file']['file_id'] = $db->insert_id;
      $post['file']['source'] = $args['file']->source;
    }

    /* insert custom fields values (product|offer|job) */
    if ($args['custom_fields']) {
      foreach ($args['custom_fields'] as $field_id => $value) {
        $db->query(sprintf("INSERT INTO custom_fields_values (value, field_id, node_id, node_type) VALUES (%s, %s, %s, %s)", secure($value), secure($field_id, 'int'), secure($post['post_id'], 'int'), secure($post['post_type'])));
      }
    }

    /* post mention notifications */
    $this->post_mentions($args['message'], $post['post_id']);

    /* post wall notifications */
    if ($post['in_wall']) {
      $this->post_notification(['to_user_id' => $post['wall_id'], 'action' => 'wall', 'node_type' => 'post', 'node_url' => $post['post_id']]);
    }

    /* post in_group notification */
    if ($post['in_group'] && !$post['group_approved']) {
      /* send notification to group admin */
      $this->post_notification(['to_user_id' => $_group['group_admin'], 'action' => 'group_post_pending', 'node_type' => $_group['group_title'], 'node_url' => $_group['group_name'] . "-[guid=]" . $post['post_id']]);
    }

    /* post in_event notification */
    if ($post['in_event'] && !$post['event_approved']) {
      /* send notification to event admin */
      $this->post_notification(['to_user_id' => $_event['event_admin'], 'action' => 'event_post_pending', 'node_type' => $_event['event_title'], 'node_url' => $_event['event_id'] . "-[guid=]" . $post['post_id']]);
    }

    /* parse text */
    $post['text_plain'] = htmlentities($post['text'], ENT_QUOTES, 'utf-8');
    $post['text'] = $this->_parse(["text" => $post['text_plain'], "trending_hashtags" => true, "post_id" => $post['post_id']]);

    /* get post colored pattern */
    $post['colored_pattern'] = $this->get_posts_colored_pattern($post['colored_pattern']);

    /* user can manage the post */
    $post['manage_post'] = true;

    /* points balance */
    $this->points_balance("add", $this->_data['user_id'], "post", $post['post_id']);

    /* check if post is pending */
    if ($post['has_approved'] == 0) {
      /* send notification to admins */
      $this->notify_system_admins("pending_post");
    }

    // return
    return $post;
  }


  /**
   * scraper
   * 
   * @param string $url
   * @return array
   */
  public function scraper($url)
  {
    $url_parsed = parse_url($url);
    if (!isset($url_parsed["scheme"])) {
      $url = "http://" . $url;
    }
    $embed = new Embed\Embed();
    $info = $embed->get($url);
    if ($info) {
      $return = [];
      $return['source_url'] = $url;
      $return['source_host'] = $url_parsed['host'];
      $return['source_title'] = $info->title;
      $return['source_text'] = $info->description;
      $return['source_thumbnail'] = remove_urls_spaces($info->image);
      $return['source_html'] = $info->code->html;
      $return['source_provider'] = $info->providerName;
      $return['source_type'] = ($info->code->html && preg_match('/<iframe|blockquote>/', $info->code->html)) ? 'rich' : 'link';
      if ($return['source_type'] === 'rich' && preg_match('/(wp-embedded-content|m3u8|\.mov)/', $info->code->html)) {
        $return['source_type'] = 'link';
      }
      if ($info->providerName == "Twitch") {
        $return['source_html'] = str_replace("meta.tag", $_SERVER['HTTP_HOST'], $info->code->html);
      }
      return $return;
    } else {
      return false;
    }
  }


  /**
   * get_posts_colored_patterns
   * 
   * @return array
   */
  public function get_posts_colored_patterns()
  {
    global $db;
    $patterns = [];
    $get_patterns = $db->query("SELECT * FROM posts_colored_patterns") or _error('SQL_ERROR_THROWEN');
    if ($get_patterns->num_rows > 0) {
      while ($pattern = $get_patterns->fetch_assoc()) {
        $patterns[] = $pattern;
      }
    }
    return $patterns;
  }


  /**
   * get_posts_colored_pattern
   * 
   * @param integer $pattern_id
   * @return array
   */
  public function get_posts_colored_pattern($pattern_id)
  {
    global $db;
    $get_pattern = $db->query(sprintf("SELECT * FROM posts_colored_patterns WHERE pattern_id = %s", secure($pattern_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_pattern->num_rows == 0) {
      return 0;
    }
    return $get_pattern->fetch_assoc();
  }


  /**
   * check_posts_needs_approval
   * 
   * @param integer $user_id
   * @param string $user_type
   * @return boolean
   */
  public function check_posts_needs_approval($user_id, $user_type = "user")
  {
    global $system, $db;
    /* check if approval is enabled (bypass for admins & moderators) */
    if ($system['posts_approval_enabled'] && $this->_data['user_group'] >= 3) {
      $get_approved_posts = $db->query(sprintf("SELECT COUNT(*) as count FROM posts WHERE user_id = %s AND user_type = %s AND has_approved = '1'", secure($user_id, 'int'), secure($user_type))) or _error('SQL_ERROR_THROWEN');
      if ($get_approved_posts->fetch_assoc()['count'] < $system['posts_approval_limit']) {
        return true;
      }
    }
    return false;
  }


  /**
   * parse
   * 
   * @param array $args
   * @return string
   */
  private function _parse($args = [])
  {
    /* validate arguments */
    $text = $args['text'];
    $decode_urls = !isset($args['decode_urls']) ? true : $args['decode_urls'];
    $decode_emojis = !isset($args['decode_emojis']) ? true : $args['decode_emojis'];
    $decode_stickers = !isset($args['decode_stickers']) ? true : $args['decode_stickers'];
    $decode_mentions = !isset($args['decode_mentions']) ? true : $args['decode_mentions'];
    $decode_hashtags = !isset($args['decode_hashtags']) ? true : $args['decode_hashtags'];
    $trending_hashtags = !isset($args['trending_hashtags']) ? false : $args['trending_hashtags'];
    $post_id = !isset($args['post_id']) ? null : $args['post_id'];
    $nl2br = !isset($args['nl2br']) ? true : $args['nl2br'];
    /* decode urls */
    if ($decode_urls) {
      $text = decode_urls($text);
    }
    /* decode emojis */
    if ($decode_emojis) {
      $text = $this->decode_emojis($text);
    }
    /* decode stickers */
    if ($decode_stickers) {
      $text = $this->decode_stickers($text);
    }
    /* decode @mention */
    if ($decode_mentions) {
      $text = $this->decode_mention($text);
    }
    /* decode #hashtag */
    if ($decode_hashtags) {
      $text = $this->decode_hashtags($text, $trending_hashtags, $post_id);
    }
    /* censored words */
    $text = censored_words($text);
    /* nl2br */
    if ($nl2br && $text) {
      $text = nl2br($text);
    }
    return $text;
  }


  /**
   * _check_posts_limit
   * 
   * @return void
   */
  private function _check_posts_limit($handle = 'user', $handle_id = null)
  {
    global $system, $db;
    /* check max posts/hour limit */
    if ($system['max_posts_hour'] > 0 && $this->_data['user_group'] >= 3) {
      $handle = ($handle == "page") ? "page" : "user";
      $where_query = ($handle == "page") ? sprintf("user_id = %s AND user_type = 'page'", secure($handle_id, 'int')) : sprintf("user_id = %s AND user_type = 'user'", secure($this->_data['user_id'], 'int'));
      $check_limit = $db->query("SELECT COUNT(*) as count FROM posts WHERE posts.time >= DATE_SUB(NOW(),INTERVAL 1 HOUR) AND " . $where_query) or _error('SQL_ERROR_THROWEN');
      if ($check_limit->fetch_assoc()['count'] >= $system['max_posts_hour']) {
        modal("MESSAGE", __("Maximum Limit Reached"), __("You have reached the maximum limit of posts/hour, please try again later"));
      }
    }
  }



  /* ------------------------------- */
  /* Posts */
  /* ------------------------------- */

  /**
   * get_posts
   * 
   * @param array $args
   * @return array
   */
  public function get_posts($args = [])
  {
    global $db, $system;
    /* initialize vars */
    $posts = [];
    /* validate arguments */
    $get = !isset($args['get']) ? 'newsfeed' : $args['get'];
    $filter = !isset($args['filter']) ? 'all' : $args['filter'];
    $country = !isset($args['country']) ? 'all' : $args['country'];
    if (!in_array($filter, ['all', '', 'link', 'media', 'live', 'photos', 'map', 'article', 'product', 'funding', 'offer', 'job', 'poll', 'video', 'audio', 'file'])) {
      _error(400);
    }
    $last_post_id = !isset($args['last_post_id']) ? null : $args['last_post_id'];
    if (isset($last_post_id) && !is_numeric($last_post_id)) {
      _error(400);
    }
    $max_results = $system['newsfeed_results'];
    if (isset($args['query'])) {
      $max_results = $system['search_results'];
      if (is_empty($args['query'])) {
        return $posts;
      } else {
        $query = secure($args['query'], 'search', false);
      }
    }
    $offset = !isset($args['offset']) ? 0 : $args['offset'];
    $offset *= $max_results;
    $get_all = !isset($args['get_all']) ? false : true;
    /* prepare query */
    $order_query = "ORDER BY posts.post_id DESC";
    $where_query = "";
    /* get posts */
    switch ($get) {
      case 'newsfeed':
        if (!$this->_logged_in && $query) {
          /* [Case: 1] user not logged in but searhing */
          $where_query .= "WHERE ("; /* [01] start of public search query clause */
          $where_query .= "(posts.text LIKE $query)";
          /* get only public posts [except: group posts & event posts & wall posts] */
          $where_query .= " AND (posts.in_group = '0' AND posts.in_event = '0' AND posts.in_wall = '0' AND posts.privacy = 'public')";
          $where_query .= ")"; /* [01] end of public search query clause */
        } else {
          /* [Case: 2] user logged in or not whatever searching or not */
          /* get viewer user's newsfeed */
          $where_query .= "WHERE ("; /* [02] start of newsfeed clause */
          if ($query) {
            $where_query .= "("; /* [03] start of private search clause */
          }
          $where_query .= "("; /* [04] start of newsfeed sources */
          switch ($system['newsfeed_source']) {
            case 'default':
              if ($this->_logged_in) {
                /* [1] get viewer posts */
                $me = $this->_data['user_id'];
                $where_query .= "(posts.user_id = $me AND posts.user_type = 'user')";
                /* [2] get posts from friends still followed */
                /* viewer friends posts -> authors */
                $where_query .= sprintf(" OR (posts.user_id IN (%s) AND posts.user_type = 'user' AND posts.privacy = 'friends' AND posts.in_group = '0' AND posts.in_event = '0')", $this->spread_ids($this->get_friends_followings_ids()));
                /* viewer friends posts -> their wall posts */
                $where_query .= sprintf(" OR (posts.in_wall = '1' AND posts.wall_id IN (%s) AND posts.user_type = 'user' AND posts.privacy = 'friends')", $this->spread_ids($this->get_friends_followings_ids()));
                /* [3] get posts from followings */
                /* viewer followings posts -> authors */
                $where_query .= sprintf(" OR (posts.user_id IN (%s) AND posts.user_type = 'user' AND posts.privacy = 'public' AND posts.in_group = '0' AND posts.in_event = '0')", $this->spread_ids($this->_data['followings_ids']));
                /* viewer followings posts -> their wall posts */
                $where_query .= sprintf(" OR (posts.in_wall = '1' AND posts.wall_id IN (%s) AND posts.user_type = 'user' AND posts.privacy = 'public')", $this->spread_ids($this->_data['followings_ids']));
                /* [4] get viewer liked pages posts */
                $where_query .= sprintf(" OR (posts.user_id IN (%s) AND posts.user_type = 'page')", $this->spread_ids($this->get_pages_ids()));
                /* [5] get groups (memberhsip approved only) posts & exclude the viewer posts */
                $where_query .= sprintf(" OR (posts.group_id IN (%s) AND posts.in_group = '1' AND posts.group_approved = '1' AND posts.user_id != %s)", $this->spread_ids($this->get_groups_ids(true)), $me);
                /* [6] get events posts & exclude the viewer posts */
                $where_query .= sprintf(" OR (posts.event_id IN (%s) AND posts.in_event = '1' AND posts.event_approved = '1' AND posts.user_id != %s)", $this->spread_ids($this->get_events_ids()), $me);
              } else {
                /* get all posts (exclude any post in a group or event and not approved) */
                $where_query .= " posts.privacy = 'public' AND ((posts.in_group = '0' AND posts.in_event = '0') OR (posts.in_group = '1' AND posts.group_approved = '1') OR (posts.in_event = '1' AND posts.event_approved = '1')) ";
              }
              break;

            case 'all_posts':
              /* get all posts (exclude any post in a group or event and not approved) */
              $where_query .= " (posts.in_group = '0' AND posts.in_event = '0') OR (posts.in_group = '1' AND posts.group_approved = '1') OR (posts.in_event = '1' AND posts.event_approved = '1')";
              break;
          }
          $where_query .= ")"; /* [04] end of newsfeed sources */
          if ($query) {
            $where_query .= " AND (posts.text LIKE $query)";
            $where_query .= ")"; /* [03] end of private search clause */
            $where_query .= " OR ("; /* [05] start of public search query clause */
            $where_query .= "(posts.text LIKE $query)";
            /* get only public posts [except: group posts & event posts & wall posts] */
            $where_query .= " AND (posts.in_group = '0' AND posts.in_event = '0' AND posts.in_wall = '0' AND posts.privacy = 'public')";
            $where_query .= ")"; /* [05] end of public search query clause */
          }
          $where_query .= ")"; /* [02] end of newsfeed clause */
        }
        break;

      case 'posts_profile':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        /* get target user's posts */
        /* check if there is a viewer user */
        if ($this->_logged_in) {
          /* check if the target user is the viewer */
          if ($id == $this->_data['user_id']) {
            /* get all posts */
            $where_query .= "WHERE (";
            /* get all target posts */
            $where_query .= "(posts.user_id = $id AND posts.user_type = 'user')";
            /* get target wall posts (from others to the target) */
            $where_query .= " OR (posts.wall_id = $id AND posts.in_wall = '1')";
            $where_query .= ")";
          } else {
            /* check if the viewer & the target user are friends */
            if ($this->friendship_approved($id)) {
              /* Yes they are friends */
              $where_query .= "WHERE (";
              /* get all target posts [except: group posts & event posts & hidden posts & anonymous posts] */
              $where_query .= "(posts.user_id = $id AND posts.user_type = 'user' AND posts.in_group = '0' AND posts.in_event = '0' AND posts.privacy != 'me' AND posts.is_hidden = '0' AND posts.is_anonymous = '0')";
              /* get target wall posts (from others to the target) [except: hidden posts] */
              $where_query .= " OR (posts.wall_id = $id AND posts.in_wall = '1' AND posts.is_hidden = '0')";
              $where_query .= ")";
            } else {
              /* No they are not friends */
              /* get only public posts [except: wall posts (both) & hidden posts &  & anonymous posts] */
              /* note: we didn't except group posts & event posts as they are not public already */
              $where_query .= "WHERE (posts.user_id = $id AND posts.user_type = 'user' AND posts.in_wall = '0' AND posts.privacy = 'public' AND posts.is_hidden = '0' AND posts.is_anonymous = '0')";
            }
          }
        } else {
          /* get only public posts [except: wall posts (both) & hidden posts] */
          /* note: we didn't except group posts & event posts as they are not public already */
          $where_query .= "WHERE (posts.user_id = $id AND posts.user_type = 'user' AND posts.in_wall = '0' AND posts.privacy = 'public' AND posts.is_hidden = '0' AND posts.is_anonymous = '0')";
        }
        break;

      case 'posts_page':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        $where_query .= "WHERE (posts.user_id = $id AND posts.user_type = 'page')";
        break;

      case 'posts_group':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        $where_query .= "WHERE (posts.group_id = $id AND posts.in_group = '1' AND posts.group_approved = '1')";
        break;

      case 'posts_group_pending':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        $me = $this->_data['user_id'];
        $where_query .= "WHERE (posts.group_id = $id AND posts.in_group = '1' AND posts.group_approved = '0' AND posts.user_id = $me)";
        break;

      case 'posts_group_pending_all':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        $where_query .= "WHERE (posts.group_id = $id AND posts.in_group = '1' AND posts.group_approved = '0')";
        break;

      case 'posts_event':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        $where_query .= "WHERE (posts.event_id = $id AND posts.in_event = '1' AND posts.event_approved = '1')";
        break;

      case 'posts_event_pending':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        $me = $this->_data['user_id'];
        $where_query .= "WHERE (posts.event_id = $id AND posts.in_event = '1' AND posts.event_approved = '0' AND posts.user_id = $me)";
        break;

      case 'posts_event_pending_all':
        if (isset($args['id']) && !is_numeric($args['id'])) {
          _error(400);
        }
        $id = $args['id'];
        $where_query .= "WHERE (posts.event_id = $id AND posts.in_event = '1' AND posts.event_approved = '0')";
        break;

      case 'popular':
        $where_query .= "WHERE posts.privacy = 'public'"; /* get all popular public posts */
        $order_query = "ORDER BY posts.comments DESC, posts.reaction_like_count DESC, posts.reaction_love_count DESC, posts.reaction_haha_count DESC, posts.reaction_yay_count DESC, posts.reaction_wow_count DESC, posts.reaction_sad_count DESC, posts.reaction_angry_count DESC, posts.shares DESC"; /* order by comments, reactions & shares */
        break;

      case 'discover':
        $where_query .= sprintf("WHERE posts.privacy = 'public' AND !(posts.user_id = %s AND posts.user_type = 'user')", secure($this->_data['user_id'], 'int'));
        /* exclude posts from viewer friends */
        /* viewer friends posts -> authors */
        $where_query .= sprintf(" AND !(posts.user_id IN (%s) AND posts.user_type = 'user')", $this->spread_ids($this->get_friends_followings_ids()));
        /* exclude posts from viewer followings */
        $where_query .= sprintf(" AND !(posts.user_id IN (%s) AND posts.user_type = 'user')", $this->spread_ids($this->_data['followings_ids']));
        /* exclude posts from viewer liked pages */
        $where_query .= sprintf(" AND !(posts.user_id IN (%s) AND posts.user_type = 'page')", $this->spread_ids($this->get_pages_ids()));
        /* exclude posts from viewer joined groups */
        $where_query .= sprintf(" AND !(posts.group_id IN (%s) AND posts.in_group = '1')", $this->spread_ids($this->get_groups_ids(true)));
        /* exclude posts from viewer joined events */
        $where_query .= sprintf(" AND !(posts.event_id IN (%s) AND posts.in_event = '1')", $this->spread_ids($this->get_events_ids()));
        break;

      case 'saved':
        $id = $this->_data['user_id'];
        $where_query .= "INNER JOIN posts_saved ON posts.post_id = posts_saved.post_id WHERE (posts_saved.user_id = $id)";
        $order_query = "ORDER BY posts_saved.time DESC"; /* order by saved time not by post_id */
        break;

      case 'memories':
        $id = $this->_data['user_id'];
        $where_query .= "WHERE DATE_FORMAT(DATE(posts.time),'%m-%d') = DATE_FORMAT(CURDATE(),'%m-%d') AND posts.time < (NOW() - INTERVAL 1 DAY) AND posts.user_id = $id AND posts.user_type = 'user'";
        break;

      case 'boosted':
        $id = $this->_data['user_id'];
        $where_query .= "WHERE (posts.boosted = '1' AND posts.boosted_by = $id)";
        break;

      default:
        _error(400);
        break;
    }
    /* exclude viewer hidden posts from results */
    $where_query .= sprintf(" AND (posts.post_id NOT IN (SELECT post_id FROM posts_hidden WHERE user_id = %s))", secure($this->_data['user_id'], 'int'));
    /* filter posts */
    if ($filter != "all") {
      $where_query .= " AND (posts.post_type = '$filter')";
    }
    /* exclude banned users posts */
    $author_join = " LEFT JOIN users AS user_post_author ON posts.user_type = 'user' AND posts.user_id = user_post_author.user_id AND user_post_author.user_banned = '0' LEFT JOIN pages AS page_post_author ON posts.user_type = 'page' AND posts.user_id = page_post_author.page_id ";
    /* exclude processing posts */
    $where_query .= " AND (posts.processing != '1')";
    /* newsfeed location filter */
    if ($system['newsfeed_location_filter_enabled'] && $country != "all") {
      $where_query .= " AND ( (posts.user_type = 'user' AND user_post_author.user_country = '$country') OR (posts.user_type = 'page' AND page_post_author.page_country = '$country') )";
    }
    /* prepare approval system */
    if ($system['posts_approval_enabled']) {
      $where_query .= " AND (posts.pre_approved = '1' OR posts.has_approved = '1') ";
    }
    /* get posts */
    if ($last_post_id != null && $get != 'popular' && $get != 'saved' && $get != 'memories') { /* excluded as not ordered by post_id */
      $get_posts = $db->query(sprintf("SELECT * FROM (SELECT posts.post_id FROM posts " . $author_join . $where_query . ") posts WHERE posts.post_id > %s ORDER BY posts.post_id DESC", secure($last_post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($max_results, 'int', false)); /* get_all for cases like download user's posts */
      $get_posts = $db->query("SELECT posts.post_id FROM posts " . $author_join . $where_query . " " . $order_query . " " . $limit_statement) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_posts->num_rows > 0) {
      while ($post = $get_posts->fetch_assoc()) {
        $post = $this->get_post($post['post_id'], true, true); /* $full_details = true, $pass_privacy_check = true */
        if ($post) {
          $posts[] = $post;
        }
      }
    }
    return $posts;
  }

  /**
   * get_posts_count
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return integer
   */
  public function get_posts_count($node_id, $node_type)
  {
    global $db;
    $node_id = secure($node_id, 'int');
    switch ($node_type) {
      case 'user':
        $where_statement = "WHERE posts.user_id = $node_id AND posts.user_type = 'user' AND posts.in_group = '0' AND posts.in_event = '0'";
        break;

      case 'page':
        $where_statement = "WHERE posts.user_id = $node_id AND posts.user_type = 'page'";
        break;

      case 'group':
        $where_statement = "WHERE posts.group_id = $node_id AND posts.in_group = '1'";
        break;

      case 'event':
        $where_statement = "WHERE posts.event_id = $node_id AND posts.in_event = '1'";
        break;

      default:
        _error(400);
        break;
    }
    $where_statement .= " AND (posts.processing != '1') AND (posts.pre_approved = '1' OR posts.has_approved = '1') ";
    $get = $db->query("SELECT COUNT(*) as count FROM posts " . $where_statement) or _error('SQL_ERROR_THROWEN');
    $count = $get->fetch_assoc();
    return $count['count'];
  }


  /**
   * get_post
   * 
   * @param integer $post_id
   * @param boolean $get_comments
   * @param boolean $pass_privacy_check
   * @param boolean $pass_views
   * @return array
   */
  public function get_post($post_id, $get_comments = true, $pass_privacy_check = false, $pass_views = false)
  {
    global $db, $system, $date;

    $post = $this->_check_post($post_id, $pass_privacy_check);
    if (!$post) {
      return false;
    }

    /* og-meta tags */
    $post['og_title'] = ($post['is_anonymous']) ? __("Anonymous") : $post['post_author_name'];
    if ($post['needs_payment']) {
      $post['og_title'] .= ($post['paid_text'] != "") ? " - " . $post['paid_text'] : "";
      $post['og_description'] = $post['paid_text'];
    }

    if (!($post['needs_subscription'] || $post['needs_payment'] || $post['needs_age_verification'])) {
      /* parse text */
      $post['text_plain'] = $post['text'];
      $post['text'] = $this->_parse(["text" => $post['text_plain']]);

      /* og-meta tags */
      $post['og_title'] .= ($post['text_plain'] != "") ? " - " . $post['text_plain'] : "";
      $post['og_description'] = $post['text_plain'];

      /* post type */
      /* check if post is a shared post */
      if ($post['post_type'] == 'shared') {
        /* get origin post */
        $post['origin'] = $this->get_post($post['origin_id'], false);
        /* check if origin post has been deleted */
        if (!$post['origin']) {
          return false;
        }
      }
      /* check if post is a status post */
      if ($post['post_type'] == '') {
        /* get colored pattern */
        if ($post['colored_pattern']) {
          $post['colored_pattern'] = $this->get_posts_colored_pattern($post['colored_pattern']);
        }
      }
      /* check if post is a link post */
      if ($post['post_type'] == 'link') {
        /* get link */
        $get_link = $db->query(sprintf("SELECT * FROM posts_links WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if link has been deleted */
        if ($get_link->num_rows == 0) {
          return false;
        }
        $post['link'] = $get_link->fetch_assoc();
        /* remove spaces from source_thumbnail */
        $post['link']['source_thumbnail'] = remove_urls_spaces($post['link']['source_thumbnail']);

        /* og-meta tags */
        $post['og_title'] = $post['link']['source_title'];
        $post['og_image'] = $post['link']['source_thumbnail'];
      }
      /* check if post is a media post */
      if ($post['post_type'] == 'media') {
        /* get media */
        $get_media = $db->query(sprintf("SELECT * FROM posts_media WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if media has been deleted */
        if ($get_media->num_rows == 0) {
          return false;
        }
        $post['media'] = $get_media->fetch_assoc();
        /* og-meta tags */
        $post['og_title'] = $post['media']['source_title'];
        $post['og_image'] = $post['media']['source_thumbnail'];
      }
      /* check if post is a live post */
      if ($post['post_type'] == 'live') {
        /* get live details */
        $get_live = $db->query(sprintf("SELECT * FROM posts_live WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if live has been deleted */
        if ($get_live->num_rows == 0) {
          return false;
        }
        $post['live'] = $get_live->fetch_assoc();
        /* generate new audience agora (uid|token) */
        $agora = $this->agora_token_builder(false, $post['live']['agora_channel_name']);
        $post['live']['agora_audience_uid'] = $agora['uid'];
        $post['live']['agora_audience_token'] = $agora['token'];
      }
      /* check if post post has photos */
      if (in_array($post['post_type'], ['photos', 'album', 'profile_picture', 'profile_cover', 'page_picture', 'page_cover', 'group_picture', 'group_cover', 'event_cover', 'combo'])) {
        /* get photos */
        $get_photos = $db->query(sprintf("SELECT * FROM posts_photos WHERE post_id = %s ORDER BY photo_id ASC", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        $post['photos_num'] = $get_photos->num_rows;
        /* check if photos has been deleted */
        if ($post['photos_num'] == 0 && $post['post_type'] != 'combo') {
          return false;
        } else {
          while ($post_photo = $get_photos->fetch_assoc()) {
            $post['photos'][] = $post_photo;
          }
          if ($post['post_type'] == 'album') {
            $post['album'] = $this->get_album($post['photos'][0]['album_id'], false);
            if (!$post['album']) {
              return false;
            }
            /* og-meta tags */
            $post['og_title'] = $post['album']['title'];
            $post['og_image'] = $post['album']['cover'];
          } else {
            /* og-meta tags */
            $post['og_image'] = $system['system_uploads'] . '/' . $post['photos'][0]['source'];
          }
        }
      }
      /* check if post is a article post */
      if ($post['post_type'] == 'article') {
        /* get article */
        $get_article = $db->query(sprintf("SELECT posts_articles.*, blogs_categories.category_name FROM posts_articles LEFT JOIN blogs_categories ON posts_articles.category_id = blogs_categories.category_id WHERE posts_articles.post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if article has been deleted */
        if ($get_article->num_rows == 0) {
          return false;
        }
        $post['article'] = $get_article->fetch_assoc();
        /* check blogs permissions */
        $post['needs_pro_package'] = false;
        /* check if user is not the author of the post or not admin */
        if ($this->_data['user_id'] != $post['post_author_id'] && !$this->_is_admin) {
          /* check if user can read blogs OR article category in user_package_blogs_categories_ids */
          if (!$this->_data['can_read_articles'] || ($system['packages_enabled'] && $this->_data['user_subscribed'] && $this->_data['allowed_blogs_categories'] > 0)) {
            if ($post['article']['category_id'] && !in_array($post['article']['category_id'], $this->_data['user_package_blogs_categories_ids'])) {
              $post['needs_pro_package'] = true;
              return $post;
            }
          }
        }
        $post['article']['category_name'] = ($post['article']['category_name']) ? $post['article']['category_name'] : __("Uncategorized");
        $post['article']['category_url'] = get_url_text($post['article']['category_name']);
        $post['article']['parsed_cover'] = get_picture($post['article']['cover'], 'article');
        $post['article']['title_url'] = get_url_text($post['article']['title']);
        $post['article']['parsed_text'] = htmlspecialchars_decode($post['article']['text'], ENT_QUOTES);
        $post['article']['text_snippet'] = get_snippet_text($post['article']['text']);
        $tags = (!is_empty($post['article']['tags'])) ? explode(',', $post['article']['tags']) : [];
        $post['article']['parsed_tags'] = array_map('get_tag', $tags);
        /* og-meta tags */
        $post['og_title'] = $post['article']['title'];
        $post['og_description'] = $post['article']['text_snippet'];
        $post['og_image'] = $post['article']['parsed_cover'];
      }
      /* check if post is a product post */
      if ($post['post_type'] == 'product') {
        /* get product */
        $get_product = $db->query(sprintf("SELECT posts_products.*, market_categories.category_name FROM posts_products INNER JOIN market_categories ON posts_products.category_id = market_categories.category_id WHERE posts_products.post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if product has been deleted */
        if ($get_product->num_rows == 0) {
          return false;
        }
        $post['product'] = $get_product->fetch_assoc();
        $post['product']['category_url'] = get_url_text($post['product']['category_name']);
        /* get photos */
        $get_photos = $db->query(sprintf("SELECT * FROM posts_photos WHERE post_id = %s ORDER BY photo_id DESC", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        $post['photos_num'] = $get_photos->num_rows;
        /* check if photos has been deleted */
        if ($post['photos_num'] > 0) {
          while ($post_photo = $get_photos->fetch_assoc()) {
            $post['photos'][] = $post_photo;
          }
          /* og-meta tags */
          $post['og_image'] = $system['system_uploads'] . '/' . $post['photos'][0]['source'];
        }
        /* og-meta tags */
        $post['og_title'] = $post['product']['name'];
      }
      /* check if post is a funding post */
      if ($post['post_type'] == 'funding') {
        /* get funding */
        $get_funding = $db->query(sprintf("SELECT * FROM posts_funding WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if funding has been deleted */
        if ($get_funding->num_rows == 0) {
          return false;
        }
        $post['funding'] = $get_funding->fetch_assoc();
        $post['funding']['funding_completion'] = round($post['funding']['raised_amount'] * (100 / $post['funding']['amount']));
        /* og-meta tags */
        $post['og_title'] = $post['funding']['title'];
        $post['og_image'] = $system['system_uploads'] . '/' . $post['funding']['cover_image'];
      }
      /* check if post is a offer post */
      if ($post['post_type'] == 'offer') {
        /* get offer */
        $get_offer = $db->query(sprintf("SELECT * FROM posts_offers WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if offer has been deleted */
        if ($get_offer->num_rows == 0) {
          return false;
        }
        $post['offer'] = $get_offer->fetch_assoc();
        /* prepare offer title */
        switch ($post['offer']['discount_type']) {
          case 'discount_percent':
            $post['offer']['meta_title'] = $post['offer']['discount_percent'] . "% " . __("Off") . " " . $post['offer']['title'];
            break;

          case 'discount_amount':
            $post['offer']['meta_title'] = print_money($post['offer']['discount_amount']) . " " . __("Off") . " " . $post['offer']['title'];
            break;

          case 'buy_get_discount':
            $post['offer']['meta_title'] = __("Buy") . " " . $post['offer']['buy_x'] . " " . __("Get") . " " . $post['offer']['get_y'] . " " . $post['offer']['title'];
            break;

          case 'spend_get_off':
            $post['offer']['meta_title'] = __("Spend") . " " . print_money($post['offer']['spend_x']) . " " . __("Get") . " " . print_money($post['offer']['amount_y']) . " " . __("Off") . " " . $post['offer']['title'];
            break;

          case 'free_shipping':
            $post['offer']['meta_title'] = __("Free Shipping") . " " . $post['offer']['title'];
            break;
        }
        /* og-meta tags */
        $post['og_title'] = $post['offer']['meta_title'];
        $post['og_image'] = $system['system_uploads'] . '/' . $post['offer']['thumbnail'];
        /* get photos */
        $get_photos = $db->query(sprintf("SELECT * FROM posts_photos WHERE post_id = %s ORDER BY photo_id DESC", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        $post['photos_num'] = $get_photos->num_rows;
        /* check if photos has been deleted */
        if ($post['photos_num'] > 0) {
          while ($post_photo = $get_photos->fetch_assoc()) {
            $post['photos'][] = $post_photo;
          }
          /* set offer thumbnail */
          $post['offer']['thumbnail'] = $post['photos'][0]['source'];
          /* og-meta tags */
          $post['og_image'] = $system['system_uploads'] . '/' . $post['photos'][0]['source'];
        }
      }
      /* check if post is a job post */
      if ($post['post_type'] == 'job') {
        /* get job */
        $get_job = $db->query(sprintf("SELECT * FROM posts_jobs WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if job has been deleted */
        if ($get_job->num_rows == 0) {
          return false;
        }
        $post['job'] = $get_job->fetch_assoc();
        /* prepare job meta data */
        switch ($post['job']['pay_salary_per']) {
          case 'per_hour':
            $post['job']['pay_salary_per_meta'] = __("Hour");
            break;

          case 'per_day':
            $post['job']['pay_salary_per_meta'] = __("Day");
            break;

          case 'per_week':
            $post['job']['pay_salary_per_meta'] = __("Week");
            break;

          case 'per_month':
            $post['job']['pay_salary_per_meta'] = __("Month");
            break;

          case 'per_year':
            $post['job']['pay_salary_per_meta'] = __("Year");
            break;
        }
        switch ($post['job']['type']) {
          case 'full_time':
            $post['job']['type_meta'] = __("Full Time");
            break;

          case 'part_time':
            $post['job']['type_meta'] = __("Part Time");
            break;

          case 'internship':
            $post['job']['type_meta'] = __("Internship");
            break;

          case 'volunteer':
            $post['job']['type_meta'] = __("Volunteer");
            break;

          case 'contract':
            $post['job']['type_meta'] = __("Contract");
            break;
        }
        /* prepare job questions */
        if ($post['job']['question_1_type'] == "multiple_choice" && $post['job']['question_1_title']) {
          $post['job']['question_1_options'] = explode(PHP_EOL, $post['job']['question_1_choices']);
        }
        if ($post['job']['question_2_type'] == "multiple_choice" && $post['job']['question_2_title']) {
          $post['job']['question_2_options'] = explode(PHP_EOL, $post['job']['question_2_choices']);
        }
        if ($post['job']['question_3_type'] == "multiple_choice" && $post['job']['question_3_title']) {
          $post['job']['question_3_options'] = explode(PHP_EOL, $post['job']['question_3_choices']);
        }
        /* get candidates count */
        $post['job']['candidates_count'] = $this->get_total_job_candidates($post['post_id']);
        /* og-meta tags */
        $post['og_title'] = $post['job']['title'];
        $post['og_image'] = $system['system_uploads'] . '/' . $post['job']['cover_image'];
      }
      /* check if post is a poll post */
      if ($post['post_type'] == 'poll') {
        /* get poll */
        $get_poll = $db->query(sprintf("SELECT * FROM posts_polls WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if poll has been deleted */
        if ($get_poll->num_rows == 0) {
          return false;
        }
        $post['poll'] = $get_poll->fetch_assoc();
        /* get poll options */
        $get_poll_options = $db->query(sprintf("SELECT posts_polls_options.option_id, posts_polls_options.text FROM posts_polls_options WHERE poll_id = %s", secure($post['poll']['poll_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_poll_options->num_rows == 0) {
          return false;
        }
        while ($poll_option = $get_poll_options->fetch_assoc()) {
          /* get option votes */
          $get_option_votes = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_polls_options_users WHERE option_id = %s", secure($poll_option['option_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          $poll_option['votes'] = $get_option_votes->fetch_assoc()['count'];
          /* check if viewer voted */
          $poll_option['checked'] = false;
          if ($this->_logged_in) {
            $check = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_polls_options_users WHERE user_id = %s AND option_id = %s", secure($this->_data['user_id'], 'int'), secure($poll_option['option_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($check->fetch_assoc()['count'] > 0) {
              $poll_option['checked'] = true;
            }
          }
          $post['poll']['options'][] = $poll_option;
        }
      }
      /* check if post is a video post */
      if ($post['post_type'] == 'video' || $post['post_type'] == 'combo') {
        /* get video */
        $get_video = $db->query(sprintf("SELECT posts_videos.*, posts_videos_categories.category_name FROM posts_videos LEFT JOIN posts_videos_categories ON posts_videos.category_id = posts_videos_categories.category_id WHERE posts_videos.post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if video has been deleted */
        if ($get_video->num_rows == 0 && $post['post_type'] != 'combo') {
          return false;
        } else {
          $post['video'] = $get_video->fetch_assoc();
          /* og-meta tags */
          if ($post['video']['thumbnail']) {
            $post['og_image'] = $system['system_uploads'] . '/' . $post['video']['thumbnail'];
          }
          /* check videos permissions */
          $post['needs_pro_package'] = false;
          /* check if user is not the author of the post or not admin */
          if ($this->_data['user_id'] != $post['post_author_id'] && !$this->_is_admin) {
            /* check if user can watch videos OR video category in user_package_videos_categories_ids */
            if (!$this->_data['can_watch_videos'] || ($system['packages_enabled'] && $this->_data['user_subscribed'] && $this->_data['allowed_videos_categories'] > 0)) {
              if ($post['video']['category_id'] && !in_array($post['video']['category_id'], $this->_data['user_package_videos_categories_ids'])) {
                $post['needs_pro_package'] = true;
                return $post;
              }
            }
          }
        }
      }
      /* check if post is a audio post */
      if ($post['post_type'] == 'audio' || $post['post_type'] == 'combo') {
        /* get audio */
        $get_audio = $db->query(sprintf("SELECT * FROM posts_audios WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if audio has been deleted */
        if ($get_audio->num_rows == 0 && $post['post_type'] != 'combo') {
          return false;
        } else {
          $post['audio'] = $get_audio->fetch_assoc();
        }
      }
      /* check if post is a file post */
      if ($post['post_type'] == 'file' || $post['post_type'] == 'combo') {
        /* get file */
        $get_file = $db->query(sprintf("SELECT * FROM posts_files WHERE post_id = %s", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if file has been deleted */
        if ($get_file->num_rows == 0 && $post['post_type'] != 'combo') {
          return false;
        } else {
          $post['file'] = $get_file->fetch_assoc();
        }
      }

      /* custom fields */
      if (in_array($post['post_type'], ['product', 'offer', 'job'])) {
        $post['custom_fields'] = $this->get_custom_fields(["for" => $post['post_type'], "get" => "profile", "node_id" => $post['post_id']]);
      }

      /* post feeling */
      if (!is_empty($post['feeling_action']) && !is_empty($post['feeling_value'])) {
        if ($post['feeling_action'] != "Feeling") {
          $_feeling_icon = get_feeling_icon($post['feeling_action'], get_feelings());
        } else {
          $_feeling_icon = get_feeling_icon($post['feeling_value'], get_feelings_types());
        }
        if ($_feeling_icon) {
          $post['feeling_icon'] = $_feeling_icon;
        }
      }

      /* get post comments */
      if ($get_comments) {
        if ($post['comments'] > 0) {
          $post['post_comments'] = $this->get_comments($post['post_id'], 0, true, true, $post);
        }
      }

      /* update post views */
      if ($system['posts_views_enabled'] && !$pass_views) {
        $counted = false;
        /* check posts views type */
        if ($system['posts_views_type'] == "all") {
          $counted = true;
        } else {
          /* check if view logged in */
          if ($this->_logged_in) {
            /* check if posts_views record exists */
            $check = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_views WHERE post_id = %s AND user_id = %s", secure($post['post_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($check->fetch_assoc()['count'] == 0) {
              $db->query(sprintf("INSERT INTO posts_views (post_id, user_id, view_date) VALUES (%s, %s, %s)", secure($post['post_id'], 'int'), secure($this->_data['user_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
              $counted = true;
            }
          } else {
            /* check if posts_views record exists */
            $check = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_views WHERE post_id = %s AND guest_ip = %s", secure($post['post_id'], 'int'), secure(get_user_ip()))) or _error('SQL_ERROR_THROWEN');
            if ($check->fetch_assoc()['count'] == 0) {
              $db->query(sprintf("INSERT INTO posts_views (post_id, guest_ip, view_date) VALUES (%s, %s, %s)", secure($post['post_id'], 'int'), secure(get_user_ip()), secure($date))) or _error('SQL_ERROR_THROWEN');
              $counted = true;
            }
          }
        }
        if ($counted) {
          /* count post views */
          $db->query(sprintf("UPDATE posts SET views = views + 1 WHERE post_id = %s", secure($post['post_id'])));
          /* points balance */
          $this->points_balance("add", $this->_data['user_id'], "post_view", $post['post_id']);
        }
      }
    }

    return $post;
  }


  /**
   * get_boosted_post
   * 
   * @return array
   */
  public function get_boosted_post()
  {
    global $db, $system;
    $get_random_post = $db->query(sprintf("SELECT post_id FROM posts WHERE boosted = '1' AND post_id NOT IN (SELECT post_id FROM posts_hidden WHERE user_id = %s) ORDER BY RAND() LIMIT 1", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_random_post->num_rows == 0) {
      return false;
    }
    $random_post = $get_random_post->fetch_assoc();
    return $this->get_post($random_post['post_id'], true, true);
  }


  /**
   * who_reacts
   * 
   * @param array $args
   * @return array
   */
  public function who_reacts($args = [])
  {
    global $db, $system;
    /* initialize arguments */
    $post_id = !isset($args['post_id']) ? null : $args['post_id'];
    $photo_id = !isset($args['photo_id']) ? null : $args['photo_id'];
    $comment_id = !isset($args['comment_id']) ? null : $args['comment_id'];
    $reaction_type = !isset($args['reaction_type']) ? 'all' : $args['reaction_type'];
    /* check reation type */
    if (!in_array($reaction_type, ['all', 'like', 'love', 'haha', 'yay', 'wow', 'sad', 'angry'])) {
      _error(403);
    }
    $offset = !isset($args['offset']) ? 0 : $args['offset'];
    /* initialize vars */
    $users = [];
    $offset *= $system['max_results'];
    if ($post_id != null) {
      /* where statement */
      $where_statement = ($reaction_type == "all") ? "" : sprintf("AND posts_reactions.reaction = %s", secure($reaction_type));
      /* get users who like the post */
      $get_users = $db->query(sprintf('SELECT posts_reactions.reaction, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM posts_reactions INNER JOIN users ON (posts_reactions.user_id = users.user_id) WHERE posts_reactions.post_id = %s ' . $where_statement . ' LIMIT %s, %s', secure($post_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    } elseif ($photo_id != null) {
      /* where statement */
      $where_statement = ($reaction_type == "all") ? "" : sprintf("AND posts_photos_reactions.reaction = %s", secure($reaction_type));
      /* get users who like the photo */
      $get_users = $db->query(sprintf('SELECT posts_photos_reactions.reaction, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM posts_photos_reactions INNER JOIN users ON (posts_photos_reactions.user_id = users.user_id) WHERE posts_photos_reactions.photo_id = %s ' . $where_statement . ' LIMIT %s, %s', secure($photo_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    } else {
      /* where statement */
      $where_statement = ($reaction_type == "all") ? "" : sprintf("AND posts_comments_reactions.reaction = %s", secure($reaction_type));
      /* get users who like the comment */
      $get_users = $db->query(sprintf('SELECT posts_comments_reactions.reaction, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM posts_comments_reactions INNER JOIN users ON (posts_comments_reactions.user_id = users.user_id) WHERE posts_comments_reactions.comment_id = %s ' . $where_statement . ' LIMIT %s, %s', secure($comment_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_users->num_rows > 0) {
      while ($_user = $get_users->fetch_assoc()) {
        $_user['user_picture'] = get_picture($_user['user_picture'], $_user['user_gender']);
        /* get the connection between the viewer & the target */
        $_user['connection'] = $this->connection($_user['user_id']);
        /* get mutual friends count */
        $_user['mutual_friends_count'] = $this->get_mutual_friends_count($_user['user_id']);
        $users[] = $_user;
      }
    }
    return $users;
  }


  /**
   * who_shares
   * 
   * @param integer $post_id
   * @param integer $offset
   * @return array
   */
  public function who_shares($post_id, $offset = 0)
  {
    global $db, $system;
    $posts = [];
    $offset *= $system['max_results'];
    $get_posts = $db->query(sprintf('SELECT posts.post_id FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE posts.post_type = "shared" AND posts.origin_id = %s LIMIT %s, %s', secure($post_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_posts->num_rows > 0) {
      while ($post = $get_posts->fetch_assoc()) {
        $post = $this->get_post($post['post_id']);
        if ($post) {
          $posts[] = $post;
        }
      }
    }
    return $posts;
  }


  /**
   * who_donates
   * 
   * @param integer $post_id
   * @param integer $offset
   * @return array
   */
  public function who_donates($post_id, $offset = 0)
  {
    global $db, $system;
    $donors = [];
    $offset *= $system['max_results'];
    $get_donors = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_picture, users.user_gender, posts_funding_donors.donation_amount, posts_funding_donors.donation_time FROM posts_funding_donors INNER JOIN users ON posts_funding_donors.user_id = users.user_id WHERE posts_funding_donors.post_id = %s ORDER BY posts_funding_donors.donation_id DESC LIMIT %s, %s", secure($post_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    while ($donor = $get_donors->fetch_assoc()) {
      $donor['user_picture'] = get_picture($donor['user_picture'], $donor['user_gender']);
      /* get the connection between the viewer & the target */
      $donor['connection'] = $this->connection($donor['user_id']);
      $donors[] = $donor;
    }
    return $donors;
  }


  /**
   * who_votes
   * 
   * @param integer $post_id
   * @param integer $offset
   * @return array
   */
  public function who_votes($option_id, $offset = 0)
  {
    global $db, $system;
    $voters = [];
    $offset *= $system['max_results'];
    $get_voters = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_picture, users.user_gender FROM posts_polls_options_users INNER JOIN users ON posts_polls_options_users.user_id = users.user_id WHERE option_id = %s LIMIT %s, %s", secure($option_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    while ($voter = $get_voters->fetch_assoc()) {
      $voter['user_picture'] = get_picture($voter['user_picture'], $voter['user_gender']);
      /* get the connection between the viewer & the target */
      $voter['connection'] = $this->connection($voter['user_id']);
      $voters[] = $voter;
    }
    return $voters;
  }


  /**
   * _check_post
   * 
   * @param integer $id
   * @param boolean $pass_privacy_check
   * @param boolean $full_details
   * @return array|false
   */
  private function _check_post($id, $pass_privacy_check = false, $full_details = true)
  {
    global $db, $system;

    /* get post */
    $get_post = $db->query(sprintf("SELECT posts.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_picture_id, users.user_cover, users.user_cover_id, users.user_verified, users.user_subscribed, users.user_pinned_post, users.user_banned, users.user_monetization_enabled, users.user_monetization_min_price, users.user_monetization_plans, packages.name as package_name, pages.*, `groups`.*, `events`.* FROM posts LEFT JOIN users ON posts.user_id = users.user_id AND posts.user_type = 'user' LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN pages ON posts.user_id = pages.page_id AND posts.user_type = 'page' LEFT JOIN `groups` ON posts.in_group = '1' AND posts.group_id = `groups`.group_id LEFT JOIN `events` ON posts.in_event = '1' AND posts.event_id = `events`.event_id WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts.post_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_post->num_rows == 0) {
      return false;
    }
    $post = $get_post->fetch_assoc();
    /* check if the page has been deleted */
    if ($post['user_type'] == "page" && !$post['page_admin']) {
      return false;
    }
    /* check if there is any blocking between the viewer & the post author */
    if ($post['user_type'] == "user" && $this->blocked($post['user_id'])) {
      return false;
    }
    /* check if the user is banned */
    if ($post['user_type'] == "user" && $post['user_banned']) {
      return false;
    }
    /* check if the post is processing */
    if ($post['processing'] == '1') {
      return false;
    }

    /* get the author id */
    $post['author_id'] = ($post['user_type'] == "page") ? $post['page_admin'] : $post['user_id'];

    /* check if the post is pending (bypass the admins & moderators & post author) */
    $post['is_pending'] = $system['posts_approval_enabled'] && ($post['pre_approved'] == '0' && $post['has_approved'] == 0);
    if ($post['is_pending']) {
      if (!$this->_logged_in) {
        return false;
      }
      if ($this->_data['user_group'] >= 3 && $post['author_id'] != $this->_data['user_id']) {
        return false;
      }
    }

    /* get the author */
    $post['is_page_admin'] = $this->check_page_adminship($this->_data['user_id'], $post['page_id']);
    $post['is_group_admin'] = $this->check_group_adminship($this->_data['user_id'], $post['group_id']);
    $post['is_event_admin'] = $this->check_event_adminship($this->_data['user_id'], $post['event_id']);
    $post['post_author_online'] = false;
    /* check the post author type */
    if ($post['user_type'] == "user") {
      /* user */
      $post['post_author_picture'] = get_picture($post['user_picture'], $post['user_gender']);
      $post['post_author_url'] = $system['system_url'] . '/' . $post['user_name'];
      $post['post_author_name'] = ($system['show_usernames_enabled']) ? $post['user_name'] : $post['user_firstname'] . " " . $post['user_lastname'];
      $post['post_author_verified'] = $post['user_verified'];
      $post['pinned'] = ((!$post['in_group'] && !$post['in_event'] && $post['post_id'] == $post['user_pinned_post']) || ($post['in_group'] && $post['post_id'] == $post['group_pinned_post']) || ($post['in_event'] && $post['post_id'] == $post['event_pinned_post'])) ? true : false;
      if ($system['posts_online_status']) {
        /* check if the post author is online & enable the chat */
        $get_user_status = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s AND user_chat_enabled = '1' AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s))", secure($post['author_id'], 'int'), secure($system['offline_time'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_user_status->fetch_assoc()['count'] > 0) {
          $post['post_author_online'] = true;
        }
      }
    } else {
      /* page */
      $post['post_author_picture'] = get_picture($post['page_picture'], "page");
      $post['post_author_url'] = $system['system_url'] . '/pages/' . $post['page_name'];
      $post['post_author_name'] = $post['page_title'];
      $post['post_author_verified'] = $post['page_verified'];
      $post['pinned'] = ($post['post_id'] == $post['page_pinned_post']) ? true : false;
    }

    /* get post rating count */
    if ($system['posts_reviews_enabled']) {
      $post['reviews_count'] = $this->get_reviews_count($post['post_id'], 'post');
    }

    /* check if viewer can manage post [Edit|Pin|Delete] */
    $post['manage_post'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $post['manage_post'] = true;
      }
      /* viewer is the author of post */
      if ($this->_data['user_id'] == $post['author_id']) {
        $post['manage_post'] = true;
      }
      /* viewer is the admin of the page of the post */
      if ($post['user_type'] == "page" && $post['is_page_admin']) {
        $post['manage_post'] = true;
      }
      /* viewer is the admin of the group of the post */
      if ($post['in_group'] && $post['is_group_admin']) {
        $post['manage_post'] = true;
      }
      /* viewer is the admin of the event of the post */
      if ($post['in_event'] && $post['is_event_admin']) {
        $post['manage_post'] = true;
      }
    }

    /* check if post can be shared with subscribers only */
    $post['can_be_for_subscriptions'] = false;
    if ($this->_logged_in) {
      if ($system['monetization_enabled'] && !$post['in_event']) {
        if ($post['in_group']) {
          $node_monetization_enabled = $post['group_monetization_enabled'];
          $node_author = $post['group_admin'];
          $node_monetization_plans = $post['group_monetization_plans'];
        } elseif ($post['user_type'] == "page") {
          $node_monetization_enabled = $post['page_monetization_enabled'];
          $node_author = $post['page_admin'];
          $node_monetization_plans = $post['page_monetization_plans'];
        } elseif ($post['user_type'] == "user") {
          $node_monetization_enabled = $post['user_monetization_enabled'];
          $node_author = $post['author_id'];
          $node_monetization_plans = $post['user_monetization_plans'];
        }
        if ($node_monetization_enabled && $this->check_user_permission($node_author, 'monetization_permission') && $node_monetization_plans > 0) {
          $post['can_be_for_subscriptions'] = true;
        }
      }
    }

    /* post monetization */
    $post['needs_payment'] = false;
    $post['needs_subscription'] = false;
    if ($system['monetization_enabled'] && ($post['for_subscriptions'] || $post['is_paid'])) {
      /* get node_monetization_enabled */
      if ($post['in_group']) {
        $node_monetization_enabled = ($post['is_paid']) ? $post['user_monetization_enabled'] : $post['group_monetization_enabled'];
        $node_author = ($post['is_paid']) ? $post['author_id'] : $post['group_admin'];
        $node_monetization_plans = $post['group_monetization_plans'];
        $node_subscription_type = 'group';
        $node_subscription_id = $post['group_id'];
        $node_subscription_price = $post['group_monetization_min_price'];
      } elseif ($post['user_type'] == "page") {
        $node_monetization_enabled = $post['page_monetization_enabled'];
        $node_author = $post['page_admin'];
        $node_monetization_plans = $post['page_monetization_plans'];
        $node_subscription_type = 'page';
        $node_subscription_id = $post['page_id'];
        $node_subscription_price = $post['page_monetization_min_price'];
      } elseif ($post['user_type'] == "user") {
        $node_monetization_enabled = $post['user_monetization_enabled'];
        $node_author = $post['author_id'];
        $node_monetization_plans = $post['user_monetization_plans'];
        $node_subscription_type = 'profile';
        $node_subscription_id = $post['user_id'];
        $node_subscription_price = $post['user_monetization_min_price'];
      }
      /* get can_monetize_content */
      $post['can_monetize_content'] = $node_monetization_enabled && $this->check_user_permission($node_author, 'monetization_permission');
      if ($post['can_monetize_content']) {
        /* check if the viewer logged in */
        if ($this->_logged_in) {
          /* check if the viewer is not (admin || moderator) && not the author of the post */
          if ($this->_data['user_group'] == 3 && $post['author_id'] != $this->_data['user_id']) {
            if ($post['is_paid']) {
              /* check if the viewer subscribed to this node || paid for this post */
              if (!$this->is_subscribed($node_subscription_id, $node_subscription_type) && !$this->is_user_paid_for_post($post['post_id'])) {
                $post['needs_payment'] = true;
              }
            } else {
              if ($node_monetization_plans > 0) {
                /* check if the viewer subscribed to this node */
                if (!$this->is_subscribed($node_subscription_id, $node_subscription_type)) {
                  $post['needs_subscription'] = true;
                  $post['needs_subscription_type'] = $node_subscription_type;
                  $post['needs_subscription_id'] = $node_subscription_id;
                  $post['needs_subscription_price'] = $node_subscription_price;
                }
              }
            }
          }
        } else {
          if ($post['is_paid']) {
            $post['needs_payment'] = true;
          } else {
            if ($node_monetization_plans > 0) {
              $post['needs_subscription'] = true;
              $post['needs_subscription_type'] = $node_subscription_type;
              $post['needs_subscription_id'] = $node_subscription_id;
              $post['needs_subscription_price'] = $node_subscription_price;
            }
          }
        }
      }
    }

    /* post adult content */
    $post['needs_age_verification'] = false;
    if ($system['adult_mode'] && $post['for_adult']) {
      /* check if the viewer logged in */
      if ($this->_logged_in) {
        /* check if the viewer is not (admin || moderator) && not the author of the post */
        if ($this->_data['user_group'] == 3 && $post['author_id'] != $this->_data['user_id']) {
          /* user not adult */
          if (!$this->_data['user_adult']) {
            $post['needs_age_verification'] = true;
          } else {
            /* user adult but not verified */
            if ($system['verification_for_adult_content'] && !$this->_data['user_verified']) {
              $post['needs_age_verification'] = true;
            }
          }
        }
      } else {
        $post['needs_age_verification'] = true;
      }
    }

    /* check privacy */
    /* overwrite the pass_privacy_check */
    if ($pass_privacy_check && $system['newsfeed_source'] == "all_posts" && ($post['in_group'] || $post['in_event'])) {
      /* pass_privacy_check set to false to exclude any post from closed/secret groups and events that users not member of */
      $pass_privacy_check = false;
    }
    /* if post in group & (the group is public || the viewer approved member of this group) => pass privacy check */
    if ($post['in_group'] && ($post['group_privacy'] == 'public' || $this->check_group_membership($this->_data['user_id'], $post['group_id']) == 'approved')) {
      $pass_privacy_check = true;
    }
    /* if post in event & (the event is public || the viewer member of this event) => pass privacy check */
    if ($post['in_event'] && ($post['event_privacy'] == 'public' || $this->check_event_membership($this->_data['user_id'], $post['event_id']))) {
      $pass_privacy_check = true;
    }

    /* get post meta details */
    if (!$post['needs_payment'] && !$post['needs_subscription'] && !$post['needs_age_verification']) {
      /* get reactions array */
      $post['reactions']['like'] = $post['reaction_like_count'];
      $post['reactions']['love'] = $post['reaction_love_count'];
      $post['reactions']['haha'] = $post['reaction_haha_count'];
      $post['reactions']['yay'] = $post['reaction_yay_count'];
      $post['reactions']['wow'] = $post['reaction_wow_count'];
      $post['reactions']['sad'] = $post['reaction_sad_count'];
      $post['reactions']['angry'] = $post['reaction_angry_count'];
      arsort($post['reactions']);

      /* get total reactions */
      $post['reactions_total_count'] = $post['reaction_like_count'] + $post['reaction_love_count'] + $post['reaction_haha_count'] + $post['reaction_yay_count'] + $post['reaction_wow_count'] + $post['reaction_sad_count'] + $post['reaction_angry_count'];

      /* full details */
      if ($full_details) {
        /* check if wall post */
        if ($post['in_wall']) {
          $get_wall_user = $db->query(sprintf("SELECT user_firstname, user_lastname, user_name FROM users WHERE user_id = %s", secure($post['wall_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_wall_user->num_rows == 0) {
            return false;
          }
          $wall_user = $get_wall_user->fetch_assoc();
          $post['wall_username'] = $wall_user['user_name'];
          $post['wall_fullname'] = ($system['show_usernames_enabled']) ? $wall_user['user_name'] : $wall_user['user_firstname'] . " " . $wall_user['user_lastname'];
        }

        /* check if viewer [reacted|saved] this post */
        $post['i_save'] = false;
        $post['i_react'] = false;
        if ($this->_logged_in) {
          /* save */
          $check_save = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_saved WHERE user_id = %s AND post_id = %s", secure($this->_data['user_id'], 'int'), secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($check_save->fetch_assoc()['count'] > 0) {
            $post['i_save'] = true;
          }
          /* reaction */
          if ($post['reactions_total_count'] > 0) {
            $get_reaction = $db->query(sprintf("SELECT reaction FROM posts_reactions WHERE user_id = %s AND post_id = %s", secure($this->_data['user_id'], 'int'), secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($get_reaction->num_rows > 0) {
              $post['i_react'] = true;
              $post['i_reaction'] = $get_reaction->fetch_assoc()['reaction'];
            }
          }
        }
      }
    }

    /* return post */
    if ($pass_privacy_check || $this->check_privacy($post['privacy'], $post['author_id'])) {
      return $post;
    }
    return false;
  }


  /**
   * check_privacy
   * 
   * @param string $privacy
   * @param integer $author_id
   * @return boolean
   */
  public function check_privacy($privacy, $author_id)
  {
    if ($privacy == 'public') {
      return true;
    }
    if ($this->_logged_in) {
      /* check if the viewer is the system admin */
      if ($this->_data['user_group'] < 3) {
        return true;
      }
      /* check if the viewer is the target */
      if ($author_id == $this->_data['user_id']) {
        return true;
      }
      /* check if the viewer and the target are friends */
      if ($privacy == 'friends' && $this->friendship_approved($author_id)) {
        return true;
      }
      if (defined('PRIVACY_ERRORS')) {
        throw new PrivacyException(__("You don't have permission to view this post"));
      }
    } else {
      if (defined('PRIVACY_ERRORS')) {
        user_login();
      }
    }
    return false;
  }



  /* ------------------------------- */
  /* Posts Actions */
  /* ------------------------------- */

  /**
   * share
   * 
   * @param integer $post_id
   * @param array $args
   *@return void
   */
  public function share($post_id, $args)
  {
    global $db, $date;
    /* check if the viewer can share the post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    if ($post['privacy'] != 'public' && !($post['in_group'] && $post['group_privacy'] == "public") && !($post['in_event'] && $post['event_privacy'] == "public")) {
      _error(403);
    }
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }
    // share post
    /* get the origin post */
    if ($post['post_type'] == "shared") {
      $origin = $this->_check_post($post['origin_id'], true);
      if (!$origin) {
        _error(403);
      }
      if ($origin['privacy'] != 'public' && !($origin['in_group'] && $origin['group_privacy'] == "public") && !($origin['in_event'] && $origin['event_privacy'] == "public")) {
        _error(403);
      }
      $post_id = $origin['post_id'];
      $author_id = $origin['author_id'];
    } else {
      $post_id = $post['post_id'];
      $author_id = $post['author_id'];
    }
    /* share to */
    switch ($args['share_to']) {
      case 'timeline':
        /* insert the new shared post */
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, origin_id, time, privacy, text) VALUES (%s, 'user', 'shared', %s, %s, 'public', %s)", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'), secure($date), secure($args['message']))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page':
        /* check if the page is valid */
        $check_page = $db->query(sprintf("SELECT COUNT(*) as count FROM pages WHERE page_id = %s", secure($args['page'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($check_page->fetch_assoc()['count'] == 0) {
          _error(400);
        }
        /* check if the viewer is page admin */
        if (!$this->check_page_adminship($this->_data['user_id'], $args['page'])) {
          _error(400);
        }
        /* insert the new shared post */
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, origin_id, time, privacy, text) VALUES (%s, 'page', 'shared', %s, %s, 'public', %s)", secure($args['page'], 'int'), secure($post_id, 'int'), secure($date), secure($args['message']))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        /* check if the group is valid */
        $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($args['group'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_group->num_rows == 0) {
          _error(400);
        }
        $group = $get_group->fetch_assoc();
        /* check if the viewer is group member */
        if ($this->check_group_membership($this->_data['user_id'], $args['group']) != "approved") {
          _error(400);
        }
        /* check if user is a group admin */
        $group['i_admin'] = $this->check_group_adminship($this->_data['user_id'], $group['group_id']);
        /* check if group publishing enabled */
        if (!$group['group_publish_enabled'] && !$group['i_admin']) {
          throw new Exception(__("Publishing to this group has been disabled by group admins"));
        }
        /* insert the new shared post */
        $group_approved = ($group['group_publish_approval_enabled'] && !$group['i_admin']) ? "0" : "1";
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, origin_id, time, privacy, text, in_group, group_id, group_approved) VALUES (%s, 'user', 'shared', %s, %s, 'custom', %s, '1', %s, %s)", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'), secure($date), secure($args['message']), secure($args['group'], 'int'), secure($group_approved))) or _error('SQL_ERROR_THROWEN');
        $post_id = $db->insert_id;
        /* post in_group notification */
        if (!$group_approved) {
          /* send notification to group admin */
          $this->post_notification(['to_user_id' => $group['group_admin'], 'action' => 'group_post_pending', 'node_type' => $group['group_title'], 'node_url' => $group['group_name'] . "-[guid=]" . $post_id]);
        }
        break;

      case 'event':
        /* check if the event is valid */
        $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($args['event'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_event->num_rows == 0) {
          _error(400);
        }
        $event = $get_event->fetch_assoc();
        /* check if the viewer is event member */
        if (!$this->check_event_membership($this->_data['user_id'], $args['event'])) {
          _error(400);
        }
        /* check if user is a event admin */
        $event['i_admin'] = $this->check_event_adminship($this->_data['user_id'], $event['event_id']);
        /* check if event publishing enabled */
        if (!$event['event_publish_enabled'] && !$event['i_admin']) {
          throw new Exception(__("Publishing to this event has been disabled by event admins"));
        }
        /* insert the new shared post */
        $event_approved = ($event['event_publish_approval_enabled'] && !$event['i_admin']) ? "0" : "1";
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, origin_id, time, privacy, text, in_event, event_id, event_approved) VALUES (%s, 'user', 'shared', %s, %s, 'custom', %s, '1', %s, %s)", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'), secure($date), secure($args['message']), secure($args['event'], 'int'), secure($event_approved))) or _error('SQL_ERROR_THROWEN');
        $post_id = $db->insert_id;
        /* post in_event notification */
        if (!$event_approved) {
          /* send notification to event admin */
          $this->post_notification(['to_user_id' => $event['event_admin'], 'action' => 'event_post_pending', 'node_type' => $event['event_title'], 'node_url' => $event['event_name'] . "-[guid=]" . $post_id]);
        }
        break;

      default:
        _error(403);
        break;
    }
    /* update the origin post shares counter */
    $db->query(sprintf("UPDATE posts SET shares = shares + 1 WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post notification */
    $this->post_notification(['to_user_id' => $author_id, 'action' => 'share', 'node_type' => 'post', 'node_url' => $post_id]);
  }


  /**
   * delete_posts
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return void
   */
  public function delete_posts($node_id, $node_type = 'user')
  {
    global $db;
    /* get all user posts */
    switch ($node_type) {
      case 'user':
        $get_posts = $db->query(sprintf("SELECT post_id FROM posts WHERE user_id = %s AND user_type = 'user'", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page':
        $get_posts = $db->query(sprintf("SELECT post_id FROM posts WHERE user_id = %s AND user_type = 'page'", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        $get_posts = $db->query(sprintf("SELECT post_id FROM posts WHERE group_id = %s AND in_group = '1'", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event':
        $get_posts = $db->query(sprintf("SELECT post_id FROM posts WHERE event_id = %s AND in_event = '1'", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;
    }
    if ($get_posts->num_rows > 0) {
      while ($post = $get_posts->fetch_assoc()) {
        $this->delete_post($post['post_id'], false);
      }
    }
  }


  /**
   * delete_post
   * 
   * @param integer $post_id
   * @param boolean $return_errors
   * @return boolean
   */
  public function delete_post($post_id, $return_errors = true)
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->get_post($post_id, false, true);
    if (!$post) {
      if (!$return_errors) {
        return;
      }
      _error(403);
    }
    /* can delete the post */
    if (!$post['manage_post']) {
      if (!$return_errors) {
        return;
      }
      /* check & delete orphaned data */
      if ($this->_is_admin) {
        $check_post = $db->query(sprintf("SELECT COUNT(*) as count FROM posts WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($check_post->fetch_assoc()['count'] > 0) {
          $db->query(sprintf("DELETE FROM posts WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
          return;
        }
      }
      _error(403);
    }
    /* delete hashtags */
    $this->delete_hashtags($post_id);
    /* delete post comments */
    $get_comments = $db->query(sprintf("SELECT posts_comments.comment_id FROM posts_comments WHERE node_id = %s AND node_type = 'post'", secure($post_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($get_comments->num_rows > 0) {
      while ($comment = $get_comments->fetch_assoc()) {
        $this->delete_comment($comment['comment_id']);
      }
    }
    /* set refresh */
    $refresh = false;
    /* delete post */
    $db->query(sprintf("DELETE FROM posts WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete post hidden */
    $db->query(sprintf("DELETE FROM posts_hidden WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete post paid */
    $db->query(sprintf("DELETE FROM posts_paid WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete posts reactions */
    $db->query(sprintf("DELETE FROM posts_reactions WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete posts saved */
    $db->query(sprintf("DELETE FROM posts_saved WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete reports */
    $db->query(sprintf("DELETE FROM reports WHERE node_type = 'post' AND node_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete nested tables */
    /* delete photos post */
    if (in_array($post['post_type'], ['photos', 'album', 'profile_cover', 'profile_picture', 'page_cover', 'page_picture', 'group_cover', 'group_picture', 'event_cover', 'product', 'combo']) && $post['photos']) {
      /* delete uploads from uploads folder */
      foreach ($post['photos'] as $photo) {
        delete_uploads_file($photo['source']);
      }
      /* delete post photos from database */
      $db->query(sprintf("DELETE FROM posts_photos WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      switch ($post['post_type']) {
        case 'profile_cover':
          /* update user cover if it's current cover */
          if ($post['user_cover_id']  ==  $post['photos'][0]['photo_id']) {
            $db->query(sprintf("UPDATE users SET user_cover = null, user_cover_id = null WHERE user_id = %s", secure($post['author_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            /* return */
            $refresh = true;
          }
          break;

        case 'profile_picture':
          /* update user picture if it's current picture */
          if ($post['user_picture_id']  ==  $post['photos'][0]['photo_id']) {
            $db->query(sprintf("UPDATE users SET user_picture = null, user_picture_id = null WHERE user_id = %s", secure($post['author_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            /* delete cropped picture from uploads folder */
            delete_uploads_file($post['user_picture']);
            /* return */
            $refresh = true;
          }
          break;

        case 'page_cover':
          /* update page cover if it's current cover */
          if ($post['page_cover_id']  ==  $post['photos'][0]['photo_id']) {
            $db->query(sprintf("UPDATE pages SET page_cover = null, page_cover_id = null WHERE page_id = %s", secure($post['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            /* return */
            $refresh = true;
          }
          break;

        case 'page_picture':
          /* update page picture if it's current picture */
          if ($post['page_picture_id']  ==  $post['photos'][0]['photo_id']) {
            $db->query(sprintf("UPDATE pages SET page_picture = null, page_picture_id = null WHERE page_id = %s", secure($post['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            /* delete cropped picture from uploads folder */
            delete_uploads_file($post['page_picture']);
            /* return */
            $refresh = true;
          }
          break;

        case 'group_cover':
          /* update group cover if it's current cover */
          if ($post['group_cover_id']  ==  $post['photos'][0]['photo_id']) {
            $db->query(sprintf("UPDATE `groups` SET group_cover = null, group_cover_id = null WHERE group_id = %s", secure($post['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            /* return */
            $refresh = true;
          }
          break;

        case 'group_picture':
          /* update group picture if it's current picture */
          if ($post['group_picture_id']  ==  $post['photos'][0]['photo_id']) {
            $db->query(sprintf("UPDATE `groups` SET group_picture = null, group_picture_id = null WHERE group_id = %s", secure($post['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            /* delete cropped from uploads folder */
            delete_uploads_file($post['group_picture']);
            /* return */
            $refresh = true;
          }
          break;

        case 'event_cover':
          /* update event cover if it's current cover */
          if ($post['event_cover_id']  ==  $post['photos'][0]['photo_id']) {
            $db->query(sprintf("UPDATE `events` SET event_cover = null, event_cover_id = null WHERE event_id = %s", secure($post['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            /* return */
            $refresh = true;
          }
          break;

        case 'product':
          /* delete nested table row */
          $db->query(sprintf("DELETE FROM posts_products WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
          break;
      }
    }
    /* delete media post */
    if ($post['post_type'] == 'media') {
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_media WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete link post */
    if ($post['post_type'] == 'link') {
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_links WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete live post */
    if ($post['post_type'] == 'live') {
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_live WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete live users */
      $db->query(sprintf("DELETE FROM posts_live_users WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete article post */
    if ($post['post_type'] == 'article') {
      /* delete uploads from uploads folder */
      delete_uploads_file($post['article']['cover']);
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_articles WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete funding post */
    if ($post['post_type'] == 'funding') {
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_fundings WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete funding donors */
      $db->query(sprintf("DELETE FROM posts_funding_donors WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete offer post */
    if ($post['post_type'] == 'offer') {
      /* delete uploads from uploads folder */
      delete_uploads_file($post['offer']['thumbnail']);
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_offers WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete job post */
    if ($post['post_type'] == 'job') {
      /* delete uploads from uploads folder */
      delete_uploads_file($post['job']['cover_image']);
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_jobs WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete poll post */
    if ($post['post_type'] == 'poll') {
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_polls WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("DELETE FROM posts_polls_options WHERE poll_id = %s", secure($post['poll']['poll_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("DELETE FROM posts_polls_options_users WHERE poll_id = %s", secure($post['poll']['poll_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete video post */
    if (($post['post_type'] == 'video' || $post['post_type'] == 'combo') && $post['video']) {
      /* delete uploads from uploads folder */
      delete_uploads_file($post['video']['thumbnail']);
      delete_uploads_file($post['video']['source']);
      delete_uploads_file($post['video']['source_240p']);
      delete_uploads_file($post['video']['source_360p']);
      delete_uploads_file($post['video']['source_480p']);
      delete_uploads_file($post['video']['source_720p']);
      delete_uploads_file($post['video']['source_1080p']);
      delete_uploads_file($post['video']['source_1440p']);
      delete_uploads_file($post['video']['source_2160p']);
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_videos WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete audio post */
    if (($post['post_type'] == 'audio' || $post['post_type'] == 'combo') && $post['audio']) {
      /* delete uploads from uploads folder */
      delete_uploads_file($post['audio']['source']);
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_audios WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* delete file post */
    if (($post['post_type'] == 'file' || $post['post_type'] == 'combo') && $post['file']) {
      /* delete uploads from uploads folder */
      delete_uploads_file($post['file']['source']);
      /* delete nested table row */
      $db->query(sprintf("DELETE FROM posts_files WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* points balance */
    $this->points_balance("delete", $post['author_id'], "post");
    /* delete post in_group notification */
    if ($post['in_group'] && !$post['group_approved']) {
      $this->delete_notification($post['group_admin'], 'group_post_pending', $post['group_title'], $post['group_name'] . "-[guid=]" . $post['post_id']);
    }
    /* delete post in_event notification */
    if ($post['in_event'] && !$post['event_approved']) {
      $this->delete_notification($post['event_admin'], 'event_post_pending', $post['event_title'], $post['event_id'] . "-[guid=]" . $post['post_id']);
    }
    return $refresh;
  }


  /**
   * edit_post
   * 
   * @param integer $post_id
   * @param string $message
   * @return array
   */
  public function edit_post($post_id, $message)
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check post max length */
    if ($system['max_post_length'] > 0 && $this->_data['user_group'] >= 3) {
      if (strlen($message) >= $system['max_post_length']) {
        modal("MESSAGE", __("Text Length Limit Reached"), __("Your message characters length is over the allowed limit") . " (" . $system['max_post_length'] . " " . __("Characters") . ")");
      }
    }
    /* delete hashtags */
    $this->delete_hashtags($post_id);
    /* check if post in_group with approval system */
    if ($post['in_group'] && $post['group_publish_approval_enabled'] && $post['group_approved']) {
      $post['group_approved'] = ($post['group_publish_approval_enabled'] && !$post['is_group_admin']) ? '0' : '1';
      /* post notification to group admin */
      if (!$post['group_approved']) {
        $this->post_notification(['to_user_id' => $post['group_admin'], 'action' => 'group_post_pending', 'node_type' => $post['group_title'], 'node_url' => $post['group_name'] . "-[guid=]" . $post['post_id']]);
      }
    }
    /* check if post in_event with approval system */
    if ($post['in_event'] && $post['event_publish_approval_enabled'] && $post['event_approved']) {
      $post['event_approved'] = ($post['event_publish_approval_enabled'] && !$post['is_event_admin']) ? '0' : '1';
      /* post notification to event admin */
      if (!$post['event_approved']) {
        $this->post_notification(['to_user_id' => $post['event_admin'], 'action' => 'event_post_pending', 'node_type' => $post['event_title'], 'node_url' => $post['event_id'] . "-[guid=]" . $post['post_id']]);
      }
    }
    /* update post */
    $db->query(sprintf("UPDATE posts SET text = %s, group_approved = %s, event_approved = %s WHERE post_id = %s", secure($message), secure($post['group_approved']), secure($post['event_approved']), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post mention notifications */
    $this->post_mentions($message, $post_id);
    /* parse text */
    $post['text_plain'] = htmlentities($message, ENT_QUOTES, 'utf-8');
    $post['text'] = $this->_parse(["text" => $post['text_plain'], "trending_hashtags" => true, "post_id" => $post_id]);
    /* get post colored pattern */
    $post['colored_pattern'] = $this->get_posts_colored_pattern($post['colored_pattern']);
    /* return */
    return $post;
  }


  /**
   * edit_product
   * 
   * @param integer $post_id
   * @param string $message
   * @param array $args
   * @return void
   */
  public function edit_product($post_id, $message, $args = [])
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* update post */
    $db->query(sprintf("UPDATE posts SET text = %s WHERE post_id = %s", secure($message), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update product */
    $db->query(sprintf("UPDATE posts_products SET name = %s, price = %s, quantity = %s, category_id = %s, status = %s, location = %s, is_digital = %s, product_download_url = %s, product_file_source = %s WHERE post_id = %s", secure($args['name']), secure($args['price'], 'float'), secure($args['quantity'], 'int'), secure($args['category'], 'int'), secure($args['status']), secure($args['location']), secure($args['is_digital']), secure($args['product_url']), secure($args['product_file']), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * edit_funding
   * 
   * @param integer $post_id
   * @param string $message
   * @param array $args
   * @return void
   */
  public function edit_funding($post_id, $message, $args = [])
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* update post */
    $db->query(sprintf("UPDATE posts SET text = %s WHERE post_id = %s", secure($message), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update funding */
    $db->query(sprintf("UPDATE posts_funding SET title = %s, amount = %s, cover_image = %s WHERE post_id = %s", secure($args['title']), secure($args['amount']), secure($args['cover_image']), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * edit_offer
   * 
   * @param integer $post_id
   * @param string $message
   * @param array $args
   * @return void
   */
  public function edit_offer($post_id, $message, $args = [])
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* prepare end date (optional) */
    $end_date = ($args['end_date']) ? secure($args['end_date'], 'datetime') : 'NULL';
    /* update post */
    $db->query(sprintf("UPDATE posts SET text = %s WHERE post_id = %s", secure($message), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update offer */
    $db->query(sprintf("UPDATE posts_offers SET category_id = %s, title = %s, discount_type = %s, discount_percent = %s, discount_amount = %s, buy_x = %s, get_y = %s, spend_x = %s, amount_y = %s, end_date = %s, thumbnail = %s, price = %s WHERE post_id = %s", secure($args['category'], 'int'), secure($args['title']), secure($args['discount_type']), secure($args['discount_percent'], 'int'), secure($args['discount_amount']), secure($args['buy_x']), secure($args['get_y']), secure($args['spend_x']), secure($args['amount_y']), $end_date, secure($args['thumbnail']), secure($args['price'], 'float'), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * edit_job
   * 
   * @param integer $post_id
   * @param string $message
   * @param array $arg
   * @return void
   */
  public function edit_job($post_id, $message, $args = [])
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* update post */
    $db->query(sprintf("UPDATE posts SET text = %s WHERE post_id = %s", secure($message), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update job */
    $db->query(sprintf(
      "UPDATE posts_jobs SET 
            category_id = %s, 
            title = %s, 
            location = %s, 
            salary_minimum = %s, 
            salary_maximum = %s, 
            pay_salary_per = %s, 
            type = %s, 
            question_1_type = %s, 
            question_1_title = %s, 
            question_1_choices = %s, 
            question_2_type = %s, 
            question_2_title = %s, 
            question_2_choices = %s, 
            question_3_type = %s, 
            question_3_title = %s, 
            question_3_choices = %s, 
            cover_image = %s 
            WHERE post_id = %s",
      secure($args['category'], 'int'),
      secure($args['title']),
      secure($args['location']),
      secure($args['salary_minimum']),
      secure($args['salary_maximum']),
      secure($args['pay_salary_per']),
      secure($args['type']),
      secure($args['question_1_type']),
      secure($args['question_1_title']),
      secure($args['question_1_choices']),
      secure($args['question_2_type']),
      secure($args['question_2_title']),
      secure($args['question_2_choices']),
      secure($args['question_3_type']),
      secure($args['question_3_title']),
      secure($args['question_3_choices']),
      secure($args['cover_image']),
      secure($post_id, 'int')
    )) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * edit_privacy
   * 
   * @param integer $post_id
   * @param string $privacy
   * @return void
   */
  public function edit_privacy($post_id, $privacy)
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check post type */
    if ($post['is_anonymous'] || $post['in_group'] || $post['in_event'] || in_array($post['post_type'], ['article', 'product', 'funding'])) {
      _error(400);
    }
    /* check if viewer can edit privacy */
    if ($post['manage_post'] && $post['user_type'] == 'user' && !$post['in_group'] && !$post['in_event'] && $post['post_type'] != "product") {
      /* update privacy */
      $db->query(sprintf("UPDATE posts SET privacy = %s WHERE post_id = %s", secure($privacy), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      _error(403);
    }
  }


  /**
   * disallow_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function disallow_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if post is_anonymous */
    if ($post['is_anonymous']) {
      throw new Exception(__("You can not do this with anonymous post"));
    }
    /* set post as hidden */
    $db->query(sprintf("UPDATE posts SET is_hidden = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * allow_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function allow_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if post is_anonymous */
    if ($post['is_anonymous']) {
      throw new Exception(__("You can not do this with anonymous post"));
    }
    /* set post as not hidden */
    $db->query(sprintf("UPDATE posts SET is_hidden = '0' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * disable_post_comments
   * 
   * @param integer $post_id
   * @return void
   */
  public function disable_post_comments($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* trun off post commenting */
    $db->query(sprintf("UPDATE posts SET comments_disabled = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * enable_post_comments
   * 
   * @param integer $post_id
   * @return void
   */
  public function enable_post_comments($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* trun on post commenting */
    $db->query(sprintf("UPDATE posts SET comments_disabled = '0' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * approve_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function approve_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can approve post */
    if ($post['in_group']) {
      if (!$post['is_group_admin']) {
        _error(403);
      }
      /* approve post */
      $db->query(sprintf("UPDATE posts SET group_approved = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* send notification to the post author */
      $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'group_post_approval', 'node_type' => $post['group_title'], 'node_url' => $post['group_name']]);
    } elseif ($post['in_event']) {
      if (!$post['is_event_admin']) {
        _error(403);
      }
      /* approve post */
      $db->query(sprintf("UPDATE posts SET event_approved = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* send notification to the post author */
      $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'event_post_approval', 'node_type' => $post['event_title'], 'node_url' => $post['event_id']]);
    }
  }


  /**
   * sold_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function sold_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* sold post */
    $db->query(sprintf("UPDATE posts_products SET available = '0' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * unsold_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function unsold_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* unsold post */
    $db->query(sprintf("UPDATE posts_products SET available = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * closed_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function closed_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* sold post */
    $db->query(sprintf("UPDATE posts_jobs SET available = '0' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * unclosed_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function unclosed_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* unsold post */
    $db->query(sprintf("UPDATE posts_jobs SET available = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * save_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function save_post($post_id)
  {
    global $db, $date;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* save post */
    if (!$post['i_save']) {
      $db->query(sprintf("INSERT INTO posts_saved (post_id, user_id, time) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($this->_data['user_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * unsave_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function unsave_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* unsave post */
    if ($post['i_save']) {
      $db->query(sprintf("DELETE FROM posts_saved WHERE post_id = %s AND user_id = %s", secure($post_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * boost_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function boost_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if viewer can boost post */
    if (!$this->_data['can_boost_posts']) {
      modal("MESSAGE", __("Sorry"), __("You reached the maximum number of boosted posts! Upgrade your package to get more"));
    }
    /* check if the post in_group or in_event */
    if ($post['in_group'] || $post['in_event']) {
      throw new Exception(__("You can't boost a post from a group or event"));
    }
    /* boost post */
    if (!$post['boosted']) {
      /* boost post */
      $db->query(sprintf("UPDATE posts SET boosted = '1', boosted_by = %s WHERE post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update user */
      $db->query(sprintf("UPDATE users SET user_boosted_posts = user_boosted_posts + 1 WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * unboost_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function unboost_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* unboost post */
    if ($post['boosted']) {
      /* unboost post */
      $db->query(sprintf("UPDATE posts SET boosted = '0', boosted_by = NULL WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update user */
      $db->query(sprintf("UPDATE users SET user_boosted_posts = IF(user_boosted_posts=0,0,user_boosted_posts-1) WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * pin_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function pin_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if post is_anonymous */
    if ($post['is_anonymous']) {
      throw new Exception(__("You can not do this with anonymous post"));
    }
    /* pin post */
    if (!$post['pinned']) {
      /* check the post author type */
      if ($post['user_type'] == "user") {
        /* user */
        if ($post['in_group']) {
          if (!$post['is_group_admin']) {
            throw new Exception(__("Only group admin can pin the post"));
          }
          /* update group */
          $db->query(sprintf("UPDATE `groups` SET group_pinned_post = %s WHERE group_id = %s", secure($post_id, 'int'), secure($post['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } elseif ($post['in_event']) {
          if (!$post['is_event_admin']) {
            throw new Exception(__("Only event admin can pin the post"));
          }
          /* update event */
          $db->query(sprintf("UPDATE `events` SET event_pinned_post = %s WHERE event_id = %s", secure($post_id, 'int'), secure($post['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* update user */
          $db->query(sprintf("UPDATE users SET user_pinned_post = %s WHERE user_id = %s", secure($post_id, 'int'), secure($post['author_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
      } else {
        /* update page */
        $db->query(sprintf("UPDATE pages SET page_pinned_post = %s WHERE page_id = %s", secure($post_id, 'int'), secure($post['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
    }
  }


  /**
   * unpin_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function unpin_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if post is_anonymous */
    if ($post['is_anonymous']) {
      throw new Exception(__("You can not do this with anonymous post"));
    }
    /* pin post */
    if ($post['pinned']) {
      /* check the post author type */
      if ($post['user_type'] == "user") {
        /* user */
        if ($post['in_group']) {
          if (!$post['is_group_admin']) {
            throw new Exception(__("Only group admin can unpin the post"));
          }
          /* update group */
          $db->query(sprintf("UPDATE `groups` SET group_pinned_post = '0' WHERE group_id = %s", secure($post['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } elseif ($post['in_event']) {
          if (!$post['is_event_admin']) {
            throw new Exception(__("Only event admin can unpin the post"));
          }
          /* update event */
          $db->query(sprintf("UPDATE `events` SET event_pinned_post = '0' WHERE event_id = %s", secure($post['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* update user */
          $db->query(sprintf("UPDATE users SET user_pinned_post = '0' WHERE user_id = %s", secure($post['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
      } else {
        /* update page */
        $db->query(sprintf("UPDATE pages SET page_pinned_post = '0' WHERE page_id = %s", secure($post['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
    }
  }


  /**
   * monetize_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function monetize_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if post can be monetized */
    if (!$post['can_be_for_subscriptions']) {
      _error(403);
    }
    /* monetize post */
    if (!$post['for_subscriptions']) {
      /* update post */
      $db->query(sprintf("UPDATE posts SET for_subscriptions = '1' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * unmonetize_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function unmonetize_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post'] && !$post['is_group_admin'] && !$post['is_event_admin']) {
      _error(403);
    }
    /* check if post can be monetized */
    if (!$post['can_be_for_subscriptions']) {
      _error(403);
    }
    /* unmonetize post */
    if ($post['for_subscriptions']) {
      /* update post */
      $db->query(sprintf("UPDATE posts SET for_subscriptions = '0' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * react_post
   * 
   * @param integer $post_id
   * @param string $reaction
   * @return void
   */
  public function react_post($post_id, $reaction)
  {
    global $db, $date;
    /* check reation */
    if (!in_array($reaction, ['like', 'love', 'haha', 'yay', 'wow', 'sad', 'angry'])) {
      _error(403);
    }
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }
    /* react the post */
    if ($post['i_react']) {
      /* remove any previous reaction */
      $db->query(sprintf("DELETE FROM posts_reactions WHERE user_id = %s AND post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update post reaction counter */
      $reaction_field = "reaction_" . $post['i_reaction'] . "_count";
      $db->query(sprintf("UPDATE posts SET $reaction_field = IF($reaction_field=0,0,$reaction_field-1) WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete notification */
      $this->delete_notification($post['author_id'], 'react_' . $post['i_reaction'], 'post', $post_id);
      /* points balance */
      $this->points_balance("delete", $this->_data['user_id'], "posts_reactions");
    }
    $db->query(sprintf("INSERT INTO posts_reactions (user_id, post_id, reaction, reaction_time) VALUES (%s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'), secure($reaction), secure($date))) or _error('SQL_ERROR_THROWEN');
    $reaction_id = $db->insert_id;
    /* update post reaction counter */
    $reaction_field = "reaction_" . $reaction . "_count";
    $db->query(sprintf("UPDATE posts SET $reaction_field = $reaction_field + 1 WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post notification */
    $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'react_' . $reaction, 'node_type' => 'post', 'node_url' => $post_id]);
    /* points balance */
    $this->points_balance("add", $this->_data['user_id'], "posts_reactions", $reaction_id);
  }


  /**
   * unreact_post
   * 
   * @param integer $post_id
   * @param string $reaction
   * @return void
   */
  public function unreact_post($post_id, $reaction)
  {
    global $db;
    /* check reation */
    if (!in_array($reaction, ['like', 'love', 'haha', 'yay', 'wow', 'sad', 'angry'])) {
      _error(403);
    }
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }
    /* unreact the post */
    if ($post['i_react']) {
      $db->query(sprintf("DELETE FROM posts_reactions WHERE user_id = %s AND post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update post reaction counter */
      $reaction_field = "reaction_" . $reaction . "_count";
      $db->query(sprintf("UPDATE posts SET $reaction_field = IF($reaction_field=0,0,$reaction_field-1) WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete notification */
      $this->delete_notification($post['author_id'], 'react_' . $reaction, 'post', $post_id);
      /* points balance */
      $this->points_balance("delete", $this->_data['user_id'], "posts_reactions");
    }
  }


  /**
   * hide_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function hide_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }
    /* hide the post */
    $db->query(sprintf("INSERT INTO posts_hidden (user_id, post_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * unhide_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function unhide_post($post_id)
  {
    global $db;
    /* (check|get) post */
    $post = $this->_check_post($post_id);
    if (!$post) {
      _error(403);
    }
    /* unhide the post */
    $db->query(sprintf("DELETE FROM posts_hidden WHERE user_id = %s AND post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * add_vote
   * 
   * @param integer $option_id
   * @return void
   */
  public function add_vote($option_id)
  {
    global $db;
    /* get poll */
    $get_poll = $db->query(sprintf("SELECT posts_polls.* FROM posts_polls_options INNER JOIN posts_polls ON posts_polls_options.poll_id = posts_polls.poll_id WHERE option_id = %s", secure($option_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_poll->num_rows == 0) {
      _error(403);
    }
    $poll = $get_poll->fetch_assoc();
    /* (check|get) post */
    $post = $this->_check_post($poll['post_id']);
    if (!$post) {
      _error(403);
    }
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }
    /* insert user vote */
    $vote = $db->query(sprintf("INSERT INTO posts_polls_options_users (user_id, poll_id, option_id) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($poll['poll_id'], 'int'), secure($option_id, 'int')));
    if ($vote) {
      /* update poll votes */
      $db->query(sprintf("UPDATE posts_polls SET votes = votes + 1 WHERE poll_id = %s", secure($poll['poll_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* post notification */
      $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'vote', 'node_type' => 'post', 'node_url' => $post['post_id']]);
    }
  }


  /**
   * delete_vote
   * 
   * @param integer $option_id
   * @return void
   */
  public function delete_vote($option_id)
  {
    global $db;
    /* get poll */
    $get_poll = $db->query(sprintf("SELECT posts_polls.* FROM posts_polls_options INNER JOIN posts_polls ON posts_polls_options.poll_id = posts_polls.poll_id WHERE option_id = %s", secure($option_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_poll->num_rows == 0) {
      _error(403);
    }
    $poll = $get_poll->fetch_assoc();
    /* (check|get) post */
    $post = $this->_check_post($poll['post_id']);
    if (!$post) {
      _error(403);
    }
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }
    /* delete user vote */
    $db->query(sprintf("DELETE FROM posts_polls_options_users WHERE user_id = %s AND poll_id = %s AND option_id = %s", secure($this->_data['user_id'], 'int'), secure($poll['poll_id'], 'int'), secure($option_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($db->affected_rows > 0) {
      /* update poll votes */
      $db->query(sprintf("UPDATE posts_polls SET votes = IF(votes=0,0,votes-1) WHERE poll_id = %s", secure($poll['poll_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete notification */
      $this->delete_notification($post['author_id'], 'vote', 'post', $post['post_id']);
    }
  }


  /**
   * change_vote
   * 
   * @param integer $option_id
   * @param integer $checked_id
   * @return void
   */
  public function change_vote($option_id, $checked_id)
  {
    global $db;
    /* get poll */
    $get_poll = $db->query(sprintf("SELECT posts_polls.* FROM posts_polls_options INNER JOIN posts_polls ON posts_polls_options.poll_id = posts_polls.poll_id WHERE option_id = %s", secure($option_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_poll->num_rows == 0) {
      _error(403);
    }
    $poll = $get_poll->fetch_assoc();
    /* (check|get) post */
    $post = $this->_check_post($poll['post_id']);
    if (!$post) {
      _error(403);
    }
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }
    /* delete old vote */
    $db->query(sprintf("DELETE FROM posts_polls_options_users WHERE user_id = %s AND poll_id = %s AND option_id = %s", secure($this->_data['user_id'], 'int'), secure($poll['poll_id'], 'int'), secure($checked_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($db->affected_rows > 0) {
      /* insert new vote */
      $db->query(sprintf("INSERT INTO posts_polls_options_users (user_id, poll_id, option_id) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($poll['poll_id'], 'int'), secure($option_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * update_media_views
   * 
   * @param string $media_type
   * @param integer $video_id
   * @return void
   */
  public function update_media_views($media_type, $media_id)
  {
    global $db;
    switch ($media_type) {
      case 'video':
        $db->query(sprintf("UPDATE posts_videos SET views = views + 1 WHERE video_id = %s", secure($media_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'audio':
        $db->query(sprintf("UPDATE posts_audios SET views = views + 1 WHERE audio_id = %s", secure($media_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;
    }
  }



  /* ------------------------------- */
  /* Comments */
  /* ------------------------------- */

  /**
   * get_comments
   * 
   * @param integer $node_id
   * @param integer $offset
   * @param boolean $is_post
   * @param boolean $pass_privacy_check
   * @param array $post
   * @param string $sorting
   * @return array
   */
  public function get_comments($node_id, $offset = 0, $is_post = true, $pass_privacy_check = true, $post = [], $sorting = "recent", $last_comment_id = null)
  {
    global $db, $system;
    $comments = [];
    $offset *= $system['min_results'];
    switch ($sorting) {
      case 'top':
        $order_query = " ORDER BY posts_comments.reaction_like_count DESC, posts_comments.reaction_love_count DESC, posts_comments.reaction_haha_count DESC, posts_comments.reaction_yay_count DESC, posts_comments.reaction_wow_count DESC, posts_comments.reaction_sad_count DESC, posts_comments.reaction_angry_count DESC, posts_comments.replies DESC";
        break;

      case 'all':
        $order_query = " ORDER BY posts_comments.comment_id ASC ";
        break;

      default:
        $order_query = " ORDER BY posts_comments.comment_id DESC ";
        break;
    }
    /* get comments */
    if ($is_post) {
      /* get post comments */
      if (!$pass_privacy_check) {
        /* (check|get) post */
        $post = $this->_check_post($node_id, false);
        if (!$post) {
          return false;
        }
      }
      /* get post comments */
      if (!$last_comment_id) {
        $get_comments = $db->query(sprintf("SELECT posts_comments.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_verified, users.user_subscribed, packages.name as package_name, pages.* FROM posts_comments LEFT JOIN users ON posts_comments.user_id = users.user_id AND posts_comments.user_type = 'user' LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN pages ON posts_comments.user_id = pages.page_id AND posts_comments.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts_comments.node_type = 'post' AND posts_comments.node_id = %s " . $order_query . " LIMIT %s, %s", secure($node_id, 'int'), secure($offset, 'int', false), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
      } else {
        $get_comments = $db->query(sprintf("SELECT posts_comments.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_verified, users.user_subscribed, packages.name as package_name, pages.* FROM posts_comments LEFT JOIN users ON posts_comments.user_id = users.user_id AND posts_comments.user_type = 'user' LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN pages ON posts_comments.user_id = pages.page_id AND posts_comments.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts_comments.node_type = 'post' AND posts_comments.node_id = %s AND posts_comments.comment_id > %s " . $order_query, secure($node_id, 'int'), secure($last_comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      }
    } else {
      /* get photo comments */
      /* check privacy */
      if (!$pass_privacy_check) {
        /* (check|get) photo */
        $photo = $this->get_photo($node_id);
        if (!$photo) {
          _error(403);
        }
        $post = $photo['post'];
      }
      /* get photo comments */
      $get_comments = $db->query(sprintf("SELECT posts_comments.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_verified, users.user_subscribed, packages.name as package_name, pages.* FROM posts_comments LEFT JOIN users ON posts_comments.user_id = users.user_id AND posts_comments.user_type = 'user' LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN pages ON posts_comments.user_id = pages.page_id AND posts_comments.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts_comments.node_type = 'photo' AND posts_comments.node_id = %s " . $order_query . " LIMIT %s, %s", secure($node_id, 'int'), secure($offset, 'int', false), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_comments->num_rows == 0) {
      return $comments;
    }
    while ($comment = $get_comments->fetch_assoc()) {

      /* pass comments_disabled from post to comment */
      $comment['comments_disabled'] = $post['comments_disabled'];

      /* check if the page has been deleted */
      if ($comment['user_type'] == "page" && !$comment['page_admin']) {
        continue;
      }
      /* check if there is any blocking between the viewer & the comment author */
      if ($comment['user_type'] == "user" && $this->blocked($comment['user_id'])) {
        continue;
      }

      /* get reactions array */
      $comment['reactions']['like'] = $comment['reaction_like_count'];
      $comment['reactions']['love'] = $comment['reaction_love_count'];
      $comment['reactions']['haha'] = $comment['reaction_haha_count'];
      $comment['reactions']['yay'] = $comment['reaction_yay_count'];
      $comment['reactions']['wow'] = $comment['reaction_wow_count'];
      $comment['reactions']['sad'] = $comment['reaction_sad_count'];
      $comment['reactions']['angry'] = $comment['reaction_angry_count'];
      arsort($comment['reactions']);

      /* get total reactions */
      $comment['reactions_total_count'] = $comment['reaction_like_count'] + $comment['reaction_love_count'] + $comment['reaction_haha_count'] + $comment['reaction_yay_count'] + $comment['reaction_wow_count'] + $comment['reaction_sad_count'] + $comment['reaction_angry_count'];

      /* get replies */
      if ($comment['replies'] > 0) {
        $comment['comment_replies'] = $this->get_replies($comment['comment_id'], 0, true, $post);
      }

      /* parse text */
      $comment['text_plain'] = $comment['text'];
      $comment['text'] = $this->_parse(["text" => $comment['text']]);

      /* get the comment author */
      if ($comment['user_type'] == "user") {
        /* user type */
        $comment['author_id'] = $comment['user_id'];
        $comment['author_picture'] = get_picture($comment['user_picture'], $comment['user_gender']);
        $comment['author_url'] = $system['system_url'] . '/' . $comment['user_name'];
        $comment['author_name'] = ($system['show_usernames_enabled']) ? $comment['user_name'] : $comment['user_firstname'] . " " . $comment['user_lastname'];
        $comment['author_verified'] = $comment['user_verified'];
      } else {
        /* page type */
        $comment['author_id'] = $comment['page_admin'];
        $comment['author_picture'] = get_picture($comment['page_picture'], "page");
        $comment['author_url'] = $system['system_url'] . '/pages/' . $comment['page_name'];
        $comment['author_name'] = $comment['page_title'];
        $comment['author_verified'] = $comment['page_verified'];
      }

      /* check if viewer user react this comment */
      $comment['i_react'] = false;
      if ($this->_logged_in) {
        /* reaction */
        if ($comment['reactions_total_count'] > 0) {
          $get_reaction = $db->query(sprintf("SELECT reaction FROM posts_comments_reactions WHERE user_id = %s AND comment_id = %s", secure($this->_data['user_id'], 'int'), secure($comment['comment_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_reaction->num_rows > 0) {
            $comment['i_react'] = true;
            $comment['i_reaction'] = $get_reaction->fetch_assoc()['reaction'];
          }
        }
      }

      /* check if viewer can manage comment [Edit|Delete] */
      $comment['edit_comment'] = false;
      $comment['delete_comment'] = false;
      if ($this->_logged_in) {
        /* viewer is (admin|moderator) */
        if ($this->_data['user_group'] < 3) {
          $comment['edit_comment'] = true;
          $comment['delete_comment'] = true;
        }
        /* viewer is the author of comment */
        if ($this->_data['user_id'] == $comment['author_id']) {
          $comment['edit_comment'] = true;
          $comment['delete_comment'] = true;
        }
        /* viewer is the author of post */
        if ($this->_data['user_id'] == $post['author_id']) {
          $comment['delete_comment'] = true;
        }
        /* viewer is the admin of the group of the post */
        if ($post['in_group'] && $post['is_group_admin']) {
          $comment['delete_comment'] = true;
        }
        /* viewer is the admin of the event of the post */
        if ($post['in_event'] && $post['is_event_admin']) {
          $comment['delete_comment'] = true;
        }
      }

      $comments[] = $comment;
    }
    return $comments;
  }


  /**
   * get_replies
   * 
   * @param integer $comment_id
   * @param integer $offset
   * @param boolean $offset
   * @param array $post
   * @return array
   */
  public function get_replies($comment_id, $offset = 0, $pass_check = true, $post = [])
  {
    global $db, $system;
    $replies = [];
    $offset *= $system['min_results'];
    if (!$pass_check) {
      $comment = $this->get_comment($comment_id);
      if (!$comment) {
        _error(403);
      }
      $post_author_id = $comment['post']['author_id'];
    } else {
      $post_author_id = $post['author_id'];
    }
    /* get replies */
    $get_replies = $db->query(sprintf("SELECT * FROM (SELECT posts_comments.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_verified, users.user_subscribed, packages.name as package_name, pages.* FROM posts_comments LEFT JOIN users ON posts_comments.user_id = users.user_id AND posts_comments.user_type = 'user' LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id LEFT JOIN pages ON posts_comments.user_id = pages.page_id AND posts_comments.user_type = 'page' WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts_comments.node_type = 'comment' AND posts_comments.node_id = %s ORDER BY posts_comments.comment_id DESC LIMIT %s, %s) comments ORDER BY comments.comment_id ASC", secure($comment_id, 'int'), secure($offset, 'int', false), secure($system['min_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_replies->num_rows == 0) {
      return $replies;
    }
    while ($reply = $get_replies->fetch_assoc()) {

      /* pass comments_disabled from (post|comment) to reply */
      $reply['comments_disabled'] = (isset($post['comments_disabled'])) ? $post['comments_disabled'] : $comment['comments_disabled'];

      /* check if the page has been deleted */
      if ($reply['user_type'] == "page" && !$reply['page_admin']) {
        continue;
      }
      /* check if there is any blocking between the viewer & the comment author */
      if ($reply['user_type'] == "user" && $this->blocked($reply['user_id'])) {
        continue;
      }

      /* get reactions array */
      $reply['reactions']['like'] = $reply['reaction_like_count'];
      $reply['reactions']['love'] = $reply['reaction_love_count'];
      $reply['reactions']['haha'] = $reply['reaction_haha_count'];
      $reply['reactions']['yay'] = $reply['reaction_yay_count'];
      $reply['reactions']['wow'] = $reply['reaction_wow_count'];
      $reply['reactions']['sad'] = $reply['reaction_sad_count'];
      $reply['reactions']['angry'] = $reply['reaction_angry_count'];
      arsort($reply['reactions']);

      /* get total reactions */
      $reply['reactions_total_count'] = $reply['reaction_like_count'] + $reply['reaction_love_count'] + $reply['reaction_haha_count'] + $reply['reaction_yay_count'] + $reply['reaction_wow_count'] + $reply['reaction_sad_count'] + $reply['reaction_angry_count'];

      /* parse text */
      $reply['text_plain'] = $reply['text'];
      $reply['text'] = $this->_parse(["text" => $reply['text']]);

      /* get the reply author */
      if ($reply['user_type'] == "user") {
        /* user type */
        $reply['author_id'] = $reply['user_id'];
        $reply['author_picture'] = get_picture($reply['user_picture'], $reply['user_gender']);
        $reply['author_url'] = $system['system_url'] . '/' . $reply['user_name'];
        $reply['author_user_name'] = $reply['user_name'];
        $reply['author_name'] = ($system['show_usernames_enabled']) ? $reply['user_name'] : $reply['user_firstname'] . " " . $reply['user_lastname'];
        $reply['author_verified'] = $reply['user_verified'];
      } else {
        /* page type */
        $reply['author_id'] = $reply['page_admin'];
        $reply['author_picture'] = get_picture($reply['page_picture'], "page");
        $reply['author_url'] = $system['system_url'] . '/pages/' . $reply['page_name'];
        $reply['author_name'] = $reply['page_title'];
        $reply['author_verified'] = $reply['page_verified'];
      }

      /* check if viewer user react this reply */
      $reply['i_react'] = false;
      if ($this->_logged_in) {
        /* reaction */
        if ($reply['reactions_total_count'] > 0) {
          $get_reaction = $db->query(sprintf("SELECT reaction FROM posts_comments_reactions WHERE user_id = %s AND comment_id = %s", secure($this->_data['user_id'], 'int'), secure($reply['comment_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_reaction->num_rows > 0) {
            $reply['i_react'] = true;
            $reply['i_reaction'] = $get_reaction->fetch_assoc()['reaction'];
          }
        }
      }

      /* check if viewer can manage reply [Edit|Delete] */
      $reply['edit_comment'] = false;
      $reply['delete_comment'] = false;
      if ($this->_logged_in) {
        /* viewer is (admin|moderator) */
        if ($this->_data['user_group'] < 3) {
          $reply['edit_comment'] = true;
          $reply['delete_comment'] = true;
        }
        /* viewer is the author of reply */
        if ($this->_data['user_id'] == $reply['author_id']) {
          $reply['edit_comment'] = true;
          $reply['delete_comment'] = true;
        }
        /* viewer is the author of post */
        if ($this->_data['user_id'] == $post_author_id) {
          $reply['delete_comment'] = true;
        }
      }

      $replies[] = $reply;
    }
    return $replies;
  }


  /**
   * comment
   * 
   * @param string $handle
   * @param integer $node_id
   * @param string $message
   * @param string $photo
   * @param string $voice_note
   * @return array
   */
  public function comment($handle, $node_id, $message, $photo, $voice_note)
  {
    global $db, $system, $date;

    /* check max comments/hour limit */
    if ($system['max_comments_hour'] > 0 && $this->_data['user_group'] >= 3) {
      $check_limit = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_comments WHERE posts_comments.time >= DATE_SUB(NOW(),INTERVAL 1 HOUR) AND user_id = %s AND user_type = 'user'", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check_limit->fetch_assoc()['count'] >= $system['max_comments_hour']) {
        modal("MESSAGE", __("Maximum Limit Reached"), __("You have reached the maximum limit of comments/hour, please try again later"));
      }
    }

    /* check comment max length */
    if ($system['max_comment_length'] > 0 && $this->_data['user_group'] >= 3) {
      if (strlen($message) >= $system['max_comment_length']) {
        modal("MESSAGE", __("Text Length Limit Reached"), __("Your message characters length is over the allowed limit") . " (" . $system['max_comment_length'] . " " . __("Characters") . ")");
      }
    }

    /* default */
    $comment = [];
    $comment['node_id'] = $node_id;
    $comment['node_type'] = $handle;
    $comment['text'] = $message;
    $comment['image'] = $photo;
    $comment['voice_note'] = $voice_note;
    $comment['time'] = $date;
    $comment['reaction_like_count'] = 0;
    $comment['reaction_love_count'] = 0;
    $comment['reaction_haha_count'] = 0;
    $comment['reaction_yay_count'] = 0;
    $comment['reaction_wow_count'] = 0;
    $comment['reaction_sad_count'] = 0;
    $comment['reaction_angry_count'] = 0;
    $comment['reactions_total_count'] = 0;
    $comment['replies'] = 0;

    /* check the handle */
    switch ($handle) {
      case 'post':
        /* (check|get) post */
        $post = $this->_check_post($node_id, false, false);
        if (!$post) {
          _error(403);
        }
        break;

      case 'photo':
        /* (check|get) photo */
        $photo = $this->get_photo($node_id);
        if (!$photo) {
          _error(403);
        }
        $post = $photo['post'];
        break;

      case 'comment':
        /* (check|get) comment */
        $parent_comment = $this->get_comment($node_id, false);
        if (!$parent_comment) {
          _error(403);
        }
        $post = $parent_comment['post'];
        break;
    }

    /* check if comments disabled */
    if ($post['comments_disabled']) {
      throw new Exception(__("Comments disabled for this post"));
    }

    /* check if there is any blocking between the viewer & the target */
    if (($post['user_type'] == "user" && $this->blocked($post['author_id'])) || ($handle == "comment" && $this->blocked($parent_comment['author_id']))) {
      _error(403);
    }

    /* check if the viewer is page admin of the target post */
    if (!$post['is_page_admin']) {
      $comment['user_id'] = $this->_data['user_id'];
      $comment['user_type'] = "user";
      $comment['author_picture'] = $this->_data['user_picture'];
      $comment['author_url'] = $system['system_url'] . '/' . $this->_data['user_name'];
      $comment['author_user_name'] = $this->_data['user_name'];
      $comment['author_name'] = $this->_data['name'];
      $comment['author_verified'] = $this->_data['user_verified'];
    } else {
      $comment['user_id'] = $post['page_id'];
      $comment['user_type'] = "page";
      $comment['author_picture'] = get_picture($post['page_picture'], "page");
      $comment['author_url'] = $system['system_url'] . '/pages/' . $post['page_name'];
      $comment['author_name'] = $post['page_title'];
      $comment['author_verified'] = $post['page_verified'];
    }

    /* insert the comment */
    $db->query(sprintf("INSERT INTO posts_comments (node_id, node_type, user_id, user_type, text, image, voice_note, time) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", secure($comment['node_id'], 'int'), secure($comment['node_type']), secure($comment['user_id'], 'int'), secure($comment['user_type']), secure($comment['text']), secure($comment['image']), secure($comment['voice_note']), secure($comment['time']))) or _error('SQL_ERROR_THROWEN');
    $comment['comment_id'] = $db->insert_id;
    /* update (post|photo|comment) (comments|replies) counter */
    switch ($handle) {
      case 'post':
        $db->query(sprintf("UPDATE posts SET comments = comments + 1 WHERE post_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'photo':
        $db->query(sprintf("UPDATE posts_photos SET comments = comments + 1 WHERE photo_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'comment':
        $db->query(sprintf("UPDATE posts_comments SET replies = replies + 1 WHERE comment_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($parent_comment['node_type'] == "post") {
          $db->query(sprintf("UPDATE posts SET comments = comments + 1 WHERE post_id = %s", secure($parent_comment['node_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } elseif ($parent_comment['node_type'] == "photo") {
          $db->query(sprintf("UPDATE posts_photos SET comments = comments + 1 WHERE photo_id = %s", secure($parent_comment['node_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        break;
    }

    /* post notification */
    if ($handle == "comment") {
      $this->post_notification(['to_user_id' => $parent_comment['author_id'], 'from_user_id' => $comment['user_id'], 'from_user_type' => $comment['user_type'], 'action' => 'reply', 'node_type' => $parent_comment['node_type'], 'node_url' => $parent_comment['node_id'], 'notify_id' => 'comment_' . $comment['comment_id']]);
      if ($post['author_id'] != $parent_comment['author_id']) {
        $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'comment', 'node_type' => $parent_comment['node_type'], 'node_url' => $parent_comment['node_id'], 'notify_id' => 'comment_' . $comment['comment_id']]);
      }
    } else {
      $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'comment', 'node_type' => $handle, 'node_url' => $node_id, 'notify_id' => 'comment_' . $comment['comment_id']]);
    }

    /* post mention notifications if any */
    if ($handle == "comment") {
      $this->post_mentions($comment['text'], $parent_comment['node_id'], "reply_" . $parent_comment['node_type'], 'comment_' . $comment['comment_id'], [$post['author_id'], $parent_comment['author_id']]);
    } else {
      $this->post_mentions($comment['text'], $node_id, "comment_" . $handle, 'comment_' . $comment['comment_id'], [$post['author_id']]);
    }

    /* parse text */
    $comment['text_plain'] = htmlentities($comment['text'], ENT_QUOTES, 'utf-8');
    $comment['text'] = $this->_parse(["text" => $comment['text_plain']]);

    /* check if viewer can manage comment [Edit|Delete] */
    $comment['edit_comment'] = true;
    $comment['delete_comment'] = true;

    /* points balance */
    $this->points_balance("add", $this->_data['user_id'], "comment", $comment['comment_id']);

    /* return */
    return $comment;
  }


  /**
   * get_comment
   * 
   * @param integer $comment_id
   * @return array|false
   */
  public function get_comment($comment_id, $recursive = true)
  {
    global $db;
    /* get comment */
    $get_comment = $db->query(sprintf("SELECT posts_comments.*, pages.page_admin FROM posts_comments LEFT JOIN pages ON posts_comments.user_type = 'page' AND posts_comments.user_id = pages.page_id WHERE posts_comments.comment_id = %s", secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_comment->num_rows == 0) {
      return false;
    }
    $comment = $get_comment->fetch_assoc();

    /* check if the page has been deleted */
    if ($comment['user_type'] == "page" && !$comment['page_admin']) {
      return false;
    }
    /* check if there is any blocking between the viewer & the comment author */
    if ($comment['user_type'] == "user" && $this->blocked($comment['user_id'])) {
      return false;
    }

    /* get reactions array */
    $comment['reactions']['like'] = $comment['reaction_like_count'];
    $comment['reactions']['love'] = $comment['reaction_love_count'];
    $comment['reactions']['haha'] = $comment['reaction_haha_count'];
    $comment['reactions']['yay'] = $comment['reaction_yay_count'];
    $comment['reactions']['wow'] = $comment['reaction_wow_count'];
    $comment['reactions']['sad'] = $comment['reaction_sad_count'];
    $comment['reactions']['angry'] = $comment['reaction_angry_count'];
    arsort($comment['reactions']);

    /* get total reactions */
    $comment['reactions_total_count'] = $comment['reaction_like_count'] + $comment['reaction_love_count'] + $comment['reaction_haha_count'] + $comment['reaction_yay_count'] + $comment['reaction_wow_count'] + $comment['reaction_sad_count'] + $comment['reaction_angry_count'];

    /* get the author */
    $comment['author_id'] = ($comment['user_type'] == "page") ? $comment['page_admin'] : $comment['user_id'];

    /* get post */
    switch ($comment['node_type']) {
      case 'post':
        $post = $this->_check_post($comment['node_id'], false, false);
        /* check if the post has been deleted */
        if (!$post) {
          return false;
        }
        $comment['post'] = $post;
        break;

      case 'photo':
        /* (check|get) photo */
        $photo = $this->get_photo($comment['node_id']);
        if (!$photo) {
          return false;
        }
        $comment['post'] = $photo['post'];
        break;

      case 'comment':
        if (!$recursive) {
          return false;
        }
        /* (check|get) comment */
        $parent_comment = $this->get_comment($comment['node_id'], false);
        if (!$parent_comment) {
          return false;
        }
        $comment['parent_comment'] = $parent_comment;
        $comment['post'] = $parent_comment['post'];
        break;
    }

    /* pass comments_disabled from post to comment */
    $comment['comments_disabled'] = $comment['post']['comments_disabled'];

    /* check if viewer user react this comment */
    $comment['i_react'] = false;
    if ($this->_logged_in) {
      /* reaction */
      if ($comment['reactions_total_count'] > 0) {
        $get_reaction = $db->query(sprintf("SELECT reaction FROM posts_comments_reactions WHERE user_id = %s AND comment_id = %s", secure($this->_data['user_id'], 'int'), secure($comment['comment_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_reaction->num_rows > 0) {
          $comment['i_react'] = true;
          $comment['i_reaction'] = $get_reaction->fetch_assoc()['reaction'];
        }
      }
    }

    /* return */
    return $comment;
  }


  /**
   * delete_comment
   * 
   * @param integer $comment_id
   * @return void
   */
  public function delete_comment($comment_id)
  {
    global $db;
    /* (check|get) comment */
    $comment = $this->get_comment($comment_id);
    if (!$comment) {
      _error(403);
    }
    /* check if viewer can manage comment [Delete] */
    $comment['delete_comment'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $comment['delete_comment'] = true;
      }
      /* viewer is the author of comment */
      if ($this->_data['user_id'] == $comment['author_id']) {
        $comment['delete_comment'] = true;
      }
      /* viewer is the author of post */
      if ($this->_data['user_id'] == $comment['post']['author_id']) {
        $comment['delete_comment'] = true;
      }
      /* viewer is the admin of the group of the post */
      if ($comment['post']['in_group'] && $comment['post']['is_group_admin']) {
        $comment['delete_comment'] = true;
      }
      /* viewer is the admin of the event of the post */
      if ($comment['post']['in_event'] && $comment['post']['is_event_admin']) {
        $comment['delete_comment'] = true;
      }
    }
    /* delete the comment */
    if ($comment['delete_comment']) {
      /* delete the comment */
      $db->query(sprintf("DELETE FROM posts_comments WHERE comment_id = %s", secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete comment image from uploads folder */
      delete_uploads_file($comment['image']);
      /* delete replies */
      if ($comment['replies'] > 0) {
        $get_replies = $db->query(sprintf("SELECT image FROM posts_comments WHERE node_type = 'comment' AND node_id = %s", secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        while ($reply = $get_replies->fetch_assoc()) {
          /* delete reply image from uploads folder */
          delete_uploads_file($reply['image']);
        }
        $db->query(sprintf("DELETE FROM posts_comments WHERE node_type = 'comment' AND node_id = %s", secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      }
      /* update comments counter */
      switch ($comment['node_type']) {
        case 'post':
          $db->query(sprintf("UPDATE posts SET comments = IF(comments=0,0,comments-%s) WHERE post_id = %s", secure($comment['replies'] + 1, 'int'), secure($comment['node_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'photo':
          $db->query(sprintf("UPDATE posts_photos SET comments = IF(comments=0,0,comments-%s) WHERE photo_id = %s", secure($comment['replies'] + 1, 'int'), secure($comment['node_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'comment':
          $db->query(sprintf("UPDATE posts_comments SET replies = IF(replies=0,0,replies-1) WHERE comment_id = %s", secure($comment['node_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($comment['parent_comment']['node_type'] == "post") {
            $db->query(sprintf("UPDATE posts SET comments = IF(comments=0,0,comments-1) WHERE post_id = %s", secure($comment['parent_comment']['node_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          } elseif ($comment['parent_comment']['node_type'] == "photo") {
            $db->query(sprintf("UPDATE posts_photos SET comments = IF(comments=0,0,comments-1) WHERE photo_id = %s", secure($comment['parent_comment']['node_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          break;
      }
      /* points balance */
      $this->points_balance("delete", $comment['author_id'], "comment");
    }
  }


  /**
   * edit_comment
   * 
   * @param integer $comment_id
   * @param string $message
   * @param string $photo
   * @return array
   */
  public function edit_comment($comment_id, $message, $photo)
  {
    global $db, $system;
    /* (check|get) comment */
    $comment = $this->get_comment($comment_id);
    if (!$comment) {
      _error(403);
    }
    /* check if viewer can manage comment [Edit] */
    $comment['edit_comment'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $comment['edit_comment'] = true;
      }
      /* viewer is the author of comment */
      if ($this->_data['user_id'] == $comment['author_id']) {
        $comment['edit_comment'] = true;
      }
    }
    if (!$comment['edit_comment']) {
      _error(400);
    }
    /* check post max length */
    if ($system['max_comment_length'] > 0 && $this->_data['user_group'] >= 3) {
      if (strlen($message) >= $system['max_comment_length']) {
        modal("MESSAGE", __("Text Length Limit Reached"), __("Your message characters length is over the allowed limit") . " (" . $system['max_comment_length'] . " " . __("Characters") . ")");
      }
    }
    /* update comment */
    $comment['text'] = $message;
    $comment['image'] = (!is_empty($comment['image'])) ? $comment['image'] : $photo;
    $db->query(sprintf("UPDATE posts_comments SET text = %s, image = %s WHERE comment_id = %s", secure($comment['text']), secure($comment['image']), secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post mention notifications if any */
    if ($comment['node_type'] == "comment") {
      $this->post_mentions($comment['text'], $comment['parent_comment']['node_id'], "reply_" . $comment['parent_comment']['node_type'], 'comment_' . $comment['comment_id'], [$comment['post']['author_id'], $comment['parent_comment']['author_id']]);
    } else {
      $this->post_mentions($comment['text'], $comment['node_id'], "comment_" . $comment['node_type'], 'comment_' . $comment['comment_id'], [comment['post']['author_id']]);
    }
    /* parse text */
    $comment['text_plain'] = htmlentities($comment['text'], ENT_QUOTES, 'utf-8');
    $comment['text'] = $this->_parse(["text" => $comment['text_plain']]);
    /* return */
    return $comment;
  }


  /**
   * react_comment
   * 
   * @param integer $comment_id
   * @param string $reaction
   * @return void
   */
  public function react_comment($comment_id, $reaction)
  {
    global $db, $date;
    /* check reation */
    if (!in_array($reaction, ['like', 'love', 'haha', 'yay', 'wow', 'sad', 'angry'])) {
      _error(403);
    }
    /* (check|get) comment */
    $comment = $this->get_comment($comment_id);
    if (!$comment) {
      _error(403);
    }
    /* check blocking */
    if ($this->blocked($comment['author_id'])) {
      _error(403);
    }
    /* react the comment */
    if ($comment['i_react']) {
      $db->query(sprintf("DELETE FROM posts_comments_reactions WHERE user_id = %s AND comment_id = %s", secure($this->_data['user_id'], 'int'), secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update comment reaction counter */
      $reaction_field = "reaction_" . $comment['i_reaction'] . "_count";
      $db->query(sprintf("UPDATE posts_comments SET $reaction_field = IF($reaction_field=0,0,$reaction_field-1) WHERE comment_id = %s", secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete notification */
      switch ($comment['node_type']) {
        case 'post':
          $this->delete_notification($comment['author_id'], 'react_' . $comment['i_reaction'], 'post_comment', $comment['node_id']);
          break;

        case 'photo':
          $this->delete_notification($comment['author_id'], 'react_' . $comment['i_reaction'], 'photo_comment', $comment['node_id']);
          break;

        case 'comment':
          $_node_type = ($comment['parent_comment']['node_type'] == "post") ? "post_reply" : "photo_reply";
          $_node_url = $comment['parent_comment']['node_id'];
          $this->delete_notification($comment['author_id'], 'react_' . $comment['i_reaction'], $_node_type, $_node_url);
          break;
      }
    }
    $db->query(sprintf("INSERT INTO posts_comments_reactions (user_id, comment_id, reaction, reaction_time) VALUES (%s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($comment_id, 'int'), secure($reaction), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* update comment reaction counter */
    $reaction_field = "reaction_" . $reaction . "_count";
    $db->query(sprintf("UPDATE posts_comments SET $reaction_field = $reaction_field + 1 WHERE comment_id = %s", secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post notification */
    switch ($comment['node_type']) {
      case 'post':
        $this->post_notification(['to_user_id' => $comment['author_id'], 'action' => 'react_' . $reaction, 'node_type' => 'post_comment', 'node_url' => $comment['node_id'], 'notify_id' => 'comment_' . $comment_id]);
        break;

      case 'photo':
        $this->post_notification(['to_user_id' => $comment['author_id'], 'action' => 'react_' . $reaction, 'node_type' => 'photo_comment', 'node_url' => $comment['node_id'], 'notify_id' => 'comment_' . $comment_id]);
        break;

      case 'comment':
        $_node_type = ($comment['parent_comment']['node_type'] == "post") ? "post_reply" : "photo_reply";
        $_node_url = $comment['parent_comment']['node_id'];
        $this->post_notification(['to_user_id' => $comment['author_id'], 'action' => 'react_' . $reaction, 'node_type' => $_node_type, 'node_url' => $_node_url, 'notify_id' => 'comment_' . $comment_id]);
        break;
    }
  }


  /**
   * unreact_comment
   * 
   * @param integer $comment_id
   * @param string $reaction
   * @return void
   */
  public function unreact_comment($comment_id, $reaction)
  {
    global $db;
    /* check reation */
    if (!in_array($reaction, ['like', 'love', 'haha', 'yay', 'wow', 'sad', 'angry'])) {
      _error(403);
    }
    /* (check|get) comment */
    $comment = $this->get_comment($comment_id);
    if (!$comment) {
      _error(403);
    }
    /* check blocking */
    if ($this->blocked($comment['author_id'])) {
      _error(403);
    }
    /* unreact the comment */
    if ($comment['i_react']) {
      $db->query(sprintf("DELETE FROM posts_comments_reactions WHERE user_id = %s AND comment_id = %s", secure($this->_data['user_id'], 'int'), secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update comment reaction counter */
      $reaction_field = "reaction_" . $reaction . "_count";
      $db->query(sprintf("UPDATE posts_comments SET $reaction_field = IF($reaction_field=0,0,$reaction_field-1) WHERE comment_id = %s", secure($comment_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete notification */
      switch ($comment['node_type']) {
        case 'post':
          $this->delete_notification($comment['author_id'], 'react_' . $reaction, 'post_comment', $comment['node_id']);
          break;

        case 'photo':
          $this->delete_notification($comment['author_id'], 'react_' . $reaction, 'photo_comment', $comment['node_id']);
          break;

        case 'comment':
          $_node_type = ($comment['parent_comment']['node_type'] == "post") ? "post_reply" : "photo_reply";
          $_node_url = $comment['parent_comment']['node_id'];
          $this->delete_notification($comment['author_id'], 'react_' . $reaction, $_node_type, $_node_url);
          break;
      }
    }
  }



  /* ------------------------------- */
  /* Photos & Albums */
  /* ------------------------------- */

  /**
   * get_photos
   * 
   * @param integer $id
   * @param string $type
   * @param integer $offset
   * @param boolean $pass_check
   * @param boolean $get_pinned
   * @return array
   */
  public function get_photos($id, $type = 'user', $offset = 0, $pass_check = true, $get_pinned = false)
  {
    global $db, $system;
    $photos = [];
    switch ($type) {
      case 'album':
        $offset *= $system['max_results_even'];
        if (!$pass_check) {
          /* check the album */
          $album = $this->get_album($id, false);
          if (!$album) {
            return $photos;
          }
        }
        $get_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts_photos.blur, posts.user_id, posts.user_type, posts.privacy FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts_photos.album_id = %s AND posts.is_anonymous = '0' AND posts.is_paid = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1') ORDER BY posts_photos.photo_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_photos->num_rows > 0) {
          while ($photo = $get_photos->fetch_assoc()) {
            /* check the photo privacy */
            if ($photo['privacy'] == "public" || $photo['privacy'] == "custom") {
              if ($album) {
                $photo['manage'] = $album['manage_album'];
              }
              $photos[] = $photo;
            } else {
              /* check the photo privacy */
              if ($this->check_privacy($photo['privacy'], $photo['user_id'])) {
                if ($album) {
                  $photo['manage'] = $album['manage_album'];
                }
                $photos[] = $photo;
              }
            }
          }
        }
        break;

      case 'user':
        /* get the target user's privacy */
        $get_privacy = $db->query(sprintf("SELECT user_privacy_photos FROM users WHERE user_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        $privacy = $get_privacy->fetch_assoc();
        /* check the target user's privacy */
        if (!$this->check_privacy($privacy['user_privacy_photos'], $id)) {
          return $photos;
        }
        /* check manage photos */
        $manage_photos = false;
        if ($this->_logged_in) {
          /* viewer is (admin|moderator) */
          if ($this->_data['user_group'] < 3) {
            $manage_photos = true;
          }
          /* viewer is the author of photos */
          if ($this->_data['user_id'] == $id) {
            $manage_photos = true;
          }
        }
        /* get all user photos (except photos from groups, events, anonymous posts & paid posts) */
        $offset *= $system['min_results_even'];
        $order_statement = ($get_pinned) ? "posts_photos.pinned DESC, posts_photos.photo_id DESC" : "posts_photos.photo_id DESC";
        $get_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts_photos.blur, posts_photos.pinned, posts.privacy FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'user' AND posts.in_group = '0' AND posts.in_event = '0' AND posts.is_anonymous = '0' AND posts.is_paid = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1') ORDER BY $order_statement LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_photos->num_rows > 0) {
          while ($photo = $get_photos->fetch_assoc()) {
            if ($this->check_privacy($photo['privacy'], $id)) {
              $photo['manage'] = $manage_photos;
              $photos[] = $photo;
            }
          }
        }
        break;

      case 'page':
        /* check manage photos */
        $manage_photos = false;
        if ($this->_logged_in) {
          /* viewer is (admin|moderator) */
          if ($this->_data['user_group'] < 3) {
            $manage_photos = true;
          }
          /* viewer is the author of photos */
          if ($this->check_page_adminship($this->_data['user_id'], $id)) {
            $manage_photos = true;
          }
        }
        /* get all page photos */
        $offset *= $system['min_results_even'];
        $get_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts_photos.blur FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'page' AND posts.is_paid = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1') ORDER BY posts_photos.photo_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_photos->num_rows > 0) {
          while ($photo = $get_photos->fetch_assoc()) {
            $photo['manage'] = $manage_photos;
            $photos[] = $photo;
          }
        }
        break;

      case 'group':
        if (!$pass_check) {
          /* check if the viewer is group member (approved) */
          if ($this->check_group_membership($this->_data['user_id'], $id) != "approved") {
            return $photos;
          }
        }
        /* check manage photos */
        $manage_photos = false;
        if ($this->_logged_in) {
          /* viewer is (admin|moderator) */
          if ($this->_data['user_group'] < 3) {
            $manage_photos = true;
          }
          /* viewer is the author of photos */
          if ($this->check_group_adminship($this->_data['user_id'], $id)) {
            $manage_photos = true;
          }
        }
        /* get all group photos */
        $offset *= $system['min_results_even'];
        $get_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts_photos.blur FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.group_id = %s AND posts.in_group = '1' AND posts.is_paid = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1') ORDER BY posts_photos.photo_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_photos->num_rows > 0) {
          while ($photo = $get_photos->fetch_assoc()) {
            $photo['manage'] = $manage_photos;
            $photos[] = $photo;
          }
        }
        break;

      case 'event':
        if (!$pass_check) {
          /* check if the viewer is event member (approved) */
          if (!$this->check_event_membership($this->_data['user_id'], $id)) {
            return $photos;
          }
        }
        /* check manage photos */
        $manage_photos = false;
        if ($this->_logged_in) {
          /* viewer is (admin|moderator) */
          if ($this->_data['user_group'] < 3) {
            $manage_photos = true;
          }
          /* viewer is the author of photos */
          if ($this->check_event_adminship($this->_data['user_id'], $id)) {
            $manage_photos = true;
          }
        }
        /* get all event photos */
        $offset *= $system['min_results_even'];
        $get_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts_photos.blur FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.event_id = %s AND posts.in_event = '1' AND posts.is_paid = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1') ORDER BY posts_photos.photo_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_photos->num_rows > 0) {
          while ($photo = $get_photos->fetch_assoc()) {
            $photo['manage'] = $manage_photos;
            $photos[] = $photo;
          }
        }
        break;
    }
    return $photos;
  }


  /**
   * get_photos_count
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return integer
   */
  public function get_photos_count($node_id, $node_type)
  {
    global $db;
    switch ($node_type) {
      case 'user':
        $get_photos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'user' AND posts.in_group = '0' AND posts.in_event = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page':
        $get_photos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'page' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        $get_photos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.group_id = %s AND posts.in_group = '1' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event':
        $get_photos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.event_id = %s AND posts.in_event = '1' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      default:
        _error(400);
        break;
    }
    if ($get_photos->num_rows > 0) {
      $count = $get_photos->fetch_assoc();
      return $count['count'];
    }
    return 0;
  }


  /**
   * get_photo
   * 
   * @param integer $photo_id
   * @param boolean $full_details
   * @param boolean $get_gallery
   * @param string $context
   * @return array
   */
  public function get_photo($photo_id, $full_details = false, $get_gallery = false, $context = 'photos')
  {
    global $db, $system;

    /* get photo */
    $get_photo = $db->query(sprintf("SELECT * FROM posts_photos WHERE photo_id = %s", secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_photo->num_rows == 0) {
      return false;
    }
    $photo = $get_photo->fetch_assoc();

    /* get post */
    $post = $this->_check_post($photo['post_id'], false, $full_details);
    if (!$post) {
      return false;
    }

    /* check if photo can be deleted */
    if ($post['in_group']) {
      /* check if (cover|profile) photo */
      $photo['can_delete'] = (($photo_id == $post['group_picture_id']) or ($photo_id == $post['group_cover_id'])) ? false : true;
    } elseif ($post['in_event']) {
      /* check if (cover) photo */
      $photo['can_delete'] = (($photo_id == $post['event_cover_id'])) ? false : true;
    } elseif ($post['user_type'] == "user") {
      /* check if (cover|profile) photo */
      $photo['can_delete'] = (($photo_id == $post['user_picture_id']) or ($photo_id == $post['user_cover_id'])) ? false : true;
    } elseif ($post['user_type'] == "page") {
      /* check if (cover|profile) photo */
      $photo['can_delete'] = (($photo_id == $post['page_picture_id']) or ($photo_id == $post['page_cover_id'])) ? false : true;
    }

    /* check photo type [single|mutiple] */
    $check_single = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_photos WHERE post_id = %s", secure($photo['post_id'], 'int')))  or _error('SQL_ERROR_THROWEN');
    $photo['is_single'] = ($check_single->fetch_assoc()['count'] > 1) ? false : true;

    /* get reactions */
    if ($photo['is_single']) {
      /* [Case: 1] single photo => get (reactions) of post */

      /* get reactions array */
      $photo['reactions'] = $post['reactions'];

      /* get total reactions */
      $photo['reactions_total_count'] = $post['reactions_total_count'];

      /* check if viewer [reacted] this post */
      $photo['i_react'] = $post['i_react'];
      $photo['i_reaction'] = $post['i_reaction'];
    } else {
      /* [Case: 2] mutiple photo => get (reactions) of photo */

      /* get reactions array */
      $photo['reactions']['like'] = $photo['reaction_like_count'];
      $photo['reactions']['love'] = $photo['reaction_love_count'];
      $photo['reactions']['haha'] = $photo['reaction_haha_count'];
      $photo['reactions']['yay'] = $photo['reaction_yay_count'];
      $photo['reactions']['wow'] = $photo['reaction_wow_count'];
      $photo['reactions']['sad'] = $photo['reaction_sad_count'];
      $photo['reactions']['angry'] = $photo['reaction_angry_count'];
      arsort($photo['reactions']);

      /* get total reactions */
      $photo['reactions_total_count'] = $photo['reaction_like_count'] + $photo['reaction_love_count'] + $photo['reaction_haha_count'] + $photo['reaction_yay_count'] + $photo['reaction_wow_count'] + $photo['reaction_sad_count'] + $photo['reaction_angry_count'];

      /* check if viewer [reacted] this photo */
      $photo['i_react'] = false;
      if ($this->_logged_in) {
        /* reaction */
        $get_reaction = $db->query(sprintf("SELECT reaction FROM posts_photos_reactions WHERE user_id = %s AND photo_id = %s", secure($this->_data['user_id'], 'int'), secure($photo['photo_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_reaction->num_rows > 0) {
          $photo['i_react'] = true;
          $photo['i_reaction'] = $get_reaction->fetch_assoc()['reaction'];
        }
      }
    }

    /* get full details (comments) */
    if ($full_details) {
      if ($photo['is_single']) {
        /* [Case: 1] single photo => get (comments) of post */

        /* get total comments */
        $photo['comments'] = $post['comments'];

        /* get post comments */
        if ($post['comments'] > 0) {
          $post['post_comments'] = $this->get_comments($post['post_id'], 0, true, true, $post);
        }
      } else {
        /* [Case: 2] mutiple photo => get (comments) of photo */

        /* get photo comments */
        if ($photo['comments'] > 0) {
          $photo['photo_comments'] = $this->get_comments($photo['photo_id'], 0, false, true, $post);
        }
      }
    }

    /* get gallery */
    if ($get_gallery) {
      switch ($context) {
        case 'post':
          $get_post_photos = $db->query(sprintf("SELECT photo_id, source FROM posts_photos WHERE post_id = %s ORDER BY photo_id ASC", secure($post['post_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          while ($post_photo = $get_post_photos->fetch_assoc()) {
            $post_photos[$post_photo['photo_id']] = $post_photo;
          }
          $photo['next'] = $post_photos[get_array_key($post_photos, $photo['photo_id'], 1)];
          $photo['prev'] = $post_photos[get_array_key($post_photos, $photo['photo_id'], -1)];
          break;

        case 'album':
          $get_album_photos = $db->query(sprintf("SELECT posts_photos.post_id, posts_photos.photo_id, posts_photos.source, posts.user_id, posts.privacy FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts_photos.album_id = %s", secure($photo['album_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          while ($album_photo = $get_album_photos->fetch_assoc()) {
            /* check the photo privacy */
            if ($album_photo['privacy'] == "public" || $album_photo['privacy'] == "custom") {
              $album_photos[$album_photo['photo_id']] = $album_photo;
            } else {
              /* check the photo privacy */
              if ($this->check_privacy($album_photo['privacy'], $album_photo['user_id'])) {
                $album_photos[$album_photo['photo_id']] = $album_photo;
              }
            }
          }
          $photo['next'] = ($post['is_anonymous']) ? null : $album_photos[get_array_key($album_photos, $photo['photo_id'], -1)];
          $photo['prev'] = ($post['is_anonymous']) ? null : $album_photos[get_array_key($album_photos, $photo['photo_id'], 1)];
          /* check the next & prev photos posts (if paid or for subscribers) */
          if ($photo['next']) {
            $next_post = $this->_check_post($photo['next']['post_id'], false, false);
            if ($next_post['needs_subscription'] || $next_post['needs_subscription']) {
              $photo['next'] = null;
            }
          }
          if ($photo['prev']) {
            $prev_post = $this->_check_post($photo['prev']['post_id'], false, false);
            if ($prev_post['needs_subscription'] || $prev_post['needs_subscription']) {
              $photo['prev'] = null;
            }
          }
          break;

        case 'photos':
          if ($post['in_group']) {
            $get_target_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts.user_id, posts.privacy FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.in_group = '1' AND posts.group_id = %s", secure($post['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          } elseif ($post['in_event']) {
            $get_target_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts.user_id, posts.privacy FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.in_event = '1' AND posts.event_id = %s", secure($post['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          } elseif ($post['user_type'] == "page") {
            $get_target_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts.user_id, posts.privacy FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.user_type = 'page' AND posts.user_id = %s", secure($post['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          } elseif ($post['user_type'] == "user") {
            $get_target_photos = $db->query(sprintf("SELECT posts_photos.photo_id, posts_photos.source, posts.user_id, posts.privacy FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE posts.user_type = 'user' AND posts.user_id = %s", secure($post['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
          while ($target_photo = $get_target_photos->fetch_assoc()) {
            /* check the photo privacy */
            if ($target_photo['privacy'] == "public" || $target_photo['privacy'] == "custom") {
              $target_photos[$target_photo['photo_id']] = $target_photo;
            } else {
              /* check the photo privacy */
              if ($this->check_privacy($target_photo['privacy'], $target_photo['user_id'])) {
                $target_photos[$target_photo['photo_id']] = $target_photo;
              }
            }
          }
          $photo['next'] = $target_photos[get_array_key($target_photos, $photo['photo_id'], -1)];
          $photo['prev'] = $target_photos[get_array_key($target_photos, $photo['photo_id'], 1)];
          break;
      }
    }

    /* og-meta tags */
    $photo['og_title'] = $post['post_author_name'];
    $photo['og_title'] .= ($post['text'] != "") ? " - " . $post['text'] : "";
    $photo['og_description'] = $post['text'];
    $photo['og_image'] = $system['system_uploads'] . '/' . $photo['source'];

    /* return post array with photo */
    $photo['post'] = $post;
    return $photo;
  }


  /**
   * delete_photo
   * 
   * @param integer $photo_id
   * @return void
   */
  public function delete_photo($photo_id)
  {
    global $db, $system;
    /* (check|get) photo */
    $photo = $this->get_photo($photo_id);
    if (!$photo) {
      _error(403);
    }
    $post = $photo['post'];
    /* check if viewer can manage post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if photo can be deleted */
    if (!$photo['can_delete']) {
      throw new Exception(__("This photo can't be deleted as it maybe your current profile image or cover"));
    }
    /* delete the photo */
    $db->query(sprintf("DELETE FROM posts_photos WHERE photo_id = %s", secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete photo from uploads folder */
    delete_uploads_file($photo['source']);
  }


  /**
   * pin_photo
   * 
   * @param integer $photo_id
   * @return void
   */
  public function pin_photo($photo_id)
  {
    global $db, $system;
    /* (check|get) photo */
    $photo = $this->get_photo($photo_id);
    if (!$photo) {
      _error(403);
    }
    $post = $photo['post'];
    /* check if viewer can manage post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* pin the photo */
    $db->query(sprintf("UPDATE posts_photos SET pinned = '1' WHERE photo_id = %s", secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * unpin_photo
   * 
   * @param integer $photo_id
   * @return void
   */
  public function unpin_photo($photo_id)
  {
    global $db, $system;
    /* (check|get) photo */
    $photo = $this->get_photo($photo_id);
    if (!$photo) {
      _error(403);
    }
    $post = $photo['post'];
    /* check if viewer can manage post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* unpin the photo */
    $db->query(sprintf("UPDATE posts_photos SET pinned = '0' WHERE photo_id = %s", secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * react_photo
   * 
   * @param integer $photo_id
   * @param string $reaction
   * @return void
   */
  public function react_photo($photo_id, $reaction)
  {
    global $db, $date;
    /* check reation */
    if (!in_array($reaction, ['like', 'love', 'haha', 'yay', 'wow', 'sad', 'angry'])) {
      _error(403);
    }
    /* (check|get) photo */
    $photo = $this->get_photo($photo_id);
    if (!$photo) {
      _error(403);
    }
    $post = $photo['post'];
    /* check blocking */
    if ($post['user_type'] == "user" && $this->blocked($post['author_id'])) {
      _error(403);
    }

    /* reaction the post */
    if ($photo['i_react']) {
      /* remove any previous reaction */
      $db->query(sprintf("DELETE FROM posts_photos_reactions WHERE user_id = %s AND photo_id = %s", secure($this->_data['user_id'], 'int'), secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* update photo reaction counter */
      $reaction_field = "reaction_" . $photo['i_reaction'] . "_count";
      $db->query(sprintf("UPDATE posts_photos SET $reaction_field = IF($reaction_field=0,0,$reaction_field-1) WHERE photo_id = %s", secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* delete notification */
      $this->delete_notification($post['author_id'], 'react_' . $photo['i_reaction'], 'photo', $photo_id);
    }
    $db->query(sprintf("INSERT INTO posts_photos_reactions (user_id, photo_id, reaction, reaction_time) VALUES (%s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($photo_id, 'int'), secure($reaction), secure($date))) or _error('SQL_ERROR_THROWEN');
    $reaction_id = $db->insert_id;
    /* update photo reaction counter */
    $reaction_field = "reaction_" . $reaction . "_count";
    $db->query(sprintf("UPDATE posts_photos SET $reaction_field = $reaction_field + 1 WHERE photo_id = %s", secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post notification */
    $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'react_' . $reaction, 'node_type' => 'photo', 'node_url' => $photo_id]);
    /* points balance */
    $this->points_balance("add", $this->_data['user_id'], "posts_photos_reactions", $reaction_id);
  }


  /**
   * unreact_photo
   * 
   * @param integer $photo_id
   * @param string $reaction
   * @return void
   */
  public function unreact_photo($photo_id, $reaction)
  {
    global $db;
    /* (check|get) photo */
    $photo = $this->get_photo($photo_id);
    if (!$photo) {
      _error(403);
    }
    $post = $photo['post'];
    /* unreact the photo */
    $db->query(sprintf("DELETE FROM posts_photos_reactions WHERE user_id = %s AND photo_id = %s", secure($this->_data['user_id'], 'int'), secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update photo reaction counter */
    $reaction_field = "reaction_" . $photo['i_reaction'] . "_count";
    $db->query(sprintf("UPDATE posts_photos SET $reaction_field = IF($reaction_field=0,0,$reaction_field-1) WHERE photo_id = %s", secure($photo_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete notification */
    $this->delete_notification($post['author_id'], 'react_' . $reaction, 'photo', $photo_id);
    /* points balance */
    $this->points_balance("delete", $this->_data['user_id'], "posts_photos_reactions");
  }


  /**
   * get_albums
   * 
   * @param integer $user_id
   * @param string $type
   * @param integer $offset
   * @return array
   */
  public function get_albums($id, $type = 'user', $offset = 0)
  {
    global $db, $system;
    /* initialize vars */
    $albums = [];
    $offset *= $system['max_results_even'];
    switch ($type) {
      case 'user':
        $get_albums = $db->query(sprintf("SELECT album_id FROM posts_photos_albums WHERE user_type = 'user' AND user_id = %s AND in_group = '0' AND in_event = '0' LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page':
        $get_albums = $db->query(sprintf("SELECT album_id FROM posts_photos_albums WHERE user_type = 'page' AND user_id = %s LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        $get_albums = $db->query(sprintf("SELECT album_id FROM posts_photos_albums WHERE in_group = '1' AND group_id = %s LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event':
        $get_albums = $db->query(sprintf("SELECT album_id FROM posts_photos_albums WHERE in_event = '1' AND event_id = %s LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;
    }
    if ($get_albums->num_rows > 0) {
      while ($album = $get_albums->fetch_assoc()) {
        $album = $this->get_album($album['album_id'], false); /* $full_details = false */
        if ($album) {
          $albums[] = $album;
        }
      }
    }
    return $albums;
  }


  /**
   * get_album
   * 
   * @param integer $album_id
   * @param boolean $full_details
   * @return array
   */
  public function get_album($album_id, $full_details = true)
  {
    global $db, $system;
    $get_album = $db->query(sprintf("SELECT posts_photos_albums.*, users.user_name, users.user_album_pictures, users.user_album_covers, users.user_album_timeline, pages.page_id, pages.page_name, pages.page_admin, pages.page_album_pictures, pages.page_album_covers, pages.page_album_timeline, `groups`.group_name, `groups`.group_admin, `groups`.group_album_pictures, `groups`.group_album_covers, `groups`.group_album_timeline, `events`.event_admin, `events`.event_album_covers, `events`.event_album_timeline FROM posts_photos_albums LEFT JOIN users ON posts_photos_albums.user_id = users.user_id AND posts_photos_albums.user_type = 'user' LEFT JOIN pages ON posts_photos_albums.user_id = pages.page_id AND posts_photos_albums.user_type = 'page' LEFT JOIN `groups` ON posts_photos_albums.in_group = '1' AND posts_photos_albums.group_id = `groups`.group_id LEFT JOIN `events` ON posts_photos_albums.in_event = '1' AND posts_photos_albums.event_id = `events`.event_id WHERE NOT (users.user_name <=> NULL AND pages.page_name <=> NULL) AND posts_photos_albums.album_id = %s", secure($album_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_album->num_rows == 0) {
      return false;
    }
    $album = $get_album->fetch_assoc();
    /* get the author */
    $album['author_id'] = ($album['user_type'] == "page") ? $album['page_admin'] : $album['user_id'];
    /* check the album privacy  */
    /* if album in group & (the group is public || the viewer approved member of this group) => pass privacy check */
    if ($album['in_group'] && ($album['group_privacy'] == 'public' || $this->check_group_membership($this->_data['user_id'], $album['group_id']) == 'approved')) {
      $pass_privacy_check = true;
    }
    /* if album in event & (the event is public || the viewer member of this event) => pass privacy check */
    if ($album['in_event'] && ($album['event_privacy'] == 'public' || $this->check_event_membership($this->_data['user_id'], $album['event_id']))) {
      $pass_privacy_check = true;
    }
    if (!$pass_privacy_check) {
      if (!$this->check_privacy($album['privacy'], $album['author_id'])) {
        return false;
      }
    }
    /* get album path */
    if ($album['in_group']) {
      $album['path'] = 'groups/' . $album['group_name'];
      /* check if (cover|profile|timeline) album */
      $album['can_delete'] = (($album_id == $album['group_album_pictures']) or ($album_id == $album['group_album_covers']) or ($album_id == $album['group_album_timeline'])) ? false : true;
    } elseif ($album['in_event']) {
      $album['path'] = 'events/' . $album['event_id'];
      /* check if (cover|profile|timeline) album */
      $album['can_delete'] = (($album_id == $album['event_album_pictures']) or ($album_id == $album['event_album_covers']) or ($album_id == $album['event_album_timeline'])) ? false : true;
    } elseif ($album['user_type'] == "user") {
      $album['path'] = $album['user_name'];
      /* check if (cover|profile|timeline) album */
      $album['can_delete'] = (($album_id == $album['user_album_pictures']) or ($album_id == $album['user_album_covers']) or ($album_id == $album['user_album_timeline'])) ? false : true;
    } elseif ($album['user_type'] == "page") {
      $album['path'] = 'pages/' . $album['page_name'];
      /* check if (cover|profile|timeline) album */
      $album['can_delete'] = (($album_id == $album['user_album_timeline']) or ($album_id == $album['page_album_covers']) or ($album_id == $album['page_album_timeline'])) ? false : true;
    }
    /* get album cover photo */
    $where_statement = ($album['user_type'] == "user" && !$album['in_group'] && !$album['in_event']) ? "posts.privacy = 'public' AND" : '';
    $get_cover = $db->query(sprintf("SELECT source, blur FROM posts_photos INNER JOIN posts ON posts_photos.post_id = posts.post_id WHERE " . $where_statement . " posts_photos.album_id = %s AND posts.is_paid = '0' ORDER BY photo_id DESC LIMIT 1", secure($album_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_cover->num_rows == 0) {
      $album['cover']['source'] = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_album.png';
      $album['cover']['blur'] = 0;
    } else {
      $cover = $get_cover->fetch_assoc();
      $album['cover']['source'] = $system['system_uploads'] . '/' . $cover['source'];
      $album['cover']['blur'] = $cover['blur'];
    }
    /* get album total photos count */
    $get_album_photos_count = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_photos WHERE album_id = %s", secure($album_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $album['photos_count'] = $get_album_photos_count->fetch_assoc()['count'];
    /* check if viewer can manage album [Edit|Update|Delete] */
    $album['is_page_admin'] = $this->check_page_adminship($this->_data['user_id'], $album['page_id']);
    $album['is_group_admin'] = $this->check_group_adminship($this->_data['user_id'], $album['group_id']);
    $album['is_event_admin'] = $this->check_event_adminship($this->_data['user_id'], $album['event_id']);
    /* check if viewer can manage album [Edit|Update|Delete] */
    $album['manage_album'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $album['manage_album'] = true;
      }
      /* viewer is the author of post */
      if ($this->_data['user_id'] == $album['author_id']) {
        $album['manage_album'] = true;
      }
      /* viewer is the admin of the page */
      if ($album['user_type'] == "page" && $album['is_page_admin']) {
        $album['manage_album'] = true;
      }
      /* viewer is the admin of the group */
      if ($album['in_group'] && $album['is_group_admin']) {
        $album['manage_album'] = true;
      }
      /* viewer is the admin of the event */
      if ($album['in_event'] && $album['is_event_admin']) {
        $album['manage_album'] = true;
      }
    }
    /* get album photos */
    if ($full_details) {
      $album['photos'] = $this->get_photos($album_id, 'album');
    }
    return $album;
  }


  /**
   * delete_album
   * 
   * @param integer $album_id
   * @return void
   */
  public function delete_album($album_id)
  {
    global $db, $system;
    /* (check|get) album */
    $album = $this->get_album($album_id, false);
    if (!$album) {
      _error(403);
    }
    /* check if viewer can manage album */
    if (!$album['manage_album']) {
      _error(403);
    }
    /* check if album can be deleted */
    if (!$album['can_delete']) {
      throw new Exception(__("This album can't be deleted"));
    }
    /* delete the album */
    $db->query(sprintf("DELETE FROM posts_photos_albums WHERE album_id = %s", secure($album_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete all album photos */
    $db->query(sprintf("DELETE FROM posts_photos WHERE album_id = %s", secure($album_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* retrun path */
    $path = $system['system_url'] . "/" . $album['path'] . "/albums";
    return $path;
  }


  /**
   * edit_album
   * 
   * @param integer $album_id
   * @param string $title
   * @return void
   */
  public function edit_album($album_id, $title)
  {
    global $db;
    /* (check|get) album */
    $album = $this->get_album($album_id, false);
    if (!$album) {
      _error(400);
    }
    /* check if viewer can manage album */
    if (!$album['manage_album']) {
      _error(400);
    }
    /* validate all fields */
    if (is_empty($title)) {
      throw new Exception(__("You must fill in all of the fields"));
    }
    /* edit the album */
    $db->query(sprintf("UPDATE posts_photos_albums SET title = %s WHERE album_id = %s", secure($title), secure($album_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * add_photos
   * 
   * @param array $args
   * @return array
   */
  public function add_album_photos($args = [])
  {
    global $db, $system, $date;
    /* (check|get) album */
    $album = $this->get_album($args['album_id'], false);
    if (!$album) {
      _error(400);
    }
    /* check if viewer can manage album */
    if (!$album['manage_album']) {
      _error(400);
    }
    /* check user_id */
    $user_id = ($album['user_type'] == "page") ? $album['page_id'] : $album['user_id'];
    /* check privacy */
    if ($album['in_group'] || $album['in_event']) {
      $args['privacy'] = 'custom';
    } elseif ($album['user_type'] == "page") {
      $args['privacy'] = 'public';
    } else {
      if (!in_array($args['privacy'], ['me', 'friends', 'public'])) {
        $args['privacy'] = 'public';
      }
    }
    /* post feeling */
    $post['feeling_action'] = '';
    $post['feeling_value'] = '';
    $post['feeling_icon'] = '';
    if (!is_empty($args['feeling_action']) && !is_empty($args['feeling_value'])) {
      if ($args['feeling_action'] != "Feeling") {
        $_feeling_icon = get_feeling_icon($args['feeling_action'], get_feelings());
      } else {
        $_feeling_icon = get_feeling_icon($args['feeling_value'], get_feelings_types());
      }
      if ($_feeling_icon) {
        $post['feeling_action'] = $args['feeling_action'];
        $post['feeling_value'] = $args['feeling_value'];
        $post['feeling_icon'] = $_feeling_icon;
      }
    }
    /* insert the post */
    $db->query(sprintf("INSERT INTO posts (user_id, user_type, in_group, group_id, in_event, event_id, post_type, time, location, privacy, text, feeling_action, feeling_value) VALUES (%s, %s, %s, %s, %s, %s, 'album', %s, %s, %s, %s, %s, %s)", secure($user_id, 'int'), secure($album['user_type']), secure($album['in_group']), secure($album['group_id'], 'int'), secure($album['in_event']), secure($album['event_id'], 'int'), secure($date), secure($args['location']), secure($args['privacy']), secure($args['message']), secure($post['feeling_action']), secure($post['feeling_value']))) or _error('SQL_ERROR_THROWEN');
    $post_id = $db->insert_id;
    /* insert new photos */
    foreach ($args['photos'] as $photo) {
      $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($args['album_id'], 'int'), secure($photo['source']), secure($photo['blur']))) or _error('SQL_ERROR_THROWEN');
    }
    /* post mention notifications */
    $this->post_mentions($args['message'], $post_id);
    return $post_id;
  }



  /* ------------------------------- */
  /* Videos */
  /* ------------------------------- */

  /**
   * get_videos
   * 
   * @param integer $id
   * @param string $type
   * @param integer $offset
   * @return array
   */
  public function get_videos($id, $type = 'user', $offset = 0)
  {
    global $db, $system;
    $videos = [];
    switch ($type) {
      case 'user':
        /* get all user videos (except videos from groups or events) */
        $offset *= $system['min_results_even'];
        $get_videos = $db->query(sprintf("SELECT posts_videos.*, posts.privacy FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'user' AND posts.in_group = '0' AND posts.in_event = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1') ORDER BY posts_videos.video_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_videos->num_rows > 0) {
          while ($video = $get_videos->fetch_assoc()) {
            if ($this->check_privacy($video['privacy'], $id)) {
              $videos[] = $video;
            }
          }
        }
        break;

      case 'page':
        $offset *= $system['min_results_even'];
        $get_videos = $db->query(sprintf("SELECT posts_videos.* FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'page' ORDER BY posts_videos.video_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_videos->num_rows > 0) {
          while ($video = $get_videos->fetch_assoc()) {
            $videos[] = $video;
          }
        }
        break;

      case 'group':
        /* check if the viewer is group member (approved) */
        if ($this->check_group_membership($this->_data['user_id'], $id) != "approved") {
          return $videos;
        }
        $offset *= $system['min_results_even'];
        $get_videos = $db->query(sprintf("SELECT posts_videos.* FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.group_id = %s AND posts.in_group = '1' ORDER BY posts_videos.video_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_videos->num_rows > 0) {
          while ($video = $get_videos->fetch_assoc()) {
            $videos[] = $video;
          }
        }
        break;

      case 'event':
        /* check if the viewer is event member (approved) */
        if (!$this->check_event_membership($this->_data['user_id'], $id)) {
          return $videos;
        }
        $offset *= $system['min_results_even'];
        $get_videos = $db->query(sprintf("SELECT posts_videos.* FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.event_id = %s AND posts.in_event = '1' ORDER BY posts_videos.video_id DESC LIMIT %s, %s", secure($id, 'int'), secure($offset, 'int', false), secure($system['min_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        if ($get_videos->num_rows > 0) {
          while ($video = $get_videos->fetch_assoc()) {
            $videos[] = $video;
          }
        }
        break;
    }
    return $videos;
  }


  /**
   * get_videos_count
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return integer
   */
  public function get_videos_count($node_id, $node_type)
  {
    global $db;
    switch ($node_type) {
      case 'user':
        $get_videos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'user' AND posts.in_group = '0' AND posts.in_event = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page':
        $get_videos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.user_id = %s AND posts.user_type = 'page' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        $get_videos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.group_id = %s AND posts.in_group = '1' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event':
        $get_videos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_videos INNER JOIN posts ON posts_videos.post_id = posts.post_id WHERE posts.event_id = %s AND posts.in_event = '1' AND (posts.pre_approved = '1' OR posts.has_approved = '1')", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      default:
        _error(400);
        break;
    }
    if ($get_videos->num_rows > 0) {
      $count = $get_videos->fetch_assoc();
      return $count['count'];
    }
    return 0;
  }



  /* ------------------------------- */
  /* Categories */
  /* ------------------------------- */

  /**
   * get_categories
   * 
   * @param string $table_name
   * @param integer $category_id
   * @param boolean $reverse
   * @param boolean $only_parents
   * @return array
   */
  public function get_categories($table_name, $category_id = 0, $reverse = false, $only_parents = false)
  {
    global $db;
    $tree = [];
    if (!$reverse) {
      // top-down tree (default)
      $get_categories = $db->query(sprintf("SELECT * FROM %s WHERE category_parent_id = %s ORDER BY category_order ASC", $table_name, secure($category_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_categories->num_rows > 0) {
        while ($category = $get_categories->fetch_assoc()) {
          $category['iteration'] = 0;
          $category['category_url'] = get_url_text($category['category_name']);
          $category['category_name'] = html_entity_decode($category['category_name'], ENT_QUOTES);
          if (!$only_parents) {
            $sub_categories = $this->get_sub_categories($table_name, $category['category_id']);
            if ($sub_categories) {
              $category['sub_categories'] = $sub_categories['sub_categories'];
            }
          }
          $tree[] = $category;
        }
      }
    } else {
      // bottom-up tree
      while (true) {
        $get_parent = $db->query(sprintf("SELECT * FROM %s WHERE category_id = %s", $table_name, secure($category_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_parent->num_rows == 0) {
          break;
        }
        $parent = $get_parent->fetch_assoc();
        $category['iteration'] = 0;
        $parent['category_url'] = get_url_text($category['category_name']);
        $category_id = $parent['category_parent_id'];
        $tree[] = $parent;
        if ($category_id == 0) {
          break;
        }
      }
    }
    return $tree;
  }


  /**
   * get_sub_categories
   * 
   * @param string $table_name
   * @param integer $category_id
   * @param integer $iteration
   * @return array
   */
  public function get_sub_categories($table_name, $category_id, $iteration = 1)
  {
    global $db;
    $tree = [];
    $get_categories = $db->query(sprintf("SELECT * FROM %s WHERE category_parent_id = %s ORDER BY category_order ASC", $table_name, secure($category_id))) or _error('SQL_ERROR_THROWEN');
    if ($get_categories->num_rows > 0) {
      while ($category = $get_categories->fetch_assoc()) {
        $category['iteration'] = $iteration;
        $category['category_url'] = get_url_text($category['category_name']);
        $category['category_name'] = html_entity_decode($category['category_name'], ENT_QUOTES);
        $sub_categories = $this->get_sub_categories($table_name, $category['category_id'], $iteration + 1);
        if ($sub_categories) {
          $category['sub_categories'] = $sub_categories['sub_categories'];
        }
        $tree['sub_categories'][] = $category;
      }
    }
    return $tree;
  }


  /**
   * get_category
   * 
   * @param string $table_name
   * @param integer $category_id
   * @param boolean $get_parent
   * @return array
   */
  public function get_category($table_name, $category_id, $get_parent = false)
  {
    global $db;
    $get_category = $db->query(sprintf("SELECT * FROM %s WHERE category_id = %s", $table_name, secure($category_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_category->num_rows == 0) {
      return false;
    }
    $category = $get_category->fetch_assoc();
    $category['category_url'] = get_url_text($category['category_name']);
    $category['category_name'] = html_entity_decode($category['category_name'], ENT_QUOTES);
    /* get parent category */
    if ($get_parent && $category['category_parent_id']) {
      $category['parent'] = $this->get_category($table_name, $category['category_parent_id']);
    }
    /* check sub_categories */
    $check_sub_categories = $db->query(sprintf("SELECT COUNT(*) as count FROM %s WHERE category_parent_id = %s", $table_name, secure($category_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $category['sub_categories'] = ($check_sub_categories->fetch_assoc()['count'] > 0) ? true : false;
    return $category;
  }


  /**
   * delete_category
   * 
   * @param string $table_name
   * @param integer $category_id
   * @return void
   */
  public function delete_category($table_name, $category_id)
  {
    global $db;
    $db->query(sprintf("DELETE FROM %s WHERE category_id = %s", $table_name, secure($category_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $get_sub_categories = $db->query(sprintf("SELECT category_id FROM %s WHERE category_parent_id = %s", $table_name, secure($category_id))) or _error('SQL_ERROR_THROWEN');
    if ($get_sub_categories->num_rows > 0) {
      while ($sub_category = $get_sub_categories->fetch_assoc()) {
        $this->delete_category($table_name, $sub_category['category_id']);
      }
    }
  }


  /**
   * check_category
   * 
   * @param string $table_name
   * @param integer $category_id
   * @return boolean
   */
  public function check_category($table_name, $category_id)
  {
    global $db;
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM %s WHERE category_id = %s", $table_name, secure($category_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }



  /* ------------------------------- */
  /* Pages */
  /* ------------------------------- */

  /**
   * get_pages
   * 
   * @param array $args
   * @return array
   */
  public function get_pages($args = [])
  {
    global $db, $system;
    /* initialize arguments */
    $user_id = !isset($args['user_id']) ? null : $args['user_id'];
    $category_id = !isset($args['category_id']) ? null : $args['category_id'];
    $offset = !isset($args['offset']) ? 0 : $args['offset'];
    $promoted = !isset($args['promoted']) ? false : true;
    $suggested = !isset($args['suggested']) ? false : true;
    $random = !isset($args['random']) ? false : true;
    $boosted = !isset($args['boosted']) ? false : true;
    $managed = !isset($args['managed']) ? false : true;
    $results = !isset($args['results']) ? $system['pages_results'] : $args['results'];
    /* initialize vars */
    $pages = [];
    $offset *= $results;
    if ($promoted) {
      /* get promoted pages */
      $get_pages = $db->query(sprintf("SELECT * FROM pages WHERE page_boosted = '1' ORDER BY RAND() LIMIT %s", $system['max_results'])) or _error('SQL_ERROR_THROWEN');
    } elseif ($suggested) {
      /* get suggested pages */
      $category_statement = ($category_id) ? "AND page_category = " . secure($category_id, 'int') : "";
      $random_statement = ($random) ? "ORDER BY RAND()" : "";
      $get_pages = $db->query(sprintf("SELECT * FROM pages WHERE page_id NOT IN (%s) %s " . $random_statement . " LIMIT %s, %s", $this->spread_ids($this->get_pages_ids()), $category_statement, secure($offset, 'int', false), secure($results, 'int', false))) or _error('SQL_ERROR_THROWEN');
    } elseif ($boosted) {
      /* get the "viewer" boosted pages */
      $get_pages = $db->query(sprintf("SELECT * FROM pages WHERE page_boosted = '1' AND page_boosted_by = %s LIMIT %s, %s", secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($results, 'int', false))) or _error('SQL_ERROR_THROWEN');
    } elseif ($managed) {
      /* get the "taget" all pages who admin */
      $limit_statement = ($user_id) ? "LIMIT " . secure($offset, 'int', false) . ", " . secure($results, 'int', false) : "";
      $get_pages = $db->query(sprintf("SELECT pages.* FROM pages_admins INNER JOIN pages ON pages_admins.page_id = pages.page_id WHERE pages_admins.user_id = %s ORDER BY page_id DESC " . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($user_id == null) {
      /* get the "viewer" pages who admin */
      $get_pages = $db->query(sprintf("SELECT pages.* FROM pages_admins INNER JOIN pages ON pages_admins.page_id = pages.page_id WHERE pages_admins.user_id = %s ORDER BY page_id DESC LIMIT %s, %s", secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($results, 'int', false))) or _error('SQL_ERROR_THROWEN');
    } else {
      /* get the "target" liked pages*/
      /* get the target user's privacy */
      $get_privacy = $db->query(sprintf("SELECT user_privacy_pages FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $privacy = $get_privacy->fetch_assoc();
      /* check the target user's privacy */
      if (!$this->check_privacy($privacy['user_privacy_pages'], $user_id)) {
        return $pages;
      }
      $get_pages = $db->query(sprintf("SELECT pages.* FROM pages INNER JOIN pages_likes ON pages.page_id = pages_likes.page_id WHERE pages_likes.user_id = %s LIMIT %s, %s", secure($user_id, 'int'), secure($offset, 'int', false), secure($results, 'int', false))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_pages->num_rows > 0) {
      while ($page = $get_pages->fetch_assoc()) {
        $page['page_picture'] = get_picture($page['page_picture'], 'page');
        /* check if the viewer liked the page */
        $page['i_like'] = $this->check_page_membership($this->_data['user_id'], $page['page_id']);
        $pages[] = $page;
      }
    }
    return $pages;
  }


  /**
   * get_page
   * 
   * @param integer $page_id
   * @return array
   */
  public function get_page($page_id)
  {
    global $db;
    $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_page->num_rows == 0) {
      return false;
    }
    return $get_page->fetch_assoc();
  }


  /**
   * create_page
   * 
   * @param array $args
   * @return void
   */
  public function create_page($args = [])
  {
    global $db, $system, $date;
    /* check if pages enabled */
    if (!$system['pages_enabled']) {
      throw new Exception(__("The pages module has been disabled by the admin"));
    }
    /* check pages permission */
    if (!$this->_data['can_create_pages']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* validate title */
    if (is_empty($args['title'])) {
      throw new Exception(__("You must enter a title for your page"));
    }
    if (strlen($args['title']) < 3) {
      throw new Exception(__("Page title must be at least 3 characters long. Please try another"));
    }
    /* validate username */
    if (is_empty($args['username'])) {
      throw new Exception(__("You must enter a username for your page"));
    }
    if (!valid_username($args['username'])) {
      throw new Exception(__("Please enter a valid username (a-z0-9_.) with minimum 3 characters long"));
    }
    if ($this->reserved_username($args['username'])) {
      throw new Exception(__("You can't use") . " " . $args['username'] . " " . __("as username"));
    }
    if ($this->check_username($args['username'], 'page')) {
      throw new Exception(__("Sorry, it looks like this username") . " " . $args['username'] . " " . __("belongs to an existing page"));
    }
    /* validate category */
    if (is_empty($args['category'])) {
      throw new Exception(__("You must select valid category for your page"));
    } else {
      if (!$this->check_category('pages_categories', $args['category'])) {
        throw new Exception(__("You must select valid category for your page"));
      }
    }
    /* validate country */
    if ($args['country'] == "none") {
      throw new Exception(__("You must select valid country"));
    } else {
      if (!$this->check_country($args['country'])) {
        throw new Exception(__("You must select valid country"));
      }
    }
    /* set custom fields */
    $custom_fields = $this->set_custom_fields($args, "page");
    /* insert new page */
    $db->query(sprintf("INSERT INTO pages (page_admin, page_category, page_name, page_title, page_country, page_description, page_date) VALUES (%s, %s, %s, %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($args['category'], 'int'), secure($args['username']), secure($args['title']), secure($args['country'], 'int'), secure($args['description']), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* get page_id */
    $page_id = $db->insert_id;
    /* insert custom fields values */
    if ($custom_fields) {
      foreach ($custom_fields as $field_id => $value) {
        $db->query(sprintf("INSERT INTO custom_fields_values (value, field_id, node_id, node_type) VALUES (%s, %s, %s, 'page')", secure($value), secure($field_id, 'int'), secure($page_id, 'int')));
      }
    }
    /* like the page */
    $this->connect("page-like", $page_id);
    /* page admin addation */
    $this->connect("page-admin-addation", $page_id, $this->_data['user_id']);
  }


  /**
   * edit_page
   * 
   * @param integer $page_id
   * @param string $edit
   * @param array $args
   * @return string
   */
  public function edit_page($page_id, $edit, $args)
  {
    global $db, $system;
    /* check if pages enabled */
    if (!$system['pages_enabled']) {
      throw new Exception(__("The pages module has been disabled by the admin"));
    }
    /* check pages permission */
    if (!$this->_data['can_create_pages']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* (check&get) page */
    $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_page->num_rows == 0) {
      _error(403);
    }
    $page = $get_page->fetch_assoc();
    /* check permission */
    $can_edit = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_edit = true;
      /* viewer is the admin of page */
    } elseif ($this->check_page_adminship($this->_data['user_id'], $page_id)) {
      $can_edit = true;
    }
    if (!$can_edit) {
      _error(403);
    }
    /* edit page */
    switch ($edit) {
      case 'settings':
        /* validate title */
        if (is_empty($args['title'])) {
          throw new Exception(__("You must enter a title for your page"));
        }
        if (strlen($args['title']) < 3) {
          throw new Exception(__("Page title must be at least 3 characters long. Please try another"));
        }
        /* validate username */
        if (strtolower($args['username']) != strtolower($page['page_name'])) {
          if (is_empty($args['username'])) {
            throw new Exception(__("You must enter a username for your page"));
          }
          if (!valid_username($args['username'])) {
            throw new Exception(__("Please enter a valid username (a-z0-9_.) with minimum 3 characters long"));
          }
          if ($this->reserved_username($args['username'])) {
            throw new Exception(__("You can't use") . " " . $args['username'] . " " . __("as username"));
          }
          if ($this->check_username($args['username'], 'page')) {
            throw new Exception(__("Sorry, it looks like this username") . " " . $args['username'] . " " . __("belongs to an existing page"));
          }
          /* set new page name */
          $page['page_name'] = $args['username'];
        }
        /* validate category */
        if (is_empty($args['category'])) {
          throw new Exception(__("You must select valid category for your page"));
        } else {
          if (!$this->check_category('pages_categories', $args['category'])) {
            throw new Exception(__("You must select valid category for your page"));
          }
        }
        /* prepare */
        $args['page_tips_enabled'] = (isset($args['page_tips_enabled'])) ? '1' : '0';
        /* edit from admin panel */
        $args['page_verified'] = ($this->_data['user_group'] == 1 && isset($args['page_verified'])) ? $args['page_verified'] : $page['page_verified'];
        /* update page */
        $db->query(sprintf("UPDATE pages SET page_category = %s, page_name = %s, page_title = %s, page_tips_enabled = %s, page_verified = %s WHERE page_id = %s", secure($args['category'], 'int'), secure($args['username']), secure($args['title']), secure($args['page_tips_enabled']), secure($args['page_verified']), secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'info':
        /* validate country */
        if ($args['country'] == "none") {
          throw new Exception(__("You must select valid country"));
        } else {
          if (!$this->check_country($args['country'])) {
            throw new Exception(__("You must select valid country"));
          }
        }
        /* validate website */
        if (!is_empty($args['website']) && !valid_url($args['website'])) {
          throw new Exception(__("Please enter a valid website URL"));
        }
        /* set custom fields */
        $this->set_custom_fields($args, "page", "settings", $page_id);
        /* update page */
        $db->query(sprintf("UPDATE pages SET page_company = %s, page_phone = %s, page_website = %s, page_location = %s, page_country = %s, page_description = %s WHERE page_id = %s", secure($args['company']), secure($args['phone']), secure($args['website']), secure($args['location']), secure($args['country'], 'int'), secure($args['description']), secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'action':
        /* validate action color */
        if (!in_array($args['action_color'], ['light', 'primary', 'success', 'info', 'warning', 'danger'])) {
          throw new Exception(__("Please select a valid action button color"));
        }
        /* validate action URL */
        if (!is_empty($args['action_url']) && !valid_url($args['action_url'])) {
          throw new Exception(__("Please enter a valid action button URL"));
        }
        /* update page */
        $db->query(sprintf("UPDATE pages SET page_action_text = %s, page_action_color = %s, page_action_url = %s WHERE page_id = %s", secure($args['action_text']), secure($args['action_color']), secure($args['action_url']), secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'social':
        /* validate facebook */
        if (!is_empty($args['facebook']) && !valid_url($args['facebook'])) {
          throw new Exception(__("Please enter a valid Facebook Profile URL"));
        }
        /* validate twitter */
        if (!is_empty($args['twitter']) && !valid_url($args['twitter'])) {
          throw new Exception(__("Please enter a valid Twitter Profile URL"));
        }
        /* validate youtube */
        if (!is_empty($args['youtube']) && !valid_url($args['youtube'])) {
          throw new Exception(__("Please enter a valid YouTube Profile URL"));
        }
        /* validate instagram */
        if (!is_empty($args['instagram']) && !valid_url($args['instagram'])) {
          throw new Exception(__("Please enter a valid Instagram Profile URL"));
        }
        /* validate linkedin */
        if (!is_empty($args['linkedin']) && !valid_url($args['linkedin'])) {
          throw new Exception(__("Please enter a valid Linkedin Profile URL"));
        }
        /* validate vkontakte */
        if (!is_empty($args['vkontakte']) && !valid_url($args['vkontakte'])) {
          throw new Exception(__("Please enter a valid Vkontakte Profile URL"));
        }
        /* update page */
        $db->query(sprintf("UPDATE pages SET page_social_facebook = %s, page_social_twitter = %s, page_social_youtube = %s, page_social_instagram = %s, page_social_linkedin = %s, page_social_vkontakte = %s WHERE page_id = %s", secure($args['facebook']), secure($args['twitter']), secure($args['youtube']), secure($args['instagram']), secure($args['linkedin']), secure($args['vkontakte']), secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'monetization':
        /* prepare */
        $args['page_monetization_enabled'] = (isset($args['page_monetization_enabled'])) ? '1' : '0';
        /* update page */
        $db->query(sprintf("UPDATE pages SET page_monetization_enabled = %s WHERE page_id = %s", secure($args['page_monetization_enabled']), secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update monetization plans */
        $this->update_monetization_plans($page_id, 'page');
        break;
    }
    return $page['page_name'];
  }


  /**
   * delete_page
   * 
   * @param integer $page_id
   * @return void
   */
  public function delete_page($page_id)
  {
    global $db, $system;
    /* check if pages enabled */
    if (!$system['pages_enabled']) {
      throw new Exception(__("The pages module has been disabled by the admin"));
    }
    /* check pages permission */
    if (!$this->_data['can_create_pages']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* (check&get) page */
    $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_page->num_rows == 0) {
      _error(403);
    }
    $page = $get_page->fetch_assoc();
    // delete page
    $can_delete = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_delete = true;
    }
    /* viewer is the super admin of page */
    if ($this->_data['user_id'] == $page['page_admin']) {
      $can_delete = true;
    }
    /* check if viewer can delete the page */
    if (!$can_delete) {
      _error(403);
    }
    /* delete the page */
    $db->query(sprintf("DELETE FROM pages WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete the page members */
    $db->query(sprintf("DELETE FROM pages_likes WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete the page admins */
    $db->query(sprintf("DELETE FROM pages_admins WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete the page events */
    $db->query(sprintf("DELETE FROM `events` WHERE event_page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * get_page_admins_ids
   * 
   * @param integer $page_id
   * @return array
   */
  public function get_page_admins_ids($page_id)
  {
    global $db;
    $admins = [];
    $get_admins = $db->query(sprintf("SELECT user_id FROM pages_admins WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_admins->num_rows > 0) {
      while ($admin = $get_admins->fetch_assoc()) {
        $admins[] = $admin['user_id'];
      }
    }
    return $admins;
  }


  /**
   * get_page_admins
   * 
   * @param integer $page_id
   * @param integer $offset
   * @return array
   */
  public function get_page_admins($page_id, $offset = 0)
  {
    global $db, $system;
    $admins = [];
    $offset *= $system['max_results_even'];
    $get_admins = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM pages_admins INNER JOIN users ON pages_admins.user_id = users.user_id WHERE pages_admins.page_id = %s LIMIT %s, %s", secure($page_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_admins->num_rows > 0) {
      while ($admin = $get_admins->fetch_assoc()) {
        $admin['user_picture'] = get_picture($admin['user_picture'], $admin['user_gender']);
        /* get the connection */
        $admin['i_admin'] = true;
        $admin['connection'] = 'page_manage';
        $admin['node_id'] = $page_id;
        $admins[] = $admin;
      }
    }
    return $admins;
  }


  /**
   * get_page_members
   * 
   * @param integer $page_id
   * @param integer $offset
   * @return array
   */
  public function get_page_members($page_id, $offset = 0)
  {
    global $db, $system;
    $members = [];
    $offset *= $system['max_results_even'];
    $get_members = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM pages_likes INNER JOIN users ON pages_likes.user_id = users.user_id WHERE pages_likes.page_id = %s LIMIT %s, %s", secure($page_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_members->num_rows > 0) {
      /* get page admins ids */
      $page_admins_ids = $this->get_page_admins_ids($page_id);
      while ($member = $get_members->fetch_assoc()) {
        $member['user_picture'] = get_picture($member['user_picture'], $member['user_gender']);
        /* get the connection */
        $member['i_admin'] = in_array($member['user_id'], $page_admins_ids);
        $member['connection'] = 'page_manage';
        $member['node_id'] = $page_id;
        $members[] = $member;
      }
    }
    return $members;
  }


  /**
   * get_page_members_ids
   * 
   * @param integer $page_id
   * @return array
   */
  public function get_page_members_ids($page_id)
  {
    global $db;
    $members = [];
    $get_members = $db->query(sprintf("SELECT user_id FROM pages_likes WHERE page_id = %s", secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_members->num_rows > 0) {
      while ($member = $get_members->fetch_assoc()) {
        $members[] = $member['user_id'];
      }
    }
    return $members;
  }


  /**
   * get_page_invites
   * 
   * @param integer $page_id
   * @param integer $offset
   * @return array
   */
  public function get_page_invites($page_id, $offset = 0)
  {
    global $db, $system;
    $friends = [];
    $offset *= $system['max_results_even'];
    $get_friends = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users LEFT JOIN pages_likes ON users.user_id = pages_likes.user_id AND pages_likes.page_id = %s LEFT JOIN pages_invites ON users.user_id = pages_invites.user_id AND pages_invites.page_id = %s AND pages_invites.from_user_id = %s WHERE users.user_id IN (%s) AND pages_likes.id IS NULL AND pages_invites.id IS NULL LIMIT %s, %s", secure($page_id, 'int'), secure($page_id, 'int'), secure($this->_data['user_id'], 'int'), $this->spread_ids($this->_data['friends_ids']), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    while ($friend = $get_friends->fetch_assoc()) {
      $friend['user_picture'] = get_picture($friend['user_picture'], $friend['user_gender']);
      $friend['connection'] = 'page_invite';
      $friend['node_id'] = $page_id;
      $friends[] = $friend;
    }
    return $friends;
  }


  /**
   * check_page_adminship
   * 
   * @param integer $user_id
   * @param integer $page_id
   * @return boolean
   */
  public function check_page_adminship($user_id, $page_id)
  {
    if ($this->_logged_in) {
      /* get page admins ids */
      $page_admins_ids = $this->get_page_admins_ids($page_id);
      if (in_array($user_id, $page_admins_ids)) {
        return true;
      }
    }
    return false;
  }


  /**
   * check_page_membership
   * 
   * @param integer $user_id
   * @param integer $page_id
   * @return boolean
   */
  public function check_page_membership($user_id, $page_id)
  {
    global $db;
    if ($this->_logged_in) {
      $get_likes = $db->query(sprintf("SELECT COUNT(*) as count FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($user_id, 'int'), secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_likes->fetch_assoc()['count'] > 0) {
        return true;
      }
    }
    return false;
  }



  /* ------------------------------- */
  /* Reviews */
  /* ------------------------------- */

  /**
   * get_reviews
   * 
   * @param integer $node_id
   * @param string $node_type
   * @param integer $offset
   * @return array
   */
  public function get_reviews($node_id, $node_type = 'page', $offset = 0)
  {
    global $system, $db;
    $reviews = [];
    $offset *= $system['max_results_even'];
    /* get reviews */
    switch ($node_type) {
      case 'page':
        $get_reviews = $db->query(sprintf("SELECT reviews.review_id FROM reviews INNER JOIN users ON reviews.user_id = users.user_id INNER JOIN pages ON reviews.node_type = 'page' AND reviews.node_id = pages.page_id WHERE reviews.node_id = %s ORDER BY reviews.review_id DESC LIMIT %s, %s", secure($node_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        $get_reviews = $db->query(sprintf("SELECT reviews.review_id FROM reviews INNER JOIN users ON reviews.user_id = users.user_id INNER JOIN groups ON reviews.node_type = 'group' AND reviews.node_id = groups.group_id WHERE reviews.node_id = %s ORDER BY reviews.review_id DESC LIMIT %s, %s", secure($node_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event':
        $get_reviews = $db->query(sprintf("SELECT reviews.review_id FROM reviews INNER JOIN users ON reviews.user_id = users.user_id INNER JOIN events ON reviews.node_type = 'event' AND reviews.node_id = events.event_id WHERE reviews.node_id = %s ORDER BY reviews.review_id DESC LIMIT %s, %s", secure($node_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'post':
        $get_reviews = $db->query(sprintf("SELECT reviews.review_id FROM reviews INNER JOIN users ON reviews.user_id = users.user_id INNER JOIN posts ON reviews.node_type = 'post' AND reviews.node_id = posts.post_id WHERE reviews.node_id = %s ORDER BY reviews.review_id DESC LIMIT %s, %s", secure($node_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;
    }
    if ($get_reviews->num_rows > 0) {
      while ($review = $get_reviews->fetch_assoc()) {
        $review = $this->get_review($review['review_id']);
        if ($review) {
          $reviews[] = $review;
        }
      }
    }
    return $reviews;
  }


  /**
   * get_review
   * 
   * @param integer $review_id
   * @return array
   */
  public function get_review($review_id)
  {
    global $db, $system;
    /* get review */
    $get_review = $db->query(sprintf("SELECT reviews.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM reviews INNER JOIN users ON reviews.user_id = users.user_id WHERE reviews.review_id = %s", secure($review_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_review->num_rows == 0) {
      return false;
    }
    $review = $get_review->fetch_assoc();
    switch ($review['node_type']) {
      case 'page':
        $page = $this->get_page($review['node_id']);
        if (!$page) {
          return false;
        }
        $page['page_picture'] = get_picture($page['page_picture'], 'page');
        $review['page'] = $page;
        $review['manage_review'] = ($this->_logged_in && $this->check_page_adminship($this->_data['user_id'], $page['page_id'])) ? true : false;
        break;

      case 'group':
        $group = $this->get_group($review['node_id']);
        if (!$group) {
          return false;
        }
        $group['group_picture'] = get_picture($group['group_picture'], 'group');
        $review['group'] = $group;
        $review['manage_review'] = ($this->_logged_in && $this->check_group_adminship($this->_data['user_id'], $group['group_id'])) ? true : false;
        break;

      case 'event':
        $event = $this->get_event($review['node_id']);
        if (!$event) {
          return false;
        }
        $event['event_picture'] = get_picture($event['event_picture'], 'event');
        $review['event'] = $event;
        $review['manage_review'] = ($this->_logged_in && $this->check_event_adminship($this->_data['user_id'], $event['event_id'])) ? true : false;
        break;

      case 'post':
        $post = $this->get_post($review['node_id']);
        if (!$post) {
          return false;
        }
        $review['post'] = $post;
        $review['manage_review'] = ($this->_logged_in && $this->_data['user_id'] == $post['author_id']) ? true : false;
        break;
    }
    /* get review user */
    $review['user_picture'] = get_picture($review['user_picture'], $review['user_gender']);
    $review['user_fullname'] = ($system['show_usernames_enabled']) ? $review['user_name'] : $review['user_firstname'] . " " . $review['user_lastname'];
    /* get review photos */
    $get_photos = $db->query(sprintf("SELECT * FROM reviews_photos WHERE review_id = %s", secure($review['review_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    while ($photo = $get_photos->fetch_assoc()) {
      $review['photos'][] = $photo;
    }
    return $review;
  }


  /**
   * add_review
   * 
   * @param integer $node_id
   * @param string $node_type
   * @param integer $rate
   * @param string $review
   * @param array $photos
   * @return array
   */
  public function add_review($node_id, $node_type, $rate, $review, $photos)
  {
    global $db, $system, $date;
    /* get node */
    switch ($node_type) {
      case 'page':
        /* check if reviews enabled */
        if (!$system['pages_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get page */
        $page = $this->get_page($node_id);
        if (!$page) {
          throw new Exception(__("The page you're trying to review doesn't exist"));
        }
        break;

      case 'group':
        /* check if reviews enabled */
        if (!$system['groups_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get group */
        $group = $this->get_group($node_id);
        if (!$group) {
          throw new Exception(__("The group you're trying to review doesn't exist"));
        }
        break;

      case 'event':
        /* check if reviews enabled */
        if (!$system['events_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get event */
        $event = $this->get_event($node_id);
        if (!$event) {
          throw new Exception(__("The event you're trying to review doesn't exist"));
        }
        break;

      case 'post':
        /* check if reviews enabled */
        if (!$system['posts_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get post */
        $post = $this->get_post($node_id);
        if (!$post) {
          throw new Exception(__("The post you're trying to review doesn't exist"));
        }
        break;

      default:
        throw new Exception(__("The node you're trying to review doesn't exist"));
        break;
    }
    /* check if the viewer already reviewed this node before */
    $get_review = $db->query(sprintf("SELECT review_id FROM reviews WHERE user_id = %s AND node_id = %s AND node_type = %s", secure($this->_data['user_id'], 'int'), secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    if ($get_review->num_rows > 0) {
      switch ($node_type) {
        case 'page':
          if (!$system['pages_reviews_replacement_enabled']) {
            throw new Exception(__("You already reviewed this before"));
          }
          break;

        case 'group':
          if (!$system['groups_reviews_replacement_enabled']) {
            throw new Exception(__("You already reviewed this before"));
          }
          break;

        case 'event':
          if (!$system['events_reviews_replacement_enabled']) {
            throw new Exception(__("You already reviewed this before"));
          }
          break;

        case 'post':
          if (!$system['posts_reviews_replacement_enabled']) {
            throw new Exception(__("You already reviewed this before"));
          }
          break;
      }
      /* delete the old review */
      $this->delete_review($get_review->fetch_assoc()['review_id']);
    }
    switch ($node_type) {
      case 'page':
        /* check if the viewer is the page admin */
        if ($this->check_page_adminship($this->_data['user_id'], $node_id)) {
          throw new Exception(__("You can't review your own page"));
        }
        break;

      case 'group':
        /* check if the viewer is the group admin */
        if ($this->check_group_adminship($this->_data['user_id'], $node_id)) {
          throw new Exception(__("You can't review your own group"));
        }
        break;

      case 'event':
        /* check if the viewer is the event admin */
        if ($this->check_event_adminship($this->_data['user_id'], $node_id)) {
          throw new Exception(__("You can't review your own event"));
        }
        break;

      case 'post':
        /* check if the viewer is the post author */
        if ($this->_data['user_id'] == $post['author_id']) {
          throw new Exception(__("You can't review your own post"));
        }
        break;
    }
    /* validate rate */
    if (!in_array($rate, ['1', '2', '3', '4', '5'])) {
      throw new Exception(__("Please select a valid rate"));
    }
    /* validate review */
    if (!is_empty($review) && strlen($review) > 1000) {
      throw new Exception(__("Your review must be less than 1000 characters"));
    }
    /* validate photos */
    $filtered_photos = [];
    if (isset($photos)) {
      $photos = json_decode($photos);
      if (is_object($photos)) {
        /* filter the photos */
        foreach ($photos as $photo) {
          $filtered_photos[] = (array) $photo;
        }
      }
    }
    /* insert new review */
    $db->query(sprintf("INSERT INTO reviews (user_id, node_id, node_type, rate, review, time) VALUES (%s, %s, %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($node_id, 'int'), secure($node_type), secure($rate, 'int'), secure($review), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* get review_id */
    $review_id = $db->insert_id;
    /* insert review photos */
    if ($filtered_photos) {
      foreach ($filtered_photos as $photo) {
        $db->query(sprintf("INSERT INTO reviews_photos (review_id, source) VALUES (%s, %s)", secure($review_id, 'int'), secure($photo['source']))) or _error('SQL_ERROR_THROWEN');
      }
    }
    /* update node rate */
    $this->update_node_rate($node_id, $node_type);
    /* notify the node owner */
    switch ($node_type) {
      case 'page':
        $this->post_notification(['to_user_id' => $page['page_admin'], 'action' => 'page_review', 'node_type' => $page['page_title'], 'node_url' => $page['page_name']]);
        break;

      case 'group':
        $this->post_notification(['to_user_id' => $group['group_admin'], 'action' => 'group_review', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
        break;

      case 'event':
        $this->post_notification(['to_user_id' => $event['event_admin'], 'action' => 'event_review', 'node_type' => $event['event_title'], 'node_url' => $event['event_id']]);
        break;

      case 'post':
        $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'post_review', 'node_type' => $post['post_text'], 'node_url' => $post['post_id']]);
        break;
    }
    /* return the review */
    return $this->get_review($review_id);
  }


  /**
   * delete_review
   * 
   * @param integer $review_id
   * @return void
   */
  public function delete_review($review_id)
  {
    global $db, $system;
    /* get review */
    $review = $this->get_review($review_id);
    if (!$review) {
      throw new Exception(__("The review you're trying to delete doesn't exist"));
    }
    switch ($review['node_type']) {
      case 'page':
        /* check if reviews enabled */
        if (!$system['pages_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        break;

      case 'group':
        /* check if reviews enabled */
        if (!$system['groups_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        break;

      case 'event':
        /* check if reviews enabled */
        if (!$system['events_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        break;

      case 'post':
        /* check if reviews enabled */
        if (!$system['posts_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        break;
    }
    /* check if the viewer is the admin or moderator */
    if ($this->_data['user_group'] < 3) {
      $can_delete = true;
    }
    /* check if the viewer is the review owner */
    if ($this->_data['user_id'] == $review['user_id']) {
      $can_delete = true;
    }
    /* check if the viewer can delete the review */
    if (!$can_delete) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* delete review */
    $db->query(sprintf("DELETE FROM reviews WHERE review_id = %s", secure($review_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete review photos */
    $db->query(sprintf("DELETE FROM reviews_photos WHERE review_id = %s", secure($review_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update node rate */
    $this->update_node_rate($review['node_id'], $review['node_type']);
  }


  /**
   * add_review_reply
   * 
   * @param integer $review_id
   * @param string $reply
   * @return void
   */
  public function add_review_reply($review_id, $reply)
  {
    global $db, $system;
    /* get review */
    $review = $this->get_review($review_id);
    if (!$review) {
      throw new NoDataException(__("The review you're trying to reply on doesn't exist"));
    }
    /* check if the viewer is the node admin */
    switch ($review['node_type']) {
      case 'page':
        /* check if reviews enabled */
        if (!$system['pages_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get page */
        $page = $this->get_page($review['node_id']);
        if (!$page) {
          throw new NoDataException(__("The page you're trying to reply on doesn't exist"));
        }
        if (!$this->check_page_adminship($this->_data['user_id'], $review['node_id'])) {
          throw new Exception(__("You don't have the permission to do this"));
        }
        break;

      case 'group':
        /* check if reviews enabled */
        if (!$system['groups_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get group */
        $group = $this->get_group($review['node_id']);
        if (!$group) {
          throw new NoDataException(__("The group you're trying to reply on doesn't exist"));
        }
        if (!$this->check_group_adminship($this->_data['user_id'], $review['node_id'])) {
          throw new Exception(__("You don't have the permission to do this"));
        }
        break;

      case 'event':
        /* check if reviews enabled */
        if (!$system['events_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get event */
        $event = $this->get_event($review['node_id']);
        if (!$event) {
          throw new NoDataException(__("The event you're trying to reply on doesn't exist"));
        }
        if (!$this->check_event_adminship($this->_data['user_id'], $review['node_id'])) {
          throw new Exception(__("You don't have the permission to do this"));
        }
        break;

      case 'post':
        /* check if reviews enabled */
        if (!$system['posts_reviews_enabled']) {
          throw new Exception(__("This feature has been disabled by the admin"));
        }
        /* get post */
        $post = $this->get_post($review['node_id']);
        if (!$post) {
          throw new NoDataException(__("The post you're trying to reply on doesn't exist"));
        }
        if ($this->_data['user_id'] != $post['author_id']) {
          throw new Exception(__("You don't have the permission to do this"));
        }
        break;
    }
    /* validate reply */
    if (is_empty($reply)) {
      throw new ValidationException(__("You must enter a reply"));
    }
    /* update review reply text field with the reply  */
    $db->query(sprintf("UPDATE reviews SET reply = %s WHERE review_id = %s", secure($reply), secure($review_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* notify the review author */
    switch ($review['node_type']) {
      case 'page':
        $this->post_notification(['to_user_id' => $review['user_id'], 'action' => 'page_review_reply', 'node_type' => $page['page_title'], 'node_url' => $page['page_name']]);
        break;

      case 'group':
        $this->post_notification(['to_user_id' => $review['user_id'], 'action' => 'group_review_reply', 'node_type' => $group['group_title'], 'node_url' => $group['group_name']]);
        break;

      case 'event':
        $this->post_notification(['to_user_id' => $review['user_id'], 'action' => 'event_review_reply', 'node_type' => $event['event_title'], 'node_url' => $event['event_id']]);
        break;

      case 'post':
        $this->post_notification(['to_user_id' => $review['user_id'], 'action' => 'post_review_reply', 'node_type' => $post['post_text'], 'node_url' => $post['post_id']]);
        break;
    }
  }


  /**
   * update_node_rate
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return void
   */
  public function update_node_rate($node_id, $node_type = "page")
  {
    global $db;
    switch ($node_type) {
      case 'page':
        /* get page */
        $page = $this->get_page($node_id);
        if (!$page) {
          throw new Exception(__("The page you're trying to review doesn't exist"));
        }
        /* update page rate */
        $db->query(sprintf("UPDATE pages SET page_rate = COALESCE((SELECT AVG(rate) FROM reviews WHERE node_id = %s AND node_type = 'page'), 0) WHERE page_id = %s", secure($node_id, 'int'), secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        /* get group */
        $group = $this->get_group($node_id);
        if (!$group) {
          throw new Exception(__("The group you're trying to review doesn't exist"));
        }
        /* update group rate */
        $db->query(sprintf("UPDATE groups SET group_rate = COALESCE((SELECT AVG(rate) FROM reviews WHERE node_id = %s AND node_type = 'group'), 0) WHERE group_id = %s", secure($node_id, 'int'), secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event':
        /* get event */
        $event = $this->get_event($node_id);
        if (!$event) {
          throw new Exception(__("The event you're trying to review doesn't exist"));
        }
        /* update event rate */
        $db->query(sprintf("UPDATE events SET event_rate = COALESCE((SELECT AVG(rate) FROM reviews WHERE node_id = %s AND node_type = 'event'), 0) WHERE event_id = %s", secure($node_id, 'int'), secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'post':
        /* get post */
        $post = $this->get_post($node_id);
        if (!$post) {
          throw new Exception(__("The post you're trying to review doesn't exist"));
        }
        /* update post rate */
        $db->query(sprintf("UPDATE posts SET post_rate = COALESCE((SELECT AVG(rate) FROM reviews WHERE node_id = %s AND node_type = 'post'), 0) WHERE post_id = %s", secure($node_id, 'int'), secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;
    }
  }


  /**
   * get_reviews_count
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return integer
   */
  public function get_reviews_count($node_id, $node_type = "page")
  {
    global $db;
    $get_reviews = $db->query(sprintf("SELECT COUNT(*) as count FROM reviews WHERE node_id = %s AND node_type = %s", secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    return $get_reviews->fetch_assoc()['count'];
  }



  /* ------------------------------- */
  /* Groups */
  /* ------------------------------- */

  /**
   * get_groups
   * 
   * @param array $args
   * @return array
   */
  public function get_groups($args = [])
  {
    global $db, $system;
    /* initialize arguments */
    $user_id = !isset($args['user_id']) ? null : $args['user_id'];
    $category_id = !isset($args['category_id']) ? null : $args['category_id'];
    $offset = !isset($args['offset']) ? 0 : $args['offset'];
    $get_all = !isset($args['get_all']) ? false : true;
    $suggested = !isset($args['suggested']) ? false : true;
    $random = !isset($args['random']) ? false : true;
    $managed = !isset($args['managed']) ? false : true;
    $results = !isset($args['results']) ? $system['groups_results'] : $args['results'];
    /* initialize vars */
    $groups = [];
    $offset *= $results;
    if ($suggested) {
      /* get suggested groups */
      $where_statement = "";
      /* make a list from joined groups (approved|pending) */
      $where_statement .= sprintf("AND group_id NOT IN (%s) ", $this->spread_ids($this->get_groups_ids()));
      $category_statement = ($category_id) ? "AND group_category = " . secure($category_id, 'int') : "";
      $sort_statement = ($random) ? " ORDER BY RAND() " : " ORDER BY group_id DESC ";
      $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($results, 'int', false));
      $get_groups = $db->query("SELECT * FROM `groups` WHERE group_privacy != 'secret' " . $where_statement . $category_statement . $sort_statement . $limit_statement) or _error('SQL_ERROR_THROWEN');
    } elseif ($managed) {
      /* get the "taget" all groups who admin */
      $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($results, 'int', false));
      $get_groups = $db->query(sprintf("SELECT `groups`.* FROM groups_admins INNER JOIN `groups` ON groups_admins.group_id = `groups`.group_id WHERE groups_admins.user_id = %s ORDER BY group_id DESC " . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($user_id == null) {
      /* get the "viewer" groups who admin */
      $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($results, 'int', false));
      $get_groups = $db->query(sprintf("SELECT `groups`.* FROM groups_admins INNER JOIN `groups` ON groups_admins.group_id = `groups`.group_id WHERE groups_admins.user_id = %s ORDER BY group_id DESC " . $limit_statement, secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* get the "target" groups*/
    } else {
      /* get the target user's privacy */
      $get_privacy = $db->query(sprintf("SELECT user_privacy_groups FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $privacy = $get_privacy->fetch_assoc();
      /* check the target user's privacy */
      if (!$this->check_privacy($privacy['user_privacy_groups'], $user_id)) {
        return $groups;
      }
      /* if the viewer not the target exclude secret groups */
      $where_statement = ($this->_data['user_id'] == $user_id) ? "" : "AND `groups`.group_privacy != 'secret'";
      $limit_statement = ($get_all) ? "" : sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($results, 'int', false));
      $get_groups = $db->query(sprintf("SELECT `groups`.* FROM `groups` INNER JOIN groups_members ON `groups`.group_id = groups_members.group_id WHERE groups_members.approved = '1' AND groups_members.user_id = %s " . $where_statement . " ORDER BY group_id DESC " . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_groups->num_rows > 0) {
      while ($group = $get_groups->fetch_assoc()) {
        $group['group_picture'] = get_picture($group['group_picture'], 'group');
        /* check if the viewer joined the group */
        $group['i_joined'] = $this->check_group_membership($this->_data['user_id'], $group['group_id']);;
        $groups[] = $group;
      }
    }
    return $groups;
  }


  /**
   * get_group
   * 
   * @param integer $group_id
   * @return array
   */
  public function get_group($group_id)
  {
    global $db;
    $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_group->num_rows == 0) {
      return false;
    }
    return $get_group->fetch_assoc();
  }


  /**
   * create_group
   * 
   * @param array $args
   * @return void
   */
  public function create_group($args = [])
  {
    global $db, $system, $date;
    /* check if groups enabled */
    if (!$system['groups_enabled']) {
      throw new Exception(__("The groups module has been disabled by the admin"));
    }
    /* check groups permission */
    if (!$this->_data['can_create_groups']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* validate title */
    if (is_empty($args['title'])) {
      throw new Exception(__("You must enter a name for your group"));
    }
    if (strlen($args['title']) < 3) {
      throw new Exception(__("Group name must be at least 3 characters long. Please try another"));
    }
    /* validate username */
    if (is_empty($args['username'])) {
      throw new Exception(__("You must enter a web address for your group"));
    }
    if (!valid_username($args['username'])) {
      throw new Exception(__("Please enter a valid web address (a-z0-9_.) with minimum 3 characters long"));
    }
    if ($this->reserved_username($args['username'])) {
      throw new Exception(__("You can't use") . " " . $args['username'] . " " . __("as web address"));
    }
    if ($this->check_username($args['username'], 'group')) {
      throw new Exception(__("Sorry, it looks like this web address") . " " . $args['username'] . " " . __("belongs to an existing group"));
    }
    /* validate category */
    if (is_empty($args['category'])) {
      throw new Exception(__("You must select valid category for your group"));
    } else {
      if (!$this->check_category('groups_categories', $args['category'])) {
        throw new Exception(__("You must select valid category for your group"));
      }
    }
    /* validate privacy */
    if (!in_array($args['privacy'], ['secret', 'closed', 'public'])) {
      throw new Exception(__("You must select a valid privacy for your group"));
    }
    /* set custom fields */
    $custom_fields = $this->set_custom_fields($args, "group");
    /* insert new group */
    $db->query(sprintf("INSERT INTO `groups` (group_privacy, group_admin, group_name, group_category, group_title, group_description, group_date) VALUES (%s, %s, %s, %s, %s, %s, %s)", secure($args['privacy']), secure($this->_data['user_id'], 'int'), secure($args['username']), secure($args['category']), secure($args['title']), secure($args['description']), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* get group_id */
    $group_id = $db->insert_id;
    /* insert custom fields values */
    if ($custom_fields) {
      foreach ($custom_fields as $field_id => $value) {
        $db->query(sprintf("INSERT INTO custom_fields_values (value, field_id, node_id, node_type) VALUES (%s, %s, %s, 'group')", secure($value), secure($field_id, 'int'), secure($group_id, 'int')));
      }
    }
    /* join the group */
    $this->connect("group-join", $group_id);
    /* group admin addation */
    $this->connect("group-admin-addation", $group_id, $this->_data['user_id']);
  }


  /**
   * edit_group
   * 
   * @param integer $group_id
   * @param string $edit
   * @param array $args
   * @return void
   */
  public function edit_group($group_id, $edit, $args)
  {
    global $db, $system;
    /* check if groups enabled */
    if (!$system['groups_enabled']) {
      throw new Exception(__("The groups module has been disabled by the admin"));
    }
    /* check groups permission */
    if (!$this->_data['can_create_groups']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* (check&get) group */
    $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_group->num_rows == 0) {
      _error(403);
    }
    $group = $get_group->fetch_assoc();
    /* check permission */
    $can_edit = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_edit = true;
      /* viewer is the admin of group */
    } elseif ($this->check_group_adminship($this->_data['user_id'], $group_id)) {
      $can_edit = true;
    }
    if (!$can_edit) {
      _error(403);
    }
    /* edit group */
    switch ($edit) {
      case 'settings':
        /* validate title */
        if (is_empty($args['title'])) {
          throw new Exception(__("You must enter a name for your group"));
        }
        if (strlen($args['title']) < 3) {
          throw new Exception(__("Group name must be at least 3 characters long. Please try another"));
        }
        /* validate username */
        if (strtolower($args['username']) != strtolower($group['group_name'])) {
          if (is_empty($args['username'])) {
            throw new Exception(__("You must enter a web address for your group"));
          }
          if (!valid_username($args['username'])) {
            throw new Exception(__("Please enter a valid web address (a-z0-9_.) with minimum 3 characters long"));
          }
          if ($this->reserved_username($args['username'])) {
            throw new Exception(__("You can't use") . " " . $args['username'] . " " . __("as web address"));
          }
          if ($this->check_username($args['username'], 'group')) {
            throw new Exception(__("Sorry, it looks like this web address") . " " . $args['username'] . " " . __("belongs to an existing group"));
          }
          /* set new group name */
          $group['group_name'] = $args['username'];
        }
        /* validate privacy */
        if (!in_array($args['privacy'], ['secret', 'closed', 'public'])) {
          throw new Exception(__("You must select a valid privacy for your group"));
        }
        /* validate category */
        if (is_empty($args['category'])) {
          throw new Exception(__("You must select valid category for your group"));
        } else {
          if (!$this->check_category('groups_categories', $args['category'])) {
            throw new Exception(__("You must select valid category for your group"));
          }
        }
        /* set custom fields */
        $this->set_custom_fields($args, "group", "settings", $group_id);
        /* update the group */
        $args['chatbox_enabled'] = (isset($args['chatbox_enabled'])) ? '1' : '0';
        $args['group_publish_enabled'] = (isset($args['group_publish_enabled'])) ? '1' : '0';
        $args['group_publish_approval_enabled'] = (isset($args['group_publish_approval_enabled'])) ? '1' : '0';
        $db->query(sprintf("UPDATE `groups` SET group_privacy = %s, group_category = %s, group_name = %s, group_title = %s, group_description = %s, chatbox_enabled = %s, group_publish_enabled = %s, group_publish_approval_enabled = %s WHERE group_id = %s", secure($args['privacy']), secure($args['category']), secure($args['username']), secure($args['title']), secure($args['description']), secure($args['chatbox_enabled']), secure($args['group_publish_enabled']), secure($args['group_publish_approval_enabled']), secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* check if group privacy changed to public */
        if ($args['privacy'] == "public") {
          /* approve any pending join requests */
          $db->query(sprintf("UPDATE groups_members SET approved = '1' WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
          /* update members counter + affected rows */
          $rows_count = $db->affected_rows;
          $db->query(sprintf("UPDATE `groups` SET group_members = group_members + %s  WHERE group_id = %s", secure($rows_count, 'int'), secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        /* check if chatbox enabled */
        if ($args['chatbox_enabled']) {
          /* create new chatbox if not */
          if (!$group['chatbox_conversation_id']) {
            $conversation_id = $this->create_chatbox($group_id, 'group');
            /* update group */
            $db->query(sprintf("UPDATE `groups` SET chatbox_conversation_id = %s WHERE group_id = %s", secure($conversation_id, 'int'), secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
          }
        }
        /* check if post approval disabled */
        if (!$args['group_publish_approval_enabled']) {
          /* approve any pending posts */
          $db->query(sprintf("UPDATE posts SET group_approved = '1' WHERE in_group = '1' AND group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        break;

      case 'monetization':
        /* prepare */
        $args['group_monetization_enabled'] = (isset($args['group_monetization_enabled'])) ? '1' : '0';
        /* update group */
        $db->query(sprintf("UPDATE `groups` SET group_monetization_enabled = %s WHERE group_id = %s", secure($args['group_monetization_enabled']), secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update monetization plans */
        $this->update_monetization_plans($group_id, 'group');
        break;
    }
    return $group['group_name'];
  }


  /**
   * delete_group
   * 
   * @param integer $group_id
   * @return void
   */
  public function delete_group($group_id)
  {
    global $db, $system;
    /* check if groups enabled */
    if (!$system['groups_enabled']) {
      throw new Exception(__("The groups module has been disabled by the admin"));
    }
    /* check groups permission */
    if (!$this->_data['can_create_groups']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* (check&get) group */
    $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_group->num_rows == 0) {
      _error(403);
    }
    $group = $get_group->fetch_assoc();
    // delete group
    $can_delete = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_delete = true;
    }
    /* viewer is the super admin of group */
    if ($this->_data['user_id'] == $group['group_admin']) {
      $can_delete = true;
    }
    /* check if viewer can delete the group */
    if (!$can_delete) {
      _error(403);
    }
    /* delete the group */
    $db->query(sprintf("DELETE FROM `groups` WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete the group members */
    $db->query(sprintf("DELETE FROM groups_members WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete the group admins */
    $db->query(sprintf("DELETE FROM groups_admins WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* check if the group has chatbox */
    if ($group['chatbox_conversation_id']) {
      /* delete the chatbox */
      $this->delete_chatbox($group['chatbox_conversation_id']);
    }
  }


  /**
   * get_group_admins_ids
   * 
   * @param integer $group_id
   * @return array
   */
  public function get_group_admins_ids($group_id)
  {
    global $db;
    $admins = [];
    $get_admins = $db->query(sprintf("SELECT user_id FROM groups_admins WHERE group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_admins->num_rows > 0) {
      while ($admin = $get_admins->fetch_assoc()) {
        $admins[] = $admin['user_id'];
      }
    }
    return $admins;
  }


  /**
   * get_group_admins
   * 
   * @param integer $group_id
   * @param integer $offset
   * @return array
   */
  public function get_group_admins($group_id, $offset = 0)
  {
    global $db, $system;
    $admins = [];
    $offset *= $system['max_results_even'];
    $get_admins = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM groups_admins INNER JOIN users ON groups_admins.user_id = users.user_id WHERE groups_admins.group_id = %s LIMIT %s, %s", secure($group_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_admins->num_rows > 0) {
      while ($admin = $get_admins->fetch_assoc()) {
        $admin['user_picture'] = get_picture($admin['user_picture'], $admin['user_gender']);
        /* get the connection */
        $admin['i_admin'] = true;
        $admin['connection'] = 'group_manage';
        $admin['node_id'] = $group_id;
        $admins[] = $admin;
      }
    }
    return $admins;
  }


  /**
   * get_group_members
   * 
   * @param integer $group_id
   * @param integer $offset
   * @param boolean $manage
   * @return array
   */
  public function get_group_members($group_id, $offset = 0, $manage = false)
  {
    global $db, $system;
    $members = [];
    $offset *= $system['max_results_even'];
    $get_members = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM groups_members INNER JOIN users ON groups_members.user_id = users.user_id WHERE groups_members.approved = '1' AND groups_members.group_id = %s LIMIT %s, %s", secure($group_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_members->num_rows > 0) {
      /* get group admins ids */
      $group_admins_ids = $this->get_group_admins_ids($group_id);
      while ($member = $get_members->fetch_assoc()) {
        $member['user_picture'] = get_picture($member['user_picture'], $member['user_gender']);
        if ($manage) {
          /* get the connection */
          $member['i_admin'] = in_array($member['user_id'], $group_admins_ids);
          $member['connection'] = 'group_manage';
          $member['node_id'] = $group_id;
        } else {
          /* get the connection between the viewer & the target */
          $member['connection'] = $this->connection($member['user_id']);
        }
        $members[] = $member;
      }
    }
    return $members;
  }


  /**
   * get_group_members_ids
   * 
   * @param integer $group_id
   * @return array
   */
  public function get_group_members_ids($group_id)
  {
    global $db;
    $members = [];
    $get_members = $db->query(sprintf("SELECT user_id FROM groups_members WHERE approved = '1' AND group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_members->num_rows > 0) {
      while ($member = $get_members->fetch_assoc()) {
        $members[] = $member['user_id'];
      }
    }
    return $members;
  }


  /**
   * get_group_invites
   * 
   * @param integer $group_id
   * @param integer $offset
   * @return array
   */
  public function get_group_invites($group_id, $offset = 0)
  {
    global $db, $system;
    $friends = [];
    $offset *= $system['max_results_even'];
    $get_friends = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users LEFT JOIN groups_members ON users.user_id = groups_members.user_id AND groups_members.group_id = %s WHERE users.user_id IN (%s) AND groups_members.id IS NULL LIMIT %s, %s", secure($group_id, 'int'), $this->spread_ids($this->_data['friends_ids']), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    while ($friend = $get_friends->fetch_assoc()) {
      $friend['user_picture'] = get_picture($friend['user_picture'], $friend['user_gender']);
      $friend['connection'] = 'group_invite';
      $friend['node_id'] = $group_id;
      $friends[] = $friend;
    }
    return $friends;
  }


  /**
   * get_group_requests
   * 
   * @param integer $group_id
   * @param integer $offset
   * @return array
   */
  public function get_group_requests($group_id, $offset = 0)
  {
    global $db, $system;
    $requests = [];
    $offset *= $system['max_results'];
    $get_requests = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users INNER JOIN groups_members ON users.user_id = groups_members.user_id WHERE groups_members.approved = '0' AND groups_members.group_id = %s LIMIT %s, %s", secure($group_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_requests->num_rows > 0) {
      while ($request = $get_requests->fetch_assoc()) {
        $request['user_picture'] = get_picture($request['user_picture'], $request['user_gender']);
        $request['connection'] = 'group_request';
        $request['node_id'] = $group_id;
        $requests[] = $request;
      }
    }
    return $requests;
  }


  /**
   * get_group_requests_total
   * 
   * @param integer $group_id
   * @return integer
   */
  public function get_group_requests_total($group_id)
  {
    global $db, $system;
    $get_requests = $db->query(sprintf("SELECT COUNT(*) as count FROM groups_members WHERE approved = '0' AND group_id = %s", secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_requests->fetch_assoc()['count'];
  }


  /**
   * check_group_adminship
   * 
   * @param integer $user_id
   * @param integer $group_id
   * @return boolean
   */
  public function check_group_adminship($user_id, $group_id)
  {
    if ($this->_logged_in) {
      /* get group admins ids */
      $group_admins_ids = $this->get_group_admins_ids($group_id);
      if (in_array($user_id, $group_admins_ids)) {
        return true;
      }
    }
    return false;
  }


  /**
   * check_group_membership
   * 
   * @param integer $user_id
   * @param integer $group_id
   * @return mixed
   */
  public function check_group_membership($user_id, $group_id)
  {
    global $db;
    if ($this->_logged_in) {
      $get_membership = $db->query(sprintf("SELECT * FROM groups_members WHERE user_id = %s AND group_id = %s", secure($user_id, 'int'), secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_membership->num_rows > 0) {
        $membership = $get_membership->fetch_assoc();
        return ($membership['approved'] == '1') ? "approved" : "pending";
      }
    }
    return false;
  }


  /**
   * get_group_pending_posts
   * 
   * @param integer $group_id
   * @param boolean $get_all
   * @return integer
   */
  public function get_group_pending_posts($group_id, $get_all = false)
  {
    global $db, $system;
    $get_all_query = ($get_all) ? "" : sprintf(" AND user_type = 'user' AND user_id = %s", secure($this->_data['user_id'], 'int'));
    $get_pending_posts = $db->query(sprintf("SELECT COUNT(*) as count FROM posts WHERE in_group = '1' AND group_approved = '0' AND group_id = %s" . $get_all_query, secure($group_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_pending_posts->fetch_assoc()['count'];
  }



  /* ------------------------------- */
  /* Events */
  /* ------------------------------- */

  /**
   * get_events
   * 
   * @param array $args
   * @return array
   */
  public function get_events($args = [])
  {
    global $db, $system;
    /* initialize arguments */
    $user_id = !isset($args['user_id']) ? null : $args['user_id'];
    $page_id = !isset($args['page_id']) ? null : $args['page_id'];
    $category_id = !isset($args['category_id']) ? null : $args['category_id'];
    $offset = !isset($args['offset']) ? 0 : $args['offset'];
    $get_all = !isset($args['get_all']) ? false : true;
    $suggested = !isset($args['suggested']) ? false : true;
    $random = !isset($args['random']) ? false : true;
    $managed = !isset($args['managed']) ? false : true;
    $filter = !isset($args['filter']) ? "admin" : $args['filter'];
    $results = !isset($args['results']) ? $system['events_results'] : $args['results'];
    /* initialize vars */
    $events = [];
    $offset *= $results;
    /* prepare limit statement */
    $limit_statement = ($get_all) ? "" : sprintf(" LIMIT %s, %s ", secure($offset, 'int', false), secure($results, 'int', false));
    if ($suggested) {
      /* get suggested events */
      $where_statement = sprintf(" AND event_id NOT IN (%s) ", $this->spread_ids($this->get_events_ids()));
      $category_statement = ($category_id) ? sprintf(" AND event_category = %s ", secure($category_id, 'int')) : "";
      $sort_statement = ($random) ? " ORDER BY RAND() " : " ORDER BY event_id DESC ";
      $get_events = $db->query("SELECT * FROM `events` WHERE event_privacy != 'secret'" . $where_statement . $category_statement . $sort_statement . $limit_statement) or _error('SQL_ERROR_THROWEN');
    } elseif ($managed) {
      /* get the "taget" all events who admin */
      $get_events = $db->query(sprintf("SELECT * FROM `events` WHERE event_admin = %s ORDER BY event_id DESC", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($page_id != null) {
      /* get events that managed by target page */
      $get_events = $db->query(sprintf("SELECT * FROM `events` WHERE event_page_id = %s ORDER BY event_id DESC" . $limit_statement, secure($page_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($user_id == null) {
      /* get the "viewer" events who (going|interested|invited|admin) */
      switch ($filter) {
        case 'going':
          $get_events = $db->query(sprintf("SELECT `events`.* FROM `events` INNER JOIN events_members ON `events`.event_id = events_members.event_id WHERE events_members.is_going = '1' AND events_members.user_id = %s ORDER BY event_id DESC" . $limit_statement, secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'interested':
          $get_events = $db->query(sprintf("SELECT `events`.* FROM `events` INNER JOIN events_members ON `events`.event_id = events_members.event_id WHERE events_members.is_interested = '1' AND events_members.user_id = %s ORDER BY event_id DESC" . $limit_statement, secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        case 'invited':
          $get_events = $db->query(sprintf("SELECT `events`.* FROM `events` INNER JOIN events_members ON `events`.event_id = events_members.event_id WHERE events_members.is_invited = '1' AND events_members.user_id = %s ORDER BY event_id DESC" . $limit_statement, secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;

        default:
          $get_events = $db->query(sprintf("SELECT * FROM `events` WHERE event_admin = %s ORDER BY event_id DESC " . $limit_statement, secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          break;
      }
    } else {
      /* get the "target" events */
      /* get the target user's privacy */
      $get_privacy = $db->query(sprintf("SELECT user_privacy_events FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $privacy = $get_privacy->fetch_assoc();
      /* check the target user's privacy */
      if (!$this->check_privacy($privacy['user_privacy_events'], $user_id)) {
        return $events;
      }
      /* if the viewer not the target exclude secret groups */
      $where_statement = ($this->_data['user_id'] == $user_id) ? "" : " AND `events`.event_privacy != 'secret' ";
      $get_events = $db->query(sprintf("SELECT `events`.* FROM `events` INNER JOIN events_members ON `events`.event_id = events_members.event_id WHERE (events_members.is_going = '1' OR events_members.is_interested = '1') AND events_members.user_id = %s" . $where_statement . "ORDER BY event_id DESC" . $limit_statement, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_events->num_rows > 0) {
      while ($event = $get_events->fetch_assoc()) {
        $event['event_picture'] = get_picture($event['event_cover'], 'event');
        /* check if the viewer joined the event */
        $event['i_joined'] = $this->check_event_membership($this->_data['user_id'], $event['event_id']);;
        $events[] = $event;
      }
    }
    return $events;
  }


  /**
   * get_event
   * 
   * @param integer $event_id
   * @return array
   */
  public function get_event($event_id)
  {
    global $db;
    $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_event->num_rows == 0) {
      return false;
    }
    return $get_event->fetch_assoc();
  }


  /**
   * create_event
   * 
   * @param array $args
   * @return integer
   */
  public function create_event($args = [])
  {
    global $db, $system, $date;
    /* check if events enabled */
    if (!$system['events_enabled']) {
      throw new Exception(__("The events module has been disabled by the admin"));
    }
    /* check events permission */
    if (!$this->_data['can_create_events']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check if it is page event */
    if (isset($args['page_id'])) {
      /* check if events page enabled */
      if (!$system['pages_events_enabled']) {
        throw new Exception(__("The pages events has been disabled by the admin"));
      }
      /* get page */
      $page = $this->get_page($args['page_id']);
      if (!$page) {
        throw new Exception(__("The page you are trying to add event to doesn't exist"));
      }
      /* check page adminship */
      if (!$this->check_page_adminship($this->_data['user_id'], $page['page_id'])) {
        throw new Exception(__("You don't have the permission to do this"));
      }
    }
    /* validate title */
    if (is_empty($args['title'])) {
      throw new Exception(__("You must enter a name for your event"));
    }
    if (strlen($args['title']) < 3) {
      throw new Exception(__("Event name must be at least 3 characters long. Please try another"));
    }
    /* validate start & end dates */
    if (is_empty($args['start_date'])) {
      throw new Exception(__("You have to enter the event start date"));
    }
    if (is_empty($args['end_date'])) {
      throw new Exception(__("You have to enter the event end date"));
    }
    if (strtotime(set_datetime($args['start_date'])) > strtotime(set_datetime($args['end_date']))) {
      throw new Exception(__("Event end date must be after the start date"));
    }
    /* validate privacy */
    $args['privacy'] = (isset($args['page_id'])) ? "public" : $args['privacy'];
    if (!in_array($args['privacy'], ['secret', 'closed', 'public'])) {
      throw new Exception(__("You must select a valid privacy for your event"));
    }
    /* validate category */
    if (is_empty($args['category'])) {
      throw new Exception(__("You must select valid category for your event"));
    } else {
      if (!$this->check_category('events_categories', $args['category'])) {
        throw new Exception(__("You must select valid category for your event"));
      }
    }
    /* set custom fields */
    $custom_fields = $this->set_custom_fields($args, "event");
    /* check tickets link & prices info */
    if (isset($args['page_id'])) {
      if (!is_empty($args['tickets_link']) && !valid_url($args['tickets_link'])) {
        throw new Exception(__("Please enter a valid tickets link"));
      }
    }
    /* insert new event */
    if (isset($args['page_id'])) {
      $db->query(sprintf("INSERT INTO `events` (event_privacy, event_admin, event_page_id, event_category, event_title, event_location, event_description, event_start_date, event_end_date, event_date, event_tickets_link, event_prices) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($args['privacy']), secure($this->_data['user_id'], 'int'), secure($args['page_id'], 'int'), secure($args['category'], 'int'), secure($args['title']), secure($args['location']), secure($args['description']), secure($args['start_date'], 'datetime'), secure($args['end_date'], 'datetime'), secure($date), secure($args['tickets_link']), secure($args['prices']))) or _error('SQL_ERROR_THROWEN');
    } else {
      $db->query(sprintf("INSERT INTO `events` (event_privacy, event_admin, event_category, event_title, event_location, event_description, event_start_date, event_end_date, event_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($args['privacy']), secure($this->_data['user_id'], 'int'), secure($args['category'], 'int'), secure($args['title']), secure($args['location']), secure($args['description']), secure($args['start_date'], 'datetime'), secure($args['end_date'], 'datetime'), secure($date))) or _error('SQL_ERROR_THROWEN');
    }
    /* get event_id */
    $event_id = $db->insert_id;
    /* insert custom fields values */
    if ($custom_fields) {
      foreach ($custom_fields as $field_id => $value) {
        $db->query(sprintf("INSERT INTO custom_fields_values (value, field_id, node_id, node_type) VALUES (%s, %s, %s, 'event')", secure($value), secure($field_id, 'int'), secure($event_id, 'int')));
      }
    }
    /* interest the event */
    $this->connect("event-interest", $event_id);
    /* notify page memebers */
    if (isset($args['page_id'])) {
      foreach ($this->get_page_members_ids($args['page_id']) as $member_id) {
        $this->post_notification(['to_user_id' => $member_id, 'from_user_id' => $args['page_id'], 'from_user_type' => 'page', 'action' => 'page_event', 'node_type' => 'event', 'node_url' => $event_id]);
      }
    }
    /* return event id */
    return $event_id;
  }


  /**
   * edit_event
   * 
   * @param integer $event_id
   * @param string $title
   * @param string $location
   * @param string $start_date
   * @param string $end_date
   * @param string $privacy
   * @param integer $category
   * @param string $description
   * @return void
   */
  public function edit_event($event_id, $args)
  {
    global $db, $system;
    /* check if events enabled */
    if (!$system['events_enabled']) {
      throw new Exception(__("The events module has been disabled by the admin"));
    }
    /* check events permission */
    if (!$this->_data['can_create_events']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* (check&get) event */
    $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_event->num_rows == 0) {
      _error(403);
    }
    $event = $get_event->fetch_assoc();
    /* check permission */
    $can_edit = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_edit = true;
      /* viewer is the admin of event */
    } elseif ($this->check_event_adminship($this->_data['user_id'], $event['event_id'])) {
      $can_edit = true;
    }
    if (!$can_edit) {
      _error(403);
    }
    /* check if it is page event */
    if ($event['event_page_id']) {
      /* check if events page enabled */
      if (!$system['pages_events_enabled']) {
        throw new Exception(__("The pages events has been disabled by the admin"));
      }
      /* get page */
      $page = $this->get_page($event['event_page_id']);
      if (!$page) {
        throw new Exception(__("The page you are trying to add event to doesn't exist"));
      }
      /* check page adminship */
      if (!$this->check_page_adminship($this->_data['user_id'], $page['page_id'])) {
        throw new Exception(__("You don't have the permission to do this"));
      }
    }
    /* validate title */
    if (is_empty($args['title'])) {
      throw new Exception(__("You must enter a name for your event"));
    }
    if (strlen($args['title']) < 3) {
      throw new Exception(__("Event name must be at least 3 characters long. Please try another"));
    }
    /* validate start & end dates */
    if (is_empty($args['start_date'])) {
      throw new Exception(__("You have to enter the event start date"));
    }
    if (is_empty($args['end_date'])) {
      throw new Exception(__("You have to enter the event end date"));
    }
    if (strtotime(set_datetime($args['start_date'])) > strtotime(set_datetime($args['end_date']))) {
      throw new Exception(__("Event end date must be after the start date"));
    }
    /* validate privacy */
    $args['privacy'] = ($event['event_page_id']) ? "public" : $args['privacy'];
    if (!in_array($args['privacy'], ['secret', 'closed', 'public'])) {
      throw new Exception(__("You must select a valid privacy for your event"));
    }
    /* validate category */
    if (is_empty($args['category'])) {
      throw new Exception(__("You must select valid category for your event"));
    } else {
      if (!$this->check_category('events_categories', $args['category'])) {
        throw new Exception(__("You must select valid category for your event"));
      }
    }
    /* set custom fields */
    $this->set_custom_fields($args, "event", "settings", $event_id);
    /* check tickets link & prices info */
    if ($event['event_page_id']) {
      if (!is_empty($args['tickets_link']) && !valid_url($args['tickets_link'])) {
        throw new Exception(__("Please enter a valid tickets link"));
      }
    } else {
      $args['tickets_link'] = '';
      $args['prices'] = '';
    }
    /* update the event */
    $args['chatbox_enabled'] = (isset($args['chatbox_enabled'])) ? '1' : '0';
    $args['event_publish_enabled'] = (isset($args['event_publish_enabled'])) ? '1' : '0';
    $args['event_publish_approval_enabled'] = (isset($args['event_publish_approval_enabled'])) ? '1' : '0';
    $db->query(sprintf("UPDATE `events` SET event_privacy = %s, event_category = %s, event_title = %s, event_location = %s, event_description = %s, event_start_date = %s, event_end_date = %s, chatbox_enabled = %s, event_publish_enabled = %s, event_publish_approval_enabled = %s, event_tickets_link = %s, event_prices = %s WHERE event_id = %s", secure($args['privacy']), secure($args['category'], 'int'), secure($args['title']), secure($args['location']), secure($args['description']), secure($args['start_date'], 'datetime'), secure($args['end_date'], 'datetime'), secure($args['chatbox_enabled']), secure($args['event_publish_enabled']), secure($args['event_publish_approval_enabled']), secure($args['tickets_link']), secure($args['prices']), secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* check if chatbox enabled */
    if ($args['chatbox_enabled']) {
      /* create new chatbox if not */
      if (!$event['chatbox_conversation_id']) {
        $conversation_id = $this->create_chatbox($event_id, 'event');
        /* update event */
        $db->query(sprintf("UPDATE `events` SET chatbox_conversation_id = %s WHERE event_id = %s", secure($conversation_id, 'int'), secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      }
    }
    /* check if post approval disabled */
    if (!$args['event_publish_approval_enabled']) {
      /* approve any pending posts */
      $db->query(sprintf("UPDATE posts SET event_approved = '1' WHERE in_event = '1' AND event_id = %s", secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * delete_event
   * 
   * @param integer $event_id
   * @return void
   */
  public function delete_event($event_id)
  {
    global $db, $system;
    /* check if events enabled */
    if (!$system['events_enabled']) {
      throw new Exception(__("The events module has been disabled by the admin"));
    }
    /* check events permission */
    if (!$this->_data['can_create_events']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* (check&get) event */
    $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_event->num_rows == 0) {
      _error(403);
    }
    $event = $get_event->fetch_assoc();
    // delete event
    $can_delete = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_delete = true;
      /* viewer is the admin of event */
    } elseif ($this->check_event_adminship($this->_data['user_id'], $event['event_id'])) {
      $can_delete = true;
    }
    /* delete the event */
    if (!$can_delete) {
      _error(403);
    }
    /* delete event */
    $db->query(sprintf("DELETE FROM `events` WHERE event_id = %s", secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete event members */
    $db->query(sprintf("DELETE FROM `events_members` WHERE event_id = %s", secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* check if the event has chatbox */
    if ($event['chatbox_conversation_id']) {
      /* delete the chatbox */
      $this->delete_chatbox($event['chatbox_conversation_id']);
    }
  }


  /**
   * get_event_members
   * 
   * @param integer $event_id
   * @param string $handle
   * @param integer $offset
   * @return array
   */
  public function get_event_members($event_id, $handle, $offset = 0)
  {
    global $db, $system;
    $members = [];
    $offset *= $system['max_results_even'];
    switch ($handle) {
      case 'going':
        $get_members = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM events_members INNER JOIN users ON events_members.user_id = users.user_id WHERE  events_members.is_going = '1' AND events_members.event_id = %s LIMIT %s, %s", secure($event_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'interested':
        $get_members = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM events_members INNER JOIN users ON events_members.user_id = users.user_id WHERE  events_members.is_interested = '1' AND events_members.event_id = %s LIMIT %s, %s", secure($event_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'invited':
        $get_members = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM events_members INNER JOIN users ON events_members.user_id = users.user_id WHERE  events_members.is_invited = '1' AND events_members.event_id = %s LIMIT %s, %s", secure($event_id, 'int'), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
        break;

      default:
        _error(400);
        break;
    }
    if ($get_members->num_rows > 0) {
      while ($member = $get_members->fetch_assoc()) {
        $member['user_picture'] = get_picture($member['user_picture'], $member['user_gender']);
        /* get the connection between the viewer & the target */
        $member['connection'] = $this->connection($member['user_id']);
        $members[] = $member;
      }
    }
    return $members;
  }


  /**
   * get_event_members_ids
   * 
   * @param integer $event_id
   * @return array
   */
  public function get_event_members_ids($event_id)
  {
    global $db;
    $members = [];
    $get_members = $db->query(sprintf("SELECT user_id FROM events_members WHERE event_id = %s", secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_members->num_rows > 0) {
      while ($member = $get_members->fetch_assoc()) {
        $members[] = $member['user_id'];
      }
    }
    return $members;
  }


  /**
   * get_event_invites
   * 
   * @param integer $event_id
   * @param integer $offset
   * @return array
   */
  public function get_event_invites($event_id, $offset = 0)
  {
    global $db, $system;
    $friends = [];
    $offset *= $system['max_results_even'];
    $get_friends = $db->query(sprintf("SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users LEFT JOIN events_members ON users.user_id = events_members.user_id AND events_members.event_id = %s WHERE users.user_id IN (%s) AND events_members.id IS NULL LIMIT %s, %s", secure($event_id, 'int'), $this->spread_ids($this->_data['friends_ids']), secure($offset, 'int', false), secure($system['max_results_even'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    while ($friend = $get_friends->fetch_assoc()) {
      $friend['user_picture'] = get_picture($friend['user_picture'], $friend['user_gender']);
      $friend['connection'] = 'event_invite';
      $friend['node_id'] = $event_id;
      $friends[] = $friend;
    }
    return $friends;
  }


  /**
   * check_event_adminship
   * 
   * @param integer $user_id
   * @param integer $event_id
   * @return boolean
   */
  public function check_event_adminship($user_id, $event_id)
  {
    global $db;
    if ($this->_logged_in) {
      $check = $db->query(sprintf("SELECT COUNT(*) as count FROM `events` WHERE event_admin = %s AND event_id = %s", secure($user_id, 'int'), secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($check->fetch_assoc()['count'] > 0) {
        return true;
      }
    }
    return false;
  }


  /**
   * check_event_membership
   * 
   * @param integer $user_id
   * @param integer $event_id
   * @return mixed
   */
  public function check_event_membership($user_id, $event_id)
  {
    global $db;
    if ($this->_logged_in) {
      $get_membership = $db->query(sprintf("SELECT is_invited, is_interested, is_going FROM events_members WHERE user_id = %s AND event_id = %s", secure($user_id, 'int'), secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_membership->num_rows > 0) {
        return $get_membership->fetch_assoc();
      }
    }
    return false;
  }


  /**
   * get_event_pending_posts
   * 
   * @param integer $event_id
   * @param boolean $get_all
   * @return integer
   */
  public function get_event_pending_posts($event_id, $get_all = false)
  {
    global $db, $system;
    $get_all_query = ($get_all) ? "" : sprintf(" AND user_type = 'user' AND user_id = %s", secure($this->_data['user_id'], 'int'));
    $get_pending_posts = $db->query(sprintf("SELECT COUNT(*) as count FROM posts WHERE in_event = '1' AND event_approved = '0' AND event_id = %s" . $get_all_query, secure($event_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_pending_posts->fetch_assoc()['count'];
  }



  /* ------------------------------- */
  /* Blogs */
  /* ------------------------------- */

  /**
   * get_articles
   * 
   * @param array $args
   * @return array
   */
  public function get_articles($args = [])
  {
    global $db, $system;
    /* initialize arguments */
    $category = !isset($args['category']) ? null : $args['category'];
    $offset = !isset($args['offset']) ? 0 : $args['offset'];
    $random = !isset($args['random']) ? false : true;
    $results = !isset($args['results']) ? $system['max_results_even'] : $args['results'];
    /* initialize vars */
    $posts = [];
    $offset *= $results;
    /* get posts */
    $where_query = ($category != null) ? sprintf("AND posts_articles.category_id = %s", secure($args['category'], 'int')) : "";
    $order_query = ($random) ? "ORDER BY RAND()" : "ORDER BY posts.post_id DESC";
    $get_posts = $db->query(sprintf("SELECT posts.post_id FROM posts INNER JOIN posts_articles ON posts.post_id = posts_articles.post_id AND posts.post_type = 'article' WHERE posts.in_group = '0' AND posts.in_event = '0' AND (posts.pre_approved = '1' OR posts.has_approved = '1') " . $where_query . " " . $order_query . " LIMIT %s, %s", secure($offset, 'int', false), secure($results, 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_posts->num_rows > 0) {
      while ($post = $get_posts->fetch_assoc()) {
        $post = $this->get_post($post['post_id']);
        if ($post) {
          $posts[] = $post;
        }
      }
    }
    return $posts;
  }


  /**
   * post_article
   * 
   * @param string $publish_to
   * @param string $page_id
   * @param string $group_id
   * @param string $event_id
   * @param string $title
   * @param string $text
   * @param string $cover
   * @param integer $category_id
   * @param string $tags
   * @param boolean $tips_enabled
   * @param boolean $for_subscriptions
   * @param boolean $is_paid
   * @param float $post_price
   * @param string $paid_text
   * @return integer
   */
  public function post_article($publish_to, $page_id, $group_id, $event_id, $title, $text, $cover, $category_id, $tags, $tips_enabled, $for_subscriptions, $is_paid, $post_price, $paid_text)
  {
    global $db, $system, $date;
    /* check if blogs enabled */
    if (!$system['blogs_enabled']) {
      throw new Exception(__("The blogs module has been disabled by the admin"));
    }
    /* check blogs permission */
    if (!$this->_data['can_write_articles']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check if verification for posts required */
    if ($system['verification_for_posts'] && !$this->_data['user_verified']) {
      throw new Exception(__("To use this feature your account must be verified"));
    }
    /* check max posts/hour limit */
    $this->_check_posts_limit(($publish_to == 'page') ? 'page' : 'user', ($publish_to == 'page') ? $page_id : null);
    /* check tips permission */
    if (!$this->_data['can_receive_tip'] && $tips_enabled) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check monetization permission */
    if ($for_subscriptions) {
      switch ($publish_to) {
        case 'timeline':
          if (!($this->_data['can_monetize_content'] && $this->_data['user_monetization_enabled'] && $this->_data['user_monetization_plans'] > 0)) {
            throw new Exception(__("You don't have the permission to do this"));
          }
          break;

        case 'page':
          /* check if the page is valid */
          $page = $this->get_page($page_id);
          if (!$page) {
            _error(400);
          }
          /* check if page's admin can monetize content */
          $page['can_monetize_content'] = $system['monetization_enabled'] && $this->check_user_permission($page['page_admin'], 'monetization_permission');
          /* check if page has monetization enabled && subscriptions plans */
          if (!($page['can_monetize_content'] && $page['page_monetization_enabled'] && $page['page_monetization_plans'] > 0)) {
            throw new Exception(__("You don't have the permission to do this"));
          }
          break;

        case 'group':
          /* check if the group is valid */
          $group = $this->get_group($group_id);
          if (!$group) {
            _error(400);
          }
          /* check if group's admin can monetize content */
          $group['can_monetize_content'] = $system['monetization_enabled'] && $this->check_user_permission($group['group_admin'], 'monetization_permission');
          /* check if group has monetization enabled && subscriptions plans */
          if (!($group['can_monetize_content'] && $group['group_monetization_enabled'] && $group['group_monetization_plans'] > 0)) {
            throw new Exception(__("You don't have the permission to do this"));
          }
          break;

        default:
          throw new Exception(__("You don't have the permission to do this"));
          break;
      }
    }
    /* check paid posts permission */
    if (!$this->_data['can_monetize_content'] && !$this->_data['user_monetization_enabled'] && $is_paid) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check if both for_subscriptions & is_paid enabled */
    if ($for_subscriptions && $is_paid) {
      throw new Exception(__("You can't enable both subscribers only & paid post"));
    }
    /* validate title */
    if (is_empty($title)) {
      throw new Exception(__("You must enter a title for your article"));
    }
    if (strlen($title) < 3) {
      throw new Exception(__("Article title must be at least 3 characters long. Please try another"));
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your article"));
    }
    /* validate category */
    if (is_empty($category_id)) {
      throw new Exception(__("You must select valid category for your article"));
    }
    $category = $this->get_category('blogs_categories', $category_id);
    if (!$category) {
      throw new Exception(__("You must select valid category for your article"));
    }
    /* validate price */
    if ($is_paid) {
      if (!is_numeric($post_price) || $post_price <= 0) {
        throw new Exception(__("Please enter a valid price"));
      }
      if (strlen($paid_text) > 1000) {
        modal("ERROR", __("Error"), __("Paid post description is more than 1000 characters"));
      }
    }
    /* prepare text */
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.SafeIframe', true);
      $config->set('URI.SafeIframeRegexp', '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/)%');
      $purifier = new HTMLPurifier($config);
      $clean_text = $purifier->purify($text);
    }
    /* prepare tags */
    $tags = ($tags) ? implode(',', array_column(json_decode(html_entity_decode($tags), true), "value")) : '';
    /* prepare tips enabled */
    $tips_enabled = ($publish_to != 'page' && $tips_enabled) ? '1' : '0';
    /* prepare approval system */
    $pre_approved = 1;
    $has_approved = 1;
    $author_id = $publish_to == 'page' ? $page_id : $this->_data['user_id'];
    $author_type = $publish_to == 'page' ? 'page' : 'user';
    if ($this->check_posts_needs_approval($author_id, $author_type)) {
      $pre_approved = 0;
      $has_approved = 0;
    }
    /* publish to */
    switch ($publish_to) {
      case 'timeline':
        /* insert the post */
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, tips_enabled, for_subscriptions, is_paid, post_price, paid_text, time, privacy, pre_approved, has_approved) VALUES (%s, 'user', 'article', %s, %s, %s, %s, %s, %s, 'public', %s, %s)", secure($this->_data['user_id'], 'int'), secure($tips_enabled), secure($for_subscriptions), secure($is_paid), secure($post_price, 'float'), secure($paid_text), secure($date), secure($pre_approved), secure($has_approved))) or _error('SQL_ERROR_THROWEN');
        $post_id = $db->insert_id;
        break;

      case 'page':
        /* check if the page is valid */
        $page = $this->get_page($page_id);
        if (!$page) {
          _error(400);
        }
        /* check if the viewer is page admin */
        if (!$this->check_page_adminship($this->_data['user_id'], $page_id)) {
          _error(400);
        }
        /* insert the post */
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, tips_enabled, for_subscriptions, is_paid, post_price, paid_text, time, privacy, pre_approved, has_approved) VALUES (%s, 'page', 'article', %s, %s, %s, %s, %s, %s, 'public', %s, %s)", secure($page_id, 'int'), secure($tips_enabled), secure($for_subscriptions), secure($is_paid), secure($post_price, 'float'), secure($paid_text), secure($date), secure($pre_approved), secure($has_approved))) or _error('SQL_ERROR_THROWEN');
        $post_id = $db->insert_id;
        break;

      case 'group':
        /* check if the group is valid */
        $group = $this->get_group($group_id);
        if (!$group) {
          _error(400);
        }
        /* check if the viewer is group member */
        if ($this->check_group_membership($this->_data['user_id'], $group_id) != "approved") {
          _error(400);
        }
        /* check if user is a group admin */
        $group['i_admin'] = $this->check_group_adminship($this->_data['user_id'], $group_id);
        /* check if group publishing enabled */
        if (!$group['group_publish_enabled'] && !$group['i_admin']) {
          throw new Exception(__("Publishing to this group has been disabled by group admins"));
        }
        /* insert the post */
        $group_approved = ($group['group_publish_approval_enabled'] && !$group['i_admin']) ? "0" : "1";
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, tips_enabled, for_subscriptions, is_paid, post_price, paid_text, time, privacy, in_group, group_id, group_approved, pre_approved, has_approved) VALUES (%s, 'user', 'article', %s, %s, %s, %s, %s, %s, 'custom', '1', %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($tips_enabled), secure($for_subscriptions), secure($is_paid), secure($post_price, 'float'), secure($paid_text), secure($date), secure($group_id, 'int'), secure($group_approved), secure($pre_approved), secure($has_approved))) or _error('SQL_ERROR_THROWEN');
        $post_id = $db->insert_id;
        /* post in_group notification */
        if (!$group_approved) {
          /* send notification to group admin */
          $this->post_notification(['to_user_id' => $group['group_admin'], 'action' => 'group_post_pending', 'node_type' => $group['group_title'], 'node_url' => $group['group_name'] . "-[guid=]" . $post_id]);
        }
        break;

      case 'event':
        /* check if the event is valid */
        $event = $this->get_event($event_id);
        if (!$event) {
          _error(400);
        }
        /* check if the viewer is event member */
        if (!$this->check_event_membership($this->_data['user_id'], $event_id)) {
          _error(400);
        }
        /* check if user is a event admin */
        $event['i_admin'] = $this->check_event_adminship($this->_data['user_id'], $event_id);
        /* check if event publishing enabled */
        if (!$event['event_publish_enabled'] && !$event['i_admin']) {
          throw new Exception(__("Publishing to this event has been disabled by event admins"));
        }
        /* insert the post */
        $event_approved = ($event['event_publish_approval_enabled'] && !$event['i_admin']) ? "0" : "1";
        $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, tips_enabled, is_paid, post_price, paid_text, time, privacy, in_event, event_id, event_approved, pre_approved, has_approved) VALUES (%s, 'user', 'article', %s, %s, %s, %s, %s, 'custom', '1', %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($tips_enabled), secure($is_paid), secure($post_price, 'float'), secure($paid_text), secure($date), secure($event_id, 'int'), secure($event_approved), secure($pre_approved), secure($has_approved))) or _error('SQL_ERROR_THROWEN');
        $post_id = $db->insert_id;
        /* post in_event notification */
        if (!$event_approved) {
          /* send notification to event admin */
          $this->post_notification(['to_user_id' => $event['event_admin'], 'action' => 'event_post_pending', 'node_type' => $event['event_title'], 'node_url' => $event['event_name'] . "-[guid=]" . $post_id]);
        }
        break;

      default:
        _error(403);
        break;
    }
    /* insert article */
    $db->query(sprintf("INSERT INTO posts_articles (post_id, cover, title, text, category_id, tags) VALUES (%s, %s, %s, %s, %s, %s)", secure($post_id, 'int'), secure($cover), secure($title), secure($clean_text), secure($category_id, 'int'), secure($tags))) or _error('SQL_ERROR_THROWEN');
    /* points balance */
    $this->points_balance("add", $this->_data['user_id'], "post", $post_id);
    /* check if post is pending */
    if ($has_approved == 0) {
      /* send notification to admins */
      $this->notify_system_admins("pending_post");
    }
    return $post_id;
  }


  /**
   * edit_article
   * 
   * @param integer $post_id
   * @param string $title
   * @param string $text
   * @param string $cover
   * @param integer $category_id
   * @param string $tags
   * @param boolean $tips_enabled
   * @param boolean $for_subscriptions
   * @param boolean $is_paid
   * @param float $post_price
   * @param string $paid_text
   * @return void
   */
  public function edit_article($post_id, $title, $text, $cover, $category_id, $tags, $tips_enabled, $for_subscriptions, $is_paid, $post_price, $paid_text)
  {
    global $db, $system, $date;
    /* (check|get) post */
    $post = $this->_check_post($post_id, true);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    /* check if blogs enabled */
    if (!$system['blogs_enabled']) {
      throw new Exception(__("The blogs has been disabled by the admin"));
    }
    /* check blogs permission */
    if (!$this->_data['can_write_articles']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check tips permission */
    if (!$this->_data['can_receive_tip'] && $tips_enabled) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check monetization permission */
    if ($for_subscriptions) {
      if (!$post['can_be_for_subscriptions']) {
        throw new Exception(__("You don't have the permission to do this"));
      }
    }
    /* check paid posts permission */
    if (!$this->_data['can_monetize_content'] && !$this->_data['user_monetization_enabled'] && $is_paid) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check if both for_subscriptions & is_paid enabled */
    if ($for_subscriptions && $is_paid) {
      throw new Exception(__("You can't enable both subscribers only & paid post"));
    }
    /* validate title */
    if (is_empty($title)) {
      throw new Exception(__("You must enter a title for your article"));
    }
    if (strlen($title) < 3) {
      throw new Exception(__("Article title must be at least 3 characters long. Please try another"));
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your article"));
    }
    /* validate category */
    if (is_empty($category_id)) {
      throw new Exception(__("You must select valid category for your article"));
    }
    $category = $this->get_category('blogs_categories', $category_id);
    if (!$category) {
      throw new Exception(__("You must select valid category for your article"));
    }
    /* validate price */
    if ($is_paid) {
      if (!is_numeric($post_price) || $post_price <= 0) {
        throw new Exception(__("Please enter a valid price"));
      }
      if (strlen($paid_text) > 1000) {
        modal("ERROR", __("Error"), __("Paid post description is more than 1000 characters"));
      }
    }
    /* prepare text */
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.SafeIframe', true);
      $config->set('URI.SafeIframeRegexp', '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/)%');
      $purifier = new HTMLPurifier($config);
      $clean_text = $purifier->purify($text);
    }
    /* prepare tags */
    $tags = ($tags) ? implode(',', array_column(json_decode(html_entity_decode($tags), true), "value")) : '';
    /* prepare tips enabled */
    $tips_enabled = ($post['user_type'] != 'page' && $tips_enabled) ? '1' : '0';
    /* update the post */
    $db->query(sprintf("UPDATE posts SET tips_enabled = %s, for_subscriptions = %s, is_paid = %s, post_price = %s, paid_text = %s WHERE post_id = %s", secure($tips_enabled), secure($for_subscriptions), secure($is_paid), secure($post_price, 'float'), secure($oaid_text), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update the article */
    $db->query(sprintf("UPDATE posts_articles SET cover = %s, title = %s, text = %s, category_id = %s, tags = %s WHERE post_id = %s", secure($cover), secure($title), secure($clean_text), secure($category_id, 'int'), secure($tags), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * update_article_views
   * 
   * @param integer $article_id
   * @return void
   */
  public function update_article_views($article_id)
  {
    global $db;
    /* update */
    $db->query(sprintf("UPDATE posts_articles SET views = views + 1 WHERE article_id = %s", secure($article_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Jobs */
  /* ------------------------------- */

  /**
   * check_user_job_application
   * 
   * @param integer $post_id
   * @return boolean
   */
  public function check_user_job_application($post_id)
  {
    global $db;
    $check_application = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_jobs_applications WHERE post_id = %s AND user_id = %s", secure($post_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_application->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }


  /**
   * send_job_application
   * 
   * @param array $post
   * @param array $args
   * @return void
   */
  public function send_job_application($post, $args = [])
  {
    global $db, $date;
    /* prepare inputs */
    $args['work_now'] = (isset($args['work_now'])) ? '1' : '0';
    /* insert job application */
    $db->query(sprintf("INSERT INTO posts_jobs_applications (post_id, user_id, name, location, phone, email, work_place, work_position, work_description, work_from, work_to, work_now, question_1_answer, question_2_answer, question_3_answer, cv, applied_time) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($post['post_id'], 'int'), secure($this->_data['user_id'], 'int'), secure($args['name']), secure($args['location']), secure($args['phone']), secure($args['email']), secure($args['work_place']), secure($args['work_position']), secure($args['work_description']), secure($args['work_from']), secure($args['work_to']), secure($args['work_now']), secure($args['question_1_answer']), secure($args['question_2_answer']), secure($args['question_3_answer']), secure($args['cv']), secure($date))) or _error('SQL_ERROR_THROWEN');
    $application_id = $db->insert_id;
    /* send notification to post author */
    $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'job_application', 'node_type' => ($post['page_title']) ? $post['page_title'] : '', 'node_url' => $post['post_id']]);
  }


  /**
   * get_total_job_candidates
   * 
   * @param integer $post_id
   * @return integer
   */
  public function get_total_job_candidates($post_id)
  {
    global $db;
    $get_candidates = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_jobs_applications WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $get_candidates->fetch_assoc()['count'];
  }


  /**
   * get_job_candidates
   * 
   * @param integer $post_id
   * @param integer $offset
   * @return array
   */
  public function get_job_candidates($post_id, $offset = 0)
  {
    global $db, $system;
    /* (check|get) post */
    $post = $this->get_post($post_id, false);
    if (!$post) {
      _error(403);
    }
    /* check if viewer can edit post */
    if (!$post['manage_post']) {
      _error(403);
    }
    $candidates = [];
    $offset *= $system['max_results'];
    $get_candidates = $db->query(sprintf('SELECT posts_jobs_applications.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM posts_jobs_applications INNER JOIN users ON posts_jobs_applications.user_id = users.user_id WHERE posts_jobs_applications.post_id = %s LIMIT %s, %s', secure($post_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_candidates->num_rows > 0) {
      while ($candidate = $get_candidates->fetch_assoc()) {
        $candidate['user_picture'] = get_picture($candidate['user_picture'], $candidate['user_gender']);
        $candidate['question_1_title'] = $post['job']['question_1_title'];
        $candidate['question_1_answer'] = ($post['job']['question_1_type'] != "multiple_choice") ? $candidate['question_1_answer'] : $post['job']['question_1_options'][$candidate['question_1_answer']];
        $candidate['question_2_title'] = $post['job']['question_2_title'];
        $candidate['question_2_answer'] = ($post['job']['question_2_type'] != "multiple_choice") ? $candidate['question_2_answer'] : $post['job']['question_2_options'][$candidate['question_2_answer']];
        $candidate['question_3_title'] = $post['job']['question_3_title'];
        $candidate['question_3_answer'] = ($post['job']['question_3_type'] != "multiple_choice") ? $candidate['question_3_answer'] : $post['job']['question_3_options'][$candidate['question_3_answer']];
        $candidates[] = $candidate;
      }
    }
    return $candidates;
  }



  /* ------------------------------- */
  /* Forums */
  /* ------------------------------- */

  /**
   * get_forums
   * 
   * @param integer $node_id
   * @param boolean $reverse
   * @return array
   */
  public function get_forums($node_id = 0, $reverse = false)
  {
    global $db;
    $tree = [];
    if (!$reverse) {
      // top-down tree (default)
      $get_nodes = $db->query(sprintf("SELECT * FROM forums WHERE forum_section = %s ORDER BY forum_order ASC", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_nodes->num_rows > 0) {
        while ($node = $get_nodes->fetch_assoc()) {
          $node['title_url'] = get_url_text($node['forum_name']);
          $node['total_threads'] = $node['forum_threads'];
          $node['total_replies'] = $node['forum_replies'];
          $childs = $this->get_child_forums($node['forum_id']);
          if ($childs) {
            $node['total_threads'] += $childs['total_threads'];
            $node['total_replies'] += $childs['total_replies'];
            $node['childs'] = $childs['childs'];
          }
          $tree[] = $node;
        }
      }
    } else {
      // bottom-up tree
      while (true) {
        $get_parent = $db->query(sprintf("SELECT * FROM forums WHERE forum_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($get_parent->num_rows == 0) {
          break;
        }
        $parent = $get_parent->fetch_assoc();
        $parent['title_url'] = get_url_text($parent['forum_name']);
        $node_id = $parent['forum_section'];
        $tree[] = $parent;
        if ($node_id == 0) {
          break;
        }
      }
    }
    return $tree;
  }


  /**
   * get_child_forums
   * 
   * @param integer $node_id
   * @param integer $iteration
   * @return array
   */
  public function get_child_forums($node_id, $iteration = 1)
  {
    global $db;
    $tree = [];
    $get_nodes = $db->query(sprintf("SELECT * FROM forums WHERE forum_section = %s ORDER BY forum_order ASC", secure($node_id))) or _error('SQL_ERROR_THROWEN');
    if ($get_nodes->num_rows > 0) {
      while ($node = $get_nodes->fetch_assoc()) {
        $node['iteration'] = $iteration;
        $node['title_url'] = get_url_text($node['forum_name']);
        $node['total_threads'] = $node['forum_threads'];
        $node['total_replies'] = $node['forum_replies'];
        $childs = $this->get_child_forums($node['forum_id'], $iteration + 1);
        if ($childs) {
          $node['total_threads'] += $childs['total_threads'];
          $node['total_replies'] += $childs['total_replies'];
          $node['childs'] = $childs['childs'];
        }
        $tree['total_threads'] += $node['total_threads'];
        $tree['total_replies'] += $node['total_replies'];
        $tree['childs'][] = $node;
      }
    }
    return $tree;
  }


  /**
   * get_child_forums_ids
   * 
   * @param array $nodes
   * @return void
   */
  public function get_child_forums_ids($nodes, &$list = [])
  {
    if (is_array($nodes)) {
      foreach ($nodes as $node) {
        $list[] = $node['forum_id'];
        if ($node['childs']) {
          $this->get_child_forums_ids($node['childs'], $list);
        }
      }
    }
  }


  /**
   * delete_forum
   * 
   * @param integer $node_id
   * @return void
   */
  public function delete_forum($node_id)
  {
    global $db;
    /* delete forum */
    $db->query(sprintf("DELETE FROM forums WHERE forum_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete replies */
    $db->query(sprintf("DELETE FROM forums_replies WHERE thread_id IN (SELECT thread_id FROM forums_threads WHERE forum_id = %s)", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete threads */
    $db->query(sprintf("DELETE FROM forums_threads WHERE forum_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete childs */
    $childs = $this->get_child_forums($node_id);
    if ($childs) {
      $this->delete_child_forums($childs['childs']);
    }
  }


  /**
   * delete_child_forums
   * 
   * @param array $nodes
   * @return void
   */
  public function delete_child_forums($nodes)
  {
    global $db;
    if (is_array($nodes)) {
      foreach ($nodes as $node) {
        /* delete forum */
        $db->query(sprintf("DELETE FROM forums WHERE forum_id = %s", secure($node['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* delete replies */
        $db->query(sprintf("DELETE FROM forums_replies WHERE thread_id IN (SELECT thread_id FROM forums_threads WHERE forum_id = %s)", secure($node['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* delete threads */
        $db->query(sprintf("DELETE FROM forums_threads WHERE forum_id = %s", secure($node['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* delete childs */
        if ($node['childs']) {
          $this->delete_child_forums($node['childs']);
        }
      }
    }
  }


  /**
   * get_forum
   * 
   * @param integer $forum_id
   * @param boolean $get_childs
   * @return array
   */
  public function get_forum($forum_id, $get_childs = true)
  {
    global $db;
    $get_forum = $db->query(sprintf("SELECT * FROM forums WHERE forum_id = %s", secure($forum_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_forum->num_rows == 0) {
      return false;
    }
    $forum = $get_forum->fetch_assoc();
    $forum['title_url'] = get_url_text($forum['forum_name']);
    $forum['total_threads'] = $forum['forum_threads'];
    $forum['total_replies'] = $forum['forum_replies'];
    if ($get_childs) {
      $childs = $this->get_child_forums($forum['forum_id']);
      if ($childs) {
        $forum['total_threads'] += $childs['total_threads'];
        $forum['total_replies'] += $childs['total_replies'];
        $forum['childs'] = $childs['childs'];
      }
    }
    $forum['parents'] = $this->get_forums($forum['forum_section'], true);
    return $forum;
  }


  /**
   * get_forum_threads
   * 
   * @param array $args
   * @return array
   */
  public function get_forum_threads($args = [])
  {
    global $db, $system, $smarty;
    /* initialize vars */
    $threads = [];
    /* initialize arguments */
    $forum = !isset($args['forum']) ? null : $args['forum'];
    $user_id = !isset($args['user_id']) ? null : $args['user_id'];
    $query = !isset($args['query']) ? null : $args['query'];
    /* get threads */
    if ($forum !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_threads WHERE forum_id = %s", secure($forum['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($user_id !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_threads WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($query !== null) {
      /* prepare where statement */
      $where = "";
      /* query */
      $where .= (!is_empty($query)) ? sprintf(' WHERE title LIKE %1$s OR text LIKE %1$s ', secure($query, 'search')) : "";
      /* forums list */
      if ($args['forums_list']) {
        $forums_list = $this->spread_ids($args['forums_list']);
        $where = ($where != '') ? $where . " AND forum_id IN ($forums_list) " : $where;
      }
      $get_total = $db->query("SELECT COUNT(*) as count FROM forums_threads" . $where) or _error('SQL_ERROR_THROWEN');
    }
    $total_items = $get_total->fetch_assoc()['count'];
    if ($total_items > 0) {
      require('class-pager.php');
      $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
      $params['total_items'] = $total_items;
      $params['items_per_page'] = $system['max_results'] * 2;
      /* prepare pager URL */
      if ($forum !== null) {
        $params['url'] = $system['system_url'] . '/forums/' . $forum['forum_id'] . '/' . $forum['title_url'] . '?page=%s';
      } elseif ($user_id !== null) {
        $params['url'] = $system['system_url'] . '/forums/my-threads?page=%s';
      } elseif ($query !== null) {
        $params['url'] = remove_querystring_var($_SERVER['REQUEST_URI'], 'page');
        $params['url'] = $params['url'] . '&page=%s';
      }
      $pager = new Pager($params);
      $limit_query = $pager->getLimitSql();
      if ($forum !== null) {
        $get_threads = $db->query(sprintf("SELECT forums_threads.*, users.user_name, users.user_firstname, users.user_lastname FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.forum_id = %s ORDER BY forums_threads.last_reply DESC " . $limit_query, secure($forum['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      } elseif ($user_id !== null) {
        $get_threads = $db->query(sprintf("SELECT forums_threads.*, users.user_name, users.user_firstname, users.user_lastname FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE forums_threads.user_id = %s ORDER BY forums_threads.last_reply DESC " . $limit_query, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      } elseif ($query !== null) {
        $get_threads = $db->query("SELECT forums_threads.*, users.user_name, users.user_firstname, users.user_lastname FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id " . $where . " ORDER BY forums_threads.last_reply DESC " . $limit_query) or _error('SQL_ERROR_THROWEN');
      }
      while ($thread = $get_threads->fetch_assoc()) {
        $thread['user_fullname'] = ($system['show_usernames_enabled']) ? $thread['user_name'] : $thread['user_firstname'] . " " . $thread['user_lastname'];
        $thread['title_url'] = get_url_text($thread['title']);
        /* parse text */
        $thread['parsed_text'] = htmlspecialchars_decode($thread['text'], ENT_QUOTES);
        $thread['text_snippet'] = get_snippet_text($thread['text']);
        /* get forum */
        if (!$forum) {
          $thread['forum'] = $this->get_forum($thread['forum_id'], false);
        }
        $threads[] = $thread;
      }
      /* assign variables */
      $smarty->assign('total', $params['total_items']);
      $smarty->assign('pager', $pager->getPager());
    }
    return $threads;
  }


  /**
   * get_forum_thread
   * 
   * @param integer $thread_id
   * @param boolean $update_views
   * @return array
   */
  public function get_forum_thread($thread_id, $update_views = false)
  {
    global $db, $system;
    $get_thread = $db->query(sprintf("SELECT forums_threads.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_threads INNER JOIN users ON forums_threads.user_id = users.user_id WHERE thread_id = %s", secure($thread_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_thread->num_rows == 0) {
      return false;
    }
    $thread = $get_thread->fetch_assoc();
    /* get forum */
    $thread['forum'] = $this->get_forum($thread['forum_id'], false);
    if (!$thread['forum']) {
      return false;
    }
    $thread['user_picture'] = get_picture($thread['user_picture'], $thread['user_gender']);
    $thread['user_fullname'] = ($system['show_usernames_enabled']) ? $thread['user_name'] : $thread['user_firstname'] . " " . $thread['user_lastname'];
    $thread['title_url'] = get_url_text($thread['title']);
    /* parse text */
    $thread['parsed_text'] = htmlspecialchars_decode($thread['text'], ENT_QUOTES);
    $thread['text_snippet'] = get_snippet_text($thread['text']);
    /* check if viewer can manage thread [Edit|Delete] */
    $thread['manage_thread'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $thread['manage_thread'] = true;
      }
      /* viewer is the author of thread */
      if ($this->_data['user_id'] == $thread['user_id']) {
        $thread['manage_thread'] = true;
      }
    }
    /* update thread views */
    if ($update_views) {
      $db->query(sprintf("UPDATE forums_threads SET views = views + 1 WHERE thread_id = %s", secure($thread['thread_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    return $thread;
  }


  /**
   * post_forum_thread
   * 
   * @param string $forum_id
   * @param string $title
   * @param string $text
   * @return integer
   */
  public function post_forum_thread($forum_id, $title, $text)
  {
    global $db, $system, $date;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get forum */
    $forum = $this->get_forum($forum_id, false);
    /* check forum */
    if (!$forum) {
      _error(403);
    }
    if ($forum['forum_section'] == 0) {
      throw new Exception(__("You can't add a thread to a main section"));
    }
    /* validate title */
    if (is_empty($title)) {
      throw new Exception(__("You must enter a title for your thread"));
    }
    if (strlen($title) < 3) {
      throw new Exception(__("Thread title must be at least 3 characters long. Please try another"));
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your thread"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.SafeIframe', true);
      $config->set('URI.SafeIframeRegexp', '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/)%');
      $purifier = new HTMLPurifier($config);
      $clean_text = $purifier->purify($text);
    }
    /* insert thread */
    $db->query(sprintf("INSERT INTO forums_threads (forum_id, user_id, title, text, time, last_reply) VALUES (%s, %s, %s, %s, %s, %s)", secure($forum_id, 'int'), secure($this->_data['user_id'], 'int'), secure($title), secure($clean_text), secure($date), secure($date))) or _error('SQL_ERROR_THROWEN');
    $thread_id = $db->insert_id;
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_threads = forum_threads + 1 WHERE forum_id = %s", secure($forum_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return $thread_id;
  }


  /**
   * edit_forum_thread
   * 
   * @param string $thread_id
   * @param string $title
   * @param string $text
   * @return void
   */
  public function edit_forum_thread($thread_id, $title, $text)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get thread */
    $thread = $this->get_forum_thread($thread_id);
    /* check thread */
    if (!$thread) {
      _error(403);
    }
    /* check permission */
    if (!$thread['manage_thread']) {
      _error(403);
    }
    /* validate title */
    if (is_empty($title)) {
      throw new Exception(__("You must enter a title for your thread"));
    }
    if (strlen($title) < 3) {
      throw new Exception(__("Thread title must be at least 3 characters long. Please try another"));
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your thread"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.SafeIframe', true);
      $config->set('URI.SafeIframeRegexp', '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/)%');
      $purifier = new HTMLPurifier($config);
      $clean_text = $purifier->purify($text);
    }
    /* edit thread */
    $db->query(sprintf("UPDATE forums_threads SET title = %s, text = %s WHERE thread_id = %s", secure($title), secure($clean_text), secure($thread_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * delete_forum_thread
   * 
   * @param string $thread_id
   * @return array
   */
  public function delete_forum_thread($thread_id)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get thread */
    $thread = $this->get_forum_thread($thread_id);
    /* check thread */
    if (!$thread) {
      _error(403);
    }
    /* check permission */
    if (!$thread['manage_thread']) {
      _error(403);
    }
    /* delete thread */
    $db->query(sprintf("DELETE FROM forums_threads WHERE thread_id = %s", secure($thread_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete replies */
    $db->query(sprintf("DELETE FROM forums_replies WHERE thread_id = %s", secure($thread_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_threads = IF(forum_threads=0,0,forum_threads-1), forum_replies = IF(forum_replies=0,0,forum_replies-%s) WHERE forum_id = %s", secure($thread['replies'], 'int'), secure($thread['forum']['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    return $thread['forum'];
  }


  /**
   * get_forum_replies
   * 
   * @param array $args
   * @return array
   */
  public function get_forum_replies($args = [])
  {
    global $db, $system, $smarty;
    /* initialize vars */
    $replies = [];
    /* initialize arguments */
    $thread = !isset($args['thread']) ? null : $args['thread'];
    $user_id = !isset($args['user_id']) ? null : $args['user_id'];
    $query = !isset($args['query']) ? null : $args['query'];
    /* get replies */
    if ($thread !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_replies WHERE thread_id = %s", secure($thread['thread_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($user_id !== null) {
      $get_total = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_replies WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    } elseif ($query !== null) {
      /* prepare where statement */
      $where = "";
      /* query */
      $where .= (!is_empty($query)) ? sprintf(' WHERE forums_replies.text LIKE %1$s ', secure($query, 'search')) : "";
      /* forums list */
      if ($args['forums_list']) {
        $forums_list = $this->spread_ids($args['forums_list']);
        $where = ($where != '') ? $where . " AND forums_threads.forum_id IN ($forums_list) " : $where;
      }
      $get_total = $db->query("SELECT COUNT(*) as count FROM forums_replies INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id" . $where) or _error('SQL_ERROR_THROWEN');
    }
    $total_items = $get_total->fetch_assoc()['count'];
    if ($total_items > 0) {
      require('class-pager.php');
      $params['selected_page'] = (!isset($_GET['page']) || (int) $_GET['page'] == 0) ? 1 : $_GET['page'];
      $params['total_items'] = $total_items;
      $params['items_per_page'] = $system['max_results'] * 2;
      /* prepare pager URL */
      if ($thread !== null) {
        $params['url'] = $system['system_url'] . '/forums/thread/' . $thread['thread_id'] . '/' . $thread['title_url'] . '?page=%s';
      } elseif ($user_id !== null) {
        $params['url'] = $system['system_url'] . '/forums/my-replies?page=%s';
      } elseif ($query !== null) {
        $params['url'] = remove_querystring_var($_SERVER['REQUEST_URI'], 'page');
        $params['url'] = $params['url'] . '&page=%s';
      }
      $pager = new Pager($params);
      $limit_query = $pager->getLimitSql();
      if ($thread !== null) {
        $get_replies = $db->query(sprintf("SELECT forums_replies.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id WHERE forums_replies.thread_id = %s ORDER BY forums_replies.reply_id ASC " . $limit_query, secure($thread['thread_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      } elseif ($user_id !== null) {
        $get_replies = $db->query(sprintf("SELECT forums_replies.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_replies INNER JOIN users ON forums_replies.user_id = users.user_id WHERE forums_replies.user_id = %s ORDER BY forums_replies.reply_id DESC " . $limit_query, secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      } elseif ($query !== null) {
        $get_replies = $db->query("SELECT forums_replies.*, users.user_group, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_registered FROM forums_replies INNER JOIN forums_threads ON forums_replies.thread_id = forums_threads.thread_id INNER JOIN users ON forums_replies.user_id = users.user_id " . $where . " ORDER BY forums_replies.reply_id DESC " . $limit_query) or _error('SQL_ERROR_THROWEN');
      }
      while ($reply = $get_replies->fetch_assoc()) {
        $reply['user_picture'] = get_picture($reply['user_picture'], $reply['user_gender']);
        $reply['user_fullname'] = ($system['show_usernames_enabled']) ? $reply['user_name'] : $reply['user_firstname'] . " " . $reply['user_lastname'];
        /* parse text */
        $reply['parsed_text'] = htmlspecialchars_decode($reply['text'], ENT_QUOTES);
        $reply['text_snippet'] = get_snippet_text($reply['text']);
        /* check if viewer can manage reply [Edit|Delete] */
        $reply['manage_reply'] = false;
        if ($this->_logged_in) {
          /* viewer is (admin|moderator) */
          if ($this->_data['user_group'] < 3) {
            $reply['manage_reply'] = true;
          }
          /* viewer is the author of reply */
          if ($this->_data['user_id'] == $reply['user_id']) {
            $reply['manage_reply'] = true;
          }
        }
        /* get thread */
        if (!$thread) {
          $reply['thread'] = $this->get_forum_thread($reply['thread_id']);
        }
        $replies[] = $reply;
      }
      /* assign variables */
      $smarty->assign('selected_page', $params['selected_page']);
      $smarty->assign('total', $params['total_items']);
      $smarty->assign('pager', $pager->getPager());
    }
    return $replies;
  }


  /**
   * get_forum_reply
   * 
   * @param integer $reply_id
   * @return array
   */
  public function get_forum_reply($reply_id)
  {
    global $db;
    $get_reply = $db->query(sprintf("SELECT * FROM forums_replies WHERE reply_id = %s", secure($reply_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_reply->num_rows == 0) {
      return false;
    }
    $reply = $get_reply->fetch_assoc();
    /* get thread */
    $reply['thread'] = $this->get_forum_thread($reply['thread_id']);
    if (!$reply['thread']) {
      return false;
    }
    /* check if viewer can manage reply [Edit|Delete] */
    $reply['manage_reply'] = false;
    if ($this->_logged_in) {
      /* viewer is (admin|moderator) */
      if ($this->_data['user_group'] < 3) {
        $reply['manage_reply'] = true;
      }
      /* viewer is the author of reply */
      if ($this->_data['user_id'] == $reply['user_id']) {
        $reply['manage_reply'] = true;
      }
    }
    return $reply;
  }


  /**
   * post_forum_reply
   * 
   * @param string $thread_id
   * @param string $text
   * @return array
   */
  public function post_forum_reply($thread_id, $text)
  {
    global $db, $system, $date;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get thread */
    $thread = $this->get_forum_thread($thread_id);
    /* check forum */
    if (!$thread) {
      _error(403);
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your reply"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.SafeIframe', true);
      $config->set('URI.SafeIframeRegexp', '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/)%');
      $purifier = new HTMLPurifier($config);
      $clean_text = $purifier->purify($text);
    }
    /* insert reply */
    $db->query(sprintf("INSERT INTO forums_replies (thread_id, user_id, text, time) VALUES (%s, %s, %s, %s)", secure($thread_id, 'int'), secure($this->_data['user_id'], 'int'), secure($clean_text), secure($date))) or _error('SQL_ERROR_THROWEN');
    $reply_id = $db->insert_id;
    /* update thread */
    $db->query(sprintf("UPDATE forums_threads SET last_reply = %s, replies = replies + 1 WHERE thread_id = %s", secure($date), secure($thread_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_replies = forum_replies + 1 WHERE forum_id = %s", secure($thread['forum']['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post notification */
    $this->post_notification(['to_user_id' => $thread['user_id'], 'action' => 'forum_reply', 'node_url' => $thread['thread_id'] . '/' . $thread['title_url'] . '/#reply-' . $reply_id]);
    return ['reply_id' => $reply_id, 'thread' => $thread];
  }


  /**
   * edit_forum_reply
   * 
   * @param string $reply_id
   * @param string $text
   * @return array
   */
  public function edit_forum_reply($reply_id, $text)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get reply */
    $reply = $this->get_forum_reply($reply_id);
    /* check reply */
    if (!$reply) {
      _error(403);
    }
    /* check permission */
    if (!$reply['manage_reply']) {
      _error(403);
    }
    /* validate text */
    if (is_empty($text)) {
      throw new Exception(__("You must enter some text for your thread"));
    }
    if ($system['html_richtext_enabled']) {
      $clean_text = $text;
    } else {
      /* HTMLPurifier */
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.SafeIframe', true);
      $config->set('URI.SafeIframeRegexp', '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/)%');
      $purifier = new HTMLPurifier($config);
      $clean_text = $purifier->purify($text);
    }
    /* edit reply */
    $db->query(sprintf("UPDATE forums_replies SET text = %s WHERE reply_id = %s", secure($clean_text), secure($reply_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return ['reply_id' => $reply_id, 'thread' => $reply['thread']];
  }


  /**
   * delete_forum_reply
   * 
   * @param string $reply_id
   * @return void
   */
  public function delete_forum_reply($reply_id)
  {
    global $db, $system;
    /* check if forums enabled */
    if (!$system['forums_enabled']) {
      throw new Exception(__("The forums module has been disabled by the admin"));
    }
    /* check forums permission */
    if (!$this->_data['can_use_forums']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* get reply */
    $reply = $this->get_forum_reply($reply_id);
    /* check reply */
    if (!$reply) {
      _error(403);
    }
    /* check permission */
    if (!$reply['manage_reply']) {
      _error(403);
    }
    /* delete reply */
    $db->query(sprintf("DELETE FROM  forums_replies WHERE reply_id = %s", secure($reply_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update thread */
    $db->query(sprintf("UPDATE forums_threads SET replies = IF(replies=0,0,replies-1) WHERE thread_id = %s", secure($reply['thread_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update forum */
    $db->query(sprintf("UPDATE forums SET forum_replies = IF(forum_replies=0,0,forum_replies-1) WHERE forum_id = %s", secure($reply['thread']['forum']['forum_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete notification */
    $this->delete_notification($reply['thread']['user_id'], 'forum_reply', '', $reply['thread']['thread_id'] . '/' . $reply['thread']['title_url'] . '/#reply-' . $reply_id);
  }


  /**
   * search_forums
   * 
   * @param string $query
   * @param string $type
   * @param mixed $forum
   * @param boolean $recursive
   * @return array
   */
  public function search_forums($query, $type, $forum, $recursive)
  {
    global $db, $system;
    /* init vars */
    $results = [];
    $params = [];
    /* check recursive */
    $recursive = (isset($recursive)) ? true : false;
    /* check forum */
    if ($forum == "all") {
      $params['forums_list'] = false;
    } else {
      $forum = $this->get_forum($forum);
      if (!$forum) {
        return $results;
      }
      $params['forums_list'][] = $forum['forum_id'];
      if ($recursive) {
        $this->get_child_forums_ids($forum['childs'], $params['forums_list']);
      }
    }
    /* include query */
    $params['query'] = $query;
    /* validate type */
    switch ($type) {
      case 'threads':
        $results = $this->get_forum_threads($params);
        break;

      case 'replies':
        $results = $this->get_forum_replies($params);
        break;
    }
    return $results;
  }


  /**
   * get_forum_online_users
   * 
   * @return array
   */
  public function get_forum_online_users()
  {
    global $db, $system;
    $users = [];
    $get_users = $db->query(sprintf("SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users WHERE user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(%s)) AND user_chat_enabled = '1'", secure($system['offline_time'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_users->num_rows > 0) {
      while ($user = $get_users->fetch_assoc()) {
        $user['user_picture'] = get_picture($user['user_picture'], $user['user_gender']);
        $users[] = $user;
      }
    }
    return $users;
  }



  /* ------------------------------- */
  /* Movies */
  /* ------------------------------- */

  /**
   * get_movies_genres
   * 
   * @param boolean $sorted
   * @return array
   */
  public function get_movies_genres($sorted = true)
  {
    global $db;
    $genres = [];
    $order_query = ($sorted) ? " ORDER BY genre_order ASC " : " ORDER BY genre_id ASC ";
    $get_genres = $db->query("SELECT * FROM movies_genres" . $order_query) or _error('SQL_ERROR_THROWEN');
    if ($get_genres->num_rows > 0) {
      while ($genre = $get_genres->fetch_assoc()) {
        $genre['genre_url'] = get_url_text($genre['genre_name']);
        $genres[] = $genre;
      }
    }
    return $genres;
  }

  /**
   * get_movies_genre
   * 
   * @param integer $genre_id
   * @return array
   */
  public function get_movies_genre($genre_id)
  {
    global $db;
    $get_genre = $db->query(sprintf('SELECT * FROM movies_genres WHERE genre_id = %s', secure($genre_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_genre->num_rows == 0) {
      return false;
    }
    $genre = $get_genre->fetch_assoc();
    $genre['genre_url'] = get_url_text($genre['genre_name']);
    return $genre;
  }


  /**
   * get_movie_genres
   * 
   * @param string $movie_genres
   * @return array
   */
  public function get_movie_genres($movie_genres)
  {
    global $db;
    $genres = [];
    if ($movie_genres) {
      $get_genres = $db->query("SELECT * FROM movies_genres WHERE genre_id IN (" . $movie_genres . ")") or _error('SQL_ERROR_THROWEN');
      if ($get_genres->num_rows > 0) {
        while ($genre = $get_genres->fetch_assoc()) {
          $genre['genre_url'] = get_url_text($genre['genre_name']);
          $genres[] = $genre;
        }
      }
    }
    return $genres;
  }


  /**
   * get_movie
   * 
   * @param integer $movie_id
   * @return array
   */
  public function get_movie($movie_id)
  {
    global $db;
    $get_movie = $db->query(sprintf('SELECT * FROM movies WHERE movie_id = %s', secure($movie_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_movie->num_rows == 0) {
      return false;
    }
    $movie = $get_movie->fetch_assoc();
    $movie['poster'] = get_picture($movie['poster'], 'movie');
    $movie['movie_url'] = get_url_text($movie['title']);
    $movie['genres_list'] = $this->get_movie_genres($movie['genres']);
    $movie['can_watch'] = true;
    /* check if is paid */
    if ($movie['is_paid']) {
      $movie['can_watch'] = false;
      if ($this->_logged_in) {
        if ($this->_data['user_group'] < 3) {
          $movie['can_watch'] = true;
        } else {
          $get_payment = $db->query(sprintf('SELECT COUNT(*) AS count FROM movies_payments WHERE movie_id = %s AND user_id = %s AND DATE_ADD(payment_time, INTERVAL %s DAY) > NOW()', secure($movie_id, 'int'), secure($this->_data['user_id'], 'int'), secure($movie['available_for'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if ($get_payment->fetch_assoc()['count'] > 0) {
            $movie['can_watch'] = true;
          }
        }
      }
    }
    /* update views */
    if ($movie['can_watch']) {
      $db->query(sprintf("UPDATE movies SET views = views + 1 WHERE movie_id = %s", secure($movie_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    return $movie;
  }


  /**
   * movie_payment
   * 
   * @param integer $movie_id
   * @param integer $user_id
   * @return string
   */
  public function movie_payment($movie_id, $user_id = null)
  {
    global $system, $db, $date;
    /* check user */
    $user_id = ($user_id) ? $user_id : $this->_data['user_id'];
    /* get movie */
    $movie = $this->get_movie($movie_id);
    /* remove any previous payment */
    $db->query(sprintf("DELETE FROM movies_payments WHERE movie_id = %s AND user_id = %s", secure($movie_id, 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* add movie payment */
    $db->query(sprintf("INSERT INTO movies_payments (movie_id, user_id, payment_time) VALUES (%s, %s,  %s)", secure($movie_id, 'int'), secure($user_id, 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* return movie link */
    return '/movie/' . $movie['movie_id'] . '/' . $movie['movie_url'];
  }



  /* ------------------------------- */
  /* Games */
  /* ------------------------------- */

  /**
   * get_games_genres
   * 
   * @param boolean $sorted
   * @return array
   */
  public function get_games_genres($sorted = true)
  {
    global $db;
    $genres = [];
    $order_query = ($sorted) ? " ORDER BY genre_order ASC " : " ORDER BY genre_id ASC ";
    $get_genres = $db->query("SELECT * FROM games_genres" . $order_query) or _error('SQL_ERROR_THROWEN');
    if ($get_genres->num_rows > 0) {
      while ($genre = $get_genres->fetch_assoc()) {
        $genre['genre_url'] = get_url_text($genre['genre_name']);
        $genres[] = $genre;
      }
    }
    return $genres;
  }

  /**
   * get_games_genre
   * 
   * @param integer $genre_id
   * @return array
   */
  public function get_games_genre($genre_id)
  {
    global $db;
    $get_genre = $db->query(sprintf('SELECT * FROM games_genres WHERE genre_id = %s', secure($genre_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_genre->num_rows == 0) {
      return false;
    }
    $genre = $get_genre->fetch_assoc();
    $genre['genre_url'] = get_url_text($genre['genre_name']);
    return $genre;
  }


  /**
   * get_game_genres
   * 
   * @param string $game_genres
   * @return array
   */
  public function get_game_genres($game_genres)
  {
    global $db;
    $genres = [];
    if ($game_genres) {
      $get_genres = $db->query("SELECT * FROM games_genres WHERE genre_id IN (" . $game_genres . ")") or _error('SQL_ERROR_THROWEN');
      if ($get_genres->num_rows > 0) {
        while ($genre = $get_genres->fetch_assoc()) {
          $genre['genre_url'] = get_url_text($genre['genre_name']);
          $genres[] = $genre;
        }
      }
    }
    return $genres;
  }


  /**
   * get_games
   * 
   * @param integer $offset
   * @param boolean $played
   * @param integer $genre_id
   * @return array
   */
  public function get_games($offset = 0, $played = false, $genre_id = null)
  {
    global $db, $system;
    $games = [];
    $offset *= $system['games_results'];
    if ($played) {
      $get_games = $db->query(sprintf('SELECT games.*, games_players.last_played_time FROM games INNER JOIN games_players ON games.game_id = games_players.game_id WHERE games_players.user_id = %s ORDER BY games_players.last_played_time DESC LIMIT %s, %s', secure($this->_data['user_id'], 'int'), secure($offset, 'int', false), secure($system['games_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    } else {
      $genre_statment = ($genre_id) ? sprintf("WHERE find_in_set(%s, genres) > 0", secure($genre_id, 'int')) : '';
      $get_games = $db->query(sprintf('SELECT * FROM games %s ORDER BY title ASC LIMIT %s, %s', $genre_statment, secure($offset, 'int', false), secure($system['games_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_games->num_rows > 0) {
      while ($game = $get_games->fetch_assoc()) {
        $game['thumbnail'] = get_picture($game['thumbnail'], 'game');
        $game['title_url'] = get_url_text($game['title']);
        $game['played'] = ($played) ? $game['last_played_time'] : false;
        $game['genres_list'] = $this->get_game_genres($game['genres']);
        $games[] = $game;
      }
    }
    return $games;
  }


  /**
   * get_game
   * 
   * @param integer $game_id
   * @return array
   */
  public function get_game($game_id)
  {
    global $db, $date;
    $get_game = $db->query(sprintf('SELECT * FROM games WHERE game_id = %s', secure($game_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_game->num_rows == 0) {
      return false;
    }
    $game = $get_game->fetch_assoc();
    $game['thumbnail'] = get_picture($game['thumbnail'], 'game');
    $game['genres_list'] = $this->get_game_genres($game['genres']);
    /* check if the viewer played this game before */
    if ($this->_logged_in) {
      $db->query(sprintf("DELETE FROM games_players WHERE game_id = %s AND user_id = %s", secure($game['game_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      $db->query(sprintf("INSERT INTO games_players (game_id, user_id, last_played_time) VALUES (%s, %s, %s)", secure($game['game_id'], 'int'), secure($this->_data['user_id'], 'int'), secure($date)));
    }
    return $game;
  }



  /* ------------------------------- */
  /* Ads */
  /* ------------------------------- */

  /**
   * ads
   * 
   * @param string $place
   * @param integer $node_id
   * @return array
   */
  public function ads($place, $node_id = null)
  {
    global $db;
    $ads = [];
    /* check if the viewer is ads free */
    if ($this->_logged_in && $this->_data['ads_free']) {
      return $ads;
    }
    /* check if ads place is pages or grpups */
    $where_query = '';
    if ($node_id) {
      $node_id = secure($node_id, 'search');
      $node_id = str_replace("'%", "'%;", $node_id);
      $node_id = str_replace("%'", "&%'", $node_id);
    }
    if ($place == 'pages') {
      $where_query = sprintf('AND ads_pages_ids LIKE %s', $node_id);
    } elseif ($place == 'groups') {
      $where_query = sprintf('AND ads_groups_ids LIKE %s', $node_id);
    }
    $get_ads = $db->query(sprintf("SELECT * FROM ads_system WHERE place = %s ", secure($place)) . $where_query) or _error('SQL_ERROR_THROWEN');
    if ($get_ads->num_rows > 0) {
      while ($ads_unit = $get_ads->fetch_assoc()) {
        $ads_unit['code'] = html_entity_decode($ads_unit['code'], ENT_QUOTES);
        $ads[] = $ads_unit;
      }
    }
    return $ads;
  }


  /**
   * ads_campaigns
   * 
   * @param string $place
   * @return array
   */
  public function ads_campaigns($place = 'sidebar')
  {
    global $db, $system, $date;
    $campaigns = [];
    /* check if ads enabled */
    if (!$system['ads_enabled']) {
      return $campaigns;
    }
    /* check if the viewer is ads free */
    if ($this->_logged_in && $this->_data['ads_free']) {
      return $campaigns;
    }
    /* get active campaigns */
    $get_campaigns = $db->query(sprintf("SELECT ads_campaigns.*, pages.page_name, `groups`.group_name, users.user_wallet_balance FROM ads_campaigns INNER JOIN users ON ads_campaigns.campaign_user_id = users.user_id LEFT JOIN pages ON ads_campaigns.ads_type = 'page' AND ads_campaigns.ads_page = pages.page_id LEFT JOIN `groups` ON ads_campaigns.ads_type = 'group' AND ads_campaigns.ads_group = `groups`.group_id WHERE ads_campaigns.campaign_is_approved = '1' AND ads_campaigns.campaign_is_active = '1' AND ads_campaigns.ads_placement = %s ORDER BY RAND()", secure($place))) or _error('SQL_ERROR_THROWEN');
    if ($get_campaigns->num_rows > 0) {
      while ($campaign = $get_campaigns->fetch_assoc()) {
        // check if viewer get 1 valid campaign
        if (count($campaigns) >= 1) {
          break;
        }

        // update campaign
        /* [1] -> campaign expired */
        if (strtotime($campaign['campaign_end_date']) <= strtotime($date)) {
          $db->query(sprintf("UPDATE ads_campaigns SET campaign_is_active = '0' WHERE campaign_id = %s", secure($campaign['campaign_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          continue;
        }
        $remaining = $campaign['campaign_budget'] - $campaign['campaign_spend']; // campaign remaining (budget - spend)
        /* [2] -> campaign remaining = 0 (spend == budget) */
        if ($remaining == 0) {
          $db->query(sprintf("UPDATE ads_campaigns SET campaign_is_active = '0' WHERE campaign_id = %s", secure($campaign['campaign_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          continue;
        }
        /* [3] -> campaign remaining > campaign's author wallet credit */
        if ($remaining > $campaign['user_wallet_balance']) {
          $db->query(sprintf("UPDATE ads_campaigns SET campaign_is_active = '0' WHERE campaign_id = %s", secure($campaign['campaign_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          continue;
        }
        /* [4] -> campaign remaining < cost per click */
        if ($remaining < $system['ads_cost_click'] && $campaign['campaign_bidding'] == "click") {
          $db->query(sprintf("UPDATE ads_campaigns SET campaign_is_active = '0' WHERE campaign_id = %s", secure($campaign['campaign_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          continue;
        }
        /* [5] -> campaign remaining < cost per view */
        if ($remaining < $system['ads_cost_view'] && $campaign['campaign_bidding'] == "view") {
          $db->query(sprintf("UPDATE ads_campaigns SET campaign_is_active = '0' WHERE campaign_id = %s", secure($campaign['campaign_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          continue;
        }

        // check if "viewer" is campaign target audience (if logged in)
        /* if viewer is campaign author */
        if ($this->_logged_in) {
          if ($this->_data['user_id'] == $campaign['campaign_user_id']) {
            continue;
          }
          /* check viewer country */
          $campaign['audience_countries'] = ($campaign['audience_countries']) ? explode(',', $campaign['audience_countries']) : [];
          if ($campaign['audience_countries'] && !in_array($this->_data['user_country'], $campaign['audience_countries'])) {
            continue;
          }
          /* check viewer gender */
          if ($campaign['audience_gender'] != "all" && $this->_data['user_gender'] != $campaign['audience_gender']) {
            continue;
          }
          /* check viewer relationship */
          if ($campaign['audience_relationship'] != "all" && $this->_data['user_relationship'] != $campaign['audience_relationship']) {
            continue;
          }
        }

        // prepare ads URL
        switch ($campaign['ads_type']) {
          case 'page':
            $campaign['ads_url'] = $system['system_url'] . '/pages/' . $campaign['page_name'];
            break;

          case 'group':
            $campaign['ads_url'] = $system['system_url'] . '/groups/' . $campaign['group_name'];
            break;

          case 'event':
            $campaign['ads_url'] = $system['system_url'] . '/events/' . $campaign['ads_event'];
            break;
        }

        // update campaign if bidding = view
        if ($campaign['campaign_bidding'] == "view") {
          /* update campaign spend & views */
          $db->query(sprintf("UPDATE ads_campaigns SET campaign_views = campaign_views + 1, campaign_spend = campaign_spend + %s WHERE campaign_id = %s", secure($system['ads_cost_view']), secure($campaign['campaign_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* decrease campaign author wallet balance */
          $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($system['ads_cost_view']), secure($campaign['campaign_user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }

        // get campaigns
        $campaigns[] = $campaign;
      }
    }
    return $campaigns;
  }


  /**
   * get_campaigns
   * 
   * @return array
   */
  public function get_campaigns()
  {
    global $db;
    $campaigns = [];
    $get_campaigns = $db->query(sprintf("SELECT * FROM ads_campaigns WHERE campaign_user_id = %s ORDER BY campaign_id DESC", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_campaigns->num_rows > 0) {
      while ($campaign = $get_campaigns->fetch_assoc()) {
        $campaigns[] = $campaign;
      }
    }
    return $campaigns;
  }


  /**
   * get_campaign
   * 
   * @param integer $campaign_id
   * @return array
   */
  public function get_campaign($campaign_id)
  {
    global $db;
    $get_campaign = $db->query(sprintf("SELECT * FROM ads_campaigns WHERE campaign_id = %s", secure($campaign_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_campaign->num_rows == 0) {
      return false;
    }
    $campaign = $get_campaign->fetch_assoc();
    /* get audience countries array */
    $campaign['audience_countries'] = ($campaign['audience_countries']) ? explode(',', $campaign['audience_countries']) : [];
    /* get campaign potential reach */
    $campaign['campaign_potential_reach'] = $this->campaign_potential_reach($campaign['audience_countries'], $campaign['audience_gender'], $campaign['audience_relationship']);
    return $campaign;
  }


  /**
   * create_campaign
   * 
   * @param array $args
   * @return void
   */
  public function create_campaign($args = [])
  {
    global $db, $system, $date;
    /* check if ads enabled */
    if (!$system['ads_enabled']) {
      throw new Exception(__("The ads system has been disabled by the admin"));
    }
    /* check ads permission */
    if (!$this->_data['can_create_ads']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* validate campaign title */
    if (is_empty($args['campaign_title'])) {
      throw new Exception(__("You have to enter the campaign title"));
    }
    /* validate campaign start & end dates (UTC) */
    if (is_empty($args['campaign_start_date'])) {
      throw new Exception(__("You have to enter the campaign start date"));
    }
    if (is_empty($args['campaign_end_date'])) {
      throw new Exception(__("You have to enter the campaign end date"));
    }
    if (strtotime(set_datetime($args['campaign_start_date'])) >= strtotime(set_datetime($args['campaign_end_date']))) {
      throw new Exception(__("Campaign end date must be after the start date"));
    }
    if (strtotime(set_datetime($args['campaign_end_date'])) <= strtotime($date)) {
      throw new Exception(__("Campaign end date must be after today datetime"));
    }
    /* validate campaign budget */
    if (is_empty($args['campaign_budget']) || !is_numeric($args['campaign_budget'])) {
      throw new Exception(__("You must enter valid budget"));
    }
    if ($args['campaign_budget'] < max($system['ads_cost_click'], $system['ads_cost_view'])) {
      throw new Exception(__("The minimum budget must be") . " " . print_money(max($system['ads_cost_click'], $system['ads_cost_view'])) . " ");
    }
    if ($this->_data['user_wallet_balance'] < $args['campaign_budget']) {
      throw new Exception(__("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* validate campaign bidding */
    if (!in_array($args['campaign_bidding'], ['click', 'view'])) {
      throw new Exception(__("You have to select a valid bidding"));
    }
    /* validate audience countries */
    $args['audience_countries'] = (isset($args['audience_countries']) && is_array($args['audience_countries'])) ? $this->spread_ids($args['audience_countries']) : "";
    /* validate audience gender */
    if ($args['audience_gender'] != "all" && !$this->check_gender($args['audience_gender'])) {
      throw new Exception(__("You have to select a valid gender"));
    }
    /* validate audience relationship */
    if (!isset($args['audience_relationship'])) {
      $args['audience_relationship'] = "all";
    } else {
      if (!in_array($args['audience_relationship'], ['all', 'single', 'relationship', 'married', "complicated", 'separated', 'divorced', 'widowed'])) {
        throw new Exception(__("You have to select a valid relationship"));
      }
    }
    /* validate ads type */
    switch ($args['ads_type']) {
      case 'url':
        if (is_empty($args['ads_url']) || !valid_url($args['ads_url'])) {
          throw new Exception(__("You have to enter a valid URL for your ads"));
        }
        $args['ads_page'] = 'null';
        $args['ads_group'] = 'null';
        $args['ads_event'] = 'null';
        break;

      case 'page':
        if ($args['ads_page'] == "none") {
          throw new Exception(__("You have to select one for your pages for your ads"));
        }
        $args['ads_url'] = 'null';
        $args['ads_group'] = 'null';
        $args['ads_event'] = 'null';
        break;

      case 'group':
        if ($args['ads_group'] == "none") {
          throw new Exception(__("You have to select one for your groups for your ads"));
        }
        $args['ads_url'] = 'null';
        $args['ads_page'] = 'null';
        $args['ads_event'] = 'null';
        break;

      case 'event':
        if ($args['ads_event'] == "none") {
          throw new Exception(__("You have to select one for your events for your ads"));
        }
        $args['ads_url'] = 'null';
        $args['ads_page'] = 'null';
        $args['ads_group'] = 'null';
        break;

      default:
        throw new Exception(__("You have to select a valid ads type"));
        break;
    }
    /* validate ads placement */
    if (!in_array($args['ads_placement'], ['newsfeed', 'sidebar'])) {
      throw new Exception(__("You have to select a valid ads placement"));
    }
    /* validate ads image */
    if (is_empty($args['ads_image'])) {
      throw new Exception(__("You have to upload an image for your ads"));
    }
    /* approval system */
    $campaign_is_approved = ($system['ads_approval_enabled']) ? '0' : '1';
    /* insert new campain */
    $db->query(sprintf(
      "INSERT INTO ads_campaigns (
            campaign_user_id, 
            campaign_title, 
            campaign_start_date, 
            campaign_end_date, 
            campaign_budget, 
            campaign_bidding, 
            audience_countries, 
            audience_gender, 
            audience_relationship, 
            ads_title, 
            ads_description, 
            ads_type, 
            ads_url, 
            ads_page, 
            ads_group, 
            ads_event, 
            ads_placement, 
            ads_image, 
            campaign_created_date, 
            campaign_is_approved) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
      secure($this->_data['user_id'], 'int'),
      secure($args['campaign_title']),
      secure($args['campaign_start_date'], 'datetime'),
      secure($args['campaign_end_date'], 'datetime'),
      secure($args['campaign_budget']),
      secure($args['campaign_bidding']),
      secure($args['audience_countries']),
      secure($args['audience_gender']),
      secure($args['audience_relationship']),
      secure($args['ads_title']),
      secure($args['ads_description']),
      secure($args['ads_type']),
      secure($args['ads_url']),
      secure($args['ads_page']),
      secure($args['ads_group']),
      secure($args['ads_event']),
      secure($args['ads_placement']),
      secure($args['ads_image']),
      secure($date),
      secure($campaign_is_approved)
    )) or _error('SQL_ERROR_THROWEN');
    // send notification to admins
    if ($system['ads_approval_enabled']) {
      $this->notify_system_admins("ads_campaign_added");
    }
  }


  /**
   * edit_campaign
   * 
   * @param integer $campaign_id
   * @param array $args
   * @return void
   */
  public function edit_campaign($campaign_id, $args)
  {
    global $db, $system;
    /* check if ads enabled */
    if ($this->_data['user_group'] >= 3 && !$system['ads_enabled']) {
      throw new Exception(__("The ads system has been disabled by the admin"));
    }
    /* (check&get) campaign */
    $get_campaign = $db->query(sprintf("SELECT ads_campaigns.*, users.user_wallet_balance FROM ads_campaigns INNER JOIN users ON ads_campaigns.campaign_user_id = users.user_id WHERE ads_campaigns.campaign_id = %s", secure($campaign_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_campaign->num_rows == 0) {
      _error(403);
    }
    $campaign = $get_campaign->fetch_assoc();
    /* check permission */
    $can_edit = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_edit = true;
      /* viewer is the creator of campaign */
    } elseif ($this->_data['user_id'] == $campaign['campaign_user_id']) {
      $can_edit = true;
    }
    /* edit the campaign */
    if (!$can_edit) {
      _error(403);
    }
    /* validate campaign title */
    if (is_empty($args['campaign_title'])) {
      throw new Exception(__("You have to enter the campaign title"));
    }
    /* validate campaign start & end dates */
    if (is_empty($args['campaign_start_date'])) {
      throw new Exception(__("You have to enter the campaign start date"));
    }
    if (is_empty($args['campaign_end_date'])) {
      throw new Exception(__("You have to enter the campaign end date"));
    }
    if (strtotime(set_datetime($args['campaign_start_date'])) > strtotime(set_datetime($args['campaign_end_date']))) {
      throw new Exception(__("Campaign end date must be after the start date"));
    }
    if (strtotime(set_datetime($args['campaign_end_date'])) <= strtotime($date)) {
      throw new Exception(__("Campaign end date must be after today datetime"));
    }
    /* validate campaign budget */
    if (is_empty($args['campaign_budget']) || !is_numeric($args['campaign_budget'])) {
      throw new Exception(__("You must enter valid budget"));
    }
    $remaining = $args['campaign_budget'] - $campaign['campaign_spend']; // campaign remaining (budget(new) - spend)
    if ($remaining == 0) {
      throw new Exception(__("The campaign total spend reached the campaign budget already, increase the campaign budget to resume the campaign"));
    }
    if ($remaining > $campaign['user_wallet_balance']) {
      throw new Exception(__("The remaining spend is larger than current wallet credit") . " " . print_money($campaign['user_wallet_balance']) . " " . __("You need to") . " " . __("Replenish wallet credit"));
    }
    /* validate campaign bidding */
    if (!in_array($args['campaign_bidding'], ['click', 'view'])) {
      throw new Exception(__("You have to select a valid bidding"));
    }
    if ($args['campaign_bidding'] == "click") {
      if ($remaining < $system['ads_cost_click']) {
        throw new Exception(__("The cost per click is larger than your campaign remaining spend") . " " . print_money($remaining) . " " . __("increase the campaign budget to resume the campaign"));
      }
    }
    if ($args['campaign_bidding'] == "view") {
      if ($remaining < $system['ads_cost_view']) {
        throw new Exception(__("The cost per view is larger than your campaign remaining spend") . " " . print_money($remaining) . " " . __("increase the campaign budget to resume the campaign"));
      }
    }
    /* validate audience countries */
    $args['audience_countries'] = (isset($args['audience_countries']) && is_array($args['audience_countries'])) ? $this->spread_ids($args['audience_countries']) : "";
    /* validate audience gender */
    if ($args['audience_gender'] != "all" && !$this->check_gender($args['audience_gender'])) {
      throw new Exception(__("You have to select a valid gender"));
    }
    /* validate audience relationship */
    if (!isset($args['audience_relationship'])) {
      $args['audience_relationship'] = "all";
    } else {
      if (!in_array($args['audience_relationship'], ['all', 'single', 'relationship', 'married', "complicated", 'separated', 'divorced', 'widowed'])) {
        throw new Exception(__("You have to select a valid relationship"));
      }
    }
    /* validate ads type */
    switch ($args['ads_type']) {
      case 'url':
        if (is_empty($args['ads_url']) || !valid_url($args['ads_url'])) {
          throw new Exception(__("You have to enter a valid URL for your ads"));
        }
        $args['ads_page'] = 'null';
        $args['ads_group'] = 'null';
        $args['ads_event'] = 'null';
        break;

      case 'page':
        if ($args['ads_page'] == "none") {
          throw new Exception(__("You have to select one for your pages for your ads"));
        }
        $args['ads_url'] = 'null';
        $args['ads_group'] = 'null';
        $args['ads_event'] = 'null';
        break;

      case 'group':
        if ($args['ads_group'] == "none") {
          throw new Exception(__("You have to select one for your groups for your ads"));
        }
        $args['ads_url'] = 'null';
        $args['ads_page'] = 'null';
        $args['ads_event'] = 'null';
        break;

      case 'event':
        if ($args['ads_event'] == "none") {
          throw new Exception(__("You have to select one for your events for your ads"));
        }
        $args['ads_url'] = 'null';
        $args['ads_page'] = 'null';
        $args['ads_group'] = 'null';
        break;

      default:
        throw new Exception(__("You have to select a valid ads type"));
        break;
    }
    /* validate ads placement */
    if (!in_array($args['ads_placement'], ['newsfeed', 'sidebar'])) {
      throw new Exception(__("You have to select a valid ads placement"));
    }
    /* validate ads image */
    if (is_empty($args['ads_image'])) {
      throw new Exception(__("You have to upload an image for your ads"));
    }
    /* approval system */
    $campaign_is_approved = ($system['ads_approval_enabled']) ? '0' : '1';
    /* update the campain */
    $db->query(sprintf(
      "UPDATE ads_campaigns SET 
            campaign_title = %s, 
            campaign_start_date = %s, 
            campaign_end_date = %s, 
            campaign_budget = %s, 
            campaign_bidding = %s, 
            audience_countries = %s, 
            audience_gender = %s, 
            audience_relationship = %s, 
            ads_title = %s, 
            ads_description = %s, 
            ads_type = %s, 
            ads_url = %s, 
            ads_page = %s, 
            ads_group = %s, 
            ads_event = %s, 
            ads_placement = %s, 
            ads_image = %s, 
            campaign_is_active = '1', 
            campaign_is_approved = %s WHERE campaign_id = %s",
      secure($args['campaign_title']),
      secure($args['campaign_start_date'], 'datetime'),
      secure($args['campaign_end_date'], 'datetime'),
      secure($args['campaign_budget']),
      secure($args['campaign_bidding']),
      secure($args['audience_countries']),
      secure($args['audience_gender']),
      secure($args['audience_relationship']),
      secure($args['ads_title']),
      secure($args['ads_description']),
      secure($args['ads_type']),
      secure($args['ads_url']),
      secure($args['ads_page']),
      secure($args['ads_group']),
      secure($args['ads_event']),
      secure($args['ads_placement']),
      secure($args['ads_image']),
      secure($campaign_is_approved),
      secure($campaign_id, 'int')
    )) or _error('SQL_ERROR_THROWEN');
    // send notification to admins
    if ($system['ads_approval_enabled']) {
      $this->notify_system_admins("ads_campaign_edited");
    }
  }


  /**
   * delete_campaign
   * 
   * @param integer $campaign_id
   * @return void
   */
  public function delete_campaign($campaign_id)
  {
    global $db, $system;
    /* check if ads enabled */
    if ($this->_data['user_group'] >= 3 && !$system['ads_enabled']) {
      throw new Exception(__("The ads system has been disabled by the admin"));
    }
    /* (check&get) campaign */
    $get_campaign = $db->query(sprintf("SELECT * FROM ads_campaigns WHERE campaign_id = %s", secure($campaign_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_campaign->num_rows == 0) {
      _error(403);
    }
    $campaign = $get_campaign->fetch_assoc();
    // delete campaign
    $can_delete = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_delete = true;
      /* viewer is the creator of campaign */
    } elseif ($this->_data['user_id'] == $campaign['campaign_user_id']) {
      $can_delete = true;
    }
    /* delete the campaign */
    if (!$can_delete) {
      _error(403);
    }
    $db->query(sprintf("DELETE FROM ads_campaigns WHERE campaign_id = %s", secure($campaign_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * update_campaign_status
   * 
   * @param integer $campaign_id
   * @param boolean $is_active
   * @return void
   */
  public function update_campaign_status($campaign_id, $is_active)
  {
    global $db, $system, $date;
    /* check if ads enabled */
    if ($this->_data['user_group'] >= 3 && !$system['ads_enabled']) {
      throw new Exception(__("The ads system has been disabled by the admin"));
    }
    /* (check&get) campaign */
    $get_campaign = $db->query(sprintf("SELECT ads_campaigns.*, users.user_wallet_balance FROM ads_campaigns INNER JOIN users ON ads_campaigns.campaign_user_id = users.user_id WHERE ads_campaigns.campaign_id = %s", secure($campaign_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_campaign->num_rows == 0) {
      _error(403);
    }
    $campaign = $get_campaign->fetch_assoc();
    // change campaign status
    $can_edit = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_edit = true;
      /* viewer is the creator of campaign */
    } elseif ($this->_data['user_id'] == $campaign['campaign_user_id']) {
      $can_edit = true;
    }
    /* change campaign status */
    if (!$can_edit) {
      _error(403);
    }
    /* check approval system */
    if (!$campaign['campaign_is_approved']) {
      throw new Exception(__("You can't do this till your campaign gets admin approval"));
    }
    $is_active = ($is_active) ? '1' : '0';
    if ($is_active) {
      /* validate campaign */
      if (strtotime($campaign['campaign_end_date']) <= strtotime($date)) {
        throw new Exception(__("Campaign end date must be after today datetime"));
      }
      $remaining = $campaign['campaign_budget'] - $campaign['campaign_spend']; // campaign remaining (budget - spend)
      if ($remaining == 0) {
        throw new Exception(__("The campaign total spend reached the campaign budget already, increase the campaign budget to resume the campaign"));
      }
      if ($remaining > $campaign['user_wallet_balance']) {
        throw new Exception(__("The remaining spend is larger than current wallet credit") . " " . print_money($campaign['user_wallet_balance']) . " " . __("You need to") . " " . __("Replenish wallet credit"));
      }
      if ($campaign['campaign_bidding'] == "click") {
        if ($remaining < $system['ads_cost_click']) {
          throw new Exception(__("The cost per click is larger than your campaign remaining spend") . " " . print_money($remaining) . " " . __("increase the campaign budget to resume the campaign"));
        }
      }
      if ($campaign['campaign_bidding'] == "view") {
        if ($remaining < $system['ads_cost_view']) {
          throw new Exception(__("The cost per view is larger than your campaign remaining spend") . " " . print_money($remaining) . " " . __("increase the campaign budget to resume the campaign"));
        }
      }
    }
    $db->query(sprintf("UPDATE ads_campaigns SET campaign_is_active = %s WHERE campaign_id = %s", secure($is_active), secure($campaign_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * update_campaign_bidding
   * 
   * @param integer $campaign_id
   * @return void
   */
  public function update_campaign_bidding($campaign_id)
  {
    global $db, $system, $date;
    /* check if ads enabled */
    if (!$system['ads_enabled']) {
      return;
    }
    /* (check&get) campaign */
    $get_campaign = $db->query(sprintf("SELECT ads_campaigns.*, users.user_wallet_balance FROM ads_campaigns INNER JOIN users ON ads_campaigns.campaign_user_id = users.user_id WHERE ads_campaigns.campaign_id = %s", secure($campaign_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_campaign->num_rows == 0) {
      _error(403);
    }
    $campaign = $get_campaign->fetch_assoc();
    // update campaign if bidding = click
    if ($campaign['campaign_bidding'] == "click") {
      /* update campaign spend & clicks */
      $db->query(sprintf("UPDATE ads_campaigns SET campaign_clicks = campaign_clicks + 1, campaign_spend = campaign_spend + %s WHERE campaign_id = %s", secure($system['ads_cost_click']), secure($campaign['campaign_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* decrease campaign author wallet balance */
      $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($system['ads_cost_click']), secure($campaign['campaign_user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * campaign_estimated_reach
   * 
   * @param array $countries
   * @param string $gender
   * @param string $relationship
   * @return integer
   */
  public function campaign_potential_reach($countries = [], $gender = 'all', $relationship = 'all')
  {
    global $db, $system;
    $results = 0;
    /* validate gender */
    if ($gender != "all" && !$this->check_gender($gender)) {
      return $results;
    }
    /* validate relationship */
    $relationship = ($system['relationship_info_enabled']) ? $relationship : 'all';
    if (isset($relationship) && !in_array($relationship, ['all', 'single', 'relationship', 'married', "complicated", 'separated', 'divorced', 'widowed'])) {
      return $results;
    }
    /* prepare where statement */
    $where = "";
    /* validate countries */
    if ($countries) {
      if (!is_array($countries) || !is_numeric(implode('', $countries))) {
        return $results;
      }
      foreach ($countries as $key => $country) {
        if (!$this->check_country($country)) {
          unset($countries[$key]);
        }
      }
      if (count($countries) == 0) {
        return $results;
      }
      $countries_list = $this->spread_ids($countries);
      $where .= " WHERE user_country IN ($countries_list)";
    }
    /* gender */
    if ($gender != "all") {
      if ($where) {
        $where .= " AND user_gender = '$gender'";
      } else {
        $where .= " WHERE user_gender = '$gender'";
      }
    }
    /* relationship */
    if ($relationship != "all") {
      if ($where) {
        $where .= " AND user_relationship = '$relationship'";
      } else {
        $where .= " WHERE user_relationship = '$relationship'";
      }
    }
    /* get users */
    $get_users = $db->query("SELECT COUNT(*) as count FROM users" . $where) or _error('SQL_ERROR_THROWEN');
    $results = $get_users->fetch_assoc()['count'];
    return $results;
  }



  /* ------------------------------- */
  /* Wallet */
  /* ------------------------------- */

  /**
   * wallet_transfer
   * 
   * @param integer $user_id
   * @param integer $amount
   * @return void
   */
  public function wallet_transfer($user_id, $amount)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if wallet transfer enabled */
    if (!$system['wallet_transfer_enabled']) {
      throw new Exception(__("The wallet transfer feature has been disabled by the admin"));
    }
    /* validate amount */
    if (is_empty($amount) || !is_numeric($amount) || $amount <= 0) {
      throw new Exception(__("You must enter valid amount of money"));
    }
    if ($amount > $system['wallet_max_transfer']) {
      throw new Exception(__("You can't transfer more than") . " " . print_money($system['wallet_max_transfer']));
    }
    /* validate target user */
    if (is_empty($user_id) || !is_numeric($user_id)) {
      throw new Exception(__("You must search for a user to send money to"));
    }
    if ($this->_data['user_id'] == $user_id) {
      throw new Exception(__("You can't send money to yourself!"));
    }
    $check_user = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_user->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You can't send money to this user!"));
    }
    /* check viewer balance */
    if ($this->_data['user_wallet_balance'] < $amount) {
      throw new Exception(__("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* decrease viewer user wallet balance */
    $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log this transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'user', $user_id, $amount, 'out');
    /* increase target user wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($amount), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* send notification (money sent) to the target user */
    $this->post_notification(['to_user_id' => $user_id, 'action' => 'money_sent', 'node_type' => $amount]);
    /* wallet transaction */
    $this->wallet_set_transaction($user_id, 'user', $this->_data['user_id'], $amount, 'in');
    $_SESSION['wallet_transfer_amount'] = $amount;
  }


  /**
   * wallet_send_tip
   * 
   * @param integer $user_id
   * @param integer $amount
   * @return void
   */
  public function wallet_send_tip($user_id, $amount)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if tips enabled */
    if (!$system['tips_enabled']) {
      throw new Exception(__("The tips system has been disabled by the admin"));
    }
    /* validate amount */
    if (is_empty($amount) || !is_numeric($amount) || $amount <= 0) {
      throw new Exception(__("You must enter valid amount of money"));
    }
    if ($amount < $system['tips_min_amount']) {
      throw new Exception(__("The amount is less the minimum amount allowed"));
    }
    if ($amount > $system['tips_max_amount']) {
      throw new Exception(__("The amount is more than the maximum amount allowed"));
    }
    /* validate target user */
    if (is_empty($user_id) || !is_numeric($user_id)) {
      throw new Exception(__("You can't send money to this user!"));
    }
    if ($this->_data['user_id'] == $user_id) {
      throw new Exception(__("You can't send money to yourself!"));
    }
    $check_user = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_user->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You can't send money to this user!"));
    }
    /* check if target user can receive tips */
    if (!$this->check_user_permission($user_id, 'tips_permission')) {
      throw new Exception(__("This user can't receive tips"));
    }
    /* check viewer balance */
    if ($this->_data['user_wallet_balance'] < $amount) {
      throw new Exception(__("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* decrease viewer user wallet balance */
    $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log this transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'tip', $user_id, $amount, 'out');
    /* increase target user wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($amount), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* send notification (money sent) to the target user */
    $this->post_notification(['to_user_id' => $user_id, 'action' => 'tip_sent', 'node_type' => $amount]);
    /* wallet transaction */
    $this->wallet_set_transaction($user_id, 'tip', $this->_data['user_id'], $amount, 'in');
  }


  /**
   * wallet_withdraw_affiliates
   * 
   * @param integer $amount
   * @return void
   */
  public function wallet_withdraw_affiliates($amount)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if affiliates enabled */
    if (!$system['affiliates_enabled'] || !$system['affiliates_money_transfer_enabled']) {
      throw new Exception(__("The affiliates system has been disabled by the admin"));
    }
    /* validate amount */
    if (is_empty($amount) || !is_numeric($amount) || $amount <= 0) {
      throw new Exception(__("You must enter valid amount of money"));
    }
    /* check viewer balance */
    if ($this->_data['user_affiliate_balance'] < $amount) {
      throw new Exception(__("The amount is larger than your current affiliates balance") . " " . print_money($this->_data['user_affiliate_balance']) . "</strong>");
    }
    /* decrease viewer user affiliate balance */
    $db->query(sprintf('UPDATE users SET user_affiliate_balance = IF(user_affiliate_balance-%1$s=0,0,user_affiliate_balance-%1$s) WHERE user_id = %2$s', secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* increase viewer user wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* wallet transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'withdraw_affiliates', 0, $amount, 'in');
    $_SESSION['wallet_withdraw_affiliates_amount'] = $amount;
  }


  /**
   * wallet_withdraw_points
   * 
   * @param integer $amount
   * @return void
   */
  public function wallet_withdraw_points($amount)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if points enabled */
    if (!$system['points_enabled'] || !$system['points_money_transfer_enabled']) {
      throw new Exception(__("The points system has been disabled by the admin"));
    }
    /* validate amount */
    if (is_empty($amount) || !is_numeric($amount) || $amount <= 0) {
      throw new Exception(__("You must enter valid amount of money"));
    }
    /* check viewer balance */
    $points_balance = ((1 / $system['points_per_currency']) * $this->_data['user_points']);
    if ($points_balance < $amount) {
      throw new Exception(__("The amount is larger than your current points balance") . " " . print_money($points_balance) . "</strong>");
    }
    /* decrease viewer user points balance */
    $balance = $this->_data['user_points'] - ($system['points_per_currency'] * $_POST['amount']);
    $db->query(sprintf("UPDATE users SET user_points = %s WHERE user_id = %s", secure($balance), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* increase viewer user wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* wallet transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'withdraw_points', 0, $amount, 'in');
    $_SESSION['wallet_withdraw_points_amount'] = $amount;
  }


  /**
   * wallet_withdraw_market
   * 
   * @param integer $amount
   * @return void
   */
  public function wallet_withdraw_market($amount)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if market enabled */
    if (!$system['market_enabled'] || !$system['market_money_transfer_enabled']) {
      throw new Exception(__("The market module has been disabled by the admin"));
    }
    /* validate amount */
    if (is_empty($amount) || !is_numeric($amount) || $amount <= 0) {
      throw new Exception(__("You must enter valid amount of money"));
    }
    /* check viewer balance */
    if ($this->_data['user_market_balance'] < $amount) {
      throw new Exception(__("The amount is larger than your current market balance") . " " . print_money($this->_data['user_market_balance']) . "</strong>");
    }
    /* decrease viewer user market balance */
    $db->query(sprintf('UPDATE users SET user_market_balance = IF(user_market_balance-%1$s=0,0,user_market_balance-%1$s) WHERE user_id = %2$s', secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* increase viewer user wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* wallet transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'withdraw_market', 0, $amount, 'in');
    $_SESSION['wallet_withdraw_market_amount'] = $amount;
  }


  /**
   * wallet_withdraw_funding
   * 
   * @param integer $amount
   * @return void
   */
  public function wallet_withdraw_funding($amount)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if funding enabled */
    if (!$system['funding_enabled'] || !$system['funding_money_transfer_enabled']) {
      throw new Exception(__("The funding module has been disabled by the admin"));
    }
    /* validate amount */
    if (is_empty($amount) || !is_numeric($amount) || $amount <= 0) {
      throw new Exception(__("You must enter valid amount of money"));
    }
    /* check viewer balance */
    if ($this->_data['user_funding_balance'] < $amount) {
      throw new Exception(__("The amount is larger than your current funding balance") . " " . print_money($this->_data['user_funding_balance']) . "</strong>");
    }
    /* decrease viewer user funding balance */
    $db->query(sprintf('UPDATE users SET user_funding_balance = IF(user_funding_balance-%1$s=0,0,user_funding_balance-%1$s) WHERE user_id = %2$s', secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* increase viewer user wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* wallet transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'withdraw_funding', 0, $amount, 'in');
    $_SESSION['wallet_withdraw_funding_amount'] = $amount;
  }


  /**
   * wallet_withdraw_monetization
   * 
   * @param integer $amount
   * @return void
   */
  public function wallet_withdraw_monetization($amount)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if monetization enabled */
    if (!$system['monetization_enabled'] || !$system['monetization_money_transfer_enabled']) {
      throw new Exception(__("The monetization system has been disabled by the admin"));
    }
    /* validate amount */
    if (is_empty($amount) || !is_numeric($amount) || $amount <= 0) {
      throw new Exception(__("You must enter valid amount of money"));
    }
    /* check viewer balance */
    if ($this->_data['user_monetization_balance'] < $amount) {
      throw new Exception(__("The amount is larger than your current monetization balance") . " " . print_money($this->_data['user_monetization_balance']) . "</strong>");
    }
    /* decrease viewer user monetization balance */
    $db->query(sprintf('UPDATE users SET user_monetization_balance = IF(user_monetization_balance-%1$s=0,0,user_monetization_balance-%1$s) WHERE user_id = %2$s', secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* increase viewer user wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($amount), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* wallet transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'withdraw_monetization', 0, $amount, 'in');
    $_SESSION['wallet_withdraw_monetization_amount'] = $amount;
  }


  /**
   * wallet_set_transaction
   * 
   * @param integer $user_id
   * @param string $node_type
   * @param integer $node_id
   * @param integer $amount
   * @param string $type
   * @return void
   */
  public function wallet_set_transaction($user_id, $node_type, $node_id, $amount, $type)
  {
    global $db, $system, $date;
    $db->query(sprintf("INSERT INTO wallet_transactions (user_id, node_type, node_id, amount, type, date) VALUES (%s, %s, %s, %s, %s, %s)", secure($user_id, 'int'), secure($node_type), secure($node_id, 'int'), secure($amount), secure($type), secure($date))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * wallet_get_transactions
   * 
   * @return array
   */
  public function wallet_get_transactions()
  {
    global $db;
    $transactions = [];
    $get_transactions = $db->query(sprintf("SELECT wallet_transactions.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM wallet_transactions LEFT JOIN users ON (wallet_transactions.node_type='user' OR wallet_transactions.node_type='tip') AND wallet_transactions.node_id = users.user_id WHERE wallet_transactions.user_id = %s ORDER BY wallet_transactions.transaction_id DESC", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_transactions->num_rows > 0) {
      while ($transaction = $get_transactions->fetch_assoc()) {
        if ($transaction['node_type'] == "user" || $transaction['node_type'] == "tip") {
          $transaction['user_picture'] = get_picture($transaction['user_picture'], $transaction['user_gender']);
        }
        $transactions[] = $transaction;
      }
    }
    return $transactions;
  }


  /**
   * wallet_get_payments
   * 
   * @return array
   */
  public function wallet_get_payments()
  {
    global $db;
    $payments = [];
    $get_payments = $db->query(sprintf("SELECT * FROM wallet_payments WHERE user_id = %s ORDER BY payment_id DESC", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_payments->num_rows > 0) {
      while ($payment = $get_payments->fetch_assoc()) {
        $payments[] = $payment;
      }
    }
    return $payments;
  }


  /**
   * wallet_package_payment
   * 
   * @param integer $package_id
   * @return void
   */
  public function wallet_package_payment($package_id)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if packages enabled */
    if (!$system['packages_enabled'] || !$system['packages_wallet_payment_enabled']) {
      throw new Exception(__("The packages system has been disabled by the admin"));
    }
    /* check package */
    $package = $this->get_package($package_id);
    if (!$package) {
      _error(400);
    }
    /* check if user already subscribed to this package */
    if ($this->_data['user_subscribed'] && $this->_data['user_package'] == $package['package_id']) {
      modal("SUCCESS", __("Subscribed"), __("You already subscribed to this package, Please select different package"));
    }
    /* check viewer balance */
    if ($this->_data['user_wallet_balance'] < $package['price']) {
      modal("ERROR", __("Sorry"), __("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* decrease viewer user wallet balance */
    $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($package['price']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log this transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'package_payment', $package['package_id'], $package['price'], 'out');
    /* update user package */
    $this->update_user_package($package['package_id'], $package['name'], $package['price'], $package['verification_badge_enabled']);
    $_SESSION['wallet_package_payment_amount'] = $package['price'];
  }


  /**
   * wallet_monetization_payment
   * 
   * @param integer $plan_id
   * @return void
   */
  public function wallet_monetization_payment($plan_id)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if monetization enabled */
    if (!$system['monetization_enabled'] || !$system['monetization_wallet_payment_enabled']) {
      throw new Exception(__("The monetization system has been disabled by the admin"));
    }
    // get monetization plan
    $monetization_plan = $this->get_monetization_plan($plan_id, true);
    if (!$monetization_plan) {
      throw new Exception(__("This monetization plan is not available"));
    }
    /* check if the viewer is subscribed to this node */
    if ($this->is_subscribed($monetization_plan['node_id'], $monetization_plan['node_type'])) {
      modal("SUCCESS", __("Subscribed"), __("You already subscribed to this") . " " . __($monetization_plan['node_type']));
    }
    /* check viewer balance */
    if ($this->_data['user_wallet_balance'] < $monetization_plan['price']) {
      modal("ERROR", __("Sorry"), __("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* decrease viewer user wallet balance */
    $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($monetization_plan['price'], 'float'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log this transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'subscribe_' . $monetization_plan['node_type'], $monetization_plan['node_id'], $monetization_plan['price'], 'out');
    /* subscribe to node */
    $this->subscribe($plan_id);
    $_SESSION['wallet_monetization_payment_amount'] = $monetization_plan['price'];
  }


  /**
   * wallet_paid_post
   * 
   * @param integer $post_id
   * @return void
   */
  public function wallet_paid_post($post_id)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check if monetization enabled */
    if (!$system['monetization_enabled'] || !$system['monetization_wallet_payment_enabled']) {
      throw new Exception(__("The monetization system has been disabled by the admin"));
    }
    // get post
    $post = $this->get_post($post_id, false, false, true);
    if (!$post) {
      throw new Exception(__("This post is not available"));
    }
    if (!$post['needs_payment']) {
      throw new Exception(__("This post doesn't need payment"));
    }
    /* check viewer balance */
    if ($this->_data['user_wallet_balance'] < $post['post_price']) {
      modal("ERROR", __("Sorry"), __("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* decrease viewer user wallet balance */
    $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($post['post_price'], 'float'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log this transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'paid_post', $post_id, $post['post_price'], 'out');
    /* unlock paid post */
    $this->unlock_paid_post($post_id);
    $_SESSION['wallet_paid_post_amount'] = $post['post_price'];
  }


  /**
   * wallet_chat_payment
   * 
   * @param float $chat_price
   * @param array $paid_recipients
   * @return void
   */
  public function wallet_chat_payment($chat_price, $paid_recipients)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check viewer balance */
    if ($this->_data['user_wallet_balance'] < $chat_price) {
      throw new Exception(__("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* decrease viewer user wallet balance */
    $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($chat_price, 'float'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log this transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'paid_chat_message', null, $chat_price, 'out');
    /* loop through the paid recipients */
    foreach ($paid_recipients as $recipient) {
      /* prepare commission */
      $message_price = $recipient['user_monetization_chat_price'];
      $commission = ($system['monetization_commission']) ? $message_price * ($system['monetization_commission'] / 100) : 0;
      $message_price = $message_price - $commission;
      /* increase recipient monetization balance */
      $db->query(sprintf("UPDATE users SET user_monetization_balance = user_monetization_balance + %s WHERE user_id = %s", secure($message_price, 'float'), secure($recipient['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* log commission */
      $this->log_commission($recipient['user_id'], $commission, 'paid_message');
      /* notify the recipient */
      $this->post_notification(['to_user_id' => $recipient['user_id'], 'action' => 'chat_message_paid', 'node_type' => $message_price]);
    }
  }


  /**
   * wallet_call_payment
   * 
   * @param float $call_price
   * @param int $from_user_id
   * @param int $to_user_id
   * @return void
   */
  public function wallet_call_payment($call_price, $from_user_id, $to_user_id)
  {
    global $db, $system;
    /* check if wallet enabled */
    if (!$system['wallet_enabled']) {
      throw new Exception(__("The wallet system has been disabled by the admin"));
    }
    /* check from_user_id balance */
    $from_user = $this->get_user($from_user_id);
    if ($from_user['user_wallet_balance'] < $call_price) {
      throw new Exception(__("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* decrease from_user_id wallet balance */
    $db->query(sprintf('UPDATE users SET user_wallet_balance = IF(user_wallet_balance-%1$s<=0,0,user_wallet_balance-%1$s) WHERE user_id = %2$s', secure($call_price, 'float'), secure($from_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log this transaction */
    $this->wallet_set_transaction($from_user_id, 'paid_call', $to_user_id, $call_price, 'out');
    /* prepare commission */
    $commission = ($system['monetization_commission']) ? $call_price * ($system['monetization_commission'] / 100) : 0;
    $call_price = $call_price - $commission;
    /* increase to_user_id monetization balance */
    $db->query(sprintf("UPDATE users SET user_monetization_balance = user_monetization_balance + %s WHERE user_id = %s", secure($call_price, 'float'), secure($to_user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log commission */
    $this->log_commission($to_user_id, $commission, 'paid_call');
  }



  /* ------------------------------- */
  /* CoinPayments */
  /* ------------------------------- */

  /**
   * coinpayments
   * 
   * @param string $handle
   * @param string $price
   * @param integer $id
   * @return string
   */
  public function coinpayments($handle, $price, $id = null)
  {
    global $system;
    /* prepare */
    switch ($handle) {
      case 'packages':
        $product = __($system['system_title']) . " " . __('Pro Package');
        $description = __('Pay For') . " " . __($system['system_title']);
        $URL['success'] = $system['system_url'] . "/settings/coinpayments";
        $URL['cancel'] = $system['system_url'];
        $URL['ipn'] = $system['system_url'] . "/webhooks/coinpayments.php?handle=packages&package_id=$id";
        break;

      case 'wallet':
        $product = __($system['system_title']) . " " . __('Wallet');
        $description = __('Pay For') . " " . __($system['system_title']);
        $URL['success'] = $system['system_url'] . "/settings/coinpayments";
        $URL['cancel'] = $system['system_url'];
        $URL['ipn'] = $system['system_url'] . "/webhooks/coinpayments.php?handle=wallet";
        break;

      case 'donate':
        $product = __($system['system_title']) . " " . __('Funding Donation');
        $description = __('Pay For') . " " . __($system['system_title']);
        $URL['success'] = $system['system_url'] . "/settings/coinpayments";
        $URL['cancel'] = $system['system_url'];
        $URL['ipn'] = $system['system_url'] . "/webhooks/coinpayments.php?handle=donate&post_id=$id";
        break;

      case 'subscribe':
        $product = __($system['system_title']) . " " . __('Subscribe');
        $description = __('Pay For') . " " . __($system['system_title']);
        $URL['success'] = $system['system_url'] . "/settings/coinpayments";
        $URL['cancel'] = $system['system_url'];
        $URL['ipn'] = $system['system_url'] . "/webhooks/coinpayments.php?handle=subscribe&plan_id=$id";
        break;

      default:
        _error(400);
        break;
    }
    /* set new coinpayments transaction */
    $transaction_id = $this->set_coinpayments_transaction($price, $product);
    /* CoinPayments */
    $form = '';
    $form =  '<form action="https://www.coinpayments.net/index.php" method="post">';
    $form .= '<input type="hidden" name="cmd" value="_pay_simple">';
    $form .= '<input type="hidden" name="reset" value="1">';
    $form .= '<input type="hidden" name="merchant" value="' . $system['coinpayments_merchant_id'] . '">';
    $form .= '<input type="hidden" name="item_name" value="' . $product . '">';
    $form .= '<input type="hidden" name="item_desc" value="' . $description . '">';
    $form .= '<input type="hidden" name="currency" value="' . $system['system_currency'] . '">';
    $form .= '<input type="hidden" name="amountf" value="' . $price . '">';
    $form .= '<input type="hidden" name="want_shipping" value="0">';
    $form .= '<input type="hidden" name="success_url" value="' . $URL['success'] . '">';
    $form .= '<input type="hidden" name="cancel_url" value="' . $URL['cancel'] . '">';
    $form .= '<input type="hidden" name="ipn_url" value="' . $URL['ipn'] . '">';
    $form .= '<input type="hidden" name="custom" value="' . $transaction_id . '">';
    $form .= '<input type="image" src="https://www.coinpayments.net/images/pub/CP-main-medium.png" alt="Buy Now with CoinPayments.net">';
    $form .= '</form>';
    return $form;
  }


  /**
   * check_coinpayments_payment
   * 
   * @param integer $transaction_id
   * @return boolean
   */
  public function check_coinpayments_payment($transaction_id)
  {
    global $system;
    if (!isset($_POST['ipn_mode']) || !isset($_POST['merchant'])) {
      $this->update_coinpayments_transaction($transaction_id, "Error (400): Bad Reuqeust [Missing POST data from callback]", '-1');
    }
    switch ($_POST['ipn_mode']) {
      case 'httpauth':
        if ($_SERVER['PHP_AUTH_USER'] != $system['coinpayments_merchant_id'] || $_SERVER['PHP_AUTH_PW'] != $system['coinpayments_ipn_secret']) {
          $this->update_coinpayments_transaction($transaction_id, "Error (400): Bad Reuqeust [Unauthorized HTTP Request]", '-1');
        }
        break;

      case 'hmac':
        /* create the HMAC hash to compare to the recieved one, using the secret key */
        $hmac = hash_hmac("sha512", file_get_contents('php://input'), $system['coinpayments_ipn_secret']);
        if ($hmac != $_SERVER['HTTP_HMAC']) {
          $this->update_coinpayments_transaction($transaction_id, "Error (400): Bad Reuqeust [Unauthorized HMAC Request]", '-1');
        }
        break;

      default:
        $this->update_coinpayments_transaction($transaction_id, "Error (400): Bad Reuqeust [Invalid IPN Mode]", '-1');
        break;
    }
    /* check the request status */
    if ($_POST['merchant'] != $system['coinpayments_merchant_id']) {
      $this->update_coinpayments_transaction($transaction_id, "Error (400): Bad Reuqeust [Mismatching Merchant ID]", '-1');
      return false;
    }
    if (intval($_POST['status']) >= 100 || intval($_POST['status']) == 2) {
      /* the payment complete successfully */
      return true;
    } else {
      /* the payment is pending */
      $this->update_coinpayments_transaction($transaction_id, __("Your payment is pending"), '1');
    }
  }


  /**
   * set_coinpayments_transaction
   * 
   * @param string $amount
   * @param string $product
   * @return integer
   */
  public function set_coinpayments_transaction($amount, $product)
  {
    global $db, $system, $date;
    $db->query(sprintf("INSERT INTO coinpayments_transactions (user_id, amount, product, created_at, last_update, status) VALUES (%s, %s, %s, %s, %s, '0')", secure($this->_data['user_id'], 'int'), secure($amount), secure($product), secure($date), secure($date))) or _error('SQL_ERROR_THROWEN');
    return $db->insert_id;
  }


  /**
   * get_coinpayments_transaction
   * 
   * @param integer $transaction_id
   * @param string $transaction_txn_id
   * @return array
   */
  public function get_coinpayments_transaction($transaction_id, $transaction_txn_id)
  {
    global $db;
    $get_transaction = $db->query(sprintf("SELECT * FROM coinpayments_transactions WHERE transaction_id = %s", secure($transaction_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_transaction->num_rows == 0) {
      return false;
    }
    $transaction = $get_transaction->fetch_assoc();
    if (is_empty($transaction['transaction_txn_id'])) {
      $db->query(sprintf("UPDATE coinpayments_transactions SET transaction_txn_id = %s WHERE transaction_id = %s", secure($transaction_txn_id), secure($transaction_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    return $transaction;
  }


  /**
   * update_coinpayments_transaction
   * 
   * @param integer $transaction_id
   * @param string $status_message
   * @param integer $status
   * @return void
   */
  public function update_coinpayments_transaction($transaction_id, $status_message, $status = 0)
  {
    global $db, $date;
    $db->query(sprintf("UPDATE coinpayments_transactions SET status = %s, status_message = %s, last_update = %s WHERE transaction_id = %s", secure($status, 'int'), secure($status_message), secure($date), secure($transaction_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    exit;
  }


  /**
   * get_coinpayments_transactions
   * 
   * @param boolean $get_all
   * @return array
   */
  public function get_coinpayments_transactions($get_all = false)
  {
    global $db;
    $transactions = [];
    if ($get_all) {
      $get_transactions = $db->query("SELECT coinpayments_transactions.*, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM coinpayments_transactions LEFT JOIN users ON coinpayments_transactions.user_id = users.user_id  ORDER BY coinpayments_transactions.created_at DESC, coinpayments_transactions.last_update DESC") or _error('SQL_ERROR_THROWEN');
    } else {
      $get_transactions = $db->query(sprintf("SELECT * FROM coinpayments_transactions WHERE user_id = %s ORDER BY transaction_id DESC", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    if ($get_transactions->num_rows > 0) {
      while ($transaction = $get_transactions->fetch_assoc()) {
        if ($get_all) {
          $transaction['user_picture'] = get_picture($transaction['user_picture'], $transaction['user_gender']);
        }
        $transactions[] = $transaction;
      }
    }
    return $transactions;
  }



  /* ------------------------------- */
  /* Pro Packages */
  /* ------------------------------- */

  /**
   * get_packages
   * 
   * @return array
   */
  public function get_packages()
  {
    global $db;
    $packages = [];
    $get_packages = $db->query("SELECT * FROM packages LEFT JOIN permissions_groups ON packages.package_permissions_group_id = permissions_groups.permissions_group_id ORDER BY packages.package_order ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_packages->num_rows > 0) {
      while ($package = $get_packages->fetch_assoc()) {
        $package['icon'] = get_picture($package['icon'], 'package');
        $packages[] = $package;
      }
    }
    return $packages;
  }


  /**
   * get_package
   * 
   * @param integer $package_id
   * @return array|false
   */
  public function get_package($package_id)
  {
    global $db;
    $get_package = $db->query(sprintf('SELECT * FROM packages WHERE package_id = %s', secure($package_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_package->num_rows == 0) {
      return false;
    }
    $package = $get_package->fetch_assoc();
    return $package;
  }


  /**
   * check_user_package
   * 
   * @return void
   */
  public function check_user_package()
  {
    global $db;
    if ($this->_data['user_subscribed']) {
      /* get package */
      $package = $this->get_package($this->_data['user_package']);
      if ($package) {
        switch ($package['period']) {
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

          case 'life':
            return;
            break;
        }
        $time_left = time() - ($package['period_num'] * $duration);
        if (strtotime($this->_data['user_subscription_date']) > $time_left) {
          return;
        }
      }
      /* remove user package */
      $db->query(sprintf("UPDATE users SET user_subscribed = '0', user_package = null, user_subscription_date = null, user_boosted_posts = '0', user_boosted_pages = '0' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* unboost posts */
      $db->query(sprintf("UPDATE posts SET boosted = '0' WHERE user_id = %s AND user_type = 'user'", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* unboost pages */
      $db->query(sprintf("UPDATE pages SET page_boosted = '0' WHERE page_admin = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * check_users_package
   * 
   * @return void
   */
  public function check_users_package()
  {
    global $db;
    $get_subscribed_users = $db->query("SELECT user_id, user_package, user_subscription_date FROM users WHERE user_subscribed = '1'") or _error('SQL_ERROR_THROWEN');
    if ($get_subscribed_users->num_rows == 0) {
      return;
    }
    while ($subscribed_user = $get_subscribed_users->fetch_assoc()) {
      /* get package */
      $package = $this->get_package($subscribed_user['user_package']);
      if ($package) {
        switch ($package['period']) {
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

          case 'life':
            continue 2;
            break;
        }
        $time_left = time() - ($package['period_num'] * $duration);
        if (strtotime($subscribed_user['user_subscription_date']) > $time_left) {
          continue;
        }
      }
      /* remove user package */
      $db->query(sprintf("UPDATE users SET user_subscribed = '0', user_package = null, user_subscription_date = null, user_boosted_posts = '0', user_boosted_pages = '0' WHERE user_id = %s", secure($subscribed_user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* unboost posts */
      $db->query(sprintf("UPDATE posts SET boosted = '0' WHERE boosted_by = %s AND boosted = '1'", secure($subscribed_user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* unboost pages */
      $db->query(sprintf("UPDATE pages SET page_boosted = '0' WHERE page_admin = %s", secure($subscribed_user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * update_user_package
   * 
   * @param integer $package_id
   * @param string $package_name
   * @param string $package_price
   * @param boolean $package_verification
   * @param integer $user_id
   * @return void
   */
  public function update_user_package($package_id, $package_name, $package_price, $package_verification, $user_id = null)
  {
    global $system, $db, $date;
    /* check if pro packages enabled */
    if (!$system['packages_enabled']) {
      throw new Exception(__("The packages system has been disabled by the admin"));
    }
    /* check user */
    $user_id = ($user_id == null) ? $this->_data['user_id'] : $user_id;
    /* remove user old package */
    $db->query(sprintf("UPDATE users SET user_subscribed = '0', user_package = null, user_subscription_date = null, user_boosted_posts = '0', user_boosted_pages = '0' WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update user package */
    $verification_statement = ($package_verification) ? " user_verified = '1', " : ""; /* to not affect already verified users */
    $free_trial = ($package_price == "0") ? " user_free_tried = '1', " : "";
    $db->query(sprintf("UPDATE users SET " . $verification_statement . $free_trial . " user_subscribed = '1', user_package = %s, user_subscription_date = %s, user_boosted_posts = '0', user_boosted_pages = '0' WHERE user_id = %s", secure($package_id, 'int'), secure($date), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* insert the payment */
    $db->query(sprintf("INSERT INTO packages_payments (payment_date, package_name, package_price, user_id) VALUES (%s, %s, %s, %s)", secure($date), secure($package_name), secure($package_price), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* affiliates system */
    if ($user_id == $this->_data['user_id']) {
      $user_referrer_id = $this->_data['user_referrer_id'];
    } else {
      $get_user_referrer = $db->query(sprintf("SELECT user_referrer_id FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      $user_referrer_id =  $get_user_referrer->fetch_assoc()['user_referrer_id'];
    }
    $this->process_affiliates("packages", $user_id, $user_referrer_id, $package_price);
  }


  /**
   * unsubscribe_user_package
   * 
   * @param integer $user_id
   * @return void
   */
  public function unsubscribe_user_package($user_id = null)
  {
    global $db;
    /* check user */
    $user_id = ($user_id == null) ? $this->_data['user_id'] : $user_id;
    /* remove user package */
    $db->query(sprintf("UPDATE users SET user_subscribed = '0', user_package = null, user_subscription_date = null, user_boosted_posts = '0', user_boosted_pages = '0' WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* get recurring payment */
    $get_recurring_payment = $db->query(sprintf("SELECT * FROM users_recurring_payments WHERE handle = 'packages' AND user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_recurring_payment->num_rows > 0) {
      $recurring_payment = $get_recurring_payment->fetch_assoc();
      /* cancel the subscription */
      if ($recurring_payment['payment_gateway'] == "paypal") {
        paypal_cancel_subscription($recurring_payment['subscription_id']);
      }
      if ($recurring_payment['payment_gateway'] == "stripe") {
        stripe_cancel_subscription($recurring_payment['subscription_id']);
      }
      /* remove user recurring payment */
      $db->query(sprintf("DELETE FROM users_recurring_payments WHERE handle = 'packages' AND user_id = %s ", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }



  /* ------------------------------- */
  /* Monetization ✅ */
  /* ------------------------------- */

  /**
   * get_monetization_plans
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return array
   */
  public function get_monetization_plans($node_id = null, $node_type = "profile")
  {
    global $db;
    $monetization_plans = [];
    $node_id = (isset($node_id)) ? $node_id : $this->_data['user_id'];
    $get_monetization_plans = $db->query(sprintf("SELECT * FROM monetization_plans WHERE node_id = %s AND node_type = %s ORDER BY plan_order", secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    if ($get_monetization_plans->num_rows > 0) {
      while ($monetization_plan = $get_monetization_plans->fetch_assoc()) {
        $monetization_plans[] = $monetization_plan;
      }
    }
    return $monetization_plans;
  }


  /**
   * get_monetization_plans_count
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return integer
   */
  public function get_monetization_plans_count($node_id = null, $node_type = "profile")
  {
    global $db;
    $node_id = (isset($node_id)) ? $node_id : $this->_data['user_id'];
    $get_monetization_plans = $db->query(sprintf("SELECT COUNT(*) as count FROM monetization_plans WHERE node_id = %s AND node_type = %s", secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    if ($get_monetization_plans->num_rows > 0) {
      return $get_monetization_plans->fetch_assoc()['count'];
    }
    return 0;
  }


  /**
   * get_monetization_plan
   * 
   * @param integer $plan_id
   * @param boolean $override_authorization
   * @return array|false
   */
  public function get_monetization_plan($plan_id, $override_authorization = false)
  {
    global $db;
    /* get plan */
    $get_monetization_plan = $db->query(sprintf("SELECT * FROM monetization_plans WHERE plan_id = %s", secure($plan_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_monetization_plan->num_rows == 0) {
      return false;
    }
    $monetization_plan = $get_monetization_plan->fetch_assoc();
    /* check if the viewer authorized */
    if (!$override_authorization) {
      if (!$this->_is_admin && !$this->_is_moderator) {
        switch ($monetization_plan['node_type']) {
          case 'profile':
            if ($monetization_plan['node_id'] != $this->_data['user_id']) {
              throw new AuthorizationException(__("You are not authorized to do this action"));
            }
            break;

          case 'page':
            if (!$this->check_page_adminship($this->_data['user_id'], $monetization_plan['node_id'])) {
              throw new AuthorizationException(__("You are not authorized to do this action"));
            }
            break;

          case 'group':
            if (!$this->check_group_adminship($this->_data['user_id'], $monetization_plan['node_id'])) {
              throw new AuthorizationException(__("You are not authorized to do this action"));
            }
            break;
        }
      }
    }
    return $monetization_plan;
  }


  /**
   * insert_monetization_plan
   * 
   * @param integer $node_id
   * @param string $node_type
   * @param string $title
   * @param float $price
   * @param integer $period_num
   * @param string $period
   * @param string $custom_description
   * @param integer $plan_order
   * @return array
   */
  public function insert_monetization_plan($node_id, $node_type, $title, $price, $period_num, $period, $custom_description, $plan_order)
  {
    global $db, $system;
    /* validate */
    if (!isset($node_id) || !is_numeric($node_id)) {
      throw new BadRequestException(__("Invalid node id"));
    }
    if (!isset($node_type) || !in_array($node_type, ['profile', 'page', 'group'])) {
      throw new BadRequestException(__("Invalid node type"));
    }
    /* generate billing plan id */
    $user_authorized = false;
    switch ($node_type) {
      case 'profile':
        $billing_plan_id = "CP" . "-" . "U" . $node_id . "-" . $price;
        if ($node_id == $this->_data['user_id']) {
          $user_authorized = true;
        }
        break;

      case 'page':
        $billing_plan_id = "CP" . "-" . "P" . $node_id . "-" . $price;
        if ($this->check_page_adminship($this->_data['user_id'], $node_id)) {
          $user_authorized = true;
        }
        break;

      case 'group':
        $billing_plan_id = "CP" . "-" . "G" . $node_id . "-" . $price;
        if ($this->check_group_adminship($this->_data['user_id'], $node_id)) {
          $user_authorized = true;
        }
        break;
    }
    /* check if the viewer is authorized */
    if (!$this->_is_admin && !$this->_is_moderator && !$user_authorized) {
      throw new AuthorizationException(__("You are not authorized to do this action"));
    }
    /* validate */
    if (!isset($title) || is_empty($title)) {
      throw new ValidationException(__("Please enter a title"));
    }
    if (!isset($price) || !is_numeric($price) || $price < 0) {
      throw new ValidationException(__("Please enter a valid price"));
    }
    /* check if the price = 0 */
    if ($price == 0) {
      /* check if the node has a free plan */
      if ($this->has_free_monetization_plan($node_id, $node_type) != false) {
        throw new ValidationException(__("You can't add more than one free plan"));
      }
    }
    if (!isset($period_num) || !is_numeric($period_num) || $period_num <= 0) {
      throw new ValidationException(__("Please enter a valid paid every value"));
    }
    if (!isset($period) || !in_array($period, ['minute', 'hour', 'day', 'week', 'month', 'year'])) {
      throw new ValidationException(__("Please enter a valid period"));
    }
    /* PayPal billing plan */
    $paypal_billing_plan = '';
    $paypal_recurring_enabled = $system['paypal_enabled'] && !is_empty($system['paypal_webhook']);
    if ($paypal_recurring_enabled && $price > 0 && !in_array($period, ['minute', 'hour'])) {
      /* create PayPal billing plan */
      $paypal_billing_plan = paypal_create_billing_plan($billing_plan_id, $billing_plan_id, $period_num, $period, $price);
    }
    /* Stripe billing plan */
    $stripe_billing_plan = '';
    $stripe_recurring_enabled = ($system['creditcard_enabled'] || $system['alipay_enabled']) && !is_empty($system['stripe_webhook']);
    if ($stripe_recurring_enabled && $price > 0 && !in_array($period, ['minute', 'hour'])) {
      /* create Stripe billing plan */
      $stripe_billing_plan = stripe_create_billing_plan($title, $custom_description, $period_num, $period, $price);
    }
    /* insert monetization plan */
    $db->query(sprintf("INSERT INTO monetization_plans (node_id, node_type, title, price, period_num, period, custom_description, plan_order, paypal_billing_plan, stripe_billing_plan) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($node_id, 'int'), secure($node_type), secure($title), secure($price, 'float'), secure($period_num), secure($period), secure($custom_description), secure($plan_order ?: '1'), secure($paypal_billing_plan), secure($stripe_billing_plan))) or _error('SQL_ERROR_THROWEN');
    $monetization_plan_id = $db->insert_id;
    /* update monetization plans */
    $this->update_monetization_plans($node_id, $node_type);
    /* return inserted plan */
    return $this->get_monetization_plan($monetization_plan_id, true);
  }


  /**
   * update_monetization_plan
   * 
   * @param integer $plan_id
   * @param string $title
   * @param float $price
   * @param integer $period_num
   * @param string $period
   * @param string $custom_description
   * @param integer $plan_order
   * @return array
   */
  public function update_monetization_plan($plan_id, $title, $price, $period_num, $period, $custom_description, $plan_order)
  {
    global $db, $system;
    /* get monetization plan */
    $monetization_plan = $this->get_monetization_plan($plan_id);
    if (!$monetization_plan) {
      throw new Exception(__("This monetization plan is not available"));
    }
    /* validate */
    if (!isset($title) || is_empty($title)) {
      throw new ValidationException(__("Please enter a title"));
    }
    if (!isset($price) || !is_numeric($price) || $price < 0) {
      throw new ValidationException(__("Please enter a valid price"));
    }
    /* check if the price = 0 */
    if ($price == 0) {
      /* check if the node has a free plan */
      $free_plan = $this->has_free_monetization_plan($monetization_plan['node_id'], $monetization_plan['node_type']);
      if (is_array($free_plan) && $free_plan['plan_id'] != $plan_id) {
        throw new ValidationException(__("You can't add more than one free plan"));
      }
    }
    if (!isset($period_num) || !is_numeric($period_num) || $period_num <= 0) {
      throw new ValidationException(__("Please enter a valid paid every value"));
    }
    if (!isset($period) || !in_array($period, ['minute', 'hour', 'day', 'week', 'month', 'year'])) {
      throw new ValidationException(__("Please enter a valid period"));
    }
    /* generate billing plan id */
    switch ($monetization_plan['node_type']) {
      case 'profile':
        $billing_plan_id = "CP" . "-" . "U" . $monetization_plan['node_id'] . "-" . $price;
        break;

      case 'page':
        $billing_plan_id = "CP" . "-" . "P" . $monetization_plan['node_id'] . "-" . $price;
        break;

      case 'group':
        $billing_plan_id = "CP" . "-" . "G" . $monetization_plan['node_id'] . "-" . $price;
        break;
    }
    /* PayPal billing plan */
    $paypal_billing_plan = $monetization_plan['paypal_billing_plan'];
    $paypal_recurring_enabled = $system['paypal_enabled'] && !is_empty($system['paypal_webhook']);
    if ($paypal_recurring_enabled && $price > 0 && !in_array($period, ['minute', 'hour'])) {
      /* check if PayPal billing plan is not created */
      if (is_empty($paypal_billing_plan)) {
        /* create PayPal billing plan */
        $paypal_billing_plan = paypal_create_billing_plan($billing_plan_id, $billing_plan_id, $period_num, $period, $price);
      } else {
        /* check if the (period || period_num || price) is edited */
        if ($monetization_plan['period'] != $period || $monetization_plan['period_num'] != $period_num || $monetization_plan['price'] != $price) {
          /* replace the plan */
          $paypal_billing_plan = paypal_replace_billing_plan($monetization_plan['paypal_billing_plan'], $billing_plan_id, $billing_plan_id, $period_num, $period, $price);
        }
      }
    } else {
      $paypal_billing_plan = '';
      if ($paypal_recurring_enabled && $monetization_plan['paypal_billing_plan']) {
        paypal_deactivate_billing_plan($monetization_plan['paypal_billing_plan']);
      }
    }
    /* Stripe billing plan */
    $stripe_billing_plan = $monetization_plan['stripe_billing_plan'];
    $stripe_recurring_enabled = ($system['creditcard_enabled'] || $system['alipay_enabled']) && !is_empty($system['stripe_webhook']);
    if ($stripe_recurring_enabled && $price > 0 && !in_array($period, ['minute', 'hour'])) {
      /* check if Stripe billing plan is not created */
      if (is_empty($stripe_billing_plan)) {
        /* create Stripe billing plan */
        $stripe_billing_plan = stripe_create_billing_plan($billing_plan_id, $billing_plan_id, $period_num, $period, $price);
      } else {
        /* check if the (period || period_num || price) is edited */
        if ($monetization_plan['period'] != $period || $monetization_plan['period_num'] != $period_num || $monetization_plan['price'] != $price) {
          /* replace the plan */
          $stripe_billing_plan = stripe_replace_billing_plan($monetization_plan['stripe_billing_plan'], $billing_plan_id, $billing_plan_id, $period_num, $period, $price);
        }
      }
    } else {
      $stripe_billing_plan = '';
      if ($stripe_recurring_enabled && $monetization_plan['stripe_billing_plan']) {
        stripe_deactivate_billing_plan($monetization_plan['stripe_billing_plan']);
      }
    }
    /* remove all users recurring payments */
    if ($paypal_billing_plan == '' && $stripe_billing_plan == '') {
      $db->query(sprintf("DELETE FROM users_recurring_payments WHERE handle = 'subscribe' AND handle_id = %s", secure($plan_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* update monetization plan */
    $db->query(sprintf("UPDATE monetization_plans SET title = %s, price = %s, period_num = %s, period = %s, custom_description = %s, plan_order = %s, paypal_billing_plan = %s, stripe_billing_plan = %s WHERE plan_id = %s", secure($title), secure($price, 'float'), secure($period_num), secure($period), secure($custom_description), secure($plan_order), secure($paypal_billing_plan), secure($stripe_billing_plan), secure($plan_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update monetization plans */
    $this->update_monetization_plans($monetization_plan['node_id'], $monetization_plan['node_type']);
    /* return updated plan */
    return $this->get_monetization_plan($plan_id, true);
  }


  /**
   * delete_monetization_plan
   * 
   * @param integer $plan_id
   * @return void
   */
  public function delete_monetization_plan($plan_id)
  {
    global $db, $system;
    /* get monetization plan */
    $monetization_plan = $this->get_monetization_plan($plan_id);
    if (!$monetization_plan) {
      throw new Exception(__("This monetization plan is not available"));
    }
    /* delete all subscriptions to this plan */
    $db->query(sprintf("DELETE FROM subscribers WHERE plan_id = %s", secure($plan_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* remove all users recurring payments */
    $db->query(sprintf("DELETE FROM users_recurring_payments WHERE handle = 'subscribe' AND handle_id = %s", secure($plan_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* delete monetization plan */
    $db->query(sprintf("DELETE FROM monetization_plans WHERE plan_id = %s", secure($plan_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update monetization plans */
    $this->update_monetization_plans($monetization_plan['node_id'], $monetization_plan['node_type']);
    /* deactivate PayPal billing plan */
    if ($monetization_plan['paypal_billing_plan']) {
      paypal_deactivate_billing_plan($monetization_plan['paypal_billing_plan']);
    }
    /* deactivate Stripe billing plan */
    if ($monetization_plan['stripe_billing_plan']) {
      stripe_deactivate_billing_plan($monetization_plan['stripe_billing_plan']);
    }
  }


  /**
   * update_monetization_plans
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return void
   */
  public function update_monetization_plans($node_id = null, $node_type = 'profile')
  {
    global $db;
    $node_id = (isset($node_id)) ? $node_id : $this->_data['user_id'];
    switch ($node_type) {
      case 'profile':
        $table = "users";
        $id_file = "user_id";
        $price_field = "user_monetization_min_price";
        $monetization_plans_field = "user_monetization_plans";
        break;

      case 'page':
        $table = "pages";
        $id_file = "page_id";
        $price_field = "page_monetization_min_price";
        $monetization_plans_field = "page_monetization_plans";
        break;

      case 'group':
        $table = "groups";
        $id_file = "group_id";
        $price_field = "group_monetization_min_price";
        $monetization_plans_field = "group_monetization_plans";
        break;
    }
    /* get min price */
    $min_price = $db->query(sprintf("SELECT MIN(price) as min_price FROM monetization_plans WHERE node_id = %s AND node_type = %s", secure($node_id, 'int'), secure($node_type)))->fetch_assoc()['min_price'];
    /* get monetization plans count */
    $monetization_plans_count = $this->get_monetization_plans_count($node_id, $node_type);
    /* update min price & monetization plans count */
    $db->query(sprintf("UPDATE `$table` SET $price_field = %s, $monetization_plans_field = %s WHERE $id_file = %s", secure($min_price, 'float'), secure($monetization_plans_count, 'int'),  secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * has_free_monetization_plan
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return boolean|array
   */
  public function has_free_monetization_plan($node_id, $node_type)
  {
    global $db;
    $get_monetization_plans = $db->query(sprintf("SELECT * FROM monetization_plans WHERE node_id = %s AND node_type = %s AND price = '0'", secure($node_id, 'int'), secure($node_type))) or _error('SQL_ERROR_THROWEN');
    if ($get_monetization_plans->num_rows > 0) {
      return $get_monetization_plans->fetch_assoc();
    }
    return false;
  }


  /**
   * subscribe
   * 
   * @param integer $plan_id
   * @param integer $user_id
   * @return string
   */
  public function subscribe($plan_id, $user_id = null)
  {
    global $system, $db, $date;
    /* check monetization enabled */
    if (!$system['monetization_enabled']) {
      throw new Exception(__("The monetization system has been disabled by the admin"));
    }
    /* check user */
    $user_id = ($user_id) ? $user_id : $this->_data['user_id'];
    /* get monetization plan */
    $monetization_plan = $this->get_monetization_plan($plan_id, true);
    if (!$monetization_plan) {
      throw new Exception(__("This monetization plan is not available"));
    }
    /* get the node */
    switch ($monetization_plan['node_type']) {
      case 'profile':
        $node = $this->get_user($monetization_plan['node_id']);
        if (!$node) {
          _error(400);
        }
        $content_creator_id = $node['user_id'];
        $notification_action = 'subscribe_profile';
        $notification_title = $node['user_name'];
        $notification_url = $node['user_name'];
        $returned_link = "/" . $node['user_name'];
        break;

      case 'page':
        $node = $this->get_page($monetization_plan['node_id']);
        if (!$node) {
          _error(400);
        }
        $content_creator_id = $node['page_admin'];
        $notification_action = 'subscribe_page';
        $notification_title = $node['page_title'];
        $notification_url = $node['page_name'];
        $returned_link = "/pages/" . $node['page_name'];
        break;

      case 'group':
        $node = $this->get_group($monetization_plan['node_id']);
        if (!$node) {
          _error(400);
        }
        $content_creator_id = $node['group_admin'];
        $notification_action = 'subscribe_group';
        $notification_title = $node['group_title'];
        $notification_url = $node['group_name'];
        $returned_link = "/groups/" . $node['group_name'];
        break;

      default:
        _error(400);
        break;
    }
    /* check if the viewer is subscribed to this node */
    if ($this->is_subscribed($monetization_plan['node_id'], $monetization_plan['node_type'], $user_id)) {
      throw new Exception(__("Already subscribed to this") . " " . __($monetization_plan['node_type']));
    }
    /* add as subscriber */
    $db->query(sprintf("INSERT INTO subscribers (user_id, node_id, node_type, plan_id, time) VALUES (%s, %s, %s, %s, %s)", secure($user_id, 'int'),  secure($monetization_plan['node_id'], 'int'), secure($monetization_plan['node_type']), secure($plan_id), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* prepare commission */
    $content_price = $monetization_plan['price'];
    $commission = ($system['monetization_commission']) ? $content_price * ($system['monetization_commission'] / 100) : 0;
    $content_price = $content_price - $commission;
    /* update content creator monetization balance */
    $db->query(sprintf("UPDATE users SET user_monetization_balance = user_monetization_balance + %s WHERE user_id = %s", secure($content_price, 'float'), secure($content_creator_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log commission */
    $this->log_commission($content_creator_id, $commission, 'subscribe');
    /* notify the content creator */
    $this->post_notification(['to_user_id' => $content_creator_id, 'action' => $notification_action, 'node_type' => $notification_title, 'node_url' => $notification_url]);
    /* return */
    return $returned_link;
  }


  /**
   * unsubscribe
   * 
   * @param integer $plan_id
   * @param integer $user_id
   * @return void
   */
  public function unsubscribe($plan_id, $user_id = null)
  {
    global $db;
    /* check user */
    $user_id = ($user_id) ? $user_id : $this->_data['user_id'];
    /* get monetization plan */
    $monetization_plan = $this->get_monetization_plan($plan_id, true);
    if (!$monetization_plan) {
      throw new Exception(__("This monetization plan is not available"));
    }
    /* get the node */
    switch ($monetization_plan['node_type']) {
      case 'profile':
        $node = $this->get_user($monetization_plan['node_id']);
        if (!$node) {
          _error(400);
        }
        break;

      case 'page':
        $node = $this->get_page($monetization_plan['node_id']);
        if (!$node) {
          _error(400);
        }
        break;

      case 'group':
        $node = $this->get_group($monetization_plan['node_id']);
        if (!$node) {
          _error(400);
        }
        break;

      default:
        _error(400);
        break;
    }
    /* check if the viewer is subscribed to this node */
    if (!$this->is_subscribed($monetization_plan['node_id'], $monetization_plan['node_type'], $user_id)) {
      throw new Exception(__("You are not subscribed to this") . " " . __($monetization_plan['node_type']));
    }
    /* remove as subscriber */
    $db->query(sprintf("DELETE FROM subscribers WHERE user_id = %s AND node_id = %s AND node_type = %s AND plan_id = %s", secure($user_id, 'int'), secure($monetization_plan['node_id'], 'int'), secure($monetization_plan['node_type']), secure($plan_id))) or _error('SQL_ERROR_THROWEN');
    /* get recurring payment */
    $get_recurring_payment = $db->query(sprintf("SELECT * FROM users_recurring_payments WHERE handle = 'subscribe' AND handle_id = %s AND user_id = %s", secure($plan_id, 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_recurring_payment->num_rows > 0) {
      $recurring_payment = $get_recurring_payment->fetch_assoc();
      /* cancel the subscription */
      if ($recurring_payment['payment_gateway'] == "paypal") {
        paypal_cancel_subscription($recurring_payment['subscription_id']);
      }
      if ($recurring_payment['payment_gateway'] == "stripe") {
        stripe_cancel_subscription($recurring_payment['subscription_id']);
      }
      /* remove user recurring payment (if any) */
      $db->query(sprintf("DELETE FROM users_recurring_payments WHERE handle = 'subscribe' AND handle_id = %s AND user_id = %s", secure($plan_id, 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * is_subscribed
   * 
   * @param integer $node_id
   * @param string $node_type
   * @param integer $user_id
   * @return boolean
   */
  public function is_subscribed($node_id, $node_type = 'profile', $user_id = null)
  {
    global $db;
    /* check user */
    if (!$user_id) {
      if (!$this->_logged_in) return false;
      $user_id = $this->_data['user_id'];
    }
    /* check if the viewer is subscribed to this node */
    $get_subscribtion = $db->query(sprintf("SELECT * FROM subscribers WHERE node_type = %s AND node_id = %s AND user_id = %s", secure($node_type), secure($node_id, 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_subscribtion->num_rows == 0) {
      return false;
    }
    /* check the subscribtion period */
    $subscribtion = $get_subscribtion->fetch_assoc();
    $monetization_plan = $this->get_monetization_plan($subscribtion['plan_id'], true);
    if (!$monetization_plan) {
      return false;
    }
    /* the subscribtion period is not expired */
    if ($subscribtion['time'] > date('Y-m-d H:i:s', strtotime('-' . $monetization_plan['period_num'] . ' ' . $monetization_plan['period']))) {
      return true;
    }
    /* the subscribtion period is expired */
    $db->query(sprintf("DELETE FROM subscribers WHERE node_type = %s AND node_id = %s AND user_id = %s", secure($node_type), secure($node_id, 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    return false;
  }


  /**
   * has_sneak_peak
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return boolean
   */
  public function has_sneak_peak($node_id, $node_type = 'profile')
  {
    global $db;
    /* check if the viewer is sneak_peaked to this node */
    $get_sneak_peak = $db->query(sprintf("SELECT * FROM sneak_peaks WHERE node_type = %s AND node_id = %s AND user_id = %s", secure($node_type), secure($node_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_sneak_peak->num_rows == 0) {
      return false;
    }
    return true;
  }


  /**
   * sneak_peak
   * 
   * @param integer $node_id
   * @param string $node_type
   * @return void
   */
  public function sneak_peak($node_id, $node_type = 'profile')
  {
    global $db, $date;
    /* add sneak peak */
    $db->query(sprintf("INSERT INTO sneak_peaks (user_id, node_id, node_type, time) VALUES (%s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($node_id, 'int'), secure($node_type), secure($date))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * unlock_paid_post
   * 
   * @param integer $post_id
   * @param integer $user_id
   * @return void
   */
  public function unlock_paid_post($post_id, $user_id = null)
  {
    global $system, $db, $date;
    /* check user */
    $user_id = ($user_id) ? $user_id : $this->_data['user_id'];
    /* get post */
    $post = $this->get_post($post_id, false, true, true);
    if (!$post) {
      throw new Exception(__("This post is not available"));
    }
    /* unlock the post */
    $db->query(sprintf("INSERT INTO posts_paid (post_id, user_id, time) VALUES (%s, %s, %s)", secure($post_id, 'int'), secure($user_id, 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* prepare commission */
    $content_price = $post['post_price'];
    $commission = ($system['monetization_commission']) ? $content_price * ($system['monetization_commission'] / 100) : 0;
    $content_price = $content_price - $commission;
    /* update content creator monetization balance */
    $db->query(sprintf("UPDATE users SET user_monetization_balance = user_monetization_balance + %s WHERE user_id = %s", secure($content_price, 'float'), secure($post['author_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log commission */
    $this->log_commission($post['author_id'], $commission, 'paid_post');
    /* notify the content creator */
    $this->post_notification(['to_user_id' => $post['author_id'], 'from_user_id' => $user_id, 'action' => 'paid_post', 'node_url' => $post['post_id']]);
    /* return */
    return "/posts/" . $post['post_id'];
  }


  /**
   * is_user_paid_for_post
   * 
   * @param integer $post_id
   * @param string $user_id
   * @return boolean
   */
  public function is_user_paid_for_post($post_id, $user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_paid WHERE post_id = %s AND user_id = %s", secure($post_id, 'int'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }



  /* ------------------------------- */
  /* Recurring Payments */
  /* ------------------------------- */

  /**
   * insert_recurring_payments
   * 
   * @param string $payment_gateway
   * @param string $handle
   * @param integer $handle_id
   * @param string $subscription_id
   * @return void
   */
  public function insert_recurring_payments($payment_gateway, $handle, $handle_id, $subscription_id)
  {
    global $db, $date;
    /* check handle */
    if (!in_array($handle, ['packages', 'subscribe'])) {
      throw new Exception(__("Invalid handle"));
    }
    $db->query(sprintf("INSERT INTO users_recurring_payments (user_id, payment_gateway, handle, handle_id, subscription_id, time) VALUES (%s, %s, %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($payment_gateway), secure($handle), secure($handle_id, 'int'), secure($subscription_id), secure($date))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * get_recurring_payment
   * 
   * @param string $payment_gateway
   * @param string $subscription_id
   * @return array
   */
  public function get_recurring_payment($payment_gateway, $subscription_id)
  {
    global $db;
    $get_recurring_payment = $db->query(sprintf("SELECT * FROM users_recurring_payments WHERE payment_gateway = %s AND subscription_id = %s", secure($payment_gateway), secure($subscription_id))) or _error('SQL_ERROR_THROWEN');
    if ($get_recurring_payment->num_rows == 0) {
      return false;
    }
    return $get_recurring_payment->fetch_assoc();
  }



  /* ------------------------------- */
  /* Logger */
  /* ------------------------------- */

  /**
   * log_payment
   * 
   * @param int $user_id
   * @param float $amount
   * @param string $method
   * @param string $handle
   * @return void
   */
  public function log_payment($user_id, $amount, $method, $handle)
  {
    global $db, $date;
    $db->query(sprintf("INSERT INTO log_payments (user_id, amount, method, handle, time) VALUES (%s, %s, %s, %s, %s)", secure($user_id, 'int'), secure($amount, 'float'), secure($method), secure($handle), secure($date))) or _error('SQL_ERROR_THROWEN');
  }

  /**
   * log_commission
   * 
   * @param int $user_id
   * @param float $amount
   * @param string $handle
   * @return void
   */
  public function log_commission($user_id, $amount, $handle)
  {
    global $db, $date;
    if ($amount > 0) {
      $db->query(sprintf("INSERT INTO log_commissions (user_id, amount, handle, time) VALUES (%s, %s, %s, %s)", secure($user_id, 'int'), secure($amount, 'float'), secure($handle), secure($date))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * log_commission
   * 
   * @return void
   */
  public function log_session()
  {
    global $db, $date;
    /* log the session */
    $session_type = 'W';
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $user_agent = $_SERVER['HTTP_USER_AGENT'];
      if (strpos($user_agent, 'Android') !== false) {
        $session_type = 'A';
      }
      if (strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false) {
        $session_type = 'I';
      }
    }
    $user_ip = get_user_ip();
    $user_browser = get_user_browser();
    $user_os = get_user_os();
    /* check if session already exists using IP and user_agent */
    $get_session = $db->query(sprintf("SELECT COUNT(*) AS count FROM log_sessions WHERE session_ip = %s AND session_user_agent = %s", secure($user_ip), secure($user_agent))) or _error('SQL_ERROR_THROWEN');
    if ($get_session->fetch_assoc()['count'] == 0) {
      $db->query(sprintf("INSERT INTO log_sessions (session_date, session_type, session_ip, session_user_agent, user_browser, user_os) VALUES (%s, %s, %s, %s, %s, %s)", secure($date), secure($session_type), secure($user_ip), secure($user_agent), secure($user_browser), secure($user_os))) or _error('SQL_ERROR_THROWEN');
    }
  }



  /* ------------------------------- */
  /* Affiliates */
  /* ------------------------------- */

  /**
   * init_affiliates
   * 
   * @return void
   */
  public function init_affiliates()
  {
    if (!$this->_logged_in && isset($_GET['ref'])) {
      set_cookie($this->_cookie_user_referrer, $_GET['ref']);
    }
  }


  /**
   * affiliates_balance
   * 
   * @param string $type
   * @param integer $referee_id
   * @param integer $referrer_id
   * @param integer $package_price
   * @return void
   */
  private function process_affiliates($type, $referee_id, $referrer_id = null, $package_price = null)
  {
    global $db, $system;
    /* check if affiliates enabled */
    if (!$system['affiliates_enabled']) {
      return;
    }
    /* check if there is no referrer */
    if (!$referrer_id && !isset($_COOKIE[$this->_cookie_user_referrer])) {
      return;
    }
    /* get referrer */
    if (!$referrer_id && isset($_COOKIE[$this->_cookie_user_referrer])) {
      $get_referrer = $db->query(sprintf("SELECT user_id FROM users WHERE user_name = %s", secure($_COOKIE[$this->_cookie_user_referrer]))) or _error('SQL_ERROR_THROWEN');
      if ($get_referrer->num_rows == 0) {
        return;
      }
      $referrer_id = $get_referrer->fetch_assoc()['user_id'];
    }
    /* secure affiliates system (prevent new user to refer himself be set cookie manually) */
    if ($referrer_id == $referee_id) {
      return;
    }
    /* update referee */
    $db->query(sprintf("UPDATE users SET user_referrer_id = %s WHERE user_id = %s AND (user_referrer_id = '' OR user_referrer_id IS NULL)", secure($referrer_id, 'int'), secure($referee_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* check if the affiliate_type is correct */
    if ($system['affiliate_type'] != $type) {
      return;
    }
    /* set balance */
    if ($system['affiliate_type'] == "packages") {
      $balance = ($package_price * $system['affiliates_percentage']) / 100;
    } else {
      $balance = $system['affiliates_per_user'];
    }
    /* update referrer balance */
    $this->update_referrer_balance($referrer_id, $referee_id, $balance);
    /* points balance */
    $this->points_balance("add", $referrer_id, "referred", $referee_id);
  }


  /**
   * update_referrer_balance
   * 
   * @param integer $referrer_id
   * @param integer $referee_id
   * @param integer $balance
   * @param integer $iteration
   * @return void
   */
  private function update_referrer_balance($referrer_id, $referee_id, $balance, $iteration = 1)
  {
    global $db, $system;
    /* update current referrer balance */
    $db->query(sprintf("UPDATE users SET user_affiliate_balance = user_affiliate_balance + %s WHERE user_id = %s", secure($balance), secure($referrer_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* insert to users affiliates graph if not exists */
    $db->query(sprintf("INSERT INTO users_affiliates (referrer_id, referee_id) VALUES (%s, %s) ON DUPLICATE KEY UPDATE referrer_id = referrer_id", secure($referrer_id, 'int'), secure($referee_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* get parent referrer */
    if ($iteration < $system['affiliates_levels']) {
      $get_referrer = $db->query(sprintf("SELECT user_referrer_id FROM users WHERE user_referrer_id != '0' AND user_id = %s", secure($referrer_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_referrer->num_rows > 0) {
        $referrer = $get_referrer->fetch_assoc();
        $iteration++;
        /* update parent referrer balance */
        $this->update_referrer_balance($referrer['user_referrer_id'], $referee_id, $balance, $iteration);
      }
    }
  }


  /**
   * get_affiliates
   * 
   * @param integer $user_id
   * @param integer $offset
   * @return array
   */
  public function get_affiliates($user_id, $offset = 0)
  {
    global $db, $system;
    $affiliates = [];
    $offset *= $system['max_results'];
    $get_affiliates = $db->query(sprintf('SELECT user_id, user_name, user_firstname, user_lastname, user_gender, user_picture, user_subscribed, user_verified FROM users INNER JOIN users_affiliates ON users.user_id = users_affiliates.referee_id WHERE users_affiliates.referrer_id = %s LIMIT %s, %s', secure($user_id, 'int'), secure($offset, 'int', false), secure($system['max_results'], 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_affiliates->num_rows > 0) {
      while ($affiliate = $get_affiliates->fetch_assoc()) {
        $affiliate['user_picture'] = get_picture($affiliate['user_picture'], $affiliate['user_gender']);
        /* get the connection between the viewer & the target */
        $affiliate['connection'] = $this->connection($affiliate['user_id'], false);
        $affiliates[] = $affiliate;
      }
    }
    return $affiliates;
  }



  /* ------------------------------- */
  /* Points System */
  /* ------------------------------- */

  /**
   * points_balance
   * 
   * @param string $type
   * @param integer $user_id
   * @param string $node_type
   * @param integer $node_id
   * @return void
   */
  public function points_balance($type, $user_id, $node_type, $node_id = null)
  {
    global $db, $system;
    /* check if points enabled */
    if (!$system['points_enabled']) {
      return;
    }
    switch ($node_type) {
      case 'post':
        $points_per_node = $system['points_per_post'];
        break;

      case 'post_view':
        $points_per_node = $system['points_per_post_view'];
        break;

      case 'comment':
        $points_per_node = $system['points_per_comment'];
        break;

      case 'posts_reactions':
      case 'posts_photos_reactions':
      case 'posts_comments_reactions':
        $points_per_node = $system['points_per_reaction'];
        break;

      case 'follow':
        $points_per_node = $system['points_per_follow'];
        break;

      case 'referred':
        $points_per_node = $system['points_per_referred'];
        break;
    }
    switch ($type) {
      case 'add':
        /* check user daily limits */
        if ($this->get_remaining_points($user_id) == 0) {
          return;
        }
        /* add points */
        $db->query(sprintf("UPDATE users SET user_points = user_points + %s WHERE user_id = %s", secure($points_per_node, 'float'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update the node as earned */
        switch ($node_type) {
          case 'post':
            $db->query(sprintf("UPDATE posts SET points_earned = '1' WHERE post_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'comment':
            $db->query(sprintf("UPDATE posts_comments SET points_earned = '1' WHERE comment_id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'posts_reactions':
            $db->query(sprintf("UPDATE posts_reactions SET points_earned = '1' WHERE id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'posts_photos_reactions':
            $db->query(sprintf("UPDATE posts_photos_reactions SET points_earned = '1' WHERE id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'posts_comments_reactions':
            $db->query(sprintf("UPDATE posts_comments_reactions SET points_earned = '1' WHERE id = %s", secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'follow':
            $db->query(sprintf("UPDATE followings SET points_earned = '1' WHERE following_id = %s AND user_id = %s", secure($user_id, 'int'), secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');

          case 'referred':
            $db->query(sprintf("UPDATE users SET points_earned = '1' WHERE user_referrer_id = %s AND user_id = %s", secure($user_id, 'int'), secure($node_id, 'int'))) or _error('SQL_ERROR_THROWEN');
            break;
        }
        break;

      case 'delete':
        /* delete points */
        $db->query(sprintf('UPDATE users SET user_points = IF(user_points-%1$s<=0,0,user_points-%1$s) WHERE user_id = %2$s', secure($points_per_node, 'float'), secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;
    }
  }


  /**
   * get_remaining_points
   * 
   * @param integer $user_id
   * @return integer
   */
  public function get_remaining_points($user_id)
  {
    global $db, $system;
    /* posts */
    $get_posts = $db->query(sprintf("SELECT COUNT(*) as count FROM posts WHERE points_earned = '1' AND posts.time >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND user_id = %s AND user_type = 'user'", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $total_posts_points = $get_posts->fetch_assoc()['count'] * $system['points_per_post'];
    /* comments */
    $get_comments = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_comments WHERE points_earned = '1' AND posts_comments.time >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND user_id = %s AND user_type = 'user'", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $total_comments_points = $get_comments->fetch_assoc()['count'] * $system['points_per_comment'];
    /* reactions */
    $total_reactions_points = 0;
    $get_reactions_posts = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_reactions WHERE points_earned = '1' AND posts_reactions.reaction_time >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $total_reactions_points += $get_reactions_posts->fetch_assoc()['count'];
    $get_reactions_photos = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_photos_reactions WHERE points_earned = '1' AND posts_photos_reactions.reaction_time >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $total_reactions_points += $get_reactions_photos->fetch_assoc()['count'];
    $get_reactions_comments = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_comments_reactions WHERE points_earned = '1' AND posts_comments_reactions.reaction_time >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $total_reactions_points += $get_reactions_comments->fetch_assoc()['count'];
    $total_reactions_points *= $system['points_per_reaction'];
    /* followers */
    $get_followers = $db->query(sprintf("SELECT COUNT(*) as count FROM followings WHERE points_earned = '1' AND followings.time >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND following_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $total_followers_points = $get_followers->fetch_assoc()['count'] * $system['points_per_follow'];
    /* affiliates */
    $get_affiliates = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE points_earned = '1' AND user_registered >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND user_referrer_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $total_affiliates_points = $get_affiliates->fetch_assoc()['count'] * $system['points_per_referred'];
    /* total daily points*/
    $total_daily_points = $total_posts_points + $total_comments_points + $total_reactions_points + $total_followers_points + $total_affiliates_points;
    /* total daily limit */
    $total_daily_limit = ($system['packages_enabled'] && $this->_data['user_subscribed']) ? $system['points_limit_pro'] : $system['points_limit_user'];
    /* return remaining points */
    $remaining_points = $total_daily_limit - $total_daily_points;
    return ($remaining_points > 0) ? $remaining_points : 0;
  }


  /**
   * reset_all_users_points
   * 
   * @return void
   */
  public function reset_all_users_points()
  {
    global $db;
    $db->query("UPDATE users SET user_points = '0'") or _error('SQL_ERROR_THROWEN');
    /* clear posts */
    $db->query("UPDATE posts SET points_earned = '0'") or _error('SQL_ERROR_THROWEN');
    /* clear posts comments */
    $db->query("UPDATE posts_comments SET points_earned = '0'") or _error('SQL_ERROR_THROWEN');
    /* clear posts reactions */
    $db->query("UPDATE posts_reactions SET points_earned = '0'") or _error('SQL_ERROR_THROWEN');
    /* clear photos reactions */
    $db->query("UPDATE posts_photos_reactions SET points_earned = '0'") or _error('SQL_ERROR_THROWEN');
    /* clear comments reactions */
    $db->query("UPDATE posts_comments_reactions SET points_earned = '0'") or _error('SQL_ERROR_THROWEN');
    /* clear followers */
    $db->query("UPDATE followings SET points_earned = '0'") or _error('SQL_ERROR_THROWEN');
    /* clear affiliates */
    $db->query("UPDATE users SET points_earned = '0'") or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Funding */
  /* ------------------------------- */

  /**
   * get_fundings
   * 
   * @param array $args
   * @return array
   */
  public function get_fundings($args = [])
  {
    global $db, $system;
    /* initialize arguments */
    $offset = !isset($args['offset']) ? 0 : $args['offset'];
    $results = !isset($args['results']) ? $system['max_results_even'] : $args['results'];
    /* initialize vars */
    $posts = [];
    $offset *= $results;
    /* get posts */
    $get_posts = $db->query(sprintf("SELECT posts.post_id FROM posts INNER JOIN posts_funding ON posts.post_id = posts_funding.post_id INNER JOIN users ON posts.user_id = users.user_id and posts.user_type = 'user' WHERE posts.post_type = 'funding' AND (posts.pre_approved = '1' OR posts.has_approved = '1') ORDER BY posts.post_id DESC LIMIT %s, %s", secure($offset, 'int', false), secure($results, 'int', false))) or _error('SQL_ERROR_THROWEN');
    if ($get_posts->num_rows > 0) {
      while ($post = $get_posts->fetch_assoc()) {
        $post = $this->get_post($post['post_id']);
        if ($post) {
          $posts[] = $post;
        }
      }
    }
    return $posts;
  }

  /**
   * funding_donation
   * 
   * @param integer $post_id
   * @param integer $donation_amount
   * @param integer $donor_id
   * @return void
   */
  public function funding_donation($post_id, $donation_amount, $donor_id = null)
  {
    global $db, $system, $date;
    /* (check|get) post */
    $post = $this->get_post($post_id);
    if (!$post) {
      _error(400);
    }
    /* check doner */
    $donor_id = ($donor_id) ? $donor_id : $this->_data['user_id'];
    /* prepare commission */
    $commission = ($system['funding_commission']) ? $donation_amount * ($system['funding_commission'] / 100) : 0;
    $donation_amount = $donation_amount - $commission;
    /* update funding request */
    $db->query(sprintf("UPDATE posts_funding SET raised_amount = raised_amount + %s, total_donations = total_donations + 1 WHERE post_id = %s", secure($donation_amount), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* insert donor */
    $db->query(sprintf("INSERT INTO posts_funding_donors (user_id, post_id, donation_amount, donation_time) VALUES (%s, %s, %s, %s)", secure($donor_id, 'int'), secure($post_id, 'int'), secure($donation_amount), secure($date)))  or _error('SQL_ERROR_THROWEN');
    /* increase target user funding balance */
    $db->query(sprintf("UPDATE users SET user_funding_balance = user_funding_balance + %s WHERE user_id = %s", secure($donation_amount), secure($post['author_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* log commission */
    $this->log_commission($post['author_id'], $commission, 'donate');
    /* send notification to the post author */
    $this->post_notification(['to_user_id' => $post['author_id'], 'action' => 'funding_donation', 'node_type' => $donation_amount, 'node_url' => $post_id]);
  }



  /* ------------------------------- */
  /* Shopping */
  /* ------------------------------- */

  /**
   * get_cart
   * 
   * @return array
   */
  public function get_cart()
  {
    global $db;
    $cart = [];
    $cart['items'] = [];
    $cart['total'] = 0;
    $get_cart = $db->query(sprintf("SELECT * FROM shopping_cart WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($get_cart->num_rows > 0) {
      while ($cart_item = $get_cart->fetch_assoc()) {
        $cart_item['post'] = $this->get_post($cart_item['product_post_id']);
        if (!$cart_item['post']) continue;
        $cart['total'] += $cart_item['post']['product']['price'] * $cart_item['quantity'];
        $cart['items'][] = $cart_item;
      }
    }
    return $cart;
  }


  /**
   * checkout_cart
   * 
   * @param int $shipping_address
   * @return void
   */
  public function checkout_cart($shipping_address)
  {
    global $db, $system, $date;
    /* check if market enabled */
    if (!$system['market_enabled']) {
      throw new AuthorizationException(__("The market module has been disabled by the admin"));
    }
    /* check if this shipping address belongs to the user */
    $check_address = $db->query(sprintf("SELECT COUNT(*) AS count FROM users_addresses WHERE address_id = %s AND user_id = %s", secure($shipping_address, 'int'), secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($check_address->fetch_assoc()['count'] == 0) {
      throw new AuthorizationException(__("You are not authorized to do this action"));
    }
    /* get cart */
    $cart = $this->get_cart();
    /* check if cart is empty */
    if (count($cart['items']) == 0) {
      throw new ValidationException(__("Your cart is empty"));
    }
    /* check if the user has enough balance */
    if ($this->_data['user_wallet_balance'] < $cart['total']) {
      throw new ValidationException(__("There is no enough credit in your wallet, Recharge your wallet to continue"));
    }
    /* checkout */
    $orders = [];
    foreach ($cart['items'] as $cart_item) {
      /* check if the product is still available */
      if ($cart_item['post']['product']['quantity'] < $cart_item['quantity']) {
        throw new ValidationException(__("The product %s is not available anymore", $cart_item['post']['product']['name']));
      }
      /* notify the seller if the product quantity is 0 */
      if ($cart_item['post']['product']['quantity'] - $cart_item['quantity'] <= 0) {
        $this->post_notification(['to_user_id' => $cart_item['post']['author_id'], 'action' => 'market_outofstock', 'node_url' => $cart_item['post']['post_id']]);
      }
      /* check if the item is digital product */
      $is_digital = $cart_item['post']['product']['is_digital'];
      $order_hash = get_hash_number();
      $order_index = ($is_digital) ? $order_hash : $cart_item['post']['author_id'];
      /* check if there is already an order for this seller */
      if (isset($orders[$order_index])) {
        /* get order id */
        $order_id = $orders[$order_index]['order_id'];
      } else {
        /* create new order */
        $db->query(sprintf("INSERT INTO orders (order_hash, is_digital, seller_id, buyer_id, buyer_address_id, commission, insert_time, update_time) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", secure($order_hash), secure($is_digital), secure($cart_item['post']['author_id'], 'int'), secure($this->_data['user_id'], 'int'), secure($shipping_address, 'int'), secure($system['market_commission'], 'float'), secure($date), secure($date))) or _error("SQL_ERROR_THROWEN");
        /* get order id */
        $order_id = $db->insert_id;
        /* add order to the orders array */
        $orders[$order_index]['order_id'] = $order_id;
        $orders[$order_index]['order_hash'] = $order_hash;
        $orders[$order_index]['is_digital'] = $is_digital;
        $orders[$order_index]['seller_id'] = $cart_item['post']['author_id'];
      }
      /* add order items */
      $db->query(sprintf("INSERT INTO orders_items (order_id, product_post_id, quantity, price) VALUES (%s, %s, %s, %s)", secure($order_id, 'int'), secure($cart_item['product_post_id'], 'int'), secure($cart_item['quantity'], 'int'), secure($cart_item['post']['product']['price'], 'float'))) or _error("SQL_ERROR_THROWEN");
      /* update product quantity */
      $db->query(sprintf("UPDATE posts_products SET quantity = quantity - %s WHERE product_id = %s", secure($cart_item['quantity'], 'int'), secure($cart_item['post']['product']['product_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
      /* update order sub total */
      $item_total_price = $cart_item['post']['product']['price'] * $cart_item['quantity'];
      $orders[$order_index]['sub_total'] += $item_total_price;
    }
    /* loop through orders */
    foreach ($orders as $order) {
      /* update order sub total */
      $db->query(sprintf("UPDATE orders SET sub_total = %s WHERE order_id = %s", secure($order['sub_total'], 'float'), secure($order['order_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
      /* send notification to the seller */
      $this->post_notification(['to_user_id' => $order['seller_id'], 'action' => 'market_order', 'node_url' => $order['order_hash']]);
      /* check if digital order -> update order as delivered */
      if ($order['is_digital']) {
        $this->update_order($order['order_id'], 'delivered', '', '', true);
      }
    }
    /* update buyer's wallet balance */
    $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance - %s WHERE user_id = %s", secure($cart['total'], 'float'), secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    /* wallet transaction */
    $this->wallet_set_transaction($this->_data['user_id'], 'market', 0, $cart['total'], 'out');
    /* empty the cart */
    $db->query(sprintf("DELETE FROM shopping_cart WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
  }


  /**
   * add_to_cart
   * 
   * @param int $post_id
   * @return void
   */
  public function add_to_cart($post_id)
  {
    global $db, $system;
    /* check if market enabled */
    if (!$system['market_enabled']) {
      throw new AuthorizationException(__("The market module has been disabled by the admin"));
    }
    /* check if the post is a product */
    $check_product = $db->query(sprintf("SELECT COUNT(*) AS count FROM posts WHERE post_id = %s AND post_type = 'product'", secure($post_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($check_product->fetch_assoc()['count'] == 0) {
      throw new ValidationException(__("This is not a product"));
    }
    /* check if the product is already in the cart */
    $check_cart = $db->query(sprintf("SELECT COUNT(*) AS count FROM shopping_cart WHERE user_id = %s AND product_post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($check_cart->fetch_assoc()['count'] > 0) {
      return;
    }
    /* add to cart */
    $db->query(sprintf("INSERT INTO shopping_cart (user_id, product_post_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error("SQL_ERROR_THROWEN");
  }


  /**
   * update_cart
   * 
   * @param int $post_id
   * @param int $quantity
   * @return void
   */
  public function update_cart($post_id, $quantity)
  {
    global $db;
    /* check if the product is already in the cart */
    $check_cart = $db->query(sprintf("SELECT COUNT(*) AS count FROM shopping_cart WHERE user_id = %s AND product_post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($check_cart->fetch_assoc()['count'] == 0) {
      throw new ValidationException(__("This product is not in the cart"));
    }
    /* check if the quantity is valid */
    if ($quantity <= 0) {
      throw new ValidationException(__("Invalid quantity"));
    }
    /* get the product */
    $product = $this->get_post($post_id);
    if (!$product) {
      throw new ValidationException(__("This product is not exists"));
    }
    /* check if the quantity is available */
    if ($quantity > $product['product']['quantity']) {
      throw new ValidationException(__("The quantity you entered is not available"));
    }
    /* update cart */
    $db->query(sprintf("UPDATE shopping_cart SET quantity = %s WHERE user_id = %s AND product_post_id = %s", secure($quantity, 'int'), secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error("SQL_ERROR_THROWEN");
  }


  /**
   * remove_from_cart
   * 
   * @param int $post_id
   * @return void
   */
  public function remove_from_cart($post_id)
  {
    global $db;
    $db->query(sprintf("DELETE FROM shopping_cart WHERE user_id = %s AND product_post_id = %s", secure($this->_data['user_id'], 'int'), secure($post_id, 'int'))) or _error("SQL_ERROR_THROWEN");
  }


  /**
   * get_orders
   * 
   * @param array $args
   * @return array
   */
  public function get_orders($args = [])
  {
    global $db, $system;
    $orders = [];
    $sales_orders = (isset($args['sales_orders']) && $args['sales_orders']) ? true : false;
    $offset = (isset($args['offset'])) ? $args['offset'] : 0;
    $query = (isset($args['query'])) ? $args['query'] : false;
    /* prepare query */
    $offset *= $system['max_results'];
    $where_query = ($sales_orders) ? sprintf("seller_id = %s", secure($this->_data['user_id'], 'int')) : sprintf("buyer_id = %s", secure($this->_data['user_id'], 'int'));
    if ($query) {
      $where_query .= sprintf(" AND order_hash LIKE %s", secure($query, 'search'));
    }
    $limit_statement = sprintf("LIMIT %s, %s", secure($offset, 'int', false), secure($system['max_results'], 'int', false));
    $get_orders = $db->query("SELECT orders.*, users_addresses.*, users.user_name, users.user_firstname, users.user_lastname FROM orders INNER JOIN users_addresses ON orders.buyer_address_id = users_addresses.address_id INNER JOIN users ON orders.buyer_id = users.user_id WHERE $where_query ORDER BY orders.order_id DESC " . $limit_statement) or _error("SQL_ERROR_THROWEN");
    if ($get_orders->num_rows > 0) {
      $order['items'] = [];
      $order['total_commission'] = 0;
      $order['final_price'] = 0;
      while ($order = $get_orders->fetch_assoc()) {
        /* prepare buyer's name */
        $order['buyer_fullname'] = ($system['show_usernames_enabled']) ? $order['user_name'] : $order['user_firstname'] . ' ' . $order['user_lastname'];
        /* prepare total commission */
        $order['total_commission'] = $order['sub_total'] * ($order['commission'] / 100);
        /* prepare final price */
        $order['final_price'] = $order['sub_total'] - $order['total_commission'];
        /* get order items */
        $get_order_items = $db->query(sprintf("SELECT * FROM orders_items WHERE order_id = %s", secure($order['order_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
        while ($order_item = $get_order_items->fetch_assoc()) {
          $order_item['post'] = $this->get_post($order_item['product_post_id']);
          if (!$order_item['post']) continue;
          $order['items'][] = $order_item;
        }
        $orders[] = $order;
      }
    }
    return $orders;
  }


  /**
   * get_order
   * 
   * @param int $order_id
   * @param bool $for_invoice
   * @param bool $for_admin
   * @return array
   */
  public function get_order($order_id, $for_invoice = false, $for_admin = false)
  {
    global $db, $system;
    /* get order */
    if (!$for_admin) {
      $where_query = (!$for_invoice) ? "AND orders.status != 'canceled' AND orders.status != 'delivered'" : "";
      $where_query .= " AND (orders.seller_id = " . secure($this->_data['user_id'], 'int') . " OR orders.buyer_id = " . secure($this->_data['user_id'], 'int') . ") ";
    }
    $get_order = $db->query(sprintf("SELECT orders.*, users_addresses.*, users.user_name, users.user_firstname, users.user_lastname FROM orders INNER JOIN users_addresses ON orders.buyer_address_id = users_addresses.address_id INNER JOIN users ON orders.buyer_id = users.user_id WHERE orders.order_id = %s $where_query", secure($order_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($get_order->num_rows == 0) {
      _error(400);
    }
    $order = $get_order->fetch_assoc();
    /* prepare buyer's name */
    $order['buyer_fullname'] = ($system['show_usernames_enabled']) ? $order['user_name'] : $order['user_firstname'] . ' ' . $order['user_lastname'];
    /* get seller info */
    $order['seller'] = $this->get_user($order['seller_id']);
    /* prepare seller's name */
    $order['seller_fullname'] = ($system['show_usernames_enabled']) ? $order['seller']['user_name'] : $order['seller']['user_firstname'] . ' ' . $order['seller']['user_lastname'];
    /* prepare total commission */
    $order['total_commission'] = $order['sub_total'] * ($order['commission'] / 100);
    /* prepare final price */
    $order['final_price'] = $order['sub_total'] - $order['total_commission'];
    /* get order items */
    $get_order_items = $db->query(sprintf("SELECT * FROM orders_items WHERE order_id = %s", secure($order['order_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    while ($order_item = $get_order_items->fetch_assoc()) {
      $order_item['post'] = $this->get_post($order_item['product_post_id']);
      if (!$order_item['post']) continue;
      $order['items'][] = $order_item;
    }
    return $order;
  }


  /**
   * update_order
   * 
   * @param int $order_id
   * @param string $status
   * @param string $tracking_link
   * @param string $tracking_number
   * @param bool $for_admin
   * @return void
   */
  public function update_order($order_id, $status, $tracking_link = null, $tracking_number = null, $for_admin = false)
  {
    global $db, $system;
    /* get order */
    $order = $this->get_order($order_id, false, $for_admin);
    $tracking_link = ($tracking_link) ? $tracking_link : $order['tracking_link'];
    $tracking_number = ($tracking_number) ? $tracking_number : $order['tracking_number'];
    /* update order */
    switch ($status) {
      case 'canceled':
        /* update product quantity */
        foreach ($order['items'] as $item) {
          $db->query(sprintf("UPDATE posts_products SET quantity = quantity + %s WHERE product_id = %s", secure($item['quantity'], 'int'), secure($item['post']['product']['product_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
        }
        /* update buyer's wallet balance */
        $db->query(sprintf("UPDATE users SET user_wallet_balance = user_wallet_balance + %s WHERE user_id = %s", secure($order['sub_total'], 'float'), secure($order['buyer_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
        /* wallet transaction */
        $this->wallet_set_transaction($order['buyer_id'], 'market', 0, $order['sub_total'], 'in');
        break;

      case 'delivered':
        /* check if the seller doing this action */
        if (!$for_admin && $order['seller_id'] == $this->_data['user_id']) {
          _error(400);
        }
        /* prepare commission */
        $commission = ($system['market_commission']) ? $order['sub_total'] * ($system['market_commission'] / 100) : 0;
        $sub_total_after_commission = $order['sub_total'] - $commission;
        /* add to the seller's market balance */
        $db->query(sprintf("UPDATE users SET user_market_balance = user_market_balance + %s WHERE user_id = %s", secure($sub_total_after_commission, 'float'), secure($order['seller_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
        /* log commission */
        $this->log_commission($order['seller_id'], $commission, 'market');
        break;
    }
    $db->query(sprintf("UPDATE orders SET status = %s, tracking_link = %s, tracking_number = %s WHERE order_id = %s", secure($status), secure($tracking_link), secure($tracking_number), secure($order_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    /* send notification to the buyer */
    if ($order['tracking_link'] != $tracking_link || $order['tracking_number'] != $tracking_number) {
      $this->post_notification(['to_user_id' => $order['buyer_id'], 'action' => 'market_order_tracking_updated', 'node_url' => $order['order_hash']]);
    }
    /* send notification to the buyer|seller */
    if ($order['status'] != $status) {
      if ($status == "delivered") {
        /* to the seller */
        $this->post_notification(['to_user_id' => $order['seller_id'], 'action' => 'market_order_delivered', 'node_url' => $order['order_hash']]);
      } else {
        /* to the buyer */
        $this->post_notification(['to_user_id' => $order['buyer_id'], 'action' => 'market_order_status_updated', 'node_url' => $order['order_hash']]);
      }
    }
  }


  /**
   * get_orders_count
   * 
   * @param bool $sales_orders
   * @return int
   */
  public function get_orders_count($sales_orders = false)
  {
    global $db;
    /* prepare query */
    $where_query = ($sales_orders) ? "seller_id = %s" : "buyer_id = %s";
    $get_orders = $db->query(sprintf("SELECT * FROM orders WHERE $where_query", secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    return $get_orders->num_rows;
  }


  /**
   * get_seller_monthly_sales
   * 
   * @return int
   */
  public function get_seller_monthly_sales()
  {
    global $db;
    $get_sales = $db->query(sprintf("SELECT SUM(sub_total - sub_total * (commission/100)) AS total_sales FROM orders WHERE seller_id = %s AND insert_time >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND status = 'delivered'", secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    $sales = $get_sales->fetch_assoc();
    return $sales['total_sales'];
  }


  /**
   * get_user_products_count
   * 
   * @return int
   */
  public function get_user_products_count()
  {
    global $db;
    $get_products = $db->query(sprintf("SELECT COUNT(*) AS count FROM posts WHERE post_type = 'product' AND user_type = 'user' AND user_id = %s", secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    return $get_products->fetch_assoc()['count'];
  }



  /* ------------------------------- */
  /* Developers Apps */
  /* ------------------------------- */

  /**
   * get_apps
   * 
   * @return array
   */
  public function get_apps()
  {
    global $db;
    $apps = [];
    $get_apps = $db->query(sprintf("SELECT * FROM developers_apps WHERE app_user_id = %s ORDER BY app_id DESC", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_apps->num_rows > 0) {
      while ($app = $get_apps->fetch_assoc()) {
        $apps[] = $app;
      }
    }
    return $apps;
  }


  /**
   * get_user_apps
   * 
   * @return array
   */
  public function get_user_apps()
  {
    global $db;
    $apps = [];
    $get_apps = $db->query(sprintf("SELECT developers_apps.* FROM developers_apps_users INNER JOIN developers_apps ON developers_apps_users.app_id = developers_apps.app_id WHERE developers_apps_users.user_id = %s ORDER BY developers_apps_users.id DESC", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_apps->num_rows > 0) {
      while ($app = $get_apps->fetch_assoc()) {
        $apps[] = $app;
      }
    }
    return $apps;
  }


  /**
   * get_app
   * 
   * @param integer $app_auth_id
   * @return array
   */
  public function get_app($app_auth_id)
  {
    global $db;
    $get_app = $db->query(sprintf("SELECT developers_apps.*, developers_apps_categories.category_name FROM developers_apps LEFT JOIN developers_apps_categories ON developers_apps.app_category_id = developers_apps_categories.category_id WHERE developers_apps.app_auth_id = %s", secure($app_auth_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_app->num_rows == 0) {
      return false;
    }
    $app = $get_app->fetch_assoc();
    return $app;
  }


  /**
   * create_app
   * 
   * @param array $args
   * @return void
   */
  public function create_app($args = [])
  {
    global $db, $system, $date;
    /* check if developers apps enabled */
    if (!$system['developers_apps_enabled']) {
      throw new Exception(__("The developers module has been disabled by the admin"));
    }
    /* validate app name */
    if (is_empty($args['app_name'])) {
      throw new Exception(__("You have to enter the App name"));
    }
    /* validate app domain */
    if (is_empty($args['app_domain'])) {
      throw new Exception(__("You have to enter the App domain"));
    }
    /* validate app redirect_url */
    if (is_empty($args['app_redirect_url'])) {
      throw new Exception(__("You have to enter the App redirect URL"));
    }
    if (!valid_url($args['app_redirect_url'])) {
      throw new Exception(__("Please enter a valid App redirect URL"));
    }
    /* validate app description */
    if (is_empty($args['app_description'])) {
      throw new Exception(__("You have to enter the App description"));
    }
    if (strlen($args['app_description']) > 200) {
      throw new Exception(__("Your App description is more than 200 characters"));
    }
    /* validate category */
    if (is_empty($args['app_category'])) {
      throw new Exception(__("You must select valid category for your App"));
    }
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM developers_apps_categories WHERE category_id = %s", secure($args['app_category'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You must select valid category for your App"));
    }
    /* validate app icon */
    if (is_empty($args['app_icon'])) {
      throw new Exception(__("You have to enter the App icon"));
    }
    /* generate app auth id & secret */
    $app_auth_id = get_hash_number();
    $app_auth_secret = get_hash_token();
    /* insert new app */
    $db->query(sprintf(
      "INSERT INTO developers_apps (
            app_user_id, 
            app_category_id, 
            app_auth_id, 
            app_auth_secret, 
            app_name, 
            app_domain, 
            app_redirect_url, 
            app_description, 
            app_icon, 
            app_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
      secure($this->_data['user_id'], 'int'),
      secure($args['app_category'], 'int'),
      secure($app_auth_id),
      secure($app_auth_secret),
      secure($args['app_name']),
      secure($args['app_domain']),
      secure($args['app_redirect_url']),
      secure($args['app_description']),
      secure($args['app_icon']),
      secure($date)
    )) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * edit_app
   * 
   * @param integer $app_auth_id
   * @param array $args
   * @return void
   */
  public function edit_app($app_auth_id, $args)
  {
    global $db, $system;
    /* check if developers apps enabled */
    if (!$system['developers_apps_enabled']) {
      throw new Exception(__("The developers module has been disabled by the admin"));
    }
    /* (check&get) app */
    $get_app = $db->query(sprintf("SELECT * FROM developers_apps WHERE app_auth_id = %s", secure($app_auth_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_app->num_rows == 0) {
      _error(403);
    }
    $app = $get_app->fetch_assoc();
    /* check permission */
    $can_edit = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_edit = true;
      /* viewer is the creator of app */
    } elseif ($this->_data['user_id'] == $app['app_user_id']) {
      $can_edit = true;
    }
    /* edit the app */
    if (!$can_edit) {
      _error(403);
    }
    /* validate app name */
    if (is_empty($args['app_name'])) {
      throw new Exception(__("You have to enter the app name"));
    }
    /* validate app domain */
    if (is_empty($args['app_domain'])) {
      throw new Exception(__("You have to enter the app domain"));
    }
    /* validate app redirect_url */
    if (is_empty($args['app_redirect_url'])) {
      throw new Exception(__("You have to enter the app redirect URL"));
    }
    if (!valid_url($args['app_redirect_url'])) {
      throw new Exception(__("Please enter a valid app redirect URL"));
    }
    /* validate app description */
    if (is_empty($args['app_description'])) {
      throw new Exception(__("You have to enter the app description"));
    }
    if (strlen($args['app_description']) > 200) {
      throw new Exception(__("Your App description is more than 200 characters"));
    }
    /* validate category */
    if (is_empty($args['app_category'])) {
      throw new Exception(__("You must select valid category for your App"));
    }
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM developers_apps_categories WHERE category_id = %s", secure($args['app_category'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You must select valid category for your App"));
    }
    /* validate app icon */
    if (is_empty($args['app_icon'])) {
      throw new Exception(__("You have to enter the app icon"));
    }
    /* update the app */
    $db->query(sprintf(
      "UPDATE developers_apps SET 
            app_category_id = %s, 
            app_name = %s, 
            app_domain = %s, 
            app_redirect_url = %s, 
            app_description = %s, 
            app_icon = %s  WHERE app_auth_id = %s",
      secure($args['app_category'], 'int'),
      secure($args['app_name']),
      secure($args['app_domain']),
      secure($args['app_redirect_url']),
      secure($args['app_description']),
      secure($args['app_icon']),
      secure($app_auth_id, 'int')
    )) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * delete_app
   * 
   * @param integer $app_auth_id
   * @return void
   */
  public function delete_app($app_auth_id)
  {
    global $db, $system;
    /* check if developers apps enabled */
    if ($this->_data['user_group'] >= 3 && !$system['developers_apps_enabled']) {
      throw new Exception(__("The developers module has been disabled by the admin"));
    }
    /* (check&get) app */
    $get_app = $db->query(sprintf("SELECT * FROM developers_apps WHERE app_auth_id = %s", secure($app_auth_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_app->num_rows == 0) {
      _error(403);
    }
    $app = $get_app->fetch_assoc();
    // delete app
    $can_delete = false;
    /* viewer is (admin|moderator) */
    if ($this->_data['user_group'] < 3) {
      $can_delete = true;
      /* viewer is the creator of app */
    } elseif ($this->_data['user_id'] == $app['app_user_id']) {
      $can_delete = true;
    }
    /* delete the app */
    if (!$can_delete) {
      _error(403);
    }
    $db->query(sprintf("DELETE FROM developers_apps WHERE app_auth_id = %s", secure($app_auth_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    $db->query(sprintf("DELETE FROM developers_apps_users WHERE app_id = %s", secure($app['app_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * delete_user_app
   * 
   * @param integer $app_auth_id
   * @return void
   */
  public function delete_user_app($app_auth_id)
  {
    global $db, $system;
    /* check if developers apps enabled */
    if (!$system['developers_apps_enabled']) {
      throw new Exception(__("The developers module has been disabled by the admin"));
    }
    /* (check&get) app */
    $get_app = $db->query(sprintf("SELECT * FROM developers_apps WHERE app_auth_id = %s", secure($app_auth_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_app->num_rows > 0) {
      $app = $get_app->fetch_assoc();
      /* delete the user-app connection */
      $db->query(sprintf("DELETE FROM developers_apps_users WHERE app_id = %s AND user_id = %s", secure($app['app_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * oauth_app
   * 
   * @param integer $app_auth_id
   * @param boolean $approving
   * @return void
   */
  public function oauth_app($app_auth_id, $approving = false)
  {
    global $db, $system, $smarty;
    /* check if developers apps enabled */
    if (!$system['developers_apps_enabled']) {
      if ($approving) {
        throw new Exception(__("The developers module has been disabled by the admin"));
      } else {
        _error(__('Error'), __("The developers module has been disabled by the admin"));
      }
    }
    /* (check&get) app */
    $app = $this->get_app($app_auth_id);
    if (!$app) {
      if ($approving) {
        throw new Exception(__("We're sorry, but the application you're trying to use doesn't exist or has been disabled"));
      } else {
        _error(__('Error'), __("We're sorry, but the application you're trying to use doesn't exist or has been disabled"));
      }
    }
    /* check if user OAuthenticated this app before */
    $check_user_connection = $db->query(sprintf("SELECT COUNT(*) as count FROM developers_apps_users WHERE app_id = %s AND user_id = %s", secure($app['app_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check_user_connection->fetch_assoc()['count'] > 0) {
      /* user OAuthenticated this app -> return redirect URL */
      /* generate new auth_key */
      $auth_key = get_hash_token();
      /* update auth_key */
      $db->query(sprintf("UPDATE developers_apps_users SET auth_key = %s WHERE app_id = %s AND user_id = %s", secure($auth_key), secure($app['app_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return redirect_URL */
      $redirect_URL = $app['app_redirect_url'] . "?auth_key=" . $auth_key;
      if ($approving) {
        return $redirect_URL;
      } else {
        header('Location: ' . $redirect_URL);
      }
    } else {
      /* user didn't OAuthenticated this app -> OAuthenticated the app */
      if ($approving) {
        /* generate new auth_key */
        $auth_key = get_hash_token();
        /* insert new auth_key */
        $db->query(sprintf("INSERT INTO developers_apps_users (app_id, user_id, auth_key) VALUES (%s, %s, %s)", secure($app['app_id'], 'int'), secure($this->_data['user_id'], 'int'), secure($auth_key))) or _error('SQL_ERROR_THROWEN');
        /* return redirect_URL */
        $redirect_URL = $app['app_redirect_url'] . "?auth_key=" . $auth_key;
        return $redirect_URL;
      } else {
        /* prepare OAuth page */
        $smarty->assign('app', $app);
        page_header(__("Log in With") . " " . __($system['system_title']));
        page_footer('app_oauth');
      }
    }
  }


  /**
   * authorize_app
   * 
   * @param integer $app_auth_id
   * @param string $app_auth_secret
   * @param string $auth_key
   * @return string
   */
  public function authorize_app($app_auth_id, $app_auth_secret, $auth_key)
  {
    global $db, $system, $date;
    /* check if developers apps enabled */
    if (!$system['developers_apps_enabled']) {
      throw new Exception(__("The developers module has been disabled by the admin"));
    }
    /* (check&get) app */
    $app = $this->get_app($app_auth_id);
    if (!$app) {
      throw new Exception(__("We're sorry, but the application you're trying to use doesn't exist or has been disabled"));
    }
    /* check app_auth_secret */
    if ($app['app_auth_secret'] !== $app_auth_secret) {
      return_json(['error' => true, 'message' => "Bad Request, invalid app_secret"]);
    }
    /* check if user OAuthenticated this app before */
    $get_user_connection = $db->query(sprintf("SELECT * FROM developers_apps_users WHERE app_id = %s AND auth_key = %s", secure($app['app_id'], 'int'), secure($auth_key))) or _error('SQL_ERROR_THROWEN');
    if ($get_user_connection->num_rows == 0) {
      return_json(['error' => true, 'message' => "Bad Request, invalid auth_key"]);
    }
    $user_connection = $get_user_connection->fetch_assoc();
    /* generate new auth_key */
    $auth_key = get_hash_token();
    /* update auth_key */
    $db->query(sprintf("UPDATE developers_apps_users SET auth_key = %s WHERE app_id = %s AND user_id = %s", secure($auth_key), secure($app['app_id'], 'int'), secure($user_connection['user_id']))) or _error('SQL_ERROR_THROWEN');
    /* check if there is access_token & valid */
    if ($user_connection['access_token'] && (strtotime($user_connection['access_token_date']) >= strtotime("-1 minutes"))) {
      $access_token = $user_connection['access_token'];
    } else {
      /* generate new access_token */
      $access_token = get_hash_token();
      /* update access_token */
      $db->query(sprintf("UPDATE developers_apps_users SET access_token = %s, access_token_date = %s WHERE app_id = %s AND user_id = %s", secure($access_token), secure($date), secure($app['app_id'], 'int'), secure($user_connection['user_id']))) or _error('SQL_ERROR_THROWEN');
    }
    return $access_token;
  }


  /**
   * access_app
   * 
   * @param string $access_token
   * @return void
   */
  public function access_app($access_token)
  {
    global $db, $system, $smarty;
    /* check if developers apps enabled */
    if (!$system['developers_apps_enabled']) {
      throw new Exception(__("The developers module has been disabled by the admin"));
    }
    /* check if user OAuthenticated this app before */
    $check_user_connection = $db->query(sprintf("SELECT * FROM developers_apps_users WHERE access_token = %s", secure($access_token))) or _error('SQL_ERROR_THROWEN');
    if ($check_user_connection->num_rows == 0) {
      return_json(['error' => true, 'message' => "Bad Request, invalid access_token"]);
    }
    $user_connection = $check_user_connection->fetch_assoc();
    /* check if there is access_token & valid */
    if ($user_connection['access_token'] && (strtotime($user_connection['access_token_date']) >= strtotime("-1 minutes"))) {
      return $user_connection['user_id'];
    } else {
      return_json(['error' => true, 'message' => "Bad Request, expired access_token"]);
    }
  }



  /* ------------------------------- */
  /* Reports */
  /* ------------------------------- */

  /**
   * report
   * 
   * @param integer $id
   * @param string $handle
   * @param integer $category
   * @param string $reason
   * @return void
   */
  public function report($id, $handle, $category, $reason)
  {
    global $db, $date;
    switch ($handle) {
      case 'user':
        /* check the user */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'page':
        /* check the page */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        /* check the group */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM `groups` WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'event':
        /* check the event */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM `events` WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'post':
        /* check the post */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM posts WHERE post_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'comment':
        /* check the comment */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM posts_comments WHERE comment_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'forum_thread':
        /* check the forum thread */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_threads WHERE thread_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'forum_reply':
        /* check the forum thread */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM forums_replies WHERE reply_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      default:
        _error(403);
        break;
    }
    /* check node */
    if ($check->fetch_assoc()['count'] == 0) {
      _error(403);
    }
    /* check old reports */
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM reports WHERE user_id = %s AND node_id = %s AND node_type = %s", secure($this->_data['user_id'], 'int'), secure($id, 'int'), secure($handle))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] > 0) {
      throw new Exception(__("You have already reported this before!"));
    }
    /* validate category */
    if (is_empty($category)) {
      throw new Exception(__("You must select valid category for your report"));
    }
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM reports_categories WHERE category_id = %s", secure($category, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] == 0) {
      throw new Exception(__("You must select valid category for your report"));
    }
    /* report */
    $db->query(sprintf("INSERT INTO reports (user_id, node_id, node_type, category_id, reason, time) VALUES (%s, %s, %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($id, 'int'), secure($handle), secure($category, 'int'), secure($reason), secure($date))) or _error('SQL_ERROR_THROWEN');
    /* send notification to admins & moderators */
    $this->notify_system_admins("report", true);
  }



  /* ------------------------------- */
  /* [Tools] Fake Generator */
  /* ------------------------------- */

  /**
   * fake_users_generator
   * 
   * @param integer $users_num
   * @param string $default_password
   * @param boolean $random_Avatar
   * @param string $language
   * 
   * @return integer
   */
  public function fake_users_generator($users_num, $default_password, $random_Avatar, $language)
  {
    global $db, $system, $date;
    /* default password */
    $default_password = ($default_password) ? $default_password : "123456789";
    /* random Avatar */
    $random_Avatar = ($random_Avatar) ? true : false;
    /* init Faker */
    $faker = Faker\Factory::create($language);
    /* random genders */
    $genders = ['male', 'female'];
    /* fake generator */
    $generated = 0;
    while ($generated < $users_num) {
      $fake_username = strtolower(str_replace(".", "_", $faker->userName)) . "_" . get_hash_key(4);
      $fake_email = $fake_username . "@" . $_SERVER['HTTP_HOST'];
      $fake_gender = array_rand($genders);
      $fake_firstname = $faker->firstName($genders[$fake_gender]);
      $fake_lastname = $faker->lastName;
      if ($random_Avatar) {
        try {
          $fake_avatar = save_picture_from_url($faker->imageUrl());
        } catch (Exception $e) {
          $fake_avatar = 'null';
        }
      } else {
        $fake_avatar = 'null';
      }
      /* insert new user */
      $query = $db->query(sprintf("INSERT INTO users (user_name, user_email, user_password, user_firstname, user_lastname, user_gender, user_registered, user_activated, user_picture) VALUES (%s, %s, %s, %s, %s, %s, %s, '1', %s)", secure($fake_username), secure($fake_email), secure(_password_hash($default_password)), secure(ucwords($fake_firstname)), secure(ucwords($fake_lastname)), secure($fake_gender), secure($date), secure($fake_avatar)));
      if (!$query) continue;
      $generated++;
    }
    return $generated;
  }


  /**
   * fake_pages_generator
   * 
   * @param integer $pages_num
   * @param boolean $random_Avatar
   * @param string $language
   * @param integer $category
   * @param integer $country
   * 
   * @return integer
   */
  public function fake_pages_generator($pages_num, $random_Avatar, $language, $category, $country)
  {
    global $db, $system, $date;
    /* random Avatar */
    $random_Avatar = ($random_Avatar) ? true : false;
    /* init Faker */
    $faker = Faker\Factory::create($language);
    /* fake generator */
    $generated = 0;
    while ($generated < $pages_num) {
      $fake_username = strtolower(str_replace(".", "_", $faker->userName)) . "_" . get_hash_key(4);
      $fake_title = $faker->firstName("male");
      if ($random_Avatar) {
        try {
          $fake_avatar = save_picture_from_url($faker->imageUrl());
        } catch (Exception $e) {
          $fake_avatar = 'null';
        }
      } else {
        $fake_avatar = 'null';
      }
      /* insert new page */
      $query = $db->query(sprintf("INSERT INTO pages (page_admin, page_category, page_name, page_title, page_description, page_picture, page_country, page_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($category, 'int'), secure($fake_username), secure($fake_title), secure($fake_title), secure($fake_avatar), secure($country), secure($date)));
      if (!$query) continue;
      $generated++;
      /* get page_id */
      $page_id = $db->insert_id;
      /* like the page */
      $this->connect("page-like", $page_id);
      /* page admin addation */
      $this->connect("page-admin-addation", $page_id, $this->_data['user_id']);
    }
    return $generated;
  }


  /**
   * fake_groups_generator
   * 
   * @param integer $groups_num
   * @param boolean $random_Avatar
   * @param string $language
   * @param integer $category
   * 
   * @return integer
   */
  public function fake_groups_generator($groups_num, $random_Avatar, $language, $category)
  {
    global $db, $system, $date;
    /* random Avatar */
    $random_Avatar = ($random_Avatar) ? true : false;
    /* init Faker */
    $faker = Faker\Factory::create($language);
    /* fake generator */
    $generated = 0;
    while ($generated < $groups_num) {
      $fake_username = strtolower(str_replace(".", "_", $faker->userName)) . "_" . get_hash_key(4);
      $fake_title = $faker->firstName("male");
      if ($random_Avatar) {
        try {
          $fake_avatar = save_picture_from_url($faker->imageUrl());
        } catch (Exception $e) {
          $fake_avatar = 'null';
        }
      } else {
        $fake_avatar = 'null';
      }
      /* insert new group */
      $query = $db->query(sprintf("INSERT INTO `groups` (group_privacy, group_admin, group_name, group_category, group_title, group_description, group_picture, group_date) VALUES ('public', %s, %s, %s, %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($fake_username), secure($category), secure($fake_title), secure($fake_title), secure($fake_avatar), secure($date))) or _error('SQL_ERROR_THROWEN');
      if (!$query) continue;
      $generated++;
      /* get group_id */
      $group_id = $db->insert_id;
      /* join the group */
      $this->connect("group-join", $group_id);
      /* group admin addation */
      $this->connect("group-admin-addation", $group_id, $this->_data['user_id']);
    }
    return $generated;
  }



  /* ------------------------------- */
  /* [Tools] Auto Connect */
  /* ------------------------------- */

  /**
   * auto_friend
   * 
   * @param integer $user_id
   * @param integer $country_id
   * @return void
   */
  public function auto_friend($user_id, $country_id = null)
  {
    global $db, $system;
    if (!$system['auto_friend'] || !isset($user_id)) {
      return;
    }
    /* check if [system || custom] auto-friend */
    if ($country_id == null) {
      /* system auto-friend */
      if (is_empty($system['auto_friend_users'])) {
        return;
      }
      $auto_friend_users = $system['auto_friend_users'];
    } else {
      /* custom auto-friend */
      $get_auto_friend_users = $db->query(sprintf("SELECT nodes_ids FROM auto_connect WHERE type = 'friend' AND country_id = %s", secure($country_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_auto_friend_users->num_rows == 0) {
        return;
      }
      $auto_friend_users = $get_auto_friend_users->fetch_assoc()['nodes_ids'];
    }
    /* make a list from target friends */
    $auto_friend_users_array = json_decode(html_entity_decode($auto_friend_users), true);
    if (count($auto_friend_users_array) == 0) {
      return;
    }
    foreach ($auto_friend_users_array as $_user) {
      if (is_numeric($_user['id'])) {
        $auto_friend_users_ids[] = $_user['id'];
      }
    }
    if (count($auto_friend_users_ids) == 0) {
      return;
    }
    $auto_friend_users = $this->spread_ids($auto_friend_users_ids);
    /* get the user_id of each new friend */
    $get_auto_friends = $db->query("SELECT user_id FROM users WHERE user_id IN ($auto_friend_users)");
    if ($get_auto_friends->num_rows > 0) {
      while ($auto_friend = $get_auto_friends->fetch_assoc()) {
        /* check if the user is already friends with the target */
        $check_friendship = $db->query(sprintf("SELECT * FROM friends WHERE (user_one_id = %s AND user_two_id = %s) OR (user_one_id = %s AND user_two_id = %s)", secure($user_id, 'int'), secure($auto_friend['user_id'], 'int'), secure($auto_friend['user_id'], 'int'), secure($user_id, 'int')));
        if ($check_friendship->num_rows > 0) continue;
        /* add friend */
        $db->query(sprintf("INSERT INTO friends (user_one_id, user_two_id, status) VALUES (%s, %s, '1')", secure($user_id, 'int'),  secure($auto_friend['user_id'], 'int')));
        /* check if the user is already following the target */
        $check_following = $db->query(sprintf("SELECT * FROM followings WHERE user_id = %s AND following_id = %s", secure($user_id, 'int'), secure($auto_friend['user_id'], 'int')));
        if ($check_following->num_rows > 0) continue;
        /* follow */
        $db->query(sprintf("INSERT INTO followings (user_id, following_id) VALUES (%s, %s)", secure($user_id, 'int'),  secure($auto_friend['user_id'], 'int')));
      }
    }
  }


  /**
   * auto_follow
   * 
   * @param integer $user_id
   * @param integer $country_id
   * @return void
   */
  public function auto_follow($user_id, $country_id = null)
  {
    global $db, $system;
    if (!$system['auto_follow'] || !isset($user_id)) {
      return;
    }
    /* check if [system || custom] auto-follow */
    if ($country_id == null) {
      /* system auto-follow */
      if (is_empty($system['auto_follow_users'])) {
        return;
      }
      $auto_follow_users = $system['auto_follow_users'];
    } else {
      /* custom auto-follow */
      $get_auto_follow_users = $db->query(sprintf("SELECT nodes_ids FROM auto_connect WHERE type = 'follow' AND country_id = %s", secure($country_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_auto_follow_users->num_rows == 0) {
        return;
      }
      $auto_follow_users = $get_auto_follow_users->fetch_assoc()['nodes_ids'];
    }
    /* make a list from target followings */
    $auto_follow_users_array = json_decode(html_entity_decode($auto_follow_users), true);
    if (count($auto_follow_users_array) == 0) {
      return;
    }
    foreach ($auto_follow_users_array as $_user) {
      if (is_numeric($_user['id'])) {
        $auto_follow_users_ids[] = $_user['id'];
      }
    }
    if (count($auto_follow_users_ids) == 0) {
      return;
    }
    $auto_follow_users = $this->spread_ids($auto_follow_users_ids);
    /* get the user_id of each new following */
    $get_auto_followings = $db->query("SELECT user_id FROM users WHERE user_id IN ($auto_follow_users)");
    if ($get_auto_followings->num_rows > 0) {
      while ($auto_following = $get_auto_followings->fetch_assoc()) {
        /* check if the user is already following the target */
        $check_following = $db->query(sprintf("SELECT * FROM followings WHERE user_id = %s AND following_id = %s", secure($user_id, 'int'), secure($auto_following['user_id'], 'int')));
        if ($check_following->num_rows > 0) continue;
        $db->query(sprintf("INSERT INTO followings (user_id, following_id) VALUES (%s, %s)", secure($user_id, 'int'),  secure($auto_following['user_id'], 'int')));
      }
    }
  }


  /**
   * auto_like
   * 
   * @param integer $user_id
   * @param integer $country_id
   * @return void
   */
  public function auto_like($user_id, $country_id = null)
  {
    global $db, $system;
    if (!$system['auto_like'] || !isset($user_id)) {
      return;
    }
    /* check if [system || custom] auto-like */
    if ($country_id == null) {
      /* system auto-like */
      if (is_empty($system['auto_like_pages'])) {
        return;
      }
      $auto_like_pages = $system['auto_like_pages'];
    } else {
      /* custom auto-like */
      $get_auto_like_pages = $db->query(sprintf("SELECT nodes_ids FROM auto_connect WHERE type = 'like' AND country_id = %s", secure($country_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_auto_like_pages->num_rows == 0) {
        return;
      }
      $auto_like_pages = $get_auto_like_pages->fetch_assoc()['nodes_ids'];
    }
    /* make a list from target pages */
    $auto_like_pages_array = json_decode(html_entity_decode($auto_like_pages), true);
    if (count($auto_like_pages_array) == 0) {
      return;
    }
    foreach ($auto_like_pages_array as $_page) {
      if (is_numeric($_page['id'])) {
        $auto_like_pages_ids[] = $_page['id'];
      }
    }
    if (count($auto_like_pages_ids) == 0) {
      return;
    }
    $auto_like_pages = $this->spread_ids($auto_like_pages_ids);
    /* get the page_id of each new like */
    $get_auto_like = $db->query("SELECT page_id FROM pages WHERE page_id IN ($auto_like_pages)");
    if ($get_auto_like->num_rows > 0) {
      while ($auto_like = $get_auto_like->fetch_assoc()) {
        /* check if the user is already liked the target */
        $check_like = $db->query(sprintf("SELECT * FROM pages_likes WHERE user_id = %s AND page_id = %s", secure($user_id, 'int'), secure($auto_like['page_id'], 'int')));
        if ($check_like->num_rows > 0) continue;
        /* like */
        $db->query(sprintf("INSERT INTO pages_likes (user_id, page_id) VALUES (%s, %s)", secure($user_id, 'int'),  secure($auto_like['page_id'], 'int')));
        /* update likes counter +1 */
        $db->query(sprintf("UPDATE pages SET page_likes = page_likes + 1  WHERE page_id = %s", secure($auto_like['page_id'], 'int')));
      }
    }
  }


  /**
   * auto_join
   * 
   * @param integer $user_id
   * @param integer $country_id
   * @return void
   */
  public function auto_join($user_id, $country_id = null)
  {
    global $db, $system;
    if (!$system['auto_join'] || !isset($user_id)) {
      return;
    }
    /* check if [system || custom] auto-join */
    if ($country_id == null) {
      /* system auto-join */
      if (is_empty($system['auto_join_groups'])) {
        return;
      }
      $auto_join_groups = $system['auto_join_groups'];
    } else {
      /* custom auto-join */
      $get_auto_join_groups = $db->query(sprintf("SELECT nodes_ids FROM auto_connect WHERE type = 'join' AND country_id = %s", secure($country_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_auto_join_groups->num_rows == 0) {
        return;
      }
      $auto_join_groups = $get_auto_join_groups->fetch_assoc()['nodes_ids'];
    }
    /* make a list from target groups */
    $auto_join_groups_array = json_decode(html_entity_decode($auto_join_groups), true);
    if (count($auto_join_groups_array) == 0) {
      return;
    }
    foreach ($auto_join_groups_array as $_group) {
      if (is_numeric($_group['id'])) {
        $auto_join_groups_ids[] = $_group['id'];
      }
    }
    if (count($auto_join_groups_ids) == 0) {
      return;
    }
    $auto_join_groups = $this->spread_ids($auto_join_groups_ids);
    /* get the group_id of each new join */
    $get_auto_join = $db->query("SELECT group_id FROM `groups` WHERE group_id IN ($auto_join_groups)");
    if ($get_auto_join->num_rows > 0) {
      while ($auto_join = $get_auto_join->fetch_assoc()) {
        /* check if the user is already joined the target */
        $check_join = $db->query(sprintf("SELECT * FROM groups_members WHERE user_id = %s AND group_id = %s", secure($user_id, 'int'), secure($auto_join['group_id'], 'int')));
        if ($check_join->num_rows > 0) continue;
        /* join */
        $db->query(sprintf("INSERT INTO groups_members (user_id, group_id, approved) VALUES (%s, %s, '1')", secure($user_id, 'int'),  secure($auto_join['group_id'], 'int')));
        /* update members counter +1 */
        $db->query(sprintf("UPDATE `groups` SET group_members = group_members + 1  WHERE group_id = %s", secure($auto_join['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      }
    }
  }


  /**
   * add_custom_auto_connect
   * 
   * @param string $handle
   * @param array $args
   * @return void
   */
  public function add_custom_auto_connect($handle, $args)
  {
    global $db;
    /* validate handle */
    if (!in_array($handle, ['friend', 'follow', 'like', 'join'])) {
      return;
    }
    $countries = array_filter(
      $args,
      function ($key) {
        global $handle;
        return strpos($key, 'auto_' . $handle . '_country_') == 0;
      },
      ARRAY_FILTER_USE_KEY
    );
    if ($countries) {
      foreach ($countries as $field_name => $country_id) {
        /* get guid */
        $guid = substr($field_name, strlen('auto_' . $handle . '_country_'));
        /* check nodes_ids */
        if (is_empty($args['auto_' . $handle . '_nodes_ids_' . $guid])) {
          continue;
        }
        /* check country */
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM system_countries WHERE country_id = %s", secure($country_id, 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($check->fetch_assoc()['count'] == 0) {
          continue;
        }
        $db->query(sprintf("INSERT INTO auto_connect (type, country_id, nodes_ids) VALUES (%s, %s, %s)", secure($handle), secure($country_id, 'int'), secure($args['auto_' . $handle . '_nodes_ids_' . $guid]))) or _error('SQL_ERROR_THROWEN');
      }
    }
  }



  /* ------------------------------- */
  /* [Tools] Backups */
  /* ------------------------------- */

  /**
   * backup_database
   * 
   * @param string $backup_folder_name
   * @return void
   */
  public function backup_database($backup_folder_name = '')
  {
    global $db, $date;
    $get_tables = $db->query('SHOW TABLES');
    while ($table = $get_tables->fetch_row()) {
      $tables[] = $table[0];
    }
    $content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\nSET time_zone = \"+00:00\";\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8mb4 */;\n\n";
    foreach ($tables as $table) {
      $result = $db->query("SELECT * FROM `$table`");
      $fields_count = $result->field_count;
      $rows_count = $db->affected_rows;
      $get_create_tbl = $db->query("SHOW CREATE TABLE `$table`");
      $TableMLine = $get_create_tbl->fetch_row();
      $content = (!isset($content) ?  '' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";
      for ($i = 0, $st_counter = 0; $i < $fields_count; $i++, $st_counter = 0) {
        while ($row = $result->fetch_row()) {
          if ($st_counter % 100 == 0 || $st_counter == 0) {
            $content .= "\nINSERT INTO " . $table . " VALUES";
          }
          $content .= "\n(";
          for ($j = 0; $j < $fields_count; $j++) {
            if (isset($row[$j])) {
              $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
              $content .= '"' . $row[$j] . '"';
            } else {
              $content .= ($row[$j] == null) ? 'null' : '""';
            }
            if ($j < ($fields_count - 1)) {
              $content .= ',';
            }
          }
          $content .= ")";
          if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_count) {
            $content .= ";";
          } else {
            $content .= ",";
          }
          $st_counter = $st_counter + 1;
        }
      }
      $content .= "\n\n\n";
    }
    $content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
    /* create backup folder */
    if (!$backup_folder_name) {
      $backup_folder_name = 'content/backups/' . date('d-m-Y') . '/' . time();
      if (!file_exists(ABSPATH . $backup_folder_name)) {
        @mkdir(ABSPATH . $backup_folder_name, 0777, true);
      }
      /* update last backup time */
      update_system_options(['last_backup_time' => secure($date)]);
    }
    /* set backup */
    $sql_backup_name = 'Database-Backup' . '_' . date('d-m-Y') . '.sql';
    $write_file = @file_put_contents(ABSPATH . $backup_folder_name . '/' . $sql_backup_name, $content);
    if (!$write_file) {
      throw new Exception(__("Backup failed!, Make sure the backup folder is writable (chmod 777)"));
    }
  }


  /**
   * backup_files
   * 
   * @param string $backup_folder_name
   * @return void
   */
  public function backup_files($backup_folder_name = '')
  {
    global $db, $date;
    /* create backup folder */
    if (!$backup_folder_name) {
      $backup_folder_name = 'content/backups/' . date('d-m-Y') . '/' . time();
      if (!file_exists(ABSPATH . $backup_folder_name)) {
        @mkdir(ABSPATH . $backup_folder_name, 0777, true);
      }
      /* update last backup time */
      update_system_options(['last_backup_time' => secure($date)]);
    }
    /* set backup */
    $files_backup_name = 'Files-Backup' . '_' . date('d-m-Y') . '.zip';
    $zip = new ZipArchive();
    $read_file = $zip->open(ABSPATH . $backup_folder_name . '/' . $files_backup_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    if ($read_file !== true) {
      throw new Exception(__("Backup failed!, Make sure the backup folder is writable (chmod 777)"));
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ABSPATH), RecursiveIteratorIterator::LEAVES_ONLY);
    foreach ($files as $name => $file) {
      if (strpos($file, 'content/backups') === false && strpos($file, 'content\backups') === false) {
        if (!$file->isDir()) {
          $file_path = $file->getRealPath();
          $relative_path = substr($file_path, strlen(ABSPATH));
          $zip->addFile($file_path, $relative_path);
        }
      }
    }
    $zip->close();
  }


  /**
   * backup_full
   * 
   * @return void
   */
  public function backup_full()
  {
    global $db, $date;
    /* backup database */
    $this->backup_database();
    /* backup files */
    $this->backup_files();
  }



  /* ------------------------------- */
  /* Custom Fields ✅ */
  /* ------------------------------- */

  /**
   * get_custom_fields
   * 
   * @param array $args
   * @return array
   */
  public function get_custom_fields($args = [])
  {
    global $db, $system;
    $fields = [];
    /* prepare "for" [user|page|group|event|product|job|offer] - default -> user */
    $args['for'] = (isset($args['for'])) ? $args['for'] : "user";
    if (!in_array($args['for'], ['user', 'page', 'group', 'event', 'product', 'job', 'offer'])) {
      throw new BadRequestException(__("Invalid field for"));
    }
    /* prepare "get" [registration|profile|settings|search] - default -> registration */
    $args['get'] = (isset($args['get'])) ? $args['get'] : "registration";
    if (!in_array($args['get'], ['registration', 'profile', 'settings', 'search'])) {
      throw new BadRequestException(__("Invalid field get"));
    }
    /* prepare where_query */
    $where_query = "WHERE field_for = '" . $args['for'] . "'";
    if ($args['get'] == "registration") {
      $where_query .= " AND in_registration = '1'";
    } elseif ($args['get'] == "profile") {
      $where_query .= " AND in_profile = '1'";
    } elseif ($args['get'] == "search") {
      $where_query .= " AND in_search = '1'";
    }
    $get_fields = $db->query("SELECT * FROM custom_fields " . $where_query . " ORDER BY field_order ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_fields->num_rows > 0) {
      while ($field = $get_fields->fetch_assoc()) {
        if ($field['type'] == "selectbox" || $field['type'] == "multipleselectbox") {
          $field['options'] = explode(PHP_EOL, $field['select_options']);
        }
        if ($args['get'] == "registration" || $args['get'] == "search") {
          /* no value neeeded */
          $fields[] = $field;
        } else {
          /* valid node_id */
          if (!isset($args['node_id'])) {
            throw new BadRequestException(__("Invalid node_id"));
          }
          /* get the custom field value */
          $get_field_value = $db->query(sprintf("SELECT value FROM custom_fields_values WHERE field_id = %s AND node_id = %s AND node_type = %s", secure($field['field_id'], 'int'), secure($args['node_id'], 'int'), secure($args['for']))) or _error('SQL_ERROR_THROWEN');
          if ($get_field_value->num_rows > 0) {
            $field_value = $get_field_value->fetch_assoc()['value'];
            switch ($field['type']) {
              case 'selectbox':
                $field['value'] = $field['options'][$field_value];
                break;

              case 'multipleselectbox':
                $field['value'] = explode(",", $field_value);
                $field['value_string'] = [];
                foreach ($field['value'] as $value) {
                  if (isset($field['options'][$value])) {
                    $field['value_string'][] = $field['options'][$value];
                  }
                }
                $field['value_string'] = implode(", ", $field['value_string']);
                break;

              default:
                $field['value'] = $field_value;
                break;
            }
          }
          /* bypass profile fields if its value is empty */
          if ($args['get'] == "profile" && !$field['value']) {
            continue;
          }
          $fields[$field['place']][] = $field;
        }
      }
    }
    return $fields;
  }


  /**
   * set_custom_fields
   * 
   * @param array $input_fields
   * @param string $for
   * @param string $set
   * @param integer $node_id
   * @return void|array
   */
  public function set_custom_fields($input_fields, $for = "user", $set = "registration", $node_id = null)
  {
    global $db, $system;
    $custom_fields = [];
    /* prepare "for" [user|page|group|event|product|job|offer] - default -> user */
    if (!in_array($for, ['user', 'page', 'group', 'event', 'product', 'job', 'offer'])) {
      throw new BadRequestException(__("Invalid field for"));
    }
    /* prepare "set" [registration|settings] - default -> registration */
    if (!in_array($set, ['registration', 'settings'])) {
      throw new BadRequestException(__("Invalid field set"));
    }
    /* prepare where_query */
    $where_query = " AND field_for = '" . $for . "'";
    if ($set == "registration") {
      $where_query .= " AND in_registration = '1'";
    }
    /* get & set input_fields */
    $prefix = "fld_";
    $prefix_len = strlen($prefix);
    foreach ($input_fields as $key => $value) {
      if (strpos($key, $prefix) === false) {
        continue;
      }
      $field_id = substr($key, $prefix_len);
      $get_field = $db->query(sprintf("SELECT * FROM custom_fields WHERE field_id = %s" . $where_query, secure($field_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_field->num_rows == 0) {
        continue;
      }
      $field = $get_field->fetch_assoc();
      /* valid field */
      if ($field['mandatory']) {
        switch ($field['type']) {
          case 'selectbox':
            /* select box */
            if ($value == "none") {
              throw new ValidationException(__("You must select") . " " . $field['label']);
            }
            break;

          case 'multipleselectbox':
            /* multiple select box */
            if (!is_array($value) || count($value) == 0) {
              throw new ValidationException(__("You must select at least one option from ") . " " . $field['label']);
            }
            break;

          default:
            /* textbox || textarea */
            if (is_empty($value)) {
              throw new ValidationException(__("You must enter") . " " . $field['label']);
            }
            break;
        }
      }
      if ($field['type'] == "textbox" || $field['type'] == "textarea") {
        if (strlen($value) > $field['length']) {
          throw new ValidationException(__("The maximum value for") . " " . $field['label'] . " " . __("is") . " " . $field['length']);
        }
      }
      /* (insert|update) node custom fields */
      $value = ($field['type'] == "multipleselectbox") ? implode(",", $value) : $value;
      if ($set == "registration") {
        /* insert query */
        $custom_fields[$field['field_id']] = $value;
      } else {
        /* valid node_id */
        if ($node_id == null) {
          _error(400);
        }
        $check_value = $db->query(sprintf("SELECT * FROM custom_fields_values WHERE field_id = %s AND node_id = %s AND node_type = %s", secure($field['field_id'], 'int'), secure($node_id, 'int'), secure($for)));
        if ($check_value->num_rows > 0) {
          /* update if already exist */
          $db->query(sprintf("UPDATE custom_fields_values SET value = %s WHERE field_id = %s AND node_id = %s AND node_type = %s", secure($value), secure($field['field_id'], 'int'), secure($node_id, 'int'), secure($for))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* insert if not exist */
          $insert_field = $db->query(sprintf("INSERT INTO custom_fields_values (value, field_id, node_id, node_type) VALUES (%s, %s, %s, %s)", secure($value), secure($field['field_id'], 'int'), secure($node_id, 'int'), secure($for)));
        }
      }
    }
    if ($set == "registration") {
      return $custom_fields;
    }
    return;
  }


  /**
   * search_custom_fields
   * 
   * @param array $input_fields
   * @return string
   */
  public function search_custom_fields($input_fields)
  {
    global $db, $system;
    $query_statement = "";
    /* check input_fields */
    $prefix = "fld_";
    $prefix_len = strlen($prefix);
    foreach ($input_fields as $key => $value) {
      if (strpos($key, $prefix) === false) {
        continue;
      }
      $field_id = substr($key, $prefix_len);
      $get_field = $db->query(sprintf("SELECT * FROM custom_fields WHERE field_for = 'user' AND in_search = '1' AND field_id = %s", secure($field_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_field->num_rows == 0) {
        continue;
      }
      $field = $get_field->fetch_assoc();
      switch ($field['type']) {
        case 'textbox':
        case 'textarea':
          if ($value) {
            $query_statement .= ($query_statement) ? " OR " : "";
            $query_statement .= sprintf(" (cfv.field_id = %s AND cfv.value LIKE %s) ", secure($field['field_id'], 'int'), secure($value, 'search'));
          }
          break;

        case 'selectbox':
          if ($value != "any") {
            $query_statement .= ($query_statement) ? " OR " : "";
            $query_statement .= sprintf(" (cfv.field_id = %s AND cfv.value = %s) ", secure($field['field_id'], 'int'), secure($value));
          }
          break;

        case 'multipleselectbox':
          if ($value) {
            $value = implode(",", $value);
            $query_statement .= ($query_statement) ? " OR " : "";
            $query_statement .= sprintf(" (cfv.field_id = %s AND cfv.value LIKE %s) ", secure($field['field_id'], 'int'), secure($value, 'search'));
          }
          break;
      }
    }
    return $query_statement;
  }



  /* ------------------------------- */
  /* Widgets */
  /* ------------------------------- */

  /**
   * widget
   * 
   * @param array $place
   * @return array
   */
  public function widgets($place)
  {
    global $db;
    $widgets = [];
    $get_widgets = $db->query(sprintf("SELECT * FROM widgets WHERE place = %s ORDER BY place_order ASC", secure($place))) or _error('SQL_ERROR_THROWEN');
    if ($get_widgets->num_rows > 0) {
      while ($widget = $get_widgets->fetch_assoc()) {
        $widget['code'] = html_entity_decode($widget['code'], ENT_QUOTES);
        $widgets[] = $widget;
      }
    }
    return $widgets;
  }



  /* ------------------------------- */
  /* Reactions & Emoji & Stickers */
  /* ------------------------------- */

  /**
   * get_reactions
   * 
   * @param boolean $only_enabled
   * @return array
   */
  public function get_reactions($only_enabled = false)
  {
    global $system, $db;
    $reactions = [];
    $where_statement = ($only_enabled) ? "WHERE enabled = '1'" : "";
    $get_reactions = $db->query("SELECT * FROM system_reactions " . $where_statement . " ORDER BY reaction_order ASC") or _error('SQL_ERROR_THROWEN');
    if ($get_reactions->num_rows > 0) {
      while ($reaction = $get_reactions->fetch_assoc()) {
        $reactions[$reaction['reaction']] = $reaction;
      }
    }
    return $reactions;
  }


  /**
   * get_emojis
   * 
   * @return array
   */
  public function get_emojis()
  {
    global $db;
    $emojis = [];
    $get_emojis = $db->query("SELECT * FROM emojis") or _error('SQL_ERROR_THROWEN');
    if ($get_emojis->num_rows > 0) {
      while ($emoji = $get_emojis->fetch_assoc()) {
        $emojis[] = $emoji;
      }
    }
    return $emojis;
  }


  /**
   * decode_emojis
   * 
   * @param string $text
   * @return string
   */
  public function decode_emojis($text)
  {
    if (!function_exists('grapheme_strpos')) {
      return $text;
    }
    global $emojis;
    if ($text && $emojis) {
      $detect_emojis = Emoji\detect_emoji(html_entity_decode($text));
      if ($detect_emojis) {
        foreach ($detect_emojis as $decoded_emoji) {
          $text = str_replace(htmlentities($decoded_emoji['emoji'], ENT_QUOTES, 'utf-8'), $decoded_emoji['emoji'], $text);
          $key = array_search($decoded_emoji['emoji'], array_column($emojis, 'unicode_char'));
          if ($key === false) continue;
          $twemoji = '<i class="twa twa-' . $emojis[$key]['class'] . '"></i>';
          $text = preg_replace("/" . $decoded_emoji['emoji'] . "/", $twemoji, $text, 1);
        }
      }
    }
    return $text;
  }


  /**
   * get_stickers
   * 
   * @return array
   */
  public function get_stickers()
  {
    global $db;
    $stickers = [];
    $get_stickers = $db->query("SELECT * FROM stickers") or _error('SQL_ERROR_THROWEN');
    if ($get_stickers->num_rows > 0) {
      while ($sticker = $get_stickers->fetch_assoc()) {
        $stickers[] = $sticker;
      }
    }
    return $stickers;
  }


  /**
   * decode_stickers
   * 
   * @param string $text
   * @return string
   */
  public function decode_stickers($text)
  {
    global $db, $system;
    if ($text) {
      $get_stickers = $db->query("SELECT * FROM stickers") or _error('SQL_ERROR_THROWEN');
      if ($get_stickers->num_rows > 0) {
        while ($sticker = $get_stickers->fetch_assoc()) {
          $replacement = '<img class="sticker" src="' . $system['system_uploads'] . '/' . $sticker['image'] . '"></i>';
          $text = preg_replace('/(^|\s):STK-' . $sticker['sticker_id'] . ':/', $replacement, $text);
        }
      }
    }
    return $text;
  }



  /* ------------------------------- */
  /* Gifts */
  /* ------------------------------- */

  /**
   * get_gifts
   * 
   * @return array
   */
  public function get_gifts()
  {
    global $db;
    $gifts = [];
    $get_gifts = $db->query("SELECT * FROM gifts") or _error('SQL_ERROR_THROWEN');
    if ($get_gifts->num_rows > 0) {
      while ($gift = $get_gifts->fetch_assoc()) {
        $gifts[] = $gift;
      }
    }
    return $gifts;
  }


  /**
   * get_gift
   * 
   * @return array
   */
  public function get_gift($gift_id)
  {
    global $db;
    $get_gift = $db->query(sprintf("SELECT gifts.image, users.user_name, users.user_firstname, users.user_lastname FROM users_gifts INNER JOIN gifts ON users_gifts.gift_id = gifts.gift_id INNER JOIN users ON users_gifts.from_user_id = users.user_id WHERE users_gifts.id = %s AND users_gifts.to_user_id = %s", secure($gift_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_gift->num_rows == 0) {
      return false;
    }
    return $get_gift->fetch_assoc();
  }


  /**
   * send_gift
   *
   * @param integer $user_id
   * @param integer $gift_id
   * 
   * @return void
   */
  public function send_gift($user_id, $gift_id)
  {
    global $db, $system;
    /* check if the viewer can send gifts */
    if (!$this->_data['can_send_gifts']) {
      throw new Exception(__("You don't have the right to send gifts"));
    }
    /* check if the viewer allowed to send a gift to the target */
    $get_target_user = $db->query(sprintf("SELECT user_privacy_gifts FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_target_user->num_rows == 0) {
      _error(400);
    }
    $target_user = $get_target_user->fetch_assoc();
    if ($target_user['user_privacy_gifts'] == "me" || ($target_user['user_privacy_gifts'] == "friends" && !$this->friendship_approved($user_id))) {
      throw new Exception(__("You can't send a gift to this user"));
    }
    /* send the gift to the target user */
    $db->query(sprintf("INSERT INTO users_gifts (from_user_id, to_user_id, gift_id) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'),  secure($user_id, 'int'), secure($gift_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* post new notification */
    $this->post_notification(['to_user_id' => $user_id, 'action' => 'gift', 'node_url' => $db->insert_id]);
  }



  /* ------------------------------- */
  /* Announcements */
  /* ------------------------------- */

  /**
   * announcements
   * 
   * @param array $place
   * @return array
   */
  public function announcements()
  {
    global $db, $date;
    $announcements = [];
    $get_announcement = $db->query(sprintf('SELECT * FROM announcements WHERE start_date <= %1$s AND end_date >= %1$s', secure($date))) or _error('SQL_ERROR_THROWEN');
    if ($get_announcement->num_rows > 0) {
      while ($announcement = $get_announcement->fetch_assoc()) {
        $check = $db->query(sprintf("SELECT COUNT(*) as count FROM announcements_users WHERE announcement_id = %s AND user_id = %s", secure($announcement['announcement_id'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        if ($check->fetch_assoc()['count'] == 0) {
          $announcement['code'] = html_entity_decode($announcement['code'], ENT_QUOTES);
          $announcements[] = $announcement;
        }
      }
    }
    return $announcements;
  }


  /**
   * hide_announcement
   * 
   * @param integer $id
   * @return void
   */
  public function hide_announcement($id)
  {
    global $db, $system;
    /* check announcement */
    $check = $db->query(sprintf("SELECT COUNT(*) as count FROM announcements WHERE announcement_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($check->fetch_assoc()['count'] == 0) {
      _error(403);
    }
    /* hide announcement */
    $db->query(sprintf("INSERT INTO announcements_users (announcement_id, user_id) VALUES (%s, %s)", secure($id, 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Invitations */
  /* ------------------------------- */

  /**
   * get_invitation_codes_details
   * 
   * @return array
   */
  public function get_invitation_codes_details()
  {
    global $db;
    $codes = [];
    $get_codes = $db->query(sprintf('SELECT invitation_codes.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture FROM invitation_codes LEFT JOIN users ON invitation_codes.used_by = users.user_id WHERE invitation_codes.created_by = %s', secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_codes->num_rows > 0) {
      while ($code = $get_codes->fetch_assoc()) {
        if ($code['used']) {
          $code['user_picture'] = get_picture($code['user_picture'], $code['user_gender']);
        }
        $codes[] = $code;
      }
    }
    return $codes;
  }


  /**
   * get_invitation_codes_stats
   * 
   * @return string
   */
  public function get_invitation_codes_stats()
  {
    global $db, $system;
    $get_generated_codes = $db->query(sprintf("SELECT COUNT(*) as count FROM invitation_codes WHERE created_by = %s AND created_date >= DATE_SUB(NOW(),INTERVAL 1 %s)", secure($this->_data['user_id'], 'int'), strtoupper($system['invitation_expire_period']))) or _error('SQL_ERROR_THROWEN');
    $generated_codes = $get_generated_codes->fetch_assoc()['count'];
    $available_codes = $system['invitation_user_limit'] - $generated_codes;
    $get_used_codes = $db->query(sprintf("SELECT COUNT(*) as count FROM invitation_codes WHERE created_by = %s AND created_date >= DATE_SUB(NOW(),INTERVAL 1 %s) AND used = '1'", secure($this->_data['user_id'], 'int'), strtoupper($system['invitation_expire_period']))) or _error('SQL_ERROR_THROWEN');
    $used_codes = $get_used_codes->fetch_assoc()['count'];
    return ["available" => $available_codes, "generated" => $generated_codes, "used" => $used_codes];
  }


  /**
   * check_user_invitation_codes
   * 
   * @return void
   */
  public function can_generate_invitation_code()
  {
    global $db, $system;
    if ($this->_data['can_invite_users']) {
      if ($system['invitation_user_limit'] == 0) {
        return true;
      }
      $check_limit = $db->query(sprintf("SELECT COUNT(*) as count FROM invitation_codes WHERE created_by = %s AND created_date >= DATE_SUB(NOW(),INTERVAL 1 %s)", secure($this->_data['user_id'], 'int'), strtoupper($system['invitation_expire_period']))) or _error('SQL_ERROR_THROWEN');
      if ($check_limit->fetch_assoc()['count'] < $system['invitation_user_limit']) {
        return true;
      }
    }
    return false;
  }


  /**
   * get_invitation_code
   * 
   * @return string
   */
  public function generate_invitation_code()
  {
    global $db, $system, $date;
    /* check invitation enabled */
    if (!$system['invitation_enabled']) {
      throw new Exception(__("The invitation system has been disabled by the admin"));
    }
    /* check invitation permission */
    if (!$this->_data['can_invite_users']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check if user can generate new code */
    if (!$this->can_generate_invitation_code()) {
      throw new Exception(__("You have reached the maximum number of invitation codes"));
    }
    $code = get_hash_key();
    $db->query(sprintf("INSERT INTO invitation_codes (code, created_by, created_date) VALUES (%s, %s, %s)", secure($code), secure($this->_data['user_id'], "int"), secure($date))) or _error('SQL_ERROR_THROWEN');
    return $code;
  }


  /**
   * check_invitation_code ✅
   * 
   * @param string $code
   * @return boolean
   * 
   */
  public function check_invitation_code($code)
  {
    global $db;
    $query = $db->query(sprintf("SELECT COUNT(*) as count FROM invitation_codes WHERE code = %s AND used = '0'", secure($code))) or _error('SQL_ERROR_THROWEN');
    if ($query->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }


  /**
   * update_invitation_code
   * 
   * @param string $code
   * @param string $user_id
   * @return void
   */
  public function update_invitation_code($code, $user_id)
  {
    global $db, $date;
    $db->query(sprintf("UPDATE invitation_codes SET used = '1', used_by = %s, used_date = %s WHERE code = %s", secure($user_id, "int"), secure($date), secure($code))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * send_invitation_email
   * 
   * @param string $email
   * @param string $code
   * @return void
   */
  public function send_invitation_email($email, $code)
  {
    global $db, $system, $date;
    /* check invitation enabled */
    if (!$system['invitation_enabled']) {
      throw new Exception(__("The invitation system has been disabled by the admin"));
    }
    /* check invitation permission */
    if (!$this->_data['can_invite_users']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check invitation code */
    if (!$this->check_invitation_code($code)) {
      throw new Exception(__("This invitation code is expired or invalid"));
    }
    /* check email */
    if (!valid_email($email)) {
      throw new Exception(__("Please enter a valid email address"));
    }
    if ($this->check_email($email)) {
      throw new Exception(__("Sorry, it looks like") . " " . $email . " " . __("belongs to an existing account"));
    }
    /* check if viewer already invited this email before */
    $check_invitation_log = $db->query(sprintf("SELECT COUNT(*) as count FROM users_invitations WHERE email_phone = %s", secure($email))) or _error('SQL_ERROR_THROWEN');
    if ($check_invitation_log->fetch_assoc()['count'] > 0) {
      throw new Exception(__("You already invited this email before"));
    }
    /* prepare invitation email */
    $subject = html_entity_decode($this->_data['name'], ENT_QUOTES) . " " . __("Invite you to join") . " " . html_entity_decode(__($system['system_title']), ENT_QUOTES);
    $body = get_email_template("invitation_user_email", $subject, ["code" => $code]);
    /* send email */
    if (!_email($email, $subject, $body['html'], $body['plain'])) {
      throw new Exception(__("Invitation email could not be sent"));
    }
    /* add to log */
    $db->query(sprintf("INSERT INTO users_invitations (user_id, email_phone, invitation_date) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($email), secure($date))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * send_invitation_sms
   * 
   * @param string $phone
   * @param string $code
   * @return void
   */
  public function send_invitation_sms($phone, $code)
  {
    global $db, $system, $date;
    /* check invitation enabled */
    if (!$system['invitation_enabled']) {
      throw new Exception(__("The invitation system has been disabled by the admin"));
    }
    /* check invitation permission */
    if (!$this->_data['can_invite_users']) {
      throw new Exception(__("You don't have the permission to do this"));
    }
    /* check invitation code */
    if (!$this->check_invitation_code($code)) {
      throw new Exception(__("This invitation code is expired or invalid"));
    }
    /* check phone */
    if (is_empty($phone)) {
      throw new Exception(__("Please enter a valid phone number"));
    }
    if ($this->check_phone($phone)) {
      throw new Exception(__("Sorry, it looks like") . " " . $phone . " " . __("belongs to an existing account"));
    }
    /* check if viewer already invited this phone before */
    $check_invitation_log = $db->query(sprintf("SELECT COUNT(*) as count FROM users_invitations WHERE email_phone = %s", secure($phone))) or _error('SQL_ERROR_THROWEN');
    if ($check_invitation_log->fetch_assoc()['count'] > 0) {
      throw new Exception(__("You already invited this phone number before"));
    }
    /* prepare activation SMS */
    $message = $this->_data['name'] . " " . __("Invite you to join") . " " . __($system['system_title']) . " " . $system['system_url'] . "/?ref=" . $this->_data['user_name'] . "&invitation_code=" . $code;
    /* send SMS */
    if (!sms_send($phone, $message)) {
      throw new Exception(__("Invitation SMS could not be sent"));
    }
    /* add to log */
    $db->query(sprintf("INSERT INTO users_invitations (user_id, email_phone, invitation_date) VALUES (%s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($phone), secure($date))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* User Settings */
  /* ------------------------------- */

  /**
   * settings
   * 
   * @param string $edit
   * @param array $args
   * @return void
   */
  public function settings($edit, $args)
  {
    global $db, $system;
    switch ($edit) {
      case 'account':
        /* validate current password (MD5 check for versions < v2.5) */
        if (md5($args['password']) != $this->_data['user_password'] && !password_verify($args['password'], $this->_data['user_password'])) {
          throw new Exception(__("Your current password is incorrect"));
        }
        /* validate email */
        if ($args['email'] != $this->_data['user_email']) {
          $this->activation_email_reset($args['email']);
        }
        /* validate phone */
        if (isset($args['phone']) && $args['phone'] != $this->_data['user_phone']) {
          $this->activation_phone_reset($args['phone']);
        }
        /* validate username */
        if (strtolower($args['username']) != strtolower($this->_data['user_name'])) {
          if (!valid_username($args['username'])) {
            throw new Exception(__("Please enter a valid username (a-z0-9_.) with minimum 3 characters long"));
          }
          if ($this->reserved_username($args['username'])) {
            throw new Exception(__("You can't use") . " " . $args['username'] . " " . __("as username"));
          }
          if ($this->check_username($args['username'])) {
            throw new Exception(__("Sorry, it looks like") . " " . $args['username'] . " " . __("belongs to an existing account"));
          }
          /* update user */
          $db->query(sprintf("UPDATE users SET user_name = %s WHERE user_id = %s", secure($args['username']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* verification badge */
          if ($this->_data['user_verified']) {
            $db->query(sprintf("UPDATE users SET user_verified = '0' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
        }
        break;

      case 'basic':
        if (!$system['show_usernames_enabled']) {
          /* validate firstname */
          if (is_empty($args['firstname'])) {
            throw new Exception(__("You must enter first name"));
          }
          if (!valid_name($args['firstname'])) {
            throw new Exception(__("First name contains invalid characters"));
          }
          if (strlen($args['firstname']) < $system['name_min_length']) {
            throw new Exception(__("First name must be at least") . " " . $system['name_min_length'] . " " . __("characters long. Please try another"));
          }
          /* validate lastname */
          if (is_empty($args['lastname'])) {
            throw new Exception(__("You must enter last name"));
          }
          if (!valid_name($args['lastname'])) {
            throw new Exception(__("Last name contains invalid characters"));
          }
          if (strlen($args['lastname']) < $system['name_min_length']) {
            throw new Exception(__("Last name must be at least") . " " . $system['name_min_length'] . " " . __("characters long. Please try another"));
          }
        } else {
          $args['firstname'] = $this->_data['user_firstname'];
          $args['lastname'] = $this->_data['user_lastname'];
        }
        /* validate gender */
        $args['gender'] = ($system['genders_disabled']) ? 1 : $args['gender'];
        if (!$system['genders_disabled'] && !$this->check_gender($args['gender'])) {
          throw new Exception(__("Please select a valid gender"));
        }
        /* validate country */
        if ($args['country'] == "none") {
          throw new Exception(__("You must select valid country"));
        } else {
          if (!$this->check_country($args['country'])) {
            throw new Exception(__("You must select valid country"));
          }
        }
        /* validate birthdate */
        if ($args['birth_month'] == "none" && $args['birth_day'] == "none" && $args['birth_year'] == "none") {
          $args['birth_date'] = 'null';
        } else {
          if (!in_array($args['birth_month'], range(1, 12))) {
            throw new Exception(__("Please select a valid birth month"));
          }
          if (!in_array($args['birth_day'], range(1, 31))) {
            throw new Exception(__("Please select a valid birth day"));
          }
          if (!in_array($args['birth_year'], range(1905, 2015))) {
            throw new Exception(__("Please select a valid birth year"));
          }
          $args['birth_date'] = $args['birth_year'] . '-' . $args['birth_month'] . '-' . $args['birth_day'];
        }
        /* validate relationship */
        if (!isset($args['relationship']) || $args['relationship'] == "none") {
          $args['relationship'] = 'null';
        } else {
          $relationships = ['single', 'relationship', 'married', "complicated", 'separated', 'divorced', 'widowed'];
          if (!in_array($args['relationship'], $relationships)) {
            throw new Exception(__("Please select a valid relationship"));
          }
        }
        /* validate website */
        if (!is_empty($args['website'])) {
          if (!valid_url($args['website'])) {
            throw new Exception(__("Please enter a valid website"));
          }
        } else {
          $args['website'] = 'null';
        }
        /* set custom fields */
        $this->set_custom_fields($args, "user", "settings", $this->_data['user_id']);
        /* update user */
        $db->query(sprintf("UPDATE users SET user_firstname = %s, user_lastname = %s, user_gender = %s, user_country = %s, user_birthdate = %s, user_relationship = %s, user_biography = %s, user_website = %s WHERE user_id = %s", secure($args['firstname']), secure($args['lastname']), secure($args['gender']), secure($args['country'], 'int'), secure($args['birth_date']), secure($args['relationship']), secure($args['biography']), secure($args['website']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* verification badge */
        if ($this->_data['user_verified'] && ($this->_data['user_firstname'] !=  $args['firstname'] || $this->_data['user_lastname'] !=  $args['lastname'])) {
          $db->query(sprintf("UPDATE users SET user_verified = '0' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        }
        break;

      case 'work':
        /* validate work website */
        if (!is_empty($args['work_url'])) {
          if (!valid_url($args['work_url'])) {
            throw new Exception(__("Please enter a valid work website"));
          }
        } else {
          $args['work_url'] = 'null';
        }
        /* set custom fields */
        $this->set_custom_fields($args, "user", "settings", $this->_data['user_id']);
        /* update user */
        $db->query(sprintf("UPDATE users SET user_work_title = %s, user_work_place = %s, user_work_url = %s WHERE user_id = %s", secure($args['work_title']), secure($args['work_place']), secure($args['work_url']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'location':
        /* set custom fields */
        $this->set_custom_fields($args, "user", "settings", $this->_data['user_id']);
        /* update user */
        $db->query(sprintf("UPDATE users SET user_current_city = %s, user_hometown = %s WHERE user_id = %s", secure($args['city']), secure($args['hometown']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'education':
        /* set custom fields */
        $this->set_custom_fields($args, "user", "settings", $this->_data['user_id']);
        /* update user */
        $db->query(sprintf("UPDATE users SET user_edu_major = %s, user_edu_school = %s, user_edu_class = %s WHERE user_id = %s", secure($args['edu_major']), secure($args['edu_school']), secure($args['edu_class']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'other':
        /* set custom fields */
        $this->set_custom_fields($args, "user", "settings", $this->_data['user_id']);
        break;

      case 'social':
        /* validate facebook */
        if (!is_empty($args['facebook']) && !valid_url($args['facebook'])) {
          throw new Exception(__("Please enter a valid Facebook Profile URL"));
        }
        /* validate twitter */
        if (!is_empty($args['twitter']) && !valid_url($args['twitter'])) {
          throw new Exception(__("Please enter a valid Twitter Profile URL"));
        }
        /* validate youtube */
        if (!is_empty($args['youtube']) && !valid_url($args['youtube'])) {
          throw new Exception(__("Please enter a valid YouTube Profile URL"));
        }
        /* validate instagram */
        if (!is_empty($args['instagram']) && !valid_url($args['instagram'])) {
          throw new Exception(__("Please enter a valid Instagram Profile URL"));
        }
        /* validate twitch */
        if (!is_empty($args['twitch']) && !valid_url($args['twitch'])) {
          throw new Exception(__("Please enter a valid Twitch Profile URL"));
        }
        /* validate linkedin */
        if (!is_empty($args['linkedin']) && !valid_url($args['linkedin'])) {
          throw new Exception(__("Please enter a valid Linkedin Profile URL"));
        }
        /* validate vkontakte */
        if (!is_empty($args['vkontakte']) && !valid_url($args['vkontakte'])) {
          throw new Exception(__("Please enter a valid Vkontakte Profile URL"));
        }
        /* update user */
        $db->query(sprintf("UPDATE users SET user_social_facebook = %s, user_social_twitter = %s, user_social_youtube = %s, user_social_instagram = %s, user_social_twitch = %s, user_social_linkedin = %s, user_social_vkontakte = %s WHERE user_id = %s", secure($args['facebook']), secure($args['twitter']), secure($args['youtube']), secure($args['instagram']), secure($args['twitch']), secure($args['linkedin']), secure($args['vkontakte']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'design':
        /* check if profile background enabled */
        if (!$system['system_profile_background_enabled']) {
          throw new Exception(__("The profile background feature has been disabled by the admin"));
        }
        /* update user */
        $db->query(sprintf("UPDATE users SET user_profile_background = %s WHERE user_id = %s", secure($args['user_profile_background']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'password':
        /* validate all fields */
        if (is_empty($args['current']) || is_empty($args['new']) || is_empty($args['confirm'])) {
          throw new Exception(__("You must fill in all of the fields"));
        }
        /* validate current password (MD5 check for versions < v2.5) */
        if (md5($args['current']) != $this->_data['user_password'] && !password_verify($args['current'], $this->_data['user_password'])) {
          throw new Exception(__("Your current password is incorrect"));
        }
        /* validate new password */
        if ($args['new'] != $args['confirm']) {
          throw new Exception(__("Your passwords do not match"));
        }
        if (strlen($args['new']) < 6) {
          throw new Exception(__("Your password must be at least 6 characters long. Please try another"));
        }
        if (strlen($args['new']) > 64) {
          throw new Exception(__("Your password must be less than 64 characters long. Please try another"));
        }
        /* update user */
        $db->query(sprintf("UPDATE users SET user_password = %s WHERE user_id = %s", secure(_password_hash($args['new'])), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* delete sessions */
        $db->query(sprintf("DELETE FROM users_sessions WHERE session_id != %s AND user_id = %s", secure($this->_data['active_session_id']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'two-factor':
        if ($system['two_factor_type'] != $args['type']) {
          _error(400);
        }
        /* prepare */
        $args['two_factor_enabled'] = (isset($args['two_factor_enabled'])) ? '1' : '0';
        switch ($system['two_factor_type']) {
          case 'email':
            if ($args['two_factor_enabled'] && !$this->_data['user_email_verified']) {
              throw new Exception(__("Two-Factor Authentication can't be enabled till you verify your email address"));
            }
            break;

          case 'sms':
            if ($args['two_factor_enabled'] && !$this->_data['user_phone']) {
              throw new Exception(__("Two-Factor Authentication can't be enabled till you enter your phone number"));
            }
            if ($args['two_factor_enabled'] && !$this->_data['user_phone_verified']) {
              throw new Exception(__("Two-Factor Authentication can't be enabled till you verify your phone number"));
            }
            break;

          case 'google':
            if (isset($args['gcode'])) {
              /* Google Authenticator */
              $ga = new Sonata\GoogleAuthenticator\GoogleAuthenticator();
              /* verify code */
              if (!$ga->checkCode($this->_data['user_two_factor_gsecret'], $args['gcode'])) {
                throw new Exception(__("Invalid code, Try again or try to scan your QR code again"));
              }
              $args['two_factor_enabled'] = '1';
            }
            break;
        }
        /* update user */
        $db->query(sprintf("UPDATE users SET user_two_factor_enabled = %s, user_two_factor_type = %s WHERE user_id = %s", secure($args['two_factor_enabled']), secure($system['two_factor_type']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'privacy':
        /* prepare */
        $privacy = ['me', 'friends', 'public'];
        /* check if chat enabled */
        $args['user_chat_enabled'] = (isset($args['user_chat_enabled'])) ? '1' : '0';
        /* check if newsletter enabled */
        $args['user_newsletter_enabled'] = (isset($args['user_newsletter_enabled'])) ? '1' : '0';
        /* check if tips enabled */
        $args['user_tips_enabled'] = (isset($args['user_tips_enabled'])) ? '1' : '0';
        /* check if pokes enabled */
        $args['user_privacy_poke'] = (isset($args['user_privacy_poke'])) ? $args['user_privacy_poke'] : 'public';
        /* check if gifts enabled */
        $args['user_privacy_gifts'] = (isset($args['user_privacy_gifts'])) ? $args['user_privacy_gifts'] : 'public';
        /* check if wall posts enabled */
        $args['user_privacy_wall'] = (isset($args['user_privacy_wall'])) ? $args['user_privacy_wall'] : 'public';
        /* check if genders enabled */
        $args['user_privacy_gender'] = (isset($args['user_privacy_gender'])) ? $args['user_privacy_gender'] : 'public';
        /* check if relationship info enabled */
        $args['user_privacy_relationship'] = (isset($args['user_privacy_relationship'])) ? $args['user_privacy_relationship'] : 'public';
        /* check if work info enabled */
        $args['user_privacy_work'] = (isset($args['user_privacy_work'])) ? $args['user_privacy_work'] : 'public';
        /* check if location info enabled */
        $args['user_privacy_location'] = (isset($args['user_privacy_location'])) ? $args['user_privacy_location'] : 'public';
        /* check if education info enabled */
        $args['user_privacy_education'] = (isset($args['user_privacy_education'])) ? $args['user_privacy_education'] : 'public';
        /* check if subscriptions enabled */
        $args['user_privacy_subscriptions'] = (isset($args['user_privacy_subscriptions'])) ? $args['user_privacy_subscriptions'] : 'public';
        /* check if pages enabled */
        $args['user_privacy_pages'] = (isset($args['user_privacy_pages'])) ? $args['user_privacy_pages'] : 'public';
        /* check if groups enabled */
        $args['user_privacy_groups'] = (isset($args['user_privacy_groups'])) ? $args['user_privacy_groups'] : 'public';
        /* check if events enabled */
        $args['user_privacy_events'] = (isset($args['user_privacy_events'])) ? $args['user_privacy_events'] : 'public';
        /* check if valid privacy */
        if (!in_array($args['user_privacy_poke'], $privacy) || !in_array($args['user_privacy_gifts'], $privacy) || !in_array($args['user_privacy_wall'], $privacy) || !in_array($args['user_privacy_gender'], $privacy) || !in_array($args['user_privacy_relationship'], $privacy) || !in_array($args['user_privacy_birthdate'], $privacy) || !in_array($args['user_privacy_basic'], $privacy) || !in_array($args['user_privacy_work'], $privacy) || !in_array($args['user_privacy_location'], $privacy) || !in_array($args['user_privacy_education'], $privacy) || !in_array($args['user_privacy_other'], $privacy) || !in_array($args['user_privacy_friends'], $privacy) || !in_array($args['user_privacy_followers'], $privacy) || !in_array($args['user_privacy_subscriptions'], $privacy) || !in_array($args['user_privacy_photos'], $privacy) || !in_array($args['user_privacy_pages'], $privacy) || !in_array($args['user_privacy_groups'], $privacy) || !in_array($args['user_privacy_events'], $privacy)) {
          _error(400);
        }
        /* update user */
        $db->query(sprintf(
          "UPDATE users SET 
                    user_chat_enabled = %s, 
                    user_newsletter_enabled = %s,
                    user_tips_enabled = %s,
                    user_privacy_poke = %s,
                    user_privacy_gifts = %s,
                    user_privacy_wall = %s,
                    user_privacy_gender = %s, 
                    user_privacy_relationship = %s,
                    user_privacy_birthdate = %s, 
                    user_privacy_basic = %s, 
                    user_privacy_work = %s, 
                    user_privacy_location = %s, 
                    user_privacy_education = %s, 
                    user_privacy_other = %s, 
                    user_privacy_friends = %s, 
                    user_privacy_followers = %s, 
                    user_privacy_subscriptions = %s, 
                    user_privacy_photos = %s, 
                    user_privacy_pages = %s, 
                    user_privacy_groups = %s, 
                    user_privacy_events = %s 
                    WHERE user_id = %s",
          secure($args['user_chat_enabled']),
          secure($args['user_newsletter_enabled']),
          secure($args['user_tips_enabled']),
          secure($args['user_privacy_poke']),
          secure($args['user_privacy_gifts']),
          secure($args['user_privacy_wall']),
          secure($args['user_privacy_gender']),
          secure($args['user_privacy_relationship']),
          secure($args['user_privacy_birthdate']),
          secure($args['user_privacy_basic']),
          secure($args['user_privacy_work']),
          secure($args['user_privacy_location']),
          secure($args['user_privacy_education']),
          secure($args['user_privacy_other']),
          secure($args['user_privacy_friends']),
          secure($args['user_privacy_followers']),
          secure($args['user_privacy_subscriptions']),
          secure($args['user_privacy_photos']),
          secure($args['user_privacy_pages']),
          secure($args['user_privacy_groups']),
          secure($args['user_privacy_events']),
          secure($this->_data['user_id'], 'int')
        )) or _error('SQL_ERROR_THROWEN');
        break;

      case 'notifications':
        /* prepare */
        $args['chat_sound'] = (isset($args['chat_sound'])) ? '1' : '0';
        $args['notifications_sound'] = (isset($args['notifications_sound'])) ? '1' : '0';
        $args['email_post_likes'] = (isset($args['email_post_likes'])) ? '1' : '0';
        $args['email_post_comments'] = (isset($args['email_post_comments'])) ? '1' : '0';
        $args['email_post_shares'] = (isset($args['email_post_shares'])) ? '1' : '0';
        $args['email_wall_posts'] = (isset($args['email_wall_posts'])) ? '1' : '0';
        $args['email_mentions'] = (isset($args['email_mentions'])) ? '1' : '0';
        $args['email_profile_visits'] = (isset($args['email_profile_visits'])) ? '1' : '0';
        $args['email_friend_requests'] = (isset($args['email_friend_requests'])) ? '1' : '0';
        $args['email_user_verification'] = (isset($args['email_user_verification'])) ? '1' : '0';
        $args['email_user_post_approval'] = (isset($args['email_user_post_approval'])) ? '1' : '0';
        $args['email_admin_verifications'] = (isset($args['email_admin_verifications'])) ? '1' : '0';
        $args['email_admin_post_approval'] = (isset($args['email_admin_post_approval'])) ? '1' : '0';
        /* update user */
        $db->query(sprintf("UPDATE users SET chat_sound = %s, notifications_sound = %s, email_post_likes = %s, email_post_comments = %s, email_post_shares = %s, email_wall_posts = %s, email_mentions = %s, email_profile_visits = %s, email_friend_requests = %s, email_user_verification = %s, email_user_post_approval = %s, email_admin_verifications = %s, email_admin_post_approval = %s WHERE user_id = %s", secure($args['chat_sound']), secure($args['notifications_sound']), secure($args['email_post_likes']), secure($args['email_post_comments']), secure($args['email_post_shares']), secure($args['email_wall_posts']), secure($args['email_mentions']), secure($args['email_profile_visits']), secure($args['email_friend_requests']), secure($args['email_user_verification']), secure($args['email_user_post_approval']), secure($args['email_admin_verifications']), secure($args['email_admin_post_approval']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'notifications_sound':
        /* prepare */
        $args['notifications_sound'] = ($args['notifications_sound'] == 0) ? 0 : 1;
        /* update user */
        $db->query(sprintf("UPDATE users SET notifications_sound = %s WHERE user_id = %s", secure($args['notifications_sound']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'chat':
        /* prepare */
        $args['user_chat_enabled'] = ($args['user_chat_enabled'] == 0) ? 0 : 1;
        /* update user */
        $db->query(sprintf("UPDATE users SET user_chat_enabled = %s WHERE user_id = %s", secure($args['user_chat_enabled']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'membership':
        /* videos */
        $videos_query = "";
        if ($this->_data['allowed_videos_categories'] > 0) {
          $package_videos_categories = json_decode(html_entity_decode($args['package_videos_categories']), true);
          if (!$package_videos_categories || count($package_videos_categories) < $this->_data['allowed_videos_categories']) {
            throw new Exception(__("You must select at least") . " " . $this->_data['allowed_videos_categories'] . " " . __("videos categories"));
          }
          if (count($package_videos_categories) > $this->_data['allowed_videos_categories']) {
            throw new Exception(__("You can't select more than") . " " . $this->_data['allowed_videos_categories'] . " " . __("videos categories"));
          }
          $videos_query = "user_package_videos_categories = " . secure($args['package_videos_categories']);
        }
        /* blogs */
        $blogs_query = "";
        if ($this->_data['allowed_blogs_categories'] > 0) {
          $package_blogs_categories = json_decode(html_entity_decode($args['package_blogs_categories']), true);
          if (!$package_blogs_categories || count($package_blogs_categories) < $this->_data['allowed_blogs_categories']) {
            throw new Exception(__("You must select at least") . " " . $this->_data['allowed_blogs_categories'] . " " . __("blogs categories"));
          }
          if (count($package_blogs_categories) > $this->_data['allowed_blogs_categories']) {
            throw new Exception(__("You can't select more than") . " " . $this->_data['allowed_blogs_categories'] . " " . __("blogs categories"));
          }
          $blogs_query = ($videos_query) ? ", " : "";
          $blogs_query .= "user_package_blogs_categories = " . secure($args['package_blogs_categories']);
        }
        /* update user */
        $db->query(sprintf("UPDATE users SET $videos_query $blogs_query WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'monetization':
        /* check monetization permission */
        if (!$this->_data['can_monetize_content']) {
          throw new Exception(__("The monetization system has been disabled by the admin"));
        }
        /* prepare */
        $args['user_monetization_enabled'] = (isset($args['user_monetization_enabled'])) ? '1' : '0';
        /* check if verification for monetization required */
        if ($args['user_monetization_enabled'] && $system['verification_for_monetization'] && !$this->_data['user_verified']) {
          throw new Exception(__("To enable this feature your account must be verified"));
        }
        /* update user */
        $db->query(sprintf("UPDATE users SET user_monetization_enabled = %s, user_monetization_chat_price = %s, user_monetization_call_price = %s WHERE user_id = %s", secure($args['user_monetization_enabled']), secure($args['user_monetization_chat_price'], 'float'), secure($args['user_monetization_call_price'], 'float'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        /* update monetization plans */
        $this->update_monetization_plans();
        break;
    }
  }



  /* ------------------------------- */
  /* Verifications */
  /* ------------------------------- */

  /**
   * update_node_verification_status
   * 
   * @param int $node_id
   * @param string $node_type
   * @param string $action
   * @return void
   */
  public function update_node_verification_status($node_id, $node_type = 'user', $action = 'verify')
  {
    global $db;
    if (!in_array($node_type, ['user', 'page'])) {
      throw new Exception(__("Invalid node type"));
    }
    if (!in_array($action, ['verify', 'unverify'])) {
      throw new Exception(__("Invalid action"));
    }
    $status = ($action == 'verify') ? '1' : '0';
    if ($node_type == 'page') {
      /* verify page */
      $db->query(sprintf("UPDATE pages SET page_verified = %s WHERE page_id = %s", secure($status, 'int'), secure($node_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    } else {
      /* verify user */
      $db->query(sprintf("UPDATE users SET user_verified = %s WHERE user_id = %s", secure($status, 'int'), secure($node_id, 'int'))) or _error("SQL_ERROR_THROWEN");
    }
    /* remove all verification requests */
    $db->query(sprintf("DELETE FROM verification_requests WHERE node_type = %s AND node_id = %s", secure($node_type), secure($node_id, 'int'))) or _error("SQL_ERROR_THROWEN");
  }


  /**
   * send_verification_request
   * 
   * @param int $node_id
   * @param string $node_type
   * @param array $inputs
   * @return void
   */
  public function send_verification_request($node_id, $node_type, $inputs)
  {
    global $system, $db, $date;
    /* check if node has any pending requests */
    $get_requests = $db->query(sprintf("SELECT COUNT(*) AS count FROM verification_requests WHERE node_id = %s AND node_type = %s AND `status` = '0'", secure($node_id, 'int'), secure($node_type))) or _error("SQL_ERROR_THROWEN");
    if ($get_requests->fetch_assoc()['count'] > 0) {
      throw new ValidationException(__("You have a pending verification request"));
    }
    /* check if the user is the page admin */
    if ($node_type == 'page' && !$this->check_page_adminship($this->_data['user_id'], $node_id)) {
      throw new ValidationException(__("You are not authorized to verify this page"));
    }
    /* valid inputs */
    if ($system['verification_docs_required']) {
      if (!isset($inputs['photo']) || is_empty($inputs['photo'])) {
        throw new ValidationException(($node_type == 'page') ? __("Please attach your company incorporation file") : __("Please attach your photo"));
      }
      if (!isset($inputs['passport']) || is_empty($inputs['passport'])) {
        throw new ValidationException(($node_type == 'page') ? __("Please attach your tax file") : __("Please attach your Passport or National ID"));
      }
    } else {
      $inputs['photo'] = '';
      $inputs['passport'] = '';
    }
    if (!isset($inputs['message']) || is_empty($inputs['message'])) {
      throw new ValidationException(($node_type == 'page') ?  __("Please share why your page should be verified") : __("Please share why your account should be verified"));
    }
    /* insert verification request */
    if ($node_type == 'user') {
      $db->query(sprintf("INSERT INTO verification_requests (node_id, node_type, photo, passport, message, time, status) VALUES (%s, 'user', %s, %s, %s, %s, '0')", secure($node_id, 'int'), secure($inputs['photo']), secure($inputs['passport']), secure($inputs['message']), secure($date))) or _error('SQL_ERROR_THROWEN');
    } else {
      $db->query(sprintf("INSERT INTO verification_requests (node_id, node_type, photo, passport, business_website, business_address, message, time, status) VALUES (%s, 'page', %s, %s, %s, %s, %s, %s, '0')", secure($node_id, 'int'), secure($inputs['photo']), secure($inputs['passport']), secure($inputs['business_website']), secure($inputs['business_address']), secure($inputs['message']), secure($date))) or _error('SQL_ERROR_THROWEN');
    }
    /* send notification to admins & moderators */
    $this->notify_system_admins("verification_request", true);
  }


  /**
   * delete_all_verification_requests
   * 
   * @param int $node_id
   * @param string $node_type
   * @return void
   */
  public function delete_all_verification_requests($node_id, $node_type)
  {
    global $db;
    $db->query(sprintf("DELETE FROM verification_requests WHERE node_id = %s AND node_type = %s", secure($node_id, 'int'), secure($node_type))) or _error("SQL_ERROR_THROWEN");
  }



  /* ------------------------------- */
  /* Addresses */
  /* ------------------------------- */

  /**
   * get_addresses
   * 
   * @param array $args
   * @return array
   */
  public function get_addresses()
  {
    global $db;
    $addresses = [];
    $get_addresses = $db->query(sprintf("SELECT * FROM users_addresses WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($get_addresses->num_rows > 0) {
      while ($address = $get_addresses->fetch_assoc()) {
        $addresses[] = $address;
      }
    }
    return $addresses;
  }


  /**
   * get_address
   * 
   * @param int $address_id
   * @return array
   */
  public function get_address($address_id)
  {
    global $db, $system;
    $get_address = $db->query(sprintf("SELECT * FROM users_addresses WHERE address_id = %s AND user_id = %s", secure($address_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($get_address->num_rows == 0) {
      throw new NoDataException(__("No data found"));
    }
    $address = $get_address->fetch_assoc();
    return $address;
  }


  /**
   * insert_address
   * 
   * @param string $title
   * @param string $country
   * @param string $city
   * @param string $zip_code
   * @param string $phone
   * @param string $address
   * @return void
   */
  public function insert_address($title, $country, $city, $zip_code, $phone, $address)
  {
    global $db, $system;
    /* validate title */
    if (is_empty($title)) {
      throw new ValidationException(__("You must enter the title"));
    }
    /* validate country */
    if (is_empty($country)) {
      throw new ValidationException(__("You must enter the country"));
    }
    /* validate city */
    if (is_empty($city)) {
      throw new ValidationException(__("You must enter the city"));
    }
    /* validate zip code */
    if (is_empty($zip_code)) {
      throw new ValidationException(__("You must enter the zip code"));
    }
    /* validate phone */
    if (is_empty($phone)) {
      throw new ValidationException(__("You must enter the phone"));
    }
    /* validate address */
    if (is_empty($address)) {
      throw new ValidationException(__("You must enter the address"));
    }
    /* insert */
    $db->query(sprintf("INSERT INTO users_addresses (user_id, address_title, address_country, address_city, address_zip_code, address_phone, address_details) VALUES (%s, %s, %s, %s, %s, %s, %s)", secure($this->_data['user_id'], 'int'), secure($title), secure($country), secure($city), secure($zip_code), secure($phone), secure($address))) or _error("SQL_ERROR_THROWEN");
  }


  /**
   * update_address
   * 
   * @param int $address_id
   * @param string $title
   * @param string $country
   * @param string $city
   * @param string $zip_code
   * @param string $phone
   * @param string $address
   * @return void
   */
  public function update_address($address_id, $title, $country, $city, $zip_code, $phone, $address)
  {
    global $db;
    /* check if this address belongs to the user */
    $check_address = $db->query(sprintf("SELECT COUNT(*) AS count FROM users_addresses WHERE address_id = %s AND user_id = %s", secure($address_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
    if ($check_address->fetch_assoc()['count'] == 0) {
      throw new AuthorizationException(__("You are not authorized to do this action"));
    }
    /* validate title */
    if (is_empty($title)) {
      throw new ValidationException(__("You must enter the title"));
    }
    /* validate country */
    if (is_empty($country)) {
      throw new ValidationException(__("You must enter the country"));
    }
    /* validate city */
    if (is_empty($city)) {
      throw new ValidationException(__("You must enter the city"));
    }
    /* validate zip code */
    if (is_empty($zip_code)) {
      throw new ValidationException(__("You must enter the zip code"));
    }
    /* validate phone */
    if (is_empty($phone)) {
      throw new ValidationException(__("You must enter the phone"));
    }
    /* validate address */
    if (is_empty($address)) {
      throw new ValidationException(__("You must enter the address"));
    }
    /* update */
    $db->query(sprintf("UPDATE users_addresses SET address_title = %s, address_country = %s, address_city = %s, address_zip_code = %s, address_phone = %s, address_details = %s WHERE address_id = %s AND user_id = %s", secure($title), secure($country), secure($city), secure($zip_code), secure($phone), secure($address), secure($address_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
  }


  /**
   * delete_address
   * 
   * @param int $address_id
   * @return void
   */
  public function delete_address($address_id)
  {
    global $db;
    $db->query(sprintf("DELETE FROM users_addresses WHERE address_id = %s AND user_id = %s", secure($address_id, 'int'), secure($this->_data['user_id'], 'int'))) or _error("SQL_ERROR_THROWEN");
  }



  /* ------------------------------- */
  /* Download User Information */
  /* ------------------------------- */

  /**
   * download_user_information
   * 
   * @return array
   */
  public function download_user_information()
  {
    global $db, $system, $smarty;
    $user_data = [];
    /* information */
    if ($_SESSION['download_information']) {
      $this->_data['user_gender'] = $this->get_gender($this->_data['user_gender']);
      $user_data['information'] = $this->_data;
      /* user sessions */
      $sessions = [];
      $get_sessions = $db->query(sprintf("SELECT * FROM users_sessions WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_sessions->num_rows > 0) {
        while ($session = $get_sessions->fetch_assoc()) {
          $sessions[] = $session;
        }
      }
      $user_data['information']['sessions'] = $sessions;
    }
    /* friends */
    if ($_SESSION['download_friends']) {
      $user_data['friends'] = $this->get_friends($this->_data['user_id'], 0, true);
    }
    /* followings */
    if ($_SESSION['download_followings']) {
      $user_data['followings'] = $this->get_followings($this->_data['user_id'], 0, true);
    }
    /* followers */
    if ($_SESSION['download_followers']) {
      $user_data['followers'] = $this->get_followers($this->_data['user_id'], 0, true);
    }
    /* pages */
    if ($_SESSION['download_pages']) {
      $user_data['pages'] = $this->get_pages(['managed' => true, 'user_id' => $this->_data['user_id']]);
    }
    /* groups */
    if ($_SESSION['download_groups']) {
      $user_data['groups'] = $this->get_groups(['get_all' => true, 'user_id' => $this->_data['user_id']]);
    }
    /* events */
    if ($_SESSION['download_events']) {
      $user_data['events'] = $this->get_events(['get_all' => true, 'user_id' => $this->_data['user_id']]);
    }
    /* posts */
    if ($_SESSION['download_posts']) {
      $user_data['posts'] = $this->get_posts(['get_all' => true, 'get' => 'posts_profile', 'id' => $this->_data['user_id']]);
    }
    /* assign variables */
    $smarty->assign('user_data', $user_data);
    /* return */
    $html_file = $smarty->fetch("information.tpl");
    header('Content-disposition: attachment; filename=' . $this->_data['user_name'] . ".html");
    header('Content-type: text/html');
    print($html_file);
    exit;
  }



  /* ------------------------------- */
  /* User Sign (in|up|out) ✅ */
  /* ------------------------------- */

  /**
   * sign_up
   * 
   * @param array $args
   * @param array $device_info
   * @return void
   */
  public function sign_up($args = [], $device_info = [])
  {
    global $db, $system, $date;
    /* prepare */
    $args['from_web'] = (isset($args['from_web'])) ? $args['from_web'] : true;
    /* check invitation code */
    if ($system['invitation_enabled']) {
      if (!$this->check_invitation_code($args['invitation_code'])) {
        throw new ValidationException(__("The invitation code is invalid or expired"));
      }
    }
    /* check IP */
    $this->_check_ip();
    /* validate */
    if ($system['show_usernames_enabled']) {
      $args['first_name'] = $args['username'];
      $args['last_name'] = $args['username'];
    } else {
      if (!valid_name($args['first_name'])) {
        throw new ValidationException(__("Your first name contains invalid characters"));
      }
      if (strlen($args['first_name']) < $system['name_min_length']) {
        throw new ValidationException(__("Your first name must be at least") . " " . $system['name_min_length'] . " " . __("characters long. Please try another"));
      }
      if (!valid_name($args['last_name'])) {
        throw new ValidationException(__("Your last name contains invalid characters"));
      }
      if (strlen($args['last_name']) < $system['name_min_length']) {
        throw new ValidationException(__("Your last name must be at least") . " " . $system['name_min_length'] . " " . __("characters long. Please try another"));
      }
    }
    if (!valid_username($args['username'])) {
      throw new ValidationException(__("Please enter a valid username (a-z0-9_.) with minimum 3 characters long"));
    }
    if ($this->reserved_username($args['username'])) {
      throw new ValidationException(__("You can't use") . " " . $args['username'] . " " . __("as username"));
    }
    if ($this->check_username($args['username'])) {
      throw new ValidationException(__("Sorry, it looks like") . " " . $args['username'] . " " . __("belongs to an existing account"));
    }
    if ($system['activation_type'] == "email") {
      if (!valid_email($args['email'])) {
        throw new ValidationException(__("Please enter a valid email address"));
      }
      if ($this->check_email($args['email'])) {
        throw new ValidationException(__("Sorry, it looks like") . " " . $args['email'] . " " . __("belongs to an existing account"));
      }
    } else {
      /* generate random email username@hostname */
      $args['email'] = $args['username'] . "@" . parse_url($system['system_url'], PHP_URL_HOST);
    }
    if ($system['activation_enabled'] && $system['activation_type'] == "sms") {
      if (is_empty($args['phone'])) {
        throw new ValidationException(__("Please enter a valid phone number"));
      }
      if ($this->check_phone($args['phone'])) {
        throw new ValidationException(__("Sorry, it looks like") . " " . $args['phone'] . " " . __("belongs to an existing account"));
      }
    } else {
      $args['phone'] = 'null';
    }
    if (strlen($args['password']) < 6) {
      throw new ValidationException(__("Your password must be at least 6 characters long. Please try another"));
    }
    if (strlen($args['password']) > 64) {
      throw new ValidationException(__("Your password must be less than 64 characters long. Please try another"));
    }
    $args['gender'] = ($system['genders_disabled']) ? 1 : $args['gender'];
    if (!$system['genders_disabled'] && !$this->check_gender($args['gender'])) {
      throw new ValidationException(__("Please select a valid gender"));
    }
    /* check age restriction */
    if ($system['age_restriction']) {
      if (!in_array($args['birth_month'], range(1, 12))) {
        throw new ValidationException(__("Please select a valid birth month (1-12)"));
      }
      if (!in_array($args['birth_day'], range(1, 31))) {
        throw new ValidationException(__("Please select a valid birth day (1-31)"));
      }
      if (!in_array($args['birth_year'], range(1905, 2017))) {
        throw new ValidationException(__("Please select a valid birth year (1905-2017)"));
      }
      if (date("Y") - $args['birth_year'] < $system['minimum_age']) {
        throw new ValidationException(__("Sorry, You must be") . " " . $system['minimum_age'] . " " . __("years old to register"));
      }
      $args['birth_date'] = $args['birth_year'] . '-' . $args['birth_month'] . '-' . $args['birth_day'];
    } else {
      $args['birth_date'] = 'null';
    }
    /* set custom fields */
    $custom_fields = $this->set_custom_fields($args);
    /* check reCAPTCHA */
    if ($system['reCAPTCHA_enabled'] && $args['from_web']) {
      $recaptcha = new \ReCaptcha\ReCaptcha($system['reCAPTCHA_secret_key'], new \ReCaptcha\RequestMethod\CurlPost());
      $resp = $recaptcha->verify($args['g-recaptcha-response'], get_user_ip());
      if (!$resp->isSuccess()) {
        throw new ValidationException(__("The security check is incorrect. Please try again"));
      }
    }
    /* check newsletter agreement */
    $newsletter_agree = (isset($args['newsletter_agree'])) ? '1' : '0';
    /* check privacy agreement */
    if (!isset($args['privacy_agree']) && $args['from_web']) {
      throw new ValidationException(__("You must read and agree to our terms and privacy policy"));
    }
    /* generate verification code */
    $email_verification_code = ($system['activation_enabled'] && $system['activation_type'] == "email") ? get_hash_key(6, true) : 'null';
    $phone_verification_code = ($system['activation_enabled'] && $system['activation_type'] == "sms") ? get_hash_key(6, true) : 'null';
    /* set custom user group */
    $custom_user_group = ($system['default_custom_user_group'] != '0' && $this->check_user_group($system['default_custom_user_group'])) ? $system['default_custom_user_group'] : '0';
    /* register user */
    $db->query(sprintf("INSERT INTO users (user_group_custom, user_name, user_email, user_phone, user_password, user_firstname, user_lastname, user_gender, user_birthdate, user_registered, user_email_verification_code, user_phone_verification_code, user_newsletter_enabled) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", secure($custom_user_group), secure($args['username']), secure($args['email']), secure($args['phone']), secure(_password_hash($args['password'])), secure(ucwords($args['first_name'])), secure(ucwords($args['last_name'])), secure($args['gender']), secure($args['birth_date']), secure($date), secure($email_verification_code), secure($phone_verification_code), secure($newsletter_agree))) or _error('SQL_ERROR_THROWEN');
    /* get user_id */
    $user_id = $db->insert_id;
    /* insert custom fields values */
    if ($custom_fields) {
      foreach ($custom_fields as $field_id => $value) {
        $db->query(sprintf("INSERT INTO custom_fields_values (value, field_id, node_id, node_type) VALUES (%s, %s, %s, 'user')", secure($value), secure($field_id, 'int'), secure($user_id, 'int')));
      }
    }
    /* send activation */
    if ($system['activation_enabled']) {
      if ($system['activation_type'] == "email") {
        /* prepare activation email */
        $subject = __("Just one more step to get started on") . " " . html_entity_decode(__($system['system_title']), ENT_QUOTES);
        $body = get_email_template("activation_email", $subject, ["first_name" => $args['first_name'], "last_name" => $args['last_name'], "email_verification_code" => $email_verification_code]);
        /* send email */
        if (!_email($args['email'], $subject, $body['html'], $body['plain'])) {
          throw new Exception(__("Activation email could not be sent") . ", " . __("But you can login now"));
        }
      } else {
        /* prepare activation SMS */
        $message  = __($system['system_title']) . " " . __("Activation Code") . ": " . $phone_verification_code;
        /* send SMS */
        if (!sms_send($args['phone'], $message)) {
          throw new Exception(__("Activation SMS could not be sent") . ", " . __("But you can login now"));
        }
      }
    } else {
      /* affiliates system (as activation disabled) */
      $this->process_affiliates("registration", $user_id);
    }
    /* update invitation code */
    if ($system['invitation_enabled']) {
      $this->update_invitation_code($args['invitation_code'], $user_id);
    }
    /* auto connect */
    $this->auto_friend($user_id);
    $this->auto_follow($user_id);
    $this->auto_like($user_id);
    $this->auto_join($user_id);
    /* set authentication */
    if ($args['from_web']) {
      $this->_set_authentication_cookies($user_id);
    } else {
      /* create JWT */
      $jwt = $this->_set_authentication_JWT($user_id, $device_info);
      /* create new user object */
      $user = new User($jwt);
      return ['token' => $jwt, 'user' => $user->_data];
    }
  }


  /**
   * sign_in
   * 
   * @param string $username_email
   * @param string $password
   * @param boolean $remember
   * @param boolean $from_web
   * @param array $device_info
   * @param boolean $connecting_account
   * 
   * @return void
   */
  public function sign_in($username_email, $password, $remember = false, $from_web = false, $device_info = [], $connecting_account = false)
  {
    global $db, $system, $date;
    /* valid inputs */
    $username_email = trim($username_email);
    if (is_empty($username_email) || is_empty($password)) {
      throw new ValidationException(__("You must fill in all of the fields"));
    }
    /* check if username or email */
    if (valid_email($username_email)) {
      $user = $this->check_email($username_email, true);
      if ($user === false) {
        throw new ValidationException(__("The email you entered does not belong to any account"));
      }
      $field = "user_email";
    } else {
      if (!valid_username($username_email)) {
        throw new ValidationException(__("Please enter a valid email address or username"));
      }
      $user = $this->check_username($username_email, 'user', true);
      if ($user === false) {
        throw new ValidationException(__("The username you entered does not belong to any account"));
      }
      $field = "user_name";
    }
    /* check brute-force attack detection */
    if ($system['brute_force_detection_enabled']) {
      $lockout_time = $system['brute_force_lockout_time'] * 60; /* convert to seconds */
      if (($user['user_failed_login_ip'] == get_user_ip()) && ($user['user_failed_login_count'] >= $system['brute_force_bad_login_limit']) && (time() - strtotime($user['user_first_failed_login']) <  $lockout_time)) {
        throw new ValidationException(__("Your account is currently locked out. Please try again later!"));
      }
    }
    /* check password */
    if (!password_verify($password, $user['user_password'])) {
      /* check brute-force attack detection */
      if ($system['brute_force_detection_enabled']) {
        if ($user['user_first_failed_login'] && (time() - strtotime($user['user_first_failed_login']) > $lockout_time)) {
          /* reset the failed login count if the lockout time has passed */
          $db->query(sprintf("UPDATE users SET user_first_failed_login = %s, user_failed_login_ip = %s, user_failed_login_count = 1 WHERE user_id = %s", secure($date), secure(get_user_ip()), secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        } else {
          /* increment failed login count and update first failed login time & ip if necessary */
          $db->query(sprintf("UPDATE users SET user_failed_login_count =  user_failed_login_count + 1 WHERE user_id = %s", secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          if (!$user['user_first_failed_login']) {
            $db->query(sprintf("UPDATE users SET user_first_failed_login = %s, user_failed_login_ip = %s WHERE user_id = %s", secure($date), secure(get_user_ip()), secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          }
        }
      }
      throw new ValidationException(__("Please re-enter your password") . ", " . __("The password you entered is incorrect"));
    }
    /* two-factor authentication */
    if ($user['user_two_factor_enabled']) {
      /* system two-factor disabled */
      if (!$system['two_factor_enabled']) {
        $this->disable_two_factor_authentication($user['user_id']);
        goto set_authentication;
      }
      /* system two-factor method != user two-factor method */
      if ($system['two_factor_type'] != $user['user_two_factor_type']) {
        $this->disable_two_factor_authentication($user['user_id']);
        goto set_authentication;
      }
      switch ($system['two_factor_type']) {
        case 'email':
          /* system two-factor method = email but user email not verified */
          if (!$user['user_email_verified']) {
            $this->disable_two_factor_authentication($user['user_id']);
            goto set_authentication;
          }
          /* generate two-factor key */
          $two_factor_key = get_hash_key(6, true);
          /* update user two factor key */
          $db->query(sprintf("UPDATE users SET user_two_factor_key = %s WHERE user_id = %s", secure($two_factor_key), secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* prepare method name */
          $method = __("Email");
          /* prepare activation email */
          $subject = html_entity_decode(__($system['system_title']), ENT_QUOTES) . " " . __("Two-Factor Authentication Token");
          $body = get_email_template("two_factor_email", $subject, ["user" => $user, "two_factor_key" => $two_factor_key]);
          /* send email */
          if (!_email($user['user_email'], $subject, $body['html'], $body['plain'])) {
            throw new Exception(__("Two-factor authentication email could not be sent"));
          }
          break;

        case 'sms':
          /* system two-factor method = sms but not user phone not verified */
          if (!$user['user_phone_verified']) {
            $this->disable_two_factor_authentication($user['user_id']);
            goto set_authentication;
          }
          /* generate two-factor key */
          $two_factor_key = get_hash_key(6, true);
          /* update user two factor key */
          $db->query(sprintf("UPDATE users SET user_two_factor_key = %s WHERE user_id = %s", secure($two_factor_key), secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
          /* prepare method name */
          $method = __("Phone");
          /* prepare activation SMS */
          $message  = __($system['system_title']) . " " . __("Two-factor authentication key") . ": " . $two_factor_key;
          /* send SMS */
          if (!sms_send($user['user_phone'], $message)) {
            throw new Exception(__("Two-factor authentication SMS could not be sent"));
          }
          break;

        case 'google':
          /* prepare method name */
          $method = __("Google Authenticator app");
          break;
      }
      if ($from_web) {
        modal("#two-factor-authentication", "{user_id: '" . $user['user_id'] . "', remember: '" . $remember . "', method: '" . $method . "', connecting_account: '" . $connecting_account . "'}");
      } else {
        return ['2FA' => true, 'user_id' => $user['user_id'], 'method' => $method, 'connecting_account' => $connecting_account];
      }
    }
    /* set authentication */
    set_authentication:
    if ($connecting_account) {
      return $user;
    }
    if ($from_web) {
      $this->_set_authentication_cookies($user['user_id'], $remember);
    } else {
      /* create JWT */
      $jwt = $this->_set_authentication_JWT($user['user_id'], $device_info);
      /* create new user object */
      $user = new User($jwt);
      return ['token' => $jwt, 'user' => $user->_data];
    }
  }


  /**
   * sign_out
   * 
   * @param boolean $from_web
   * @return void
   */
  public function sign_out($from_web = false)
  {
    global $db, $date;
    /* delete the session */
    $db->query(sprintf("DELETE FROM users_sessions WHERE session_token = %s AND user_id = %s", secure($this->_data['session_token']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* destroy the session */
    session_destroy();
    if ($from_web) {
      /* unset the cookies */
      unset($_COOKIE[$this->_cookie_user_id]);
      unset($_COOKIE[$this->_cookie_user_token]);
      unset_cookie($this->_cookie_user_id);
      unset_cookie($this->_cookie_user_token);
    }
  }


  /**
   * _set_authentication_cookies
   * 
   * @param integer $user_id
   * @param boolean $remember
   * @return void
   */
  private function _set_authentication_cookies($user_id, $remember = false)
  {
    global $db, $system, $date;
    /* generate new token */
    $session_token = get_hash_token();
    /* check brute-force attack detection */
    if ($system['brute_force_detection_enabled']) {
      $db->query(sprintf("UPDATE users SET user_failed_login_count = 0 WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* insert user session */
    $db->query(sprintf("INSERT INTO users_sessions (session_token, session_date, user_id, user_ip, user_browser, user_os) VALUES (%s, %s, %s, %s, %s, %s)", secure($session_token), secure($date), secure($user_id, 'int'), secure(get_user_ip()), secure(get_user_browser()), secure(get_user_os()))) or _error('SQL_ERROR_THROWEN');
    /* secured cookies */
    $secured = (get_system_protocol() == "https") ? true : false;
    /* set authentication cookies */
    $is_expired = ($remember) ? false : true;
    set_cookie($this->_cookie_user_id, $user_id, $is_expired);
    set_cookie($this->_cookie_user_token, $session_token, $is_expired);
  }


  /**
   * _set_authentication_JWT 
   * 
   * @param integer $user_id
   * @param array $device_info
   * @return string
   */
  private function _set_authentication_JWT($user_id, $device_info)
  {
    global $db, $system, $date;
    /* generate new token */
    $session_token = get_hash_token();
    /* generate new JWT */
    $payload = [
      'uid' => $user_id,
      'token' => $session_token
    ];
    $jwt = Firebase\JWT\JWT::encode($payload, JWT_SECRET, 'HS256');
    /* check brute-force attack detection */
    if ($system['brute_force_detection_enabled']) {
      $db->query(sprintf("UPDATE users SET user_failed_login_count = 0 WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* insert user session */
    $db->query(sprintf("INSERT INTO users_sessions (session_token, session_date, session_type, user_id, user_ip, user_os, user_os_version, user_device_name) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", secure($session_token), secure($date), secure($device_info['device_type']), secure($user_id, 'int'), secure(get_user_ip()), secure($device_info['device_os']), secure($device_info['device_os_version']), secure($device_info['device_name']))) or _error('SQL_ERROR_THROWEN');
    return $jwt;
  }


  /**
   * _check_ip
   * 
   * @return void
   */
  private function _check_ip()
  {
    global $db, $system;
    if ($system['max_accounts'] > 0) {
      $check = $db->query(sprintf("SELECT user_ip, COUNT(*) FROM users_sessions WHERE user_ip = %s GROUP BY user_id", secure(get_user_ip()))) or _error('SQL_ERROR_THROWEN');
      if ($check->num_rows >= $system['max_accounts']) {
        throw new ValidationException(__("You have reached the maximum number of account for your IP"));
      }
    }
  }



  /* ------------------------------- */
  /* Login As */
  /* ------------------------------- */

  /**
   * login_as
   * 
   * @param integer $user_id
   * @return void
   */
  public function login_as($user_id)
  {
    global $db, $system;
    /* check if the viewer is admin */
    if (!$this->_is_admin) {
      throw new AuthorizationException(__("You are not authorized to do this action"));
    }
    /* check if the viewer is not the target user */
    if ($user_id == $this->_data['user_id']) {
      throw new Exception(__("Sorry, it looks like this account belongs to you"));
    }
    /* check if the target user exists */
    $user = $this->get_user($user_id);
    if (!$user) {
      throw new NoDataException(__("No data found"));
    }
    /* check if the target user is not the super admin */
    if ($user['user_id'] == 1) {
      throw new AuthorizationException(__("You are not authorized to do this action"));
    }
    /* set login as cookie */
    set_cookie($this->_cookie_user_login_as, $user['user_id']);
  }


  /**
   * revoke_login_as
   * 
   * @return void
   */
  public function revoke_login_as()
  {
    unset_cookie($this->_cookie_user_login_as);
  }



  /* ------------------------------- */
  /* Connected Accounts ✅ */
  /* ------------------------------- */

  /**
   * get_connected_accounts
   * 
   * @param integer $user_id
   * @return array
   */
  public function get_connected_accounts($user_id = null)
  {
    global $db;
    $user_id = (isset($user_id)) ? $user_id : $this->_data['user_id'];
    $accounts = [];
    $get_accounts = $db->query(sprintf('
    SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users WHERE user_id = %1$s 
    UNION
    SELECT users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM users INNER JOIN users_accounts ON users.user_id = users_accounts.account_id WHERE users_accounts.user_id = %1$s', secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($get_accounts->num_rows > 0) {
      while ($account = $get_accounts->fetch_assoc()) {
        $account['user_picture'] = get_picture($account['user_picture'], $account['user_gender']);
        $accounts[] = $account;
      }
    }
    return $accounts;
  }


  /**
   * connected_account_signin
   * 
   * @param string $username_email
   * @param string $password
   * @return void
   */
  public function connected_account_signin($username_email, $password)
  {
    global $db;
    $user = $this->sign_in($username_email, $password, false, true, [], true);
    $this->connected_account_connect($user);
  }


  /**
   * connected_account_connect
   * 
   * @param array $user
   * @return void
   */
  public function connected_account_connect($user)
  {
    global $db;
    /* check if this user is the current user */
    if ($user['user_id'] == $this->_data['user_id']) {
      throw new Exception(__("Sorry, it looks like this account belongs to you"));
    }
    /* check if this user is already connected to other master */
    if ($user['user_master_account'] != $user['user_id']) {
      throw new Exception(__("Sorry, it looks like this account is already connected to another account"));
    }
    /* check if this user is a master account for other accounts */
    $master_check = $db->query(sprintf("SELECT COUNT(*) AS count FROM users WHERE user_master_account = %s", secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    if ($master_check->fetch_assoc()['count'] > 1) {
      throw new Exception(__("Sorry, it looks like this account is a master account for other accounts"));
    }
    /* insert new account as connected accounts */
    $db->query(sprintf("INSERT INTO users_accounts (user_id, account_id) VALUES (%s, %s)", secure($this->_data['user_id'], 'int'), secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update new account master */
    $db->query(sprintf("UPDATE users SET user_master_account = %s WHERE user_id = %s", secure($this->_data['user_id'], 'int'), secure($user['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * connected_account_switch
   * 
   * @param integer $account_id
   * @return void
   */
  public function connected_account_switch($account_id)
  {
    global $db;
    /* search connected account if this account belongs to current user */
    if (array_search($account_id, array_column($this->_data['connected_accounts'], 'user_id')) === false) {
      throw new Exception(__("Sorry, it looks like this account doesn't belong to you"));
    }
    $this->_set_authentication_cookies($account_id, true);
  }


  /**
   * connected_account_remove
   * 
   * @param integer $account_id
   * @return void
   */
  public function connected_account_remove($account_id)
  {
    global $db;
    /* search connected account if this account belongs to current user */
    if (array_search($account_id, array_column($this->_data['connected_accounts'], 'user_id')) === false) {
      throw new Exception(__("Sorry, it looks like this account doesn't belong to you"));
    }
    /* remove from connected accounts */
    $db->query(sprintf("DELETE FROM users_accounts WHERE user_id = %s AND account_id = %s", secure($this->_data['user_id'], 'int'), secure($account_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update target user master */
    $db->query(sprintf("UPDATE users SET user_master_account = user_id WHERE user_id = %s", secure($account_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * connected_account_revoke
   * 
   * @return void
   */
  public function connected_account_revoke()
  {
    global $db;
    /* remove from connected accounts */
    $db->query(sprintf("DELETE FROM users_accounts WHERE user_id = %s AND account_id = %s", secure($this->_data['user_master_account'], 'int'), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* update current user master */
    $db->query(sprintf("UPDATE users SET user_master_account = user_id WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Getting Started ✅ */
  /* ------------------------------- */

  /**
   * getting_satrted_update
   * 
   * @param array $args
   * @return void
   */
  public function getting_satrted_update($args)
  {
    global $db;
    /* validate country */
    if ($args['country'] == "none") {
      throw new ValidationException(__("You must select valid country"));
    } else {
      if (!$this->check_country($args['country'])) {
        throw new ValidationException(__("You must select valid country"));
      }
    }
    /* validate work website */
    if (!is_empty($args['work_url'])) {
      if (!valid_url($args['work_url'])) {
        throw new ValidationException(__("Please enter a valid work website"));
      }
    } else {
      $args['work_url'] = 'null';
    }
    /* update user */
    $db->query(sprintf("UPDATE users SET user_country = %s, user_work_title = %s, user_work_place = %s, user_work_url = %s, user_current_city = %s, user_hometown = %s, user_edu_major = %s, user_edu_school = %s, user_edu_class = %s WHERE user_id = %s", secure($args['country'], 'int'), secure($args['work_title']), secure($args['work_place']), secure($args['work_url']), secure($args['city']), secure($args['hometown']), secure($args['edu_major']), secure($args['edu_school']), secure($args['edu_class']), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
  }


  /**
   * getting_satrted_finish
   * 
   * @return void
   */
  public function getting_satrted_finish()
  {
    global $db, $system;
    // check if there is any required data
    if ($system['getting_started_profile_image_required'] || $system['getting_started_location_required'] || $system['getting_started_education_required'] || $system['getting_started_work_required']) {
      $user_info = $this->get_user($this->_data['user_id']);
      /* check if profile image required */
      if ($system['getting_started_profile_image_required'] && is_empty($user_info['user_picture'])) {
        throw new Exception(__("You must upload your profile image"));
      }
      /* check if location data required */
      if ($system['getting_started_location_required'] && (is_empty($user_info['user_country']) || is_empty($user_info['user_current_city']) || is_empty($user_info['user_hometown']))) {
        throw new Exception(__("You must enter your location info"));
      }
      /* check if work data required */
      if ($system['getting_started_work_required'] && (is_empty($user_info['user_work_title']) || is_empty($user_info['user_work_place']) || is_empty($user_info['user_work_url']))) {
        throw new Exception(__("You must enter your work info"));
      }
      /* check if education data required */
      if ($system['getting_started_education_required'] && (is_empty($user_info['user_edu_major']) || is_empty($user_info['user_edu_school']) || is_empty($user_info['user_edu_class']))) {
        throw new Exception(__("You must enter your education info"));
      }
    }
    // update user info
    $db->query(sprintf("UPDATE users SET user_started = '1' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    // auto connect (custom country)
    $this->auto_friend($this->_data['user_id'], $this->_data['user_country']);
    $this->auto_follow($this->_data['user_id'], $this->_data['user_country']);
    $this->auto_like($this->_data['user_id'], $this->_data['user_country']);
    $this->auto_join($this->_data['user_id'], $this->_data['user_country']);
  }



  /* ------------------------------- */
  /* Social Login */
  /* ------------------------------- */

  /**
   * social_login
   * 
   * @param string $provider
   * @param object $user_profile
   * 
   * @return void
   */
  public function social_signin($provider, $user_profile)
  {
    global $db, $system, $smarty;
    switch ($provider) {
      case 'facebook':
        $social_id = "facebook_id";
        $social_connected = "facebook_connected";
        break;

      case 'google':
        $social_id = "google_id";
        $social_connected = "google_connected";
        break;

      case 'twitter':
        $social_id = "twitter_id";
        $social_connected = "twitter_connected";
        break;

      case 'instagram':
        $social_id = "instagram_id";
        $social_connected = "instagram_connected";
        break;

      case 'linkedin':
        $social_id = "linkedin_id";
        $social_connected = "linkedin_connected";
        break;

      case 'vkontakte':
        $social_id = "vkontakte_id";
        $social_connected = "vkontakte_connected";
        break;

      case 'wordpress':
        $social_id = "wordpress_id";
        $social_connected = "wordpress_connected";
        break;

      case 'sngine':
        $social_id = "sngine_id";
        $social_connected = "sngine_connected";
        break;
    }
    /* check if user connected or not */
    $check_user = $db->query(sprintf("SELECT user_id FROM users WHERE $social_id = %s", secure($user_profile->identifier))) or _error('SQL_ERROR_THROWEN');
    if ($check_user->num_rows > 0) {
      /* social account connected and just signing-in */
      $user = $check_user->fetch_assoc();
      /* signout if user logged-in */
      if ($this->_logged_in) {
        $this->sign_out(true);
      }
      /* set authentication cookies */
      $this->_set_authentication_cookies($user['user_id'], true);
      redirect();
    } else {
      /* user cloud be connecting his social account or signing-up */
      if ($this->_logged_in) {
        /* [1] connecting social account */
        $db->query(sprintf("UPDATE users SET $social_connected = '1', $social_id = %s WHERE user_id = %s", secure($user_profile->identifier), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
        redirect('/settings/linked');
      } else {
        /* [2] signup with social account */
        $_SESSION['social_id'] = $user_profile->identifier;
        page_header(__($system['system_title']) . " &rsaquo; " . __("Sign Up"));
        $smarty->assign('provider', $provider);
        $smarty->assign('user_profile', $user_profile);
        $smarty->assign('genders', $this->get_genders());
        $smarty->display("signup_social.tpl");
      }
    }
  }


  /**
   * social_signup
   * 
   * @param string $first_name
   * @param string $last_name
   * @param string $username
   * @param string $email
   * @param string $password
   * @param string $gender
   * @param boolean $newsletter_agree
   * @param boolean $privacy_agree
   * @param string $avatar
   * @param string $provider
   * @param string $invitation_code
   * @return void
   */
  public function social_signup($first_name, $last_name, $username, $email, $password, $gender, $newsletter_agree, $privacy_agree, $avatar, $provider, $invitation_code = "")
  {
    global $db, $system, $date;
    switch ($provider) {
      case 'facebook':
        $social_id = "facebook_id";
        $social_connected = "facebook_connected";
        break;

      case 'google':
        $social_id = "google_id";
        $social_connected = "google_connected";
        break;

      case 'twitter':
        $social_id = "twitter_id";
        $social_connected = "twitter_connected";
        break;

      case 'instagram':
        $social_id = "instagram_id";
        $social_connected = "instagram_connected";
        break;

      case 'linkedin':
        $social_id = "linkedin_id";
        $social_connected = "linkedin_connected";
        break;

      case 'vkontakte':
        $social_id = "vkontakte_id";
        $social_connected = "vkontakte_connected";
        break;

      case 'wordpress':
        $social_id = "wordpress_id";
        $social_connected = "wordpress_connected";
        break;

      case 'sngine':
        $social_id = "sngine_id";
        $social_connected = "sngine_connected";
        break;

      default:
        _error(400);
        break;
    }
    /* check invitation code */
    if ($system['invitation_enabled']) {
      if (!$this->check_invitation_code($invitation_code)) {
        throw new Exception(__("The invitation code is invalid or expired"));
      }
    }
    /* check IP */
    $this->_check_ip();
    if (is_empty($first_name) || is_empty($last_name) || is_empty($username) || is_empty($email) || is_empty($password)) {
      throw new Exception(__("You must fill in all of the fields"));
    }
    if (!valid_username($username)) {
      throw new Exception(__("Please enter a valid username (a-z0-9_.) with minimum 3 characters long"));
    }
    if ($this->reserved_username($username)) {
      throw new Exception(__("You can't use") . " " . $username . " " . __("as username"));
    }
    if ($this->check_username($username)) {
      throw new Exception(__("Sorry, it looks like") . " " . $username . " " . __("belongs to an existing account"));
    }
    if (!valid_email($email)) {
      throw new Exception(__("Please enter a valid email address"));
    }
    if ($this->check_email($email)) {
      throw new Exception(__("Sorry, it looks like") . " " . $email . " " . __("belongs to an existing account"));
    }
    if (strlen($password) < 6) {
      throw new Exception(__("Your password must be at least 6 characters long. Please try another"));
    }
    if (strlen($password) > 64) {
      throw new Exception(__("Your password must be less than 64 characters long. Please try another"));
    }
    if (!valid_name($first_name)) {
      throw new Exception(__("Your first name contains invalid characters"));
    }
    if (strlen($first_name) < $system['name_min_length']) {
      throw new Exception(__("Your first name must be at least") . " " . $system['name_min_length'] . " " . __("characters long. Please try another"));
    }
    if (!valid_name($last_name)) {
      throw new Exception(__("Your last name contains invalid characters"));
    }
    if (strlen($last_name) < $system['name_min_length']) {
      throw new Exception(__("Your last name must be at least") . " " . $system['name_min_length'] . " " . __("characters long. Please try another"));
    }
    $gender = ($system['genders_disabled']) ? 1 : $gender;
    if (!$system['genders_disabled'] && !$this->check_gender($gender)) {
      throw new Exception(__("Please select a valid gender"));
    }
    /* check newsletter agreement */
    $newsletter_agree = (isset($args['newsletter_agree'])) ? '1' : '0';
    /* check privacy agreement */
    if (!isset($privacy_agree)) {
      throw new Exception(__("You must read and agree to our terms and privacy policy"));
    }
    /* save avatar */
    $image_name = save_picture_from_url($avatar);
    /* set custom user group */
    $custom_user_group = ($system['default_custom_user_group'] != '0' && $this->check_user_group($system['default_custom_user_group'])) ? $system['default_custom_user_group'] : '0';
    /* register user */
    $db->query(sprintf("INSERT INTO users (user_group_custom, user_name, user_email, user_password, user_firstname, user_lastname, user_gender, user_registered, user_activated, user_picture, $social_id, $social_connected) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, '1', %s, %s, '1')", secure($custom_user_group), secure($username), secure($email), secure(_password_hash($password)), secure(ucwords($first_name)), secure(ucwords($last_name)), secure($gender), secure($date), secure($image_name), secure($_SESSION['social_id']))) or _error('SQL_ERROR_THROWEN');
    /* get user_id */
    $user_id = $db->insert_id;
    /* affiliates system */
    $this->process_affiliates("registration", $user_id);
    /* update invitation code */
    if ($system['invitation_enabled']) {
      $this->update_invitation_code($invitation_code, $user_id);
    }
    /* auto connect */
    $this->auto_friend($user_id);
    $this->auto_follow($user_id);
    $this->auto_like($user_id);
    $this->auto_join($user_id);
    /* set authentication cookies */
    $this->_set_authentication_cookies($user_id);
  }



  /* ------------------------------- */
  /* Two-Factor Authentication ✅ */
  /* ------------------------------- */

  /**
   * two_factor_authentication
   * 
   * @param string $two_factor_key
   * @param integer $user_id
   * @param boolean $remember
   * @param boolean $from_web
   * @param array $device_info
   * @param boolean $connecting_account
   * @return void
   */
  public function two_factor_authentication($two_factor_key, $user_id, $remember = false, $from_web = false, $device_info = [], $connecting_account = false)
  {
    global $db, $system;
    if ($system['two_factor_type'] == "google") {
      /* get user */
      $get_user = $db->query(sprintf("SELECT user_two_factor_gsecret FROM users WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_user->num_rows == 0) {
        _error(400);
      }
      $_user = $get_user->fetch_assoc();
      /* Google Authenticator */
      $ga = new Sonata\GoogleAuthenticator\GoogleAuthenticator();
      /* verify code */
      if (!$ga->checkCode($_user['user_two_factor_gsecret'], $two_factor_key)) {
        throw new ValidationException(__("Invalid code, please try again"));
      }
    } else {
      /* check two-factor key */
      $check_key = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_id = %s AND user_two_factor_key = %s", secure($user_id, 'int'), secure($two_factor_key))) or _error('SQL_ERROR_THROWEN');
      if ($check_key->fetch_assoc()['count'] == 0) {
        throw new ValidationException(__("Invalid code, please try again"));
      }
    }
    /* set authentication */
    if ($connecting_account) {
      $user = $this->get_user($user_id);
      $this->connected_account_connect($user);
    }
    if ($from_web) {
      $this->_set_authentication_cookies($user_id, $remember);
    } else {
      /* create JWT */
      $jwt = $this->_set_authentication_JWT($user_id, $device_info);
      /* create new user object */
      $user = new User($jwt);
      return ['token' => $jwt, 'user' => $user->_data];
    }
  }


  /**
   * disable_two_factor_authentication
   * 
   * @param integer $user_id
   * 
   * @return void
   */
  public function disable_two_factor_authentication($user_id)
  {
    global $db;
    $db->query(sprintf("UPDATE users SET user_two_factor_enabled = '0', user_two_factor_type = null, user_two_factor_key = null, user_two_factor_gsecret = null WHERE user_id = %s", secure($user_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Password ✅ */
  /* ------------------------------- */

  /**
   * forget_password
   * 
   * @param string $email
   * @param string $recaptcha_response
   * @param bool $from_web
   * @return void
   */
  public function forget_password($email, $recaptcha_response = null, $from_web = false)
  {
    global $db, $system;
    if (!valid_email($email)) {
      throw new ValidationException(__("Please enter a valid email address"));
    }
    if (!$this->check_email($email)) {
      throw new ValidationException(__("Sorry, it looks like") . " " . $email . " " . __("doesn't belong to any account"));
    }
    /* check reCAPTCHA */
    if ($system['reCAPTCHA_enabled'] && $from_web) {
      $recaptcha = new \ReCaptcha\ReCaptcha($system['reCAPTCHA_secret_key'], new \ReCaptcha\RequestMethod\CurlPost());
      $resp = $recaptcha->verify($recaptcha_response, get_user_ip());
      if (!$resp->isSuccess()) {
        throw new ValidationException(__("The security check is incorrect. Please try again"));
      }
    }
    /* generate reset key */
    $reset_key = get_hash_key(6, true);
    /* update user */
    $db->query(sprintf("UPDATE users SET user_reset_key = %s, user_reseted = '1' WHERE user_email = %s", secure($reset_key), secure($email))) or _error('SQL_ERROR_THROWEN');
    /* send reset email */
    /* prepare reset email */
    $subject = __("Forget password activation key!");
    $body = get_email_template("forget_password_email", $subject, ["email" => $email, "reset_key" => $reset_key]);
    /* send email */
    if (!_email($email, $subject, $body['html'], $body['plain'])) {
      throw new Exception(__("Activation key email could not be sent!"));
    }
  }


  /**
   * forget_password_confirm
   * 
   * @param string $email
   * @param string $reset_key
   * @return void
   */
  public function forget_password_confirm($email, $reset_key)
  {
    global $db;
    if (!valid_email($email)) {
      throw new ValidationException(__("Invalid email, please try again"));
    }
    /* check reset key */
    $check_key = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_email = %s AND user_reset_key = %s AND user_reseted = '1'", secure($email), secure($reset_key))) or _error('SQL_ERROR_THROWEN');
    if ($check_key->fetch_assoc()['count'] == 0) {
      throw new ValidationException(__("Invalid code, please try again"));
    }
  }


  /**
   * forget_password_reset
   * 
   * @param string $email
   * @param string $reset_key
   * @param string $password
   * @param string $confirm
   * @return void
   */
  public function forget_password_reset($email, $reset_key, $password, $confirm)
  {
    global $db;
    if (!valid_email($email)) {
      throw new ValidationException(__("Invalid email, please try again"));
    }
    /* check reset key */
    $check_key = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_email = %s AND user_reset_key = %s AND user_reseted = '1'", secure($email), secure($reset_key))) or _error('SQL_ERROR_THROWEN');
    if ($check_key->fetch_assoc()['count'] == 0) {
      throw new ValidationException(__("Invalid code, please try again"));
    }
    /* check password length */
    if (strlen($password) < 6) {
      throw new ValidationException(__("Your password must be at least 6 characters long. Please try another"));
    }
    if (strlen($password) > 64) {
      throw new ValidationException(__("Your password must be less than 64 characters long. Please try another"));
    }
    /* check password confirm */
    if ($password !== $confirm) {
      throw new ValidationException(__("Your passwords do not match. Please try another"));
    }
    /* update user password */
    $db->query(sprintf("UPDATE users SET user_password = %s, user_reseted = '0' WHERE user_email = %s", secure(_password_hash($password)), secure($email))) or _error('SQL_ERROR_THROWEN');
    /* delete sessions */
    $db->query(sprintf("DELETE FROM users_sessions WHERE user_id = (SELECT user_id FROM users WHERE user_email = %s)", secure($email))) or _error('SQL_ERROR_THROWEN');
  }



  /* ------------------------------- */
  /* Activation Email ✅ */
  /* ------------------------------- */

  /**
   * activation_email
   * 
   * @param string $code
   * @return void
   */
  public function activation_email($code)
  {
    global $db, $system;
    /* check user */
    if ($this->_data['user_email_verified']) {
      return;
    }
    if ($this->_data['user_email_verification_code'] != $code) {
      throw new ValidationException(__("Invalid verification code"));
    }
    /* check if user [1] activate his account & verify his email or [2] just verify his email */
    if ($system['activation_enabled'] && $system['activation_type'] == "email" && !$this->_data['user_activated']) {
      /* [1] activate his account & verify his email */
      $db->query(sprintf("UPDATE users SET user_activated = '1', user_email_verified = '1' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* affiliates system */
      $this->process_affiliates("registration", $this->_data['user_id'], $this->_data['user_referrer_id']);
    } else {
      /* [2] just verify his email */
      $db->query(sprintf("UPDATE users SET user_email_verified = '1' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * activation_email_resend
   * 
   * @return void
   */
  public function activation_email_resend()
  {
    global $db, $system;
    /* generate email verification code */
    $email_verification_code = get_hash_key(6, true);
    /* update user */
    $db->query(sprintf("UPDATE users SET user_email_verification_code = %s WHERE user_id = %s", secure($email_verification_code), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* prepare activation email */
    $subject = __("Just one more step to get started on") . " " . html_entity_decode(__($system['system_title']), ENT_QUOTES);
    $body = get_email_template("activation_email", $subject, ["name" => $this->_data['name'], "email_verification_code" => $email_verification_code]);
    /* send email */
    if (!_email($this->_data['user_email'], $subject, $body['html'], $body['plain'])) {
      throw new Exception(__("Activation email could not be sent"));
    }
  }


  /**
   * activation_email_reset
   * 
   * @param string $email
   * @return void
   */
  public function activation_email_reset($email)
  {
    global $db, $system;
    if (!valid_email($email)) {
      throw new ValidationException(__("Please enter a valid email address"));
    }
    if ($this->check_email($email)) {
      throw new ValidationException(__("Sorry, it looks like") . " " . $email . " " . __("belongs to an existing account"));
    }
    /* generate email verification code */
    $email_verification_code = get_hash_key(6, true);
    /* check if activation via email enabled */
    if ($system['activation_enabled'] && $system['activation_type'] == "email") {
      /* update user (not activated) */
      $db->query(sprintf("UPDATE users SET user_email = %s, user_email_verified = '0', user_email_verification_code = %s, user_activated = '0' WHERE user_id = %s", secure($email), secure($email_verification_code), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      /* update user */
      $db->query(sprintf("UPDATE users SET user_email = %s, user_email_verified = '0', user_email_verification_code = %s WHERE user_id = %s", secure($email), secure($email_verification_code), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* prepare activation email */
    $subject = __("Just one more step to get started on") . " " . html_entity_decode(__($system['system_title']), ENT_QUOTES);
    $body = get_email_template("activation_email", $subject, ["name" => $this->_data['name'], "email_verification_code" => $email_verification_code]);
    /* send email */
    if (!_email($email, $subject, $body['html'], $body['plain'])) {
      throw new Exception(__("Activation email could not be sent"));
    }
  }



  /* ------------------------------- */
  /* Activation Phone ✅ */
  /* ------------------------------- */

  /**
   * activation_phone
   * 
   * @param string $code
   * @return void
   */
  public function activation_phone($code)
  {
    global $db, $system;
    /* check if phone already verified */
    if ($this->_data['user_phone_verified']) {
      return;
    }
    /* check the verification code */
    if ($this->_data['user_phone_verification_code'] != $code) {
      throw new ValidationException(__("Invalid code, please try again"));
    }
    /* check if user [1] activate his account & his phone or [2] just verify his phone */
    if ($system['activation_enabled'] && $system['activation_type'] == "sms" && !$this->_data['user_activated']) {
      /* [1] activate his account & his phone */
      $db->query(sprintf("UPDATE users SET user_activated = '1', user_phone_verified = '1' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* affiliates system */
      $this->process_affiliates("registration", $this->_data['user_id'], $this->_data['user_referrer_id']);
    } else {
      /* [2] just verify his phone */
      $db->query(sprintf("UPDATE users SET user_phone_verified = '1' WHERE user_id = %s", secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }


  /**
   * activation_phone_resend
   * 
   * @return void
   */
  public function activation_phone_resend()
  {
    global $db, $system;
    /* generate phone verification code */
    $phone_verification_code = get_hash_key(6, true);
    /* update user */
    $db->query(sprintf("UPDATE users SET user_phone_verification_code = %s WHERE user_id = %s", secure($phone_verification_code), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    /* prepare activation SMS */
    $message  = __($system['system_title']) . " " . __("Activation Code") . ": " . $phone_verification_code;
    /* send SMS */
    if (!sms_send($this->_data['user_phone'], $message)) {
      throw new Exception(__("Activation SMS could not be sent"));
    }
  }


  /**
   * activation_phone_reset
   * 
   * @param string $phone
   * @return void
   */
  public function activation_phone_reset($phone)
  {
    global $db, $system;
    if (is_empty($phone)) {
      throw new ValidationException(__("Please enter a valid phone number"));
    }
    if ($this->check_phone($phone)) {
      throw new ValidationException(__("Sorry, it looks like") . " " . $phone . " " . __("belongs to an existing account"));
    }
    /* generate phone verification code */
    $phone_verification_code = get_hash_key(6, true);
    /* check if activation via sms enabled */
    if ($system['activation_enabled'] && $system['activation_type'] == "sms") {
      /* update user (not activated) */
      $db->query(sprintf("UPDATE users SET user_phone = %s, user_phone_verified = '0', user_phone_verification_code = %s, user_activated = '0' WHERE user_id = %s", secure($phone), secure($phone_verification_code), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    } else {
      /* update user */
      $db->query(sprintf("UPDATE users SET user_phone = %s, user_phone_verified = '0', user_phone_verification_code = %s WHERE user_id = %s", secure($phone), secure($phone_verification_code), secure($this->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
    }
    /* prepare activation SMS */
    $message  = __($system['system_title']) . " " . __("Activation Code") . ": " . $phone_verification_code;
    /* send SMS */
    if (!sms_send($phone, $message)) {
      throw new Exception(__("Activation SMS could not be sent"));
    }
  }



  /* ------------------------------- */
  /* Security Checks ✅ */
  /* ------------------------------- */

  /**
   * check_password
   * 
   * @param string $password
   * @return void
   * 
   */
  public function check_password($password)
  {
    /* check if empty */
    if (is_empty($password)) {
      throw new ValidationException(__("You have to enter your password to continue"));
    }
    /* validate current password */
    if (!password_verify($password, $this->_data['user_password'])) {
      throw new ValidationException(__("Your current password is incorrect"));
    }
  }


  /**
   * check_email
   * 
   * @param string $email
   * @param boolean $return_info
   * @return boolean|array
   * 
   */
  public function check_email($email, $return_info = false)
  {
    global $db;
    /* check if banned by the system */
    $email_domain = explode('@', $email)[1];
    $check_banned = $db->query(sprintf("SELECT COUNT(*) as count FROM blacklist WHERE node_type = 'email' AND node_value = %s", secure(explode('@', $email)[1]))) or _error('SQL_ERROR_THROWEN');
    if ($check_banned->fetch_assoc()['count'] > 0) {
      throw new ValidationException(__("Sorry but this provider") . " " . $email_domain . " " . __("is not allowed in our system"));
    }
    $query = $db->query(sprintf("SELECT * FROM users WHERE user_email = %s", secure($email))) or _error('SQL_ERROR_THROWEN');
    if ($query->num_rows > 0) {
      if ($return_info) {
        $info = $query->fetch_assoc();
        return $info;
      }
      return true;
    }
    return false;
  }


  /**
   * check_phone
   * 
   * @param string $phone
   * @return boolean
   * 
   */
  public function check_phone($phone)
  {
    global $db;
    $query = $db->query(sprintf("SELECT COUNT(*) as count FROM users WHERE user_phone = %s", secure($phone))) or _error('SQL_ERROR_THROWEN');
    if ($query->fetch_assoc()['count'] > 0) {
      return true;
    }
    return false;
  }


  /**
   * check_username
   * 
   * @param string $username
   * @param string $type
   * @param boolean $return_info
   * @return boolean|array
   */
  public function check_username($username, $type = 'user', $return_info = false)
  {
    global $db;
    /* check if banned by the system */
    $check_banned = $db->query(sprintf("SELECT COUNT(*) as count FROM blacklist WHERE node_type = 'username' AND node_value = %s", secure($username))) or _error('SQL_ERROR_THROWEN');
    if ($check_banned->fetch_assoc()['count'] > 0) {
      throw new ValidationException(__("Sorry but this username") . " " . $username . " " . __("is not allowed in our system"));
    }
    /* check type (user|page|group) */
    switch ($type) {
      case 'page':
        $query = $db->query(sprintf("SELECT * FROM pages WHERE page_name = %s", secure($username))) or _error('SQL_ERROR_THROWEN');
        break;

      case 'group':
        $query = $db->query(sprintf("SELECT * FROM `groups` WHERE group_name = %s", secure($username))) or _error('SQL_ERROR_THROWEN');
        break;

      default:
        $query = $db->query(sprintf("SELECT * FROM users WHERE user_name = %s", secure($username))) or _error('SQL_ERROR_THROWEN');
        break;
    }
    if ($query->num_rows > 0) {
      if ($return_info) {
        $info = $query->fetch_assoc();
        return $info;
      }
      return true;
    }
    return false;
  }


  /**
   * reserved_username
   * 
   * @param string $username
   * @return boolean
   */
  public function reserved_username($username)
  {
    global $system;
    if (!$system['reserved_usernames_enabled']) {
      return false;
    }
    /* make a list from target usernames */
    $reserved_usernames = array_column(json_decode(html_entity_decode($system['reserved_usernames']), true), "value");
    if (count($reserved_usernames) == 0) {
      return false;
    }
    if (in_array(strtolower($username), $reserved_usernames)) {
      return true;
    }
    return false;
  }
}
