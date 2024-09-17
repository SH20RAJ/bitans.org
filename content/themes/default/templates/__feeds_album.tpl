<div class="col-sm-6 col-md-4 col-lg-3">
  <div class="album-card">
    {if $album['cover']['blur']}<div class="x-blured">{/if}
      <a class="album-cover" href="{$system['system_url']}/{$album['path']}/album/{$album['album_id']}" style="background-image:url({$album['cover']['source']});">
      </a>
      {if $album['cover']['blur']}
    </div>{/if}
    <div class="album-details">
      <a href="{$system['system_url']}/{$album['path']}/album/{$album['album_id']}">{__($album['title'])}</a>
      <div>
        {$album['photos_count']} {__("photos")}
        <div class="float-end">
          {if $album['privacy'] == "me"}
            <i class="fa fa-lock" data-bs-toggle="tooltip" title='{__("Shared with")}: {__("Only Me")}'></i>
          {elseif $album['privacy'] == "friends"}
            <i class="fa fa-users" data-bs-toggle="tooltip" title='{__("Shared with")}: {__("Friends")}'></i>
          {elseif $album['privacy'] == "public"}
            <i class="fa fa-globe" data-bs-toggle="tooltip" title='{__("Shared with")}: {__("Public")}'></i>
          {elseif $album['privacy'] == "custom"}
            <i class="fa fa-cog" data-bs-toggle="tooltip" title='{__("Shared with")} {__("Custom People")}'></i>
          {/if}
        </div>
      </div>
    </div>
  </div>
</div>