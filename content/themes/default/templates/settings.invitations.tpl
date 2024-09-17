<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="invitation" class="main-icon mr15" width="24px" height="24px"}
  {__("Invitations")}
  <div class="float-end">
    <button data-toggle="modal" data-url="users/invitations.php?do=generate" class="btn btn-md btn-primary">
      <i class="fa fa-plus-circle"></i><span class="ml5 d-none d-lg-inline-block">{__("Generate New Code")}</span>
    </button>
  </div>
</div>
<div class="card-body">
  <div class="alert alert-info">
    <div class="text">
      <strong>{__("Invitations System")}</strong><br>
      {__("You have")} <strong><span class="badge rounded-pill bg-danger">{if $system['invitation_user_limit'] == 0}{__("Unlimited")}{else}{$system['invitation_user_limit']}{/if}</span></strong> {__("invitations")} {if $system['invitation_user_limit'] != 0}{__("every")} {__($system['invitation_expire_period']|ucfirst)}{/if}
      <br>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-4">
      <div class="stat-panel border">
        <div class="stat-cell">
          <i class="fa fa-mail-bulk icon bg-gradient-success"></i>
          <span class="text-xxlg">{if $system['invitation_user_limit'] == 0}<i class="fas fa-infinity"></i>{else}{$invitation_codes_stats['available']}{/if}</span><br>
          <span>{__("Available Invitations")}</span>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="stat-panel border">
        <div class="stat-cell">
          <i class="fa fa-envelope icon bg-gradient-primary"></i>
          <span class="text-xxlg">{$invitation_codes_stats['generated']}</span><br>
          <span>{__("Generated Invitations")}</span>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="stat-panel border">
        <div class="stat-cell">
          <i class="fa fa-envelope-open-text icon bg-gradient-danger"></i>
          <span class="text-xxlg">{$invitation_codes_stats['used']}</span><br>
          <span>{__("Used Invitations")}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="section-title mb20">
    {__("Your Invitations Codes")}
  </div>
  {if $invitation_codes}
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{__("Invitation Code")}</th>
            <th>{__("Created")}</th>
            <th>{__("Used")}</th>
            <th>{__("Used By")}</th>
            <th>{__("Actions")}</th>
          </tr>
        </thead>
        <tbody>
          {foreach $invitation_codes as $invitation_code}
            <tr>
              <td><span class="badge rounded-pill badge-lg bg-secondary">{$invitation_code['code']}</span></td>
              <td>{$invitation_code['created_date']|date_format:"%e %B %Y"}</td>
              <td>
                {if $invitation_code['used']}
                  <span class="badge rounded-pill badge-lg bg-danger">{__("Yes")}</span>
                {else}
                  <span class="badge rounded-pill badge-lg bg-success">{__("No")}</span>
                {/if}
              </td>
              <td>
                {if $invitation_code['used']}
                  <a target="_blank" href="{$system['system_url']}/{$invitation_code['user_name']}">
                    <img class="tbl-image" src="{$invitation_code['user_picture']}">
                    {$invitation_code['user_firstname']} {$invitation_code['user_lastname']}
                  </a>
                {/if}
              </td>
              <td>
                {if !$invitation_code['used']}
                  <div class="d-inline-block" data-bs-toggle="tooltip" title='{__("Share")}'>
                    <button data-toggle="modal" data-url="users/invitations.php?do=share&code={$invitation_code['code']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                      <i class="fa fa-link"></i>
                    </button>
                  </div>
                {/if}
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  {else}
    {include file='_no_data.tpl'}
  {/if}
</div>