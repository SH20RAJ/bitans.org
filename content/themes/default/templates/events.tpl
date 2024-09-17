{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_calendar_re_ki49.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
    <h2>{__("Events")}</h2>
    <p class="text-xlg">{__($system['system_description_events'])}</p>
    <div class="row mt20">
      <div class="col-sm-9 col-lg-6 mx-sm-auto">
        <form class="js_search-form" data-filter="events">
          <div class="input-group">
            <input type="text" class="form-control" name="query" placeholder='{__("Search for events")}'>
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
        <li {if $view == "" || $view == "category"}class="active" {/if}>
          <a href="{$system['system_url']}/events">{__("Discover")}</a>
        </li>
        {if $user->_logged_in}
          <li {if $view == "going"}class="active" {/if}>
            <a href="{$system['system_url']}/events/going">{__("Going")}</a>
          </li>
          <li {if $view == "interested"}class="active" {/if}>
            <a href="{$system['system_url']}/events/interested">{__("Interested")}</a>
          </li>
          <li {if $view == "invited"}class="active" {/if}>
            <a href="{$system['system_url']}/events/invited">{__("Invited")}</a>
          </li>
          <li {if $view == "manage"}class="active" {/if}>
            <a href="{$system['system_url']}/events/manage">{__("My Events")}</a>
          </li>
        {/if}
      </ul>
      {if $user->_data['can_create_events']}
        <div class="mt10 float-end">
          <button class="btn btn-md btn-primary d-none d-lg-block" data-toggle="modal" data-url="modules/add.php?type=event">
            <i class="fa fa-plus-circle mr5"></i>{__("Create Event")}
          </button>
          <button class="btn btn-sm btn-icon btn-success d-block d-lg-none" data-toggle="modal" data-url="modules/add.php?type=event">
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
                  <a href="{$system['system_url']}/events">
                    {__("All")}
                  </a>
                </li>
              {else}
                <li>
                  {if $current_category['parent']}
                    <a href="{$system['system_url']}/events/category/{$current_category['parent']['category_id']}/{$current_category['parent']['category_url']}">
                      <i class="fas fa-arrow-alt-circle-left mr5"></i>{__($current_category['parent']['category_name'])}
                    </a>
                  {else}
                    <a href="{$system['system_url']}/events">
                      {if $current_category['sub_categories']}<i class="fas fa-arrow-alt-circle-left mr5"></i>{/if}{__("All")}
                    </a>
                  {/if}
                </li>
              {/if}
              {foreach $categories as $category}
                <li {if $view == "category" && $current_category['category_id'] == $category['category_id']}class="active" {/if}>
                  <a href="{$system['system_url']}/events/category/{$category['category_id']}/{$category['category_url']}">
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
        {if $events}
          <ul class="row">
            {foreach $events as $_event}
              {include file='__feeds_event.tpl' _tpl='box'}
            {/foreach}
          </ul>

          <!-- see-more -->
          {if count($events) >= $system['events_results']}
            <div class="alert alert-post see-more js_see-more" data-get="{$get}" {if $view == "category"}data-id="{$current_category['category_id']}" {/if} {if $view == "going" || $view == "interested" || $view == "invited" || $view == "manage"}data-uid="{$user->_data['user_id']}" {/if}>
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