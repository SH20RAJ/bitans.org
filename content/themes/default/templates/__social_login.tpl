{if $system['facebook_login_enabled'] || $system['google_login_enabled'] || $system['twitter_login_enabled'] || $system['linkedin_login_enabled'] || $system['vkontakte_login_enabled'] || $system['wordpress_login_enabled'] || $system['sngine_login_enabled']}
  {if $_or_pos != 'bottom'}
    <div class="hr-heading mt5 mb10">
      <div class="hr-heading-text">
        {__("or")}
      </div>
    </div>
  {/if}
  {if $system['facebook_login_enabled']}
    <a href="{$system['system_url']}/connect/facebook" class="d-block mb5 btn btn-social">
      {include file='__svg_icons.tpl' icon="facebook" class="mr5" width="24px" height="24px"}
      {__("Sign in with Facebook")}
    </a>
  {/if}
  {if $system['google_login_enabled']}
    <a href="{$system['system_url']}/connect/google" class="d-block mb5 btn btn-social">
      {include file='__svg_icons.tpl' icon="google" class="mr5" width="24px" height="24px"}
      {__("Sign in with Google")}
    </a>
  {/if}
  {if $system['twitter_login_enabled']}
    <a href="{$system['system_url']}/connect/twitter" class="d-block mb5 btn btn-social">
      {include file='__svg_icons.tpl' icon="twitter" class="mr5" width="24px" height="24px"}
      {__("Sign in with X")}
    </a>
  {/if}
  {if $system['linkedin_login_enabled']}
    <a href="{$system['system_url']}/connect/linkedin" class="d-block mb5 btn btn-social">
      {include file='__svg_icons.tpl' icon="linkedin" class="mr5" width="24px" height="24px"}
      {__("Sign in with LinkedIn")}
    </a>
  {/if}
  {if $system['vkontakte_login_enabled']}
    <a href="{$system['system_url']}/connect/vkontakte" class="d-block mb5 btn btn-social">
      {include file='__svg_icons.tpl' icon="vk" class="mr5" width="24px" height="24px"}
      {__("Sign in with VK")}
    </a>
  {/if}
  {if $system['wordpress_login_enabled']}
    <a href="{$system['system_url']}/connect/wordpress" class="d-block mb5 btn btn-social">
      {include file='__svg_icons.tpl' icon="wordpress" class="mr5" width="24px" height="24px"}
      {__("Sign in with WordPress")}
    </a>
  {/if}
  {if $system['sngine_login_enabled']}
    <a href="https://{$system['sngine_app_domain']}/api/oauth?app_id={$system['sngine_appid']}" class="d-block mb5 btn btn-social">
      <img src="{$system['system_uploads']}/{$system['sngine_app_icon']}" width="24" height="24" alt="{__({$system['sngine_app_name']})}" class="mr5">
      {__("Sign in with")} {$system['sngine_app_name']}
    </a>
  {/if}
  {if $_or_pos == 'bottom'}
    <div class="hr-heading mt20 mb20">
      <div class="hr-heading-text">
        {__("or")}
      </div>
    </div>
  {/if}
{/if}