<div class="dropdown-menu dropdown-menu-right">
  <!-- Order -->
  <div class="dropdown-item pointer" data-title='{__("Post Order")}'>
    <i class="fa fa-sort-amount-down mr10"></i>{__("Order")}
    <div class="float-right">
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" name="posts_order" id="posts_order_latest" class="custom-control-input js_posts-order" value="latest" {if isset($smarty.session.posts_order) && $smarty.session.posts_order == "latest"}checked{/if}>
        <label class="custom-control-label" for="posts_order_latest">{__("Latest")}</label>
      </div>
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" name="posts_order" id="posts_order_random" class="custom-control-input js_posts-order" value="random" {if !isset($smarty.session.posts_order) || $smarty.session.posts_order == "random"}checked{/if}>
        <label class="custom-control-label" for="posts_order_random">{__("Random")}</label>
      </div>
    </div>
  </div>
  <!-- Order -->
  <div class="dropdown-divider"></div>
  <!-- Filter -->
  <div class="dropdown-item pointer js_posts-filter" data-value="all" {if $selected_filter == "all"}checked{/if}>
    <i class="fa fa-bars mr10"></i>{__("All Posts")}
  </div>
  <div class="dropdown-item pointer js_posts-filter" data-value="link" {if $selected_filter == "link"}checked{/if}>
    <i class="fa fa-link mr10"></i>{__("Links")}
  </div>
  <div class="dropdown-item pointer js_posts-filter" data-value="media" {if $selected_filter == "media"}checked{/if}>
    <i class="fa fa-video mr10"></i>{__("Media")}
  </div>
  <div class="dropdown-item pointer js_posts-filter" data-value="photos" {if $selected_filter == "photos"}checked{/if}>
    <i class="fa fa-file-image mr10"></i>{__("Photos")}
  </div>
  <div class="dropdown-item pointer js_posts-filter" data-value="video" {if $selected_filter == "video"}checked{/if}>
    <i class="fa fa-film mr10"></i>{__("Videos")}
  </div>
  <!-- Filter -->
</div>
