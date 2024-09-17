{strip}

  <!-- Initialize -->
  <script>
    /* initialize vars */
    var site_title = "{__($system['system_title'])}";
    var site_path = "{$system['system_url']}";
    var ajax_path = site_path + "/includes/ajax/";
    var uploads_path = "{$system['system_uploads']}";
    var current_page = "{$page}";
    var system_debugging_mode = {if $system['DEBUGGING']}true{else}false{/if};
    /* language */
    var system_langauge_dir = "{strtolower($system['language']['dir'])}";
    var system_langauge_code = "{substr($system['language']['code'], 0, -3)}";
    /* datetime */
    var system_datetime_format = {if $system['system_datetime_format'] == "m/d/Y H:i"}'MM/DD/YYYY HH:mm'{else}'DD/MM/YYYY HH:mm'{/if};
    /* theme */
    var theme_mode_night = {if $system['theme_mode_night']}true{else}false{/if};
    var theme_dir_rtl = {if $system['language']['dir'] == "LTR"}false{else}true{/if};
    /* payments */
    var currency = "{$system['system_currency']}";
    var stripe_key = {if $system['stripe_mode'] == "live"}"{$system['stripe_live_publishable']}"{else}"{$system['stripe_test_publishable']}"{/if};
    var twocheckout_merchant_code = "{$system['2checkout_merchant_code']}";
    var twocheckout_publishable_key = "{$system['2checkout_publishable_key']}";
    var razorpay_key = "{$system['razorpay_key_id']}";
    var securionpay_key = "{$system['securionpay_api_key']}";
    /* features */
    var adblock_detector = {if !$user->_is_admin && $system['adblock_detector_enabled']}true{else}false{/if};
    var location_finder = {if !$user->_is_admin && $system['location_finder_enabled']}true{else}false{/if};
    var desktop_infinite_scroll = {if $system['desktop_infinite_scroll']}true{else}false{/if};
    var mobile_infinite_scroll = {if $system['mobile_infinite_scroll']}true{else}false{/if};
    var auto_play_videos = {if $system['auto_play_videos']}true{else}false{/if};
    {if $user->_logged_in}
      /* user */
      var user_id = "{$user->_data['user_id']}";
      /* ajax */
      var min_data_heartbeat = "{$system['data_heartbeat']*1000}";
      var min_chat_heartbeat = "{$system['chat_heartbeat']*1000}";
      /* uploads */
      var secret = "{$secret}";
      var accpeted_video_extensions = "{$system['accpeted_video_extensions']}";
      var accpeted_audio_extensions = "{$system['accpeted_audio_extensions']}";
      var accpeted_file_extensions = "{$system['accpeted_file_extensions']}";
      var tinymce_photos_enabled = {if $system['tinymce_photos_enabled']}true{else}false{/if};
      /* chat */
      var chat_enabled = {if $system['chat_enabled']}true{else}false{/if};
      var chat_typing_enabled = {if $system['chat_typing_enabled']}true{else}false{/if};
      var chat_seen_enabled = {if $system['chat_seen_enabled']}true{else}false{/if};
      var chat_sound = {if $user->_data['chat_sound']}true{else}false{/if};
      /* live */
      var live_enabled = {if $system['live_enabled']}true{else}false{/if};
      {if $system['live_enabled']}
        var agora_app_id = "{$system['agora_app_id']}";
        {if $page == "live"}
          var agora_uid = {$agora['uid']};
          var agora_token = "{$agora['token']}";
          var agora_channel_name = "{$agora['channel_name']}";
        {/if}
      {/if}
      /* notifications */
      var notifications_sound = {if $user->_data['notifications_sound']}true{else}false{/if};
      var noty_notifications_enabled = {if $system['noty_notifications_enabled']}true{else}false{/if};
      var browser_notifications_enabled = {if $system['browser_notifications_enabled']}true{else}false{/if};
      /* stories */
      {if $system['stories_enabled']}
        var stories_duration = "{$system['stories_duration']}";
      {/if}
      /* posts */
      var daytime_msg_enabled = {if $daytime_msg_enabled}true{else}false{/if};
      var giphy_key = "{$system['giphy_key']}";
      var geolocation_enabled = {if $system['geolocation_enabled']}true{else}false{/if};
      var yandex_key = "{$system['yandex_key']}";
      var post_translation_enabled = {if $system['post_translation_enabled']}true{else}false{/if};
      var voice_notes_durtaion = "{$system['voice_notes_durtaion']}";
      var voice_notes_encoding = "{$system['voice_notes_encoding']}";
    {/if}
  </script>
  <script>
    /* i18n for JS */
    var __ = [];
    __['Ask something'] = "{__('Ask something')}";
    __['Add Friend'] = "{__('Add Friend')}";
    __['Friends'] = "{__('Friends')}"; 
    __['Sent'] = "{__('Sent')}";
    __['Following'] = "{__('Following')}";
    __['Follow'] = "{__('Follow')}";
    __['Pending'] = "{__('Pending')}";
    __['Remove'] = "{__('Remove')}";
    __['Error'] = "{__('Error')}";
    __['Success'] = "{__('Success')}";
    __['Loading'] = "{__('Loading')}";
    __['Like'] = "{__('Like')}";
    __['Unlike'] = "{__('Unlike')}";
    __['React'] = "{__('React')}";
    __['Joined'] = "{__('Joined')}";
    __['Join'] = "{__('Join')}";
    __['Remove Admin'] = "{__('Remove Admin')}";
    __['Make Admin'] = "{__('Make Admin')}";
    __['Going'] = "{__('Going')}";
    __['Interested'] = "{__('Interested')}";
    __['Delete'] = "{__('Delete')}";
    __['Delete Cover'] = "{__('Delete Cover')}";
    __['Delete Picture'] = "{__('Delete Picture')}";
    __['Delete Post'] = "{__('Delete Post')}";
    __['Delete Comment'] = "{__('Delete Comment')}";
    __['Delete Conversation'] = "{__('Delete Conversation')}";
    __['Report'] = "{__('Report')}";
    __['Block User'] = "{__('Block User')}";
    __['Unblock User'] = "{__('Unblock User')}";
    __['Mark as Available'] = "{__('Mark as Available')}";
    __['Mark as Sold'] = "{__('Mark as Sold')}";
    __['Save Post'] = "{__('Save Post')}";
    __['Unsave Post'] = "{__('Unsave Post')}";
    __['Boost Post'] = "{__('Boost Post')}";
    __['Unboost Post'] = "{__('Unboost Post')}";
    __['Pin Post'] = "{__('Pin Post')}";
    __['Unpin Post'] = "{__('Unpin Post')}";
    __['For Everyone'] = "{__('For Everyone')}";
    __['For Subscribers Only'] = "{__('For Subscribers Only')}";
    __['Verify'] = "{__('Verify')}";
    __['Decline'] = "{__('Decline')}";
    __['Boost'] = "{__('Boost')}";
    __['Unboost'] = "{__('Unboost')}";
    __['Mark as Paid'] = "{__('Mark as Paid')}";
    __['Read more'] = "{__('Read more')}";
    __['Read less'] = "{__('Read less')}";
    __['Turn On Chat'] = "{__('Turn On Chat')}";
    __['Turn Off Chat'] = "{__('Turn Off Chat')}";
    __['Monthly Average'] = "{__('Monthly Average')}";
    __['PayIn Methods'] = "{__('PayIn Methods')}";
    __['PayIn Types'] = "{__('PayIn Types')}";
    __['Commissions Types'] = "{__('Commissions Types')}";
    __['Packages'] = "{__('Packages')}";
    __['Jan'] = "{__('Jan')}";
    __['Feb'] = "{__('Feb')}";
    __['Mar'] = "{__('Mar')}";
    __['Apr'] = "{__('Apr')}";
    __['May'] = "{__('May')}";
    __['Jun'] = "{__('Jun')}";
    __['Jul'] = "{__('Jul')}";
    __['Aug'] = "{__('Aug')}";
    __['Sep'] = "{__('Sep')}";
    __['Oct'] = "{__('Oct')}";
    __['Nov'] = "{__('Nov')}";
    __['Dec'] = "{__('Dec')}";
    __['Users'] = "{__('Users')}";
    __['Pages'] = "{__('Pages')}";
    __['Groups'] = "{__('Groups')}";
    __['Events'] = "{__('Events')}";
    __['Posts'] = "{__('Posts')}";
    __['Translated'] = "{__('Translated')}";
    __['Are you sure you want to delete this?'] = "{__('Are you sure you want to delete this?')}";
    __['Are you sure you want to remove your cover photo?'] = "{__('Are you sure you want to remove your cover photo?')}";
    __['Are you sure you want to remove your profile picture?'] = "{__('Are you sure you want to remove your profile picture?')}";
    __['Are you sure you want to delete this post?'] = "{__('Are you sure you want to delete this post?')}";
    __['Are you sure you want to delete this comment?'] = "{__('Are you sure you want to delete this comment?')}";
    __['Are you sure you want to delete this conversation?'] = "{__('Are you sure you want to delete this conversation?')}";
    __['Are you sure you want to report this?'] = "{__('Are you sure you want to report this?')}";
    __['Are you sure you want to block this user?'] = "{__('Are you sure you want to block this user?')}";
    __['Are you sure you want to unblock this user?'] = "{__('Are you sure you want to unblock this user?')}";
    __['Are you sure you want to delete your account?'] = "{__('Are you sure you want to delete your account?')}";
    __['Are you sure you want to verify this request?'] = "{__('Are you sure you want to verify this request?')}";
    __['Are you sure you want to decline this request?'] = "{__('Are you sure you want to decline this request?')}";
    __['Are you sure you want to approve this request?'] = "{__('Are you sure you want to approve this request?')}";
    __['Are you sure you want to do this?'] = "{__('Are you sure you want to do this?')}";
    __['Factory Reset'] = "{__('Factory Reset')}";
    __['Are you sure you want to reset your website?'] = "{__('Are you sure you want to reset your website?')}";
    __['There is something that went wrong!'] = "{__('There is something that went wrong!')}";
    __['There is no more data to show'] = "{__('There is no more data to show')}";
    __['This website uses cookies to ensure you get the best experience on our website'] = "{__('This website uses cookies to ensure you get the best experience on our website')}";
    __['Got It!'] = "{__('Got It!')}";
    __['Learn More'] = "{__('Learn More')}";
    __['No result found'] = "{__('No result found')}";
    __['Turn on Commenting'] = "{__('Turn on Commenting')}";
    __['Turn off Commenting'] = "{__('Turn off Commenting')}";
    __['Day Mode'] = "{__('Day Mode')}";
    __['Night Mode'] = "{__('Night Mode')}";
    __['Message'] = "{__('Message')}";
    __['You haved poked'] = "{__('You haved poked')}";
    __['Touch to unmute'] = "{__('Touch to unmute')}";
    __['Press space to see next'] = "{__('Press space to see next')}";
    __['Visit link'] = "{__('Visit link')}";
    __['ago'] = "{__('ago')}";
    __['hour'] = "{__('hour')}";
    __['hours'] = "{__('hours')}";
    __['minute'] = "{__('minute')}";
    __['minutes'] = "{__('minutes')}";
    __['from now'] = "{__('from now')}";
    __['seconds'] = "{__('seconds')}";
    __['yesterday'] = "{__('yesterday')}";
    __['tomorrow'] = "{__('tomorrow')}";
    __['days'] = "{__('days')}";
    __['Nothing selected'] = "{__('Nothing selected')}";
    __['Seen by'] = "{__('Seen by')}";
    __['Ringing'] = "{__('Ringing')}";
    __['is Offline'] = "{__('is Offline')}";
    __['is Busy'] = "{__('is Busy')}";
    __['No Answer'] = "{__('No Answer')}";
    __['You can not connect to this user'] = "{__('You can not connect to this user')}";
    __['You have an active call already'] = "{__('You have an active call already')}";
    __['The recipient declined the call'] = "{__('The recipient declined the call')}";
    __['Connection has been lost'] = "{__('Connection has been lost')}";
    __['You must fill in all of the fields'] = "{__('You must fill in all of the fields')}";
    __['Hide from Timeline'] = "{__('Hide from Timeline')}";
    __['Allow on Timeline'] = "{__('Allow on Timeline')}";
    __['Are you sure you want to hide this post from your profile timeline? It may still appear in other places like newsfeed and search results'] = "{__('Are you sure you want to hide this post from your profile timeline? It may still appear in other places like newsfeed and search results')}";
    __['Select All'] = "{__('Select All')}";
    __['Deselect All'] = "{__('Deselect All')}";
    __['Total'] = "{__('Total')}";
    __['Stop Campaign'] = "{__('Stop Campaign')}";
    __['Resume Campaign'] = "{__('Resume Campaign')}";
    __['Sorry, WebRTC is not available in your browser'] = "{__('Sorry, WebRTC is not available in your browser')}";
    __['Not able to connect, Try again later!'] = "{__('Not able to connect, Try again later!')}";
    __['You are ready to Go Live now'] = "{__('You are ready to Go Live now')}";
    __['Getting permissions failed'] = "{__('Getting permissions failed')}";
    __['Going Live'] = "{__('Going Live')}";
    __['You are live now'] = "{__('You are live now')}";
    __['You are offline now'] = "{__('You are offline now')}";
    __['Going live failed'] = "{__('Going live failed')}";
    __['Online'] = "{__('Online')}";
    __['Offline'] = "{__('Offline')}";
    __['Switching stream sources'] = "{__('Switching stream sources')}";
    __['Successfully switched to new device'] = "{__('Successfully switched to new device')}";
    __['Unable to determine device type'] = "{__('Unable to determine device type')}";
    __['Failed to switch to new device'] = "{__('Failed to switch to new device')}";
    __['Ending live error'] = "{__('Ending live error')}";
    __['Joining live stream failed'] = "{__('Joining live stream failed')}";
    __['Video Muted'] = "{__('Video Muted')}";
    __['Audio Muted'] = "{__('Audio Muted')}";
    __['Live Ended'] = "{__('Live Ended')}";
    __['Try Package'] = "{__('Try Package')}";
    __['Are you sure you want to subscribe to this free package?'] = "{__('Are you sure you want to subscribe to this free package?')}";
    __['Sneak Peak'] = "{__('Sneak Peak')}";
    __['Are you sure you want to subscribe to this free plan?'] = "{__('Are you sure you want to subscribe to this free plan?')}";
    __['Processing'] = "{__('Processing')}";
    __['Your video is being processed, We will let you know when it is ready!'] = "{__('Your video is being processed, We will let you know when it is ready!')}";
    __['Under Review'] = "{__('Under Review')}";
    __['Your post is under review now, We will let you know when it is ready!'] = "{__('Your post is under review now, We will let you know when it is ready!')}";
    __['Payment Confirmation'] = "{__('Payment Confirmation')}";
    __['This message will cost you'] = "{__('This message will cost you')}";
    __['This call will cost you'] = "{__('This call will cost you')}";
    __['Login As'] = "{__('Login As')}";
    __['Are you sure you want to login as this user?'] = "{__('Are you sure you want to login as this user?')}";
    __['Are you sure you want to switch back to your account?'] = "{__('Are you sure you want to switch back to your account?')}";
    /* i18n for DataTables */
    __['Processing...'] = "{__('Processing...')}";
    __['Search:'] = "{__('Search:')}";
    __['Show _MENU_ entries'] = "{__('Show _MENU_ entries')}";
    __['Showing _START_ to _END_ of _TOTAL_ entries'] = "{__('Showing _START_ to _END_ of _TOTAL_ entries')}";
    __['Showing 0 to 0 of 0 entries'] = "{__('Showing 0 to 0 of 0 entries')}";
    __['(filtered from _MAX_ total entries)'] = "{__('(filtered from _MAX_ total entries)')}";
    __['Loading...'] = "{__('Loading...')}";
    __['No matching records found'] = "{__('No matching records found')}";
    __['No data available in table'] = "{__('No data available in table')}";
    __['First'] = "{__('First')}";
    __['Previous'] = "{__('Previous')}";
    __['Next'] = "{__('Next')}";
    __['Last'] = "{__('Last')}";
    __[': activate to sort column ascending'] = "{__(': activate to sort column ascending')}";
    __[': activate to sort column descending'] = "{__(': activate to sort column descending')}";
    /* i18n for OneSignal */
    __['Subscribe to notifications'] = "{__('Subscribe to notifications')}";
    __['You are subscribed to notifications'] = "{__('You are subscribed to notifications')}";
    __['You have blocked notifications'] = "{__('You have blocked notifications')}";
    __['Click to subscribe to notifications'] = "{__('Click to subscribe to notifications')}";
    __['Thanks for subscribing!'] = "{__('Thanks for subscribing!')}";
    __['You are subscribed to notifications'] = "{__('You are subscribed to notifications')}";
    __['You will not receive notifications again'] = "{__('You will not receive notifications again')}";
    __['Manage Site Notifications'] = "{__('Manage Site Notifications')}";
    __['SUBSCRIBE'] = "{__('SUBSCRIBE')}";
    __['UNSUBSCRIBE'] = "{__('UNSUBSCRIBE')}";
    __['Unblock Notifications'] = "{__('Unblock Notifications')}";
    __['Follow these instructions to allow notifications:'] = "{__('Follow these instructions to allow notifications:')}";
    /* i18n for Video.js */
    __['Play'] = "{__('Play')}";
    __['Pause'] = "{__('Pause')}";
    __['Mute'] = "{__('Mute')}";
    __['Unmute'] = "{__('Unmute')}";
    __['Current Time'] = "{__('Current Time')}";
    __['Duration'] = "{__('Duration')}";
    __['Remaining Time'] = "{__('Remaining Time')}";
    __['Fullscreen'] = "{__('Fullscreen')}";
    __['Picture-in-Picture'] = "{__('Picture-in-Picture')}";
    /* i18n for Highcharts.js */
    __['View Fullscreen'] = "{__('View Fullscreen')}";
    __['Print Chart'] = "{__('Print Chart')}";
    __['Download PNG'] = "{__('Download PNG')}";
    __['Download JPEG'] = "{__('Download JPEG')}";
    __['Download PDF'] = "{__('Download PDF')}";
    __['Download SVG vector image'] = "{__('Download SVG vector image')}";
    __['Chart context menu'] = "{__('Chart context menu')}";
  </script>
  <!-- Initialize -->

  <!-- Dependencies Libs [jQuery|Bootstrap] -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <script src="{$system['system_url']}/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <!-- Dependencies Libs [jQuery|Bootstrap] -->

  <!-- Dependencies Plugins -->
  <script src="{$system['system_url']}/node_modules/mustache/mustache.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <script src="{$system['system_url']}/node_modules/jquery-form/dist/jquery.form.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <script src="{$system['system_url']}/node_modules/jquery-inview/jquery.inview.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <script src="{$system['system_url']}/node_modules/autosize/dist/autosize.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <script src="{$system['system_url']}/node_modules/readmore-js/readmore.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <script src="{$system['system_url']}/node_modules/moment/min/moment-with-locales.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <script src="https://unpkg.com/video.js@7.21.1/dist/video.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <link href="https://unpkg.com/@silvermine/videojs-quality-selector/dist/css/quality-selector.css" rel="stylesheet">
  <script src="https://unpkg.com/@silvermine/videojs-quality-selector/dist/js/silvermine-videojs-quality-selector.min.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  <link href="https://unpkg.com/video.js@7.21.1/dist/video-js.min.css" rel="stylesheet">
  {if $system['auto_play_videos']}
    <script src="{$system['system_url']}/node_modules/jquery-fracs/dist/jquery.fracs.min.js" {if !$user->_logged_in}defer{/if}>
      
    </script>
  {/if}

  {if $user->_logged_in}
    <!-- jQuery-UI -->
    <script>
      var _tooltip = jQuery.fn.tooltip;
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script>
      jQuery.fn.tooltip = _tooltip;
    </script>
    <script src="{$system['system_url']}/node_modules/triggeredautocomplete/jquery-ui.triggeredAutocomplete.js"></script>
    <!-- jQuery-UI -->

    <!-- Sticky Sidebar -->
    <script src="{$system['system_url']}/node_modules/theia-sticky-sidebar/dist/theia-sticky-sidebar.min.js"></script>
    <!-- Sticky Sidebar -->

    <!-- Slick Slider -->
    {if $page == "index"}
      <script src="{$system['system_url']}/node_modules/slick-carousel/slick/slick.min.js"></script>
      <link rel="stylesheet" type='text/css' href="{$system['system_url']}/node_modules/slick-carousel/slick/slick.css">
      <link rel="stylesheet" type='text/css' href="{$system['system_url']}/node_modules/slick-carousel/slick/slick-theme.css">
    {/if}
    <!-- Slick Slider -->

    <!-- Google Geocomplete -->
    {if $system['geolocation_enabled']}
      <script src="https://cdnjs.cloudflare.com/ajax/libs/geocomplete/1.7.0/jquery.geocomplete.min.js" integrity="sha512-4bp4fE4hv0i/1jLM7d+gXDaCAhnXXfGBKdHrBcpGBgnz7OlFMjUgVH4kwB85YdumZrZyryaTLnqGKlbmBatCpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
      <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={$system['geolocation_key']}"></script>
    {/if}
    <!-- Google Geocomplete -->

    <!-- Crop Profile Picture & Reposition Cover Photo -->
    {if $page == "started" || $page == "profile" || $page == "page" || $page == "group" || $page == "event"}
      <script src="{$system['system_url']}/node_modules/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
      <script src="{$system['system_url']}/node_modules/JQ-Image-Drag/script/jquery.imagedrag.min.js"></script>
      <script src="{$system['system_url']}/node_modules/rcrop/dist/rcrop.min.js"></script>
      <link rel="stylesheet" type='text/css' href="{$system['system_url']}/node_modules/rcrop/dist/rcrop.min.css">
    {/if}
    <!-- Crop Profile Picture & Reposition Cover Photo -->

    <!-- Stories -->
    {if $page == "index" && $view == ""}
      <script src="{$system['system_url']}/node_modules/zuck.js/dist/zuck.min.js"></script>
      <link rel="stylesheet" type='text/css' href="{$system['system_url']}/node_modules/zuck.js/dist/zuck.min.css">
    {/if}
    <!-- Stories -->

    <!-- Voice Notes -->
    {if $system['voice_notes_posts_enabled'] || $system['voice_notes_comments_enabled'] || $system['voice_notes_chat_enabled']}
      <script src="{$system['system_url']}/node_modules/web-audio-recorder-js/lib-minified/WebAudioRecorder.min.js"></script>
    {/if}
    <!-- Voice Notes -->

    <!-- TinyMCE -->
    {if $page == "admin" || $page == "blogs" || $page == "forums"}
      <script src="{$system['system_url']}/node_modules/tinymce/tinymce.min.js"></script>
    {/if}
    <!-- TinyMCE -->

    <!-- Stripe & 2Checkout & Razorpay & SecurionPay -->
    {if $page == "packages" || $page == "ads" || $page == "wallet" || ($page == "index" || $page == "profile" || $page == "page" || $page == "group" || $page == "post" || $page == "directory" || $page == "search" || $page == "movies")}
      {if $system['creditcard_enabled'] || $system['alipay_enabled']}
        <script src="https://js.stripe.com/v3"></script>
      {/if}
      {if $system['2checkout_enabled']}
        <script src="https://www.2checkout.com/checkout/api/2co.min.js"></script>
      {/if}
      {if $system['razorpay_enabled']}
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
      {/if}
      {if $system['securionpay_enabled']}
        <script src="https://securionpay.com/checkout.js"></script>
      {/if}
    {/if}
    <!-- Stripe & 2Checkout & Razorpay & SecurionPay -->

    <!-- Twilio -->
    {if $system['audio_call_enabled'] || $system['video_call_enabled']}
      <script src="https://sdk.twilio.com/js/video/releases/2.20.1/twilio-video.min.js"></script>
    {/if}
    <!-- Twilio -->

    <!-- Agora -->
    {if $system['live_enabled']}
      <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.18.0.js"></script>
    {/if}
    <!-- Agora -->

    <!-- Easytimer -->
    {if $system['audio_call_enabled'] || $system['video_call_enabled'] || $system['voice_notes_posts_enabled'] || $system['voice_notes_comments_enabled'] || $system['voice_notes_chat_enabled']}
      <script src="{$system['system_url']}/node_modules/easytimer.js/dist/easytimer.min.js"></script>
    {/if}
    <!-- Easytimer -->

    <!-- Datatables -->
    {if $page == "admin" || $page == "ads" || $page == "wallet" || $page == "developers"}
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.min.css" />
      <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
      <script src="https://cdn.datatables.net/2.0.2/js/dataTables.bootstrap5.min.js"></script>
    {/if}
    <!-- Datatables -->

    <!-- Tagify -->
    {if $page =="packages" || $page == "settings" || $page == "blogs"}
      <script src="{$system['system_url']}/node_modules/@yaireo/tagify/dist/jQuery.tagify.min.js"></script>
      <link rel="stylesheet" type='text/css' href="{$system['system_url']}//node_modules/@yaireo/tagify/dist/tagify.css">
    {/if}
    <!-- Tagify -->

    <!-- HTML2PDF -->
    {if $page == "market"}
      <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    {/if}
    <!-- HTML2PDF -->

    <!-- Clipboard -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js" integrity="sha512-7O5pXpc0oCRrxk8RUfDYFgn0nO1t+jLuIOQdOMRp4APB7uZ4vSjspzp5y6YDtDs4VzUSTbWzBFZ/LKJhnyFOKw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Clipboard -->

    <!-- XRegExp -->
    <script src="https://unpkg.com/xregexp/xregexp-all.js"></script>
    <!-- XRegExp -->
  {/if}
  <!-- Dependencies Plugins -->

  <!-- System [JS] -->
  <script src="{$system['system_url']}/includes/assets/js/core/core.js" {if !$user->_logged_in}defer{/if}>
    
  </script>
  {if $user->_logged_in}
    <script src="{$system['system_url']}/includes/assets/js/core/user.js"></script>
    <script src="{$system['system_url']}/includes/assets/js/core/post.js"></script>
    <script src="{$system['system_url']}/includes/assets/js/core/chat.js"></script>
    <script src="{$system['system_url']}/includes/assets/js/core/ad_code.js"></script>
    {if $system['live_enabled'] && $page == "live"}
      <script src="{$system['system_url']}/includes/assets/js/core/live.js"></script>
    {/if}
  {else}
    <script src="{$system['system_url']}/includes/assets/js/core/login.js" defer></script>
  {/if}
  <!-- System [JS] -->

  {if $page == "admin"}
    <!-- Dependencies Plugins -->
    <script src="{$system['system_url']}/node_modules/jquery-treegrid/js/jquery.treegrid.min.js"></script>
    <link rel="stylesheet" type='text/css' href="{$system['system_url']}/node_modules/jquery-treegrid/css/jquery.treegrid.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js" integrity="sha512-rQImvJlBa8MV1Tl1SXR5zD2bWfmgCEIzTieFegGg89AAt7j/NBEe50M5CqYQJnRwtkjKMmuYgHBqtD1Ubbk5ww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js" integrity="sha512-I6CdJdruzGtvDyvdO4YsiAq+pkWf2efgd1ZUSK2FnM/u2VuRASPC7GowWQrWyjxCZn6CT89s3ddGI+be0Ak9Fg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="{$system['system_url']}/node_modules/@yaireo/tagify/dist/jQuery.tagify.min.js"></script>
    <link rel="stylesheet" type='text/css' href="{$system['system_url']}//node_modules/@yaireo/tagify/dist/tagify.css">
    <!-- Dependencies Plugins [JS] -->

    <!-- System [JS] -->
    <script src="{$system['system_url']}/includes/assets/js/core/admin.js"></script>
    <!-- System [JS] -->

    <!-- Admin Charts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    {if $view == "dashboard"}
      <script>
        $(function() {
          $('#admin-chart-dashboard').highcharts({
            lang: {
              viewFullscreen: __['View Fullscreen'],
              printChart: __['Print Chart'],
              downloadPNG: __['Download PNG'],
              downloadJPEG: __['Download JPEG'],
              downloadPDF: __['Download PDF'],
              downloadSVG: __['Download SVG vector image'],
              contextButtonTitle: __['Chart context menu'],
            },
            chart: {
              type: 'column',
              backgroundColor: 'transparent',
            },
            title: {
              text: __['Monthly Average']
            },
            xAxis: {
              categories: [
                __['Jan'],
                __['Feb'],
                __['Mar'],
                __['Apr'],
                __['May'],
                __['Jun'],
                __['Jul'],
                __['Aug'],
                __['Sep'],
                __['Oct'],
                __['Nov'],
                __['Dec']
              ],
              crosshair: true
            },
            yAxis: {
              min: 0,
              title: {
                text: __['Total']
              }
            },
            tooltip: {
              headerFormat: '<span style="font-size:10px">{literal}{point.key}{/literal}</span><table>',
              pointFormat: '<tr><td style="color:{literal}{series.color}{/literal};padding:0">{literal}{series.name}{/literal}: </td>' +
              '<td style="padding:0"><b>{literal}{point.y}{/literal}</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
            },
            plotOptions: {
              column: {
                pointPadding: 0.2,
                borderWidth: 0
              }
            },
            series: [{
                name: __['Users'],
                data: [{','|implode:$chart['users']}]
              },
              {
                name: __['Pages'],
                data: [{','|implode:$chart['pages']}]
              },
              {
                name: __['Groups'],
                data: [{','|implode:$chart['groups']}]
              },
              {
                name: __['Events'],
                data: [{','|implode:$chart['events']}]
              },
              {
                name: __['Posts'],
                data: [{','|implode:$chart['posts']}]
              }
            ]
          });
        });
      </script>
    {/if}
    {if $view == "earnings" && $sub_view == ""}
      <script>
        $(function() {
          $('#payment-methods-chart').highcharts({
            lang: {
              viewFullscreen: __['View Fullscreen'],
              printChart: __['Print Chart'],
              downloadPNG: __['Download PNG'],
              downloadJPEG: __['Download JPEG'],
              downloadPDF: __['Download PDF'],
              downloadSVG: __['Download SVG vector image'],
              contextButtonTitle: __['Chart context menu'],
            },
            chart: {
              type: 'column',
              backgroundColor: 'transparent',
            },
            title: {
              text: __['PayIn Methods']
            },
            xAxis: {
              categories: [
                __['Jan'],
                __['Feb'],
                __['Mar'],
                __['Apr'],
                __['May'],
                __['Jun'],
                __['Jul'],
                __['Aug'],
                __['Sep'],
                __['Oct'],
                __['Nov'],
                __['Dec']
              ],
              crosshair: true
            },
            yAxis: {
              min: 0,
              title: {
                text: __['Total'] + ' ' + '(' + currency + ')'
              }
            },
            tooltip: {
              headerFormat: '<span style="font-size:10px">{literal}{point.key}{/literal}</span><table>',
              pointFormat: '<tr><td style="color:{literal}{series.color}{/literal};padding:0">{literal}{series.name}{/literal}: </td>' +
              '<td style="padding:0"><b>{literal}{point.y}{/literal}</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
            },
            plotOptions: {
              column: {
                pointPadding: 0.2,
                borderWidth: 0
              }
            },
            series: [
              {foreach $payment_methods as $method}
                {
                  name: '{$method|capitalize}',
                  data: [
                    {foreach $payment_methods_chart as $month => $payments}
                      {$payments[$method]},
                    {/foreach}
                  ]
                },
              {/foreach}
            ]
          });
          $('#payment-handles-chart').highcharts({
            lang: {
              viewFullscreen: __['View Fullscreen'],
              printChart: __['Print Chart'],
              downloadPNG: __['Download PNG'],
              downloadJPEG: __['Download JPEG'],
              downloadPDF: __['Download PDF'],
              downloadSVG: __['Download SVG vector image'],
              contextButtonTitle: __['Chart context menu'],
            },
            chart: {
              type: 'column',
              backgroundColor: 'transparent',
            },
            title: {
              text: __['PayIn Types']
            },
            xAxis: {
              categories: [
                __['Jan'],
                __['Feb'],
                __['Mar'],
                __['Apr'],
                __['May'],
                __['Jun'],
                __['Jul'],
                __['Aug'],
                __['Sep'],
                __['Oct'],
                __['Nov'],
                __['Dec']
              ],
              crosshair: true
            },
            yAxis: {
              min: 0,
              title: {
                text: __['Total'] + ' ' + '(' + currency + ')'
              }
            },
            tooltip: {
              headerFormat: '<span style="font-size:10px">{literal}{point.key}{/literal}</span><table>',
              pointFormat: '<tr><td style="color:{literal}{series.color}{/literal};padding:0">{literal}{series.name}{/literal}: </td>' +
              '<td style="padding:0"><b>{literal}{point.y}{/literal}</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
            },
            plotOptions: {
              column: {
                pointPadding: 0.2,
                borderWidth: 0
              }
            },
            series: [
              {foreach $payment_handles as $handle}
                {
                  name: '{$handle|capitalize}',
                  data: [
                    {foreach $payment_handles_chart as $month => $payments}
                      {$payments[$handle]},
                    {/foreach}
                  ]
                },
              {/foreach}
            ]
          });
        });
      </script>
    {/if}
    {if $view == "earnings" && $sub_view == "commissions"}
      <script>
        $(function() {
          $('#commissions-chart').highcharts({
            lang: {
              viewFullscreen: __['View Fullscreen'],
              printChart: __['Print Chart'],
              downloadPNG: __['Download PNG'],
              downloadJPEG: __['Download JPEG'],
              downloadPDF: __['Download PDF'],
              downloadSVG: __['Download SVG vector image'],
              contextButtonTitle: __['Chart context menu'],
            },
            chart: {
              type: 'column',
              backgroundColor: 'transparent',
            },
            title: {
              text: __['Commissions Types']
            },
            xAxis: {
              categories: [
                __['Jan'],
                __['Feb'],
                __['Mar'],
                __['Apr'],
                __['May'],
                __['Jun'],
                __['Jul'],
                __['Aug'],
                __['Sep'],
                __['Oct'],
                __['Nov'],
                __['Dec']
              ],
              crosshair: true
            },
            yAxis: {
              min: 0,
              title: {
                text: __['Total'] + ' ' + '(' + currency + ')'
              }
            },
            tooltip: {
              headerFormat: '<span style="font-size:10px">{literal}{point.key}{/literal}</span><table>',
              pointFormat: '<tr><td style="color:{literal}{series.color}{/literal};padding:0">{literal}{series.name}{/literal}: </td>' +
              '<td style="padding:0"><b>{literal}{point.y}{/literal}</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
            },
            plotOptions: {
              column: {
                pointPadding: 0.2,
                borderWidth: 0
              }
            },
            series: [
              {foreach $commissions_handles as $handle}
                {
                  name: '{$handle|capitalize}',
                  data: [
                    {foreach $commissions_handles_chart as $month => $commissions}
                      {$commissions[$handle]},
                    {/foreach}
                  ]
                },
              {/foreach}
            ]
          });
        });
      </script>
    {/if}
    {if $view == "earnings" && $sub_view == "packages"}
      <script>
        $(function() {
          $('#admin-chart-earnings').highcharts({
            lang: {
              viewFullscreen: __['View Fullscreen'],
              printChart: __['Print Chart'],
              downloadPNG: __['Download PNG'],
              downloadJPEG: __['Download JPEG'],
              downloadPDF: __['Download PDF'],
              downloadSVG: __['Download SVG vector image'],
              contextButtonTitle: __['Chart context menu'],
            },
            chart: {
              type: 'column',
              backgroundColor: 'transparent',
            },
            title: {
              text: __['Packages']
            },
            xAxis: {
              categories: [
                __['Jan'],
                __['Feb'],
                __['Mar'],
                __['Apr'],
                __['May'],
                __['Jun'],
                __['Jul'],
                __['Aug'],
                __['Sep'],
                __['Oct'],
                __['Nov'],
                __['Dec']
              ],
              crosshair: true
            },
            yAxis: {
              min: 0,
              title: {
                text: __['Total'] + ' ' + '(' + currency + ')'
              }
            },
            tooltip: {
              headerFormat: '<span style="font-size:10px">{literal}{point.key}{/literal}</span><table>',
              pointFormat: '<tr><td style="color:{literal}{series.color}{/literal};padding:0">{literal}{series.name}{/literal}: </td>' +
              '<td style="padding:0"><b>{literal}{point.y}{/literal}</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
            },
            plotOptions: {
              column: {
                pointPadding: 0.2,
                borderWidth: 0
              }
            },
            series: [
              {foreach $rows as $key => $value}
                {
                  name: "{$key}",
                  data: [{','|implode:$value['months_sales']}]
                },
              {/foreach}
            ]
          });

        });
      </script>
    {/if}
    <!-- Admin Charts -->

    <!-- Admin Code Editor -->
    {if $view == "design"}
      <script>
        $(function() {
          CodeMirror.fromTextArea(document.getElementById('custome_js_header'), {
            mode: "javascript",
            lineNumbers: true,
            readOnly: false
          });

          CodeMirror.fromTextArea(document.getElementById('custome_js_footer'), {
            mode: "javascript",
            lineNumbers: true,
            readOnly: false
          });

          CodeMirror.fromTextArea(document.getElementById('custom-css'), {
            mode: "css",
            lineNumbers: true,
            readOnly: false
          });
        });
      </script>
    {/if}
    {if $view == "settings" && $sub_view == "uploads"}
      <script>
        $(function() {
          $('.nav-tabs a').on('shown.bs.tab', function() {
            cm.refresh();
          });
          cm = CodeMirror.fromTextArea(document.getElementById('google_cloud_file'), {
            mode: "javascript",
            lineNumbers: true,
            readOnly: false
          });
        });
      </script>
    {/if}
    <!-- Admin Code Editor -->
  {/if}

  <!-- Cookies Policy -->
  {if $system['cookie_consent_enabled']}
    <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js" {if !$user->_logged_in}defer{/if}>
      
    </script>
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
    <script>
      window.addEventListener("load", function() {
        window.cookieconsent.initialise({
          "palette": {
            "popup": {
              "background": "#1e2321",
              "text": "#fff"
            },
            "button": {
              "background": "#5e72e4"
            }
          },
          "theme": "edgeless",
          "position": {if $system['language']['dir'] == 'LTR'}"bottom-left"{else}"bottom-right"{/if},
          "content": {
            "message": __['This website uses cookies to ensure you get the best experience on our website'],
            "dismiss": __['Got It!'],
            "link": __['Learn More'],
            "href": site_path + "/static/privacy"
          }
        })
      });
    </script>
  {/if}
  <!-- Cookies Policy -->

  <!-- OneSignal Notifications -->
  {if $user->_logged_in && $system['onesignal_notification_enabled']}
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
      var onesignal_app_id = "{$system['onesignal_app_id']}";
      var onesignal_user_id = "{$user->_data['onesignal_user_id']}";
      var onesignal_push_id = "";
      var OneSignal = window.OneSignal || [];
      OneSignal.push(function() {

        OneSignal.init({
          appId: onesignal_app_id,
          autoResubscribe: false,
          notifyButton: {
            enable: true,
            /* Required to use the Subscription Bell */
            size: 'medium',
            /* One of 'small', 'medium', or 'large' */
            theme: 'default',
            /* One of 'default' (red-white) or 'inverse" (white-red) */
            position: (theme_dir_rtl) ? 'bottom-right' : 'bottom-left',
            /* Either 'bottom-left' or 'bottom-right' */
            offset: {
              bottom: '20px',
              left: '20px',
              /* Only applied if bottom-left */
              right: '20px' /* Only applied if bottom-right */
            },
            prenotify: true,
            /* Show an icon with 1 unread message for first-time site visitors */
            showCredit: false,
            /* Hide the OneSignal logo */
            text: {
              'tip.state.unsubscribed': __['Subscribe to notifications'],
              'tip.state.subscribed': __['You are subscribed to notifications'],
              'tip.state.blocked': __['You have blocked notifications'],
              'message.prenotify': __['Click to subscribe to notifications'],
              'message.action.subscribed': __['Thanks for subscribing!'],
              'message.action.resubscribed': __['You are subscribed to notifications'],
              'message.action.unsubscribed': __['You will not receive notifications again'],
              'dialog.main.title': __['Manage Site Notifications'],
              'dialog.main.button.subscribe': __['SUBSCRIBE'],
              'dialog.main.button.unsubscribe': __['UNSUBSCRIBE'],
              'dialog.blocked.title': __['Unblock Notifications'],
              'dialog.blocked.message': __['Follow these instructions to allow notifications:']
            },
            colors: {
              'circle.background': 'rgb(84,110,123)',
              'circle.foreground': 'white',
              'badge.background': 'rgb(84,110,123)',
              'badge.foreground': 'white',
              'badge.bordercolor': 'white',
              'pulse.color': 'white',
              'dialog.button.background.hovering': 'rgb(77, 101, 113)',
              'dialog.button.background.active': 'rgb(70, 92, 103)',
              'dialog.button.background': 'rgb(84,110,123)',
              'dialog.button.foreground': 'white'
            },
          },
          allowLocalhostAsSecureOrigin: true,
        });

        OneSignal.getUserId(function(userId) {
          onesignal_push_id = userId;
          if (userId != onesignal_user_id) {
            $.post(api['users/notifications'], { handle: 'update', id: onesignal_push_id });
          }
        });

        OneSignal.on('subscriptionChange', function(isSubscribed) {
          if (isSubscribed == false) {
            $.post(api['users/notifications'], { handle: 'delete' });
          } else {
            $.post(api['users/notifications'], { handle: 'update', id: onesignal_push_id });
          }
        });
      });
    </script>
  {/if}
  <!-- OneSignal Notifications -->

{/strip}