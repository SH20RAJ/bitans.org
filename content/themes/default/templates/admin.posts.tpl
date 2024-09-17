<div class="card">
  <div class="card-header with-icon">
    {if $sub_view == "find"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/posts" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
    {elseif $sub_view == "videos_categories"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/posts/add_videos_category" class="btn btn-md btn-primary">
          <i class="fa fa-plus"></i><span class="ml5 d-none d-lg-inline-block">{__("Add New Category")}</span>
        </a>
      </div>
    {elseif $sub_view == "add_videos_category" || $sub_view == "edit_videos_category"}
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/posts/videos_categories" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left"></i><span class="ml5 d-none d-lg-inline-block">{__("Go Back")}</span>
        </a>
      </div>
    {/if}
    <i class="fa fa-newspaper mr10"></i>{__("Posts")}
    {if $sub_view == "find"} &rsaquo; {__("Find")}{/if}
    {if $sub_view == "pending"} &rsaquo; {__("Pending")}{/if}
    {if $sub_view == "videos_categories"} &rsaquo; {__("Videos Categories")}{/if}
    {if $sub_view == "add_videos_category"} &rsaquo; {__("Videos Categories")} &rsaquo; {__("Add New Category")}{/if}
    {if $sub_view == "edit_videos_category"} &rsaquo; {__("Videos Categories")} &rsaquo; {$data['category_name']}{/if}
  </div>

  {if $sub_view == "" || $sub_view == "pending" || $sub_view == "find"}

    <div class="card-body">

      {if $sub_view == "" || $sub_view == "pending"}
        <div class="row">
          <div class="col-md-6 col-lg-6 col-xl-3">
            <div class="stat-panel bg-gradient-indigo">
              <div class="stat-cell narrow">
                <i class="fa fa-newspaper bg-icon"></i>
                <span class="text-xxlg">{$insights['posts']}</span><br>
                <span class="text-lg">{__("Posts")}</span><br>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-6 col-xl-3">
            <div class="stat-panel bg-gradient-warning">
              <div class="stat-cell narrow">
                <i class="fa fa-clock bg-icon"></i>
                <span class="text-xxlg">{$insights['pending_posts']}</span><br>
                <span class="text-lg">{__("Pending Posts")}</span><br>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-6 col-xl-3">
            <div class="stat-panel bg-gradient-primary">
              <div class="stat-cell narrow">
                <i class="fa fa-comments bg-icon"></i>
                <span class="text-xxlg">{$insights['posts_comments']}</span><br>
                <span class="text-lg">{__("Comments")}</span><br>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-6 col-xl-3">
            <div class="stat-panel bg-gradient-info">
              <div class="stat-cell narrow">
                <i class="fa fa-smile bg-icon"></i>
                <span class="text-xxlg">{$insights['posts_likes']}</span><br>
                <span class="text-lg">{__("Reactions")}</span><br>
              </div>
            </div>
          </div>
        </div>
      {/if}

      <!-- search form -->
      <div class="mb20">
        <form class="d-flex flex-row align-items-center flex-wrap" action="{$system['system_url']}/{$control_panel['url']}/posts/find" method="get">
          <div class="form-group mb0">
            <div class="input-group">
              <input type="text" class="form-control" name="query">
              <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-search mr5"></i>{__("Search")}</button>
            </div>
          </div>
        </form>
        <div class="form-text small">
          {__("Search by Post ID or Text")}
        </div>
      </div>
      <!-- search form -->

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Author")}</th>
              <th>{__("Type")}</th>
              <th>{__("Approved")}</th>
              <th>{__("Time")}</th>
              <th>{__("Link")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                <tr>
                  <td>
                    {$row['post_id']}
                  </td>
                  <td>
                    <a target="_blank" href="{$row['post_author_url']}">
                      <img class="tbl-image" src="{$row['post_author_picture']}">
                      {$row['post_author_name']}
                    </a>
                  </td>
                  <td>
                    <span class="badge rounded-pill badge-lg bg-secondary">
                      {if $row['post_type'] == "shared"}
                        {__("Share")}

                      {elseif $row['post_type'] == ""}
                        {__("Text")}

                      {elseif $row['post_type'] == "map"}
                        {__("Maps")}

                      {elseif $row['post_type'] == "link"}
                        {__("Link")}

                      {elseif $row['post_type'] == "media"}
                        {__("Media")}

                      {elseif $row['post_type'] == "live"}
                        {__("Live Streaming")}

                      {elseif $row['post_type'] == "photos"}
                        {__("Photos")}

                      {elseif $row['post_type'] == "album"}
                        {__("Album")}

                      {elseif $row['post_type'] == "profile_picture"}
                        {__("Profile Picture")}

                      {elseif $row['post_type'] == "profile_cover"}
                        {__("Cover Photo")}

                      {elseif $row['post_type'] == "page_picture"}
                        {__("Page Picture")}

                      {elseif $row['post_type'] == "page_cover"}
                        {__("Page Cover")}

                      {elseif $row['post_type'] == "group_picture"}
                        {__("Group Picture")}

                      {elseif $row['post_type'] == "group_cover"}
                        {__("Group Cover")}

                      {elseif $row['post_type'] == "event_cover"}
                        {__("Event Cover")}

                      {elseif $row['post_type'] == "article"}
                        {__("Article")}

                      {elseif $row['post_type'] == "product"}
                        {__("Product")}

                      {elseif $row['post_type'] == "funding"}
                        {__("Funding")}

                      {elseif $row['post_type'] == "offer"}
                        {__("Offer")}

                      {elseif $row['post_type'] == "job"}
                        {__("Job")}

                      {elseif $row['post_type'] == "poll"}
                        {__("Poll")}

                      {elseif $row['post_type'] == "video"}
                        {__("Video")}

                      {elseif $row['post_type'] == "audio"}
                        {__("Audio")}

                      {elseif $row['post_type'] == "file"}
                        {__("File")}

                      {/if}
                    </span>
                  </td>
                  <td>
                    {if $row['pre_approved'] || $row['has_approved']}
                      <span class="badge rounded-pill bg-success">
                        {__("Yes")}
                      </span>
                    {else}
                      <span class="badge rounded-pill bg-danger">
                        {__("No")}
                      </span>
                    {/if}
                  </td>
                  <td>
                    <span class="js_moment" data-time="{$row['time']}">{$row['time']}</span>
                  </td>
                  <td>
                    <a class="btn btn-sm btn-light" href="{$system['system_url']}/posts/{$row['post_id']}" target="_blank">
                      <i class="fa fa-eye mr5"></i>{__("View")}
                    </a>
                  </td>
                  <td>
                    {if $sub_view == "pending"}
                      <button data-bs-toggle="tooltip" title='{__("Approve")}' class="btn btn-sm btn-icon btn-rounded btn-success js_post-approve" data-id="{$row['post_id']}">
                        <i class="fa fa-check"></i>
                      </button>
                    {/if}
                    <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="post" data-id="{$row['post_id']}">
                      <i class="fa fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              {/foreach}
            {else}
              <tr>
                <td colspan="7" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>

      {$pager}

    </div>

  {elseif $sub_view == "videos_categories"}

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_treegrid">
          <thead>
            <tr>
              <th>{__("Title")}</th>
              <th>{__("Description")}</th>
              <th>{__("Order")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {if $rows}
              {foreach $rows as $row}
                {include file='__categories.recursive_rows.tpl' _url="posts" _edit_slug="videos" _handle="video_category"}
              {/foreach}
            {else}
              <tr>
                <td colspan="5" class="text-center">
                  {__("No data to show")}
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "add_videos_category"}

    <form class="js_ajax-forms" data-url="admin/posts.php?do=add_videos_category">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_name">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="category_description" rows="3"></textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Parent Category")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="category_parent_id">
              <option value="0">{__("Set as a Partent Category")}</option>
              {foreach $categories as $category}
                {include file='__categories.recursive_options.tpl'}
              {/foreach}
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_order">
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

  {elseif $sub_view == "edit_videos_category"}

    <form class="js_ajax-forms" data-url="admin/posts.php?do=edit_videos_category&id={$data['category_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Name")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_name" value="{$data['category_name']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Description")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="category_description" rows="3">{$data['category_description']}</textarea>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Parent Category")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="category_parent_id">
              <option value="0">{__("Set as a Partent Category")}</option>
              {foreach $data["categories"] as $category}
                {include file='__categories.recursive_options.tpl' data_category=$data['category_parent_id']}
              {/foreach}
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Order")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="category_order" value="{$data['category_order']}">
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