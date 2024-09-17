<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == "find"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/events" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {elseif $sub_view == "edit_event"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/events" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left"></i><span class="ml5 d-none d-lg-inline-block">{__("Go Back")}</span>
        </a>
        <a target="_blank" href="{$system['system_url']}/events/{$data['event_id']}" class="btn btn-md btn-info">
          <i class="fa fa-eye"></i><span class="ml5 d-none d-lg-inline-block">{__("View Event")}</span>
        </a>
      </div>
    {elseif $sub_view == "categories"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/events/add_category" class="btn btn-md btn-primary">
          <i class="fa fa-plus"></i><span class="ml5 d-none d-lg-inline-block">{__("Add New Category")}</span>
        </a>
      </div>
    {elseif $sub_view == "add_category" || $sub_view == "edit_category"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/events/categories" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left"></i><span class="ml5 d-none d-lg-inline-block">{__("Go Back")}</span>
        </a>
      </div>
    {/if}
    <i class="fa fa-calendar mr10"></i>{__("Events")}
    {if $sub_view == "find"} &rsaquo; {__("Find")}{/if}
    {if $sub_view == "edit_event"} &rsaquo; {$data['event_title']}{/if}
    {if $sub_view == "categories"} &rsaquo; {__("Categories")}{/if}
    {if $sub_view == "add_category"} &rsaquo; {__("Categories")} &rsaquo; {__("Add New Category")}{/if}
    {if $sub_view == "edit_category"} &rsaquo; {__("Categories")} &rsaquo; {$data['category_name']}{/if}
  </div>

  {if $sub_view == "" || $sub_view == "find"}

    <div class="card-body">

      <!-- search form -->
      <div class="mb20">
        <form class="d-flex flex-row align-items-center flex-wrap" action="{$system['system_url']}/{$control_panel['url']}/events/find" method="get">
          <div class="form-group mb0">
            <div class="input-group">
              <input type="text" class="form-control" name="query">
              <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-search mr5"></i>{__("Search")}</button>
            </div>
          </div>
        </form>
        <div class="form-text small">
          {__("Search by Event Title")}
        </div>
      </div>
      <!-- search form -->

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Event")}</th>
              <th>{__("Admin")}</th>
              <th>{__("Privacy")}</th>
              <th>{__("Interested")}</th>
              <th>{__("Going")}</th>
              <th>{__("Invited")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>
                    <a href="{$system['system_url']}/events/{$row['event_id']}" target="_blank">{$row['event_id']}</a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/events/{$row['event_id']}">
                      {$row['event_title']}
                    </a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                      <img class="tbl-image" src="{$row['user_picture']}">
                      {if $system['show_usernames_enabled']}{$row['user_name']}{else}{$row['user_firstname']} {$row['user_lastname']}{/if}
                    </a>
                  </td>
                  <td>
                    {if $row['event_privacy'] == "public"}
                      <i class="fa fa-globe fa-fw mr5"></i>{__("Public")}
                    {elseif $row['event_privacy'] == "closed"}
                      <i class="fa fa-unlock-alt fa-fw mr5"></i>{__("Closed")}
                    {elseif $row['event_privacy'] == "secret"}
                      <i class="fa fa-lock fa-fw mr5"></i>{__("Secret")}
                    {/if}
                  </td>
                  <td>{$row['event_interested']}</td>
                  <td>{$row['event_going']}</td>
                  <td>{$row['event_invited']}</td>
                  <td>
                    <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/events/edit_event/{$row['event_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                      <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="event" data-id="{$row['event_id']}">
                      <i class="fa fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              {/foreach}
            {else}
              <tr>
                <td colspan="8" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>
      {$pager}
    </div>

  {elseif $sub_view == "edit_event"}

    <form class="js_ajax-forms" data-url="admin/events.php?do=edit_event&id={$data['event_id']}">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-2 text-center mb20">
            <img class="img-fluid img-thumbnail rounded" src="{$data['event_picture']}">
          </div>
          <div class="col-12 col-md-5 mb20">
            <ul class="list-group">
              <li class="list-group-item">
                <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['event_id']}</span>
                {__("Event ID")}
              </li>
              <li class="list-group-item">
                <span class="float-end badge badge-lg rounded-pill bg-secondary">
                  {if $data['event_privacy'] == "public"}
                    <i class="fa fa-globe fa-fw mr5"></i>{__("Public")}
                  {elseif $data['event_privacy'] == "closed"}
                    <i class="fa fa-unlock-alt fa-fw mr5"></i>{__("Closed")}
                  {elseif $data['event_privacy'] == "secret"}
                    <i class="fa fa-lock fa-fw mr5"></i>{__("Secret")}
                  {/if}
                </span>
                {__("Privacy")}
              </li>
            </ul>
          </div>
          <div class="col-12 col-md-5 mb20">
            <ul class="list-group">
              <li class="list-group-item">
                <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['event_interested']}</span>
                {__("Interested")}
              </li>
              <li class="list-group-item">
                <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['event_going']}</span>
                {__("Going")}
              </li>
              <li class="list-group-item">
                <span class="float-end badge badge-lg rounded-pill bg-secondary">{$data['event_invited']}</span>
                {__("Invited")}
              </li>
            </ul>
          </div>
        </div>

        <!-- tabs nav -->
        <ul class="nav nav-tabs mb20">
          <li class="nav-item">
            <a class="nav-link active" href="#event_settings" data-bs-toggle="tab">
              <i class="fa fa-cog fa-fw mr5"></i><strong>{__("Event Settings")}</strong>
            </a>
          </li>
        </ul>
        <!-- tabs nav -->

        <!-- tabs content -->
        <div class="tab-content">
          <!-- settings tab -->
          <div class="tab-pane active" id="event_settings">
            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Admin")}
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
                {__("Name Your Event")}
              </label>
              <div class="col-md-9">
                <input class="form-control" name="title" value="{$data['event_title']}">
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Location")}
              </label>
              <div class="col-md-9">
                <input class="form-control" name="location" value="{$data['event_location']}">
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Start Date")}
              </label>
              <div class="col-md-9">
                <input type="datetime-local" class="form-control" name="start_date" value="{$data['event_start_date']}">
                <div class="form-text">
                  {__("Your current server datetime is")}: {$date} (UTC)
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("End Date")}
              </label>
              <div class="col-md-9">
                <input type="datetime-local" class="form-control" name="end_date" value="{$data['event_end_date']}">
                <div class="form-text">
                  {__("Your current server datetime is")}: {$date} (UTC)
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Select Privacy")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="privacy">
                  <option {if $data['event_privacy'] == "public"}selected{/if} value="public">{__("Public Event")}</option>
                  <option {if $data['event_privacy'] == "closed"}selected{/if} value="closed">{__("Closed Event")}</option>
                  <option {if $data['event_privacy'] == "secret"}selected{/if} value="secret">{__("Secret Event")}</option>
                </select>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Category")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="category">
                  {foreach $data['categories'] as $category}
                    {include file='__categories.recursive_options.tpl' data_category=$data['event_category']}
                  {/foreach}
                </select>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("About")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="description">{$data['event_description']}</textarea>
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
                <div class="form-text d-none d-sm-block">{__("Enable chat box for this event")}</div>
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
                <div class="form-text d-none d-sm-block">{__("Members can publish posts or only event admin")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="event_publish_enabled">
                  <input type="checkbox" name="event_publish_enabled" id="event_publish_enabled" {if $data['event_publish_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div>
                <div class="form-label h6">{__("Post Approval")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("All posts must be approved by the event admin")}<br>
                  ({__("Note: Disable it will approve any pending posts")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="event_publish_approval_enabled">
                  <input type="checkbox" name="event_publish_approval_enabled" id="event_publish_approval_enabled" {if $data['event_publish_approval_enabled']}checked{/if}>
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
          </div>
          <!-- settings tab -->
        </div>
        <!-- tabs content -->
      </div>
      <div class="card-footer text-end">
        <button type="button" class="btn btn-danger js_admin-deleter" data-handle="event_posts" data-id="{$data['event_id']}" data-delete-message="{__("Are you sure you want to delete all posts?")}">
          <i class="fa fa-trash-alt mr5"></i>{__("Delete Posts")}
        </button>
        <button type="button" class="btn btn-danger js_admin-deleter" data-handle="event" data-id="{$data['event_id']}" data-redirect="{$system['system_url']}/{$control_panel['url']}/events">
          <i class="fa fa-trash-alt mr5"></i>{__("Delete Event")}
        </button>
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

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
                {include file='__categories.recursive_rows.tpl' _url="events" _handle="event_category"}
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

    <form class="js_ajax-forms" data-url="admin/events.php?do=add_category">
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

    <form class="js_ajax-forms" data-url="admin/events.php?do=edit_category&id={$data['category_id']}">
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