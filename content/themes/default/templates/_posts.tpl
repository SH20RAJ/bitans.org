<!-- posts-filter -->
<div class="posts-filter">
  <span>{if $_title}{$_title}{else}{__("Recent Updates")}{/if}</span>
  {if $user->_logged_in && !$_filter}
    <div class="float-end">
      <div class="btn-group btn-group-sm js_posts-filter" data-value="all" title='{__("All")}'>
        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
          <i class="btn-group-icon fa fa-bars fa-fw"></i> <span class="btn-group-text">{__("All")}</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
          <div class="js_scroller">
            <div class="dropdown-item pointer" data-title='{__("All")}' data-value="all">
              {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon mr10" width="24px" height="24px"}
              {__("All")}
            </div>
            <div class="dropdown-item pointer" data-title='{__("Text")}' data-value="">
              {include file='__svg_icons.tpl' icon="comments" class="main-icon mr10" width="24px" height="24px"}
              {__("Text")}
            </div>
            <div class="dropdown-item pointer" data-title='{__("Links")}' data-value="link">
              {include file='__svg_icons.tpl' icon="links" class="main-icon mr10" width="24px" height="24px"}
              {__("Links")}</div>
            <div class="dropdown-item pointer" data-title='{__("Media")}' data-value="media">
              {include file='__svg_icons.tpl' icon="media" class="main-icon mr10" width="24px" height="24px"}
              {__("Media")}
            </div>
            {if $system['live_enabled'] && $_get != "posts_page" && $_get != "posts_group" && $_get != "posts_event"}
              <div class="dropdown-item pointer" data-title='{__("Live")}' data-value="live">
                {include file='__svg_icons.tpl' icon="live" class="main-icon mr10" width="24px" height="24px"}
                {__("Live")}
              </div>
            {/if}
            <div class="dropdown-item pointer" data-title='{__("Photos")}' data-value="photos">
              {include file='__svg_icons.tpl' icon="photos" class="main-icon mr10" width="24px" height="24px"}
              {__("Photos")}
            </div>
            {if $system['geolocation_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Maps")}' data-value="map">
                {include file='__svg_icons.tpl' icon="map" class="main-icon mr10" width="24px" height="24px"}
                {__("Maps")}
              </div>
            {/if}
            {if $system['blogs_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Articles")}' data-value="article">
                {include file='__svg_icons.tpl' icon="articles" class="main-icon mr10" width="24px" height="24px"}
                {__("Articles")}
              </div>
            {/if}
            {if $system['market_enabled'] && $_get != "posts_page" && $_get != "posts_group" && $_get != "posts_event"}
              <div class="dropdown-item pointer" data-title='{__("Products")}' data-value="product">
                {include file='__svg_icons.tpl' icon="products" class="main-icon mr10" width="24px" height="24px"}
                {__("Products")}
              </div>
            {/if}
            {if $system['funding_enabled'] && $_get != "posts_page" && $_get != "posts_group" && $_get != "posts_event"}
              <div class="dropdown-item pointer" data-title='{__("Funding")}' data-value="funding">
                {include file='__svg_icons.tpl' icon="funding" class="main-icon mr10" width="24px" height="24px"}
                {__("Funding")}
              </div>
            {/if}
            {if $system['offers_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Offers")}' data-value="offer">
                {include file='__svg_icons.tpl' icon="offers" class="main-icon mr10" width="24px" height="24px"}
                {__("Offers")}
              </div>
            {/if}
            {if $system['jobs_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Jobs")}' data-value="job">
                {include file='__svg_icons.tpl' icon="jobs" class="main-icon mr10" width="24px" height="24px"}
                {__("Jobs")}
              </div>
            {/if}
            {if $system['polls_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Polls")}' data-value="poll">
                {include file='__svg_icons.tpl' icon="polls" class="main-icon mr10" width="24px" height="24px"}
                {__("Polls")}
              </div>
            {/if}
            {if $system['videos_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Videos")}' data-value="video">
                {include file='__svg_icons.tpl' icon="videos" class="main-icon mr10" width="24px" height="24px"}
                {__("Videos")}
              </div>
            {/if}
            {if $system['audio_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Audios")}' data-value="audio">
                {include file='__svg_icons.tpl' icon="audios" class="main-icon mr10" width="24px" height="24px"}
                {__("Audios")}
              </div>
            {/if}
            {if $system['file_enabled']}
              <div class="dropdown-item pointer" data-title='{__("Files")}' data-value="file">
                {include file='__svg_icons.tpl' icon="files" class="main-icon mr10" width="24px" height="24px"}
                {__("Files")}
              </div>
            {/if}
          </div>
        </div>
      </div>
    </div>
  {elseif $_filter == "article"}
    {if $user->_data['can_write_articles']}
      <div class="float-end">
        <a href="{$system['system_url']}/blogs/new" class="btn btn-sm btn-primary">
          {__("Add New Article")}
        </a>
      </div>
    {/if}
  {elseif $_filter == "product"}
    {if $user->_data['can_sell_products']}
      <div class="float-end">
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-url="posts/product.php?do=create">
          {__("Add New Product")}
        </button>
      </div>
    {/if}
  {elseif $_filter == "funding"}
    {if $user->_data['can_raise_funding']}
      <div class="float-end">
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-url="posts/funding.php?do=create">
          {__("Add Funding Request")}
        </button>
      </div>
    {/if}
  {/if}

  <!-- newsfeed location filter -->
  {if $system['newsfeed_location_filter_enabled'] && in_array($page, ['index', 'group', 'event']) && $view != "boosted_posts" && (!$_filter || $view == "watch")}
    <div class="float-end mr10">
      <a href="#" data-bs-toggle="dropdown" class="countries-filter">
        <i class="fa fa-globe fa-fw"></i>
        {if $selected_country}
          <span>{$selected_country['country_name']}</span>
        {else}
          <span>{__("All Countries")}</span>
        {/if}
      </a>
      <div class="dropdown-menu dropdown-menu-end countries-dropdown">
        <div class="js_scroller">
          <a class="dropdown-item" href="?country=all">
            {__("All Countries")}
          </a>
          {foreach $countries as $country}
            <a class="dropdown-item" href="?country={$country['country_name_native']}">
              {$country['country_name']}
            </a>
          {/foreach}
        </div>
      </div>
    </div>
  {/if}
  <!-- newsfeed location filter -->
</div>
<!-- posts-filter -->

<!-- posts-loader -->
<div class="post x-hidden js_posts_loader">
  <div class="post-body with-loader">
    <div class="panel-effect">
      <div class="fake-effect fe-0"></div>
      <div class="fake-effect fe-1"></div>
      <div class="fake-effect fe-2"></div>
      <div class="fake-effect fe-3"></div>
      <div class="fake-effect fe-4"></div>
      <div class="fake-effect fe-5"></div>
      <div class="fake-effect fe-6"></div>
      <div class="fake-effect fe-7"></div>
      <div class="fake-effect fe-8"></div>
      <div class="fake-effect fe-9"></div>
      <div class="fake-effect fe-10"></div>
      <div class="fake-effect fe-11"></div>
    </div>
  </div>
</div>
<!-- posts-loader -->

<!-- posts staging -->
<button class="btn btn-primary rounded-pill posts-staging-btn js_view-staging-posts">
  {__("View")} <span>0</span> {__("New Posts")}
</button>

<div class="js_posts_stream_staging" style="display: none;"></div>
<!-- posts staging -->

<!-- posts stream -->
<div class="js_posts_stream" data-get="{$_get}" data-filter="{if $_filter}{$_filter}{else}all{/if}" data-country="{if $selected_country}{$selected_country['country_id']}{else}all{/if}" {if $_id}data-id="{$_id}" {/if}>
  {if $posts}
    <ul>
      <!-- posts -->
      {foreach $posts as $post}
        {include file='__feeds_post.tpl' _get=$_get}
      {/foreach}
      <!-- posts -->
    </ul>

    <!-- see-more -->
    <div class="alert alert-post see-more mb20 js_see-more js_see-more-infinite" data-get="{$_get}" data-filter="{if $_filter}{$_filter}{else}all{/if}" data-country="{if $selected_country}{$selected_country['country_id']}{else}all{/if}" {if $_id}data-id="{$_id}" {/if}>
      <span>{__("More Stories")}</span>
      <div class="loader loader_small x-hidden"></div>
    </div>
    <!-- see-more -->
  {else}
    <div class="js_posts_stream" data-get="{$_get}" data-filter="{if $_filter}{$_filter}{else}all{/if}" data-country="{if $selected_country}{$selected_country['country_id']}{else}all{/if}" {if $_id}data-id="{$_id}" {/if}>
      <ul>
        {include file='_no_data.tpl'}
      </ul>
    </div>
  {/if}
</div>
<!-- posts stream -->