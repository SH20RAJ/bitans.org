<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="funding" class="main-icon mr10" width="24px" height="24px"}
    {__("Edit Funding Request")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="posts/edit.php">
  <div class="modal-body">
    <!-- funding title -->
    <div class="form-group">
      <label class="form-label">{__("Title")}</label>
      <input name="title" type="text" class="form-control" value="{$post['funding']['title']}">
    </div>
    <!-- funding title -->
    <!-- funding amount -->
    <div class="form-group">
      <label class="form-label">{__("Amount You Would Like To Receive")}</label>
      <div class="input-money {$system['system_currency_dir']}">
        <span>{$system['system_currency_symbol']}</span>
        <input type="text" class="form-control" placeholder="0.00" name="amount" value="{$post['funding']['amount']}">
      </div>
    </div>
    <!-- funding amount -->
    <!-- funding description -->
    <div class="form-group">
      <label class="form-label">{__("Description")}</label>
      <textarea name="description" rows="5" dir="auto" class="form-control">{$post['text_plain']}</textarea>
    </div>
    <!-- funding description -->
    <!-- cover image -->
    <div class="form-group">
      <label class="form-label">{__("Cover Image")}</label>
      <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$post['funding']['cover_image']}')">
        <div class="x-image-loader">
          <div class="progress x-progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
        <input type="hidden" class="js_x-image-input" name="cover_image" value="{$post['funding']['cover_image']}">
      </div>
    </div>
    <!-- cover image -->
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <input type="hidden" name="handle" value="funding">
    <input type="hidden" name="id" value="{$post['post_id']}">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
    <button type="submit" class="btn btn-primary">{__("Save")}</button>
  </div>
</form>