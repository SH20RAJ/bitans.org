<div class="modal-header">
  <h6 class="modal-title">
    {__("Update Order")} #{$order['order_hash']}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="users/orders.php?do=update">
  <div class="modal-body">
    <!-- status -->
    <div class="form-group">
      <label class="form-label">{__("Status")}</label>
      <select class="form-select" name="status">
        {if $order['seller_id'] == $user->_data['user_id']}
          <option value="placed" {if $order['status'] == "placed"}selected{/if}>{__("placed")|ucfirst}</option>
          <option value="canceled" {if $order['status'] == "canceled"}selected{/if}>{__("canceled")|ucfirst}</option>
          <option value="accepted" {if $order['status'] == "accepted"}selected{/if}>{__("accepted")|ucfirst}</option>
          <option value="packed" {if $order['status'] == "packed"}selected{/if}>{__("packed")|ucfirst}</option>
          <option value="shipped" {if $order['status'] == "shipped"}selected{/if}>{__("shipped")|ucfirst}</option>
        {else}
          <option value="delivered" {if $order['status'] == "delivered"}selected{/if}>{__("delivered")|ucfirst}</option>
        {/if}
      </select>
    </div>
    <!-- status -->
    {if $order['seller_id'] == $user->_data['user_id']}
      <!-- tracking link -->
      <div class="form-group">
        <label class="form-label">{__("Tracking Link")}</label>
        <input name="tracking_link" type="text" class="form-control" value="{$order['tracking_link']}">
      </div>
      <!-- tracking link -->
      <!-- tracking number -->
      <div class="form-group">
        <label class="form-label">{__("Tracking Number")}</label>
        <input name="tracking_number" type="text" class="form-control" value="{$order['tracking_number']}">
      </div>
      <!-- tracking number -->
    {/if}
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <input type="hidden" name="order_id" value="{$order['order_id']}">
    <button type="submit" class="btn btn-primary">{__("Update")}</button>
  </div>
</form>