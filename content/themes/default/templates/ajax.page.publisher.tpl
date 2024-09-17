<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="pages" class="main-icon mr10" width="24px" height="24px"}
    {__("Create New Page")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="modules/create.php?type=page&do=create">
  <div class="modal-body">
    <div class="form-group">
      <label class="form-label" for="title">{__("Name Your Page")}</label>
      <input type="text" class="form-control" name="title" id="title">
    </div>
    <div class="form-group">
      <label class="form-label" for="username">{__("Page Username")}</label>
      <div class="input-group">
        <span class="input-group-text d-none d-sm-block">{$system['system_url']}/pages/</span>
        <input type="text" class="form-control" name="username" id="username">
      </div>
      <div class="form-text">
        {__("Can only contain alphanumeric characters (A–Z, 0–9) and periods ('.')")}
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="category">{__("Category")}</label>
      <select class="form-select" name="category" id="category">
        <option>{__("Select Category")}</option>
        {foreach $categories as $category}
          {include file='__categories.recursive_options.tpl'}
        {/foreach}
      </select>
    </div>
    <div class="form-group">
      <label class="form-label" for="country">{__("Country")}</label>
      <select class="form-select" name="country">
        <option value="none">{__("Select Country")}</option>
        {foreach $countries as $country}
          <option value="{$country['country_id']}">{$country['country_name']}</option>
        {/foreach}
      </select>
    </div>
    <div class="form-group">
      <label class="form-label" for="description">{__("About")}</label>
      <textarea class="form-control" name="description" name="description"></textarea>
    </div>
    <!-- custom fields -->
    {if $custom_fields}
      {include file='__custom_fields.tpl' _custom_fields=$custom_fields _registration=true}
    {/if}
    <!-- custom fields -->
    <!-- error -->
    <div class="alert alert-danger mb0 mt10 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">{__("Create")}</button>
  </div>
</form>