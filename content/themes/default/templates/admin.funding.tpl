<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == "find"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/funding/requests" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-hand-holding-usd mr10"></i>{__("Funding")}
    {if $sub_view == "payments"} &rsaquo; {__("Payment Requests")}{/if}
    {if $sub_view == "requests"} &rsaquo; {__("Funding Requests")}{/if}
    {if $sub_view == "find"} &rsaquo; {__("Find")}{/if}
  </div>

  {if $sub_view == ""}

    <form class="js_ajax-forms" data-url="admin/settings.php?edit=funding">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="funding" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Funding")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the funding On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="funding_enabled">
              <input type="checkbox" name="funding_enabled" id="funding_enabled" {if $system['funding_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider dashed"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="withdrawal" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Users Can Withdraw Earned Money")}</div>
            <div class="form-text d-none d-sm-block">{__("If enabled users will be able to withdraw earned money")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="funding_money_withdraw_enabled">
              <input type="checkbox" name="funding_money_withdraw_enabled" id="funding_money_withdraw_enabled" {if $system['funding_money_withdraw_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Payment Method")}
          </label>
          <div class="col-md-9">
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_paypal" id="method_paypal" {if in_array("paypal", $system['funding_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_paypal">{__("PayPal")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_skrill" id="method_skrill" {if in_array("skrill", $system['funding_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_skrill">{__("Skrill")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_moneypoolscash" id="method_moneypoolscash" {if in_array("moneypoolscash", $system['funding_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_moneypoolscash">{__("MoneyPoolsCash")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_bank" id="method_bank" {if in_array("bank", $system['funding_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_bank">{__("Bank Transfer")}</label>
            </div>
            <div class="form-check form-check-inline" id="js_custome-withdrawal">
              <input type="checkbox" class="form-check-input" name="method_custom" id="method_custom" {if in_array("custom", $system['funding_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_custom">{__("Custom Method")}</label>
            </div>
            <div class="form-text">
              {__("Users can send withdrawal requests via any of these methods")}
            </div>
          </div>
        </div>

        <div id="js_custome-withdrawal-name" {if !in_array("custom", $system['funding_payment_method_array'])}class="x-hidden" {/if}>
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Custom Method Name")}
            </label>
            <div class="col-md-9">
              <input type="text" class="form-control" name="funding_payment_method_custom" value="{$system['funding_payment_method_custom']}">
              <div class="form-text">
                {__("Set the name of your custom withdrawal payment method")}
              </div>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Minimum Withdrawal Request")} ({$system['system_currency']})
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="funding_min_withdrawal" value="{$system['funding_min_withdrawal']}">
            <div class="form-text">
              {__("The minimum amount of money so user can send a withdrawal request")}
            </div>
          </div>
        </div>

        <div class="divider dashed"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="wallet" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Users Can Transfer Earned Money To Wallet")}</div>
            <div class="form-text d-none d-sm-block">
              {__("If wallet enabled users will be able to transfer earned money to their wallet")}<br>
              {__("Make sure you have enabled")} <a href="{$system['system_url']}/{$control_panel['url']}/wallet">{__("Wallet System")}</a>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="funding_money_transfer_enabled">
              <input type="checkbox" name="funding_money_transfer_enabled" id="funding_money_transfer_enabled" {if $system['funding_money_transfer_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider dashed"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Commission")} (%)
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="funding_commission" value="{$system['funding_commission']}">
            <div class="form-text">
              {__("Leave it 0 if you don't want to get any commissions")}
            </div>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {elseif $sub_view == "payments"}

    <div class="card-body">

      <div class="alert alert-info">
        <div class="icon">
          <i class="fa fa-info-circle fa-2x"></i>
        </div>
        <div class="text pt5">
          {__("You will need to make the payments from your Paypal, Skrill, Bank Account... etc")}. {__("After making the payment you can mark the payment request as paid")}.
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("User")}</th>
              <th>{__("Amount")}</th>
              <th>{__("Method")}</th>
              <th>{__("Transfer To")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['payment_id']}</td>
                <td>
                  <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                    <img class="tbl-image" src="{$row['user_picture']}">
                    {if $system['show_usernames_enabled']}{$row['user_name']}{else}{$row['user_firstname']} {$row['user_lastname']}{/if}
                  </a>
                </td>
                <td>{print_money($row['amount']|number_format:2)}</td>
                <td>
                  <span class="badge rounded-pill badge-lg bg-{$row['method_color']}">
                    {$row['method']|ucfirst}
                  </span>
                </td>
                <td>{$row['method_value']}</td>
                <td>
                  <button data-bs-toggle="tooltip" title='{__("Mark as Paid")}' class="btn btn-sm btn-icon btn-rounded btn-success js_admin-withdraw" data-type="funding" data-handle="approve" data-id="{$row['payment_id']}">
                    <i class="fa fa-check"></i>
                  </button>
                  <button data-bs-toggle="tooltip" title='{__("Decline")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-withdraw" data-type="funding" data-handle="decline" data-id="{$row['payment_id']}">
                    <i class="fa fa-times"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "requests" || $sub_view == "find"}

    <div class="card-body">

      <!-- search form -->
      <div class="mb20">
        <form class="d-flex flex-row align-items-center flex-wrap" action="{$system['system_url']}/{$control_panel['url']}/funding/find" method="get">
          <div class="form-group mb0">
            <div class="input-group">
              <input type="text" class="form-control" name="query">
              <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-search mr5"></i>{__("Search")}</button>
            </div>
          </div>
        </form>
        <div class="form-text small">
          {__("Search by Funding Title or Description")}
        </div>
      </div>
      <!-- search form -->

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Author")}</th>
              <th>{__("Title")}</th>
              <th>{__("Time")}</th>
              <th>{__("Link")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>{$row['post_id']}</td>
                  <td>
                    <a target="_blank" href="{$row['post_author_url']}">
                      <img class="tbl-image" src="{$row['post_author_picture']}">
                      {$row['post_author_name']}
                    </a>
                  </td>
                  <td>
                    <span title="{$row['title']}">{$row['title']|truncate:30}</span>
                  </td>
                  <td><span class="js_moment" data-time="{$row['time']}">{$row['time']}</span></td>
                  <td>
                    <a class="btn btn-sm btn-light" href="{$system['system_url']}/posts/{$row['post_id']}" target="_blank">
                      <i class="fa fa-eye mr5"></i>{__("View")}
                    </a>
                  </td>
                  <td>
                    <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="post" data-id="{$row['post_id']}">
                      <i class="fa fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              {/foreach}
            {else}
              <tr>
                <td colspan="6" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>

      {$pager}

    </div>

  {/if}
</div>