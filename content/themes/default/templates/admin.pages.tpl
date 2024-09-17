<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == "find"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/pages" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {elseif $sub_view == "edit_page"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/pages" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left"></i><span class="ml5 d-none d-lg-inline-block">{__("Go Back")}</span>
        </a>
        <a target="_blank" href="{$system['system_url']}/pages/{$data['page_name']}" class="btn btn-md btn-info">
          <i class="fa fa-eye"></i><span class="ml5 d-none d-lg-inline-block">{__("View Page")}</span>
        </a>
      </div>
    {elseif $sub_view == "categories"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/pages/add_category" class="btn btn-md btn-primary">
          <i class="fa fa-plus"></i><span class="ml5 d-none d-lg-inline-block">{__("Add New Category")}</span>
        </a>
      </div>
    {elseif $sub_view == "add_category" || $sub_view == "edit_category"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/pages/categories" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left"></i><span class="ml5 d-none d-lg-inline-block">{__("Go Back")}</span>
        </a>
      </div>
    {/if}
    <i class="fa fa-flag mr10"></i>{__("Pages")}
    {if $sub_view == "find"} &rsaquo; {__("Find")}{/if}
    {if $sub_view == "edit_page"} &rsaquo; {$data['page_title']}{/if}
    {if $sub_view == "categories"} &rsaquo; {__("Categories")}{/if}
    {if $sub_view == "add_category"} &rsaquo; {__("Categories")} &rsaquo; {__("Add New Category")}{/if}
    {if $sub_view == "edit_category"} &rsaquo; {__("Categories")} &rsaquo; {$data['category_name']}{/if}
  </div>

  {if $sub_view == "" || $sub_view == "find"}

    <div class="card-body">

      {if $sub_view == ""}
        <div class="row">
          <div class="col-sm-4">
            <div class="stat-panel bg-gradient-indigo">
              <div class="stat-cell narrow">
                <i class="fa fa-flag bg-icon"></i>
                <span class="text-xxlg">{$insights['pages']}</span><br>
                <span class="text-lg">{__("Pages")}</span><br>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="stat-panel bg-gradient-primary">
              <div class="stat-cell narrow">
                <i class="fa fa-check bg-icon"></i>
                <span class="text-xxlg">{$insights['pages_verified']}</span><br>
                <span class="text-lg">{__("Verified Pages")}</span><br>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="stat-panel bg-gradient-info">
              <div class="stat-cell narrow">
                <i class="fa fa-heart bg-icon"></i>
                <span class="text-xxlg">{$insights['pages_likes']}</span><br>
                <span class="text-lg">{__("Total Likes")}</span><br>
              </div>
            </div>
          </div>
        </div>
      {/if}

      <!-- search form -->
      <div class="mb20">
        <form class="d-flex flex-row align-items-center flex-wrap" action="{$system['system_url']}/{$control_panel['url']}/pages/find" method="get">
          <div class="form-group mb0">
            <div class="input-group">
              <input type="text" class="form-control" name="query">
              <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-search mr5"></i>{__("Search")}</button>
            </div>
          </div>
        </form>
        <div class="form-text small">
          {__("Search by Page Web Address or Title")}
        </div>
      </div>
      <!-- search form -->

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Page")}</th>
              <th>{__("Admin")}</th>
              <th>{__("Likes")}</th>
              <th>{__("Verified")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>
                    <a href="{$system['system_url']}/pages/{$row['page_name']}" target="_blank">
                      {$row['page_id']}
                    </a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/pages/{$row['page_name']}">
                      <img class="tbl-image" src="{$row['page_picture']}">
                      {$row['page_title']}
                    </a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                      <img class="tbl-image" src="{$row['user_picture']}">
                      {if $system['show_usernames_enabled']}{$row['user_name']}{else}{$row['user_firstname']} {$row['user_lastname']}{/if}
                    </a>
                  </td>
                  <td>{$row['page_likes']}</td>
                  <td>
                    {if $row['page_verified']}
                      <span class="badge rounded-pill badge-lg bg-success">{__("Yes")}</span>
                    {else}
                      <span class="badge rounded-pill badge-lg bg-danger">{__("No")}</span>
                    {/if}
                  </td>
                  <td>
                    <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/pages/edit_page/{$row['page_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                      <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="page" data-id="{$row['page_id']}">
                      <i class="fa fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              {/foreach}
            {else}
              <tr>
                <td colspan="6" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>
      {$pager}
    </div>

  {elseif $sub_view == "edit_page"}

    <div class="card-body">
      <div class="row">
        <div class="col-12 col-md-2 text-center mb20">
          <img class="img-fluid img-thumbnail rounded-circle" src="{$data['page_picture']}">
        </div>
        <div class="col-12 col-md-10 mb20">
          <ul class="list-group">
            <li class="list-group-item">
              <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['page_id']}</span>
              {__("Page ID")}
            </li>
            <li class="list-group-item">
              <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['page_likes']}</span>
              {__("Likes")}
            </li>
          </ul>
        </div>
      </div>

      <!-- tabs nav -->
      <ul class="nav nav-tabs mb20">
        <li class="nav-item">
          <a class="nav-link active" href="#page_settings" data-bs-toggle="tab">
            <i class="fa fa-cog fa-fw mr5"></i><strong>{__("Settings")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#page_info" data-bs-toggle="tab">
            <i class="fa fa-info-circle fa-fw mr5"></i><strong>{__("Info")}</strong>
          </a>
        </li>
        {if $system['monetization_enabled']}
          <li class="nav-item">
            <a class="nav-link" href="#page_monetization" data-bs-toggle="tab">
              <i class="fa fa-coins fa-fw mr5"></i><strong>{__("Monetization")}</strong>
            </a>
          </li>
        {/if}
      </ul>
      <!-- tabs nav -->

      <!-- tabs content -->
      <div class="tab-content">
        <!-- settings tab -->
        <div class="tab-pane active" id="page_settings">
          <form class="js_ajax-forms" data-url="admin/pages.php?do=edit_page&edit=settings&id={$data['page_id']}">
            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Created By")}
              </label>
              <div class="col-md-9">
                <a target="_blank" href="{$system['system_url']}/{$data['user_name']}">
                  <img class="tbl-image" src="{$data['user_picture']}">
                  {if $system['show_usernames_enabled']}{$data['user_name']}{else}{$data['user_firstname']} {$data['user_lastname']}{/if}
                </a>
                <a target="_blank" data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/users/edit/{$data['user_id']}" class="btn btn-sm btn-light btn-icon btn-rounded ml10">
                  <i class="fa fa-pencil-alt"></i>
                </a>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Verified Page")}
              </label>
              <div class="col-md-9">
                <label class="switch" for="page_verified">
                  <input type="checkbox" name="page_verified" id="page_verified" {if $data['page_verified']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Name Your Page")}
              </label>
              <div class="col-md-9">
                <input class="form-control" name="title" value="{$data['page_title']}">
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Page Username")}
              </label>
              <div class="col-md-9">
                <div class="input-group">
                  <span class="input-group-text d-none d-sm-block">{$system['system_url']}/pages/</span>
                  <input type="text" class="form-control" name="username" id="username" value="{$data['page_name']}">
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Category")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="category">
                  {foreach $data['categories'] as $category}
                    {include file='__categories.recursive_options.tpl' data_category=$data['page_category']}
                  {/foreach}
                </select>
              </div>
            </div>

            {if $system['tips_enabled']}
              <div class="divider"></div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="tip" class="main-icon" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Tips Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Allow the send tips button on your page")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="page_tips_enabled">
                    <input type="checkbox" name="page_tips_enabled" id="page_tips_enabled" {if $data['page_tips_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>
            {/if}

            <!-- success -->
            <div class="alert alert-success mb0 mt20 x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger mb0 mt20 x-hidden"></div>
            <!-- error -->

            <div class="card-footer-fake text-end">
              <button type="button" class="btn btn-danger js_admin-deleter" data-handle="page_posts" data-id="{$data['page_id']}" data-delete-message="{__("Are you sure you want to delete all posts?")}">
                <i class="fa fa-trash-alt mr5"></i>{__("Delete Posts")}
              </button>
              <button type="button" class="btn btn-danger js_admin-deleter" data-handle="page" data-id="{$data['page_id']}" data-redirect="{$system['system_url']}/{$control_panel['url']}/pages">
                <i class="fa fa-trash-alt mr5"></i>{__("Delete Page")}
              </button>
              <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
            </div>
          </form>
        </div>
        <!-- settings tab -->

        <!-- info tab -->
        <div class="tab-pane" id="page_info">
          <form class="js_ajax-forms" data-url="admin/pages.php?do=edit_page&edit=info&id={$data['page_id']}">
            <div class="row">
              <div class="form-group col-md-6">
                <label class="form-label" for="company">{__("Company")}</label>
                <input type="text" class="form-control" name="company" id="company" value="{$data['page_company']}">
              </div>
              <div class="form-group col-md-6">
                <label class="form-label" for="phone">{__("Phone")}</label>
                <input type="text" class="form-control" name="phone" id="phone" value="{$data['page_phone']}">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-6">
                <label class="form-label" for="website">{__("Website")}</label>
                <input type="text" class="form-control" name="website" id="website" value="{$data['page_website']}">
              </div>
              <div class="form-group col-md-6">
                <label class="form-label" for="location">{__("Location")}</label>
                <input type="text" class="form-control js_geocomplete" name="location" id="location" value="{$data['page_location']}">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="country">{__("Country")}</label>
              <select class="form-select" name="country">
                <option value="none">{__("Select Country")}</option>
                {foreach $data['countries'] as $country}
                  <option value="{$country['country_id']}" {if $data['page_country'] == $country['country_id']}selected{/if}>{$country['country_name']}</option>
                {/foreach}
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="description">{__("About")}</label>
              <textarea class="form-control" name="description" id="description">{$data['page_description']}</textarea>
            </div>

            <!-- custom fields -->
            {if $custom_fields['basic']}
              {include file='__custom_fields.tpl' _custom_fields=$custom_fields['basic'] _registration=false}
            {/if}
            <!-- custom fields -->

            <!-- success -->
            <div class="alert alert-success x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger x-hidden"></div>
            <!-- error -->

            <div class="card-footer-fake text-end">
              <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
            </div>
          </form>
        </div>
        <!-- info tab -->

        <!-- monetization tab -->
        <div class="tab-pane" id="page_monetization">
          {if $data['can_monetize_content']}
            <form class="js_ajax-forms" data-url="admin/pages.php?do=edit_page&edit=monetization&id={$data['page_id']}">
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="monetization" class="main-icon" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Monetization")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable or disable monetization for your content")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="page_monetization_enabled">
                    <input type="checkbox" name="page_monetization_enabled" id="page_monetization_enabled" {if $data['page_monetization_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Payment Plans")}
                </label>
                <div class="col-md-9">
                  <div class="payment-plans">
                    {foreach $monetization_plans as $plan}
                      <div class="payment-plan">
                        <div class="text-xxlg">{__($plan['title'])}</div>
                        <div class="text-xlg">{print_money($plan['price'])} / {if $plan['period_num'] != '1'}{$plan['period_num']}{/if} {__($plan['period']|ucfirst)}</div>
                        {if {$plan['custom_description']}}
                          <div>{$plan['custom_description']}</div>
                        {/if}
                        <div class="mt10">
                          <span class="text-link mr10 js_monetization-deleter" data-id="{$plan['plan_id']}">
                            <i class="fa fa-trash-alt mr5"></i>{__("Delete")}
                          </span>
                          |
                          <span data-toggle="modal" data-url="monetization/controller.php?do=edit&id={$plan['plan_id']}" class="text-link ml10">
                            <i class="fa fa-pen mr5"></i>{__("Edit")}
                          </span>
                        </div>
                      </div>
                    {/foreach}
                    <div data-toggle="modal" data-url="monetization/controller.php?do=add&node_id={$data['page_id']}&node_type=page" class="payment-plan new">{__("Add new plan")} </div>
                  </div>
                </div>
              </div>

              <!-- success -->
              <div class="alert alert-success x-hidden"></div>
              <!-- success -->

              <!-- error -->
              <div class="alert alert-danger x-hidden"></div>
              <!-- error -->

              <div class="card-footer-fake text-end">
                <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
              </div>
            </form>
          {else}
            <div class="alert alert-danger">
              <div class="icon">
                <i class="fa fa-minus-circle fa-2x"></i>
              </div>
              <div class="text pt5">
                {__("This page super admin is not eligible for monetization")}
              </div>
            </div>
          {/if}
        </div>
        <!-- monetization tab -->
      </div>
      <!-- tabs content -->
    </div>

  {elseif $sub_view == "categories"}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_treegrid">
          <thead>
            <tr>
              <th>{__("Title")}</th>
              <th>{__("Description")}</th>
              <th>{__("Order")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                {include file='__categories.recursive_rows.tpl' _url="pages" _handle="page_category"}
              {/foreach}
            {else}
              <tr>
                <td colspan="5" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "add_category"}

    <form class="js_ajax-forms" data-url="admin/pages.php?do=add_category">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_name">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="category_description" rows="3"></textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Parent Category")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="category_parent_id">
              <option value="0">{__("Set as a Partent Category")}</option>
              {foreach $categories as $category}
                {include file='__categories.recursive_options.tpl'}
              {/foreach}
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_order">
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

  {elseif $sub_view == "edit_category"}

    <form class="js_ajax-forms" data-url="admin/pages.php?do=edit_category&id={$data['category_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_name" value="{$data['category_name']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="category_description" rows="3">{$data['category_description']}</textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Parent Category")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="category_parent_id">
              <option value="0">{__("Set as a Partent Category")}</option>
              {foreach $data["categories"] as $category}
                {include file='__categories.recursive_options.tpl' data_category=$data['category_parent_id']}
              {/foreach}
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_order" value="{$data['category_order']}">
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