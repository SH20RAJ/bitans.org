{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_online_shopping_ga73.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="{if $system['fluid_design']}container-fluid{else}container{/if}">
    <h2>{__("Marketplace")}</h2>
    <p class="text-xlg">{__($system['system_description_marketplace'])}</p>
    <div class="row mt20">
      <div class="col-sm-9 col-lg-6 mx-sm-auto">
        <form class="js_search-form" data-handle="market">
          <div class="input-group">
            <input type="text" class="form-control" name="query" placeholder='{__("Search for products")}'>
            <button type="submit" class="btn btn-light">{__("Search")}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} sg-offcanvas" style="margin-top: -25px;">

  <div class="position-relative">
    <!-- tabs -->
    <div class="content-tabs rounded-sm shadow-sm clearfix">
      <ul>
        <li {if $view == "" || $view == "search" || $view == "category"}class="active" {/if}>
          <a href="{$system['system_url']}/market">
            {include file='__svg_icons.tpl' icon="market" class="main-icon mr10" width="24px" height="24px"}
            <span class="ml5 d-none d-lg-inline-block">{__("Market")}</span>
          </a>
        </li>
        {if $system['market_shopping_cart_enabled'] && $user->_logged_in}
          <li {if $view == "cart"}class="active" {/if}>
            <a href="{$system['system_url']}/market/cart">
              {include file='__svg_icons.tpl' icon="products" class="main-icon mr10" width="24px" height="24px"}
              <span class="ml5 d-none d-lg-inline-block">{__("Shopping Cart")}</span>
            </a>
          </li>
          <li {if $view == "orders"}class="active" {/if}>
            <a href="{$system['system_url']}/market/orders">
              {include file='__svg_icons.tpl' icon="articles" class="main-icon mr10" width="24px" height="24px"}
              <span class="ml5 d-none d-lg-inline-block">{__("Orders")}</span>
            </a>
          </li>
          {if $user->_data['can_sell_products']}
            <li {if $view == "sales"}class="active" {/if}>
              <a href="{$system['system_url']}/market/sales">
                {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="24px" height="24px"}
                <span class="ml5 d-none d-lg-inline-block">{__("Sales")}</span>
              </a>
            </li>
          {/if}
        {/if}
      </ul>
      {if $system['market_shopping_cart_enabled'] && $user->_data['can_sell_products']}
        <div class="mt10 float-end">
          <button class="btn btn-md btn-primary d-none d-lg-block" data-toggle="modal" data-url="posts/product.php?do=create">
            <i class="fa fa-plus-circle mr5"></i>{__("Sell New Product")}
          </button>
          <button class="btn btn-sm btn-icon btn-primary d-block d-lg-none" data-toggle="modal" data-url="posts/product.php?do=create">
            <i class="fa fa-plus-circle"></i>
          </button>
        </div>
      {/if}
    </div>
    <!-- tabs -->
  </div>

  {if in_array($view, ['', 'search', 'category'])}

    <div class="row">
      <!-- left panel -->
      <div class="col-md-4 col-lg-3 sg-offcanvas-sidebar">
        <!-- categories -->
        <div class="card">
          <div class="card-body with-nav">
            <ul class="side-nav">
              {if $view != "category"}
                <li class="active">
                  <a href="{$system['system_url']}/market">
                    {__("All")}
                  </a>
                </li>
              {else}
                <li>
                  {if $current_category['parent']}
                    <a href="{$system['system_url']}/market/category/{$current_category['parent']['category_id']}/{$current_category['parent']['category_url']}">
                      <i class="fas fa-arrow-alt-circle-left mr5"></i>{__($current_category['parent']['category_name'])}
                    </a>
                  {else}
                    <a href="{$system['system_url']}/market">
                      {if $current_category['sub_categories']}<i class="fas fa-arrow-alt-circle-left mr5"></i>{/if}{__("All")}
                    </a>
                  {/if}
                </li>
              {/if}
              {foreach $categories as $category}
                <li {if $view == "category" && $current_category['category_id'] == $category['category_id']}class="active" {/if}>
                  <a href="{$system['system_url']}/market/category/{$category['category_id']}/{$category['category_url']}">
                    {__($category['category_name'])}
                    {if $category['sub_categories']}
                      <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    {/if}
                  </a>
                </li>
              {/foreach}
            </ul>
          </div>
        </div>
        <!-- categories -->
      </div>
      <!-- left panel -->

      <!-- right panel -->
      <div class="col-md-8 col-lg-9 sg-offcanvas-mainbar">

        {include file='_ads.tpl'}

        {if $view == "search"}
          <div class="bs-callout bs-callout-info mt0">
            <!-- results counter -->
            <span class="badge rounded-pill badge-lg bg-secondary">{$total}</span> {__("results were found for the search for")} "<strong class="text-primary">{htmlentities($query, ENT_QUOTES, 'utf-8')}</strong>"
            <!-- results counter -->
          </div>
        {/if}

        {if $view == "" && $promoted_products}
          <div class="articles-widget-header">
            <div class="articles-widget-title">{__("Promoted Products")}</div>
          </div>
          <div class="row mb20">
            {foreach $promoted_products as $post}
              {include file='__feeds_product.tpl' _boosted=true}
            {/foreach}
          </div>
        {/if}

        {if $rows}
          <div class="articles-widget-header clearfix">
            <!-- sort -->
            <div class="float-end">
              <div class="dropdown">
                <button type="button" class="btn btn-sm btn-light dropdown-toggle ml10" data-bs-toggle="dropdown" data-display="static">
                  {if !$sort || $sort == "latest"}
                    <i class="fas fa-bars fa-fw"></i> {__("Latest")}
                  {elseif $sort == "price-high"}
                    <i class="fas fa-sort-amount-down fa-fw"></i> {__("Price High")}
                  {elseif $sort == "price-low"}
                    <i class="fas fa-sort-amount-down-alt fa-fw"></i> {__("Price Low")}
                  {/if}
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a href="?{if $distance}distance={$distance}&{/if}sort=latest" class="dropdown-item"><i class="fas fa-bars fa-fw mr10"></i>{__("Latest")}</a>
                  <a href="?{if $distance}distance={$distance}&{/if}sort=price-high" class="dropdown-item"><i class="fas fa-sort-amount-down fa-fw mr10"></i>{__("Price High")}</a>
                  <a href="?{if $distance}distance={$distance}&{/if}sort=price-low" class="dropdown-item"><i class="fas fa-sort-amount-down-alt fa-fw mr10"></i>{__("Price Low")}</a>
                </div>
              </div>
            </div>
            <!-- sort -->
            {if $user->_logged_in && $system['location_finder_enabled']}
              <!-- location filter -->
              <div class="float-end">
                <div class="dropdown">
                  <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" data-display="static">
                    <i class="fa fa-map-marker-alt mr5"></i>{__("Location")}
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                    <form class="ptb15 plr15" method="get" action="?">
                      <div class="form-group">
                        <label class="form-label">{__("Distance")}</label>
                        <div>
                          {if $sort}
                            <input type="hidden" name="sort" value="{$sort}">
                          {/if}
                          <div class="d-grid mb10">
                            <input type="range" class="custom-range" min="1" max="5000" name="distance" value="{if $distance}{$distance}{else}5000{/if}" oninput="this.form.distance_value.value=this.value">
                          </div>
                          <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">{if $system['system_distance'] == "mile"}{__("ML")}{else}{__("KM")}{/if}</span>
                            <input disabled type="number" class="form-control" min="1" max="5000" name="distance_value" value="{if $distance}{$distance}{else}5000{/if}" oninput="this.form.distance.value=this.value">
                          </div>
                        </div>
                      </div>
                      <div class="d-grid">
                        <button type="submit" class="btn btn-md btn-primary">{__("Filter")}</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- location filter -->
            {/if}
            <div class="articles-widget-title">{__("Products")}</div>
          </div>

          <div class="row">
            {foreach $rows as $post}
              {include file='__feeds_product.tpl'}
            {/foreach}
          </div>

          {$pager}
        {else}
          {include file='_no_data.tpl'}
        {/if}
      </div>
      <!-- right panel -->
    </div>

  {elseif $view == "cart"}

    <div class="card">
      <div class="card-header with-icon">
        {include file='__svg_icons.tpl' icon="products" class="main-icon mr10" width="24px" height="24px"}
        {__("Shopping Cart")}
      </div>
      <div class="card-body page-content">
        <div class="row">
          <!-- Addresses -->
          <div class="col-md-5">
            <div class="section-title mb20">
              {include file='__svg_icons.tpl' icon="map" class="main-icon mr5" width="20px" height="20px"}
              {__("Your Addresses")}
            </div>
            {include file='_addresses.tpl' _small=true}
          </div>
          <!-- Addresses -->

          <!-- Shopping Cart -->
          <div class="col-md-7">
            <div class="section-title mb20">
              {include file='__svg_icons.tpl' icon="products" class="main-icon mr5" width="20px" height="20px"}
              {__("Shopping Cart")}
            </div>
            <div class="heading-small mb20">
              {__("Items")}
            </div>
            <div class="pl-md-4">
              {if $cart['items']}
                <div class="row">
                  {foreach $cart['items'] as $cart_item}
                    <div class="col-lg-6">
                      <div class="card product active">
                        <div class="product-image">
                          <div class="product-price">
                            {if $cart_item['post']['product']['price'] > 0}
                              {print_money($cart_item['post']['product']['price'])}
                            {else}
                              {__("Free")}
                            {/if}
                          </div>
                          {if $cart_item['post']['photos_num'] > 0}
                            <img src="{$system['system_uploads']}/{$cart_item['post']['photos'][0]['source']}">
                          {else}
                            <img src="{$system['system_url']}/content/themes/{$system['theme']}/images/blank_product.png">
                          {/if}
                        </div>
                        <div class="product-info plr15">
                          <div class="product-meta title">
                            <a href="{$system['system_url']}/posts/{$cart_item['post']['post_id']}" class="title">{$cart_item['post']['product']['name']}</a>
                            {if $cart_item['post']['product']['is_digital']}
                              <span class="badge bg-primary">{__("Digital")}</span>
                            {/if}
                            {if $cart_item['post']['product']['status'] == "new"}
                              <span class="badge bg-info">{__("New")}</span>
                            {else}
                              <span class="badge bg-info">{__("Used")}</span>
                            {/if}
                          </div>
                          <div class="mt20">
                            <div class="form-group">
                              <div class="input-group">
                                <span class="input-group-text">{__("Qty:")}</span>
                                <select class="form-select js_shopping-update-cart" name="quantity" data-id="{$cart_item['product_post_id']}">
                                  {for $i=1; $i <= $cart_item['post']['product']['quantity']; $i++}
                                    <option value="{$i}" {if $i == $cart_item['quantity']}selected{/if}>{$i}</option>
                                  {/for}
                                </select>
                              </div>
                            </div>
                          </div>
                          <div>
                            <div class="d-grid">
                              <button type="button" class="btn btn-sm btn-light js_shopping-remove-from-cart" data-id="{$cart_item['product_post_id']}">
                                {include file='__svg_icons.tpl' icon="delete" class="danger-icon" width="18px" height="18px"}
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  {/foreach}
                </div>
              {else}
                <div class="alert alert-info">
                  {__("Your cart is empty.")}
                </div>
              {/if}
            </div>
            <div class="heading-small mb20">
              {__("Shipping Address")}
            </div>
            <div class="pl-md-4">
              {if $addresses}
                {foreach $addresses as $address}
                  <div class="shipping-address">
                    <div class="form-check">
                      <input class="form-check-input js_shipping-address" type="radio" name="shipping_address" id="shipping_address-{$address['address_id']}" value="{$address['address_id']}">
                      <label class="form-check-label" for="shipping_address-{$address['address_id']}">
                        <strong>{$address['address_title']}</strong> ({$address['address_details']}, {$address['address_city']} - {$address['address_country']})
                      </label>
                    </div>
                  </div>
                {/foreach}
                <!-- error -->
                <div class="alert alert-danger mt15 mb0 x-hidden" id="addresses-error">
                  {__("Select a shipping address")}
                </div>
                <!-- error -->
              {else}
                <div class="alert alert-warning">
                  {__("You have no addresses")}
                </div>
              {/if}
            </div>
            <div class="divider"></div>
            {if $user->_data['user_wallet_balance'] < $cart['total']}
              <div class="alert alert-warning mt15 d-block">
                {__("Your wallet balance is insufficient")}. <a href="{$system['system_url']}/wallet">{__("Please add funds to continue")}</a>
              </div>
            {/if}
            <div class="text-end">
              {__("Total Price")}
              <p>
                <span class="text-xxlg">
                  {print_money(number_format($cart['total'], 2), $system['system_currency_symbol'], $system['system_currency_dir'])}
                </span>
              </p>
            </div>
            <div class="divider"></div>
            <div class="text-end">
              <button type="button" class="btn btn-success btn-lg js_shopping-checkout" {if !$cart['items'] || $user->_data['user_wallet_balance'] < $cart['total']}disabled{/if}>
                {__("Checkout")}
              </button>
            </div>
          </div>
          <!-- Shopping Cart -->
        </div>
      </div>
    </div>

  {elseif $view == "orders"}

    <div class="articles-widget-header clearfix">
      <!-- search -->
      <div class="float-end">
        <form action="{$system['system_url']}/market/orders" method="get">
          <input type="text" class="form-control form-control-sm" name="query" placeholder='{__("Search")}'>
        </form>
      </div>
      <!-- search -->
      <div class="articles-widget-title lg">
        {__("Orders")} {if !$query}<span class="badge bg-info rounded-pill">{$orders_count}</span>{/if}
        {if $query}
          <span class="badge bg-secondary rounded-pill">{__("for")}: {$query}</span>
        {/if}
      </div>
    </div>

    {if $orders}
      <ul class="js_orders-stream">
        {foreach $orders as $order}
          {include file='_order.tpl'}
        {/foreach}
      </ul>

      <!-- see-more -->
      {if count($orders) >= $system['max_results']}
        <div class="alert alert-post see-more js_see-more" data-get="orders" data-target-stream=".js_orders-stream" {if $query}data-filter="{$query}" {/if}>
          <span>{__("See More")}</span>
          <div class="loader loader_small x-hidden"></div>
        </div>
      {/if}
      <!-- see-more -->
    {else}
      {include file='_no_data.tpl'}
    {/if}

  {elseif $view == "sales"}

    <div class="row">
      <div class="col-lg-4">
        <div class="stat-panel bg-gradient-primary">
          <div class="stat-cell narrow">
            <i class="fa fa-file-invoice bg-icon"></i>
            <span class="text-xxlg">{$orders_count}</span><br>
            <span class="text-lg">{__("Total Orders")}</span><br>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="stat-panel bg-gradient-info">
          <div class="stat-cell narrow">
            <i class="fa fa-dollar-sign bg-icon"></i>
            <span class="text-xxlg">{print_money($monthly_sales|number_format:2)}</span><br>
            <span class="text-lg">{__("This Month Earnings")}</span><br>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="stat-panel bg-gradient-info">
          <div class="stat-cell narrow">
            <i class="fa fa-dollar-sign bg-icon"></i>
            <span class="text-xxlg">{print_money($user->_data['user_market_balance']|number_format:2)}</span><br>
            <span class="text-lg">{__("Total Earnings")}</span><br>
          </div>
        </div>
      </div>
    </div>

    <div class="articles-widget-header clearfix">
      <!-- search -->
      <div class="float-end">
        <form action="{$system['system_url']}/market/sales" method="get">
          <input type="text" class="form-control form-control-sm" name="query" placeholder='{__("Search")}'>
        </form>
      </div>
      <!-- search -->
      <div class="articles-widget-title lg">
        {__("Sales Orders")}
        {if $query}
          <span class="badge bg-secondary rounded-pill">{__("for")}: {$query}</span>
        {/if}
      </div>
    </div>

    {if $orders}
      <ul class="js_orders-stream">
        {foreach $orders as $order}
          {include file='_order.tpl' sales = true}
        {/foreach}
      </ul>

      <!-- see-more -->
      {if count($orders) >= $system['max_results']}
        <div class="alert alert-post see-more js_see-more" data-get="sales_orders" data-target-stream=".js_orders-stream" {if $query}data-filter="{$query}" {/if}>
          <span>{__("See More")}</span>
          <div class="loader loader_small x-hidden"></div>
        </div>
      {/if}
      <!-- see-more -->
    {else}
      {include file='_no_data.tpl'}
    {/if}

  {/if}

</div>
<!-- page content -->

{include file='_footer.tpl'}