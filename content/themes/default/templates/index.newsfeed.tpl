{include file='_head.tpl'}
{include file='_header.tpl'}

<div class="{if $system['fluid_design']}container-fluid{else}container{/if} mt20 sg-offcanvas">
  <div class="row">

    <!-- side panel -->
    <div class="col-md-4 col-lg-3 sg-offcanvas-sidebar js_sticky-sidebar">
      {include file='_sidebar.tpl'}
    </div>
    <!-- side panel -->

    <!-- content panel -->
    <div class="col-md-8 col-lg-9 sg-offcanvas-mainbar">
      <div class="row">
        <!-- center panel -->
        <div class="col-lg-8">

          <!-- announcments -->
          {include file='_announcements.tpl'}
          <!-- announcments -->

          {if $view == ""}

            {if $user->_logged_in}
              <!-- stories -->
              {if $user->_data['can_add_stories'] || ($system['stories_enabled'] && !empty($stories['array']))}
                <div class="card">
                  <div class="card-header bg-transparent border-bottom-0">
                    <strong class="text-muted">{__("Stories")}</strong>
                    {if $has_story}
                      <div class="float-end">
                        <button data-bs-toggle="tooltip" title='{__("Delete Your Story")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_story-deleter">
                          <i class="fa fa-trash-alt"></i>
                        </button>
                      </div>
                    {/if}
                  </div>
                  <div class="card-body pt5 stories-wrapper">
                    <div id="stories" data-json='{htmlspecialchars($stories["json"], ENT_QUOTES, 'UTF-8')}'>
                      {if $user->_data['can_add_stories']}
                        <div class="add-story" data-toggle="modal" data-url="posts/story.php?do=create">
                          <div class="img" style="background-image:url({$user->_data['user_picture']});">
                          </div>
                          <div class="add">
                            {include file='__svg_icons.tpl' icon="add" class="main-icon" width="18px" height="18px"}
                          </div>
                        </div>
                      {/if}
                    </div>
                  </div>
                </div>
              {/if}
              <!-- stories -->

              <!-- publisher -->
              {include file='_publisher.tpl' _handle="me" _node_can_monetize_content=$user->_data['can_monetize_content'] _node_monetization_enabled=$user->_data['user_monetization_enabled'] _node_monetization_plans=$user->_data['user_monetization_plans'] _privacy=true}
              <!-- publisher -->

              <!-- pro users -->
              {if $pro_members}
                <div class="d-block d-lg-none">
                  <div class="card bg-indigo border-0">
                    <div class="card-header ptb20 bg-transparent border-bottom-0">
                      {if $system['packages_enabled'] && !$user->_data['user_subscribed']}
                        <div class="float-end">
                          <small><a class="text-white text-underline" href="{$system['system_url']}/packages">{__("Upgrade")}</a></small>
                        </div>
                      {/if}
                      <h6 class="pb0">
                        {include file='__svg_icons.tpl' icon="pro" class="mr5" width="20px" height="20px" style="fill: #fff;"}
                        {__("Pro Users")}
                      </h6>
                    </div>
                    <div class="card-body pt0 plr5">
                      <div class="pro-box-wrapper {if count($pro_members) > 3}js_slick{else}full-opacity{/if}">
                        {foreach $pro_members as $_member}
                          <a class="user-box text-white" href="{$system['system_url']}/{$_member['user_name']}">
                            <img alt="" src="{$_member['user_picture']}" />
                            <div class="name">
                              {if $system['show_usernames_enabled']}
                                {$_member['user_name']}
                              {else}
                                {$_member['user_firstname']} {$_member['user_lastname']}
                              {/if}
                            </div>
                          </a>
                        {/foreach}
                      </div>
                    </div>
                  </div>
                </div>
              {/if}
              <!-- pro users -->

              <!-- pro pages -->
              {if $promoted_pages}
                <div class="d-block d-lg-none">
                  <div class="card bg-teal border-0">
                    <div class="card-header ptb20 bg-transparent border-bottom-0">
                      {if $system['packages_enabled'] && !$user->_data['user_subscribed']}
                        <div class="float-end">
                          <small><a class="text-white text-underline" href="{$system['system_url']}/packages">{__("Upgrade")}</a></small>
                        </div>
                      {/if}
                      <h6 class="pb0">
                        {include file='__svg_icons.tpl' icon="pro" class="mr5" width="20px" height="20px" style="fill: #fff;"}
                        {__("Pro Pages")}
                      </h6>
                    </div>
                    <div class="card-body pt0 plr5">
                      <div class="pro-box-wrapper {if count($promoted_pages) > 3}js_slick{else}full-opacity{/if}">
                        {foreach $promoted_pages as $_page}
                          <a class="user-box text-white" href="{$system['system_url']}/pages/{$_page['page_name']}">
                            <img alt="{$_page['page_title']}" src="{$_page['page_picture']}" />
                            <div class="name" title="{$_page['page_title']}">
                              {$_page['page_title']}
                            </div>
                          </a>
                        {/foreach}
                      </div>
                    </div>
                  </div>
                </div>
              {/if}
              <!-- pro pages -->
            {/if}

            <!-- boosted post -->
            {if $boosted_post}
              {include file='_boosted_post.tpl' post=$boosted_post}
            {/if}
            <!-- boosted post -->

            <!-- posts -->
            {include file='_posts.tpl' _get="newsfeed"}
            <!-- posts -->

          {elseif $view == "popular"}
            <!-- popular posts -->
            {include file='_posts.tpl' _get="popular" _title=__("Popular Posts")}
            <!-- popular posts -->

          {elseif $view == "discover"}
            <!-- discover posts -->
            {include file='_posts.tpl' _get="discover" _title=__("Discover Posts")}
            <!-- discover posts -->

          {elseif $view == "saved"}
            <!-- saved posts -->
            {include file='_posts.tpl' _get="saved" _title=__("Saved Posts")}
            <!-- saved posts -->

          {elseif $view == "memories"}
            <!-- page header -->
            <div class="page-header mini rounded mb10">
              <div class="circle-1"></div>
              <div class="circle-2"></div>
              <div class="inner">
                <h2>{__("Memories")}</h2>
                <p class="text-lg">{__("Enjoy looking back on your memories")}</p>
              </div>
            </div>
            <!-- page header -->

            <!-- memories posts -->
            {include file='_posts.tpl' _get="memories" _title=__("ON THIS DAY") _filter="all"}
            <!-- memories posts -->

          {elseif $view == "articles"}
            <!-- articles posts -->
            {include file='_posts.tpl' _get="posts_profile" _id=$user->_data['user_id'] _filter="article" _title=__("My Articles")}
            <!-- articles posts -->

          {elseif $view == "products"}
            <!-- products posts -->
            {include file='_posts.tpl' _get="posts_profile" _id=$user->_data['user_id'] _filter="product" _title=__("My Products")}
            <!-- products posts -->

          {elseif $view == "funding_requests"}
            <!-- funding posts -->
            {include file='_posts.tpl' _get="posts_profile" _id=$user->_data['user_id'] _filter="funding" _title=__("My Funding Requests")}
            <!-- funding posts -->

          {elseif $view == "boosted_posts"}
            {if $user->_is_admin || $user->_data['user_subscribed']}
              <!-- boosted posts -->
              {include file='_posts.tpl' _get="boosted" _title=__("My Boosted Posts")}
              <!-- boosted posts -->
            {else}
              <!-- upgrade -->
              <div class="alert alert-warning">
                <div class="icon">
                  <i class="fa fa-id-card fa-2x"></i>
                </div>
                <div class="text">
                  <strong>{__("Membership")}</strong><br>
                  {__("Choose the Plan That's Right for You")}, {__("Check the package from")} <a href="{$system['system_url']}/packages">{__("Here")}</a>
                </div>
              </div>
              <div class="text-center">
                <a href="{$system['system_url']}/packages" class="btn btn-primary"><i class="fa fa-rocket mr5"></i>{__("Upgrade to Pro")}</a>
              </div>
              <!-- upgrade -->
            {/if}

          {elseif $view == "boosted_pages"}
            {if $user->_is_admin || $user->_data['user_subscribed']}
              <div class="card">
                <div class="card-header">
                  <strong>{__("My Boosted Pages")}</strong>
                </div>
                <div class="card-body">
                  {if $boosted_pages}
                    <ul>
                      {foreach $boosted_pages as $_page}
                        {include file='__feeds_page.tpl' _tpl="list"}
                      {/foreach}
                    </ul>

                    {if count($boosted_pages) >= $system['max_results_even']}
                      <!-- see-more -->
                      <div class="alert alert-info see-more js_see-more" data-get="boosted_pages">
                        <span>{__("See More")}</span>
                        <div class="loader loader_small x-hidden"></div>
                      </div>
                      <!-- see-more -->
                    {/if}
                  {else}
                    {include file='_no_data.tpl'}
                  {/if}
                </div>
              </div>
            {else}
              <!-- upgrade -->
              <div class="alert alert-warning">
                <div class="icon">
                  <i class="fa fa-id-card fa-2x"></i>
                </div>
                <div class="text">
                  <strong>{__("Membership")}</strong><br>
                  {__("Choose the Plan That's Right for You")}, {__("Check the package from")} <a href="{$system['system_url']}/packages">{__("Here")}</a>
                </div>
              </div>
              <div class="text-center">
                <a href="{$system['system_url']}/packages" class="btn btn-primary"><i class="fa fa-rocket mr5"></i>{__("Upgrade to Pro")}</a>
              </div>
              <!-- upgrade -->
            {/if}

          {elseif $view == "watch"}
            <!-- videos posts -->
            {include file='_posts.tpl' _get="discover" _filter="video" _title=__("Watch")}
            <!-- videos posts -->

          {/if}
        </div>
        <!-- center panel -->

        <!-- right panel -->
        <div class="col-lg-4 js_sticky-sidebar">

          <!-- pro users -->
          {if $pro_members}
            <div class="d-none d-lg-block">
              <div class="card bg-indigo border-0">
                <div class="card-header ptb20 bg-transparent border-bottom-0">
                  {if $system['packages_enabled'] && !$user->_data['user_subscribed']}
                    <div class="float-end">
                      <small><a class="text-white text-underline" href="{$system['system_url']}/packages">{__("Upgrade")}</a></small>
                    </div>
                  {/if}
                  <h6 class="pb0">
                    {include file='__svg_icons.tpl' icon="pro" class="mr5" width="20px" height="20px" style="fill: #fff;"}
                    {__("Pro Users")}
                  </h6>
                </div>
                <div class="card-body pt0 plr5">
                  <div class="pro-box-wrapper {if count($pro_members) > 3}js_slick{else}full-opacity{/if}">
                    {foreach $pro_members as $_member}
                      <a class="user-box text-white" href="{$system['system_url']}/{$_member['user_name']}">
                        <img alt="" src="{$_member['user_picture']}" />
                        <div class="name">
                          {if $system['show_usernames_enabled']}
                            {$_member['user_name']}
                          {else}
                            {$_member['user_firstname']} {$_member['user_lastname']}
                          {/if}
                        </div>
                      </a>
                    {/foreach}
                  </div>
                </div>
              </div>
            </div>
          {/if}
          <!-- pro users -->

          <!-- pro pages -->
          {if $promoted_pages}
            <div class="d-none d-lg-block">
              <div class="card bg-teal border-0">
                <div class="card-header ptb20 bg-transparent border-bottom-0">
                  {if $system['packages_enabled'] && !$user->_data['user_subscribed']}
                    <div class="float-end">
                      <small><a class="text-white text-underline" href="{$system['system_url']}/packages">{__("Upgrade")}</a></small>
                    </div>
                  {/if}
                  <h6 class="pb0">
                    {include file='__svg_icons.tpl' icon="pro" class="mr5" width="20px" height="20px" style="fill: #fff;"}
                    {__("Pro Pages")}
                  </h6>
                </div>
                <div class="card-body pt0 plr5">
                  <div class="pro-box-wrapper {if count($promoted_pages) > 3}js_slick{else}full-opacity{/if}">
                    {foreach $promoted_pages as $_page}
                      <a class="user-box text-white" href="{$system['system_url']}/pages/{$_page['page_name']}">
                        <img alt="{$_page['page_title']}" src="{$_page['page_picture']}" />
                        <div class="name" title="{$_page['page_title']}">
                          {$_page['page_title']}
                        </div>
                      </a>
                    {/foreach}
                  </div>
                </div>
              </div>
            </div>
          {/if}
          <!-- pro pages -->

          <!-- trending -->
          {if $trending_hashtags}
            {include file='_trending_widget.tpl'}
          {/if}
          <!-- trending -->

          {include file='_ads.tpl'}
          {include file='_ads_campaigns.tpl'}
          {include file='_widget.tpl'}

          <!-- friend suggestions -->
          {if $new_people}
            <div class="card">
              <div class="card-header bg-transparent">
                <div class="float-end">
                  <small><a href="{$system['system_url']}/people">{__("See All")}</a></small>
                </div>
                {__("Friend Suggestions")}
              </div>
              <div class="card-body with-list">
                <ul>
                  {foreach $new_people as $_user}
                    {include file='__feeds_user.tpl' _tpl="list" _connection="add"}
                  {/foreach}
                </ul>
              </div>
            </div>
          {/if}
          <!-- friend suggestions -->

          <!-- suggested pages -->
          {if $new_pages}
            <div class="card">
              <div class="card-header bg-transparent">
                <div class="float-end">
                  <small><a href="{$system['system_url']}/pages">{__("See All")}</a></small>
                </div>
                {__("Suggested Pages")}
              </div>
              <div class="card-body with-list">
                <ul>
                  {foreach $new_pages as $_page}
                    {include file='__feeds_page.tpl' _tpl="list"}
                  {/foreach}
                </ul>
              </div>
            </div>
          {/if}
          <!-- suggested pages -->

          <!-- suggested groups -->
          {if $new_groups}
            <div class="card">
              <div class="card-header bg-transparent">
                <div class="float-end">
                  <small><a href="{$system['system_url']}/groups">{__("See All")}</a></small>
                </div>
                {__("Suggested Groups")}
              </div>
              <div class="card-body with-list">
                <ul>
                  {foreach $new_groups as $_group}
                    {include file='__feeds_group.tpl' _tpl="list"}
                  {/foreach}
                </ul>
              </div>
            </div>
          {/if}
          <!-- suggested groups -->

          <!-- suggested events -->
          {if $new_events}
            <div class="card">
              <div class="card-header bg-transparent">
                <div class="float-end">
                  <small><a href="{$system['system_url']}/events">{__("See All")}</a></small>
                </div>
                {__("Suggested Events")}
              </div>
              <div class="card-body with-list">
                <ul>
                  {foreach $new_events as $_event}
                    {include file='__feeds_event.tpl' _tpl="list" _small=true}
                  {/foreach}
                </ul>
              </div>
            </div>
          {/if}
          <!-- suggested events -->

          <!-- invitation widget -->
          {if $user->_data['can_invite_users']}
            <div class="card">
              <div class="card-body text-center">
                <div class="mb10">
                  {include file='__svg_icons.tpl' icon="invitation" class="main-icon" width="60px" height="60px"}
                </div>
                <a class="btn btn-sm btn-primary rounded-pill" href="{$system['system_url']}/settings/invitations">{__("Invite Your Friends")}</a>
              </div>
            </div>
          {/if}
          <!-- invitation widget -->

          <!-- mini footer -->
          {include file='_footer_mini.tpl'}
          <!-- mini footer -->

        </div>
        <!-- right panel -->
      </div>
    </div>
    <!-- content panel -->

  </div>
</div>

{include file='_footer.tpl'}