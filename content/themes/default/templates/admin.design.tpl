<div class="card">
  <div class="card-header with-icon">
    <i class="fa fa-paint-brush mr10"></i>{__("Design")}
  </div>
  <form class="js_ajax-forms" data-url="admin/design.php">
    <div class="card-body">
      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="fluid" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Fluid Design")}</div>
          <div class="form-text d-none d-sm-block">{__("Turn the full width containers On and Off")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="fluid_design">
            <input type="checkbox" name="fluid_design" id="fluid_design" {if $system['fluid_design']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="dark_light" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Night Mode is Default")}</div>
          <div class="form-text d-none d-sm-block">
            {__("Make the night mode is the default mode of your website")}
          </div>
        </div>
        <div class="text-end">
          <label class="switch" for="system_theme_night_on">
            <input type="checkbox" name="system_theme_night_on" id="system_theme_night_on" {if $system['system_theme_night_on']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="settings" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Users Can Change Mode")}</div>
          <div class="form-text d-none d-sm-block">
            {__("Allow users to select between day and night mode")}
          </div>
        </div>
        <div class="text-end">
          <label class="switch" for="system_theme_mode_select">
            <input type="checkbox" name="system_theme_mode_select" id="system_theme_mode_select" {if $system['system_theme_mode_select']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="divider"></div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Landing Page")}
        </label>
        <div class="col-md-9">
          <div class="form-check form-check-inline">
            <input type="radio" name="landing_page_template" id="default_landing" value="default" class="form-check-input" {if $system['landing_page_template'] == "default"}checked{/if}>
            <label class="form-check-label" for="default_landing">{__("Default")}</label>
          </div>
          <div class="form-check form-check-inline">
            <input type="radio" name="landing_page_template" id="elengine_landing" value="elengine" class="form-check-input" {if $system['landing_page_template'] == "elengine"}checked{/if}>
            <label class="form-check-label" for="elengine_landing">{__("Elengine")}</label>
            <small>(<a target="_blank" href="{$system['system_url']}/content/themes/{$system['theme']}/images/elengine_landing.png">{__("preview")}</a>)</small>
          </div>
          <div class="form-text">
            {__("Note: You can get the whole Elengine theme from")} <a target="_blank" href="https://bit.ly/SngineElengineTheme">{__("Here")}</a>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Logo")} ({__("Light Mode")})
        </label>
        <div class="col-md-9">
          {if $system['system_logo'] == ''}
            <div class="x-image">
              <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'></button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_logo" value="">
            </div>
          {else}
            <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$system['system_logo']}')">
              <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'></button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_logo" value="{$system['system_logo']}">
            </div>
          {/if}
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Logo")} ({__("Dark Mode")})
        </label>
        <div class="col-md-9">
          {if $system['system_logo_dark'] == ''}
            <div class="x-image">
              <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

              </button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_logo_dark" value="">
            </div>
          {else}
            <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$system['system_logo_dark']}')">
              <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'></button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_logo_dark" value="{$system['system_logo_dark']}">
            </div>
          {/if}
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="star" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Default Favicon")}</div>
          <div class="form-text d-none d-sm-block">
            {__("Use the default")} (<a target="_blank" href="{$system['system_url']}/content/themes/{$system['theme']}/images/favicon.png">{__("preview")}</a>) ({__("Disable it to use your custom uploaded image")})
          </div>
        </div>
        <div class="text-end">
          <label class="switch" for="system_favicon_default">
            <input type="checkbox" name="system_favicon_default" id="system_favicon_default" {if $system['system_favicon_default']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Custom Favicon")}
        </label>
        <div class="col-md-9">
          {if $system['system_favicon'] == ''}
            <div class="x-image">
              <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

              </button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_favicon" value="">
            </div>
          {else}
            <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$system['system_favicon']}')">
              <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'></button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_favicon" value="{$system['system_favicon']}">
            </div>
          {/if}
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="social_share" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Default OG-Image")}</div>
          <div class="form-text d-none d-sm-block">
            {__("Use the default")} (<a target="_blank" href="{$system['system_url']}/content/themes/{$system['theme']}/images/og-image.jpg">{__("preview")}</a>) ({__("Disable it to use your custom uploaded image")})
          </div>
        </div>
        <div class="text-end">
          <label class="switch" for="system_ogimage_default">
            <input type="checkbox" name="system_ogimage_default" id="system_ogimage_default" {if $system['system_ogimage_default']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Custom OG-Image")}
        </label>
        <div class="col-md-9">
          {if $system['system_ogimage'] == ''}
            <div class="x-image">
              <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'>

              </button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_ogimage" value="">
            </div>
          {else}
            <div class="x-image" style="background-image: url('{$system['system_uploads']}/{$system['system_ogimage']}')">
              <button type="button" class="btn-close js_x-image-remover" title='{__("Remove")}'></button>
              <div class="x-image-loader">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
              <input type="hidden" class="js_x-image-input" name="system_ogimage" value="{$system['system_ogimage']}">
            </div>
          {/if}
          <div class="form-text">
            {__("The perfect size for your og-image should be (wdith: 590px & height: 300px)")}
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="playstore" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Google Play Store Badge")}</div>
          <div class="form-text d-none d-sm-block">{__("Show Google Play Store badge on the landing page")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="play_store_badge_enabled">
            <input type="checkbox" name="play_store_badge_enabled" id="play_store_badge_enabled" {if $system['play_store_badge_enabled']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Google Play Store Link")}
        </label>
        <div class="col-md-9">
          <input type="text" class="form-control" name="play_store_link" value="{$system['play_store_link']}">
          <div class="form-text">
            {__("The app link on Google Play Store")}
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="appgallery" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Huawei AppGallery Badge")}</div>
          <div class="form-text d-none d-sm-block">{__("Show Huawei AppGallery badge on the landing page")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="appgallery_badge_enabled">
            <input type="checkbox" name="appgallery_badge_enabled" id="appgallery_badge_enabled" {if $system['appgallery_badge_enabled']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Huawei AppGallery Link")}
        </label>
        <div class="col-md-9">
          <input type="text" class="form-control" name="appgallery_store_link" value="{$system['appgallery_store_link']}">
          <div class="form-text">
            {__("The app link on Huawei AppGallery")}
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="appstore" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Apple App Store Badge")}</div>
          <div class="form-text d-none d-sm-block">{__("Show Apple App Store badge on the landing page")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="app_store_badge_enabled">
            <input type="checkbox" name="app_store_badge_enabled" id="app_store_badge_enabled" {if $system['app_store_badge_enabled']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Apple App Store Link")}
        </label>
        <div class="col-md-9">
          <input type="text" class="form-control" name="app_store_link" value="{$system['app_store_link']}">
          <div class="form-text">
            {__("The app link on Apple App Store")}
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-table-row">
        <div class="avatar">
          {include file='__svg_icons.tpl' icon="themes_switcher" class="main-icon" width="40px" height="40px"}
        </div>
        <div>
          <div class="form-label h6">{__("Enable Customization")}</div>
          <div class="form-text d-none d-sm-block">{__("Turn the customization On and Off")}</div>
        </div>
        <div class="text-end">
          <label class="switch" for="css_customized">
            <input type="checkbox" name="css_customized" id="css_customized" {if $system['css_customized']}checked{/if}>
            <span class="slider round"></span>
          </label>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Background Color")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_background" value="{$system['css_background']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_background']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Link Color")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_link_color" value="{$system['css_link_color']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_link_color']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Header Color")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_header" value="{$system['css_header']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_header']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Header Search Background")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_header_search" value="{$system['css_header_search']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_header_search']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Header Search Font")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_header_search_color" value="{$system['css_header_search_color']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_header_search_color']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Button Primary")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_btn_primary" value="{$system['css_btn_primary']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_btn_primary']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Header Icons")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_header_icons" value="{$system['css_header_icons']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_header_icons']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Header Icons (Night Mode)")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_header_icons_night" value="{$system['css_header_icons_night']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_header_icons_night']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Main Icons")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_main_icons" value="{$system['css_main_icons']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_main_icons']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Main Icons (Night Mode)")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_main_icons_night" value="{$system['css_main_icons_night']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_main_icons_night']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Action Icons")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_action_icons" value="{$system['css_action_icons']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_action_icons']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Action Icons (Night Mode)")}
        </label>
        <div class="col-md-9">
          <div class="input-group js_colorpicker">
            <input type="text" class="form-control form-control-color" name="css_action_icons_night" value="{$system['css_action_icons_night']}" />
            <input type="color" class="form-control form-control-color" value="{$system['css_action_icons_night']}" />
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Custom CSS")}
        </label>
        <div class="col-md-9">
          <textarea class="form-control" rows="10" name="css_custome_css" id="custom-css">{$system['css_custome_css']}</textarea>
          <div class="form-text">
            {__("Header Custom CSS")}
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Header Custom JavaScript")}
        </label>
        <div class="col-md-9">
          <textarea name="custome_js_header" id="custome_js_header">{$system['custome_js_header']}</textarea>
          <div class="form-text">
            {__("The code will be added in head tag")}
          </div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-md-3 form-label">
          {__("Footer Custom JavaScript")}
        </label>
        <div class="col-md-9">
          <textarea name="custome_js_footer" id="custome_js_footer">{$system['custome_js_footer']}</textarea>
          <div class="form-text">
            {__("The code will be added at the end of body tag")}
          </div>
        </div>
      </div>

      <!-- error -->
      <div class="alert alert-danger mt15 mb0 x-hidden"></div>
      <!-- error -->
    </div>
    <div class="card-footer text-end">
      <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
    </div>
  </form>

</div>