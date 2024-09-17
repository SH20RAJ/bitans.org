<li class="feeds-item {if !$notification['seen']}unread{/if}" data-id="{$notification['notification_id']}">
  <a class="data-container" href="{$notification['url']}" {if $notification['action'] == "mass_notification"}target="_blank" {/if}>
    <div class="data-avatar">
      <img src="{$notification['user_picture']}" alt="">
    </div>
    <div class="data-content">
      <div>
        <span class="name">{$notification['name']}</span>
        {if !$notification['system_notification'] && $notification['user_verified']}
          <span class="verified-badge" data-bs-toggle="tooltip" title='{__("Verified User")}'>
            {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
          </span>
        {/if}
        {if !$notification['system_notification'] && $notification['user_subscribed']}
          <span class="pro-badge" data-bs-toggle="tooltip" title='{__("Pro User")}'>
            {include file='__svg_icons.tpl' icon="pro_badge" width="20px" height="20px"}
          </span>
        {/if}
      </div>
      <div>
        {if $notification['reaction']}
          <div class="reaction-btn float-start mr5">
            <div class="reaction-btn-icon">
              <div class="inline-emoji no_animation">
                {include file='__reaction_emojis.tpl' _reaction=$notification['reaction']}
              </div>
            </div>
          </div>
        {else}
          <i class="{$notification['icon']} mr5"></i>
        {/if}
        {$notification['message']}
      </div>
      <div class="time js_moment" data-time="{$notification['time']}">{$notification['time']}</div>
    </div>
  </a>
</li>