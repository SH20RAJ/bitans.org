{if !$user->_logged_in}

  <body data-hash-tok="{$session_hash['token']}" data-hash-pos="{$session_hash['position']}" class="{if $system['theme_mode_night']}night-mode{/if} visitor n_chat {if $page == 'index' && !$system['newsfeed_public']}index-body{/if}" {if $page == 'profile' && $system['system_profile_background_enabled'] && $profile['user_profile_background']}style="background: url({$profile['user_profile_background']}) fixed !important; background-size: 100% auto;" {/if}>
  {else}

    <body data-hash-tok="{$session_hash['token']}" data-hash-pos="{$session_hash['position']}" data-chat-enabled="{$user->_data['user_chat_enabled']}" class="{if $system['theme_mode_night']}night-mode{/if} {if !$system['chat_enabled']}n_chat{/if}{if $system['activation_enabled'] && !$system['activation_required'] && !$user->_data['user_activated']} n_activated{/if}{if !$system['system_live']} n_live{/if}" {if $page == 'profile' && $system['system_profile_background_enabled'] && $profile['user_profile_background']}style="background: url({$profile['user_profile_background']}) fixed !important; background-size: 100% auto;" {/if} {if $page == "share" && $url}onload="initialize_scraper()" {/if}>
    {/if}
    <!-- main wrapper -->
    <div class="main-wrapper">
      {if $user->_logged_in && $system['activation_enabled'] && !$system['activation_required'] && !$user->_data['user_activated']}
        <!-- top-bar -->
        <div class="top-bar">
          <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
            <div class="row">
              <div class="col-sm-7 d-none d-sm-block">
                {if $system['activation_type'] == "email"}
                  {__("Please go to")} <span class="text-primary">{$user->_data['user_email']}</span> {__("to complete the activation process")}.
                {else}
                  {__("Please check the SMS on your phone")} <strong>{$user->_data['user_phone']}</strong> {__("to complete the activation process")}.
                {/if}
              </div>
              <div class="col-sm-5">
                {if $system['activation_type'] == "email"}
                  <span class="text-link" data-toggle="modal" data-url="core/activation_email_resend.php">
                    {__("Resend Verification Email")}
                  </span>
                  -
                  <span class="text-link" data-toggle="modal" data-url="#activation-email-reset">
                    {__("Change Email")}
                  </span>
                {else}
                  <span class="btn btn-info btn-sm mr10" data-toggle="modal" data-url="#activation-phone">{__("Enter Code")}</span>
                  {if $user->_data['user_phone']}
                    <span class="text-link" data-toggle="modal" data-url="core/activation_phone_resend.php">
                      {__("Resend SMS")}
                    </span>
                    -
                  {/if}
                  <span class="text-link" data-toggle="modal" data-url="#activation-phone-reset">
                    {__("Change Phone Number")}
                  </span>
                {/if}
              </div>
            </div>
          </div>
        </div>
        <!-- top-bar -->
      {/if}

      {if !$system['system_live']}
        <!-- top-bar alert-->
        <div class="top-bar danger">
          <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
            <i class="fa fa-exclamation-triangle fa-lg pr5"></i>
            <span class="d-none d-sm-inline">{__("The system has been shutted down")}.</span>
            <span>{__("Turn it on from")}</span> <a href="{$system['system_url']}/admincp/settings">{__("Admin Panel")}</a>
          </div>
        </div>
        <!-- top-bar alert-->
      {/if}

      {if $user->_login_as}
        <!-- bottom-bar alert-->
        <div class="bottom-bar">
          <div class="container logged-as-container">
            <i class="fa fa-exclamation-triangle fa-lg pr5"></i>
            <span>{__("You are currently logged in as")} <a href="{$system['system_url']}/{$user->_data['user_name']}">{$user->_data['name']}</a></span>
            <button class="btn btn-sm btn-warning ml20 js_login-as" data-handle="revoke">
              <i class="fa-solid fa-arrow-rotate-left mr5"></i>{__("Switch Back")}
            </button>
          </div>
        </div>
        <!-- bottom-bar alert-->
      {/if}

      <!-- main-header -->
      {if $page != "index" || ($user->_logged_in || $system['newsfeed_public'])}
        <div class="main-header" {if $system['fluid_design']}style="padding-right: 0;" {/if}>
          <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
            <div class="row">

              <div class="{if !$user->_logged_in}col-6{/if} col-md-4 col-lg-3 {if $user->_logged_in}d-none d-md-block{/if}">
                <!-- logo-wrapper -->
                <div class="logo-wrapper">

                  {if !$user->_logged_in && $system['newsfeed_public']}
                    <!-- menu-icon -->
                    <a href="#" data-bs-toggle="sg-offcanvas" class="menu-icon d-block d-md-none">
                      {include file='__svg_icons.tpl' icon="header-menu" class="header-icon" width="20px" height="20px"}
                    </a>
                    <!-- menu-icon -->
                  {/if}

                  <!-- logo -->
                  <a href="{$system['system_url']}" class="logo {if !$user->_logged_in && $system['newsfeed_public']}with-menu-icon{/if}">
                    {if $system['system_logo']}
                      <img class="logo-light img-fluid" src="{$system['system_uploads']}/{$system['system_logo']}" alt="{__($system['system_title'])}" title="{__($system['system_title'])}">
                      {if !$system['system_logo_dark']}
                        <img class="logo-dark img-fluid" src="{$system['system_uploads']}/{$system['system_logo']}" alt="{__($system['system_title'])}" title="{__($system['system_title'])}">
                      {else}
                        <img class="logo-dark img-fluid" src="{$system['system_uploads']}/{$system['system_logo_dark']}" alt="{$system['system_title']}" title="{__($system['system_title'])}">
                      {/if}
                    {else}
                      {__($system['system_title'])}
                    {/if}
                  </a>
                  <!-- logo -->

                  {if $user->_logged_in}
                    <!-- home-icon -->
                    <a href="{$system['system_url']}" class="home-icon">
                      {include file='__svg_icons.tpl' icon="header-home" class="header-icon" width="24px" height="24px" }
                    </a>
                    <!-- home-icon -->
                  {/if}

                </div>
                <!-- logo-wrapper -->
              </div>

              <div class="{if !$user->_logged_in}col-6{/if} col-md-8 col-lg-9">
                <div class="row">
                  <div class="col-md-7 col-lg-8">
                    <!-- search-wrapper -->
                    {if $user->_logged_in || (!$user->_logged_in && $system['system_public']) }
                      {include file='_header.search.tpl'}
                    {/if}
                    <!-- search-wrapper -->
                  </div>
                  <div class="col-md-5 col-lg-4">
                    <!-- navbar-wrapper -->
                    <div class="navbar-wrapper">
                      <ul>
                        {if $user->_logged_in}
                          <!-- bars -->
                          <li class="d-block d-md-none">
                            <a href="#" data-bs-toggle="sg-offcanvas">
                              {include file='__svg_icons.tpl' icon="header-menu" class="header-icon" width="20px" height="20px"}
                            </a>
                          </li>
                          <!-- bars -->

                          <!-- home -->
                          <li class="d-block d-md-none">
                            <a href="{$system['system_url']}">
                              {include file='__svg_icons.tpl' icon="header-home" class="header-icon" width="24px" height="24px"}
                            </a>
                          </li>
                          <!-- home -->

                          {if $user->_data['can_add_stories'] || $user->_data['can_write_articles'] || $user->_data['can_sell_products'] || $user->_data['can_raise_funding'] || $user->_data['can_create_ads'] || $user->_data['can_create_pages'] || $user->_data['can_create_groups'] || $user->_data['can_create_events']}
                            <!-- add -->
                            <li class="d-none d-xxl-block dropdown">
                              <a href="#" data-bs-toggle="dropdown" data-display="static">
                                {include file='__svg_icons.tpl' icon="header-plus" class="header-icon" width="24px" height="24px"}
                              </a>
                              <div class="dropdown-menu dropdown-menu-end">
                                {if $user->_data['can_add_stories']}
                                  <div class="dropdown-item pointer" data-toggle="modal" data-url="posts/story.php?do=create">
                                    {include file='__svg_icons.tpl' icon="24_hours" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Story")}
                                  </div>
                                {/if}
                                {if $user->_data['can_write_articles']}
                                  <a class="dropdown-item" href="{$system['system_url']}/blogs/new">
                                    {include file='__svg_icons.tpl' icon="articles" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Blog")}
                                  </a>
                                {/if}
                                {if $user->_data['can_sell_products']}
                                  <div class="dropdown-item pointer" data-toggle="modal" data-url="posts/product.php?do=create">
                                    {include file='__svg_icons.tpl' icon="products" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Product")}
                                  </div>
                                {/if}
                                {if $user->_data['can_raise_funding']}
                                  <div class="dropdown-item pointer" data-toggle="modal" data-url="posts/funding.php?do=create">
                                    {include file='__svg_icons.tpl' icon="funding" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Funding")}
                                  </div>
                                {/if}
                                {if $user->_data['can_create_ads']}
                                  <a class="dropdown-item" href="{$system['system_url']}/ads/new">
                                    {include file='__svg_icons.tpl' icon="ads" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Ads")}
                                  </a>
                                {/if}
                                {if $user->_data['can_create_pages']}
                                  <div class="dropdown-item pointer" data-toggle="modal" data-url="modules/add.php?type=page">
                                    {include file='__svg_icons.tpl' icon="pages" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Page")}
                                  </div>
                                {/if}
                                {if $user->_data['can_create_groups']}
                                  <div class="dropdown-item pointer" data-toggle="modal" data-url="modules/add.php?type=group">
                                    {include file='__svg_icons.tpl' icon="groups" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Group")}
                                  </div>
                                {/if}
                                {if $user->_data['can_create_events']}
                                  <div class="dropdown-item pointer" data-toggle="modal" data-url="modules/add.php?type=event">
                                    {include file='__svg_icons.tpl' icon="events" class="main-icon mr10" width="24px" height="24px"}
                                    {__("Create Event")}
                                  </div>
                                {/if}
                              </div>
                            </li>
                            <!-- add -->
                          {/if}

                          <!-- friend requests -->
                          {include file='_header.friend_requests.tpl'}
                          <!-- friend requests -->

                          <!-- messages -->
                          {include file='_header.messages.tpl'}
                          <!-- messages -->

                          <!-- notifications -->
                          {include file='_header.notifications.tpl'}
                          <!-- notifications -->

                          <!-- search -->
                          <li class="d-block d-md-none">
                            <a href="{$system['system_url']}/search">
                              {include file='__svg_icons.tpl' icon="header-search" class="header-icon" width="24px" height="24px"}
                            </a>
                          </li>
                          <!-- search -->

                          <!-- user-menu -->
                          <li class="dropdown">
                            <a href="#" class="dropdown-toggle user-menu" data-bs-toggle="dropdown" data-display="static">
                              <img src="{$user->_data['user_picture']}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                              <a class="dropdown-item" href="{$system['system_url']}/{$user->_data['user_name']}">
                                <img class="rounded-circle mr10" src="{$user->_data.user_picture}" width="20px" height="20px">
                                <span>{$user->_data['name']}</span>
                              </a>
                              {if $system['switch_accounts_enabled']}
                                <div class="dropdown-item pointer" data-toggle="modal" data-url="#account-switcher">
                                  {include file='__svg_icons.tpl' icon="accounts_switcher" class="main-icon mr10" width="20px" height="20px"}
                                  {__("Switch Accounts")}
                                </div>
                              {/if}
                              <div class="dropdown-divider"></div>
                              {if $system['packages_enabled'] && !$user->_data['user_subscribed']}
                                <a class="dropdown-item" href="{$system['system_url']}/packages">
                                  {include file='__svg_icons.tpl' icon="membership" class="main-icon mr10" width="20px" height="20px"}
                                  {__("Upgrade to Pro")}
                                </a>
                              {/if}
                              {if $system['points_enabled'] || $system['wallet_enabled']}
                                {if $system['points_enabled']}
                                  <a class="dropdown-item" href="{$system['system_url']}/settings/points">
                                    {include file='__svg_icons.tpl' icon="points" class="main-icon mr10" width="20px" height="20px"}
                                    {__("Points")}: <span class="badge bg-light text-primary">{$user->_data['user_points']}</span>
                                  </a>
                                {/if}
                                {if $system['wallet_enabled']}
                                  <a class="dropdown-item" href="{$system['system_url']}/wallet">
                                    {include file='__svg_icons.tpl' icon="wallet" class="main-icon mr10" width="20px" height="20px"}
                                    {__("Wallet")}: <span class="badge bg-light text-primary">{print_money($user->_data['user_wallet_balance']|number_format:2)}</span>
                                  </a>
                                {/if}
                                <div class="dropdown-divider"></div>
                              {/if}

                              <a class="dropdown-item" href="{$system['system_url']}/settings">
                                {include file='__svg_icons.tpl' icon="settings" class="main-icon mr10" width="20px" height="20px"}
                                {__("Settings")}
                              </a>
                              {if $user->_is_admin}
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{$system['system_url']}/admincp">
                                  {include file='__svg_icons.tpl' icon="admin_panel" class="main-icon mr10" width="20px" height="20px"}
                                  {__("Admin Panel")}
                                </a>
                              {elseif $user->_is_moderator}
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{$system['system_url']}/modcp">
                                  {include file='__svg_icons.tpl' icon="admin_panel" class="main-icon mr10" width="20px" height="20px"}{__("Moderator Panel")}
                                </a>
                              {/if}
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="{$system['system_url']}/signout/?cache={$secret}">
                                {include file='__svg_icons.tpl' icon="logout" class="main-icon mr10" width="20px" height="20px"}
                                {__("Sign Out")}
                              </a>
                              <div class="dropdown-divider"></div>
                              <div class="dropdown-item pointer d-none d-lg-block" data-toggle="modal" data-url="#keyboard-shortcuts">
                                {include file='__svg_icons.tpl' icon="keyboard" class="main-icon mr10" width="20px" height="20px"}
                                {__("Keyboard Shortcuts")}
                              </div>
                              {if $system['themes'] && count($system['themes']) > 1}
                                <div class="dropdown-item pointer" data-toggle="modal" data-url="#theme-switcher">
                                  {include file='__svg_icons.tpl' icon="themes_switcher" class="main-icon mr10" width="20px" height="20px"}
                                  {__("Theme Switcher")}
                                </div>
                              {/if}
                              {if $system['system_theme_mode_select']}
                                {if $system['theme_mode_night']}
                                  <div class="dropdown-item pointer js_theme-mode" data-mode="day">
                                    {include file='__svg_icons.tpl' icon="dark_light" class="main-icon mr10" width="20px" height="20px"}
                                    <span class="js_theme-mode-text">{__("Day Mode")}</span>
                                  </div>
                                {else}
                                  <div class="dropdown-item pointer js_theme-mode" data-mode="night">
                                    {include file='__svg_icons.tpl' icon="dark_light" class="main-icon mr10" width="20px" height="20px"}
                                    <span class="js_theme-mode-text">{__("Night Mode")}</span>
                                  </div>
                                {/if}
                              {/if}
                            </div>
                          </li>
                          <!-- user-menu -->

                        {else}

                          <li class="dropdown float-end">
                            <a href="#" class="dropdown-toggle user-menu" data-bs-toggle="dropdown" data-display="static">
                              <img src="{$system['system_url']}/content/themes/{$system['theme']}/images/blank_profile_male.png">
                              <span>{__("Join")}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                              <a class="dropdown-item" href="{$system['system_url']}/signin">
                                {include file='__svg_icons.tpl' icon="login" class="main-icon mr10" width="20px" height="20px"}
                                {__("Sign In")}
                              </a>
                              {if $system['registration_enabled']}
                                <a class="dropdown-item" href="{$system['system_url']}/signup">
                                  {include file='__svg_icons.tpl' icon="user_add" class="main-icon mr10" width="20px" height="20px"}
                                  {__("Sign Up")}
                                </a>
                              {/if}
                              {if $user->_logged_in || (!$user->_logged_in && $system['system_public']) }
                                <div class="d-block d-md-none">
                                  <div class="dropdown-divider"></div>
                                  <a class="dropdown-item" href="{$system['system_url']}/search">
                                    {include file='__svg_icons.tpl' icon="search" class="main-icon mr10" width="20px" height="20px"}
                                    {__("Search")}
                                  </a>
                                </div>
                              {/if}
                              {if ($system['themes'] && count($system['themes']) > 1) || $system['system_theme_mode_select']}
                                <div class="dropdown-divider"></div>
                              {/if}
                              {if $system['themes'] && count($system['themes']) > 1}
                                <div class="dropdown-item pointer" data-toggle="modal" data-url="#theme-switcher">
                                  {include file='__svg_icons.tpl' icon="themes_switcher" class="main-icon mr10" width="20px" height="20px"}
                                  {__("Theme Switcher")}
                                </div>
                              {/if}
                              {if $system['system_theme_mode_select']}
                                {if $system['theme_mode_night']}
                                  <div class="dropdown-item pointer js_theme-mode" data-mode="day">
                                    {include file='__svg_icons.tpl' icon="dark_light" class="main-icon mr10" width="20px" height="20px"}
                                    <span class="js_theme-mode-text">{__("Day Mode")}</span>
                                  </div>
                                {else}
                                  <div class="dropdown-item pointer js_theme-mode" data-mode="night">
                                    {include file='__svg_icons.tpl' icon="dark_light" class="main-icon mr10" width="20px" height="20px"}
                                    <span class="js_theme-mode-text">{__("Night Mode")}</span>
                                  </div>
                                {/if}
                              {/if}
                            </div>
                          </li>

                        {/if}
                      </ul>
                    </div>
                    <!-- navbar-wrapper -->
                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>
      {/if}
      <!-- main-header -->

      <!-- ads -->
      {include file='_ads.tpl' _ads=$ads_master['header'] _master=true}
<!-- ads -->