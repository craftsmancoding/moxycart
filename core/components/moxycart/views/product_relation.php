<?php
/*
Used when defining related products and their types
*/
?>
<li id="product_relation_<?php print $data['product_id']; ?>">
    <?php print $data['name']; ?> (<?php print $data['sku']; ?>)
    <select name="relations[ <?php print $data['related_id']; ?> ]">
        <option value="related"<?php print $data['related.is_selected']; ?>>Related</option>
        <option value="bundle"<?php print $data['bundle.is_selected']; ?>>Bundle (1 per Order)</option>
        <option value="bundle_match_qty"<?php print $data['bundle_match_qty.is_selected']; ?>>Bundle (Match Qty)</option>
    </select>
    <span class="btn" onclick="javascript:remove_relation(<?php print $data['product_id']; ?>);" style="height:10px;">Remove</span>
</li>