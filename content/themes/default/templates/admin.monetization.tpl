<div class="card">
  <div class="card-header with-icon">
    <i class="fa fa-coins mr5"></i>{__("Monetization")}
    {if $sub_view == "payments"} &rsaquo; {__("Payment Requests")}{/if}
  </div>

  {if $sub_view == ""}

    <form class="js_ajax-forms" data-url="admin/monetization.php?do=settings">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="monetization" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Monetization Enabled")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Enable monetization to enable your users to earn money from their content")}<br>
              {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/settings/payments">{__("Payments Settings")}</a>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="monetization_enabled">
              <input type="checkbox" name="monetization_enabled" id="monetization_enabled" {if $system['monetization_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider dashed"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="wallet" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Users Can Subscribe Via Wallet Balance")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Enable users to subscribe via their wallet balance")}<br>
              {__("Make sure you have enabled")} <a href="{$system['system_url']}/{$control_panel['url']}/wallet">{__("Wallet System")}</a>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="monetization_wallet_payment_enabled">
              <input type="checkbox" name="monetization_wallet_payment_enabled" id="monetization_wallet_payment_enabled" {if $system['monetization_wallet_payment_enabled']}checked{/if}>
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
            <label class="switch" for="monetization_money_withdraw_enabled">
              <input type="checkbox" name="monetization_money_withdraw_enabled" id="monetization_money_withdraw_enabled" {if $system['monetization_money_withdraw_enabled']}checked{/if}>
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
              <input type="checkbox" class="form-check-input" name="method_paypal" id="method_paypal" {if in_array("paypal", $system['monetization_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_paypal">{__("PayPal")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_skrill" id="method_skrill" {if in_array("skrill", $system['monetization_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_skrill">{__("Skrill")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_moneypoolscash" id="method_moneypoolscash" {if in_array("moneypoolscash", $system['monetization_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_moneypoolscash">{__("MoneyPoolsCash")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_bank" id="method_bank" {if in_array("bank", $system['monetization_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_bank">{__("Bank Transfer")}</label>
            </div>
            <div class="form-check form-check-inline" id="js_custome-withdrawal">
              <input type="checkbox" class="form-check-input" name="method_custom" id="method_custom" {if in_array("custom", $system['monetization_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_custom">{__("Custom Method")}</label>
            </div>
            <div class="form-text">
              {__("Users can send withdrawal requests via any of these methods")}
            </div>
          </div>
        </div>

        <div id="js_custome-withdrawal-name" {if !in_array("custom", $system['monetization_payment_method_array'])}class="x-hidden" {/if}>
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Custom Method Name")}
            </label>
            <div class="col-md-9">
              <input type="text" class="form-control" name="monetization_payment_method_custom" value="{$system['monetization_payment_method_custom']}">
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
            <input type="text" class="form-control" name="monetization_min_withdrawal" value="{$system['monetization_min_withdrawal']}">
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
            <label class="switch" for="monetization_money_transfer_enabled">
              <input type="checkbox" name="monetization_money_transfer_enabled" id="monetization_money_transfer_enabled" {if $system['monetization_money_transfer_enabled']}checked{/if}>
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
            <input type="text" class="form-control" name="monetization_commission" value="{$system['monetization_commission']}">
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
                  <button data-bs-toggle="tooltip" title='{__("Mark as Paid")}' class="btn btn-sm btn-icon btn-rounded btn-success js_admin-withdraw" data-type="monetization" data-handle="approve" data-id="{$row['payment_id']}">
                    <i class="fa fa-check"></i>
                  </button>
                  <button data-bs-toggle="tooltip" title='{__("Decline")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-withdraw" data-type="monetization" data-handle="decline" data-id="{$row['payment_id']}">
                    <i class="fa fa-times"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {/if}
</div>