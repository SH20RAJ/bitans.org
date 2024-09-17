<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == "find"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/market/products" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {elseif $sub_view == "find_orders"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/market/orders" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {elseif $sub_view == "categories"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/market/add_category" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New Category")}
        </a>
      </div>
    {elseif $sub_view == "add_category" || $sub_view == "edit_category"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/market/categories" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-shopping-bag mr10"></i>{__("Marketplace")}
    {if $sub_view == "products"} &rsaquo; {__("Products")}{/if}
    {if $sub_view == "find"} &rsaquo; {__("Products")} &rsaquo; {__("Find")}{/if}
    {if $sub_view == "orders"} &rsaquo; {__("Orders")}{/if}
    {if $sub_view == "find_orders"} &rsaquo; {__("Orders")} &rsaquo; {__("Find")}{/if}
    {if $sub_view == "categories"} &rsaquo; {__("Categories")}{/if}
    {if $sub_view == "add_category"} &rsaquo; {__("Categories")} &rsaquo; {__("Add New Category")}{/if}
    {if $sub_view == "edit_category"} &rsaquo; {__("Categories")} &rsaquo; {$data['category_name']}{/if}
    {if $sub_view == "payments"} &rsaquo; {__("Payments Requests")}{/if}
  </div>

  {if $sub_view == ""}

    <form class="js_ajax-forms" data-url="admin/settings.php?edit=market">
      <div class="card-body">

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="market" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Marketplace")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the marketplace On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="market_enabled">
              <input type="checkbox" name="market_enabled" id="market_enabled" {if $system['market_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="products" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Shopping Cart")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the shopping cart On and Off")}<br>
              {__("Note: If disabled buyers can only contact sellers to buy products")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="market_shopping_cart_enabled">
              <input type="checkbox" name="market_shopping_cart_enabled" id="market_shopping_cart_enabled" {if $system['market_shopping_cart_enabled']}checked{/if}>
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
            <label class="switch" for="market_money_withdraw_enabled">
              <input type="checkbox" name="market_money_withdraw_enabled" id="market_money_withdraw_enabled" {if $system['market_money_withdraw_enabled']}checked{/if}>
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
              <input type="checkbox" class="form-check-input" name="method_paypal" id="method_paypal" {if in_array("paypal", $system['market_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_paypal">{__("PayPal")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_skrill" id="method_skrill" {if in_array("skrill", $system['market_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_skrill">{__("Skrill")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_moneypoolscash" id="method_moneypoolscash" {if in_array("moneypoolscash", $system['market_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_moneypoolscash">{__("MoneyPoolsCash")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="method_bank" id="method_bank" {if in_array("bank", $system['market_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_bank">{__("Bank Transfer")}</label>
            </div>
            <div class="form-check form-check-inline" id="js_custome-withdrawal">
              <input type="checkbox" class="form-check-input" name="method_custom" id="method_custom" {if in_array("custom", $system['market_payment_method_array'])}checked{/if}>
              <label class="form-check-label" for="method_custom">{__("Custom Method")}</label>
            </div>
            <div class="form-text">
              {__("Users can send withdrawal requests via any of these methods")}
            </div>
          </div>
        </div>

        <div id="js_custome-withdrawal-name" {if !in_array("custom", $system['market_payment_method_array'])}class="x-hidden" {/if}>
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Custom Method Name")}
            </label>
            <div class="col-md-9">
              <input type="text" class="form-control" name="market_payment_method_custom" value="{$system['market_payment_method_custom']}">
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
            <input type="text" class="form-control" name="market_min_withdrawal" value="{$system['market_min_withdrawal']}">
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
            <label class="switch" for="market_money_transfer_enabled">
              <input type="checkbox" name="market_money_transfer_enabled" id="market_money_transfer_enabled" {if $system['market_money_transfer_enabled']}checked{/if}>
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
            <input type="text" class="form-control" name="market_commission" value="{$system['market_commission']}">
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

  {elseif $sub_view == "products" || $sub_view == "find"}

    <div class="card-body">

      <!-- search form -->
      <div class="mb20">
        <form class="d-flex flex-row align-items-center flex-wrap" action="{$system['system_url']}/{$control_panel['url']}/market/find" method="get">
          <div class="form-group mb0">
            <div class="input-group">
              <input type="text" class="form-control" name="query">
              <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-search mr5"></i>{__("Search")}</button>
            </div>
          </div>
        </form>
        <div class="form-text small">
          {__("Search by Product Name, Description or Location")}
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
                    <span title="{$row['name']}">{$row['name']|truncate:30}</span>
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

  {elseif $sub_view == "orders" || $sub_view == "find_orders"}

    <div class="card-body">

      <!-- search form -->
      <div class="mb20">
        <form class="d-flex flex-row align-items-center flex-wrap" action="{$system['system_url']}/{$control_panel['url']}/market/find_orders" method="get">
          <div class="form-group mb0">
            <div class="input-group">
              <input type="text" class="form-control" name="query">
              <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-search mr5"></i>{__("Search")}</button>
            </div>
          </div>
        </form>
        <div class="form-text small">
          {__("Search by Order ID")}
        </div>
      </div>
      <!-- search form -->

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("Order ID")}</th>
              <th>{__("Seller")}</th>
              <th>{__("Buyer")}</th>
              <th>{__("Status")}</th>
              <th>{__("Subtotal")}</th>
              <th>{__("Time")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>{$row['order_hash']}</td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['seller_username']}">
                      <img class="tbl-image" src="{$row['seller_picture']}">
                      {$row['seller_fullname']}
                    </a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['buyer_username']}">
                      <img class="tbl-image" src="{$row['buyer_picture']}">
                      {$row['buyer_fullname']}
                    </a>
                  </td>
                  <td>
                    {if $row['status'] == "canceled"}
                      <span class="badge badge-lg bg-danger">{__($row['status'])|ucfirst}</span>
                    {elseif $row['status'] == "delivered"}
                      <span class="badge badge-lg bg-success">{__($row['status'])|ucfirst}</span>
                    {else}
                      <span class="badge badge-lg bg-info">{__($row['status'])|ucfirst}</span>
                    {/if}
                  </td>
                  <td>
                    {print_money(number_format($row['sub_total'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}
                  </td>
                  <td><span class="js_moment" data-time="{$row['insert_time']}">{$row['insert_time']}</span></td>
                  <td>
                    <button class="btn btn-sm btn-icon btn-rounded btn-info" data-toggle="modal" data-url="users/orders.php?do=view&id={$row['order_id']}" data-size="extra-large">
                      <i class="fa fa-eye"></i>
                    </button>
                    {if $row['status'] != "delivered" && $row['status'] != "canceled"}
                      <button class="btn btn-sm btn-icon btn-rounded btn-success" data-toggle="modal" data-url="users/orders.php?do=edit&id={$row['order_id']}">
                        <i class="fa fa-pencil"></i>
                      </button>
                    {/if}
                    <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="order" data-id="{$row['order_id']}">
                      <i class="fa fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              {/foreach}
            {else}
              <tr>
                <td colspan="7" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>

      {$pager}

    </div>

  {elseif $sub_view == "categories"}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_treegrid">
          <thead>
            <tr>
              <th>{__("Title")}</th>
              <th>{__("Description")}</th>
              <th>{__("Order")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                {include file='__categories.recursive_rows.tpl' _url="market" _handle="market_category"}
              {/foreach}
            {else}
              <tr>
                <td colspan="5" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "add_category"}

    <form class="js_ajax-forms" data-url="admin/market.php?do=add_category">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_name">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="category_description" rows="3"></textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Parent Category")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="category_parent_id">
              <option value="0">{__("Set as a Partent Category")}</option>
              {foreach $categories as $category}
                {include file='__categories.recursive_options.tpl'}
              {/foreach}
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_order">
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

  {elseif $sub_view == "edit_category"}

    <form class="js_ajax-forms" data-url="admin/market.php?do=edit_category&id={$data['category_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_name" value="{$data['category_name']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="category_description" rows="3">{$data['category_description']}</textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Parent Category")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="category_parent_id">
              <option value="0">{__("Set as a Partent Category")}</option>
              {foreach $data["categories"] as $category}
                {include file='__categories.recursive_options.tpl' data_category=$data['category_parent_id']}
              {/foreach}
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_order" value="{$data['category_order']}">
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
                  <button data-bs-toggle="tooltip" title='{__("Mark as Paid")}' class="btn btn-sm btn-icon btn-rounded btn-success js_admin-withdraw" data-type="market" data-handle="approve" data-id="{$row['payment_id']}">
                    <i class="fa fa-check"></i>
                  </button>
                  <button data-bs-toggle="tooltip" title='{__("Decline")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-withdraw" data-type="market" data-handle="decline" data-id="{$row['payment_id']}">
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