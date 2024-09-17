<div class="card-header with-icon">
  {include file='__svg_icons.tpl' icon="membership" class="main-icon mr15" width="24px" height="24px"}
  {__("Membership")}
</div>
<div class="card-body">
  <div class="alert alert-info">
    <div class="text">
      <strong>{__("Membership")}</strong><br>
      {__("Choose the Plan That's Right for You")}, {__("Check the package from")} <a class="alert-link" href="{$system['system_url']}/packages">{__("Here")}</a>
    </div>
  </div>

  {if $user->_data['user_subscribed']}
    <div class="heading-small mb20">
      {__("Package Details")}
    </div>
    <div class="pl-md-4">
      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Package")}
        </label>
        <div class="col-md-9">
          <p class="form-control-plaintext">
            {__($user->_data['package_name'])} ({print_money($user->_data['price'])}
            {if $user->_data['period'] == "life"}{__("Life Time")}{else}{__("per")} {if $user->_data['period_num'] != '1'}{$user->_data['period_num']}{/if} {__($user->_data['period']|ucfirst)}{/if})
          </p>
        </div>
      </div>
      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Subscription Date")}
        </label>
        <div class="col-md-9">
          <p class="form-control-plaintext">
            {$user->_data['user_subscription_date']|date_format:"%e %B %Y"}
          </p>
        </div>
      </div>
      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Expiration Date")}
        </label>
        <div class="col-md-9">
          <p class="form-control-plaintext">
            {if $user->_data['period'] == "life"}
              {__("Life Time")}
            {else}
              {$user->_data['subscription_end']|date_format:"%e %B %Y"} ({if $user->_data['subscription_timeleft'] > 0}{__("Remaining")} {$user->_data['subscription_timeleft']} {__("Days")}{else}{__("Expired")}{/if})
            {/if}
          </p>
        </div>
      </div>
      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Boosted Posts")}
        </label>
        <div class="col-md-9">
          <p class="form-control-plaintext">
            {$user->_data['user_boosted_posts']}/{$user->_data['boost_posts']} (<a href="{$system['system_url']}/boosted/posts">{__("Manage")}</a>)
          </p>

          <div class="progress mb5">
            <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="{if $user->_data['boost_posts'] == 0}0{else}{($user->_data['user_boosted_posts']/$user->_data['boost_posts'])*100}{/if}" aria-valuemin="0" aria-valuemax="100" style="width: {if $user->_data['boost_posts'] == 0}0{else}{($user->_data['user_boosted_posts']/$user->_data['boost_posts'])*100}{/if}%"></div>
          </div>
        </div>
      </div>
      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Boosted Pages")}
        </label>
        <div class="col-md-9">
          <p class="form-control-plaintext">
            {$user->_data['user_boosted_pages']}/{$user->_data['boost_pages']} (<a href="{$system['system_url']}/boosted/pages">{__("Manage")}</a>)
          </p>

          <div class="progress mb5">
            <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{if $user->_data['boost_pages'] == 0}0{else}{($user->_data['user_boosted_pages']/$user->_data['boost_pages'])*100}{/if}" aria-valuemin="0" aria-valuemax="100" style="width: {if $user->_data['boost_pages'] == 0}0{else}{($user->_data['user_boosted_pages']/$user->_data['boost_pages'])*100}{/if}%"></div>
          </div>
        </div>
      </div>

      {if !$user->_data['can_pick_categories']}
        <div class="row form-group">
          <div class="col-md-9 offset-md-3">
            <button type="button" class="btn btn-danger js_unsubscribe-package">
              <i class="fa fa-trash-alt mr10"></i>{__("Unsubscribe")}
            </button>
          </div>
        </div>
      {/if}

      {if $user->_data['can_pick_categories']}
        <form class="js_ajax-forms" data-url="users/settings.php?edit=membership">
          {if $user->_data['allowed_videos_categories'] > 0}
            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Videos Categories")}
              </label>
              <div class="col-md-9">
                <input type="text" class="js_tagify-ajax" data-handle="video_categories" name="package_videos_categories" value='{$user->_data['user_package_videos_categories']}'>
                <div class="form-text">
                  {__("You can select")} {$user->_data['allowed_videos_categories']} {__("categories")}
                </div>
              </div>
            </div>
          {/if}

          {if $user->_data['allowed_blogs_categories'] > 0}
            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Blogs Categories")}
              </label>
              <div class="col-md-9">
                <input type="text" class="js_tagify-ajax" data-handle="blogs_categories" name="package_blogs_categories" value='{$user->_data['user_package_blogs_categories']}'>
                <div class="form-text">
                  {__("You can select")} {$user->_data['allowed_blogs_categories']} {__("categories")}
                </div>
              </div>
            </div>
          {/if}

          <div class="row">
            <div class="col-md-9 offset-md-3">
              <button type="button" class="btn btn-danger js_unsubscribe-package">
                <i class="fa fa-trash-alt mr10"></i>{__("Unsubscribe")}
              </button>
              <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
            </div>
          </div>

          <!-- success -->
          <div class="alert alert-success mt15 mb0 x-hidden"></div>
          <!-- success -->

          <!-- error -->
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
          <!-- error -->
        </form>
      {/if}
    </div>
    <div class="divider"></div>
    <div class="heading-small mb20">
      {__("Upgrade Package")}
    </div>
    <div class="pl-md-4">
      <div class="text-center">
        <a href="{$system['system_url']}/packages" class="btn btn-success">{__("Upgrade Package")}</a>
      </div>
    </div>
  {else}
    <div class="text-center">
      <a href="{$system['system_url']}/packages" class="btn btn-success">{__("Upgrade to Pro")}</a>
    </div>
  {/if}
</div>