{if $_registration}
  {foreach $_custom_fields as $custom_field}
    <label class="form-label">{__($custom_field['label'])} {if $custom_field['mandatory'] && !$_search}*{/if}</label>
    {if $custom_field['type'] == "textbox"}
      <input class="form-control" name="fld_{$custom_field['field_id']}" type="text" {if !$_search}placeholder="{__($custom_field['label'])}" {/if} {if $custom_field['mandatory'] && !$_search}required{/if}>
    {elseif $custom_field['type'] == "textarea"}
      <textarea class="form-control" name="fld_{$custom_field['field_id']}" {if !$_search}placeholder="{__($custom_field['label'])}" {/if} {if $custom_field['mandatory'] && !$_search}required{/if}></textarea>
    {elseif $custom_field['type'] == "selectbox"}
      <select class="form-select" name="fld_{$custom_field['field_id']}" {if $custom_field['mandatory'] && !$_search}required{/if}>
        {if $_search}
          <option selected value="any">{__("Any")}</option>
        {else}
          <option selected value="none">{__("Select")} {__($custom_field['label'])}</option>
        {/if}
        {foreach $custom_field['options'] as $id => $value}
          <option value="{$id}">{__($value)}</option>
        {/foreach}
      </select>
    {elseif $custom_field['type'] == "multipleselectbox"}
      <select class="form-select" name="fld_{$custom_field['field_id']}[]" multiple {if $custom_field['mandatory'] && !$_search}required{/if}>
        {foreach $custom_field['options'] as $id => $value}
          <option value="{$id}">{__($value)}</option>
        {/foreach}
      </select>
    {/if}
    {if $custom_field['description'] && !$_search}
      <div class="form-text">
        {__($custom_field['description'])}
      </div>
    {/if}
  {/foreach}
{else}
  {foreach $_custom_fields as $custom_field}
    <div class="form-group {if $_inline}row{/if}">
      <label class="form-label {if $_inline}col-md-3{/if}">{__($custom_field['label'])} {if $custom_field['mandatory']}*{/if}</label>
      {if $_inline}<div class="col-md-9">{/if}
        {if $custom_field['type'] == "textbox"}
          <input class="form-control" type="text" name="fld_{$custom_field['field_id']}" value="{$custom_field['value']}" {if $custom_field['mandatory']}required{/if}>
        {elseif $custom_field['type'] == "textarea"}
          <textarea class="form-control" name="fld_{$custom_field['field_id']}" {if $custom_field['mandatory']}required{/if}>{$custom_field['value']}</textarea>
        {elseif $custom_field['type'] == "selectbox"}
          <select class="form-select" name="fld_{$custom_field['field_id']}" {if $custom_field['mandatory']}required{/if}>
            <option {if $custom_field['value'] == ""}selected{/if} value="none">{__("Select")} {__($custom_field['label'])}</option>
            {foreach $custom_field['options'] as $id => $value}
              <option {if $custom_field['value'] == $value}selected{/if} value="{$id}">{__($value)}</option>
            {/foreach}
          </select>
        {elseif $custom_field['type'] == "multipleselectbox"}
          <select class="form-select" name="fld_{$custom_field['field_id']}[]" multiple {if $custom_field['mandatory']}required{/if}>
            {foreach $custom_field['options'] as $id => $value}
              <option {if isset($custom_field['value']) && in_array($id, $custom_field['value'])}selected{/if} value="{$id}">{__($value)}</option>
            {/foreach}
          </select>
        {/if}
        {if $custom_field['description']}
          <div class="form-text">
            {__($custom_field['description'])}
          </div>
        {/if}
        {if $_inline}
      </div>{/if}
    </div>
  {/foreach}
{/if}