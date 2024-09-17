<div class="card">
  <div class="card-header with-icon">
    <i class="fa fa-paper-plane mr10"></i>{__("Newsletter")}
  </div>

  <!-- Newsletter -->
  <form class="js_ajax-forms" data-url="admin/newsletter.php">
    <div class="card-body">
      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="account_activation" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Test Message")}</div>
          <div class="form-text d-none d-sm-block">{__("The message will sent to Website Email only")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="is_test">
            <input type="checkbox" name="is_test" id="is_test">
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Send to")}
        </label>
        <div class="col-sm-9">
          <select class="form-select" name="to">
            <option value="all_users">{__("All Users")} ({$insights['users_all']} {__("user")})</option>
            <option value="users_activated">{__("Users who activated their account")} ({$insights['users_activated']} {__("user")})</option>
            <option value="users_not_activated">{__("Users who did not activated their account")} ({$insights['users_not_activated']} {__("user")})</option>
            <option value="users_not_logged_week">{__("Users who did not login from 1 week")} ({$insights['users_not_logged_week']} {__("user")})</option>
            <option value="users_not_logged_month">{__("Users who did not login from 1 month")} ({$insights['users_not_logged_month']} {__("user")})</option>
            <option value="users_not_logged_3_months">{__("Users who did not login from 3 months")} ({$insights['users_not_logged_3_months']} {__("user")})</option>
            <option value="users_not_logged_6_months">{__("Users who did not login from 6 months")} ({$insights['users_not_logged_6_months']} {__("user")})</option>
            <option value="users_not_logged_9_months">{__("Users who did not login from 9 months")} ({$insights['users_not_logged_9_months']} {__("user")})</option>
            <option value="users_not_logged_year">{__("Users who did not login from 1 year")} ({$insights['users_not_logged_year']} {__("user")})</option>
          </select>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Subject")}
        </label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="subject">
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Message")}
        </label>
        <div class="col-sm-9">
          <textarea class="form-control js_wysiwyg-advanced" rows="10" name="message"></textarea>
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
      <button type="submit" class="btn btn-danger">
        <i class="fa fa-paper-plane mr10"></i>{__("Send")}
      </button>
    </div>
  </form>
  <!-- Newsletter -->

</div>