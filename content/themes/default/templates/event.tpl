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
      <div class="profile-header no-avatar">
        <!-- profile-cover -->
        <div class="profile-cover-wrapper">
          {if $event['event_cover_id']}
            <!-- full-cover -->
            <img class="js_position-cover-full x-hidden" src="{$event['event_cover_full']}">
            <!-- full-cover -->

            <!-- cropped-cover -->
            <img class="js_position-cover-cropped js_lightbox" data-init-position="{$event['event_cover_position']}" data-id="{$event['event_cover_id']}" data-image="{$event['event_cover_full']}" data-context="album" src="{$event['event_cover']}" alt="{$event['event_title']}">
            <!-- cropped-cover -->
          {/if}

          {if $event['i_admin']}
            <!-- buttons -->
            <div class="profile-cover-buttons">
              <div class="profile-cover-change">
                <i class="fa fa-camera" data-bs-toggle="dropdown" data-display="static"></i>
                <div class="dropdown-menu action-dropdown-menu">
                  <!-- upload -->
                  <div class="dropdown-item pointer js_x-uploader" data-handle="cover-event" data-id="{$event['event_id']}">
                    <div class="action">
                      {include file='__svg_icons.tpl' icon="camera" class="main-icon mr10" width="20px" height="20px"}
                      {__("Upload Photo")}
                    </div>
                    <div class="action-desc">{__("Upload a new photo")}</div>
                  </div>
                  <!-- upload -->
                  <!-- select -->
                  <div class="dropdown-item pointer" data-toggle="modal" data-url="users/photos.php?filter=cover&type=event&id={$event['event_id']}">
                    <div class="action">
                      {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="20px" height="20px"}
                      {__("Select Photo")}
                    </div>
                    <div class="action-desc">{__("Select a photo")}</div>
                  </div>
                  <!-- select -->
                </div>
              </div>
              <div class="profile-cover-position {if !$event['event_cover']}x-hidden{/if}">
                <input class="js_position-picture-val" type="hidden" name="position-picture-val">
                <i class="fa fa-crop-alt js_init-position-picture" data-handle="event" data-id="{$event['event_id']}"></i>
              </div>
              <div class="profile-cover-position-buttons">
                <i class="fa fa-check fa-fw js_save-position-picture"></i>
              </div>
              <div class="profile-cover-position-buttons">
                <i class="fa fa-times fa-fw js_cancel-position-picture"></i>
              </div>
              <div class="profile-cover-delete {if !$event['event_cover']}x-hidden{/if}">
                <i class="fa fa-trash js_delete-cover" data-handle="cover-event" data-id="{$event['event_id']}"></i>
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

        <!-- profile-date -->
        <div class="profle-date-wrapper">
          {__($event['event_start_date']|date_format:"%b")}<br>{$event['event_start_date']|date_format:"%e"}
        </div>
        <!-- profile-date -->

        <!-- profile-name -->
        <div class="profile-name-wrapper">
          <a href="{$system['system_url']}/events/{$event['event_id']}">{$event['event_title']}</a>
          {if $event['event_privacy'] == "public"}
            <i data-bs-toggle="tooltip" title='{__("Public Event")}' class="fa fa-globe fa-fw privacy-badge"></i>
          {elseif $event['event_privacy'] == "closed"}
            <i data-bs-toggle="tooltip" title='{__("Closed Event")}' class="fa fa-unlock-alt fa-fw privacy-badge"></i>
          {elseif $event['event_privacy'] == "secret"}
            <i data-bs-toggle="tooltip" title='{__("Secret Event")}' class="fa fa-lock fa-fw privacy-badge"></i>
          {/if}
        </div>
        <!-- profile-name -->

        <!-- profile-meta -->
        <div class="profle-meta-wrapper">
          <i class="fa fa-clock"></i> {$event['event_start_date']|date_format:"%e"} {__($event['event_start_date']|date_format:"%b")} {$event['event_start_date']|date_format:"%I:%M %p"} {__("to")} {$event['event_end_date']|date_format:"%e"} {__($event['event_end_date']|date_format:"%b")} {$event['event_end_date']|date_format:"%I:%M %p"}
        </div>
        <!-- profile-meta -->

        <!-- profile-buttons -->
        <div class="profile-buttons-wrapper">
          <!-- going & interested -->
          {if $event['event_privacy'] == "public" || $event['i_joined'] || $event['i_admin']}
            <!-- going -->
            {if $event['i_joined']['is_going']}
              <button type="button" class="btn btn-md btn-light rounded-pill js_ungo-event" data-id="{$event['event_id']}">
                <i class="fa fa-check"></i>
                <span class="d-none d-xxl-inline-block ml5">{__("Going")}</span>
              </button>
            {else}
              <button type="button" class="btn btn-md btn-success rounded-pill js_go-event" data-id="{$event['event_id']}">
                <i class="fa fa-calendar-check"></i>
                <span class="d-none d-xxl-inline-block ml5">{__("Going")}</span>
              </button>
            {/if}
            <!-- going -->

            <!-- interested -->
            {if $event['i_joined']['is_interested']}
              <button type="button" class="btn btn-md btn-light rounded-pill js_uninterest-event" data-id="{$event['event_id']}">
                <i class="fa fa-check"></i>
                <span class="d-none d-xxl-inline-block ml5">{__("Interested")}</span>
              </button>
            {else}
              <button type="button" class="btn btn-md btn-primary rounded-pill js_interest-event" data-id="{$event['event_id']}">
                <i class="fa fa-star"></i>
                <span class="d-none d-xxl-inline-block ml5">{__("Interested")}</span>
              </button>
            {/if}
            <!-- interested -->
          {/if}
          <!-- going & interested -->

          <!-- review -->
          {if $system['events_reviews_enabled']}
            {if !$event['i_admin']}
              <button type="button" class="btn btn-md rounded-pill btn-light" data-toggle="modal" data-url="modules/review.php?do=review&id={$event['event_id']}&type=event">
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
              <div class="dropdown-item pointer" data-toggle="modal" data-url="modules/share.php?node_type=event&node_username={$event['event_id']}">
                <div class="action">
                  {include file='__svg_icons.tpl' icon="share" class="main-icon mr10" width="20px" height="20px"}
                  {__("Share")}
                </div>
                <div class="action-desc">{__("Share this event")}</div>
              </div>
              <!-- share -->
              {if $user->_logged_in && !$event['i_admin']}
                <!-- report -->
                <div class="dropdown-item pointer" data-toggle="modal" data-url="data/report.php?do=create&handle=event&id={$event['event_id']}">
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
                  <a class="dropdown-item" href="{$system['system_url']}/admincp/events/edit_event/{$event['event_id']}">
                    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr10" width="20px" height="20px"}
                    {__("Edit in Admin Panel")}
                  </a>
                {elseif $user->_is_moderator}
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{$system['system_url']}/modcp/events/edit_event/{$event['event_id']}">
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
        {if $event['event_privacy'] == "public" || $event['i_joined'] || $event['i_admin'] || $user->_is_admin || $user->_is_moderator}
          <a href="{$system['system_url']}/events/{$event['event_id']}" {if $view == ""}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Timeline")}</span>
          </a>
          <a href="{$system['system_url']}/events/{$event['event_id']}/photos" {if $view == "photos" || $view == "albums" || $view == "album"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Photos")}</span>
          </a>
          {if $system['videos_enabled']}
            <a href="{$system['system_url']}/events/{$event['event_id']}/videos" {if $view == "videos"}class="active" {/if}>
              {include file='__svg_icons.tpl' icon="videos" class="main-icon mr5" width="24px" height="24px"}
              <span class="ml5 d-none d-xl-inline-block">{__("Videos")}</span>
            </a>
          {/if}
          {if $system['events_reviews_enabled']}
            <a href="{$system['system_url']}/events/{$event['event_id']}/reviews" {if $view == "reviews"}class="active" {/if}>
              {include file='__svg_icons.tpl' icon="star" class="main-icon mr5" width="24px" height="24px"}
              <span class="ml5 d-none d-xl-inline-block">{__("Reviews")} {if $event['event_rate']}<span class="badge bg-light text-primary">{$event['event_rate']|number_format:1}</span>{/if}</span>
            </a>
          {/if}
          <a href="{$system['system_url']}/events/{$event['event_id']}/going" {if $view == "going" || $view == "interested" || $view == "invited" || $view == "invites"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
            <span class="ml5 d-none d-xl-inline-block">{__("Members")}</span>
          </a>
          {if $event['i_admin']}
            <a href="{$system['system_url']}/events/{$event['event_id']}/settings" {if $view == "settings"}class="active" {/if}>
              {include file='__svg_icons.tpl' icon="settings" class="main-icon mr5" width="24px" height="24px"}
              <span class="ml5 d-none d-xl-inline-block">{__("Settings")}</span>
            </a>
          {/if}
        {else}
          <a href="{$system['system_url']}/events/{$event['event_id']}" {if $view == "about"}class="active" {/if}>
            {include file='__svg_icons.tpl' icon="info" class="main-icon mr5" width="24px" height="24px"}
            {__("About")}
          </a>
        {/if}
      </div>
      <!-- profile-tabs -->

      <!-- profile-content -->
      <div class="row">
        <!-- view content -->
        {if $view == ""}

          <!-- left panel -->
          <div class="{if $event['chatbox_enabled']}col-lg-3 order-3 order-lg-1{else}col-lg-4 order-2 order-lg-1{/if}">
            <!-- panel [about] -->
            <div class="card">
              <div class="card-body">
                {if !is_empty($event['event_description'])}
                  <div class="about-bio">
                    <div class="js_readmore overflow-hidden">
                      {$event['event_description']|nl2br}
                    </div>
                  </div>
                {/if}
                <ul class="about-list">
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="unhide" class="main-icon" width="24px" height="24px"}
                      {if $event['event_privacy'] == "public"}
                        <i class="fa fa-globe fa-fw"></i>
                        {__("Public Event")}
                      {elseif $event['event_privacy'] == "closed"}
                        <i class="fa fa-unlock-alt fa-fw"></i>
                        {__("Closed Event")}
                      {elseif $event['event_privacy'] == "secret"}
                        <i class="fa fa-lock fa-fw"></i>
                        {__("Secret Event")}
                      {/if}
                    </div>
                  </li>
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="clock" class="main-icon" width="24px" height="24px"}
                      {$event['event_start_date']|date_format:"%e"} {__($event['event_start_date']|date_format:"%b")} - {$event['event_end_date']|date_format:"%e"} {__($event['event_end_date']|date_format:"%b")}<br>
                      <small class="text-muted">
                        {$event['event_start_date']|date_format:"%e"} {__($event['event_start_date']|date_format:"%b")} {$event['event_start_date']|date_format:"%I:%M %p"} {__("to")} {$event['event_end_date']|date_format:"%e"} {__($event['event_end_date']|date_format:"%b")} {$event['event_end_date']|date_format:"%I:%M %p"}
                      </small>
                    </div>
                  </li>
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="profile" class="main-icon" width="24px" height="24px"}
                      {__("Hosted By")} <a target="_blank" href="{$event['host_url']}">{$event['host_name']}</a>
                    </div>
                  </li>
                  <!-- posts -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon" width="24px" height="24px"}
                      {__($event['posts_count'])} {__("Posts")}
                    </div>
                  </li>
                  <!-- posts -->
                  <!-- photos -->
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="photos" class="main-icon" width="24px" height="24px"}
                      {__($event['photos_count'])} {__("Photos")}
                    </div>
                  </li>
                  <!-- photos -->
                  {if $system['videos_enabled']}
                    <!-- videos -->
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="videos" class="main-icon" width="24px" height="24px"}
                        {__($event['videos_count'])} {__("Videos")}
                      </div>
                    </li>
                    <!-- videos -->
                  {/if}
                  {if $system['events_reviews_enabled']}
                    <!-- reviews -->
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="star" class="main-icon" width="24px" height="24px"}
                        {__($event['reviews_count'])} {__("Reviews")}
                        {if $event['event_rate']}
                          <span class="review-stars small ml5">
                            <i class="fa fa-star {if $event['event_rate'] >= 1}checked{/if}"></i>
                            <i class="fa fa-star {if $event['event_rate'] >= 2}checked{/if}"></i>
                            <i class="fa fa-star {if $event['event_rate'] >= 3}checked{/if}"></i>
                            <i class="fa fa-star {if $event['event_rate'] >= 4}checked{/if}"></i>
                            <i class="fa fa-star {if $event['event_rate'] >= 5}checked{/if}"></i>
                          </span>
                          <span class="badge bg-light text-primary">{$event['event_rate']|number_format:1}</span>
                        {/if}
                      </div>
                    </li>
                    <!-- reviews -->
                  {/if}
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="tag" class="main-icon" width="24px" height="24px"}
                      {__($event['event_category_name'])}
                    </div>
                  </li>
                  {if $event['event_location']}
                    <!-- event location -->
                    <li>
                      <div class="about-list-item">
                        {include file='__svg_icons.tpl' icon="map" class="main-icon" width="24px" height="24px"}
                        {$event['event_location']}
                      </div>
                    </li>
                    {if $system['geolocation_enabled']}
                      <div style="margin-left: -20px; margin-right: -20px;">
                        <iframe width="100%" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key={$system['geolocation_key']}&amp;q={$event['event_location']}&amp;language=en"></iframe>
                      </div>
                    {/if}
                    <!-- event location -->
                  {/if}
                  <li class="divider mtb10"></li>
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="going" class="main-icon" width="24px" height="24px"}
                      <a href="{$system['system_url']}/events/{$event['event_id']}/going">{$event['event_going']} {__("Going")}</a>
                    </div>
                  </li>
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="star" class="main-icon" width="24px" height="24px"}
                      <a href="{$system['system_url']}/events/{$event['event_id']}/interested">{$event['event_interested']} {__("Interested")}</a>
                    </div>
                  </li>
                  <li>
                    <div class="about-list-item">
                      {include file='__svg_icons.tpl' icon="invitation" class="main-icon" width="24px" height="24px"}
                      <a href="{$system['system_url']}/events/{$event['event_id']}/invited">{$event['event_invited']} {__("Invited")}</a>
                    </div>
                  </li>
                  {if $event['event_tickets_link'] || $event['event_prices']}
                    <li class="divider mtb10"></li>
                    {if $event['event_tickets_link']}
                      <li>
                        <div class="about-list-item">
                          {include file='__svg_icons.tpl' icon="link" class="main-icon" width="24px" height="24px"}
                          <a href="{$event['event_tickets_link']}">{$event['event_tickets_link']}</a>
                        </div>
                      </li>
                    {/if}
                    {if $event['event_prices']}
                      <li>
                        <div class="about-list-item">
                          {include file='__svg_icons.tpl' icon="funding" class="main-icon" width="24px" height="24px"}
                          {$event['event_prices']|nl2br}
                        </div>
                      </li>
                    {/if}
                  {/if}
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

            <!-- invite friends -->
            {if $event['i_joined'] && $event['invites']}
              <div class="card">
                <div class="card-header bg-transparent">
                  <div class="float-end">
                    <small><a href="{$system['system_url']}/events/{$event['event_id']}/invites">{__("See All")}</a></small>
                  </div>
                  {include file='__svg_icons.tpl' icon="friends" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/events/{$event['event_id']}/invites">{__("Invite Friends")}</a></strong>
                </div>
                <div class="card-body">
                  <ul>
                    {foreach $event['invites'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"] _small=true}
                    {/foreach}
                  </ul>
                </div>
              </div>
            {/if}
            <!-- invite friends -->

            <!-- photos -->
            {if $event['photos']}
              <div class="card panel-photos">
                <div class="card-header bg-transparent">
                  {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
                  <strong><a href="{$system['system_url']}/events/{$event['event_id']}/photos">{__("Photos")}</a></strong>
                </div>
                <div class="card-body">
                  <div class="row">
                    {foreach $event['photos'] as $photo}
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
          <div class="{if $event['chatbox_enabled']}col-lg-6 order-1 order-lg-2{else}col-lg-8 order-1 order-lg-2{/if}">
            <!-- super admin alert -->
            {if $user->_data['user_group'] < 3 && ($event['event_privacy'] == "secret" || $event['event_privacy'] == "closed") && (!$event['i_joined'] && !$event['i_admin']) }
              <div class="alert alert-warning">
                <button type="button" class="btn-close float-end" data-dismiss="alert" aria-label="Close"></button>
                <div class="icon"><i class="fa fa-info-circle fa-2x"></i></div>
                <div class="text align-middle">
                  {__("You can access this as your account is system admin account!")}
                </div>
              </div>
            {/if}
            <!-- super admin alert -->

            {if $get == "posts_event"}
              <!-- event pending posts -->
              {if $event['pending_posts'] > 0}
                <div class="alert alert-light">
                  <button type="button" class="btn-close float-end" data-dismiss="alert" aria-label="Close"></button>
                  <div class="icon"><i class="fa fa-comments fa-lg"></i></div>
                  <div class="text align-middle">
                    <a href="?pending" class="alert-link">
                      {if $event['i_admin']}
                        <span class="badge bg-secondary mr5">{$event['pending_posts']}</span>{if $event['pending_posts'] == 1}{__("post")}{else}{__("posts")}{/if} {__("pending needs your approval")}
                      {else}
                        {__("You have")}<span class="badge bg-secondary mlr5">{$event['pending_posts']}</span>{if $event['pending_posts'] == 1}{__("post")}{else}{__("posts")}{/if} {__("pending")}
                      {/if}
                    </a>
                  </div>
                </div>
              {/if}
              <!-- event pending posts -->

              <!-- publisher -->
              {if $event['i_joined'] && ($event['event_publish_enabled'] OR (!$event['event_publish_enabled'] && $event['i_admin']))}
                {if $event['event_page'] && $event['i_admin']}
                  {include file='_publisher.tpl' _handle="event" _id=$event['event_id'] _post_as_page=true _page_id=$event['event_page']['page_id'] _avatar=$event['event_page']['page_picture']}
                {else}
                  {include file='_publisher.tpl' _handle="event" _id=$event['event_id']}
                {/if}
              {/if}
              <!-- publisher -->

              <!-- pinned post -->
              {if $pinned_post}
                {include file='_pinned_post.tpl' post=$pinned_post _get="posts_event"}
              {/if}
              <!-- pinned post -->

              <!-- posts -->
              {include file='_posts.tpl' _get="posts_event" _id=$event['event_id']}
              <!-- posts -->
            {else}
              <!-- posts -->
              {include file='_posts.tpl' _get=$get _id=$event['event_id'] _title=__("Pending Posts")}
              <!-- posts -->
            {/if}
          </div>
          <!-- right panel -->

          <!-- chatbox -->
          {if $event['chatbox_enabled']}
            <div class="col-lg-3 order-2 order-lg-3">
              {include file='_chatbox.tpl' _node_type="event" _node=$event}
            </div>
          {/if}
          <!-- chatbox -->

        {elseif $view == "photos"}
          <!-- photos -->
          <div class="col-12">
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
                    <a class="nav-link active" href="{$system['system_url']}/events/{$event['event_id']}/photos">{__("Photos")}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{$system['system_url']}/events/{$event['event_id']}/albums">{__("Albums")}</a>
                  </li>
                </ul>
                <!-- panel nav -->
              </div>
              <div class="card-body">
                {if $event['photos']}
                  <ul class="row">
                    {foreach $event['photos'] as $photo}
                      {include file='__feeds_photo.tpl' _context="photos"}
                    {/foreach}
                  </ul>
                  <!-- see-more -->
                  <div class="alert alert-post see-more js_see-more" data-get="photos" data-id="{$event['event_id']}" data-type='event'>
                    <span>{__("See More")}</span>
                    <div class="loader loader_small x-hidden"></div>
                  </div>
                  <!-- see-more -->
                {else}
                  <p class="text-center text-muted mt10">
                    {$event['event_title']} {__("doesn't have photos")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- photos -->

        {elseif $view == "albums"}
          <!-- albums -->
          <div class="col-12">
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
                    <a class="nav-link" href="{$system['system_url']}/events/{$event['event_id']}/photos">{__("Photos")}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active" href="{$system['system_url']}/events/{$event['event_id']}/albums">{__("Albums")}</a>
                  </li>
                </ul>
                <!-- panel nav -->
              </div>
              <div class="card-body">
                {if $event['albums']}
                  <ul class="row">
                    {foreach $event['albums'] as $album}
                      {include file='__feeds_album.tpl'}
                    {/foreach}
                  </ul>
                  {if count($event['albums']) >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="albums" data-id="{$event['event_id']}" data-type='event'>
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {$event['event_title']} {__("doesn't have albums")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- albums -->

        {elseif $view == "album"}
          <!-- albums -->
          <div class="col-12">
            <div class="card panel-photos">
              <div class="card-header with-icon with-nav">
                <!-- back to albums -->
                <div class="float-end">
                  <a href="{$system['system_url']}/events/{$event['event_id']}/albums" class="btn btn-md btn-light">
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
                    <a class="nav-link" href="{$system['system_url']}/events/{$event['event_id']}/photos">{__("Photos")}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active" href="{$system['system_url']}/events/{$event['event_id']}/albums">{__("Albums")}</a>
                  </li>
                </ul>
                <!-- panel nav -->
              </div>
              <div class="card-body">
                {include file='_album.tpl'}
              </div>
            </div>
          </div>
          <!-- albums -->

        {elseif $view == "videos"}
          <!-- videos -->
          <div class="col-12">
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
                {if $event['videos']}
                  <ul class="row">
                    {foreach $event['videos'] as $video}
                      {include file='__feeds_video.tpl'}
                    {/foreach}
                  </ul>
                  <!-- see-more -->
                  <div class="alert alert-post see-more js_see-more" data-get="videos" data-id="{$event['event_id']}" data-type='event'>
                    <span>{__("See More")}</span>
                    <div class="loader loader_small x-hidden"></div>
                  </div>
                  <!-- see-more -->
                {else}
                  <p class="text-center text-muted mt10">
                    {$event['event_title']} {__("doesn't have videos")}
                  </p>
                {/if}
              </div>
            </div>
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
                  {if $event['event_rate']}
                    <span class="review-stars small ml5">
                      <i class="fa fa-star {if $event['event_rate'] >= 1}checked{/if}"></i>
                      <i class="fa fa-star {if $event['event_rate'] >= 2}checked{/if}"></i>
                      <i class="fa fa-star {if $event['event_rate'] >= 3}checked{/if}"></i>
                      <i class="fa fa-star {if $event['event_rate'] >= 4}checked{/if}"></i>
                      <i class="fa fa-star {if $event['event_rate'] >= 5}checked{/if}"></i>
                    </span>
                    <span class="badge bg-light text-primary">{$event['event_rate']|number_format:1}</span>
                  {/if}
                </div>
                <!-- panel title -->
              </div>
              <div class="card-body pb0">
                {if $event['reviews_count'] > 0}
                  <ul class="row">
                    {foreach $event['reviews'] as $_review}
                      {include file='__feeds_review.tpl' _darker=true}
                    {/foreach}
                  </ul>
                  {if $event['reviews_count'] >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more mt0 mb20 js_see-more" data-get="reviews" data-id="{$event['event_id']}" data-type="event">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {$event['event_title']} {__("doesn't have reviews")}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- reviews -->

        {elseif $view == "going" || $view == "interested" || $view == "invited" || $view == "invites"}
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
                    <a class="nav-link {if $view == "going"}active{/if}" href="{$system['system_url']}/events/{$event['event_id']}/going">
                      {__("Going")}
                      <span class="badge rounded-pill bg-info">{$event['event_going']}</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link {if $view == "interested"}active{/if}" href="{$system['system_url']}/events/{$event['event_id']}/interested">
                      {__("Interested")}
                      <span class="badge rounded-pill bg-info">{$event['event_interested']}</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link {if $view == "invited"}active{/if}" href="{$system['system_url']}/events/{$event['event_id']}/invited">
                      {__("Invited")}
                      <span class="badge rounded-pill bg-info">{$event['event_invited']}</span>
                    </a>
                  </li>
                  {if $event['i_joined']}
                    <li class="nav-item">
                      <a class="nav-link {if $view == "invites"}active{/if}" href="{$system['system_url']}/events/{$event['event_id']}/invites">
                        {__("Invites")}
                      </a>
                    </li>
                  {/if}
                </ul>
                <!-- panel nav -->
              </div>
              <div class="card-body">
                {if $event['total_members'] > 0}
                  <ul class="row">
                    {foreach $event['members'] as $_user}
                      {include file='__feeds_user.tpl' _tpl="box" _connection=$_user["connection"] _darker=true}
                    {/foreach}
                  </ul>

                  {if $event['total_members'] >= $system['max_results_even']}
                    <!-- see-more -->
                    <div class="alert alert-post see-more js_see-more" data-get="event_{$view}" data-id="{$event['event_id']}">
                      <span>{__("See More")}</span>
                      <div class="loader loader_small x-hidden"></div>
                    </div>
                    <!-- see-more -->
                  {/if}
                {else}
                  <p class="text-center text-muted mt10">
                    {if $view == "invites"}
                      {__("No friends to invite")}
                    {else}
                      {__("No people available")}
                    {/if}
                  </p>
                {/if}
              </div>
            </div>
          </div>
          <!-- members -->

        {elseif $view == "settings"}
          <div class="col-lg-3">
            <div class="card">
              <div class="card-body with-nav">
                <ul class="side-nav">
                  <li {if $sub_view == ""}class="active" {/if}>
                    <a href="{$system['system_url']}/events/{$event['event_id']}/settings">
                      {include file='__svg_icons.tpl' icon="settings" class="main-icon mr10" width="24px" height="24px"}
                      {__("Event Settings")}
                    </a>
                  </li>
                  <li {if $sub_view == "delete"}class="active" {/if}>
                    <a href="{$system['system_url']}/events/{$event['event_id']}/settings/delete">
                      {include file='__svg_icons.tpl' icon="delete" class="main-icon mr10" width="24px" height="24px"}
                      {__("Delete Event")}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-9">
            <div class="card">
              {if $sub_view == ""}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="settings" class="main-icon mr10" width="24px" height="24px"}
                  {__("Event Settings")}
                </div>
                <form class="js_ajax-forms" data-url="modules/create.php?type=event&do=edit&id={$event['event_id']}">
                  <div class="card-body">
                    <div class="form-group">
                      <label class="form-label" for="title">{__("Name Your Event")}</label>
                      <input type="text" class="form-control" name="title" id="title" value="{$event['event_title']}">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="location">{__("Location")}</label>
                      <input type="text" class="form-control" name="location" id="location" value="{$event['event_location']}">
                    </div>
                    <div class="form-group">
                      <label class="form-label">{__("Start Date")}</label>
                      <input type="datetime-local" class="form-control" name="start_date" value="{$event['event_start_date']}">
                    </div>
                    <div class="form-group">
                      <label class="form-label">{__("End Date")}</label>
                      <input type="datetime-local" class="form-control" name="end_date" value="{$event['event_end_date']}">
                    </div>
                    {if !$event['event_page']}
                      <div class="form-group">
                        <label class="form-label" for="privacy">{__("Select Privacy")}</label>
                        <select class="form-select" name="privacy">
                          <option {if $event['event_privacy'] == "public"}selected{/if} value="public">{__("Public Event")}</option>
                          <option {if $event['event_privacy'] == "closed"}selected{/if} value="closed">{__("Closed Event")}</option>
                          <option {if $event['event_privacy'] == "secret"}selected{/if} value="secret">{__("Secret Event")}</option>
                        </select>
                      </div>
                    {/if}
                    <div class="form-group">
                      <label class="form-label" for="category">{__("Category")}</label>
                      <select class="form-select" name="category">
                        {foreach $categories as $category}
                          {include file='__categories.recursive_options.tpl' data_category=$event['event_category']}
                        {/foreach}
                      </select>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="description">{__("About")}</label>
                      <textarea class="form-control" name="description">{$event['event_description']}</textarea>
                    </div>
                    <!-- custom fields -->
                    {if $custom_fields['basic']}
                      {include file='__custom_fields.tpl' _custom_fields=$custom_fields['basic'] _registration=false}
                    {/if}
                    <!-- custom fields -->
                    {if $event['event_page']}
                      <div class="divider"></div>
                      <div class="form-group">
                        <label class="form-label" for="tickets_link">{__("Tickets Link")}</label>
                        <input type="text" class="form-control" name="tickets_link" id="tickets_link" value="{$event['event_tickets_link']}">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="prices">{__("Prices Info")}</label>
                        <textarea class="form-control" name="prices">{$event['event_prices']}</textarea>
                      </div>
                    {/if}

                    <div class="divider"></div>

                    <div class="form-table-row">
                      <div class="avatar">
                        {include file='__svg_icons.tpl' icon="chat" class="main-icon" width="40px" height="40px"}
                      </div>
                      <div>
                        <div class="form-label h6">{__("Chat Box")}</div>
                        <div class="form-text d-none d-sm-block">{__("Enable chat box for this event")}</div>
                      </div>
                      <div class="text-end">
                        <label class="switch" for="chatbox_enabled">
                          <input type="checkbox" name="chatbox_enabled" id="chatbox_enabled" {if $event['chatbox_enabled']}checked{/if}>
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </div>

                    <div class="divider"></div>

                    <div class="form-table-row">
                      <div>
                        <div class="form-label h6">{__("Members Can Publish Posts?")}</div>
                        <div class="form-text d-none d-sm-block">{__("Members can publish posts or only event admin")}</div>
                      </div>
                      <div class="text-end">
                        <label class="switch" for="event_publish_enabled">
                          <input type="checkbox" name="event_publish_enabled" id="event_publish_enabled" {if $event['event_publish_enabled']}checked{/if}>
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </div>

                    <div class="form-table-row">
                      <div>
                        <div class="form-label h6">{__("Post Approval")}</div>
                        <div class="form-text d-none d-sm-block">
                          {__("All posts must be approved by the event admin")}<br>
                          ({__("Note: Disable it will approve any pending posts")})
                        </div>
                      </div>
                      <div class="text-end">
                        <label class="switch" for="event_publish_approval_enabled">
                          <input type="checkbox" name="event_publish_approval_enabled" id="event_publish_approval_enabled" {if $event['event_publish_approval_enabled']}checked{/if}>
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
              {elseif $sub_view == "delete"}
                <div class="card-header with-icon">
                  {include file='__svg_icons.tpl' icon="delete" class="main-icon mr10" width="24px" height="24px"}
                  {__("Delete Event")}
                </div>
                <div class="card-body">
                  <div class="alert alert-warning">
                    <div class="icon">
                      <i class="fa fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="text pt5">
                      {__("Once you delete your event you will no longer can access it again")}
                    </div>
                  </div>

                  <div class="text-center">
                    <button class="btn btn-danger js_delete-event" data-id="{$event['event_id']}">
                      {__("Delete Event")}
                    </button>
                  </div>
                </div>
              {/if}
            </div>
          </div>

        {elseif $view == "about"}
          <!-- info -->
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                {__("This event is private and you need to be invited to see its info, members and posts")}
              </div>
            </div>
          </div>
          <!-- info -->

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