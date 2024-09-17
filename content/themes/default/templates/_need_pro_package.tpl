<!-- need pro package -->
<div class="text-center text-muted">
  {include file='__svg_icons.tpl' icon="locked" class="main-icon mb20" width="96px" height="96px"}
  <div class="text-md">
    <span style="padding: 8px 20px; background: #ececec; border-radius: 18px; font-weight: bold; font-size: 13px;">
      {__("You can't access this category")}
    </span>
  </div>
  {if $_manage}
    <div class="d-grid">
      <a class="btn btn-info rounded rounded-pill mt20 text-no-underline" href="{$system['system_url']}/settings/membership">
        <i class="fa fa-cog mr5"></i>{__("Manage Allowed Categories")}
      </a>
    </div>
  {/if}
</div>
<!-- need pro package -->