<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="share" class="main-icon mr10" width="24px" height="24px"}
    {__("Share")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="posts/share.php?do=publish&post_id={$post['post_id']}">
  <div class="modal-body">

    {if $photo}
      <div class="h6 text-center">
        {__("Photo link")}
      </div>

      <div style="margin: 25px auto;">
        <div class="input-group">
          <input type="text" disabled class="form-control" value="{$system['system_url']}/photos/{$photo['photo_id']}">
          <button type="button" class="btn btn-light js_clipboard" data-clipboard-text="{$system['system_url']}/photos/{$photo['photo_id']}" data-bs-toggle="tooltip" title='{__("Copy")}'>
            <i class="fas fa-copy"></i>
          </button>
        </div>
      </div>

      {if $system['social_share_enabled']}
        <div class="post-social-share">
          {include file='__social_share.tpl' _link="{$system['system_url']}/photos/{$photo['photo_id']}"}
        </div>
      {/if}

      <div class="h6 text-center">
        {__("Post link")}
      </div>
    {/if}

    <div style="margin: 25px auto;">
      <div class="input-group">
        <input type="text" disabled class="form-control" value="{$system['system_url']}/posts/{$post['post_id']}">
        <button type="button" class="btn btn-light js_clipboard" data-clipboard-text="{$system['system_url']}/posts/{$post['post_id']}" data-bs-toggle="tooltip" title='{__("Copy")}'>
          <i class="fas fa-copy"></i>
        </button>
      </div>
    </div>

    {if $system['social_share_enabled']}
      <div class="post-social-share">
        {include file='__social_share.tpl' _link="{$system['system_url']}/posts/{$post['post_id']}"}
      </div>
    {/if}

    <div class="h5 text-center">
      {__("Share the post to")}
    </div>

    <!-- share to options -->
    <div class="mb20 text-center">
      <!-- timeline -->
      <input class="x-hidden input-label" type="radio" name="share_to" id="share_to_timeline" value="timeline" checked="checked" />
      <label class="button-label small" for="share_to_timeline">
        <div class="icon">
          {include file='__svg_icons.tpl' icon="newsfeed" class="main-icon" width="20px" height="20px"}
        </div>
        <div class="title">{__("Timeline")}</div>
      </label>
      <!-- timeline -->
      <!-- page -->
      {if $system['pages_enabled'] && $pages}
        <input class="x-hidden input-label" type="radio" name="share_to" id="share_to_page" value="page" />
        <label class="button-label small" for="share_to_page">
          <div class="icon">
            {include file='__svg_icons.tpl' icon="pages" class="main-icon" width="20px" height="20px"}
          </div>
          <div class="title">{__("Page")}</div>
        </label>
      {/if}
      <!-- page -->
      <!-- group -->
      {if $system['groups_enabled'] && $groups}
        <input class="x-hidden input-label" type="radio" name="share_to" id="share_to_group" value="group" />
        <label class="button-label small" for="share_to_group">
          <div class="icon">
            {include file='__svg_icons.tpl' icon="groups" class="main-icon" width="20px" height="20px"}
          </div>
          <div class="title">{__("Group")}</div>
        </label>
      {/if}
      <!-- group -->
      <!-- event -->
      {if $system['events_enabled'] && $events}
        <input class="x-hidden input-label" type="radio" name="share_to" id="share_to_event" value="event" />
        <label class="button-label small" for="share_to_event">
          <div class="icon">
            {include file='__svg_icons.tpl' icon="events" class="main-icon" width="20px" height="20px"}
          </div>
          <div class="title">{__("Event")}</div>
        </label>
      {/if}
      <!-- event -->
    </div>
    <!-- share to options -->

    <div id="js_share-to-page" class="x-hidden">
      <div class="form-group">
        <label class="form-label">{__("Select Page")}</label>
        <select class="form-select" name="page">
          {foreach $pages as $page}
            <option value="{$page['page_id']}">{$page['page_title']}</option>
          {/foreach}
        </select>
      </div>
    </div>

    <div id="js_share-to-group" class="x-hidden">
      <div class="form-group">
        <label class="form-label">{__("Select Group")}</label>
        <select class="form-select" name="group">
          {foreach $groups as $group}
            <option value="{$group['group_id']}">{$group['group_title']}</option>
          {/foreach}
        </select>
      </div>
    </div>

    <div id="js_share-to-event" class="x-hidden">
      <div class="form-group">
        <label class="form-label">{__("Select Event")}</label>
        <select class="form-select" name="event">
          {foreach $events as $event}
            <option value="{$event['event_id']}">{$event['event_title']}</option>
          {/foreach}
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="form-label">{__("Message")}</label>
          <textarea name="message" rows="3" dir="auto" class="form-control"></textarea>
        </div>
      </div>
    </div>

    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">{__("Share")}</button>
  </div>
</form>

<script>
  /* share post */
  $('input[type=radio][name=share_to]').on('change', function() {
    switch ($(this).val()) {
      case 'timeline':
        $('#js_share-to-page').hide();
        $('#js_share-to-group').hide();
        $('#js_share-to-event').hide();
        break;
      case 'page':
        $('#js_share-to-page').fadeIn();
        $('#js_share-to-group').hide();
        $('#js_share-to-event').hide();
        break;
      case 'group':
        $('#js_share-to-page').hide();
        $('#js_share-to-group').fadeIn();
        $('#js_share-to-event').hide();
        break;
      case 'event':
        $('#js_share-to-page').hide();
        $('#js_share-to-group').hide();
        $('#js_share-to-event').fadeIn();
        break;
    }
  });
</script>