<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == ""}
      <div class="float-end">
        <!-- Export CSV -->
        <button type="button" class="btn btn-md btn-success" data-toggle="modal" data-url="#export-csv" data-options='{ "handle": "earnings" }'>
          <i class="fa-solid fa-file-csv"></i><span class="ml5 d-none d-lg-inline-block">{__("Export CSV")}</span>
        </button>
        <!-- Export CSV -->
      </div>
    {elseif $sub_view == "commissions"}
      <div class="float-end">
        <!-- Export CSV -->
        <button type="button" class="btn btn-md btn-success" data-toggle="modal" data-url="#export-csv" data-options='{ "handle": "commissions" }'>
          <i class="fa-solid fa-file-csv"></i><span class="ml5 d-none d-lg-inline-block">{__("Export CSV")}</span>
        </button>
        <!-- Export CSV -->
      </div>
    {elseif $sub_view == "movies"}
      <div class="float-end">
        <!-- Export CSV -->
        <button type="button" class="btn btn-md btn-success" data-toggle="modal" data-url="#export-csv" data-options='{ "handle": "movies" }'>
          <i class="fa-solid fa-file-csv"></i><span class="ml5 d-none d-lg-inline-block">{__("Export CSV")}</span>
        </button>
        <!-- Export CSV -->
      </div>
    {/if}
    <i class="fa fa-chart-line mr5"></i>{__("Earnings")}
    {if $sub_view == ""} &rsaquo; {__("Payments")}{/if}
    {if $sub_view == "commissions"} &rsaquo; {__("Commissions")}{/if}
    {if $sub_view == "packages"} &rsaquo; {__("Packages")}{/if}
    {if $sub_view == "movies"} &rsaquo; {__("Movies")}{/if}
  </div>

  {if $sub_view == ""}

    <div class="card-body">
      <div class="row">
        <div class="col-lg-6">
          <div id="payment-methods-chart" class="admin-chart mb20"></div>
        </div>
        <div class="col-lg-6">
          <div id="payment-handles-chart" class="admin-chart mb20"></div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-primary">
            <div class="stat-cell narrow">
              <i class="fa fa-donate bg-icon"></i>
              <span class="text-xxlg">{print_money($total_payin|number_format:2)}</span><br>
              <span class="text-lg">{__("Total PayIn")}</span><br>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-info">
            <div class="stat-cell narrow">
              <i class="fa fa-donate bg-icon"></i>
              <span class="text-xxlg">{print_money($month_payin|number_format:2)}</span><br>
              <span class="text-lg">{__("This Month PayIn")}</span><br>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-red">
            <div class="stat-cell narrow">
              <i class="fa fa-hourglass-half bg-icon"></i>
              <span class="text-xxlg">{print_money($total_pending_payout|number_format:2)}</span><br>
              <span class="text-lg">{__("Total Pedning PayOut")}</span><br>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-warning">
            <div class="stat-cell narrow">
              <i class="fa fa-hourglass-half bg-icon"></i>
              <span class="text-xxlg">{print_money($month_pending_payout|number_format:2)}</span><br>
              <span class="text-lg">{__("This Month Pedning PayOut")}</span><br>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-cyan">
            <div class="stat-cell narrow">
              <i class="fa fa-money-bill-trend-up bg-icon"></i>
              <span class="text-xxlg">{print_money($total_approved_payout|number_format:2)}</span><br>
              <span class="text-lg">{__("Total Approved PayOut")}</span><br>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-success">
            <div class="stat-cell narrow">
              <i class="fa fa-money-bill-trend-up bg-icon"></i>
              <span class="text-xxlg">{print_money($month_approved_payout|number_format:2)}</span><br>
              <span class="text-lg">{__("This Month Approved PayOut")}</span><br>
            </div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Payer")}</th>
              <th>{__("Amount")}</th>
              <th>{__("Via")}</th>
              <th>{__("Type")}</th>
              <th>{__("Date")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>{$row['payment_id']}</td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                      <img class="tbl-image" src="{$row['user_picture']}">
                      {$row['user_firstname']} {$row['user_lastname']}
                    </a>
                  </td>
                  <td>
                    {print_money(number_format($row['amount']))}
                  </td>
                  <td>
                    <span class="badge rounded-pill badge-lg bg-primary">{$row['method']|capitalize}</span>
                  </td>
                  <td>
                    <span class="badge rounded-pill badge-lg bg-info">{$row['handle']|capitalize}</span>
                  </td>
                  <td>
                    {$row['time']}
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

  {elseif $sub_view == "commissions"}

    <div class="card-body">
      <div id="commissions-chart" class="admin-chart mb20"></div>

      <div class="row">
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-primary">
            <div class="stat-cell narrow">
              <i class="fa fa-dollar-sign bg-icon"></i>
              <span class="text-xxlg">{print_money($total_commissions|number_format:2)}</span><br>
              <span class="text-lg">{__("Total Commissions")}</span><br>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-info">
            <div class="stat-cell narrow">
              <i class="fa fa-dollar-sign bg-icon"></i>
              <span class="text-xxlg">{print_money($month_commissions|number_format:2)}</span><br>
              <span class="text-lg">{__("This Month Commissions")}</span><br>
            </div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("From")}</th>
              <th>{__("Amount")}</th>
              <th>{__("Type")}</th>
              <th>{__("Date")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>{$row['payment_id']}</td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                      <img class="tbl-image" src="{$row['user_picture']}">
                      {$row['user_firstname']} {$row['user_lastname']}
                    </a>
                  </td>
                  <td>
                    {print_money(number_format($row['amount']))}
                  </td>
                  <td>
                    <span class="badge rounded-pill badge-lg bg-info">{$row['handle']|capitalize}</span>
                  </td>
                  <td>
                    {$row['time']}
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

  {elseif $sub_view == "packages"}

    <div class="card-body">
      <div id="admin-chart-earnings" class="admin-chart mb20"></div>

      <div class="row">
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-primary">
            <div class="stat-cell narrow">
              <i class="fa fa-dollar-sign bg-icon"></i>
              <span class="text-xxlg">{print_money($total_earnings|number_format:2)}</span><br>
              <span class="text-lg">{__("Total Earnings")}</span><br>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-info">
            <div class="stat-cell narrow">
              <i class="fa fa-dollar-sign bg-icon"></i>
              <span class="text-xxlg">{print_money($month_earnings|number_format:2)}</span><br>
              <span class="text-lg">{__("This Month Earnings")}</span><br>
            </div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("Package")}</th>
              <th>{__("Total Sales")}</th>
              <th>{__("Total Earnings")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $key => $value}
              <tr>
                <td>{$key}</td>
                <td>{$value['sales']}</td>
                <td>{print_money($value['earnings']|number_format:2)}</td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "movies"}

    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-primary">
            <div class="stat-cell narrow">
              <i class="fa fa-dollar-sign bg-icon"></i>
              <span class="text-xxlg">{print_money($total_earnings|number_format:2)}</span><br>
              <span class="text-lg">{__("Total Earnings")}</span><br>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="stat-panel bg-gradient-info">
            <div class="stat-cell narrow">
              <i class="fa fa-dollar-sign bg-icon"></i>
              <span class="text-xxlg">{print_money($month_earnings|number_format:2)}</span><br>
              <span class="text-lg">{__("This Month Earnings")}</span><br>
            </div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("User")}</th>
              <th>{__("Movie")}</th>
              <th>{__("Price")}</th>
              <th>{__("Date")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>{$row['id']}</td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/{$row['user_name']}">
                      <img class="tbl-image" src="{$row['user_picture']}">
                      {$row['user_firstname']} {$row['user_lastname']}
                    </a>
                  </td>
                  <td>
                    <a target="_blank" href="{$system['system_url']}/movie/{$row['movie_id']}/{$row['movie_url']}">
                      <img class="tbl-image" src="{$row['poster']}">
                      {$row['title']}
                    </a>
                  </td>
                  <td>{print_money(number_format($row['price']))}</td>
                  <td>{$row['payment_time']}</td>
                </tr>
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
      {$pager}
    </div>

  {/if}
</div>