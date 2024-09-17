<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="affiliates" class="main-icon mr15" width="24px" height="24px"}
  {__("Affiliates")}
</div>
<div class="card-body">
  {if $sub_view == ""}
    <div class="alert alert-info">
      <div class="text">
        <strong>{__("Affiliates System")}</strong><br>
        {__("Earn up to")}
        {if $system['affiliate_type'] == "registration"}
          {print_money($system['affiliates_per_user']|number_format:2)} {__("For each user you will refer")}.<br>
          {__("You will be paid when")} {__("new user registered")}
        {else}
          {if $system['affiliate_payment_type'] == "fixed"}
            {print_money($system['affiliates_per_user']|number_format:2)} {__("For each user you will refer")}.<br>
          {else}
            {$system['affiliates_percentage']}% {__("From the package price of your refered user")}.<br>
          {/if}
          {__("You will be paid when")} {__("new user registered & bought a package")}
        {/if}
        <br>
        {if $system['affiliates_money_withdraw_enabled']}
          {__("You can withdraw your money")}
        {/if}
        {if $system['affiliates_money_transfer_enabled']}
          {if $system['affiliates_money_withdraw_enabled']}{__("or")} {/if}
          {__("You can transfer your money to your")} <a class="alert-link" href="{$system['system_url']}/wallet" target="_blank"><i class="fa fa-wallet"></i> {__("wallet")}</a>
        {/if}
      </div>
    </div>
    <div class="text-center text-xlg">
      {__("Your affiliate link is")}
    </div>
    <div style="margin: 25px auto; width: 60%;">
      <div class="input-group">
        <input type="text" disabled class="form-control" value="{$system['system_url']}/?ref={$user->_data['user_name']}">
        <button class="btn btn-light js_clipboard" data-clipboard-text="{$system['system_url']}/?ref={$user->_data['user_name']}" data-bs-toggle="tooltip" title='{__("Copy")}'>
          <i class="fas fa-copy"></i>
        </button>
      </div>
    </div>
    <div class="text-center text-xlg mb20">
      {__("Share")}<br>
      {include file='__social_share.tpl' _link="{$system['system_url']}/?ref%3D{$user->_data['user_name']}"}
    </div>
    <div class="row justify-content-center">
      <!-- money balance -->
      <div class="col-sm-6">
        <div class="section-title mb20">
          {__("Affiliates Money Balance")}
        </div>
        <div class="stat-panel bg-primary">
          <div class="stat-cell">
            <i class="fa fas fa-donate bg-icon"></i>
            <div class="h3 mtb10">
              {print_money($user->_data['user_affiliate_balance']|number_format:2)}
            </div>
          </div>
        </div>
      </div>
      <!-- money balance -->
    </div>

    <div class="divider"></div>

    {if count($affiliates) > 0}
      <ul>
        {foreach $affiliates as $_user}
          {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"]}
        {/foreach}
      </ul>
    {else}
      <p class="text-center text-muted">
        {__("No affiliates")}
      </p>
    {/if}

    <!-- see-more -->
    {if count($affiliates) >= $system['max_results']}
      <div class="alert alert-info see-more js_see-more" data-uid="{$user->_data['user_id']}" data-get="affiliates">
        <span>{__("See More")}</span>
        <div class="loader loader_small x-hidden"></div>
      </div>
    {/if}
    <!-- see-more -->
  {elseif $sub_view == "payments"}
    <div class="heading-small mb20">
      {__("Withdrawal Request")}
    </div>
    <div class="pl-md-4">
      <form class="js_ajax-forms" data-url="users/withdraw.php?type=affiliates">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Your Balance")}
          </label>
          <div class="col-md-9">
            <h6>
              <span class="badge badge-lg bg-info">
                {print_money($user->_data['user_affiliate_balance']|number_format:2)}
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
              {__("The minimum withdrawal request amount is")} {print_money($system['affiliates_min_withdrawal'])}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Payment Method")}
          </label>
          <div class="col-md-9">
            {if in_array("paypal", $system['affiliate_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_paypal" value="paypal" class="form-check-input">
                <label class="form-check-label" for="method_paypal">{__("PayPal")}</label>
              </div>
            {/if}
            {if in_array("skrill", $system['affiliate_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_skrill" value="skrill" class="form-check-input">
                <label class="form-check-label" for="method_skrill">{__("Skrill")}</label>
              </div>
            {/if}
            {if in_array("moneypoolscash", $system['affiliate_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_moneypoolscash" value="moneypoolscash" class="form-check-input">
                <label class="form-check-label" for="method_moneypoolscash">{__("MoneyPoolsCash")}</label>
              </div>
            {/if}
            {if in_array("bank", $system['affiliate_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_bank" value="bank" class="form-check-input">
                <label class="form-check-label" for="method_bank">{__("Bank Transfer")}</label>
              </div>
            {/if}
            {if in_array("custom", $system['affiliate_payment_method_array'])}
              <div class="form-check form-check-inline">
                <input type="radio" name="method" id="method_custom" value="custom" class="form-check-input">
                <label class="form-check-label" for="method_custom">{__($system['affiliate_payment_method_custom'])}</label>
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
                      {$system['affiliate_payment_method_custom']}
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