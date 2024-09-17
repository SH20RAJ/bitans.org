<div class="col-6 col-md-4 col-lg-3">
  <a class="pg_video" href="{$system['system_url']}/posts/{$video['post_id']}">
    <video>
      {if empty($video['source_240p']) && empty($video['source_360p']) && empty($video['source_480p']) && empty($video['source_720p']) && empty($video['source_1080p']) && empty($video['source_1440p']) && empty($video['source_2160p'])}
        <source src="{$system['system_uploads']}/{$video['source']}" type="video/mp4">
      {/if}
      {if $video['source_240p']}
        <source src="{$system['system_uploads']}/{$video['source_240p']}" type="video/mp4" label="240p" res="240">
      {/if}
      {if $video['source_360p']}
        <source src="{$system['system_uploads']}/{$video['source_360p']}" type="video/mp4" label="360p" res="360">
      {/if}
      {if $video['source_480p']}
        <source src="{$system['system_uploads']}/{$video['source_480p']}" type="video/mp4" label="480p" res="480">
      {/if}
      {if $video['source_720p']}
        <source src="{$system['system_uploads']}/{$video['source_720p']}" type="video/mp4" label="720p" res="720">
      {/if}
      {if $video['source_1080p']}
        <source src="{$system['system_uploads']}/{$video['source_1080p']}" type="video/mp4" label="1080p" res="1080">
      {/if}
      {if $video['source_1440p']}
        <source src="{$system['system_uploads']}/{$video['source_1440p']}" type="video/mp4" label="1440p" res="1440">
      {/if}
      {if $video['source_2160p']}
        <source src="{$system['system_uploads']}/{$video['source_2160p']}" type="video/mp4" label="2160p" res="2160">
      {/if}
    </video>
    <div class="play-button"><i class="fa fa-play fa-2x"></i></div>
  </a>
</div>