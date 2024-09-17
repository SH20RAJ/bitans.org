<div class="pg_wrapper clearfix">
  {if $_post['photos_num'] == 1}
    <div class="pg_1x {if $_post['photos'][0]['blur']}x-blured{/if}">
      <a href="{$system['system_url']}/photos/{$_post['photos'][0]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][0]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][0]['source']}" data-context="{if in_array($_post['post_type'], ['product', 'offer'])}post{else}album{/if}">
        <img src="{$system['system_uploads']}/{$_post['photos'][0]['source']}">
      </a>
    </div>
  {elseif $_post['photos_num'] == 2}
    {foreach $_post['photos'] as $photo}
      <div class="pg_2x {if $photo['blur']}x-blured{/if}">
        <a href="{$system['system_url']}/photos/{$photo['photo_id']}" class="js_lightbox" data-id="{$photo['photo_id']}" data-image="{$system['system_uploads']}/{$photo['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$photo['source']}');"></a>
      </div>
    {/foreach}
  {elseif $_post['photos_num'] == 3}
    <div class="pg_3x">
      <div class="pg_2o3">
        <div class="pg_2o3_in {if $_post['photos'][0]['blur']}x-blured{/if}">
          <a href="{$system['system_url']}/photos/{$_post['photos'][0]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][0]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][0]['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$_post['photos'][0]['source']}');"></a>
        </div>
      </div>
      <div class="pg_1o3">
        <div class="pg_1o3_in {if $_post['photos'][1]['blur']}x-blured{/if}">
          <a href="{$system['system_url']}/photos/{$_post['photos'][1]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][1]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][1]['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$_post['photos'][1]['source']}');"></a>
        </div>
        <div class="pg_1o3_in {if $_post['photos'][2]['blur']}x-blured{/if}">
          <a href="{$system['system_url']}/photos/{$_post['photos'][2]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][2]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][2]['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$_post['photos'][2]['source']}');"></a>
        </div>
      </div>
    </div>
  {else}
    <div class="pg_4x">
      <div class="pg_2o3">
        <div class="pg_2o3_in {if $_post['photos'][0]['blur']}x-blured{/if}">
          <a href="{$system['system_url']}/photos/{$_post['photos'][0]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][0]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][0]['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$_post['photos'][0]['source']}');"></a>
        </div>
      </div>
      <div class="pg_1o3">
        <div class="pg_1o3_in {if $_post['photos'][1]['blur']}x-blured{/if}">
          <a href="{$system['system_url']}/photos/{$_post['photos'][1]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][1]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][1]['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$_post['photos'][1]['source']}');"></a>
        </div>
        <div class="pg_1o3_in {if $_post['photos'][2]['blur']}x-blured{/if}">
          <a href="{$system['system_url']}/photos/{$_post['photos'][2]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][2]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][2]['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$_post['photos'][2]['source']}');"></a>
        </div>
        <div class="pg_1o3_in {if $_post['photos'][3]['blur']}x-blured{/if}">
          <a href="{$system['system_url']}/photos/{$_post['photos'][3]['photo_id']}" class="js_lightbox" data-id="{$_post['photos'][3]['photo_id']}" data-image="{$system['system_uploads']}/{$_post['photos'][3]['source']}" data-context="post" style="background-image:url('{$system['system_uploads']}/{$_post['photos'][3]['source']}');">
            {if $_post['photos_num'] > 4}
              <span class="more">+{$_post['photos_num']-4}</span>
            {/if}
          </a>
        </div>
      </div>
    </div>
  {/if}
</div>