<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="blocking" class="main-icon mr15" width="24px" height="24px"}
  {__("Manage Blocking")}
</div>
<div class="card-body">
  <div class="alert alert-warning">
    <div class="icon">
      <i class="fa fa-exclamation-triangle fa-2x"></i>
    </div>
    <div class="text pt5">
      {__("Once you block someone, that person can no longer see things you post on your timeline")}
    </div>
  </div>
  {if $blocks}
    <ul>
      {foreach $blocks as $_user}
        {include file='__feeds_user.tpl' _tpl="list" _connection="blocked"}
      {/foreach}
    </ul>

    {if count($blocks) >= $system['max_results']}
      <!-- see-more -->
      <div class="alert alert-info see-more js_see-more" data-get="blocks">
        <span>{__("See More")}</span>
        <div class="loader loader_small x-hidden"></div>
      </div>
      <!-- see-more -->
    {/if}
  {else}
    {include file='_no_data.tpl'}
  {/if}
</div>