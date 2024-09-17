<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="notifications" class="main-icon mr15" width="24px" height="24px"}
  {__("Notifications")}
</div>
<form class="js_ajax-forms" data-url="users/settings.php?edit=notifications">
  <div class="card-body">
    <!-- System Notifications -->
    <div class="heading-small mb20">
      {__("System Notifications")}
    </div>
    <div class="pl-md-4">
      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="chat_bell" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Chat Message Sound")}</div>
          <div class="form-text d-none d-sm-block">{__("A sound will be played each time you receive a new message on an inactive chat window")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="chat_sound_settings">
            <input type="checkbox" name="chat_sound" id="chat_sound_settings" {if $user->_data['chat_sound']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="notification_bell" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Notifications Sound")}</div>
          <div class="form-text d-none d-sm-block">{__("A sound will be played each time you receive a new activity notification")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="notifications_sound_settings">
            <input type="checkbox" name="notifications_sound" id="notifications_sound_settings" {if $user->_data['notifications_sound']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>
    </div>
    <!-- System Notifications -->

    <!-- Email Notifications -->
    {if $email_user_notifications_enabled || $email_admin_notifications_enabled}
      <div class="divider"></div>
      <div class="heading-small mb20">
        {__("Email Notifications")}
      </div>
      <div class="pl-md-4">
        <div class="row form-group">
          <label class="col-md-2 form-label">{__("Email Me When")}</label>
          <div class="col-md-10">
            {if $system['email_post_likes']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_post_likes" id="email_post_likes" {if $user->_data['email_post_likes']}checked{/if}>
                <label class="form-check-label" for="email_post_likes">{__("Someone reacted to my post")}</label>
              </div>
            {/if}
            {if $system['email_post_comments']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_post_comments" id="email_post_comments" {if $user->_data['email_post_comments']}checked{/if}>
                <label class="form-check-label" for="email_post_comments">{__("Someone commented on my post")}</label>
              </div>
            {/if}
            {if $system['email_post_shares']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_post_shares" id="email_post_shares" {if $user->_data['email_post_shares']}checked{/if}>
                <label class="form-check-label" for="email_post_shares">{__("Someone shared my post")}</label>
              </div>
            {/if}
            {if $system['email_wall_posts']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_wall_posts" id="email_wall_posts" {if $user->_data['email_wall_posts']}checked{/if}>
                <label class="form-check-label" for="email_wall_posts">{__("Someone posted on my timeline")}</label>
              </div>
            {/if}
            {if $system['email_mentions']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_mentions" id="email_mentions" {if $user->_data['email_mentions']}checked{/if}>
                <label class="form-check-label" for="email_mentions">{__("Someone mentioned me")}</label>
              </div>
            {/if}
            {if $system['email_profile_visits']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_profile_visits" id="email_profile_visits" {if $user->_data['email_profile_visits']}checked{/if}>
                <label class="form-check-label" for="email_profile_visits">{__("Someone visited my profile")}</label>
              </div>
            {/if}
            {if $system['email_friend_requests']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_friend_requests" id="email_friend_requests" {if $user->_data['email_friend_requests']}checked{/if}>
                <label class="form-check-label" for="email_friend_requests">{__("Someone sent me/accepted my friend requset")}</label>
              </div>
            {/if}
            {if $system['verification_requests'] && $system['email_user_verification']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_user_verification" id="email_user_verification" {if $user->_data['email_user_verification']}checked{/if}>
                <label class="form-check-label" for="email_user_verification">{__("Admin approved/declined my verification requests")}</label>
              </div>
            {/if}
            {if $system['posts_approval_enabled'] && $system['email_user_post_approval']}
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="email_user_post_approval" id="email_user_post_approval" {if $user->_data['email_user_post_approval']}checked{/if}>
                <label class="form-check-label" for="email_user_post_approval">{__("Admin approved my pending posts")}</label>
              </div>
            {/if}
            {if $email_admin_notifications_enabled && ($user->_is_admin || $user->_is_moderator)}
              {if $email_user_notifications_enabled}<div class="divider dashed"></div>{/if}
              {if $system['email_admin_verifications']}
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_admin_verifications" id="email_admin_verifications" {if $user->_data['email_admin_verifications']}checked{/if}>
                  <label class="form-check-label" for="email_admin_verifications">{__("Verification request sent by user/page")}</label>
                </div>
              {/if}
              {if $system['email_admin_post_approval']}
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_admin_post_approval" id="email_admin_post_approval" {if $user->_data['email_admin_post_approval']}checked{/if}>
                  <label class="form-check-label" for="email_admin_post_approval">{__("Post published and needs approval")}</label>
                </div>
              {/if}
            {/if}
          </div>
        </div>
      </div>
    {/if}
    <!-- Email Notifications -->

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