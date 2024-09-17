{if !$user->_logged_in && !$system['newsfeed_public']}
  {include file='index.landing.tpl'}
{else}
  {include file='index.newsfeed.tpl'}
{/if}