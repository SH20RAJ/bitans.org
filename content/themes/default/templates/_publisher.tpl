{if $user->_data['can_publish_posts']}
  {if $system['verification_for_posts'] && !$user->_data['user_verified']}
    <div class="alert alert-danger">
      <div class="icon">
        <i class="fa fa-exclamation-circle fa-2x"></i>
      </div>
      <div class="text">
        <strong>{__("Account Verification Required")}</strong><br>
        {__("To publish posts your account must be verified")} <a href="{$system['system_url']}/settings/verification">{__("Verify Now")}</a>
      </div>
    </div>
  {else}
    <div class="publisher-overlay"></div>

    <div class="x-form publisher" data-handle="{$_handle}" {if $_id}data-id="{$_id}" {/if}>

      <!-- publisher loader -->
      <div class="publisher-loader">
        <div class="loader loader_small"></div>
      </div>
      <!-- publisher loader -->

      <!-- publisher-message -->
      <div class="publisher-message">
        {if $_handle == "page" || $_post_as_page}
          <img class="publisher-avatar" src="{$_avatar}">
        {else}
          <img class="publisher-avatar" src="{$user->_data['user_picture']}">
        {/if}
        <div class="colored-text-wrapper">
          <textarea dir="auto" class="js_autosize js_mention js_publisher-scraper" data-init-placeholder='{__("What is on your mind? #Hashtag.. @Mention.. Link..")}' placeholder='{__("What is on your mind? #Hashtag.. @Mention.. Link..")}'>{if $page == "share" && $url}{$url}{/if}</textarea>
        </div>
        <div class="publisher-emojis">
          <div class="position-relative">
            <i class="far fa-smile-wink fa-lg js_emoji-menu-toggle"></i>
          </div>
        </div>
      </div>
      <!-- publisher-message -->

      <!-- publisher-slider -->
      <div class="publisher-slider">

        <!-- publisher scraper -->
        <div class="publisher-scraper"></div>
        <!-- publisher scraper -->

        <!-- post attachments (photos) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-photos"></div>
        <!-- post attachments -->

        <!-- post attachments (videos) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-video"></div>
        <!-- post attachments -->

        <!-- post attachments (audios) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-audio"></div>
        <!-- post attachments -->

        <!-- post attachments (files) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-file"></div>
        <!-- post attachments -->

        <!-- post album -->
        <div class="publisher-meta" data-meta="album">
          {include file='__svg_icons.tpl' icon="photos" class="main-icon" width="16px" height="16px"}
          <input type="text" placeholder='{__("Album title")}'>
        </div>
        <!-- post album -->

        <!-- post feelings -->
        <div class="publisher-meta" data-meta="feelings">
          <div id="feelings-menu-toggle" data-init-text='{__("What are you doing?")}'>{__("What are you doing?")}</div>
          <div id="feelings-data" style="display: none">
            <input type="text" class="no-icon" placeholder='{__("What are you doing?")}'>
            <span></span>
          </div>
          <div id="feelings-menu" class="dropdown-menu dropdown-widget">
            <div class="dropdown-widget-body ptb5">
              <div class="js_scroller">
                <ul class="feelings-list">
                  {foreach $feelings as $feeling}
                    <li class="feeling-item js_feelings-add" data-action="{$feeling['action']}" data-placeholder="{__($feeling['placeholder'])}">
                      <div class="icon">
                        <i class="twa twa-3x twa-{$feeling['icon']}"></i>
                      </div>
                      <div class="data">
                        {__($feeling['text'])}
                      </div>
                    </li>
                  {/foreach}
                </ul>
              </div>
            </div>
          </div>
          <div id="feelings-types" class="dropdown-menu dropdown-widget">
            <div class="dropdown-widget-body ptb5">
              <div class="js_scroller">
                <ul class="feelings-list">
                  {foreach $feelings_types as $type}
                    <li class="feeling-item js_feelings-type" data-type="{$type['action']}" data-icon="{$type['icon']}">
                      <div class="icon">
                        <i class="twa twa-3x twa-{$type['icon']}"></i>
                      </div>
                      <div class="data">
                        {__($type['text'])}
                      </div>
                    </li>
                  {/foreach}
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- post feelings -->

        <!-- post location -->
        <div class="publisher-meta" data-meta="location">
          {include file='__svg_icons.tpl' icon="map" class="main-icon" width="16px" height="16px"}
          <input class="js_geocomplete" type="text" placeholder='{__("Where are you?")}'>
        </div>
        <!-- post location -->

        <!-- post colored -->
        <div class="publisher-meta" data-meta="colored">
          {foreach $colored_patterns as $pattern}
            <div class="colored-pattern-item js_publisher-pattern" data-id="{$pattern['pattern_id']}" data-type="{$pattern['type']}" data-background-image="{$pattern['background_image']}" data-background-color-1="{$pattern['background_color_1']}" data-background-color-2="{$pattern['background_color_2']}" data-text-color="{$pattern['text_color']}" {if $pattern['type'] == "color"} style="background-image: linear-gradient(45deg, {$pattern['background_color_1']}, {$pattern['background_color_2']});" {else} style="background-image: url({$system['system_uploads']}/{$pattern['background_image']})" {/if}>
            </div>
          {/foreach}
        </div>
        <!-- post colored -->

        <!-- post voice notes -->
        <div class="publisher-meta" data-meta="voice_notes">
          <div class="voice-recording-wrapper" data-handle="publisher">
            <!-- processing message -->
            <div class="x-hidden js_voice-processing-message">
              {include file='__svg_icons.tpl' icon="upload" class="main-icon static mr5" width="16px" height="16px"}
              {__("Processing")}<span class="loading-dots"></span>
            </div>
            <!-- processing message -->

            <!-- success message -->
            <div class="x-hidden js_voice-success-message">
              {include file='__svg_icons.tpl' icon="checkmark" class="main-icon static mr5" width="16px" height="16px"}
              {__("Voice note recorded successfully")}
              <div class="float-end">
                <button type="button" class="btn-close js_voice-remove"></button>
              </div>
            </div>
            <!-- success message -->

            <!-- start recording -->
            <div class="btn-voice-start js_voice-start">
              <i class="fas fa-microphone mr5"></i>{__("Record")}
            </div>
            <!-- start recording -->

            <!-- stop recording -->
            <div class="btn-voice-stop js_voice-stop" style="display: none">
              <i class="far fa-stop-circle mr5"></i>{__("Recording")} <span class="js_voice-timer">00:00</span>
            </div>
            <!-- stop recording -->
          </div>
        </div>
        <!-- post voice notes -->

        <!-- post gif -->
        <div class="publisher-meta" data-meta="gif">
          {include file='__svg_icons.tpl' icon="gif" class="main-icon" width="16px" height="16px"}
          <input class="js_publisher-gif-search" type="text" placeholder='{__("Search GIFs")}'>
        </div>
        <!-- post gif -->

        <!-- post poll -->
        <div class="publisher-meta" data-meta="poll">
          {include file='__svg_icons.tpl' icon="plus" class="main-icon" width="16px" height="16px"}
          <input type="text" placeholder='{__("Add an option")}...'>
        </div>
        <div class="publisher-meta" data-meta="poll">
          {include file='__svg_icons.tpl' icon="plus" class="main-icon" width="16px" height="16px"}
          <input type="text" placeholder='{__("Add an option")}...'>
        </div>
        <!-- post poll -->

        <!-- post video -->
        <div class="publisher-meta" data-meta="video">
          {include file='__svg_icons.tpl' icon="checkmark" class="main-icon static mr5" width="16px" height="16px"}
          {__("Video uploaded successfully")}
          <div class="float-end">
            <button type="button" class="btn-close js_publisher-attachment-file-remover" data-type="video"></button>
          </div>
        </div>
        <div class="publisher-meta" data-meta="video">
          <select class="form-select" name="video_category" id="video_category">
            {foreach $videos_categories as $category}
              {include file='__categories.recursive_options.tpl'}
            {/foreach}
          </select>
        </div>
        <div class="publisher-custom-thumbnail">
          {__("Custom Video Thumbnail")}
          <div class="x-image">
            <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'></button>
            <div class="x-image-loader">
              <div class="progress x-progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
            <input type="hidden" class="js_x-image-input" name="video_thumbnail" value="">
          </div>
        </div>
        <!-- post video -->

        <!-- post audio -->
        <div class="publisher-meta" data-meta="audio">
          {include file='__svg_icons.tpl' icon="checkmark" class="main-icon static mr5" width="16px" height="16px"}
          {__("Audio uploaded successfully")}
          <div class="float-end">
            <button type="button" class="btn-close js_publisher-attachment-file-remover" data-type="audio"></button>
          </div>
        </div>
        <!-- post audio -->

        <!-- post file -->
        <div class="publisher-meta" data-meta="file">
          {include file='__svg_icons.tpl' icon="checkmark" class="main-icon static mr5" width="16px" height="16px"}
          {__("File uploaded successfully")}
          <div class="float-end">
            <button type="button" class="btn-close js_publisher-attachment-file-remover" data-type="file"></button>
          </div>
        </div>
        <!-- post file -->

        <!-- publisher-tools-tabs -->
        <div class="publisher-tools-tabs">
          <ul class="row">
            {if $user->_data['can_go_live']}
              <li class="col-md-6">
                <a class="publisher-tools-tab link js_publisher-tab" data-tab="live" href="{$system['system_url']}/live{if $_handle == "page"}?page_id={$_id}{/if}{if $_handle == "group"}?group_id={$_id}{/if}{if $_handle == "event"}?event_id={$_id}{/if}">
                  {include file='__svg_icons.tpl' icon="live" class="main-icon mr5" width="24px" height="24px"}
                  {__("Go Live")}
                  <div class="spinner-grow text-danger ml5" style="width: 10px; height: 10px;">
                  </div>
                </a>
              </li>
            {/if}
            {if $system['photos_enabled']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab" data-tab="photos">
                  <span class="js_x-uploader" data-handle="publisher" data-multiple="true">
                    {include file='__svg_icons.tpl' icon="camera" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload Photos")}
                </div>
              </li>
              <li class="col-md-6">
                <div class="publisher-tools-tab js_publisher-tab" data-tab="album">
                  {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
                  {__("Create Album")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_add_activity_posts']}
              <li class="col-md-6">
                <div class="publisher-tools-tab js_publisher-feelings">
                  {include file='__svg_icons.tpl' icon="smile" class="main-icon mr5" width="24px" height="24px"}
                  {__("Feelings/Activity")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_add_geolocation_posts']}
              <li class="col-md-6">
                <div class="publisher-tools-tab js_publisher-tab" data-tab="location">
                  {include file='__svg_icons.tpl' icon="map" class="main-icon mr5" width="24px" height="24px"}
                  {__("Check In")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_add_colored_posts']}
              <li class="col-md-6">
                <div class="publisher-tools-tab js_publisher-tab" data-tab="colored">
                  {include file='__svg_icons.tpl' icon="posts_colored" class="main-icon mr5" width="24px" height="24px"}
                  {__("Colored Posts")}
                </div>
              </li>
            {/if}
            {if $system['voice_notes_posts_enabled']}
              <li class="col-md-6">
                <div class="publisher-tools-tab js_publisher-tab" data-tab="voice_notes">
                  {include file='__svg_icons.tpl' icon="voice_notes" class="main-icon mr5" width="24px" height="24px"}
                  {__("Voice Notes")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_add_gif_posts']}
              <li class="col-md-6">
                <div class="publisher-tools-tab js_publisher-tab" data-tab="gif">
                  {include file='__svg_icons.tpl' icon="gif" class="main-icon mr5" width="24px" height="24px"}
                  {__("GIF")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_write_articles'] && in_array($_handle, ['me', 'page', 'group','event'])}
              <li class="col-md-6">
                <a class="publisher-tools-tab link js_publisher-tab" data-tab="article" href='{$system['system_url']}/blogs/new{if $_handle == "page"}?page={$_id}{/if}{if $_handle == "group"}?group={$_id}{/if}{if $_handle == "event"}?event={$_id}{/if}'>
                  {include file='__svg_icons.tpl' icon="articles" class="main-icon mr5" width="24px" height="24px"}
                  {__("Write Article")}
                </a>
              </li>
            {/if}
            {if $user->_data['can_sell_products'] && $_handle == "me"}
              <li class="col-md-6">
                <div class="publisher-tools-tab link js_publisher-tab" data-tab="product" data-toggle="modal" data-url="posts/product.php?do=create">
                  {include file='__svg_icons.tpl' icon="products" class="main-icon mr5" width="24px" height="24px"}
                  {__("Sell Something")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_raise_funding'] && $_handle == "me"}
              <li class="col-md-6">
                <div class="publisher-tools-tab link js_publisher-tab" data-tab="funding" data-toggle="modal" data-url="posts/funding.php?do=create">
                  {include file='__svg_icons.tpl' icon="funding" class="main-icon mr5" width="24px" height="24px"}
                  {__("Raise Funding")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_create_offers'] && in_array($_handle, ['me', 'page', 'group','event'])}
              <li class="col-md-6">
                <div class="publisher-tools-tab link js_publisher-tab" data-tab="offer" data-toggle="modal" data-url="posts/offer.php?do=create{if $_handle == "page"}&page={$_id}{/if}{if $_handle == "group"}&group={$_id}{/if}{if $_handle == "event"}&event={$_id}{/if}">
                  {include file='__svg_icons.tpl' icon="offers" class="main-icon mr5" width="24px" height="24px"}
                  {__("Create Offer")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_create_jobs'] && in_array($_handle, ['me', 'page', 'group','event'])}
              <li class="col-md-6">
                <div class="publisher-tools-tab link js_publisher-tab" data-tab="job" data-toggle="modal" data-url="posts/job.php?do=create{if $_handle == "page"}&page={$_id}{/if}{if $_handle == "group"}&group={$_id}{/if}{if $_handle == "event"}&event={$_id}{/if}">
                  {include file='__svg_icons.tpl' icon="jobs" class="main-icon mr5" width="24px" height="24px"}
                  {__("Create Job")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_add_polls_posts']}
              <li class="col-md-6">
                <div class="publisher-tools-tab js_publisher-tab" data-tab="poll">
                  {include file='__svg_icons.tpl' icon="polls" class="main-icon mr5" width="24px" height="24px"}
                  {__("Create Poll")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_upload_videos']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab" data-tab="video">
                  <span class="js_x-uploader" data-handle="publisher" data-type="video">
                    {include file='__svg_icons.tpl' icon="videos" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload Video")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_upload_audios']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab" data-tab="audio">
                  <span class="js_x-uploader" data-handle="publisher" data-type="audio">
                    {include file='__svg_icons.tpl' icon="audios" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload Audio")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_upload_files']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab" data-tab="file">
                  <span class="js_x-uploader" data-handle="publisher" data-type="file">
                    {include file='__svg_icons.tpl' icon="files" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload File")}
                </div>
              </li>
            {/if}
          </ul>
        </div>
        <!-- publisher-tools-tabs -->

        <!-- publisher-footer -->
        <div class="publisher-footer">
          {if $system['adult_mode'] || ($_handle == "me" && $user->_data['can_add_anonymous_posts']) || ($_handle != "page" && $user->_data['can_receive_tip']) || (in_array($_handle, ['me', 'page', 'group']) && $_node_can_monetize_content && $_node_monetization_enabled && $_node_monetization_plans > 0) || (in_array($_handle, ['me', 'page', 'group','event']) && $user->_data['can_monetize_content'] && $user->_data['user_monetization_enabled']) }
            <!-- publisher-options -->
            <div class="publisher-footer-options">

              <!-- anonymous post -->
              {if $_handle == "me" && $user->_data['can_add_anonymous_posts']}
                <div class="form-table-row mb10">
                  <div class="avatar">
                    {include file='__svg_icons.tpl' icon="spy" class="main-icon" width="24px" height="24px"}
                  </div>
                  <div>
                    <div class="form-label mb0">{__("Anonymous Post")}</div>
                    <div class="form-text d-none d-sm-block mt0">{__("Share your post as anonymous post")}</div>
                  </div>
                  <div class="text-end">
                    <label class="switch" for="is_anonymous">
                      <input type="checkbox" name="is_anonymous" id="is_anonymous" class="js_publisher-anonymous-toggle">
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              {/if}
              <!-- anonymous post -->

              <!-- adult content -->
              {if $system['adult_mode']}
                <div class="form-table-row mb10">
                  <div class="avatar">
                    {include file='__svg_icons.tpl' icon="adult" class="main-icon" width="24px" height="24px"}
                  </div>
                  <div>
                    <div class="form-label mb0">{__("Adult Content")}</div>
                    <div class="form-text d-none d-sm-block mt0">{__("Share your post as adult content")}</div>
                  </div>
                  <div class="text-end">
                    <label class="switch" for="for_adult">
                      <input type="checkbox" name="for_adult" id="for_adult" class="js_publisher-adult-toggle">
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              {/if}
              <!-- adult content -->

              <!-- enable tips -->
              {if $_handle != "page" && $user->_data['can_receive_tip']}
                <div class="form-table-row mb10">
                  <div class="avatar">
                    {include file='__svg_icons.tpl' icon="tips" class="main-icon" width="24px" height="24px"}
                  </div>
                  <div>
                    <div class="form-label mb0">{__("Enable Tips")}</div>
                    <div class="form-text d-none d-sm-block mt0">{__("Allow people to send you tips")}</div>
                  </div>
                  <div class="text-end">
                    <label class="switch" for="tips_enabled">
                      <input type="checkbox" name="tips_enabled" id="tips_enabled" class="js_publisher-tips-toggle">
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              {/if}
              <!-- enable tips -->

              <!-- only for subscribers -->
              {if in_array($_handle, ['me', 'page', 'group']) && $_node_can_monetize_content && $_node_monetization_enabled && $_node_monetization_plans > 0 }
                <div class="form-table-row mb10" id="subscribers-toggle-wrapper">
                  <div class="avatar">
                    {include file='__svg_icons.tpl' icon="groups" class="main-icon" width="24px" height="24px"}
                  </div>
                  <div>
                    <div class="form-label mb0">{__("Subscribers Only")}</div>
                    <div class="form-text d-none d-sm-block mt0">{__("Share this post to")} {if $_handle != "me"}{__($_handle)} {/if}{__("subscribers only")}</div>
                  </div>
                  <div class="text-end">
                    <label class="switch" for="subscribers_only">
                      <input type="checkbox" name="subscribers_only" id="subscribers_only" class="js_publisher-subscribers-toggle">
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              {/if}
              <!-- only for subscribers -->

              <!-- paid post -->
              {if in_array($_handle, ['me', 'page', 'group','event']) && $user->_data['can_monetize_content'] && $user->_data['user_monetization_enabled']}
                <div class="form-table-row mb10" id="paid-toggle-wrapper">
                  <div class="avatar">
                    {include file='__svg_icons.tpl' icon="monetization" class="main-icon" width="24px" height="24px"}
                  </div>
                  <div>
                    <div class="form-label mb0">{__("Paid Post")}</div>
                    <div class="form-text d-none d-sm-block mt0">{__("Set a price to your post")}</div>
                  </div>
                  <div class="text-end">
                    <label class="switch" for="paid_post">
                      <input type="checkbox" name="paid_post" id="paid_post" class="js_publisher-paid-toggle">
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
                <div class="form-group x-hidden" id="paid-price-wrapper">
                  <input type="text" class="form-control" name="paid_post_price" placeholder="{__("Price")} ({$system['system_currency']})">
                </div>
                <div class="form-group x-hidden" id="paid-text-wrapper">
                  <textarea class="form-control" name="paid_post_text" rows="3" placeholder="{__("Paid Post Description")}"></textarea>
                </div>
              {/if}
              <!-- paid post -->

            </div>
          {/if}
          <!-- publisher-options -->

          <!-- publisher-buttons -->
          <div class="publisher-footer-buttons">
            {if $_privacy}
              {if $system['newsfeed_source'] == "default"}
                <!-- privacy -->
                {if $system['default_privacy'] == "me"}
                  <div class="btn-group js_publisher-privacy" data-value="me">
                    <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
                      <i class="btn-group-icon fa fa-lock mr10"></i><span class="btn-group-text">{__("Only Me")}</span>
                    </button>
                    <div class="dropdown-menu">
                      <div class="dropdown-item pointer" data-value="public">
                        <i class="fa fa-globe mr5"></i>{__("Public")}
                      </div>
                      <div class="dropdown-item pointer" data-value="friends">
                        <i class="fa fa-users mr5"></i>{__("Friends")}
                      </div>
                      {if $_handle == 'me'}
                        <div class="dropdown-item pointer" data-value="me">
                          <i class="fa fa-lock mr5"></i>{__("Only Me")}
                        </div>
                      {/if}
                    </div>
                  </div>
                {elseif $system['default_privacy'] == "friends"}
                  <div class="btn-group js_publisher-privacy" data-value="friends">
                    <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
                      <i class="btn-group-icon fa fa-users mr10"></i><span class="btn-group-text">{__("Friends")}</span>
                    </button>
                    <div class="dropdown-menu">
                      <div class="dropdown-item pointer" data-value="public">
                        <i class="fa fa-globe mr5"></i>{__("Public")}
                      </div>
                      <div class="dropdown-item pointer" data-value="friends">
                        <i class="fa fa-users mr5"></i>{__("Friends")}
                      </div>
                      {if $_handle == 'me'}
                        <div class="dropdown-item pointer" data-value="me">
                          <i class="fa fa-lock mr5"></i>{__("Only Me")}
                        </div>
                      {/if}
                    </div>
                  </div>
                {else}
                  <div class="btn-group js_publisher-privacy" data-value="public">
                    <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
                      <i class="btn-group-icon fa fa-globe mr10"></i><span class="btn-group-text">{__("Public")}</span>
                    </button>
                    <div class="dropdown-menu">
                      <div class="dropdown-item pointer" data-value="public">
                        <i class="fa fa-globe mr5"></i>{__("Public")}
                      </div>
                      <div class="dropdown-item pointer" data-value="friends">
                        <i class="fa fa-users mr5"></i>{__("Friends")}
                      </div>
                      {if $_handle == 'me'}
                        <div class="dropdown-item pointer" data-value="me">
                          <i class="fa fa-lock mr5"></i>{__("Only Me")}
                        </div>
                      {/if}
                    </div>
                  </div>
                {/if}

                {if $_handle == "me" && $system['anonymous_mode']}
                  <button disabled="disabled" type="button" class="btn btn-light x-hidden js_publisher-privacy-public">
                    <i class="btn-group-icon fa fa-globe mr10"></i><span class="btn-group-text">{__("Public")}</span>
                  </button>
                {/if}
                <!-- privacy -->
              {/if}
            {/if}
            <div class="d-grid">
              {if $_post_as_page}
                <input type="hidden" name="post_as_page" value="{$_page_id}">
              {/if}
              <button type="button" class="btn btn-primary ml5 js_publisher-btn js_publisher">{__("Post")}</button>
            </div>
            <!-- publisher-buttons -->
          </div>
        </div>
        <!-- publisher-footer -->
      </div>
      <!-- publisher-slider -->
    </div>
  {/if}
{/if}