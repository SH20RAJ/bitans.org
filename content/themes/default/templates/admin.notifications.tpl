<div class="card">
  <div class="card-header with-icon">
    <i class="fa fa-bell mr10"></i>{__("Mass Notifications")}
  </div>

  <!-- Mass Notifications -->
  <form class="js_ajax-forms" data-url="admin/notifications.php">
    <div class="card-body">
      <div class="alert alert-info">
        <div class="text">
          <strong>{__("Mass Notifications")}</strong><br>
          {__("Enable you to send notifications to all site users")}.<br>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("URL")}
        </label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="url">
          <div class="form-text">
            {__("Notification link used when user clicks on the notification")}
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Message")}
        </label>
        <div class="col-sm-9">
          <textarea class="form-control" rows="3" name="message"></textarea>
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
  <!-- Mass Notifications -->

</div>