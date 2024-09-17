{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} sg-offcanvas">
  <div class="row">

    <!-- side panel -->
    <div class="col-12 d-block d-md-none sg-offcanvas-sidebar mt20">
      {include file='_sidebar.tpl'}
    </div>
    <!-- side panel -->

    <!-- content panel -->
    <div class="col-12 sg-offcanvas-mainbar">
      <!-- profile-header -->
      <div class="profile-header">
        <!-- profile-cover -->
        <div class="profile-cover-wrapper">
          {if $spage['page_cover_id']}
            <!-- full-cover -->
            <img class="js_position-cover-full x-hidden" src="{$spage['page_cover_full']}">
            <!-- full-cover -->

            <!-- cropped-cover -->
            <img class="js_position-cover-cropped js_lightbox" data-init-position="{$spage['page_cover_position']}" data-id="{$spage['page_cover_id']}" data-image="{$spage['page_cover_full']}" data-context="album" src="{$spage['page_cover']}" alt="{$spage['page_title']}">
            <!-- cropped-cover -->
          {/if}

          {if $spage['i_admin']}
            <!-- buttons -->
            <div class="profile-cover-buttons">
              <div class="profile-cover-change">
                <i class="fa fa-camera" data-bs-toggle="dropdown" data-display="static"></i>
                <div class="dropdown-menu action-dropdown-menu">
                  <!-- upload -->
                  <div class="dropdown-item pointer js_x-uploader" data-handle="cover-page" data-id="{$spage['page_id']}">
                    <div class="action">
                      {include file='__svg_icons.tpl' icon="camera" class="main-icon mr10" width="20px" height="20px"}
                      {__("Upload Photo")}
                    </div>
                    <div class="action-desc">{__("Upload a new photo")}</div>
                  </div>
                  <!-- upload -->
                  <!-- select -->
                  <div class="dropdown-item pointer" data-toggle="modal" data-url="users/photos.php?filter=cover&type=page&id={$spage['page_id']}">
                    <div class="action">
                      {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="20px" height="20px"}
                      {__("Select Photo")}
                    </div>
                    <div class="action-desc">{__("Select a photo")}</div>
                  </div>
                  <!-- select -->
                </div>
              </div>
              <div class="profile-cover-position {if !$spage['page_cover']}x-hidden{/if}">
                <input class="js_position-picture-val" type="hidden" name="position-picture-val">
                <i class="fa fa-crop-alt js_init-position-picture" data-handle="page" data-id="{$spage['page_id']}"></i>
              </div>
              <div class="profile-cover-position-buttons">
                <i class="fa fa-check fa-fw js_save-position-picture"></i>
              </div>
              <div class="profile-cover-position-buttons">
                <i class="fa fa-times fa-fw js_cancel-position-picture"></i>
              </div>
              <div class="profile-cover-delete {if !$spage['page_cover']}x-hidden{/if}">
                <i class="fa fa-trash js_delete-cover" data-handle="cover-page" data-id="{$spage['page_id']}"></i>
              </div>
            </div>
            <!-- buttons -->

            <!-- loaders -->
            <div class="profile-cover-change-loader">
              <div class="progress x-progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="profile-cover-position-loader">
              <i class="fa fa-arrows-alt mr5"></i>{__("Drag to reposition cover")}
            </div>
            <!-- loaders -->
          {/if}
        </div>
        <!-- profile-cover -->

        <!-- profile-avatar -->
        <div class="profile-avatar-wrapper">
          <img {if !$spage['page_picture_default']} class="js_lightbox" data-id="{$spage['page_picture_id']}" data-image="{$spage['page_picture_full']}" data-context="album" {/if} src="{$spage['page_picture']}" alt="{$spage['page_title']}">

          {if $spage['i_admin']}
            <!-- buttons -->
            <div class="profile-avatar-change">
              <i class="fa fa-camera" data-bs-toggle="dropdown" data-display="static"></i>
              <div class="dropdown-menu action-dropdown-menu">
                <!-- upload -->
                <div class="dropdown-item pointer js_x-uploader" data-handle="picture-page" data-id="{$spage['page_id']}">
                  <div class="action">
                    {include file='__svg_icons.tpl' icon="camera" class="main-icon mr10" width="20px" height="20px"}
                    {__("Upload Photo")}
                  </div>
                  <div class="action-desc">{__("Upload a new photo")}</div>
                </div>
                <!-- upload -->
                <!-- select -->
                <div class="dropdown-item pointer" data-toggle="modal" data-url="users/photos.php?filter=avatar&type=page&id={$spage['page_id']}">
                  <div class="action">
                    {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="20px" height="20px"}
                    {__("Select Photo")}
                  </div>
                  <div class="action-desc">{__("Select a photo")}</div>
                </div>
                <!-- select -->
              </div>
            </div>
            <div class="profile-avatar-crop {if $spage['page_picture_default']}x-hidden{/if}">
              <i class="fa fa-crop-alt js_init-crop-picture" data-image="{$spage['page_picture_full']}" data-handle="page" data-id="{$spage['page_id']}"></i>
            </div>
            <div class="profile-avatar-delete {if $spage['page_picture_default']}x-hidden{/if}">
              <i class="fa fa-trash js_delete-picture" data-handle="picture-page" data-id="{$spage['page_id']}"></i>
            </div>
            <!-- buttons -->
            <!-- loaders -->
            <div class="profile-avatar-change-loader">
              <div class="progress x-progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <!-- loaders -->
          {/if}
        </div>
        <!-- profile-avatar -->

        <!-- profile-name -->
        <div class="profile-name-wrapper">
          <a href="{$system['system_url']}/pages/{$spage['page_name']}">{$spage['page_title']}</a>
          {if $spage['page_verified']}
            <span class="verified-badge" data-bs-toggle="tooltip" title='{__("Verified Page")}'>
              {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
            </span>
          {/if}
        </div>
        <!-- profile-name -->

        <!-- profile-buttons -->
        <div class="profile-buttons-wrapper">
          <!-- like -->
          {if $spage['i_like']}
            <button type="button" class="btn btn-md rounded-pill btn-primary js_unlike-page" data-id="{$spage['page_id']}">
              <i class="fa fa-heart"></i>
              <span class="d-none d-xxl-inline-block ml5">{__("Unlike")}</span>
            </button>
          {else}
            <button type="button" class="btn btn-md rounded-pill btn-primary js_like-page" data-id="{$spage['page_id']}">
              <i class="fa fa-heart"></i>
              <span class="d-none d-xxl-inline-block ml5">{__("Like")}</span>
            </button>
          {/if}
          <!-- like -->

          <!-- custom button -->
          {if $spage['page_action_text'] && $spage['page_action_url']}
            <a target="_blank" href="{$spage['page_action_url']}" class="btn btn-md rounded-pill btn-{$spage['page_action_color']}">{$spage['page_action_text']}</a>
          {/if}
          <!-- custom button -->

          <!-- boost -->
          {if $system['packages_enabled'] && $spage['i_admin']}
            {if $user->_data['can_boost_pages']}
              {if $spage['page_boosted']}
                <button type="button" class="btn btn-md rounded-pill btn-danger js_unboost-page" data-id="{$spage['page_id']}">
                  <i class="fa fa-bolt"></i>
                  <span class="d-none d-xxl-inline-block ml5">{__("Unboost")}</span>
                </button>
              {else}
                <button type="button" class="btn btn-md rounded-pill btn-danger js_boost-page" data-id="{$spage['page_id']}">
                  <i class="fa fa-bolt"></i>
                  <span class="d-none d-xxl-inline-block ml5">{__("Boost")}</span>
                </button>
              {/if}
            {else}
              <a href="{$system['system_url']}/packages" class="btn btn-md rounded-pill btn-danger">
                <i class="fa fa-bolt"></i>
                <span class="d-none d-xxl-inline-block ml5">{__("Boost Page")}</span>
              </a>
            {/if}
          {/if}
          <!-- boost -->

          <!-- review -->
          {if $system['pages_reviews_enabled']}
            {if !$spage['i_admin']}
              <button type="button" class="btn btn-md rounded-pill btn-light" data-toggle="modal" data-url="modules/review.php?do=review&id={$spage['page_id']}&type=page">
                <i class="fa fa-star"></i>
                <span class="d-none d-xxl-inline-block ml5">{__("Review")}</span>
              </button>
            {/if}
          {/if}
          <!-- review -->

          <!-- report menu -->
          <div class="d-inline-block dropdown ml5">
            <button type="button" class="btn btn-icon rounded-pill btn-light" data-bs-toggle="dropdown" data-display="static">
              <i class="fa fa-ellipsis-v fa-fw"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end action-dropdown-menu">
              <!-- share -->
              <div class="dropdown-item pointer" data-toggle="modal" data-url="modules/share.php?node_type=page&node_username={$spage['page_name']}">
                <div class="action">
                  {include file='__svg_icons.tpl' icon="share" class="main-icon mr10" width="20px" height="20px"}
                  {__("Share")}
                </div>
                <div class="action-desc">{__("Share this page")}</div>
              </div>
              <!-- share -->
              {if $user->_logged_in && !$spage['i_admin']}
                <!-- report -->
                <div class="dropdown-item pointer" data-toggle="modal" data-url="data/report.php?do=create&handle=page&id={$spage['page_id']}">
                  <div class="action">
                    {include file='__svg_icons.tpl' icon="report" class="main-icon mr10" width="20px" height="20px"}
                    {__("Report")}
                  </div>
                  <div class="action-desc">{__("Report this to admins")}</div>
                </div>
                <!-- report -->
                <!-- manage -->
                {if $user->_is_admin}
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{$system['system_url']}/admincp/pages/edit_page/{$spage['page_id']}">
                    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr10" width="20px" height="20px"}
                    {__("Edit in Admin Panel")}
                  </a>
                {elseif $user->_is_moderator}
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{$system['system_url']}/modcp/pages/edit_page/{$spage['page_id']}">
                    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr10" width="20px" height="20px"}
                    {__("Edit in Moderator Panel")}
                  </a>
                {/if}
                <!-- manage -->
              {/if}
            </div>
          </div>
          <!-- report menu -->
        </div>
        <!-- profile-buttons -->
      </div>
      <!-- profile-header -->

      <!-- profile-tabs -->
      <div class="profile-tabs-wrapper d-flex justify-content-evenly">
        <a href="{$system['system_url']}/pages/{$spage['page_name']}" {if $view == ""}class="active" {/if}>
          {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon mr5" width="24px" height="24px"}
          <span class="ml5 d-none d-xl-inline-block">{__("Timeline")}</span>
        </a>
        <a href="{$system['system_url']}/pages/{$spage['page_name']}/photos" {if $view == "photos" || $view == "albums" || $view == "album"}class="active" {/if}>
          {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
          <span class="ml5 d-none d-xl-inline-block">{__("Photos")}</span>
        </a>
        {if $system['videos_enabled']}
          <a href="{$system['system_url']}/pages/{$spage['page_name']}/videos" {if $view == "videos"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="videos" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Videos")}</span>
          </a>
        {/if}
        {if $system['pages_reviews_enabled']}
          <a href="{$system['system_url']}/pages/{$spage['page_name']}/reviews" {if $view == "reviews"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="star" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Reviews")} {if $spage['page_rate']}<span class="badge bg-light text-primary">{$spage['page_rate']|number_format:1}</span>{/if}</span>
          </a>
        {/if}
        {if $system['events_enabled'] && $system['pages_events_enabled']}
          <a href="{$system['system_url']}/pages/{$spage['page_name']}/events" {if $view == "events"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="events" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Events")}</span>
          </a>
        {/if}
        {if $spage['i_like']}
          <a href="{$system['system_url']}/pages/{$spage['page_name']}/invites" {if $view == "invites"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Invite Friends")}</span>
          </a>
        {/if}
        {if $spage['i_admin']}
          <a href="{$system['system_url']}/pages/{$spage['page_name']}/settings" {if $view == "settings"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="settings" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Settings")}</span>
          </a>
        {/if}
      </div>
      <!-- profile-tabs -->

      <!-- profile-content -->
      <div class="row">
        <!-- view content -->
        {if $view == ""}

          <!-- left panel -->
          <div class="col-lg-4 order-2 order-lg-1">
            {include file='_ads.tpl'}

            <!-- tips -->
            {if $user->_logged_in && !$spage['i_admin'] && $spage['can_receive_tips']&& $spage['page_tips_enabled']}
              <div class="d-grid">
                <button type="button" class="btn bg-red rounded-pill mb20" data-toggle="modal" data-url="#send-tip" data-options='{ "id": "{$spage['page_admin']}"}'>
                  {include file='__svg_icons.tpl' icon="tip" class="white-icon mr5" width="24px" height="24px"}
                  {__("Send a Tip")}
                </button>
              </div>
            {/if}
            <!-- tips -->

            <!-- panel [about] -->
            <div class="card">
              <div class="card-body">
                {if !is_empty($spage['page_description'])}
                  <div class="about-bio">
                    <div class="js_readmore overflow-hidden">
                      {$spage['page_description']|nl2br}
                    </div>
                  </div>
                {/if}
                <ul class="about-list">
                  <!-- likes -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="friends" class="main-icon" width="24px" height="24px"}
                      {$spage['page_likes']} {__("people like this")}
                    </div>
                  </li>
                  <!-- likes -->
                  <!-- posts -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon" width="24px" height="24px"}
                      {__($spage['posts_count'])} {__("Posts")}
                    </div>
                  </li>
                  <!-- posts -->
                  <!-- photos -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="photos" class="main-icon" width="24px" height="24px"}
                      {__($spage['photos_count'])} {__("Photos")}
                    </div>
                  </li>
                  <!-- photos -->
                  {if $system['videos_enabled']}
                    <!-- videos -->
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="videos" class="main-icon" width="24px" height="24px"}
                        {__($spage['videos_count'])} {__("Videos")}
                      </div>
                    </li>
                    <!-- videos -->
                  {/if}
                  {if $system['pages_reviews_enabled']}
                    <!-- reviews -->
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="star" class="main-icon" width="24px" height="24px"}
                        {__($spage['reviews_count'])} {__("Reviews")}
                        {if $spage['page_rate']}
                          <span class="review-stars small ml5">
                            <i class="fa fa-star {if $spage['page_rate'] >= 1}checked{/if}"></i>
                            <i class="fa fa-star {if $spage['page_rate'] >= 2}checked{/if}"></i>
                            <i class="fa fa-star {if $spage['page_rate'] >= 3}checked{/if}"></i>
                            <i class="fa fa-star {if $spage['page_rate'] >= 4}checked{/if}"></i>
                            <i class="fa fa-star {if $spage['page_rate'] >= 5}checked{/if}"></i>
                          </span>
                          <span class="badge bg-light text-primary">{$spage['page_rate']|number_format:1}</span>
                        {/if}
                      </div>
                    </li>
                    <!-- reviews -->
                  {/if}
                  <!-- category -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="tag" class="main-icon" width="24px" height="24px"}
                      {__($spage['page_category_name'])}
                    </div>
                  </li>
                  <!-- category -->
                  <!-- info -->
                  {if $spage['page_company']}
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="jobs" class="main-icon" width="24px" height="24px"}
                        {$spage['page_company']}
                      </div>
                    </li>
                  {/if}
                  {if $spage['page_phone']}
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="call_audio" class="main-icon" width="24px" height="24px"}
                        {$spage['page_phone']}
                      </div>
                    </li>
                  {/if}
                  {if $spage['page_website']}
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="website" class="main-icon" width="24px" height="24px"}
                        <a target="_blank" href="{$spage['page_website']}">{$spage['page_website']}</a>
                      </div>
                    </li>
                  {/if}
                  {if $spage['page_location']}
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="map" class="main-icon" width="24px" height="24px"}
                        {$spage['page_location']}
                      </div>
                    </li>
                    {if $system['geolocation_enabled']}
                      <div style="margin-left: -20px; margin-right: -20px;">
                        <iframe width="100%" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key={$system['geolocation_key']}&amp;q={$spage['page_location']}&amp;language=en"></iframe>
                      </div>
                    {/if}
                  {/if}
                  <!-- info -->
                </ul>
              </div>
            </div>
            <!-- panel [about] -->

            <!-- custom fields [basic] -->
            {if $custom_fields['basic']}
              <div class="card">
                <div class="card-header bg-transparent">
                  {include file='__svg_icons.tpl' icon="info" class="main-icon mr5" width="24px" height="24px"}
                  <strong>{__("Info")}</strong>
                </div>
                <div class="card-body">
                  <ul class="about-list">
                    {foreach $custom_fields['basic'] as $custom_field}
                      {if $custom_field['value']}
                        <li>
                          <strong>{__($custom_field['label'])}</strong><br>
                          {if $custom_field['type'] == "textbox" && $custom_field['is_link']}
                            <a href="{$custom_field['value']}">{$custom_field['value']}</a>
                          {elseif $custom_field['type'] == "multipleselectbox"}
                            {$custom_field['value_string']}
                          {else}
                            {$custom_field['value']}
                          {/if}
                        </li>
                      {/if}
                    {/foreach}
                  </ul>
                </div>
              </div>
            {/if}
            <!-- custom fields [basic] -->

            <!-- social links -->
            {if $spage['page_social_facebook'] || $spage['page_social_twitter'] || $spage['page_social_youtube'] || $spage['page_social_instagram'] || $spage['page_social_linkedin'] || $spage['page_social_vkontakte']}
              <div class="card">
                <div class="card-header bg-transparent">
                  {include file='__svg_icons.tpl' icon="social_share" class="main-icon mr5" width="24px" height="24px"}
                  <strong>{__("Social Links")}</strong>
                </div>
                <div class="card-body text-center">
                  {if $spage['page_social_facebook']}
                    <a target="_blank" href="{$spage['page_social_facebook']}" class="btn-icon-social">
                      {include file='__svg_icons.tpl' icon="facebook" width="24px" height="24px"}
                    </a>
                  {/if}
                  {if $spage['page_social_twitter']}
                    <a target="_blank" href="{$spage['page_social_twitter']}" class="btn-icon-social">
                      {include file='__svg_icons.tpl' icon="twitter" width="24px" height="24px"}
                    </a>
                  {/if}
                  {if $spage['page_social_youtube']}
                    <a target="_blank" href="{$spage['page_social_youtube']}" class="btn-icon-social">
                      {include file='__svg_icons.tpl' icon="youtube" width="24px" height="24px"}
                    </a>
                  {/if}
                  {if $spage['page_social_instagram']}
                    <a target="_blank" href="{$spage['page_social_instagram']}" class="btn-icon-social">
                      {include file='__svg_icons.tpl' icon="instagram" width="24px" height="24px"}
                    </a>
                  {/if}
                  {if $spage['page_social_twitch']}
                    <a target="_blank" href="{$spage['page_social_twitch']}" class="btn-icon-social">
                      {include file='__svg_icons.tpl' icon="twitch" width="24px" height="24px"}
                    </a>
                  {/if}
                  {if $spage['page_social_linkedin']}
                    <a target="_blank" href="{$spage['page_social_linkedin']}" class="btn-icon-social">
                      {include file='__svg_icons.tpl' icon="linkedin" width="24px" height="24px"}
                    </a>
                  {/if}
                  {if $spage['page_social_vkontakte']}
                    <a target="_blank" href="{$spage['page_social_vkontakte']}" class="btn-icon-social">
                      {include file='__svg_icons.tpl' icon="vk" width="24px" height="24px"}
                    </a>
                  {/if}
                </div>
              </div>
            {/if}
            <!-- social links -->

            <!-- photos -->
            {if $spage['photos']}
              <div class="card panel-photos">
                <div class="card-header bg-transparent">
                  {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/pages/{$spage['page_name']}/photos">{__("Photos")}</a></strong>
                </div>
                <div class="card-body">
                  <div class="row">
                    {foreach $spage['photos'] as $photo}
                      {include file='__feeds_photo.tpl' _context="photos" _small=true}
                    {/foreach}
                  </div>
                </div>
              </div>
            {/if}
            <!-- photos -->

            <!-- subscribers -->
            {if $spage['subscribers_count'] > 0}
              <div class="card">
                <div class="card-header bg-transparent">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/pages/{$spage['page_name']}/subscribers">{__("Subscribers")}</a></strong>
                  <span class="badge rounded-pill bg-info ml5">{$spage['subscribers_count']}</span>
                </div>
                <div class="card-body ptb10 plr10">
                  <div class="row">
                    {foreach $spage['subscribers'] as $_subscriber}
                      <div class="col-3 col-lg-4">
                        <div class="circled-user-box">
                          <a class="user-box" href="{$system['system_url']}/{$_subscriber['user_name']}">
                            <img src="{$_subscriber['user_picture']}" />
                            <div class="name">
                              {if $system['show_usernames_enabled']}{$_subscriber['user_name']}{else}{$_subscriber['user_firstname']} {$_subscriber['user_lastname']}{/if}
                            </div>
                          </a>
                        </div>
                      </div>
                    {/foreach}
                  </div>
                </div>
              </div>
            {/if}
            <!-- subscribers -->

            <!-- invite friends -->
            {if $spage['i_like'] && $spage['invites']}
              <div class="card">
                <div class="card-header bg-transparent">
                  <div class="float-end">
                    <small><a href="{$system['system_url']}/pages/{$spage['page_name']}/invites">{__("See All")}</a></small>
                  </div>
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/pages/{$spage['page_name']}/invites">{__("Invite Friends")}</a></strong>
                </div>
                <div class="card-body">
                  <ul>
                    {foreach $spage['invites'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"] _small=true}
                    {/foreach}
                  </ul>
                </div>
              </div>
            {/if}
            <!-- invite friends -->

            <!-- page events -->
            {if $spage['events']}
              <div class="card">
                <div class="card-header bg-transparent">
                  <div class="float-end">
                    <small><a href="{$system['system_url']}/pages/{$spage['page_name']}/events">{__("See All")}</a></small>
                  </div>
                  {include file='__svg_icons.tpl' icon="events" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/pages/{$spage['page_name']}/events">{__("Events")}</a></strong>
                </div>
                <div class="card-body with-list">
                  <ul>
                    {foreach $spage['events'] as $_event}
                      {include file='__feeds_event.tpl' _tpl="list" _small=true}
                    {/foreach}
                  </ul>
                </div>
              </div>
            {/if}
            <!-- page events -->

            <!-- mini footer -->
            {include file='_footer_mini.tpl'}
            <!-- mini footer -->
          </div>
          <!-- left panel -->

          <!-- right panel -->
          <div class="col-lg-8 order-1 order-lg-2">

            <!-- publisher -->
            {if $spage['i_admin']}
              {include file='_publisher.tpl' _handle="page" _id=$spage['page_id'] _avatar=$spage['page_picture'] _node_can_monetize_content=$spage['can_monetize_content'] _node_monetization_enabled=$spage['page_monetization_enabled'] _node_monetization_plans=$spage['page_monetization_plans']}
            {/if}
            <!-- publisher -->

            <!-- pinned post -->
            {if $pinned_post}
              {include file='_pinned_post.tpl' post=$pinned_post}
            {/if}
            <!-- pinned post -->

            <!-- posts -->
            {include file='_posts.tpl' _get="posts_page" _id=$spage['page_id']}
            <!-- posts -->

          </div>
          <!-- right panel -->

        {elseif $view == "photos"}
          <!-- photos -->
          <div class="col-12">
            {if $spage['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='page' node_id=$spage['page_id'] price=$spage['page_monetization_min_price']}
            {else}
              <div class="card panel-photos">
                <div class="card-header with-icon with-nav">
                  <!-- panel title -->
                  <div class="mb20">
                    {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="24px" height="24px"}
                    {__("Photos")}
                  </div>
                  <!-- panel title -->

                  <!-- panel nav -->
                  <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <a class="nav-link active" href="{$system['system_url']}/pages/{$spage['page_name']}/photos">{__("Photos")}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/pages/{$spage['page_name']}/albums">{__("Albums")}</a>
                    </li>
                  </ul>
                  <!-- panel nav -->
                </div>
                <div class="card-body">
                  {if $spage['photos']}
                    <ul class="row">
                      {foreach $spage['photos'] as $photo}
                        {include file='__feeds_photo.tpl' _context="photos"}
                      {/foreach}
                    </ul>
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="photos" data-id="{$spage['page_id']}" data-type='page'>
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {else}
                    <p class="text-center text-muted mt10">
                      {$spage['page_title']} {__("doesn't have photos")}
                    </p>
                  {/if}
                </div>
              </div>
            {/if}
          </div>
          <!-- photos -->

        {elseif $view == "albums"}
          <!-- albums -->
          <div class="col-12">
            {if $spage['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='page' node_id=$spage['page_id'] price=$spage['page_monetization_min_price']}
            {else}
              <div class="card">
                <div class="card-header with-icon with-nav">
                  <!-- panel title -->
                  <div class="mb20">
                    {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="24px" height="24px"}
                    {__("Photos")}
                  </div>
                  <!-- panel title -->

                  <!-- panel nav -->
                  <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/pages/{$spage['page_name']}/photos">{__("Photos")}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="{$system['system_url']}/pages/{$spage['page_name']}/albums">{__("Albums")}</a>
                    </li>
                  </ul>
                  <!-- panel nav -->
                </div>
                <div class="card-body">
                  {if $spage['albums']}
                    <ul class="row">
                      {foreach $spage['albums'] as $album}
                        {include file='__feeds_album.tpl'}
                      {/foreach}
                    </ul>
                    {if count($spage['albums']) >= $system['max_results_even']}
                      <!-- see-more -->
                      <div class="alert alert-post see-more js_see-more" data-get="albums" data-id="{$spage['page_id']}" data-type='page'>
                        <span>{__("See More")}</span>
                        <div class="loader loader_small x-hidden"></div>
                      </div>
                      <!-- see-more -->
                    {/if}
                  {else}
                    <p class="text-center text-muted mt10">
                      {$spage['page_title']} {__("doesn't have albums")}
                    </p>
                  {/if}
                </div>
              </div>
            {/if}
          </div>
          <!-- albums -->

        {elseif $view == "album"}
          <!-- albums -->
          <div class="col-12">
            {if $spage['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='page' node_id=$spage['page_id'] price=$spage['page_monetization_min_price']}
            {else}
              <div class="card panel-photos">
                <div class="card-header with-icon with-nav">
                  <!-- back to albums -->
                  <div class="float-end">
                    <a href="{$system['system_url']}/pages/{$spage['page_name']}/albums" class="btn btn-md btn-light">
                      <i class="fa fa-arrow-circle-left mr5"></i>{__("Back to Albums")}
                    </a>
                  </div>
                  <!-- back to albums -->

                  <!-- panel title -->
                  <div class="mb20">
                    {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="24px" height="24px"}
                    {__("Photos")}
                  </div>
                  <!-- panel title -->

                  <!-- panel nav -->
                  <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/pages/{$spage['page_name']}/photos">{__("Photos")}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="{$system['system_url']}/pages/{$spage['page_name']}/albums">{__("Albums")}</a>
                    </li>
                  </ul>
                  <!-- panel nav -->
                </div>
                <div class="card-body">
                  {include file='_album.tpl'}
                </div>
              </div>
            {/if}
          </div>
          <!-- albums -->

        {elseif $view == "videos"}
          <!-- videos -->
          <div class="col-12">
            {if $spage['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='page' node_id=$spage['page_id'] price=$spage['page_monetization_min_price']}
            {else}
              <div class="card panel-videos">
                <div class="card-header with-icon">
                  <!-- panel title -->
                  <div class="mb20">
                    {include file='__svg_icons.tpl' icon="videos" class="main-icon mr10" width="24px" height="24px"}
                    {__("Videos")}
                  </div>
                  <!-- panel title -->
                </div>
                <div class="card-body">
                  {if $spage['videos']}
                    <ul class="row">
                      {foreach $spage['videos'] as $video}
                        {include file='__feeds_video.tpl'}
                      {/foreach}
                    </ul>
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="videos" data-id="{$spage['page_id']}" data-type='page'>
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {else}
                    <p class="text-center text-muted mt10">
                      {$spage['page_title']} {__("doesn't have videos")}
                    </p>
                  {/if}
                </div>
              </div>
            {/if}
          </div>
          <!-- videos -->

        {elseif $view == "reviews"}
          <!-- reviews -->
          <div class="col-12">
            <div class="card">
              <div class="card-header with-icon">
                <!-- panel title -->
                <div>
                  {include file='__svg_icons.tpl' icon="star" class="main-icon mr10" width="24px" height="24px"}
                  {__("Reviews")}
                  {if $spage['page_rate']}
                    <span class="review-stars small ml5">
                      <i class="fa fa-star {if $spage['page_rate'] >= 1}checked{/if}"></i>
                      <i class="fa fa-star {if $spage['page_rate'] >= 2}checked{/if}"></i>
                      <i class="fa fa-star {if $spage['page_rate'] >= 3}checked{/if}"></i>
                      <i class="fa fa-star {if $spage['page_rate'] >= 4}checked{/if}"></i>
                      <i class="fa fa-star {if $spage['page_rate'] >= 5}checked{/if}"></i>
                    </span>
                    <span class="badge bg-light text-primary">{$spage['page_rate']|number_format:1}</span>
                  {/if}
                </div>
                <!-- panel title -->
              </div>
              <div class="card-body pb0">
                {if $spage['reviews_count'] > 0}
                  <ul class="row">
                    {foreach $spage['reviews'] as $_review}
                      {include file='__feeds_review.tpl' _darker=true}
                    {/foreach}
                  </ul>
                  {if $spage['reviews_count'] >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more mt0 mb20 js_see-more" data-get="reviews" data-id="{$spage['page_id']}" data-type="page">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {$spage['page_title']} {__("doesn't have reviews")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- reviews -->

        {elseif $view == "events"}
          <!-- events -->
          <div class="col-12">
            <div class="card">
              <div class="card-header with-icon">
                <!-- panel title -->
                {include file='__svg_icons.tpl' icon="events" class="main-icon mr10" width="24px" height="24px"}
                {__("Events")}
                <!-- panel title -->

                {if $spage['i_admin'] && $system['events_enabled'] && $system['pages_events_enabled']}
                  <div class="float-end">
                    <button class="btn btn-md btn-primary d-none d-lg-block" data-toggle="modal" data-url="modules/add.php?type=event&page_id={$spage['page_id']}">
                      <i class="fa fa-plus-circle mr5"></i>{__("Create Event")}
                    </button>
                    <button class="btn btn-sm btn-icon btn-success d-block d-lg-none" data-toggle="modal" data-url="modules/add.php?type=event&page_id={$spage['page_id']}">
                      <i class="fa fa-plus-circle"></i>
                    </button>
                  </div>
                {/if}
              </div>
              <div class="card-body">
                {if $spage['events']}
                  <ul class="row">
                    {foreach $spage['events'] as $_event}
                      {include file='__feeds_event.tpl' _tpl="box" _darker=true}
                    {/foreach}
                  </ul>

                  {if count($spage['events']) >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="page_events" data-id="{$spage['page_id']}">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  {include file='_no_data.tpl'}
                {/if}
              </div>
            </div>
          </div>
          <!-- events -->

        {elseif $view == "subscribers"}
          <!-- subscribers -->
          <div class="col-12">
            <div class="card">
              <div class="card-header with-icon">
                <!-- panel title -->
                <div>
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Subscribers")}
                </div>
                <!-- panel title -->
              </div>
              <div class="card-body pb0">
                {if $spage['subscribers_count'] > 0}
                  <ul class="row">
                    {foreach $spage['subscribers'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="box" _connection=$_user["connection"] _darker=true}
                    {/foreach}
                  </ul>
                  {if count($spage['subscribers']) >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more mt0 mb20 js_see-more" data-get="subscribers" data-uid="{$spage['page_id']}" data-type="page">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {$spage['page_title']} {__("doesn't have subscribers")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- subscribers -->

        {elseif $view == "invites"}
          <!-- invites -->
          <div class="col-12">
            <div class="card">
              <div class="card-header with-icon">
                <!-- panel title -->
                <div>
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Invites")}
                </div>
                <!-- panel title -->
              </div>
              <div class="card-body">
                {if $spage['invites']}
                  <ul class="row">
                    {foreach $spage['invites'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="box" _connection=$_user["connection"] _darker=true}
                    {/foreach}
                  </ul>

                  {if count($spage['invites']) >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="page_invites" data-id="{$spage['page_id']}">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {__("No friends to invite")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- invites -->

        {elseif $view == "settings"}
          <div class="col-lg-3">
            <div class="card">
              <div class="card-body with-nav">
                <ul class="side-nav">
                  <li {if $sub_view == ""}class="active" {/if}>
                    <a href="{$system['system_url']}/pages/{$spage['page_name']}/settings">
                      {include file='__svg_icons.tpl' icon="settings" class="main-icon mr10" width="24px" height="24px"}
                      {__("Page Settings")}
                    </a>
                  </li>
                  <li {if $sub_view == "info"}class="active" {/if}>
                    <a href="{$system['system_url']}/pages/{$spage['page_name']}/settings/info">
                      {include file='__svg_icons.tpl' icon="info" class="main-icon mr10" width="24px" height="24px"}
                      {__("Page Information")}
                    </a>
                  </li>
                  <li {if $sub_view == "admins"}class="active" {/if}>
                    <a href="{$system['system_url']}/pages/{$spage['page_name']}/settings/admins">
                      {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                      {__("Admins")}
                    </a>
                  </li>
                  {if $user->_data['can_monetize_content']}
                    <li {if $sub_view == "monetization"}class="active" {/if}>
                      <a href="{$system['system_url']}/pages/{$spage['page_name']}/settings/monetization">
                        {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="24px" height="24px"}
                        {__("Monetization")}
                      </a>
                    </li>
                  {/if}
                  {if $system['verification_requests']}
                    <li {if $sub_view == "verification"}class="active" {/if}>
                      <a href="{$system['system_url']}/pages/{$spage['page_name']}/settings/verification">
                        {include file='__svg_icons.tpl' icon="verification" class="main-icon mr10" width="24px" height="24px"}
                        {__("Verification")}
                      </a>
                    </li>
                  {/if}
                  {if $user->_data['user_id'] == $spage['page_admin']}
                    <li {if $sub_view == "delete"}class="active" {/if}>
                      <a href="{$system['system_url']}/pages/{$spage['page_name']}/settings/delete">
                        {include file='__svg_icons.tpl' icon="delete" class="main-icon mr10" width="24px" height="24px"}
                        {__("Delete Page")}
                      </a>
                    </li>
                  {/if}
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-9">
            <div class="card">
              {if $sub_view == ""}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="settings" class="main-icon mr10" width="24px" height="24px"}
                  {__("Page Settings")}
                </div>
                <form class="js_ajax-forms" data-url="modules/create.php?type=page&do=edit&edit=settings&id={$spage['page_id']}">
                  <div class="card-body">
                    <div class="form-group">
                      <label class="form-label" for="title">{__("Name Your Page")}</label>
                      <input type="text" class="form-control" name="title" id="title" value="{$spage['page_title']}">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="username">{__("Page Username")}</label>
                      <div class="input-group">
                        <span class="input-group-text d-none d-sm-block">{$system['system_url']}/pages/</span>
                        <input type="text" class="form-control" name="username" id="username" value="{$spage['page_name']}">
                      </div>
                      <div class="form-text">
                        {__("Can only contain alphanumeric characters (A–Z, 0–9) and periods ('.')")}
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="category">{__("Category")}</label>
                      <select class="form-select" name="category" id="category">
                        <option>{__("Select Category")}</option>
                        {foreach $categories as $category}
                          {include file='__categories.recursive_options.tpl' data_category=$spage['page_category']}
                        {/foreach}
                      </select>
                    </div>
                    {if $user->_data['can_receive_tip']}
                      <div class="divider"></div>
                      <div class="form-table-row">
                        <div class="avatar">
                          {include file='__svg_icons.tpl' icon="tip" class="main-icon" width="40px" height="40px"}
                        </div>
                        <div>
                          <div class="form-label h6">{__("Tips Enabled")}</div>
                          <div class="form-text d-none d-sm-block">{__("Allow the send tips button on your page")}</div>
                        </div>
                        <div class="text-end">
                          <label class="switch" for="page_tips_enabled">
                            <input type="checkbox" name="page_tips_enabled" id="page_tips_enabled" {if $spage['page_tips_enabled']}checked{/if}>
                            <span class="slider round"></span>
                          </label>
                        </div>
                      </div>
                    {/if}

                    <!-- error -->
                    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
                    <!-- error -->
                  </div>
                  <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                  </div>
                </form>

              {elseif $sub_view == "info"}
                <div class="card-header with-icon with-nav">
                  <!-- panel title -->
                  <div class="mb20">
                    {include file='__svg_icons.tpl' icon="info" class="main-icon mr10" width="24px" height="24px"}
                    {__("Page Information")}
                  </div>
                  <!-- panel title -->

                  <!-- panel nav -->
                  <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <a class="nav-link active" href="#basic" data-bs-toggle="tab">
                        <i class="fa fa-flag fa-fw mr5"></i><strong class="pr5">{__("Basic")}</strong>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#action" data-bs-toggle="tab">
                        <i class="fa fa-magic fa-fw mr5"></i><strong class="pr5">{__("Action Button")}</strong>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#social" data-bs-toggle="tab">
                        <i class="fab fa-facebook fa-fw mr5"></i><strong class="pr5">{__("Social Links")}</strong>
                      </a>
                    </li>
                  </ul>
                  <!-- panel nav -->
                </div>

                <!-- tab-content -->
                <div class="tab-content">
                  <!-- basic tab -->
                  <div class="tab-pane active" id="basic">
                    <form class="js_ajax-forms" data-url="modules/create.php?type=page&do=edit&edit=info&id={$spage['page_id']}">
                      <div class="card-body">
                        <div class="row">
                          <div class="form-group col-md-6">
                            <label class="form-label" for="company">{__("Company")}</label>
                            <input type="text" class="form-control" name="company" id="company" value="{$spage['page_company']}">
                          </div>
                          <div class="form-group col-md-6">
                            <label class="form-label" for="phone">{__("Phone")}</label>
                            <input type="text" class="form-control" name="phone" id="phone" value="{$spage['page_phone']}">
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group col-md-6">
                            <label class="form-label" for="website">{__("Website")}</label>
                            <input type="text" class="form-control" name="website" id="website" value="{$spage['page_website']}">
                            <div class="form-text">
                              {__("Website link must start with http:// or https://")}
                            </div>
                          </div>
                          <div class="form-group col-md-6">
                            <label class="form-label" for="location">{__("Location")}</label>
                            <input type="text" class="form-control js_geocomplete" name="location" id="location" value="{$spage['page_location']}">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="country">{__("Country")}</label>
                          <select class="form-select" name="country">
                            <option value="none">{__("Select Country")}</option>
                            {foreach $countries as $country}
                              <option value="{$country['country_id']}" {if $spage['page_country'] == $country['country_id']}selected{/if}>{$country['country_name']}</option>
                            {/foreach}
                          </select>
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="description">{__("About")}</label>
                          <textarea class="form-control" name="description" id="description">{$spage['page_description']}</textarea>
                        </div>
                        <!-- custom fields -->
                        {if $custom_fields['basic']}
                          {include file='__custom_fields.tpl' _custom_fields=$custom_fields['basic'] _registration=false}
                        {/if}
                        <!-- custom fields -->

                        <!-- error -->
                        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
                        <!-- error -->
                      </div>
                      <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                      </div>
                    </form>
                  </div>
                  <!-- basic tab -->

                  <!-- action tab -->
                  <div class="tab-pane" id="action">
                    <form class="js_ajax-forms" data-url="modules/create.php?type=page&do=edit&edit=action&id={$spage['page_id']}">
                      <div class="card-body">
                        <div class="form-group">
                          <label class="form-label">{__("Action Button Text")}</label>
                          <input type="text" class="form-control" name="action_text" id="action_text" value="{$spage['page_action_text']}">
                          <div class="form-text">
                            {__("For example: Subscribe, Get tickets, Preorder now or Shop now")}
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label">{__("Action Button Color")}</label>
                          <div class="mt10">
                            <div class="form-check form-check-inline">
                              <input type="radio" name="action_color" id="action_color_light" value="light" class="form-check-input" {if $spage['page_action_color'] == "light"}checked{/if}>
                              <label class="form-check-label" for="action_color_light">
                                <button type="button" class="btn btn-sm btn-light">{__("Action")}</button>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input type="radio" name="action_color" id="action_color_primary" value="primary" class="form-check-input" {if $spage['page_action_color'] == "primary"}checked{/if}>
                              <label class="form-check-label" for="action_color_primary">
                                <button type="button" class="btn btn-sm btn-primary">{__("Action")}</button>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input type="radio" name="action_color" id="action_color_success" value="success" class="form-check-input" {if $spage['page_action_color'] == "success"}checked{/if}>
                              <label class="form-check-label" for="action_color_success">
                                <button type="button" class="btn btn-sm btn-success">{__("Action")}</button>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input type="radio" name="action_color" id="action_color_info" value="info" class="form-check-input" {if $spage['page_action_color'] == "info"}checked{/if}>
                              <label class="form-check-label" for="action_color_info">
                                <button type="button" class="btn btn-sm btn-info">{__("Action")}</button>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input type="radio" name="action_color" id="action_color_warning" value="warning" class="form-check-input" {if $spage['page_action_color'] == "warning"}checked{/if}>
                              <label class="form-check-label" for="action_color_warning">
                                <button type="button" class="btn btn-sm btn-warning">{__("Action")}</button>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input type="radio" name="action_color" id="action_color_danger" value="danger" class="form-check-input" {if $spage['page_action_color'] == "danger"}checked{/if}>
                              <label class="form-check-label" for="action_color_danger">
                                <button type="button" class="btn btn-sm btn-danger">{__("Action")}</button>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label">{__("Action Button URL")}</label>
                          <input type="text" class="form-control" name="action_url" id="action_url" value="{$spage['page_action_url']}">
                        </div>

                        <!-- error -->
                        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
                        <!-- error -->
                      </div>
                      <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                      </div>
                    </form>
                  </div>
                  <!-- action tab -->

                  <!-- social tab -->
                  <div class="tab-pane" id="social">
                    <form class="js_ajax-forms" data-url="modules/create.php?type=page&do=edit&edit=social&id={$spage['page_id']}">
                      <div class="card-body">
                        <div class="row">
                          <div class="form-group col-md-6">
                            <label class="form-label">{__("Facebook Profile URL")}</label>
                            <div class="input-group">
                              <span class="input-group-text bg-transparent">
                                {include file='__svg_icons.tpl' icon="facebook" width="22px" height="22px"}
                              </span>
                              <input type="text" class="form-control" name="facebook" value="{$spage['page_social_facebook']}">
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label class="form-label">{__("Twitter Profile URL")}</label>
                            <div class="input-group">
                              <span class="input-group-text bg-transparent">
                                {include file='__svg_icons.tpl' icon="twitter" width="22px" height="22px"}
                              </span>
                              <input type="text" class="form-control" name="twitter" value="{$spage['page_social_twitter']}">
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label class="form-label">{__("YouTube Profile URL")}</label>
                            <div class="input-group">
                              <span class="input-group-text bg-transparent">
                                {include file='__svg_icons.tpl' icon="youtube" width="22px" height="22px"}
                              </span>
                              <input type="text" class="form-control" name="youtube" value="{$spage['page_social_youtube']}">
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label class="form-label">{__("Instagram Profile URL")}</label>
                            <div class="input-group">
                              <span class="input-group-text bg-transparent">
                                {include file='__svg_icons.tpl' icon="instagram" width="22px" height="22px"}
                              </span>
                              <input type="text" class="form-control" name="instagram" value="{$spage['page_social_instagram']}">
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label class="form-label">{__("LinkedIn Profile URL")}</label>
                            <div class="input-group">
                              <span class="input-group-text bg-transparent">
                                {include file='__svg_icons.tpl' icon="linkedin" width="22px" height="22px"}
                              </span>
                              <input type="text" class="form-control" name="linkedin" value="{$spage['page_social_linkedin']}">
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label class="form-label">{__("Vkontakte Profile URL")}</label>
                            <div class="input-group">
                              <span class="input-group-text bg-transparent">
                                {include file='__svg_icons.tpl' icon="vk" width="22px" height="22px"}
                              </span>
                              <input type="text" class="form-control" name="vkontakte" value="{$spage['page_social_vkontakte']}">
                            </div>
                          </div>
                        </div>

                        <!-- error -->
                        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
                        <!-- error -->
                      </div>
                      <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                      </div>
                    </form>
                  </div>
                  <!-- social tab -->
                </div>
                <!-- tab-content -->

              {elseif $sub_view == "admins"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Members")}
                </div>
                <div class="card-body">
                  <!-- admins -->
                  <div class="heading-small mb20">
                    {__("Admins")} <span class="text-muted">({$spage['page_admins_count']})</span>
                  </div>
                  <div class="pl-md-4">
                    {if $spage['page_admins']}
                      <ul>
                        {foreach $spage['page_admins'] as $_user}
                          {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"]}
                        {/foreach}
                      </ul>

                      {if $spage['page_admins_count'] >= $system['max_results_even']}
                        <!-- see-more -->
                        <div class="alert alert-post see-more js_see-more" data-get="page_admins" data-id="{$spage['page_id']}">
                          <span>{__("See More")}</span>
                          <div class="loader loader_small x-hidden"></div>
                        </div>
                        <!-- see-more -->
                      {/if}
                    {else}
                      <p class="text-center text-muted mt10">
                        {$spage['page_title']} {__("doesn't have admins")}
                      </p>
                    {/if}
                  </div>
                  <!-- admins -->

                  <div class="divider"></div>

                  <!-- members -->
                  <div class="heading-small mb20">
                    {__("All Members")} <span class="text-muted">({$spage['page_likes']})</span>
                  </div>
                  <div class="pl-md-4">
                    {if $spage['page_likes'] > 0}
                      <ul>
                        {foreach $spage['members'] as $_user}
                          {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"]}
                        {/foreach}
                      </ul>

                      {if $spage['page_likes'] >= $system['max_results_even']}
                        <!-- see-more -->
                        <div class="alert alert-post see-more js_see-more" data-get="page_members" data-id="{$spage['page_id']}">
                          <span>{__("See More")}</span>
                          <div class="loader loader_small x-hidden"></div>
                        </div>
                        <!-- see-more -->
                      {/if}
                    {else}
                      <p class="text-center text-muted mt10">
                        {$spage['page_title']} {__("doesn't have members")}
                      </p>
                    {/if}
                  </div>
                  <!-- members -->
                </div>

              {elseif $sub_view == "monetization"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="24px" height="24px"}
                  {__("Monetization")}
                </div>
                <div class="card-body">
                  <div class="alert alert-info">
                    <div class="text">
                      <strong>{__("Monetization")}</strong><br>
                      {__("Now you can earn money from your content. Via paid posts or subscriptions plans.")}
                      <br>
                      {if $system['monetization_commission'] > 0}
                        {__("There is commission")} <strong><span class="badge rounded-pill bg-warning">{$system['monetization_commission']}%</span></strong> {__("will be deducted")}.
                        <br>
                      {/if}
                      {if $system['monetization_money_withdraw_enabled']}
                        {__("You can")} <a class="alert-link" href="{$system['system_url']}/settings/monetization/payments" target="_blank">{__("withdraw your money")}</a>
                      {/if}
                      {if $system['monetization_money_transfer_enabled']}
                        {if $system['monetization_money_withdraw_enabled']}{__("or")} {/if}
                        {__("You can transfer your money to your")} <a class="alert-link" href="{$system['system_url']}/wallet" target="_blank"><i class="fa fa-wallet"></i> {__("wallet")}</a>
                      {/if}
                    </div>
                  </div>

                  <div class="alert alert-info">
                    <div class="icon">
                      <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="text pt5">
                      {__("Only super admin can manage monetization and money goes to his monetization money balance")}.
                    </div>
                  </div>

                  <div class="heading-small mb20">
                    {__("Monetization Settings")}
                  </div>
                  <div class="pl-md-4">
                    <form class="js_ajax-forms" data-url="modules/create.php?type=page&do=edit&edit=monetization&id={$spage['page_id']}">
                      <div class="form-table-row">
                        <div class="avatar">
                          {include file='__svg_icons.tpl' icon="monetization" class="main-icon" width="40px" height="40px"}
                        </div>
                        <div>
                          <div class="form-label h6">{__("Monetization")}</div>
                          <div class="form-text d-none d-sm-block">{__("Enable or disable monetization for your content")}</div>
                        </div>
                        <div class="text-end">
                          <label class="switch" for="page_monetization_enabled">
                            <input type="checkbox" name="page_monetization_enabled" id="page_monetization_enabled" {if $spage['page_monetization_enabled']}checked{/if}>
                            <span class="slider round"></span>
                          </label>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="col-md-3 form-label">
                          {__("Subscriptions Plans")}
                        </label>
                        <div class="col-md-9">
                          <div class="payment-plans">
                            {foreach $monetization_plans as $plan}
                              <div class="payment-plan">
                                <div class="text-xxlg">{__($plan['title'])}</div>
                                <div class="text-xlg">{print_money($plan['price'])} / {if $plan['period_num'] != '1'}{$plan['period_num']}{/if} {__($plan['period']|ucfirst)}</div>
                                {if {$plan['custom_description']}}
                                  <div>{$plan['custom_description']}</div>
                                {/if}
                                <div class="mt10">
                                  <span class="text-link mr10 js_monetization-deleter" data-id="{$plan['plan_id']}">
                                    <i class="fa fa-trash-alt mr5"></i>{__("Delete")}
                                  </span>
                                  |
                                  <span data-toggle="modal" data-url="monetization/controller.php?do=edit&id={$plan['plan_id']}" class="text-link ml10">
                                    <i class="fa fa-pen mr5"></i>{__("Edit")}
                                  </span>
                                </div>
                              </div>
                            {/foreach}
                            <div data-toggle="modal" data-url="monetization/controller.php?do=add&node_id={$spage['page_id']}&node_type=page" class="payment-plan new">{__("Add new plan")} </div>
                          </div>
                        </div>
                      </div>

                      <div class="form-group row">
                        <div class="col-md-9 offset-md-3">
                          <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                        </div>
                      </div>

                      <!-- success -->
                      <div class="alert alert-success mt15 mb0 x-hidden"></div>
                      <!-- success -->

                      <!-- error -->
                      <div class="alert alert-danger mt15 mb0 x-hidden"></div>
                      <!-- error -->
                    </form>
                  </div>

                  <div class="divider"></div>

                  <div class="heading-small mb20">
                    {__("Monetization Balance")}
                  </div>
                  <div class="pl-md-4">
                    <div class="row">
                      <!-- subscribers -->
                      <div class="col-sm-6">
                        <div class="section-title mb20">
                          {__("Page Subscribers")}
                        </div>
                        <div class="stat-panel bg-gradient-info">
                          <div class="stat-cell">
                            <i class="fa fas fa-users bg-icon"></i>
                            <div class="h3 mtb10">
                              {$subscribers_count}
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- subscribers -->

                      <!-- money balance -->
                      <div class="col-sm-6">
                        <div class="section-title mb20">
                          {__("Monetization Money Balance")}
                        </div>
                        <div class="stat-panel bg-gradient-primary">
                          <div class="stat-cell">
                            <i class="fa fa-donate bg-icon"></i>
                            <div class="h3 mtb10">
                              {print_money($user->_data['user_monetization_balance']|number_format:2)}
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- monetization balance -->
                    </div>
                  </div>
                </div>

              {elseif $sub_view == "verification"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="verification" class="main-icon mr10" width="24px" height="24px"}
                  {__("Verification")}
                </div>
                {if $case == "verified"}
                  <div class="card-body">
                    <div class="text-center">
                      {include file='__svg_icons.tpl' icon="verification" class="main-icon mb10" width="60px" height="60px"}
                      <h4>{__("Congratulations")}</h4>
                      <p class="mt20">{__("This page is verified")}</p>
                    </div>
                  </div>
                {elseif $case == "pending"}
                  <div class="card-body">
                    <div class="text-center">
                      {include file='__svg_icons.tpl' icon="pending" class="main-icon mb10" width="60px" height="60px"}
                      <h4>{__("Pending")}</h4>
                      <p class="mt20">{__("Your verification request is still awaiting admin approval")}</p>
                    </div>
                  </div>
                {elseif $case == "request" || "declined"}
                  <form class="js_ajax-forms" data-url="users/verify.php?node=page&node_id={$spage['page_id']}">
                    <div class="card-body">
                      {if $case == "declined"}
                        <div class="text-center">
                          {include file='__svg_icons.tpl' icon="declined" class="main-icon mb10" width="60px" height="60px"}
                          <h4>{__("Sorry")}</h4>
                          <p class="mt20">{__("Your verification request has been declined by the admin")}</p>
                        </div>
                        <div class="divider"></div>
                      {/if}

                      {if $system['verification_docs_required']}
                        <div class="form-group row">
                          <label class="col-md-3 form-label">
                            {__("Verification Documents")}
                          </label>
                          <div class="col-md-9">
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="section-title mb20">
                                  {__("Company Incorporation File")}
                                </div>
                                <div class="x-image full">
                                  <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'></button>
                                  <div class="x-image-loader">
                                    <div class="progress x-progress">
                                      <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </div>
                                  <i class="fa fa-camera fa-2x js_x-uploader" data-handle="x-image"></i>
                                  <input type="hidden" class="js_x-image-input" name="photo" value="">
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="section-title mb20">
                                  {__("Company Tax File")}
                                </div>
                                <div class="x-image full">
                                  <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

                                  </button>
                                  <div class="x-image-loader">
                                    <div class="progress x-progress">
                                      <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </div>
                                  <i class="fa fa-camera fa-2x js_x-uploader" data-handle="x-image"></i>
                                  <input type="hidden" class="js_x-image-input" name="passport" value="">
                                </div>
                              </div>
                            </div>
                            <div class="form-text">
                              {__("Upload your company incorporation file and tax file")}
                            </div>
                          </div>
                        </div>
                      {/if}

                      <div class="form-group row">
                        <label class="col-md-3 form-label">
                          {__("Business Website")}
                        </label>
                        <div class="col-md-9">
                          <input type="text" class="form-control" name="business_website">
                          <div class="form-text">
                            {__("Enter your business website")}
                          </div>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="col-md-3 form-label">
                          {__("Business Address")}
                        </label>
                        <div class="col-md-9">
                          <textarea class="form-control" name="business_address"></textarea>
                          <div class="form-text">
                            {__("Enter your business address")}
                          </div>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="col-md-3 form-label">
                          {__("Additional Information")}
                        </label>
                        <div class="col-md-9">
                          <textarea class="form-control" name="message"></textarea>
                          <div class="form-text">
                            {__("Please share why your account should be verified")}
                          </div>
                        </div>
                      </div>

                      <!-- success -->
                      <div class="alert alert-success mt15 mb0 x-hidden"></div>
                      <!-- success -->

                      <!-- error -->
                      <div class="alert alert-danger mt15 mb0 x-hidden"></div>
                      <!-- error -->
                    </div>
                    <div class="card-footer text-end">
                      <button type="submit" class="btn btn-primary">
                        {__("Send")}
                      </button>
                    </div>
                  </form>

                {/if}

              {elseif $sub_view == "delete"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="delete" class="main-icon mr10" width="24px" height="24px"}
                  {__("Delete Page")}
                </div>
                <div class="card-body">
                  <div class="alert alert-warning">
                    <div class="icon">
                      <i class="fa fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="text pt5">
                      {__("Once you delete your page you will no longer can access it again")}
                    </div>
                  </div>

                  <div class="text-center">
                    <button class="btn btn-danger js_delete-page" data-id="{$spage['page_id']}">
                      {__("Delete Page")}
                    </button>
                  </div>
                </div>

              {/if}
            </div>
          </div>

        {/if}
        <!-- view content -->
      </div>
      <!-- profile-content -->

      <!-- footer links -->
      {if $view != ""}
        {include file='_footer.links.tpl'}
      {/if}
      <!-- footer links -->
    </div>
    <!-- content panel -->

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}