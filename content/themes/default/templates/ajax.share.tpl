<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="share" class="main-icon mr10" width="24px" height="24px"}
    {__("Share")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">

  <div style="margin: 25px auto;">
    <div class="input-group">
      <input type="text" disabled class="form-control" value="{$share_link}">
      <button type="button" class="btn btn-light js_clipboard" data-clipboard-text="{$share_link}" data-bs-toggle="tooltip" title='{__("Copy")}'>
        <i class="fas fa-copy"></i>
      </button>
    </div>
  </div>

  {if $system['social_share_enabled']}
    <div class="post-social-share border-bottom-0">
      {include file='__social_share.tpl' _link=$share_link}
    </div>
  {/if}

</div>