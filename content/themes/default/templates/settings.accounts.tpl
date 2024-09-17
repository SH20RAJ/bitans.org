<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="user_add" class="main-icon mr15" width="24px" height="24px"}
  {__("Connected Accounts")}
  {if $user->_data['user_id'] == $user->_data['user_master_account']}
    <div class="float-end">
      <button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-url="#account-connector">
        <i class="fa fa-plus-circle"></i><span class="ml5 d-none d-lg-inline-block">{__("Connect Account")}</span>
      </button>
    </div>
  {/if}
</div>
<div class="card-body">
  {if count($user->_data['connected_accounts']) > 1}
    {if $user->_data['user_id'] == $user->_data['user_master_account']}
      <ul>
        {foreach $user->_data['connected_accounts'] as $_user}
          {if $_user['user_id'] != $user->_data['user_id']}
            {include file='__feeds_user.tpl' _tpl="list" _connection="connected_account_remove"}
          {/if}
        {/foreach}
      </ul>
    {else}
      <div class="alert alert-info">
        {__("You are connected to this account")}
      </div>
      <ul>
        {foreach $user->_data['connected_accounts'] as $_user}
          {if $_user['user_id'] == $user->_data['user_master_account']}
            {include file='__feeds_user.tpl' _tpl="list" _connection="connected_account_revoke"}
          {/if}
        {/foreach}
      </ul>
    {/if}
  {else}
    {include file='_no_data.tpl'}
  {/if}
</div>