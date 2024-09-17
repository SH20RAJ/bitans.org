<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="24px" height="24px"}
    {__("Monetization Plans")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="payment-plans">
    {foreach $monetization_plans as $plan}
      <div class="payment-plan">
        <div class="text-xxlg">{__($plan['title'])}</div>
        <div class="text-xlg">{print_money($plan['price'])} / {if $plan['period_num'] != '1'}{$plan['period_num']}{/if} {__($plan['period']|ucfirst)}</div>
        {if {$plan['custom_description']}}
          <div>{$plan['custom_description']}</div>
        {/if}
        <div class="d-grid mt10">
          {if $plan['price'] == 0}
            <button class="btn btn-warning rounded rounded-pill mt20 js_sneak-peak" data-id="{$plan['plan_id']}">
              <i class="fa fa-eye mr5"></i>{__("Sneak Peak")}
            </button>
          {else}
            <button class="btn btn-info rounded rounded-pill mt20" data-toggle="modal" data-url="#payment" data-options='{ "handle": "subscribe", "subscribe": "true", "id": {$plan['plan_id']}, "price": {$plan['price']}, "vat": "{get_payment_vat_value($plan['price'])}", "fees": "{get_payment_fees_value($plan['price'])}", "total": "{get_payment_total_value($plan['price'])}", "total_printed": "{get_payment_total_value($plan['price'], true)}" }'>
              <i class="fa fa-money-check-alt mr5"></i>{__("Subscribe")} ({print_money($plan['price']|number_format:2)})
            </button>
          {/if}
        </div>
      </div>
    {/foreach}
  </div>
</div>