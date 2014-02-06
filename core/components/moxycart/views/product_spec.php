<tr class="li_product_spec" id="tr_spec_<?php print $data['spec_id']; ?>">
    <td>
        <p class="spec_name"><?php print $data['name'] ;?> (<?php print $data['identifier']; ?>)</p>        
    </td>
    <td>
        <input type="text" name="specs[<?php print $data['spec_id']; ?>]" value="<?php print $data['value']; ?>" />
    </td>
    <td>
        <p class="spec_description"><?php print $data['description'] ;?></p>
        <span class="btn" style="float:right;" onclick="javascript:remove_spec(<?php print $data['spec_id']; ?>)">X</span>
    </td>
</tr>