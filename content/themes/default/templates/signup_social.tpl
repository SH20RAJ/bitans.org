{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <img class="floating-img d-none d-md-block" src="{$system['system_url']}/content/themes/{$system['theme']}/images/headers/undraw_product_teardown_elol.svg">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="inner">
    <h2>{__("Getting Started")}</h2>
    <p class="text-xlg">{__("This information will let us know more about you")}</p>
  </div>
</div>
<!-- page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if}" style="margin-top: -25px;">
  <div class="row">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5 mx-md-auto">
      <div class="card px-4 py-4 shadow">
        <h3 class="mb20 text-center">{__("Welcome")} <span class="text-primary">{$user_profile->displayName}</span></h3>
        <div class="text-center">
          <img class="img-thumbnail rounded-circle" src="{$user_profile->photoURL}" width="99" height="99">
        </div>
        <form class="js_ajax-forms" data-url="core/signup_social.php">
          {if $system['invitation_enabled']}
            <div class="form-group">
              <label class="form-label">{__("Invitation Code")}</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-handshake fa-fw"></i></span>
                <input name="invitation_code" type="text" class="form-control" required autofocus>
              </div>
            </div>
          {/if}
          <div class="form-group">
            <label class="form-label">{__("First name")}</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user fa-fw"></i></span>
              <input name="first_name" type="text" class="form-control" value="{$user_profile->firstName}" required autofocus>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">{__("Last name")}</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user fa-fw"></i></span>
              <input name="last_name" type="text" class="form-control" value="{$user_profile->lastName}" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">{__("Username")}</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-globe fa-fw"></i></span>
              <input name="username" type="text" class="form-control" value="{$user_profile->username}" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">{__("Email")}</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-envelope fa-fw"></i></span>
              <input name="email" type="email" class="form-control" value="{$user_profile->email}" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">{__("Password")}</label>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-key fa-fw"></i></span>
                <input name="password" type="password" class="form-control" required>
              </div>
            </div>
          </div>
          {if !$system['genders_disabled']}
            <div class="form-group">
              <label class="form-label">{__("I am")}</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-mars fa-fw"></i></span>
                <select class="form-select" name="gender" required>
                  <option value="none">{__("Select Sex")}:</option>
                  {foreach $genders as $gender}
                    <option value="{$gender['gender_id']}">{$gender['gender_name']}</option>
                  {/foreach}
                </select>
              </div>
            </div>
          {/if}
          <!-- newsletter consent -->
          {if $system['newsletter_consent']}
            <div class="form-check mb10">
              <input type="checkbox" class="form-check-input" name="newsletter_agree" id="newsletter_agree">
              <label class="form-check-label" for="newsletter_agree">
                {__("I expressly agree to receive the newsletter")}
              </label>
            </div>
          {/if}
          <!-- newsletter consent -->
          <div class="form-check mb10">
            <input type="checkbox" class="form-check-input" name="privacy_agree" id="privacy_agree">
            <label class="form-check-label" for="privacy_agree">
              {__("By creating your account, you agree to our")} <a href="{$system['system_url']}/static/terms" target="_blank">{__("Terms")}</a> & <a href="{$system['system_url']}/static/privacy" target="_blank">{__("Privacy Policy")}</a>
            </label>
          </div>
          <div class="d-grid form-group">
            <input value="{$user_profile->photoURL}" name="avatar" type="hidden">
            <input value="{$provider}" name="provider" type="hidden">
            <button type="submit" class="btn btn-success bg-gradient-green border-0 rounded-pill">{__("Sign Up")}</button>
          </div>
          <!-- error -->
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
          <!-- error -->
        </form>
      </div>
    </div>
  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}