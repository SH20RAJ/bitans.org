{if $sub_view == "password"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="security" class="main-icon mr15" width="24px" height="24px"}
    {__("Change Password")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=password">
    <div class="card-body">
      <div class="alert alert-warning">
        <div class="icon">
          <i class="fa fa-exclamation-triangle fa-2x"></i>
        </div>
        <div class="text pt5">
          {__("Changing password will log you out from all other sessions")}
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">{__("Confirm Current Password")}</label>
        <input type="password" class="form-control" name="current">
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Your New Password")}</label>
          <input type="password" class="form-control" name="new">
        </div>
        <div class="form-group col-md-6">
          <label class="form-label">{__("Confirm New Password")}</label>
          <input type="password" class="form-control" name="confirm">
        </div>
      </div>

      <!-- success -->
      <div class="alert alert-success mt15 mb0 x-hidden"></div>
      <!-- success -->

      <!-- error -->
      <div class="alert alert-danger mt15 mb0 x-hidden"></div>
      <!-- error -->
    </div>
    <div class="card-footer text-end">
      <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
    </div>
  </form>

{elseif $sub_view == "sessions"}
  <div class="card-header with-icon">
    <div class="float-end">
      <button class="btn btn-md btn-danger js_session-delete-all">
        <i class="fa fa-sign-out-alt"></i><span class="ml5 d-none d-lg-inline-block">{__("Log Out Of All Sessions")}</span>
      </button>
    </div>
    {include file='__svg_icons.tpl' icon="security" class="main-icon mr15" width="24px" height="24px"}{__("Manage Sessions")}
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>{__("ID")}</th>
            <th>{__("Browser")}</th>
            <th>{__("OS")}</th>
            <th>{__("Date")}</th>
            <th>{__("IP")}</th>
            <th>{__("Actions")}</th>
          </tr>
        </thead>
        <tbody>
          {if $sessions}
            {foreach $sessions as $session}
              <tr>
                <td>{$session@iteration}</td>
                <td>
                  {$session['user_browser']} {if $session['session_id'] == $user->_data['active_session_id']}<span class="badge rounded-pill badge-lg bg-success">{__("Active Session")}</span>{/if}
                </td>
                <td>{$session['user_os']}</td>
                <td>
                  <span class="js_moment" data-time="{$session['session_date']}">{$session['session_date']}</span>
                </td>
                <td>{$session['user_ip']}</td>
                <td>
                  <button data-bs-toggle="tooltip" title='{__("End Session")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_session-deleter" data-id="{$session['session_id']}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
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
  </div>

{elseif $sub_view == "two-factor"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="security" class="main-icon mr15" width="24px" height="24px"}{__("Two-Factor Authentication")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=two-factor">
    <div class="card-body">
      <div class="alert alert-info">
        <div class="text">
          <strong>{__("Two-Factor Authentication")}</strong><br>
          {__("Log in with a code from your")}
          {if $system['two_factor_type'] == "email"}{__("email")}{/if}
          {if $system['two_factor_type'] == "sms"}{__("phone")}{/if}
          {if $system['two_factor_type'] == "google"}{__("Google Authenticator App")}{/if}
          {__("as well as a password")}
        </div>
      </div>
      {if !$user->_data['user_two_factor_enabled'] && $system['two_factor_type'] == "google"}
        <div class="heading-small mb20">
          {__("Configuring your authenticator")}
        </div>
        <div class="pl-md-4">
          <ol class="mtb20">
            <li class="mb5">
              {__("You need to download Google Authenticator app for")} <a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en">{__("Android")}</a> {__("or")} <a target="_blank" href="https://itunes.apple.com/eg/app/google-authenticator/id388497605?mt=8">{__("IOS")}</a>
            </li>
            <li>
              {__("In your app, add a new account using the details below")}:
            </li>
          </ol>

          <div class="row text-center">
            <div class="form-group col-md-6">
              <h6>{__("Scanning the QR code")}</h6>
              <img src="{$two_factor_QR}">
            </div>
            <div class="form-group col-md-6">
              <h6>{__("Manually by entering this token")}</h6>
              <h3>
                <span class="badge bg-warning pt10 plr20">{$two_factor_gsecret}</span>
              </h3>
            </div>
          </div>
        </div>
        <div class="heading-small mb20">
          {__("Activate your authenticator")}
        </div>
        <div class="pl-md-4">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Verification Code")}
            </label>
            <div class="col-md-9">
              <input name="gcode" type="text" class="form-control" placeholder="######" required autofocus>
              <div class="form-text">
                {__("Enter the code shown on your app")}
              </div>
            </div>
          </div>

          <!-- success -->
          <div class="alert alert-success mt15 mb0 x-hidden"></div>
          <!-- success -->

          <!-- error -->
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
          <!-- error -->
        </div>
      {else}
        <div class="form-table-row">
          <div>
            <div class="form-label h6">{__("Two-Factor Authentication")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable two-factor authentication to log in with a code from your email/phone as well as a password")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="two_factor_enabled">
              <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled" {if $user->_data['user_two_factor_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      {/if}
    </div>
    <div class="card-footer text-end">
      <input type="hidden" name="type" value="{$system['two_factor_type']}">
      <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
    </div>
  </form>

{/if}