<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="24_hours" class="main-icon mr10" width="24px" height="24px"}
    {__("Create New Story")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="publisher-mini">
  <div class="modal-body">

    {if $user->_is_admin}
      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="ads" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Ads Story")}</div>
          <div class="form-text d-none d-sm-block">{__("Share this story as ads so all users see it")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="is_ads">
            <input type="checkbox" name="is_ads" id="is_ads">
            <span class="slider round"></span>
          </label>
        </div>
      </div>
    {/if}

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="form-label">{__("Message")}</label>
          <textarea name="message" rows="5" dir="auto" class="form-control"></textarea>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">{__("Photos")}</label>
      <div class="attachments clearfix" data-type="photos">
        <ul>
          <li class="add">
            <i class="fa fa-camera js_x-uploader" data-handle="publisher-mini" data-multiple="true"></i>
          </li>
        </ul>
      </div>
    </div>

    {if $user->_data['can_upload_videos']}
      <div class="form-group">
        <label class="form-label">{__("Videos")}</label>
        <div class="attachments clearfix" data-type="videos">
          <ul>
            <li class="add">
              <i class="fa fa-video js_x-uploader" data-type="video" data-handle="publisher-mini" data-multiple="true"></i>
            </li>
          </ul>
        </div>
      </div>
    {/if}

    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-primary js_publisher-btn js_publisher-story">{__("Publish")}</button>
  </div>
</form>