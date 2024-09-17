<li class="dropdown js_live-requests">
  <a href="#" data-bs-toggle="dropdown" data-display="static">
    {include file='__svg_icons.tpl' icon="header-friends" class="header-icon" width="24px" height="24px"}
    <span class="counter red shadow-sm rounded-pill {if $user->_data['user_live_requests_counter'] == 0}x-hidden{/if}">
      {$user->_data['user_live_requests_counter']}
    </span>
  </a>
  <div class="dropdown-menu dropdown-menu-end dropdown-widget js_dropdown-keepopen">
    <div class="dropdown-widget-header">
      <span class="title">{__("Friend Requests")}</span>
    </div>
    <div class="dropdown-widget-body">
      <div class="js_scroller">
        <!-- Friend Requests -->
        {if $user->_data['friend_requests']}
          <ul>
            {foreach $user->_data['friend_requests'] as $_user}
              {include file='__feeds_user.tpl' _tpl="list" _connection="request"}
            {/foreach}
          </ul>
        {else}
          <p class="text-center text-muted mt10">
            {__("No new requests")}
          </p>
        {/if}
        <!-- Friend Requests -->
      </div>
    </div>
    <a class="dropdown-widget-footer" href="{$system['system_url']}/people/friend_requests">{__("See All")}</a>
  </div>
</li>