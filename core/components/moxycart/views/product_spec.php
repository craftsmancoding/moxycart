<tr class="li_product_spec">
    <td>
        <p class="spec_name"><?php print $data['spec'] ;?></p>        
    </td>
    <td>
        <input type="text" name="specs[<?php print $data['spec_id']; ?>]" value="<?php print $data['value']; ?>" />
    </td>
    <td>
        <p class="spec_description"><?php print $data['description'] ;?></p>
    </td>
</tr>