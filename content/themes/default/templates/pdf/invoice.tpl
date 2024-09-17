<div id="invoice-wrapper-{$order['order_id']}" style="display: none;">
  <div id="invoice-{$order['order_hash']}">
    <div class="container">
      <h1 class="text-center mb30">{__($system['system_title'])}</h1>
      <div class="row">
        <div class="col-12">
          <hr>
          <div class="invoice-title">
            <h2>{__("Invoice")}</h2>
            <h3 class="float-end">{__("Order")} #{$order['order_hash']}</h3>
          </div>
          <hr>
          <div class="row">
            <div class="col-6">
              <address>
                <strong>{__("Seller")}:</strong><br>
                {$order['seller_fullname']}<br>
              </address>
              <address class="mt20">
                <strong>{__("Order Date")}:</strong><br>
                {$order['insert_time']}<br><br>
              </address>
            </div>
            <div class="col-6 text-end">
              <address>
                <strong>{__("Billed & Shipped To")}:</strong><br>
                {$order['buyer_fullname']}<br>
                {$order['address_details']}<br>
                {$order['address_city']}<br>
                {$order['address_country']}<br>
                {$order['address_zip_code']}<br>
                {$order['address_phone']}<br>
              </address>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="order-summary">
            <div class="order-summary-header">
              <h3><strong>{__("Order summary")}</strong></h3>
            </div>
            <div class="order-summary-body">
              <div class="table-responsive">
                <table class="table table-condensed table-invoice">
                  <thead>
                    <tr>
                      <td><strong>{__("Item")}</strong></td>
                      <td class="text-center"><strong>{__("Price")}</strong></td>
                      <td class="text-center"><strong>{__("Quantity")}</strong></td>
                      <td class="text-right"><strong>{__("Totals")}</strong></td>
                    </tr>
                  </thead>
                  <tbody>
                    {foreach $order['items'] as $order_item}
                      <tr>
                        <td>{$order_item['post']['product']['name']}</td>
                        <td class="text-center">
                          {print_money($order_item['post']['product']['price'])}
                        </td>
                        <td class="text-center">{$order_item['quantity']}</td>
                        <td class="text-right">
                          {$order_item['quantity'] * $order_item['post']['product']['price']}
                        </td>
                      </tr>
                    {/foreach}
                    <tr>
                      <td class="thick-line"></td>
                      <td class="thick-line"></td>
                      <td class="thick-line text-center"><strong>{if $order['seller_id'] == $user->_data['user_id']}{__("Subtotal")}{else}{__("Total")}{/if}</strong></td>
                      <td class="thick-line text-right">{print_money(number_format($order['sub_total'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}</td>
                    </tr>
                    {if $order['seller_id'] == $user->_data['user_id']}
                      <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-center"><strong>{__("Commission")}</strong></td>
                        <td class="no-line text-right">- {print_money(number_format($order['total_commission'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}</td>
                      </tr>
                      <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-center"><strong>{__("Total")}</strong></td>
                        <td class="no-line text-right">{print_money(number_format($order['final_price'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}</td>
                      </tr>
                    {/if}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    var invoice = document.querySelector('#invoice-{$order['order_hash']}');
    var opt = {
      margin: 0.5,
      filename: "invoice-{$order['order_hash']}.pdf",
      jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().from(invoice).set(opt).save();
  </script>
</div>