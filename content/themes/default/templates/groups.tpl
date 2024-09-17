{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_community_re_cyrm.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
    <h2>{__("Groups")}</h2>
    <p class="text-xlg">{__($system['system_description_groups'])}</p>
    <div class="row mt20">
      <div class="col-sm-9 col-lg-6 mx-sm-auto">
        <form class="js_search-form" data-filter="groups">
          <div class="input-group">
            <input type="text" class="form-control" name="query" placeholder='{__("Search for groups")}'>
            <button type="submit" class="btn btn-light">{__("Search")}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} sg-offcanvas" style="margin-top: -25px;">

  <div class="position-relative">
    <!-- tabs -->
    <div class="content-tabs rounded-sm shadow-sm clearfix">
      <ul>
        <li {if $view == ""}class="active" {/if}>
          <a href="{$system['system_url']}/groups">{__("Discover")}</a>
        </li>
        {if $user->_logged_in}
          <li {if $view == "joined"}class="active" {/if}>
            <a href="{$system['system_url']}/groups/joined">{__("Joined Groups")}</a>
          </li>
          <li {if $view == "manage"}class="active" {/if}>
            <a href="{$system['system_url']}/groups/manage">{__("My Groups")}</a>
          </li>
        {/if}
      </ul>
      {if $user->_data['can_create_groups']}
        <div class="mt10 float-end">
          <button class="btn btn-md btn-primary d-none d-lg-block" data-toggle="modal" data-url="modules/add.php?type=group">
            <i class="fa fa-plus-circle mr5"></i>{__("Create Group")}
          </button>
          <button class="btn btn-sm btn-icon btn-primary d-block d-lg-none" data-toggle="modal" data-url="modules/add.php?type=group">
            <i class="fa fa-plus-circle"></i>
          </button>
        </div>
      {/if}
    </div>
    <!-- tabs -->
  </div>

  <div class="row">

    {if $view == "" || $view == "category"}
      <!-- left panel -->
      <div class="col-md-4 col-lg-3 sg-offcanvas-sidebar">
        <!-- categories -->
        <div class="card">
          <div class="card-body with-nav">
            <ul class="side-nav">
              {if $view != "category"}
                <li class="active">
                  <a href="{$system['system_url']}/groups">
                    {__("All")}
                  </a>
                </li>
              {else}
                <li>
                  {if $current_category['parent']}
                    <a href="{$system['system_url']}/groups/category/{$current_category['parent']['category_id']}/{$current_category['parent']['category_url']}">
                      <i class="fas fa-arrow-alt-circle-left mr5"></i>{__($current_category['parent']['category_name'])}
                    </a>
                  {else}
                    <a href="{$system['system_url']}/groups">
                      {if $current_category['sub_categories']}<i class="fas fa-arrow-alt-circle-left mr5"></i>{/if}{__("All")}
                    </a>
                  {/if}
                </li>
              {/if}
              {foreach $categories as $category}
                <li {if $view == "category" && $current_category['category_id'] == $category['category_id']}class="active" {/if}>
                  <a href="{$system['system_url']}/groups/category/{$category['category_id']}/{$category['category_url']}">
                    {__($category['category_name'])}
                    {if $category['sub_categories']}
                      <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    {/if}
                  </a>
                </li>
              {/foreach}
            </ul>
          </div>
        </div>
        <!-- categories -->
      </div>
      <!-- left panel -->
    {else}
      <!-- side panel -->
      <div class="col-12 d-block d-md-none sg-offcanvas-sidebar">
        {include file='_sidebar.tpl'}
      </div>
      <!-- side panel -->
    {/if}

    <!-- content panel -->
    <div class="{if $view == "" || $view == "category"}col-md-8 col-lg-9 sg-offcanvas-mainbar{else}col-12 sg-offcanvas-mainbar{/if}">
      <!-- content -->
      <div>
        {if $groups}
          <ul class="row">
            {foreach $groups as $_group}
              {include file='__feeds_group.tpl' _tpl='box'}
            {/foreach}
          </ul>

          <!-- see-more -->
          {if count($groups) >= $system['groups_results']}
            <div class="alert alert-post see-more js_see-more" data-get="{$get}" {if $view == "category"}data-id="{$current_category['category_id']}" {/if} {if $view == "joined" || $view == "manage"}data-uid="{$user->_data['user_id']}" {/if}>
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

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}