<div class="modal-header">
  <h6 class="modal-title">{__("Edit Album")}</h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="albums/action.php">
  <div class="modal-body">
    <div class="form-group">
      <label class="form-label">{__("Title")}</label>
      <input type="hidden" name="do" value="edit_album">
      <input type="hidden" name="id" value="{$album['album_id']}">
      <input name="title" type="text" value="{$album['title']}" class="form-control" required autofocus>
    </div>
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
    <button type="submit" class="btn btn-primary">{__("Save")}</button>
  </div>
</form>