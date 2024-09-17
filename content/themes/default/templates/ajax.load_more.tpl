{if $get == 'newsfeed' || $get == 'posts_profile' || $get == 'posts_page' || $get == 'posts_group' || $get == 'posts_group_pending' || $get == 'posts_group_pending_all' || $get == 'posts_event' || $get == 'posts_event_pending' || $get == 'posts_event_pending_all' || $get == 'saved' || $get == 'memories' || $get == 'boosted' || $get == 'popular' || $get == 'discover' }
  {include file='_ads.tpl'}
  {include file='_ads_campaigns.tpl'}

  {foreach $data as $post}
    {include file='__feeds_post.tpl' _get=$get}
  {/foreach}


{elseif $get == 'products'}
  {foreach $data as $post}
    {include file='__feeds_product.tpl'}
  {/foreach}


{elseif $get == 'shares'}
  {foreach $data as $post}
    {include file='__feeds_post.tpl' _snippet=true}
  {/foreach}


{elseif $get == 'articles'}
  {foreach $data as $article}
    {include file='__feeds_article.tpl' _tpl="featured"}
  {/foreach}


{elseif $get == 'category_articles'}
  {foreach $data as $article}
    {include file='__feeds_article.tpl'}
  {/foreach}


{elseif $get == 'fundings'}
  {foreach $data as $funding}
    {include file='__feeds_funding.tpl'}
  {/foreach}


{elseif $get == 'post_comments' || $get == 'post_comments_top' || $get == 'post_comments_all' || $get == 'photo_comments' || $get == 'photo_comments_top' || $get == 'photo_comments_all'}
  {foreach $data as $comment}
    {include file='__feeds_comment.tpl' _comment=$comment}
  {/foreach}


{elseif $get == 'comment_replies'}
  {foreach $data as $comment}
    {include file='__feeds_comment.tpl' _comment=$comment _is_reply=true}
  {/foreach}


{elseif $get == 'photos'}
  {foreach $data as $photo}
    {if $type == "user"}
      {include file='__feeds_photo.tpl' _context=$context _can_pin=true}
    {else}
      {include file='__feeds_photo.tpl' _context=$context}
    {/if}
  {/foreach}


{elseif $get == 'profile_photos'}
  {foreach $data as $photo}
    {include file='__feeds_profile_photo.tpl' _filter=$filter}
  {/foreach}


{elseif $get == 'albums'}
  {foreach $data as $album}
    {include file='__feeds_album.tpl'}
  {/foreach}


{elseif $get == 'videos'}
  {foreach $data as $video}
    {include file='__feeds_video.tpl'}
  {/foreach}


{elseif $get == 'post_reactions' || $get == 'photo_reactions' || $get == 'comment_reactions' || $get == 'donors' || $get == 'voters' || $get == 'blocks' || $get == 'affiliates' || $get == 'group_requests'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"] _reaction=$_user["reaction"] _donation=$_user['donation_amount'] _donation_time=$_user['donation_time']}
  {/foreach}


{elseif $get == 'friend_requests'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="list" _connection="request"}
  {/foreach}


{elseif $get == 'friend_requests_sent'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="list" _connection="cancel"}
  {/foreach}


{elseif $get == 'mutual_friends'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="list" _connection="remove"}
  {/foreach}


{elseif $get == 'new_people'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="list" _connection="add"}
  {/foreach}


{elseif $get == 'friends' || $get == 'followers' || $get == 'followings' || $get == 'subscribers' || $get == 'page_invites' || $get == 'group_members' || $get == 'group_invites'  || $get == 'event_going'  || $get == 'event_interested'  || $get == 'event_invited'  || $get == 'event_invites'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="box" _connection=$_user["connection"]}
  {/foreach}


{elseif $get == 'subscriptions'}
  {foreach $data as $_subscription}
    {if $_subscription['node_type'] == "profile"}
      {include file='__feeds_user.tpl' _user=$_subscription _tpl="box" _connection='unsubscribe' _darker=true}
    {elseif $_subscription['node_type'] == "page"}
      {include file='__feeds_page.tpl' _page=$_subscription _tpl="box" _connection='unsubscribe' _darker=true}
    {elseif $_subscription['node_type'] == "group"}
      {include file='__feeds_group.tpl' _group=$_subscription _tpl="box" _connection='unsubscribe' _darker=true}
    {/if}
  {/foreach}


{elseif $get == 'page_members' || $get == 'page_admins' || $get == 'group_members_manage' || $get == 'group_admins'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="list" _connection=$_user["connection"]}
  {/foreach}


{elseif $get == 'pages' || $get == 'suggested_pages' || $get == 'category_pages' || $get == 'liked_pages' || $get == 'profile_pages'}
  {foreach $data as $_page}
    {include file='__feeds_page.tpl' _tpl='box'}
  {/foreach}


{elseif $get == 'boosted_pages'}
  {foreach $data as $_page}
    {include file='__feeds_page.tpl' _tpl='list'}
  {/foreach}


{elseif $get == 'groups' || $get == 'suggested_groups' || $get == 'category_groups' || $get == 'joined_groups' || $get == 'profile_groups'}
  {foreach $data as $_group}
    {include file='__feeds_group.tpl' _tpl='box'}
  {/foreach}


{elseif $get == 'events' || $get == 'suggested_events' || $get == 'category_events' || $get == 'going_events' || $get == 'interested_events' || $get == 'invited_events'}
  {foreach $data as $_event}
    {include file='__feeds_event.tpl' _tpl='box'}
  {/foreach}


{elseif $get == 'profile_events' || $get == 'page_events'}
  {foreach $data as $_event}
    {include file='__feeds_event.tpl' _tpl='box' _darker=true}
  {/foreach}


{elseif $get == 'games' || $get == 'genre_games' || $get == 'played_games'}
  {foreach $data as $_game}
    {include file='__feeds_game.tpl' _tpl='box'}
  {/foreach}


{elseif $get == 'notifications'}
  {foreach $data as $notification}
    {include file='__feeds_notification.tpl'}
  {/foreach}


{elseif $get == 'conversations'}
  {foreach $data as $conversation}
    {include file='__feeds_conversation.tpl'}
  {/foreach}


{elseif $get == 'messages'}
  {foreach $data as $message}
    {include file='__feeds_message.tpl'}
  {/foreach}


{elseif $get == 'job_candidates'}
  {foreach $data as $candidate}
    {include file='__feeds_candidate.tpl'}
  {/foreach}


{elseif $get == 'search_posts' || $get == 'search_articles'}
  {foreach $data as $post}
    {include file='__feeds_post.tpl'}
  {/foreach}


{elseif $get == 'search_users'}
  {foreach $data as $_user}
    {include file='__feeds_user.tpl' _tpl="list" _connection=$_user['connection']}
  {/foreach}


{elseif $get == 'search_pages'}
  {foreach $data as $_page}
    {include file='__feeds_page.tpl' _tpl="list"}
  {/foreach}


{elseif $get == 'search_groups'}
  {foreach $data as $_group}
    {include file='__feeds_group.tpl' _tpl="list"}
  {/foreach}


{elseif $get == 'search_events'}
  {foreach $data as $_event}
    {include file='__feeds_event.tpl' _tpl="list"}
  {/foreach}


{elseif $get == 'orders'}
  {foreach $data as $order}
    {include file='_order.tpl'}
  {/foreach}


{elseif $get == 'sales_orders'}
  {foreach $data as $order}
    {include file='_order.tpl' sales=true}
  {/foreach}


{elseif $get == 'reviews'}
  {foreach $data as $_review}
    {include file='__feeds_review.tpl' _darker=true}
  {/foreach}

{/if}