<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="jobs" class="main-icon mr10" width="24px" height="24px"}
    {__("Create New Job")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="posts/job.php?do=publish">
  <div class="modal-body">
    <div class="row">
      <!-- job title -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Title")}</label>
        <input name="title" type="text" class="form-control">
      </div>
      <!-- job title -->
      <!-- location -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Location")}</label>
        <input name="location" type="text" class="form-control">
      </div>
      <!-- location -->
    </div>
    <!-- salary range -->
    <div class="form-group">
      <label class="form-label">{__("Salary Range")}</label>
      <div class="row">
        <div class="col-md-6">
          <div class="input-group">
            {if $system['system_currency_dir'] == "left"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
            <input name="salary_minimum" type="text" class="form-control" placeholder="{__("Minimum")}">
            {if $system['system_currency_dir'] == "right"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
          </div>
        </div>
        <div class="col-md-6">
          <div class="input-group">
            {if $system['system_currency_dir'] == "left"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
            <input name="salary_maximum" type="text" class="form-control" placeholder="{__("Maximum")}">
            {if $system['system_currency_dir'] == "right"}
              <span class="input-group-text">{$system['system_currency_symbol']}</span>
            {/if}
          </div>
        </div>
      </div>
      <div class="row mt15">
        <div class="col-md-12">
          <select class="form-select" name="pay_salary_per">
            <option value="per_hour">{__("Per Hour")}</option>
            <option value="per_day">{__("Per Day")}</option>
            <option value="per_week">{__("Per Week")}</option>
            <option value="per_month">{__("Per Month")}</option>
            <option value="per_year">{__("Per Year")}</option>
          </select>
        </div>
      </div>
    </div>
    <!-- salary range -->
    <div class="row">
      <!-- job type -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Type")}</label>
        <select class="form-select" name="type">
          <option value="full_time">{__("Full Time")}</option>
          <option value="part_time">{__("Part Time")}</option>
          <option value="internship">{__("Internship")}</option>
          <option value="volunteer">{__("Volunteer")}</option>
          <option value="contract">{__("Contract")}</option>
        </select>
      </div>
      <!-- job type -->
      <!-- category -->
      <div class="form-group col-md-6">
        <label class="form-label">{__("Category")}</label>
        <select class="form-select" name="category">
          {foreach $jobs_categories as $category}
            {include file='__categories.recursive_options.tpl'}
          {/foreach}
        </select>
      </div>
      <!-- category -->
    </div>
    <!-- description -->
    <div class="form-group">
      <label class="form-label">{__("Description")}</label>
      <textarea name="description" rows="5" dir="auto" class="form-control"></textarea>
      <div class="form-text">
        {__("Describe the responsibilities and preferred skills for this job")}
      </div>
    </div>
    <!-- description -->
    <!-- custom fields -->
    {if $custom_fields}
      {include file='__custom_fields.tpl' _custom_fields=$custom_fields _registration=true}
    {/if}
    <!-- custom fields -->
    <!-- questions -->
    <div class="form-group">
      <label class="form-label">{__("Questions")}</label>
      <div>
        <!-- add question -->
        <div class="add-job-question js_add-job-question"><i class="fas fa-plus-circle mr5"></i>{__("Add Question")}</div>
        <!-- add question -->
        <!-- question #1 -->
        <div class="job-question x-hidden" data-id="1">
          <label class="form-label mb10">{__("Question")} #1</label>
          <select class="form-select js_question-type" name="question_1_type">
            <option value="free_text">{__("Free Text Question")}</option>
            <option value="yes_no_question">{__("Yes/No Question")}</option>
            <option value="multiple_choice">{__("Multiple Choice Question")}</option>
          </select>
          <div class="form-text">
            {__("Select the type of your question")}
          </div>
          <input name="question_1_title" type="text" class="form-control mt10">
          <div class="form-text">
            {__("Ask your question")}
          </div>
          <div class="x-hidden js_question-choices">
            <textarea name="question_1_choices" rows="3" dir="auto" class="form-control mt10"></textarea>
            <div class="form-text">
              {__("One option per line")}
            </div>
          </div>
        </div>
        <!-- question #1 -->
        <!-- question #2 -->
        <div class="job-question x-hidden" data-id="2">
          <label class="form-label mb10">{__("Question")} #2</label>
          <select class="form-select js_question-type" name="question_2_type">
            <option value="free_text">{__("Free Text Question")}</option>
            <option value="yes_no_question">{__("Yes/No Question")}</option>
            <option value="multiple_choice">{__("Multiple Choice Question")}</option>
          </select>
          <div class="form-text">
            {__("Select the type of your question")}
          </div>
          <input name="question_2_title" type="text" class="form-control mt10">
          <div class="form-text">
            {__("Ask your question")}
          </div>
          <div class="x-hidden js_question-choices">
            <textarea name="question_2_choices" rows="3" dir="auto" class="form-control mt10"></textarea>
            <div class="form-text">
              {__("One option per line")}
            </div>
          </div>
        </div>
        <!-- question #2 -->
        <!-- question #3 -->
        <div class="job-question x-hidden" data-id="3">
          <label class="form-label mb10">{__("Question")} #3</label>
          <select class="form-select js_question-type" name="question_3_type">
            <option value="free_text">{__("Free Text Question")}</option>
            <option value="yes_no_question">{__("Yes/No Question")}</option>
            <option value="multiple_choice">{__("Multiple Choice Question")}</option>
          </select>
          <div class="form-text">
            {__("Select the type of your question")}
          </div>
          <input name="question_3_title" type="text" class="form-control mt10">
          <div class="form-text">
            {__("Ask your question")}
          </div>
          <div class="x-hidden js_question-choices">
            <textarea name="question_3_choices" rows="3" dir="auto" class="form-control mt10"></textarea>
            <div class="form-text">
              {__("One option per line")}
            </div>
          </div>
        </div>
        <!-- question #3 -->
      </div>
    </div>
    <!-- questions -->
    <!-- cover image -->
    <div class="form-group">
      <label class="form-label">{__("Cover Image")}</label>
      <div class="x-image">
        <div class="x-image-loader">
          <div class="progress x-progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image"></i>
        <input type="hidden" class="js_x-image-input" name="cover_image" value="">
      </div>
    </div>
    <!-- cover image -->
    <!-- error -->
    <div class="alert alert-danger mt15 mb0 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    {if $share_to == "page"}
      <input type="hidden" name="handle" value="page">
      <input type="hidden" name="id" value="{$share_to_id}">
    {elseif $share_to == "group"}
      <input type="hidden" name="handle" value="group">
      <input type="hidden" name="id" value="{$share_to_id}">
    {elseif $share_to == "event"}
      <input type="hidden" name="handle" value="event">
      <input type="hidden" name="id" value="{$share_to_id}">
    {/if}
    <button type="submit" class="btn btn-primary">{__("Publish")}</button>
  </div>
</form>

<script>
  $(function() {
    /* handle job questions */
    $('.js_add-job-question').on('click', function() {
      if ($('.job-question[data-id="1"]').is(":hidden")) {
        $('.job-question[data-id="1"]').show();
        return;
      }
      if ($('.job-question[data-id="2"]').is(":hidden")) {
        $('.job-question[data-id="2"]').show();
        return;
      }
      if ($('.job-question[data-id="3"]').is(":hidden")) {
        $('.job-question[data-id="3"]').show();
        $(this).hide();
        return;
      }
    });
    /* handle job questions dependencies */
    $('.js_question-type').on('change', function() {
      if ($(this).val() == "multiple_choice") {
        $(this).parents('.job-question').find(".js_question-choices").show();
      } else {
        $(this).parents('.job-question').find(".js_question-choices").hide();
      }
    });
  });
</script>