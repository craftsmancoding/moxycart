<script>
jQuery(document).ready(function() {
	$("#product_rows tbody").sortable();
    $("#product_rows tbody").disableSelection();
});

function product_save_seq() {
    var values = jQuery('#products').serialize();
    jQuery.post( connector_url+"product_save_seq", values, function( data ){
    	data = jQuery.parseJSON(data);
    	if(data.success == true) {
    		$('#moxy-result').html('Success');
    		$('#moxy-result-msg').html(data.msg);
    		$(".moxy-msg").delay(3200).fadeOut(300);
            setTimeout(function() {
                window.location.href = back_url;
            }, 1000);

    	} else{
    		$('#moxy-result').html('Failed');
    		$('#moxy-result-msg').html(data.msg);
    		$(".moxy-msg").delay(3200).fadeOut(300);
    	}
    });
}
</script>
<h2>Set Manual Sort Order</h2>

<div class="moxy-msg">
	<div id="moxy-result"></div>
	<div id="moxy-result-msg"></div>
</div>

<p>Drag and drop your products into any order.</p>

<form id="products">
    <table id="product_rows">
        <thead>
            <tr>
                <th>Name</th>
                <th>SKU</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach($data['results'] as $p):
            ?>
                <tr>
                    <td><?php print $p['name']; ?>
                        <input type="hidden" name="seq[]" value="<?php print $p['product_id']; ?>" />
                    </td>
                    <td><?php print $p['sku']; ?></td>
                    <td><?php print $p['price']; ?></td>
                </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <span class="btn" onclick="javascript:product_save_seq(); return false;">Save</span>
    <a class="btn" href="<?php print $data['back_url']; ?>">Close</span>
</form>