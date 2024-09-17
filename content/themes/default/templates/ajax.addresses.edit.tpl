<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="map" class="main-icon mr10" width="24px" height="24px"}
    {__("Edit Address")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="users/addresses.php?do=update">
  <div class="modal-body">
    <!-- title -->
    <div class="form-group">
      <label class="form-label">{__("Title")}</label>
      <input name="title" type="text" class="form-control" value="{$address['address_title']}">
    </div>
    <!-- title -->
    <div class="row">
      <!-- country -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Country")}</label>
        <input name="country" type="text" class="form-control" value="{$address['address_country']}">
      </div>
      <!-- country -->
      <!-- city -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("City")}</label>
        <input name="city" type="text" class="form-control" value="{$address['address_city']}">
      </div>
      <!-- city -->
    </div>
    <div class="row">
      <!-- zip code -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Zip Code")}</label>
        <input name="zip_code" type="text" class="form-control" value="{$address['address_zip_code']}">
      </div>
      <!-- zip code -->
      <!-- phone -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Phone")}</label>
        <input name="phone" type="text" class="form-control" value="{$address['address_phone']}">
      </div>
      <!-- phone -->
    </div>
    <!-- address -->
    <div class="form-group">
      <label class="form-label">{__("Address")}</label>
      <textarea name="address" rows="2" dir="auto" class="form-control">{$address['address_details']}</textarea>
    </div>
    <!-- address -->
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <input type="hidden" name="address_id" value="{$address['address_id']}">
    <button type="submit" class="btn btn-primary">{__("Update")}</button>
  </div>
</form>