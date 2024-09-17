<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="coinpayments" class="main-icon mr15" width="24px" height="24px"}
  {__("CoinPayments Transactions")}
</div>
<div class="card-body">
  <div class="heading-small mb20">
    {__("Transactions History")}
  </div>
  <div class="pl-md-4">
    {if $coinpayments_transactions}
      <div class="table-responsive mt20">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Product")}</th>
              <th>{__("Amount")}</th>
              <th>{__("Created")}</th>
              <th>{__("Updated")}</th>
              <th>{__("Status")}</th>
              <th>{__("Status Message")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $coinpayments_transactions as $transaction}
              <tr>
                <td>{$transaction@iteration}</td>
                <td>{$transaction['product']}</td>
                <td>{print_money($transaction['amount'])}</td>
                <td>
                  <span class="js_moment" data-time="{$transaction['created_at']}">{$transaction['created_at']}</span>
                </td>
                <td>
                  <span class="js_moment" data-time="{$transaction['last_update']}">{$transaction['last_update']}</span>
                </td>
                <td>
                  {if $transaction['status'] == '-1'}
                    <span class="badge rounded-pill badge-lg bg-danger">{__("Error")}</span>
                  {elseif $transaction['status'] == '0'}
                    <span class="badge rounded-pill badge-lg bg-info">{__("Processing")}</span>
                  {elseif $transaction['status'] == '1'}
                    <span class="badge rounded-pill badge-lg bg-warning">{__("Pending")}</span>
                  {elseif $transaction['status'] == '2'}
                    <span class="badge rounded-pill badge-lg bg-success">{__("Complete")}</span>
                  {/if}
                </td>
                <td>
                  {if $transaction['status'] == '-1'}
                    {__("Error while processing your payment")}
                  {else}
                    {$transaction['status_message']}
                  {/if}
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    {else}
      {include file='_no_transactions.tpl'}
    {/if}
  </div>
</div>