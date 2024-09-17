{if $type == "user"}
  <!-- user popover -->
  <div class="user-popover-content">
    <div class="user-card">
      <div class="user-card-cover" {if $profile['user_cover']}style="background-image:url('{$system['system_uploads']}/{$profile['user_cover']}');" {/if}>
      </div>
      <div class="user-card-avatar">
        <img src="{$profile['user_picture']}" alt="">
      </div>
      <div class="user-card-info">
        <a class="name" href="{$system['system_url']}/{$profile['user_name']}">
          {if $system['show_usernames_enabled']}
            {$profile['user_name']}
          {else}
            {$profile['user_firstname']} {$profile['user_lastname']}
          {/if}
        </a>
        {if $profile['user_verified']}
          <span class="verified-badge" data-bs-toggle="tooltip" title='{__("Verified User")}'>
            {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
          </span>
        {/if}
        <div class="info">
          <a href="{$system['system_url']}/{$profile['user_name']}/followers">{$profile['followers_count']} {__("followers")}</a>
        </div>
      </div>
    </div>
    <div class="user-card-meta">
      <!-- mutual friends -->
      {if $profile['mutual_friends_count'] && $profile['mutual_friends_count'] > 0}
        <div class="mb10">
          {include file='__svg_icons.tpl' icon="friends" class="main-icon mr10" width="16px" height="16px"}
          <span class="text-underline" data-toggle="modal" data-url="users/mutual_friends.php?uid={$profile['user_id']}">{$profile['mutual_friends_count']} {__("mutual friends")}</span>
        </div>
      {/if}
      <!-- mutual friends -->
      <!-- work -->
      {if $system['work_info_enabled']}
        {if !is_empty($profile['user_work_title'])}
          {if $profile['user_id'] == $user->_data['user_id'] || $profile['user_privacy_work'] == "public" || $profile['we_friends']}
            <div class="mb10">
              {include file='__svg_icons.tpl' icon="jobs" class="main-icon mr10" width="16px" height="16px"}
              {$profile['user_work_title']} {__("at")} <span class="text-primary">{$profile['user_work_place']}</span>
            </div>
          {/if}
        {/if}
      {/if}
      <!-- work -->
      <!-- hometown -->
      {if $system['location_info_enabled']}
        {if !is_empty($profile['user_hometown'])}
          {if $profile['user_id'] == $user->_data['user_id'] || $profile['user_privacy_location'] == "public" || $profile['we_friends']}
            <div class="mb10">
              {include file='__svg_icons.tpl' icon="home" class="main-icon mr10" width="16px" height="16px"}
              {__("From")} <span class="text-primary">{$profile['user_hometown']}</span>
            </div>
          {/if}
        {/if}
      {/if}
      <!-- hometown -->
    </div>
    <div class="popover-footer">
      {if $user->_data['user_id'] != $profile['user_id']}
        <!-- add friend -->
        {if $profile['we_friends']}
          <button type="button" class="btn btn-sm btn-success btn-delete rounded-pill js_friend-remove" data-uid="{$profile['user_id']}">
            <i class="fa fa-check mr5"></i>{__("Friends")}
          </button>
        {elseif $profile['he_request']}
          <button type="button" class="btn btn-sm btn-primary rounded-pill js_friend-accept" data-uid="{$profile['user_id']}">{__("Confirm")}</button>
          <button type="button" class="btn btn-sm btn-danger rounded-pill js_friend-decline" data-uid="{$profile['user_id']}">{__("Decline")}</button>
        {elseif $profile['i_request']}
          <button type="button" class="btn btn-sm btn-warning rounded-pill js_friend-cancel" data-uid="{$profile['user_id']}">
            <i class="fa fa-user-plus mr5"></i>{__("Sent")}
          </button>
        {else}
          <button type="button" class="btn btn-sm btn-success rounded-pill js_friend-add" data-uid="{$profile['user_id']}">
            <i class="fa fa-user-plus mr5"></i>{__("Add Friend")}
          </button>
        {/if}
        <!-- add friend -->

        <!-- follow -->
        {if $profile['i_follow']}
          <button type="button" class="btn btn-sm btn-light rounded-pill js_unfollow" data-uid="{$profile['user_id']}">
            <i class="fa fa-check mr5"></i>{__("Following")}
          </button>
        {else}
          <button type="button" class="btn btn-sm btn-light rounded-pill js_follow" data-uid="{$profile['user_id']}">
            <i class="fa fa-rss mr5"></i>{__("Follow")}
          </button>
        {/if}
        <!-- follow -->

        <!-- message -->
        <button type="button" class="btn btn-icon rounded-pill btn-light js_chat-start" data-uid="{$profile['user_id']}" data-name="{if $system['show_usernames_enabled']}{$profile['user_name']}{else}{$profile['user_firstname']} {$profile['user_lastname']}{/if}" data-link="{$profile['user_name']}" data-picture="{$profile['user_picture']}">
          {include file='__svg_icons.tpl' icon="header-messages" class="main-icon" width="16px" height="16px"}
        </button>
        <!-- message -->
      {else}
        <!-- edit -->
        <a href="{$system['system_url']}/settings/profile" class="btn btn-sm btn-light rounded-pill">
          {__("Update")}
        </a>
        <!-- edit -->
      {/if}
    </div>
  </div>
  <!-- user popover -->
{else}
  <!-- page popover -->
  <div class="user-popover-content">
    <div class="user-card">
      <div class="user-card-cover" {if $profile['page_cover']}style="background-image:url('{$system['system_uploads']}/{$profile['page_cover']}');" {/if}></div>
      <div class="user-card-avatar">
        <img class="img-fluid" src="{$profile['page_picture']}" alt="{$profile['page_title']}">
      </div>
      <div class="user-card-info">
        <a class="name" href="{$system['system_url']}/pages/{$profile['page_name']}">{$profile['page_title']}</a>
        {if $profile['page_verified']}
          <span class="verified-badge" data-bs-toggle="tooltip" title='{__("Verified Page")}'>
            {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
          </span>
        {/if}
        <div class="info">{$profile['page_likes']} {__("Likes")}</div>
      </div>
    </div>
    <div class="popover-footer">
      <!-- like -->
      {if $profile['i_like']}
        <button type="button" class="btn btn-sm btn-primary js_unlike-page" data-id="{$profile['page_id']}">
          <i class="fa fa-heart mr5"></i>{__("Unlike")}
        </button>
      {else}
        <button type="button" class="btn btn-primary js_like-page" data-id="{$profile['page_id']}">
          <i class="fa fa-heart mr5"></i>{__("Like")}
        </button>
      {/if}
      <!-- like -->
    </div>
  </div>
  <!-- page popover -->
{/if}