<div class="col-4 mb10">
  <div class="pg_photo pointer {if $_filter == "avatar"}js_profile-picture-change{else}js_profile-cover-change{/if}" data-id={$id} data-type={$type} data-image="{$photo['source']}" style="background-image:url({$system['system_uploads']}/{$photo['source']});">
  </div>
</div>