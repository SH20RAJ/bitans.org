<div class="payment-plans">
  {foreach $addresses as $address}
    <div class="payment-plan">
      <div class="text-xxlg">{$address['address_title']}</div>
      <div>{$address['address_details']}</div>
      <div>{$address['address_city']}</div>
      <div>{$address['address_country']}</div>
      <div>{$address['address_zip_code']}</div>
      <div>{$address['address_phone']}</div>
      <div class="mt10 row g-1">
        <div class="col-12 {if $_small}col-md-8{else}col-md-9{/if} mb5">
          <div class="d-grid">
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-url="users/addresses.php?do=edit&id={$address['address_id']}" class="text-link ml10">
              {__("Edit")}
            </button>
          </div>
        </div>
        <div class="col-12 {if $_small}col-md-4{else}col-md-3{/if}">
          <div class="d-grid">
            <button type="button" class="btn btn-sm btn-light js_address-deleter" data-id="{$address['address_id']}">
              {include file='__svg_icons.tpl' icon="delete" class="danger-icon" width="18px" height="18px"}
            </button>
          </div>
        </div>
      </div>
    </div>
  {/foreach}
  <div data-toggle="modal" data-url="users/addresses.php?do=add" class="payment-plan new address">
    <i class="fa fa-plus mr5"></i>
    {__("Add New")}
  </div>
</div>