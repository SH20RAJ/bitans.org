<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == "find"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/groups" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {elseif $sub_view == "edit_group"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/groups" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left"></i><span class="ml5 d-none d-lg-inline-block">{__("Go Back")}</span>
        </a>
        <a target="_blank" href="{$system['system_url']}/groups/{$data['group_name']}" class="btn btn-md btn-info">
          <i class="fa fa-eye"></i><span class="ml5 d-none d-lg-inline-block">{__("View Group")}</span>
        </a>
      </div>
    {elseif $sub_view == "categories"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/groups/add_category" class="btn btn-md btn-primary">
          <i class="fa fa-plus"></i><span class="ml5 d-none d-lg-inline-block">{__("Add New Category")}</span>
        </a>
      </div>
    {elseif $sub_view == "add_category" || $sub_view == "edit_category"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/groups/categories" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left"></i><span class="ml5 d-none d-lg-inline-block">{__("Go Back")}</span>
        </a>
      </div>
    {/if}
    <i class="fa fa-users mr10"></i>{__("Groups")}
    {if $sub_view == "find"} &rsaquo; {__("Find")}{/if}
    {if $sub_view == "edit_group"} &rsaquo; {$data['group_title']}{/if}
    {if $sub_view == "categories"} &rsaquo; {__("Categories")}{/if}
    {if $sub_view == "add_category"} &rsaquo; {__("Categories")} &rsaquo; {__("Add New Category")}{/if}
    {if $sub_view == "edit_category"} &rsaquo; {__("Categories")} &rsaquo; {$data['category_name']}{/if}
  </div>

  {if $sub_view == "" || $sub_view == "find"}

    <div class="card-body">

      <!-- search form -->
      <div class="mb20">
        <form class="d-flex flex-row align-items-center flex-wrap" action="{$system['system_url']}/{$control_panel['url']}/groups/find" method="get">
          <div class="form-group mb0">
            <div class="input-group">
              <input type="text" class="form-control" name="query">
              <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-search mr5"></i>{__("Search")}</button>
            </div>
          </div>
        </form>
        <div class="form-text small">
          {__("Search by Group Web Address or Title")}
        </div>
      </div>
      <!-- search form -->

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Group")}</th>
              <th>{__("Admin")}</th>
              <th>{__("Privacy")}</th>
              <th>{__("Members")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>
                    <a href="{$system['system_url']}/groups/{$row['group_name']}" target="_blank">{$row['group_id']}</a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/groups/{$row['group_name']}">
                      <img class="tbl-image" src="{$row['group_picture']}">
                      {$row['group_title']}
                    </a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                      <img class="tbl-image" src="{$row['user_picture']}">
                      {if $system['show_usernames_enabled']}{$row['user_name']}{else}{$row['user_firstname']} {$row['user_lastname']}{/if}
                    </a>
                  </td>
                  <td>
                    {if $row['group_privacy'] == "public"}
                      <i class="fa fa-globe fa-fw mr5"></i>{__("Public")}
                    {elseif $row['group_privacy'] == "closed"}
                      <i class="fa fa-unlock-alt fa-fw mr5"></i>{__("Closed")}
                    {elseif $row['group_privacy'] == "secret"}
                      <i class="fa fa-lock fa-fw mr5"></i>{__("Secret")}
                    {/if}
                  </td>
                  <td>{$row['group_members']}</td>
                  <td>
                    <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/groups/edit_group/{$row['group_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                      <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="group" data-id="{$row['group_id']}">
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

  {elseif $sub_view == "edit_group"}

    <div class="card-body">
      <div class="row">
        <div class="col-12 col-md-2 text-center mb20">
          <img class="img-fluid img-thumbnail rounded-circle" src="{$data['group_picture']}">
        </div>
        <div class="col-12 col-md-10 mb20">
          <ul class="list-group">
            <li class="list-group-item">
              <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['group_id']}</span>
              {__("Group ID")}
            </li>
            <li class="list-group-item">
              <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['group_members']}</span>
              {__("Members")}
            </li>
            <li class="list-group-item">
              <span class="float-end badge badge-lg rounded-pill bg-secondary">
                {if $data['group_privacy'] == "public"}
                  <i class="fa fa-globe fa-fw mr5"></i>{__("Public")}
                {elseif $data['group_privacy'] == "closed"}
                  <i class="fa fa-unlock-alt fa-fw mr5"></i>{__("Closed")}
                {elseif $data['group_privacy'] == "secret"}
                  <i class="fa fa-lock fa-fw mr5"></i>{__("Secret")}
                {/if}
              </span>
              {__("Privacy")}
            </li>
          </ul>
        </div>
      </div>

      <!-- tabs nav -->
      <ul class="nav nav-tabs mb20">
        <li class="nav-item">
          <a class="nav-link active" href="#group_settings" data-bs-toggle="tab">
            <i class="fa fa-cog fa-fw mr5"></i><strong>{__("Settings")}</strong>
          </a>
        </li>
        {if $system['monetization_enabled']}
          <li class="nav-item">
            <a class="nav-link" href="#group_monetization" data-bs-toggle="tab">
              <i class="fa fa-coins fa-fw mr5"></i><strong>{__("Monetization")}</strong>
            </a>
          </li>
        {/if}
      </ul>
      <!-- tabs nav -->

      <!-- tabs content -->
      <div class="tab-content">
        <!-- settings tab -->
        <div class="tab-pane active" id="group_settings">
          <form class="js_ajax-forms" data-url="admin/groups.php?do=edit_group&edit=settings&id={$data['group_id']}">
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
                {__("Name Your Group")}
              </label>
              <div class="col-md-9">
                <input class="form-control" name="title" value="{$data['group_title']}">
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Group Username")}
              </label>
              <div class="col-md-9">
                <div class="input-group">
                  <span class="input-group-text d-none d-sm-block">{$system['system_url']}/groups/</span>
                  <input type="text" class="form-control" name="username" id="username" value="{$data['group_name']}">
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Select Privacy")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="privacy">
                  <option {if $data['group_privacy'] == "public"}selected{/if} value="public">{__("Public Group")}</option>
                  <option {if $data['group_privacy'] == "closed"}selected{/if} value="closed">{__("Closed Group")}</option>
                  <option {if $data['group_privacy'] == "secret"}selected{/if} value="secret">{__("Secret Group")}</option>
                </select>
                <div class="form-text">
                  ({__("Note: Change group privacy to public will approve any pending join requests")})
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
                    {include file='__categories.recursive_options.tpl' data_category=$data['group_category']}
                  {/foreach}
                </select>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("About")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="description" rows="5">{$data['group_description']}</textarea>
              </div>
            </div>

            <!-- custom fields -->
            {if $custom_fields['basic']}
              {include file='__custom_fields.tpl' _custom_fields=$custom_fields['basic'] _registration=false _inline=true}
            {/if}
            <!-- custom fields -->

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="chat" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Chat Box")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable chat box for this group")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="chatbox_enabled">
                  <input type="checkbox" name="chatbox_enabled" id="chatbox_enabled" {if $data['chatbox_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div>
                <div class="form-label h6">{__("Members Can Publish Posts?")}</div>
                <div class="form-text d-none d-sm-block">{__("Members can publish posts or only group admins")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="group_publish_enabled">
                  <input type="checkbox" name="group_publish_enabled" id="group_publish_enabled" {if $data['group_publish_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div>
                <div class="form-label h6">{__("Post Approval")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("All posts must be approved by a group admin")}<br>
                  ({__("Note: Disable it will approve any pending posts")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="group_publish_approval_enabled">
                  <input type="checkbox" name="group_publish_approval_enabled" id="group_publish_approval_enabled" {if $data['group_publish_approval_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <!-- success -->
            <div class="alert alert-success mt15 mb0 x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger mt15 mb0 x-hidden"></div>
            <!-- error -->
            <div class="card-footer-fake text-end">
              <button type="button" class="btn btn-danger js_admin-deleter" data-handle="group_posts" data-id="{$data['group_id']}" data-delete-message="{__("Are you sure you want to delete all posts?")}">
                <i class="fa fa-trash-alt mr5"></i>{__("Delete Posts")}
              </button>
              <button type="button" class="btn btn-danger js_admin-deleter" data-handle="group" data-id="{$data['group_id']}" data-redirect="{$system['system_url']}/{$control_panel['url']}/groups">
                <i class="fa fa-trash-alt mr5"></i>{__("Delete Group")}
              </button>
              <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
            </div>
          </form>
        </div>
        <!-- settings tab -->

        <!-- monetization tab -->
        <div class="tab-pane" id="group_monetization">
          {if $data['can_monetize_content']}
            <form class="js_ajax-forms" data-url="admin/groups.php?do=edit_group&edit=monetization&id={$data['group_id']}">
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="monetization" class="main-icon" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Monetization")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable or disable monetization for your content")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="group_monetization_enabled">
                    <input type="checkbox" name="group_monetization_enabled" id="group_monetization_enabled" {if $data['group_monetization_enabled']}checked{/if}>
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
                    <div data-toggle="modal" data-url="monetization/controller.php?do=add&node_id={$data['group_id']}&node_type=group" class="payment-plan new">{__("Add new plan")} </div>
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
                {__("This group super admin is not eligible for monetization")}
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
                {include file='__categories.recursive_rows.tpl' _url="groups" _handle="group_category"}
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

    <form class="js_ajax-forms" data-url="admin/groups.php?do=add_category">
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

    <form class="js_ajax-forms" data-url="admin/groups.php?do=edit_category&id={$data['category_id']}">
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