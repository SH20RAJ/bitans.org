<div class="card">

  {if $sub_view == ""}

    <div class="card-header with-icon">
      <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {__("Settings")}
    </div>

    <form class="js_ajax-forms" data-url="admin/ads.php?do=settings">
      <div class="card-body">
        <!-- adblock-warning-message -->
        <div class="adblock-warning-message">
          {__("Turn off the ad blocker or add this web page's URL as an exception so you use ads system without any problems")}, {__("After you turn off the ad blocker, you'll need to refresh your screen")}
        </div>
        <!-- adblock-warning-message -->

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="ads" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Ads Campaigns")}</div>
            <div class="form-text d-none d-sm-block">{__("Allow users to create ads")} ({__("Enable it will enable wallet by default")})</div>
          </div>
          <div class="text-end">
            <label class="switch" for="ads_enabled">
              <input type="checkbox" name="ads_enabled" id="ads_enabled" {if $system['ads_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="verification" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Ads Campaigns Approval System")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the approval system On and Off")} ({__("If disabled all campaigns will be approved by default")})</div>
          </div>
          <div class="text-end">
            <label class="switch" for="ads_approval_enabled">
              <input type="checkbox" name="ads_approval_enabled" id="ads_approval_enabled" {if $system['ads_approval_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-sm-3 form-label">
            {__("Cost by View")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="ads_cost_view" value="{$system['ads_cost_view']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-sm-3 form-label">
            {__("Cost by Click")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="ads_cost_click" value="{$system['ads_cost_click']}">
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {elseif $sub_view == "users_ads"}

    <!-- card-header -->
    <div class="card-header with-icon with-nav">
      <!-- panel title -->
      <div class="mb20">
        <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {__("Users Ads")}
      </div>
      <!-- panel title -->

      <!-- panel nav -->
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#Pending" data-bs-toggle="tab">
            <i class="fa fa-clock fa-fw mr5"></i><strong>{__("Pending")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Approved" data-bs-toggle="tab">
            <i class="fa fa-check-circle fa-fw mr5"></i><strong>{__("Approved")}</strong>
          </a>
        </li>
      </ul>
      <!-- panel nav -->
    </div>
    <!-- card-header -->

    <!-- tab-content -->
    <div class="tab-content">

      <!-- Pending -->
      <div class="tab-pane active" id="Pending">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover js_dataTable">
              <thead>
                <tr>
                  <th>{__("ID")}</th>
                  <th>{__("Title")}</th>
                  <th>{__("By")}</th>
                  <th>{__("Budget")}</th>
                  <th>{__("Status")}</th>
                  <th>{__("Actions")}</th>
                </tr>
              </thead>
              <tbody>
                {foreach $rows["pending"] as $row}
                  <tr>
                    <td>{$row['campaign_id']}</td>
                    <td>{$row['campaign_title']}</td>
                    <td>
                      <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                        <img class="tbl-image" src="{$row['user_picture']}">
                        {if $system['show_usernames_enabled']}{$row['user_name']}{else}{$row['user_firstname']} {$row['user_lastname']}{/if}
                      </a>
                    </td>
                    <td>{print_money($row['campaign_budget']|number_format:2)}</td>
                    <td>
                      <span class="badge rounded-pill badge-lg bg-warning">{__("Pending")}</span>
                    </td>
                    <td>
                      <a data-bs-toggle="tooltip" title='{__("View")}' href="{$system['system_url']}/ads/edit/{$row['campaign_id']}" target="_blank" class="btn btn-sm btn-icon btn-rounded btn-primary">
                        <i class="fa fa-eye"></i>
                      </a>
                      <button data-bs-toggle="tooltip" title='{__("Approve")}' class="btn btn-sm btn-icon btn-rounded btn-success js_ads-approve" data-id="{$row['campaign_id']}">
                        <i class="fa fa-check"></i>
                      </button>
                      <button data-bs-toggle="tooltip" title='{__("Decline")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_ads-decline" data-id="{$row['campaign_id']}">
                        <i class="fa fa-times"></i>
                      </button>
                    </td>
                  </tr>
                {/foreach}
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- Pending -->

      <!-- Approved -->
      <div class="tab-pane" id="Approved">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover js_dataTable">
              <thead>
                <tr>
                  <th>{__("ID")}</th>
                  <th>{__("Title")}</th>
                  <th>{__("By")}</th>
                  <th>{__("Budget")}</th>
                  <th>{__("Spend")}</th>
                  <th>{__("Clicks/Views")}</th>
                  <th>{__("Status")}</th>
                  <th>{__("Actions")}</th>
                </tr>
              </thead>
              <tbody>
                {foreach $rows["approved"] as $row}
                  <tr>
                    <td>{$row['campaign_id']}</td>
                    <td>{$row['campaign_title']}</td>
                    <td>
                      <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                        <img class="tbl-image" src="{$row['user_picture']}">
                        {if $system['show_usernames_enabled']}{$row['user_name']}{else}{$row['user_firstname']} {$row['user_lastname']}{/if}
                      </a>
                    </td>
                    <td>{print_money($row['campaign_budget']|number_format:2)}</td>
                    <td>{print_money($row['campaign_spend']|number_format:2)}</td>
                    <td>
                      {if $row['campaign_bidding'] == "click"}
                        {$row['campaign_clicks']} {__("Clicks")}
                      {else}
                        {$row['campaign_views']} {__("Views")}
                      {/if}
                    </td>
                    <td>
                      {if $row['campaign_is_active']}
                        <span class="badge rounded-pill badge-lg bg-success">{__("Active")}</span>
                      {else}
                        <span class="badge rounded-pill badge-lg bg-danger">{__("Not Active")}</span>
                      {/if}
                    </td>
                    <td>
                      <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/ads/edit/{$row['campaign_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                        <i class="fa fa-pencil-alt"></i>
                      </a>
                      {if $row['campaign_is_active']}
                        <button data-bs-toggle="tooltip" title='{__("Stop")}' class="btn btn-sm btn-icon btn-rounded btn-warning js_ads-stop-campaign" data-id="{$row['campaign_id']}">
                          <i class="fas fa-stop-circle"></i>
                        </button>
                      {else}
                        <button data-bs-toggle="tooltip" title='{__("Resume")}' class="btn btn-sm btn-icon btn-rounded btn-success js_ads-resume-campaign" data-id="{$row['campaign_id']}">
                          <i class="fas fa-play-circle"></i>
                        </button>
                      {/if}
                      <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_ads-delete-campaign" data-id="{$row['campaign_id']}">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                {/foreach}
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- Approved -->

    </div>
    <!-- tab-content -->

  {elseif $sub_view == "system_ads"}

    <div class="card-header with-icon">
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/ads/add" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New Ads")}
        </a>
      </div>
      <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {__("System Ads")}
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Title")}</th>
              <th>{__("Place")}</th>
              <th>{__("Date")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['ads_id']}</td>
                <td>{$row['title']}</td>
                <td>
                  {if $row['place'] == "home"}<i class='fa fa-home fa-fw mr5'></i>{__("Home Page")}{/if}
                  {if $row['place'] == "search"}<i class='fa fa-search fa-fw mr5'></i>{__("Search Page")}{/if}
                  {if $row['place'] == "people"}<i class='fa fa-users fa-fw mr5'></i>{__("Discover People Page")}{/if}
                  {if $row['place'] == "notifications"}<i class='fa fa-bell fa-fw mr5'></i>{__("Notifications Page")}{/if}
                  {if $row['place'] == "post"}<i class='fa fa-file-powerpoint fa-fw mr5'></i>{__("Post (Right Panel)")}{/if}
                  {if $row['place'] == "post_footer"}<i class='fa fa-file-powerpoint fa-fw mr5'></i>{__("Post (Footer)")}{/if}
                  {if $row['place'] == "photo"}<i class='fa fa-file-image fa-fw mr5'></i>{__("Photo Page")}{/if}
                  {if $row['place'] == "pages"}<i class='fa fa-flag fa-fw mr5'></i>{__("Pages")}{/if}
                  {if $row['place'] == "groups"}<i class='fa fa-users fa-fw mr5'></i>{__("Groups")}{/if}
                  {if $row['place'] == "directory"}<i class='fa fa-th-list fa-fw mr5'></i>{__("Directory Page")}{/if}
                  {if $row['place'] == "market"}<i class='fa fa-shopping-bag fa-fw mr5'></i>{__("Market Page")}{/if}
                  {if $row['place'] == "offers"}<i class='fa fa-tag fa-fw mr5'></i>{__("Offers Page")}{/if}
                  {if $row['place'] == "jobs"}<i class='fa fa-briefcase fa-fw mr5'></i>{__("Jobs Page")}{/if}
                  {if $row['place'] == "movies"}<i class='fa fa-film fa-fw mr5'></i>{__("Movies Page")}{/if}
                  {if $row['place'] == "newfeed_1"}<i class='fa fa-newspaper fa-fw mr5'></i>{__("Posts Feed")} 1{/if}
                  {if $row['place'] == "newfeed_2"}<i class='fa fa-newspaper fa-fw mr5'></i>{__("Posts Feed")} 2{/if}
                  {if $row['place'] == "newfeed_3"}<i class='fa fa-newspaper fa-fw mr5'></i>{__("Posts Feed")} 3{/if}
                  {if $row['place'] == "article"}<i class='fa fa-file-alt fa-fw mr5'></i>{__("Article (Right Panel)")}{/if}
                  {if $row['place'] == "article_footer"}<i class='fa fa-file-alt fa-fw mr5'></i>{__("Article (Footer)")}{/if}
                  {if $row['place'] == "header"}<i class='fa fa-chevron-circle-up fa-fw mr5'></i>{__("Header")}{/if}
                  {if $row['place'] == "footer"}<i class='fa fa-chevron-circle-down fa-fw mr5'></i>{__("Footer")}{/if}
                </td>
                <td>{$row['time']|date_format:"%e %B %Y"}</td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/ads/edit/{$row['ads_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="ads_system" data-id="{$row['ads_id']}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "edit"}

    <div class="card-header with-icon">
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/ads/system_ads" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
      <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {$data['title']}
    </div>

    <form class="js_ajax-forms" data-url="admin/ads.php?do=edit&id={$data['ads_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title" value="{$data['title']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Place")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="place" id="js_ads-place">
              <option {if $data['place'] == "home"}selected{/if} value="home">{__("Home")}</option>
              <option {if $data['place'] == "search"}selected{/if} value="search">{__("Search")}</option>
              <option {if $data['place'] == "people"}selected{/if} value="people">{__("Discover People")}</option>
              <option {if $data['place'] == "notifications"}selected{/if} value="notifications">{__("Notifications")}</option>
              <option {if $data['place'] == "post"}selected{/if} value="post">{__("Post (Right Panel)")}</option>
              <option {if $data['place'] == "post_footer"}selected{/if} value="post_footer">{__("Post (Footer)")}</option>
              <option {if $data['place'] == "photo"}selected{/if} value="photo">{__("Photo")}</option>
              <option {if $data['place'] == "pages"}selected{/if} value="pages">{__("Pages")}</option>
              <option {if $data['place'] == "groups"}selected{/if} value="groups">{__("Groups")}</option>
              <option {if $data['place'] == "directory"}selected{/if} value="directory">{__("Directory")}</option>
              <option {if $data['place'] == "market"}selected{/if} value="market">{__("Marketplace")}</option>
              <option {if $data['place'] == "offers"}selected{/if} value="offers">{__("Jobs")}</option>
              <option {if $data['place'] == "jobs"}selected{/if} value="jobs">{__("Jobs")}</option>
              <option {if $data['place'] == "movies"}selected{/if} value="movies">{__("Movies")}</option>
              <option {if $data['place'] == "newfeed_1"}selected{/if} value="newfeed_1">{__("Posts Feed")} 1</option>
              <option {if $data['place'] == "newfeed_2"}selected{/if} value="newfeed_2">{__("Posts Feed")} 2</option>
              <option {if $data['place'] == "newfeed_3"}selected{/if} value="newfeed_3">{__("Posts Feed")} 3</option>
              <option {if $data['place'] == "article"}selected{/if} value="article">{__("Article (Right Panel)")}</option>
              <option {if $data['place'] == "article_footer"}selected{/if} value="article_footer">{__("Article (Footer)")}</option>
              <option {if $data['place'] == "header"}selected{/if} value="header">{__("Header")}</option>
              <option {if $data['place'] == "footer"}selected{/if} value="footer">{__("Footer")}</option>
            </select>
          </div>
        </div>

        <div id="js_selected-pages" {if !$data['ads_pages_ids']}class="x-hidden" {/if}>
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Pages")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="pages" name="ads_pages_ids" value="{$data['ads_pages_ids']}">
              <div class="form-text">
                {__("Search for pages you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div id="js_selected-groups" {if !$data['ads_groups_ids']}class="x-hidden" {/if}>
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Groups")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="groups" name="ads_groups_ids" value="{$data['ads_groups_ids']}">
              <div class="form-text">
                {__("Search for groups you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("HTML")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="message" rows="8">{$data['code']}</textarea>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {elseif $sub_view == "add"}

    <div class="card-header with-icon">
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/ads/system_ads" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
      <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {__("Add New Ads")}
    </div>

    <form class="js_ajax-forms" data-url="admin/ads.php?do=add">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Place")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="place" id="js_ads-place">
              <option value="home">{__("Home")}</option>
              <option value="search">{__("Search")}</option>
              <option value="people">{__("Discover People")}</option>
              <option value="notifications">{__("Notifications")}</option>
              <option value="post">{__("Post (Right Panel)")}</option>
              <option value="post_footer">{__("Post (Footer)")}</option>
              <option value="photo">{__("Photo")}</option>
              <option value="pages">{__("Pages")}</option>
              <option value="groups">{__("Groups")}</option>
              <option value="directory">{__("Directory")}</option>
              <option value="market">{__("Marketplace")}</option>
              <option value="offers">{__("Offers")}</option>
              <option value="jobs">{__("Jobs")}</option>
              <option value="movies">{__("Movies")}</option>
              <option value="newfeed_1">{__("Posts Feed")} 1</option>
              <option value="newfeed_2">{__("Posts Feed")} 2</option>
              <option value="newfeed_3">{__("Posts Feed")} 3</option>
              <option value="article">{__("Article (Right Panel)")}</option>
              <option value="article_footer">{__("Article (Footer)")}</option>
              <option value="header">{__("Header")}</option>
              <option value="footer">{__("Footer")}</option>
            </select>
          </div>
        </div>

        <div id="js_selected-pages" class="x-hidden">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Pages")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="pages" name="ads_pages_ids">
              <div class="form-text">
                {__("Search for pages you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div id="js_selected-groups" class="x-hidden">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Groups")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="groups" name="ads_groups_ids">
              <div class="form-text">
                {__("Search for groups you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("HTML")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="message" rows="8"></textarea>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {/if}

</div>