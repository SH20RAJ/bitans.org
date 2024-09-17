<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == "packages"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/pro/add" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New Package")}
        </a>
      </div>
    {elseif $sub_view == "add" || $sub_view == "edit"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/pro/packages" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-cubes mr5"></i>{__("Pro System")}
    {if $sub_view == "packages"} &rsaquo; {__("Packages")}{/if}
    {if $sub_view == "edit"} &rsaquo; {$data['name']}{/if}
    {if $sub_view == "add"} &rsaquo; {__("Add New Package")}{/if}
    {if $sub_view == "subscribers"} &rsaquo; {__("Subscribers")}{/if}
  </div>

  {if $sub_view == ""}

    <form class="js_ajax-forms" data-url="admin/pro.php?do=settings">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="membership" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Pro Packages Enabled")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Enable pro packages to be used as upgrading plans or for subscriptions")}<br>
              {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/settings/payments">{__("Payments Settings")}</a>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="packages_enabled">
              <input type="checkbox" name="packages_enabled" id="packages_enabled" {if $system['packages_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="wallet" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Users Can Buy Packages From Wallet Balance")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Enable users to buy packages from their wallet balance")}<br>
              {__("Make sure you have enabled")} <a href="{$system['system_url']}/{$control_panel['url']}/wallet">{__("Wallet System")}</a>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="packages_wallet_payment_enabled">
              <input type="checkbox" name="packages_wallet_payment_enabled" id="packages_wallet_payment_enabled" {if $system['packages_wallet_payment_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="noads" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Ads FREE")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Disable any ads for all PRO user")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="packages_ads_free_enabled">
              <input type="checkbox" name="packages_ads_free_enabled" id="packages_ads_free_enabled" {if $system['packages_ads_free_enabled']}checked{/if}>
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
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {elseif $sub_view == "packages"}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Name")}</th>
              <th>{__("Permissions")}</th>
              <th>{__("Price")}</th>
              <th>{__("Period")}</th>
              <th>{__("Order")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['package_id']}</td>
                <td>
                  <a target="_blank" href="{$system['system_url']}/{$control_panel['url']}/pro/edit/{$row['package_id']}">
                    <img class="tbl-image" src="{$row['icon']}">
                    {$row['name']}
                  </a>
                </td>
                <td>
                  {if $row['package_permissions_group_id'] == 0}
                    {if $row['verification_badge_enabled']}
                      <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/verified">
                        {__("Verified Permissions")}
                      </a>
                    {else}
                      <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/users">
                        {__("Users Permissions")}
                      </a>
                    {/if}
                  {else}
                    <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/{$row['package_permissions_group_id']}">
                      {$row['permissions_group_title']}
                    </a>
                  {/if}
                </td>
                <td>{print_money($row['price'])}</td>
                <td>
                  {if $row['period'] == 'life'}
                    {__("Life Time")}
                  {else}
                    {$row['period_num']} {$row['period']|ucfirst}
                  {/if}
                </td>
                <td>{$row['package_order']}</td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/pro/edit/{$row['package_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="package" data-id="{$row['package_id']}">
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

    <form class="js_ajax-forms" data-url="admin/pro.php?do=edit&id={$data['package_id']}">
      <div class="card-body">

        <div class="alert alert-info">
          <div class="icon">
            <i class="fa fa-info-circle fa-2x"></i>
          </div>
          <div class="text pt5">
            {__("If package comes with verified badge and package permissions set to be")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/users">{__("Users Permissions")}</a> {__("so the")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/verified">{__("Verified Permissions")}</a> {__("will be used instead")}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="name" value="{$data['name']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Price")} ({$system['system_currency']})
          </label>
          <div class="col-md-9">
            <input class="form-control" name="price" value="{$data['price']}">
            <div class="form-text">
              {__("You can set the price as 0 and it will be trial package")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Paid Every")}
          </label>
          <div class="col-md-9">
            <div class="row">
              <div class="col-sm-8">
                <input class="form-control" name="period_num" value="{$data['period_num']}">
              </div>
              <div class="col-sm-4">
                <select class="form-select" name="period">
                  <option {if $data['period'] == "day"}selected{/if} value="day">{__("Day")}</option>
                  <option {if $data['period'] == "week"}selected{/if} value="week">{__("Week")}</option>
                  <option {if $data['period'] == "month"}selected{/if} value="month">{__("Month")}</option>
                  <option {if $data['period'] == "year"}selected{/if} value="year">{__("Year")}</option>
                  <option {if $data['period'] == "life"}selected{/if} value="life">{__("Life Time")}</option>
                </select>
              </div>
            </div>
            <div class="form-text">
              {__("For example 15 days, 2 Months, 1 Year")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Color")}
          </label>
          <div class="col-md-9">
            <div class="input-group js_colorpicker">
              <input type="text" class="form-control form-control-color" name="color" value="{$data['color']}" />
              <input type="color" class="form-control form-control-color" value="{$data['color']}" />
            </div>
            <div class="form-text">
              {__("The theme color for this package")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Icon")}
          </label>
          <div class="col-md-9">
            {if $data['icon'] == ''}
              <div class="x-image">
                <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

                </button>
                <div class="x-image-loader">
                  <div class="progress x-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                <input type="hidden" class="js_x-image-input" name="icon" value="">
              </div>
            {else}
              <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$data['icon']}')">
                <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'>

                </button>
                <div class="x-image-loader">
                  <div class="progress x-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                <input type="hidden" class="js_x-image-input" name="icon" value="{$data['icon']}">
              </div>
            {/if}
            <div class="form-text">
              {__("The perfect size for icon should be (wdith: 60px & height: 60px)")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Package Permissions")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="permissions_group">
              <option value="1" {if $data['package_permissions_group_id'] == '1'}selected{/if}>
                {__("Users Permissions")}
              </option>
              <option value="2" {if $data['package_permissions_group_id'] == '2'}selected{/if}>
                {__("Verified Permissions")}
              </option>
              {foreach $data['permissions_groups'] as $group}
                <option {if $data['package_permissions_group_id'] == $group['permissions_group_id']}selected{/if} value="{$group['permissions_group_id']}">{$group['permissions_group_title']}</option>
              {/foreach}
            </select>
            <div class="form-text">
              {__("You can manage permissions from")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups">{__("Permissions Groups")}</a>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Videos Categories")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="allowed_videos_categories" value="{$data['allowed_videos_categories']}">
            <div class="form-text">
              {__("How many videos categories allowed for this package (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Blogs Categories")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="allowed_blogs_categories" value="{$data['allowed_blogs_categories']}">
            <div class="form-text">
              {__("How many blogs categories allowed for this package (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Market Products")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="allowed_products" value="{$data['allowed_products']}">
            <div class="form-text">
              {__("How many market products allowed to sell for this package (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Verification Badge Enabled")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="verification_badge_enabled">
              <input type="checkbox" name="verification_badge_enabled" id="verification_badge_enabled" {if $data['verification_badge_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Enable verification badge with this package")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Boost Posts Enabled")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="boost_posts_enabled">
              <input type="checkbox" name="boost_posts_enabled" id="boost_posts_enabled" {if $data['boost_posts_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Enable boost posts feature")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Posts Boosts")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="boost_posts" value="{$data['boost_posts']}">
            <div class="form-text">
              {__("Max posts boosts allowed")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Boost Pages Enabled")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="boost_pages_enabled">
              <input type="checkbox" name="boost_pages_enabled" id="boost_pages_enabled" {if $data['boost_pages_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Enable boost pages feature")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Pages Boosts")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="boost_pages" value="{$data['boost_pages']}">
            <div class="form-text">
              {__("Max pages boosts allowed")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Custom Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="custom_description" rows="5">{$data['custom_description']}</textarea>
            <div class="form-text">
              {__("Add more text to show it to your users")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="package_order" value="{$data['package_order']}">
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

    <form class="js_ajax-forms" data-url="admin/pro.php?do=add">
      <div class="card-body">

        <div class="alert alert-info">
          <div class="icon">
            <i class="fa fa-info-circle fa-2x"></i>
          </div>
          <div class="text pt5">
            {__("If package comes with verified badge and package permissions set to be")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/users">{__("Users Permissions")}</a> {__("so the")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups/edit/verified">{__("Verified Permissions")}</a> {__("will be used instead")}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="name">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Price")} ({$system['system_currency']})
          </label>
          <div class="col-md-9">
            <input class="form-control" name="price">
            <div class="form-text">
              {__("You can set the price as 0 and it will be trial package")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Paid Every")}
          </label>
          <div class="col-md-9">
            <div class="row">
              <div class="col-sm-8">
                <input class="form-control" name="period_num">
              </div>
              <div class="col-sm-4">
                <select class="form-select" name="period">
                  <option value="day">{__("Day")}</option>
                  <option value="week">{__("Week")}</option>
                  <option value="month">{__("Month")}</option>
                  <option value="year">{__("Year")}</option>
                  <option value="life">{__("Life Time")}</option>
                </select>
              </div>
            </div>
            <div class="form-text">
              {__("For example 15 days, 2 Months, 1 Year")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Color")}
          </label>
          <div class="col-md-9">
            <div class="input-group js_colorpicker">
              <input type="color" class="form-control form-control-color" name="color" />
              <input type="color" class="form-control form-control-color" />
            </div>
            <div class="form-text">
              {__("The theme color for this package")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Icon")}
          </label>
          <div class="col-md-9">
            <div class="x-image">
              <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

              </button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="icon" value="">
            </div>
            <div class="form-text">
              {__("The perfect size for icon should be (wdith: 60px & height: 60px)")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Package Permissions")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="permissions_group">
              <option value="1">
                {__("Users Permissions")}
              </option>
              <option value="2">
                {__("Verified Permissions")}
              </option>
              {foreach $permissions_groups as $group}
                <option value="{$group['permissions_group_id']}">{$group['permissions_group_title']}</option>
              {/foreach}
            </select>
            <div class="form-text">
              {__("You can manage permissions from")} <a href="{$system['system_url']}/{$control_panel['url']}/permissions_groups">{__("Permissions Groups")}</a><br>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Videos Categories")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="allowed_videos_categories">
            <div class="form-text">
              {__("How many videos categories allowed for this package (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Blogs Categories")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="allowed_blogs_categories">
            <div class="form-text">
              {__("How many blogs categories allowed for this package (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Market Products")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="allowed_products">
            <div class="form-text">
              {__("How many market products allowed to sell for this package (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Verification Badge Enabled")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="verification_badge_enabled">
              <input type="checkbox" name="verification_badge_enabled" id="verification_badge_enabled">
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Enable verification badge with this package")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Boost Posts Enabled")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="boost_posts_enabled">
              <input type="checkbox" name="boost_posts_enabled" id="boost_posts_enabled">
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Enable boost posts feature")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Posts Boosts")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="boost_posts">
            <div class="form-text">
              {__("Max posts boosts allowed")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Boost Pages Enabled")}
          </label>
          <div class="col-md-9">
            <label class="switch" for="boost_pages_enabled">
              <input type="checkbox" name="boost_pages_enabled" id="boost_pages_enabled">
              <span class="slider round"></span>
            </label>
            <div class="form-text">
              {__("Enable boost pages feature")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Pages Boosts")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="boost_pages">
            <div class="form-text">
              {__("Max pages boosts allowed")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Custom Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="custom_description" rows="5"></textarea>
            <div class="form-text">
              {__("Add more text to show it to your users")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="package_order">
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

  {elseif $sub_view == "subscribers"}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("User")}</th>
              <th>{__("Package")}</th>
              <th>{__("Subscription")}</th>
              <th>{__("Expiration")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td><a href="{$system['system_url']}/{$row['user_name']}" target="_blank">{$row['user_id']}</a></td>
                <td>
                  <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                    <img class="tbl-image" src="{$row['user_picture']}">
                    {if $system['show_usernames_enabled']}{$row['user_name']}{else}{$row['user_firstname']} {$row['user_lastname']}{/if}
                  </a>
                </td>
                <td>
                  <a target="_blank" href="{$system['system_url']}/{$control_panel['url']}/pro/edit/{$row['package_id']}">
                    <img class="tbl-image" src="{$row['icon']}">
                    {$row['name']}
                  </a>
                </td>
                <td>{$row['user_subscription_date']|date_format:"%e %B %Y"}</td>
                <td>
                  {if $row['period'] == "life"}
                    {__("Life Time")}
                  {else}
                    {$row['subscription_end']|date_format:"%e %B %Y"} ({if $row['subscription_timeleft'] > 0}{__("Remaining")} {$row['subscription_timeleft']} {__("Days")}{else}{__("Expired")}{/if})
                  {/if}
                </td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/users/edit/{$row['user_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {/if}
</div>