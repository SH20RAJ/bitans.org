<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == ""}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/themes/add" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New Theme")}
        </a>
      </div>
    {elseif $sub_view == "add" || $sub_view == "edit"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/themes" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-desktop mr10"></i>{__("Themes")}
    {if $sub_view == "edit"} &rsaquo; {$data['name']}{/if}
    {if $sub_view == "add"} &rsaquo; {__("Add New Theme")}{/if}
  </div>

  {if $sub_view == ""}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Thumbnail")}</th>
              <th>{__("Name")}</th>
              <th>{__("Default")}</th>
              <th>{__("Selectable")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['theme_id']}</td>
                <td>
                  <img width="210" src="{$system['system_url']}/content/themes/{$row['name']}/thumbnail.png">
                </td>
                <td>{$row['name']}</td>
                <td>
                  {if $row['default']}
                    <span class="badge rounded-pill badge-lg bg-success">{__("Yes")}</span>
                  {else}
                    <span class="badge rounded-pill badge-lg bg-danger">{__("No")}</span>
                  {/if}
                </td>
                <td>
                  {if $row['enabled']}
                    <span class="badge rounded-pill badge-lg bg-success">{__("Yes")}</span>
                  {else}
                    <span class="badge rounded-pill badge-lg bg-danger">{__("No")}</span>
                  {/if}
                </td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/themes/edit/{$row['theme_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="theme" data-id="{$row['theme_id']}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>

      <div class="divider"></div>

      <div class="mt-4">
        <label class="d-block form-label">
          {__("Third-party Theme")}
        </label>
        <div class="d-flex py-3 px-2 align-items-start justify-content-between flex-wrap">
          <div class="d-inline-flex align-items-start">
            <a href="https://bit.ly/SngineElengineTheme" target="_blank">
              <img src="{$system['system_url']}/content/themes/{$system['theme']}/images/elengine.png">
            </a>
            <div class="ml15">
              <h5><a href="https://bit.ly/SngineElengineTheme" target="_blank">Elengine - The Ultimate Sngine Theme</a></h5>
              <ul>
                <li>{__("Elegant and Modern Design")}</li>
                <li>{__("Regular and Free Updates")}</li>
                <li>{__("Lifetime support")}</li>
              </ul>
            </div>
          </div>
          <a href="https://bit.ly/SngineElengineTheme" class="btn btn-md btn-primary" target="_blank">
            {__("Get Theme")}
          </a>
        </div>
      </div>
    </div>

  {elseif $sub_view == "edit"}

    <form class="js_ajax-forms" data-url="admin/themes.php?do=edit&id={$data['theme_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Default")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="default">
              <input type="checkbox" name="default" id="default" {if $data['default']}checked{/if}>
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Make it the default theme of the site")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Selectable")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="enabled">
              <input type="checkbox" name="enabled" id="enabled" {if $data['enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Make it the selectable so users can change the theme")}.<br>
              {__("(You must have 2+ selectable themes)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="name" value="{$data['name']}">
            <div class="form-text">
              {__("Theme name should not contain spaces or special characters")}.<br>
              {__("(Valid name examples: mytheme, material, custom_theme)")}
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

    <form class="js_ajax-forms" data-url="admin/themes.php?do=add">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Default")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="default">
              <input type="checkbox" name="default" id="default">
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Make it the default theme of the site")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Selectable")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="enabled">
              <input type="checkbox" name="enabled" id="enabled">
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Make it the selectable so users can change the theme")}.<br>
              {__("(You must have 2+ selectable themes)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="name">
            <div class="form-text">
              {__("Theme name should not contain spaces or special characters")}.<br>
              {__("(Valid name examples: mytheme, material, custom_theme)")}
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