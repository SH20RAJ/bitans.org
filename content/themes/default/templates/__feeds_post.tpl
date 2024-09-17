{if !$standalone}<li>{/if}
  <!-- post -->
  <div class="post 
                {if $_get == "posts_profile" && $user->_data['user_id'] == $post['author_id'] && ($post['is_hidden'] || $post['is_anonymous'])}is_hidden{/if} 
                {if $boosted}boosted{/if} 
                {if ($post['is_pending']) OR ($post['in_group'] && !$post['group_approved']) OR ($post['in_event'] && !$post['event_approved'])}pending{/if}
            " data-id="{$post['post_id']}">

    {if ($post['is_pending']) OR ($post['in_group'] && !$post['group_approved']) OR ($post['in_event'] && !$post['event_approved'])}
      <div class="pending-icon" data-bs-toggle="tooltip" title="{__("Pending Post")}">
        <i class="fa fa-clock"></i>
      </div>
    {/if}

    {if $standalone && $pinned}
      <div class="pin-icon" data-bs-toggle="tooltip" title="{__("Pinned Post")}">
        <i class="fa fa-bookmark"></i>
      </div>
    {/if}

    {if $standalone && $boosted}
      <div class="boosted-icon" data-bs-toggle="tooltip" title="{__("Promoted")}">
        <i class="fa fa-bullhorn"></i>
      </div>
    {/if}

    <!-- memory post -->
    {if $_get == "memories"}
      <div class="post-memory-header">
        <span class="js_moment" data-time="{$post['time']}">{$post['time']}</span>
      </div>
    {/if}
    <!-- memory post -->

    <!-- post body -->
    <div class="post-body">

      <!-- post top alert -->
      {if $_get == "posts_profile" && $user->_data['user_id'] == $post['author_id'] && ($post['is_hidden'] || $post['is_anonymous'])}
        <div class="post-top-alert">{__("Only you can see this post")}</div>
      {/if}
      <!-- post top alert -->

      {include file='__feeds_post.body.tpl' _post=$post _shared=false}

      {if !$post['needs_payment'] && !$post['needs_subscription'] && !$post['needs_pro_package'] && !$post['needs_age_verification']}
        <!-- post stats -->
        <div class="post-stats clearfix">
          <!-- reactions stats -->
          {if $post['reactions_total_count'] > 0}
            <div class="float-start mr10" data-toggle="modal" data-url="posts/who_reacts.php?post_id={$post['post_id']}">
              <div class="reactions-stats">
                {foreach $post['reactions'] as $reaction_type => $reaction_count}
                  {if $reaction_count > 0}
                    <div class="reactions-stats-item">
                      <div class="inline-emoji no_animation">
                        {include file='__reaction_emojis.tpl' _reaction=$reaction_type}
                      </div>
                    </div>
                  {/if}
                {/foreach}
                <!-- reactions count -->
                <span>
                  {$post['reactions_total_count']}
                </span>
                <!-- reactions count -->
              </div>
            </div>
          {/if}
          <!-- reactions stats -->

          <!-- comments & shares & views & plays & donations -->
          <span class="float-end">
            <!-- comments -->
            <span class="pointer js_comments-toggle">
              <i class="fa fa-comments"></i> {$post['comments']} {__("Comments")}
            </span>
            <!-- comments -->

            <!-- shares -->
            <span class="pointer ml10 {if $post['shares'] == 0}x-hidden{/if}" data-toggle="modal" data-url="posts/who_shares.php?post_id={$post['post_id']}">
              <i class="fa fa-share"></i> {$post['shares']} {__("Shares")}
            </span>
            <!-- shares -->

            <!-- views -->
            {if $system['posts_views_enabled']}
              <span class="pointer ml10">
                <i class="fa fa-eye"></i> {$post['views']} {__("Views")}
              </span>
            {/if}
            <!-- views -->

            <!-- video views -->
            {if $post['post_type'] == "video"}
              <span class="pointer ml10">
                <i class="fas fa-play-circle mr5"></i>{$post['video']['views']}
              </span>
            {/if}
            {if $post['post_type'] == "shared" && $post['origin']['post_type'] == "video"}
              <span class="pointer ml10">
                <i class="fas fa-play-circle mr5"></i>{$post['origin']['video']['views']}
              </span>
            {/if}
            <!-- video views -->

            <!-- audio views -->
            {if $post['post_type'] == "audio"}
              <span class="pointer ml10">
                <i class="fas fa-play-circle mr5"></i>{$post['audio']['views']}
              </span>
            {/if}
            {if $post['post_type'] == "shared" && $post['origin']['post_type'] == "audio"}
              <span class="pointer ml10">
                <i class="fas fa-play-circle mr5"></i>{$post['origin']['audio']['views']}
              </span>
            {/if}
            <!-- audio views -->

            <!-- donations -->
            {if $post['post_type'] == "funding"}
              <span class="pointer ml10" data-toggle="modal" data-url="posts/who_donates.php?post_id={$post['post_id']}">
                <i class="fa fa-hand-holding-usd"></i> {$post['funding']['total_donations']} {__("Donations")}
              </span>
            {/if}
            <!-- donations -->


            {if $system['posts_reviews_enabled']}
              <!-- reviews -->
              <span class="pointer ml10" data-toggle="modal" data-url="posts/who_reviews.php?post_id={$post['post_id']}">
                <i class="fa fa-star mr5"></i>{$post['reviews_count']} {__("Reviews")}
              </span>
              <!-- reviews -->
            {/if}
          </span>
          <!-- comments & shares & views & plays & donations -->
        </div>
        <!-- post stats -->

        <!-- post actions -->
        {if $user->_logged_in && $_get != "posts_information"}
          <div class="post-actions">
            <!-- reactions -->
            <div class="action-btn unselectable reactions-wrapper {if $post['i_react']}js_unreact-post{/if}" data-reaction="{$post['i_reaction']}">
              <!-- reaction-btn -->
              <div class="reaction-btn">
                {if !$post['i_react']}
                  <div class="reaction-btn-icon">
                    <i class="far fa-smile fa-fw action-icon"></i>
                  </div>
                  <span class="reaction-btn-name d-none d-xl-inline-block">{__("React")}</span>
                {else}
                  <div class="reaction-btn-icon">
                    <div class="inline-emoji no_animation">
                      {include file='__reaction_emojis.tpl' _reaction=$post['i_reaction']}
                    </div>
                  </div>
                  <span class="reaction-btn-name" style="color: {$reactions[$post['i_reaction']]['color']};">{__($reactions[$post['i_reaction']]['title'])}</span>
                {/if}
              </div>
              <!-- reaction-btn -->

              <!-- reactions-container -->
              <div class="reactions-container">
                {foreach $reactions_enabled as $reaction}
                  <div class="reactions_item reaction reaction-{$reaction@iteration} js_react-post" data-reaction="{$reaction['reaction']}" data-reaction-color="{$reaction['color']}" data-title="{__($reaction['title'])}">
                    {include file='__reaction_emojis.tpl' _reaction=$reaction['reaction']}
                  </div>
                {/foreach}
              </div>
              <!-- reactions-container -->
            </div>
            <!-- reactions -->

            <!-- comment -->
            <div class="action-btn js_comment {if $post['comments_disabled']}x-hidden{/if}">
              {include file='__svg_icons.tpl' icon="comment" class="action-icon mr5" width="24px" height="24px"}
              <span class="d-none d-xl-inline-block">{__("Comment")}</span>
            </div>
            <!-- comment -->

            <!-- share -->
            {if $post['privacy'] == "public" || ($post['in_group'] && $post['group_privacy'] == "public") || ($post['in_event'] && $post['event_privacy'] == "public") }
              <div class="action-btn" data-toggle="modal" data-url="posts/share.php?do=create&post_id={$post['post_id']}">
                {include file='__svg_icons.tpl' icon="share" class="action-icon mr5" width="24px" height="24px"}
                <span class="d-none d-xl-inline-block">{__("Share")}</span>
              </div>
            {/if}
            <!-- share -->

            <!-- review -->
            {if $post['author_id'] != $user->_data['user_id'] && $system['posts_reviews_enabled']}
              <div class="action-btn" data-toggle="modal" data-url="modules/review.php?do=review&id={$post['post_id']}&type=post">
                {include file='__svg_icons.tpl' icon="star" class="action-icon mr5" width="24px" height="24px"}
                <span class="d-none d-xl-inline-block">{__("Review")}</span>
              </div>
            {/if}
            <!-- review -->

            <!-- tips -->
            {if $post['author_id'] != $user->_data['user_id'] && $post['tips_enabled']}
              <div class="action-btn" data-toggle="modal" data-url="#send-tip" data-options='{ "id": "{$post['author_id']}"}'>
                {include file='__svg_icons.tpl' icon="tip" class="action-icon mr5" width="24px" height="24px"}
                <span class="ml5 d-none d-xl-inline-block">{__("Tip")}</span>
              </div>
            {/if}
            <!-- tips -->
          </div>
        {/if}
        <!-- post actions -->
      {/if}

    </div>
    <!-- post body -->

    <!-- post footer -->
    {if !$post['needs_payment'] && !$post['needs_subscription'] && !$post['needs_pro_package'] && !$post['needs_age_verification']}
      {if $user->_logged_in}
        <div class="post-footer {if !$standalone || ($page != "post" && $post['boosted'])}x-hidden{/if}">
          <!-- comments -->
          {include file='__feeds_post.comments.tpl'}
          <!-- comments -->
        </div>
      {else}
        <div class="post-footer">
          <div class="post-comments">
            <div class="pb10">
              <a href="{$system['system_url']}/signin">{__("Please log in to like, share and comment!")}</a>
            </div>
          </div>
        </div>
      {/if}
    {/if}
    <!-- post footer -->

    <!-- post approval -->
    {if ($post['in_group'] && $post['is_group_admin'] &&!$post['group_approved']) OR ($post['in_event'] && $post['is_event_admin'] &&!$post['event_approved']) }
      <div class="post-approval">
        <button class="btn btn-success btn-sm mr5 js_approve-post"><i class="fa fa-check mr5"></i>{__("Approve")}</button>
        <button class="btn btn-danger btn-sm js_delete-post"><i class="fa fa-times mr5"></i>{__("Decline")}</button>
      </div>
    {/if}
    <!-- post approval -->

  </div>
  <!-- post -->
  {if !$standalone}
</li>{/if}