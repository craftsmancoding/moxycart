<?php
/*
Used when defining related products and their types
*/
?>
 <tr id="product_relation_<?php print $data['product_id']; ?>">
    <td><?php print $data['name']; ?> <?php print !empty($p['sku']) ? '('.$p['sku'].')' : ''; ?></td>
    <td> 
    	<select name="relations[ <?php print $data['related_id']; ?> ]">
        <option value="related"<?php print $data['related.is_selected']; ?>>Related</option>
        <option value="bundle-1:order"<?php print $data['bundle-1:order.is_selected']; ?>>Bundle (1 per Order)</option>
        <option value="bundle-1:1"<?php print $data['bundle-1:1.is_selected']; ?>>Bundle (Match Qty)</option>
    	</select>
    </td>
    <td><span class="btn" onclick="javascript:remove_relation(<?php print $data['product_id']; ?>);" style="height:10px;">Remove</span></td>
  </tr>