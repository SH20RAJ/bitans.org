<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="events" class="main-icon mr10" width="24px" height="24px"}
    {__("Create New Event")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="modules/create.php?type=event&do=create">
  <div class="modal-body">
    <div class="form-group">
      <label class="form-label" for="title">{__("Name Your Event")}</label>
      <input type="text" class="form-control" name="title" id="title">
    </div>
    <div class="form-group">
      <label class="form-label" for="location">{__("Location")}</label>
      <input type="text" class="form-control js_geocomplete" name="location" id="location">
    </div>
    <div class="form-group">
      <label class="form-label">{__("Start Date")}</label>
      <input type="datetime-local" class="form-control" name="start_date">
    </div>
    <div class="form-group">
      <label class="form-label">{__("End Date")}</label>
      <input type="datetime-local" class="form-control" name="end_date">
    </div>
    {if !$page_id}
      <div class="form-group">
        <label class="form-label" for="privacy">{__("Select Privacy")}</label>
        <select class="form-select" name="privacy">
          <option value="public">{__("Public Event")}</option>
          <option value="closed">{__("Closed Event")}</option>
          <option value="secret">{__("Secret Event")}</option>
        </select>
      </div>
    {/if}
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
      <label class="form-label" for="description">{__("About")}</label>
      <textarea class="form-control" name="description"></textarea>
    </div>
    <!-- custom fields -->
    {if $custom_fields}
      {include file='__custom_fields.tpl' _custom_fields=$custom_fields _registration=true}
    {/if}
    <!-- custom fields -->
    {if $page_id}
      <div class="divider"></div>
      <div class="form-group">
        <label class="form-label" for="tickets_link">{__("Tickets Link")}</label>
        <input type="text" class="form-control" name="tickets_link" id="tickets_link" value="{$event['event_tickets_link']}">
      </div>
      <div class="form-group">
        <label class="form-label" for="prices">{__("Prices Info")}</label>
        <textarea class="form-control" name="prices">{$event['event_prices']}</textarea>
      </div>
    {/if}
    <!-- error -->
    <div class="alert alert-danger mb0 mt10 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    {if $page_id}
      <input type="hidden" name="page_id" value="{$page_id}">
    {/if}
    <button type="submit" class="btn btn-primary">{__("Create")}</button>
  </div>
</form>