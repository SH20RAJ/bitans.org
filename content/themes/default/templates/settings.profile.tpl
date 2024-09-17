{if $sub_view == ""}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr15" width="24px" height="24px"}{__("Basic")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=basic">
    <div class="card-body">
      {if !$system['show_usernames_enabled'] && $user->_data['user_verified']}
        <div class="alert alert-warning">
          <div class="icon">
            <i class="fa fa-exclamation-triangle fa-2x"></i>
          </div>
          <div class="text">
            <strong>{__("Attention")}</strong><br>
            {__("Your account is already verified if you changed your name you will lose the verification badge")}
          </div>
        </div>
      {/if}

      <div class="row">
        {if !$system['show_usernames_enabled']}
          <div class="form-group col-md-6">
            <label class="form-label">{__("First Name")}</label>
            <input type="text" class="form-control" name="firstname" value="{$user->_data['user_firstname']}">
          </div>

          <div class="form-group col-md-6">
            <label class="form-label">{__("Last Name")}</label>
            <input type="text" class="form-control" name="lastname" value="{$user->_data['user_lastname']}">
          </div>
        {/if}

        {if !$system['genders_disabled']}
          <div class="form-group col-md-6">
            <label class="form-label">{__("I am")}</label>
            <select class="form-select" name="gender">
              <option value="none">{__("Select Sex")}</option>
              {foreach $genders as $gender}
                <option {if $user->_data['user_gender'] == $gender['gender_id']}selected{/if} value="{$gender['gender_id']}">{$gender['gender_name']}</option>
              {/foreach}
            </select>
          </div>
        {/if}

        {if $system['relationship_info_enabled']}
          <div class="form-group col-md-6">
            <label class="form-label">{__("Relationship Status")}</label>
            <select class="form-select" name="relationship">
              <option value="none">{__("Select Relationship")}</option>
              <option {if $user->_data['user_relationship'] == "single"}selected{/if} value="single">{__("Single")}</option>
              <option {if $user->_data['user_relationship'] == "relationship"}selected{/if} value="relationship">{__("In a relationship")}</option>
              <option {if $user->_data['user_relationship'] == "married"}selected{/if} value="married">{__("Married")}</option>
              <option {if $user->_data['user_relationship'] == "complicated"}selected{/if} value="complicated">{__("It's complicated")}</option>
              <option {if $user->_data['user_relationship'] == "separated"}selected{/if} value="separated">{__("Separated")}</option>
              <option {if $user->_data['user_relationship'] == "divorced"}selected{/if} value="divorced">{__("Divorced")}</option>
              <option {if $user->_data['user_relationship'] == "widowed"}selected{/if} value="widowed">{__("Widowed")}</option>
            </select>
          </div>
        {/if}

        <div class="form-group col-md-6">
          <label class="form-label">{__("Country")}</label>
          <select class="form-select" name="country">
            <option value="none">{__("Select Country")}</option>
            {foreach $countries as $country}
              <option {if $user->_data['user_country'] == $country['country_id']}selected{/if} value="{$country['country_id']}">{$country['country_name']}</option>
            {/foreach}
          </select>
        </div>

        {if $system['website_info_enabled']}
          <div class="form-group col-md-6">
            <label class="form-label">{__("Website")}</label>
            <input type="text" class="form-control" name="website" value="{$user->_data['user_website']}">
            <div class="form-text">
              {__("Website link must start with http:// or https://")}
            </div>
          </div>
        {/if}
      </div>

      <div class="form-group">
        <label class="form-label">{__("Birthdate")}</label>
        <div class="row">
          <div class="col">
            <select class="form-select" name="birth_month">
              <option value="none">{__("Select Month")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '1'}selected{/if} value="1">{__("Jan")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '2'}selected{/if} value="2">{__("Feb")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '3'}selected{/if} value="3">{__("Mar")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '4'}selected{/if} value="4">{__("Apr")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '5'}selected{/if} value="5">{__("May")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '6'}selected{/if} value="6">{__("Jun")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '7'}selected{/if} value="7">{__("Jul")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '8'}selected{/if} value="8">{__("Aug")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '9'}selected{/if} value="9">{__("Sep")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '10'}selected{/if} value="10">{__("Oct")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '11'}selected{/if} value="11">{__("Nov")}</option>
              <option {if $user->_data['user_birthdate_parsed']['month'] == '12'}selected{/if} value="12">{__("Dec")}</option>
            </select>
          </div>
          <div class="col">
            <select class="form-select" name="birth_day">
              <option value="none">{__("Select Day")}</option>
              {for $i=1 to 31}
                <option {if $user->_data['user_birthdate_parsed']['day'] == $i}selected{/if} value="{$i}">{$i}</option>
              {/for}
            </select>
          </div>
          <div class="col">
            <select class="form-select" name="birth_year">
              <option value="none">{__("Select Year")}</option>
              {for $i=1905 to 2023}
                <option {if $user->_data['user_birthdate_parsed']['year'] == $i}selected{/if} value="{$i}">{$i}</option>
              {/for}
            </select>
          </div>
        </div>
      </div>

      {if $system['biography_info_enabled']}
        <div class="form-group">
          <label class="form-label">{__("About Me")}</label>
          <textarea class="form-control" name="biography">{$user->_data['user_biography']}</textarea>
        </div>
      {/if}

      <!-- custom fields -->
      {if $custom_fields['basic']}
        {include file='__custom_fields.tpl' _custom_fields=$custom_fields['basic'] _registration=false}
      {/if}
      <!-- custom fields -->

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

{elseif $sub_view == "work"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr15" width="24px" height="24px"}{__("Work")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=work">
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">{__("Work Title")}</label>
        <input type="text" class="form-control" name="work_title" value="{$user->_data['user_work_title']}">
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Work Place")}</label>
          <input type="text" class="form-control" name="work_place" value="{$user->_data['user_work_place']}">
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("Work Website")}</label>
          <input type="text" class="form-control" name="work_url" value="{$user->_data['user_work_url']}">
          <div class="form-text">
            {__("Website link must start with http:// or https://")}
          </div>
        </div>
      </div>

      <!-- custom fields -->
      {if $custom_fields['work']}
        {include file='__custom_fields.tpl' _custom_fields=$custom_fields['work'] _registration=false}
      {/if}
      <!-- custom fields -->

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

{elseif $sub_view == "location"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr15" width="24px" height="24px"}{__("Location")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=location">
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">{__("Current City")}</label>
        <input type="text" class="form-control js_geocomplete" name="city" value="{$user->_data['user_current_city']}">
      </div>

      <div class="form-group">
        <label class="form-label">{__("Hometown")}</label>
        <input type="text" class="form-control js_geocomplete" name="hometown" value="{$user->_data['user_hometown']}">
      </div>

      <!-- custom fields -->
      {if $custom_fields['location']}
        {include file='__custom_fields.tpl' _custom_fields=$custom_fields['location'] _registration=false}
      {/if}
      <!-- custom fields -->

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

{elseif $sub_view == "education"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr15" width="24px" height="24px"}{__("Education")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=education">
    <div class="card-body">
      <div class="form-group">
        <label class="form-label">{__("School")}</label>
        <input type="text" class="form-control" name="edu_school" value="{$user->_data['user_edu_school']}">
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Major")}</label>
          <input type="text" class="form-control" name="edu_major" value="{$user->_data['user_edu_major']}">
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("Class")}</label>
          <input type="text" class="form-control" name="edu_class" value="{$user->_data['user_edu_class']}">
        </div>
      </div>

      <!-- custom fields -->
      {if $custom_fields['education']}
        {include file='__custom_fields.tpl' _custom_fields=$custom_fields['education'] _registration=false}
      {/if}
      <!-- custom fields -->

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

{elseif $sub_view == "other"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr15" width="24px" height="24px"}{__("Other")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=other">
    <div class="card-body">
      <!-- custom fields -->
      {if $custom_fields['other']}
        {include file='__custom_fields.tpl' _custom_fields=$custom_fields['other'] _registration=false}
      {/if}
      <!-- custom fields -->

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

{elseif $sub_view == "social"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr15" width="24px" height="24px"}{__("Social Links")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=social">
    <div class="card-body">
      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label">{__("Facebook Profile URL")}</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent">
              {include file='__svg_icons.tpl' icon="facebook" width="22px" height="22px"}
            </span>
            <input type="text" class="form-control" name="facebook" value="{$user->_data['user_social_facebook']}">
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("X Profile URL")}</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent">
              {include file='__svg_icons.tpl' icon="twitter" width="22px" height="22px"}
            </span>
            <input type="text" class="form-control" name="twitter" value="{$user->_data['user_social_twitter']}">
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("YouTube Profile URL")}</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent">
              {include file='__svg_icons.tpl' icon="youtube" width="22px" height="22px"}
            </span>
            <input type="text" class="form-control" name="youtube" value="{$user->_data['user_social_youtube']}">
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("Instagram Profile URL")}</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent">
              {include file='__svg_icons.tpl' icon="instagram" width="22px" height="22px"}
            </span>
            <input type="text" class="form-control" name="instagram" value="{$user->_data['user_social_instagram']}">
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("Twitch Profile URL")}</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent">
              {include file='__svg_icons.tpl' icon="twitch" width="22px" height="22px"}
            </span>
            <input type="text" class="form-control" name="twitch" value="{$user->_data['user_social_twitch']}">
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("LinkedIn Profile URL")}</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent">
              {include file='__svg_icons.tpl' icon="linkedin" width="22px" height="22px"}
            </span>
            <input type="text" class="form-control" name="linkedin" value="{$user->_data['user_social_linkedin']}">
          </div>
        </div>

        <div class="form-group col-md-6">
          <label class="form-label">{__("Vkontakte Profile URL")}</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent">
              {include file='__svg_icons.tpl' icon="vk" width="22px" height="22px"}
            </span>
            <input type="text" class="form-control" name="vkontakte" value="{$user->_data['user_social_vkontakte']}">
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
      <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
    </div>
  </form>

{elseif $sub_view == "design"}
  <div class="card-header with-icon">
    {include file='__svg_icons.tpl' icon="edit_profile" class="main-icon mr15" width="24px" height="24px"}{__("Design")}
  </div>
  <form class="js_ajax-forms" data-url="users/settings.php?edit=design">
    <div class="card-body">
      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Profile Background")}
        </label>
        <div class="col-md-9">
          {if $user->_data['user_profile_background'] == ''}
            <div class="x-image">
              <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

              </button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="user_profile_background" value="">
            </div>
          {else}
            <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$user->_data['user_profile_background']}')">
              <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'></button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="user_profile_background" value="{$user->_data['user_profile_background']}">
            </div>
          {/if}
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

{/if}