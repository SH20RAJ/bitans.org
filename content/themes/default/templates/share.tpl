{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} mt20">
  <div class="row">

    <!-- content panel -->
    <div class="col-12">
      <!-- publisher -->
      {include file='_publisher.tpl' _handle="me" _node_can_monetize_content=$user->_data['can_monetize_content'] _node_monetization_enabled=$user->_data['user_monetization_enabled'] _node_monetization_plans=$user->_data['user_monetization_plans'] _privacy=true}
      <!-- publisher -->
    </div>
    <!-- content panel -->

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}