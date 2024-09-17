{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_wallet_aym5.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="inner">
    <h2>{__("Wallet")}</h2>
    <p class="text-xlg">{__("Send and Transfer Money")}</p>
  </div>
</div>
<!-- page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} sg-offcanvas" style="margin-top: -25px;">
  <div class="row">

    <!-- side panel -->
    <div class="col-12 d-block d-md-none sg-offcanvas-sidebar">
      {include file='_sidebar.tpl'}
    </div>
    <!-- side panel -->

    <!-- content panel -->
    <div class="col-12 sg-offcanvas-mainbar">

      <!-- tabs -->
      <div class="position-relative">
        <div class="content-tabs rounded-sm shadow-sm clearfix">
          <ul>
            <li {if $view == ""}class="active" {/if}>
              <a href="{$system['system_url']}/wallet">
                {include file='__svg_icons.tpl' icon="wallet" class="main-icon mr10" width="24px" height="24px"}
                {__("Wallet")}
              </a>
            </li>
            {if $system['wallet_withdrawal_enabled']}
              <li {if $view == "payments"}class="active" {/if}>
                <a href="{$system['system_url']}/wallet/payments">
                  {include file='__svg_icons.tpl' icon="payments" class="main-icon mr10" width="24px" height="24px"}
                  {__("Payments")}
                </a>
              </li>
            {/if}
          </ul>
        </div>
      </div>
      <!-- tabs -->

      {if $view == ""}

        <!-- wallet -->
        <div class="card mt20">
          <div class="card-header with-icon">
            {include file='__svg_icons.tpl' icon="wallet" class="main-icon mr10" width="24px" height="24px"}
            {__("Wallet")}
          </div>
          <div class="card-body page-content">
            {if $wallet_transfer_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Your")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_transfer_amount|number_format:2)}</span> {__("transfer transaction successfuly sent")}
              </div>
            {/if}
            {if $wallet_replenish_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Congratulation! Your wallet credit replenished successfully with")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_replenish_amount|number_format:2)}</span>
              </div>
            {/if}
            {if $wallet_withdraw_affiliates_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Congratulation! Your wallet credit replenished successfully with")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_withdraw_affiliates_amount|number_format:2)}</span> {__("from your affiliates credit")}
              </div>
            {/if}
            {if $wallet_withdraw_points_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Congratulation! Your wallet credit replenished successfully with")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_withdraw_points_amount|number_format:2)}</span> {__("from your points credit")}
              </div>
            {/if}
            {if $wallet_withdraw_market_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Congratulation! Your wallet credit replenished successfully with")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_withdraw_market_amount|number_format:2)}</span> {__("from your market credit")}
              </div>
            {/if}
            {if $wallet_withdraw_funding_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Congratulation! Your wallet credit replenished successfully with")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_withdraw_funding_amount|number_format:2)}</span> {__("from your funding credit")}
              </div>
            {/if}
            {if $wallet_withdraw_monetization_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Congratulation! Your wallet credit replenished successfully with")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_withdraw_monetization_amount|number_format:2)}</span> {__("from your monetization credit")}
              </div>
            {/if}
            {if $wallet_package_payment_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Your")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_package_payment_amount|number_format:2)}</span> {__("payment transaction successfuly done")}
              </div>
            {/if}
            {if $wallet_monetization_payment_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Your")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_monetization_payment_amount|number_format:2)}</span> {__("payment transaction successfuly done")}
              </div>
            {/if}
            {if $wallet_paid_post_amount}
              <div class="alert alert-success mb20">
                <i class="fas fa-check-circle mr5"></i>
                {__("Your")} <span class="badge rounded-pill badge-lg bg-secondary">{print_money($wallet_paid_post_amount|number_format:2)}</span> {__("payment transaction successfuly done")}
              </div>
            {/if}

            <div class="row">
              <!-- credit -->
              <div class="col-md-5">
                <div class="section-title mb20">
                  {__("Your Credit")}
                </div>
                <div class="stat-panel bg-gradient-info">
                  <div class="stat-cell small">
                    <i class="fa fa-money-bill-alt bg-icon"></i>
                    <div class="h3 mtb10">
                      {print_money($user->_data['user_wallet_balance']|number_format:2)}
                    </div>
                  </div>
                </div>
              </div>
              <!-- credit -->

              <!-- send & recieve money -->
              <div class="col-md-7">
                <div class="section-title mb20">
                  {__("Send & Recieve Money")}
                </div>
                <div class="d-grid">
                  {if $system['wallet_transfer_enabled']}
                    <button class="btn btn-outline-primary mb10" data-toggle="modal" data-url="#wallet-transfer">
                      {include file='__svg_icons.tpl' icon="wallet_transfer" class="main-icon mr10" width="24px" height="24px"}
                      {__("Send Money")}
                    </button>
                  {/if}
                </div>

                <div class="d-grid gap-2">
                  <button class="btn btn-outline-primary" data-toggle="modal" data-url="#wallet-replenish">
                    {include file='__svg_icons.tpl' icon="payments" class="main-icon mr10" width="24px" height="24px"}
                    {__("Replenish Credit")}
                  </button>
                  {if $system['affiliates_enabled'] && $system['affiliates_money_transfer_enabled']}
                    <button class="btn btn-outline-primary" data-toggle="modal" data-url="#wallet-withdraw-affiliates">
                      {include file='__svg_icons.tpl' icon="affiliates" class="main-icon mr10" width="24px" height="24px"}
                      {__("Affiliates Credit")}
                    </button>
                  {/if}
                  {if $system['points_enabled'] && $system['points_money_transfer_enabled']}
                    <button class="btn btn-outline-primary" data-toggle="modal" data-url="#wallet-withdraw-points">
                      {include file='__svg_icons.tpl' icon="points" class="main-icon mr10" width="24px" height="24px"}
                      {__("Points Credit")}
                    </button>
                  {/if}
                  {if $user->_data['can_sell_products'] && $system['market_money_transfer_enabled']}
                    <button class="btn btn-outline-primary" data-toggle="modal" data-url="#wallet-withdraw-market">
                      {include file='__svg_icons.tpl' icon="market" class="main-icon mr10" width="24px" height="24px"}
                      {__("Marketplace Credit")}
                    </button>
                  {/if}
                  {if $user->_data['can_raise_funding'] && $system['funding_money_transfer_enabled']}
                    <button class="btn btn-outline-primary" data-toggle="modal" data-url="#wallet-withdraw-funding">
                      {include file='__svg_icons.tpl' icon="funding" class="main-icon mr10" width="24px" height="24px"}
                      {__("Funding Credit")}
                    </button>
                  {/if}
                  {if $user->_data['can_monetize_content'] && $system['monetization_money_transfer_enabled']}
                    <button class="btn btn-outline-primary" data-toggle="modal" data-url="#wallet-withdraw-monetization">
                      {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="24px" height="24px"}
                      {__("Monetization Credit")}
                    </button>
                  {/if}
                </div>
              </div>
              <!-- send & recieve money -->

              <!-- wallet transactions -->
              <div class="col-12 mt20">
                <div class="section-title mt10 mb20">
                  {__("Wallet Transactions")}
                </div>
                {if $transactions}
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover js_dataTable">
                      <thead>
                        <tr>
                          <th>{__("ID")}</th>
                          <th>{__("Amount")}</th>
                          <th>{__("From / To")}</th>
                          <th>{__("Time")}</th>
                        </tr>
                      </thead>
                      <tbody>
                        {foreach $transactions as $transaction}
                          <tr>
                            <td>{$transaction['transaction_id']}</td>
                            <td>
                              {if $transaction['type'] == "out"}
                                <span class="badge rounded-pill badge-lg bg-danger mr5"><i class="far fa-arrow-alt-circle-down"></i></span>
                                <strong class="text-danger">{print_money($transaction['amount']|number_format:2)}</strong>
                              {else}
                                <span class="badge rounded-pill badge-lg bg-success mr5"><i class="far fa-arrow-alt-circle-up"></i></span>
                                <strong class="text-success">{print_money($transaction['amount']|number_format:2)}</strong>
                              {/if}
                            </td>
                            <td>
                              {if $transaction['type'] == "out"}
                                <span class="badge rounded-pill badge-lg bg-danger mr10">{__("To")}</span>
                              {else}
                                <span class="badge rounded-pill badge-lg bg-success mr10">{__("From")}</span>
                              {/if}
                              {if $transaction['node_type'] == "user" || $transaction['node_type'] == "tip"}
                                {if $transaction['node_type'] == "tip"}
                                  <span class="badge rounded-pill badge-lg bg-secondary mr10">{__("Tip")}</span>
                                {/if}
                                <a target="_blank" href="{$system['system_url']}/{$transaction['user_name']}">
                                  <img class="tbl-image" src="{$transaction['user_picture']}" style="float: none;">
                                  {if $system['show_usernames_enabled']}
                                    {$transaction['user_name']}
                                  {else}
                                    {$transaction['user_firstname']} {$transaction['user_lastname']}
                                  {/if}
                                </a>
                              {elseif $transaction['node_type'] == "recharge"}
                                {__("Replenish Credit")}
                              {elseif $transaction['node_type'] == "withdraw_wallet"}
                                {__("Wallet Withdrawal")}
                              {elseif $transaction['node_type'] == "withdraw_affiliates"}
                                {__("Affiliates Credit")}
                              {elseif $transaction['node_type'] == "withdraw_points"}
                                {__("Points Credit")}
                              {elseif $transaction['node_type'] == "withdraw_market"}
                                {__("Market Credit")}
                              {elseif $transaction['node_type'] == "withdraw_funding"}
                                {__("Funding Credit")}
                              {elseif $transaction['node_type'] == "withdraw_monetization"}
                                {__("Monetization Credit")}
                              {elseif $transaction['node_type'] == "package_payment"}
                                {__("Buy Pro Package")}
                              {elseif $transaction['node_type'] == "subscribe_profile" || $transaction['node_type'] == "subscribe_user"}
                                {__("Subscribe to Profile")}
                              {elseif $transaction['node_type'] == "subscribe_page"}
                                {__("Subscribe to Page")}
                              {elseif $transaction['node_type'] == "subscribe_group"}
                                {__("Subscribe to Group")}
                              {elseif $transaction['node_type'] == "paid_post"}
                                {__("Paid Post")}
                              {elseif $transaction['node_type'] == "market"}
                                {__("Market Purchase")}
                              {elseif $transaction['node_type'] == "paid_chat_message"}
                                {__("Paid Chat Message")}
                              {elseif $transaction['node_type'] == "paid_call"}
                                {__("Paid Call")}
                              {/if}
                            </td>
                            <td><span class="js_moment" data-time="{$transaction['date']}">{$transaction['date']}</span></td>
                          </tr>
                        {/foreach}
                      </tbody>
                    </table>
                  </div>
                {else}
                  {include file='_no_transactions.tpl'}
                {/if}
              </div>
              <!-- wallet transactions -->
            </div>
          </div>
        </div>
        <!-- wallet -->

      {elseif $view == "payments"}

        <!-- payments -->
        <div class="card mt20">
          <div class="card-header with-icon">
            {include file='__svg_icons.tpl' icon="payments" class="main-icon mr10" width="24px" height="24px"}
            {__("Payments")}
          </div>
          <div class="card-body page-content">
            <div class="section-title mt10 mb20">
              {__("Withdrawal Request")}
            </div>
            <form class="js_ajax-forms" data-url="users/withdraw.php?type=wallet">
              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Your Balance")}
                </label>
                <div class="col-md-9">
                  <h6>
                    <span class="badge badge-lg bg-info">
                      {print_money($user->_data['user_wallet_balance']|number_format:2)}
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
                    {__("The minimum withdrawal request amount is")} {print_money($system['wallet_min_withdrawal'])}
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Payment Method")}
                </label>
                <div class="col-md-9">
                  {if in_array("paypal", $system['wallet_payment_method_array'])}
                    <div class="form-check form-check-inline">
                      <input type="radio" name="method" id="method_paypal" value="paypal" class="form-check-input">
                      <label class="form-check-label" for="method_paypal">{__("PayPal")}</label>
                    </div>
                  {/if}
                  {if in_array("skrill", $system['wallet_payment_method_array'])}
                    <div class="form-check form-check-inline">
                      <input type="radio" name="method" id="method_skrill" value="skrill" class="form-check-input">
                      <label class="form-check-label" for="method_skrill">{__("Skrill")}</label>
                    </div>
                  {/if}
                  {if in_array("moneypoolscash", $system['wallet_payment_method_array'])}
                    <div class="form-check form-check-inline">
                      <input type="radio" name="method" id="method_moneypoolscash" value="moneypoolscash" class="form-check-input">
                      <label class="form-check-label" for="method_moneypoolscash">{__("MoneyPoolsCash")}</label>
                    </div>
                  {/if}
                  {if in_array("bank", $system['wallet_payment_method_array'])}
                    <div class="form-check form-check-inline">
                      <input type="radio" name="method" id="method_bank" value="bank" class="form-check-input">
                      <label class="form-check-label" for="method_bank">{__("Bank Transfer")}</label>
                    </div>
                  {/if}
                  {if in_array("custom", $system['wallet_payment_method_array'])}
                    <div class="form-check form-check-inline">
                      <input type="radio" name="method" id="method_custom" value="custom" class="form-check-input">
                      <label class="form-check-label" for="method_custom">{__($system['wallet_payment_method_custom'])}</label>
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

            <div class="section-title mt20 mb20">
              {__("Withdrawal History")}
            </div>
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
                            {$system['wallet_payment_method_custom']}
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
        </div>
        <!-- payments -->

      {/if}
    </div>
    <!-- content panel -->

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}