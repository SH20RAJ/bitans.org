<?php

/**
 * connect
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../bootstrap.php');

try {

  switch ($_REQUEST['do']) {
    case 'connect':
      // check if social login enabled
      if (!$system['social_login_enabled']) {
        _error(404);
      }

      // check if user cancelled the connection
      if (isset($_GET['error']) || isset($_GET['denied'])) {
        redirect();
      }

      // check provider
      switch ($_REQUEST['provider']) {
        case 'facebook':
          if (!$system['facebook_login_enabled']) {
            _error(404);
          }
          break;

        case 'google':
          if (!$system['google_login_enabled']) {
            _error(404);
          }
          break;

        case 'twitter':
          if (!$system['twitter_login_enabled']) {
            _error(404);
          }
          break;

        case 'linkedin':
          if (!$system['linkedin_login_enabled']) {
            _error(404);
          }
          break;

        case 'vkontakte':
          if (!$system['vkontakte_login_enabled']) {
            _error(404);
          }
          break;

        case 'wordpress':
          if (!$system['wordpress_login_enabled']) {
            _error(404);
          }
          break;

        case 'sngine':
          if (!$system['sngine_login_enabled']) {
            _error(404);
          }
          break;

        default:
          _error(404);
          break;
      }

      // set provider
      $provider = $_REQUEST["provider"];

      if ($provider != "sngine") {

        // config hybridauth
        $config = [
          'callback' => $system['system_url'] . "/connect/" . $provider,
          "providers" => [
            "Facebook" => [
              "enabled" => true,
              "keys"    => ["id" => $system['facebook_appid'], "secret" => $system['facebook_secret']],
              "scope"   => "email, public_profile",
              "trustForwarded" => false
            ],
            "Google" => [
              "enabled" => true,
              "keys"    => ["key" => $system['google_appid'], "secret" => $system['google_secret']],
            ],
            "Twitter" => [
              "enabled" => true,
              "keys"    => ["key" => $system['twitter_appid'], "secret" => $system['twitter_secret']],
              "includeEmail" => true
            ],
            "Instagram" => [
              "enabled" => true,
              "keys"    => ["id" => $system['instagram_appid'], "secret" => $system['instagram_secret']],
            ],
            "LinkedIn" => [
              "enabled" => true,
              "keys"    => ["id" => $system['linkedin_appid'], "secret" => $system['linkedin_secret']],
            ],
            "Vkontakte" => [
              "enabled" => true,
              "keys"    => ["id" => $system['vkontakte_appid'], "secret" => $system['vkontakte_secret']],
            ],
            "WordPress" => [
              "enabled" => true,
              "keys"    => ["id" => $system['wordpress_appid'], "secret" => $system['wordpress_secret']],
            ],
          ],
        ];

        // initialize Hybrid_Auth with a given file
        $hybridauth = new Hybridauth\Hybridauth($config);

        // try to authenticate with the selected provider
        $adapter = $hybridauth->authenticate($provider);

        // then grab the user profile
        $user_profile = $adapter->getUserProfile();

        // socail login
        $user->social_signin($provider, $user_profile);

        // disconnect
        $adapter->disconnect();
      } else {

        // get access token
        $app_id = $system['sngine_appid'];
        $app_secret = $system['sngine_secret'];
        $app_url = $system['sngine_app_domain'];
        $auth_key = $_GET['auth_key'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$app_url/api/authorize?app_id=$app_id&app_secret=$app_secret&auth_key=$auth_key");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $responseJson = json_decode($response, true);
        if ($responseJson['error']) {
          throw new Exception($responseJson['message']);
        }

        // get user profile
        $access_token = $responseJson['access_token'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$app_url/api/get_user_info?access_token=$access_token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $responseJson = json_decode($response, true);
        if ($responseJson['error']) {
          throw new Exception($responseJson['message']);
        }

        // create new user profile object
        $user_info = $responseJson['user_info'];
        $user_profile = [];
        $user_profile['identifier'] = $user_info['user_id'];
        $user_profile['firstName'] = $user_info['user_firstname'];
        $user_profile['lastName'] = $user_info['user_lastname'];
        $user_profile['username'] = $user_info['user_name'];
        $user_profile['displayName'] = $user_info['user_firstname'] . " " . $user_info['user_lastname'];
        $user_profile['email'] = $user_info['user_email'];
        $user_profile['photoURL'] = $user_info['profile_picture'];
        $user_profile = (object) $user_profile;

        // socail login
        $user->social_signin($provider, $user_profile);
      }
      break;

    case 'revoke':
      // user access
      user_access();

      // check provider
      switch ($_REQUEST['provider']) {
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
          _error(404);
          break;
      }

      // revoke
      $db->query(sprintf("UPDATE users SET $social_connected = '0', $social_id = NULL WHERE user_id = %s", secure($user->_data['user_id'], 'int'))) or _error('SQL_ERROR_THROWEN');
      redirect('/settings/linked');
      break;

    default:
      _error(404);
      break;
  }
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}
