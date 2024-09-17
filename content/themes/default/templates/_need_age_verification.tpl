<!-- need age verification -->
<div class="text-center text-muted">
  {include file='__svg_icons.tpl' icon="adult" class="main-icon mb20" width="56px" height="56px"}
  <div class="text-md">
    <span style="padding: 8px 20px; background: #ececec; border-radius: 18px; font-weight: bold; font-size: 13px;">
      {if !$user->_data['user_adult']}
        {__("You must be 18+ to view this content")}
      {else}
        {__("Your age must be verified to view this content")}
      {/if}
    </span>
  </div>
</div>
<!-- need age verification -->