<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == ""}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/users_groups/add" class="btn btn-md btn-primary">
          <i class="fa fa-users mr5"></i>{__("Add New Group")}
        </a>
      </div>
    {elseif $sub_view == "add" || $sub_view == "edit"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/users_groups" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-users mr10"></i>{__("Users Groups")}
    {if $sub_view == "edit"} &rsaquo; {$data['user_group_title']}{/if}
    {if $sub_view == "add"} &rsaquo; {__("Add New Group")}{/if}
  </div>

  {if $sub_view == ""}

    <div class="card-body">
      <!-- System Groups (Admins/Mods/Users) -->
      <h6 class="card-title mb20">{__("System Groups")}</h6>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Title")}</th>
              <th>{__("Permissions")}</th>
              <th>{__("Users Count")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            <!-- Admins -->
            <tr>
              <td>1</td>
              <td>{__("Admins")}</td>
              <td>{__("Admins Permissions")}</td>
              <td>{$counters['admins_count']}</td>
              <td>
                <a data-bs-toggle="tooltip" title='{__("List Accounts")}' href="{$system['system_url']}/{$control_panel['url']}/users/admins" class="btn btn-sm btn-icon btn-rounded btn-info">
                  <i class="fa fa-users"></i>
                </a>
              </td>
            </tr>
            <!-- Admins -->
            <!-- Moderators -->
            <tr>
              <td>2</td>
              <td>{__("Moderators")}</td>
              <td>{__("Moderators Permissions")}</td>
              <td>{$counters['moderators_count']}</td>
              <td>
                <a data-bs-toggle="tooltip" title='{__("List Accounts")}' href="{$system['system_url']}/{$control_panel['url']}/users/moderators" class="btn btn-sm btn-icon btn-rounded btn-info">
                  <i class="fa fa-users"></i>
                </a>
              </td>
            </tr>
            <!-- Moderators -->
            <!-- Users -->
            <tr>
              <td>3</td>
              <td>{__("Users")}</td>
              <td>
                <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/users">
                  {__("Users Permissions")}
                </a>
              </td>
              <td>{$counters['users_count']}</td>
              <td>
                <a data-bs-toggle="tooltip" title='{__("List Accounts")}' href="{$system['system_url']}/{$control_panel['url']}/users?ncug=true" class="btn btn-sm btn-icon btn-rounded btn-info">
                  <i class="fa fa-users"></i>
                </a>
              </td>
            </tr>
            <!-- Users -->
          </tbody>
        </table>
      </div>
      <!-- System Groups (Admins/Mods/Users) -->

      <div class="divider"></div>

      <!-- Custom Groups -->
      <h6 class="card-title mb20">{__("Custom Groups")}</h6>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Title")}</th>
              <th>{__("Permissions")}</th>
              <th>{__("Users Count")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['user_group_id']}</td>
                <td>
                  {$row['user_group_title']}
                </td>
                <td>
                  <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/{$row['permissions_group_id']}">
                    {$row['permissions_group_title']}
                  </a>
                </td>
                <td>{$row['users_count']}</td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("List Accounts")}' href="{$system['system_url']}/{$control_panel['url']}/users?cug={$row['user_group_id']}" class="btn btn-sm btn-icon btn-rounded btn-info">
                    <i class="fa fa-users"></i>
                  </a>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/users_groups/edit/{$row['user_group_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="user_group" data-id="{$row['user_group_id']}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
      <!-- Custom Groups -->
    </div>

  {elseif $sub_view == "add"}

    <form class="js_ajax-forms" data-url="admin/users_groups.php?do=add">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Group Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Group Permissions")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="permissions_group">
              <option value="none">{__("Select permissions group")}</option>
              {foreach $permissions_groups as $group}
                <option value="{$group['permissions_group_id']}">{$group['permissions_group_title']}</option>
              {/foreach}
            </select>
            <div class="form-text">
              {__("You can manage permissions from")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups">{__("Permissions Groups")}</a>
            </div>
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

  {elseif $sub_view == "edit"}

    <form class="js_ajax-forms" data-url="admin/users_groups.php?do=edit&id={$data['user_group_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Group Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title" value="{$data['user_group_title']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Group Permissions")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="permissions_group">
              <option value="none">{__("Select permissions group")}</option>
              {foreach $data['permissions_groups'] as $group}
                <option {if $data['permissions_group_id'] == $group['permissions_group_id']}selected{/if} value="{$group['permissions_group_id']}">{$group['permissions_group_title']}</option>
              {/foreach}
            </select>
            <div class="form-text">
              {__("You can manage permissions from")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups">{__("Permissions Groups")}</a>
            </div>
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