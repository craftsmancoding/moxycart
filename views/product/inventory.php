<script>
jQuery(document).ready(function(){
    // onSubmit
    jQuery('#product_bulk_editor').submit(function(e){
        console.log('Bulk Editor Form Submitted.');
        var postData = jQuery(this).serializeArray();
        var formURL = jQuery(this).attr("action");
        jQuery.ajax(
        {
            url : formURL,
            type: "POST",
            data : postData,
            success:function(data, textStatus, jqXHR) 
            {
                console.log(data);
                if (data.status=="success") {
                    show_success('Update successful.');
                    // update product list. Todo: AJAX
                    jQuery.colorbox.close(); 
                    location.reload(true);
                }
                else {
                    show_error('There was a problem updating the products.');
                    jQuery.colorbox.close(); 
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                //if fails      
                alert('Success.');
            }
        });
        e.preventDefault(); //STOP default action
        //e.unbind(); //unbind. to stop multiple form submit.
        return false;
    });
});
</script>
<form id="product_bulk_editor" action="<?php print static::url('product','bulk'); ?>" method="post">
    <table class="prod-inventory-tbl classy">
        <thead>
            <tr>
                <th>Name</th>
                <th>Active</th>
                <th>Price</th>
                <th>Track</th>
                <th>Qty</th>
                <th>(+/-)</th>
                <th>Alert</th>
                <th>Backorder Max</th>
            </tr>
        </thead>
        <tbody id="product_list">
        <?php foreach ($data['results'] as $r): ?>
            <tr class="">
                <td>    
                    <input type="hidden" name="product_id[<?php print $r['product_id']; ?>]" value="<?php print $r['product_id']; ?>"/>
                    <input type="text" name="name[<?php print $r['product_id']; ?>]" class="row-field input-150" value="<?php print htmlentities($r['name']); ?>"/>
                </td>
                <td>
                <?php
                print \Formbuilder\Form::checkbox('is_active['.$r['product_id'].']', $r['is_active']);
                ?>
                </td>
                <td><input type="text" name="price[<?php print $r['product_id']; ?>]" class="input-50 row-field" value="<?php print htmlentities($r['price']); ?>"/></td>
                <td>
                <?php
                print \Formbuilder\Form::checkbox('track_inventory['.$r['product_id'].']', $r['track_inventory']);
                ?>
                </td>
                <td class="pull-right"><p style="margin-top: 10px;"><?php print $r['qty_inventory']; ?></p></td>
                <td>
                    <input type="text" name="change_inventory[<?php print $r['product_id']; ?>]" class="input-50 row-field" value=""/>
                </td>
                <td>
                    <input type="text" name="qty_alert[<?php print $r['product_id']; ?>]" class="input-50 row-field" value="<?php print $r['qty_alert']; ?>"/>
                </td>
                <td>
                    <input type="text" name="qty_backorder_max[<?php print $r['product_id']; ?>]" class="input-50 row-field" value="<?php print $r['qty_backorder_max']; ?>"/>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="clearfix"></div>
    <div class="moxy-modal-controls">
        <input class="btn" type="submit" value="Save"/>
        <span class="btn" onclick="javascript:jQuery.colorbox.close();">Cancel</span>
    </div>    

</form>