{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_movie_night_fldd.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
    <h2>{__("Movies")}</h2>
    <p class="text-xlg">{__($system['system_description_movies'])}</p>
    <div class="row mt20">
      <div class="col-sm-9 col-lg-6 mx-sm-auto">
        <form class="js_search-form" data-handle="movies">
          <div class="input-group">
            <input type="text" class="form-control" name="query" placeholder='{__("Search for movies")}'>
            <button type="submit" class="btn btn-light">{__("Search")}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} mt20 sg-offcanvas">
  <div class="row">

    <!-- left panel -->
    <div class="col-md-4 col-lg-3 sg-offcanvas-sidebar">
      <!-- genres -->
      <div class="card">
        <div class="card-body with-nav">
          <ul class="side-nav">
            <li {if $view == "" || $view == "search"}class="active" {/if}>
              <a href="{$system['system_url']}/movies">
                {__("All")}
              </a>
            </li>
            {foreach $genres as $_genre}
              <li {if $view == "genre" && $genre['genre_id'] == $_genre['genre_id']}class="active" {/if}>
                <a href="{$system['system_url']}/movies/genre/{$_genre['genre_id']}/{$_genre['genre_url']}">
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

    <!-- right panel -->
    <div class="col-md-8 col-lg-9 sg-offcanvas-mainbar">

      {include file='_ads.tpl'}

      {if $view == "movie"}

        <div class="article-wrapper" style="border-radius: 16px;">

          <div>
            <div class="post-avatar">
              <div class="post-avatar-picture" style="background-image:url({$movie['poster']});">
              </div>
            </div>
            <div class="post-meta">
              <h3 style="margin-top: 0px; margin-bottom: 5px;">{$movie['title']}</h3>
              {if $movie['release_year']}
                <span class="text-muted">{$movie['release_year']}</span>
              {/if}
              {if $movie['genres_list']}
                {if $movie['release_year']} ‧ {/if}
                {foreach $movie['genres_list'] as $_genre}
                  <a href="{$system['system_url']}/movies/genre/{$_genre['genre_id']}/{$_genre['genre_url']}">
                    {__($_genre['genre_name'])}
                  </a>
                  {if !$_genre@last}/{/if}
                {/foreach}
              {/if}
              {if $movie['duration']}
                {if $movie['genres_list']} ‧ {/if}
                <span class="text-muted">{$movie['duration']} {__("minutes")}</span>
              {/if}
            </div>
          </div>

          <div style="margin: 20px -30px;">
            {if $movie['can_watch']}
              {if $movie['source_type'] == "youtube"}
                <div class="ratio ratio-16x9">
                  <iframe width="560" height="315" src="https://www.youtube.com/embed/{get_youtube_id($movie['source'], false)}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
              {elseif $movie['source_type'] == "vimeo"}
                <div class="ratio ratio-16x9">
                  <iframe width="560" height="315" src="https://player.vimeo.com/video/{get_vimeo_id($movie['source'])}" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                </div>
              {elseif $movie['source_type'] == "link"}
                <div>
                  <video class="js_videojs video-js vjs-fluid vjs-default-skin" id="video-{$movie['movie_id']}" controls preload="false">
                    <source src="{$movie['source']}" type="video/mp4">
                    <source src="{$movie['source']}" type="video/webm">
                  </video>
                </div>
              {elseif $movie['source_type'] == "uploaded"}
                <div>
                  <video class="js_videojs video-js vjs-fluid vjs-default-skin" id="video-{$movie['movie_id']}" controls preload="false">
                    <source src="{$system['system_uploads']}/{$movie['source']}" type="video/mp4">
                    <source src="{$system['system_uploads']}/{$movie['source']}" type="video/webm">
                  </video>
                </div>
              {/if}
            {else}
              <!-- need payment -->
              <div class="text-center text-muted ptb25 plr25">
                {include file='__svg_icons.tpl' icon="locked" class="main-icon mb20" width="96px" height="96px"}
                <div class="text-md">
                  <span style="padding: 8px 20px; background: #ececec; border-radius: 18px; font-weight: bold;">
                    {__("PAID CONTENT")}
                  </span>
                </div>
                <div class="d-grid">
                  <button class="btn btn-info rounded rounded-pill mt20" data-toggle="modal" data-url="#payment" data-options='{ "handle": "movies", "id": {$movie['movie_id']}, "price": {$movie['price']}, "vat": "{get_payment_vat_value($movie['price'])}", "fees": "{get_payment_fees_value($movie['price'])}", "total": "{get_payment_total_value($movie['price'])}", "total_printed": "{get_payment_total_value($movie['price'], true)}" }'>
                    <i class="fa fa-money-check-alt mr5"></i>{__("PAY")} {print_money($movie['price']|number_format:2)} {__("TO WATCH FOR")} {$movie['available_for']} {__("DAYS")}
                  </button>
                </div>
              </div>
              <!-- need payment -->
            {/if}
          </div>

          <div class="article-text mb20">
            {$movie['description']}
          </div>

          {if {$movie['stars']}}
            <div>
              <strong>{__("Stars")}:</strong>
              {foreach explode(',', $movie['stars']) as $_star}
                <span class="text-primary">{$_star}</span>{if !$_star@last}, {/if}
              {/foreach}
            </div>
          {/if}

          {if $movie['release_year']}
            <div class="mt20">
              <strong>{__("Release")}:</strong> {$movie['release_year']}
            </div>
          {/if}

          {if $movie['duration']}
            <div class="mt20">
              <strong>{__("Duration")}:</strong> {$movie['duration']} {__("minutes")}
            </div>
          {/if}

          {if $movie['genres_list']}
            <div class="article-tags mt20">
              <ul>
                <li>
                  <strong>{__("Genres")}:</strong>
                </li>
                {foreach $movie['genres_list'] as $_genre}
                  <li>
                    <a href="{$system['system_url']}/movies/genre/{$_genre['genre_id']}/{$_genre['genre_url']}">
                      {__($_genre['genre_name'])}
                    </a>
                  </li>
                {/foreach}
              </ul>
            </div>
          {/if}

          {if $movie['imdb_url']}
            <div class="mt20">
              <strong>{__("IMDB")}:</strong> <a href="{$movie['imdb_url']}" target="_blank">{$movie['imdb_url']}</a>
            </div>
          {/if}

          <div class="mt20">
            <strong>{__("Views")}:</strong> {$movie['views']}
          </div>

          <div class="mt20">
            <strong>{__("Share")}:</strong>
            {include file='__social_share.tpl' _link="{$system['system_url']}/movie/{$movie['movie_id']}/{$movie['movie_url']}"}
          </div>
        </div>

      {else}

        {if $view == "search"}
          <div class="bs-callout bs-callout-info mt0">
            <!-- results counter -->
            <span class="badge rounded-pill badge-lg bg-secondary">{$total}</span> {__("results were found for the search for")} "<strong class="text-primary">{htmlentities($query, ENT_QUOTES, 'utf-8')}</strong>"
            <!-- results counter -->
          </div>
        {/if}

        {if $movies}
          <ul class="row">
            {foreach $movies as $_movie}
              <li class="col-sm-6 col-md-6 col-lg-4">
                <div class="movie-card">
                  <a href="{$system['system_url']}/movie/{$_movie['movie_id']}/{$_movie['movie_url']}" class="movie-card-top">
                    <div class="movie-picture" style="background-image:url('{$_movie['poster']}');"></div>
                    <div class="movie-info">
                      {if $_movie['genres_list'][0]}
                        <div class="meta">{__($_movie['genres_list'][0]['genre_name'])}</div>
                      {/if}
                      {if $_movie['genres_list'][1]}
                        <div class="meta">{__($_movie['genres_list'][1]['genre_name'])}</div>
                      {/if}
                      <div class="meta">
                        <span class="btn btn-info">{__("Watch")}</span>
                      </div>
                    </div>
                  </a>
                  <div class="movie-card-bottom">
                    <a href="{$system['system_url']}/movie/{$_movie['movie_id']}/{$_movie['movie_url']}" class="movie-title">
                      {$_movie['title']} {if $_movie['is_paid']}<span class="badge rounded-pill bg-danger">{__("Paid")}</span>{/if}
                    </a>
                    <div class="movie-year">{if $_movie['release_year']}{$_movie['release_year']}{else}{__("N/A")}{/if}</div>
                  </div>
                </div>
              </li>
            {/foreach}
          </ul>

          {$pager}
        {else}
          {include file='_no_data.tpl'}
        {/if}

      {/if}
    </div>
    <!-- right panel -->

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}