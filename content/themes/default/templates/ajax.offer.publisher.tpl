<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="offers" class="main-icon mr10" width="24px" height="24px"}</i>{__("Create New Offer")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms publisher-mini" data-url="posts/offer.php?do=publish">
  <div class="modal-body">
    <div class="row">
      <!-- discount type -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Type")}</label>
        <select class="form-select" id="js_discount-type" name="discount_type">
          <option value="discount_percent">{__("Discount Percent")}</option>
          <option value="discount_amount">{__("Discount Amount")}</option>
          <option value="buy_get_discount">{__("Buy X Get Y Discount")}</option>
          <option value="spend_get_off">{__("Spend X Get Y Off")}</option>
          <option value="free_shipping">{__("Free Shipping")}</option>
        </select>
      </div>
      <!-- discount type -->
      <!-- discount percent -->
      <div id="js_discount-percent" class="form-group col-md-6">
        <label class="form-label">{__("Discount Percent")}</label>
        <select class="form-select" name="discount_percent">
          {for $i=1 to 99}
            <option value="{$i}">{$i}%</option>
          {/for}
        </select>
      </div>
      <!-- discount percent -->
      <!-- discount amount -->
      <div id="js_discount-amount" class="form-group col-md-6 x-hidden">
        <label class="form-label">{__("Discount Amount")}</label>
        <div class="input-group">
          {if $system['system_currency_dir'] == "left"}
            <span class="input-group-text">{$system['system_currency_symbol']}</span>
          {/if}
          <input name="discount_amount" type="text" class="form-control" placeholder="0.00">
          {if $system['system_currency_dir'] == "right"}
            <span class="input-group-text">{$system['system_currency_symbol']}</span>
          {/if}
        </div>
      </div>
      <!-- discount amount -->
    </div>
    <!-- buy get discount -->
    <div id="js_buy-get-discount" class="x-hidden">
      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Buy")}</label>
          <input name="buy_x" type="text" class="form-control">
          <div class="form-text">
            {__("Enter numric value (Example: 5)")}
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label">{__("Get")}</label>
          <input name="get_y" type="text" class="form-control">
          <div class="form-text">
            {__("Enter numric value (Example: 2)")}
          </div>
        </div>
      </div>
    </div>
    <!-- buy get discount -->
    <!-- spend get off -->
    <div id="js_spend-get-off" class="x-hidden">
      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Spend")}</label>
          <div class="input-group">
            {if $system['system_currency_dir'] == "left"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
            <input name="spend_x" type="text" class="form-control" placeholder="0.00">
            {if $system['system_currency_dir'] == "right"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label">{__("Amount Off")}</label>
          <div class="input-group">
            {if $system['system_currency_dir'] == "left"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
            <input name="amount_y" type="text" class="form-control" placeholder="0.00">
            {if $system['system_currency_dir'] == "right"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
          </div>
        </div>
      </div>
    </div>
    <!-- spend get off -->
    <div class="row">
      <!-- end date -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("End Date")} <small class="badge bg-light text-dark">{__("Optional")}</small></label>
        <input type="datetime-local" class="form-control" name="end_date">
      </div>
      <!-- end date -->
      <!-- category -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Category")}</label>
        <select class="form-select" name="category">
          {foreach $offers_categories as $category}
            {include file='__categories.recursive_options.tpl'}
          {/foreach}
        </select>
      </div>
      <!-- category -->
    </div>
    <!-- title -->
    <div class="form-group">
      <label class="form-label">{__("Discounted Items and/or Services")}</label>
      <input name="title" type="text" class="form-control">
    </div>
    <!-- title -->
    <!-- original price -->
    <div class="form-group">
      <label class="form-label">{__("Original Price")} <small class="badge bg-light text-dark">{__("Optional")}</small></label>
      <input name="price" type="text" class="form-control">
    </div>
    <!-- original price -->
    <!-- description -->
    <div class="form-group">
      <label class="form-label">{__("Description")}</label>
      <textarea name="description" rows="5" dir="auto" class="form-control"></textarea>
    </div>
    <!-- description -->
    <!-- custom fields -->
    {if $custom_fields}
      {include file='__custom_fields.tpl' _custom_fields=$custom_fields _registration=true}
    {/if}
    <!-- custom fields -->
    <!-- photos -->
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
    <!-- photos -->
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    {if $share_to == "page"}
      <input type="hidden" name="handle" value="page">
      <input type="hidden" name="id" value="{$share_to_id}">
    {elseif $share_to == "group"}
      <input type="hidden" name="handle" value="group">
      <input type="hidden" name="id" value="{$share_to_id}">
    {elseif $share_to == "event"}
      <input type="hidden" name="handle" value="event">
      <input type="hidden" name="id" value="{$share_to_id}">
    {/if}
    <input type="hidden" class="js_hidden-input-photos" name="photos" value="">
    <button type="submit" class="btn btn-primary js_publisher-btn">{__("Publish")}</button>
  </div>
</form>

<script>
  $(function() {
    /* handle offer input dependencies */
    $('#js_discount-type').on('change', function() {
      switch ($(this).val()) {
        case "discount_percent":
          $("#js_discount-percent").show();
          $("#js_discount-amount").hide();
          $("#js_buy-get-discount").hide();
          $("#js_spend-get-off").hide();
          break;

        case "discount_amount":
          $("#js_discount-percent").hide();
          $("#js_discount-amount").show();
          $("#js_buy-get-discount").hide();
          $("#js_spend-get-off").hide();
          break;

        case "buy_get_discount":
          $("#js_discount-percent").hide();
          $("#js_discount-amount").hide();
          $("#js_buy-get-discount").show();
          $("#js_spend-get-off").hide();
          break;

        case "spend_get_off":
          $("#js_discount-percent").hide();
          $("#js_discount-amount").hide();
          $("#js_buy-get-discount").hide();
          $("#js_spend-get-off").show();
          break;

        case "free_shipping":
          $("#js_discount-percent").hide();
          $("#js_discount-amount").hide();
          $("#js_buy-get-discount").hide();
          $("#js_spend-get-off").hide();
          break;
      }
    });
  });
</script>