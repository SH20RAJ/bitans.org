{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_file_searching_duff.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="inner">
    <h2>{__("Search")}</h2>
    <p class="text-xlg">{__("Discover new people, create new connections and make new friends")}</p>
  </div>
</div>
<!-- page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} sg-offcanvas" style="margin-top: -45px;">
  <div class="row">

    <!-- side panel -->
    <div class="col-12 d-block d-sm-none sg-offcanvas-sidebar mt30">
      {include file='_sidebar.tpl'}
    </div>
    <!-- side panel -->

    <!-- content panel -->
    <div class="col-12 sg-offcanvas-mainbar">
      <!-- search form -->
      <div class="card">
        <div class="card-body">
          <form class="js_search-form" {if $tab} data-filter="{$tab}" {/if}>
            <div class="form-group mb0">
              <div class="input-group">
                <input type="text" class="form-control" name="query" placeholder='{__("Search")}' {if $query} value="{$query}" {/if}>
                <button type="submit" name="submit" class="btn btn-light plr30">{__("Search")}</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <!-- search form -->

      <div class="row">
        <!-- left panel -->
        <div class="col-lg-8">
          <!-- panel nav -->
          <ul class="nav nav-pills nav-fill nav-search mb10">
            <li class="nav-item">
              <a class="nav-link rounded-pill {if $tab == "" || $tab == "posts"}active{/if}" href="{$system['system_url']}/search/{if $hashtag}hashtag/{/if}{if $query}{$query}/posts{/if}">
                <strong>{__("Posts")}</strong>
              </a>
            </li>
            {if $system['blogs_enabled']}
              <li class="nav-item">
                <a class="nav-link rounded-pill {if $tab == "articles"}active{/if}" href="{$system['system_url']}/search/{if $hashtag}hashtag/{/if}{if $query}{$query}/articles{/if}">
                  <strong>{__("Articles")}</strong>
                </a>
              </li>
            {/if}
            <li class="nav-item">
              <a class="nav-link rounded-pill {if $tab == "users"}active{/if}" href="{$system['system_url']}/search/{if $query}{$query}/users{/if}">
                <strong>{__("Users")}</strong>
              </a>
            </li>
            {if $system['pages_enabled']}
              <li class="nav-item">
                <a class="nav-link rounded-pill {if $tab == "pages"}active{/if}" href="{$system['system_url']}/search/{if $query}{$query}/pages{/if}">
                  <strong>{__("Pages")}</strong>
                </a>
              </li>
            {/if}
            {if $system['groups_enabled']}
              <li class="nav-item">
                <a class="nav-link rounded-pill {if $tab == "groups"}active{/if}" href="{$system['system_url']}/search/{if $query}{$query}/groups{/if}">
                  <strong>{__("Groups")}</strong>
                </a>
              </li>
            {/if}
            {if $system['events_enabled']}
              <li class="nav-item">
                <a class="nav-link rounded-pill {if $tab == "events"}active{/if}" href="{$system['system_url']}/search/{if $query}{$query}/events{/if}">
                  <strong>{__("Events")}</strong>
                </a>
              </li>
            {/if}
          </ul>
          <!-- panel nav -->

          <div class="tab-content">

            <div class="tab-pane active">
              {if $results}
                <ul>
                  {if $tab == "" || $tab == "posts"}
                    <!-- posts -->
                    {foreach $results as $post}
                      {include file='__feeds_post.tpl'}
                    {/foreach}
                    <!-- posts -->
                  {elseif $tab == "articles"}
                    <!-- articles -->
                    {foreach $results as $post}
                      {include file='__feeds_post.tpl'}
                    {/foreach}
                    <!-- articles -->
                  {elseif $tab == "users"}
                    <!-- users -->
                    {foreach $results as $_user}
                      {include file='__feeds_user.tpl' _tpl="list" _connection=$_user['connection']}
                    {/foreach}
                    <!-- users -->
                  {elseif $tab == "pages"}
                    <!-- pages -->
                    {foreach $results as $_page}
                      {include file='__feeds_page.tpl' _tpl="list"}
                    {/foreach}
                    <!-- pages -->
                  {elseif $tab == "groups"}
                    <!-- groups -->
                    {foreach $results as $_group}
                      {include file='__feeds_group.tpl' _tpl="list"}
                    {/foreach}
                    <!-- groups -->
                  {elseif $tab == "events"}
                    <!-- events -->
                    {foreach $results as $_event}
                      {include file='__feeds_event.tpl' _tpl="list"}
                    {/foreach}
                    <!-- events -->
                  {/if}
                </ul>

                {if count($results) >= $system['search_results']}
                  <!-- see-more -->
                  <div class="alert alert-post see-more mb20 js_see-more js_see-more-infinite" data-get="search_{$tab}" data-filter="{$query}">
                    <span>{__("More Results")}</span>
                    <div class="loader loader_small x-hidden"></div>
                  </div>
                  <!-- see-more -->
                {/if}
              {else}
                {include file='_no_data.tpl'}
              {/if}
            </div>

          </div>
        </div>
        <!-- left panel -->

        <!-- right panel -->
        <div class="col-lg-4">
          {include file='_ads_campaigns.tpl'}
          {include file='_ads.tpl'}
          {include file='_widget.tpl'}
        </div>
        <!-- right panel -->
      </div>
    </div>
    <!-- content panel -->

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}