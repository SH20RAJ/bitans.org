<div class="card">
  <div class="card-header with-icon">
    <i class="fa fa-dollar-sign mr10"></i>{__("Tips")}
  </div>

  <form class="js_ajax-forms" data-url="admin/settings.php?edit=tips">
    <div class="card-body">
      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="tips" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Tips")}</div>
          <div class="form-text d-none d-sm-block">
            {__("Turn the tips On and Off")}<br>
            {__("Make sure you have enabled")} <a href="{$system['system_url']}/{$control_panel['url']}/wallet">{__("Wallet System")}</a>
          </div>
        </div>
        <div class="text-end">
          <label class="switch" for="tips_enabled">
            <input type="checkbox" name="tips_enabled" id="tips_enabled" {if $system['tips_enabled']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Minimum Tip")} ({$system['system_currency']})
        </label>
        <div class="col-md-9">
          <input type="text" class="form-control" name="tips_min_amount" value="{$system['tips_min_amount']}">
          <div class="form-text">
            {__("The minimum amount of money so user can tip")}
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Maximum Tip")} ({$system['system_currency']})
        </label>
        <div class="col-md-9">
          <input type="text" class="form-control" name="tips_max_amount" value="{$system['tips_max_amount']}">
          <div class="form-text">
            {__("The maximum amount of money so user can tip")}
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
</div>