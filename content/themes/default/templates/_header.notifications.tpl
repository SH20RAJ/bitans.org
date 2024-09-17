<li class="dropdown js_live-notifications">
  <a href="#" data-bs-toggle="dropdown" data-display="static">
    {include file='__svg_icons.tpl' icon="header-notifications" class="header-icon" width="24px" height="24px"}
    <span class="counter red shadow-sm rounded-pill {if $user->_data['user_live_notifications_counter'] == 0}x-hidden{/if}">
      {$user->_data['user_live_notifications_counter']}
    </span>
  </a>
  <div class="dropdown-menu dropdown-menu-end dropdown-widget js_dropdown-keepopen">
    <div class="dropdown-widget-header">
      <span class="title">{__("Notifications")}</span>

      <label class="switch sm float-end" for="notifications_sound">
        <input type="checkbox" class="js_notifications-sound-toggle" name="notifications_sound" id="notifications_sound" {if $user->_data['notifications_sound']}checked{/if}>
        <span class="slider round"></span>
      </label>
      <div class="float-end mr5">
        {__("Sound")}
      </div>
    </div>
    <div class="dropdown-widget-body">
      <div class="js_scroller">
        {if $user->_data['notifications']}
          <ul>
            {foreach $user->_data['notifications'] as $notification}
              {include file='__feeds_notification.tpl'}
            {/foreach}
          </ul>
        {else}
          <p class="text-center text-muted mt10">
            {__("No notifications")}
          </p>
        {/if}
      </div>
    </div>
    <a class="dropdown-widget-footer" href="{$system['system_url']}/notifications">{__("See All")}</a>
  </div>
</li>