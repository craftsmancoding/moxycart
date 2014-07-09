<script>
jQuery(document).ready(function(){
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
                
                }
                else {
                    alert('Fail');
                }

                
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                //if fails      
                alert('Success.');
            }
        });
        e.preventDefault(); //STOP default action
        e.unbind(); //unbind. to stop multiple form submit.
        return false;
    });
});
</script>
<form id="product_bulk_editor" action="<?php print static::url('product','bulk'); ?>" method="post">
    <table class="classy">
        <thead>
            <tr>
                <th>Name</th>
                <th>Keywords</th>
                <th>Price</th>
                <th>Modify Inventory</th>
            </tr>
        </thead>
        <tbody id="product_list">
        <?php foreach ($data['results'] as $r): ?>
            <tr class="">
                <td>
                    <input type="hidden" name="product_id[]" value="<?php print $r['product_id']; ?>"/>
                    <input type="text" name="name[]" class="row-field" value="<?php print htmlentities($r['name']); ?>"/>
                </td>
                <td><input type="text" name="meta_keywords[]" class="row-field" value="<?php print htmlentities($r['meta_keywords']); ?>"/></td>
                <td><input type="text" name="price[]" class="row-field" value="<?php print htmlentities($r['price']); ?>"/></td>
                <td><input type="text" name="inventory_change[]" class="row-field" value="0"/></td>
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