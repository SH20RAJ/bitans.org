<div class="modal-header">
  <h6 class="modal-title">
    <i class="fa fa-share mr5"></i>{__("Share Invitation Code")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="users/invitations.php?do=send">
  <div class="modal-body">
    <div class="text-center">
      <div class="text-xlg">
        {__("Your invitation code is")}
      </div>
      <h3>
        <span class="badge bg-warning">{$code}</span>
      </h3>
    </div>

    <div class="divider"></div>

    <div class="h5 text-center">
      {__("Share the code to")}
    </div>

    <div class="post-social-share">
      {include file='__social_share.tpl' _link="{$system['system_url']}/signup?invitation_code={$code}"}
    </div>

    <!-- send method -->
    <div class="mb20 text-center">
      {if $system['invitation_send_method'] == "email" || $system['invitation_send_method'] == "both"}
        <!-- Email -->
        <input class="x-hidden input-label" type="radio" name="send_method" id="send_method_email" value="email" checked="checked" />
        <label class="button-label" for="send_method_email">
          <div class="icon">
            {include file='__svg_icons.tpl' icon="email" class="main-icon" width="32px" height="32px"}
          </div>
          <div class="title">{__("Email")}</div>
        </label>
        <!-- Email -->
      {/if}

      {if $system['invitation_send_method'] == "sms" || $system['invitation_send_method'] == "both"}
        <!-- SMS -->
        <input class="x-hidden input-label" type="radio" name="send_method" id="send_method_sms" value="sms" />
        <label class="button-label" for="send_method_sms">
          <div class="icon">
            {include file='__svg_icons.tpl' icon="sms" class="main-icon" width="32px" height="32px"}
          </div>
          <div class="title">{__("SMS")}</div>
        </label>
        <!-- SMS -->
      {/if}
    </div>
    <!-- send method -->

    <div id="js_method-email" {if $system['invitation_send_method'] == "sms"}class="x-hidden" {/if}>
      <div class="form-group">
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-envelope"></i></span>
          <input type="email" class="form-control" name="email">
        </div>
      </div>
    </div>

    <div id="js_method-sms" class="x-hidden">
      <div class="form-group">
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-globe-americas"></i></span>
          <input type="text" class="form-control" name="phone">
          <span class="input-group-text"><i class="fas fa-phone"></i></span>
        </div>
        <div class="form-text">
          {__("Phone number i.e +1234567890")}
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
  <div class="modal-footer">
    <input type="hidden" name="code" value="{$code}">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
    <button type="submit" class="btn btn-primary">{__("Send")}</button>
  </div>
</form>

<script>
  /* share post */
  $('input[type=radio][name=send_method]').on('change', function() {
    switch ($(this).val()) {
      case 'email':
        $('#js_method-sms').hide();
        $('#js_method-email').fadeIn();
        break;
      case 'sms':
        $('#js_method-email').hide();
        $('#js_method-sms').fadeIn();
        break;
    }
  });
</script>