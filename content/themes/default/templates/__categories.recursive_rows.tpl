<tr class="treegrid-{$row['category_id']} {if $row['category_parent_id'] != '0'}treegrid-parent-{$row['category_parent_id']}{/if}">
  <td>
    {$row['category_name']}
  </td>
  <td>
    {$row['category_description']|truncate:50}
  </td>
  <td>
    <span class="badge rounded-pill badge-lg bg-info">{$row['category_order']}</span>
  </td>
  <td>
    <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/{$_url}/edit_{if $_edit_slug}{$_edit_slug}_{/if}category/{$row['category_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
      <i class="fa fa-pencil-alt"></i>
    </a>
    <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="{$_handle}" data-id="{$row['category_id']}">
      <i class="fa fa-trash-alt"></i>
    </button>
  </td>
</tr>
{if $row['sub_categories']}
  {foreach $row['sub_categories'] as $_row}
    {include file='__categories.recursive_rows.tpl' row = $_row _url=$_url _handle=$_handle}
  {/foreach}
{/if}