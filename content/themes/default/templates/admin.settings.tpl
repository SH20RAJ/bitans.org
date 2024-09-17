<div class="card">

  {if $sub_view == ""}

    <!-- card-header -->
    <div class="card-header with-icon with-nav">
      <!-- panel title -->
      <div class="mb20">
        <i class="fa fa-cog mr10"></i>{__("Settings")}
      </div>
      <!-- panel title -->

      <!-- panel nav -->
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#General" data-bs-toggle="tab">
            <i class="fa fa-server fa-fw mr5"></i><strong>{__("General")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#SEO" data-bs-toggle="tab">
            <i class="fa fa-sitemap fa-fw mr5"></i><strong>{__("SEO")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Modules" data-bs-toggle="tab">
            <i class="fa fa-dice-d6 fa-fw mr5"></i><strong>{__("Modules")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Features" data-bs-toggle="tab">
            <i class="fa fa-microchip fa-fw mr5"></i><strong>{__("Features")}</strong>
          </a>
        </li>
      </ul>
      <!-- panel nav -->
    </div>
    <!-- card-header -->

    <!-- tab-content -->
    <div class="tab-content">
      <!-- General -->
      <div class="tab-pane active" id="General">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=general">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="website_live" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Website Live")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the entire website On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="system_live">
                  <input type="checkbox" name="system_live" id="system_live" {if $system['system_live']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Shutdown Message")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_message" rows="3">{$system['system_message']}</textarea>
                <div class="form-text">
                  {__("The text that is presented when the site is closed")}
                </div>
              </div>
            </div>

            <div class="divider dashed"></div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("System Email")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="system_email" value="{$system['system_email']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("The contact email that all messages send to")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("System Datetime Format")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="system_datetime_format">
                  <option {if $system['system_datetime_format'] == "d/m/Y H:i"}selected{/if} value="d/m/Y H:i">d/m/Y H:i ({__("Example")}: 30/05/2023 01:30 PM)</option>
                  <option {if $system['system_datetime_format'] == "m/d/Y H:i"}selected{/if} value="m/d/Y H:i">m/d/Y H:i ({__("Example")}: 05/30/2023 01:30 PM)</option>
                </select>
                <div class="form-text">
                  {__("Select the datetime format of the datetime picker")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("System Distance Unit")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="system_distance">
                  <option {if $system['system_distance'] == "mile"}selected{/if} value="mile">{__("Mile")}</option>
                  <option {if $system['system_distance'] == "kilometer"}selected{/if} value="kilometer">{__("Kilometer")}</option>
                </select>
                <div class="form-text">
                  {__("Select the distance measure unit of your website")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("System Currency")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="system_currency">
                  {foreach $system_currencies as $currency}
                    <option {if $currency['default']}selected{/if} value="{$currency['currency_id']}">
                      {$currency['name']} ({$currency['code']})
                    </option>
                  {/foreach}
                </select>
                <div class="form-text">
                  {__("You can add, edit or delete currencies from")} <a href="{$system['system_url']}/{$control_panel['url']}/currencies">{__("Currencies")}</a>
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
      </div>
      <!-- General -->

      <!-- SEO -->
      <div class="tab-pane" id="SEO">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=seo">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="website_public" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Website Public")}</div>
                <div class="form-text d-none d-sm-block">{__("Make the website public to allow non logged users to view website content")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="system_public">
                  <input type="checkbox" name="system_public" id="system_public" {if $system['system_public']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Newsfeed Public")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Make the newsfeed available for visitors in landing page")}<br>
                  {__("Enable this will make your website public and list only public posts")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="newsfeed_public">
                  <input type="checkbox" name="newsfeed_public" id="newsfeed_public" {if $system['newsfeed_public']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="directory" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6"> {__("Directory")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable the directory for better SEO results")}<br>
                  {__("Make the website public to allow non logged users to view website content")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="directory_enabled">
                  <input type="checkbox" name="directory_enabled" id="directory_enabled" {if $system['directory_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Website Title")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="system_title" value="{__($system['system_title'])}">
                <div class="form-text">
                  {__("Title of your website")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Website Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description" rows="3">{$system['system_description']}</textarea>
                <div class="form-text">
                  {__("Description of your website")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Website Keywords")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_keywords" rows="3">{$system['system_keywords']}</textarea>
                <div class="form-text">
                  {__("Example: social, social site")}
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Directory Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_directory" rows="3">{$system['system_description_directory']}</textarea>
                <div class="form-text">
                  {__("Description of your Directory")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Pages Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_pages" rows="3">{$system['system_description_pages']}</textarea>
                <div class="form-text">
                  {__("Description of your pages module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Groups Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_groups" rows="3">{$system['system_description_groups']}</textarea>
                <div class="form-text">
                  {__("Description of your groups module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Events Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_events" rows="3">{$system['system_description_events']}</textarea>
                <div class="form-text">
                  {__("Description of your events module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Blogs Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_blogs" rows="3">{$system['system_description_blogs']}</textarea>
                <div class="form-text">
                  {__("Description of your blogs module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Marketplace Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_marketplace" rows="3">{$system['system_description_marketplace']}</textarea>
                <div class="form-text">
                  {__("Description of your marketplace module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Funding Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_funding" rows="3">{$system['system_description_funding']}</textarea>
                <div class="form-text">
                  {__("Description of your funding module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Offers Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_offers" rows="3">{$system['system_description_offers']}</textarea>
                <div class="form-text">
                  {__("Description of your offer module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Jobs Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_jobs" rows="3">{$system['system_description_jobs']}</textarea>
                <div class="form-text">
                  {__("Description of your jobs module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Forums Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_forums" rows="3">{$system['system_description_forums']}</textarea>
                <div class="form-text">
                  {__("Description of your forums module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Movies Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_movies" rows="3">{$system['system_description_movies']}</textarea>
                <div class="form-text">
                  {__("Description of your movies module")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Games Description")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_description_games" rows="3">{$system['system_description_games']}</textarea>
                <div class="form-text">
                  {__("Description of your games module")}
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
      </div>
      <!-- SEO -->

      <!-- Modules -->
      <div class="tab-pane" id="Modules">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=modules">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="pages" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Pages")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the pages On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="pages_enabled">
                  <input type="checkbox" name="pages_enabled" id="pages_enabled" {if $system['pages_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="star" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Pages Reviews")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the pages reviews On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="pages_reviews_enabled">
                  <input type="checkbox" name="pages_reviews_enabled" id="pages_reviews_enabled" {if $system['pages_reviews_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Pages Review Replacement")}</div>
                <div class="form-text d-none d-sm-block">{__("Enbale this to allow user to change his review")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="pages_reviews_replacement_enabled">
                  <input type="checkbox" name="pages_reviews_replacement_enabled" id="pages_reviews_replacement_enabled" {if $system['pages_reviews_replacement_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="groups" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Groups")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the groups On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="groups_enabled">
                  <input type="checkbox" name="groups_enabled" id="groups_enabled" {if $system['groups_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="star" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Groups Reviews")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the groups reviews On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="groups_reviews_enabled">
                  <input type="checkbox" name="groups_reviews_enabled" id="groups_reviews_enabled" {if $system['groups_reviews_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Groups Review Replacement")}</div>
                <div class="form-text d-none d-sm-block">{__("Enbale this to allow user to change his review")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="groups_reviews_replacement_enabled">
                  <input type="checkbox" name="groups_reviews_replacement_enabled" id="groups_reviews_replacement_enabled" {if $system['groups_reviews_replacement_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="events" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Events")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the events On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="events_enabled">
                  <input type="checkbox" name="events_enabled" id="events_enabled" {if $system['events_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Pages Events")}</div>
                <div class="form-text d-none d-sm-block">{__("Allow pages to create events")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="pages_events_enabled">
                  <input type="checkbox" name="pages_events_enabled" id="pages_events_enabled" {if $system['pages_events_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="star" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Events Reviews")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the events reviews On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="events_reviews_enabled">
                  <input type="checkbox" name="events_reviews_enabled" id="events_reviews_enabled" {if $system['events_reviews_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Events Review Replacement")}</div>
                <div class="form-text d-none d-sm-block">{__("Enbale this to allow user to change his review")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="events_reviews_replacement_enabled">
                  <input type="checkbox" name="events_reviews_replacement_enabled" id="events_reviews_replacement_enabled" {if $system['events_reviews_replacement_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="watch" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Watch")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Turn the watch videos On and Off")}<br>
                  {__("Watch module will show all public videos at one place")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="watch_enabled">
                  <input type="checkbox" name="watch_enabled" id="watch_enabled" {if $system['watch_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="blogs" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Blogs")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the blogs On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="blogs_enabled">
                  <input type="checkbox" name="blogs_enabled" id="blogs_enabled" {if $system['blogs_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="offers" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Offers")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Turn the offers On and Off")}<br>
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="offers_enabled">
                  <input type="checkbox" name="offers_enabled" id="offers_enabled" {if $system['offers_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="jobs" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Jobs")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Turn the jobs On and Off")}<br>
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="jobs_enabled">
                  <input type="checkbox" name="jobs_enabled" id="jobs_enabled" {if $system['jobs_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="forums" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Forums")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the forums On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="forums_enabled">
                  <input type="checkbox" name="forums_enabled" id="forums_enabled" {if $system['forums_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="user_online" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Online Users")}</div>
                <div class="form-text d-none d-sm-block">{__("Show forums online users")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="forums_online_enabled">
                  <input type="checkbox" name="forums_online_enabled" id="forums_online_enabled" {if $system['forums_online_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="stats" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Statistics")}</div>
                <div class="form-text d-none d-sm-block">{__("Show forums statistics")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="forums_statistics_enabled">
                  <input type="checkbox" name="forums_statistics_enabled" id="forums_statistics_enabled" {if $system['forums_statistics_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="movies" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Movies")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the movies On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="movies_enabled">
                  <input type="checkbox" name="movies_enabled" id="movies_enabled" {if $system['movies_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="games" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Games")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the games On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="games_enabled">
                  <input type="checkbox" name="games_enabled" id="games_enabled" {if $system['games_enabled']}checked{/if}>
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
          </div>
          <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
          </div>
        </form>
      </div>
      <!-- Modules -->

      <!-- Features -->
      <div class="tab-pane" id="Features">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=features">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="map" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Fliter by Location")}</div>
                <div class="form-text d-none d-sm-block">{__("If enabled user will able to filter people, products ... etc by location")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="location_finder_enabled">
                  <input type="checkbox" name="location_finder_enabled" id="location_finder_enabled" {if $system['location_finder_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="contat_us" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Contact Us")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the contact us page On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="contact_enabled">
                  <input type="checkbox" name="contact_enabled" id="contact_enabled" {if $system['contact_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="dark_light" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("DayTime Messages")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the DayTime Messages (Good Morning, Afternoon, Evening) On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="daytime_msg_enabled">
                  <input type="checkbox" name="daytime_msg_enabled" id="daytime_msg_enabled" {if $system['daytime_msg_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Morning Message")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_morning_message" rows="3">{$system['system_morning_message']}</textarea>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Afternoon Message")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_afternoon_message" rows="3">{$system['system_afternoon_message']}</textarea>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Evening Message")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="system_evening_message" rows="3">{$system['system_evening_message']}</textarea>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="poke" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Pokes")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable users to poke each others")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="pokes_enabled">
                  <input type="checkbox" name="pokes_enabled" id="pokes_enabled" {if $system['pokes_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="gifts" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Gifts")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable users to send gifts to each others")}<br>
                  {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/gifts">{__("Gifts")}</a>
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="gifts_enabled">
                  <input type="checkbox" name="gifts_enabled" id="gifts_enabled" {if $system['gifts_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="cookie" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Cookie Consent")} ({__("GDPR")})</div>
                <div class="form-text d-none d-sm-block">{__("Turn the cookie consent notification On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="cookie_consent_enabled">
                  <input type="checkbox" name="cookie_consent_enabled" id="cookie_consent_enabled" {if $system['cookie_consent_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="adblock" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Adblock Detector")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Turn the Adblock auto detector notification On and Off")}, {__("(Note: Admin is exception)")}<br>
                  {__("Red block message will appear to make user disable adblock from his browser")}<br>
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="adblock_detector_enabled">
                  <input type="checkbox" name="adblock_detector_enabled" id="adblock_detector_enabled" {if $system['adblock_detector_enabled']}checked{/if}>
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
          </div>
          <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
          </div>
        </form>
      </div>
      <!-- Features -->
    </div>
    <!-- tab-content -->

  {elseif $sub_view == "posts"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Posts")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <!-- Posts -->
    <form class="js_ajax-forms" data-url="admin/settings.php?edit=posts">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="24_hours" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Stories")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the stories On and Off")}<br>
              {__("Stories are photos and videos that only last 24 hours")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="stories_enabled">
              <input type="checkbox" name="stories_enabled" id="stories_enabled" {if $system['stories_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Story Duration")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="stories_duration" value="{$system['stories_duration']}">
            <div class="form-text">
              {__("The story duration in seconds")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="verification" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Posts Approval System")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the approval system On and Off (If disabled all posts will be approved by default)")}<br>
              {__("If enabled, posts will be pending for approval by the admin or moderators")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="posts_approval_enabled">
              <input type="checkbox" name="posts_approval_enabled" id="posts_approval_enabled" {if $system['posts_approval_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Appoval Limit")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="posts_approval_limit" value="{$system['posts_approval_limit']}">
            <div class="form-text">
              {__("After how many posts needs to be approved so the user can post without approval")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Newsfeed Posts Source")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="newsfeed_source">
              <option value="default" {if $system['newsfeed_source'] == "default"}selected{/if}>{__("Default")} [{__("Show what user is followings (Friends, Followings, Pages... etc)")}]</option>
              <option value="all_posts" {if $system['newsfeed_source'] == "all_posts"}selected{/if}>{__("All Posts")} [{__("All posts will be shown")}]</option>
            </select>
            <div class="form-text">
              {__("Algorithm will exclude any post from closed/secret groups and events that users not member of incase of all posts also will disable all posts privacy")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="map" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Newsfeed Location Filter")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the newsfeed location filter On and Off")}<br>
              {__("Will enable your users to filter their newsfeed by countries")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="newsfeed_location_filter_enabled">
              <input type="checkbox" name="newsfeed_location_filter_enabled" id="newsfeed_location_filter_enabled" {if $system['newsfeed_location_filter_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="popularity" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Popular Posts")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the popular posts On and Off")}<br>
              {__("Popular posts are public posts ordered by most reactions, comments & shares")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="popular_posts_enabled">
              <input type="checkbox" name="popular_posts_enabled" id="popular_posts_enabled" {if $system['popular_posts_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="posts_discover" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Discover Posts")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the discover posts On and Off")}<br>
              {__("Discover posts are public posts ordered from most recent to old")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="discover_posts_enabled">
              <input type="checkbox" name="discover_posts_enabled" id="discover_posts_enabled" {if $system['discover_posts_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="memories" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Memories")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the memories On and Off")}<br>
              {__("Memories are posts from the same day on last year")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="memories_enabled">
              <input type="checkbox" name="memories_enabled" id="memories_enabled" {if $system['memories_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="wall_posts" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Wall Posts")}</div>
            <div class="form-text d-none d-sm-block">{__("Users can publish posts on their friends walls")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="wall_posts_enabled">
              <input type="checkbox" name="wall_posts_enabled" id="wall_posts_enabled" {if $system['wall_posts_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="posts_colored" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Colored Posts")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the colored posts On and Off")}<br>
              {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/colored_posts">{__("Colored Posts")}</a>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="colored_posts_enabled">
              <input type="checkbox" name="colored_posts_enabled" id="colored_posts_enabled" {if $system['colored_posts_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="smile" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Feelings/Activity Posts")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the feelings and activity posts On and Off")}<br>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="activity_posts_enabled">
              <input type="checkbox" name="activity_posts_enabled" id="activity_posts_enabled" {if $system['activity_posts_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="voice_notes" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Voice Notes in Posts")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the voice notes in posts On and Off")}<br>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="voice_notes_posts_enabled">
              <input type="checkbox" name="voice_notes_posts_enabled" id="voice_notes_posts_enabled" {if $system['voice_notes_posts_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            <div style="width: 40px; height: 40px;"></div>
          </div>
          <div>
            <div class="form-label h6">{__("Voice Notes in Comments")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the voice notes in comments On and Off")}<br>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="voice_notes_comments_enabled">
              <input type="checkbox" name="voice_notes_comments_enabled" id="voice_notes_comments_enabled" {if $system['voice_notes_comments_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Voice Notes Max Duration")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="voice_notes_durtaion" value="{$system['voice_notes_durtaion']}">
            <div class="form-text">
              {__("The maximum length for voice note in seconds")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Voice Notes Encoding")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="voice_notes_encoding">
              <option value="mp3" {if $system['voice_notes_encoding'] == "mp3"}selected{/if}>mp3</option>
              <option value="ogg" {if $system['voice_notes_encoding'] == "ogg"}selected{/if}>ogg</option>
              <option value="wav" {if $system['voice_notes_encoding'] == "wav"}selected{/if}>wav</option>
            </select>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="polls" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Polls")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the poll posts On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="polls_enabled">
              <input type="checkbox" name="polls_enabled" id="polls_enabled" {if $system['polls_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="map" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Geolocation")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the post Geolocation On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="geolocation_enabled">
              <input type="checkbox" name="geolocation_enabled" id="geolocation_enabled" {if $system['geolocation_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Geolocation Google Key")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="geolocation_key" value="{$system['geolocation_key']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Check the documentation to learn how to get this key")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="gif" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("GIF")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the gif posts On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="gif_enabled">
              <input type="checkbox" name="gif_enabled" id="gif_enabled" {if $system['gif_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Giphy API Key")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="giphy_key" value="{$system['giphy_key']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Check the documentation to learn how to get this key")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="language" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Post Translation")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the post translation On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="post_translation_enabled">
              <input type="checkbox" name="post_translation_enabled" id="post_translation_enabled" {if $system['post_translation_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Yandex Key")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="yandex_key" value="{$system['yandex_key']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Check the documentation to learn how to get this key")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="youtube_player" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Smart YouTube Player")}</div>
            <div class="form-text d-none d-sm-block">{__("Smart YouTube player will save a lot of bandwidth")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="smart_yt_player">
              <input type="checkbox" name="smart_yt_player" id="smart_yt_player" {if $system['smart_yt_player']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="social_share" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Social Media Share")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the social media share for posts On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="social_share_enabled">
              <input type="checkbox" name="social_share_enabled" id="social_share_enabled" {if $system['social_share_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Max Post Characters")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="max_post_length" value="{$system['max_post_length']}">
            <div class="form-text">
              {__("The Maximum allowed post characters length (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Max Comment Characters")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="max_comment_length" value="{$system['max_comment_length']}">
            <div class="form-text">
              {__("The Maximum allowed comment characters length (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Max Posts/Hour")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="max_posts_hour" value="{$system['max_posts_hour']}">
            <div class="form-text">
              {__("The Maximum number of posts that user can publish per hour (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Max Comments/Hour")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="max_comments_hour" value="{$system['max_comments_hour']}">
            <div class="form-text">
              {__("The Maximum number of comments that user can publish per hour (0 for unlimited)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Default Posts Privacy")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="default_privacy">
              <option value="public" {if $system['default_privacy'] == "public"}selected{/if}>{__("Public")}</option>
              <option value="friends" {if $system['default_privacy'] == "friends"}selected{/if}>{__("Friends")}</option>
              <option value="me" {if $system['default_privacy'] == "me"}selected{/if}>{__("Only Me")}</option>
            </select>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="spy" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Post As Anonymous")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn Anonymous mode On and Off")}<br>
              {__("Note: Admins and Moderators will able to see the real post author")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="anonymous_mode">
              <input type="checkbox" name="anonymous_mode" id="anonymous_mode" {if $system['anonymous_mode']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="adult" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Adult Mode")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Adult mode will hide content that marked for adult from users under 18 years old")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="adult_mode">
              <input type="checkbox" name="adult_mode" id="adult_mode" {if $system['adult_mode']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="user_online" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Online Status on Posts")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn online indicator on Posts On and Off (User must be online and enabled the chat)")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="posts_online_status">
              <input type="checkbox" name="posts_online_status" id="posts_online_status" {if $system['posts_online_status']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="scroll_desktop" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Desktop Infinite Scroll")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn infinite scroll on desktop screens On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="desktop_infinite_scroll">
              <input type="checkbox" name="desktop_infinite_scroll" id="desktop_infinite_scroll" {if $system['desktop_infinite_scroll']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="scroll_mobile" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Mobile Infinite Scroll")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn infinite scroll on mobile screens On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="mobile_infinite_scroll">
              <input type="checkbox" name="mobile_infinite_scroll" id="mobile_infinite_scroll" {if $system['mobile_infinite_scroll']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="videos" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Auto Play Videos")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn auto play videos On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="auto_play_videos">
              <input type="checkbox" name="auto_play_videos" id="auto_play_videos" {if $system['auto_play_videos']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="fluid_vertical" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Videos Fluid Mode")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable video player fluid mode")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="fluid_videos_enabled">
              <input type="checkbox" name="fluid_videos_enabled" id="fluid_videos_enabled" {if $system['fluid_videos_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="views" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Posts Views")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn posts views system On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="posts_views_enabled">
              <input type="checkbox" name="posts_views_enabled" id="posts_views_enabled" {if $system['posts_views_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Views Type")}
          </label>
          <div class="col-md-9">
            <div class="form-check form-check-inline">
              <input type="radio" name="posts_views_type" id="all_views" value="all" class="form-check-input" {if $system['posts_views_type'] == "all"}checked{/if}>
              <label class="form-check-label" for="all_views">{__("All Views")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="posts_views_type" id="unique_views" value="unique" class="form-check-input" {if $system['posts_views_type'] == "unique"}checked{/if}>
              <label class="form-check-label" for="unique_views">{__("Unique Views Only")}</label>
            </div>
            <div class="form-text">
              {__("Note: All views will count all views including the same user multiple views")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="star" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Posts Reviews")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the posts reviews On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="posts_reviews_enabled">
              <input type="checkbox" name="posts_reviews_enabled" id="posts_reviews_enabled" {if $system['posts_reviews_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            <div style="width: 40px; height: 40px;"></div>
          </div>
          <div>
            <div class="form-label h6">{__("Posts Review Replacement")}</div>
            <div class="form-text d-none d-sm-block">{__("Enbale this to allow user to change his review")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="posts_reviews_replacement_enabled">
              <input type="checkbox" name="posts_reviews_replacement_enabled" id="posts_reviews_replacement_enabled" {if $system['posts_reviews_replacement_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="trending" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6 mb5">{__("Trending Hashtags")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the trending hashtags feature On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="trending_hashtags_enabled">
              <input type="checkbox" name="trending_hashtags_enabled" id="trending_hashtags_enabled" {if $system['trending_hashtags_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Trending Interval")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="trending_hashtags_interval">
              <option {if $system['trending_hashtags_interval'] == "day"}selected{/if} value="day">{__("Last 24 Hours")}</option>
              <option {if $system['trending_hashtags_interval'] == "week"}selected{/if} value="week">{__("Last Week")}</option>
              <option {if $system['trending_hashtags_interval'] == "month"}selected{/if} value="month">{__("Last Month")}</option>
            </select>
            <div class="form-text">
              {__("Select the interval of trending hashtags")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Hashtags Limit")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="trending_hashtags_limit" value="{$system['trending_hashtags_limit']}">
            <div class="form-text">
              {__("How many hashtags you want to display")}
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
    <!-- Posts -->

  {elseif $sub_view == "registration"}

    <!-- card-header -->
    <div class="card-header with-icon with-nav">
      <!-- panel title -->
      <div class="mb20">
        <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Registration")}
      </div>
      <!-- panel title -->

      <!-- panel nav -->
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#General" data-bs-toggle="tab">
            <i class="fa fa-sign-in-alt fa-fw mr5"></i><strong>{__("General")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Social" data-bs-toggle="tab">
            <i class="fab fa-facebook fa-fw mr5"></i><strong>{__("Social Login")}</strong>
          </a>
        </li>
      </ul>
      <!-- panel nav -->
    </div>
    <!-- card-header -->

    <!-- tabs content -->
    <div class="tab-content">
      <!-- General -->
      <div class="tab-pane active" id="General">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=registration">
          <div class="card-body">
            <div class="alert alert-info">
              <div class="icon">
                <i class="fa fa-info-circle fa-2x"></i>
              </div>
              <div class="text pt5">
                {__("If Registration is Free and Pro Packages enabled they will be used as optional upgrading plans")}.
              </div>
            </div>
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="registration" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Registration")}</div>
                <div class="form-text d-none d-sm-block">{__("Allow users to create accounts")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="registration_enabled">
                  <input type="checkbox" name="registration_enabled" id="registration_enabled" {if $system['registration_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Registration Type")}
              </label>
              <div class="col-md-9">
                <div class="form-check form-check-inline">
                  <input type="radio" name="registration_type" id="registration_free" value="free" class="form-check-input" {if $system['registration_type'] == "free"}checked{/if}>
                  <label class="form-check-label" for="registration_free">{__("Free")}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" name="registration_type" id="registration_paid" value="paid" class="form-check-input" {if $system['registration_type'] == "paid"}checked{/if}>
                  <label class="form-check-label" for="registration_paid">{__("Subscriptions Only")}</label>
                </div>
                <div class="form-text">
                  {__("Allow users to create accounts Free or via Subscriptions only")}<br>
                  {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/pro">{__("Pro System")}</a>
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Default User Group")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="default_custom_user_group">
                  <option value="0" {if $system['default_custom_user_group'] == '0'}selected{/if}>
                    {__("Users")}
                  </option>
                  {foreach $user_groups as $user_group}
                    <option value="{$user_group['user_group_id']}" {if $system['default_custom_user_group'] == $user_group['user_group_id']}selected{/if}>
                      {$user_group['user_group_title']}
                    </option>
                  {/foreach}
                </select>
                <div class="form-text">
                  {__("Select the default user group for new accounts")}<br>
                  {__("You can manage users groups from")} <a href="{$system['system_url']}/{$control_panel['url']}/users_groups">{__("User Groups")}</a>
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="invitation" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Invitation System")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("This option is used to register the users by invitation codes only")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="invitation_enabled">
                  <input type="checkbox" name="invitation_enabled" id="invitation_enabled" {if $system['invitation_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Invitations/User")}
              </label>
              <div class="col-md-9">
                <div class="row">
                  <div class="col-sm-8">
                    <input class="form-control" name="invitation_user_limit" value="{$system['invitation_user_limit']}">
                  </div>
                  <div class="col-sm-4">
                    <select class="form-select" name="invitation_expire_period">
                      <option {if $system['invitation_expire_period'] == "hour"}selected{/if} value="hour">{__("Hour")}</option>
                      <option {if $system['invitation_expire_period'] == "day"}selected{/if} value="day">{__("Day")}</option>
                      <option {if $system['invitation_expire_period'] == "week"}selected{/if} value="week">{__("Week")}</option>
                      <option {if $system['invitation_expire_period'] == "month"}selected{/if} value="month">{__("Month")}</option>
                      <option {if $system['invitation_expire_period'] == "year"}selected{/if} value="year">{__("Year")}</option>
                    </select>
                  </div>
                </div>
                <div class="form-text">
                  {__("Number of invitation codes allowed to each user (0 for unlimited) ")}<br>
                  {__("For example 1 code per day, 5 codes per month")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Send Method")}
              </label>
              <div class="col-md-9">
                <div class="form-check form-check-inline">
                  <input type="radio" name="invitation_send_method" id="invitation_email" value="email" class="form-check-input" {if $system['invitation_send_method'] == "email"}checked{/if}>
                  <label class="form-check-label" for="invitation_email">{__("Email")}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" name="invitation_send_method" id="invitation_sms" value="sms" class="form-check-input" {if $system['invitation_send_method'] == "sms"}checked{/if}>
                  <label class="form-check-label" for="invitation_sms">{__("SMS")}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" name="invitation_send_method" id="invitation_both" value="both" class="form-check-input" {if $system['invitation_send_method'] == "both"}checked{/if}>
                  <label class="form-check-label" for="invitation_both">{__("Both")}</label>
                </div>
                <div class="form-text">
                  {__("Select Email or SMS to send invitation link to new user's email/phone")}<br>
                  {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/settings/sms">{__("SMS Settings")}</a>
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="account_activation" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Activation Enabled")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable account activation to send activation code to user's email/phone")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="activation_enabled">
                  <input type="checkbox" name="activation_enabled" id="activation_enabled" {if $system['activation_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="adblock" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Activation Required")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable this and user will not be able to access without activation")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="activation_required">
                  <input type="checkbox" name="activation_required" id="activation_required" {if $system['activation_required']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Activation Type")}
              </label>
              <div class="col-md-9">
                <div class="form-check form-check-inline">
                  <input type="radio" name="activation_type" id="activation_email" value="email" class="form-check-input" {if $system['activation_type'] == "email"}checked{/if}>
                  <label class="form-check-label" for="activation_email">{__("Email")}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" name="activation_type" id="activation_sms" value="sms" class="form-check-input" {if $system['activation_type'] == "sms"}checked{/if}>
                  <label class="form-check-label" for="activation_sms">{__("SMS")}</label>
                </div>
                <div class="form-text">
                  {__("Select Email or SMS activation to send activation code to user's email/phone")}<br>
                  {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/settings/sms">{__("SMS Settings")}</a>
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="age_limit" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Age Restriction")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable/Disable age restriction")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="age_restriction">
                  <input type="checkbox" name="age_restriction" id="age_restriction" {if $system['age_restriction']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Minimum Age")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="minimum_age" value="{$system['minimum_age']}">
                <div class="form-text">
                  {__("The minimum age required to register (in years)")}
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="getting_started" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Getting Started")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable/Disable getting started page after registration")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="getting_started">
                  <input type="checkbox" name="getting_started" id="getting_started" {if $system['getting_started']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Required Data")}
              </label>
              <div class="col-md-9">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="getting_started_profile_image_required" id="getting_started_profile_image_required" {if $system['getting_started_profile_image_required']}checked{/if}>
                  <label class="form-check-label" for="getting_started_profile_image_required">{__("Profile Image Required")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="getting_started_location_required" id="getting_started_location_required" {if $system['getting_started_location_required']}checked{/if}>
                  <label class="form-check-label" for="getting_started_location_required">{__("Location Data Required")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="getting_started_work_required" id="getting_started_work_required" {if $system['getting_started_work_required']}checked{/if}>
                  <label class="form-check-label" for="getting_started_work_required">{__("Work Data Required")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="getting_started_education_required" id="getting_started_education_required" {if $system['getting_started_education_required']}checked{/if}>
                  <label class="form-check-label" for="getting_started_education_required">{__("Education Data Required")}</label>
                </div>
                <span class="form-text mt10">
                  {__("Such data will be mandatory when user getting started")}
                </span>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="newsletter" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Newsletter Consent")} ({__("GDPR")})</div>
                <div class="form-text d-none d-sm-block">{__("Enable/Disable newsletter consent during the registration")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="newsletter_consent">
                  <input type="checkbox" name="newsletter_consent" id="newsletter_consent" {if $system['newsletter_consent']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max Accounts/IP")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_accounts" value="{$system['max_accounts']}">
                <div class="form-text">
                  {__("The Maximum number of accounts allowed to register per IP (0 for unlimited)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Name Minimum Length")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="name_min_length" value="{$system['name_min_length']}">
                <div class="form-text">
                  {__("The First and Last name minimum length")}
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
      </div>
      <!-- General -->

      <!-- Social -->
      <div class="tab-pane" id="Social">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=social_login">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="social_share" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Social Logins")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via social media (Facebook, Twitter and etc) On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="social_login_enabled">
                  <input type="checkbox" name="social_login_enabled" id="social_login_enabled" {if $system['social_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <!-- facebook -->
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="facebook" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Facebook")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via Facebook On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="facebook_login_enabled">
                  <input type="checkbox" name="facebook_login_enabled" id="facebook_login_enabled" {if $system['facebook_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Facebook App ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="facebook_appid" value="{$system['facebook_appid']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Facebook App Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="facebook_secret" value="{$system['facebook_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>
            <!-- facebook -->

            <div class="divider"></div>

            <!-- google -->
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="google" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Google")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via Google On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="google_login_enabled">
                  <input type="checkbox" name="google_login_enabled" id="google_login_enabled" {if $system['google_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Google App ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="google_appid" value="{$system['google_appid']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Google App Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="google_secret" value="{$system['google_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>
            <!-- google -->

            <div class="divider"></div>

            <!-- twitter -->
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="twitter" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Twitter")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via Twitter On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="twitter_login_enabled">
                  <input type="checkbox" name="twitter_login_enabled" id="twitter_login_enabled" {if $system['twitter_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Twitter App ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="twitter_appid" value="{$system['twitter_appid']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Twitter App Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="twitter_secret" value="{$system['twitter_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>
            <!-- twitter -->

            <div class="divider"></div>

            <!-- linkedin -->
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="linkedin" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Linkedin")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via Linkedin On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="linkedin_login_enabled">
                  <input type="checkbox" name="linkedin_login_enabled" id="linkedin_login_enabled" {if $system['linkedin_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Linkedin App ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="linkedin_appid" value="{$system['linkedin_appid']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Linkedin App Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="linkedin_secret" value="{$system['linkedin_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>
            <!-- linkedin -->

            <div class="divider"></div>

            <!-- vk -->
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="vk" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Vkontakte")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via Vkontakte On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="vkontakte_login_enabled">
                  <input type="checkbox" name="vkontakte_login_enabled" id="vkontakte_login_enabled" {if $system['vkontakte_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Vkontakte App ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="vkontakte_appid" value="{$system['vkontakte_appid']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Vkontakte App Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="vkontakte_secret" value="{$system['vkontakte_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>
            <!-- vk -->

            <div class="divider"></div>

            <!-- wordpress -->
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="wordpress" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("WordPress")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via WordPress On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="wordpress_login_enabled">
                  <input type="checkbox" name="wordpress_login_enabled" id="wordpress_login_enabled" {if $system['wordpress_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("WordPress App ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="wordpress_appid" value="{$system['wordpress_appid']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("WordPress App Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="wordpress_secret" value="{$system['wordpress_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>
            <!-- wordpress -->

            <div class="divider"></div>

            <!-- Sngine -->
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="developers" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Sngine")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn registration/login via other Sngine website On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="sngine_login_enabled">
                  <input type="checkbox" name="sngine_login_enabled" id="sngine_login_enabled" {if $system['sngine_login_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Sngine App ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="sngine_appid" value="{$system['sngine_appid']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Sngine App Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="sngine_secret" value="{$system['sngine_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Sngine App Domain")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="sngine_app_domain" value="{$system['sngine_app_domain']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Please enter your Sngine App Domain without http:// or https://")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Sngine App Name")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="sngine_app_name" value="{$system['sngine_app_name']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Sngine App Icon")}
              </label>
              <div class="col-md-9">
                {if $system['sngine_app_icon'] == ''}
                  <div class="x-image">
                    <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'></button>
                    <div class="x-image-loader">
                      <div class="progress x-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                    <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                    <input type="hidden" class="js_x-image-input" name="sngine_app_icon" value="">
                  </div>
                {else}
                  <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$system['sngine_app_icon']}')">
                    <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'></button>
                    <div class="x-image-loader">
                      <div class="progress x-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                    <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                    <input type="hidden" class="js_x-image-input" name="sngine_app_icon" value="{$system['sngine_app_icon']}">
                  </div>
                {/if}
              </div>
            </div>
            <!-- Sngine -->

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
      </div>
      <!-- Social -->
    </div>
    <!-- tabs content -->

  {elseif $sub_view == "accounts"}

    <!-- card-header -->
    <div class="card-header with-icon with-nav">
      <!-- panel title -->
      <div class="mb20">
        <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Accounts")}
      </div>
      <!-- panel title -->

      <!-- panel nav -->
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#General" data-bs-toggle="tab">
            <i class="fa fa-user-cog fa-fw mr5"></i><strong>{__("General")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Profile" data-bs-toggle="tab">
            <i class="fa fa-address-card fa-fw mr5"></i><strong>{__("Profile")}</strong>
          </a>
        </li>
      </ul>
      <!-- panel nav -->
    </div>
    <!-- card-header -->

    <!-- tab-content -->
    <div class="tab-content">
      <!-- General -->
      <div class="tab-pane active" id="General">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=accounts">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="accounts_switcher" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Switch Accounts")}</div>
                <div class="form-text d-none d-sm-block">{__("Allow users to switch between multiple accounts")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="switch_accounts_enabled">
                  <input type="checkbox" name="switch_accounts_enabled" id="switch_accounts_enabled" {if $system['switch_accounts_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="genders" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Disable Genders")}</div>
                <div class="form-text d-none d-sm-block">{__("If disabled genders will be hidden for the users")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="genders_disabled">
                  <input type="checkbox" name="genders_disabled" id="genders_disabled" {if $system['genders_disabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="username" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Show Usernames Only")}</div>
                <div class="form-text d-none d-sm-block">{__("If disabled full names will be displayed instead")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="show_usernames_enabled">
                  <input type="checkbox" name="show_usernames_enabled" id="show_usernames_enabled" {if $system['show_usernames_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="special_characters" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Allow Special Characters")}</div>
                <div class="form-text d-none d-sm-block">{__("Allow special Characters in user's first & last name")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="special_characters_enabled">
                  <input type="checkbox" name="special_characters_enabled" id="special_characters_enabled" {if $system['special_characters_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="delete_user" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Delete Account")} ({__("GDPR")})</div>
                <div class="form-text d-none d-sm-block">{__("Allow users to delete their account")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="delete_accounts_enabled">
                  <input type="checkbox" name="delete_accounts_enabled" id="delete_accounts_enabled" {if $system['delete_accounts_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="user_information" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Download User Information")} ({__("GDPR")})</div>
                <div class="form-text d-none d-sm-block">{__("Allow users to download their account information from settings page")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="download_info_enabled">
                  <input type="checkbox" name="download_info_enabled" id="download_info_enabled" {if $system['download_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="verification" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Verification Requests")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the verification requests from users & pages On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="verification_requests">
                  <input type="checkbox" name="verification_requests" id="verification_requests" {if $system['verification_requests']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Verification Documents")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable it to make verification documents required")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="verification_docs_required">
                  <input type="checkbox" name="verification_docs_required" id="verification_docs_required" {if $system['verification_docs_required']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Required for Posts")}</div>
                <div class="form-text d-none d-sm-block">{__("If enabled then verification will be required to publish posts")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="verification_for_posts">
                  <input type="checkbox" name="verification_for_posts" id="verification_for_posts" {if $system['verification_for_posts']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Required for Monetization")}</div>
                <div class="form-text d-none d-sm-block">{__("If enabled then verification will be required to enable monetization")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="verification_for_monetization">
                  <input type="checkbox" name="verification_for_monetization" id="verification_for_monetization" {if $system['verification_for_monetization']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6">{__("Required for Adult Content")}</div>
                <div class="form-text d-none d-sm-block">{__("If enabled then verification will be required to share or consume adult content")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="verification_for_adult_content">
                  <input type="checkbox" name="verification_for_adult_content" id="verification_for_adult_content" {if $system['verification_for_adult_content']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="followings" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Disable Friend Request After Decline")}</div>
                <div class="form-text d-none d-sm-block">{__("If enabled user A will be able to send friendship request to user B again")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="disable_declined_friendrequest">
                  <input type="checkbox" name="disable_declined_friendrequest" id="disable_declined_friendrequest" {if $system['disable_declined_friendrequest']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max Friends/User")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_friends" value="{$system['max_friends']}">
                <div class="form-text">
                  {__("The Maximum number of friends allowed per User (0 for unlimited)")}
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
      </div>
      <!-- General -->

      <!-- Profile -->
      <div class="tab-pane" id="Profile">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=profile">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="relationship" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Relationship Status")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the Relationship Status On/Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="relationship_info_enabled">
                  <input type="checkbox" name="relationship_info_enabled" id="relationship_info_enabled" {if $system['relationship_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="website" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Website")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the Website On/Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="website_info_enabled">
                  <input type="checkbox" name="website_info_enabled" id="website_info_enabled" {if $system['website_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="biography" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("About Me")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the About Me On/Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="biography_info_enabled">
                  <input type="checkbox" name="biography_info_enabled" id="biography_info_enabled" {if $system['biography_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="jobs" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Work Info")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the Work info On/Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="work_info_enabled">
                  <input type="checkbox" name="work_info_enabled" id="work_info_enabled" {if $system['work_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="map" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Location Info")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the Location info On/Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="location_info_enabled">
                  <input type="checkbox" name="location_info_enabled" id="location_info_enabled" {if $system['location_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="education" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Education Info")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the Education info On/Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="education_info_enabled">
                  <input type="checkbox" name="education_info_enabled" id="education_info_enabled" {if $system['education_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="social_share" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Social Links")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the Social Links On/Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="social_info_enabled">
                  <input type="checkbox" name="social_info_enabled" id="social_info_enabled" {if $system['social_info_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="design" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Profile Design")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Allow users to upload background image to their profiles")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="system_profile_background_enabled">
                  <input type="checkbox" name="system_profile_background_enabled" id="system_profile_background_enabled" {if $system['system_profile_background_enabled']}checked{/if}>
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
          </div>
          <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
          </div>
        </form>
      </div>
      <!-- Profile -->
    </div>
    <!-- tab-content -->

  {elseif $sub_view == "email"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Email")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <form class="js_ajax-forms" data-url="admin/settings.php?edit=email">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="email" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("SMTP")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Enable/Disable SMTP email system")}<br />
              {__("PHP mail() function will be used in case of disabled")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="email_smtp_enabled">
              <input type="checkbox" name="email_smtp_enabled" id="email_smtp_enabled" {if $system['email_smtp_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="authentication" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("SMTP Require Authentication")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable/Disable SMTP authentication")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="email_smtp_authentication">
              <input type="checkbox" name="email_smtp_authentication" id="email_smtp_authentication" {if $system['email_smtp_authentication']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="ssl" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("SMTP SSL Encryption")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Enable/Disable SMTP SSL encryption")}<br />
              {__("TLS encryption will be used in case of disabled")}
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="email_smtp_ssl">
              <input type="checkbox" name="email_smtp_ssl" id="email_smtp_ssl" {if $system['email_smtp_ssl']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("SMTP Server")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="email_smtp_server" value="{$system['email_smtp_server']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("SMTP Port")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="email_smtp_port" value="{$system['email_smtp_port']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("SMTP Username")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="email_smtp_username" value="{$system['email_smtp_username']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("SMTP Password")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="email_smtp_password" value="{$system['email_smtp_password']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Set From")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="email_smtp_setfrom" value="{$system['email_smtp_setfrom']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Set the From email address")}, {__("For example: email@domain.com")}
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
        <button type="button" class="btn btn-danger js_admin-tester" data-handle="smtp">
          <i class="fa fa-bolt mr10"></i>{__("Test Connection")}
        </button>
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {elseif $sub_view == "sms"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("SMS")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <!-- SMS -->
    <form class="js_ajax-forms" data-url="admin/settings.php?edit=sms">
      <div class="card-body">

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("SMS Provider")}
          </label>
          <div class="col-md-9">
            <div>
              <!-- Twilio -->
              <input class="x-hidden input-label" type="radio" name="sms_provider" value="twilio" id="sms_twilio" {if $system['sms_provider'] == "twilio"}checked{/if} />
              <label class="button-label" for="sms_twilio">
                <div class="icon">
                  {include file='__svg_icons.tpl' icon="twilio" width="32px" height="32px"}
                </div>
                <div class="title">{__("Twilio")}</div>
              </label>
              <!-- Twilio -->
              <!-- BulkSMS -->
              <input class="x-hidden input-label" type="radio" name="sms_provider" value="bulksms" id="sms_bulksms" {if $system['sms_provider'] == "bulksms"}checked{/if} />
              <label class="button-label" for="sms_bulksms">
                <div class="icon">
                  {include file='__svg_icons.tpl' icon="bulksms" width="52px" height="32px"}
                </div>
                <div class="title">{__("BulkSMS")}</div>
              </label>
              <!-- BulkSMS -->
              <!-- Infobip -->
              <input class="x-hidden input-label" type="radio" name="sms_provider" value="infobip" id="sms_infobip" {if $system['sms_provider'] == "infobip"}checked{/if} />
              <label class="button-label" for="sms_infobip">
                <div class="icon">
                  {include file='__svg_icons.tpl' icon="infobip" width="52px" height="32px"}
                </div>
                <div class="title">{__("Infobip")}</div>
              </label>
              <!-- Infobip -->
              <!-- Msg91 -->
              <input class="x-hidden input-label" type="radio" name="sms_provider" value="msg91" id="sms_msg91" {if $system['sms_provider'] == "msg91"}checked{/if} />
              <label class="button-label" for="sms_msg91">
                <div class="icon">
                  {include file='__svg_icons.tpl' icon="msg91" width="52px" height="32px"}
                </div>
                <div class="title">{__("Msg91")}</div>
              </label>
              <!-- Msg91 -->
            </div>

            <div class="form-text">
              {__("Select your default SMS provider")}<br />
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("SMS Limit")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="sms_limit" value="{$system['sms_limit']}">
            <div class="form-text">
              {__("Set the maximum number of SMS messages that can be sent per hour")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <!-- Twilio -->
        <div class="heading-small mb20">
          {__("Twilio")}
        </div>
        <div class="pl-md-4">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Twilio Account SID")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="twilio_sid" value="{$system['twilio_sid']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Twilio Auth Token")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="twilio_token" value="{$system['twilio_token']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Twilio Phone Number")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="twilio_phone" value="{$system['twilio_phone']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>
        </div>
        <!-- Twilio -->

        <div class="divider"></div>

        <!-- BulkSMS -->
        <div class="heading-small mb20">
          {__("BulkSMS")}
        </div>
        <div class="pl-md-4">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("BulkSMS Username")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="bulksms_username" value="{$system['bulksms_username']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("BulkSMS Password")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="bulksms_password" value="{$system['bulksms_password']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>
        </div>
        <!-- BulkSMS -->

        <div class="divider"></div>

        <!-- Infobip -->
        <div class="heading-small mb20">
          {__("Infobip")}
        </div>
        <div class="pl-md-4">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Infobip Username")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="infobip_username" value="{$system['infobip_username']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Infobip Password")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="infobip_password" value="{$system['infobip_password']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>
        </div>
        <!-- Infobip -->

        <div class="divider"></div>

        <!-- Msg91 -->
        <div class="heading-small mb20">
          {__("Msg91")}
        </div>
        <div class="pl-md-4">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Msg91 AuthKey")}
            </label>
            <div class="col-md-9">
              {if !$user->_data['user_demo']}
                <input type="text" class="form-control" name="msg91_authkey" value="{$system['msg91_authkey']}">
              {else}
                <input type="password" class="form-control" value="*********">
              {/if}
            </div>
          </div>
        </div>
        <!-- Msg91 -->

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Test Phone Number")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="system_phone" value="{$system['system_phone']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Your phone number to test the SMS service i.e +12344567890")}<br />
              {__("A test SMS will be sent to this phone number when you test the connection")}
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
        <button type="button" class="btn btn-danger js_admin-tester" data-handle="sms">
          <i class="fa fa-bolt mr10"></i>{__("Test Connection")}
        </button>
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>
    <!-- SMS -->

  {elseif $sub_view == "notifications"}

    <!-- card-header -->
    <div class="card-header with-icon with-nav">
      <!-- panel title -->
      <div class="mb20">
        <i class="fa fa-bell mr10"></i>{__("Settings")} &rsaquo; {__("Notifications")}
      </div>
      <!-- panel title -->

      <!-- panel nav -->
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#Website" data-bs-toggle="tab">
            <i class="fa fa-bell fa-fw mr5"></i><strong>{__("Website Notifications")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Email" data-bs-toggle="tab">
            <i class="fa fa-envelope fa-fw mr5"></i><strong>{__("Email Notifications")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Push" data-bs-toggle="tab">
            <i class="fas fa-broadcast-tower fa-fw mr5"></i><strong>{__("Push Notifications")}</strong>
          </a>
        </li>
      </ul>
      <!-- panel nav -->
    </div>
    <!-- card-header -->

    <!-- tabs content -->
    <div class="tab-content">
      <!-- Website Notifications -->
      <div class="tab-pane active" id="Website">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=website_notifications">
          <div class="card-body">

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="profile_notifications" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Profile Visit Notification")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the profile visit notification On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="profile_notification_enabled">
                  <input type="checkbox" name="profile_notification_enabled" id="profile_notification_enabled" {if $system['profile_notification_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="browser_notifications" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Browser Notifications")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the browser notifications On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="browser_notifications_enabled">
                  <input type="checkbox" name="browser_notifications_enabled" id="browser_notifications_enabled" {if $system['browser_notifications_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="noty_notifications" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Noty Notifications")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Turn the noty notifications On and Off")} (<a target="_blank" href="{$system['system_url']}/content/themes/{$system['theme']}/images/screenshots/noty_notification.png">{__("preview")}</a>)
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="noty_notifications_enabled">
                  <input type="checkbox" name="noty_notifications_enabled" id="noty_notifications_enabled" {if $system['noty_notifications_enabled']}checked{/if}>
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
          </div>
          <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
          </div>
        </form>
      </div>
      <!-- Website Notifications -->

      <!-- Email Notifications -->
      <div class="tab-pane" id="Email">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=email_notifications">
          <div class="card-body">

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="newsletter" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Email Notifications")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable/Disable email notifications system")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="email_notifications">
                  <input type="checkbox" name="email_notifications" id="email_notifications" {if $system['email_notifications']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Email User When")}
              </label>
              <div class="col-md-9">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_post_likes" id="email_post_likes" {if $system['email_post_likes']}checked{/if}>
                  <label class="form-check-label" for="email_post_likes">{__("Someone reacted to his post")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_post_comments" id="email_post_comments" {if $system['email_post_comments']}checked{/if}>
                  <label class="form-check-label" for="email_post_comments">{__("Someone commented on his post")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_post_shares" id="email_post_shares" {if $system['email_post_shares']}checked{/if}>
                  <label class="form-check-label" for="email_post_shares">{__("Someone shared his post")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_wall_posts" id="email_wall_posts" {if $system['email_wall_posts']}checked{/if}>
                  <label class="form-check-label" for="email_wall_posts">{__("Someone posted on his timeline")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_mentions" id="email_mentions" {if $system['email_mentions']}checked{/if}>
                  <label class="form-check-label" for="email_mentions">{__("Someone mentioned him")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_profile_visits" id="email_profile_visits" {if $system['email_profile_visits']}checked{/if}>
                  <label class="form-check-label" for="email_profile_visits">{__("Someone visited his profile")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_friend_requests" id="email_friend_requests" {if $system['email_friend_requests']}checked{/if}>
                  <label class="form-check-label" for="email_friend_requests">{__("Someone sent him or accepted his friend requset")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_user_verification" id="email_user_verification" {if $system['email_user_verification']}checked{/if}>
                  <label class="form-check-label" for="email_user_verification">{__("Admin approved/declined my verification requests")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_user_post_approval" id="email_user_post_approval" {if $system['email_user_post_approval']}checked{/if}>
                  <label class="form-check-label" for="email_user_post_approval">{__("Admin approved my pending posts")}</label>
                </div>
              </div>
            </div>

            <div class="divider dashed"></div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Email Admin When")}
              </label>
              <div class="col-md-9">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_admin_verifications" id="email_admin_verifications" {if $system['email_admin_verifications']}checked{/if}>
                  <label class="form-check-label" for="email_admin_verifications">{__("Verification request sent by user/page")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="email_admin_post_approval" id="email_admin_post_approval" {if $system['email_admin_post_approval']}checked{/if}>
                  <label class="form-check-label" for="email_admin_post_approval">{__("Post published and needs approval")}</label>
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
      </div>
      <!-- Email Notifications -->

      <!-- Push Notifications -->
      <div class="tab-pane" id="Push">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=push_notifications">
          <div class="card-body">

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="onesignal" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("OneSignal Push Notifications")}</div>
                <div class="form-text d-none d-sm-block">{__("Turn the OneSignal push notification On and Off")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="onesignal_notification_enabled">
                  <input type="checkbox" name="onesignal_notification_enabled" id="onesignal_notification_enabled" {if $system['onesignal_notification_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("OneSignal APP ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="onesignal_app_id" value="{$system['onesignal_app_id']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("OneSignal REST API Key")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="onesignal_api_key" value="{$system['onesignal_api_key']}">
                {else}
                  <input type="password" class="form-control" value="*********">
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
      </div>
      <!-- Push Notifications -->
    </div>

  {elseif $sub_view == "chat"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Chat")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <!-- Chat -->
    <form class="js_ajax-forms" data-url="admin/settings.php?edit=chat">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="chat" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Chat Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the chat system On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="chat_enabled">
              <input type="checkbox" name="chat_enabled" id="chat_enabled" {if $system['chat_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="voice_notes" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Voice Notes")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the voice notes in chat On and Off")}<br>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="voice_notes_chat_enabled">
              <input type="checkbox" name="voice_notes_chat_enabled" id="voice_notes_chat_enabled" {if $system['voice_notes_chat_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="chat_status" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("User Status Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the Last Seen On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="chat_status_enabled">
              <input type="checkbox" name="chat_status_enabled" id="chat_status_enabled" {if $system['chat_status_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="chat_typing" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Typing Status Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the Typing Status On and Off")} ({__("Needs a good server to work fine")})</div>
          </div>
          <div class="text-end">
            <label class="switch" for="chat_typing_enabled">
              <input type="checkbox" name="chat_typing_enabled" id="chat_typing_enabled" {if $system['chat_typing_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="chat_seen" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Seen Status Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the Seen Status On and Off")} ({__("Needs a good server to work fine")})</div>
          </div>
          <div class="text-end">
            <label class="switch" for="chat_seen_enabled">
              <input type="checkbox" name="chat_seen_enabled" id="chat_seen_enabled" {if $system['chat_seen_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="delete" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Delete For Everyone")}</div>
            <div class="form-text d-none d-sm-block">{__("Permanently remove the conversation for all chat members when user delete it")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="chat_permanently_delete_enabled">
              <input type="checkbox" name="chat_permanently_delete_enabled" id="chat_permanently_delete_enabled" {if $system['chat_permanently_delete_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="call_audio" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Audio Call Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the audio call system On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="audio_call_enabled">
              <input type="checkbox" name="audio_call_enabled" id="audio_call_enabled" {if $system['audio_call_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="call_video" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Video Call Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the video call system On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="video_call_enabled">
              <input type="checkbox" name="video_call_enabled" id="video_call_enabled" {if $system['video_call_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Twilio Account SID")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="twilio_sid" value="{$system['twilio_sid']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Twilio API SID")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="twilio_apisid" value="{$system['twilio_apisid']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Twilio API SECRET")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="twilio_apisecret" value="{$system['twilio_apisecret']}">
            {else}
              <input type="password" class="form-control" value="*********">
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
    <!-- Chat -->

  {elseif $sub_view == "live"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Live Stream")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <!-- Live -->
    <form class="js_ajax-forms" data-url="admin/settings.php?edit=live">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="live" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Live Stream Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn the live stream system On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="live_enabled">
              <input type="checkbox" name="live_enabled" id="live_enabled" {if $system['live_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Agora App ID")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="agora_app_id" value="{$system['agora_app_id']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Agora App Certificate")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="agora_app_certificate" value="{$system['agora_app_certificate']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="cloud_save" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Save Live Videos")}</div>
            <div class="form-text d-none d-sm-block">
              {__("Turn the save live stream videos On and Off")}<br>
            </div>
          </div>
          <div class="text-end">
            <label class="switch" for="save_live_enabled">
              <input type="checkbox" name="save_live_enabled" id="save_live_enabled" {if $system['save_live_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Agora Customer ID")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="agora_customer_id" value="{$system['agora_customer_id']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Agora Customer Secret")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="agora_customer_certificate" value="{$system['agora_customer_certificate']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {"S3"} {__("Bucket Name")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="agora_s3_bucket" value="{$system['agora_s3_bucket']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Your Amazon S3 bucket name")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {"S3"} {__("Bucket Region")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="agora_s3_region">
              <option value="us-east-1" {if $system['agora_s3_region'] == "us-east-1"}selected{/if}>US East (N. Virginia) us-east-1</option>
              <option value="us-east-2" {if $system['agora_s3_region'] == "us-east-2"}selected{/if}>US East (Ohio) us-east-2</option>
              <option value="us-west-1" {if $system['agora_s3_region'] == "us-west-1"}selected{/if}>US West (N. California) us-west-1</option>
              <option value="us-west-2" {if $system['agora_s3_region'] == "us-west-2"}selected{/if}>US West (Oregon) us-west-2</option>
              <option value="eu-west-1" {if $system['agora_s3_region'] == "eu-west-1"}selected{/if}>EU (Ireland) eu-west-1</option>
              <option value="eu-west-2" {if $system['agora_s3_region'] == "eu-west-2"}selected{/if}>EU (London) eu-west-2</option>
              <option value="eu-west-3" {if $system['agora_s3_region'] == "eu-west-3"}selected{/if}>EU (Paris) eu-west-3</option>
              <option value="eu-central-1" {if $system['agora_s3_region'] == "eu-central-1"}selected{/if}>EU (Frankfurt) eu-central-1</option>
              <option value="ap-southeast-1" {if $system['agora_s3_region'] == "ap-southeast-1"}selected{/if}>Asia Pacific (Singapore) ap-southeast-1</option>
              <option value="ap-southeast-2" {if $system['agora_s3_region'] == "ap-southeast-2"}selected{/if}>Asia Pacific (Sydney) ap-southeast-2</option>
              <option value="ap-northeast-1" {if $system['agora_s3_region'] == "ap-northeast-1"}selected{/if}>Asia Pacific (Tokyo) ap-northeast-1</option>
              <option value="ap-northeast-2" {if $system['agora_s3_region'] == "ap-northeast-2"}selected{/if}>Asia Pacific (Seoul) ap-northeast-2</option>
              <option value="sa-east-1" {if $system['agora_s3_region'] == "sa-east-1"}selected{/if}>South America (São Paulo) sa-east-1</option>
              <option value="ca-central-1" {if $system['agora_s3_region'] == "ca-central-1"}selected{/if}>Canada (Central) ca-central-1</option>
              <option value="ap-south-1" {if $system['agora_s3_region'] == "ap-south-1"}selected{/if}>Asia Pacific (Mumbai)</option>
            </select>
            <div class="form-text">
              {__("Your Amazon S3 bucket region")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {"S3"} {__("Access Key ID")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="agora_s3_key" value="{$system['agora_s3_key']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Your Amazon S3 Access Key ID")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {"S3"} {__("Access Key Secret")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="agora_s3_secret" value="{$system['agora_s3_secret']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
            <div class="form-text">
              {__("Your Amazon S3 Access Key Secret")}
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
        <button type="button" class="btn btn-danger js_admin-tester" data-handle="s3-agora">
          <i class="fa fa-bolt mr10"></i> {__("Test Connection")} ({__("S3")})
        </button>
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>
    <!-- Live -->

  {elseif $sub_view == "uploads"}

    <!-- card-header -->
    <div class="card-header with-icon with-nav">
      <!-- panel title -->
      <div class="mb20">
        <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Uploads")}
      </div>
      <!-- panel title -->

      <!-- panel nav -->
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#General" data-bs-toggle="tab">
            <i class="fa fa-upload fa-fw mr5"></i><strong class="pr5">{__("General")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Cloud" data-bs-toggle="tab">
            <i class="fas fa-cloud-upload-alt fa-fw mr5"></i><strong class="pr5">{__("Cloud Hosting")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#FTP" data-bs-toggle="tab">
            <i class="fa fa-server fa-fw mr5"></i><strong class="pr5">{__("FTP")}</strong>
          </a>
        </li>
      </ul>
      <!-- panel nav -->
    </div>
    <!-- card-header -->

    <!-- tabs content -->
    <div class="tab-content">
      <!-- General -->
      <div class="tab-pane active" id="General">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=uploads">
          <div class="card-body">
            <div class="alert alert-warning">
              <div class="icon">
                <i class="fa fa-exclamation-triangle fa-2x"></i>
              </div>
              <div class="text">
                {__("Your server max upload size")} = {$max_upload_size}<br>
                {__("You can't upload files larger than")} {$max_upload_size} - {__("To upload larger files, contact your hosting provider")}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Uploads Directory")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="uploads_directory" value="{$system['uploads_directory']}">
                <div class="form-text">
                  {__("The path of uploads local directory")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Uploads Prefix")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="uploads_prefix" value="{$system['uploads_prefix']}">
                <div class="form-text">
                  {__("Add a prefix to the uploaded files (No spaces or special characters only like 'mysite' or 'my_site')")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Uploads CDN Endpoint")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="uploads_cdn_url" value="{$system['uploads_cdn_url']}">
                <div class="form-text">
                  {__("Your CDN URL like AWS CloudFront")}
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="photos" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Photo Upload")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable photo upload to share & upload photos to the site")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="photos_enabled">
                  <input type="checkbox" name="photos_enabled" id="photos_enabled" {if $system['photos_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Photo Upload in Comments")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable photo upload in comments")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="comments_photos_enabled">
                  <input type="checkbox" name="comments_photos_enabled" id="comments_photos_enabled" {if $system['comments_photos_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Photo Upload in Chat")} </div>
                <div class="form-text d-none d-sm-block">{__("Enable photo upload in chat")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="chat_photos_enabled">
                  <input type="checkbox" name="chat_photos_enabled" id="chat_photos_enabled" {if $system['chat_photos_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                <div style="width: 40px; height: 40px;"></div>
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Photo Upload in Blogs and Forums")} </div>
                <div class="form-text d-none d-sm-block">{__("Enable photo upload in articles and forums threads")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="tinymce_photos_enabled">
                  <input type="checkbox" name="tinymce_photos_enabled" id="tinymce_photos_enabled" {if $system['tinymce_photos_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max Photo Size")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_photo_size" value="{$system['max_photo_size']}">
                <div class="form-text">
                  {__("The Maximum size of uploaded photo in posts")} {__("in kilobytes (1M = 1024KB)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Photo Quality")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="uploads_quality">
                  <option value="high" {if $system['uploads_quality'] == "high"}selected{/if}>{__("High quality photos with low compression")}</option>
                  <option value="medium" {if $system['uploads_quality'] == "medium"}selected{/if}>{__("Medium quality photos with medium compression")}</option>
                  <option value="low" {if $system['uploads_quality'] == "low"}selected{/if}>{__("Low quality photos with high compression")}</option>
                </select>
              </div>
            </div>

            <div class="divider dashed"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="resolution" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Cover Photo Resolution Limit")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable cover photo limit (Minimum width 1108px & Minimum height 360px)")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="limit_cover_photo">
                  <input type="checkbox" name="limit_cover_photo" id="limit_cover_photo" {if $system['limit_cover_photo']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="gif" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Animated Images for Avatars/Covers")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable user to upload animated images for avarats and covers")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="allow_animated_images">
                  <input type="checkbox" name="allow_animated_images" id="allow_animated_images" {if $system['allow_animated_images']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max Cover Photo Size")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_cover_size" value="{$system['max_cover_size']}">
                <div class="form-text">
                  {__("The Maximum size of cover photo")} {__("in kilobytes (1 M = 1024 KB)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max Profile Photo Size")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_avatar_size" value="{$system['max_avatar_size']}">
                <div class="form-text">
                  {__("The Maximum size of profile photo")} {__("in kilobytes (1 M = 1024 KB)")}
                </div>
              </div>
            </div>

            <div class="divider dashed"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="watermark" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Watermark Images")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable it to add watermark icon to all uploaded photos (except: profile pictures and cover images)")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="watermark_enabled">
                  <input type="checkbox" name="watermark_enabled" id="watermark_enabled" {if $system['watermark_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Icon")}
              </label>
              <div class="col-md-9">
                {if $system['watermark_icon'] == ''}
                  <div class="x-image">
                    <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

                    </button>
                    <div class="x-image-loader">
                      <div class="progress x-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                    <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                    <input type="hidden" class="js_x-image-input" name="watermark_icon" value="">
                  </div>
                {else}
                  <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$system['watermark_icon']}')">
                    <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'>

                    </button>
                    <div class="x-image-loader">
                      <div class="progress x-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                    <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                    <input type="hidden" class="js_x-image-input" name="watermark_icon" value="{$system['watermark_icon']}">
                  </div>
                {/if}
                <div class="form-text">
                  {__("Upload your watermark icon (PNG is recommended)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Position")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="watermark_position">
                  <option {if $system['watermark_position'] == "top left"}selected{/if} value="top left">{__("Top Left")}</option>
                  <option {if $system['watermark_position'] == "top right"}selected{/if} value="top right">{__("Top Right")}</option>
                  <option {if $system['watermark_position'] == "top"}selected{/if} value="top">{__("Top")}</option>
                  <option {if $system['watermark_position'] == "bottom left"}selected{/if} value="bottom left">{__("Bottom Left")}</option>
                  <option {if $system['watermark_position'] == "bottom right"}selected{/if} value="bottom right">{__("Bottom Right")}</option>
                  <option {if $system['watermark_position'] == "bottom"}selected{/if} value="bottom">{__("Bottom")}</option>
                  <option {if $system['watermark_position'] == "left"}selected{/if} value="left">{__("Left")}</option>
                  <option {if $system['watermark_position'] == "right"}selected{/if} value="right">{__("Right")}</option>
                </select>
                <div class="form-text">
                  {__("Select the position (the anchor point) of your watermark icon")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Opacity")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="watermark_opacity" value="{$system['watermark_opacity']}">
                <div class="form-text">
                  {__("The opacity level of the watermark icon (value between 0 - 1)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark X Offset")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="watermark_xoffset" value="{$system['watermark_xoffset']}">
                <div class="form-text">
                  {__("Horizontal offset in pixels")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Y Offset")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="watermark_yoffset" value="{$system['watermark_yoffset']}">
                <div class="form-text">
                  {__("Vertical offset in pixels")}
                </div>
              </div>
            </div>

            <div class="divider dashed"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="adult" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Adult Images Detection")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable it to detect the adult images and system will blur or delete them")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="adult_images_enabled">
                  <input type="checkbox" name="adult_images_enabled" id="adult_images_enabled" {if $system['adult_images_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Adult Images Action")}
              </label>
              <div class="col-md-9">
                <div class="form-check form-check-inline">
                  <input type="radio" name="adult_images_action" id="action_blue" value="blur" class="form-check-input" {if $system['adult_images_action'] == "blur"}checked{/if}>
                  <label class="form-check-label" for="action_blue">{__("Blur")}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input type="radio" name="adult_images_action" id="action_delete" value="delete" class="form-check-input" {if $system['adult_images_action'] == "delete"}checked{/if}>
                  <label class="form-check-label" for="action_delete">{__("Delete")}</label>
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Google Vision API Key")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="adult_images_api_key" value="{$system['adult_images_api_key']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your Cloud Vision API Key")}
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="videos" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Video Upload")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable video upload to share & upload videos to the site")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="videos_enabled">
                  <input type="checkbox" name="videos_enabled" id="videos_enabled" {if $system['videos_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max video size")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_video_size" value="{$system['max_video_size']}">
                <div class="form-text">
                  {__("The Maximum size of uploaded video in posts")} {__("in kilobytes (1M = 1024KB)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Video extensions")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="video_extensions" value="{$system['video_extensions']}">
                <div class="form-text">
                  {__("Allowed video extensions (separated with comma ',)")}
                </div>
              </div>
            </div>

            <div class="divider dashed"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="ffmpeg" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("FFMPEG Enabled")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable FFMPEG to convert and optimize videos to mp4")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="ffmpeg_enabled">
                  <input type="checkbox" name="ffmpeg_enabled" id="ffmpeg_enabled" {if $system['ffmpeg_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("FFMPEG Binary Path")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="ffmpeg_path" value="{$system['ffmpeg_path']}">
                <div class="form-text">
                  {__("Example: Linux(/usr/bin/ffmpeg) or Windows(C:\\ffmpeg\bin\ffmpeg.exe)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("FFMPEG Conversion Speed")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="ffmpeg_speed">
                  <option value="ultrafast" {if $system['ffmpeg_speed'] == "ultrafast"}selected{/if}>{__("Ultrafast")}</option>
                  <option value="superfast" {if $system['ffmpeg_speed'] == "superfast"}selected{/if}>{__("Superfast")}</option>
                  <option value="veryfast" {if $system['ffmpeg_speed'] == "veryfast"}selected{/if}>{__("Veryfast")}</option>
                  <option value="faster" {if $system['ffmpeg_speed'] == "faster"}selected{/if}>{__("Faster")}</option>
                  <option value="fast" {if $system['ffmpeg_speed'] == "fast"}selected{/if}>{__("Fast")}</option>
                  <option value="medium" {if $system['ffmpeg_speed'] == "medium"}selected{/if}>{__("Medium")}</option>
                  <option value="slow" {if $system['ffmpeg_speed'] == "slow"}selected{/if}>{__("Slow")}</option>
                  <option value="slower" {if $system['ffmpeg_speed'] == "slower"}selected{/if}>{__("Slower")}</option>
                  <option value="veryslow" {if $system['ffmpeg_speed'] == "veryslow"}selected{/if}>{__("Veryslow")}</option>
                </select>
                <div class="form-text">
                  {__("Slow speed gives you better compression and quality and vice versa")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Video Resolutions")}
              </label>
              <div class="col-md-9">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="ffmpeg_240p_enabled" id="ffmpeg_240p_enabled" {if $system['ffmpeg_240p_enabled']}checked{/if}>
                  <label class="form-check-label" for="ffmpeg_240p_enabled">{__("240p Resolution")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="ffmpeg_360p_enabled" id="ffmpeg_360p_enabled" {if $system['ffmpeg_360p_enabled']}checked{/if}>
                  <label class="form-check-label" for="ffmpeg_360p_enabled">{__("360p Resolution")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="ffmpeg_480p_enabled" id="ffmpeg_480p_enabled" {if $system['ffmpeg_480p_enabled']}checked{/if}>
                  <label class="form-check-label" for="ffmpeg_480p_enabled">{__("480p Resolution")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="ffmpeg_720p_enabled" id="ffmpeg_720p_enabled" {if $system['ffmpeg_720p_enabled']}checked{/if}>
                  <label class="form-check-label" for="ffmpeg_720p_enabled">{__("720p Resolution")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="ffmpeg_1080p_enabled" id="ffmpeg_1080p_enabled" {if $system['ffmpeg_1080p_enabled']}checked{/if}>
                  <label class="form-check-label" for="ffmpeg_1080p_enabled">{__("1080p Resolution")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="ffmpeg_1440p_enabled" id="ffmpeg_1440p_enabled" {if $system['ffmpeg_1440p_enabled']}checked{/if}>
                  <label class="form-check-label" for="ffmpeg_1440p_enabled">{__("1440p Resolution")}</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="ffmpeg_2160p_enabled" id="ffmpeg_2160p_enabled" {if $system['ffmpeg_2160p_enabled']}checked{/if}>
                  <label class="form-check-label" for="ffmpeg_2160p_enabled">{__("2160p Resolution")}</label>
                </div>
                <span class="form-text mt10">
                  {__("Select the resolutions you want to convert your videos to")}
                </span>
              </div>
            </div>

            <div class="divider dashed"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="watermark" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Watermark Videos")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable it to add watermark icon to all uploaded videos (Note: FFmpeg must be enabled)")}
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="watermark_videos_enabled">
                  <input type="checkbox" name="watermark_videos_enabled" id="watermark_videos_enabled" {if $system['watermark_videos_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Icon")}
              </label>
              <div class="col-md-9">
                {if $system['watermark_videos_icon'] == ''}
                  <div class="x-image">
                    <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

                    </button>
                    <div class="x-image-loader">
                      <div class="progress x-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                    <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                    <input type="hidden" class="js_x-image-input" name="watermark_videos_icon" value="">
                  </div>
                {else}
                  <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$system['watermark_videos_icon']}')">
                    <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'>

                    </button>
                    <div class="x-image-loader">
                      <div class="progress x-progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                    <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
                    <input type="hidden" class="js_x-image-input" name="watermark_videos_icon" value="{$system['watermark_videos_icon']}">
                  </div>
                {/if}
                <div class="form-text">
                  {__("Upload your watermark icon (PNG is recommended)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Position")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="watermark_videos_position">
                  <option {if $system['watermark_videos_position'] == "top_left"}selected{/if} value="top_left">{__("Top Left")}</option>
                  <option {if $system['watermark_videos_position'] == "top_right"}selected{/if} value="top_right">{__("Top Right")}</option>
                  <option {if $system['watermark_videos_position'] == "center"}selected{/if} value="center">{__("Center")}</option>
                  <option {if $system['watermark_videos_position'] == "bottom_left"}selected{/if} value="bottom_left">{__("Bottom Left")}</option>
                  <option {if $system['watermark_videos_position'] == "bottom_right"}selected{/if} value="bottom_right">{__("Bottom Right")}</option>
                </select>
                <div class="form-text">
                  {__("Select the position (the anchor point) of your watermark icon")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Opacity")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="watermark_videos_opacity" value="{$system['watermark_videos_opacity']}">
                <div class="form-text">
                  {__("The opacity level of the watermark icon (value between 0 - 1)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark X Offset")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="watermark_videos_xoffset" value="{$system['watermark_videos_xoffset']}">
                <div class="form-text">
                  {__("Horizontal offset in pixels")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Watermark Y Offset")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="watermark_videos_yoffset" value="{$system['watermark_videos_yoffset']}">
                <div class="form-text">
                  {__("Vertical offset in pixels")}
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="audios" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("Audio Upload")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable audio upload to share & upload sounds to the site")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="audio_enabled">
                  <input type="checkbox" name="audio_enabled" id="audio_enabled" {if $system['audio_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max audio size")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_audio_size" value="{$system['max_audio_size']}">
                <div class="form-text">
                  {__("The Maximum size of uploaded audio in posts")} {__("in kilobytes (1M = 1024KB)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Audio extensions")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="audio_extensions" value="{$system['audio_extensions']}">
                <div class="form-text">
                  {__("Allowed audio extensions (separated with comma ',)")}
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="files" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6 mb5">{__("File Upload")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable file upload to share & upload files to the site")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="file_enabled">
                  <input type="checkbox" name="file_enabled" id="file_enabled" {if $system['file_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Max file size")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="max_file_size" value="{$system['max_file_size']}">
                <div class="form-text">
                  {__("The Maximum size of uploaded file in posts")} {__("in kilobytes (1M = 1024KB)")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("File extensions")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="file_extensions" value="{$system['file_extensions']}">
                <div class="form-text">
                  {__("Allowed file extensions (separated with comma ',)")}
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
            <button type="button" class="btn btn-danger js_admin-tester" data-handle="ffmpeg">
              <i class="fa fa-bolt mr10"></i> {__("Test Connection (FFMPEG)")}
            </button>
            <button type="button" class="btn btn-danger js_admin-tester" data-handle="google_vision">
              <i class="fa fa-bolt mr10"></i> {__("Test Connection (Vision API)")}
            </button>
            <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
          </div>
        </form>
      </div>
      <!-- General -->

      <!-- Cloud -->
      <div class="tab-pane" id="Cloud">
        <div class="card-body">

          <div class="alert alert-primary">
            <div class="icon">
              <i class="fas fa-cloud-upload-alt fa-2x"></i>
            </div>
            <div class="text">
              <strong>{__("Cloud Hosting")}</strong><br>
              {__("Before enabling cloud hosting, make sure you upload the whole 'uploads' folder to your bucket")}.<br>
              {__("Before disabling cloud hosting, make sure you download the whole 'uploads' folder to your server")}.
            </div>
          </div>

          <!-- S3 -->
          <form class="js_ajax-forms" data-url="admin/settings.php?edit=s3">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="aws_s3" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Amazon S3 Storage")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable Amazon S3 storage")} ({__("Note: Enable this will disable all other options")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="s3_enabled">
                  <input type="checkbox" name="s3_enabled" id="s3_enabled" {if $system['s3_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bucket Name")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="s3_bucket" value="{$system['s3_bucket']}">
                <div class="form-text">
                  {__("Your Amazon S3 bucket name")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bucket Region")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="s3_region">
                  <option value="us-east-2" {if $system['s3_region'] == "us-east-2"}selected{/if}>US East (Ohio) us-east-2</option>
                  <option value="us-east-1" {if $system['s3_region'] == "us-east-1"}selected{/if}>US East (N. Virginia) us-east-1</option>
                  <option value="us-west-1" {if $system['s3_region'] == "us-west-1"}selected{/if}>US West (N. California) us-west-1</option>
                  <option value="us-west-2" {if $system['s3_region'] == "us-west-2"}selected{/if}>US West (Oregon) us-west-2</option>
                  <option value="ap-east-1" {if $system['s3_region'] == "ap-east-1"}selected{/if}>Asia Pacific (Hong Kong) ap-east-1</option>
                  <option value="ap-south-1" {if $system['s3_region'] == "ap-south-1"}selected{/if}>Asia Pacific (Mumbai)</option>
                  <option value="ap-northeast-3" {if $system['s3_region'] == "ap-northeast-3"}selected{/if}>Asia Pacific (Osaka-Local) ap-northeast-3</option>
                  <option value="ap-northeast-2" {if $system['s3_region'] == "ap-northeast-2"}selected{/if}>Asia Pacific (Seoul) ap-northeast-2</option>
                  <option value="ap-southeast-1" {if $system['s3_region'] == "ap-southeast-1"}selected{/if}>Asia Pacific (Singapore) ap-southeast-1</option>
                  <option value="ap-southeast-2" {if $system['s3_region'] == "ap-southeast-2"}selected{/if}>Asia Pacific (Sydney) ap-southeast-2</option>
                  <option value="ap-northeast-1" {if $system['s3_region'] == "ap-northeast-1"}selected{/if}>Asia Pacific (Tokyo) ap-northeast-1</option>
                  <option value="ca-central-1" {if $system['s3_region'] == "ca-central-1"}selected{/if}>Canada (Central) ca-central-1</option>
                  <option value="eu-central-1" {if $system['s3_region'] == "eu-central-1"}selected{/if}>EU (Frankfurt) eu-central-1</option>
                  <option value="eu-west-1" {if $system['s3_region'] == "eu-west-1"}selected{/if}>EU (Ireland) eu-west-1</option>
                  <option value="eu-west-2" {if $system['s3_region'] == "eu-west-2"}selected{/if}>EU (London) eu-west-2</option>
                  <option value="eu-west-3" {if $system['s3_region'] == "eu-west-3"}selected{/if}>EU (Paris) eu-west-3</option>
                  <option value="eu-north-1" {if $system['s3_region'] == "eu-north-1"}selected{/if}>Europe (Stockholm) eu-north-1</option>
                  <option value="me-south-1" {if $system['s3_region'] == "me-south-1"}selected{/if}>Middle East (Bahrain) me-south-1</option>
                  <option value="sa-east-1" {if $system['s3_region'] == "sa-east-1"}selected{/if}>South America (São Paulo) sa-east-1</option>
                </select>
                <div class="form-text">
                  {__("Your Amazon S3 bucket region")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="s3_key" value="{$system['s3_key']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your Amazon S3 Access Key ID")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="s3_secret" value="{$system['s3_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your Amazon S3 Access Key Secret")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label"></label>
              <div class="col-md-9">
                <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                <button type="button" class="btn btn-danger js_admin-tester" data-handle="s3">
                  <i class="fa fa-bolt mr10"></i> {__("Test Connection")}
                </button>
              </div>
            </div>

            <!-- success -->
            <div class="alert alert-success mt15 mb0 x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger mt15 mb0 x-hidden"></div>
            <!-- error -->
          </form>
          <!-- S3 -->

          <div class="divider"></div>

          <!-- Google -->
          <form class="js_ajax-forms" data-url="admin/settings.php?edit=google_cloud">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="google_cloud" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Google Cloud")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable Google Cloud storage")} ({__("Note: Enable this will disable all other options")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="google_cloud_enabled">
                  <input type="checkbox" name="google_cloud_enabled" id="google_cloud_enabled" {if $system['google_cloud_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bucket Name")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="google_cloud_bucket" value="{$system['google_cloud_bucket']}">
                <div class="form-text">
                  {__("Your Google Cloud bucket name")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Google Cloud File")}
              </label>
              <div class="col-md-9">
                <textarea name="google_cloud_file" id="google_cloud_file">{$system['google_cloud_file']}</textarea>
                <div class="form-text">
                  {__("Your service account keys JSON")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label"></label>
              <div class="col-md-9">
                <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                <button type="button" class="btn btn-danger js_admin-tester" data-handle="google_cloud">
                  <i class="fa fa-bolt mr10"></i> {__("Test Connection")}
                </button>
              </div>
            </div>

            <!-- success -->
            <div class="alert alert-success mt15 mb0 x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger mt15 mb0 x-hidden"></div>
            <!-- error -->
          </form>
          <!-- Google -->

          <div class="divider"></div>

          <!-- DigitalOcean -->
          <form class="js_ajax-forms" data-url="admin/settings.php?edit=digitalocean">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="digitalocean" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("DigitalOcean Space")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable DigitalOcean storage")} ({__("Note: Enable this will disable all other options")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="digitalocean_enabled">
                  <input type="checkbox" name="digitalocean_enabled" id="digitalocean_enabled" {if $system['digitalocean_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Space Name")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="digitalocean_space_name" value="{$system['digitalocean_space_name']}">
                <div class="form-text">
                  {__("Your DigitalOcean space name")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Space Region")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="digitalocean_space_region">
                  <option value="sfo2" {if $system['digitalocean_space_region'] == "sfo2"}selected{/if}>San Francisco 2</option>
                  <option value="sfo3" {if $system['digitalocean_space_region'] == "sfo3"}selected{/if}>San Francisco 3</option>
                  <option value="nyc3" {if $system['digitalocean_space_region'] == "nyc3"}selected{/if}>New York</option>
                  <option value="ams3" {if $system['digitalocean_space_region'] == "ams3"}selected{/if}>Amsterdam</option>
                  <option value="sgp1" {if $system['digitalocean_space_region'] == "sgp1"}selected{/if}>Singapore</option>
                  <option value="fra1" {if $system['digitalocean_space_region'] == "fra1"}selected{/if}>Frankfurt</option>
                </select>
                <div class="form-text">
                  {__("Your DigitalOcean space region")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="digitalocean_key" value="{$system['digitalocean_key']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your DigitalOcean Access Key ID")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="digitalocean_secret" value="{$system['digitalocean_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your DigitalOcean Access Key Secret")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label"></label>
              <div class="col-md-9">
                <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                <button type="button" class="btn btn-danger js_admin-tester" data-handle="digitalocean">
                  <i class="fa fa-bolt mr10"></i> {__("Test Connection")}
                </button>
              </div>
            </div>

            <!-- success -->
            <div class="alert alert-success mb20 x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger mt15 mb0 x-hidden"></div>
            <!-- error -->
          </form>
          <!-- DigitalOcean -->

          <div class="divider"></div>

          <!-- Wasabi -->
          <form class="js_ajax-forms" data-url="admin/settings.php?edit=wasabi">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="wasabi" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Wasabi")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable Wasabi storage")} ({__("Note: Enable this will disable all other options")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="wasabi_enabled">
                  <input type="checkbox" name="wasabi_enabled" id="wasabi_enabled" {if $system['wasabi_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bucket Name")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="wasabi_bucket" value="{$system['wasabi_bucket']}">
                <div class="form-text">
                  {__("Your Wasabi bucket name")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bucket Region")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="wasabi_region">
                  <option value="us-east-1" {if $system['wasabi_region'] == "us-east-1"}selected{/if}>us-east-1</option>
                  <option value="us-east-2" {if $system['wasabi_region'] == "us-east-2"}selected{/if}>us-east-2</option>
                  <option value="us-central-1" {if $system['wasabi_region'] == "us-central-1"}selected{/if}>us-central-1</option>
                  <option value="us-west-1" {if $system['wasabi_region'] == "us-west-1"}selected{/if}>us-west-1</option>
                  <option value="ca-central-1" {if $system['wasabi_region'] == "ca-central-1"}selected{/if}>ca-central-1</option>
                  <option value="eu-central-1" {if $system['wasabi_region'] == "eu-central-1"}selected{/if}>eu-central-1</option>
                  <option value="eu-central-2" {if $system['wasabi_region'] == "eu-central-2"}selected{/if}>eu-central-2</option>
                  <option value="eu-west-1" {if $system['wasabi_region'] == "eu-west-1"}selected{/if}>eu-west-1</option>
                  <option value="eu-west-2" {if $system['wasabi_region'] == "eu-west-2"}selected{/if}>eu-west-2</option>
                  <option value="ap-northeast-1" {if $system['wasabi_region'] == "ap-northeast-1"}selected{/if}>ap-northeast-1</option>
                  <option value="ap-northeast-2" {if $system['wasabi_region'] == "ap-northeast-2"}selected{/if}>ap-northeast-2</option>
                  <option value="ap-southeast-1" {if $system['wasabi_region'] == "ap-southeast-1"}selected{/if}>ap-southeast-1</option>
                  <option value="ap-southeast-2" {if $system['wasabi_region'] == "ap-southeast-2"}selected{/if}>ap-southeast-2</option>
                </select>
                <div class="form-text">
                  {__("Your Wasabi bucket region")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="wasabi_key" value="{$system['wasabi_key']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your Wasabi Access Key ID")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="wasabi_secret" value="{$system['wasabi_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your Wasabi Access Key Secret")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label"></label>
              <div class="col-md-9">
                <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                <button type="button" class="btn btn-danger js_admin-tester" data-handle="wasabi">
                  <i class="fa fa-bolt mr10"></i> {__("Test Connection")}
                </button>
              </div>
            </div>

            <!-- success -->
            <div class="alert alert-success mt15 mb0 x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger mt15 mb0 x-hidden"></div>
            <!-- error -->
          </form>
          <!-- Wasabi -->

          <div class="divider"></div>

          <!-- Backblaze -->
          <form class="js_ajax-forms" data-url="admin/settings.php?edit=backblaze">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="backblaze" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Backblaze")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable Backblaze storage")} ({__("Note: Enable this will disable all other options")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="backblaze_enabled">
                  <input type="checkbox" name="backblaze_enabled" id="backblaze_enabled" {if $system['backblaze_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bucket Name")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="backblaze_bucket" value="{$system['backblaze_bucket']}">
                <div class="form-text">
                  {__("Your Backblaze bucket name")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bucket Region")}
              </label>
              <div class="col-md-9">
                <select class="form-select" name="backblaze_region">
                  <option value="eu-central-003" {if $system['backblaze_region'] == "eu-central-003"}selected{/if}>eu-central-003</option>
                  <option value="us-west-004" {if $system['backblaze_region'] == "us-west-004"}selected{/if}>us-west-004</option>
                  <option value="us-east-005" {if $system['backblaze_region'] == "us-east-005"}selected{/if}>us-east-005</option>
                </select>
                <div class="form-text">
                  {__("Your Backblaze bucket region")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key ID")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="backblaze_key" value="{$system['backblaze_key']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your Backblaze Access Key ID")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Access Key Secret")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="backblaze_secret" value="{$system['backblaze_secret']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("Your Backblaze Access Key Secret")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label"></label>
              <div class="col-md-9">
                <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
                <button type="button" class="btn btn-danger js_admin-tester" data-handle="backblaze">
                  <i class="fa fa-bolt mr10"></i> {__("Test Connection")}
                </button>
              </div>
            </div>

            <!-- success -->
            <div class="alert alert-success mt15 mb0 x-hidden"></div>
            <!-- success -->

            <!-- error -->
            <div class="alert alert-danger mt15 mb0 x-hidden"></div>
            <!-- error -->
          </form>
          <!-- Backblaze -->

        </div>
      </div>
      <!-- Cloud -->

      <!-- FTP -->
      <div class="tab-pane" id="FTP">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=ftp">
          <div class="card-body">
            <div class="alert alert-primary">
              <div class="icon">
                <i class="fa fa-server fa-2x"></i>
              </div>
              <div class="text">
                <strong>{__("FTP Storage")}</strong><br>
                {__("Before enabling FTP Storage, make sure you upload the whole 'uploads' folder to your space")}.<br>
                {__("Before disabling FTP Storage, make sure you download the whole 'uploads' folder to your server")}.
              </div>
            </div>

            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="server" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("FTP Storage")}</div>
                <div class="form-text d-none d-sm-block">
                  {__("Enable FTP Storage upload")} ({__("Note: Enable this will disable all other options")})
                </div>
              </div>
              <div class="text-end">
                <label class="switch" for="ftp_enabled">
                  <input type="checkbox" name="ftp_enabled" id="ftp_enabled" {if $system['ftp_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Hostname")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="ftp_hostname" value="{$system['ftp_hostname']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Port")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="ftp_port" value="{$system['ftp_port']}" placeholder="21">
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Username")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="ftp_username" value="{$system['ftp_username']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Password")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="ftp_password" value="{$system['ftp_password']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("FTP Path")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="ftp_path" value="{$system['ftp_path']}" placeholder="./">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("The path to your uploads folder (Examples: './' or 'public_html/uploads')")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("FTP Endpoint")}
              </label>
              <div class="col-md-9">
                {if !$user->_data['user_demo']}
                  <input type="text" class="form-control" name="ftp_endpoint" value="{$system['ftp_endpoint']}">
                {else}
                  <input type="password" class="form-control" value="*********">
                {/if}
                <div class="form-text">
                  {__("The URL to your uploads folder (Examples: 'https://domain.com/uploads' or 'https://64.233.191.255/uploads')")}
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
            <button type="button" class="btn btn-danger js_admin-tester" data-handle="ftp">
              <i class="fa fa-bolt mr10"></i> {__("Test Connection")}
            </button>
            <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
          </div>
        </form>
      </div>
      <!-- FTP -->
    </div>
    <!-- tabs content -->

  {elseif $sub_view == "security"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Security")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <!-- Security -->
    <form class="js_ajax-forms" data-url="admin/settings.php?edit=security">
      <div class="card-body">
        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="spy" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Unusual Login Detection")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable unusual login detection, System will not allow user to login with same session from different device or location")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="unusual_login_enabled">
              <input type="checkbox" name="unusual_login_enabled" id="unusual_login_enabled" {if $system['unusual_login_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="firewall" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Brute Force Detection")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable brute force attack detection, System will block the user account if hacker try to login with invalid password too many times to guess the correct account password")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="brute_force_detection_enabled">
              <input type="checkbox" name="brute_force_detection_enabled" id="brute_force_detection_enabled" {if $system['brute_force_detection_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Bad Login Limit")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="brute_force_bad_login_limit" value="{$system['brute_force_bad_login_limit']}">
            <div class="form-text">
              {__("Number of bad login attempts till account get blocked")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Lockout Time")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="brute_force_lockout_time" value="{$system['brute_force_lockout_time']}">
            <div class="form-text">
              {__("Number of minutes the account will still locked out")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="fingerprint" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Two-Factor Authentication")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable two-factor authentication to log in with a code from your email/phone as well as a password")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="two_factor_enabled">
              <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled" {if $system['two_factor_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-sm-3 form-label">
            {__("Two-Factor Authentication Via")}
          </label>
          <div class="col-md-9">
            <div class="form-check form-check-inline">
              <input type="radio" name="two_factor_type" id="two_factor_email" value="email" class="form-check-input" {if $system['two_factor_type'] == "email"}checked{/if}>
              <label class="form-check-label" for="two_factor_email">{__("Email")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="two_factor_type" id="two_factor_sms" value="sms" class="form-check-input" {if $system['two_factor_type'] == "sms"}checked{/if}>
              <label class="form-check-label" for="two_factor_sms">{__("SMS")}</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="two_factor_type" id="two_factor_google" value="google" class="form-check-input" {if $system['two_factor_type'] == "google"}checked{/if}>
              <label class="form-check-label" for="two_factor_google">{__("Google Authenticator")}</label>
            </div>
            <div class="form-text">
              {__("Select Email, SMS or Google Authenticator to send log in code to user")}<br>
              {__("Make sure you have configured")} <a href="{$system['system_url']}/{$control_panel['url']}/settings/sms">{__("SMS Settings")}</a>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="reserved" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Reserved Usernames Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable/Disable Reserved Usernames")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="reserved_usernames_enabled">
              <input type="checkbox" name="reserved_usernames_enabled" id="reserved_usernames_enabled" {if $system['reserved_usernames_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Reserved Usernames")}
          </label>
          <div class="col-md-9">
            <textarea class="js_tagify x-hidden" name="reserved_usernames" rows="3">{$system['reserved_usernames']}</textarea>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="censored" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("Censored Words Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable/Disable Words to be censored")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="censored_words_enabled">
              <input type="checkbox" name="censored_words_enabled" id="censored_words_enabled" {if $system['censored_words_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Censored Words")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="censored_words" rows="3">{$system['censored_words']}</textarea>
            <div class="form-text">
              {__("Words to be censored, separated by a comma (,)")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="html" class="main-icon" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("HTML Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Enable/Disable HTML code in Rich Text Editor (blogs and forums)")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="html_richtext_enabled">
              <input type="checkbox" name="html_richtext_enabled" id="html_richtext_enabled" {if $system['html_richtext_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="divider"></div>

        <div class="form-table-row">
          <div class="avatar">
            {include file='__svg_icons.tpl' icon="google_recaptcha" width="40px" height="40px"}
          </div>
          <div>
            <div class="form-label h6">{__("reCAPTCHA Enabled")}</div>
            <div class="form-text d-none d-sm-block">{__("Turn reCAPTCHA On and Off")}</div>
          </div>
          <div class="text-end">
            <label class="switch" for="reCAPTCHA_enabled">
              <input type="checkbox" name="reCAPTCHA_enabled" id="reCAPTCHA_enabled" {if $system['reCAPTCHA_enabled']}checked{/if}>
              <span class="slider round"></span>
            </label>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("reCAPTCHA Site Key")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="reCAPTCHA_site_key" value="{$system['reCAPTCHA_site_key']}">
            {else}
              <input type="password" class="form-control" value="*********">
            {/if}
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("reCAPTCHA Secret Key")}
          </label>
          <div class="col-md-9">
            {if !$user->_data['user_demo']}
              <input type="text" class="form-control" name="reCAPTCHA_secret_key" value="{$system['reCAPTCHA_secret_key']}">
            {else}
              <input type="password" class="form-control" value="*********">
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
    <!-- Security -->

  {elseif $sub_view == "payments"}

    <!-- card-header -->
    <div class="card-header with-icon with-nav">
      <!-- panel title -->
      <div class="mb20">
        <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Payments")}
      </div>
      <!-- panel title -->

      <!-- panel nav -->
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#Settings" data-bs-toggle="tab">
            <i class="fa fa-cog fa-fw mr5"></i><strong class="pr5">{__("Settings")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Payments" data-bs-toggle="tab">
            <i class="fa fa-credit-card fa-fw mr5"></i><strong class="pr5">{__("Online Payments")}</strong>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Bank" data-bs-toggle="tab">
            <i class="fa fa-university fa-fw mr5"></i><strong class="pr5">{__("Bank Transfers")}</strong>
          </a>
        </li>
      </ul>
      <!-- panel nav -->
    </div>
    <!-- card-header -->

    <!-- tabs content -->
    <div class="tab-content">
      <!-- Settings -->
      <div class="tab-pane active" id="Settings">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=payments_settings">
          <div class="card-body">
            <div class="heading-small mb20">
              {__("Fees")}
            </div>
            <div class="pl-md-4">
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="withdrawal" class="main-icon" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Payment Fees")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable/Disable Payment Fees")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="payment_fees_enabled">
                    <input type="checkbox" name="payment_fees_enabled" id="payment_fees_enabled" {if $system['payment_fees_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Fees Percentage")} (%)
                </label>
                <div class="col-md-9">
                  <input type="text" class="form-control" name="payment_fees_percentage" value="{$system['payment_fees_percentage']}">
                  <div class="form-text">
                    {__("Percentage of fees to be added to the payment amount")}
                  </div>
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <div class="heading-small mb20">
              {__("VAT")}
            </div>
            <div class="pl-md-4">
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="vat" class="main-icon" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("VAT Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable/Disable VAT")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="payment_vat_enabled">
                    <input type="checkbox" name="payment_vat_enabled" id="payment_vat_enabled" {if $system['payment_vat_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="map" class="main-icon" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("VAT By User Country")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable/Disable VAT by user country")} (<a href="{$system['system_url']}/{$control_panel['url']}/countries">{__("Manage Countries VAT")}</a>)</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="payment_country_vat_enabled">
                    <input type="checkbox" name="payment_country_vat_enabled" id="payment_country_vat_enabled" {if $system['payment_country_vat_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Default VAT Percentage")} (%)
                </label>
                <div class="col-md-9">
                  <input type="text" class="form-control" name="payment_vat_percentage" value="{$system['payment_vat_percentage']}">
                  <div class="form-text">
                    {__("Note: If VAT By User Country enabled then VAT will be calculated based on user country VAT")}<br>
                    {__("Note: If user didn't select his country then VAT will be calculated based on default VAT percentage")}<br>
                    {__("Note: If country VAT is not set then VAT will be calculated based on default VAT percentage")}<br>
                  </div>
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
      </div>
      <!-- Settings -->

      <!-- Payments -->
      <div class="tab-pane" id="Payments">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=payments_methods">
          <div class="card-body">

            <!-- PayPal -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="paypal" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Paypal Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via Paypal")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="paypal_enabled">
                    <input type="checkbox" name="paypal_enabled" id="paypal_enabled" {if $system['paypal_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Paypal Mode")}
                </label>
                <div class="col-md-9">
                  <div class="form-check form-check-inline">
                    <input type="radio" name="paypal_mode" id="paypal_live" value="live" class="form-check-input" {if $system['paypal_mode'] == "live"}checked{/if}>
                    <label class="form-check-label" for="paypal_live">{__("Live")}</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input type="radio" name="paypal_mode" id="paypal_sandbox" value="sandbox" class="form-check-input" {if $system['paypal_mode'] == "sandbox"}checked{/if}>
                    <label class="form-check-label" for="paypal_sandbox">{__("Sandbox")}</label>
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("PayPal Client ID")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="paypal_id" value="{$system['paypal_id']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("PayPal Secret Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="paypal_secret" value="{$system['paypal_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("PayPal Webhook Id")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="paypal_webhook" value="{$system['paypal_webhook']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>
            </div>
            <!-- PayPal -->

            <div class="divider"></div>

            <!-- Stripe -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="stripe" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Stripe Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via Credit Card")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="creditcard_enabled">
                    <input type="checkbox" name="creditcard_enabled" id="creditcard_enabled" {if $system['creditcard_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="alipay" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Alipay Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via Alipay")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="alipay_enabled">
                    <input type="checkbox" name="alipay_enabled" id="alipay_enabled" {if $system['alipay_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Stripe Mode")}
                </label>
                <div class="col-md-9">
                  <div class="form-check form-check-inline">
                    <input type="radio" name="stripe_mode" id="stripe_live" value="live" class="form-check-input" {if $system['stripe_mode'] == "live"}checked{/if}>
                    <label class="form-check-label" for="stripe_live">{__("Live")}</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input type="radio" name="stripe_mode" id="stripe_test" value="test" class="form-check-input" {if $system['stripe_mode'] == "test"}checked{/if}>
                    <label class="form-check-label" for="stripe_test">{__("Test")}</label>
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Test Secret Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="stripe_test_secret" value="{$system['stripe_test_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                  <div class="form-text">
                    {__("Stripe secret key that starts with sk_")}
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Test Publishable Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="stripe_test_publishable" value="{$system['stripe_test_publishable']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                  <div class="form-text">
                    {__("Stripe publishable key that starts with pk_")}
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Live Secret Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="stripe_live_secret" value="{$system['stripe_live_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                  <div class="form-text">
                    {__("Stripe secret key that starts with sk_")}
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Live Publishable Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="stripe_live_publishable" value="{$system['stripe_live_publishable']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                  <div class="form-text">
                    {__("Stripe publishable key that starts with pk_")}
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Webhook Signing Secret")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="stripe_webhook" value="{$system['stripe_webhook']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                  <div class="form-text">
                    {__("Stripe webhook signing secret that starts with whsec_")}
                  </div>
                </div>
              </div>
            </div>
            <!-- Stripe -->

            <div class="divider"></div>

            <!-- Paystack -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="paystack" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Paystack Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via Paystack")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="paystack_enabled">
                    <input type="checkbox" name="paystack_enabled" id="paystack_enabled" {if $system['paystack_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Secret Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="paystack_secret" value="{$system['paystack_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                  <div class="form-text">
                    {__("Paystack secret key that starts with sk_")}
                  </div>
                </div>
              </div>
            </div>
            <!-- Paystack -->

            <div class="divider"></div>

            <!-- CoinPayments -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="bitcoin" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("CoinPayments Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via CoinPayments")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="coinpayments_enabled">
                    <input type="checkbox" name="coinpayments_enabled" id="coinpayments_enabled" {if $system['coinpayments_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Merchant ID")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="coinpayments_merchant_id" value="{$system['coinpayments_merchant_id']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("IPN Secret")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="coinpayments_ipn_secret" value="{$system['coinpayments_ipn_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>
            </div>
            <!-- CoinPayments -->

            <div class="divider"></div>

            <!-- 2Checkout -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="2co" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("2Checkout Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via 2Checkout")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="2checkout_enabled">
                    <input type="checkbox" name="2checkout_enabled" id="2checkout_enabled" {if $system['2checkout_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("2Checkout Mode")}
                </label>
                <div class="col-md-9">
                  <div class="form-check form-check-inline">
                    <input type="radio" name="2checkout_mode" id="2checkout_live" value="live" class="form-check-input" {if $system['2checkout_mode'] == "live"}checked{/if}>
                    <label class="form-check-label" for="2checkout_live">{__("Live")}</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input type="radio" name="2checkout_mode" id="2checkout_sandbox" value="sandbox" class="form-check-input" {if $system['2checkout_mode'] == "sandbox"}checked{/if}>
                    <label class="form-check-label" for="2checkout_sandbox">{__("Demo")}</label>
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Merchant Code")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="2checkout_merchant_code" value="{$system['2checkout_merchant_code']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("API Publishable Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="2checkout_publishable_key" value="{$system['2checkout_publishable_key']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("API Private Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="2checkout_private_key" value="{$system['2checkout_private_key']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>
            </div>
            <!-- 2Checkout -->

            <div class="divider"></div>

            <!-- Razorpay -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="razorpay" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Razorpay Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via Razorpay")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="razorpay_enabled">
                    <input type="checkbox" name="razorpay_enabled" id="razorpay_enabled" {if $system['razorpay_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Key ID")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="razorpay_key_id" value="{$system['razorpay_key_id']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Key Secret")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="razorpay_key_secret" value="{$system['razorpay_key_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>
            </div>
            <!-- Razorpay -->

            <div class="divider"></div>

            <!-- Cashfree -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  <img width="40px" height="40px" src="{$system['system_url']}/content/themes/{$system['theme']}/images/cashfree.png">
                </div>
                <div>
                  <div class="form-label h6">{__("Cashfree Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via Cashfree")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="cashfree_enabled">
                    <input type="checkbox" name="cashfree_enabled" id="cashfree_enabled" {if $system['cashfree_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Cashfree Mode")}
                </label>
                <div class="col-md-9">
                  <div class="form-check form-check-inline">
                    <input type="radio" name="cashfree_mode" id="Cashfree_live" value="live" class="form-check-input" {if $system['cashfree_mode'] == "live"}checked{/if}>
                    <label class="form-check-label" for="Cashfree_live">{__("Live")}</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input type="radio" name="cashfree_mode" id="Cashfree_sandbox" value="sandbox" class="form-check-input" {if $system['cashfree_mode'] == "sandbox"}checked{/if}>
                    <label class="form-check-label" for="Cashfree_sandbox">{__("Sandbox")}</label>
                  </div>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Client ID")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="cashfree_client_id" value="{$system['cashfree_client_id']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Client Secret")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="cashfree_client_secret" value="{$system['cashfree_client_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>
            </div>
            <!-- Cashfree -->

            <div class="divider"></div>

            <!-- Coinbase -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="coinbase" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("Coinbase Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via Coinbase")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="coinbase_enabled">
                    <input type="checkbox" name="coinbase_enabled" id="coinbase_enabled" {if $system['coinbase_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("API Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="coinbase_api_key" value="{$system['coinbase_api_key']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>
            </div>
            <!-- Coinbase -->

            <div class="divider"></div>

            <!-- SecurionPay -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="securionpay" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("SecurionPay Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via SecurionPay")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="securionpay_enabled">
                    <input type="checkbox" name="securionpay_enabled" id="securionpay_enabled" {if $system['securionpay_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("API Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="securionpay_api_key" value="{$system['securionpay_api_key']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("API Secret")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="securionpay_api_secret" value="{$system['securionpay_api_secret']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>
            </div>
            <!-- SecurionPay -->

            <div class="divider"></div>

            <!-- MoneyPoolsCash -->
            <div>
              <div class="form-table-row">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="moneypoolscash" width="40px" height="40px"}
                </div>
                <div>
                  <div class="form-label h6">{__("MoneyPoolsCash Enabled")}</div>
                  <div class="form-text d-none d-sm-block">{__("Enable payments via MoneyPoolsCash")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="moneypoolscash_enabled">
                    <input type="checkbox" name="moneypoolscash_enabled" id="moneypoolscash_enabled" {if $system['moneypoolscash_enabled']}checked{/if}>
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("API Key")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="moneypoolscash_api_key" value="{$system['moneypoolscash_api_key']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Merchant Email")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="moneypoolscash_merchant_email" value="{$system['moneypoolscash_merchant_email']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                </div>
              </div>

              <div class="row form-group">
                <label class="col-md-3 form-label">
                  {__("Merchant Wallet Password")}
                </label>
                <div class="col-md-9">
                  {if !$user->_data['user_demo']}
                    <input type="text" class="form-control" name="moneypoolscash_merchant_password" value="{$system['moneypoolscash_merchant_password']}">
                  {else}
                    <input type="password" class="form-control" value="*********">
                  {/if}
                  <div class="form-text">
                    {__("This required only for automatic withdrawal payments not for online payments")}
                  </div>
                </div>
              </div>
            </div>
            <!-- MoneyPoolsCash -->

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
      </div>
      <!-- Payments -->

      <!-- Bank -->
      <div class="tab-pane" id="Bank">
        <form class="js_ajax-forms" data-url="admin/settings.php?edit=bank">
          <div class="card-body">
            <div class="form-table-row">
              <div class="avatar">
                {include file='__svg_icons.tpl' icon="bank" class="main-icon" width="40px" height="40px"}
              </div>
              <div>
                <div class="form-label h6">{__("Bank Transfers Enabled")}</div>
                <div class="form-text d-none d-sm-block">{__("Enable payments via Bank Transfers")}</div>
              </div>
              <div class="text-end">
                <label class="switch" for="bank_transfers_enabled">
                  <input type="checkbox" name="bank_transfers_enabled" id="bank_transfers_enabled" {if $system['bank_transfers_enabled']}checked{/if}>
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bank Name")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="bank_name" value="{$system['bank_name']}">
                <div class="form-text">
                  {__("Your Bank Name")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bank Account Number")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="bank_account_number" value="{$system['bank_account_number']}">
                <div class="form-text">
                  {__("Your Bank Account Number")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bank Account Name")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="bank_account_name" value="{$system['bank_account_name']}">
                <div class="form-text">
                  {__("Your Bank Account Name")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bank Account Routing Code")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="bank_account_routing" value="{$system['bank_account_routing']}">
                <div class="form-text">
                  {__("Your Bank Account Routing Code or SWIFT Code")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Bank Account Country")}
              </label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="bank_account_country" value="{$system['bank_account_country']}">
                <div class="form-text">
                  {__("Your Bank Account Country")}
                </div>
              </div>
            </div>

            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Transfer Note")}
              </label>
              <div class="col-md-9">
                <textarea class="form-control" name="bank_transfer_note" rows="5">{$system['bank_transfer_note']}</textarea>
                <div class="form-text">
                  {__("This note will be displayed to the user while upload his bank transfer receipt")}
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
      </div>
      <!-- Bank -->
    </div>

  {elseif $sub_view == "limits"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Limits")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <!-- Limits -->
    <form class="js_ajax-forms" data-url="admin/settings.php?edit=limits">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Data Heartbeat")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="data_heartbeat" value="{$system['data_heartbeat']}">
            <div class="form-text">
              {__("The update interval to check for new data (in seconds)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Chat Heartbeat")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="chat_heartbeat" value="{$system['chat_heartbeat']}">
            <div class="form-text">
              {__("The update interval to check for new messages (in seconds)")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Offline After")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="offline_time" value="{$system['offline_time']}">
            <div class="form-text">
              {__("The amount of time to be considered online since the last user's activity (in seconds)")}<br>
              {__("The maximim value is one day = 86400 seconds")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Newsfeed Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="newsfeed_results" value="{$system['newsfeed_results']}">
            <div class="form-text">
              {__("The number of posts in the newsfeed")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Pages Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="pages_results" value="{$system['pages_results']}">
            <div class="form-text">
              {__("The number of results in the pages module")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Groups Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="groups_results" value="{$system['groups_results']}">
            <div class="form-text">
              {__("The number of results in the groups module")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Events Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="events_results" value="{$system['events_results']}">
            <div class="form-text">
              {__("The number of results in the events module")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Marketplace Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="marketplace_results" value="{$system['marketplace_results']}">
            <div class="form-text">
              {__("The number of results in the marketplace module")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Offers Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="offers_results" value="{$system['offers_results']}">
            <div class="form-text">
              {__("The number of results in the offers module")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Jobs Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="jobs_results" value="{$system['jobs_results']}">
            <div class="form-text">
              {__("The number of results in the jobs module")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Games Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="games_results" value="{$system['games_results']}">
            <div class="form-text">
              {__("The number of results in the games module")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Search Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="search_results" value="{$system['search_results']}">
            <div class="form-text">
              {__("The number of results in the search module")}
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Minimum Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="min_results" value="{$system['min_results']}">
            <div class="form-text">
              {__("The Min number of results per request")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Maximum Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="max_results" value="{$system['max_results']}">
            <div class="form-text">
              {__("The Max number of results per request")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Minimum Even Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="min_results_even" value="{$system['min_results_even']}">
            <div class="form-text">
              {__("The Min even number of results per request")}
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Maximum Even Results")}
          </label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="max_results_even" value="{$system['max_results_even']}">
            <div class="form-text">
              {__("The Max even number of results per request")}
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
    <!-- Limits -->

  {elseif $sub_view == "analytics"}

    <!-- card-header -->
    <div class="card-header with-icon">
      <!-- panel title -->
      <i class="fa fa-cog mr10"></i>{__("Settings")} &rsaquo; {__("Analytics")}
      <!-- panel title -->
    </div>
    <!-- card-header -->

    <!-- Analytics -->
    <form class="js_ajax-forms" data-url="admin/settings.php?edit=analytics">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Tracking Code")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="message" rows="3">{$system['analytics_code']}</textarea>
            <div class="form-text">
              {__("The analytics tracking code (Ex: Google Analytics)")}
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
    <!-- Analytics -->

  {/if}

</div>