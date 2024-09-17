<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="verification" class="main-icon mr15" width="24px" height="24px"}{__("Verification")}
</div>
{if $case == "verified"}
  <div class="card-body">
    <div class="text-center">
      {include file='__svg_icons.tpl' icon="verification" class="main-icon mb10" width="60px" height="60px"}
      <h4>{__("Congratulations")}</h4>
      <p class="mt20">{__("This account is verified")}</p>
    </div>
  </div>
{elseif $case == "pending"}
  <div class="card-body">
    <div class="text-center">
      {include file='__svg_icons.tpl' icon="pending" class="main-icon mb10" width="60px" height="60px"}
      <h4>{__("Pending")}</h4>
      <p class="mt20">{__("Your verification request is still awaiting admin approval")}</p>
    </div>
  </div>
{elseif $case == "request" || "declined"}
  <form class="js_ajax-forms" data-url="users/verify.php?node=user">
    <div class="card-body">
      {if $case == "declined"}
        <div class="text-center">
          {include file='__svg_icons.tpl' icon="declined" class="main-icon mb10" width="60px" height="60px"}
          <h4>{__("Sorry")}</h4>
          <p class="mt20">{__("Your verification request has been declined by the admin")}</p>
        </div>
        <div class="divider"></div>
      {/if}

      {if $system['verification_docs_required']}
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Verification Documents")}
          </label>
          <div class="col-md-9">
            <div class="row">
              <div class="col-sm-6">
                <div class="section-title mb20">
                  {__("Your Photo")}
                </div>
                <div class="x-image full">
                  <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

                  </button>
                  <div class="x-image-loader">
                    <div class="progress x-progress">
                      <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <i class="fa fa-camera fa-2x js_x-uploader" data-handle="x-image"></i>
                  <input type="hidden" class="js_x-image-input" name="photo" value="">
                </div>

              </div>
              <div class="col-sm-6">
                <div class="section-title mb20">
                  {__("Passport or National ID")}
                </div>
                <div class="x-image full">
                  <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

                  </button>
                  <div class="x-image-loader">
                    <div class="progress x-progress">
                      <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <i class="fa fa-camera fa-2x js_x-uploader" data-handle="x-image"></i>
                  <input type="hidden" class="js_x-image-input" name="passport" value="">
                </div>
              </div>
            </div>
            <div class="form-text">
              {__("Please attach your photo and your Passport or National ID")}
            </div>
          </div>
        </div>
      {/if}

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Additional Information")}
        </label>
        <div class="col-md-9">
          <textarea class="form-control" name="message"></textarea>
          <div class="form-text">
            {__("Please share why your account should be verified")}
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
    <div class="card-footer text-end">
      <button type="submit" class="btn btn-primary">
        {__("Send")}
      </button>
    </div>
  </form>
{/if}