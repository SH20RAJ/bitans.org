<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="jobs" class="main-icon mr10" width="24px" height="24px"}
    {__("Job Apply")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="posts/job.php?do=apply&post_id={$post['post_id']}">
  <div class="modal-body">
    <div class="text-xlg mb10">{__("Your Information")}</div>
    <div class="row">
      <!-- name -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Name")}</label>
        <input name="name" type="text" class="form-control" value="{$user->_data['name']}">
      </div>
      <!-- name -->
      <!-- location -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Location")}</label>
        <input name="location" type="text" class="form-control">
      </div>
      <!-- location -->
    </div>
    <div class="row">
      <!-- phone -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Phone")}</label>
        <input name="phone" type="text" class="form-control" value="{$user->_data['user_phone']}">
      </div>
      <!-- phone -->
      <!-- email -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Email")}</label>
        <input name="email" type="text" class="form-control" value="{$user->_data['user_email']}">
      </div>
      <!-- email -->
    </div>
    <div class="divider mtb10"></div>
    <div class="mb10">
      <span class="text-xlg">{__("Your Experience")}</span>
      <small class="badge bg-light text-dark mr5">{__("Optional")}</small>
    </div>
    <div class="row">
      <!-- work place -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Where did you work?")}</label>
        <input name="work_place" type="text" class="form-control">
      </div>
      <!-- work place -->
      <!-- work position -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Position")}</label>
        <input name="work_position" type="text" class="form-control">
      </div>
      <!-- work position -->
    </div>
    <!-- work description -->
    <div class="form-group">
      <label class="form-label">{__("Description")}</label>
      <textarea name="work_description" rows="5" dir="auto" class="form-control"></textarea>
    </div>
    <!-- work description -->
    <!-- work duration -->
    <div class="row">
      <div class="col-md-6 form-group">
        <label class="form-label">{__("From")}</label>
        <select class="form-select" name="work_from">
          {$i=2022}
          {while $i >= 1970}
            <option value="{$i}">{$i}</option>
            {$i--}
          {/while}
        </select>
      </div>
      <div class="col-md-6 form-group">
        <label class="form-label">{__("To")}</label>
        <select class="form-select" name="work_to">
          {$i=2022}
          {while $i >= 1970}
            <option value="{$i}">{$i}</option>
            {$i--}
          {/while}
        </select>
        <div class="form-check mt10">
          <input type="checkbox" class="form-check-input" name="work_now" id="work_now">
          <label class="form-check-label" for="work_now">
            {__("I currently work here")}
          </label>
        </div>
      </div>
    </div>
    <!-- work duration -->
    <!-- questions -->
    {if $post['job']['question_1_title'] || $post['job']['question_2_title'] || $post['job']['question_3_title']}
      <div class="divider mtb10"></div>
      <div class="mb10">
        <span class="text-xlg">{__("Job Questions")}</span>
        <small class="badge bg-light text-dark mr5">{__("Required")}</small>
      </div>
      <div class="form-group">
        <div>
          <!-- question #1 -->
          {if $post['job']['question_1_title']}
            <div class="job-question">
              <label class="form-label mb5">{$post['job']['question_1_title']}</label>
              {if $post['job']['question_1_type'] == "free_text"}
                <textarea name="question_1_answer" rows="3" dir="auto" class="form-control"></textarea>
              {elseif $post['job']['question_1_type'] == "yes_no_question"}
                <div>
                  <div class="form-check">
                    <input type="radio" name="question_1_answer" id="question_1_yes" value="yes" class="form-check-input">
                    <label class="form-check-label" for="question_1_yes">{__("Yes")}</label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="question_1_answer" id="question_1_no" value="no" class="form-check-input">
                    <label class="form-check-label" for="question_1_no">{__("No")}</label>
                  </div>
                </div>
              {else}
                <div>
                  <select class="form-select" name="question_1_answer">
                    {foreach $post['job']['question_1_options'] as $id => $value}
                      <option value="{$id}">{$value}</option>
                    {/foreach}
                  </select>
                </div>
              {/if}
            </div>
          {/if}
          <!-- question #1 -->
          <!-- question #2 -->
          {if $post['job']['question_2_title']}
            <div class="job-question">
              <label class="form-label mb5">{$post['job']['question_2_title']}</label>
              {if $post['job']['question_2_type'] == "free_text"}
                <textarea name="question_2_answer" rows="3" dir="auto" class="form-control"></textarea>
              {elseif $post['job']['question_2_type'] == "yes_no_question"}
                <div>
                  <div class="form-check">
                    <input type="radio" name="question_2_answer" id="question_2_yes" value="yes" class="form-check-input">
                    <label class="form-check-label" for="question_2_yes">{__("Yes")}</label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="question_2_answer" id="question_2_no" value="no" class="form-check-input">
                    <label class="form-check-label" for="question_2_no">{__("No")}</label>
                  </div>
                </div>
              {else}
                <div>
                  <select class="form-select" name="question_2_answer">
                    {foreach $post['job']['question_2_options'] as $id => $value}
                      <option value="{$id}">{$value}</option>
                    {/foreach}
                  </select>
                </div>
              {/if}
            </div>
          {/if}
          <!-- question #2 -->
          <!-- question #3 -->
          {if $post['job']['question_3_title']}
            <div class="job-question">
              <label class="form-label mb5">{$post['job']['question_3_title']}</label>
              {if $post['job']['question_3_type'] == "free_text"}
                <textarea name="question_3_answer" rows="3" dir="auto" class="form-control"></textarea>
              {elseif $post['job']['question_3_type'] == "yes_no_question"}
                <div>
                  <div class="form-check">
                    <input type="radio" name="question_3_answer" id="question_3_yes" value="yes" class="form-check-input">
                    <label class="form-check-label" for="question_3_yes">{__("Yes")}</label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="question_3_answer" id="question_3_no" value="no" class="form-check-input">
                    <label class="form-check-label" for="question_3_no">{__("No")}</label>
                  </div>
                </div>
              {else}
                <div>
                  <select class="form-select" name="question_3_answer">
                    {foreach $post['job']['question_3_options'] as $id => $value}
                      <option value="{$id}">{$value}</option>
                    {/foreach}
                  </select>
                </div>
              {/if}
            </div>
          {/if}
          <!-- question #3 -->
        </div>
      </div>
    {/if}
    <!-- questions -->
    <!-- CV attachment -->
    <div class="divider mtb10"></div>
    <div class="mb10">
      <span class="text-xlg">{__("CV Attachment")}</span>
      <small class="badge bg-light text-dark mr5">{__("Optional")}</small>
    </div>
    <div class="form-group">
      <label class="form-label">{__("Upload your CV")}</label>
      <div class="x-image">
        <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'></button>
        <div class="x-image-loader">
          <div class="progress x-progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <i class="fa fa-paperclip fa-lg js_x-uploader" data-handle="x-file" data-type="file"></i>
        <input type="hidden" class="js_x-image-input" name="cv" value="">
      </div>
      <div class="form-text">
        {__("Allowed file types")}: {$system['file_extensions']}
      </div>
    </div>
    <!-- CV attachment -->


    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">{__("Apply")}</button>
  </div>
</form>