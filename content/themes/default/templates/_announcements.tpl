{foreach $announcements as $announcement}
  <div class="alert alert-{$announcement['type']} text-with-list">
    {if $user->_logged_in}
      <button type="button" class="btn-close float-end js_announcment-remover" data-id="{$announcement['announcement_id']}"></button>
    {/if}
    {if $announcement['title']}<div class="title">{$announcement['title']}</div>{/if}
    {$announcement['code']}
  </div>
{/foreach}