<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="funding" class="main-icon mr10" width="24px" height="24px"}
    {__("Create New Funding Request")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="posts/funding.php?do=publish">
  <div class="modal-body">
    <!-- funding title -->
    <div class="form-group">
      <label class="form-label">{__("Title")}</label>
      <input name="title" type="text" class="form-control">
    </div>
    <!-- funding title -->
    <!-- funding amount -->
    <div class="form-group">
      <label class="form-label">{__("Amount You Would Like To Receive")}</label>
      <div class="input-money {$system['system_currency_dir']}">
        <span>{$system['system_currency_symbol']}</span>
        <input type="text" class="form-control" placeholder="0.00" name="amount">
      </div>
    </div>
    <!-- funding amount -->
    <!-- funding description -->
    <div class="form-group">
      <label class="form-label">{__("Description")}</label>
      <textarea name="description" rows="5" dir="auto" class="form-control"></textarea>
    </div>
    <!-- funding description -->
    <!-- cover image -->
    <div class="form-group">
      <label class="form-label">{__("Cover Image")}</label>
      <div class="x-image">
        <div class="x-image-loader">
          <div class="progress x-progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
        <input type="hidden" class="js_x-image-input" name="cover_image" value="">
      </div>
    </div>
    <!-- cover image -->
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">{__("Publish")}</button>
  </div>
</form>