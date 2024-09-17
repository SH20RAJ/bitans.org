<div class="card bg-red border-0">
  <div class="card-header pt20 pb10 bg-transparent border-bottom-0">
    <h6 class="mb0">
      {include file='__svg_icons.tpl' icon="trend" class="mr5" width="20px" height="20px" style="fill: #fff;"}
      {__("Trending")}
    </h6>
  </div>
  <div class="card-body pt0">
    {foreach $trending_hashtags as $hashtag}
      <a class="trending-item" href="{$system['system_url']}/search/hashtag/{$hashtag['hashtag']}">
        <span class="hash">
          #{$hashtag['hashtag']}
        </span>
        <span class="frequency">
          {$hashtag['frequency']} {__("Posts")}
        </span>
      </a>
    {/foreach}
  </div>
</div>