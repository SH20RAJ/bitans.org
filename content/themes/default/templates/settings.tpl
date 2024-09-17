{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} mt20 sg-offcanvas">
  <div class="row">

    <!-- left panel -->
    <div class="col-md-4 col-lg-3 sg-offcanvas-sidebar">
      <div class="card">
        <div class="card-body with-nav">
          <ul class="side-nav">
            <li {if $view == ""}class="active" {/if}>
              <a href="{$system['system_url']}/settings">
                {include file='__svg_icons.tpl' icon="settings" class="main-icon mr10" width="24px" height="24px"}
                {__("Account Settings")}
              </a>
            </li>

            <li {if $view == "profile"}class="active" {/if}>
              <a href="#info-settings" data-bs-toggle="collapse" {if $view == "profile"}aria-expanded="true" {/if}>
                {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr10" width="24px" height="24px"}
                {__("Edit Profile")}
              </a>
              <div class='collapse {if $view == "profile"}show{/if}' id="info-settings">
                <ul>
                  <li {if $view == "profile" && $sub_view == ""}class="active" {/if}>
                    <a href="{$system['system_url']}/settings/profile">
                      {__("Basic")}
                    </a>
                  </li>
                  {if $system['work_info_enabled']}
                    <li {if $view == "profile" && $sub_view == "work"}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/profile/work">
                        {__("Work")}
                      </a>
                    </li>
                  {/if}
                  {if $system['location_info_enabled']}
                    <li {if $view == "profile" && $sub_view == "location"}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/profile/location">
                        {__("Location")}
                      </a>
                    </li>
                  {/if}
                  {if $system['education_info_enabled']}
                    <li {if $view == "profile" && $sub_view == "education"}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/profile/education">
                        {__("Education")}
                      </a>
                    </li>
                  {/if}
                  {if $custom_fields['other']}
                    <li {if $view == "profile" && $sub_view == "other"}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/profile/other">
                        {__("Other")}
                      </a>
                    </li>
                  {/if}
                  {if $system['social_info_enabled']}
                    <li {if $view == "profile" && $sub_view == "social"}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/profile/social">
                        {__("Social Links")}
                      </a>
                    </li>
                  {/if}
                  {if $system['system_profile_background_enabled']}
                    <li {if $view == "profile" && $sub_view == "design"}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/profile/design">
                        {__("Design")}
                      </a>
                    </li>
                  {/if}
                </ul>
              </div>
            </li>

            <li {if $view == "security"}class="active" {/if}>
              <a href="#security-settings" data-bs-toggle="collapse" {if $view == "security"}aria-expanded="true" {/if}>
                {include file='__svg_icons.tpl' icon="security" class="main-icon mr10" width="24px" height="24px"}
                {__("Security Settings")}
              </a>
              <div class='collapse {if $view == "security"}show{/if}' id="security-settings">
                <ul>
                  <li {if $view == "security" && $sub_view == "password"}class="active" {/if}>
                    <a href="{$system['system_url']}/settings/security/password">
                      {__("Password")}
                    </a>
                  </li>
                  <li {if $view == "security" && $sub_view == "sessions"}class="active" {/if}>
                    <a href="{$system['system_url']}/settings/security/sessions">
                      {__("Manage Sessions")}
                    </a>
                  </li>
                  {if $system['two_factor_enabled']}
                    <li {if $view == "security" && $sub_view == "two-factor"}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/security/two-factor">
                        {__("Two-Factor Authentication")}
                      </a>
                    </li>
                  {/if}
                </ul>
              </div>
            </li>

            <div class="divider mtb5"></div>

            <li {if $view == "notifications"}class="active" {/if}>
              <a href="{$system['system_url']}/settings/notifications">
                {include file='__svg_icons.tpl' icon="notifications" class="main-icon mr10" width="24px" height="24px"}
                {__("Notifications")}
              </a>
            </li>

            {if $system['verification_requests']}
              <li {if $view == "verification"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/verification">
                  {include file='__svg_icons.tpl' icon="verification" class="main-icon mr10" width="24px" height="24px"}
                  {__("Verification")}
                </a>
              </li>
            {/if}

            <div class="divider mtb5"></div>

            <li {if $view == "privacy"}class="active" {/if}>
              <a href="{$system['system_url']}/settings/privacy">
                {include file='__svg_icons.tpl' icon="privacy" class="main-icon mr10" width="24px" height="24px"}
                {__("Privacy")}
              </a>
            </li>

            <li {if $view == "blocking"}class="active" {/if}>
              <a href="{$system['system_url']}/settings/blocking">
                {include file='__svg_icons.tpl' icon="blocking" class="main-icon mr10" width="24px" height="24px"}
                {__("Blocking")}
              </a>
            </li>

            <div class="divider mtb5"></div>

            {if $system['switch_accounts_enabled']}
              <li {if $view == "accounts"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/accounts">
                  {include file='__svg_icons.tpl' icon="user_add" class="main-icon mr10" width="24px" height="24px"}
                  {__("Connected Accounts")}
                </a>
              </li>
            {/if}

            {if $system['social_login_enabled']}
              {if $system['facebook_login_enabled'] || $system['google_login_enabled'] || $system['twitter_login_enabled'] || $system['linkedin_login_enabled'] || $system['vkontakte_login_enabled'] || $system['wordpress_login_enabled'] || $system['sngine_login_enabled']}
                <li {if $view == "linked"}class="active" {/if}>
                  <a href="{$system['system_url']}/settings/linked">
                    {include file='__svg_icons.tpl' icon="linked_accounts" class="main-icon mr10" width="24px" height="24px"}
                    {__("Linked Accounts")}
                  </a>
                </li>
              {/if}
            {/if}

            {if $system['packages_enabled'] || $user->_data['can_monetize_content'] || $user->_data['can_invite_users'] || $system['affiliates_enabled'] || $system['points_enabled'] || $user->_data['can_sell_products'] || $user->_data['can_raise_funding'] || $system['coinpayments_enabled'] || $system['bank_transfers_enabled']}
              <div class="divider mtb5"></div>
            {/if}

            {if $system['packages_enabled']}
              <li {if $view == "membership"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/membership">
                  {include file='__svg_icons.tpl' icon="membership" class="main-icon mr10" width="24px" height="24px"}
                  {__("Membership")}
                </a>
              </li>
            {/if}

            {if $user->_data['can_monetize_content']}
              <li {if $view == "monetization"}class="active" {/if}>
                <a href="#monetization-settings" data-bs-toggle="collapse" {if $view == "monetization"}aria-expanded="true" {/if}>
                  {include file='__svg_icons.tpl' icon="monetization" class="main-icon mr10" width="24px" height="24px"}
                  {__("Monetization")}
                </a>
                <div class='collapse {if $view == "monetization"}show{/if}' id="monetization-settings">
                  <ul>
                    <li {if $view == "monetization" && $sub_view == ""}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/monetization">
                        {__("Settings")}
                      </a>
                    </li>
                    {if $system['monetization_money_withdraw_enabled']}
                      <li {if $view == "monetization" && $sub_view == "payments"}class="active" {/if}>
                        <a href="{$system['system_url']}/settings/monetization/payments">
                          {__("Payments")}
                        </a>
                      </li>
                    {/if}
                  </ul>
                </div>
              </li>
            {/if}

            {if $user->_data['can_invite_users']}
              <li {if $view == "invitations"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/invitations">
                  {include file='__svg_icons.tpl' icon="invitation" class="main-icon mr10" width="24px" height="24px"}
                  {__("Invitations")}
                </a>
              </li>
            {/if}

            {if $system['affiliates_enabled']}
              <li {if $view == "affiliates"}class="active" {/if}>
                <a href="#affiliates-settings" data-bs-toggle="collapse" {if $view == "affiliates"}aria-expanded="true" {/if}>
                  {include file='__svg_icons.tpl' icon="affiliates" class="main-icon mr10" width="24px" height="24px"}
                  {__("Affiliates")}
                </a>
                <div class='collapse {if $view == "affiliates"}show{/if}' id="affiliates-settings">
                  <ul>
                    <li {if $view == "affiliates" && $sub_view == ""}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/affiliates">
                        {__("My Affiliates")}
                      </a>
                    </li>
                    {if $system['affiliates_money_withdraw_enabled']}
                      <li {if $view == "affiliates" && $sub_view == "payments"}class="active" {/if}>
                        <a href="{$system['system_url']}/settings/affiliates/payments">
                          {__("Payments")}
                        </a>
                      </li>
                    {/if}
                  </ul>
                </div>
              </li>
            {/if}

            {if $system['points_enabled']}
              <li {if $view == "points"}class="active" {/if}>
                <a href="#points-settings" data-bs-toggle="collapse" {if $view == "points"}aria-expanded="true" {/if}>
                  {include file='__svg_icons.tpl' icon="points" class="main-icon mr10" width="24px" height="24px"}
                  {__("Points")}
                </a>
                <div class='collapse {if $view == "points"}show{/if}' id="points-settings">
                  <ul>
                    <li {if $view == "points" && $sub_view == ""}class="active" {/if}>
                      <a href="{$system['system_url']}/settings/points">
                        {__("My Points")}
                      </a>
                    </li>
                    {if $system['points_money_withdraw_enabled']}
                      <li {if $view == "points" && $sub_view == "payments"}class="active" {/if}>
                        <a href="{$system['system_url']}/settings/points/payments">
                          {__("Payments")}
                        </a>
                      </li>
                    {/if}
                  </ul>
                </div>
              </li>
            {/if}

            {if $user->_data['can_sell_products']}
              <li {if $view == "market"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/market">
                  {include file='__svg_icons.tpl' icon="market" class="main-icon mr10" width="24px" height="24px"}
                  {__("Marketplace")}
                </a>
              </li>
            {/if}

            {if $user->_data['can_raise_funding']}
              <li {if $view == "funding"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/funding">
                  {include file='__svg_icons.tpl' icon="funding" class="main-icon mr10" width="24px" height="24px"}
                  {__("Funding")}
                </a>
              </li>
            {/if}

            {if $system['coinpayments_enabled']}
              <li {if $view == "coinpayments"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/coinpayments">
                  {include file='__svg_icons.tpl' icon="coinpayments" class="main-icon mr10" width="24px" height="24px"}
                  {__("CoinPayments")}
                </a>
              </li>
            {/if}

            {if $system['bank_transfers_enabled']}
              <li {if $view == "bank"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/bank">
                  {include file='__svg_icons.tpl' icon="bank" class="main-icon mr10" width="24px" height="24px"}
                  {__("Bank Transfers")}
                </a>
              </li>
            {/if}

            {if $system['developers_apps_enabled']}
              <div class="divider mtb5"></div>

              <li {if $view == "apps"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/apps">
                  {include file='__svg_icons.tpl' icon="apps" class="main-icon mr10" width="24px" height="24px"}
                  {__("Apps")}
                </a>
              </li>
            {/if}

            {if $system['download_info_enabled'] || $system['delete_accounts_enabled']}
              <div class="divider mtb5"></div>
            {/if}

            {if $system['market_enabled']}
              <li {if $view == "addresses"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/addresses">
                  {include file='__svg_icons.tpl' icon="map" class="main-icon mr10" width="24px" height="24px"}
                  {__("Your Addresses")}
                </a>
              </li>
            {/if}

            {if $system['download_info_enabled']}
              <li {if $view == "information"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/information">
                  {include file='__svg_icons.tpl' icon="user_information" class="main-icon mr10" width="24px" height="24px"}
                  {__("Your Information")}
                </a>
              </li>
            {/if}

            {if $system['delete_accounts_enabled']}
              <li {if $view == "delete"}class="active" {/if}>
                <a href="{$system['system_url']}/settings/delete">
                  {include file='__svg_icons.tpl' icon="delete_user" class="main-icon mr10" width="24px" height="24px"}
                  {__("Delete Account")}
                </a>
              </li>
            {/if}
          </ul>
        </div>
      </div>
    </div>
    <!-- left panel -->

    <!-- right panel -->
    <div class="col-md-8 col-lg-9 sg-offcanvas-mainbar">
      <div class="card">
        {if $view == ""}
          {include file='settings.account.tpl'}
        {elseif $view == "profile"}
          {include file='settings.profile.tpl'}
        {elseif $view == "security"}
          {include file='settings.security.tpl'}
        {elseif $view == "privacy"}
          {include file='settings.privacy.tpl'}
        {elseif $view == "notifications"}
          {include file='settings.notifications.tpl'}
        {elseif $view == "accounts"}
          {include file='settings.accounts.tpl'}
        {elseif $view == "linked"}
          {include file='settings.linked.tpl'}
        {elseif $view == "membership"}
          {include file='settings.membership.tpl'}
        {elseif $view == "invitations"}
          {include file='settings.invitations.tpl'}
        {elseif $view == "affiliates"}
          {include file='settings.affiliates.tpl'}
        {elseif $view == "points"}
          {include file='settings.points.tpl'}
        {elseif $view == "market"}
          {include file='settings.market.tpl'}
        {elseif $view == "funding"}
          {include file='settings.funding.tpl'}
        {elseif $view == "monetization"}
          {include file='settings.monetization.tpl'}
        {elseif $view == "coinpayments"}
          {include file='settings.coinpayments.tpl'}
        {elseif $view == "bank"}
          {include file='settings.bank.tpl'}
        {elseif $view == "verification"}
          {include file='settings.verification.tpl'}
        {elseif $view == "apps"}
          {include file='settings.apps.tpl'}
        {elseif $view == "blocking"}
          {include file='settings.blocking.tpl'}
        {elseif $view == "addresses"}
          {include file='settings.addresses.tpl'}
        {elseif $view == "information"}
          {include file='settings.information.tpl'}
        {elseif $view == "delete"}
          {include file='settings.delete.tpl'}
        {/if}
      </div>
    </div>
    <!-- right panel -->

  </div>
</div>
<!-- page content -->

{include file='_footer.tpl'}