<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == ""}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/announcements/add" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New Announcement")}
        </a>
      </div>
    {else}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/announcements" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-bullhorn mr10"></i>{__("Announcements")}
    {if $sub_view == "edit"} &rsaquo; {$data['name']}{/if}
    {if $sub_view == "add"} &rsaquo; {__("Add New Announcement")}{/if}
  </div>

  {if $sub_view == ""}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Name")}</th>
              <th>{__("Type")}</th>
              <th>{__("Start Date")}</th>
              <th>{__("End Date")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['announcement_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['type']}</td>
                <td>{$row['start_date']}</td>
                <td>{$row['end_date']}</td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/announcements/edit/{$row['announcement_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="announcement" data-id="{$row['announcement_id']}">
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

    <form class="js_ajax-forms" data-url="admin/announcements.php?do=edit&id={$data['announcement_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="name" value="{$data['name']}">
            <div class="form-text">
              {__("Announcement name will appear only in the admin panel (mandatory)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title" value="{$data['title']}">
            <div class="form-text">
              {__("Announcement title will appear on the announcement block")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Type")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="type">
              <option {if $data['type'] == "success"}selected{/if} value="success" class="alert-success">{__("Success")}</option>
              <option {if $data['type'] == "warning"}selected{/if} value="warning" class="alert-warning">{__("Warning")}</option>
              <option {if $data['type'] == "danger"}selected{/if} value="danger" class="alert-danger">{__("Danger")}</option>
              <option {if $data['type'] == "info"}selected{/if} value="info" class="alert-info">{__("Info")}</option>
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("HTML")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control js_wysiwyg-advanced" name="code">{$data['code']}</textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Start Date")}
          </label>
          <div class="col-md-9">
            <input type="datetime-local" class="form-control" name="start_date" value="{$data['start_date']}">
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
            <input type="datetime-local" class="form-control" name="end_date" value="{$data['end_date']}">
            <div class="form-text">
              {__("Your current server datetime is")}: {$date} (UTC)
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

  {elseif $sub_view == "add"}

    <form class="js_ajax-forms" data-url="admin/announcements.php?do=add">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="name">
            <div class="form-text">
              {__("Announcement name will appear only in the admin panel")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title">
            <div class="form-text">
              {__("Announcement title will appear on the announcement block")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Type")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="type">
              <option value="success" class="alert-success">{__("Success")}</option>
              <option value="warning" class="alert-warning">{__("Warning")}</option>
              <option value="danger" class="alert-danger">{__("Danger")}</option>
              <option value="info" class="alert-info">{__("Info")}</option>
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("HTML")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control js_wysiwyg-advanced" name="code"></textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Start Date")}
          </label>
          <div class="col-md-9">
            <input type="datetime-local" class="form-control" name="start_date">
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
            <input type="datetime-local" class="form-control" name="end_date">
            <div class="form-text">
              {__("Your current server datetime is")}: {$date} (UTC)
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