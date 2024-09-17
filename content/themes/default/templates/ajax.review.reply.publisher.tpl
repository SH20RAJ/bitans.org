<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="star" class="main-icon mr10" width="24px" height="24px"}
    {__("Reply To Review")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="modules/review.php?do=publish-reply&id={$review['review_id']}" method="POST">
  <div class="modal-body">
    <div class="form-group">
      <label class="form-label">{__("Write Your Reply")}</label>
      <textarea name="reply" rows="5" dir="auto" class="form-control"></textarea>
    </div>

    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">{__("Submit")}</button>
  </div>
</form>