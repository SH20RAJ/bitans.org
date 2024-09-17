<div class="modal-header">
  <h6 class="modal-title">
    {__("View Order")} #{$order['order_hash']}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  {include file='_order.tpl' for_admin=true sales=true}
</div>