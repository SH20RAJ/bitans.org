<div class="card">
  <div class="card-header ptb30 plr30">
    <div class="row">
      <div class="col-md-3">
        <div><strong>{__("Order")} #:</strong></div>
        {$order['order_hash']}
      </div>

      <div class="col-md-3">
        <div><strong>{__("Order Placed")}:</strong></div>
        {$order['insert_time']}
      </div>

      <div class="col-md-3">
        <div><strong>{__("Status")}:</strong></div>
        {if $order['status'] == "canceled"}
          <span class="badge badge-lg bg-danger">{__($order['status'])|ucfirst}</span>
        {elseif $order['status'] == "delivered"}
          <span class="badge badge-lg bg-success">{__($order['status'])|ucfirst}</span>
        {else}
          <span class="badge badge-lg bg-info">{__($order['status'])|ucfirst}</span>
        {/if}
      </div>

      <div class="col-md-3 text-end">
        {if !$for_admin}
          <!-- update order -->
          {if $sales}
            {if $order['status'] != "delivered" && $order['status'] != "canceled"}
              <button class="btn btn-md btn-outline-primary" data-toggle="modal" data-url="users/orders.php?do=edit&id={$order['order_id']}">
                {__("UPDATE")}
              </button>
            {/if}
          {else}
            {if $order['status'] != "delivered" && $order['status'] != "canceled"}
              <button class="btn btn-md btn-outline-primary" data-toggle="modal" data-url="users/orders.php?do=edit&id={$order['order_id']}">
                {__("UPDATE")}
              </button>
            {/if}
          {/if}
          <!-- update order -->

          <!-- invoice -->
          <button class="btn btn-md btn-outline-success js_shopping-download-invoice" data-id="{$order['order_id']}">
            {__("INVOICE")}
          </button>
          <!-- invoice -->
        {/if}
      </div>
    </div>
  </div>
  <div class="card-body page-content">
    <div class="row">

      <div class="col-md-5 mb30">
        <!-- Tracking Details -->
        <div class="section-title mb20">
          {include file='__svg_icons.tpl' icon="linked_accounts" class="main-icon mr5" width="20px" height="20px"}
          {if $order['is_digital']}
            {__("Download Details")}
          {else}
            {__("Tracking Details")}
          {/if}
        </div>
        <div class="plr20">
          {if $order['is_digital']}
            <div class="mb20">
              <div class="mb10">
                <strong>{__("Download Link")}:</strong>
              </div>
              {if $order['items'][0]['post']['product']['product_file_source']}
                <div>
                  <a class="btn btn-md btn-outline-primary" href="{$system['system_uploads']}/{$order['items'][0]['post']['product']['product_file_source']}" target="_blank">{__("Download")}</a>
                </div>
              {else}
                <div>
                  <a class="btn btn-md btn-outline-primary" href="{$order['items'][0]['post']['product']['product_download_url']}" target="_blank">{__("Download")}</a>
                </div>
              {/if}
            </div>
          {else}
            <div class="mb20">
              <div>
                <strong>{__("Tracking Link")}:</strong>
              </div>
              <div>
                {if $order['tracking_link']}<a href="{$order['tracking_link']}" target="_blank">{$order['tracking_link']}</a>{else}{__("N/A")}{/if}
              </div>
            </div>

            <div class="mb20">
              <div>
                <strong>{__("Tracking Number")}:</strong>
              </div>
              <div>
                {if $order['tracking_number']}{$order['tracking_number']}{else}{__("N/A")}{/if}
              </div>
            </div>
          {/if}
        </div>
        <!-- Tracking Details -->

        <!-- Shipping Addresses -->
        <div class="section-title mt30 mb20">
          {include file='__svg_icons.tpl' icon="map" class="main-icon mr5" width="20px" height="20px"}
          {__("Shipping Addresses")}
        </div>
        <div class="payment-plans">
          <div class="payment-plan full">
            <div class="text-xlg"><strong>{$order['buyer_fullname']}</strong></div>
            <div>{$order['address_details']}</div>
            <div>{$order['address_city']}</div>
            <div>{$order['address_country']}</div>
            <div>{$order['address_zip_code']}</div>
            <div>{$order['address_phone']}</div>
          </div>
        </div>
      </div>
      <!-- Shipping Addresses -->

      <div class="col-md-7">
        <!-- Payments -->
        <div class="section-title mb20">
          {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr5" width="20px" height="20px"}
          {__("Payments")}
        </div>
        <div class="plr20">
          <div class="mb5">
            <span class="text-lg">{if $sales}{__("Subtotal")}{else}{__("Total")}{/if}:</span>
            <span class="float-end">
              <span class="text-lg">
                {print_money(number_format($order['sub_total'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}
              </span>
            </span>
          </div>
          {if $sales}
            <div class="mb5">
              <span class="text-lg">{__("Commission")}:</span>
              <span class="float-end">
                <span class="text-lg">
                  - {print_money(number_format($order['total_commission'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}
                </span>
              </span>
            </div>
            <div class="divider mtb5"></div>
            <div class="mb5">
              <span class="text-lg"><strong>{__("Total")}:</strong></span>
              <span class="float-end">
                <span class="text-lg">
                  <strong>
                    {print_money(number_format($order['final_price'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}
                  </strong>
                </span>
              </span>
            </div>
          {/if}
        </div>
        <!-- Payments -->

        <!-- Order Items -->
        <div class="section-title mt30 mb20">
          {include file='__svg_icons.tpl' icon="products" class="main-icon mr5" width="20px" height="20px"}
          {__("Items")}
        </div>
        <div class="row">
          {foreach $order['items'] as $order_item}
            <div class="col-lg-6">
              <div class="card product active">
                <div class="product-image">
                  <div class="product-price">
                    {if $order_item['post']['product']['price'] > 0}
                      {print_money($order_item['post']['product']['price'])}
                    {else}
                      {__("Free")}
                    {/if}
                  </div>
                  {if $order_item['post']['photos_num'] > 0}
                    <img src="{$system['system_uploads']}/{$order_item['post']['photos'][0]['source']}">
                  {else}
                    <img src="{$system['system_url']}/content/themes/{$system['theme']}/images/blank_product.png">
                  {/if}
                </div>
                <div class="product-info plr15">
                  <div class="product-meta title">
                    <a href="{$system['system_url']}/posts/{$order_item['post']['post_id']}" class="title">{$order_item['post']['product']['name']}</a>
                    {if $order_item['post']['product']['status'] == "new"}
                      <span class="badge bg-info">{__("New")}</span>
                    {else}
                      <span class="badge bg-info">{__("Used")}</span>
                    {/if}
                  </div>
                  <div class="mt20">
                    <strong>{__("Qty:")}</strong>
                    {$order_item['quantity']}
                  </div>
                </div>
              </div>
            </div>
          {/foreach}
        </div>
        <!-- Order Items -->
      </div>
    </div>
  </div>
</div>