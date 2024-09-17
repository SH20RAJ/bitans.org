{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page content -->
<div class="container mt30">
  <div class="row">
    <div class="col-md-6 col-lg-5 mx-md-auto">
      <!-- reset form -->
      <div class="card card-register">
        <div class="card-header">
          <h4 class="card-title">{__("Forgot your password?")}</h4>
          <p class="card-subtitle">{__("Enter the email address associated with your account and we will send you a link to reset your password.")}</p>
        </div>
        <div class="card-body pt0">
          <form class="js_ajax-forms" data-url="core/forget_password.php">
            <!-- email -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="fas fa-envelope fa-fw"></i></span>
                <input name="email" type="email" class="form-control" placeholder='{__("Email")}' required>
              </div>
            </div>
            <!-- email -->

            {if $system['reCAPTCHA_enabled']}
              <div class="form-group">
                <!-- reCAPTCHA -->
                <script src='https://www.google.com/recaptcha/api.js' async defer></script>
                <div class="g-recaptcha" data-sitekey="{$system['reCAPTCHA_site_key']}"></div>
                <!-- reCAPTCHA -->
              </div>
            {/if}

            <div class="d-grid form-group">
              <input type="hidden" name="secret" value="{$secret}">
              <button type="submit" class="btn btn-primary">
                {__("Continue")}
              </button>
            </div>

            <!-- error -->
            <div class="alert alert-danger x-hidden"></div>
            <!-- error -->
          </form>
          <div class="mt20 text-center">
            <a href="{$system['system_url']}/signin" class="text-link">{__("Back to Signin")}</a>
          </div>
          <div class="mt20 text-center">
            {__("Do not have an account?")} <a href="{$system['system_url']}/signup" class="text-link">{__("Sign Up")}</a>
          </div>
        </div>
      </div>
      <!-- reset form -->
    </div>
  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}