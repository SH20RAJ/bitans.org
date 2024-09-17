<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == ""}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/blacklist/add" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New")}
        </a>
      </div>
    {elseif $sub_view == "add"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/blacklist" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-minus-circle mr5"></i>{__("Blacklist")}
    {if $sub_view == "add"} &rsaquo; {__("Add New")}{/if}
  </div>

  {if $sub_view == ""}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Type")}</th>
              <th>{__("Value")}</th>
              <th>{__("Added")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['node_id']}</td>
                <td>
                  {if $row['node_type'] == "ip"}
                    <span class="badge badge-lg bg-danger">IP</span>
                  {elseif $row['node_type'] == "email"}
                    <span class="badge badge-lg bg-primary">{$row['node_type']|capitalize}</span>
                  {elseif $row['node_type'] == "username"}
                    <span class="badge badge-lg bg-info">{$row['node_type']|capitalize}</span>
                  {/if}
                </td>
                <td><span class="badge badge-lg bg-warning">{$row['node_value']}</span></td>
                <td><span class="js_moment" data-time="{$row['created_time']}">{$row['created_time']}</span></td>
                <td>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="blacklist_node" data-id="{$row['node_id']}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "add"}

    <form class="js_ajax-forms" data-url="admin/blacklist.php?do=add">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Type")}
          </label>
          <div class="col-md-9">
            <div class="form-check form-check-inline">
              <input type="radio" name="node_type" id="ip" value="ip" class="form-check-input" checked>
              <label class="form-check-label" for="ip">{__("IP")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="node_type" id="email" value="email" class="form-check-input">
              <label class="form-check-label" for="email">{__("Email Provider")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="node_type" id="username" value="username" class="form-check-input">
              <label class="form-check-label" for="username">{__("Username")}</label>
            </div>
            <div class="form-text">
              {__("Select what you want to add to the blackist")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Value")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="node_value">
            <div class="form-text">
              {__("IP (example: 192.168.687.123) | Email Povider (example: outlook.com or gmail.com) | Username (example: admin or superadmin)")}<br>
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