<div class="modal-header">
  <h6 class="modal-title">{__("Post Reviews")}</h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  {if $post['reviews_count'] > 0}
    <ul class="row">
      {foreach $post['reviews'] as $_review}
        {include file='__feeds_review.tpl' _darker=true _wide=true}
      {/foreach}
    </ul>
    {if $post['reviews_count'] >= $system['max_results_even']}
      <!-- see-more -->
      <div class="alert alert-post see-more mt0 mb20 js_see-more" data-get="reviews" data-id="{$post['post_id']}" data-type="post">
        <span>{__("See More")}</span>
        <div class="loader loader_small x-hidden"></div>
      </div>
      <!-- see-more -->
    {/if}
  {else}
    <p class="text-center text-muted mt10">
      {__("this post")} {__("doesn't have reviews")}
    </p>
  {/if}
</div>