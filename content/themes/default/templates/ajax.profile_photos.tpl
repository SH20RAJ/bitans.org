<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="24px" height="24px"}
    {__("Select Photo")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  {if $photos}
    <ul class="row">
      {foreach $photos as $photo}
        {include file='__feeds_profile_photo.tpl' _filter=$filter}
      {/foreach}
    </ul>
    <!-- see-more -->
    <div class="alert alert-post see-more mt20 js_see-more" data-get="profile_photos" data-id="{$id}" data-type='{$type}' data-filter="{$filter}">
      <span>{__("See More")}</span>
      <div class="loader loader_small x-hidden"></div>
    </div>
    <!-- see-more -->
  {else}
    {include file='_no_data.tpl'}
  {/if}
</div>