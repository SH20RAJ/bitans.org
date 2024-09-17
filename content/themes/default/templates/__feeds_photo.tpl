<div class="{if $_small}col-4{else}col-6 col-md-4 col-lg-2{/if} {if $photo['blur']}x-blured{/if}">
  <a class="pg_photo {if !$_small}large{/if} js_lightbox" href="{$system['system_url']}/photos/{$photo['photo_id']}" data-id="{$photo['photo_id']}" data-image="{$system['system_uploads']}/{$photo['source']}" data-context="{$_context}" style="background-image:url({$system['system_uploads']}/{$photo['source']});">
    {if !$_small && ($_manage || $photo['manage'])}
      <!-- delete -->
      <div class="pg_photo-delete-btn">
        <button type="button" class="btn-close js_delete-photo" data-id="{$photo['photo_id']}" data-bs-toggle="tooltip" title='{__("Delete")}'></button>
      </div>
      <!-- delete -->
      {if $_can_pin}
        <!-- pin -->
        <div class="pg_photo-pin-btn {if $photo['pinned']}js_unpin-photo pinned{else}js_pin-photo{/if}" data-id="{$photo['photo_id']}" data-bs-toggle="tooltip" title='{__("Pin")}'>
          <i class="fa-solid fa-paperclip"></i>
        </div>
        <!-- pin -->
      {/if}
    {/if}
  </a>
</div>