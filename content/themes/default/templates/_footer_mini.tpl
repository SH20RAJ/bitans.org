<div class="mini-footer mtb20 plr10">
  <div class="copyrights dropdown">
    <span class="mr5">&copy; {date('Y')} {__($system['system_title'])}</span>
    <!-- language -->
    <a href="#" class="language-dropdown" data-bs-toggle="dropdown">
      <img width="16" height="16" class="mr10" src="{$system['language']['flag']}">
      <span>{$system['language']['title']}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end">
      <div class="js_scroller">
        {foreach $system['languages'] as $language}
          <a class="dropdown-item" href="?lang={$language['code']}">
            <img width="16" height="16" class="mr10" src="{$language['flag']}">{$language['title']}
          </a>
        {/foreach}
      </div>
    </div>
    <!-- language -->
  </div>
  <ul class="links">
    {if $static_pages}
      {foreach $static_pages as $static_page}
        <li>
          <a href="{$system['system_url']}/static/{$static_page['page_url']}">
            {__($static_page['page_title'])}
          </a>
        </li>
      {/foreach}
    {/if}
    {if $system['contact_enabled']}
      <li>
        <a href="{$system['system_url']}/contacts">
          {__("Contact Us")}
        </a>
      </li>
    {/if}
    {if $system['directory_enabled']}
      <li>
        <a href="{$system['system_url']}/directory">
          {__("Directory")}
        </a>
      </li>
    {/if}
  </ul>
</div>