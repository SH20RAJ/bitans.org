<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="offers" class="main-icon mr10" width="24px" height="24px"}{__("Edit Offer")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="posts/edit.php">
  <div class="modal-body">
    <div class="row">
      <!-- discount type -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Type")}</label>
        <select class="form-select" id="js_discount-type" name="discount_type">
          <option value="discount_percent" {if $post['offer']['discount_type'] == "discount_percent"}selected{/if}>{__("Discount Percent")}</option>
          <option value="discount_amount" {if $post['offer']['discount_type'] == "discount_amount"}selected{/if}>{__("Discount Amount")}</option>
          <option value="buy_get_discount" {if $post['offer']['discount_type'] == "buy_get_discount"}selected{/if}>{__("Buy X Get Y Discount")}</option>
          <option value="spend_get_off" {if $post['offer']['discount_type'] == "spend_get_off"}selected{/if}>{__("Spend X Get Y Off")}</option>
          <option value="free_shipping" {if $post['offer']['discount_type'] == "free_shipping"}selected{/if}>{__("Free Shipping")}</option>
        </select>
      </div>
      <!-- discount type -->
      <!-- discount percent -->
      <div id="js_discount-percent" class="form-group col-md-6 {if $post['offer']['discount_type'] != "discount_percent"}x-hidden{/if}">
        <label class="form-label">{__("Discount Percent")}</label>
        <select class="form-select" name="discount_percent">
          {for $i=1 to 99}
            <option value="{$i}" {if $post['offer']['discount_percent'] == $i}selected{/if}>{$i}%</option>
          {/for}
        </select>
      </div>
      <!-- discount percent -->
      <!-- discount amount -->
      <div id="js_discount-amount" class="form-group col-md-6 {if $post['offer']['discount_type'] != "discount_amount"}x-hidden{/if}">
        <label class="form-label">{__("Discount Amount")}</label>
        <div class="input-group">
          {if $system['system_currency_dir'] == "left"}
            <span class="input-group-text">{$system['system_currency_symbol']}</span>
          {/if}
          <input name="discount_amount" type="text" class="form-control" placeholder="0.00" value="{$post['offer']['discount_amount']}">
          {if $system['system_currency_dir'] == "right"}
            <span class="input-group-text">{$system['system_currency_symbol']}</span>
          {/if}
        </div>
      </div>
      <!-- discount amount -->
    </div>
    <!-- buy get discount -->
    <div id="js_buy-get-discount" {if $post['offer']['discount_type'] != "buy_get_discount"}class="x-hidden" {/if}>
      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Buy")}</label>
          <input name="buy_x" type="text" class="form-control" value="{$post['offer']['buy_x']}">
          <div class="form-text">
            {__("Enter numric value (Example: 5)")}
          </div>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label">{__("Get")}</label>
          <input name="get_y" type="text" class="form-control" value="{$post['offer']['get_y']}">
          <div class="form-text">
            {__("Enter numric value (Example: 2)")}
          </div>
        </div>
      </div>
    </div>
    <!-- buy get discount -->
    <!-- spend get off -->
    <div id="js_spend-get-off" {if $post['offer']['discount_type'] != "spend_get_off"}class="x-hidden" {/if}>
      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Spend")}</label>
          <div class="input-group">
            {if $system['system_currency_dir'] == "left"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
            <input name="spend_x" type="text" class="form-control" placeholder="0.00" value="{$post['offer']['spend_x']}">
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
            <input name="amount_y" type="text" class="form-control" placeholder="0.00" value="{$post['offer']['amount_y']}">
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
        <label class="form-label">{__("End Date")}</label>
        <input type="datetime-local" class="form-control" name="end_date" value="{$post['offer']['end_date']}">
      </div>
      <!-- end date -->
      <!-- category -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Category")}</label>
        <select class="form-select" name="category">
          {foreach $offers_categories as $category}
            {include file='__categories.recursive_options.tpl' data_category=$post['offer']['category_id']}
          {/foreach}
        </select>
      </div>
      <!-- category -->
    </div>

    <!-- title -->
    <div class="form-group">
      <label class="form-label">{__("Discounted Items and/or Services")}</label>
      <input name="title" type="text" class="form-control" value="{$post['offer']['title']}">
    </div>
    <!-- title -->
    <!-- original price -->
    <div class="form-group">
      <label class="form-label">{__("Original Price")}</label>
      <input name="price" type="text" class="form-control" value="{$post['offer']['price']}">
    </div>
    <!-- original price -->
    <!-- description -->
    <div class="form-group">
      <label class="form-label">{__("Description")}</label>
      <textarea name="description" rows="5" dir="auto" class="form-control">{$post['text_plain']}</textarea>
    </div>
    <!-- description -->
    <!-- custom fields -->
    {if $custom_fields['basic']}
      {include file='__custom_fields.tpl' _custom_fields=$custom_fields['basic'] _registration=false}
    {/if}
    <!-- custom fields -->
    <!-- thumbnail -->
    {if $post['offer']['thumbnail']}
      <div class="form-group">
        <label class="form-label">{__("Thumbnail")}</label>
        <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$post['offer']['thumbnail']}')">
          <div class="x-image-loader">
            <div class="progress x-progress">
              <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
          <input type="hidden" class="js_x-image-input" name="thumbnail" value="{$post['offer']['thumbnail']}">
        </div>
      </div>
    {/if}
    <!-- thumbnail -->
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <input type="hidden" name="handle" value="offer">
    <input type="hidden" name="id" value="{$post['post_id']}">
    <button type="submit" class="btn btn-primary">{__("Save")}</button>
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
  })
</script>