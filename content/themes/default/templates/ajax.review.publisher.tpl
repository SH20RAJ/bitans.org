<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="star" class="main-icon mr10" width="24px" height="24px"}
    {__("Review")} {$node_title}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="publisher-mini" data-id="{$node_id}" data-type="{$node_type}">
  <div class="modal-body">

    <div class="form-group">
      <label class="form-label">{__("Rating")}</label>
      <div class="star-rating js_star-rating">
        <input type="radio" id="star5" name="rating" value="5" />
        <label for="star5"><i class="fa fa-star"></i></label>
        <input type="radio" id="star4" name="rating" value="4" />
        <label for="star4"><i class="fa fa-star"></i></label>
        <input type="radio" id="star3" name="rating" value="3" />
        <label for="star3"><i class="fa fa-star"></i></label>
        <input type="radio" id="star2" name="rating" value="2" />
        <label for="star2"><i class="fa fa-star"></i></label>
        <input type="radio" id="star1" name="rating" value="1" />
        <label for="star1"><i class="fa fa-star"></i></label>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">{__("Write Your Review")}</label>
      <textarea name="review" rows="5" dir="auto" class="form-control"></textarea>
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

    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-primary js_publisher-btn js_publisher-review">{__("Submit")}</button>
  </div>
</form>