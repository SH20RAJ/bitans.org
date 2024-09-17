{if $_tpl == "box"}
  <div class="col-md-6 col-lg-3">
    <div class="ui-box {if $_darker}darker{/if}">
      <div class="img">
        <a href="{$system['system_url']}/{$_user['user_name']}">
          <img alt="" src="{$_user['user_picture']}" />
        </a>
      </div>
      <div class="mt10">
        <span class="js_user-popover" data-uid="{$_user['user_id']}">
          <a class="h6" href="{$system['system_url']}/{$_user['user_name']}">
            {if $system['show_usernames_enabled']}
              {$_user['user_name']}
            {else}
              {$_user['user_firstname']} {$_user['user_lastname']}
            {/if}
          </a>
        </span>
        {if $_user['user_verified']}
          <span class="verified-badge" data-bs-toggle="tooltip" title='{__("Verified User")}'>
            {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
          </span>
        {/if}
        {if $_user['user_subscribed']}
          <span class="pro-badge" data-bs-toggle="tooltip" title='{__("Pro User")}'>
            {include file='__svg_icons.tpl' icon="pro_badge" width="20px" height="20px"}
          </span>
        {/if}
      </div>
      {if $_user['monetization_plan']}
        <div class="mt10">
          <span class="badge bg-info">{print_money($_user['monetization_plan']['price'])} / {if $_user['monetization_plan']['period_num'] != '1'}{$_user['monetization_plan']['period_num']}{/if} {__($_user['monetization_plan']['period']|ucfirst)}</span>
        </div>
      {/if}
      <div class="mt10">
        <!-- buttons -->
        {if $_connection == "request"}
          <button type="button" class="btn btn-sm btn-primary js_friend-accept" data-uid="{$_user['user_id']}">{__("Confirm")}</button>
          <button type="button" class="btn btn-sm btn-danger js_friend-decline" data-uid="{$_user['user_id']}">{__("Decline")}</button>

        {elseif $_connection == "add"}
          <button type="button" class="btn btn-sm btn-success js_friend-add" data-uid="{$_user['user_id']}">
            <i class="fa fa-user-plus mr5"></i>{if $_small}{__("Add")}{else}{__("Add Friend")}{/if}
          </button>

        {elseif $_connection == "cancel"}
          <button type="button" class="btn btn-sm btn-light js_friend-cancel" data-uid="{$_user['user_id']}">
            <i class="fa fa-clock mr5"></i>{__("Sent")}
          </button>

        {elseif $_connection == "remove"}
          <button type="button" class="btn btn-sm btn-success {if !$_no_action}btn-delete{/if} js_friend-remove" data-uid="{$_user['user_id']}">
            <i class="fa fa-check mr5"></i>{__("Friends")}
          </button>
          {if $_top_friends}
            <button type="button" class="btn btn-sm btn-warning {if $_user['top_friend']}js_friend-unfavorite{else}js_friend-favorite{/if}" data-uid="{$_user['user_id']}">
              {if $_user['top_friend']}
                <i class="fa-solid fa-star"></i>
              {else}
                <i class="fa-regular fa-star"></i>
              {/if}
            </button>
          {/if}

        {elseif $_connection == "follow"}
          <button type="button" class="btn btn-sm btn-info js_follow" data-uid="{$_user['user_id']}">
            <i class="fa fa-rss mr5"></i>{__("Follow")}
          </button>

        {elseif $_connection == "unfollow"}
          <button type="button" class="btn btn-sm btn-info js_unfollow" data-uid="{$_user['user_id']}">
            <i class="fa fa-check mr5"></i>{__("Following")}
          </button>

        {elseif $_connection == "blocked"}
          <button type="button" class="btn btn-sm btn-danger js_unblock-user" data-uid="{$_user['user_id']}">
            <i class="fa fa-trash mr5"></i>{__("Unblock")}
          </button>

        {elseif $_connection == "page_invite"}
          <button type="button" class="btn btn-info btn-sm js_page-invite" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
            <i class="fa fa-user-plus mr5"></i>{__("Invite")}
          </button>

        {elseif $_connection == "page_manage"}
          <button type="button" class="btn btn-danger js_page-member-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
            <i class="fa fa-trash mr5"></i>{__("Remove")}
          </button>
          {if $_user['i_admin']}
            <button type="button" class="btn btn-danger js_page-admin-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-trash mr5"></i>{__("Remove Admin")}
            </button>
          {else}
            <button type="button" class="btn btn-primary js_page-admin-addation" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-check mr5"></i>{__("Make Admin")}
            </button>
          {/if}

        {elseif $_connection == "group_invite"}
          <button type="button" class="btn btn-sm btn-info js_group-invite" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
            <i class="fa fa-user-plus mr5"></i>{__("Add")}
          </button>

        {elseif $_connection == "group_request"}
          <button type="button" class="btn btn-sm btn-primary js_group-request-accept" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">{__("Approve")}</button>
          <button type="button" class="btn btn-sm btn-danger js_group-request-decline" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">{__("Decline")}</button>

        {elseif $_connection == "group_manage"}
          <button type="button" class="btn btn-sm btn-danger js_group-member-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
            <i class="fa fa-trash mr5"></i>{__("Remove")}
          </button>
          {if $_user['i_admin']}
            <button type="button" class="btn btn-sm btn-danger js_group-admin-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-trash mr5"></i>{__("Remove Admin")}
            </button>
          {else}
            <button type="button" class="btn btn-sm btn-primary js_group-admin-addation" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-check mr5"></i>{__("Make Admin")}
            </button>
          {/if}

        {elseif $_connection == "event_invite"}
          <button type="button" class="btn btn-sm btn-info js_event-invite" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
            <i class="fa fa-user-plus mr5"></i> {__("Invite")}
          </button>

        {elseif $_connection == "unsubscribe"}
          {if $user->_data['user_id'] == $_user['plan_user_id']}
            <button type="button" class="btn btn-sm btn-danger js_unsubscribe-plan" data-id="{$_user['plan_id']}">
              <i class="fa fa-trash mr5"></i> {__("Unsubscribe")}
            </button>
          {/if}

        {/if}
        <!-- buttons -->
      </div>
    </div>
  </div>
{elseif $_tpl == "list"}
  <li class="feeds-item" {if $_user['id']}data-id="{$_user['id']}" {/if}>
    <div class="data-container {if $_small}small{/if}">
      <a class="data-avatar" href="{$system['system_url']}/{$_user['user_name']}{if $_search}?ref=qs{/if}">
        <img src="{$_user['user_picture']}" alt="">
        {if $_reaction}
          <div class="data-reaction">
            <div class="inline-emoji no_animation">
              {include file='__reaction_emojis.tpl' _reaction=$_reaction}
            </div>
          </div>
        {/if}
      </a>
      <div class="data-content">
        <div class="float-end">
          <!-- buttons -->
          {if $_connection == "request"}
            <button type="button" class="btn btn-sm btn-primary js_friend-accept" data-uid="{$_user['user_id']}">{__("Confirm")}</button>
            <button type="button" class="btn btn-sm btn-danger js_friend-decline" data-uid="{$_user['user_id']}">{__("Decline")}</button>

          {elseif $_connection == "add"}
            <button type="button" class="btn btn-sm btn-light rounded-pill js_friend-add" data-uid="{$_user['user_id']}">
              {include file='__svg_icons.tpl' icon="user_add" class="main-icon" width="20px" height="20px"}
            </button>

          {elseif $_connection == "cancel"}
            <button type="button" class="btn btn-sm btn-warning js_friend-cancel" data-uid="{$_user['user_id']}">
              <i class="fa fa-clock mr5"></i>{__("Sent")}
            </button>

          {elseif $_connection == "remove"}
            <button type="button" class="btn btn-sm btn-success {if !$_no_action}btn-delete{/if} js_friend-remove" data-uid="{$_user['user_id']}">
              <i class="fa fa-check mr5"></i>{__("Friends")}
            </button>

          {elseif $_connection == "follow"}
            <button type="button" class="btn btn-sm js_follow" data-uid="{$_user['user_id']}">
              <i class="fa fa-rss mr5"></i>{__("Follow")}
            </button>

          {elseif $_connection == "unfollow"}
            <button type="button" class="btn btn-sm js_unfollow" data-uid="{$_user['user_id']}">
              <i class="fa fa-check mr5"></i>{__("Following")}
            </button>

          {elseif $_connection == "blocked"}
            <button type="button" class="btn btn-sm btn-danger js_unblock-user" data-uid="{$_user['user_id']}">
              <i class="fa fa-trash mr5"></i>{__("Unblock")}
            </button>

          {elseif $_connection == "page_invite"}
            <button type="button" class="btn btn-sm btn-info js_page-invite" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-user-plus mr5"></i>{__("Invite")}
            </button>

          {elseif $_connection == "page_manage"}
            <button type="button" class="btn btn-danger js_page-member-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-trash mr5"></i>{__("Remove")}
            </button>
            {if $_user['i_admin']}
              <button type="button" class="btn btn-danger js_page-admin-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
                <i class="fa fa-trash mr5"></i>{__("Remove Admin")}
              </button>
            {else}
              <button type="button" class="btn btn-primary js_page-admin-addation" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
                <i class="fa fa-check mr5"></i>{__("Make Admin")}
              </button>
            {/if}

          {elseif $_connection == "group_invite"}
            <button type="button" class="btn btn-sm btn-info js_group-invite" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-user-plus mr5"></i>{__("Add")}
            </button>

          {elseif $_connection == "group_request"}
            <button type="button" class="btn btn-sm btn-primary js_group-request-accept" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">{__("Approve")}</button>
            <button type="button" class="btn btn-sm btn-danger js_group-request-decline" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">{__("Decline")}</button>

          {elseif $_connection == "group_manage"}
            <button type="button" class="btn btn-sm btn-danger js_group-member-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-trash mr5"></i>{__("Remove")}
            </button>
            {if $_user['i_admin']}
              <button type="button" class="btn btn-sm btn-danger js_group-admin-remove" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
                <i class="fa fa-trash mr5"></i>{__("Remove Admin")}
              </button>
            {else}
              <button type="button" class="btn btn-sm btn-primary js_group-admin-addation" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
                <i class="fa fa-check mr5"></i>{__("Make Admin")}
              </button>
            {/if}

          {elseif $_connection == "event_invite"}
            <button type="button" class="btn btn-sm btn-info js_event-invite" data-id="{$_user['node_id']}" data-uid="{$_user['user_id']}">
              <i class="fa fa-user-plus mr5"></i> {__("Invite")}
            </button>

          {elseif $_connection == "connected_account_remove"}
            <button type="button" class="btn btn-sm btn-danger js_connected-account-remove" data-uid="{$_user['user_id']}">
              <i class="fa fa-trash mr5"></i> {__("Remove")}
            </button>

          {elseif $_connection == "connected_account_revoke"}
            <button type="button" class="btn btn-sm btn-danger js_connected-account-revoke">
              <i class="fa fa-trash mr5"></i> {__("Revoke")}
            </button>

          {/if}
          <!-- buttons -->
        </div>
        <div class="mt5">
          <span class="name js_user-popover" data-uid="{$_user['user_id']}">
            <a href="{$system['system_url']}/{$_user['user_name']}{if $_search}?ref=qs{/if}">
              {if $system['show_usernames_enabled']}
                {$_user['user_name']}
              {else}
                {$_user['user_firstname']} {$_user['user_lastname']}
              {/if}
            </a>
          </span>
          {if $_user['user_verified']}
            <span class="verified-badge" data-bs-toggle="tooltip" title='{__("Verified User")}'>
              {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
            </span>
          {/if}
          {if $_user['user_subscribed']}
            <span class="pro-badge" data-bs-toggle="tooltip" title='{__("Pro User")}'>
              {include file='__svg_icons.tpl' icon="pro_badge" width="20px" height="20px"}
            </span>
          {/if}
        </div>
        {if $_connection != "me" && $_user['mutual_friends_count'] > 0}
          <div>
            <span class="text-underline" data-toggle="modal" data-url="users/mutual_friends.php?uid={$_user['user_id']}">{$_user['mutual_friends_count']} {__("mutual friends")}</span>
          </div>
        {/if}
        {if $_donation}
          <div>
            <span class="badge bg-success">{print_money($_donation|number_format:2)}</span>
            <span class="js_moment" data-time="{$_donation_time}">{$_donation_time}</span>
          </div>
        {/if}
      </div>
    </div>
  </li>
{/if}