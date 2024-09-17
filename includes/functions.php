<?php

/**
 * functions
 *
 * @package Sngine
 * @author Zamblek
 */


/* ------------------------------- */
/* Core */
/* ------------------------------- */

/**
 * check_system_requirements
 *
 * @return array
 */
function check_system_requirements()
{
  /* init errors */
  $errors = [];
  /* set required php version*/
  $required_php_version = '7.4';
  /* check php version */
  if (version_compare($required_php_version, PHP_VERSION, '>=')) {
    $errors['PHP'] = true;
  }
  /* check if mysqli enabled */
  if (!extension_loaded('mysqli') || !function_exists('mysqli_connect')) {
    $errors['mysqli'] = true;
  }
  /* check if curl enabled */
  if (!extension_loaded('curl') || !function_exists('curl_init')) {
    $errors['curl'] = true;
  }
  /* check if mbstring enabled */
  if (!extension_loaded('mbstring')) {
    $errors['mbstring'] = true;
  }
  /* check if gd enabled */
  if (!extension_loaded('gd') || !function_exists('gd_info')) {
    $errors['gd'] = true;
  }
  /* check if mbstring enabled */
  if (!extension_loaded('mbstring')) {
    $errors['mbstring'] = true;
  }
  /* check if zip enabled */
  if (!extension_loaded('zip')) {
    $errors['zip'] = true;
  }
  /* check if allow_url_fopen enabled */
  if (!ini_get('allow_url_fopen')) {
    $errors['allow_url_fopen'] = true;
  }
  /* check if htaccess exist */
  if (!file_exists(ABSPATH . '.htaccess')) {
    $errors['htaccess'] = true;
  }
  /* check if config writable */
  if (!is_writable(ABSPATH . 'includes/config-example.php')) {
    $errors['config'] = true;
  }
  /* return */
  return $errors;
}


/**
 * get_licence_key
 *
 * @param string $code
 * @return string
 */
function get_licence_key($code)
{
  $url = 'https://www.zamblek.com/licenses/sngine/verify.php';
  $data = "code=" . $code . "&domain=" . $_SERVER['HTTP_HOST'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:5.0) Gecko/20100101 Firefox/5.0 Firefox/5.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  if ($httpCode != 200) {
    throw new Exception("Error Processing Request");
  }
  $responseJson = json_decode($response, true);
  if ($responseJson['error']) {
    throw new Exception($responseJson['error']['message'] . ' Error Code #' . $responseJson['error']['code']);
  }
  return $responseJson['licence_key'];
}


/**
 * valid_api_key
 *
 * @return boolean
 */
function valid_api_key()
{
  $apiKey = _getallheaders()["x-api-key"];
  if ($apiKey == LICENCE_KEY) {
    return true;
  }
  return false;
}


/**
 * redirect
 *
 * @param string $url
 * @return void
 */
function redirect($url = '')
{
  if ($url) {
    header('Location: ' . SYS_URL . $url);
  } else {
    header('Location: ' . SYS_URL);
  }
  exit;
}


/**
 * reload
 *
 * @return void
 */
function reload()
{
  header("Refresh:0");
  exit;
}



/* ------------------------------- */
/* System */
/* ------------------------------- */

/**
 * get_system_protocol
 *
 * @return string
 */
function get_system_protocol()
{
  $is_secure = false;
  if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
    $is_secure = true;
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $is_secure = true;
  }
  return $is_secure ? 'https' : 'http';
}


/**
 * get_system_url
 *
 * @return string
 */
function get_system_url()
{
  $protocol = get_system_protocol();
  $system_url =  $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
  return rtrim($system_url, '/');
}


/**
 * check_system_url
 *
 * @return void
 */
function check_system_url()
{
  $protocol = get_system_protocol();
  $parsed_url = parse_url(SYS_URL);
  if (($parsed_url['scheme'] != $protocol) || ($parsed_url['host'] != $_SERVER['HTTP_HOST'])) {
    header('Location: ' . SYS_URL);
  }
}


/**
 * init_system_session
 *
 * @return void
 */
function init_system_session()
{
  ini_set('session.cookie_httponly', 1);
  if (get_system_protocol() == "https") {
    ini_set('session.cookie_secure', 1);
  }
  session_start();
  /* set session secret */
  if (!isset($_SESSION['secret'])) {
    $_SESSION['secret'] = get_hash_token();
  }
}


/**
 * init_security_headers
 *
 * @return void
 */
function init_security_headers()
{
  header('X-Frame-Options: SAMEORIGIN');
  header('X-XSS-Protection: 1; mode=block');
  header('X-Content-Type-Options: nosniff');
  header('Referrer-Policy: no-referrer');
  header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}


/**
 * init_system_datetime
 *
 * @return string
 */
function init_system_datetime()
{
  date_default_timezone_set('UTC');
  $minutes_to_add = 0;
  $DateTime = new DateTime();
  $DateTime->add(new DateInterval('PT' . $minutes_to_add . 'M'));
  return $DateTime->format('Y-m-d H:i:s');
}


/**
 * init_db_connection
 *
 * @param string $db_host
 * @param string $db_user
 * @param string $db_password
 * @param string $db_name
 * @param string $db_port
 *
 * @return object
 */
function init_db_connection($db_host = null, $db_user = null, $db_password = null, $db_name = null, $db_port = null)
{
  $db_host = (isset($db_host)) ? $db_host : DB_HOST;
  $db_user = (isset($db_user)) ? $db_user : DB_USER;
  $db_password = (isset($db_password)) ? $db_password : DB_PASSWORD;
  $db_name = (isset($db_name)) ? $db_name : DB_NAME;
  $db_port = (isset($db_port)) ? $db_port : DB_PORT;
  $db = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);
  if (mysqli_connect_error()) {
    throw new Exception("DB_ERROR");
  }
  /* set db charset */
  $db->set_charset('utf8mb4');
  /* set db time to UTC */
  $db->query("SET time_zone = '+0:00'");
  return $db;
}


/**
 * init_system
 *
 * @param array &$system
 *
 * @return void
 */
function init_system(&$system)
{
  global $db, $gettextTranslator;
  $get_system_options = $db->query("SELECT * FROM system_options") or _error('SQL_ERROR_THROWEN');
  while ($system_option = $get_system_options->fetch_assoc()) {
    $system[$system_option['option_name']] = $system_option['option_value'];
  }

  /* set system version */
  $system['system_version'] = SYS_VER;

  /* set system URL */
  $system['system_url'] = SYS_URL;

  /* set system debugging */
  $system['DEBUGGING'] = DEBUGGING;

  /* set system_date_format */
  $system['system_date_format'] = explode(' ', $system['system_datetime_format'], 2)[0];

  /* set system uploads */
  if ($system['uploads_cdn_url']) {
    $system['system_uploads'] = $system['uploads_cdn_url'];
  } else {
    if ($system['s3_enabled']) {
      $endpoint = "https://s3." . $system['s3_region'] . ".amazonaws.com/" . $system['s3_bucket'];
      $system['system_uploads'] = $endpoint . "/uploads";
    } elseif ($system['google_cloud_enabled']) {
      $endpoint = "https://storage.googleapis.com/" . $system['google_cloud_bucket'];
      $system['system_uploads'] = $endpoint . "/uploads";
    } elseif ($system['digitalocean_enabled']) {
      $endpoint = "https://" . $system['digitalocean_space_name'] . "." . $system['digitalocean_space_region'] . ".digitaloceanspaces.com";
      $system['system_uploads'] = $endpoint . "/uploads";
    } elseif ($system['wasabi_enabled']) {
      $endpoint = "https://s3." . $system['wasabi_region'] . ".wasabisys.com/" . $system['wasabi_bucket'];
      $system['system_uploads'] = $endpoint . "/uploads";
    } elseif ($system['backblaze_enabled']) {
      $endpoint = "https://s3." . $system['backblaze_region'] . ".backblazeb2.com/" . $system['backblaze_bucket'];
      $system['system_uploads'] = $endpoint . "/uploads";
    } elseif ($system['ftp_enabled']) {
      $system['system_uploads'] = $system['ftp_endpoint'];
    } else {
      $system['system_uploads'] = $system['system_url'] . '/' . $system['uploads_directory'];
    }
  }

  /* set agora uploads */
  if ($system['live_enabled'] && $system['save_live_enabled']) {
    $system['system_agora_uploads'] = "https://s3." . $system['agora_s3_region'] . ".amazonaws.com/" . $system['agora_s3_bucket'];
  }

  /* set uploads accpeted extensions */
  $system['accpeted_video_extensions'] = set_extensions_string($system['video_extensions']);
  $system['accpeted_audio_extensions'] = set_extensions_string($system['audio_extensions']);
  $system['accpeted_file_extensions'] = set_extensions_string($system['file_extensions']);

  /* get system themes */
  $get_system_themes = $db->query("SELECT * FROM system_themes") or _error('SQL_ERROR_THROWEN');
  while ($theme = $get_system_themes->fetch_assoc()) {
    if ($theme['default']) {
      $system['theme'] = $theme['name'];
    }
    if ($theme['enabled']) {
      $system['themes'][$theme['name']] = $theme;
    }
  }

  /* set system theme */
  if (isset($_GET['theme'])) {
    if (array_key_exists($_GET['theme'], $system['themes'])) {
      if (file_exists(ABSPATH . 'content/themes/' . $_GET['theme'])) {
        $system['theme'] = $_GET['theme'];
        /* set theme cookie */
        set_cookie('s_theme', $_GET['theme']);
      }
    }
  } elseif (isset($_COOKIE['s_theme'])) {
    if (array_key_exists($_COOKIE['s_theme'], $system['themes'])) {
      if (file_exists(ABSPATH . 'content/themes/' . $_COOKIE['s_theme'])) {
        $system['theme'] = $_COOKIE['s_theme'];
      } else {
        /* unset theme cookie */
        unset_cookie('s_theme');
      }
    }
  } else {
    if (!isset($system['theme'])) {
      $system['theme'] = "default";
    }
  }

  /* set system theme (day|night) mode */
  $system['theme_mode_night'] = $system['system_theme_night_on'];
  if ($system['system_theme_mode_select']) {
    if (isset($_COOKIE['s_night_mode'])) {
      /* get cookei for web app */
      $system['theme_mode_night'] = ($_COOKIE['s_night_mode']) ? 1 : 0;
    }
  }

  /* get system languages */
  $get_system_languages = $db->query("SELECT * FROM system_languages WHERE enabled = '1' ORDER BY language_order") or _error('SQL_ERROR_THROWEN');
  while ($language = $get_system_languages->fetch_assoc()) {
    $language['flag'] = get_picture($language['flag'], 'flag');
    if ($language['default']) {
      $system['default_language'] = $language;
    }
    $system['languages'][$language['code']] = $language;
  }

  /* set system langauge */
  $system['current_language'] = DEFAULT_LOCALE;
  if (isset($_GET['lang'])) {
    /* get GET for web app */
    if (array_key_exists($_GET['lang'], $system['languages'])) {
      $system['language'] = $system['languages'][$_GET['lang']];
      if ($system['language']['code'] != DEFAULT_LOCALE) {
        $gettextTranslator->loadTranslations(Gettext\Translations::fromPoFile(ABSPATH . 'content/languages/locale/' . $system['language']['code'] . '/LC_MESSAGES/messages.po'));
      }
      $system['current_language'] = $system['language']['code'];
      /* set language cookie */
      set_cookie('s_lang', $_GET['lang']);
    }
  } elseif (isset($_COOKIE['s_lang'])) {
    /* get cookie for web app */
    if (array_key_exists($_COOKIE['s_lang'], $system['languages'])) {
      $system['language'] = $system['languages'][$_COOKIE['s_lang']];
      if ($system['language']['code'] != DEFAULT_LOCALE) {
        $gettextTranslator->loadTranslations(Gettext\Translations::fromPoFile(ABSPATH . 'content/languages/locale/' . $system['language']['code'] . '/LC_MESSAGES/messages.po'));
      }
      $system['current_language'] = $system['language']['code'];
    }
  } elseif (isset(_getallheaders()["x-lang"])) {
    /* get header for web app */
    if (array_key_exists(_getallheaders()["x-lang"], $system['languages'])) {
      $system['language'] = $system['languages'][_getallheaders()["x-lang"]];
      if ($system['language']['code'] != DEFAULT_LOCALE) {
        $gettextTranslator->loadTranslations(Gettext\Translations::fromPoFile(ABSPATH . 'content/languages/locale/' . $system['language']['code'] . '/LC_MESSAGES/messages.po'));
      }
      $system['current_language'] = $system['language']['code'];
    }
  } else {
    if (isset($system['default_language'])) {
      $system['language'] = $system['default_language'];
      if ($system['default_language']['code'] != DEFAULT_LOCALE) {
        $gettextTranslator->loadTranslations(Gettext\Translations::fromPoFile(ABSPATH . 'content/languages/locale/' . $system['default_language']['code'] . '/LC_MESSAGES/messages.po'));
      }
      $system['current_language'] = $system['default_language']['code'];
    }
  }

  /* get system currency */
  $get_currency = $db->query("SELECT * FROM system_currencies WHERE system_currencies.default = '1'") or _error('SQL_ERROR_THROWEN');
  $currency = $get_currency->fetch_assoc();
  $system['system_currency'] = $currency['code'];
  $system['system_currency_symbol'] = $currency['symbol'];
  $system['system_currency_dir'] = $currency['dir'];

  /* get system withdrawal method array */
  $system['wallet_payment_method_array'] = explode(",", $system['wallet_payment_method']);
  $system['affiliate_payment_method_array'] = explode(",", $system['affiliate_payment_method']);
  $system['points_payment_method_array'] = explode(",", $system['points_payment_method']);
  $system['market_payment_method_array'] = explode(",", $system['market_payment_method']);
  $system['funding_payment_method_array'] = explode(",", $system['funding_payment_method']);
  $system['monetization_payment_method_array'] = explode(",", $system['monetization_payment_method']);

  /* check if viewer IP banned */
  $check_banned_ip = $db->query(sprintf("SELECT COUNT(*) as count FROM blacklist WHERE node_type = 'ip' AND node_value = %s", secure(get_user_ip()))) or _error('SQL_ERROR_THROWEN');
  $system['viewer_ip_banned'] = ($check_banned_ip->fetch_assoc()['count'] > 0) ? true : false;
}


/**
 * init_smarty
 *
 * @return object
 */
function init_smarty()
{
  global $system;
  $smarty = new Smarty;
  $smarty->template_dir = ABSPATH . 'content/themes/' . $system['theme'] . '/templates';
  $smarty->compile_dir = ABSPATH . 'content/themes/' . $system['theme'] . '/templates_compiled';
  $smarty->loadFilter('output', 'trimwhitespace');
  $smarty->registerPlugin("modifier", "ucfirst", "ucfirst");
  $smarty->registerPlugin('modifier', '__', '__');
  $smarty->registerPlugin('modifier', 'print_money', 'print_money');
  $smarty->registerPlugin('modifier', 'is_empty', 'is_empty');
  $smarty->registerPlugin('modifier', 'array_reverse', 'array_reverse');
  $smarty->registerPlugin('modifier', 'get_payment_vat_value', 'get_payment_vat_value');
  $smarty->registerPlugin('modifier', 'htmlentities', 'htmlentities');
  $smarty->registerPlugin('modifier', 'get_youtube_id', 'get_youtube_id');
  $smarty->registerPlugin('modifier', 'minimize_css', 'minimize_css');
  return $smarty;
}


/**
 * get_system_session_hash
 *
 * @param string $hash
 * @return array
 */
function get_system_session_hash($hash)
{
  $hash_tokens = explode('-', $hash);
  if (count($hash_tokens) != 6) {
    return false;
  }
  $position = array_rand($hash_tokens);
  $token = $hash_tokens[$position];
  return ['token' => $token, 'position' => $position + 1];
}


/**
 * update_system_options
 *
 * @param array $args
 * @param boolean $error_thrown
 * @return void
 */
function update_system_options($args = [], $error_thrown = true)
{
  global $db;
  $query_values = "";
  foreach ($args as $key => $value) {
    $query_values .= sprintf(" ('%s', %s),", $key, $value);
  }
  $query_values = substr($query_values, 0, -1);
  $db->query("INSERT INTO system_options (option_name, option_value) VALUES " . $query_values . " ON DUPLICATE KEY UPDATE option_name = VALUES(option_name), option_value = VALUES(option_value)") or ($error_thrown) ? _error('SQL_ERROR_THROWEN') : _error("Error", $db->error);
}



/* ------------------------------- */
/* Security */
/* ------------------------------- */

/**
 * secure
 *
 * @param string $value
 * @param string $type
 * @param boolean $quoted
 * @return string
 */
function secure($value, $type = "", $quoted = true)
{
  global $db;
  if ($value !== 'null') {
    // [1] Sanitize
    /* Convert all applicable characters to HTML entities */
    $value = htmlentities((string)$value, ENT_QUOTES, 'utf-8');
    // [2] Safe SQL
    $value = $db->real_escape_string($value);
    switch ($type) {
      case 'int':
        $value = ($quoted) ? "'" . intval($value) . "'" : intval($value);
        break;
      case 'float':
        $value = ($quoted) ? "'" . floatval($value) . "'" : floatval($value);
        break;
      case 'datetime':
        $value = ($quoted) ? "'" . set_datetime($value) . "'" : set_datetime($value);
        break;
      case 'search':
        if ($quoted) {
          $value = (!is_empty($value)) ? "'%" . $value . "%'" : "''";
        } else {
          $value = (!is_empty($value)) ? "'%%" . $value . "%%'" : "''";
        }
        break;
      default:
        $value = (!is_empty($value)) ? $value : "";
        $value = ($quoted) ? "'" . $value . "'" : $value;
        break;
    }
  }
  return $value;
}


/**
 * _password_hash
 *
 * @param string $password
 * @return string
 */
function _password_hash($password)
{
  return password_hash($password, PASSWORD_DEFAULT);
}


/**
 * get_hash_key
 *
 * @param integer $length
 * @param boolean $only_numbers
 * @return string
 */
function get_hash_key($length = 8, $only_numbers = false)
{
  $chars = ($only_numbers) ? '0123456789' : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $count = mb_strlen($chars);
  for ($i = 0, $result = ''; $i < $length; $i++) {
    $index = rand(0, $count - 1);
    $result .= mb_substr($chars, $index, 1);
  }
  return $result;
}


/**
 * get_hash_token
 *
 * @return string
 */
function get_hash_token()
{
  return md5(get_hash_number());
}


/**
 * get_hash_number
 *
 * @return string
 */
function get_hash_number()
{
  return time() * rand(1, 99999);
}


/**
 * extarct_hash_token
 *
 * @param string $file_name
 * @return string
 */
function extarct_hash_token($file_name)
{
  $hash = '';
  preg_match('/\_.*\./', $file_name, $results);
  if ($results[0] != "") {
    $hash = $results[0];
    $hash = str_replace("_", "", $hash);
    $hash = str_replace(".", "", $hash);
  }
  return $hash;
}



/* ------------------------------- */
/* Cookies */
/* ------------------------------- */

/**
 * setcookie
 *
 * @param string $cookie_name
 * @param string $cookie_value
 * @param integer $is_expired
 * @return void
 */
function set_cookie($cookie_name, $cookie_value, $is_expired = false)
{
  $secured = (get_system_protocol() == "https") ? true : false;
  $expire_time = ($is_expired) ?  0 : time() + 2592000;
  $options = [
    'expires' => $expire_time,
    'path' => '/',
    'domain' => '',
    'secure' => $secured,
    'httponly' => true,
    'samesite' => 'Lax'
  ];
  setcookie($cookie_name, $cookie_value, $options);
}


/**
 * unset_cookie
 *
 * @param string $cookie_name
 * @return void
 */
function unset_cookie($cookie_name)
{
  setcookie($cookie_name, "", -1, '/');
}



/* ------------------------------- */
/* Validation */
/* ------------------------------- */

/**
 * is_ajax
 *
 * @return void
 */
function is_ajax()
{
  if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')) {
    redirect();
  }
}


/**
 * is_empty
 *
 * @param string $value
 * @return boolean
 */
function is_empty($value)
{
  if ($value == null || strlen(trim(preg_replace('/\xc2\xa0/', ' ', $value))) == 0) {
    return true;
  }
  return false;
}


/**
 * valid_email
 *
 * @param string $email
 * @return boolean
 */
function valid_email($email)
{
  if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
    return true;
  } else {
    return false;
  }
}


/**
 * valid_url
 *
 * @param string $url
 * @return boolean
 */
function valid_url($url)
{
  if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
    return true;
  } else {
    return false;
  }
}


/**
 * valid_username
 *
 * @param string $username
 * @return boolean
 */
function valid_username($username)
{
  if (strlen($username) >= 3 && preg_match('/^[a-zA-Z0-9]+([_|.]?[a-zA-Z0-9])*$/', $username)) {
    return true;
  } else {
    return false;
  }
}


/**
 * valid_name
 *
 * @param string $name
 * @return boolean
 */
function valid_name($name)
{
  global $system;
  if ((!$system['special_characters_enabled'] && !ctype_graph($name)) || (!$system['special_characters_enabled'] && preg_match('/[[:punct:]]/i', $name) != false) || valid_url($name)) {
    return false;
  }
  return true;
}


/**
 * valid_extension
 *
 * @param string $extension
 * @param string $allowed_extensions
 * @return boolean
 */
function valid_extension($extension, $allowed_extensions)
{
  $extensions = explode(',', $allowed_extensions);
  foreach ($extensions as $key => $value) {
    $extensions[$key] = strtolower(trim($value));
  }
  if (is_array($extensions) && in_array($extension, $extensions)) {
    return true;
  }
  return false;
}


/**
 * set_extensions_string
 *
 * @param string $extensions
 * @return string
 */
function set_extensions_string($extensions)
{
  $extensions_string = "";
  $extensions = explode(',', $extensions);
  foreach ($extensions as $key => $value) {
    $extensions_string .= "." . strtolower(trim($value)) . ",";
  }
  $extensions_string = substr($extensions_string, 0, -1);
  return $extensions_string;
}



/* ------------------------------- */
/* Date */
/* ------------------------------- */

/**
 * set_datetime
 *
 * @param string $date
 * @return string
 */
function set_datetime($date)
{
  global $system;
  $date = str_replace(['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'], range(0, 9), $date); /* check and replace arabic numbers if any */
  $datetime = date("Y-m-d H:i:s", strtotime($date));
  return $datetime;
}


/**
 * get_datetime
 *
 * @param string $date
 * @return string
 */
function get_datetime($date)
{
  global $system;
  return date($system['system_datetime_format'], strtotime($date));
}



/* ------------------------------- */
/* JSON */
/* ------------------------------- */

/**
 * return_json
 *
 * @param array $response
 * @return json
 */
function return_json($response = [])
{
  header('Content-Type: application/json');
  exit(json_encode($response));
}


/**
 * return_json_async
 *
 * @param array $response
 * @return void
 */
function return_json_async($response = [])
{
  if (!empty(ob_get_status()) && is_callable('fastcgi_finish_request')) {
    ob_end_clean();
    header("Content-Encoding: none");
    header("Connection: close");
    ignore_user_abort();
    ob_start();
    header('Content-Type: application/json');
    echo json_encode($response);
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();
    flush();
    session_write_close();
    fastcgi_finish_request();
  }
}



/* ------------------------------- */
/* Error */
/* ------------------------------- */

/**
 * _error
 *
 * @return void
 */
function _error()
{
  $args = func_get_args();
  if (count($args) > 1 && !in_array($args[0], ['BANNED_USER', 'PERMISSION'])) {
    $title = $args[0];
    $message = $args[1];
  } else {
    switch ($args[0]) {
      case 'DB_ERROR':
        $title = "Database Error";
        $message = "<div class='text-start'><h1>" . "Error establishing a database connection" . "</h1>
                            <p>" . "This either means that the username and password information in your config.php file is incorrect or we can't contact the database server at localhost. This could mean your host's database server is down." . "</p>
                            <ul>
                                <li>" . "Are you sure you have the correct username and password?" . "</li>
                                <li>" . "Are you sure that you have typed the correct hostname?" . "</li>
                                <li>" . "Are you sure that the database server is running?" . "</li>
                            </ul>
                            <p>" . "If you're unsure what these terms mean you should probably contact your host. If you still need help you can always visit the" . " <a href='https://support.zamblek.com'>" . "Sngine Support" . ".</a></p>
                            </div>";
        break;

      case 'SQL_ERROR':
        $title = __("Database Error");
        $message = __("An error occurred while writing to database. Please try again later");
        if (DEBUGGING) {
          $backtrace = debug_backtrace();
          $line = $backtrace[0]['line'];
          $file = $backtrace[0]['file'];
          $message .= "<br><br><small>This error function was called from line $line in file $file</small>";
        }
        break;

      case 'SQL_ERROR_THROWEN':
        $message = __("An error occurred while writing to database. Please try again later");
        if (DEBUGGING) {
          $backtrace = debug_backtrace();
          $line = $backtrace[0]['line'];
          $file = $backtrace[0]['file'];
          $message .= "<br><br><small>This error function was called from line $line in file $file</small>";
        }
        throw new SQLException($message);
        break;

      case 'PERMISSION':
        global $smarty;
        $title = __("Permission Needed");
        $message = ($args[1]) ? $args[1] : __("You do not have the permission to view this content");
        if (isset($smarty)) {
          $smarty->assign('message', $message);
          page_header($title);
          page_footer('permission');
          exit;
        }
        break;

      case 'BANNED':
        global $smarty;
        $title = __("Banned");
        $message = __("You do not have the permission to view this content");
        if (isset($smarty)) {
          $smarty->assign('message', $message);
          page_header($title);
          page_footer('banned');
          exit;
        }
        break;

      case 'BANNED_USER':
        global $smarty;
        $title = __("Banned Account");
        $message = $args[1];
        if (isset($smarty)) {
          $smarty->assign('message', $message);
          page_header($title);
          page_footer('banned');
          exit;
        }
        break;

      case 'ACTIVATION':
        global $smarty;
        if (isset($smarty)) {
          page_header(__("Activation Required"));
          page_footer('activation');
          exit;
        }
        break;

      case '400':
        header('HTTP/1.0 400 Bad Request');
        if (DEBUGGING) {
          $backtrace = debug_backtrace();
          $line = $backtrace[0]['line'];
          $file = $backtrace[0]['file'];
          exit("This error function was called from line $line in file $file");
        }
        exit;
        break;

      case '401':
        header('HTTP/1.0 401 Unauthorized');
        if (DEBUGGING) {
          $backtrace = debug_backtrace();
          $line = $backtrace[0]['line'];
          $file = $backtrace[0]['file'];
          exit("This error function was called from line $line in file $file");
        }
        exit;
        break;

      case '403':
        header('HTTP/1.0 403 Access Denied');
        if (DEBUGGING) {
          $backtrace = debug_backtrace();
          $line = $backtrace[0]['line'];
          $file = $backtrace[0]['file'];
          exit("This error function was called from line $line in file $file");
        }
        exit;
        break;

      case '404':
        global $smarty;
        header('HTTP/1.0 404 Not Found');
        $title = __("404 Not Found");
        $message = __("Sorry but the page you are looking for does not exist, have been removed. name changed or is temporarily unavailable");
        if (DEBUGGING) {
          $backtrace = debug_backtrace();
          $line = $backtrace[0]['line'];
          $file = $backtrace[0]['file'];
          $message .= "<br><br><small>This error function was called from line $line in file $file</small>";
        }
        if (isset($smarty)) {
          $smarty->assign('message', $message);
          page_header($title);
          page_footer('404');
          exit;
        }
        break;

      default:
        $title = __("Error");
        $message = __("There is some thing went wrong");
        if (DEBUGGING) {
          $backtrace = debug_backtrace();
          $line = $backtrace[0]['line'];
          $file = $backtrace[0]['file'];
          $message .= "<br><br>" . "<small>This error function was called from line $line in file $file</small>";
        }
        break;
    }
  }
  echo '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>' . $title . '</title>
                <style type="text/css">
                    html {
                        background: #f1f1f1;
                    }
                    body {
                        color: #555;
                        font-family: "Open Sans", Arial,sans-serif;
                        margin: 0;
                        padding: 0;
                    }
                    .error-title {
                        background: #ce3426;
                        color: #fff;
                        text-align: center;
                        font-size: 34px;
                        font-weight: 100;
                        line-height: 50px;
                        padding: 60px 0;
                    }
                    .error-message {
                        margin: 1em auto;
                        padding: 1em 2em;
                        max-width: 600px;
                        font-size: 1em;
                        line-height: 1.8em;
                        text-align: center;
                    }
                    .error-message .code,
                    .error-message p {
                        margin-top: 0;
                        margin-bottom: 1.3em;
                    }
                    .error-message .code {
                        font-family: Consolas, Monaco, monospace;
                        background: rgba(0, 0, 0, 0.7);
                        padding: 10px;
                        color: rgba(255, 255, 255, 0.7);
                        word-break: break-all;
                        border-radius: 2px;
                    }
                    h1 {
                        font-size: 1.2em;
                    }

                    ul li {
                        margin-bottom: 1em;
                        font-size: 0.9em;
                    }
                    a {
                        color: #ce3426;
                        text-decoration: none;
                    }
                    a:hover {
                        text-decoration: underline;
                    }
                    .button {
                        background: #f7f7f7;
                        border: 1px solid #cccccc;
                        color: #555;
                        display: inline-block;
                        text-decoration: none;
                        margin: 0;
                        padding: 5px 10px;
                        cursor: pointer;
                        -webkit-border-radius: 3px;
                        -webkit-appearance: none;
                        border-radius: 3px;
                        white-space: nowrap;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing:    border-box;
                        box-sizing:         border-box;

                        -webkit-box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
                        box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
                        vertical-align: top;
                    }

                    .button.button-large {
                        height: 29px;
                        line-height: 28px;
                        padding: 0 12px;
                    }

                    .button:hover,
                    .button:focus {
                        background: #fafafa;
                        border-color: #999;
                        color: #222;
                        text-decoration: none;
                    }

                    .button:focus  {
                        -webkit-box-shadow: 1px 1px 1px rgba(0,0,0,.2);
                        box-shadow: 1px 1px 1px rgba(0,0,0,.2);
                    }

                    .button:active {
                        background: #eee;
                        border-color: #999;
                        color: #333;
                        -webkit-box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
                        box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
                    }
                    .text-start {
                        text-align: left;
                    }
                </style>
            </head>
            <body>
                <div class="error-title">' . $title . '</div>
                <div class="error-message">' . $message . '</div>
            </body>
            </html>';
  exit;
}



/* ------------------------------- */
/* Email */
/* ------------------------------- */

/**
 * _email
 *
 * @param string $email
 * @param string $subject
 * @param string $body_html
 * @param string $body_plain
 * @param boolean $is_html
 * @param boolean $only_smtp
 * @return boolean
 */
function _email($email, $subject, $body_html, $body_plain, $is_html = true, $only_smtp = false)
{
  global $system;
  /* set header */
  $header  = "MIME-Version: 1.0\r\n";
  $header .= "Mailer: " . html_entity_decode(__($system['system_title']), ENT_QUOTES) . "\r\n";
  if ($system['system_email']) {
    $header = "From: " . $system['system_email'] . "\r\n";
    $header .= "Reply-To: " . $system['system_email'] . "\r\n";
  }
  if ($is_html) {
    $header .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
  } else {
    $header .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
  }
  /* send email */
  if ($system['email_smtp_enabled']) {
    /* SMTP */
    $mail = new PHPMailer\PHPMailer\PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();
    $mail->Host = $system['email_smtp_server'];
    $mail->SMTPAuth = ($system['email_smtp_authentication']) ? true : false;
    $mail->Username = $system['email_smtp_username'];
    $mail->Password = html_entity_decode($system['email_smtp_password']);
    $mail->SMTPSecure = ($system['email_smtp_ssl']) ? 'ssl' : 'tls';
    $mail->Port = $system['email_smtp_port'];
    $setfrom = (is_empty($system['email_smtp_setfrom'])) ? $system['email_smtp_username'] : $system['email_smtp_setfrom'];
    $mail->setFrom($setfrom, html_entity_decode(__($system['system_title']), ENT_QUOTES));
    $mail->addAddress($email);
    $mail->Subject = $subject;
    if ($is_html) {
      $mail->isHTML(true);
      $mail->Body = $body_html;
      $mail->AltBody = $body_plain;
    } else {
      $mail->Body = $body_plain;
    }
    if (!$mail->send()) {
      if ($only_smtp) {
        return false;
      }
      /* send using mail() */
      if (!mail($email, $subject, $body_html, $header)) {
        return false;
      }
    }
  } else {
    if ($only_smtp) {
      return false;
    }
    /* send using mail() */
    if (!mail($email, $subject, $body_html, $header)) {
      return false;
    }
  }
  return true;
}


/**
 * email_smtp_test
 *
 * @return void
 */
function email_smtp_test()
{
  global $system;
  /* prepare test email */
  $subject = __("Test SMTP Connection on") . " " . html_entity_decode(__($system['system_title']), ENT_QUOTES);
  $body = get_email_template("test_email", $subject);
  /* send email */
  if (!_email($system['system_email'], $subject, $body['html'], $body['plain'], true, true)) {
    throw new Exception(__("Test email could not be sent. Please check your settings"));
  }
}


/**
 * get_email_template
 *
 * @param string $template_name
 * @param string $template_subject
 * @param array $template_variables
 * @return array
 */
function get_email_template($template_name, $template_subject, $template_variables = [])
{
  global $system, $smarty;
  $smarty->assign('system', $system);
  $smarty->assign("template_subject", $template_subject);
  if ($template_variables) {
    foreach ($template_variables as $key => $value) {
      $smarty->assign($key, $value);
    }
  }
  $body['html'] = $smarty->fetch("emails/" . $template_name . ".html");
  $body['plain'] = $smarty->fetch("emails/" . $template_name . ".txt");
  return $body;
}



/* ------------------------------- */
/* SMS */
/* ------------------------------- */

/**
 * sms_send
 *
 * @param string $phone
 * @param string $message
 * @return boolean
 */
function sms_send($phone, $message)
{
  global $system, $db, $date;
  /* check if this phone sent more than 3 SMS within 1 hour */
  $check_log = $db->query(sprintf("SELECT * FROM users_sms WHERE phone = %s AND insert_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)", secure($phone))) or _error('SQL_ERROR_THROWEN');
  if ($check_log->num_rows > $system['sms_limit']) {
    throw new Exception(__("You have reached the maximum number of SMS allowed per hour"));
    return false;
  }
  switch ($system['sms_provider']) {
    case 'twilio':
      $client = new Twilio\Rest\Client($system['twilio_sid'], $system['twilio_token']);
      $message = $client->account->messages->create(
        $phone,
        [
          'from' => $system['twilio_phone'],
          'body' => $message
        ]
      );
      if (!$message->sid) {
        return false;
      }
      break;

    case 'bulksms':
      $username = $system['bulksms_username'];
      $password = $system['bulksms_password'];
      $headers = [
        'Content-Type:application/json',
        'Authorization:Basic ' . base64_encode("$username:$password")
      ];
      $request_body = [
        [
          'to' => $phone,
          'body' => $message
        ]
      ];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_URL, "https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
      curl_setopt($ch, CURLOPT_TIMEOUT, 20);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (curl_errno($ch)) {
        return false;
      }
      curl_close($ch);
      if ($httpCode != 201) {
        return false;
      }
      break;

    case 'infobip':
      $headers = [
        "Content-Type:application/json",
        "Accept:application/json"
      ];
      $request_body = [
        "from" => $system['system_title'],
        "to" => $phone,
        "text" => $message
      ];
      $userpwd = $system['infobip_username'] . ':' . $system['infobip_password'];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://api.infobip.com/sms/1/text/single");
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, html_entity_decode($userpwd));
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (curl_errno($ch)) {
        return false;
      }
      curl_close($ch);
      if (!($httpCode >= 200 && $httpCode < 300)) {
        return false;
      }
      break;

    case 'msg91':
      $request_body = [
        'authkey' => $system['msg91_authkey'],
        'mobiles' => $phone,
        'message' => $message,
        'sender' => uniqid(),
        'route' => "4"
      ];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://api.msg91.com/api/sendhttp.php");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (curl_errno($ch)) {
        return false;
      }
      curl_close($ch);
      if (!($httpCode >= 200 && $httpCode < 300)) {
        return false;
      }
      break;
  }
  /* insert this to SMS log */
  $db->query(sprintf("INSERT INTO users_sms (phone, insert_date) VALUES (%s, %s)", secure($phone), secure($date)));
  return true;
}


/**
 * sms_test
 *
 * @return void
 */
function sms_test()
{
  global $system;
  if (is_empty($system['system_phone'])) {
    throw new Exception(__("You need to enter Test Phone Number"));
  }
  switch ($system['sms_provider']) {
    case 'twilio':
      $client = new Twilio\Rest\Client($system['twilio_sid'], $system['twilio_token']);
      $message = $client->account->messages->create(
        $system['system_phone'],
        [
          'from' => $system['twilio_phone'],
          'body' => __("Test SMS from") . " " . __($system['system_title'])
        ]
      );
      if (!$message->sid) {
        throw new Exception(__("Test SMS could not be sent. Please check your settings"));
      }
      break;

    case 'bulksms':
      $username = $system['bulksms_username'];
      $password = $system['bulksms_password'];
      $headers = [
        'Content-Type:application/json',
        'Authorization:Basic ' . base64_encode("$username:$password")
      ];
      $request_body = [
        [
          'to' => $system['system_phone'],
          'body' => __("Test SMS from") . " " . __($system['system_title'])
        ]
      ];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_URL, "https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
      curl_setopt($ch, CURLOPT_TIMEOUT, 20);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (curl_errno($ch)) {
        throw new Exception(__("Test SMS could not be sent. Please check your settings"));
      }
      curl_close($ch);
      if ($httpCode != 201) {
        throw new Exception(__("Test SMS could not be sent. Please check your settings"));
      }
      break;

    case 'infobip':
      $headers = [
        "Content-Type:application/json",
        "Accept:application/json"
      ];
      $request_body = [
        "from" => $system['system_title'],
        "to" => $system['system_phone'],
        "text" => __("Test SMS from") . " " . __($system['system_title'])
      ];
      $userpwd = $system['infobip_username'] . ':' . $system['infobip_password'];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://api.infobip.com/sms/1/text/single");
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, html_entity_decode($userpwd));
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (curl_errno($ch)) {
        throw new Exception(__("Test SMS could not be sent. Please check your settings"));
      }
      curl_close($ch);
      if (!($httpCode >= 200 && $httpCode < 300)) {
        throw new Exception(__("Test SMS could not be sent. Please check your settings"));
      }
      break;

    case 'msg91':
      $message = urlencode(__("Test SMS from") . " " . __($system['system_title']));
      $request_body = [
        'authkey' => $system['msg91_authkey'],
        'mobiles' => $system['system_phone'],
        'message' => $message,
        'sender' => uniqid(),
        'route' => "4"
      ];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://api.msg91.com/api/sendhttp.php");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (curl_errno($ch)) {
        throw new Exception(__("Test SMS could not be sent. Please check your settings"));
      }
      curl_close($ch);
      if (!($httpCode >= 200 && $httpCode < 300)) {
        throw new Exception(__("Test SMS could not be sent. Please check your settings"));
      }
      break;
  }
}



/* ------------------------------- */
/* OneSignal Notifications */
/* ------------------------------- */

/**
 * onesignal_notification
 *
 * @param string $send_to
 * @param string $notification
 * @return boolean
 */
function onesignal_notification($send_to, $notification)
{
  global $system;
  $request_body = [
    'app_id' => $system['onesignal_app_id'],
    'include_player_ids' => [$send_to],
    'url' => $notification['url'],
    'contents' => [
      'en' => $notification['full_message']
    ],
    'headings' => [
      'en' => $system['system_title']
    ],
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  if (!($httpCode >= 200 && $httpCode < 300)) {
    return false;
  }
  return true;
}



/* ------------------------------- */
/* Google Vision */
/* ------------------------------- */

/**
 * google_vision_test
 *
 * @return void
 */
function google_vision_test()
{
  global $system;
  $image_source = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/og-image.jpg';
  try {
    $content = '{
        "requests":[
            {
                "image":{
                    "content": "' . base64_encode(file_get_contents($image_source)) . '",
                },
                "features":[
                    {
                        "type":"SAFE_SEARCH_DETECTION",
                        "maxResults":1
                    },
                    {
                        "type":"WEB_DETECTION",
                        "maxResults":2
                    }
                ]
            }
        ]
    }';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://vision.googleapis.com/v1/images:annotate?key=' . $system['adult_images_api_key']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($content)]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response  = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (curl_errno($ch)) {
      throw new Exception("Error Processing Request");
    }
    curl_close($ch);
    $responseJson = json_decode($response);
    if ($responseJson->error) {
      throw new Exception($responseJson->error->message);
    }
    if ($responseJson->responses[0]->error) {
      throw new Exception($responseJson->responses[0]->error->message);
    }
    if (!$responseJson->responses[0]->safeSearchAnnotation) {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  } catch (Exception $e) {
    if (DEBUGGING) {
      throw new Exception($e->getMessage());
    } else {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  }
}


/**
 * google_vision_check
 *
 * @param string $image_source
 * @return boolean
 */
function google_vision_check($image_source)
{
  global $system;
  try {
    $content = '{
        "requests":[
            {
                "image":{
                    "content": "' . base64_encode(file_get_contents($image_source)) . '",
                },
                "features":[
                    {
                        "type":"SAFE_SEARCH_DETECTION",
                        "maxResults":1
                    },
                    {
                        "type":"WEB_DETECTION",
                        "maxResults":2
                    }
                ]
            }
        ]
    }';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://vision.googleapis.com/v1/images:annotate?key=' . $system['adult_images_api_key']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($content)]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response  = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (curl_errno($ch)) {
      return false;
    }
    curl_close($ch);
    $responseJson = json_decode($response);
    if ($responseJson->error) {
      return false;
    }
    if ($responseJson->responses[0]->error) {
      return false;
    }
    if ($responseJson->responses[0]->safeSearchAnnotation->adult == 'LIKELY' || $responseJson->responses[0]->safeSearchAnnotation->adult == 'VERY_LIKELY') {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    return false;
  }
}



/* ------------------------------- */
/* Upload ✅ */
/* ------------------------------- */

/**
 * upload_file
 *
 * @param boolean $from_web
 * @return string|array
 */
function upload_file($from_web = false)
{
  global $system, $db, $user, $date;
  // set execution time (unlimited)
  set_time_limit(0);

  // check secret
  if ($from_web && $_SESSION['secret'] != $_POST['secret']) {
    /* check server post_max_size */
    if (isset($_SERVER['CONTENT_LENGTH'])) {
      $post_max_size = ini_get('post_max_size');
      $post_max_size = trim($post_max_size);
      $last = strtolower($post_max_size[strlen($post_max_size) - 1]);
      switch ($last) {
        case 'g':
          $post_max_size *= 1024;
        case 'm':
          $post_max_size *= 1024;
        case 'k':
          $post_max_size *= 1024;
      }
      if ($_SERVER['CONTENT_LENGTH'] > $post_max_size) {
        throw new ValidationException(__("The file size is so big") . ", " . __("The 'post_max_size' size is:") . " " . ($post_max_size / 1024 / 1024) . __("MB"));
      }
    }
    /* check server upload_max_filesize */
    if (isset($_SERVER['CONTENT_LENGTH'])) {
      $upload_max_filesize = ini_get('upload_max_filesize');
      $upload_max_filesize = trim($upload_max_filesize);
      $last = strtolower($upload_max_filesize[strlen($upload_max_filesize) - 1]);
      switch ($last) {
        case 'g':
          $upload_max_filesize *= 1024;
        case 'm':
          $upload_max_filesize *= 1024;
        case 'k':
          $upload_max_filesize *= 1024;
      }
      if ($_SERVER['CONTENT_LENGTH'] > $upload_max_filesize) {
        throw new ValidationException(__("The file size is so big") . ", " . __("The 'upload_max_filesize' size is:") . " " . ($upload_max_filesize / 1024 / 1024) . __("MB"));
      }
    }
    /* check server memory_limit */
    if (isset($_SERVER['CONTENT_LENGTH'])) {
      $memory_limit = ini_get('memory_limit');
      $memory_limit = trim($memory_limit);
      $last = strtolower($memory_limit[strlen($memory_limit) - 1]);
      switch ($last) {
        case 'g':
          $memory_limit *= 1024;
        case 'm':
          $memory_limit *= 1024;
        case 'k':
          $memory_limit *= 1024;
      }
      if ($_SERVER['CONTENT_LENGTH'] > $memory_limit) {
        throw new ValidationException(__("The file size is so big") . ", " . __("The 'memory_limit is:") . " " . ($memory_limit / 1024 / 1024) . __("MB"));
      }
    }
    /* none of the above */
    throw new BadRequestException(__("Invalid secret"));
  }

  // check type & handle & multiple
  if (!isset($_POST["type"])) {
    throw new BadRequestException(__("Invalid type"));
  }
  if (!isset($_POST["handle"])) {
    throw new BadRequestException(__("Invalid handle"));
  }
  if (!isset($_POST["multiple"])) {
    throw new BadRequestException(__("Invalid multiple"));
  }

  switch ($_POST["type"]) {
    case 'photos':
      // fetch image class
      require_once(ABSPATH . 'includes/class-image.php');

      // check photo upload enabled
      if ($_POST['handle'] == 'publisher' && !$system['photos_enabled']) {
        throw new AuthorizationException(__("Uploading photos feature has been disabled by the admin"));
      }
      if ($_POST['handle'] == 'comment' && !$system['comments_photos_enabled']) {
        throw new AuthorizationException(__("Uploading photos feature has been disabled by the admin"));
      }
      if ($_POST['handle'] == 'chat' && !$system['chat_photos_enabled']) {
        throw new AuthorizationException(__("Uploading photos feature has been disabled by the admin"));
      }
      if ($_POST['handle'] == 'tinymce' && !$system['tinymce_photos_enabled']) {
        throw new AuthorizationException(__("Uploading photos feature has been disabled by the admin"));
      }

      // get allowed file size
      if ($_POST['handle'] == 'picture-user' || $_POST['handle'] == 'picture-page' || $_POST['handle'] == 'picture-group') {
        $max_allowed_size = $system['max_avatar_size'] * 1024;
      } elseif ($_POST['handle'] == 'cover-user' || $_POST['handle'] == 'cover-page' || $_POST['handle'] == 'cover-group') {
        $max_allowed_size = $system['max_cover_size'] * 1024;
      } else {
        $max_allowed_size = $system['max_photo_size'] * 1024;
      }

      // prepare uploads directory
      $folder = 'photos';
      $directory = $folder . '/' . date('Y') . '/' . date('m') . '/';

      if ($_POST["multiple"] == "true") {

        $files = [];
        foreach ($_FILES['file'] as $key => $val) {
          for ($i = 0; $i < count($val); $i++) {
            $files[$i][$key] = $val[$i];
          }
        }
        $return_files = [];
        $files_num = count($files);
        foreach ($files as $file) {
          // valid inputs
          if (!isset($file) || $file["error"] != UPLOAD_ERR_OK) {
            if ($files_num > 1) {
              continue;
            } else {
              throw new Exception(__("Something wrong with upload! Is 'upload_max_filesize' set correctly?"));
            }
          }

          // check file size
          if (!$user->_is_admin && !$user->_is_moderator) {
            if ($file["size"] > $max_allowed_size) {
              if ($files_num > 1) {
                continue;
              } else {
                throw new ValidationException(__("The file size is so big") . ", " . __("The allowed file size is:") . " " . ($max_allowed_size / 1024 / 1024) . __("MB"));
              }
            }
          }

          // init image & prepare image name & path
          try {
            $image = new Image($file["tmp_name"]);
          } catch (Exception $e) {
            if ($files_num > 1) {
              continue;
            } else {
              throw new Exception(__("Sorry, can not upload the file"));
            }
          }
          $prefix = $system['uploads_prefix'] . '_' . get_hash_token();
          $file_name = $directory . $prefix . $image->_img_ext;
          $path = ABSPATH . $system['uploads_directory'] . '/' . $file_name;
          $image_blured = 0;

          // adult images detection
          if ($system['adult_images_enabled']) {
            if ($_POST['handle'] != "x-image" && google_vision_check($file["tmp_name"])) {
              if ($system['adult_images_action'] == "delete") {
                if ($files_num > 1) {
                  continue;
                } else {
                  throw new ValidationException(__("Sorry, can not upload the file for adult content"));
                }
              } else {
                $image_blured = 1;
              }
            }
          }

          // watermark images
          $image_watermarked = false;
          if ($system['watermark_enabled']) {
            if (($_POST['handle'] == "publisher" || $_POST['handle'] == "publisher-mini") && !in_array($image->_img_type, ["image/gif", "image/webp"])) {
              watermark_image($file["tmp_name"], $image->_img_type);
              $image_watermarked = true;
            }
          }

          // upload to
          if ($system['s3_enabled']) {
            /* Amazon S3 */
            aws_s3_upload($file["tmp_name"], $file_name, mime_content_type($file['tmp_name']));
          } elseif ($system['google_cloud_enabled']) {
            /* Google Cloud */
            google_cloud_upload($file["tmp_name"], $file_name, mime_content_type($file['tmp_name']));
          } elseif ($system['digitalocean_enabled']) {
            /* DigitalOcean */
            digitalocean_space_upload($file["tmp_name"], $file_name, mime_content_type($file['tmp_name']));
          } elseif ($system['wasabi_enabled']) {
            /* Wasabi */
            wasabi_upload($file["tmp_name"], $file_name, mime_content_type($file['tmp_name']));
          } elseif ($system['backblaze_enabled']) {
            /* Backblaze */
            backblaze_upload($file["tmp_name"], $file_name, mime_content_type($file['tmp_name']));
          } elseif ($system['ftp_enabled']) {
            /* FTP */
            ftp_upload($file["tmp_name"], $file_name);
          } else {
            /* local server */
            /* set uploads directory */
            if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder)) {
              @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder, 0777, true);
            }
            if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'))) {
              @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'), 0777, true);
            }
            if (!file_exists($system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'))) {
              @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'), 0777, true);
            }
            /* save the new image */
            if (in_array($image->_img_type, ["image/gif", "image/webp"])) {
              if (!@move_uploaded_file($file["tmp_name"], $path)) {
                throw new Exception(__("Sorry, can not upload the file"));
              }
            } else {
              if ($image_watermarked) {
                $image = new Image($file["tmp_name"]);
              }
              $image->save($path, $system['uploads_quality']);
            }
          }

          /* return */
          $return_files[] = ["source" => $file_name, "blur" => $image_blured];
        }

        // return the return_files & exit
        return $return_files;
      } else {

        // valid inputs
        if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
          throw new Exception(__("Something wrong with upload! Is 'upload_max_filesize' set correctly?"));
        }

        // check file size
        if (!$user->_is_admin && !$user->_is_moderator) {
          if ($_FILES["file"]["size"] > $max_allowed_size) {
            throw new ValidationException(__("The file size is so big") . ", " . __("The allowed file size is:") . " " . ($max_allowed_size / 1024 / 1024) . __("MB"));
          }
        }

        // init image & prepare image name & path
        $image = new Image($_FILES["file"]["tmp_name"]);
        $prefix = $system['uploads_prefix'] . '_' . get_hash_token();
        $file_name = $directory . $prefix . $image->_img_ext;
        $path = ABSPATH . $system['uploads_directory'] . '/' . $file_name;
        $image_blured = 0;


        // check if animated webp allowed
        if (!$system['allow_animated_images']) {
          if ($image->_img_type == "image/gif" || ($image->isWebpAnimated($_FILES["file"]["tmp_name"]) && in_array($_POST['handle'], ['cover-user', 'picture-user', 'cover-page', 'picture-page', 'cover-group', 'picture-group', 'cover-event']))) {
            throw new ValidationException(__("Sorry, You can't upload animated webp images"));
          }
        }

        // check image resolution
        if ($_POST['handle'] == 'picture-user' || $_POST['handle'] == 'picture-page' || $_POST['handle'] == 'picture-group') {
          if ($image->getWidth() < 150 || $image->getHeight() < 150) {
            throw new ValidationException(__("Please choose an image that's at least 150 pixels wide and at least 150 pixels tall"));
          }
        } elseif ($_POST['handle'] == 'cover-user' || $_POST['handle'] == 'cover-page' || $_POST['handle'] == 'cover-group') {
          if ($system['limit_cover_photo']) {
            if ($image->getWidth() < 1108 || $image->getHeight() < 360) {
              throw new ValidationException(__("Please choose an image that's at least 1108 pixels wide and at least 360 pixels tall"));
            }
          }
        }

        // adult images detection
        if ($system['adult_images_enabled']) {
          if ($_POST['handle'] != "x-image" && google_vision_check($_FILES['file']['tmp_name'])) {
            if ($system['adult_images_action'] == "delete") {
              throw new ValidationException(__("Sorry, can not upload the file for adult content"));
            } else {
              $image_blured = 1;
            }
          }
        }

        // upload to
        if ($system['s3_enabled']) {
          /* Amazon S3 */
          aws_s3_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['google_cloud_enabled']) {
          /* Google Cloud */
          google_cloud_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['digitalocean_enabled']) {
          /* DigitalOcean */
          digitalocean_space_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['wasabi_enabled']) {
          /* Wasabi */
          wasabi_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['backblaze_enabled']) {
          /* Backblaze */
          backblaze_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['ftp_enabled']) {
          /* FTP */
          ftp_upload($_FILES['file']['tmp_name'], $file_name);
        } else {
          /* local server */
          /* set uploads directory */
          if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder)) {
            @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder, 0777, true);
          }
          if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'))) {
            @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'), 0777, true);
          }
          if (!file_exists($system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'))) {
            @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'), 0777, true);
          }
          /* save the new image */
          if (in_array($image->_img_type, ["image/gif", "image/webp"])) {
            if (!@move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
              throw new Exception(__("Sorry, can not upload the file"));
            }
          } else {
            $image->save($path, $system['uploads_quality']);
          }
        }

        // check the handle
        switch ($_POST['handle']) {
          case 'cover-user':
            /* check for cover album */
            if (!$user->_data['user_album_covers']) {
              /* create new cover album */
              $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'user', 'Cover Photos', 'public')", secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
              $user->_data['user_album_covers'] = $db->insert_id;
              /* update user cover album id */
              $db->query(sprintf("UPDATE users SET user_album_covers = %s WHERE user_id = %s", secure($user->_data['user_album_covers'], 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            }
            /* insert updated cover photo post */
            $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'user', 'profile_cover', %s, 'public')", secure($user->_data['user_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
            $post_id = $db->insert_id;
            /* insert new cover photo to album */
            $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($user->_data['user_album_covers'], 'int'), secure($file_name), secure($image_blured))) or _error('SQL_ERROR_THROWEN');
            $photo_id = $db->insert_id;
            /* update user cover */
            $db->query(sprintf("UPDATE users SET user_cover = %s, user_cover_id = %s WHERE user_id = %s", secure($file_name), secure($photo_id, 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'picture-user':
            /* check for profile pictures album */
            if (!$user->_data['user_album_pictures']) {
              /* create new profile pictures album */
              $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'user', 'Profile Pictures', 'public')", secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
              $user->_data['user_album_pictures'] = $db->insert_id;
              /* update user profile picture album id */
              $db->query(sprintf("UPDATE users SET user_album_pictures = %s WHERE user_id = %s", secure($user->_data['user_album_pictures'], 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            }
            /* insert updated profile picture post */
            $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'user', 'profile_picture', %s, 'public')", secure($user->_data['user_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
            $post_id = $db->insert_id;
            /* insert new profile picture to album */
            $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($user->_data['user_album_pictures'], 'int'), secure($file_name), secure($image_blured))) or _error('SQL_ERROR_THROWEN');
            $photo_id = $db->insert_id;
            /* delete old cropped picture from uploads folder */
            delete_uploads_file($user->_data['user_picture_raw']);
            /* update user profile picture */
            $db->query(sprintf("UPDATE users SET user_picture = %s, user_picture_id = %s WHERE user_id = %s", secure($file_name), secure($photo_id, 'int'), secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'cover-page':
            /* check if page id is set */
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check the page */
            $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($get_page->num_rows == 0) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            $page = $get_page->fetch_assoc();
            /* check if the user is the page admin */
            if (!$user->check_page_adminship($user->_data['user_id'], $page['page_id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check for cover album */
            if (!$page['page_album_covers']) {
              /* create new cover album */
              $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'page', 'Cover Photos', 'public')", secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
              $page['page_album_covers'] = $db->insert_id;
              /* update page cover album id */
              $db->query(sprintf("UPDATE pages SET page_album_covers = %s WHERE page_id = %s", secure($page['page_album_covers'], 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            }
            /* insert updated cover photo post */
            $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'page', 'page_cover', %s, 'public')", secure($page['page_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
            $post_id = $db->insert_id;
            /* insert new cover photo to album */
            $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($page['page_album_covers'], 'int'), secure($file_name), secure($image_blured))) or _error('SQL_ERROR_THROWEN');
            $photo_id = $db->insert_id;
            /* update page cover */
            $db->query(sprintf("UPDATE pages SET page_cover = %s, page_cover_id = %s WHERE page_id = %s", secure($file_name), secure($photo_id, 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'picture-page':
            /* check if page id is set */
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check the page */
            $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($get_page->num_rows == 0) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            $page = $get_page->fetch_assoc();
            /* check if the user is the page admin */
            if (!$user->check_page_adminship($user->_data['user_id'], $page['page_id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check for page pictures album */
            if (!$page['page_album_pictures']) {
              /* create new page pictures album */
              $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, title, privacy) VALUES (%s, 'page', 'Profile Pictures', 'public')", secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
              $page['page_album_pictures'] = $db->insert_id;
              /* update page profile picture album id */
              $db->query(sprintf("UPDATE pages SET page_album_pictures = %s WHERE page_id = %s", secure($page['page_album_pictures'], 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            }
            /* insert updated page picture post */
            $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, time, privacy) VALUES (%s, 'page', 'page_picture', %s, 'public')", secure($page['page_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
            $post_id = $db->insert_id;
            /* insert new page picture to album */
            $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($page['page_album_pictures'], 'int'), secure($file_name), secure($image_blured))) or _error('SQL_ERROR_THROWEN');
            $photo_id = $db->insert_id;
            /* delete old cropped picture from uploads folder */
            delete_uploads_file($page['page_picture']);
            /* update page picture */
            $db->query(sprintf("UPDATE pages SET page_picture = %s, page_picture_id = %s WHERE page_id = %s", secure($file_name), secure($photo_id, 'int'), secure($page['page_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'cover-group':
            /* check if group id is set */
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check the group */
            $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($get_group->num_rows == 0) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            $group = $get_group->fetch_assoc();
            /* check if the user is the group admin */
            if (!$user->check_group_adminship($user->_data['user_id'], $group['group_id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check for group covers album */
            if (!$group['group_album_covers']) {
              /* create new group covers album */
              $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_group, group_id, title, privacy) VALUES (%s, 'user', '1', %s, 'Cover Photos', 'public')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
              $group['group_album_covers'] = $db->insert_id;
              /* update group cover album id */
              $db->query(sprintf("UPDATE `groups` SET group_album_covers = %s WHERE group_id = %s", secure($group['group_album_covers'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            }
            /* insert updated group cover post */
            $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, in_group, group_id, time, privacy) VALUES (%s, 'user', 'group_cover', '1', %s, %s, 'custom')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
            $post_id = $db->insert_id;
            /* insert new group cover to album */
            $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($group['group_album_covers'], 'int'), secure($file_name), secure($image_blured))) or _error('SQL_ERROR_THROWEN');
            $photo_id = $db->insert_id;
            /* update group cover */
            $db->query(sprintf("UPDATE `groups` SET group_cover = %s, group_cover_id = %s WHERE group_id = %s", secure($file_name), secure($photo_id, 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'picture-group':
            /* check if group id is set */
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check the group */
            $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($get_group->num_rows == 0) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            $group = $get_group->fetch_assoc();
            /* check if the user is the group admin */
            if (!$user->check_group_adminship($user->_data['user_id'], $group['group_id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check for group pictures album */
            if (!$group['group_album_pictures']) {
              /* create new group pictures album */
              $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_group, group_id, title, privacy) VALUES (%s, 'user', '1', %s, 'Profile Pictures', 'public')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
              $group['group_album_pictures'] = $db->insert_id;
              /* update group profile picture album id */
              $db->query(sprintf("UPDATE `groups` SET group_album_pictures = %s WHERE group_id = %s", secure($group['group_album_pictures'], 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            }
            /* insert updated group picture post */
            $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, in_group, group_id, time, privacy) VALUES (%s, 'user', 'group_picture', '1', %s, %s, 'custom')", secure($user->_data['user_id'], 'int'), secure($group['group_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
            $post_id = $db->insert_id;
            /* insert new group picture to album */
            $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($group['group_album_pictures'], 'int'), secure($file_name), secure($image_blured))) or _error('SQL_ERROR_THROWEN');
            $photo_id = $db->insert_id;
            /* delete old cropped picture from uploads folder */
            delete_uploads_file($group['group_picture']);
            /* update group picture */
            $db->query(sprintf("UPDATE `groups` SET group_picture = %s, group_picture_id = %s WHERE group_id = %s", secure($file_name), secure($photo_id, 'int'), secure($group['group_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            break;

          case 'cover-event':
            /* check if event id is set */
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check the event */
            $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($_POST['id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            if ($get_event->num_rows == 0) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            $event = $get_event->fetch_assoc();
            /* check if the user is the event admin */
            if (!$user->check_event_adminship($user->_data['user_id'], $event['event_id'])) {
              /* delete the uploaded image & return error 403 */
              unlink($path);
              _error(403);
            }
            /* check for event covers album */
            if (!$event['event_album_covers']) {
              /* create new event covers album */
              $db->query(sprintf("INSERT INTO posts_photos_albums (user_id, user_type, in_event, event_id, title, privacy) VALUES (%s, 'user', '1', %s, 'Cover Photos', 'public')", secure($user->_data['user_id'], 'int'), secure($event['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
              $event['event_album_covers'] = $db->insert_id;
              /* update event cover album id */
              $db->query(sprintf("UPDATE `events` SET event_album_covers = %s WHERE event_id = %s", secure($event['event_album_covers'], 'int'), secure($event['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            }
            /* prepare author */
            $author_type = ($event['event_page_id']) ? 'page' : 'user';
            $author_id = ($event['event_page_id']) ? $event['event_page_id'] : $user->_data['user_id'];
            /* insert updated event cover post */
            $db->query(sprintf("INSERT INTO posts (user_id, user_type, post_type, in_event, event_id, time, privacy) VALUES (%s, %s, 'event_cover', '1', %s, %s, 'custom')", secure($author_id, 'int'), secure($author_type), secure($event['event_id'], 'int'), secure($date))) or _error('SQL_ERROR_THROWEN');
            $post_id = $db->insert_id;
            /* insert new event cover to album */
            $db->query(sprintf("INSERT INTO posts_photos (post_id, album_id, source, blur) VALUES (%s, %s, %s, %s)", secure($post_id, 'int'), secure($event['event_album_covers'], 'int'), secure($file_name), secure($image_blured))) or _error('SQL_ERROR_THROWEN');
            $photo_id = $db->insert_id;
            /* update event cover */
            $db->query(sprintf("UPDATE `events` SET event_cover = %s, event_cover_id = %s WHERE event_id = %s", secure($file_name), secure($photo_id, 'int'), secure($event['event_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
            break;
        }

        // return the file name & exit
        return $file_name;
      }
      break;

    case 'video':
      // check videos upload permission
      if (!$user->_data['can_upload_videos']) {
        throw new AuthorizationException(__("You don't have the permission to do this"));
      }

      // get allowed file size
      $max_allowed_size = $system['max_video_size'] * 1024;

      // prepare uploads directory
      $folder = 'videos';
      $directory = $folder . '/' . date('Y') . '/' . date('m') . '/';

      if ($_POST["multiple"] == "true") {

        // prepare files
        $files = [];
        foreach ($_FILES['file'] as $key => $val) {
          for ($i = 0; $i < count($val); $i++) {
            $files[$i][$key] = $val[$i];
          }
        }
        $return_files = [];
        $files_num = count($files);

        // upload files
        foreach ($files as $file) {

          // valid inputs
          if (!isset($file) || $file["error"] != UPLOAD_ERR_OK) {
            if ($files_num > 1) {
              continue;
            } else {
              throw new Exception(__("Something wrong with upload! Is 'upload_max_filesize' set correctly?"));
            }
          }

          // check file size
          if (!$user->_is_admin && !$user->_is_moderator) {
            if ($file["size"] > $max_allowed_size) {
              if ($files_num > 1) {
                continue;
              } else {
                throw new ValidationException(__("The file size is so big") . ", " . __("The allowed file size is:") . " " . ($max_allowed_size / 1024 / 1024) . __("MB"));
              }
            }
          }

          // check file extesnion
          $extension = get_extension($file["name"]);
          if (!valid_extension($extension, $system['video_extensions'])) {
            if ($files_num > 1) {
              continue;
            } else {
              throw new BadRequestException(__("The file type is not valid or not supported"));
            }
          }

          // prepare file name & path
          $prefix = $system['uploads_prefix'] . '_' . get_hash_token();
          $file_name = $directory . $prefix . '.' . $extension;
          $path = ABSPATH . $system['uploads_directory'] . '/' . $file_name;

          // upload to
          if ($system['s3_enabled']) {
            /* Amazon S3 */
            aws_s3_upload($file['tmp_name'], $file_name, mime_content_type($file["tmp_name"]));
          } elseif ($system['google_cloud_enabled']) {
            /* Google Cloud */
            google_cloud_upload($file['tmp_name'], $file_name, mime_content_type($file["tmp_name"]));
          } elseif ($system['digitalocean_enabled']) {
            /* DigitalOcean */
            digitalocean_space_upload($file['tmp_name'], $file_name, mime_content_type($file["tmp_name"]));
          } elseif ($system['wasabi_enabled']) {
            /* Wasabi */
            wasabi_upload($file['tmp_name'], $file_name, mime_content_type($file["tmp_name"]));
          } elseif ($system['backblaze_enabled']) {
            /* Backblaze */
            backblaze_upload($file['tmp_name'], $file_name, mime_content_type($file["tmp_name"]));
          } elseif ($system['ftp_enabled']) {
            /* FTP */
            ftp_upload($file['tmp_name'], $file_name);
          } else {
            /* local server */
            /* set uploads directory */
            if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder)) {
              @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder, 0777, true);
            }
            if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'))) {
              @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'), 0777, true);
            }
            if (!file_exists($system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'))) {
              @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'), 0777, true);
            }
            /* check if the file uploaded successfully */
            if (!@move_uploaded_file($file["tmp_name"], $path)) {
              throw new Exception(__("Sorry, can not upload the file"));
            }
          }

          /* return */
          $return_files[] = $file_name;
        }

        // return the return_files & exit
        return $return_files;
      } else {

        // valid inputs
        if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
          throw new Exception(__("Something wrong with upload! Is 'upload_max_filesize' set correctly?"));
        }

        // check file size
        if (!$user->_is_admin && !$user->_is_moderator) {
          if ($_FILES["file"]["size"] > $max_allowed_size) {
            throw new ValidationException(__("The file size is so big") . ", " . __("The allowed file size is:") . " " . ($max_allowed_size / 1024 / 1024) . __("MB"));
          }
        }

        // check file extesnion
        $extension = get_extension($_FILES['file']['name']);
        if (!valid_extension($extension, $system['video_extensions'])) {
          throw new BadRequestException(__("The file type is not valid or not supported"));
        }

        // prepare file name & path
        $prefix = $system['uploads_prefix'] . '_' . get_hash_token();
        $file_name = $directory . $prefix . '.' . $extension;
        $path = ABSPATH . $system['uploads_directory'] . '/' . $file_name;

        // upload to
        if ($system['s3_enabled']) {
          /* Amazon S3 */
          aws_s3_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['google_cloud_enabled']) {
          /* Google Cloud */
          google_cloud_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['digitalocean_enabled']) {
          /* DigitalOcean */
          digitalocean_space_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['wasabi_enabled']) {
          /* Wasabi */
          wasabi_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['backblaze_enabled']) {
          /* Backblaze */
          backblaze_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
        } elseif ($system['ftp_enabled']) {
          /* FTP */
          ftp_upload($_FILES['file']['tmp_name'], $file_name);
        } else {
          /* local server */
          /* set uploads directory */
          if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder)) {
            @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder, 0777, true);
          }
          if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'))) {
            @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'), 0777, true);
          }
          if (!file_exists($system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'))) {
            @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'), 0777, true);
          }
          /* check if the file uploaded successfully */
          if (!@move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
            throw new Exception(__("Sorry, can not upload the file"));
          }
        }

        // return the file new name & exit
        return $file_name;
      }
      break;

    case 'audio':
      // check audios upload permission
      if (!$user->_data['can_upload_audios']) {
        throw new AuthorizationException(__("You don't have the permission to do this"));
      }

      // get allowed file size
      $max_allowed_size = $system['max_audio_size'] * 1024;

      // prepare uploads directory
      $folder = 'sounds';
      $directory = $folder . '/' . date('Y') . '/' . date('m') . '/';

      // valid inputs
      if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
        throw new Exception(__("Something wrong with upload! Is 'upload_max_filesize' set correctly?"));
      }

      // check file size
      if (!$user->_is_admin && !$user->_is_moderator) {
        if ($_FILES["file"]["size"] > $max_allowed_size) {
          throw new ValidationException(__("The file size is so big") . ", " . __("The allowed file size is:") . " " . ($max_allowed_size / 1024 / 1024) . __("MB"));
        }
      }

      // check file extesnion
      $extension = get_extension($_FILES['file']['name']);
      if (!valid_extension($extension, $system['audio_extensions'])) {
        throw new BadRequestException(__("The file type is not valid or not supported"));
      }

      // prepare file name & path
      $prefix = $system['uploads_prefix'] . '_' . get_hash_token();
      $file_name = $directory . $prefix . '.' . $extension;
      $path = ABSPATH . $system['uploads_directory'] . '/' . $file_name;

      // upload to
      if ($system['s3_enabled']) {
        /* Amazon S3 */
        aws_s3_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['google_cloud_enabled']) {
        /* Google Cloud */
        google_cloud_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['digitalocean_enabled']) {
        /* DigitalOcean */
        digitalocean_space_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['wasabi_enabled']) {
        /* Wasabi */
        wasabi_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['backblaze_enabled']) {
        /* Backblaze */
        backblaze_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['ftp_enabled']) {
        /* FTP */
        ftp_upload($_FILES['file']['tmp_name'], $file_name);
      } else {
        /* local server */
        /* set uploads directory */
        if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder)) {
          @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder, 0777, true);
        }
        if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'))) {
          @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'), 0777, true);
        }
        if (!file_exists($system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'))) {
          @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'), 0777, true);
        }
        /* check if the file uploaded successfully */
        if (!@move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
          throw new Exception(__("Sorry, can not upload the file"));
        }
      }

      // return the file new name & exit
      return $file_name;
      break;

    case 'file':
      // check files upload permission
      if (!$user->_data['can_upload_files']) {
        modal("ERROR", __("Not Allowed"), __("You don't have the permission to do this"));
      }

      // get allowed file size
      $max_allowed_size = $system['max_file_size'] * 1024;

      // prepare uploads directory
      $folder = 'files';
      $directory = $folder . '/' . date('Y') . '/' . date('m') . '/';

      // valid inputs
      if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
        throw new Exception(__("Something wrong with upload! Is 'upload_max_filesize' set correctly?"));
      }

      // check file size
      if (!$user->_is_admin && !$user->_is_moderator) {
        if ($_FILES["file"]["size"] > $max_allowed_size) {
          throw new ValidationException(__("The file size is so big") . ", " . __("The allowed file size is:") . " " . ($max_allowed_size / 1024 / 1024) . __("MB"));
        }
      }

      // check file extesnion
      $extension = get_extension($_FILES['file']['name']);
      if (!valid_extension($extension, $system['file_extensions'])) {
        throw new BadRequestException(__("The file type is not valid or not supported"));
      }

      // prepare file name & path
      $prefix = $system['uploads_prefix'] . '_' . get_hash_token();
      $file_name = $directory . $prefix . '.' . $extension;
      $path = ABSPATH . $system['uploads_directory'] . '/' . $file_name;

      // upload to
      if ($system['s3_enabled']) {
        /* Amazon S3 */
        aws_s3_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['google_cloud_enabled']) {
        /* Google Cloud */
        google_cloud_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['digitalocean_enabled']) {
        /* DigitalOcean */
        digitalocean_space_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['wasabi_enabled']) {
        /* Wasabi */
        wasabi_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['backblaze_enabled']) {
        /* Backblaze */
        backblaze_upload($_FILES['file']['tmp_name'], $file_name, mime_content_type($_FILES['file']['tmp_name']));
      } elseif ($system['ftp_enabled']) {
        /* FTP */
        ftp_upload($_FILES['file']['tmp_name'], $file_name);
      } else {
        /* local server */
        /* set uploads directory */
        if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder)) {
          @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder, 0777, true);
        }
        if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'))) {
          @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'), 0777, true);
        }
        if (!file_exists($system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'))) {
          @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'), 0777, true);
        }
        /* check if the file uploaded successfully */
        if (!@move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
          throw new Exception(__("Sorry, can not upload the file"));
        }
      }

      // return the file new name & exit
      return $file_name;
      break;

    default:
      _error(403);
      break;
  }
}


/**
 * delete_avatar_cover_image
 *
 * @param string $handle
 * @param int $id
 * @return void
 */
function delete_avatar_cover_image($handle, $id = null)
{
  global $db, $user, $system;
  switch ($_POST['handle']) {
    case 'cover-user':
      /* update user cover */
      $db->query(sprintf("UPDATE users SET user_cover = null, user_cover_id = null, user_cover_position = null WHERE user_id = %s", secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'picture-user':
      /* update user picture */
      $db->query(sprintf("UPDATE users SET user_picture = null, user_picture_id = null WHERE user_id = %s", secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return get_picture('', $user->_data['user_gender']);
      break;

    case 'cover-page':
      /* check if page id is set */
      if (!isset($id) || !is_numeric($id)) {
        throw new BadRequestException(__("Invalid ID"));
      }
      /* check the page */
      $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_page->num_rows == 0) {
        throw new NoDataException(__("Page not found"));
      }
      $page = $get_page->fetch_assoc();
      /* check if the user is the page admin */
      if (!$user->check_page_adminship($user->_data['user_id'], $page['page_id'])) {
        throw new AuthorizationException(__("You don't have the right permission to do this"));
      }
      /* update page cover */
      $db->query(sprintf("UPDATE pages SET page_cover = null, page_cover_id = null, page_cover_position = null WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'picture-page':
      /* check if page id is set */
      if (!isset($id) || !is_numeric($id)) {
        throw new BadRequestException(__("Invalid ID"));
      }
      /* check the page */
      $get_page = $db->query(sprintf("SELECT * FROM pages WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_page->num_rows == 0) {
        throw new NoDataException(__("Page not found"));
      }
      $page = $get_page->fetch_assoc();
      /* check if the user is the page admin */
      if (!$user->check_page_adminship($user->_data['user_id'], $page['page_id'])) {
        throw new AuthorizationException(__("You don't have the right permission to do this"));
      }
      /* update page picture */
      $db->query(sprintf("UPDATE pages SET page_picture = null, page_picture_id = null WHERE page_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return get_picture('', 'page');
      break;

    case 'cover-group':
      /* check if group id is set */
      if (!isset($id) || !is_numeric($id)) {
        throw new BadRequestException(__("Invalid ID"));
      }
      /* check the group */
      $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_group->num_rows == 0) {
        throw new NoDataException(__("Group not found"));
      }
      $group = $get_group->fetch_assoc();
      /* check if the user is the group admin */
      if (!$user->check_group_adminship($user->_data['user_id'], $group['group_id'])) {
        throw new AuthorizationException(__("You don't have the right permission to do this"));
      }
      /* update group cover */
      $db->query(sprintf("UPDATE `groups` SET group_cover = null, group_cover_id = null, group_cover_position = null WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    case 'picture-group':
      /* check if group id is set */
      if (!isset($id) || !is_numeric($id)) {
        throw new BadRequestException(__("Invalid ID"));
      }
      /* check the group */
      $get_group = $db->query(sprintf("SELECT * FROM `groups` WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_group->num_rows == 0) {
        throw new NoDataException(__("Group not found"));
      }
      $group = $get_group->fetch_assoc();
      /* check if the user is the group admin */
      if (!$user->check_group_adminship($user->_data['user_id'], $group['group_id'])) {
        throw new AuthorizationException(__("You don't have the right permission to do this"));
      }
      /* update group picture */
      $db->query(sprintf("UPDATE `groups` SET group_picture = null, group_picture_id = null WHERE group_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* return */
      return get_picture('', 'group');
      break;

    case 'cover-event':
      /* check if event id is set */
      if (!isset($id) || !is_numeric($id)) {
        throw new BadRequestException(__("Invalid ID"));
      }
      /* check the event */
      $get_event = $db->query(sprintf("SELECT * FROM `events` WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      if ($get_event->num_rows == 0) {
        throw new NoDataException(__("Event not found"));
      }
      $event = $get_event->fetch_assoc();
      /* check if the user is the event admin */
      if (!$user->check_event_adminship($user->_data['user_id'], $event['event_id'])) {
        throw new AuthorizationException(__("You don't have the right permission to do this"));
      }
      /* update event cover */
      $db->query(sprintf("UPDATE `events` SET event_cover = null, event_cover_id = null, event_cover_position = null WHERE event_id = %s", secure($id, 'int'))) or _error('SQL_ERROR_THROWEN');
      break;

    default:
      throw new BadRequestException(__("Invalid request"));
      break;
  }
}



/* ------------------------------- */
/* Cloud Storage */
/* ------------------------------- */

/**
 * aws_s3_test
 *
 * @param string $s3_bucket
 * @param string $s3_region
 * @param string $s3_key
 * @param string $s3_secret
 *
 * @return void
 */
function aws_s3_test($s3_bucket, $s3_region, $s3_key, $s3_secret)
{
  try {
    $s3Client = Aws\S3\S3Client::factory([
      'version'    => 'latest',
      'region'      => $s3_region,
      'credentials' => [
        'key'    => $s3_key,
        'secret' => $s3_secret,
      ]
    ]);
    $buckets = $s3Client->listBuckets();
    if (empty($buckets)) {
      throw new Exception(__("There is no buckets in your account"));
    }
    if (!$s3Client->doesBucketExist($s3_bucket)) {
      throw new Exception(__("There is no bucket with this name in your account"));
    }
  } catch (Exception $e) {
    if (DEBUGGING) {
      throw new Exception($e->getMessage());
    } else {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  }
}


/**
 * aws_s3_upload
 *
 * @param string $file_source
 * @param string $file_name
 * @param string $content_type
 * @return void
 */
function aws_s3_upload($file_source, $file_name, $content_type = "")
{
  global $system;
  $s3Client = Aws\S3\S3Client::factory([
    'version'     => 'latest',
    'region'      => $system['s3_region'],
    'credentials' => [
      'key'     => $system['s3_key'],
      'secret'  => $system['s3_secret'],
    ]
  ]);
  $Key = 'uploads/' . $file_name;
  $s3Client->putObject([
    'Bucket' => $system['s3_bucket'],
    'Key'    => $Key,
    'Body'   => fopen($file_source, 'r+'),
    'ContentDisposition' => 'inline',
    'ContentType' => $content_type,
    'ACL'    => 'public-read',
  ]);
  /* remove local file */
  gc_collect_cycles();
  if ($s3Client->doesObjectExist($system['s3_bucket'], $Key)) {
    unlink($file_source);
  }
}


/**
 * google_cloud_test
 *
 * @return void
 */
function google_cloud_test()
{
  global $system;
  try {
    $storage = new Google\Cloud\Storage\StorageClient([
      'keyFile' => json_decode(html_entity_decode($system['google_cloud_file'], ENT_QUOTES), true),
    ]);
    $bucket = $storage->bucket($system['google_cloud_bucket']);
    if (!$bucket->exists()) {
      throw new Exception(__("There is no buckets in your account"));
    }
  } catch (Exception $e) {
    if (DEBUGGING) {
      throw new Exception($e->getMessage());
    } else {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  }
}


/**
 * google_cloud_upload
 *
 * @param string $file_source
 * @param string $file_name
 * @param string $content_type
 * @return void
 */
function google_cloud_upload($file_source, $file_name, $content_type = "")
{
  global $system;
  $storage = new Google\Cloud\Storage\StorageClient([
    'keyFile' => json_decode(html_entity_decode($system['google_cloud_file'], ENT_QUOTES), true),
  ]);
  $bucket = $storage->bucket($system['google_cloud_bucket']);
  $fileContent = file_get_contents($file_source);
  $Key = 'uploads/' . $file_name;
  $storageObject = $bucket->upload($fileContent, ['name' => $Key]);
  /* remove local file */
  gc_collect_cycles();
  if (!empty($storageObject)) {
    unlink($file_source);
  }
}


/**
 * digitalocean_space_test
 *
 * @return void
 */
function digitalocean_space_test()
{
  global $system;
  try {
    $s3Client = Aws\S3\S3Client::factory([
      'version'     => 'latest',
      'endpoint'    => 'https://' . $system['digitalocean_space_region'] . '.digitaloceanspaces.com',
      'region'      => $system['digitalocean_space_region'],
      'credentials' => [
        'key'     => $system['digitalocean_key'],
        'secret'  => $system['digitalocean_secret'],
      ]
    ]);
    $buckets = $s3Client->listBuckets();
    if (empty($buckets)) {
      throw new Exception(__("There is no buckets in your account"));
    }
    if (!$s3Client->doesBucketExist($system['digitalocean_space_name'])) {
      throw new Exception(__("There is no bucket with this name in your account"));
    }
  } catch (Exception $e) {
    if (DEBUGGING) {
      throw new Exception($e->getMessage());
    } else {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  }
}


/**
 * digitalocean_space_upload
 *
 * @param string $file_source
 * @param string $file_name
 * @param string $content_type
 * @return void
 */
function digitalocean_space_upload($file_source, $file_name, $content_type = "")
{
  global $system;
  $s3Client = Aws\S3\S3Client::factory([
    'version'     => 'latest',
    'endpoint'    => 'https://' . $system['digitalocean_space_region'] . '.digitaloceanspaces.com',
    'region'      => $system['digitalocean_space_region'],
    'credentials' => [
      'key'     => $system['digitalocean_key'],
      'secret'  => $system['digitalocean_secret'],
    ]
  ]);
  $Key = 'uploads/' . $file_name;
  $s3Client->putObject([
    'Bucket' => $system['digitalocean_space_name'],
    'Key'    => $Key,
    'Body'   => fopen($file_source, 'r+'),
    'ContentDisposition' => 'inline',
    'ContentType' => $content_type,
    'ACL'    => 'public-read',
  ]);
  /* remove local file */
  gc_collect_cycles();
  if ($s3Client->doesObjectExist($system['digitalocean_space_name'], $Key)) {
    unlink($file_source);
  }
}


/**
 * wasabi_test
 *
 * @return void
 */
function wasabi_test()
{
  global $system;
  try {
    $s3Client = Aws\S3\S3Client::factory([
      'version'     => 'latest',
      'endpoint'    => 'https://s3.' . $system['wasabi_region'] . '.wasabisys.com',
      'region'      => $system['wasabi_region'],
      'credentials' => [
        'key'     => $system['wasabi_key'],
        'secret'  => $system['wasabi_secret'],
      ]
    ]);
    $buckets = $s3Client->listBuckets();
    if (empty($buckets)) {
      throw new Exception(__("There is no buckets in your account"));
    }
    if (!$s3Client->doesBucketExist($system['wasabi_bucket'])) {
      throw new Exception(__("There is no bucket with this name in your account"));
    }
  } catch (Exception $e) {
    if (DEBUGGING) {
      throw new Exception($e->getMessage());
    } else {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  }
}


/**
 * wasabi_upload
 *
 * @param string $file_source
 * @param string $file_name
 * @param string $content_type
 * @return void
 */
function wasabi_upload($file_source, $file_name, $content_type = "")
{
  global $system;
  $s3Client = Aws\S3\S3Client::factory([
    'version'     => 'latest',
    'endpoint'    => 'https://s3.' . $system['wasabi_region'] . '.wasabisys.com',
    'region'      => $system['wasabi_region'],
    'credentials' => [
      'key'     => $system['wasabi_key'],
      'secret'  => $system['wasabi_secret'],
    ]
  ]);
  $Key = 'uploads/' . $file_name;
  $s3Client->putObject([
    'Bucket' => $system['wasabi_bucket'],
    'Key'    => $Key,
    'Body'   => fopen($file_source, 'r+'),
    'ContentDisposition' => 'inline',
    'ContentType' => $content_type,
    'ACL'    => 'public-read',
  ]);
  /* remove local file */
  gc_collect_cycles();
  if ($s3Client->doesObjectExist($system['wasabi_bucket'], $Key)) {
    unlink($file_source);
  }
}


/**
 * backblaze_test
 *
 * @return void
 */
function backblaze_test()
{
  global $system;
  try {
    $s3Client = Aws\S3\S3Client::factory([
      'version'     => 'latest',
      'endpoint'    => 'https://s3.' . $system['backblaze_region'] . '.backblazeb2.com',
      'region'      => $system['backblaze_region'],
      'credentials' => [
        'key'     => $system['backblaze_key'],
        'secret'  => $system['backblaze_secret'],
      ]
    ]);
    $buckets = $s3Client->listBuckets();
    if (empty($buckets)) {
      throw new Exception(__("There is no buckets in your account"));
    }
    if (!$s3Client->doesBucketExist($system['backblaze_bucket'])) {
      throw new Exception(__("There is no bucket with this name in your account"));
    }
  } catch (Exception $e) {
    if (DEBUGGING) {
      throw new Exception($e->getMessage());
    } else {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  }
}


/**
 * backblaze_upload
 *
 * @param string $file_source
 * @param string $file_name
 * @param string $content_type
 * @return void
 */
function backblaze_upload($file_source, $file_name, $content_type = "")
{
  global $system;
  $s3Client = Aws\S3\S3Client::factory([
    'version'     => 'latest',
    'endpoint'    => 'https://s3.' . $system['backblaze_region'] . '.backblazeb2.com',
    'region'      => $system['backblaze_region'],
    'credentials' => [
      'key'     => $system['backblaze_key'],
      'secret'  => $system['backblaze_secret'],
    ]
  ]);
  $Key = 'uploads/' . $file_name;
  $s3Client->putObject([
    'Bucket' => $system['backblaze_bucket'],
    'Key'    => $Key,
    'Body'   => fopen($file_source, 'r+'),
    'ContentDisposition' => 'inline',
    'ContentType' => $content_type,
    'ACL'    => 'public-read',
  ]);
  /* remove local file */
  gc_collect_cycles();
  if ($s3Client->doesObjectExist($system['backblaze_bucket'], $Key)) {
    unlink($file_source);
  }
}


/**
 * ftp_test
 *
 * @return void
 */
function ftp_test()
{
  global $system;
  try {
    $ftp = new \FtpClient\FtpClient();
    $ftp->connect($system['ftp_hostname'], false, $system['ftp_port']);
    $ftp->login($system['ftp_username'], $system['ftp_password']);
  } catch (Exception $e) {
    if (DEBUGGING) {
      throw new Exception($e->getMessage());
    } else {
      throw new Exception(__("Connection Failed, Please check your settings"));
    }
  }
}


/**
 * ftp_upload
 *
 * @param string $file_source
 * @param string $file_name
 * @return void
 */
function ftp_upload($file_source, $file_name)
{
  global $system;
  $ftp = new \FtpClient\FtpClient();
  $ftp->connect($system['ftp_hostname'], false, $system['ftp_port']);
  $ftp->login($system['ftp_username'], $system['ftp_password']);
  if (!empty($system['ftp_path']) && $system['ftp_path'] != "./") {
    $ftp->chdir($system['ftp_path']);
  }
  $file_path = substr($file_name, 0, strrpos($file_name, '/'));
  $ftp_path_info = explode('/', $file_path);
  $ftp_path = '';
  if (!$ftp->isDir($file_path)) {
    foreach ($ftp_path_info as $key => $value) {
      if (!empty($ftp_path)) {
        $ftp_path .= '/' . $value . '/';
      } else {
        $ftp_path .= $value . '/';
      }
      if (!$ftp->isDir($ftp_path)) {
        $mkdir = $ftp->mkdir($ftp_path);
      }
    }
  }
  $ftp->chdir($file_path);
  $ftp->pasv(true);
  if ($ftp->putFromPath($file_source, $file_name)) {
    unlink($file_source);
  }
  $ftp->close();
}


/**
 * delete_uploads_file
 *
 * @param string $file_name
 * @return void
 */
function delete_uploads_file($file_name)
{
  global $system;
  if (!$file_name) {
    return;
  }
  if ($system['s3_enabled']) {
    /* Amazon S3 */
    $s3Client = Aws\S3\S3Client::factory([
      'version'    => 'latest',
      'region'      => $system['s3_region'],
      'credentials' => [
        'key'    => $system['s3_key'],
        'secret' => $system['s3_secret'],
      ]
    ]);
    $Key = 'uploads/' . $file_name;
    if ($s3Client->doesObjectExist($system['s3_bucket'], $Key)) {
      $s3Client->deleteObject([
        'Bucket' => $system['s3_bucket'],
        'Key'    => $Key,
      ]);
    }
  } elseif ($system['google_cloud_enabled']) {
    /* Google Cloud */
    $storage = new Google\Cloud\Storage\StorageClient([
      'keyFile' => json_decode(html_entity_decode($system['google_cloud_file'], ENT_QUOTES), true),
    ]);
    $bucket = $storage->bucket($system['google_cloud_bucket']);
    $Key = 'uploads/' . $file_name;
    $object = $bucket->object($Key);
    if ($object->exists()) {
      $object->delete();
    }
  } elseif ($system['digitalocean_enabled']) {
    /* DigitalOcean */
    $s3Client = Aws\S3\S3Client::factory([
      'version'     => 'latest',
      'endpoint'    => 'https://' . $system['digitalocean_space_region'] . '.digitaloceanspaces.com',
      'region'      => $system['digitalocean_space_region'],
      'credentials' => [
        'key'     => $system['digitalocean_key'],
        'secret'  => $system['digitalocean_secret'],
      ]
    ]);
    $Key = 'uploads/' . $file_name;
    if ($s3Client->doesObjectExist($system['digitalocean_space_name'], $Key)) {
      $s3Client->deleteObject([
        'Bucket' => $system['digitalocean_space_name'],
        'Key'    => $Key,
      ]);
    }
  } elseif ($system['wasabi_enabled']) {
    /* Wasabi */
    $s3Client = Aws\S3\S3Client::factory([
      'version'     => 'latest',
      'endpoint'    => 'https://s3.' . $system['wasabi_region'] . '.wasabisys.com',
      'region'      => $system['wasabi_region'],
      'credentials' => [
        'key'     => $system['wasabi_key'],
        'secret'  => $system['wasabi_secret'],
      ]
    ]);
    $Key = 'uploads/' . $file_name;
    if ($s3Client->doesObjectExist($system['wasabi_bucket'], $Key)) {
      $s3Client->deleteObject([
        'Bucket' => $system['wasabi_bucket'],
        'Key'    => $Key,
      ]);
    }
  } elseif ($system['backblaze_enabled']) {
    /* Backblaze */
    $s3Client = Aws\S3\S3Client::factory([
      'version'     => 'latest',
      'endpoint'    => 'https://s3.' . $system['backblaze_region'] . '.backblazeb2.com',
      'region'      => $system['backblaze_region'],
      'credentials' => [
        'key'     => $system['backblaze_key'],
        'secret'  => $system['backblaze_secret'],
      ]
    ]);
    $Key = 'uploads/' . $file_name;
    if ($s3Client->doesObjectExist($system['backblaze_bucket'], $Key)) {
      $s3Client->deleteObject([
        'Bucket' => $system['backblaze_bucket'],
        'Key'    => $Key,
        'DeleteMarker' => 'true'
      ]);
    }
  } elseif ($system['ftp_enabled']) {
    /* FTP */
    $ftp = new \FtpClient\FtpClient();
    $ftp->connect($system['ftp_hostname'], false, $system['ftp_port']);
    $ftp->login($system['ftp_username'], $system['ftp_password']);
    if (!empty($system['ftp_path']) && $system['ftp_path'] != "./") {
      $ftp->chdir($system['ftp_path']);
    }
    $file_path = substr($file_name, 0, strrpos($file_name, '/'));
    $file_name = substr($file_name, strrpos($file_name, '/') + 1);
    if (!$ftp->isDir($file_path)) {
      return;
    }
    $ftp->chdir($file_path);
    $ftp->pasv(true);
    $ftp->remove($file_name);
    $ftp->close();
  } else {
    /* local server */
    $realpath = realpath(ABSPATH . $system['uploads_directory'] . '/' . $file_name);
    if (is_file($realpath) && file_exists($realpath)) {
      unlink($realpath);
    }
  }
}


/**
 * save_file_to_cloud
 *
 * @param string $path
 * @param string $file_name
 * @return void
 */

function save_file_to_cloud($path, $file_name)
{
  global $system;
  /* Cloud Storage */
  if ($system['s3_enabled']) {
    /* Amazon S3 */
    aws_s3_upload($path, $file_name);
  } elseif ($system['google_cloud_enabled']) {
    /* Google Cloud */
    google_cloud_upload($path, $file_name);
  } elseif ($system['digitalocean_enabled']) {
    /* DigitalOcean */
    digitalocean_space_upload($path, $file_name);
  } elseif ($system['wasabi_enabled']) {
    /* Wasabi */
    wasabi_upload($path, $file_name);
  } elseif ($system['backblaze_enabled']) {
    /* Backblaze */
    backblaze_upload($path, $file_name);
  } elseif ($system['ftp_enabled']) {
    /* FTP */
    ftp_upload($path, $file_name);
  }
}



/* ------------------------------- */
/* FFMPEG */
/* ------------------------------- */

/**
 * ffmpeg_test
 *
 * @return string
 */

function ffmpeg_test()
{
  global $system;
  /* check ffmpeg settings */
  if (!function_exists('shell_exec')) {
    throw new Exception(__("shell_exec function must be enabled for FFMPEG"));
  }
  if (!$system['ffmpeg_enabled']) {
    throw new Exception(__("FFMPEG must be enable before testing"));
  }
  if ($system['ffmpeg_path'] == "") {
    throw new Exception(__("FFMPEG path must be defined before testing"));
  }
  /* prepare */
  $input_video = ABSPATH . "includes/assets/videos/ffmpeg_test.mp4";
  $output_video = ABSPATH . "includes/assets/videos/ffmpeg_test_240.mp4";
  @unlink($output_video);
  $shell_response = shell_exec($system['ffmpeg_path'] . " -y -i $input_video -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=426:-2 -crf 26 $output_video 2>&1");
  if (!file_exists($output_video)) {
    throw new Exception(__("FFMPEG Error") . ": " . $shell_response);
  }
  return $shell_response;
}

/**
 * ffmpeg_convert
 *
 * @param integer $post_id
 * @param integer $post_author_id
 * @param string $video_name
 * @param string $thumbnail
 * @return void
 */

function ffmpeg_convert($post_id, $post_author_id, $video_name, $thumbnail = '')
{
  global $system, $db, $user;
  /* check ffmpeg settings */
  if (!function_exists('shell_exec')) {
    throw new Exception(__("shell_exec function must be enabled for FFMPEG"));
  }
  if (!$system['ffmpeg_enabled']) {
    throw new Exception(__("FFMPEG must be enable before testing"));
  }
  if ($system['ffmpeg_path'] == "") {
    throw new Exception(__("FFMPEG path must be defined before testing"));
  }
  /* prepare uploads folder as staging */
  $photos_folder = 'photos';
  $photos_directory = $photos_folder . '/' . date('Y') . '/' . date('m') . '/';
  if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $photos_folder)) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $photos_folder, 0777, true);
  }
  if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $photos_folder . '/' . date('Y'))) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $photos_folder . '/' . date('Y'), 0777, true);
  }
  if (!file_exists($system['uploads_directory'] . '/' . $photos_folder . '/' . date('Y') . '/' . date('m'))) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $photos_folder . '/' . date('Y') . '/' . date('m'), 0777, true);
  }
  $videos_folder = 'videos';
  $videos_directory = $videos_folder . '/' . date('Y') . '/' . date('m') . '/';
  if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $videos_folder)) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $videos_folder, 0777, true);
  }
  if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $videos_folder . '/' . date('Y'))) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $videos_folder . '/' . date('Y'), 0777, true);
  }
  if (!file_exists($system['uploads_directory'] . '/' . $videos_folder . '/' . date('Y') . '/' . date('m'))) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $videos_folder . '/' . date('Y') . '/' . date('m'), 0777, true);
  }
  /* save original video */
  $original_video_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_name;
  $delete_fetched_file = false;
  if (!file_exists($original_video_local_path)) {
    $delete_fetched_file = true;
    $remote_file = fopen($system['system_uploads'] . '/' . $video_name, 'rb');
    $local_file = fopen($original_video_local_path, 'wb');
    if ($remote_file && $local_file) {
      while (!feof($remote_file)) {
        fwrite($local_file, fread($remote_file, 8192));
      }
      fclose($remote_file);
      fclose($local_file);
    } else {
      /* update post & return */
      $db->query(sprintf("UPDATE posts SET processing = '0' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      return;
    }
  }
  /* set ffprobe_path */
  $system['ffprobe_path'] = str_replace('ffmpeg', 'ffprobe', $system['ffmpeg_path']);
  /* get original video resolution */
  $resolution = 0;
  $resolution = shell_exec($system['ffprobe_path'] . " -v error -select_streams v:0 -show_entries stream=width -of csv=s=x:p=0 " . $original_video_local_path);
  /* get original video duration */
  $duration = 1;
  $duration = shell_exec($system['ffprobe_path'] . " -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . $original_video_local_path);
  /* generate new video thumbnail */
  if (!$thumbnail) {
    $thumbnail_prefix = $system['uploads_prefix'] . '_' . get_hash_token();
    $thumbnail_name = $photos_directory . $thumbnail_prefix . '.jpeg';
    $thumbnail_path = ABSPATH . $system['uploads_directory'] . '/' . $thumbnail_name;
    $extraction_time = ($duration > 1) ? ($duration / 2) : 1;
    if (!file_exists($thumbnail_path)) {
      shell_exec($system['ffmpeg_path'] . " -ss \"$extraction_time\" -i $original_video_local_path -vframes 1 -f mjpeg $thumbnail_path 2<&1");
      /* save the new video thumbnail to cloud */
      save_file_to_cloud($thumbnail_path, $thumbnail_name);
      /* update video thumbnail */
      $db->query(sprintf("UPDATE posts_videos SET thumbnail = %s WHERE post_id = %s", secure($thumbnail_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
    }
  }
  /* get original hash */
  $original_hash = extarct_hash_token($video_name);
  /* set video prefix */
  $video_prefix = $system['uploads_prefix'] . '_' . $original_hash;
  /* check watermark videos enabled */
  $watermark_cmd = "";
  if ($system['watermark_videos_enabled']) {
    /* get water mark settings */
    $watermark_videos_icon = ABSPATH . $system['uploads_directory'] . '/' . $system['watermark_videos_icon'];
    switch ($system['watermark_videos_position']) {
      case 'top_left':
        $watermark_videos_position = sprintf("%s:%s", $system['watermark_videos_xoffset'], $system['watermark_videos_yoffset']);
        break;

      case 'top_right':
        $watermark_videos_position = sprintf("main_w-overlay_w-%s:%s", $system['watermark_videos_xoffset'], $system['watermark_videos_yoffset']);
        break;

      case 'bottom_left':
        $watermark_videos_position = sprintf("%s:main_h-overlay_h-%s", $system['watermark_videos_xoffset'], $system['watermark_videos_yoffset']);
        break;

      case 'bottom_right':
        $watermark_videos_position = sprintf("main_w-overlay_w-%s:main_h-overlay_h-%s", $system['watermark_videos_xoffset'], $system['watermark_videos_yoffset']);
        break;

      case 'center':
        $watermark_videos_position = sprintf("(main_w-overlay_w)/2:(main_h-overlay_h)/2");
        break;
    }
    $watermark_cmd = sprintf("-i %s -i %s -filter_complex \"[1:v]scale=iw:ih [watermark]; [watermark]format=argb,colorchannelmixer=aa=%s [watermark]; [0:v][watermark] overlay=%s\" -c:a copy", $watermark_videos_icon, $original_video_local_path, $system['watermark_videos_opacity'], $watermark_videos_position);
  }
  /* convert video to 240p */
  $video_240p_name = $videos_directory . $video_prefix . '_240p.mp4';
  $video_240_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_240p_name;
  if ($system['ffmpeg_240p_enabled'] && !file_exists($video_240_local_path) && $resolution >= 426) {
    shell_exec($system['ffmpeg_path'] . " -y -i $original_video_local_path -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=426:-2 -crf 26 $video_240_local_path 2<&1");
    if ($watermark_cmd) {
      $video_240p_marked_name = $videos_directory . $video_prefix . '_marked_240p.mp4';
      $video_240_marked_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_240p_marked_name;
      shell_exec($system['ffmpeg_path'] . " -y -i $video_240_local_path $watermark_cmd $video_240_marked_local_path 2<&1");
      /* save the new video to cloud */
      save_file_to_cloud($video_240_marked_local_path, $video_240p_marked_name);
      /* update video */
      $db->query(sprintf("UPDATE posts_videos SET source_240p = %s WHERE post_id = %s", secure($video_240p_marked_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove local file */
      unlink($video_240_local_path);
      $video_240p_name = $video_240p_marked_name;
      $video_240_local_path = $video_240_marked_local_path;
    }
    /* save the new video to cloud */
    save_file_to_cloud($video_240_local_path, $video_240p_name);
    /* update video */
    $db->query(sprintf("UPDATE posts_videos SET source_240p = %s WHERE post_id = %s", secure($video_240p_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }
  /* convert video to 360p */
  $video_360p_name = $videos_directory . $video_prefix . '_360p.mp4';
  $video_360_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_360p_name;
  if ($system['ffmpeg_360p_enabled'] && !file_exists($video_360_local_path) && $resolution >= 640) {
    shell_exec($system['ffmpeg_path'] . " -y -i $original_video_local_path -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=640:-2 -crf 26 $video_360_local_path 2<&1");
    if ($watermark_cmd) {
      $video_360p_marked_name = $videos_directory . $video_prefix . '_marked_360p.mp4';
      $video_360_marked_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_360p_marked_name;
      shell_exec($system['ffmpeg_path'] . " -y -i $video_360_local_path $watermark_cmd $video_360_marked_local_path 2<&1");
      /* save the new video to cloud */
      save_file_to_cloud($video_360_marked_local_path, $video_360p_marked_name);
      /* update video */
      $db->query(sprintf("UPDATE posts_videos SET source_360p = %s WHERE post_id = %s", secure($video_360p_marked_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove local file */
      unlink($video_360_local_path);
      $video_360p_name = $video_360p_marked_name;
      $video_360_local_path = $video_360_marked_local_path;
    }
    /* save the new video to cloud */
    save_file_to_cloud($video_360_local_path, $video_360p_name);
    /* update video */
    $db->query(sprintf("UPDATE posts_videos SET source_360p = %s WHERE post_id = %s", secure($video_360p_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }
  /* convert video to 480p */
  $video_480p_name = $videos_directory . $video_prefix . '_480p.mp4';
  $video_480_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_480p_name;
  if ($system['ffmpeg_480p_enabled'] && !file_exists($video_480_local_path) && $resolution >= 854) {
    shell_exec($system['ffmpeg_path'] . " -y -i $original_video_local_path -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=854:-2 -crf 26 $video_480_local_path 2<&1");
    if ($watermark_cmd) {
      $video_480p_marked_name = $videos_directory . $video_prefix . '_marked_480p.mp4';
      $video_480_marked_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_480p_marked_name;
      shell_exec($system['ffmpeg_path'] . " -y -i $video_480_local_path $watermark_cmd $video_480_marked_local_path 2<&1");
      /* save the new video to cloud */
      save_file_to_cloud($video_480_marked_local_path, $video_480p_marked_name);
      /* update video */
      $db->query(sprintf("UPDATE posts_videos SET source_480p = %s WHERE post_id = %s", secure($video_480p_marked_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove local file */
      unlink($video_480_local_path);
      $video_480p_name = $video_480p_marked_name;
      $video_480_local_path = $video_480_marked_local_path;
    }
    /* save the new video to cloud */
    save_file_to_cloud($video_480_local_path, $video_480p_name);
    /* update video */
    $db->query(sprintf("UPDATE posts_videos SET source_480p = %s WHERE post_id = %s", secure($video_480p_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }
  /* convert video to 720p */
  $video_720p_name = $videos_directory . $video_prefix . '_720p.mp4';
  $video_720_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_720p_name;
  if ($system['ffmpeg_720p_enabled'] && !file_exists($video_720_local_path) && $resolution >= 1280) {
    shell_exec($system['ffmpeg_path'] . " -y -i $original_video_local_path -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=1280:-2 -crf 26 $video_720_local_path 2<&1");
    if ($watermark_cmd) {
      $video_720p_marked_name = $videos_directory . $video_prefix . '_marked_720p.mp4';
      $video_720_marked_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_720p_marked_name;
      shell_exec($system['ffmpeg_path'] . " -y -i $video_720_local_path $watermark_cmd $video_720_marked_local_path 2<&1");
      /* save the new video to cloud */
      save_file_to_cloud($video_720_marked_local_path, $video_720p_marked_name);
      /* update video */
      $db->query(sprintf("UPDATE posts_videos SET source_720p = %s WHERE post_id = %s", secure($video_720p_marked_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove local file */
      unlink($video_720_local_path);
      $video_720p_name = $video_720p_marked_name;
      $video_720_local_path = $video_720_marked_local_path;
    }
    /* save the new video to cloud */
    save_file_to_cloud($video_720_local_path, $video_720p_name);
    /* update video */
    $db->query(sprintf("UPDATE posts_videos SET source_720p = %s WHERE post_id = %s", secure($video_720p_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }
  /* convert video to 1080p */
  $video_1080p_name = $videos_directory . $video_prefix . '_1080p.mp4';
  $video_1080_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_1080p_name;
  if ($system['ffmpeg_1080p_enabled'] && !file_exists($video_1080_local_path) && $resolution >= 1920) {
    shell_exec($system['ffmpeg_path'] . " -y -i $original_video_local_path -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=1920:-2 -crf 26 $video_1080_local_path 2<&1");
    if ($watermark_cmd) {
      $video_1080p_marked_name = $videos_directory . $video_prefix . '_marked_1080p.mp4';
      $video_1080_marked_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_1080p_marked_name;
      shell_exec($system['ffmpeg_path'] . " -y -i $video_1080_local_path $watermark_cmd $video_1080_marked_local_path 2<&1");
      /* save the new video to cloud */
      save_file_to_cloud($video_1080_marked_local_path, $video_1080p_marked_name);
      /* update video */
      $db->query(sprintf("UPDATE posts_videos SET source_1080p = %s WHERE post_id = %s", secure($video_1080p_marked_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove local file */
      unlink($video_1080_local_path);
      $video_1080p_name = $video_1080p_marked_name;
      $video_1080_local_path = $video_1080_marked_local_path;
    }
    /* save the new video to cloud */
    save_file_to_cloud($video_1080_local_path, $video_1080p_name);
    /* update video */
    $db->query(sprintf("UPDATE posts_videos SET source_1080p = %s WHERE post_id = %s", secure($video_1080p_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }
  /* convert video to 1440p */
  $video_1440p_name = $videos_directory . $video_prefix . '_1440p.mp4';
  $video_1440_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_1440p_name;
  if ($system['ffmpeg_1440p_enabled'] && !file_exists($video_1440_local_path) && $resolution >= 2560) {
    shell_exec($system['ffmpeg_path'] . " -y -i $original_video_local_path -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=2560:-2 -crf 26 $video_1440_local_path 2<&1");
    if ($watermark_cmd) {
      $video_1440p_marked_name = $videos_directory . $video_prefix . '_marked_1440p.mp4';
      $video_1440_marked_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_1440p_marked_name;
      shell_exec($system['ffmpeg_path'] . " -y -i $video_1440_local_path $watermark_cmd $video_1440_marked_local_path 2<&1");
      /* save the new video to cloud */
      save_file_to_cloud($video_1440_marked_local_path, $video_1440p_marked_name);
      /* update video */
      $db->query(sprintf("UPDATE posts_videos SET source_1440p = %s WHERE post_id = %s", secure($video_1440p_marked_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove local file */
      unlink($video_1440_local_path);
      $video_1440p_name = $video_1440p_marked_name;
      $video_1440_local_path = $video_1440_marked_local_path;
    }
    /* save the new video to cloud */
    save_file_to_cloud($video_1440_local_path, $video_1440p_name);
    /* update video */
    $db->query(sprintf("UPDATE posts_videos SET source_1440p = %s WHERE post_id = %s", secure($video_1440p_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }
  /* convert video to 2160p */
  $video_2160p_name = $videos_directory . $video_prefix . '_2160p.mp4';
  $video_2160_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_2160p_name;
  if ($system['ffmpeg_2160p_enabled'] && !file_exists($video_2160_local_path) && $resolution >= 3840) {
    shell_exec($system['ffmpeg_path'] . " -y -i $original_video_local_path -vcodec libx264 -preset " . $system['ffmpeg_speed'] . " -filter:v scale=3840:-2 -crf 26 $video_2160_local_path 2<&1");
    if ($watermark_cmd) {
      $video_2160p_marked_name = $videos_directory . $video_prefix . '_marked_2160p.mp4';
      $video_2160_marked_local_path = ABSPATH . $system['uploads_directory'] . '/' . $video_2160p_marked_name;
      shell_exec($system['ffmpeg_path'] . " -y -i $video_2160_local_path $watermark_cmd $video_2160_marked_local_path 2<&1");
      /* save the new video to cloud */
      save_file_to_cloud($video_2160_marked_local_path, $video_2160p_marked_name);
      /* update video */
      $db->query(sprintf("UPDATE posts_videos SET source_2160p = %s WHERE post_id = %s", secure($video_2160p_marked_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
      /* remove local file */
      unlink($video_2160_local_path);
      $video_2160p_name = $video_2160p_marked_name;
      $video_2160_local_path = $video_2160_marked_local_path;
    }
    /* save the new video to cloud */
    save_file_to_cloud($video_2160_local_path, $video_2160p_name);
    /* update video */
    $db->query(sprintf("UPDATE posts_videos SET source_2160p = %s WHERE post_id = %s", secure($video_2160p_name), secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  }
  /* remove fetched file (if any) */
  if ($delete_fetched_file) {
    unlink($original_video_local_path);
  }
  /* update post */
  $db->query(sprintf("UPDATE posts SET processing = '0' WHERE post_id = %s", secure($post_id, 'int'))) or _error('SQL_ERROR_THROWEN');
  /* notify post author */
  $user->post_notification(['to_user_id' => $post_author_id, 'action' => 'video_converted', 'node_type' => 'post', 'node_url' => $post_id]);
}


/* ------------------------------- */
/* Payments */
/* ------------------------------- */

/**
 * get_payment_vat_percentage
 *
 * @return int
 */
function get_payment_vat_percentage()
{
  global $system, $user;
  $payment_vat_percentage = $system['payment_vat_percentage'];
  if ($system['payment_country_vat_enabled']) {
    $user_country = $user->get_country($user->_data['user_country']);
    if ($user_country && $user_country['country_vat']) {
      $payment_vat_percentage = $user_country['country_vat'];
    }
  }
  return $payment_vat_percentage;
}


/**
 * get_payment_vat_value
 *
 * @param float $amount
 * @return float
 */
function get_payment_vat_value($amount)
{
  global $system;
  $vat = 0;
  if ($system['payment_vat_enabled']) {
    $payment_vat_percentage = get_payment_vat_percentage();
    $vat = ($amount * $payment_vat_percentage) / 100;
  }
  return $vat;
}


/**
 * get_payment_fees_value
 *
 * @param float $amount
 * @return float
 */
function get_payment_fees_value($amount)
{
  global $system;
  $fees = 0;
  if ($system['payment_fees_enabled']) {
    $fees = ($amount * $system['payment_fees_percentage']) / 100;
  }
  return $fees;
}


/**
 * get_payment_total_value
 *
 * @param float $amount
 * @return float
 */
function get_payment_total_value($amount, $printed = false)
{
  $total = $amount + get_payment_vat_value($amount) + get_payment_fees_value($amount);
  $total = round($total, 2);
  return ($printed) ? print_money($total) : $total;
}



/* ------------------------------- */
/* PayPal */
/* ------------------------------- */

/**
 * paypal_access_token
 *
 * @return string
 */
function paypal_access_token()
{
  global $system;
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/oauth2/token');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERPWD, $system['paypal_id'] . ":" . $system['paypal_secret']);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if ($httpCode != 200) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  return $response->access_token;
}


/**
 * paypal_payment_link
 *
 * @param string $handle
 * @param float $price
 * @param integer $id
 * @return string
 */
function paypal_payment_link($handle, $price, $id = null)
{
  global $system, $user;
  /* prepare */
  $total = get_payment_total_value($price);
  switch ($handle) {
    case 'packages':
      /* get package */
      $package = $user->get_package($id);
      if ($package['paypal_billing_plan']) {
        return paypal_subscription_link($handle, $id, $package['paypal_billing_plan']);
      }
      $product = __($system['system_title']) . " " . __('Package') . " " . __($package['name']);
      $description = __('Pay For') . " " . __($system['system_title']) . " " . __('Package') . " " . __($package['name']);
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=packages&package_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      break;

    case 'subscribe':
      /* get monetization plan */
      $monetization_plan = $user->get_monetization_plan($id, true);
      if (!$monetization_plan) {
        throw new Exception(__("The monetization plan you're trying to subscribe to is not found"));
      }
      if ($monetization_plan['paypal_billing_plan']) {
        return paypal_subscription_link($handle, $id, $monetization_plan['paypal_billing_plan']);
      }
      $product = __($system['system_title']) . " " . __('Monetization Plan') . " " . __($monetization_plan['name']);
      $description = __('Pay For') . " " . __($system['system_title']) . " " . __('Monetization Plan') . " " . __($monetization_plan['name']);
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=subscribe&plan_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      break;

    case 'wallet':
      $product = __($system['system_title']) . " " . __('Wallet');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=wallet";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      $_SESSION['wallet_replenish_amount'] = $price;
      break;

    case 'donate':
      $product = __($system['system_title']) . " " . __('Funding Donation');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=donate&post_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      $_SESSION['donation_amount'] = $price;
      break;

    case 'paid_post':
      $product = __($system['system_title']) . " " . __('Paid Post');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=paid_post&post_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      break;

    case 'movies':
      $product = __($system['system_title']) . " " . __('Movies');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=movies&movie_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      break;

    default:
      _error(400);
      break;
  }
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* create a payment */
  $request_body = [
    'intent' => 'sale',
    'payer' => [
      'payment_method' => 'paypal',
    ],
    'redirect_urls' => [
      'return_url' => $URL['success'],
      'cancel_url' => $URL['cancel'],
    ],
    'transactions' => [
      [
        'item_list' => [
          'items' => [
            [
              'name' => $product,
              'sku' => 'item',
              'price' => $total,
              'currency' => $system['system_currency'],
              'quantity' => 1,
            ],
          ],
        ],
        'amount' => [
          'currency' => $system['system_currency'],
          'total' => $total,
        ],
        'description' => $description,
      ],
    ],
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/payments/payment');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if (!($httpCode >= 200 && $httpCode < 300)) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  return $response->links[1]->href;
}


/**
 * paypal_payment_check
 *
 * @param string $payment_id
 * @param string $payer_id
 * @return string
 */
function paypal_payment_check($payment_id, $payer_id)
{
  global $system;
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* check payment */
  $request_body = [
    'payer_id' => $payer_id,
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/payments/payment/' . $payment_id . '/execute');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if (!($httpCode >= 200 && $httpCode < 300)) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  return $response;
}


/**
 * paypal_subscription_link
 *
 * @param string $handle
 * @param integer $id
 * @param string $billing_plan_id
 * 
 * @return string
 */
function paypal_subscription_link($handle, $id, $billing_plan_id)
{
  global $system, $user;
  /* prepare handle */
  switch ($handle) {
    case 'packages':
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=packages&package_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      break;

    case 'subscribe':
      $URL['success'] = $system['system_url'] . "/webhooks/paypal.php?status=success&handle=subscribe&plan_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/paypal.php?status=cancel";
      break;

    default:
      _error(400);
      break;
  }
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* create a subscription */
  $request_body = [
    'plan_id' => $billing_plan_id,
    'subscriber' => [
      'name' => [
        'given_name' => $user->_data['user_firstname'],
        'surname' => $user->_data['user_lastname'],
      ],
      'email_address' => $user->_data['user_email'],
    ],
    'application_context' => [
      'brand_name' => $system['system_title'],
      'locale' => $system['system_locale'],
      'shipping_preference' => 'NO_SHIPPING',
      'user_action' => 'SUBSCRIBE_NOW',
      'payment_method' => [
        'payer_selected' => 'PAYPAL',
        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
      ],
      'return_url' => $URL['success'],
      'cancel_url' => $URL['cancel'],
    ],
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/billing/subscriptions');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if (!($httpCode >= 200 && $httpCode < 300)) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  return $response->links[0]->href;
}


/**
 * paypal_subscription_check
 *
 * @param string $subscription_id
 * 
 * @return boolean
 */
function paypal_subscription_check($subscription_id)
{
  global $system;
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* check subscription */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/billing/subscriptions/' . $subscription_id);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if (!($httpCode >= 200 && $httpCode < 300)) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  if ($response->status == 'ACTIVE') {
    return true;
  }
  return false;
}


/**
 * paypal_create_billing_plan
 *
 * @param string $name
 * @param string $description
 * @param string $frequency
 * @param string $interval
 * @param string $amount
 * @return string
 */
function paypal_create_billing_plan($name, $description, $frequency, $interval, $amount)
{
  global $system;
  /* prepare total price */
  $total = get_payment_total_value($amount);
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* create a product for the plan */
  $request_body = [
    'name' => $name,
    'description' => ($description) ? $description : $name,
    'home_url' => $system['system_url'],
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/catalogs/products');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if ($httpCode != 201) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  $product_id = $response->id;
  /* create a plan */
  $request_body = [
    'product_id' => $product_id,
    'name' => $name,
    'description' => ($description) ? $description : $name,
    'status' => 'ACTIVE',
    'billing_cycles' => [
      [
        'frequency' => [
          'interval_unit' => strtoupper($interval),
          'interval_count' => $frequency,
        ],
        'tenure_type' => 'REGULAR',
        'sequence' => 1,
        'total_cycles' => 0,
        'pricing_scheme' => [
          'fixed_price' => [
            'value' => $total,
            'currency_code' => $system['system_currency'],
          ],
        ],
      ],
    ],
    'payment_preferences' => [
      'auto_bill_outstanding' => true,
      'setup_fee' => [
        'value' => '0',
        'currency_code' => $system['system_currency'],
      ],
      'setup_fee_failure_action' => 'CONTINUE',
      'payment_failure_threshold' => 3,
    ],
    'taxes' => [
      'percentage' => '0',
      'inclusive' => false,
    ],
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/billing/plans');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if ($httpCode != 201) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  $plan_id = $response->id;
  return $plan_id;
}


/**
 * paypal_edit_billing_plan
 *
 * @param string $billing_plan_id
 * @param string $name
 * @param string $description
 * @return void
 */
function paypal_edit_billing_plan($billing_plan_id, $name, $description)
{
  global $system;
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* get plan details */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/billing/plans/' . $billing_plan_id);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if ($httpCode != 200) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  $product_id = $response->product_id;
  /* update plan */
  $request_body = [
    [
      "op" => "replace",
      "path" => "/name",
      "value" => $name
    ],
    [
      "op" => "replace",
      "path" => "/description",
      "value" => ($description) ? $description : $name
    ]
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/billing/plans/' . $billing_plan_id);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if ($httpCode != 204) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
}


/**
 * paypal_deactivate_billing_plan
 *
 * @param string $billing_plan_id
 * @return void
 */
function paypal_deactivate_billing_plan($billing_plan_id)
{
  global $system;
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* deactivate plan */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/billing/plans/' . $billing_plan_id . '/deactivate');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if ($httpCode != 204) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
}


/**
 * paypal_replace_billing_plan
 *
 * @param string $billing_plan_id
 * @param string $name
 * @param string $description
 * @param string $frequency
 * @param string $interval
 * @param string $amount
 * 
 * @return string
 */
function paypal_replace_billing_plan($billing_plan_id, $name, $description, $frequency, $interval, $amount)
{
  /* deactivate old plan */
  paypal_deactivate_billing_plan($billing_plan_id);
  /* create new plan */
  $new_plan_id = paypal_create_billing_plan($name, $description, $frequency, $interval, $amount);
  return $new_plan_id;
}


/**
 * paypal_verify_webhook_signature
 *
 * @param string $headers
 * @param string $body
 * 
 * @return boolean
 */
function paypal_verify_webhook_signature($headers, $body)
{
  global $system;
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* verify webhook signature */
  $request_body = [
    "auth_algo" => $headers['paypal-auth-algo'],
    "cert_url" => $headers['paypal-cert-url'],
    "transmission_id" => $headers['paypal-transmission-id'],
    "transmission_sig" => $headers['paypal-transmission-sig'],
    "transmission_time" => $headers['paypal-transmission-time'],
    "webhook_id" => $system['paypal_webhook'],
    "webhook_event" => $body
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/notifications/verify-webhook-signature');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  $response = json_decode($response, true);
  if (!($httpCode >= 200 && $httpCode < 300)) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
    throw new Exception($error);
  }
  if ($response['verification_status'] == 'SUCCESS') {
    return true;
  }
  return false;
}


/**
 * paypal_cancel_subscription
 *
 * @param string $subscription_id
 * 
 * @return void
 */
function paypal_cancel_subscription($subscription_id)
{
  global $system;
  /* get access token */
  $access_token = paypal_access_token();
  /* prepare API url */
  $paypal_api_url = ($system['paypal_mode'] == "sandbox") ? "https://api-m.sandbox.paypal.com" : "https://api.paypal.com";
  /* cancel subscription */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $paypal_api_url . '/v1/billing/subscriptions/' . $subscription_id . '/cancel');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $response = json_decode($response);
  if ($httpCode != 204) {
    $error = "Error Processing Request";
    if (DEBUGGING) {
      if ($response->error_description) {
        $error = $response->error_description;
      } elseif ($response->message) {
        $error = $response->message;
      } else {
        $backtrace = debug_backtrace();
        $line = $backtrace[0]['line'];
        $file = $backtrace[0]['file'];
        $error = "This error function was called from line $line in file $file";
      }
    }
  }
}



/* ------------------------------- */
/* Stripe */
/* ------------------------------- */

/**
 * stripe_payment_session
 *
 * @param string $method
 * @param string $handle
 * @param string $price
 * @param integer $id
 * @return string
 */
function stripe_payment_session($method, $handle, $price, $id = null)
{
  global $system, $user;
  /* prepare */
  $total = get_payment_total_value($price);
  switch ($handle) {
    case 'packages':
      /* get package */
      $package = $user->get_package($id);
      if ($package['stripe_billing_plan']) {
        return stripe_subscription_session($method, $handle, $id, $package['stripe_billing_plan']);
      }
      $product = __($system['system_title']) . " " . __('Package') . " " . __($package['name']);
      $description = __('Pay For') . " " . __($system['system_title']) . " " . __('Package') . " " . __($package['name']);
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=packages&package_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      break;

    case 'subscribe':
      /* get monetization plan */
      $monetization_plan = $user->get_monetization_plan($id, true);
      if (!$monetization_plan) {
        throw new Exception(__("The monetization plan you're trying to subscribe to is not found"));
      }
      if ($monetization_plan['stripe_billing_plan']) {
        return stripe_subscription_session($method, $handle, $id, $monetization_plan['stripe_billing_plan']);
      }
      $product = __($system['system_title']) . " " . __('Monetization Plan') . " " . __($monetization_plan['name']);
      $description = __('Pay For') . " " . __($system['system_title']) . " " . __('Monetization Plan') . " " . __($monetization_plan['name']);
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=subscribe&plan_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      break;

    case 'wallet':
      $product = __($system['system_title']) . " " . __('Wallet');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=wallet";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      $_SESSION['wallet_replenish_amount'] = $price;
      break;

    case 'donate':
      $product = __($system['system_title']) . " " . __('Funding Donation');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=donate&post_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      $_SESSION['donation_amount'] = $price;
      break;

    case 'paid_post':
      $product = __($system['system_title']) . " " . __('Paid Post');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=paid_post&post_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      break;

    case 'movies':
      $product = __($system['system_title']) . " " . __('Movies');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=movies&movie_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      break;

    default:
      _error(400);
      break;
  }
  $method = ($method == 'credit') ? 'card' : 'alipay';
  /* Stripe */
  $secret_key = ($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret'];
  \Stripe\Stripe::setApiKey($secret_key);
  $product = \Stripe\Product::create([
    'name' => $product,
    'type' => 'service',
  ]);
  $session = \Stripe\Checkout\Session::create([
    'payment_method_types' => [$method],
    'line_items' => [[
      'price_data' => [
        'product' => $product->id,
        'unit_amount' => ($system['system_currency'] == 'JPY') ? $total : $total * 100,
        'currency' => $system['system_currency'],
      ],
      'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $URL['success'],
    'cancel_url' => $URL['cancel'],
  ]);
  $_SESSION['stripe_session'] = $session;
  return $session;
}


function stripe_subscription_session($method, $handle, $id, $billing_plan_id)
{
  global $system, $user;
  /* prepare method */
  $method = ($method == 'credit') ? 'card' : 'alipay';
  /* prepare handle */
  switch ($handle) {
    case 'packages':
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=packages&package_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      break;

    case 'subscribe':
      $URL['success'] = $system['system_url'] . "/webhooks/stripe.php?status=success&handle=subscribe&plan_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/stripe.php?status=cancel";
      break;

    default:
      _error(400);
      break;
  }
  /* Stripe */
  $secret_key = ($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret'];
  \Stripe\Stripe::setApiKey($secret_key);
  $session = \Stripe\Checkout\Session::create([
    'payment_method_types' => [$method],
    'line_items' => [[
      'price' => $billing_plan_id,
      'quantity' => 1,
    ]],
    'mode' => 'subscription',
    'success_url' => $URL['success'],
    'cancel_url' => $URL['cancel'],
  ]);
  $_SESSION['stripe_session'] = $session;
  return $session;
}


/**
 * stripe_payment_check
 *
 * @return boolean
 */
function stripe_payment_check()
{
  global $system;
  if ($_SESSION['stripe_session']) {
    $secret_key = ($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret'];
    \Stripe\Stripe::setApiKey($secret_key);
    $session = \Stripe\Checkout\Session::retrieve($_SESSION['stripe_session']['id']);
    unset($_SESSION['stripe_session']);
    if ($session->payment_status == "paid") {
      return $session;
    }
  }
  return false;
}


/**
 * stripe_create_billing_plan
 *
 * @param string $name
 * @param string $description
 * @param string $frequency
 * @param string $interval
 * @param string $amount
 * @return string
 */
function stripe_create_billing_plan($name, $description, $frequency, $interval, $amount)
{
  global $system;
  /* prepare total price */
  $total = get_payment_total_value($amount);
  /* Stripe */
  $secret_key = ($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret'];
  \Stripe\Stripe::setApiKey($secret_key);
  $product = \Stripe\Product::create([
    'name' => $name,
    'description' => ($description) ? $description : $name,
    'type' => 'service',
  ]);
  $plan = \Stripe\Plan::create([
    'product' => $product->id,
    'nickname' => ($description) ? $description : $name,
    'interval' => $interval,
    'interval_count' => $frequency,
    'currency' => $system['system_currency'],
    'amount' => ($system['system_currency'] == 'JPY') ? $total : $total * 100,
  ]);
  return $plan->id;
}


/**
 * stripe_edit_billing_plan
 *
 * @param string $billing_plan_id
 * @param string $description
 * @return void
 */
function stripe_edit_billing_plan($billing_plan_id, $description)
{
  global $system;
  /* Stripe */
  $secret_key = ($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret'];
  \Stripe\Stripe::setApiKey($secret_key);
  $plan = \Stripe\Plan::update(
    $billing_plan_id,
    [
      'nickname' => $description,
    ]
  );
}


/**
 * stripe_deactivate_billing_plan
 *
 * @param string $billing_plan_id
 * @return void
 */
function stripe_deactivate_billing_plan($billing_plan_id)
{
  global $system;
  /* Stripe */
  $secret_key = ($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret'];
  \Stripe\Stripe::setApiKey($secret_key);
  $plan = \Stripe\Plan::update(
    $billing_plan_id,
    [
      'active' => false,
    ]
  );
}


/**
 * stripe_replace_billing_plan
 *
 * @param string $billing_plan_id
 * @param string $name
 * @param string $description
 * @param string $frequency
 * @param string $interval
 * @param string $amount
 * 
 * @return string
 */
function stripe_replace_billing_plan($billing_plan_id, $name, $description, $frequency, $interval, $amount)
{
  /* deactivate old plan */
  stripe_deactivate_billing_plan($billing_plan_id);
  /* create new plan */
  $new_plan_id = stripe_create_billing_plan($name, $description, $frequency, $interval, $amount);
  return $new_plan_id;
}


/**
 * stripe_cancel_subscription
 *
 * @param string $subscription_id
 * 
 * @return void
 */
function stripe_cancel_subscription($subscription_id)
{
  global $system;
  /* Stripe */
  $secret_key = ($system['stripe_mode'] == "live") ? $system['stripe_live_secret'] : $system['stripe_test_secret'];
  \Stripe\Stripe::setApiKey($secret_key);
  /* retrieve the subscription */
  $subscription = \Stripe\Subscription::retrieve($subscription_id);
  /* cancel the subscription */
  $subscription->cancel();
}



/* ------------------------------- */
/* Paystack */
/* ------------------------------- */

/**
 * paystack
 *
 * @param string $handle
 * @param string $price
 * @param integer $id
 * @return string
 */
function paystack($handle, $price, $id = null)
{
  global $system, $user;
  /* prepare */
  $total = get_payment_total_value($price);
  switch ($handle) {
    case 'packages':
      $callback = $system['system_url'] . "/webhooks/paystack.php?status=success&handle=packages&package_id=$id";
      break;

    case 'wallet':
      $callback = $system['system_url'] . "/webhooks/paystack.php?status=success&handle=wallet";
      $_SESSION['wallet_replenish_amount'] = $price;
      break;

    case 'donate':
      $callback = $system['system_url'] . "/webhooks/paystack.php?status=success&handle=donate&post_id=$id";
      $_SESSION['donation_amount'] = $price;
      break;

    case 'subscribe':
      $callback = $system['system_url'] . "/webhooks/paystack.php?status=success&handle=subscribe&plan_id=$id";
      break;

    case 'paid_post':
      $callback = $system['system_url'] . "/webhooks/paystack.php?status=success&handle=paid_post&post_id=$id";
      break;

    case 'movies':
      $callback = $system['system_url'] . "/webhooks/paystack.php?status=success&handle=movies&movie_id=$id";
      break;

    default:
      _error(400);
      break;
  }
  /* Paystack */
  $headers = [
    'Authorization: Bearer ' . $system['paystack_secret'],
    'Content-Type: application/json',
  ];
  $request_body = [
    'email' => $user->_data['user_email'],
    'amount' => $total * 100,
    'callback_url' => $callback
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/initialize");
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  if (!$responseJson['status']) {
    throw new Exception($responseJson['message']);
  }
  return $responseJson['data']['authorization_url'];
}


/**
 * paystack_check
 *
 * @param string $reference
 * @return boolean
 */
function paystack_check($reference)
{
  global $system;
  $headers = [
    'Authorization: Bearer ' . $system['paystack_secret']
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . $reference);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  if ($responseJson['data']['status'] == 'success') {
    return true;
  }
  return false;
}



/* ------------------------------- */
/* Razorpay */
/* ------------------------------- */

/**
 * razorpay_check
 *
 * @param string $payment_id
 * @param integer $amount
 * @return boolean
 */

function razorpay_check($payment_id, $amount)
{
  global $system;
  /* prepare total */
  $total = get_payment_total_value($amount);
  /* Razorpay */
  $url = 'https://api.razorpay.com/v1/payments/' . $payment_id . '/capture';
  $params = http_build_query(['amount' => $total * 100, 'currency' => $system['currency']]);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_USERPWD, $system['razorpay_key_id'] . ':' . $system['razorpay_key_secret']);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  if ($responseJson['error_code']) {
    return false;
  }
  return true;
}



/* ------------------------------- */
/* Cashfree */
/* ------------------------------- */

/**
 * cashfree
 *
 * @param string $handle
 * @param string $price
 * @param integer $id
 * @param string $billing_name
 * @param string $billing_email
 * @param string $billing_phone
 * @return string
 */
function cashfree($handle, $price, $id, $billing_name, $billing_email, $billing_phone)
{
  global $system;
  /* prepare */
  $total = get_payment_total_value($price);
  $return_url = $system['system_url'] . "/webhooks/cashfree.php?orderId={order_id}&token={order_token}";
  switch ($handle) {
    case 'packages':
      $return_url .= "&handle=packages&package_id=$id";
      break;

    case 'wallet':
      $return_url .= "&handle=wallet";
      $_SESSION['wallet_replenish_amount'] = $price;
      break;

    case 'donate':
      $return_url .= "&handle=donate&post_id=$id";
      $_SESSION['donation_amount'] = $price;
      break;

    case 'subscribe':
      $return_url .= "&handle=subscribe&plan_id=$id";
      break;

    case 'paid_post':
      $return_url .= "&handle=paid_post&post_id=$id";
      break;

    case 'movies':
      $return_url .= "&handle=movies&movie_id=$id";
      break;

    default:
      _error(400);
      break;
  }
  $headers = [
    "content-type: application/json",
    "x-client-id: " . $system['cashfree_client_id'],
    "x-client-secret: " . $system['cashfree_client_secret'],
    "x-api-version: " . "2021-05-21",
  ];
  $request_body = [
    "order_amount" => $total,
    "order_currency" => $system['system_currency'],
    "customer_details" => [
      "customer_id" => uniqid(),
      "customer_email" => $billing_email,
      "customer_phone" => $billing_phone
    ],
    "order_meta" => [
      "return_url" => $return_url,
      "notify_url" => "",
    ]
  ];
  $apiBase = ($system['cashfree_mode'] == 'sandbox') ? 'https://sandbox.cashfree.com/pg/' : 'https://api.cashfree.com/pg';
  $apiURL = $apiBase . "/orders";
  /* Cashfree */
  $ch = curl_init($apiURL);
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_URL, $apiURL);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  if ($httpCode != 200) {
    throw new Exception($responseJson['message']);
  }
  return $responseJson["payment_link"];
}


/**
 * cashfree_check
 *
 * @param string $orderId
 * @return boolean
 */
function cashfree_check($orderId)
{
  global $system;
  /* prepare */
  $headers = [
    "content-type: application/json",
    "x-client-id: " . $system['cashfree_client_id'],
    "x-client-secret: " . $system['cashfree_client_secret'],
    "x-api-version: " . "2021-05-21",
  ];
  $apiBase = ($system['cashfree_mode'] == 'sandbox') ? 'https://sandbox.cashfree.com/pg/' : 'https://api.cashfree.com/pg';
  $apiURL = $apiBase . "/orders/" . $orderId;
  /* Cashfree */
  $ch = curl_init($apiURL);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  if ($httpCode == 200 && $responseJson['order_status'] == 'PAID') {
    return true;
  }
  return false;
}



/* ------------------------------- */
/* Coinbase */
/* ------------------------------- */

/**
 * coinbase
 *
 * @param string $handle
 * @param string $price
 * @param integer $id
 * @return array
 */
function coinbase($handle, $price, $id = null)
{
  global $system;
  /* prepare */
  $total = get_payment_total_value($price);
  $coinbase_hash = get_hash_token();
  switch ($handle) {
    case 'packages':
      $product = __($system['system_title']) . " " . __('Pro Package');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/coinbase.php?status=success&handle=packages&package_id=$id&coinbase_hash=$coinbase_hash";
      $URL['cancel'] = $system['system_url'] . "/webhooks/coinbase.php?status=cancel";
      break;

    case 'wallet':
      $product = __($system['system_title']) . " " . __('Wallet');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/coinbase.php?status=success&handle=wallet&coinbase_hash=$coinbase_hash";
      $URL['cancel'] = $system['system_url'] . "/webhooks/coinbase.php?status=cancel";
      $_SESSION['wallet_replenish_amount'] = $price;
      break;

    case 'donate':
      $product = __($system['system_title']) . " " . __('Funding Donation');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/coinbase.php?status=success&handle=donate&post_id=$id&coinbase_hash=$coinbase_hash";
      $URL['cancel'] = $system['system_url'] . "/webhooks/coinbase.php?status=cancel";
      $_SESSION['donation_amount'] = $price;
      break;

    case 'subscribe':
      $product = __($system['system_title']) . " " . __('Subscribe');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/coinbase.php?status=success&handle=subscribe&plan_id=$id&coinbase_hash=$coinbase_hash";
      $URL['cancel'] = $system['system_url'] . "/webhooks/coinbase.php?status=cancel";
      break;

    case 'paid_post':
      $product = __($system['system_title']) . " " . __('Paid Post');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/coinbase.php?status=success&handle=paid_post&post_id=$id&coinbase_hash=$coinbase_hash";
      $URL['cancel'] = $system['system_url'] . "/webhooks/coinbase.php?status=cancel";
      break;

    case 'movies':
      $product = __($system['system_title']) . " " . __('Movies');
      $description = __('Pay For') . " " . __($system['system_title']);
      $URL['success'] = $system['system_url'] . "/webhooks/coinbase.php?status=success&handle=movies&movie_id=$id&coinbase_hash=$coinbase_hash";
      $URL['cancel'] = $system['system_url'] . "/webhooks/coinbase.php?status=cancel";
      break;

    default:
      _error(400);
      break;
  }
  $headers = [
    "content-type: application/json",
    "X-Cc-Api-Key: " . $system['coinbase_api_key'],
    "X-Cc-Version: " . "2018-03-22",
  ];
  $request_body =  [
    'name' =>  $product,
    'description' => $description,
    'pricing_type' => 'fixed_price',
    'local_price' => [
      'amount' => $total,
      'currency' => $system['system_currency']
    ],
    'metadata' => [
      'coinbase_hash' => $coinbase_hash
    ],
    "redirect_url" => $URL['success'],
    'cancel_url' => $URL['cancel']
  ];
  /* Coinbase */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.commerce.coinbase.com/charges');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  return [
    "coinbase_hash" => $coinbase_hash,
    "coinbase_code" => $responseJson['data']['code'],
    "hosted_url" => $responseJson["data"]["hosted_url"]
  ];
}


/**
 * coinbase_check
 *
 * @param string $coinbase_code
 * @return boolean
 */
function coinbase_check($coinbase_code)
{
  global $system;
  /* prepare */
  $headers = [
    "content-type: application/json",
    "X-Cc-Api-Key: " . $system['coinbase_api_key'],
    "X-Cc-Version: " . "2018-03-22",
  ];
  /* Coinbase */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.commerce.coinbase.com/charges/' . $coinbase_code);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  if (!empty($responseJson) && $responseJson['data']['payments'][0]['status'] == 'CONFIRMED') {
    return true;
  }
  return false;
}



/* ------------------------------- */
/* SecurionPay */
/* ------------------------------- */

/**
 * securionpay
 *
 * @param string $price
 * @return string
 */
function securionpay($price)
{
  global $system;
  $total = get_payment_total_value($price);
  $securionPay = new SecurionPay\SecurionPayGateway($system['securionpay_api_secret']);
  $checkoutCharge = new SecurionPay\Request\CheckoutRequestCharge();
  $checkoutCharge->amount($total * 100)->currency($system['system_currency']);
  $checkoutRequest = new SecurionPay\Request\CheckoutRequest();
  $checkoutRequest->charge($checkoutCharge);
  $signedCheckoutRequest = $securionPay->signCheckoutRequest($checkoutRequest);
  return $signedCheckoutRequest;
}


/**
 * securionpay_check
 *
 * @param string $charge_id
 * @return boolean
 */
function securionpay_check($charge_id)
{
  global $system;
  /* SecurionPay */
  $url = "https://api.securionpay.com/charges?limit=10";
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_USERPWD, $system['securionpay_api_secret'] . ":password");
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  $responseJson = json_decode($response, true);
  if (!empty($responseJson) && $responseJson['list'][0]['id'] == $charge_id) {
    return true;
  }
  return false;
}



/* ------------------------------- */
/* MoneyPoolsCash */
/* ------------------------------- */

/**
 * moneypoolscash_payment_token
 *
 * @return string
 */
function moneypoolscash_payment_token()
{
  global $system;
  $headers = [
    'Content-Type: application/json',
    'API-KEY: ' . $system['moneypoolscash_api_key'],
  ];
  $request_body = [
    'merchant_email' => $system['moneypoolscash_merchant_email']
  ];
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => 'https://moneypoolscash.com/gettoken?merchant_email=' . $system['moneypoolscash_merchant_email'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => json_encode($request_body),
    CURLOPT_HTTPHEADER => $headers,
  ]);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  if ($httpCode != 200) {
    throw new Exception("Error Processing Request");
  }
  $response = json_decode($response, true);
  if ($response['status'] != 'success') {
    throw new Exception("Error Processing Request");
  }
  $token = $response['data']['token'];
  return $token;
}


/**
 * moneypoolscash_wallet_token
 *
 * @return string
 */
function moneypoolscash_wallet_token()
{
  global $system;
  $headers = [
    'Content-Type: application/json',
    'User-Agent: Sngine',
    'API-KEY: ' . $system['moneypoolscash_api_key'],
  ];
  $request_body = [
    'user' => [
      'email' => $system['moneypoolscash_merchant_email'],
      'password' => $system['moneypoolscash_merchant_password']
    ],
  ];
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => 'https://moneypoolscash.com/api/loginapp',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($request_body),
    CURLOPT_HTTPHEADER => $headers,
  ]);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  if ($httpCode != 200) {
    throw new Exception("Error Processing Request");
  }
  $response = json_decode($response, true);
  if ($response['result'] != 'success') {
    throw new Exception("Error Processing Request");
  }
  $token = $response['token'];
  return $token;
}


/**
 * moneypoolscash
 *
 * @param string $handle
 * @param string $price
 * @param integer $id
 * @return string
 */
function moneypoolscash($handle, $price, $id = null)
{
  global $system;
  /* prepare */
  $total = get_payment_total_value($price);
  $token = moneypoolscash_payment_token();
  switch ($handle) {
    case 'packages':
      $URL['success'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=success&handle=packages&package_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=cancel";
      break;

    case 'wallet':
      $URL['success'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=success&handle=wallet";
      $URL['cancel'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=cancel";
      $_SESSION['wallet_replenish_amount'] = $price;
      break;

    case 'donate':
      $URL['success'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=success&handle=donate&post_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=cancel";
      $_SESSION['donation_amount'] = $price;
      break;

    case 'subscribe':
      $URL['success'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=success&handle=subscribe&plan_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=cancel";
      break;

    case 'paid_post':
      $URL['success'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=success&handle=paid_post&post_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=cancel";
      break;

    case 'movies':
      $URL['success'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=success&handle=movies&movie_id=$id";
      $URL['cancel'] = $system['system_url'] . "/webhooks/moneypoolscash.php?status=cancel";
      break;

    default:
      _error(400);
      break;
  }
  /* make payment request */
  $merchant_ref = md5(time() . rand(1111, 9999));
  $headers = [
    'Content-Type: application/json',
    'API-KEY: ' . $system['moneypoolscash_api_key'],
    'token: ' . $token,
  ];
  $request_body = [
    'merchant_email' => $system['moneypoolscash_merchant_email'],
    'amount' => $total,
    'currency' => $system['system_currency'],
    'return_url' => $URL['success'],
    'cancel_url' => $URL['cancel'],
    'merchant_ref' => $merchant_ref,
  ];
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => 'https://moneypoolscash.com/payrequest',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => json_encode($request_body),
    CURLOPT_HTTPHEADER => $headers,
  ]);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    throw new Exception("Error Processing Request");
  }
  curl_close($ch);
  if ($httpCode != 200) {
    throw new Exception("Error Processing Request");
  }
  $response = json_decode($response, true);
  $_SESSION['moneypoolscash_merchant_ref'] = $merchant_ref;
  $_SESSION['moneypoolscash_trx'] = $response['data']['trx'];
  return $response['data']['redirect_url'];
}


/**
 * moneypoolscash_check
 *
 * @return boolean
 */
function moneypoolscash_check()
{
  global $system;
  /* get token */
  $token = moneypoolscash_payment_token();
  $headers = [
    'Content-Type: application/json',
    'API-KEY: ' . $system['moneypoolscash_api_key'],
    'token: ' . $token,
  ];
  $request_body = [
    'merchant_email' => $system['moneypoolscash_merchant_email'],
    'trx' => $_SESSION['moneypoolscash_trx'],
    'merchant_ref' => $_SESSION['moneypoolscash_merchant_ref'],
  ];
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => 'https://moneypoolscash.com/gettrx',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => json_encode($request_body),
    CURLOPT_HTTPHEADER => $headers,
  ]);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if (curl_errno($ch)) {
    return false;
  }
  curl_close($ch);
  if ($httpCode != 200) {
    return false;
  }
  $response = json_decode($response, true);
  if ($response['code'] == '200' && $response['status'] == 'completed') {
    return true;
  }
  return false;
}


/**
 * process_automatic_withdrawal
 *
 * @param string $method
 * @param integer $amount
 * @param string $transfer_to
 * @return void
 */

function process_automatic_withdrawal($method, $amount, $transfer_to)
{
  global $system;
  switch ($method) {
    case 'moneypoolscash':
      /* get token */
      $token = moneypoolscash_wallet_token();
      $headers = [
        'Content-Type: application/json',
        'API-KEY: ' . $system['moneypoolscash_api_key'],
        'token: ' . $token,
        'email: ' . $system['moneypoolscash_merchant_email'],
      ];
      $request_body = [
        'toemail' => $transfer_to,
        'amount' => $amount,
        'currency' => $system['system_currency'],
      ];
      $ch = curl_init();
      curl_setopt_array($ch, [
        CURLOPT_URL => 'https://moneypoolscash.com/api/transferfunds',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($request_body),
        CURLOPT_HTTPHEADER => $headers,
      ]);
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (curl_errno($ch)) {
        throw new Exception("Error Processing Request");
      }
      curl_close($ch);
      if ($httpCode != 200) {
        throw new Exception("Error Processing Request");
      }
      $response = json_decode($response, true);
      if ($response['code'] != '200') {
        throw new Exception($response['message']);
      }
      break;
  }
}


/* ------------------------------- */
/* User Access */
/* ------------------------------- */

/**
 * user_access
 *
 * @param boolean $is_ajax
 * @param boolean $bypass_subscription
 * @return void
 */
function user_access($is_ajax = false, $bypass_subscription = false)
{
  global $user, $system;
  if ($is_ajax) {
    /* check user logged in */
    if (!$user->_logged_in) {
      modal('LOGIN');
    }
    /* check user activated */
    if ($system['activation_enabled'] && !$user->_data['user_activated']) {
      modal("MESSAGE", __("Not Activated"), __("Before you can interact with other users, you need to confirm your email address"));
    }
    /* check registration type */
    if ($system['registration_type'] == "paid" && $user->_data['user_group'] > '1' && !$user->_data['user_subscribed'] && !$bypass_subscription) {
      modal("MESSAGE", __("Subscription Needed"), __("Before you can interact with other users, you need to buy subscription package"));
    }
  } else {
    if (!$user->_logged_in) {
      user_login();
    } else {
      /* check registration type */
      if ($system['registration_type'] == "paid" && $user->_data['user_group'] > '1' && !$user->_data['user_subscribed']) {
        redirect('/packages');
      }
      /* check user activated */
      if ($system['activation_enabled'] && $system['activation_required'] && !$user->_data['user_activated']) {
        _error('ACTIVATION');
      }
      /* check user getted started */
      if ($system['getting_started'] && !$user->_data['user_started']) {
        redirect('/started');
      }
    }
  }
}


/**
 * user_login
 *
 * @return void
 */
function user_login()
{
  global $user, $smarty;
  $smarty->assign('genders', $user->get_genders());
  $smarty->assign('custom_fields', $user->get_custom_fields());
  $smarty->assign('highlight', __("You must sign in to see this page"));
  page_header(__("Sign in"));
  page_footer('sign');
  exit;
}



/* ------------------------------- */
/* Modal */
/* ------------------------------- */

/**
 * modal
 *
 * @return json
 */
function modal()
{
  $args = func_get_args();
  switch ($args[0]) {
    case 'LOGIN':
      return_json(["callback" => "modal('#modal-login')"]);
      break;
    case 'MESSAGE':
      return_json(["callback" => "modal('#modal-message', {title: '" . $args[1] . "', message: '" . addslashes($args[2]) . "'})"]);
      break;
    case 'ERROR':
      return_json(["callback" => "modal('#modal-error', {title: '" . $args[1] . "', message: '" . addslashes($args[2]) . "'})"]);
      break;
    case 'SUCCESS':
      return_json(["callback" => "modal('#modal-success', {title: '" . $args[1] . "', message: '" . addslashes($args[2]) . "'})"]);
      break;
    default:
      if (isset($args[1])) {
        return_json(["callback" => "modal('" . $args[0] . "', " . $args[1] . ")"]);
      } else {
        return_json(["callback" => "modal('" . $args[0] . "')"]);
      }
      break;
  }
}



/* ------------------------------- */
/* Popover */
/* ------------------------------- */

/**
 * popover
 *
 * @param integer $uid
 * @param string $username
 * @param string $name
 * @return string
 */
function popover($uid, $username, $name)
{
  global $system;
  $popover = '<span class="js_user-popover" data-uid="' . $uid . '"><a href="' . $system['system_url'] . '/' . $username . '">' . $name . '</a></span>';
  return $popover;
}



/* ------------------------------- */
/* Page */
/* ------------------------------- */

/**
 * page_header
 *
 * @param string $title
 * @param string $description
 * @return void
 */
function page_header($title, $description = '', $image = '')
{
  global $smarty, $system;
  $description = ($description != '') ? $description : __($system['system_description']);
  if ($image == '') {
    if ($system['system_ogimage']) {
      $image = $system['system_uploads'] . '/' . $system['system_ogimage'];
    } else {
      $image = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/og-image.jpg';
    }
  }
  $smarty->assign('page_title', $title);
  $smarty->assign('page_description', $description);
  $smarty->assign('page_image', $image);
}


/**
 * page_footer
 *
 * @param string $page
 * @return void
 */
function page_footer($page)
{
  global $smarty;
  $smarty->assign('page', $page);
  $smarty->display("$page.tpl");
}



/* ------------------------------- */
/* Post Feelings */
/* ------------------------------- */

/**
 * get_feelings
 *
 * @return array
 */
function get_feelings()
{
  $feelings = [
    ["icon" => "grinning-face-with-smiling-eyes",  "action" => "Feeling",      "text" => __("Feeling"),      "placeholder" => __("How are you feeling?")],
    ["icon" => "headphone",                        "action" => "Listening To", "text" => __("Listening To"), "placeholder" => __("What are you listening to?")],
    ["icon" => "glasses",                          "action" => "Watching",     "text" => __("Watching"),     "placeholder" => __("What are you watching?")],
    ["icon" => "video-game",                       "action" => "Playing",      "text" => __("Playing"),      "placeholder" => __("What are you playing?")],
    ["icon" => "shortcake",                        "action" => "Eating",       "text" => __("Eating"),       "placeholder" => __("What are you eating?")],
    ["icon" => "tropical-drink",                   "action" => "Drinking",     "text" => __("Drinking"),     "placeholder" => __("What are you drinking?")],
    ["icon" => "airplane",                         "action" => "Traveling To", "text" => __("Traveling To"), "placeholder" => __("Where are you going?")],
    ["icon" => "books",                            "action" => "Reading",      "text" => __("Reading"),      "placeholder" => __("What are you reading?")],
    ["icon" => "calendar",                         "action" => "Attending",    "text" => __("Attending"),    "placeholder" => __("What are you attending?")],
    ["icon" => "birthday-cake",                    "action" => "Celebrating",  "text" => __("Celebrating"),  "placeholder" => __("What are you celebrating?")],
    ["icon" => "magnifying-glass-tilted-left",     "action" => "Looking For",  "text" => __("Looking For"),  "placeholder" => __("What are you looking for?")]
  ];
  return $feelings;
}


/**
 * get_feelings_types
 *
 * @return array
 */
function get_feelings_types()
{
  $feelings_types = [
    ["icon" => "grinning-face-with-smiling-eyes",  "action" => "Happy",      "text" => __("Happy")],
    ["icon" => "smiling-face-with-heart-eyes",     "action" => "Loved",      "text" => __("Loved")],
    ["icon" => "relieved-face",                    "action" => "Satisfied",  "text" => __("Satisfied")],
    ["icon" => "flexed-biceps",                    "action" => "Strong",     "text" => __("Strong")],
    ["icon" => "disappointed-face",                "action" => "Sad",        "text" => __("Sad")],
    ["icon" => "winking-face-with-tongue",         "action" => "Crazy",      "text" => __("Crazy")],
    ["icon" => "downcast-face-with-sweat",         "action" => "Tired",      "text" => __("Tired")],
    ["icon" => "sleeping-face",                    "action" => "Sleepy",     "text" => __("Sleepy")],
    ["icon" => "confused-face",                    "action" => "Confused",   "text" => __("Confused")],
    ["icon" => "worried-face",                     "action" => "Worried",    "text" => __("Worried")],
    ["icon" => "angry-face",                       "action" => "Angry",      "text" => __("Angry")],
    ["icon" => "pouting-face",                     "action" => "Annoyed",    "text" => __("Annoyed")],
    ["icon" => "face-with-open-mouth",             "action" => "Shocked",    "text" => __("Shocked")],
    ["icon" => "pensive-face",                     "action" => "Down",       "text" => __("Down")],
    ["icon" => "confounded-face",                  "action" => "Confounded", "text" => __("Confounded")]
  ];
  return $feelings_types;
}


/**
 * get_feeling_icon
 *
 * @param string $needle
 * @param array $array
 * @param string $key
 * @return string
 */
function get_feeling_icon($needle, $array, $key = "action")
{
  foreach ($array as $_key => $_val) {
    if ($_val[$key] === $needle) {
      return $array[$_key]['icon'];
    }
  }
  return false;
}



/* ------------------------------- */
/* Censored Words */
/* ------------------------------- */

/**
 * censored_words
 *
 * @param string $text
 * @return string
 */
function censored_words($text)
{
  global $system;
  if ($system['censored_words_enabled'] && $text) {
    $bad_words = explode(',', trim($system['censored_words']));
    if ($bad_words) {
      foreach ($bad_words as $word) {
        $word = trim($word);
        $pattern = '/\b' . $word . '\b/iu';
        $text = preg_replace($pattern, str_repeat('*', strlen($word)), $text);
      }
    }
  }
  return $text;
}



/* ------------------------------- */
/* Images */
/* ------------------------------- */

/**
 * get_picture
 *
 * @param string $picture
 * @param string $type
 * @return string
 */
function get_picture($picture, $type)
{
  global $system;
  if ($picture == "") {
    switch ($type) {
      case 'page':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_page.png';
        break;

      case 'group':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_group.png';
        break;

      case 'event':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_event.png';
        break;

      case 'article':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_article.png';
        break;

      case 'movie':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_movie.png';
        break;

      case 'game':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_game.png';
        break;

      case 'package':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_package.png';
        break;

      case 'flag':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_flag.png';
        break;

      case 'system':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/svg/dashboard.svg';
        break;

      case '1':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_profile_male.png';
        break;

      case '2':
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_profile_female.png';
        break;

      default:
        $picture = $system['system_url'] . '/content/themes/' . $system['theme'] . '/images/blank_profile.png';
        break;
    }
  } else {
    $picture = $system['system_uploads'] . '/' . $picture;
  }
  return $picture;
}


/**
 * save_picture_from_url
 *
 * @param string $file
 * @param boolean $cropped
 * @return string
 */
function save_picture_from_url($file, $cropped = false, $resize = false)
{
  global $system;
  /* check & create uploads dir */
  $folder = 'photos';
  $directory = $folder . '/' . date('Y') . '/' . date('m') . '/';
  // init image & prepare image name & path
  require_once(ABSPATH . 'includes/class-image.php');
  $image = new Image($file);
  $prefix = $system['uploads_prefix'] . '_' . get_hash_token();
  if ($cropped) {
    $image_name = $directory . $prefix . "_cropped" . $image->_img_ext;
    if ($resize) {
      $image->resizeWidth($_POST['resize_width']);
    }
    $_POST['width'] = (isset($_POST['width'])) ? $_POST['width'] : $image->getWidth();
    $_POST['height'] = (isset($_POST['height'])) ? $_POST['height'] : $image->getHeight();
    $image->crop($_POST['width'], $_POST['height'], $_POST['x'], $_POST['y']);
  } else {
    $image_name = $directory . $prefix . $image->_img_ext;
  }
  $path = ABSPATH . $system['uploads_directory'] . '/' . $image_name;
  /* set uploads directory */
  if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder)) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder, 0777, true);
  }
  if (!file_exists(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'))) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y'), 0777, true);
  }
  if (!file_exists($system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'))) {
    @mkdir(ABSPATH . $system['uploads_directory'] . '/' . $folder . '/' . date('Y') . '/' . date('m'), 0777, true);
  }
  /* save the new image */
  $image->save($path, $system['uploads_quality']);
  /* cloud storage */
  save_file_to_cloud($path, $image_name);
  return $image_name;
}


/**
 * watermark_image
 *
 * @param string $image_path
 * @param string $image_type
 * @return void
 */
function watermark_image($image_path, $image_type)
{
  global $system;
  if (!is_empty($system['watermark_icon'])) {
    try {
      $image = new claviska\SimpleImage();
      $image
        ->fromFile($image_path)
        ->autoOrient()
        ->overlay($system['system_uploads'] . "/" . $system['watermark_icon'], $system['watermark_position'], $system['watermark_opacity'], $system['watermark_xoffset'], $system['watermark_yoffset'])
        ->toFile($image_path, $image_type);
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
}



/* ------------------------------- */
/* Utilities */
/* ------------------------------- */

/**
 * _getallheaders
 *
 * @return array
 */
function _getallheaders()
{
  return array_change_key_case(getallheaders(), CASE_LOWER);
}


/**
 * minimize_css
 *
 * @param string $file_path
 * @return string
 */
function minimize_css($file_path)
{
  global $smarty;
  $css = $smarty->fetch($file_path);
  if (empty($css)) {
    return $css;
  }
  // normalize whitespace.
  $css = preg_replace('/\s+/', ' ', $css);
  // remove spaces before and after comment.
  $css = preg_replace('/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css);
  // remove all comments
  $css = preg_replace('/\/\*(.*?)\*\//', '', $css);
  // remove ; before }.
  $css = preg_replace('/;(?=\s*})/', '', $css);
  // remove space after , : ; { } */ >.
  $css = preg_replace('/(,|:|;|\{|}|\*\/|>) /', '$1', $css);
  // Remove space before , ; { } ( ) >.
  $css = preg_replace('/ (,|;|\{|}|\(|\)|>)/', '$1', $css);
  // strips leading 0 on decimal values (converts 0.5px into .5px).
  $css = preg_replace('/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css);
  // Strips units if value is 0 (converts 0px to 0).
  $css = preg_replace('/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css);
  // converts all zeros value into short-hand.
  $css = preg_replace('/0 0 0 0/', '0', $css);
  // shortern 6-character hex color codes to 3-character where possible.
  $css = preg_replace('/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css);
  return trim($css);
}


/**
 * get_ip
 *
 * @return string
 */
function get_user_ip()
{
  /* handle CloudFlare IP addresses */
  return (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR']);
}


/**
 * get_os
 *
 * @return string
 */
function get_user_os()
{
  $os_platform = "Unknown OS Platform";
  $os_array = [
    '/windows nt 10/i'      =>  'Windows 10',
    '/windows nt 6.3/i'     =>  'Windows 8.1',
    '/windows nt 6.2/i'     =>  'Windows 8',
    '/windows nt 6.1/i'     =>  'Windows 7',
    '/windows nt 6.0/i'     =>  'Windows Vista',
    '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
    '/windows nt 5.1/i'     =>  'Windows XP',
    '/windows xp/i'         =>  'Windows XP',
    '/windows nt 5.0/i'     =>  'Windows 2000',
    '/windows me/i'         =>  'Windows ME',
    '/win98/i'              =>  'Windows 98',
    '/win95/i'              =>  'Windows 95',
    '/win16/i'              =>  'Windows 3.11',
    '/macintosh|mac os x/i' =>  'Mac OS X',
    '/mac_powerpc/i'        =>  'Mac OS 9',
    '/linux/i'              =>  'Linux',
    '/ubuntu/i'             =>  'Ubuntu',
    '/iphone/i'             =>  'iPhone',
    '/ipod/i'               =>  'iPod',
    '/ipad/i'               =>  'iPad',
    '/android/i'            =>  'Android',
    '/blackberry/i'         =>  'BlackBerry',
    '/webos/i'              =>  'Mobile'
  ];
  foreach ($os_array as $regex => $value) {
    if (preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
      $os_platform = $value;
    }
  }
  return $os_platform;
}


/**
 * get_browser
 *
 * @return string
 */
function get_user_browser()
{
  $browser = "Unknown Browser";
  $browser_array = [
    '/msie/i'       =>  'Internet Explorer',
    '/firefox/i'    =>  'Firefox',
    '/safari/i'     =>  'Safari',
    '/chrome/i'     =>  'Chrome',
    '/edge/i'       =>  'Edge',
    '/opera/i'      =>  'Opera',
    '/netscape/i'   =>  'Netscape',
    '/maxthon/i'    =>  'Maxthon',
    '/konqueror/i'  =>  'Konqueror',
    '/mobile/i'     =>  'Handheld Browser'
  ];
  foreach ($browser_array as $regex => $value) {
    if (preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
      $browser = $value;
    }
  }
  return $browser;
}


/**
 * get_user_age
 *
 * @param string $birthdate
 * @return string
 */
function get_user_age($birthdate)
{
  if ($birthdate === null) {
    return null;
  }
  $birthdate = DateTime::createFromFormat('Y-m-d', $birthdate);
  if ($birthdate === false) {
    return null;
  }
  $today = new DateTime('today');
  $age = $today->diff($birthdate)->y;
  return $age;
}


/**
 * get_extension
 *
 * @param string $path
 * @return string
 */
function get_extension($path)
{
  return strtolower(pathinfo($path, PATHINFO_EXTENSION));
}


/**
 * get_origin_url
 *
 * @param string $url
 * @return string
 */
function get_origin_url($url)
{
  stream_context_set_default([
    'http' => [
      'ignore_errors' => true,
      'method' => 'HEAD',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
    ]
  ]);
  $headers = get_headers($url, 1);
  if ($headers !== false && (isset($headers['location']) || isset($headers['Location']))) {
    $location = (isset($headers['location'])) ? $headers['location'] : $headers['Location'];
    return is_array($location) ? array_pop($location) : $location;
  }
  return $url;
}


/**
 * decode_urls
 *
 * @param string $text
 * @return string
 */
function decode_urls($text)
{
  $text = ($text) ? preg_replace('/(https?:\/\/[^\s]+)/', "<a target='_blank' rel='nofollow' href=\"$1\">$1</a>", $text) : $text;
  return $text;
}


/**
 * remove_urls_spaces
 *
 * @param string $url
 * @return string
 */
function remove_urls_spaces($url)
{
  $url = str_replace('%20', '%E2%80%AF', $url);
  return $url;
}


/**
 * get_url_text
 *
 * @param string $string
 * @param integer $length
 * @return string
 */
function get_url_text($string, $length = 10)
{
  $string = html_entity_decode($string, ENT_QUOTES);
  $string = htmlspecialchars_decode($string, ENT_QUOTES);
  $string = preg_replace('/[^\\pL\d]+/u', '-', $string);
  $string = trim($string, '-');
  $words = explode("-", $string);
  if (count($words) > $length) {
    $string = "";
    for ($i = 0; $i < $length; $i++) {
      $string .= "-" . $words[$i];
    }
    $string = trim($string, '-');
  }
  return $string;
}


/**
 * remove_querystring_var
 *
 * @param string $url
 * @param string $key
 * @return string
 */
function remove_querystring_var($url, $key)
{
  $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
  $url = substr($url, 0, -1);
  return $url;
}


/**
 * get_snippet_text
 *
 * @param string $string
 * @return string
 */
function get_snippet_text($string)
{
  $string = htmlspecialchars_decode($string, ENT_QUOTES);
  $string = strip_tags($string);
  return $string;
}


/**
 * get_tag
 *
 * @param string $string
 * @return string
 */
function get_tag($string)
{
  $string = trim($string);
  $string = preg_replace('/\s+/', '_', $string);
  return $string;
}


/**
 * get_youtube_id
 *
 * @param string $url
 * @param boolean $embed
 * @return string
 */
function get_youtube_id($url, $embed = true)
{
  if ($embed) {
    preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id);
    return $id[1];
  } else {
    parse_str(parse_url($url, PHP_URL_QUERY), $id);
    return $id['v'];
  }
}


/**
 * get_vimeo_id
 *
 * @param string $url
 * @return string
 */
function get_vimeo_id($url)
{
  return (int) substr(parse_url($url, PHP_URL_PATH), 1);
}


/**
 * get_video_type
 *
 * @param string $url
 * @return string
 */
function get_video_type($url)
{
  if (strpos($url, 'youtube') > 0) {
    return 'youtube';
  } elseif (strpos($url, 'vimeo') > 0) {
    return 'vimeo';
  } else {
    return 'link';
  }
}


/**
 * get_array_key
 *
 * @param array $array
 * @param integer $current
 * @param integer $offset
 * @return mixed
 */
function get_array_key($array, $current, $offset = 1)
{
  $keys = array_keys($array);
  $index = array_search($current, $keys);
  if (isset($keys[$index + $offset])) {
    return $keys[$index + $offset];
  }
  return false;
}


/**
 * print_money
 *
 * @param string $amount
 * @param string $symbol
 * @param string $dir
 * @return string
 */

function print_money($amount, $symbol = null, $dir = null)
{
  global $system;
  $symbol = ($symbol) ? $symbol : $system['system_currency_symbol'];
  $dir = ($dir) ? $dir : $system['system_currency_dir'];
  if ($dir == "right") {
    return $amount . $symbol;
  } else {
    return $symbol . $amount;
  }
}
