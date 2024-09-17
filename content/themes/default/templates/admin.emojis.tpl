<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == ""}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/emojis/add" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New Emoji")}
        </a>
      </div>
    {else}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/emojis" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {/if}
    <i class="fa fa-grin-tears mr10"></i>{__("Emojis")}
    {if $sub_view == "add"} &rsaquo; {__("Add New Emoji")}{/if}
    {if $sub_view == "edit"} &rsaquo; {__("Edit Emoji")}{/if}
  </div>

  {if $sub_view == ""}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Preview")}</th>
              <th>{__("Native")}</th>
              <th>{__("Twemoji Class")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['emoji_id']}</td>
                <td><i class="twa twa-2x twa-{$row['class']}"></i></td>
                <td><span class="text-xxlg">{$row['unicode_char']}</span></td>
                <td>{$row['class']}</td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/emojis/edit/{$row['emoji_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="emoji" data-id="{$row['emoji_id']}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "add"}

    <form class="js_ajax-forms" data-url="admin/emojis.php?do=add">
      <div class="card-body">
        <div class="alert alert-info">
          <div class="icon">
            <i class="fa fa-info-circle fa-2x"></i>
          </div>
          <div class="text pt5">
            {__("System uses")} <a class="alert-link" target="_blank" href="https://github.com/SebastianAigner/twemoji-amazing">Twemoji Amazing</a> {__("and you can check")} <a class="alert-link" target="_blank" href="https://unicode.org/emoji/charts/emoji-list.html">{__("Emoji Cheat Sheet")}</a> {__("for the Emojis")}.<br>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Native Emoji")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="unicode_char">
            <div class="form-text">
              {__("You can get it from here")} <a target="_blank" href="https://getemoji.com/">https://getemoji.com/</a>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Twemoji Class")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="class">
            <div class="form-text">
              {__("You must replace spaces with hyphens, For example: 'grinning face' become 'grinning-face'")}
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

  {elseif $sub_view == "edit"}

    <form class="js_ajax-forms" data-url="admin/emojis.php?do=edit&id={$data['emoji_id']}">
      <div class="card-body">
        <div class="alert alert-info">
          <div class="icon">
            <i class="fa fa-info-circle fa-2x"></i>
          </div>
          <div class="text pt5">
            {__("System uses")} <a class="alert-link" target="_blank" href="https://github.com/SebastianAigner/twemoji-amazing">Twemoji Amazing</a> {__("and you can check")} <a class="alert-link" target="_blank" href="https://unicode.org/emoji/charts/emoji-list.html">{__("Emoji Cheat Sheet")}</a> {__("for the Emojis")}.<br>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Native Emoji")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="unicode_char" value="{$data['unicode_char']}">
            <div class="form-text">{__("You can get it from here")} <a target="_blank" href="https://getemoji.com/">https://getemoji.com/</a></div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Twemoji Class")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="class" value="{$data['class']}">
            <div class="form-text">
              {__("You must replace spaces with hyphens, For example: 'grinning face' become 'grinning-face'")}
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

  {/if}
</div>