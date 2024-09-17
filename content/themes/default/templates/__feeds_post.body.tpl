<!-- post header -->
<div class="post-header">

  <!-- post picture -->
  <div class="post-avatar">
    {if $_post['is_anonymous']}
      <div class="post-avatar-anonymous">
        {include file='__svg_icons.tpl' icon="spy" class="main-icon" width="40px" height="40px"}
      </div>
    {else}
      <a class="post-avatar-picture" href="{$_post['post_author_url']}" style="background-image:url({$_post['post_author_picture']});">
      </a>
      {if $_post['post_author_online']}<i class="fa fa-circle online-dot"></i>{/if}
    {/if}
  </div>
  <!-- post picture -->

  <!-- post meta -->
  <div class="post-meta">
    <!-- post menu -->
    {if $user->_logged_in && !$_shared && $_get != "posts_information"}
      <div class="float-end dropdown">
        <i class="fa fa-chevron-down dropdown-toggle" data-bs-toggle="dropdown" data-display="static"></i>
        <div class="dropdown-menu dropdown-menu-end action-dropdown-menu">
          {if $_post['manage_post'] && $_post['post_type'] == "product"}
            {if $_post['product']['available']}
              <div class="dropdown-item pointer js_sold-post">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="products" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Mark as Sold")}</span>
                </div>
              </div>
            {else}
              <div class="dropdown-item pointer js_unsold-post">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="products" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Mark as Available")}</span>
                </div>
              </div>
            {/if}
            <div class="dropdown-divider"></div>
          {/if}
          {if $_post['manage_post'] && $_post['post_type'] == "job"}
            {if $_post['job']['available']}
              <div class="dropdown-item pointer js_closed-post">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="jobs" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Mark as Closed")}</span>
                </div>
              </div>
            {else}
              <div class="dropdown-item pointer js_unclosed-post">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="jobs" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Mark as Available")}</span>
                </div>
              </div>
            {/if}
            <div class="dropdown-divider"></div>
          {/if}
          {if $_post['i_save']}
            <div href="#" class="dropdown-item pointer js_unsave-post">
              <div class="action no-desc">
                {include file='__svg_icons.tpl' icon="saved" class="main-icon mr10" width="20px" height="20px"}
                <span>{__("Unsave Post")}</span>
              </div>
            </div>
          {else}
            <div class="dropdown-item pointer js_save-post">
              <div class="action no-desc">
                {include file='__svg_icons.tpl' icon="saved" class="main-icon mr10" width="20px" height="20px"}
                <span>{__("Save Post")}</span>
              </div>
            </div>
          {/if}
          <div class="dropdown-divider"></div>
          {if $_post['manage_post']}
            <!-- Boost -->
            {if $system['packages_enabled'] && !$_post['in_group'] && !$_post['in_event']}
              {if $_post['boosted']}
                <div class="dropdown-item pointer js_unboost-post">
                  <div class="action no-desc">
                    {include file='__svg_icons.tpl' icon="boosted" class="main-icon mr10" width="20px" height="20px"}
                    <span>{__("Unboost Post")}</span>
                  </div>
                </div>
              {else}
                {if $user->_data['can_boost_posts']}
                  <div class="dropdown-item pointer js_boost-post">
                    <div class="action no-desc">
                      {include file='__svg_icons.tpl' icon="boosted" class="main-icon mr10" width="20px" height="20px"}
                      <span>{__("Boost Post")}</span>
                    </div>
                  </div>
                {else}
                  <a href="{$system['system_url']}/packages" class="dropdown-item">
                    <div class="action no-desc">
                      {include file='__svg_icons.tpl' icon="boosted" class="main-icon mr10" width="20px" height="20px"}
                      <span>{__("Boost Post")}</span>
                    </div>
                  </a>
                {/if}
              {/if}
            {/if}
            <!-- Boost -->
            <!-- Pin -->
            {if !$_post['is_anonymous']}
              {if (!$_post['in_group'] && !$_post['in_event']) || ($_post['in_group'] && $_post['is_group_admin']) || ($_post['in_event'] && $_post['is_event_admin'])}
                {if $_post['pinned']}
                  <div class="dropdown-item pointer js_unpin-post">
                    <div class="action no-desc">
                      {include file='__svg_icons.tpl' icon="pin" class="main-icon mr10" width="20px" height="20px"}
                      <span>{__("Unpin Post")}</span>
                    </div>
                  </div>
                {else}
                  <div class="dropdown-item pointer js_pin-post">
                    <div class="action no-desc">
                      {include file='__svg_icons.tpl' icon="pin" class="main-icon mr10" width="20px" height="20px"}
                      <span>{__("Pin Post")}</span>
                    </div>
                  </div>
                {/if}
              {/if}
            {/if}
            <!-- Pin -->
            <!-- Edit -->
            {if $_post['post_type'] == "article"}
              <a href="{$system['system_url']}/blogs/edit/{$_post['post_id']}" class="dropdown-item pointer">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="edit" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Edit Article")}</span>
                </div>
              </a>
            {elseif $_post['post_type'] == "product"}
              <div class="dropdown-item pointer" data-toggle="modal" data-url="posts/product.php?do=edit&post_id={$_post['post_id']}">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="edit" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Edit Product")}</span>
                </div>
              </div>
            {elseif $_post['post_type'] == "funding"}
              <div class="dropdown-item pointer" data-toggle="modal" data-url="posts/funding.php?do=edit&post_id={$_post['post_id']}">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="edit" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Edit Funding")}</span>
                </div>
              </div>
            {elseif $_post['post_type'] == "offer"}
              <div class="dropdown-item pointer" data-toggle="modal" data-url="posts/offer.php?do=edit&post_id={$_post['post_id']}">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="edit" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Edit Offer")}</span>
                </div>
              </div>
            {elseif $_post['post_type'] == "job"}
              <div class="dropdown-item pointer" data-toggle="modal" data-url="posts/job.php?do=edit&post_id={$_post['post_id']}">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="edit" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Edit Job")}</span>
                </div>
              </div>
            {else}
              <div class="dropdown-item pointer js_edit-post">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="edit" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Edit Post")}</span>
                </div>
              </div>
            {/if}
            <!-- Edit -->
            <!-- Monetization -->
            {if $_post['can_be_for_subscriptions']}
              {if $_post['for_subscriptions']}
                <div class="dropdown-item pointer js_unmonetize-post">
                  <div class="action no-desc">
                    {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="20px" height="20px"}
                    <span>{__("For Everyone")}</span>
                  </div>
                </div>
              {else}
                <div class="dropdown-item pointer js_monetize-post">
                  <div class="action no-desc">
                    {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="20px" height="20px"}
                    <span>{__("For Subscribers Only")}</span>
                  </div>
                </div>
              {/if}
            {/if}
            <!-- Monetization -->
            <!-- Delete -->
            <div class="dropdown-item pointer js_delete-post">
              <div class="action no-desc">
                {include file='__svg_icons.tpl' icon="delete" class="main-icon mr10" width="20px" height="20px"}
                <span>{__("Delete Post")}</span>
              </div>
            </div>
            <!-- Delete -->
            <!-- Hide -->
            {if $_post['user_type'] == "user" && !$_post['in_group'] && !$_post['in_event'] && !$_post['is_anonymous']}
              {if $_post['is_hidden']}
                <div class="dropdown-item pointer js_allow-post">
                  <div class="action no-desc">
                    {include file='__svg_icons.tpl' icon="unhide" class="main-icon mr10" width="20px" height="20px"}
                    <span>{__("Allow on Timeline")}</span>
                  </div>
                </div>
              {else}
                <div class="dropdown-item pointer js_disallow-post">
                  <div class="action no-desc">
                    {include file='__svg_icons.tpl' icon="hide" class="main-icon mr10" width="20px" height="20px"}
                    <span>{__("Hide from Timeline")}</span>
                  </div>
                </div>
              {/if}
            {/if}
            <!-- Hide -->
            <!-- Disable Comments -->
            {if $_post['comments_disabled']}
              <div class="dropdown-item pointer js_enable-post-comments">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="comments" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Turn on Commenting")}</span>
                </div>
              </div>
            {else}
              <div class="dropdown-item pointer js_disable-post-comments">
                <div class="action no-desc">
                  {include file='__svg_icons.tpl' icon="comments" class="main-icon mr10" width="20px" height="20px"}
                  <span>{__("Turn off Commenting")}</span>
                </div>
              </div>
            {/if}
            <!-- Disable Comments -->
          {else}
            {if $_post['user_type'] == "user" && !$_post['is_anonymous']}
              <div class="dropdown-item pointer js_hide-author" data-author-id="{$_post['user_id']}" data-author-name="{$_post['post_author_name']}">
                <div class="action">
                  {include file='__svg_icons.tpl' icon="block" class="main-icon mr10" width="20px" height="20px"}
                  {__("Unfollow")} {if $system['show_usernames_enabled']}{$_post['user_name']}{else}{$_post['user_firstname']}{/if}
                </div>
                <div class="action-desc">{__("Stop seeing posts but stay friends")}</div>
              </div>
            {/if}
            <div class="dropdown-item pointer js_hide-post">
              <div class="action">
                {include file='__svg_icons.tpl' icon="hide" class="main-icon mr10" width="20px" height="20px"}
                {__("Hide this post")}
              </div>
              <div class="action-desc">{__("See fewer posts like this")}</div>
            </div>
            <div class="dropdown-item pointer" data-toggle="modal" data-url="data/report.php?do=create&handle=post&id={$_post['post_id']}">
              <div class="action no-desc">
                {include file='__svg_icons.tpl' icon="report" class="main-icon mr10" width="20px" height="20px"}
                <span>{__("Report post")}</span>
              </div>
            </div>
          {/if}
          <div class="dropdown-divider"></div>
          <a href="{$system['system_url']}/posts/{$_post['post_id']}" target="_blank" class="dropdown-item">
            <div class="action no-desc">
              {include file='__svg_icons.tpl' icon="link" class="main-icon mr10" width="20px" height="20px"}
              <span>{__("Open post in new tab")}</span>
            </div>
          </a>
          {if $_post['is_anonymous'] && ($user->_is_admin || $user->_is_moderator)}
            <div class="dropdown-divider"></div>
            <a href="{$_post['post_author_url']}" target="_blank" class="dropdown-item">
              <div class="action no-desc">
                {include file='__svg_icons.tpl' icon="spy" class="main-icon mr10" width="20px" height="20px"}
                <span>{__("Open Author Profile")}</span>
              </div>
            </a>
          {/if}
        </div>
      </div>
    {/if}
    <!-- post menu -->

    <!-- post author -->
    {if $_post['is_anonymous']}
      <span class="post-author">{__("Anonymous")}</span>
    {else}
      <span class="js_user-popover" data-type="{$_post['user_type']}" data-uid="{$_post['user_id']}">
        <a class="post-author" href="{$_post['post_author_url']}">{$_post['post_author_name']}</a>
      </span>
      {if $_post['post_author_verified']}
        <span class="verified-badge" data-bs-toggle="tooltip" title='{if $_post['user_type'] == "user"}{__("Verified User")}{else}{__("Verified Page")}{/if}'>
          {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
        </span>
      {/if}
      {if $_post['user_subscribed']}
        <span class="pro-badge" data-bs-toggle="tooltip" title='{__($_post['package_name'])} {__("Member")}'>
          {include file='__svg_icons.tpl' icon="pro_badge" width="20px" height="20px"}
        </span>
      {/if}
    {/if}
    <!-- post author -->

    <!-- post-title -->
    <span class="post-title">
      {if !$_shared && $_post['post_type'] == "shared"}
        {__("shared")}
        {if $_post['origin']['is_anonymous']}
          {__("Anonymous post")}
        {else}
          <span class="js_user-popover" data-type="{$_post['origin']['user_type']}" data-uid="{$_post['origin']['user_id']}">
            <a href="{$_post['origin']['post_author_url']}">
              {$_post['origin']['post_author_name']}
            </a>{__("'s")}
          </span>
          <a href="{$system['system_url']}/posts/{$_post['origin']['post_id']}">
            {if $_post['origin']['post_type'] == 'link'}
              {__("link")}

            {elseif $_post['origin']['post_type'] == 'media'}
              {if $_post['origin']['media']['media_type'] != "soundcloud"}
                {__("video")}
              {else}
                {__("song")}
              {/if}

            {elseif $_post['origin']['post_type'] == 'photos'}
              {if $_post['origin']['photos_num'] > 1}{__("photos")}{else}{__("photo")}{/if}

            {elseif $_post['origin']['post_type'] == 'album'}
              {__("album")}

            {elseif $_post['origin']['post_type'] == 'poll'}
              {__("poll")}

            {elseif $_post['origin']['post_type'] == 'video'}
              {__("video")}

            {elseif $_post['origin']['post_type'] == 'audio'}
              {__("audio")}

            {elseif $_post['origin']['post_type'] == 'file'}
              {__("file")}

            {else}
              {__("post")}
            {/if}
          </a>
        {/if}

      {elseif $_post['post_type'] == "link"}
        {__("shared a link")}

      {elseif $_post['post_type'] == "live"}
        {if $_post['live']['live_ended']}
          {__("was live")}
        {else}
          {__("is live now")}
        {/if}

      {elseif $_post['post_type'] == "photos"}
        {if $_post['photos_num'] == 1}
          {__("added a photo")}
        {else}
          {__("added")} {$_post['photos_num']} {__("photos")}
        {/if}

      {elseif $_post['post_type'] == "album"}
        {__("added")} {$_post['photos_num']} {__("photos to the album")}: <a href="{$system['system_url']}/{$_post['album']['path']}/album/{$_post['album']['album_id']}">{$_post['album']['title']}</a>

      {elseif $_post['post_type'] == "profile_picture"}
        {__("updated the profile picture")}

      {elseif $_post['post_type'] == "profile_cover"}
        {__("updated the cover photo")}

      {elseif $_post['post_type'] == "page_picture"}
        {__("updated page picture")}

      {elseif $_post['post_type'] == "page_cover"}
        {__("updated cover photo")}

      {elseif $_post['post_type'] == "group_picture"}
        {__("updated group picture")}

      {elseif $_post['post_type'] == "group_cover"}
        {__("updated group cover")}

      {elseif $_post['post_type'] == "event_cover"}
        {__("updated event cover")}

      {elseif $_post['post_type'] == "article"}
        {__("added article")} <a href="{$system['system_url']}/blogs/category/{$_post['article']['category_id']}/{$_post['article']['category_url']}" class="article-category text-no-underline">{__($_post['article']['category_name'])}</a>

      {elseif $_post['post_type'] == "product"}
        {__("added product for sale")}

      {elseif $_post['post_type'] == "funding"}
        {__("raised funding request")}

      {elseif $_post['post_type'] == "offer"}
        {__("added offer")}

      {elseif $_post['post_type'] == "job"}
        {__("added job")}

      {elseif $_post['post_type'] == "poll"}
        {__("added poll")}

      {elseif $_post['post_type'] == "video"}
        {__("added video")} <span class="badge rounded-pill badge-lg bg-info">{__($_post['video']['category_name'])}</span>

      {elseif $_post['post_type'] == "audio"}
        {__("added audio")}

      {elseif $_post['post_type'] == "file"}
        {__("added file")}

      {/if}

      {if $_get != 'posts_group' && $_post['in_group']}
        <i class="fa fa-chevron-right ml5 mr5"></i>
        {include file='__svg_icons.tpl' icon="groups" class="main-icon mr5" width="20px" height="20px"}
        <a href="{$system['system_url']}/groups/{$_post['group_name']}">{$_post['group_title']}</a>
      {elseif $_get != 'posts_event' && $_post['in_event']}
        <i class="fa fa-chevron-right ml5 mr5"></i>
        {include file='__svg_icons.tpl' icon="events" class="main-icon mr5" width="20px" height="20px"}
        <a href="{$system['system_url']}/events/{$_post['event_id']}">{$_post['event_title']}</a>
      {elseif $_post['in_wall']}
        <i class="fa fa-chevron-right ml5 mr5"></i>
        <span class="js_user-popover" data-type="user" data-uid="{$_post['wall_id']}">
          <a href="{$system['system_url']}/{$_post['wall_username']}">{$_post['wall_fullname']}</a>
        </span>
      {/if}
    </span>
    <!-- post-title -->

    <!-- post feeling -->
    {if $_post['feeling_action']}
      <span class="post-title">
        {if $_post['post_type'] != "" && $_post['post_type'] != "map"  && $_post['post_type'] != "media"} & {/if}{__("is")} {__($_post["feeling_action"])} {__($_post["feeling_value"])} <i class="twa twa-lg twa-{$_post['feeling_icon']}"></i>
      </span>
    {/if}
    <!-- post feeling -->

    <!-- post time & location & privacy -->
    <div class="post-time">
      <a href="{$system['system_url']}/posts/{$_post['post_id']}" class="js_moment" data-time="{$_post['time']}">{$_post['time']}</a>
      {if $_post['location']}
        - <i class="fa fa-map-marker"></i> <span>{$_post['location']}</span>
      {/if}
      {if $system['post_translation_enabled']}
        - <span class="text-link js_translator">{__("Translate")}</span>
      {/if}
      {if $system['newsfeed_source'] == "default"}
        -
        {if !$_post['is_anonymous'] && !$_shared && $_post['manage_post'] && $_post['user_type'] == 'user' && !$_post['in_group'] && !$_post['in_event'] && $_post['post_type'] != "article" && $_post['post_type'] != "product" && $_post['post_type'] != "funding"}
          <!-- privacy -->
          {if $_post['privacy'] == "me"}
            <div class="btn-group" data-bs-toggle="tooltip" data-value="me" title='{__("Shared with: Only Me")}'>
              <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
                <i class="btn-group-icon fa fa-lock"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-left">
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Public")}' data-value="public">
                  <i class="fa fa-globe"></i> {__("Public")}
                </div>
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Friends")}' data-value="friends">
                  <i class="fa fa-users"></i> {__("Friends")}
                </div>
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Only Me")}' data-value="me">
                  <i class="fa fa-lock"></i> {__("Only Me")}
                </div>
              </div>
            </div>
          {elseif $_post['privacy'] == "friends"}
            <div class="btn-group" data-bs-toggle="tooltip" data-value="friends" title='{__("Shared with: Friends")}'>
              <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
                <i class="btn-group-icon fa fa-users"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-left">
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Public")}' data-value="public">
                  <i class="fa fa-globe"></i> {__("Public")}
                </div>
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Friends")}' data-value="friends">
                  <i class="fa fa-users"></i> {__("Friends")}
                </div>
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Only Me")}' data-value="me">
                  <i class="fa fa-lock"></i> {__("Only Me")}
                </div>
              </div>
            </div>
          {elseif $_post['privacy'] == "public"}
            <div class="btn-group" data-bs-toggle="tooltip" data-value="public" title='{__("Shared with: Public")}'>
              <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
                <i class="btn-group-icon fa fa-globe"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-left">
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Public")}' data-value="public">
                  <i class="fa fa-globe"></i> {__("Public")}
                </div>
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Friends")}' data-value="friends">
                  <i class="fa fa-users"></i> {__("Friends")}
                </div>
                <div class="dropdown-item pointer js_edit-privacy" data-title='{__("Shared with: Only Me")}' data-value="me">
                  <i class="fa fa-lock"></i> {__("Only Me")}
                </div>
              </div>
            </div>
          {/if}

          <!-- privacy -->
        {else}
          {if $_post['privacy'] == "me"}
            <i class="fa fa-lock" data-bs-toggle="tooltip" title='{__("Shared with")} {__("Only Me")}'></i>
          {elseif $_post['privacy'] == "friends"}
            <i class="fa fa-users" data-bs-toggle="tooltip" title='{__("Shared with")} {__("Friends")}'></i>
          {elseif $_post['privacy'] == "public"}
            <i class="fa fa-globe" data-bs-toggle="tooltip" title='{__("Shared with")} {__("Public")}'></i>
          {elseif $_post['privacy'] == "custom"}
            <i class="fa fa-cog" data-bs-toggle="tooltip" title='{__("Shared with")} {__("Custom People")}'></i>
          {/if}
        {/if}
      {/if}
      {if $_post['for_subscriptions']}
        <span class="badge bg-light text-primary ml5"><i class="fa fa-star mr5"></i>{__("Subscriptions")|upper}</span>
      {/if}
      {if $_post['is_paid']}
        <span class="badge bg-light text-primary ml5"><i class="fa-solid fa-sack-dollar mr5"></i>{__("Paid")|upper}</span>
      {/if}
      {if $_post['for_adult']}
        <span class="badge bg-light text-primary ml5"><i class="fa-solid fa-eye-slash mr5"></i>{__("Adult")|upper}</span>
      {/if}

      {if $system['posts_reviews_enabled'] && $post['post_type'] != "product"}
        {if $_post['post_rate']}
          <span class="review-stars small ml5">
            <i class="fa fa-star {if $_post['post_rate'] >= 1}checked{/if}"></i>
            <i class="fa fa-star {if $_post['post_rate'] >= 2}checked{/if}"></i>
            <i class="fa fa-star {if $_post['post_rate'] >= 3}checked{/if}"></i>
            <i class="fa fa-star {if $_post['post_rate'] >= 4}checked{/if}"></i>
            <i class="fa fa-star {if $_post['post_rate'] >= 5}checked{/if}"></i>
          </span>
          <span class="badge bg-light text-primary">{$_post['post_rate']|number_format:1}</span>
        {/if}
      {/if}
    </div>
    <!-- post time & location & privacy -->
  </div>
  <!-- post meta -->
</div>
<!-- post header -->

{if !$_post['needs_payment'] && !$_post['needs_subscription'] && !$_post['needs_age_verification']}

  {if !$_post['needs_pro_package']}
    <!-- post text -->
    {if $_post['post_type'] != "product" && $_post['post_type'] != "funding" && $_post['post_type'] != "offer" && $_post['post_type'] != "job"}
      {if !$_shared}
        {include file='__feeds_post.text.tpl'}
      {else}
        {if $_post['colored_pattern']}
          <div class="post-colored" {if $_post['colored_pattern']['type'] == "color"} style="background-image: linear-gradient(45deg, {$_post['colored_pattern']['background_color_1']}, {$_post['colored_pattern']['background_color_2']});" {else} style="background-image: url({$system['system_uploads']}/{$_post['colored_pattern']['background_image']})" {/if}>
            <div class="post-colored-text-wrapper js_scroller" data-slimScroll-height="240">
              <div class="post-text" dir="auto" style="color: {$_post['colored_pattern']['text_color']};">
                {$_post['text']}
              </div>
            </div>
          </div>
        {else}
          <div class="post-text js_readmore" dir="auto">{$_post['text']}</div>
        {/if}
        <div class="post-text-translation x-hidden" dir="auto"></div>
      {/if}
    {/if}
    <!-- post text -->

    {if !$_shared && $_post['post_type'] == "shared" && $_post['origin']}
      <div class="post-snippet {if in_array($_post['origin']['post_type'], ['product', 'funding', 'job', 'poll'])}pb15{/if}">
        {if $_snippet}
          <div class="post-snippet-toggle text-link js_show-attachments">{__("Show Attachments")}</div>
        {/if}
        <div {if $_snippet}class="x-hidden" {/if}>
          {include file='__feeds_post.body.tpl' _post=$_post['origin'] _shared=true}
        </div>
      </div>
    {/if}

    {if $_post['post_type'] == "link" && $_post['link']}
      <div class="mt10 plr15">
        <div class="post-media">
          {if $_post['link']['source_thumbnail']}
            <a class="post-media-image" href="{$_post['link']['source_url']}" target="_blank" rel="nofollow">
              <div class="image" style="background-image:url('{$_post['link']['source_thumbnail']}');"></div>
              <div class="source">{$_post['link']['source_host']|upper}</div>
            </a>
          {/if}
          <div class="post-media-meta">
            <a class="title mb5" href="{$_post['link']['source_url']}" target="_blank" rel="nofollow">{$_post['link']['source_title']}</a>
            <div class="text mb5">{$_post['link']['source_text']}</div>
          </div>
        </div>
      </div>
    {/if}

    {if $_post['post_type'] == "media" && $_post['media']}
      <div class="mt10 plr15">
        {if $_post['media']['source_type'] == "photo"}
          <div class="post-media">
            <div class="post-media-image">
              <div class="image" style="background-image:url('{$_post['media']['source_url']}');"></div>
            </div>
            <div class="post-media-meta">
              <div class="source"><a target="_blank" href="{$_post['media']['source_url']}">{$_post['media']['source_provider']}</a></div>
            </div>
          </div>
        {else}
          {if $_post['media']['source_provider'] == "YouTube"}
            {if $system['smart_yt_player']}
              {$_post['media']['vidoe_id'] = get_youtube_id($_post['media']['source_html'])}
              <div class="youtube-player js_youtube-player" data-id="{$_post['media']['vidoe_id']}">
                <img src="https://i.ytimg.com/vi/{$_post['media']['vidoe_id']}/hqdefault.jpg">
                <div class="play"></div>
              </div>
            {else}
              <div class="post-media">
                <div class="ratio ratio-16x9">
                  {html_entity_decode($_post['media']['source_html'], ENT_QUOTES)}
                </div>
              </div>
            {/if}

          {elseif in_array($_post['media']['source_provider'], ["Vimeo", "Twitch", "Rumble.com", "Banned.Video", "Brighteon", "Odysee", "Gab TV"])}
            <div class="post-media">
              <div class="ratio ratio-16x9">
                {html_entity_decode($_post['media']['source_html'], ENT_QUOTES)}
              </div>
            </div>
          {elseif $_post['media']['source_provider'] == "Facebook"}
            <div class="embed-facebook-wrapper">
              {html_entity_decode($_post['media']['source_html'], ENT_QUOTES)}
              <div class="embed-facebook-placeholder ptb30">
                <div class="d-flex justify-content-center">
                  <div class="spinner-grow"></div>
                </div>
              </div>
            </div>
          {else}
            <div class="embed-iframe-wrapper">
              {html_entity_decode($_post['media']['source_html'], ENT_QUOTES)}
            </div>
          {/if}
        {/if}
      </div>
    {/if}

    {if $_post['post_type'] == "live" && $_post['live']}
      {if $system['save_live_enabled'] && $_post['live']['live_ended'] && $_post['live']['live_recorded']}
        <div class="overflow-hidden">
          <video class="js_videojs video-js vjs-fluid vjs-default-skin" id="video-{$_post['live']['live_id']}{if $pinned || $boosted}-{$_post['post_id']}{/if}" {if $_post['live']['video_thumbnail']}poster="{$system['system_uploads']}/{$_post['live']['video_thumbnail']}" {/if} controls preload="auto">
            <source src="{$system['system_agora_uploads']}/{$_post['live']['agora_file']}" type="application/x-mpegURL">
          </video>
        </div>
      {else}
        <div class="youtube-player with-live js_lightbox-live">
          <img src="{$system['system_uploads']}/{$_post['live']['video_thumbnail']}">
          <div class="play"></div>
        </div>
      {/if}
    {/if}

    {if ($_post['post_type'] == "photos" || $_post['post_type'] == "album" || $_post['post_type'] == "profile_picture" || $_post['post_type'] == "profile_cover" || $_post['post_type'] == "page_picture" || $_post['post_type'] == "page_cover" || $_post['post_type'] == "group_picture" || $_post['post_type'] == "group_cover" || $_post['post_type'] == "event_cover" || $_post['post_type'] == "product" || $_post['post_type'] == "combo") && $_post['photos_num'] > 0}
      <div class="mt10">
        {include file='__feeds_post.body.photos.tpl'}
      </div>
    {/if}

    {if $_post['post_type'] == "map"}
      <div class="post-map">
        <img src="https://maps.googleapis.com/maps/api/staticmap?center={$_post['location']}&amp;zoom=20&amp;size=600x250&amp;maptype=roadmap&amp;markers=color:red%7C{$_post['location']}&amp;key={$system['geolocation_key']}" width="100%">
      </div>
    {/if}

    {if $_post['post_type'] == "article" && $_post['article']}
      <div class="mt10 plr15">
        <div class="post-media">
          {if $_post['article']['cover']}
            <a class="post-media-image" href="{$system['system_url']}/blogs/{$_post['post_id']}/{$_post['article']['title_url']}">
              <div style="padding-top: 50%; background-size: cover; background-image:url('{$system['system_uploads']}/{$_post['article']['cover']}');"></div>
            </a>
          {/if}
          <div class="post-media-meta">
            <a class="title mb5" href="{$system['system_url']}/blogs/{$_post['post_id']}/{$_post['article']['title_url']}">{$_post['article']['title']}</a>
            <div class="text mb5">{$_post['article']['text_snippet']|truncate:400}</div>
          </div>
        </div>
      </div>
    {/if}

    {if $_post['post_type'] == "funding" && $_post['funding']}
      <div class="mt10">
        <div class="post-media">
          <a class="post-media-image" href="{$system['system_url']}/posts/{$_post['post_id']}" target="_blank">
            <div class="image" style="background-image:url('{$system['system_uploads']}/{$_post['funding']['cover_image']}');"></div>
            <div class="icon">
              {include file='__svg_icons.tpl' icon="funding" class="main-icon" width="32px" height="32px"}
            </div>
          </a>
        </div>
        <div class="post-funding-meta">
          <div class="funding-title mb10 mt20">
            {$_post['funding']['title']}
          </div>
          <div class="funding-completion mb10 mt20">
            <span class="float-end">{$_post['funding']['funding_completion']}%</span>
            <strong>{print_money($_post['funding']['raised_amount'])} {__("Raised of")} {print_money($_post['funding']['amount'])}</strong>
            <div class="progress mt5">
              <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="{$_post['funding']['funding_completion']}" aria-valuemin="0" aria-valuemax="100" style="width: {$_post['funding']['funding_completion']}%"></div>
            </div>
          </div>
          <div class="funding-description">
            <!-- post text -->
            {if !$_shared}
              {include file='__feeds_post.text.tpl'}
            {else}
              <div class="post-text js_readmore text-muted" dir="auto">{$_post['text']}</div>
              <div class="post-text-translation x-hidden" dir="auto"></div>
            {/if}
            <!-- post text -->
          </div>
          {if $user->_logged_in && $_post['author_id'] != $user->_data['user_id'] }
            <div class="mt10 d-grid">
              <button type="button" class="btn btn-success js_funding-donate" data-toggle="modal" data-url="#funding-donate" data-options='{ "post_id": {$_post["post_id"]} }'>
                {__("Donate")}
              </button>
            </div>
          {/if}
        </div>
      </div>
    {/if}

    {if $_post['post_type'] == "offer" && $_post['offer']}
      <div class="mt10 plr15">
        <div class="post-media">
          <div class="post-media-image">
            {if $_post['photos_num'] == 1}
              <div class="image" style="background-image:url('{$system['system_uploads']}/{$_post['offer']['thumbnail']}');"></div>
            {else $_post['photos']}
              {include file='__feeds_post.body.photos.tpl'}
            {/if}
            {if $_post['offer']['end_date']}
              <div class="source">
                <i class="far fa-calendar-alt mr5"></i>{__("Expires")}: <strong>{$_post['offer']['end_date']|date_format:$system['system_date_format']}</strong>
              </div>
            {/if}
            <div class="icon">
              {include file='__svg_icons.tpl' icon="offers" width="32px" height="32px"}
            </div>
          </div>
          <div class="post-media-meta">
            <span class="title text-active mb5 mt20">{$_post['offer']['meta_title']}</span>
            {if $_post['offer']['price']}
              <div class="text-success mtb5">
                {__("From")} <strong>{print_money($_post['offer']['price'])}</strong>
              </div>
            {/if}
            <!-- post text -->
            {if !$_shared}
              {include file='__feeds_post.text.tpl'}
            {else}
              <div class="post-text js_readmore text-muted" dir="auto">{$_post['text']}</div>
              <div class="post-text-translation x-hidden" dir="auto"></div>
            {/if}
            <!-- post text -->
            <!-- custom fileds -->
            {if $_post['custom_fields']['basic']}
              <div class="post-custom-fileds-wrapper mt10">
                {foreach $_post['custom_fields']['basic'] as $custom_field}
                  {if $custom_field['value']}
                    <div>
                      <strong>{__($custom_field['label'])}</strong><br>
                      {if $custom_field['type'] == "textbox" && $custom_field['is_link']}
                        <a href="{$custom_field['value']}">{$custom_field['value']}</a>
                      {elseif $custom_field['type'] == "multipleselectbox"}
                        {$custom_field['value_string']}
                      {else}
                        {$custom_field['value']}
                      {/if}
                    </div>
                  {/if}
                {/foreach}
              </div>
            {/if}
            <!-- custom fileds -->
          </div>
        </div>
      </div>
    {/if}

    {if $_post['post_type'] == "job" && $_post['job']}
      <div class="mt10 plr15">
        <div class="post-media">
          <a class="post-media-image" href="{$system['system_url']}/posts/{$_post['post_id']}" target="_blank">
            <div class="image" style="background-image:url('{$system['system_uploads']}/{$_post['job']['cover_image']}');"></div>
            <div class="source">
              <strong>{print_money($_post['job']['salary_minimum'])} - {print_money($_post['job']['salary_maximum'])} / {$_post['job']['pay_salary_per_meta']}</strong>
            </div>
            <div class="icon">
              {include file='__svg_icons.tpl' icon="jobs" width="32px" height="32px"}
            </div>
          </a>
        </div>
        <div class="post-job-meta">
          <div class="job-title mb10 mt20">
            <a href="{$system['system_url']}/posts/{$_post['post_id']}" target="_blank">{$_post['job']['title']}</a>
          </div>
          <div class="post-product-wrapper">
            <div class="post-product-details">
              <div class="title">
                <i class="fa fa-map-marker fa-fw mr5" style="color: #1f9cff;"></i>{__("Location")}
              </div>
              <div class="description">
                {$_post['job']['location']}
              </div>
            </div>
            <div class="post-product-details">
              <div class="title">
                <i class="fa fa-briefcase fa-fw mr5" style="color: #2bb431;"></i>{__("Type")}
              </div>
              <div class="description">
                {$_post['job']['type_meta']}
              </div>
            </div>
            <div class="post-product-details">
              <div class="title">
                <i class="fa fa-clock fa-fw mr5" style="color: #a038b2;"></i>{__("Status")}
              </div>
              <div class="description">
                {if $_post['job']['available']}
                  <span class="badge bg-success">{__("Open")}</span>
                {else}
                  <span class="badge bg-danger">{__("Closed")}</span>
                {/if}
              </div>
            </div>
          </div>
          <div class="job-description">
            <!-- post text -->
            {if !$_shared}
              {include file='__feeds_post.text.tpl'}
            {else}
              <div class="post-text js_readmore text-muted" dir="auto">{$_post['text']}</div>
              <div class="post-text-translation x-hidden" dir="auto"></div>
            {/if}
            <!-- post text -->
          </div>
          <!-- custom fileds -->
          {if $_post['custom_fields']['basic']}
            <div class="post-custom-fileds-wrapper mt10">
              {foreach $_post['custom_fields']['basic'] as $custom_field}
                {if $custom_field['value']}
                  <div>
                    <strong>{__($custom_field['label'])}</strong><br>
                    {if $custom_field['type'] == "textbox" && $custom_field['is_link']}
                      <a href="{$custom_field['value']}">{$custom_field['value']}</a>
                    {elseif $custom_field['type'] == "multipleselectbox"}
                      {$custom_field['value_string']}
                    {else}
                      {$custom_field['value']}
                    {/if}
                  </div>
                {/if}
              {/foreach}
            </div>
          {/if}
          <!-- custom fileds -->
          {if $_post['author_id'] == $user->_data['user_id']}
            <div class="mt10 d-grid">
              <button type="button" class="btn btn-primary js_job-apply" data-toggle="modal" data-size="large" data-url="posts/job.php?do=candidates&post_id={$_post['post_id']}" {if $_post['job']['candidates_count'] == 0}disabled{/if}>
                <i class="fa fa-users mr5"></i>{__("View Candidates")} ({$_post['job']['candidates_count']})
              </button>
            </div>
          {/if}
          {if $user->_logged_in && $_post['job']['available'] &&  $_post['author_id'] != $user->_data['user_id'] }
            <div class="mt10 d-grid">
              <button type="button" class="btn btn-success js_job-apply" data-toggle="modal" data-size="large" data-url="posts/job.php?do=application&post_id={$_post['post_id']}">
                {__("Apply Now")}
              </button>
            </div>
          {/if}
        </div>
      </div>
    {/if}

    {if $_post['post_type'] == "poll" && $_post['poll']}
      <div class="poll-options mt10" data-poll-votes="{$_post['poll']['votes']}">
        {foreach $_post['poll']['options'] as $option}
          <div class="mb5">
            <div class="poll-option js_poll-vote" data-id="{$option['option_id']}" data-option-votes="{$option['votes']}">
              <div class="percentage-bg" {if $_post['poll']['votes'] > 0} style="width: {($option['votes']/$_post['poll']['votes'])*100}%" {/if}></div>
              <div class="form-check form-check-inline">
                <input type="radio" name="poll_{$_post['poll']['poll_id']}" id="option_{$option['option_id']}" class="form-check-input" {if $option['checked']}checked{/if}>
                <label class="form-check-label" for="option_{$option['option_id']}">{$option['text']}</label>
              </div>
            </div>
            <div class="poll-voters">
              <div class="more" data-toggle="modal" data-url="posts/who_votes.php?option_id={$option['option_id']}">
                {$option['votes']}
              </div>
            </div>
          </div>
        {/foreach}
      </div>
    {/if}

    {if ($_post['post_type'] == "video" || $_post['post_type'] == "combo") && $_post['video']}
      <div class="overflow-hidden {if $_post['post_type'] == "combo"}mt10{/if}">
        <video class="js_videojs video-js {if $system['fluid_videos_enabled']}vjs-fluid{else}vjs-16-9{/if} vjs-default-skin" id="video-{$_post['video']['video_id']}{if $pinned || $boosted}-{$_post['post_id']}{/if}" {if $user->_logged_in}onplay="update_media_views('video', {$_post['video']['video_id']})" {/if} {if $_post['video']['thumbnail']}poster="{$system['system_uploads']}/{$_post['video']['thumbnail']}" {/if} controls preload="auto" {if $system['auto_play_videos']}muted="muted" {/if} style="width:100%;height:100%;" width="100%" height="100%">
          {if empty($_post['video']['source_240p']) && empty($_post['video']['source_360p']) && empty($_post['video']['source_480p']) && empty($_post['video']['source_720p']) && empty($_post['video']['source_1080p']) && empty($_post['video']['source_1440p']) && empty($_post['video']['source_2160p'])}
            <source src="{$system['system_uploads']}/{$_post['video']['source']}" type="video/mp4">
          {/if}
          {if $_post['video']['source_240p']}
            <source src="{$system['system_uploads']}/{$_post['video']['source_240p']}" type="video/mp4" label="240p" res="240">
          {/if}
          {if $_post['video']['source_360p']}
            <source src="{$system['system_uploads']}/{$_post['video']['source_360p']}" type="video/mp4" label="360p" res="360">
          {/if}
          {if $_post['video']['source_480p']}
            <source src="{$system['system_uploads']}/{$_post['video']['source_480p']}" type="video/mp4" label="480p" res="480">
          {/if}
          {if $_post['video']['source_720p']}
            <source src="{$system['system_uploads']}/{$_post['video']['source_720p']}" type="video/mp4" label="720p" res="720">
          {/if}
          {if $_post['video']['source_1080p']}
            <source src="{$system['system_uploads']}/{$_post['video']['source_1080p']}" type="video/mp4" label="1080p" res="1080">
          {/if}
          {if $_post['video']['source_1440p']}
            <source src="{$system['system_uploads']}/{$_post['video']['source_1440p']}" type="video/mp4" label="1440p" res="1440">
          {/if}
          {if $_post['video']['source_2160p']}
            <source src="{$system['system_uploads']}/{$_post['video']['source_2160p']}" type="video/mp4" label="2160p" res="2160">
          {/if}
        </video>
      </div>
    {/if}

    {if ($_post['post_type'] == "audio" || $_post['post_type'] == "combo") && $_post['audio']}
      <div class="plr10 {if $_post['post_type'] == "combo"}mt10{/if}">
        <audio class="js_audio" id="audio-{$_post['audio']['audio_id']}" {if $user->_logged_in}onplay="update_media_views('audio', {$_post['audio']['audio_id']})" {/if} controls preload="auto" style="width: 100%;">
          <source src="{$system['system_uploads']}/{$_post['audio']['source']}" type="audio/mpeg">
          <source src="{$system['system_uploads']}/{$_post['audio']['source']}" type="audio/mp3">
          {__("Your browser does not support HTML5 audio")}
        </audio>
      </div>
    {/if}

    {if ($_post['post_type'] == "file" || $_post['post_type'] == "combo") && $_post['file']}
      <div class="post-downloader {if $_post['post_type'] == "combo"}mt10{/if}">
        <div class="icon">
          <i class="fa fa-file-alt fa-2x"></i>
        </div>
        <div class="info">
          <strong>{__("File Type")}</strong>: {get_extension({$_post['file']['source']})}
          <div class="mt10">
            <a class="btn btn-primary btn-sm" href="{$system['system_uploads']}/{$_post['file']['source']}">{__("Download")}</a>
          </div>
        </div>
      </div>

    {/if}

    <!-- product -->
    {if $_post['post_type'] == "product" && $_post['product']}
      <div class="post-product-container">
        <div class="mtb10 text-xlg">
          <strong>{$_post['product']['name']}</strong>
          {if $_post['product']['is_digital']}
            <span class="badge bg-primary">{__("Digital")}</span>
          {/if}
          {if $_post['product']['status'] == "new"}
            <span class="badge bg-info">{__("New")}</span>
          {else}
            <span class="badge bg-info">{__("Used")}</span>
          {/if}
        </div>
        <div class="mb20 text-lg text-success">
          <strong>
            {if $_post['product']['price'] > 0}
              {print_money($_post['product']['price'])}
            {else}
              {__("Free")}
            {/if}
          </strong>
        </div>
        <div class="mb10">
          {include file='__svg_icons.tpl' icon="market" class="main-icon mr5" width="24px" height="24px"}
          {if $_post['product']['available']}
            {if $_post['product']['quantity'] > 0}
              <span class="badge badge-lg bg-light text-success">{__("In stock")}</span>
            {else}
              <span class="badge badge-lg bg-light text-danger">{__("Out of stock")}</span>
            {/if}
          {else}
            <span class="badge badge-lg bg-light text-danger">{__("SOLD")}</span>
          {/if}
        </div>
        {if $_post['product']['location']}
          <div class="mb10">
            {include file='__svg_icons.tpl' icon="map" class="main-icon mr5" width="24px" height="24px"}
            {$_post['product']['location']}
          </div>
        {/if}
        <div class="mb20">
          {include file='__svg_icons.tpl' icon="saved" class="main-icon mr5" width="24px" height="24px"}
          <a href="{$system['system_url']}/market/category/{$_post['product']['category_id']}/{$_post['product']['category_url']}" class="badge badge-lg bg-light text-primary text-no-underline">{__($_post['product']['category_name'])}</a>
        </div>
        {if $system['posts_reviews_enabled']}
          <div class="mb20">
            {include file='__svg_icons.tpl' icon="star" class="main-icon mr5" width="24px" height="24px"}
            <span class="pointer ml10" data-toggle="modal" data-url="posts/who_reviews.php?post_id={$post['post_id']}">
              {$post['reviews_count']} {__("Reviews")}
            </span>
            {if $post['post_rate']}
              <span class="review-stars small ml5">
                <i class="fa fa-star {if $post['post_rate'] >= 1}checked{/if}"></i>
                <i class="fa fa-star {if $post['post_rate'] >= 2}checked{/if}"></i>
                <i class="fa fa-star {if $post['post_rate'] >= 3}checked{/if}"></i>
                <i class="fa fa-star {if $post['post_rate'] >= 4}checked{/if}"></i>
                <i class="fa fa-star {if $post['post_rate'] >= 5}checked{/if}"></i>
              </span>
              <span class="badge bg-light text-primary">{$post['post_rate']|number_format:1}</span>
            {/if}
          </div>
        {/if}
        <!-- post text -->
        {if !$_shared}
          {include file='__feeds_post.text.tpl'}
        {else}
          <div class="post-text js_readmore text-muted" dir="auto">{$_post['text']}</div>
          <div class="post-text-translation x-hidden" dir="auto"></div>
        {/if}
        <!-- post text -->
        <!-- custom fileds -->
        {if $_post['custom_fields']['basic']}
          <div class="post-custom-fileds-wrapper mt10">
            {foreach $_post['custom_fields']['basic'] as $custom_field}
              {if $custom_field['value']}
                <div>
                  <strong>{__($custom_field['label'])}</strong><br>
                  {if $custom_field['type'] == "textbox" && $custom_field['is_link']}
                    <a href="{$custom_field['value']}">{$custom_field['value']}</a>
                  {elseif $custom_field['type'] == "multipleselectbox"}
                    {$custom_field['value_string']}
                  {else}
                    {$custom_field['value']}
                  {/if}
                </div>
              {/if}
            {/foreach}
          </div>
        {/if}
        <!-- custom fileds -->
        {if $_post['author_id'] != $user->_data['user_id'] }
          <div class="mt10 row g-1">
            {if $system['market_shopping_cart_enabled']}
              <div class="col-12 col-md-9 mb5">
                <div class="d-grid">
                  {if $_post['product']['available'] && $_post['product']['quantity'] > 0}
                    <button type="button" class="btn btn-primary js_shopping-add-to-cart" data-id="{$_post['post_id']}">
                      {if $_post['product']['is_digital']}
                        {__("Buy & Download")}
                      {else}
                        {__("Buy")}
                      {/if}
                    </button>
                  {else}
                    <button type="button" class="btn btn-primary" disabled>
                      {__("Currently unavailable")}
                    </button>
                  {/if}
                </div>
              </div>
            {/if}
            <div class="col-12 {if $system['market_shopping_cart_enabled']}col-md-3{/if}">
              <div class="d-grid">
                <button type="button" class="btn btn-light js_chat-start" data-uid="{$_post['author_id']}" data-name="{$_post['post_author_name']}" data-link="{$_post['user_name']}" data-picture="{$_post['post_author_picture']}">
                  {include file='__svg_icons.tpl' icon="header-messages" class="main-icon" width="20px" height="20px"}
                  {if !$system['market_shopping_cart_enabled']}
                    <span class="ml10">{__("Contact Seller")}</span>
                  {/if}
                </button>
              </div>
            </div>
          </div>
        {/if}
      </div>
    {/if}
    <!-- product -->

  {else}
    <div class="ptb20 plr20">
      {include file='_need_pro_package.tpl' _manage = true}
    </div>
  {/if}

{else}

  {if $_post['needs_payment']}
    <div class="ptb20 plr20">
      {include file='_need_payment.tpl' post_id=$_post['post_id'] price=$_post['post_price'] paid_text=$_post['paid_text']}
    </div>
  {elseif $_post['needs_subscription']}
    <div class="ptb20 plr20">
      {include file='_need_subscription.tpl' node_type=$_post['needs_subscription_type'] node_id=$_post['needs_subscription_id'] price=$_post['needs_subscription_price']}
    </div>
  {elseif $_post['needs_age_verification']}
    <div class="ptb20 plr20">
      {include file='_need_age_verification.tpl'}
    </div>
  {/if}

{/if}