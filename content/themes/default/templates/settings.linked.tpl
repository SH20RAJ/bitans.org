<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="linked_accounts" class="main-icon mr15" width="24px" height="24px"}
  {__("Linked Accounts")}
</div>
<div class="card-body">
  {if $system['facebook_login_enabled']}
    <div class="form-table-row">
      <div class="avatar">
        {include file='__svg_icons.tpl' icon="facebook" width="40px" height="40px"}
      </div>
      <div>
        <div class="form-label h6 mb5">{__("Facebook")}</div>
        <div class="form-text d-none d-sm-block">
          {if $user->_data['facebook_connected']}
            {__("Your account is connected to")} {__("Facebook")}
          {else}
            {__("Connect your account to")} {__("Facebook")}
          {/if}
        </div>
      </div>
      <div class="text-end">
        {if $user->_data['facebook_connected']}
          <a class="btn btn-sm btn-danger" href="{$system['system_url']}/revoke/facebook">{__("Disconnect")}</a>
        {else}
          <a class="btn btn-sm btn-primary" href="{$system['system_url']}/connect/facebook">{__("Connect")}</a>
        {/if}
      </div>
    </div>
  {/if}

  {if $system['google_login_enabled']}
    <div class="form-table-row">
      <div class="avatar">
        {include file='__svg_icons.tpl' icon="google" width="40px" height="40px"}
      </div>
      <div>
        <div class="form-label h6 mb5">{__("Google")}</div>
        <div class="form-text d-none d-sm-block">
          {if $user->_data['google_connected']}
            {__("Your account is connected to")} {__("Google")}
          {else}
            {__("Connect your account to")} {__("Google")}
          {/if}
        </div>
      </div>
      <div class="text-end">
        {if $user->_data['google_connected']}
          <a class="btn btn-sm btn-danger" href="{$system['system_url']}/revoke/google">{__("Disconnect")}</a>
        {else}
          <a class="btn btn-sm btn-primary" href="{$system['system_url']}/connect/google">{__("Connect")}</a>
        {/if}
      </div>
    </div>
  {/if}

  {if $system['twitter_login_enabled']}
    <div class="form-table-row">
      <div class="avatar">
        {include file='__svg_icons.tpl' icon="twitter" width="40px" height="40px"}
      </div>
      <div>
        <div class="form-label h6 mb5">{__("Twitter")}</div>
        <div class="form-text d-none d-sm-block">
          {if $user->_data['twitter_connected']}
            {__("Your account is connected to")} {__("Twitter")}
          {else}
            {__("Connect your account to")} {__("Twitter")}
          {/if}
        </div>
      </div>
      <div class="text-end">
        {if $user->_data['twitter_connected']}
          <a class="btn btn-sm btn-danger" href="{$system['system_url']}/revoke/twitter">{__("Disconnect")}</a>
        {else}
          <a class="btn btn-sm btn-primary" href="{$system['system_url']}/connect/twitter">{__("Connect")}</a>
        {/if}
      </div>
    </div>
  {/if}

  {if $system['linkedin_login_enabled']}
    <div class="form-table-row">
      <div class="avatar">
        {include file='__svg_icons.tpl' icon="linkedin" width="40px" height="40px"}
      </div>
      <div>
        <div class="form-label h6 mb5">{__("Linkedin")}</div>
        <div class="form-text d-none d-sm-block">
          {if $user->_data['linkedin_connected']}
            {__("Your account is connected to")} {__("Linkedin")}
          {else}
            {__("Connect your account to")} {__("Linkedin")}
          {/if}
        </div>
      </div>
      <div class="text-end">
        {if $user->_data['linkedin_connected']}
          <a class="btn btn-sm btn-danger" href="{$system['system_url']}/revoke/linkedin">{__("Disconnect")}</a>
        {else}
          <a class="btn btn-sm btn-primary" href="{$system['system_url']}/connect/linkedin">{__("Connect")}</a>
        {/if}
      </div>
    </div>
  {/if}

  {if $system['vkontakte_login_enabled']}
    <div class="form-table-row">
      <div class="avatar">
        {include file='__svg_icons.tpl' icon="vk" width="40px" height="40px"}
      </div>
      <div>
        <div class="form-label h6 mb5">{__("Vkontakte")}</div>
        <div class="form-text d-none d-sm-block">
          {if $user->_data['vkontakte_connected']}
            {__("Your account is connected to")} {__("Vkontakte")}
          {else}
            {__("Connect your account to")} {__("Vkontakte")}
          {/if}
        </div>
      </div>
      <div class="text-end">
        {if $user->_data['vkontakte_connected']}
          <a class="btn btn-sm btn-danger" href="{$system['system_url']}/revoke/vkontakte">{__("Disconnect")}</a>
        {else}
          <a class="btn btn-sm btn-primary" href="{$system['system_url']}/connect/vkontakte">{__("Connect")}</a>
        {/if}
      </div>
    </div>
  {/if}

  {if $system['wordpress_login_enabled']}
    <div class="form-table-row">
      <div class="avatar">
        {include file='__svg_icons.tpl' icon="wordpress" width="40px" height="40px"}
      </div>
      <div>
        <div class="form-label h6 mb5">{__("Wordpress")}</div>
        <div class="form-text d-none d-sm-block">
          {if $user->_data['wordpress_connected']}
            {__("Your account is connected to")} {__("wordpress")}
          {else}
            {__("Connect your account to")} {__("wordpress")}
          {/if}
        </div>
      </div>
      <div class="text-end">
        {if $user->_data['wordpress_connected']}
          <a class="btn btn-sm btn-danger" href="{$system['system_url']}/revoke/wordpress">{__("Disconnect")}</a>
        {else}
          <a class="btn btn-sm btn-primary" href="{$system['system_url']}/connect/wordpress">{__("Connect")}</a>
        {/if}
      </div>
    </div>
  {/if}

  {if $system['sngine_login_enabled']}
    <div class="form-table-row">
      <div class="avatar">
        <img src="{$system['system_uploads']}/{$system['sngine_app_icon']}" width="40" height="40" alt="{__({$system['sngine_app_name']})}">
      </div>
      <div>
        <div class="form-label h6 mb5">{__({$system['sngine_app_name']})}</div>
        <div class="form-text d-none d-sm-block">
          {if $user->_data['sngine_connected']}
            {__("Your account is connected to")} {__({$system['sngine_app_name']})}
          {else}
            {__("Connect your account to")} {__({$system['sngine_app_name']})}
          {/if}
        </div>
      </div>
      <div class="text-end">
        {if $user->_data['sngine_connected']}
          <a class="btn btn-sm btn-danger" href="{$system['system_url']}/revoke/sngine">{__("Disconnect")}</a>
        {else}
          <a class="btn btn-sm btn-primary" href="https://{$system['sngine_app_domain']}/api/oauth?app_id={$system['sngine_appid']}">{__("Connect")}</a>
        {/if}
      </div>
    </div>
  {/if}
</div>