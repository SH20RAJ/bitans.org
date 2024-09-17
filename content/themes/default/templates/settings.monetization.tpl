<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr15" width="24px" height="24px"}
  {__("Monetization")}
</div>
<div class="card-body">
  {if $sub_view == ""}

    <div class="alert alert-info">
      <div class="text">
        <strong>{__("Monetization")}</strong><br>
        {__("Now you can earn money via paid posts, paid chat, paid audio/video calls or subscriptions plans.")}
        <br>
        {if $system['monetization_commission'] > 0}
          {__("There is commission")} <strong><span class="badge rounded-pill bg-warning">{$system['monetization_commission']}%</span></strong> {__("will be deducted")}.
          <br>
        {/if}
        {if $system['monetization_money_withdraw_enabled']}
          {__("You can")} <a class="alert-link" href="{$system['system_url']}/settings/monetization/payments" target="_blank">{__("withdraw your money")}</a>
        {/if}
        {if $system['monetization_money_transfer_enabled']}
          {if $system['monetization_money_withdraw_enabled']}{__("or")} {/if}
          {__("You can transfer your money to your")} <a class="alert-link" href="{$system['system_url']}/wallet" target="_blank"><i class="fa fa-wallet"></i> {__("wallet")}</a>
        {/if}
      </div>
    </div>

    {if $system['verification_for_monetization'] && !$user->_data['user_verified']}
      <div class="alert alert-danger">
        <div class="icon">
          <i class="fa fa-exclamation-circle fa-2x"></i>
        </div>
        <div class="text">
          <strong>{__("Account Verification Required")}</strong><br>
          {__("To enable monetization your account must be verified")} <a href="{$system['system_url']}/settings/verification">{__("Verify Now")}</a>
        </div>
      </div>
    {/if}

    <div class="heading-small mb20">
      {__("Monetization Settings")}
    </div>
    <div class="pl-md-4">
      <form class="js_ajax-forms" data-url="users/settings.php?edit=monetization">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="monetization"  class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Monetization")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable or disable monetization for your content")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="user_monetization_enabled">
              <input type="checkbox" name="user_monetization_enabled" id="user_monetization_enabled" {if $user->_data['user_monetization_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="heading-small mb20">
          {__("Paid Chat")}
        </div>
        <div class="pl-md-4">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Chat Message")} ({$system['system_currency']})
            </label>
            <div class="col-md-9">
              <input type="text" class="form-control" name="user_monetization_chat_price" value="{$user->_data['user_monetization_chat_price']}">
              <div class="form-text">
                {__("The price you want to charge for each message sent by the user (0 for free)")}
              </div>
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Audio/Video Call")} ({$system['system_currency']})
            </label>
            <div class="col-md-9">
              <input type="text" class="form-control" name="user_monetization_call_price" value="{$user->_data['user_monetization_call_price']}">
              <div class="form-text">
                {__("The price you want to charge for each audio/video call by the user (0 for free)")}
              </div>
            </div>
          </div>
        </div>

        <div class="heading-small mb20">
          {__("Subscriptions")}
        </div>
        <div class="pl-md-4">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Subscriptions Plans")}
            </label>
            <div class="col-md-9">
              <div class="payment-plans">
                {foreach $monetization_plans as $plan}
                  <div class="payment-plan">
                    <div class="text-xxlg">{__($plan['title'])}</div>
                    <div class="text-xlg">{print_money($plan['price'])} / {if $plan['period_num'] != '1'}{$plan['period_num']}{/if} {__($plan['period']|ucfirst)}</div>
                    {if {$plan['custom_description']}}
                      <div>{$plan['custom_description']}</div>
                    {/if}
                    <div class="mt10">
                      <span class="text-link mr10 js_monetization-deleter" data-id="{$plan['plan_id']}">
                        <i class="fa fa-trash-alt mr5"></i>{__("Delete")}
                      </span>
                      |
                      <span data-toggle="modal" data-url="monetization/controller.php?do=edit&id={$plan['plan_id']}" class="text-link ml10">
                        <i class="fa fa-pen mr5"></i>{__("Edit")}
                      </span>
                    </div>
                  </div>
                {/foreach}
                <div data-toggle="modal" data-url="monetization/controller.php?do=add&node_id={$user->_data['user_id']}&node_type=profile" class="payment-plan new">
                  <div class="d-flex align-items-center justify-content-center">
                    <i class="fa fa-plus mr5"></i>
                    {__("Add New")}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-9 offset-md-3">
            <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </form>
    </div>

    <div class="divider"></div>

    <div class="heading-small mb20">
      {__("Monetization Balance")}
    </div>
    <div class="pl-md-4">
      <div class="row">
        <!-- subscribers -->
        <div class="col-sm-6">
          <div class="section-title mb20">
            {__("Profile Subscribers")}
          </div>
          <div class="stat-panel bg-info">
            <div class="stat-cell">
              <i class="fa fas fa-users bg-icon"></i>
              <div class="h3 mtb10">
                {$subscribers_count}
              </div>
            </div>
          </div>
        </div>
        <!-- subscribers -->

        <!-- money balance -->
        <div class="col-sm-6">
          <div class="section-title mb20">
            {__("Monetization Money Balance")}
          </div>
          <div class="stat-panel bg-primary">
            <div class="stat-cell">
              <i class="fa fa-donate bg-icon"></i>
              <div class="h3 mtb10">
                {print_money($user->_data['user_monetization_balance']|number_format:2)}
              </div>
            </div>
          </div>
        </div>
        <!-- monetization balance -->
      </div>
    </div>
  {elseif $sub_view == "payments"}
    <div class="heading-small mb20">
      {__("Withdrawal Request")}
    </div>
    <div class="pl-md-4">
      <form class="js_ajax-forms" data-url="users/withdraw.php?type=monetization">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Your Balance")}
          </label>
          <div class="col-md-9">
            <h6>
              <span class="badge badge-lg bg-info">
                {print_money($user->_data['user_monetization_balance']|number_format:2)}
              </span>
            </h6>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Amount")} ({$system['system_currency']})
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="amount">
            <div class="form-text">
              {__("The minimum withdrawal request amount is")} {print_money($system['monetization_min_withdrawal'])}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Payment Method")}
          </label>
          <div class="col-md-9">
            {if in_array("paypal", $system['monetization_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_paypal" value="paypal" class="form-check-input">
                <label class="form-check-label" for="method_paypal">{__("PayPal")}</label>
              </div>
            {/if}
            {if in_array("skrill", $system['monetization_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_skrill" value="skrill" class="form-check-input">
                <label class="form-check-label" for="method_skrill">{__("Skrill")}</label>
              </div>
            {/if}
            {if in_array("moneypoolscash", $system['monetization_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_moneypoolscash" value="moneypoolscash" class="form-check-input">
                <label class="form-check-label" for="method_moneypoolscash">{__("MoneyPoolsCash")}</label>
              </div>
            {/if}
            {if in_array("bank", $system['monetization_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_bank" value="bank" class="form-check-input">
                <label class="form-check-label" for="method_bank">{__("Bank Transfer")}</label>
              </div>
            {/if}
            {if in_array("custom", $system['monetization_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_custom" value="custom" class="form-check-input">
                <label class="form-check-label" for="method_custom">{__($system['monetization_payment_method_custom'])}</label>
              </div>
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Transfer To")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="method_value">
          </div>
        </div>

        <div class="row">
          <div class="col-md-9 offset-md-3">
            <button type="submit" class="btn btn-primary">{__("Make a withdrawal")}</button>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </form>
    </div>

    <div class="divider"></div>

    <div class="heading-small mb20">
      {__("Withdrawal History")}
    </div>
    <div class="pl-md-4">
      {if $payments}
        <div class="table-responsive mt20">
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>{__("ID")}</th>
                <th>{__("Amount")}</th>
                <th>{__("Method")}</th>
                <th>{__("Transfer To")}</th>
                <th>{__("Time")}</th>
                <th>{__("Status")}</th>
              </tr>
            </thead>
            <tbody>
              {foreach $payments as $payment}
                <tr>
                  <td>{$payment@iteration}</td>
                  <td>{print_money($payment['amount']|number_format:2)}</td>
                  <td>
                    {if $payment['method'] == "custom"}
                      {$system['monetization_payment_method_custom']}
                    {else}
                      {$payment['method']|ucfirst}
                    {/if}
                  </td>
                  <td>{$payment['method_value']}</td>
                  <td>
                    <span class="js_moment" data-time="{$payment['time']}">{$payment['time']}</span>
                  </td>
                  <td>
                    {if $payment['status'] == '0'}
                      <span class="badge rounded-pill badge-lg bg-warning">{__("Pending")}</span>
                    {elseif $payment['status'] == '1'}
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
  {/if}
</div>