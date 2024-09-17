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
          {if $group['group_cover_id']}
            <!-- full-cover -->
            <img class="js_position-cover-full x-hidden" src="{$group['group_cover_full']}">
            <!-- full-cover -->

            <!-- cropped-cover -->
            <img class="js_position-cover-cropped js_lightbox" data-init-position="{$group['group_cover_position']}" data-id="{$group['group_cover_id']}" data-image="{$group['group_cover_full']}" data-context="album" src="{$group['group_cover']}" alt="{$group['group_title']}">
            <!-- cropped-cover -->
          {/if}

          {if $group['i_admin']}
            <!-- buttons -->
            <div class="profile-cover-buttons">
              <div class="profile-cover-change">
                <i class="fa fa-camera" data-bs-toggle="dropdown" data-display="static"></i>
                <div class="dropdown-menu action-dropdown-menu">
                  <!-- upload -->
                  <div class="dropdown-item pointer js_x-uploader" data-handle="cover-group" data-id="{$group['group_id']}">
                    <div class="action">
                      {include file='__svg_icons.tpl' icon="camera" class="main-icon mr10" width="20px" height="20px"}
                      {__("Upload Photo")}
                    </div>
                    <div class="action-desc">{__("Upload a new photo")}</div>
                  </div>
                  <!-- upload -->
                  <!-- select -->
                  <div class="dropdown-item pointer" data-toggle="modal" data-url="users/photos.php?filter=cover&type=group&id={$group['group_id']}">
                    <div class="action">
                      {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="20px" height="20px"}
                      {__("Select Photo")}
                    </div>
                    <div class="action-desc">{__("Select a photo")}</div>
                  </div>
                  <!-- select -->
                </div>
              </div>
              <div class="profile-cover-position {if !$group['group_cover']}x-hidden{/if}">
                <input class="js_position-picture-val" type="hidden" name="position-picture-val">
                <i class="fa fa-crop-alt js_init-position-picture" data-handle="group" data-id="{$group['group_id']}"></i>
              </div>
              <div class="profile-cover-position-buttons">
                <i class="fa fa-check fa-fw js_save-position-picture"></i>
              </div>
              <div class="profile-cover-position-buttons">
                <i class="fa fa-times fa-fw js_cancel-position-picture"></i>
              </div>
              <div class="profile-cover-delete {if !$group['group_cover']}x-hidden{/if}">
                <i class="fa fa-trash js_delete-cover" data-handle="cover-group" data-id="{$group['group_id']}"></i>
              </div>
            </div>

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
          <img {if $group['group_picture_id']} class="js_lightbox" data-id="{$group['group_picture_id']}" data-context="album" data-image="{$group['group_picture_full']}" {elseif !$group['group_picture_default']} class="js_lightbox-nodata" data-image="{$group['group_picture']}" {/if} src="{$group['group_picture']}" alt="{$group['group_title']}">

          {if $group['i_admin']}
            <!-- buttons -->
            <div class="profile-avatar-change">
              <i class="fa fa-camera" data-bs-toggle="dropdown" data-display="static"></i>
              <div class="dropdown-menu action-dropdown-menu">
                <!-- upload -->
                <div class="dropdown-item pointer js_x-uploader" data-handle="picture-group" data-id="{$group['group_id']}">
                  <div class="action">
                    {include file='__svg_icons.tpl' icon="camera" class="main-icon mr10" width="20px" height="20px"}
                    {__("Upload Photo")}
                  </div>
                  <div class="action-desc">{__("Upload a new photo")}</div>
                </div>
                <!-- upload -->
                <!-- select -->
                <div class="dropdown-item pointer" data-toggle="modal" data-url="users/photos.php?filter=avatar&type=group&id={$group['group_id']}">
                  <div class="action">
                    {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="20px" height="20px"}
                    {__("Select Photo")}
                  </div>
                  <div class="action-desc">{__("Select a photo")}</div>
                </div>
                <!-- select -->
              </div>
            </div>
            <div class="profile-avatar-crop {if $group['group_picture_default']}x-hidden{/if}">
              <i class="fa fa-crop-alt js_init-crop-picture" data-image="{$group['group_picture_full']}" data-handle="group" data-id="{$group['group_id']}"></i>
            </div>
            <div class="profile-avatar-delete {if $group['group_picture_default']}x-hidden{/if}">
              <i class="fa fa-trash js_delete-picture" data-handle="picture-group" data-id="{$group['group_id']}"></i>
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
          <a href="{$system['system_url']}/groups/{$group['group_name']}">{$group['group_title']}</a>
          {if $group['group_privacy'] == "public"}
            <i data-bs-toggle="tooltip" title='{__("Public Group")}' class="fa fa-globe fa-fw privacy-badge"></i>
          {elseif $group['group_privacy'] == "closed"}
            <i data-bs-toggle="tooltip" title='{__("Closed Group")}' class="fa fa-unlock-alt fa-fw privacy-badge"></i>
          {elseif $group['group_privacy'] == "secret"}
            <i data-bs-toggle="tooltip" title='{__("Secret Group")}' class="fa fa-lock fa-fw privacy-badge"></i>
          {/if}
        </div>
        <!-- profile-name -->

        <!-- profile-buttons -->
        <div class="profile-buttons-wrapper">
          <!-- join -->
          {if $group['i_joined'] == "approved"}
            <button type="button" class="btn btn-md btn-success btn-delete rounded-pill js_leave-group" data-id="{$group['group_id']}" data-privacy="{$group['group_privacy']}">
              <i class="fa fa-check"></i>
              <span class="d-none d-xxl-inline-block ml5">{__("Joined")}</span>
            </button>
          {elseif $group['i_joined'] == "pending"}
            <button type="button" class="btn btn-md btn-warning rounded-pill js_leave-group" data-id="{$group['group_id']}" data-privacy="{$group['group_privacy']}">
              <i class="fa fa-clock"></i>
              <span class="d-none d-xxl-inline-block ml5">{__("Pending")}</span>
            </button>
          {else}
            <button type="button" class="btn btn-md btn-success rounded-pill js_join-group" data-id="{$group['group_id']}" data-privacy="{if $group['i_admin']}public{else}{$group['group_privacy']}{/if}">
              <i class="fa fa-user-plus"></i>
              <span class="d-none d-xxl-inline-block ml5">{__("Join")}</span>
            </button>
          {/if}
          <!-- join -->

          <!-- review -->
          {if $system['groups_reviews_enabled']}
            {if !$group['i_admin']}
              <button type="button" class="btn btn-md rounded-pill btn-light" data-toggle="modal" data-url="modules/review.php?do=review&id={$group['group_id']}&type=group">
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
              <div class="dropdown-item pointer" data-toggle="modal" data-url="modules/share.php?node_type=group&node_username={$group['group_name']}">
                <div class="action">
                  {include file='__svg_icons.tpl' icon="share" class="main-icon mr10" width="20px" height="20px"}
                  {__("Share")}
                </div>
                <div class="action-desc">{__("Share this group")}</div>
              </div>
              <!-- share -->
              {if $user->_logged_in && !$group['i_admin']}
                <!-- report -->
                <div class="dropdown-item pointer" data-toggle="modal" data-url="data/report.php?do=create&handle=group&id={$group['group_id']}">
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
                  <a class="dropdown-item" href="{$system['system_url']}/admincp/groups/edit_group/{$group['group_id']}">
                    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr10" width="20px" height="20px"}
                    {__("Edit in Admin Panel")}
                  </a>
                {elseif $user->_is_moderator}
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{$system['system_url']}/modcp/groups/edit_group/{$group['group_id']}">
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
        {if $group['group_privacy'] == "closed" && $group['i_joined'] != "approved" && !$group['i_admin'] && !$user->_is_admin && !$user->_is_moderator}
          <a href="{$system['system_url']}/groups/{$group['group_name']}">
            {include file='__svg_icons.tpl' icon="info" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("About")}</span>
          </a>
        {else}
          <a href="{$system['system_url']}/groups/{$group['group_name']}" {if $view == ""}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Timeline")}</span>
          </a>
          <a href="{$system['system_url']}/groups/{$group['group_name']}/photos" {if $view == "photos" || $view == "albums" || $view == "album"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Photos")}</span>
          </a>
          {if $system['videos_enabled']}
            <a href="{$system['system_url']}/groups/{$group['group_name']}/videos" {if $view == "videos"}class="active" {/if}>
              {include file='__svg_icons.tpl' icon="videos" class="main-icon mr5" width="24px" height="24px"}
              <span class="ml5 d-none d-xl-inline-block">{__("Videos")}</span>
            </a>
          {/if}
          {if $system['groups_reviews_enabled']}
            <a href="{$system['system_url']}/groups/{$group['group_name']}/reviews" {if $view == "reviews"}class="active" {/if}>
              {include file='__svg_icons.tpl' icon="star" class="main-icon mr5" width="24px" height="24px"}
              <span class="ml5 d-none d-xl-inline-block">{__("Reviews")} {if $group['group_rate']}<span class="badge bg-light text-primary">{$group['group_rate']|number_format:1}</span>{/if}</span>
            </a>
          {/if}
          <a href="{$system['system_url']}/groups/{$group['group_name']}/members" {if $view == "members" || $view == "invites"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Members")}</span>
          </a>
          {if $group['i_admin']}
            <a href="{$system['system_url']}/groups/{$group['group_name']}/settings" {if $view == "settings"}class="active" {/if}>
              {include file='__svg_icons.tpl' icon="settings" class="main-icon mr5" width="24px" height="24px"}
              <span class="ml5 d-none d-xl-inline-block">{__("Settings")}</span>
            </a>
          {/if}
        {/if}
      </div>
      <!-- profile-tabs -->

      <!-- profile-content -->
      <div class="row">
        <!-- view content -->
        {if $view == ""}

          <!-- left panel -->
          <div class="{if $group['chatbox_enabled']}col-lg-3 order-3 order-lg-1{else}col-lg-4 order-2 order-lg-1{/if}">
            {include file='_ads.tpl'}

            <!-- panel [about] -->
            <div class="card">
              <div class="card-body">
                {if !is_empty($group['group_description'])}
                  <div class="about-bio">
                    <div class="js_readmore overflow-hidden">
                      {$group['group_description']|nl2br}
                    </div>
                  </div>
                {/if}
                <ul class="about-list">
                  <!-- privacy -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="unhide" class="main-icon" width="24px" height="24px"}
                      {if $group['group_privacy'] == "public"}
                        <i class="fa fa-globe fa-fw"></i>
                        {__("Public Group")}
                      {elseif $group['group_privacy'] == "closed"}
                        <i class="fa fa-unlock-alt fa-fw"></i>
                        {__("Closed Group")}
                      {elseif $group['group_privacy'] == "secret"}
                        <i class="fa fa-lock fa-fw"></i>
                        {__("Secret Group")}
                      {/if}
                    </div>
                  </li>
                  <!-- privacy -->
                  <!-- members -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="friends" class="main-icon" width="24px" height="24px"}
                      <a href="{$system['system_url']}/groups/{$group['group_name']}/members">{$group['group_members']} {__("members")}</a>
                    </div>
                  </li>
                  <!-- members -->
                  <!-- posts -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon" width="24px" height="24px"}
                      {__($group['posts_count'])} {__("Posts")}
                    </div>
                  </li>
                  <!-- posts -->
                  <!-- photos -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="photos" class="main-icon" width="24px" height="24px"}
                      {__($group['photos_count'])} {__("Photos")}
                    </div>
                  </li>
                  <!-- photos -->
                  {if $system['videos_enabled']}
                    <!-- videos -->
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="videos" class="main-icon" width="24px" height="24px"}
                        {__($group['videos_count'])} {__("Videos")}
                      </div>
                    </li>
                    <!-- videos -->
                  {/if}
                  {if $system['groups_reviews_enabled']}
                    <!-- reviews -->
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="star" class="main-icon" width="24px" height="24px"}
                        {__($group['reviews_count'])} {__("Reviews")}
                        {if $group['group_rate']}
                          <span class="review-stars small ml5">
                            <i class="fa fa-star {if $group['group_rate'] >= 1}checked{/if}"></i>
                            <i class="fa fa-star {if $group['group_rate'] >= 2}checked{/if}"></i>
                            <i class="fa fa-star {if $group['group_rate'] >= 3}checked{/if}"></i>
                            <i class="fa fa-star {if $group['group_rate'] >= 4}checked{/if}"></i>
                            <i class="fa fa-star {if $group['group_rate'] >= 5}checked{/if}"></i>
                          </span>
                          <span class="badge bg-light text-primary">{$group['group_rate']|number_format:1}</span>
                        {/if}
                      </div>
                    </li>
                    <!-- reviews -->
                  {/if}
                  <!-- category -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="tag" class="main-icon" width="24px" height="24px"}
                      {__($group['group_category_name'])}
                    </div>
                  </li>
                  <!-- category -->
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

            <!-- subscribers -->
            {if $group['subscribers_count'] > 0}
              <div class="card">
                <div class="card-header bg-transparent">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/groups/{$group['group_name']}/subscribers">{__("Subscribers")}</a></strong>
                  <span class="badge rounded-pill bg-info ml5">{$group['subscribers_count']}</span>
                </div>
                <div class="card-body ptb10 plr10">
                  <div class="row">
                    {foreach $group['subscribers'] as $_subscriber}
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
            {if $group['i_joined'] == "approved" && $group['invites']}
              <div class="card">
                <div class="card-header bg-transparent">
                  <div class="float-end">
                    <small><a href="{$system['system_url']}/groups/{$group['group_name']}/invites">{__("See All")}</a></small>
                  </div>
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/groups/{$group['group_name']}/invites">{__("Invite Friends")}</a></strong>
                </div>
                <div class="card-body">
                  <ul>
                    {foreach $group['invites'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"] _small=true}
                    {/foreach}
                  </ul>
                </div>
              </div>
            {/if}
            <!-- invite friends -->

            <!-- photos -->
            {if $group['photos']}
              <div class="card panel-photos">
                <div class="card-header bg-transparent">
                  {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/groups/{$group['group_name']}/photos">{__("Photos")}</a></strong>
                </div>
                <div class="card-body">
                  <div class="row">
                    {foreach $group['photos'] as $photo}
                      {include file='__feeds_photo.tpl' _context="photos" _small=true}
                    {/foreach}
                  </div>
                </div>
              </div>
            {/if}
            <!-- photos -->

            <!-- mini footer -->
            {include file='_footer_mini.tpl'}
            <!-- mini footer -->
          </div>
          <!-- left panel -->

          <!-- right panel -->
          <div class="{if $group['chatbox_enabled']}col-lg-6 order-1 order-lg-2{else}col-lg-8 order-1 order-lg-2{/if}">

            <!-- super admin alert -->
            {if $user->_data['user_group'] < 3 && ($group['group_privacy'] == "secret" || $group['group_privacy'] == "closed") && ($group['i_joined'] != "approved" && !$group['i_admin']) }
              <div class="alert alert-warning">
                <button type="button" class="btn-close float-end" data-dismiss="alert" aria-label="Close"></button>
                <div class="icon"><i class="fa fa-info-circle fa-2x"></i></div>
                <div class="text align-middle">
                  {__("You can access this as your account is system admin account!")}
                </div>
              </div>
            {/if}
            <!-- super admin alert -->

            {if $get == "posts_group"}
              <!-- group requests -->
              {if $group['i_admin'] && $group['total_requests'] > 0}
                <div class="alert alert-light">
                  <button type="button" class="btn-close float-end" data-dismiss="alert" aria-label="Close"></button>
                  <div class="icon"><i class="fa fa-users fa-lg"></i></div>
                  <div class="text align-middle">
                    <a href="{$system['system_url']}/groups/{$group['group_name']}/settings/requests" class="alert-link">
                      <span class="badge bg-secondary mr5">{$group['total_requests']}</span>{if $group['total_requests'] == 1}{__("person")}{else}{__("persons")}{/if} {__("wants to join this group")}
                    </a>
                  </div>
                </div>
              {/if}
              <!-- group requests -->

              <!-- group pending posts -->
              {if $group['pending_posts'] > 0}
                <div class="alert alert-light">
                  <button type="button" class="btn-close float-end" data-dismiss="alert" aria-label="Close"></button>
                  <div class="icon"><i class="fa fa-comments fa-lg"></i></div>
                  <div class="text align-middle">
                    <a href="?pending" class="alert-link">
                      {if $group['i_admin']}
                        <span class="badge bg-secondary mr5">{$group['pending_posts']}</span>{if $group['pending_posts'] == 1}{__("post")}{else}{__("posts")}{/if} {__("pending needs your approval")}
                      {else}
                        {__("You have")}<span class="badge bg-secondary mlr5">{$group['pending_posts']}</span>{if $group['pending_posts'] == 1}{__("post")}{else}{__("posts")}{/if} {__("pending")}
                      {/if}
                    </a>
                  </div>
                </div>
              {/if}
              <!-- group pending posts -->

              <!-- publisher -->
              {if $group['i_joined'] == "approved" && ($group['group_publish_enabled'] OR (!$group['group_publish_enabled'] && $group['i_admin']))}
                {include file='_publisher.tpl' _handle="group" _id=$group['group_id'] _node_can_monetize_content=$group['can_monetize_content'] _node_monetization_enabled=$group['group_monetization_enabled'] _node_monetization_plans=$group['group_monetization_plans']}
              {/if}
              <!-- publisher -->

              <!-- pinned post -->
              {if $pinned_post}
                {include file='_pinned_post.tpl' post=$pinned_post _get="posts_group"}
              {/if}
              <!-- pinned post -->

              <!-- posts -->
              {include file='_posts.tpl' _get="posts_group" _id=$group['group_id']}
              <!-- posts -->
            {else}
              <!-- posts -->
              {include file='_posts.tpl' _get=$get _id=$group['group_id'] _title=__("Pending Posts")}
              <!-- posts -->
            {/if}
          </div>
          <!-- right panel -->

          <!-- chatbox -->
          {if $group['chatbox_enabled']}
            <div class="col-lg-3 order-2 order-lg-3">
              {include file='_chatbox.tpl' _node_type="group" _node=$group}
            </div>
          {/if}
          <!-- chatbox -->

        {elseif $view == "photos"}
          <!-- photos -->
          <div class="col-12">
            {if $group['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='group' node_id=$group['group_id'] price=$group['group_monetization_min_price']}
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
                      <a class="nav-link active" href="{$system['system_url']}/groups/{$group['group_name']}/photos">{__("Photos")}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/albums">{__("Albums")}</a>
                    </li>
                  </ul>
                  <!-- panel nav -->
                </div>
                <div class="card-body">
                  {if $group['photos']}
                    <ul class="row">
                      {foreach $group['photos'] as $photo}
                        {include file='__feeds_photo.tpl' _context="photos"}
                      {/foreach}
                    </ul>
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="photos" data-id="{$group['group_id']}" data-type='group'>
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {else}
                    <p class="text-center text-muted mt10">
                      {$group['group_title']} {__("doesn't have photos")}
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
            {if $group['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='group' node_id=$group['group_id'] price=$group['group_monetization_min_price']}
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
                      <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/photos">{__("Photos")}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="{$system['system_url']}/groups/{$group['group_name']}/albums">{__("Albums")}</a>
                    </li>
                  </ul>
                  <!-- panel nav -->
                </div>
                <div class="card-body">
                  {if $group['albums']}
                    <ul class="row">
                      {foreach $group['albums'] as $album}
                        {include file='__feeds_album.tpl'}
                      {/foreach}
                    </ul>
                    {if count($group['albums']) >= $system['max_results_even']}
                      <!-- see-more -->
                      <div class="alert alert-post see-more js_see-more" data-get="albums" data-id="{$group['group_id']}" data-type='group'>
                        <span>{__("See More")}</span>
                        <div class="loader loader_small x-hidden"></div>
                      </div>
                      <!-- see-more -->
                    {/if}
                  {else}
                    <p class="text-center text-muted mt10">
                      {$group['group_title']} {__("doesn't have albums")}
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
            {if $group['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='group' node_id=$group['group_id'] price=$group['group_monetization_min_price']}
            {else}
              <div class="card panel-photos">
                <div class="card-header with-icon with-nav">
                  <!-- back to albums -->
                  <div class="float-end">
                    <a href="{$system['system_url']}/groups/{$group['group_name']}/albums" class="btn btn-md btn-light">
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
                      <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/photos">{__("Photos")}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="{$system['system_url']}/groups/{$group['group_name']}/albums">{__("Albums")}</a>
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
            {if $group['needs_subscription']}
              {include file='_need_subscription.tpl' node_type='group' node_id=$group['group_id'] price=$group['group_monetization_min_price']}
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
                  {if $group['videos']}
                    <ul class="row">
                      {foreach $group['videos'] as $video}
                        {include file='__feeds_video.tpl'}
                      {/foreach}
                    </ul>
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="videos" data-id="{$group['group_id']}" data-type='group'>
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {else}
                    <p class="text-center text-muted mt10">
                      {$group['group_title']} {__("doesn't have videos")}
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
                  {if $group['group_rate']}
                    <span class="review-stars small ml5">
                      <i class="fa fa-star {if $group['group_rate'] >= 1}checked{/if}"></i>
                      <i class="fa fa-star {if $group['group_rate'] >= 2}checked{/if}"></i>
                      <i class="fa fa-star {if $group['group_rate'] >= 3}checked{/if}"></i>
                      <i class="fa fa-star {if $group['group_rate'] >= 4}checked{/if}"></i>
                      <i class="fa fa-star {if $group['group_rate'] >= 5}checked{/if}"></i>
                    </span>
                    <span class="badge bg-light text-primary">{$group['group_rate']|number_format:1}</span>
                  {/if}
                </div>
                <!-- panel title -->
              </div>
              <div class="card-body pb0">
                {if $group['reviews_count'] > 0}
                  <ul class="row">
                    {foreach $group['reviews'] as $_review}
                      {include file='__feeds_review.tpl' _darker=true}
                    {/foreach}
                  </ul>
                  {if $group['reviews_count'] >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more mt0 mb20 js_see-more" data-get="reviews" data-id="{$group['group_id']}" data-type="group">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {$group['group_title']} {__("doesn't have reviews")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- reviews -->

        {elseif $view == "members"}
          <!-- members -->
          <div class="col-12">
            <div class="card">
              <div class="card-header with-icon with-nav">
                <!-- panel title -->
                <div class="mb20">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Members")}
                </div>
                <!-- panel title -->

                <!-- panel nav -->
                <ul class="nav nav-tabs">
                  <li class="nav-item">
                    <a class="nav-link active" href="{$system['system_url']}/groups/{$group['group_name']}/members">
                      {__("Members")}
                      <span class="badge rounded-pill bg-info">{$group['group_members']}</span>
                    </a>
                  </li>
                  {if $group['has_subscriptions_plans']}
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/subscribers">{__("Subscribers")}</a>
                    </li>
                  {/if}
                  {if $group['i_joined'] == "approved"}
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/invites">{__("Invites")}</a>
                    </li>
                  {/if}
                </ul>
                <!-- panel nav -->
              </div>
              <div class="card-body">
                {if $group['group_members'] > 0}
                  <ul class="row">
                    {foreach $group['members'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="box" _connection=$_user["connection"] _darker=true}
                    {/foreach}
                  </ul>

                  {if $group['group_members'] >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="group_members" data-id="{$group['group_id']}">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {$group['group_title']} {__("doesn't have members")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- members -->

        {elseif $view == "subscribers"}
          <!-- subscribers -->
          <div class="col-12">
            <div class="card">
              <div class="card-header with-icon with-nav">
                <!-- panel title -->
                <div class="mb20">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Subscribers")}
                </div>
                <!-- panel title -->

                <!-- panel nav -->
                <ul class="nav nav-tabs">
                  <li class="nav-item">
                    <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/members">{__("Members")}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active" href="{$system['system_url']}/groups/{$group['group_name']}/subscribers">
                      {__("Subscribers")}
                      <span class="badge rounded-pill bg-info">{$group['subscribers_count']}</span>
                    </a>
                  </li>
                  {if $group['i_joined'] == "approved"}
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/invites">{__("Invites")}</a>
                    </li>
                  {/if}
                </ul>
                <!-- panel nav -->
              </div>
              <div class="card-body pb0">
                {if $group['subscribers_count'] > 0}
                  <ul class="row">
                    {foreach $group['subscribers'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="box" _connection=$_user["connection"] _darker=true}
                    {/foreach}
                  </ul>
                  {if count($group['subscribers']) >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more mt0 mb20 js_see-more" data-get="subscribers" data-uid="{$group['group_id']}" data-type="group">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {$group['group_title']} {__("doesn't have subscribers")}
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
              <div class="card-header with-icon with-nav">
                <!-- panel title -->
                <div class="mb20">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Invites")}
                </div>
                <!-- panel title -->

                <!-- panel nav -->
                <ul class="nav nav-tabs">
                  <li class="nav-item">
                    <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/members">{__("Members")}</a>
                  </li>
                  {if $group['has_subscriptions_plans']}
                    <li class="nav-item">
                      <a class="nav-link" href="{$system['system_url']}/groups/{$group['group_name']}/subscribers">{__("Subscribers")}</a>
                    </li>
                  {/if}
                  <li class="nav-item">
                    <a class="nav-link active" href="{$system['system_url']}/groups/{$group['group_name']}/invites">
                      {__("Invites")}
                    </a>
                  </li>
                </ul>
                <!-- panel nav -->
              </div>
              <div class="card-body">
                {if $group['invites']}
                  <ul class="row">
                    {foreach $group['invites'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="box" _connection=$_user["connection"] _darker=true}
                    {/foreach}
                  </ul>

                  {if count($group['invites']) >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="group_invites" data-id="{$group['group_id']}">
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
                    <a href="{$system['system_url']}/groups/{$group['group_name']}/settings">
                      {include file='__svg_icons.tpl' icon="settings" class="main-icon mr10" width="24px" height="24px"}
                      {__("Group Settings")}
                    </a>
                  </li>
                  {if $group['group_privacy'] != "public"}
                    <li {if $sub_view == "requests"}class="active" {/if}>
                      <a href="{$system['system_url']}/groups/{$group['group_name']}/settings/requests">
                        {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                        {__("Join Requests")}
                      </a>
                    </li>
                  {/if}
                  <li {if $sub_view == "members"}class="active" {/if}>
                    <a href="{$system['system_url']}/groups/{$group['group_name']}/settings/members">
                      {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                      {__("Members")}
                    </a>
                  </li>
                  {if $user->_data['can_monetize_content']}
                    <li {if $sub_view == "monetization"}class="active" {/if}>
                      <a href="{$system['system_url']}/groups/{$group['group_name']}/settings/monetization">
                        {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="24px" height="24px"}
                        {__("Monetization")}
                      </a>
                    </li>
                  {/if}
                  {if $user->_data['user_id'] == $group['group_admin']}
                    <li {if $sub_view == "delete"}class="active" {/if}>
                      <a href="{$system['system_url']}/groups/{$group['group_name']}/settings/delete">
                        {include file='__svg_icons.tpl' icon="delete" class="main-icon mr10" width="24px" height="24px"}
                        {__("Delete Group")}
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
                  {__("Group Settings")}
                </div>
                <form class="js_ajax-forms" data-url="modules/create.php?type=group&do=edit&edit=settings&id={$group['group_id']}">
                  <div class="card-body">
                    <div class="form-group">
                      <label class="form-label" for="title">{__("Name Your Group")}</label>
                      <input type="text" class="form-control" name="title" id="title" placeholder='{__("Name of your group")}' value="{$group['group_title']}">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="username">{__("Group Username")}</label>
                      <div class="input-group">
                        <span class="input-group-text d-none d-sm-block">{$system['system_url']}/groups/</span>
                        <input type="text" class="form-control" name="username" id="username" value="{$group['group_name']}">
                      </div>
                      <div class="form-text">
                        {__("Can only contain alphanumeric characters (A–Z, 0–9) and periods ('.')")}
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="privacy">{__("Select Privacy")}</label>
                      <select class="form-select" name="privacy">
                        <option {if $group['group_privacy'] == "public"}selected{/if} value="public">{__("Public Group")}</option>
                        <option {if $group['group_privacy'] == "closed"}selected{/if} value="closed">{__("Closed Group")}</option>
                        <option {if $group['group_privacy'] == "secret"}selected{/if} value="secret">{__("Secret Group")}</option>
                      </select>
                      <div class="form-text">
                        ({__("Note: Change group privacy to public will approve any pending join requests")})
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="title">{__("Category")}</label>
                      <select class="form-select" name="category" id="category">
                        {foreach $categories as $category}
                          {include file='__categories.recursive_options.tpl' data_category=$group['group_category']}
                        {/foreach}
                      </select>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="description">{__("About")}</label>
                      <textarea class="form-control" name="description" id="description">{$group['group_description']}</textarea>
                    </div>
                    <!-- custom fields -->
                    {if $custom_fields['basic']}
                      {include file='__custom_fields.tpl' _custom_fields=$custom_fields['basic'] _registration=false}
                    {/if}
                    <!-- custom fields -->

                    <div class="divider"></div>

                    <div class="form-table-row">
                      <div class="avatar">
                        {include file='__svg_icons.tpl' icon="chat" class="main-icon" width="40px" height="40px"}
                      </div>
                      <div>
                        <div class="form-label h6">{__("Chat Box")}</div>
                        <div class="form-text d-none d-sm-block">{__("Enable chat box for this group")}</div>
                      </div>
                      <div class="text-end">
                        <label class="switch" for="chatbox_enabled">
                          <input type="checkbox" name="chatbox_enabled" id="chatbox_enabled" {if $group['chatbox_enabled']}checked{/if}>
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </div>

                    <div class="divider"></div>

                    <div class="form-table-row">
                      <div>
                        <div class="form-label h6">{__("Members Can Publish Posts?")}</div>
                        <div class="form-text d-none d-sm-block">{__("Members can publish posts or only group admins")}</div>
                      </div>
                      <div class="text-end">
                        <label class="switch" for="group_publish_enabled">
                          <input type="checkbox" name="group_publish_enabled" id="group_publish_enabled" {if $group['group_publish_enabled']}checked{/if}>
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </div>

                    <div class="form-table-row">
                      <div>
                        <div class="form-label h6">{__("Post Approval")}</div>
                        <div class="form-text d-none d-sm-block">
                          {__("All posts must be approved by a group admin")}<br>
                          ({__("Note: Disable it will approve any pending posts")})
                        </div>
                      </div>
                      <div class="text-end">
                        <label class="switch" for="group_publish_approval_enabled">
                          <input type="checkbox" name="group_publish_approval_enabled" id="group_publish_approval_enabled" {if $group['group_publish_approval_enabled']}checked{/if}>
                          <span class="slider round"></span>
                        </label>
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
              {elseif $sub_view == "requests"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Member Requests")}
                </div>
                <div class="card-body">
                  {if $group['requests']}
                    <ul>
                      {foreach $group['requests'] as $_user}
                        {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"]}
                      {/foreach}
                    </ul>

                    {if count($group['requests']) >= $system['max_results']}
                      <!-- see-more -->
                      <div class="alert alert-post see-more js_see-more" data-get="group_requests" data-id="{$group['group_id']}">
                        <span>{__("See More")}</span>
                        <div class="loader loader_small x-hidden"></div>
                      </div>
                      <!-- see-more -->
                    {/if}
                  {else}
                    <p class="text-center text-muted mt10">
                      {__("No Requests")}
                    </p>
                  {/if}
                </div>
              {elseif $sub_view == "members"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="24px" height="24px"}
                  {__("Members")}
                </div>
                <div class="card-body">
                  <!-- admins -->
                  <div class="heading-small mb20">
                    {__("Admins")} <span class="text-muted">({$group['group_admins_count']})</span>
                  </div>
                  <div class="pl-md-4">
                    {if $group['group_admins']}
                      <ul>
                        {foreach $group['group_admins'] as $_user}
                          {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"]}
                        {/foreach}
                      </ul>

                      {if $group['group_admins_count'] >= $system['max_results_even']}
                        <!-- see-more -->
                        <div class="alert alert-post see-more js_see-more" data-get="group_admins" data-id="{$group['group_id']}">
                          <span>{__("See More")}</span>
                          <div class="loader loader_small x-hidden"></div>
                        </div>
                        <!-- see-more -->
                      {/if}
                    {else}
                      <p class="text-center text-muted mt10">
                        {$group['group_title']} {__("doesn't have admins")}
                      </p>
                    {/if}
                  </div>
                  <!-- admins -->

                  <div class="divider"></div>

                  <!-- members -->
                  <div class="heading-small mb20">
                    {__("All Members")} <span class="text-muted">({$group['group_members']})</span>
                  </div>
                  <div class="pl-md-4">
                    {if $group['group_members'] > 0}
                      <ul>
                        {foreach $group['members'] as $_user}
                          {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"]}
                        {/foreach}
                      </ul>

                      {if $group['group_members'] >= $system['max_results_even']}
                        <!-- see-more -->
                        <div class="alert alert-post see-more js_see-more" data-get="group_members_manage" data-id="{$group['group_id']}">
                          <span>{__("See More")}</span>
                          <div class="loader loader_small x-hidden"></div>
                        </div>
                        <!-- see-more -->
                      {/if}
                    {else}
                      <p class="text-center text-muted mt10">
                        {$group['group_title']} {__("doesn't have members")}
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
                  <div class="alert alert-primary">
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
                    <form class="js_ajax-forms" data-url="modules/create.php?type=group&do=edit&edit=monetization&id={$group['group_id']}">
                      <div class="form-table-row">
                        <div class="avatar">
                          {include file='__svg_icons.tpl' icon="monetization" class="main-icon" width="40px" height="40px"}
                        </div>
                        <div>
                          <div class="form-label h6">{__("Monetization")}</div>
                          <div class="form-text d-none d-sm-block">{__("Enable or disable monetization for your content")}</div>
                        </div>
                        <div class="text-end">
                          <label class="switch" for="group_monetization_enabled">
                            <input type="checkbox" name="group_monetization_enabled" id="group_monetization_enabled" {if $group['group_monetization_enabled']}checked{/if}>
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
                            <div data-toggle="modal" data-url="monetization/controller.php?do=add&node_id={$group['group_id']}&node_type=group" class="payment-plan new">{__("Add new plan")} </div>
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
                          {__("Group Subscribers")}
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
              {elseif $sub_view == "delete"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="delete" class="main-icon mr5" width="24px" height="24px"}
                  {__("Delete Group")}
                </div>
                <div class="card-body">
                  <div class="alert alert-warning">
                    <div class="icon">
                      <i class="fa fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="text pt5">
                      {__("Once you delete your group you will no longer can access it again")}
                    </div>
                  </div>

                  <div class="text-center">
                    <button class="btn btn-danger js_delete-group" data-id="{$group['group_id']}">
                      {__("Delete Group")}
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