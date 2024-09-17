{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_gaming_re_cma2.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
    <h2>{__("Games")}</h2>
    <p class="text-xlg">{__($system['system_description_games'])}</p>
  </div>
</div>
<!-- page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} sg-offcanvas" style="margin-top: -25px;">

  <div class="position-relative">
    <!-- tabs -->
    <div class="content-tabs rounded-sm shadow-sm clearfix">
      <ul>
        <li {if $view == "" || $view == "genre"}class="active" {/if}>
          <a href="{$system['system_url']}/games">{__("Discover")}</a>
        </li>
        {if $user->_logged_in}
          <li {if $view != "" && $view != "genre"}class="active" {/if}>
            <a href="{$system['system_url']}/games/played">{__("Your Games")}</a>
          </li>
        {/if}
      </ul>
    </div>
    <!-- tabs -->
  </div>

  <div class="row">

    {if $view == "" || $view == "genre"}
      <!-- left panel -->
      <div class="col-md-4 col-lg-3 sg-offcanvas-sidebar">
        <!-- genres -->
        <div class="card">
          <div class="card-body with-nav">
            <ul class="side-nav">
              <li {if $view == "" || $view == "search"}class="active" {/if}>
                <a href="{$system['system_url']}/games">
                  {__("All")}
                </a>
              </li>
              {foreach $genres as $_genre}
                <li {if $view == "genre" && $genre['genre_id'] == $_genre['genre_id']}class="active" {/if}>
                  <a href="{$system['system_url']}/games/genre/{$_genre['genre_id']}/{$_genre['genre_url']}">
                    {__($_genre['genre_name'])}
                  </a>
                </li>
              {/foreach}
            </ul>
          </div>
        </div>
        <!-- genres -->
      </div>
      <!-- left panel -->
    {else}
      <!-- side panel -->
      <div class="col-12 d-block d-md-none sg-offcanvas-sidebar">
        {include file='_sidebar.tpl'}
      </div>
      <!-- side panel -->
    {/if}

    {if $view == "game"}

      <!-- content panel -->
      <div class="col-12 sg-offcanvas-mainbar">
        <div class="post">
          <div class="ptb20 plr20">
            <div class="post-header mb0">
              <div class="post-avatar">
                <div class="post-avatar-picture" style="background-image:url({$game['thumbnail']});">
                </div>
              </div>
              <div class="post-meta">
                <div class="float-end">
                  <a href="{$system['system_url']}/games" class="btn btn-sm btn-light d-none d-lg-block">
                    <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
                  </a>
                  <a href="{$system['system_url']}/games" class="btn btn-sm btn-icon btn-light d-block d-lg-none">
                    <i class="fa fa-arrow-circle-left"></i>
                  </a>
                </div>
                <div class="h6 mt5 mb0">{$game['title']}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="ratio ratio-16x9">
          <iframe frameborder="0" src="{$game['source']}"></iframe>
        </div>
      </div>
      <!-- content panel -->

    {else}

      <!-- content panel -->
      {if $view == "" || $view == "genre"}<div class="col-md-8 col-lg-9 sg-offcanvas-mainbar">{else}<div class="col-12 sg-offcanvas-mainbar">{/if}

          <!-- content -->
          <div>
            {if $games}
              <ul class="row">
                {foreach $games as $_game}
                  {include file='__feeds_game.tpl' _tpl='box'}
                {/foreach}
              </ul>

              <!-- see-more -->
              {if count($games) >= $system['games_results']}
                <div class="alert alert-post see-more js_see-more" data-get="{$get}" {if $view == "genre"}data-id="{$genre['genre_id']}" {/if} {if $view == "played"}data-uid="{$user->_data['user_id']}" {/if}>
                  <span>{__("See More")}</span>
                  <div class="loader loader_small x-hidden"></div>
                </div>
              {/if}
              <!-- see-more -->
            {else}
              {include file='_no_data.tpl'}
            {/if}
          </div>
          <!-- content -->

        </div>
        <!-- content panel -->

      {/if}

    </div>
  </div>
  <!-- page content -->

{include file='_footer.tpl'}