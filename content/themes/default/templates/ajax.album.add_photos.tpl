<div class="modal-body plr0 ptb0">
  <div class="x-form publisher mini" data-id="{$album['album_id']}">

    <!-- publisher close -->
    <div class="publisher-close">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
    </div>
    <!-- publisher close -->

    <!-- publisher-message -->
    <div class="publisher-message no-avatar">
      <textarea dir="auto" class="js_autosize js_mention" placeholder='{__("What is on your mind? #Hashtag.. @Mention.. Link..")}'></textarea>
      <div class="publisher-emojis" style="display: block;">
        <div class="position-relative">
          <span class="js_emoji-menu-toggle">
            <i class="far fa-smile-wink fa-lg"></i>
          </span>
        </div>
      </div>
    </div>
    <!-- publisher-message -->

    <!-- post attachments -->
    <div class="publisher-attachments attachments clearfix x-hidden js_attachments-photos"></div>
    <!-- post attachments -->

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

    <!-- publisher-tools-tabs -->
    <div class="publisher-tools-tabs">
      <ul class="row">
        {if $system['photos_enabled']}
          <li class="col-md-6">
            <div class="publisher-tools-tab attach js_publisher-tab" data-tab="photos">
              <span class="js_x-uploader" data-handle="publisher" data-multiple="true">
                {include file='__svg_icons.tpl' icon="photos" class="main-icon mr5" width="24px" height="24px"}
              </span>
              {__("Upload Photos")}
            </div>
          </li>
        {/if}
        {if $system['activity_posts_enabled']}
          <li class="col-md-6">
            <div class="publisher-tools-tab js_publisher-feelings">
              {include file='__svg_icons.tpl' icon="smile" class="main-icon mr5" width="24px" height="24px"}
              {__("Feelings/Activity")}
            </div>
          </li>
        {/if}
        {if $system['geolocation_enabled']}
          <li class="col-md-6">
            <div class="publisher-tools-tab js_publisher-tab" data-tab="location">
              {include file='__svg_icons.tpl' icon="map" class="main-icon mr5" width="24px" height="24px"}
              {__("Check In")}
            </div>
          </li>
        {/if}
      </ul>
    </div>
    <!-- publisher-tools-tabs -->

    <!-- publisher-footer -->
    <div class="publisher-footer">
      <div class="publisher-footer-buttons">
        <!-- publisher-buttons -->
        {if $album['user_type'] == 'user' && !$album['in_group'] && !$album['in_event']}
          <!-- privacy -->
          <div class="btn-group" data-value="friends">
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
              <div class="dropdown-item pointer" data-value="me">
                <i class="fa fa-lock mr5"></i>{__("Only Me")}
              </div>
            </div>
          </div>
          <!-- privacy -->
        {else}
          <!-- privacy -->
          {if $album['privacy'] == "custom"}
            <div class="btn-group" data-value="custom">
              <button type="button" class="btn btn-light">
                <i class="btn-group-icon fa fa-cog mr10"></i> <span class="btn-group-text">{__("Custom")}</span>
              </button>
            </div>
          {elseif $album['privacy'] == "public"}
            <div class="btn-group" data-value="public">
              <button type="button" class="btn btn-light">
                <i class="btn-group-icon fa fa-users mr10"></i> <span class="btn-group-text">{__("Public")}</span>
              </button>
            </div>
          {/if}
          <!-- privacy -->
        {/if}

        <div class="d-grid">
          <button type="button" class="btn btn-primary ml5 js_publisher-btn js_publisher-album">{__("Post")}</button>
        </div>
        <!-- publisher-buttons -->
      </div>
    </div>
    <!-- publisher-footer -->
  </div>
</div>