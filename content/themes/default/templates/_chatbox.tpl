<div class="card panel-messages" data-cid="{$_node['chatbox_conversation']['conversation_id']}">
  <div class="card-header">
    {include file='__svg_icons.tpl' icon="chat" class="main-icon mr10" width="24px" height="24px"}
    {__("Chatbox")}
  </div>
  <div class="card-body">
    {if ($_node_type == "group" && $_node['i_joined'] == "approved") || ($_node_type == "event" && ($event['i_joined']['is_going'] || $event['i_joined']['is_interested']))}
      <div class="chat-conversations js_scroller" data-slimScroll-height="420px" data-slimScroll-start="bottom">
        {include file='ajax.chat.conversation.messages.tpl' conversation=$_node['chatbox_conversation']}
      </div>
      <div class="chat-typing">
        <i class="far fa-comment-dots mr5"></i><span class="loading-dots"><span class="js_chat-typing-users"></span> {__("Typing")}</span>
      </div>
      <div class="chat-voice-notes">
        <div class="voice-recording-wrapper" data-handle="chat">
          <!-- processing message -->
          <div class="x-hidden js_voice-processing-message">
            {include file='__svg_icons.tpl' icon="upload" class="main-icon mr5" width="16px" height="16px"}
            {__("Processing")}<span class="loading-dots"></span>
          </div>
          <!-- processing message -->

          <!-- success message -->
          <div class="x-hidden js_voice-success-message">
            {include file='__svg_icons.tpl' icon="checkmark" class="main-icon mr5" width="16px" height="16px"}
            {__("Voice note recorded successfully")}
            <div class="float-end">
              <button type="button" class="btn-close js_voice-remove"></button>
            </div>
          </div>
          <!-- success message -->

          <!-- start recording -->
          <div class="btn-voice-start js_voice-start">
            <i class="fas fa-microphone mr5"></i>{__("Record")}
          </div>
          <!-- start recording -->

          <!-- stop recording -->
          <div class="btn-voice-stop js_voice-stop" style="display: none">
            <i class="far fa-stop-circle mr5"></i>{__("Recording")} <span class="js_voice-timer">00:00</span>
          </div>
          <!-- stop recording -->
        </div>
      </div>
      <div class="chat-attachments attachments clearfix x-hidden">
        <ul>
          <li class="loading">
            <div class="progress x-progress">
              <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </li>
        </ul>
      </div>
      <div class="x-form chat-form">
        <div class="chat-form-message">
          <textarea class="js_autosize js_post-message" dir="auto" rows="1" placeholder='{__("Write a message")}'></textarea>
        </div>
        <ul class="x-form-tools clearfix">
          {if $system['chat_photos_enabled']}
            <li class="x-form-tools-attach">
              <i class="far fa-image fa-lg fa-fw js_x-uploader" data-handle="chat"></i>
            </li>
          {/if}
          {if $system['voice_notes_chat_enabled']}
            <li class="x-form-tools-voice js_chat-voice-notes-toggle">
              <i class="fas fa-microphone fa-lg fa-fw"></i>
            </li>
          {/if}
          <li class="x-form-tools-emoji js_emoji-menu-toggle">
            <i class="far fa-smile-wink fa-lg fa-fw"></i>
          </li>
          <li class="x-form-tools-post js_post-message">
            <i class="far fa-paper-plane fa-lg fa-fw"></i>
          </li>
        </ul>
      </div>
    </div>
  {else}
    <div class="text-center text-muted" style="padding-top: 60px; min-height: 510px;">
      {include file='__svg_icons.tpl' icon="empty" class="mb20" width="96px" height="96px"}
      <p class="mt10 mb0">{__("Join the")} {__($_node_type)} {__("to join the chatbox")}</p>
    </div>
  {/if}
</div>