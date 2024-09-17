<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="delete_user" class="main-icon mr15" width="24px" height="24px"}{__("Delete Account")}
</div>
<div class="card-body">
  <div class="alert alert-warning">
    <div class="icon">
      <i class="fa fa-exclamation-triangle fa-2x"></i>
    </div>
    <div class="text pt5">
      {__("Once you delete your account you will no longer can access it again")}
    </div>
  </div>

  <div class="text-center">
    <button class="btn btn-danger js_delete-user">
      {__("Delete My Account")}
    </button>
  </div>
</div>