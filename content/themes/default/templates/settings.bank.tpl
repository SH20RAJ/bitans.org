<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="bank" class="main-icon mr15" width="24px" height="24px"}
  {__("Bank Transfers")}
</div>
<div class="card-body">
  <div class="heading-small mb20">
    {__("Transactions History")}
  </div>
  <div class="pl-md-4">
    {if $transfers}
      <div class="table-responsive mt20">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Type")}</th>
              <th>{__("Time")}</th>
              <th>{__("Status")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $transfers as $transfer}
              <tr>
                <td>{$transfer@iteration}</td>
                <td>
                  {if $transfer['handle'] == "packages"}
                    {__($transfer['package_name'])} {__("Package")} = <strong>{print_money($transfer['package_price'])}</strong>
                  {elseif $transfer['handle'] == "wallet"}
                    {__("Add Wallet Balance")} = <strong>{print_money($transfer['price'])}</strong>
                  {elseif $transfer['handle'] == "donate"}
                    {__("Funding Donation")} = <strong>{print_money($transfer['price'])}</strong>
                  {elseif $transfer['handle'] == "subscribe"}
                    {__("Subscribe")} = <strong>{print_money($transfer['price'])}</strong>
                  {elseif $transfer['handle'] == "paid_post"}
                    {__("Paid Post")} = <strong>{print_money($transfer['price'])}</strong>
                  {elseif $transfer['handle'] == "movies"}
                    {__("Movies")} = <strong>{print_money($transfer['price'])}</strong>
                  {/if}
                </td>
                <td>
                  <span class="js_moment" data-time="{$transfer['time']}">{$transfer['time']}</span>
                </td>
                <td>
                  {if $transfer['status'] == '0'}
                    <span class="badge rounded-pill badge-lg bg-warning">{__("Pending")}</span>
                  {elseif $transfer['status'] == '1'}
                    <span class="badge rounded-pill badge-lg bg-success">{__("Approved")}</span>
                  {else}
                    <span class="badge rounded-pill badge-lg bg-danger">{__("Declined")}</span>
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