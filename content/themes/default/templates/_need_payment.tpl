<!-- need payment -->
<div class="text-center text-muted">
  {include file='__svg_icons.tpl' icon="locked" class="main-icon mb20" width="56px" height="56px"}

  <div class="text-md">
    <span style="padding: 8px 20px; background: #ececec; border-radius: 18px; font-weight: bold; font-size: 13px;">
      {__("PAID POST")}
    </span>
  </div>
  <div class="d-grid">
    <button class="btn btn-info rounded rounded-pill mt20 {if !$user->_logged_in}js_login{/if}" {if $user->_logged_in}data-toggle="modal" data-url="#payment" data-options='{ "handle": "paid_post", "paid_post": "true", "id": {$post_id}, "price": {$price}, "vat": "{get_payment_vat_value($price)}", "fees": "{get_payment_fees_value($price)}", "total": "{get_payment_total_value($price)}", "total_printed": "{get_payment_total_value($price, true)}" }' {/if}>
      <i class="fa fa-money-check-alt mr5"></i>{__("PAY TO UNLOCK")} ({print_money($price|number_format:2)})
    </button>
    {if $paid_text}
      <div class="post-paid-description rounded">
        {$paid_text}
      </div>
    {/if}
  </div>
</div>
<!-- need payment -->