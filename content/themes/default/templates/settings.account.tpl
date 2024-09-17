<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="settings" class="main-icon mr15" width="24px" height="24px"}
  {__("Account Settings")}
</div>
<form class="js_ajax-forms" data-url="users/settings.php?edit=account">
  <div class="card-body">
    <div class="heading-small mb20">
      {__("Email Address")}
    </div>
    <div class="pl-md-4">
      {if !$user->_data['user_email_verified']}
        <div class="alert alert-danger">
          <div class="icon">
            <i class="fa fa-exclamation-circle fa-2x"></i>
          </div>
          <div class="text">
            <strong>{__("Email Verification Required")}</strong><br>
            {__("Check your email inbox")} {__("to complete the verification process")}
            <button class="btn btn-sm btn-success ml10" data-toggle="modal" data-url="core/activation_email_resend.php">{__("Resend Verification Email")}</button>
          </div>
        </div>
      {/if}

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Email Address")}
        </label>
        <div class="col-md-9">
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" name="email" value="{$user->_data['user_email']}">
          </div>
        </div>
      </div>
    </div>

    {if ($system['activation_enabled'] && $system['activation_type'] == "sms") || ($system['two_factor_enabled'] && $system['two_factor_type'] == "sms")}
      <div class="divider"></div>

      <div class="heading-small mb20">
        {__("Phone Number")}
      </div>
      <div class="pl-md-4">
        {if $user->_data['user_phone'] && !$user->_data['user_phone_verified']}
          <div class="alert alert-danger">
            <div class="icon">
              <i class="fa fa-exclamation-circle fa-2x"></i>
            </div>
            <div class="text">
              <strong>{__("Phone Verification Required")}</strong><br>
              {__("Check your phone SMS")} {__("to complete phone verification process")}
              <button class="btn btn-sm btn-success ml10" data-toggle="modal" data-url="#activation-phone">{__("Enter Code")}</button>
            </div>
          </div>
        {/if}

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Phone Number")}
          </label>
          <div class="col-md-9">
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-globe-americas"></i></span>
              <input type="text" class="form-control" name="phone" value="{$user->_data['user_phone']}">
              <span class="input-group-text"><i class="fas fa-phone"></i></span>
            </div>
            <div class="form-text">
              {__("Phone number (e.g: +1234567890)")}
            </div>
          </div>
        </div>
      </div>
    {/if}

    <div class="divider"></div>

    {if $user->_data['user_verified']}
      <div class="alert alert-warning">
        <div class="icon">
          <i class="fa fa-exclamation-triangle fa-2x"></i>
        </div>
        <div class="text">
          <strong>{__("Attention")}</strong><br>
          {__("Your account is already verified if you changed your username you will lose the verification badge")}
        </div>
      </div>
    {/if}

    <div class="heading-small mb20">
      {__("Username")}
    </div>
    <div class="pl-md-4">
      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Username")}
        </label>
        <div class="col-md-9">
          <div class="input-group">
            <span class="input-group-text d-none d-sm-block">{$system['system_url']}/</span>
            <input type="text" class="form-control" name="username" value="{$user->_data['user_name']}">
          </div>
          <div class="form-text">
            {__("Can only contain alphanumeric characters (A–Z, 0–9) and periods ('.')")}
          </div>
        </div>
      </div>
    </div>

    <!-- Secuirty Check -->
    <div class="js_hidden-section x-hidden">
      <div class="divider"></div>
      <div class="heading-small mb20">
        {__("Secuirty Check")}
      </div>
      <div class="pl-md-4">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Current Password")}
          </label>
          <div class="col-md-9">
            <input type="password" class="form-control" name="password">
            <div class="form-text">
              {__("You need to enter your current password for security check")}
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Secuirty Check -->

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