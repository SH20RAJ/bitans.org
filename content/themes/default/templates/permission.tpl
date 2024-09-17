{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} mt20 sg-offcanvas">
  <div class="row">

    <!-- side panel -->
    <div class="col-12 d-block d-sm-none sg-offcanvas-sidebar">
      {include file='_sidebar.tpl'}
    </div>
    <!-- side panel -->

    <!-- content panel -->
    <div class="col-12 sg-offcanvas-mainbar">
      <div class="notfound-wrapper">
        <div class="notfound">
          <div class="notfound-circle">
            <i class="far fa-frown"></i>
          </div>
          <h1>{__("Oops!")}</h1>
          <h2>{__("Permission Needed")}</h2>
          <p class="mt10">{$message}</p>
          <a class="btn btn-primary rounded-pill" href="{$system['system_url']}">{__("Back to homepage")}</a>
        </div>
      </div>
    </div>
    <!-- content panel -->

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}